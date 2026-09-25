@props([
    'media' => null,
    'sizes' => '100vw',
    'fit' => 'cover',          // cover | contain | natural
    'ratio' => null,           // CSS aspect-ratio override, e.g. "16/9"
    'lightbox' => false,
    'eager' => false,
    'imgClass' => '',
    'video' => [],             // autoplay, loop, muted, controls
    'alt' => null,
])
@php
    /** @var \App\Models\Media|null $media */
    $ratioStyle = $ratio ?: ($media?->width && $media?->height ? $media->width.'/'.$media->height : null);
    $objectFit = $fit === 'contain' ? 'object-contain' : 'object-cover';
    $altText = $alt ?? ($media?->alt ?: '');
@endphp

@if (! $media)
    <div {{ $attributes->merge(['class' => 'bg-paper-2']) }} @if ($ratioStyle) style="aspect-ratio: {{ $ratioStyle }}" @endif></div>
@elseif ($media->isImage())
    @php
        $img = '<img src="'.e($media->url(1600)).'"'
            .($media->srcset() ? ' srcset="'.e($media->srcset()).'" sizes="'.e($sizes).'"' : '')
            .' alt="'.e($altText).'"'
            .($media->width ? ' width="'.$media->width.'" height="'.$media->height.'"' : '')
            .' loading="'.($eager ? 'eager' : 'lazy').'" decoding="async"'
            .($eager ? ' fetchpriority="high"' : '')
            .' class="img-fade block w-full '.($fit === 'natural' ? 'h-auto' : 'h-full '.$objectFit).' '.e($imgClass).'">';
    @endphp
    <div {{ $attributes->merge(['class' => 'relative overflow-hidden']) }}
         style="{{ $ratioStyle && $fit !== 'natural' ? 'aspect-ratio: '.$ratioStyle.';' : '' }} background-color: {{ $media->color ?? 'transparent' }}">
        @if ($lightbox)
            <a href="{{ $media->url('full') }}" data-lightbox data-pswp-width="{{ $media->width ?: 1600 }}" data-pswp-height="{{ $media->height ?: 1000 }}"
               data-cursor="{{ __('Zoom') }}" class="block w-full h-full">{!! $img !!}</a>
        @else
            {!! $img !!}
        @endif
    </div>
@elseif ($media->isVideo())
    @php
        $autoplay = ! empty($video['autoplay']);
        $controls = $video['controls'] ?? true;
        $poster = $media->poster?->url(1600);
    @endphp
    <div {{ $attributes->merge(['class' => 'relative overflow-hidden bg-black']) }}
         @if ($ratioStyle) style="aspect-ratio: {{ $ratioStyle }}" @endif
         x-data="videoPlayer({{ $autoplay || ! $controls ? 'true' : 'false' }})">
        <video x-ref="video" src="{{ $media->url(null) }}" @if ($poster) poster="{{ $poster }}" @endif
               playsinline preload="{{ $autoplay ? 'auto' : 'metadata' }}"
               @if ($autoplay) muted autoplay data-autoplay @elseif (! empty($video['muted'])) muted @endif
               @if (! empty($video['loop']) || $autoplay) loop @endif
               controlsList="nodownload" disablePictureInPicture
               class="block w-full h-full {{ $objectFit }}"></video>
        @if (! $autoplay && $controls)
            <button type="button" x-show="!playing" @click="play()" data-cursor="{{ __('Play') }}"
                    class="group absolute inset-0 grid place-items-center bg-black/10 hover:bg-black/25 transition" aria-label="{{ __('Play video') }}">
                <span class="grid place-items-center w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-brand text-brand-ink shadow-2xl group-hover:scale-110 transition-transform duration-500">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="currentColor" class="ms-1"><path d="M7 4.5v15a1 1 0 0 0 1.5.86l12.5-7.5a1 1 0 0 0 0-1.72L8.5 3.64A1 1 0 0 0 7 4.5z"/></svg>
                </span>
            </button>
        @endif
    </div>
@elseif ($media->isEmbed())
    @php $thumb = $media->meta['thumbnail'] ?? null; @endphp
    <div {{ $attributes->merge(['class' => 'relative overflow-hidden bg-black']) }} style="aspect-ratio: {{ $ratio ?: '16/9' }}"
         x-data="embedFacade(@js($media->embedSrc(array_merge($video, ['autoplay' => true]))))">
        <template x-if="loaded">
            <iframe :src="src" class="absolute inset-0 w-full h-full" allow="autoplay; fullscreen; picture-in-picture; encrypted-media" allowfullscreen loading="lazy" title="{{ $media->name }}"></iframe>
        </template>
        <button type="button" x-show="!loaded" @click="load()" data-cursor="{{ __('Play') }}" class="group absolute inset-0 grid place-items-center" aria-label="{{ __('Play video') }}">
            @if ($thumb)<img src="{{ $thumb }}" alt="" loading="lazy" class="absolute inset-0 w-full h-full object-cover opacity-90 group-hover:opacity-100 transition">@endif
            <span class="relative grid place-items-center w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-brand text-brand-ink shadow-2xl group-hover:scale-110 transition-transform duration-500">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="currentColor" class="ms-1"><path d="M7 4.5v15a1 1 0 0 0 1.5.86l12.5-7.5a1 1 0 0 0 0-1.72L8.5 3.64A1 1 0 0 0 7 4.5z"/></svg>
            </span>
        </button>
    </div>
@endif
