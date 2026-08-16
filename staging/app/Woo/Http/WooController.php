<?php

namespace App\Woo\Http;

use App\Inventory;
use App\Item;
use App\ItemQuantity;
use App\Woo\Models\WooOrder;
use App\Woo\WooSettings;
use App\Woo\WooSync;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * All inbound endpoints the WooCommerce plugin talks to. Fully isolated from the
 * rest of the POS: its own routes, its own auth (shared secret + HMAC).
 */
class WooController extends Controller
{
    /**
     * Reject the request unless the bearer token matches the shared secret.
     */
    protected function authorizeBearer(Request $request)
    {
        $secret = WooSettings::secret();
        $bearer = (string) $request->bearerToken();

        if ($secret === '' || ! hash_equals($secret, $bearer)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return null;
    }

    /**
     * GET /api/woo/products — catalogue export for the plugin's import button.
     */
    public function products(Request $request)
    {
        if ($deny = $this->authorizeBearer($request)) {
            return $deny;
        }

        $products = [];

        // Only tracked products (type_id = 1) are sellable online.
        Item::where('type_id', 1)->chunk(500, function ($items) use (&$products) {
            foreach ($items as $item) {
                $products[] = WooSync::payloadForItem($item);
            }
        });

        return response()->json(['products' => $products]);
    }

    /**
     * POST /api/woo/map — plugin reports back pos_item_id => woo_product_id after import.
     */
    public function map(Request $request)
    {
        if ($deny = $this->authorizeBearer($request)) {
            return $deny;
        }

        $mappings = $request->input('mappings', []);
        if (! is_array($mappings)) {
            return response()->json(['error' => 'mappings must be an array'], 422);
        }

        $updated = 0;
        foreach ($mappings as $m) {
            $posId = isset($m['pos_item_id']) ? (int) $m['pos_item_id'] : 0;
            $wooId = isset($m['woo_product_id']) ? (int) $m['woo_product_id'] : 0;
            if ($posId && $wooId) {
                $updated += Item::where('id', $posId)->update(['woo_product_id' => $wooId]);
            }
        }

        return response()->json(['ok' => true, 'updated' => $updated]);
    }

    /**
     * POST /api/woo/order — a web order was placed; decrement inventory.
     *
     * Auth: bearer token + HMAC signature over the raw body. Idempotent on woo_order_id.
     */
    public function order(Request $request)
    {
        if ($deny = $this->authorizeBearer($request)) {
            return $deny;
        }

        if (! WooSync::verify($request->getContent(), $request->header('X-POS-Signature'))) {
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $orderId = (int) $request->input('woo_order_id');
        $lineItems = $request->input('line_items', []);

        if (! $orderId || ! is_array($lineItems)) {
            return response()->json(['error' => 'Invalid payload'], 422);
        }

        // Idempotency: if we've seen this order, acknowledge without re-decrementing.
        if (WooOrder::where('woo_order_id', $orderId)->exists()) {
            return response()->json(['ok' => true, 'duplicate' => true]);
        }

        try {
            DB::transaction(function () use ($orderId, $lineItems, $request) {
                foreach ($lineItems as $line) {
                    $itemId = $this->resolveItemId($line);
                    $qty = isset($line['quantity']) ? (int) $line['quantity'] : 0;
                    if ($itemId && $qty > 0) {
                        $this->decrementItem($itemId, $qty, $orderId);
                    }
                }

                WooOrder::create([
                    'woo_order_id' => $orderId,
                    'status'       => (string) $request->input('status'),
                    'payload'      => $request->getContent(),
                    'processed_at' => now(),
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('Woo order processing failed for #' . $orderId . ': ' . $e->getMessage());
            return response()->json(['error' => 'Processing failed'], 500);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * POST /api/woo/order-cancel — an order was fully cancelled; restore inventory.
     *
     * Reverses exactly what was decremented, using the line items stored when the
     * order was processed. Idempotent: only restocks an order that was previously
     * decremented and not already cancelled.
     */
    public function orderCancel(Request $request)
    {
        if ($deny = $this->authorizeBearer($request)) {
            return $deny;
        }

        if (! WooSync::verify($request->getContent(), $request->header('X-POS-Signature'))) {
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $orderId = (int) $request->input('woo_order_id');
        if (! $orderId) {
            return response()->json(['error' => 'Invalid payload'], 422);
        }

        $order = WooOrder::where('woo_order_id', $orderId)->first();

        // Never decremented for this order → nothing to put back.
        if (! $order) {
            return response()->json(['ok' => true, 'skipped' => 'not_processed']);
        }

        // Already restocked → idempotent.
        if ($order->status === 'cancelled') {
            return response()->json(['ok' => true, 'duplicate' => true]);
        }

        // Reverse exactly what was decremented, from the stored processing payload.
        $stored    = json_decode((string) $order->payload, true);
        $lineItems = (is_array($stored) && isset($stored['line_items'])) ? $stored['line_items'] : [];

        try {
            DB::transaction(function () use ($orderId, $lineItems, $order) {
                foreach ($lineItems as $line) {
                    $itemId = $this->resolveItemId((array) $line);
                    $qty    = isset($line['quantity']) ? (int) $line['quantity'] : 0;
                    if ($itemId && $qty > 0) {
                        $this->restoreItem($itemId, $qty, $orderId);
                    }
                }

                $order->status       = 'cancelled';
                $order->processed_at = now();
                $order->save();
            });
        } catch (\Throwable $e) {
            Log::error('Woo order cancel failed for #' . $orderId . ': ' . $e->getMessage());
            return response()->json(['error' => 'Processing failed'], 500);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Resolve a POS item id from a line item (prefer pos_item_id, fall back to SKU).
     */
    protected function resolveItemId(array $line): int
    {
        if (! empty($line['pos_item_id'])) {
            return (int) $line['pos_item_id'];
        }

        if (! empty($line['sku']) && strpos($line['sku'], 'POS-') === 0) {
            return (int) substr($line['sku'], 4);
        }

        return 0;
    }

    /**
     * Decrement `$qty` of an item across the configured web locations (in order),
     * writing an Inventory audit row per location touched. Overflow (oversell)
     * comes off the first web location so POS books still balance.
     */
    protected function decrementItem(int $itemId, int $qty, int $orderId): void
    {
        $item = Item::find($itemId);
        if (! $item || $item->type_id != 1) {
            return; // Only tracked products carry stock.
        }

        $locations = WooSettings::locations();
        if (empty($locations)) {
            return;
        }

        $userId = WooSettings::systemUserId();
        $remaining = $qty;

        foreach ($locations as $locationId) {
            if ($remaining <= 0) {
                break;
            }

            $iq = ItemQuantity::where('item_id', $itemId)
                ->where('location_id', $locationId)
                ->first();

            if (! $iq) {
                continue;
            }

            $available = max((int) $iq->quantity, 0);
            $take = min($remaining, $available);
            if ($take <= 0) {
                continue;
            }

            $this->writeMovement($itemId, $locationId, $iq, $take, $userId, $orderId);
            $remaining -= $take;
        }

        // Oversell: take the remainder from the primary web location (may go negative).
        if ($remaining > 0) {
            $primary = $locations[0];
            $iq = ItemQuantity::where('item_id', $itemId)
                ->where('location_id', $primary)
                ->first();
            if ($iq) {
                $this->writeMovement($itemId, $primary, $iq, $remaining, $userId, $orderId);
            }
        }
    }

    /**
     * Write one Inventory row + update the ItemQuantity, mirroring SaleController.
     */
    protected function writeMovement(int $itemId, int $locationId, ItemQuantity $iq, int $take, int $userId, int $orderId): void
    {
        $inventory = new Inventory;
        $inventory->item_id = $itemId;
        $inventory->user_id = $userId;
        $inventory->location_id = $locationId;
        $inventory->in_out_qty = -$take;
        $inventory->remarks = 'WEB' . $orderId;
        $inventory->qty_before_transaction = (int) $iq->quantity;
        $inventory->save();

        $iq->quantity = (int) $iq->quantity - $take;
        $iq->save(); // Fires the observer → queues an absolute stock push back to Woo.
    }

    /**
     * Restore `$qty` of an item on a full order cancellation. Single-location setup:
     * the quantity is put back on the primary web location, writing a `WEBCANCEL<id>`
     * audit row (positive in_out_qty).
     */
    protected function restoreItem(int $itemId, int $qty, int $orderId): void
    {
        $item = Item::find($itemId);
        if (! $item || $item->type_id != 1) {
            return; // Only tracked products carry stock.
        }

        $locations = WooSettings::locations();
        if (empty($locations)) {
            return;
        }

        $primary = $locations[0];
        $userId  = WooSettings::systemUserId();

        $iq = ItemQuantity::where('item_id', $itemId)
            ->where('location_id', $primary)
            ->first();

        if (! $iq) {
            // Should exist, but recreate so a restock is never silently lost.
            $iq = new ItemQuantity;
            $iq->item_id = $itemId;
            $iq->location_id = $primary;
            $iq->quantity = 0;
        }

        $inventory = new Inventory;
        $inventory->item_id = $itemId;
        $inventory->user_id = $userId;
        $inventory->location_id = $primary;
        $inventory->in_out_qty = $qty; // positive — stock returning
        $inventory->remarks = 'WEBCANCEL' . $orderId;
        $inventory->qty_before_transaction = (int) $iq->quantity;
        $inventory->save();

        $iq->quantity = (int) $iq->quantity + $qty;
        $iq->save(); // Fires the observer → queues an absolute stock push back to Woo.
    }
}
