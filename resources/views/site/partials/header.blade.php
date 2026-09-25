@php
    $links = [
        ['label' => __('Work'), 'url' => lroute('work.index'), 'active' => request()->routeIs('work.*')],
        ['label' => __('About'), 'url' => lroute('about'), 'active' => request()->routeIs('about')],
    ];
    foreach ($navPages ?? [] as $navPage) {
        $links[] = ['label' => $navPage->title, 'url' => $navPage->url(), 'active' => request()->is('*/p/'.$navPage->slug)];
    }
    $links[] = ['label' => __('Contact'), 'url' => lroute('contact'), 'active' => request()->routeIs('contact')];
    $otherLocale = collect(locales())->keys()->first(fn ($l) => $l !== app()->getLocale());
@endphp

<header x-data="siteHeader" class="fixed inset-x-0 top-0 z-50 transition-transform duration-500"
        :class="hidden && '-translate-y-full'">
    <div
         :class="scrolled || menu ? 'bg-paper/85 backdrop-blur-xl border-b border-line' : 'bg-transparent border-b border-transparent'">
        <div class="container-site h-[var(--header-h)] flex items-center gap-6">
            <a href="{{ lroute('home') }}" class="relative z-10 shrink-0" aria-label="{{ setting()->text('general.site_name') }}">
                <x-site.logo class="h-8 sm:h-9" />
            </a>

            <nav class="hidden md:flex items-center gap-7 ms-auto text-[15px]">
                @foreach ($links as $link)
                    <a href="{{ $link['url'] }}" class="relative link-underline py-1 {{ $link['active'] ? 'text-ink font-semibold' : 'text-mute hover:text-ink' }}">
                        {{ $link['label'] }}
                        @if ($link['active'])<span class="absolute -top-1 -end-2 w-1.5 h-1.5 rounded-full bg-brand"></span>@endif
                    </a>
                @endforeach
            </nav>

            <div class="flex items-center gap-1.5 ms-auto md:ms-0">
                <a href="{{ locale_url($otherLocale) }}" hreflang="{{ $otherLocale }}" lang="{{ $otherLocale }}"
                   class="grid place-items-center h-10 min-w-10 px-2 rounded-full text-sm font-semibold hover:bg-ink/5 transition">
                    {{ locales()[$otherLocale]['native'] }}
                </a>

                <button type="button" x-data="themeToggle" @click="cycle()" class="grid place-items-center w-10 h-10 rounded-full hover:bg-ink/5 transition"
                        :aria-label="pref">
                    <span x-show="pref === 'light'"><x-icon name="sun" :size="18" /></span>
                    <span x-show="pref === 'dark'" x-cloak><x-icon name="moon" :size="18" /></span>
                    <span x-show="pref === 'system'" x-cloak><x-icon name="monitor" :size="18" /></span>
                </button>

                <a href="{{ lroute('contact') }}" class="hidden lg:inline-flex items-center gap-2 h-10 ps-4 pe-3 ms-2 rounded-full bg-ink text-paper text-sm font-semibold hover:bg-brand hover:text-brand-ink transition-colors">
                    {{ __("Let's talk") }}
                    <x-icon name="arrow-up-end" :size="16" class="rtl:-scale-x-100" />
                </a>

                <button type="button" @click="menu = !menu" class="md:hidden relative z-10 grid place-items-center w-10 h-10 rounded-full hover:bg-ink/5" :aria-expanded="menu" aria-label="{{ __('Menu') }}">
                    <span class="relative block w-5 h-3">
                        <span class="absolute inset-x-0 top-0 h-0.5 bg-current rounded transition" :class="menu && 'top-1.5 rotate-45'"></span>
                        <span class="absolute inset-x-0 bottom-0 h-0.5 bg-current rounded transition" :class="menu && 'bottom-[0.3rem] -rotate-45'"></span>
                    </span>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div x-cloak x-show="menu" x-transition.opacity.duration.300ms @keydown.escape.window="menu = false"
         class="md:hidden fixed inset-0 top-[var(--header-h)] bg-paper h-[calc(100dvh-var(--header-h))] overflow-y-auto">
        <nav class="container-site pt-8 pb-12 flex flex-col">
            @foreach ($links as $i => $link)
                <a href="{{ $link['url'] }}" class="font-display text-4xl font-bold py-3 border-b border-line flex items-center justify-between {{ $link['active'] ? 'text-ink' : 'text-mute' }}"
                   x-show="menu" x-transition:enter="transition duration-500" x-transition:enter-start="opacity-0 translate-y-4" style="transition-delay: {{ $i * 60 }}ms">
                    {{ $link['label'] }}
                    <x-icon name="arrow-up-end" :size="28" class="text-brand rtl:-scale-x-100" />
                </a>
            @endforeach
            @if (($navCategories ?? collect())->count())
                <p class="mt-10 mb-3 text-xs uppercase tracking-widest text-mute">{{ __('Categories') }}</p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($navCategories as $category)
                        <a href="{{ lroute('work.category', $category->slug) }}" class="px-4 h-10 inline-flex items-center rounded-full border border-line text-sm">{{ $category->name }}</a>
                    @endforeach
                </div>
            @endif
            @include('site.partials.socials', ['class' => 'mt-10'])
        </nav>
    </div>
</header>
