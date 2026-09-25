@php
    $sizes = ['md' => 'text-2xl sm:text-3xl', 'lg' => 'text-[1.75rem] sm:text-3xl lg:text-[2.5rem]', 'xl' => 'text-display', 'hero' => 'text-hero'];
    $align = ['start' => 'text-start', 'center' => 'text-center mx-auto', 'end' => 'text-end ms-auto'][$data['align']] ?? 'text-start';
@endphp
<div class="{{ $wrap }}">
    <div class="{{ $align }} max-w-5xl" {{ $reveal }}>
        @if (tr($data['eyebrow']))
            <p class="inline-flex items-center gap-2.5 text-sm text-mute mb-5"><span class="w-2 h-2 rounded-full bg-brand"></span>{{ tr($data['eyebrow']) }}</p>
        @endif
        <h2 class="{{ $sizes[$data['size']] ?? $sizes['lg'] }} font-display font-bold leading-[1.15]">{{ tr($data['text']) }}</h2>
    </div>
</div>
