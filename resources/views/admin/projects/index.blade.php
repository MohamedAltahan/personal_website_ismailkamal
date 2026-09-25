@extends('layouts.dashboard')

@section('title', __('Projects'))
@section('page-title', __('Projects'))

@section('header-actions')
    <a href="{{ route('admin.projects.create') }}" class="btn-primary h-10 hidden sm:inline-flex">
        <x-icon name="plus" :stroke="2.4" :size="17" /> {{ __('New project') }}
    </a>
@endsection

@php
    $statusTabs = [
        '' => [__('All'), $counts['all']],
        'active' => [__('Published'), $counts['active']],
        'inactive' => [__('Hidden'), $counts['inactive']],
        'featured' => [__('Featured'), $counts['featured']],
    ];
    $current = $filters['status'] ?? '';
@endphp

@section('content')
<div x-data="{
        async saveOrder() {
            const ids = [...$refs.list.querySelectorAll(':scope > [data-id]')].map(el => +el.dataset.id);
            try { const { data } = await http.post(@js(route('admin.projects.reorder')), { ids }); toast(data.message); }
            catch (e) { toast(errorMessage(e), 'error'); }
        },
     }">

    {{-- Toolbar --}}
    <div class="card p-3 mb-5 space-y-3">
        <div class="flex flex-wrap items-center gap-2">
            <nav class="flex flex-wrap items-center gap-1 rounded-full bg-canvas p-1">
                @foreach ($statusTabs as $key => [$label, $count])
                    <a href="{{ route('admin.projects.index', array_filter(['status' => $key ?: null, 'category' => $filters['category'] ?? null, 'q' => $filters['q'] ?? null])) }}"
                       class="tab h-9 {{ $current === $key ? 'tab-active' : '' }}">
                        {{ $label }}
                        <span class="text-[11px] px-1.5 rounded-full {{ $current === $key ? 'bg-accent-500 text-secondary-900' : 'bg-surface text-subtle' }}">{{ $count }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="ms-auto flex items-center gap-1 rounded-full bg-canvas p-1">
                <a href="{{ request()->fullUrlWithQuery(['view' => 'grid']) }}" title="{{ __('Grid') }}"
                   class="grid place-items-center w-9 h-8 rounded-full {{ $view === 'grid' ? 'bg-surface text-ink shadow-sm' : 'text-muted hover:text-ink' }}"><x-icon name="gallery" :size="16" /></a>
                <a href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}" title="{{ __('List') }}"
                   class="grid place-items-center w-9 h-8 rounded-full {{ $view === 'list' ? 'bg-surface text-ink shadow-sm' : 'text-muted hover:text-ink' }}"><x-icon name="list" :size="16" /></a>
            </div>
            <a href="{{ route('admin.projects.create') }}" class="btn-primary h-10 sm:hidden"><x-icon name="plus" :stroke="2.4" :size="17" /> {{ __('New project') }}</a>
        </div>

        <form method="GET" class="flex flex-wrap items-center gap-2">
            @if ($current)<input type="hidden" name="status" value="{{ $current }}">@endif
            <div class="relative flex-1 min-w-52">
                <x-icon name="search" :size="16" class="absolute top-1/2 -translate-y-1/2 start-3.5 text-subtle" />
                <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="{{ __('Search by title or client…') }}" class="field ps-10 h-10 py-0">
            </div>
            <select name="category" class="field w-auto min-w-44 h-10 py-0" onchange="this.form.submit()">
                <option value="">{{ __('All categories') }}</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(($filters['category'] ?? null) == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <button class="btn-ghost h-10">{{ __('Search') }}</button>
            @if (array_filter($filters))
                <a href="{{ route('admin.projects.index') }}" class="btn h-10 text-muted hover:text-ink">{{ __('Reset') }}</a>
            @endif
        </form>
    </div>

    @if ($canReorder && $projects->count() > 1)
        <p class="flex items-center gap-2 text-xs text-muted mb-3">
            <x-icon name="drag" :size="14" /> {{ __('Drag cards to change their order on the site') }}
        </p>
    @endif

    @if ($projects->isEmpty())
        <div class="card py-20 text-center">
            <div class="grid place-items-center w-16 h-16 mx-auto rounded-full bg-accent-100 text-accent-800 dark:bg-accent-500/15 dark:text-accent-400 mb-4"><x-icon name="briefcase" :size="28" /></div>
            <p class="font-bold mb-1">{{ __('No projects found') }}</p>
            <p class="text-sm text-muted mb-6">{{ __('Create your first project and build its page with images, videos and text.') }}</p>
            <a href="{{ route('admin.projects.create') }}" class="btn-primary">{{ __('New project') }}</a>
        </div>
    @elseif ($view === 'grid')
        {{-- ============================ Grid ============================ --}}
        <div x-ref="list" @if ($canReorder) x-sort="saveOrder()" @endif
             class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-5">
            @foreach ($projects as $project)
                <article data-id="{{ $project->id }}" x-sort:item="{{ $project->id }}" id="project-{{ $project->id }}" class="card group flex flex-col">
                    <div class="relative aspect-[16/10] rounded-t-card overflow-hidden" style="background-color: {{ $project->cover?->color ?? '#1c1b47' }}">
                        <a href="{{ route('admin.projects.edit', $project) }}" class="absolute inset-0" aria-label="{{ __('Edit') }}">
                            @if ($project->cover)
                                <img src="{{ $project->cover->url(480) }}" alt="" loading="lazy" class="w-full h-full object-cover group-hover:scale-[1.03] transition duration-500">
                            @else
                                <span class="absolute inset-0 grid place-items-center text-white/40 text-xs gap-1">
                                    <span class="flex flex-col items-center gap-2"><x-icon name="image" :size="26" />{{ __('No cover') }}</span>
                                </span>
                            @endif
                        </a>
                        <div class="absolute top-3 start-3 flex gap-1.5 pointer-events-none">
                            @if ($project->is_featured)<span class="badge bg-accent-500 text-secondary-900"><x-icon name="star" :size="12" /> {{ __('Featured') }}</span>@endif
                            @if ($project->status !== 'active')<span class="badge bg-black/70 text-white"><x-icon name="eye-off" :size="12" /> {{ __('Hidden') }}</span>@endif
                        </div>
                        @if ($canReorder)
                            <span x-sort:handle title="{{ __('Drag to reorder') }}"
                                  class="absolute top-3 end-3 grid place-items-center w-8 h-8 rounded-full bg-black/60 text-white cursor-grab opacity-0 group-hover:opacity-100 transition">
                                <x-icon name="drag" :size="16" />
                            </span>
                        @endif
                    </div>

                    <div class="flex-1 flex flex-col p-4">
                        <a href="{{ route('admin.projects.edit', $project) }}" dir="auto"
                           class="font-bold leading-snug line-clamp-2 text-start hover:text-primary-600 dark:hover:text-accent-500">{{ $project->title ?: __('Untitled') }}</a>
                        <p class="mt-1.5 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-muted">
                            <span>{{ $project->category?->name ?? __('No category') }}</span>
                            @if ($project->year)<span class="text-subtle">•</span><span>{{ $project->year }}</span>@endif
                            <span class="text-subtle">•</span><span>{{ trans_choice(':count block|:count blocks', count($project->blocks ?? [])) }}</span>
                            @if ($project->views)<span class="text-subtle">•</span><span class="inline-flex items-center gap-1"><x-icon name="eye" :size="12" /> {{ number_format($project->views) }}</span>@endif
                        </p>

                        <div class="mt-auto pt-4 flex items-center gap-1.5">
                            <a href="{{ route('admin.projects.edit', $project) }}" class="btn-primary h-9 flex-1 px-3">
                                <x-icon name="edit" :size="15" /> {{ __('Edit') }}
                            </a>
                            @include('admin.projects._actions', ['project' => $project])
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        {{-- ============================ List ============================ --}}
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs text-muted border-b border-line bg-canvas/60">
                            @if ($canReorder)<th class="w-10"></th>@endif
                            <th class="text-start font-semibold px-4 py-3">{{ __('Project') }}</th>
                            <th class="text-start font-semibold px-4 py-3 hidden md:table-cell">{{ __('Category') }}</th>
                            <th class="text-start font-semibold px-4 py-3 hidden lg:table-cell">{{ __('Year') }}</th>
                            <th class="text-start font-semibold px-4 py-3 hidden lg:table-cell">{{ __('Views') }}</th>
                            <th class="text-start font-semibold px-4 py-3 hidden xl:table-cell">{{ __('Updated') }}</th>
                            <th class="text-end font-semibold px-4 py-3">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody x-ref="list" @if ($canReorder) x-sort="saveOrder()" @endif class="divide-y divide-line">
                        @foreach ($projects as $project)
                            <tr data-id="{{ $project->id }}" x-sort:item="{{ $project->id }}" id="project-{{ $project->id }}" class="bg-surface hover:bg-canvas/50">
                                @if ($canReorder)
                                    <td class="ps-3"><span x-sort:handle class="grid place-items-center w-8 h-8 text-subtle hover:text-ink cursor-grab"><x-icon name="drag" :size="16" /></span></td>
                                @endif
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.projects.edit', $project) }}" class="flex items-center gap-3 min-w-0">
                                        <span class="relative w-20 h-12 shrink-0 rounded-lg overflow-hidden" style="background-color: {{ $project->cover?->color ?? '#1c1b47' }}">
                                            @if ($project->cover)<img src="{{ $project->cover->url(480) }}" alt="" loading="lazy" class="w-full h-full object-cover">@endif
                                        </span>
                                        <span class="min-w-0">
                                            <span class="block font-bold truncate max-w-[22rem] hover:text-primary-600 dark:hover:text-accent-500" dir="auto">{{ $project->title ?: __('Untitled') }}</span>
                                            <span class="flex gap-1.5 mt-1">
                                                @if ($project->is_featured)<span class="badge bg-accent-500 text-secondary-900 py-0.5">{{ __('Featured') }}</span>@endif
                                                @if ($project->status !== 'active')<span class="badge bg-canvas text-muted py-0.5">{{ __('Hidden') }}</span>@endif
                                            </span>
                                        </span>
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-muted hidden md:table-cell">{{ $project->category?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-muted hidden lg:table-cell tabular-nums">{{ $project->year ?: '—' }}</td>
                                <td class="px-4 py-3 text-muted hidden lg:table-cell tabular-nums">{{ number_format($project->views) }}</td>
                                <td class="px-4 py-3 text-muted hidden xl:table-cell whitespace-nowrap">{{ $project->updated_at?->diffForHumans() }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.projects.edit', $project) }}" class="btn-primary h-9 px-3"><x-icon name="edit" :size="15" /> {{ __('Edit') }}</a>
                                        @include('admin.projects._actions', ['project' => $project])
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
