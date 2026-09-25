@php $item = $media[$data['media']] ?? null; @endphp
@if ($item)
    <figure class="{{ $wrap }}" {{ $reveal }}>
        @if ($data['link'])<a href="{{ $data['link'] }}" class="block">@endif
        <x-media :media="$item" fit="natural" :lightbox="$data['lightbox'] && ! $data['link']" :alt="tr($data['caption']) ?: null"
                 sizes="{{ $style['width'] === 'narrow' ? '768px' : '100vw' }}" class="{{ $data['rounded'] ? 'rounded-[clamp(0.75rem,2vw,1.5rem)]' : '' }}" />
        @if ($data['link'])</a>@endif
        @if (tr($data['caption']))
            <figcaption class="mt-3 text-sm text-mute">{{ tr($data['caption']) }}</figcaption>
        @endif
    </figure>
@endif
