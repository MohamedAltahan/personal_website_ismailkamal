<div>
    <label class="block text-xs font-semibold text-muted mb-1.5">{{ __('Number of columns') }}</label>
    <div class="flex rounded-field bg-canvas p-1 gap-1">
        <template x-for="n in [1, 2, 3, 4]" :key="n">
            <button type="button" class="flex-1 h-8 rounded-md text-xs font-semibold" x-text="n"
                    :class="sel.data.count === n ? 'bg-surface text-ink shadow-sm' : 'text-muted'"
                    @click="sel.data.count = n; while (sel.data.cols.length < n) sel.data.cols.push({ kind: 'text', media: null, html: { ar: '', en: '' }, caption: { ar: '', en: '' } })"></button>
        </template>
    </div>
</div>

<template x-for="(col, ci) in sel.data.cols.slice(0, sel.data.count)" :key="sel.id + '-col-' + ci">
    <div class="rounded-xl border border-line p-3 space-y-3">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold">{{ __('Column') }} <span x-text="ci + 1"></span></span>
            <div class="flex rounded-full bg-canvas p-0.5 gap-0.5">
                <template x-for="k in ['text', 'image', 'video']" :key="k">
                    <button type="button" @click="col.kind = k" class="h-7 px-2.5 rounded-full text-[11px] font-semibold"
                            :class="col.kind === k ? 'bg-surface shadow-sm text-ink' : 'text-muted'"
                            x-text="{ text: @js(__('Text')), image: @js(__('Image')), video: @js(__('Video')) }[k]"></button>
                </template>
            </div>
        </div>
        <template x-if="col.kind === 'text'">
            <x-b.rich key="html" obj="col" uid="sel.id + '-c' + ci" :label="__('Text')" />
        </template>
        <template x-if="col.kind !== 'text'">
            <div class="space-y-3">
                <x-b.media obj="col" :label="__('Media')" accept="col.kind === 'image' ? ['image'] : ['video', 'embed']" />
                <x-b.text key="caption" obj="col" :label="__('Caption')" />
            </div>
        </template>
    </div>
</template>
<x-b.segmented key="gap" :label="__('Spacing')" :options="['sm' => 'S', 'md' => 'M', 'lg' => 'L']" />
<x-b.segmented key="valign" :label="__('Vertical alignment')" :options="['start' => __('Top'), 'center' => __('Middle'), 'end' => __('Bottom')]" />
