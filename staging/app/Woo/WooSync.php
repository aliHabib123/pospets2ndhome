<?php

namespace App\Woo;

use App\Item;
use App\ItemQuantity;
use App\Woo\Models\WooSyncQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Core service for the WooCommerce integration.
 *
 * Design goals:
 *  - Never throw into the POS request path. enqueue*() swallows everything.
 *  - Push absolute stock values to Woo (never deltas) so there is no feedback loop.
 *  - Touch only its own tables plus items.woo_product_id.
 */
class WooSync
{
    /** @var bool|null Cached "do the woo tables exist" check. */
    protected static $tablesReady = null;

    /**
     * Are the integration tables present? Cached for the request.
     */
    protected static function tablesReady(): bool
    {
        if (self::$tablesReady === null) {
            try {
                self::$tablesReady = Schema::hasTable('woo_sync_queue')
                    && Schema::hasColumn('items', 'woo_product_id');
            } catch (\Throwable $e) {
                self::$tablesReady = false;
            }
        }

        return self::$tablesReady;
    }

    /**
     * Queue a stock/price push for an item at a given location.
     *
     * Called from model observers, so it must never throw. It no-ops unless the
     * integration is enabled, the location is a web location, and the item is
     * mapped to a WooCommerce product.
     */
    public static function enqueueStock($itemId, $locationId = null): void
    {
        try {
            if (! self::tablesReady() || ! WooSettings::enabled()) {
                return;
            }

            // Only care about changes at the configured web locations.
            if ($locationId !== null) {
                $locations = WooSettings::locations();
                if (! empty($locations) && ! in_array((int) $locationId, $locations, true)) {
                    return;
                }
            }

            $item = Item::find($itemId);
            if (! $item || empty($item->woo_product_id)) {
                return; // Not imported into Woo yet — nothing to update.
            }

            // Collapse duplicate pending rows for the same item.
            $exists = WooSyncQueue::where('item_id', $itemId)
                ->where('action', 'stock')
                ->where('status', 'pending')
                ->exists();

            if (! $exists) {
                WooSyncQueue::create([
                    'item_id'  => $itemId,
                    'action'   => 'stock',
                    'status'   => 'pending',
                    'attempts' => 0,
                ]);
            }
        } catch (\Throwable $e) {
            // Integration must never break the POS. Log and move on.
            Log::warning('WooSync enqueue failed: ' . $e->getMessage());
        }
    }

    /**
     * Aggregate web stock for an item = sum of item_quantities over web locations.
     */
    public static function stockForItem($itemId): int
    {
        $locations = WooSettings::locations();

        $query = ItemQuantity::where('item_id', $itemId);
        if (! empty($locations)) {
            $query->whereIn('location_id', $locations);
        }

        return (int) $query->sum('quantity');
    }

    /**
     * Build the outbound payload for an item.
     */
    public static function payloadForItem(Item $item): array
    {
        return [
            'pos_item_id'  => (int) $item->id,
            'sku'          => 'POS-' . $item->id,
            'name'         => $item->item_name,
            'description'  => (string) $item->description,
            'price'        => (string) ($item->selling_price_usd ?? '0'),
            'stock'        => self::stockForItem($item->id),
            'manage_stock' => true,
        ];
    }

    /**
     * Push one item's current stock/price to the WooCommerce plugin.
     *
     * @return array{0:bool,1:string} [success, error message]
     */
    public static function pushItem(Item $item): array
    {
        $url = WooSettings::wpUrl() . '/wp-json/pos-sync/v1/product';
        $body = json_encode(self::payloadForItem($item));

        return self::signedPost($url, $body);
    }

    /**
     * HMAC-SHA256 signature of a raw body.
     */
    public static function sign(string $body): string
    {
        return hash_hmac('sha256', $body, WooSettings::secret());
    }

    /**
     * Constant-time verification of an inbound signature.
     */
    public static function verify(string $body, ?string $signature): bool
    {
        $secret = WooSettings::secret();
        if ($secret === '' || empty($signature)) {
            return false;
        }

        return hash_equals(self::sign($body), (string) $signature);
    }

    /**
     * POST a signed JSON body to the WordPress plugin over cURL (SSL verified).
     *
     * @return array{0:bool,1:string}
     */
    protected static function signedPost(string $url, string $body): array
    {
        if (WooSettings::wpUrl() === '') {
            return [false, 'WordPress URL not configured'];
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Accept: application/json',
                'X-POS-Signature: ' . self::sign($body),
            ],
        ]);

        $response = curl_exec($ch);
        $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            return [false, 'cURL error: ' . $error];
        }

        if ($status < 200 || $status >= 300) {
            return [false, 'HTTP ' . $status . ': ' . substr((string) $response, 0, 200)];
        }

        return [true, ''];
    }
}
