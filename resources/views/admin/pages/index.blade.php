@extends('layouts.dashboard')

@section('title', __('Pages'))
@section('page-title', __('Pages'))

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
        <div>
            <h2 class="text-xl font-bold">{{ __('Pages') }}</h2>
            <p class="text-sm text-muted">{{ __('Home, About and any extra page — all built with the same flexible blocks.') }}</p>
        </div>
        <a href="{{ route('admin.pages.create') }}" class="btn-primary h-11"><x-icon name="plus" :stroke="2.4" /> {{ __('New page') }}</a>
    </div>

    <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach ($pages as $page)
            <div id="page-{{ $page->id }}" class="card p-5 flex flex-col">
                <div class="flex items-start gap-3 mb-4">
                    <span class="grid place-items-center w-11 h-11 rounded-xl {{ $page->is_system ? 'bg-accent-100 text-accent-800 dark:bg-accent-500/15 dark:text-accent-400' : 'bg-brand-soft text-primary-700 dark:text-primary-300' }}">
                        <x-icon :name="$page->slug === 'home' ? 'hero' : ($page->slug === 'about' ? 'user' : 'file')" :size="20" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="font-bold truncate">{{ $page->title }}</p>
                        <p class="text-xs text-muted" dir="ltr">{{ parse_url($page->url(), PHP_URL_PATH) }}</p>
                    </div>
                    @if ($page->status !== 'active')<span class="badge bg-canvas text-muted">{{ __('Hidden') }}</span>@endif
                    @if ($page->in_menu)<span class="badge bg-info-soft text-info">{{ __('In menu') }}</span>@endif
                </div>
                <p class="text-sm text-muted mb-5">{{ trans_choice(':count block|:count blocks', count($page->blocks ?? [])) }} · {{ __('Updated :time', ['time' => $page->updated_at?->diffForHumans()]) }}</p>
                <div class="mt-auto flex items-center gap-2">
                    <a href="{{ route('admin.pages.edit', $page) }}" class="btn-primary flex-1"><x-icon name="edit" :size="16" /> {{ __('Edit page') }}</a>
                    <a href="{{ $page->url() }}" target="_blank" class="btn-ghost px-3" title="{{ __('View on site') }}"><x-icon name="external" :size="16" /></a>
                    @unless ($page->is_system)
                        <button type="button" class="btn-ghost px-3 hover:text-danger"
                                onclick="deleteResource(@js(route('admin.pages.destroy', $page)), { title: @js(__('Delete this page?')), el: document.getElementById('page-{{ $page->id }}') })">
                            <x-icon name="trash" :size="16" />
                        </button>
                    @endunless
                </div>
            </div>
        @endforeach
    </div>
@endsection
