@extends('layouts.dashboard')

@section('title', __('Overview'))
@section('page-title', __('Overview'))

@php
    $bytes = $stats['storage'];
    $storage = $bytes > 1024 ** 3 ? round($bytes / 1024 ** 3, 1).' GB' : round($bytes / 1024 ** 2).' MB';
    $cards = [
        ['label' => __('Projects'), 'value' => $stats['projects'], 'hint' => __(':n published', ['n' => $stats['published']]), 'icon' => 'briefcase', 'tone' => 'bg-accent-100 text-accent-800 dark:bg-accent-500/15 dark:text-accent-400'],
        ['label' => __('Images'), 'value' => $stats['images'], 'hint' => $storage.' '.__('total'), 'icon' => 'image', 'tone' => 'bg-info-soft text-info dark:text-primary-300'],
        ['label' => __('Videos'), 'value' => $stats['videos'], 'hint' => __('Uploads & embeds'), 'icon' => 'video', 'tone' => 'bg-brand-soft text-primary-700 dark:text-primary-300'],
        ['label' => __('Messages'), 'value' => $stats['messages'], 'hint' => __(':n unread', ['n' => $stats['unread']]), 'icon' => 'mail', 'tone' => 'bg-success-soft text-success'],
    ];
@endphp

@section('content')
    {{-- Welcome --}}
    <div class="relative overflow-hidden rounded-2xl bg-secondary-900 text-white px-6 sm:px-8 py-7">
        <div class="absolute -top-16 -start-10 w-56 h-56 rounded-full bg-white/[0.04]"></div>
        <div class="absolute -bottom-24 start-48 w-64 h-64 rounded-full bg-accent-500/[0.06]"></div>
        <div class="relative flex flex-wrap items-center gap-5 justify-between">
            <div>
                <p class="text-[11px] text-white/45 mb-1.5">{{ now()->translatedFormat('l، j F Y') }}</p>
                <h2 class="text-xl sm:text-[26px] font-bold mb-2">{{ __('Hello, :name', ['name' => auth()->user()->name]) }} 👋</h2>
                <p class="text-sm text-white/65">
                    @if ($stats['unread'])
                        {!! __('You have :n new messages waiting.', ['n' => '<span class="text-accent-500 font-bold">'.$stats['unread'].'</span>']) !!}
                    @else
                        {{ __('Everything is up to date. Time to add something new?') }}
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.projects.create') }}" class="btn-gold h-11">
                    <x-icon name="plus" :stroke="2.4" /> {{ __('New project') }}
                </a>
                <a href="{{ route('admin.media.index') }}" class="btn h-11 border border-white/25 hover:bg-white/10 text-white">
                    <x-icon name="upload" /> {{ __('Upload media') }}
                </a>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mt-5">
        @foreach ($cards as $card)
            <div class="card p-5">
                <div class="flex items-start justify-between mb-4">
                    <span class="grid place-items-center w-11 h-11 rounded-xl {{ $card['tone'] }}"><x-icon :name="$card['icon']" :size="21" /></span>
                </div>
                <p class="text-3xl font-bold tabular-nums">{{ number_format($card['value']) }}</p>
                <p class="text-sm text-muted mt-1">{{ $card['label'] }}</p>
                <p class="text-xs text-subtle mt-0.5">{{ $card['hint'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid xl:grid-cols-3 gap-4 mt-5">
        <div class="card p-5 xl:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold">{{ __('Messages — last 12 months') }}</h3>
                <a href="{{ route('admin.messages.index') }}" class="text-xs text-primary-600 dark:text-accent-500 hover:underline">{{ __('View all') }}</a>
            </div>
            @if (array_sum($messagesChart['values']))
                <div class="h-64"><canvas x-data="chart({ type: 'line', labels: @js($messagesChart['labels']), values: @js($messagesChart['values']) })"></canvas></div>
            @else
                <div class="h-64 grid place-items-center text-center">
                    <div>
                        <div class="grid place-items-center w-14 h-14 mx-auto rounded-full bg-canvas text-subtle mb-3"><x-icon name="chart" :size="24" /></div>
                        <p class="text-sm font-bold">{{ __('No messages in the last 12 months') }}</p>
                        <p class="text-xs text-muted mt-1">{{ __('Messages from the contact page will be charted here.') }}</p>
                    </div>
                </div>
            @endif
        </div>
        <div class="card p-5">
            <h3 class="font-bold mb-4">{{ __('Projects by category') }}</h3>
            <div class="h-64"><canvas x-data="chart({ type: 'doughnut', labels: @js($categoriesChart['labels']), values: @js($categoriesChart['values']) })"></canvas></div>
        </div>
    </div>

    <div class="grid xl:grid-cols-3 gap-4 mt-5">
        <div class="card xl:col-span-2 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-line">
                <h3 class="font-bold">{{ __('Recently updated projects') }}</h3>
                <a href="{{ route('admin.projects.index') }}" class="text-xs text-primary-600 dark:text-accent-500 hover:underline">{{ __('All projects') }}</a>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 p-5">
                @forelse ($latestProjects as $project)
                    <a href="{{ route('admin.projects.edit', $project) }}" class="group block">
                        <div class="aspect-[4/3] rounded-xl overflow-hidden bg-canvas mb-2.5" style="background-color: {{ $project->cover?->color }}">
                            @if ($project->cover)
                                <img src="{{ $project->cover->url(480) }}" alt="" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @endif
                        </div>
                        <p class="text-sm font-bold truncate text-start group-hover:text-primary-600 dark:group-hover:text-accent-500" dir="auto">{{ $project->title }}</p>
                        <p class="text-xs text-muted">{{ $project->category?->name ?? '—' }} · {{ $project->updated_at->diffForHumans() }}</p>
                    </a>
                @empty
                    <p class="text-sm text-muted col-span-full py-10 text-center">{{ __('No projects yet.') }}</p>
                @endforelse
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-line">
                <h3 class="font-bold">{{ __('Latest messages') }}</h3>
                <a href="{{ route('admin.messages.index') }}" class="text-xs text-primary-600 dark:text-accent-500 hover:underline">{{ __('Inbox') }}</a>
            </div>
            <div class="divide-y divide-line">
                @forelse ($latestMessages as $message)
                    <a href="{{ route('admin.messages.show', $message) }}" class="flex items-start gap-3 px-5 py-3.5 hover:bg-canvas transition">
                        <span class="grid place-items-center w-9 h-9 shrink-0 rounded-full bg-secondary-900 text-accent-500 text-sm font-bold">{{ mb_substr($message->name, 0, 1) }}</span>
                        <span class="min-w-0 flex-1">
                            <span class="flex items-center gap-2">
                                <span class="text-sm font-bold truncate">{{ $message->name }}</span>
                                @unless ($message->read_at)<span class="w-2 h-2 rounded-full bg-accent-500 shrink-0"></span>@endunless
                            </span>
                            <span class="block text-xs text-muted truncate">{{ \Illuminate\Support\Str::limit($message->message, 70) }}</span>
                        </span>
                        <span class="text-[11px] text-subtle shrink-0">{{ $message->created_at?->diffForHumans(short: true) }}</span>
                    </a>
                @empty
                    <p class="text-sm text-muted text-center py-12">{{ __('No messages yet.') }}</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid xl:grid-cols-3 gap-4 mt-5">
        {{-- Most viewed --}}
        <div class="card overflow-hidden xl:col-span-2">
            <div class="flex items-center justify-between px-5 py-4 border-b border-line">
                <h3 class="font-bold">{{ __('Most viewed projects') }}</h3>
                <span class="text-xs text-muted">{{ __(':n total views', ['n' => number_format($stats['views'])]) }}</span>
            </div>
            @php $maxViews = max(1, (int) $topProjects->max('views')); @endphp
            <div class="divide-y divide-line">
                @forelse ($topProjects as $i => $project)
                    <a href="{{ route('admin.projects.edit', $project) }}" class="flex items-center gap-4 px-5 py-3 hover:bg-canvas transition">
                        <span class="w-5 text-center text-sm font-bold text-subtle tabular-nums">{{ $i + 1 }}</span>
                        <span class="w-14 h-10 shrink-0 rounded-lg overflow-hidden" style="background-color: {{ $project->cover?->color ?? '#1c1b47' }}">
                            @if ($project->cover)<img src="{{ $project->cover->url(480) }}" alt="" class="w-full h-full object-cover">@endif
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-sm font-bold truncate text-start" dir="auto">{{ $project->title }}</span>
                            <span class="block h-1.5 mt-2 rounded-full bg-canvas overflow-hidden">
                                <span class="block h-full rounded-full bg-accent-500" style="width: {{ round($project->views / $maxViews * 100) }}%"></span>
                            </span>
                        </span>
                        <span class="text-sm font-bold tabular-nums w-14 text-end">{{ number_format($project->views) }}</span>
                    </a>
                @empty
                    <p class="text-sm text-muted text-center py-12">{{ __('No projects yet.') }}</p>
                @endforelse
            </div>
        </div>

        {{-- Quick actions --}}
        <div class="card p-5">
            <h3 class="font-bold mb-4">{{ __('Quick actions') }}</h3>
            <div class="grid grid-cols-2 gap-3">
                @foreach ([
                    ['route' => 'admin.projects.create', 'icon' => 'plus', 'label' => __('New project')],
                    ['route' => 'admin.media.index', 'icon' => 'upload', 'label' => __('Upload media')],
                    ['route' => 'admin.pages.index', 'icon' => 'file', 'label' => __('Edit pages')],
                    ['route' => 'admin.settings.index', 'icon' => 'palette', 'label' => __('Colours & fonts'), 'params' => 'appearance'],
                    ['route' => 'admin.settings.index', 'icon' => 'image', 'label' => __('Image quality'), 'params' => 'media'],
                    ['route' => 'admin.socials.index', 'icon' => 'share', 'label' => __('Social links')],
                ] as $action)
                    <a href="{{ route($action['route'], $action['params'] ?? []) }}"
                       class="flex flex-col items-center justify-center gap-2 h-24 rounded-xl border border-line hover:border-accent-500 hover:bg-accent-500/5 text-center transition">
                        <span class="grid place-items-center w-9 h-9 rounded-full bg-canvas text-muted"><x-icon :name="$action['icon']" :size="17" /></span>
                        <span class="text-xs font-bold">{{ $action['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endsection
