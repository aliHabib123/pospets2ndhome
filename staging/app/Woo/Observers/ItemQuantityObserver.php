<?php

namespace App\Woo\Observers;

use App\ItemQuantity;
use App\Woo\WooSync;

/**
 * Catches every stock mutation. Because sales, refunds, receiving and transfers
 * all call ItemQuantity::save(), this single hook queues a Woo push for all of
 * them without touching any existing controller.
 */
class ItemQuantityObserver
{
    public function saved(ItemQuantity $quantity): void
    {
        WooSync::enqueueStock($quantity->item_id, $quantity->location_id);
    }
}
