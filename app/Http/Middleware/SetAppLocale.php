<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class SetAppLocale
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $allowed = ['fa', 'en', 'ar'];

        $queryLocale = $request->query('lang');
        if (is_string($queryLocale)) {
            $queryLocale = strtolower(trim($queryLocale));
        }

        if (is_string($queryLocale) && in_array($queryLocale, $allowed, true)) {
            $request->session()->put('app_locale', $queryLocale);
        }

        $locale = $request->session()->get('app_locale');
        if (is_string($locale) && in_array($locale, $allowed, true)) {
            app()->setLocale($locale);
            Carbon::setLocale($locale);
        }

        return $next($request);
    }
}
