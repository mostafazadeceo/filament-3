<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $allowed = ['fa', 'en', 'ar'];

        $locale = $request->cookies->get('abrak_locale');
        if (! $locale && $request->hasSession()) {
            $locale = $request->session()->get('abrak_locale')
                ?? $request->session()->get('app_locale');
        }

        if (is_string($locale) && in_array($locale, $allowed, true)) {
            app()->setLocale($locale);
            Carbon::setLocale($locale);
        }

        return $next($request);
    }
}
