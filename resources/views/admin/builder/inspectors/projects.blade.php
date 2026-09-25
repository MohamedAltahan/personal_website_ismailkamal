<x-b.text key="title" :label="__('Section title')" />
<x-b.segmented key="source" :label="__('Show')" :options="['featured' => __('Featured first'), 'latest' => __('By order'), 'category' => __('One category')]" />
<template x-if="sel.data.source === 'category'">
    <div>
        <label class="block text-xs font-semibold text-muted mb-1.5">{{ __('Category') }}</label>
        <select class="field text-sm h-10 py-0" x-model.number="sel.data.category_id">
            <option value="">—</option>
            <template x-for="c in cfg.categories" :key="c.id">
                <option :value="c.id" x-text="c.name" :selected="c.id == sel.data.category_id"></option>
            </template>
        </select>
    </div>
</template>
<x-b.range key="limit" :label="__('Number of projects')" :min="1" :max="24" />
<x-b.segmented key="layout" :label="__('Layout')" :options="['editorial' => __('Editorial'), 'grid' => __('Grid'), 'masonry' => __('Masonry')]" />
<x-b.toggle key="show_all_link" :label="__('Show “All work” link')" />
