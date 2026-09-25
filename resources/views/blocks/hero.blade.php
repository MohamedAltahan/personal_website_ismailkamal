@php
    use App\Support\Blocks\BlockRegistry;
    use App\Support\Color;

    $bg = $media[$data['media']] ?? null;
    $heights = ['screen' => 'min-h-[100svh]', 'large' => 'min-h-[85svh]', 'medium' => 'min-h-[60svh]', 'auto' => ''];
    $center = $data['align'] === 'center';
    $overlay = max(0, min(90, (int) $data['overlay']));

    // Effects (older hero blocks saved before effects existed default to none).
    $effect = in_array($data['effect'] ?? 'none', BlockRegistry::HERO_EFFECTS, true) ? $data['effect'] ?? 'none' : 'none';
    $textFx = in_array($data['text_effect'] ?? 'none', BlockRegistry::TEXT_EFFECTS, true) ? $data['text_effect'] ?? 'none' : 'none';
    $intensity = max(10, min(100, (int) ($data['effect_intensity'] ?? 60))) / 100;
    $fxColor = Color::valid($data['effect_color'] ?? null) ? $data['effect_color'] : 'var(--brand)';
    $interactive = (bool) ($data['interactive'] ?? false);
    $marquee = collect(preg_split('/[,،]/u', tr($data['marquee'] ?? '')))->map(fn ($w) => trim($w))->filter()->values();
    $title = tr($data['title']);
    $canvasFx = in_array($effect, ['particles', 'waves', 'stars'], true);
