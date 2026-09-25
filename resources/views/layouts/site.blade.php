@php
    $locale = app()->getLocale();
    $rtl = is_rtl();
    $siteName = setting()->text('general.site_name');
    $brand = \App\Support\Color::valid(setting('appearance.primary_color')) ? setting('appearance.primary_color') : '#ffd200';
    $darkPaper = \App\Support\Color::valid(setting('appearance.ink_color')) ? setting('appearance.ink_color') : '#0b0b0c';
    $fontAr = setting('appearance.font_arabic');
    $fontDisplay = setting('appearance.font_latin');
    $fontBody = setting('appearance.font_latin_body');
    $families = collect($rtl ? [$fontAr] : [$fontDisplay, $fontBody, $fontAr])->filter()->unique()
        ->map(fn ($f) => 'family='.str_replace(' ', '+', $f).':wght@400;500;600;700')->implode('&');
    $pageTitle = trim($__env->yieldContent('title'));
    $fullTitle = $pageTitle ? $pageTitle.' — '.$siteName : $siteName.' — '.setting()->text('general.tagline');
    $description = trim($__env->yieldContent('description')) ?: setting()->text('seo.meta_description') ?: setting()->text('general.tagline');
    $ogImage = trim($__env->yieldContent('og_image')) ?: setting()->media('branding.og_image')?->url(1600);
    $preview = $preview ?? false;
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $rtl ? 'rtl' : 'ltr' }}"
      data-protect="{{ setting('appearance.protect_media') && ! $preview ? 'on' : 'off' }}"
      data-custom-cursor="{{ setting('appearance.custom_cursor') && ! $preview ? setting('appearance.cursor_scope', 'hero') : 'off' }}"
      data-animations="{{ setting('appearance.animations') ? 'on' : 'off' }}"
      data-reduced-motion="{{ setting('appearance.respect_reduced_motion', true) ? 'respect' : 'ignore' }}"
      data-preview="{{ $preview ? 'on' : 'off' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ $fullTitle }}</title>
    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($description), 160) }}">
    @if (! setting('seo.indexable') || $preview)
        <meta name="robots" content="noindex, nofollow">
    @endif
    <meta name="theme-color" content="{{ $darkPaper }}" media="(prefers-color-scheme: dark)">
    <meta name="theme-color" content="#f4f3ef" media="(prefers-color-scheme: light)">

    @unless ($preview)
        <link rel="canonical" href="{{ url()->current() }}">
        @foreach (array_keys(locales()) as $alt)
            <link rel="alternate" hreflang="{{ $alt }}" href="{{ $__env->yieldContent('alternate_'.$alt) ?: preg_replace('#^('.preg_quote(url('/'), '#').')/'.$locale.'(/|$)#', '$1/'.$alt.'$2', url()->current()) }}">
        @endforeach
    @endunless

    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $fullTitle }}">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($description), 200) }}">
    <meta property="og:locale" content="{{ $locale === 'ar' ? 'ar_AR' : 'en_US' }}">
    @if ($ogImage)
        <meta property="og:image" content="{{ url($ogImage) }}">
        <meta name="twitter:card" content="summary_large_image">
    @endif

    @if ($favicon = setting()->media('branding.favicon'))
        <link rel="icon" href="{{ $favicon->url(480) }}">
        <link rel="apple-touch-icon" href="{{ $favicon->url(480) }}">
    @endif

    @include('partials.theme-script', ['key' => 'theme', 'default' => setting('appearance.default_theme', 'dark')])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?{{ $families }}&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand: {{ $brand }};
            --brand-ink: {{ \App\Support\Color::contrast($brand) }};
            --dark-paper: {{ $darkPaper }};
            --font-body: '{{ $rtl ? $fontAr : $fontBody }}', '{{ $fontAr }}';
            --font-heading: '{{ $rtl ? $fontAr : $fontDisplay }}', '{{ $fontAr }}';
        }
    </style>

    @vite(['resources/css/site.css', 'resources/js/site.js'])

    @if (($ga = setting('seo.google_analytics')) && ! $preview && app()->isProduction())
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga }}"></script>
        <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config',@json($ga));</script>
    @endif

    @stack('head')
</head>
<body class="min-h-screen flex flex-col">
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:start-3 focus:z-[90] focus:bg-brand focus:text-brand-ink focus:px-4 focus:py-2 focus:rounded-full">{{ __('Skip to content') }}</a>

    @include('site.partials.header')

    <main id="main" class="flex-1">
        @yield('content')
    </main>

    @include('site.partials.footer')

    @stack('scripts')
</body>
</html>
