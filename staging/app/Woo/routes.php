<?php

use Illuminate\Support\Facades\Route;

/*
 * WooCommerce integration endpoints. Loaded by WooServiceProvider.
 * Uses the "api" middleware group (throttle + bindings, no CSRF/session),
 * so it is fully independent of the app's web routes and auth guards.
 * Auth is enforced inside the controller (shared secret + HMAC).
 */
Route::middleware('api')->prefix('api/woo')->group(function () {
    Route::get('products', 'App\Woo\Http\WooController@products');
    Route::post('order', 'App\Woo\Http\WooController@order');
    Route::post('map', 'App\Woo\Http\WooController@map');
});
