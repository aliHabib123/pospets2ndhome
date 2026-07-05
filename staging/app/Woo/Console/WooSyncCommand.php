<?php

namespace App\Woo\Console;

use App\Item;
use App\Woo\Models\WooSyncQueue;
use App\Woo\WooSettings;
use App\Woo\WooSync;
use Illuminate\Console\Command;

/**
 * Drains the outbox: pushes queued stock/price changes to WooCommerce.
 * Run every minute via cron:  * * * * * php artisan woo:sync
 */
class WooSyncCommand extends Command
{
    protected $signature = 'woo:sync {--limit=200 : Max queue rows to process} {--max-attempts=5}';

    protected $description = 'Push queued POS stock/price changes to WooCommerce';

    public function handle(): int
    {
        if (! WooSettings::enabled()) {
            $this->info('Woo sync disabled (woo_sync_enabled != yes). Nothing to do.');
            return 0;
        }

        $maxAttempts = (int) $this->option('max-attempts');

        $rows = WooSyncQueue::where('status', 'pending')
            ->where('attempts', '<', $maxAttempts)
            ->orderBy('id')
            ->limit((int) $this->option('limit'))
            ->get();

        if ($rows->isEmpty()) {
            $this->info('Queue empty.');
            return 0;
        }

        // Collapse to one push per item.
        $byItem = $rows->groupBy('item_id');
        $ok = 0;
        $failed = 0;

        foreach ($byItem as $itemId => $itemRows) {
            $ids = $itemRows->pluck('id')->all();
            $item = Item::find($itemId);

            if (! $item || empty($item->woo_product_id)) {
                // Nothing to push (deleted or unmapped) — clear the rows.
                WooSyncQueue::whereIn('id', $ids)->delete();
                continue;
            }

            [$success, $error] = WooSync::pushItem($item);

            if ($success) {
                WooSyncQueue::whereIn('id', $ids)->delete();
                $ok++;
            } else {
                WooSyncQueue::whereIn('id', $ids)->update([
                    'attempts'   => $itemRows->max('attempts') + 1,
                    'last_error' => substr($error, 0, 500),
                ]);
                $failed++;
                $this->warn("Item {$itemId}: {$error}");
            }
        }

        $this->info("Pushed {$ok} item(s), {$failed} failed.");
        return 0;
    }
}
