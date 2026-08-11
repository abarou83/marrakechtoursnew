<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Currency;
use Illuminate\Support\Facades\Cookie;

class SetCurrency
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Change currency via query string ?currency=CODE
        if ($request->has('currency')) {
            $code = strtoupper(substr($request->query('currency'), 0, 3));
            $currency = Currency::where('code', $code)->where('is_active', true)->first();
            if ($currency) {
                session(['currency' => $currency->code]);
                // Persist in cookie (30 days)
                Cookie::queue('currency', $currency->code, 60 * 24 * 30);
            }
        }

        // Ensure a currency is always set
        if (!session()->has('currency')) {
            // Try to restore from cookie if available
            $cookieCode = $request->cookie('currency');
            if ($cookieCode) {
                $found = Currency::where('code', strtoupper(substr($cookieCode, 0, 3)))->where('is_active', true)->first();
                if ($found) {
                    session(['currency' => $found->code]);
                }
            }
        }

        if (!session()->has('currency')) {
            $default = Currency::where('is_default', true)->first() ?: Currency::where('is_active', true)->first();
            if ($default) {
                session(['currency' => $default->code]);
                Cookie::queue('currency', $default->code, 60 * 24 * 30);
            }
        }

        return $next($request);
    }
}
