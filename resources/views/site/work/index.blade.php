@extends('layouts.site')

@section('title', $category?->name ?? __('Work'))
@section('description', $category ? ($category->description ?: __('Selected :name projects.', ['name' => $category->name])) : __('Selected projects in motion design, animation and visual storytelling.'))

@section('content')
    <header class="container-site pt-[calc(var(--header-h)+clamp(3rem,8vw,7rem))] pb-[clamp(2rem,4vw,3.5rem)]">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <h1 class="text-hero font-display" data-reveal>
                {{ $category?->name ?? __('Work') }}<sup class="text-[0.28em] align-super ms-2 text-brand font-sans">{{ $total }}</sup>
            </h1>
            @if ($category?->description)
                <p class="max-w-md text-lg text-mute" data-reveal>{{ $category->description }}</p>
            @endif
        </div>

        @if ($navCategories->count() > 1)
            <nav class="mt-10 flex gap-2 overflow-x-auto pb-2 -mx-1 px-1 [scrollbar-width:none]" data-reveal style="--reveal-delay:.1s" aria-label="{{ __('Categories') }}">
                <a href="{{ lroute('work.index') }}"
                   class="shrink-0 inline-flex items-center h-11 px-5 rounded-full border text-sm font-semibold transition-colors {{ ! $category ? 'bg-ink text-paper border-ink' : 'border-line hover:border-ink' }}">
                    {{ __('All') }}
                </a>
                @foreach ($navCategories as $navCategory)
                    <a href="{{ lroute('work.category', $navCategory->slug) }}"
                       class="shrink-0 inline-flex items-center h-11 px-5 rounded-full border text-sm font-semibold transition-colors {{ $category?->id === $navCategory->id ? 'bg-ink text-paper border-ink' : 'border-line hover:border-ink' }}">
                        {{ $navCategory->name }}
                    </a>
                @endforeach
            </nav>
        @endif

        @if ($category && $category->subCategories->count())
            <nav class="mt-3 flex flex-wrap gap-2">
                @foreach ($category->subCategories as $sub)
                    <a href="{{ request()->fullUrlWithQuery(['sub' => request('sub') == $sub->id ? null : $sub->id, 'page' => null]) }}"
                       class="inline-flex items-center h-9 px-4 rounded-full text-sm {{ request('sub') == $sub->id ? 'bg-brand text-brand-ink' : 'bg-paper-2 hover:bg-line' }}">
                        {{ $sub->name }}
                    </a>
                @endforeach
            </nav>
        @endif
    </header>

    <section class="container-site" x-data="loadMore(@js($projects->nextPageUrl()))">
        @if ($projects->count())
            <div x-ref="grid" class="contents">
                @include('site.partials.project-grid', ['projects' => $projects, 'offset' => $offset])
            </div>
            <div class="mt-16 text-center" x-show="next">
                <button type="button" @click="more()" :disabled="busy"
                        class="inline-flex items-center gap-3 h-14 px-8 rounded-full border border-ink font-semibold hover:bg-ink hover:text-paper transition-colors disabled:opacity-50">
                    <span x-show="!busy">{{ __('Load more work') }}</span>
                    <span x-show="busy" x-cloak>{{ __('Loading…') }}</span>
                </button>
            </div>
        @else
            <p class="py-24 text-center text-mute text-lg">{{ __('No projects here yet — check back soon.') }}</p>
        @endif
    </section>
@endsection
