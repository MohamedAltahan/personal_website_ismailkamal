@extends('layouts.site')

@php
    $locale = app()->getLocale();
    $seo = $project->seo ?? [];
    $blocks = $project->blocks ?? [];
    $startsWithHero = ($blocks[0]['type'] ?? null) === 'hero';
    $facts = array_filter([
        __('Client') => $project->client,
        __('Category') => $project->category?->name,
        __('Year') => $project->year,
    ]);
@endphp

@section('title', ($seo['title'][$locale] ?? null) ?: $project->title)
@section('description', ($seo['description'][$locale] ?? null) ?: ($project->excerpt ?: $project->title))
@section('og_type', 'article')
@section('og_image', $project->cover?->url(1600) ?? '')

@section('content')
    @unless ($startsWithHero)
        <header class="container-site pt-[calc(var(--header-h)+clamp(2.5rem,7vw,6rem))]">
            <a href="{{ $project->category ? lroute('work.category', $project->category->slug) : lroute('work.index') }}"
               class="inline-flex items-center gap-2 text-sm text-mute hover:text-ink mb-8" data-reveal>
                <x-icon name="arrow-end" :size="16" class="ltr:-scale-x-100" />
                {{ $project->category?->name ?? __('Work') }}
            </a>
            <h1 class="text-display font-display max-w-6xl" data-reveal style="--reveal-delay:.06s">{{ $project->title }}</h1>

            <div class="mt-10 grid md:grid-cols-12 gap-8 border-t border-line pt-8" data-reveal style="--reveal-delay:.12s">
                @if ($project->excerpt)
                    <p class="md:col-span-7 text-lg sm:text-xl leading-relaxed text-mute">{{ $project->excerpt }}</p>
                @endif
                <dl class="md:col-span-5 {{ $project->excerpt ? '' : 'md:col-start-1' }} grid grid-cols-3 gap-6">
                    @foreach ($facts as $label => $value)
                        <div>
                            <dt class="text-xs uppercase tracking-widest text-mute mb-1.5">{{ $label }}</dt>
                            <dd class="font-semibold">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
            @if (! empty($project->tags))
                <div class="mt-6 flex flex-wrap gap-2">
                    @foreach ($project->tags as $tag)
                        <span class="px-3 h-8 inline-flex items-center rounded-full bg-paper-2 text-sm">{{ $tag }}</span>
                    @endforeach
                </div>
            @endif
        </header>

        {{-- Cover only when the page doesn't start with its own visual. --}}
        @if ($project->cover && ! in_array($blocks[0]['type'] ?? null, ['image', 'video', 'gallery', 'embed'], true))
            <div class="container-site mt-[clamp(2rem,5vw,4rem)]" data-reveal>
                <x-media :media="$project->cover" fit="natural" eager class="rounded-[clamp(0.75rem,2vw,1.5rem)]" />
            </div>
        @endif
    @endunless

    <div class="{{ $startsWithHero ? '' : 'mt-[clamp(1rem,3vw,2rem)]' }}">
        <x-blocks :blocks="$blocks" :media="$media" :context="$project" />
    </div>

    @if ($next)
        <section class="container-site mt-[clamp(4rem,10vw,9rem)]">
            <a href="{{ $next->url() }}" class="group block border-t border-line pt-10" data-cursor="{{ __('Next') }}">
                <p class="text-sm text-mute mb-4 flex items-center gap-2">{{ __('Next project') }} <x-icon name="arrow-end" :size="16" class="rtl:-scale-x-100" /></p>
                <div class="grid md:grid-cols-12 gap-8 items-end">
                    <h2 class="md:col-span-7 text-display font-display group-hover:text-brand transition-colors duration-500">{{ $next->title }}</h2>
                    @if ($next->cover)
                        <div class="md:col-span-5 overflow-hidden rounded-[clamp(0.5rem,1vw,0.9rem)]">
                            <x-media :media="$next->cover" ratio="16/10" imgClass="transition-transform duration-[1.2s] group-hover:scale-105" sizes="(min-width: 768px) 40vw, 100vw" />
                        </div>
                    @endif
                </div>
            </a>
        </section>
    @endif

    @push('head')
        <script type="application/ld+json">{!! json_encode(array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'CreativeWork',
            'name' => $project->title,
            'description' => $project->excerpt,
            'image' => $project->cover ? url($project->cover->url(1600)) : null,
            'dateCreated' => $project->year,
            'genre' => $project->category?->name,
            'creator' => ['@type' => 'Person', 'name' => setting()->text('general.site_name')],
            'url' => $project->url(),
        ]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    @endpush
@endsection
