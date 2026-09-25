@php
    $limit = max(1, min(24, (int) $data['limit']));
    $query = \App\Models\Project::published()->with(['cover', 'hover', 'category']);
    $query = match ($data['source']) {
        'featured' => $query->orderByDesc('is_featured')->ordered(),
        'category' => $query->where('category_id', $data['category_id'])->ordered(),
        default => $query->ordered(),
    };
    $projects = $query->take($limit)->get();
@endphp
@if ($projects->count())
    <div class="{{ $wrap }}">
        @if (tr($data['title']) || $data['show_all_link'])
            <div class="flex items-end justify-between gap-6 mb-[clamp(2rem,4vw,3.5rem)]" {{ $reveal }}>
                @if (tr($data['title']))
                    <h2 class="text-[1.75rem] sm:text-3xl lg:text-[2.5rem] font-display font-bold leading-tight">{{ tr($data['title']) }}</h2>
                @endif
                @if ($data['show_all_link'])
                    <a href="{{ lroute('work.index') }}" class="shrink-0 inline-flex items-center gap-2 font-semibold link-underline">
                        {{ __('All work') }} <x-icon name="arrow-end" :size="18" class="rtl:-scale-x-100" />
                    </a>
                @endif
            </div>
        @endif
        @include('site.partials.project-grid', ['projects' => $projects, 'layout' => $data['layout']])
    </div>
@endif
