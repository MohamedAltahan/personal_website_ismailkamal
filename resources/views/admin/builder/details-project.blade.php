<x-b.text key="title" obj="doc" :label="__('Project title')" />
<x-b.text key="excerpt" obj="doc" :label="__('Short description')" multiline :rows="3" />

<div>
    <label class="block text-xs font-semibold text-muted mb-1.5">{{ __('Cover image') }}</label>
    <x-b.media key="cover_media_id" obj="doc" label="" :hint="__('Shown in project grids and when sharing the link.')" />
</div>
<x-b.media key="hover_media_id" obj="doc" :label="__('Hover preview (optional)')" accept="['image', 'video']" :hint="__('A short looping video or second image shown when hovering the card.')" />

<div class="grid grid-cols-2 gap-3">
    <div>
        <label class="block text-xs font-semibold text-muted mb-1.5">{{ __('Category') }}</label>
        <select class="field text-sm h-10 py-0" x-model.number="doc.category_id" @change="doc.sub_category_id = null">
            <option value="">—</option>
            <template x-for="c in cfg.categories" :key="c.id">
                <option :value="c.id" x-text="c.name" :selected="c.id == doc.category_id"></option>
            </template>
        </select>
    </div>
    <div x-show="subCategories.length">
        <label class="block text-xs font-semibold text-muted mb-1.5">{{ __('Subcategory') }}</label>
        <select class="field text-sm h-10 py-0" x-model.number="doc.sub_category_id">
            <option value="">—</option>
            <template x-for="s in subCategories" :key="s.id">
                <option :value="s.id" x-text="s.name" :selected="s.id == doc.sub_category_id"></option>
            </template>
        </select>
    </div>
</div>

<div class="grid grid-cols-2 gap-3">
    <x-b.text key="client" obj="doc" :label="__('Client')" plain />
    <x-b.text key="year" obj="doc" :label="__('Year')" plain dir="ltr" />
</div>

<div>
    <label class="block text-xs font-semibold text-muted mb-1.5">{{ __('Tags / tools') }}</label>
    <div class="field flex flex-wrap gap-1.5 min-h-10 py-1.5">
        <template x-for="(tag, i) in doc.tags" :key="tag">
            <span class="badge bg-accent-500/15 text-accent-800 dark:text-accent-300">
                <span x-text="tag"></span>
                <button type="button" @click="doc.tags.splice(i, 1)" class="opacity-60 hover:opacity-100">×</button>
            </span>
        </template>
        <input type="text" x-model="tagInput" @keydown.enter.prevent="addTag()" @keydown.comma.prevent="addTag()" @blur="addTag()"
               placeholder="After Effects, Cinema 4D…" class="flex-1 min-w-24 bg-transparent outline-none text-sm">
    </div>
</div>

<div class="rounded-xl border border-line p-3 space-y-2">
    <x-b.toggle key="is_featured" obj="doc" :label="__('Featured project')" :hint="__('Shown first on the home page.')" />
    <label class="flex items-center justify-between gap-3 py-1">
        <span class="text-sm font-semibold">{{ __('Published') }}</span>
        <button type="button" role="switch" @click="doc.status = doc.status === 'active' ? 'inactive' : 'active'"
                class="relative shrink-0 w-10 h-6 rounded-full transition" :class="doc.status === 'active' ? 'bg-primary-900 dark:bg-accent-500' : 'bg-gray-300 dark:bg-white/15'">
            <span class="absolute top-1 w-4 h-4 rounded-full bg-white shadow transition-all" :class="doc.status === 'active' ? 'start-5' : 'start-1'"></span>
        </button>
    </label>
    <div>
        <label class="block text-xs font-semibold text-muted mb-1.5">{{ __('Publish date (optional — schedule)') }}</label>
        <input type="datetime-local" x-model="doc.published_at" class="field text-sm h-10 py-0" dir="ltr">
    </div>
</div>

<details class="group rounded-xl border border-line">
    <summary class="flex items-center justify-between px-3 h-11 text-sm font-bold">
        {{ __('Link & SEO') }} <x-icon name="chevron-down" :size="16" class="group-open:rotate-180 transition" />
    </summary>
    <div class="p-3 pt-0 space-y-3">
        <div>
            <label class="block text-xs font-semibold text-muted mb-1.5">{{ __('URL slug') }}</label>
            <div class="flex gap-1.5">
                <input type="text" x-model="doc.slug" dir="ltr" class="field text-sm h-10 py-0 font-mono" placeholder="my-project">
                <button type="button" @click="slugify()" class="btn-ghost h-10 px-3 text-xs" title="{{ __('Generate from English title') }}"><x-icon name="refresh" :size="15" /></button>
            </div>
            <p class="mt-1 text-[11px] text-subtle" dir="ltr">/{{ app()->getLocale() }}/work/<span x-text="doc.slug || '…'"></span></p>
        </div>
        <x-b.text key="title" obj="doc.seo" :label="__('SEO title')" />
        <x-b.text key="description" obj="doc.seo" :label="__('SEO description')" multiline />
    </div>
</details>
