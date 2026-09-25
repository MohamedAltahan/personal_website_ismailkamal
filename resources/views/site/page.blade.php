@extends('layouts.site')

@section('title', $page->slug === \App\Models\Page::HOME ? '' : ($page->seo['title'][app()->getLocale()] ?? null ?: $page->title))
@section('description', $page->seo['description'][app()->getLocale()] ?? '')

@php
    $first = $page->blocks[0]['type'] ?? null;
@endphp

@section('content')
    {{-- Pages that don't open with a hero get a simple title header. --}}
    @if ($first !== 'hero' && $first !== 'heading')
        <header class="container-site pt-[calc(var(--header-h)+clamp(3rem,8vw,7rem))] pb-8">
            <h1 class="text-display font-display" data-reveal>{{ $page->title }}</h1>
        </header>
    @elseif ($first === 'heading')
        <div class="pt-[var(--header-h)]"></div>
    @endif

    <x-blocks :blocks="$page->blocks ?? []" :media="$media" :context="$page" />

    @if ($page->slug === \App\Models\Page::HOME)
        @push('head')
            <script type="application/ld+json">{!! json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'Person',
                'name' => setting()->text('general.site_name'),
                'jobTitle' => setting()->text('general.tagline'),
                'url' => url('/'),
                'email' => setting('general.contact_email'),
                'sameAs' => ($socials ?? collect())->pluck('link')->values(),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
        @endpush
    @endif
@endsection
