@props(['blocks' => [], 'media' => collect(), 'context' => null])
@php
    $pad = ['none' => '0', 'sm' => 'clamp(1.5rem,3vw,2.5rem)', 'md' => 'clamp(3rem,6vw,5.5rem)', 'lg' => 'clamp(4.5rem,9vw,8rem)', 'xl' => 'clamp(6rem,13vw,12rem)'];
    $types = \App\Support\Blocks\BlockRegistry::types();
    $widths = [
        'full' => 'w-full',
        'wide' => 'container-site',
        'container' => 'w-full max-w-6xl mx-auto px-[clamp(1rem,4vw,3rem)]',
        'narrow' => 'w-full max-w-3xl mx-auto px-[clamp(1rem,4vw,3rem)]',
    ];
@endphp
@foreach ($blocks as $block)
    @php
        $style = array_replace(\App\Support\Blocks\BlockRegistry::STYLE_DEFAULTS, $block['style'] ?? []);
        $classes = collect([
            $style['hide_mobile'] ? 'hidden md:block' : null,
            $style['hide_desktop'] ? 'md:hidden' : null,
        ])->filter()->implode(' ');
        $inline = collect([
            'padding-top: '.($pad[$style['pt']] ?? $pad['md']),
            'padding-bottom: '.($pad[$style['pb']] ?? $pad['md']),
            \App\Support\Color::valid($style['bg']) ? 'background-color: '.$style['bg'] : null,
            \App\Support\Color::valid($style['text']) ? 'color: '.$style['text'] : null,
        ])->filter()->implode('; ');
        $view = 'blocks.'.$block['type'];
    @endphp
    @if (view()->exists($view))
        <section @if ($style['anchor']) id="{{ \Illuminate\Support\Str::slug($style['anchor']) }}" @endif
                 data-block-id="{{ $block['id'] }}" data-block="{{ $block['type'] }}"
                 class="relative {{ $classes }}" style="{{ $inline }}">
            @include($view, [
                'block' => $block,
                'data' => array_replace($types[$block['type']]['data'] ?? [], $block['data'] ?? []),
                'style' => $style,
                'media' => $media,
                'wrap' => $widths[$style['width']] ?? $widths['container'],
                'reveal' => $style['animate'] !== 'none' ? 'data-reveal='.$style['animate'] : '',
                'context' => $context,
            ])
        </section>
    @endif
@endforeach
