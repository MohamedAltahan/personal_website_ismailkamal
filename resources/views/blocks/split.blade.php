@php
    $item = $media[$data['media']] ?? null;
    $ratio = ['50' => ['md:col-span-6', 'md:col-span-6'], '40' => ['md:col-span-5', 'md:col-span-7'], '60' => ['md:col-span-7', 'md:col-span-5']][$data['ratio']] ?? ['md:col-span-6', 'md:col-span-6'];
    $valign = ['start' => 'items-start', 'center' => 'items-center', 'end' => 'items-end'][$data['valign']] ?? 'items-center';
    $mediaEnd = $data['media_side'] === 'end';
@endphp
<div class="{{ $wrap }}">
    <div class="grid md:grid-cols-12 gap-[clamp(1.5rem,5vw,5rem)] {{ $valign }}">
        <div class="{{ $ratio[0] }} {{ $mediaEnd ? 'md:order-2' : '' }}" {{ $reveal }}>
            @if ($item)
                <x-media :media="$item" fit="natural" :lightbox="$item->isImage()" sizes="(min-width: 768px) 50vw, 100vw"
                         :video="['controls' => true]" class="{{ $data['rounded'] ? 'rounded-[clamp(0.75rem,2vw,1.5rem)]' : '' }} {{ $item->isImage() ? '' : 'aspect-video' }}" />
            @endif
        </div>
        <div class="{{ $ratio[1] }}" {{ $reveal }} style="--reveal-delay:.1s">
            @if (tr($data['eyebrow']))
                <p class="inline-flex items-center gap-2.5 text-sm text-mute mb-4"><span class="w-2 h-2 rounded-full bg-brand"></span>{{ tr($data['eyebrow']) }}</p>
            @endif
            @if (tr($data['heading']))
                <h2 class="text-2xl sm:text-4xl font-display font-bold leading-tight mb-6">{{ tr($data['heading']) }}</h2>
            @endif
            <div class="prose-site">{!! tr($data['html']) !!}</div>
            @if (tr($data['button_label']) && $data['button_url'])
                <a href="{{ $data['button_url'] }}" class="mt-8 inline-flex items-center gap-2 h-12 px-6 rounded-full border border-ink hover:bg-ink hover:text-paper transition-colors font-semibold">
                    {{ tr($data['button_label']) }} <x-icon name="arrow-end" :size="18" class="rtl:-scale-x-100" />
                </a>
            @endif
        </div>
    </div>
</div>
