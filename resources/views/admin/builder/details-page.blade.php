<x-b.text key="title" obj="doc" :label="__('Page title')" />

<template x-if="!cfg.system">
    <div>
        <label class="block text-xs font-semibold text-muted mb-1.5">{{ __('URL slug') }}</label>
        <div class="flex gap-1.5">
            <input type="text" x-model="doc.slug" dir="ltr" class="field text-sm h-10 py-0 font-mono">
            <button type="button" @click="slugify()" class="btn-ghost h-10 px-3 text-xs"><x-icon name="refresh" :size="15" /></button>
        </div>
        <p class="mt-1 text-[11px] text-subtle" dir="ltr">/{{ app()->getLocale() }}/p/<span x-text="doc.slug || '…'"></span></p>
    </div>
</template>

<div class="rounded-xl border border-line p-3 space-y-2">
    <label class="flex items-center justify-between gap-3 py-1">
        <span class="text-sm font-semibold">{{ __('Published') }}</span>
        <button type="button" role="switch" @click="doc.status = doc.status === 'active' ? 'inactive' : 'active'"
                class="relative shrink-0 w-10 h-6 rounded-full transition" :class="doc.status === 'active' ? 'bg-primary-900 dark:bg-accent-500' : 'bg-gray-300 dark:bg-white/15'">
            <span class="absolute top-1 w-4 h-4 rounded-full bg-white shadow transition-all" :class="doc.status === 'active' ? 'start-5' : 'start-1'"></span>
        </button>
    </label>
    <template x-if="!cfg.system">
        <x-b.toggle key="in_menu" obj="doc" :label="__('Show in the site menu')" />
    </template>
</div>

<x-b.text key="title" obj="doc.seo" :label="__('SEO title')" />
<x-b.text key="description" obj="doc.seo" :label="__('SEO description')" multiline />
