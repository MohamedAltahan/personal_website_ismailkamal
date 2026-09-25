@php
    $effects = [
        'none' => [__('None'), 'background: var(--canvas)'],
        'aurora' => [__('Aurora'), 'background: radial-gradient(circle at 30% 30%, #ffd200 0, transparent 45%), radial-gradient(circle at 75% 70%, #ff3d7f 0, transparent 45%), radial-gradient(circle at 20% 80%, #3d7bff 0, transparent 40%), #111033'],
        'particles' => [__('Particles'), 'background: radial-gradient(#ffd200 1.5px, transparent 2px) 0 0/14px 14px, #111033'],
        'spotlight' => [__('Spotlight'), 'background: radial-gradient(circle at 60% 40%, rgba(255,210,0,.55), transparent 55%), radial-gradient(rgba(255,255,255,.35) 1px, transparent 1.5px) 0 0/8px 8px, #111033'],
        'grid' => [__('3D grid'), 'background: linear-gradient(transparent 55%, rgba(255,210,0,.35)), repeating-linear-gradient(90deg, rgba(255,210,0,.6) 0 1px, transparent 1px 12px), #111033'],
        'waves' => [__('Waves'), 'background: repeating-radial-gradient(ellipse at 50% 140%, transparent 0 8px, rgba(255,210,0,.55) 8px 9px), #111033'],
        'stars' => [__('Warp stars'), 'background: radial-gradient(circle, #fff 0 1px, transparent 1.5px) 0 0/11px 13px, radial-gradient(circle, #ffd200 0 1px, transparent 1.5px) 5px 6px/17px 15px, #0b0b0c'],
    ];
    $textEffects = ['none' => __('None'), 'rise' => __('Rise'), 'scramble' => __('Decode'), 'typewriter' => __('Typing'), 'shimmer' => __('Shimmer')];
@endphp

<x-b.text key="eyebrow" :label="__('Small text above the title')" />
<x-b.text key="title" :label="__('Title')" multiline :rows="2" />
<x-b.text key="subtitle" :label="__('Subtitle')" multiline :rows="3" />
<div class="grid grid-cols-2 gap-3">
    <x-b.text key="button_label" :label="__('Button text')" />
    <x-b.text key="button_url" :label="__('Button link')" plain dir="ltr" placeholder="#work" />
</div>

{{-- ============ Effects ============ --}}
<div class="rounded-2xl border border-accent-500/40 bg-accent-500/[0.04] p-3.5 space-y-4">
    <div class="flex items-center gap-2">
        <span class="grid place-items-center w-7 h-7 rounded-lg bg-accent-500 text-secondary-900"><x-icon name="sparkles" :size="15" /></span>
        <span class="text-sm font-bold">{{ __('Magic effects') }}</span>
    </div>

    <div>
        <label class="block text-xs font-semibold text-muted mb-2">{{ __('Background effect') }}</label>
        <div class="grid grid-cols-3 gap-2">
            @foreach ($effects as $key => [$label, $swatch])
                <button type="button" @click="sel.data.effect = '{{ $key }}'"
                        class="group rounded-xl border-2 p-1 text-center transition"
                        :class="(sel.data.effect || 'none') === '{{ $key }}' ? 'border-accent-500' : 'border-transparent hover:border-line'">
                    <span class="block h-12 rounded-lg border border-line" style="{{ $swatch }}"></span>
                    <span class="block mt-1 text-[11px] font-bold truncate">{{ $label }}</span>
                </button>
            @endforeach
        </div>
    </div>

    <template x-if="sel.data.effect && sel.data.effect !== 'none'">
        <div class="space-y-4">
            <x-b.range key="effect_intensity" :label="__('Intensity')" :min="10" :max="100" suffix="%" />
            <x-b.color key="effect_color" obj="sel.data" :label="__('Effect colour (empty = brand colour)')" />
        </div>
    </template>

    <x-b.segmented key="text_effect" :label="__('Title animation')" :options="$textEffects" />
    <p class="text-[11px] text-subtle -mt-2" x-show="sel.data.text_effect === 'scramble'">{{ __('Decode works on English titles; Arabic titles are typed instead.') }}</p>

    <x-b.toggle key="interactive" :label="__('Follow the mouse')" :hint="__('Background and button react to the cursor (desktop).')" />
    <x-b.toggle key="grain" :label="__('Film grain')" :hint="__('A subtle cinematic noise texture.')" />
    <x-b.text key="marquee" :label="__('Moving words strip (comma separated)')" :placeholder="__('Motion Design, Animation, Branding, 3D')" />
</div>

{{-- ============ Background & layout ============ --}}
<x-b.media :label="__('Background image or video (optional)')" accept="['image', 'video']" />
<template x-if="sel.data.media">
    <x-b.range key="overlay" :label="__('Darken background')" :max="90" suffix="%" />
</template>
<x-b.segmented key="height" :label="__('Height')" :options="['screen' => __('Full'), 'large' => __('Large'), 'medium' => __('Medium'), 'auto' => __('Auto')]" />
<x-b.segmented key="align" :label="__('Alignment')" :options="['start' => __('Start'), 'center' => __('Center')]" />
<x-b.toggle key="scroll_hint" :label="__('Show scroll hint')" />
