<?php

namespace App\Woo\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Outbox row: a pending "push this item's stock/price to WooCommerce" job.
 */
class WooSyncQueue extends Model
{
    protected $table = 'woo_sync_queue';

    protected $fillable = [
        'item_id',
        'action',
        'status',
        'attempts',
        'last_error',
    ];
}
