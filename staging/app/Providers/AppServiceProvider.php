<?php

namespace App\Providers;

use App\Location;
use App\Setting;
use App\Transfer;
use App\TransferTemp;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        view()->composer('*', function ($view)
        {
            if (!empty(Auth::user()->id)) {
                $user_locations  = User::with('locations')->find(Auth::user()->id)->locations;

                View::share('globalLocations', $user_locations);
            }else{

                View::share('globalLocations', []);
            }
 

            $location_id = Session::get('selectedLocationId');
            $transfers = Transfer::where('to_location', $location_id)
                ->where('status', 0)
                ->get();


            View::share('global_transfers', $transfers);


            $currency = Setting::where('config', 'currency') ->first()->value;
            View::share('currency', $currency);

            $salesCurrency = Setting::where('config', 'sales-currency') ->first()->value;
            View::share('sales_currency', $salesCurrency);

            $salesCurrency = Setting::where('config', 'site-url') ->first()->value;
            View::share('site_url', $salesCurrency);

        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
