<?php

namespace App\Woo;

use App\Item;
use App\ItemQuantity;
use App\Woo\Console\WooReconcileCommand;
use App\Woo\Console\WooSyncCommand;
use App\Woo\Observers\ItemObserver;
use App\Woo\Observers\ItemQuantityObserver;
use Illuminate\Support\ServiceProvider;

/**
 * Single entry point for the WooCommerce integration. Registering this one
 * provider in config/app.php wires up routes, observers and commands. Removing
 * that one line fully disables the integration with zero other changes.
 */
class WooServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Inbound API routes (isolated under /api/woo).
        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        // Catch every stock/product mutation to queue outbound pushes.
        ItemQuantity::observe(ItemQuantityObserver::class);
        Item::observe(ItemObserver::class);

        // Console commands + cron drainer.
        if ($this->app->runningInConsole()) {
            $this->commands([
                WooSyncCommand::class,
                WooReconcileCommand::class,
            ]);
        }
    }

    public function register()
    {
        //
    }
}