@endphp
<div class="fx-hero {{ $bg ? 'fx-on-media' : '' }} {{ $interactive ? 'fx-interactive' : '' }} relative {{ $heights[$data['height']] ?? $heights['large'] }} flex flex-col {{ $bg ? 'text-white' : '' }} overflow-hidden"
     x-data="heroFx(@js(['effect' => $effect, 'textEffect' => $textFx, 'intensity' => $intensity, 'interactive' => $interactive]))"
     style="--fx: {{ $fxColor }}; --fx-i: {{ $intensity }}; --hero-ink: {{ $bg ? '#ffffff' : 'var(--ink)' }};">

    {{-- Background media --}}
    @if ($bg)
        <div class="fx-layer">
            <div class="absolute inset-0 {{ $interactive ? 'fx-parallax' : '' }}">
                @if ($bg->isVideo())
                    <video src="{{ $bg->url(null) }}" @if ($bg->poster) poster="{{ $bg->poster->url(1600) }}" @endif
                           muted autoplay loop playsinline data-autoplay class="w-full h-full object-cover"></video>
                @else
                    <x-media :media="$bg" class="w-full h-full" eager />
                @endif
            </div>
            <div class="absolute inset-0 bg-black" style="opacity: {{ $overlay / 100 }}"></div>
            <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black/50 to-transparent"></div>
        </div>
    @endif

    {{-- Effect layer --}}
    @switch($effect)
        @case('aurora')
            <div class="fx-layer fx-aurora" aria-hidden="true"><span></span><span></span><span></span><span></span></div>
            @break
        @case('spotlight')
            <div class="fx-layer fx-spotlight" aria-hidden="true"></div>
            @break
        @case('grid')
            <div class="fx-layer fx-grid" aria-hidden="true"><div class="glow"></div><div class="plane"></div></div>
            @break
    @endswitch
    @if ($canvasFx)
        <canvas x-ref="canvas" class="fx-layer w-full h-full" aria-hidden="true"></canvas>
    @endif
    @if ($interactive)
        <div class="fx-layer fx-cursor" aria-hidden="true"></div>
    @endif
    @if ($data['grain'] ?? false)
        <div class="fx-grain" aria-hidden="true"></div>
    @endif

    {{-- Content --}}
    <div class="{{ $style['width'] === 'full' ? 'container-site' : $wrap }} relative z-[1] flex-1 flex flex-col justify-end {{ $center ? 'items-center text-center' : '' }} pt-[calc(var(--header-h)+3rem)] pb-[clamp(3rem,8vw,6rem)]">
        @if (tr($data['eyebrow']))
            <p class="flex items-center gap-2.5 text-sm sm:text-base mb-6 {{ $bg ? 'text-white/80' : 'text-mute' }}" data-reveal style="--reveal-delay:.05s">
                <span class="w-8 h-px bg-brand"></span>{{ tr($data['eyebrow']) }}
            </p>
        @endif

        @switch($textFx)
            @case('rise')
                <h1 class="text-hero font-display max-w-[14ch] {{ $center ? 'mx-auto' : '' }}" aria-label="{{ $title }}">
                    @foreach (preg_split('/\s+/u', trim($title)) as $i => $word)
                        <span class="fx-word" aria-hidden="true"><span style="--i: {{ $i }}">{{ $word }}</span></span>{{ ' ' }}
                    @endforeach
                </h1>
                @break
            @case('scramble')
            @case('typewriter')
                <h1 x-ref="title" data-text="{{ $title }}" class="fx-js-text text-hero font-display max-w-[14ch] {{ $center ? 'mx-auto' : '' }}">{{ $title }}</h1>
                @break
            @case('shimmer')
                <h1 class="fx-shimmer text-hero font-display max-w-[14ch] {{ $center ? 'mx-auto' : '' }}" data-reveal style="--reveal-delay:.12s">{{ $title }}</h1>
                @break
            @default
                <h1 class="text-hero font-display max-w-[14ch] {{ $center ? 'mx-auto' : '' }}" data-reveal style="--reveal-delay:.12s">{{ $title }}</h1>
        @endswitch

        @if (tr($data['subtitle']))
            <p class="mt-8 max-w-2xl text-lg sm:text-xl leading-relaxed {{ $bg ? 'text-white/80' : 'text-mute' }} {{ $center ? 'mx-auto' : '' }}" data-reveal style="--reveal-delay:.3s">
                {{ tr($data['subtitle']) }}
            </p>
        @endif
        @if (tr($data['button_label']) && $data['button_url'])
            <div class="mt-10" data-reveal style="--reveal-delay:.4s">
                <a href="{{ $data['button_url'] }}" @if ($interactive) data-magnetic @endif
                   class="group inline-flex items-center gap-3 h-14 ps-7 pe-2 rounded-full bg-brand text-brand-ink font-semibold shadow-[0_10px_40px_-10px_var(--fx)]">
                    {{ tr($data['button_label']) }}
                    <span class="grid place-items-center w-10 h-10 rounded-full bg-brand-ink text-brand group-hover:rotate-45 transition-transform duration-500">
                        <x-icon name="arrow-up-end" :size="18" class="rtl:-scale-x-100" />
                    </span>
                </a>
            </div>
        @endif
    </div>

    {{-- Moving strip of words --}}
    @if ($marquee->count())
        <div class="relative z-[1] border-y border-current/10 py-4 overflow-hidden {{ $bg ? 'bg-black/20 backdrop-blur-sm' : '' }}" aria-hidden="true">
            <div class="marquee-track flex w-max gap-10 whitespace-nowrap font-display text-lg sm:text-2xl font-semibold">
                @foreach (range(1, 2) as $copy)
                    @foreach (range(1, max(2, (int) ceil(8 / $marquee->count()))) as $repeat)
                        @foreach ($marquee as $word)
                            <span class="flex items-center gap-10">{{ $word }}<span class="text-brand text-base">✦</span></span>
                        @endforeach
                    @endforeach
                @endforeach
            </div>
        </div>
    @endif

    @if ($data['scroll_hint'] && in_array($data['height'], ['screen', 'large'], true) && ! $marquee->count())
        <div class="absolute z-[1] bottom-6 end-[clamp(1rem,4vw,3rem)] hidden sm:flex flex-col items-center gap-2 text-xs {{ $bg ? 'text-white/70' : 'text-mute' }}">
            <span class="[writing-mode:vertical-rl] tracking-widest uppercase">{{ __('Scroll') }}</span>
            <span class="w-px h-12 bg-current/40 overflow-hidden relative"><span class="absolute inset-x-0 top-0 h-1/2 bg-brand animate-[scroll-line_2s_ease-in-out_infinite]"></span></span>
        </div>
    @endif
</div>
