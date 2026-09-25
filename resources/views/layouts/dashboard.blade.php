@if (request()->header('X-Fragment') === '1')
    @yield('content')
@else
@php
    $me = auth()->user();
    $locale = app()->getLocale();
    $rtl = is_rtl();
    $unread = \App\Models\Message::unread()->count();
    $nav = [
        ['label' => __('Overview'), 'route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'icon' => 'grid'],
        ['label' => __('Projects'), 'route' => 'admin.projects.index', 'active' => 'admin.projects.*', 'icon' => 'briefcase'],
        ['label' => __('Categories'), 'route' => 'admin.categories.index', 'active' => 'admin.categories.*', 'icon' => 'layers'],
        ['label' => __('Pages'), 'route' => 'admin.pages.index', 'active' => 'admin.pages.*', 'icon' => 'file'],
        ['label' => __('Media library'), 'route' => 'admin.media.index', 'active' => 'admin.media.*', 'icon' => 'images'],
        ['label' => __('Messages'), 'route' => 'admin.messages.index', 'active' => 'admin.messages.*', 'icon' => 'mail', 'badge' => $unread],
        ['label' => __('Social links'), 'route' => 'admin.socials.index', 'active' => 'admin.socials.*', 'icon' => 'share'],
        ['label' => __('Settings'), 'route' => 'admin.settings.index', 'active' => 'admin.settings.*', 'icon' => 'gear'],
    ];
    $initial = mb_substr($me->name ?? 'A', 0, 1);
    $otherLocale = collect(locales())->keys()->first(fn ($l) => $l !== $locale);
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $rtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <meta name="upload-chunk-size" content="{{ \App\Support\Uploads::chunkSize() }}">
    <meta name="media-upload-url" content="{{ route('admin.media.upload') }}">
    <meta name="media-list-url" content="{{ route('admin.media.list') }}">
    <meta name="media-embed-url" content="{{ route('admin.media.embed') }}">
    <title>@yield('title', __('Dashboard')) — {{ setting()->text('general.site_name') }}</title>

    @if ($favicon = setting()->media('branding.favicon'))
        <link rel="icon" href="{{ $favicon->url(null) }}">
    @endif

    @include('partials.theme-script', ['key' => 'admin-theme', 'default' => setting('appearance.admin_theme', 'light')])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">

    @stack('vite')
    @vite(['resources/css/dashboard.css', 'resources/js/dashboard.js'])
    @stack('styles')
</head>
<body class="antialiased @yield('body-class')" x-data="{ sidebarOpen: false }">

<div class="flex min-h-screen">

    <div x-cloak x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
         class="fixed inset-0 z-30 bg-secondary-900/60 lg:hidden"></div>

    {{-- ===================== Sidebar ===================== --}}
    <aside class="fixed lg:sticky inset-y-0 start-0 top-0 z-40 w-[14.4rem] shrink-0 h-screen flex flex-col
                  bg-sidebar text-white transition-transform duration-300 lg:translate-x-0 @yield('sidebar-class')"
           :class="sidebarOpen ? 'translate-x-0' : '{{ $rtl ? 'translate-x-full' : '-translate-x-full' }}'">

        <a href="{{ route('admin.dashboard') }}" class="h-[68px] shrink-0 flex items-center gap-3 px-5">
            @if ($logo = setting()->media('branding.logo_dark') ?? setting()->media('branding.logo'))
                <img src="{{ $logo->url(480) }}" alt="" class="h-9 w-auto max-w-[9rem] object-contain drop-shadow-[0_0_8px_rgba(196,154,25,0.55)]">
            @else
                <span class="grid place-items-center w-9 h-9 shrink-0 rounded-xl bg-accent-500 text-secondary-900 text-sm font-bold tracking-tight" dir="ltr">{{ site_initials() }}</span>
                <span class="font-bold truncate">{{ setting()->text('general.site_name') }}</span>
            @endif
        </a>

        <nav class="flex-1 overflow-y-auto px-3 py-2 space-y-1 scrollbar-thin">
            @foreach ($nav as $item)
                @php $active = request()->routeIs(...(array) $item['active']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="group flex items-center gap-3 rounded-full px-3.5 h-11 text-sm border-s-2 transition
                          {{ $active ? 'bg-sidebar-active border-sidebar-ring text-white font-bold' : 'border-transparent text-white/70 hover:bg-white/5 hover:text-white' }}">
                    <x-icon :name="$item['icon']" :size="19" class="shrink-0 {{ $active ? 'text-accent-500' : 'text-white/55 group-hover:text-white/90' }}" />
                    <span class="flex-1 truncate">{{ $item['label'] }}</span>
                    @if (! empty($item['badge']))
                        <span class="min-w-5 h-5 px-1.5 grid place-items-center rounded-full bg-accent-500 text-secondary-900 text-[11px] font-bold">{{ $item['badge'] }}</span>
                    @endif
                </a>
            @endforeach
        </nav>

        <div class="shrink-0 px-3 pb-3">
            <a href="{{ lroute('home', [], $locale) }}" target="_blank"
               class="flex items-center gap-3 rounded-full px-3.5 h-11 text-sm text-white/70 hover:bg-white/5 hover:text-white transition">
                <x-icon name="external" :size="18" class="text-white/55" />
                {{ __('View website') }}
            </a>
        </div>

        <div class="shrink-0 border-t border-white/10 px-4 pt-4 pb-5">
            <div class="flex items-center gap-3">
                @if ($me->avatarUrl())
                    <img src="{{ $me->avatarUrl() }}" alt="" class="w-9 h-9 shrink-0 rounded-full object-cover">
                @else
                    <span class="grid place-items-center w-9 h-9 shrink-0 rounded-full bg-accent-500 text-secondary-900 font-bold text-sm">{{ $initial }}</span>
                @endif
                <div class="leading-tight min-w-0 flex-1">
                    <p class="text-sm font-bold truncate">{{ $me->name }}</p>
                    <p class="text-[11px] text-white/45 truncate">{{ __('Administrator') }}</p>
                </div>
                <a href="{{ route('admin.profile.edit') }}" class="text-white/40 hover:text-white/80 transition" aria-label="{{ __('Profile') }}">
                    <x-icon name="user" :size="18" />
                </a>
            </div>
        </div>
    </aside>

    {{-- ===================== Content ===================== --}}
    <div class="flex-1 min-w-0 flex flex-col">
        <header class="h-[68px] shrink-0 bg-surface/90 backdrop-blur border-b border-line flex items-center gap-3 px-4 sm:px-6 sticky top-0 z-20">
            <button type="button" @click="sidebarOpen = true" class="lg:hidden btn-icon" aria-label="{{ __('Menu') }}">
                <x-icon name="menu" :size="20" />
            </button>

            <div class="min-w-0">
                @hasSection('breadcrumb')
                    <div class="text-[11px] text-subtle truncate">@yield('breadcrumb')</div>
                @endif
                <h1 class="text-lg font-bold text-ink truncate">@yield('page-title', __('Dashboard'))</h1>
            </div>

            <div class="ms-auto flex items-center gap-1.5 sm:gap-2">
                @yield('header-actions')

                {{-- Theme --}}
                <div x-data="themeToggle" class="relative">
                    <button type="button" @click="cycle()" class="btn-icon" :title="pref">
                        <span x-show="pref === 'light'"><x-icon name="sun" /></span>
                        <span x-show="pref === 'dark'" x-cloak><x-icon name="moon" /></span>
                        <span x-show="pref === 'system'" x-cloak><x-icon name="monitor" /></span>
                    </button>
                </div>

                {{-- Language --}}
                <form method="POST" action="{{ route('admin.locale', $otherLocale) }}">
                    @csrf
                    <button type="submit" class="btn-icon w-auto px-3 text-xs font-bold" title="{{ locales()[$otherLocale]['name'] }}">
                        {{ locales()[$otherLocale]['native'] }}
                    </button>
                </form>

                {{-- Profile --}}
                <div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
                    <button type="button" @click="open = ! open"
                            class="flex items-center gap-2 rounded-full border border-line bg-surface ps-1 pe-2 py-1 hover:bg-canvas transition">
                        @if ($me->avatarUrl())
                            <img src="{{ $me->avatarUrl() }}" alt="" class="w-8 h-8 rounded-full object-cover">
                        @else
                            <span class="grid place-items-center w-8 h-8 rounded-full bg-accent-500 text-secondary-900 font-bold text-sm">{{ $initial }}</span>
                        @endif
                        <x-icon name="chevron-down" :size="15" class="text-subtle transition-transform" ::class="open && 'rotate-180'" />
                    </button>
                    <div x-cloak x-show="open" @click.outside="open = false" x-transition.opacity
                         class="absolute top-[calc(100%+8px)] end-0 w-64 rounded-2xl bg-raised border border-line shadow-xl shadow-secondary-900/10 overflow-hidden">
                        <div class="px-4 py-3.5 border-b border-line">
                            <p class="text-sm font-bold truncate">{{ $me->name }}</p>
                            <p class="text-xs text-muted truncate">{{ $me->email }}</p>
                        </div>
                        <a href="{{ route('admin.profile.edit') }}" class="flex items-center gap-3 px-4 h-11 text-sm hover:bg-canvas transition">
                            <x-icon name="user" class="text-muted" /> {{ __('Profile') }}
                        </a>
                        <a href="{{ route('admin.profile.edit') }}#password" class="flex items-center gap-3 px-4 h-11 text-sm hover:bg-canvas transition border-b border-line">
                            <x-icon name="key" class="text-muted" /> {{ __('Change password') }}
                        </a>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-4 h-11 text-sm font-bold text-danger hover:bg-danger-soft transition">
                                <x-icon name="logout" /> {{ __('Log out') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 @yield('main-class', 'px-4 sm:px-6 pt-6 pb-10')">
            @yield('content')
        </main>
    </div>
</div>

@include('admin.partials.toasts')
@include('admin.partials.confirm')
@include('admin.partials.media-picker')

@stack('scripts')
</body>
</html>
@endif
