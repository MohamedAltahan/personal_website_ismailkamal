<?php

use App\Support\Settings;

if (! function_exists('setting')) {
    /**
     * Read a site setting using "group.key" notation, e.g. setting('media.quality').
     */
    function setting(?string $key = null, mixed $default = null): mixed
    {
        $settings = app(Settings::class);

        return $key === null ? $settings : $settings->get($key, $default);
    }
}

if (! function_exists('locales')) {
    /** @return array<string, array{name: string, dir: string}> */
    function locales(): array
    {
        return config('site.locales');
    }
}

if (! function_exists('is_rtl')) {
    function is_rtl(?string $locale = null): bool
    {
        return (locales()[$locale ?? app()->getLocale()]['dir'] ?? 'ltr') === 'rtl';
    }
}

if (! function_exists('tr')) {
    /**
     * Pick the current-locale value out of a {ar, en} array (used by block content),
     * falling back to the other locale when the current one is empty.
     */
    function tr(mixed $value, ?string $locale = null): string
    {
        if (! is_array($value)) {
            return (string) $value;
        }

        $locale ??= app()->getLocale();

        if (filled($value[$locale] ?? null)) {
            return (string) $value[$locale];
        }

        foreach ($value as $fallback) {
            if (filled($fallback)) {
                return (string) $fallback;
            }
        }

        return '';
    }
}

if (! function_exists('lroute')) {
    /**
     * Route URL for the public site in the given (or current) locale.
     */
    function lroute(string $name, mixed $parameters = [], ?string $locale = null): string
    {
        $parameters = is_array($parameters) ? $parameters : [$parameters];

        return route($name, ['locale' => $locale ?? app()->getLocale()] + $parameters);
    }
}

if (! function_exists('site_changed')) {
    /** Flush cached public navigation / settings after content changes in the dashboard. */
    function site_changed(): void
    {
        \App\View\Composers\SiteComposer::flush();
    }
}

if (! function_exists('locale_url')) {
    /** The current public page in another language (falls back to that language's home). */
    function locale_url(string $locale): string
    {
        $route = request()->route();

        if ($route?->getName() && array_key_exists('locale', $route->originalParameters())) {
            $url = route($route->getName(), array_merge($route->originalParameters(), ['locale' => $locale]));
            $query = request()->getQueryString();

            return $query ? $url.'?'.$query : $url;
        }

        return url('/'.$locale);
    }
}

if (! function_exists('site_initials')) {
    /** Two-letter monogram from the English site name, e.g. "Ismail Kamal" → "IK". */
    function site_initials(): string
    {
        $name = tr(setting('general.site_name'), 'en') ?: 'Site';

        return mb_strtoupper(collect(preg_split('/\s+/u', trim($name)))->filter()->take(2)
            ->map(fn ($word) => mb_substr($word, 0, 1))->implode(''));
    }
}
