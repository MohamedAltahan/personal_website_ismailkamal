@php
    $sizes = ['sm' => 'text-[0.95rem]', 'base' => '', 'lg' => 'text-xl sm:text-2xl !leading-relaxed', 'xl' => 'text-xl sm:text-3xl !leading-snug font-display'];
    $align = ['start' => 'text-start', 'center' => 'text-center', 'end' => 'text-end', 'justify' => 'text-justify'][$data['align']] ?? '';
    $cols = (int) $data['columns'] === 2 ? 'md:columns-2 md:gap-12' : '';
@endphp
<div class="{{ $wrap }}">
    <div class="prose-site {{ $sizes[$data['size']] ?? '' }} {{ $align }} {{ $cols }}" {{ $reveal }}>
        {!! tr($data['html']) !!}
    </div>
</div>
