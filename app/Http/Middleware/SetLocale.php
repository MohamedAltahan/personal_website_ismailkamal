<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/** Public site: locale comes from the {locale} URL prefix and is remembered in a cookie. */
class SetLocale
{
    public const COOKIE = 'site_locale';

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');

        if (! array_key_exists($locale, locales())) {
            abort(404);
        }

        app()->setLocale($locale);
        URL::defaults(['locale' => $locale]);
        $request->route()->forgetParameter('locale');

        $response = $next($request);

        if ($request->cookie(self::COOKIE) !== $locale) {
            Cookie::queue(self::COOKIE, $locale, 60 * 24 * 365);
        }

        return $response;
    }

    /**
     * Best locale for a visitor hitting "/": the language they chose before (cookie),
     * then — only if enabled in Settings — their browser language, then the site default.
     */
    public static function preferred(Request $request): string
    {
        $available = array_keys(locales());
        $cookie = $request->cookie(self::COOKIE);

        if (in_array($cookie, $available, true)) {
            return $cookie;
        }

        if (setting('appearance.detect_browser_locale') && $request->header('Accept-Language')) {
            $browser = $request->getPreferredLanguage($available);
            if ($browser) {
                return $browser;
            }
        }

        $default = setting('appearance.default_locale', config('app.locale'));

        return in_array($default, $available, true) ? $default : $available[0];
    }
}
