{{--
    Project cards.
    layout: editorial (asymmetric 12-col rhythm) | grid (uniform 3 cols) | masonry
    $offset keeps the editorial rhythm continuous across "load more" pages.
--}}
@php
    $layout = $layout ?? setting('appearance.grid_style', 'editorial');
    $offset = $offset ?? 0;
    // Editorial rhythm: [span, aspect]
    $pattern = [
        ['md:col-span-7', '4/3'], ['md:col-span-5', '4/5'],
        ['md:col-span-5', '4/5'], ['md:col-span-7', '4/3'],
        ['md:col-span-12', '21/9'],
    ];
@endphp
<div data-grid
     class="{{ match ($layout) {
         'grid' => 'grid sm:grid-cols-2 lg:grid-cols-3 gap-x-[clamp(1rem,2vw,2rem)] gap-y-[clamp(2.5rem,5vw,4.5rem)]',
         'masonry' => 'columns-1 sm:columns-2 lg:columns-3 gap-[clamp(1rem,2vw,2rem)]',
         default => 'grid md:grid-cols-12 gap-x-[clamp(1rem,2.5vw,2.5rem)] gap-y-[clamp(2.5rem,6vw,6rem)]',
     } }}">
    @foreach ($projects as $i => $project)
        @php
            [$span, $aspect] = $layout === 'editorial' ? $pattern[($i + $offset) % count($pattern)] : ['', $layout === 'grid' ? '4/3' : null];
            $cover = $project->cover;
            $hover = $project->hover;
            if ($layout === 'masonry') {
                $aspect = $cover?->width ? $cover->width.'/'.$cover->height : '4/3';
            }
        @endphp
        <article class="{{ $span }} {{ $layout === 'masonry' ? 'break-inside-avoid mb-[clamp(2rem,4vw,3.5rem)]' : '' }}" data-reveal>
            <a href="{{ $project->url() }}" class="group block" data-cursor="{{ __('View') }}" @if ($hover?->isVideo()) data-hover-video @endif>
                <div class="relative overflow-hidden rounded-[clamp(0.5rem,1vw,0.9rem)] bg-paper-2" style="aspect-ratio: {{ $aspect }}; background-color: {{ $cover?->color }}">
                    @if ($cover)
                        <x-media :media="$cover" class="absolute inset-0 w-full h-full" imgClass="transition-transform duration-[1.2s] ease-[cubic-bezier(.16,1,.3,1)] group-hover:scale-[1.04]"
                                 :sizes="$layout === 'editorial' ? '(min-width: 768px) 60vw, 100vw' : '(min-width: 1024px) 33vw, 100vw'" />
                    @endif
                    @if ($hover?->isVideo())
                        <video src="{{ $hover->url(null) }}" muted loop playsinline preload="none"
                               class="absolute inset-0 w-full h-full object-cover opacity-0 group-hover:opacity-100 transition-opacity duration-500"></video>
                    @elseif ($hover?->isImage())
                        <x-media :media="$hover" class="absolute inset-0 w-full h-full opacity-0 group-hover:opacity-100 transition-opacity duration-500" />
                    @endif
                    @if ($project->is_featured)
                        <span class="absolute top-4 start-4 inline-flex items-center gap-1.5 px-3 h-7 rounded-full bg-brand text-brand-ink text-xs font-semibold">
                            <x-icon name="star" :size="12" fill="currentColor" /> {{ __('Featured') }}
                        </span>
                    @endif
                </div>
                <div class="flex items-start justify-between gap-4 mt-4">
                    <div class="min-w-0">
                        <h3 class="text-xl sm:text-2xl font-display font-semibold leading-tight group-hover:text-brand transition-colors">{{ $project->title }}</h3>
                        <p class="mt-1.5 text-sm text-mute">
                            {{ $project->category?->name }}@if ($project->year)<span class="mx-1.5">·</span>{{ $project->year }}@endif
                        </p>
                    </div>
                    <span class="shrink-0 grid place-items-center w-11 h-11 rounded-full border border-line group-hover:bg-brand group-hover:border-brand group-hover:text-brand-ink transition-colors">
                        <x-icon name="arrow-up-end" :size="18" class="rtl:-scale-x-100 transition-transform duration-500 group-hover:rotate-45" />
                    </span>
                </div>
            </a>
        </article>
    @endforeach
</div>
