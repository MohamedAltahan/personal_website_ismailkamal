@php
    $before = $media[$data['before']] ?? null;
    $after = $media[$data['after']] ?? null;
@endphp
@if ($before && $after)
    <div class="{{ $wrap }}" {{ $reveal }}>
        <div x-data="compare" class="compare relative overflow-hidden select-none" style="aspect-ratio: {{ $after->width && $after->height ? $after->width.'/'.$after->height : '16/9' }}" data-protect>
            <x-media :media="$after" class="absolute inset-0 w-full h-full" />
            <div class="absolute inset-0" :style="`clip-path: inset(0 ${document.dir === 'rtl' ? 0 : 100 - pos}% 0 ${document.dir === 'rtl' ? 100 - pos : 0}%)`">
                <x-media :media="$before" class="w-full h-full" />
            </div>
            <div class="absolute inset-y-0 w-0.5 bg-white pointer-events-none" :style="`inset-inline-start: ${pos}%`">
                <span class="absolute top-1/2 -translate-y-1/2 -translate-x-1/2 rtl:translate-x-1/2 grid place-items-center w-12 h-12 rounded-full bg-brand text-brand-ink shadow-xl">
                    <x-icon name="compare" :size="20" />
                </span>
            </div>
            <span class="absolute top-4 start-4 px-3 py-1 rounded-full bg-black/60 text-white text-xs font-semibold">{{ tr($data['label_before']) }}</span>
            <span class="absolute top-4 end-4 px-3 py-1 rounded-full bg-black/60 text-white text-xs font-semibold">{{ tr($data['label_after']) }}</span>
            <input type="range" min="0" max="100" step="0.1" x-model="pos" aria-label="{{ tr($data['label_before']) }} / {{ tr($data['label_after']) }}"
                   class="absolute inset-0 w-full h-full opacity-0 cursor-ew-resize">
        </div>
    </div>
@endif
