@php
    $item = $media[$data['media']] ?? null;
    $ratio = ['auto' => null, '16:9' => '16/9', '9:16' => '9/16', '1:1' => '1/1', '4:5' => '4/5', '21:9' => '21/9'][$data['ratio']] ?? null;
    if ($item && ($posterId = $data['poster']) && isset($media[$posterId])) {
        $item->setRelation('poster', $media[$posterId]);
    }
    $vertical = $ratio === '9/16' || ($item && ! $ratio && $item->height > $item->width);
@endphp
@if ($item)
    <figure class="{{ $wrap }}" {{ $reveal }}>
        <div class="{{ $vertical ? 'max-w-md mx-auto' : '' }}">
            <x-media :media="$item" :ratio="$ratio ?? ($item->isEmbed() ? '16/9' : null)"
                     :video="['autoplay' => $data['autoplay'], 'loop' => $data['loop'], 'muted' => $data['muted'], 'controls' => $data['controls']]"
                     class="{{ $data['rounded'] ? 'rounded-[clamp(0.75rem,2vw,1.5rem)]' : '' }} {{ $ratio || $item->isEmbed() ? '' : 'aspect-video' }}" />
        </div>
        @if (tr($data['caption']))
            <figcaption class="mt-3 text-sm text-mute">{{ tr($data['caption']) }}</figcaption>
        @endif
    </figure>
@endif
