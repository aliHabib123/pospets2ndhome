<?php

namespace App\Http\Middleware;

use Closure;
use App, DB;

class LanguangeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        App::setLocale(DB::table('settings')->where('id', 1)->first()->languange);
        return $next($request);
    }
}
