<?php

namespace App\Woo\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Record of a processed WooCommerce order — used for idempotency so a
 * re-delivered webhook never decrements stock twice.
 */
class WooOrder extends Model
{
    protected $table = 'woo_orders';

    protected $fillable = [
        'woo_order_id',
        'status',
        'payload',
        'processed_at',
    ];

    protected $dates = ['processed_at'];
}
