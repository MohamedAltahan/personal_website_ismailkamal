<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Dashboard: locale from the signed-in user's preference, then session, then the dashboard default. */
class SetAdminLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->user()?->locale
            ?? $request->session()->get('admin_locale')
            ?? setting('appearance.admin_locale', 'ar');

        if (array_key_exists($locale, locales())) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
