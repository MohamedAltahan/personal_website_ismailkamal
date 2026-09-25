<div>
    <div class="flex items-center justify-between mb-2">
        <label class="text-xs font-semibold text-muted">{{ __('Images') }} (<span x-text="sel.data.items.length"></span>)</label>
        <button type="button" @click="addItems(sel.data)" class="text-xs font-bold text-primary-600 dark:text-accent-500 hover:underline inline-flex items-center gap-1">
            <x-icon name="plus" :size="14" /> {{ __('Add images') }}
        </button>
    </div>
    <div class="grid grid-cols-3 gap-2" x-sort="(from, to) => reorderItems(sel.data.items, from, to)">
        <template x-for="(item, i) in sel.data.items" :key="i + '-' + item.media">
            <div class="group relative aspect-square rounded-lg overflow-hidden checkerboard cursor-grab" x-sort:item="i"
                 x-data="{ open: false }">
                <img :src="m(item.media)?.thumb" class="w-full h-full object-cover" alt="">
                <span x-show="item.span !== '1'" class="absolute top-1 start-1 badge bg-accent-500 text-secondary-900 px-1.5 py-0 text-[10px]" x-text="item.span === 'full' ? '⟷' : '×2'"></span>
                <div class="absolute inset-0 bg-black/55 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-1">
                    <button type="button" @click="open = !open" class="grid place-items-center w-7 h-7 rounded-full bg-white text-secondary-900" title="{{ __('Options') }}"><x-icon name="edit" :size="13" /></button>
                    <button type="button" @click="sel.data.items.splice(i, 1)" class="grid place-items-center w-7 h-7 rounded-full bg-white text-danger" title="{{ __('Remove') }}"><x-icon name="trash" :size="13" /></button>
                </div>
                <div x-show="open" x-cloak @click.outside="open = false"
                     class="fixed z-50 w-64 rounded-xl bg-raised border border-line shadow-2xl p-3 space-y-3 cursor-default"
                     x-init="$watch('open', o => { if (o) { const r = $el.parentElement.getBoundingClientRect(); $el.style.top = (r.bottom + 6) + 'px'; $el.style.left = Math.max(8, Math.min(r.left, innerWidth - 270)) + 'px'; } })">
                    <x-b.text key="caption" obj="item" :label="__('Caption')" />
                    <x-b.segmented key="span" obj="item" :label="__('Width in grid')" :options="['1' => '1×', '2' => '2×', 'full' => __('Full row')]" />
                </div>
            </div>
        </template>
        <button type="button" @click="addItems(sel.data)" class="aspect-square rounded-lg border-2 border-dashed border-line hover:border-accent-500 grid place-items-center text-muted">
            <x-icon name="plus" :size="20" />
        </button>
    </div>
    <p class="mt-2 text-[11px] text-subtle">{{ __('Drag to reorder. Hover an image for caption & width.') }}</p>
</div>
<x-b.segmented key="layout" :label="__('Layout')" :options="['grid' => __('Grid'), 'masonry' => __('Masonry'), 'carousel' => __('Slider'), 'stack' => __('Stack')]" />
<template x-if="sel.data.layout !== 'stack'">
    <x-b.segmented key="columns" :label="__('Columns')" :options="[1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6']" number />
</template>
<template x-if="sel.data.layout === 'grid' || sel.data.layout === 'carousel'">
    <x-b.select key="ratio" :label="__('Image shape')" :options="['auto' => __('Original'), 'square' => '1:1', '4:3' => '4:3', '3:4' => '3:4', '4:5' => '4:5', '16:9' => '16:9']" />
</template>
<x-b.segmented key="gap" :label="__('Spacing')" :options="['none' => '0', 'sm' => 'S', 'md' => 'M', 'lg' => 'L']" />
<x-b.toggle key="lightbox" :label="__('Open full size on click')" />
<x-b.toggle key="rounded" :label="__('Rounded corners')" />
