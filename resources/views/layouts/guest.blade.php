@php($rtl = is_rtl())
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $rtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title') — {{ setting()->text('general.site_name') }}</title>
    @if ($favicon = setting()->media('branding.favicon'))
        <link rel="icon" href="{{ $favicon->url(null) }}">
    @endif
    @include('partials.theme-script', ['key' => 'admin-theme', 'default' => setting('appearance.admin_theme', 'light')])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/dashboard.css', 'resources/js/dashboard.js'])
</head>
<body class="antialiased">
<div class="min-h-screen grid lg:grid-cols-[1.05fr_1fr]">
    {{-- Brand panel --}}
    <div class="relative hidden lg:flex flex-col justify-between overflow-hidden bg-sidebar text-white p-12">
        <div class="absolute -top-32 -start-24 w-[28rem] h-[28rem] rounded-full bg-accent-500/10 blur-3xl"></div>
        <div class="absolute -bottom-40 end-0 w-[30rem] h-[30rem] rounded-full bg-primary-500/10 blur-3xl"></div>
        <div class="absolute inset-0 opacity-[0.06]" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 22px 22px;"></div>

        <div class="relative flex items-center gap-3">
            @if ($logo = setting()->media('branding.logo_dark') ?? setting()->media('branding.logo'))
                <img src="{{ $logo->url(480) }}" alt="" class="h-11 w-auto drop-shadow-[0_0_10px_rgba(196,154,25,0.6)]">
            @endif
        </div>

        <div class="relative">
            <p class="text-accent-500 font-bold mb-4">{{ setting()->text('general.tagline') }}</p>
            <h1 class="text-5xl font-bold leading-tight mb-5">{{ setting()->text('general.site_name') }}</h1>
            <p class="text-white/60 max-w-md leading-relaxed">{{ __('Manage your projects, pages and media from one place — in Arabic and English.') }}</p>
        </div>

        <p class="relative text-xs text-white/35">© {{ date('Y') }} {{ setting()->text('general.site_name') }}</p>
    </div>

    {{-- Form --}}
    <div class="flex flex-col bg-canvas">
        <div class="flex items-center justify-end gap-2 p-5">
            <div x-data="themeToggle">
                <button type="button" @click="cycle()" class="btn-icon">
                    <span x-show="pref === 'light'"><x-icon name="sun" /></span>
                    <span x-show="pref === 'dark'" x-cloak><x-icon name="moon" /></span>
                    <span x-show="pref === 'system'" x-cloak><x-icon name="monitor" /></span>
                </button>
            </div>
            @php($other = collect(locales())->keys()->first(fn ($l) => $l !== app()->getLocale()))
            <form method="POST" action="{{ route('admin.locale', $other) }}">
                @csrf
                <button class="btn-ghost h-9 px-3 text-xs">{{ locales()[$other]['name'] }}</button>
            </form>
        </div>
        <div class="flex-1 grid place-items-center px-5 pb-16">
            <div class="w-full max-w-[420px]">
                @yield('content')
            </div>
        </div>
    </div>
</div>
@include('admin.partials.toasts')
</body>
</html>
