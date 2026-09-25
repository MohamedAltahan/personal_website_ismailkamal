@if ($paginator->hasPages())
    <nav class="flex flex-wrap items-center justify-between gap-3" aria-label="{{ __('Page navigation') }}">
        <p class="text-sm text-muted">
            {{ __('Showing :from–:to of :total', ['from' => $paginator->firstItem(), 'to' => $paginator->lastItem(), 'total' => $paginator->total()]) }}
        </p>
        <div class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="btn-icon opacity-40"><x-icon name="chevron-start" :size="16" class="rtl:-scale-x-100" /></span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="btn-icon" rel="prev"><x-icon name="chevron-start" :size="16" class="rtl:-scale-x-100" /></a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 text-subtle">…</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="grid place-items-center min-w-9 h-9 px-2 rounded-full bg-primary-900 text-white dark:bg-accent-500 dark:text-secondary-900 text-sm font-bold">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="grid place-items-center min-w-9 h-9 px-2 rounded-full text-sm text-muted hover:bg-surface hover:text-ink">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="btn-icon" rel="next"><x-icon name="chevron-end" :size="16" class="rtl:-scale-x-100" /></a>
            @else
                <span class="btn-icon opacity-40"><x-icon name="chevron-end" :size="16" class="rtl:-scale-x-100" /></span>
            @endif
        </div>
    </nav>
@endif
