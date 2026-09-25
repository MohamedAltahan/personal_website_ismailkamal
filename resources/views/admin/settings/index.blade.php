@extends('layouts.dashboard')

@section('title', __('Settings'))
@section('page-title', __('Settings'))

@php
    $tabMeta = [
        'general' => ['icon' => 'user', 'label' => __('General'), 'hint' => __('Name, tagline & contact info')],
        'branding' => ['icon' => 'star', 'label' => __('Branding'), 'hint' => __('Logos, favicon, share image')],
        'appearance' => ['icon' => 'palette', 'label' => __('Appearance'), 'hint' => __('Colours, fonts, theme & effects')],
        'media' => ['icon' => 'image', 'label' => __('Images & media'), 'hint' => __('Quality, sizes, watermark')],
        'seo' => ['icon' => 'globe', 'label' => __('SEO'), 'hint' => __('Search engines & analytics')],
        'contact' => ['icon' => 'mail', 'label' => __('Contact form'), 'hint' => __('Email notifications')],
    ];
@endphp

@section('content')
<div class="grid lg:grid-cols-[16rem_1fr] gap-5 items-start">
    <nav class="card p-2 lg:sticky lg:top-[84px] flex lg:flex-col gap-1 overflow-x-auto">
        @foreach ($tabs as $key)
            <a href="{{ route('admin.settings.index', $key) }}"
               class="flex items-center gap-3 rounded-xl px-3 py-2.5 shrink-0 transition {{ $tab === $key ? 'bg-secondary-900 text-white dark:bg-accent-500 dark:text-secondary-900' : 'hover:bg-canvas' }}">
                <x-icon :name="$tabMeta[$key]['icon']" :size="18" class="{{ $tab === $key ? 'text-accent-500 dark:text-secondary-900' : 'text-muted' }}" />
                <span>
                    <span class="block text-sm font-bold">{{ $tabMeta[$key]['label'] }}</span>
                    <span class="hidden lg:block text-[11px] {{ $tab === $key ? 'opacity-60' : 'text-subtle' }}">{{ $tabMeta[$key]['hint'] }}</span>
                </span>
            </a>
        @endforeach
    </nav>

    <form method="POST" action="{{ route('admin.settings.update', $tab) }}" class="card">
        @csrf
        @method('PUT')
        <div class="px-6 py-4 border-b border-line">
            <h2 class="font-bold text-lg">{{ $tabMeta[$tab]['label'] }}</h2>
            <p class="text-sm text-muted">{{ $tabMeta[$tab]['hint'] }}</p>
        </div>

        <div class="p-6 space-y-5">
            @include('admin.settings.tabs.'.$tab)
        </div>

        <div class="px-6 py-4 border-t border-line flex justify-end sticky bottom-0 bg-surface/95 backdrop-blur rounded-b-card">
            <button class="btn-primary h-11 px-6"><x-icon name="save" :size="17" /> {{ __('Save changes') }}</button>
        </div>
    </form>
</div>
@endsection
