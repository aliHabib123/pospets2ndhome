<?php

namespace App\Woo\Console;

use App\Item;
use App\Woo\WooSettings;
use App\Woo\WooSync;
use Illuminate\Console\Command;

/**
 * Safety net: re-push the current stock/price of every mapped item to Woo,
 * correcting any drift. Run nightly, e.g.  0 3 * * * php artisan woo:reconcile
 */
class WooReconcileCommand extends Command
{
    protected $signature = 'woo:reconcile';

    protected $description = 'Re-push current stock/price for all Woo-mapped items';

    public function handle(): int
    {
        if (! WooSettings::enabled()) {
            $this->info('Woo sync disabled. Nothing to do.');
            return 0;
        }

        $ok = 0;
        $failed = 0;

        Item::whereNotNull('woo_product_id')->chunk(100, function ($items) use (&$ok, &$failed) {
            foreach ($items as $item) {
                [$success, $error] = WooSync::pushItem($item);
                if ($success) {
                    $ok++;
                } else {
                    $failed++;
                    $this->warn("Item {$item->id}: {$error}");
                }
            }
        });

        $this->info("Reconciled {$ok} item(s), {$failed} failed.");
        return 0;
    }
}
