<?php

namespace App\Woo\Observers;

use App\Item;
use App\Woo\WooSync;

/**
 * Pushes title/price/description changes to Woo. Only relevant fields trigger a
 * push, and only for items already mapped to a WooCommerce product.
 */
class ItemObserver
{
    public function saved(Item $item): void
    {
        // Nothing to sync unless a Woo-relevant field actually changed.
        $watched = ['item_name', 'description', 'selling_price_usd'];

        if ($item->wasRecentlyCreated || $this->changed($item, $watched)) {
            WooSync::enqueueStock($item->id);
        }
    }

    protected function changed(Item $item, array $fields): bool
    {
        foreach ($fields as $field) {
            if (array_key_exists($field, $item->getChanges())) {
                return true;
            }
        }

        return false;
    }
}
