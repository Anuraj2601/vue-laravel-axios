<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetUserLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->query('lang')
               ?? $request->header('Accept-Language')
               ?? $request->cookie('lang')
               ?? 'en';

        
        $supportedLocales = ['en', 'de'];

        if (strpos($locale, ',') !== false) {
            $locale = explode(',', $locale)[0];
        }

        if (strlen($locale) > 2) {
            $locale = substr($locale, 0, 2);
        }

        if (!in_array($locale, $supportedLocales)) {
            $locale = 'en';
        }

        App::setLocale($locale);

        return $next($request);
    
    }
}
