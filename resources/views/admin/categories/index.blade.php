@extends('layouts.dashboard')

@section('title', __('Categories'))
@section('page-title', __('Categories'))

@section('content')
<div x-data="{
        form: { id: null, name: { ar: '', en: '' }, description: { ar: '', en: '' }, slug: '', status: 'active' },
        sub: { id: null, category: null, name: { ar: '', en: '' }, active: true },
        startAdd() { this.form = { id: null, name: { ar: '', en: '' }, description: { ar: '', en: '' }, slug: '', status: 'active' }; $dispatch('open-modal', 'category') },
        startEdit(c) { this.form = JSON.parse(JSON.stringify(c)); $dispatch('open-modal', 'category') },
        startSub(categoryId, s = null) { this.sub = s ? { ...JSON.parse(JSON.stringify(s)), category: categoryId } : { id: null, category: categoryId, name: { ar: '', en: '' }, active: true }; $dispatch('open-modal', 'sub') },
        async saveOrder() {
            const ids = [...$refs.list.querySelectorAll('[data-id]')].map(el => +el.dataset.id);
            try { const { data } = await http.post(@js(route('admin.categories.reorder')), { ids }); toast(data.message) } catch (e) { toast(errorMessage(e), 'error') }
        },
     }">

    <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
        <div>
            <h2 class="text-xl font-bold">{{ __('Categories') }}</h2>
            <p class="text-sm text-muted">{{ __('Categories appear as filters on the Work page. Drag to reorder.') }}</p>
        </div>
        <button type="button" @click="startAdd()" class="btn-primary h-11"><x-icon name="plus" :stroke="2.4" /> {{ __('New category') }}</button>
    </div>

    <div x-ref="list" x-sort="saveOrder()" class="space-y-3">
        @forelse ($categories as $category)
            @php
                $payload = [
                    'id' => $category->id,
                    'name' => $category->getTranslations('name') + ['ar' => '', 'en' => ''],
                    'description' => $category->getTranslations('description') + ['ar' => '', 'en' => ''],
                    'slug' => $category->slug,
                    'status' => $category->status,
                ];
            @endphp
            <div class="card" data-id="{{ $category->id }}" x-sort:item="{{ $category->id }}" id="category-{{ $category->id }}" x-data="{ open: false }">
                <div class="flex items-center gap-3 p-4">
                    <span x-sort:handle class="text-subtle hover:text-ink cursor-grab"><x-icon name="drag" /></span>
                    <div class="min-w-0 flex-1">
                        @php
                            $nameAr = $category->getTranslation('name', 'ar', false);
                            $nameEn = $category->getTranslation('name', 'en', false);
                        @endphp
                        <p class="font-bold truncate">
                            <bdi>{{ $nameAr ?: $nameEn }}</bdi>
                            @if ($nameEn && $nameEn !== $nameAr)
                                <span class="text-muted font-normal text-sm">· <bdi>{{ $nameEn }}</bdi></span>
                            @elseif ($nameAr === $nameEn)
                                <span class="badge bg-warning-soft text-warning ms-1 py-0.5">{{ __('Needs Arabic name') }}</span>
                            @endif
                        </p>
                        <a href="{{ lroute('work.category', $category->slug, app()->getLocale()) }}" target="_blank" class="text-xs text-muted hover:underline" dir="ltr">/{{ app()->getLocale() }}/category/{{ $category->slug }}</a>
                    </div>
                    <span class="badge bg-canvas text-muted">{{ trans_choice(':count project|:count projects', $category->projects_count) }}</span>
                    <button type="button" @click="open = !open" class="badge bg-canvas text-muted hover:text-ink">
                        {{ trans_choice(':count subcategory|:count subcategories', $category->subCategories->count()) }}
                        <x-icon name="chevron-down" :size="13" ::class="open && 'rotate-180'" />
                    </button>
                    <button type="button" x-data="ajaxToggle(@js(route('admin.categories.toggle', $category)), @js($category->status === 'active'))" @click="toggle()"
                            class="badge" :class="value ? 'bg-success-soft text-success' : 'bg-canvas text-muted'"
                            x-text="value ? @js(__('Visible')) : @js(__('Hidden'))"></button>
                    <button type="button" @click="startEdit(@js($payload))" class="btn-icon"><x-icon name="edit" :size="17" /></button>
                    <button type="button" class="btn-icon hover:text-danger"
                            @click="deleteResource(@js(route('admin.categories.destroy', $category)), { title: @js(__('Delete this category?')), el: document.getElementById('category-{{ $category->id }}') })">
                        <x-icon name="trash" :size="17" />
                    </button>
                </div>
                <div x-show="open" x-collapse class="border-t border-line px-4 py-3 bg-canvas/50">
                    <div class="flex flex-wrap gap-2">
                        @foreach ($category->subCategories as $sub)
                            @php $subPayload = ['id' => $sub->id, 'name' => $sub->getTranslations('name') + ['ar' => '', 'en' => ''], 'active' => $sub->status === 'active']; @endphp
                            <span id="sub-{{ $sub->id }}" class="inline-flex items-center gap-1 ps-3 pe-1 h-9 rounded-full bg-surface border border-line text-sm {{ $sub->status !== 'active' ? 'opacity-50' : '' }}">
                                {{ $sub->name }} <span class="text-xs text-subtle">({{ $sub->projects_count }})</span>
                                <button type="button" @click="startSub({{ $category->id }}, @js($subPayload))" class="btn-icon w-7 h-7"><x-icon name="edit" :size="13" /></button>
                                <button type="button" class="btn-icon w-7 h-7 hover:text-danger"
                                        @click="deleteResource(@js(route('admin.categories.sub.destroy', $sub)), { title: @js(__('Delete this subcategory?')), el: document.getElementById('sub-{{ $sub->id }}') })"><x-icon name="x" :size="13" /></button>
                            </span>
                        @endforeach
                        <button type="button" @click="startSub({{ $category->id }})" class="inline-flex items-center gap-1 px-3 h-9 rounded-full border-2 border-dashed border-line text-sm text-muted hover:text-ink">
                            <x-icon name="plus" :size="14" /> {{ __('Add subcategory') }}
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="card py-16 text-center text-muted">{{ __('No categories yet.') }}</div>
        @endforelse
    </div>

    {{-- Category modal --}}
    <x-modal name="category" :title="__('Category')">
        <form method="POST" :action="form.id ? @js(url('admin/categories')) + '/' + form.id : @js(route('admin.categories.store'))" class="p-6 space-y-4">
            @csrf
            <template x-if="form.id"><input type="hidden" name="_method" value="PUT"></template>
            <div class="grid sm:grid-cols-2 gap-4">
                <div><label class="label">{{ __('Name') }} (عربي)</label><input name="name[ar]" x-model="form.name.ar" class="field" dir="rtl"></div>
                <div><label class="label">{{ __('Name') }} (English)</label><input name="name[en]" x-model="form.name.en" class="field" dir="ltr"></div>
                <div><label class="label">{{ __('Description') }} (عربي)</label><textarea name="description[ar]" x-model="form.description.ar" rows="2" class="field" dir="rtl"></textarea></div>
                <div><label class="label">{{ __('Description') }} (English)</label><textarea name="description[en]" x-model="form.description.en" rows="2" class="field" dir="ltr"></textarea></div>
                <div><label class="label">{{ __('URL slug') }}</label><input name="slug" x-model="form.slug" class="field font-mono" dir="ltr" placeholder="{{ __('Automatic') }}"></div>
                <div>
                    <label class="label">{{ __('Status') }}</label>
                    <select name="status" x-model="form.status" class="field">
                        <option value="active">{{ __('Visible') }}</option>
                        <option value="inactive">{{ __('Hidden') }}</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="$dispatch('close-modal', 'category')" class="btn-ghost">{{ __('Cancel') }}</button>
                <button class="btn-primary">{{ __('Save') }}</button>
            </div>
        </form>
    </x-modal>

    {{-- Subcategory modal --}}
    <x-modal name="sub" :title="__('Subcategory')" maxWidth="lg">
        <form method="POST" :action="sub.id ? @js(url('admin/sub-categories')) + '/' + sub.id : @js(url('admin/categories')) + '/' + sub.category + '/sub'" class="p-6 space-y-4">
            @csrf
            <template x-if="sub.id"><input type="hidden" name="_method" value="PUT"></template>
            <div><label class="label">{{ __('Name') }} (عربي)</label><input name="name[ar]" x-model="sub.name.ar" class="field" dir="rtl"></div>
            <div><label class="label">{{ __('Name') }} (English)</label><input name="name[en]" x-model="sub.name.en" class="field" dir="ltr"></div>
            <template x-if="sub.id">
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="active" value="1" x-model="sub.active"> {{ __('Visible') }}</label>
            </template>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="$dispatch('close-modal', 'sub')" class="btn-ghost">{{ __('Cancel') }}</button>
                <button class="btn-primary">{{ __('Save') }}</button>
            </div>
        </form>
    </x-modal>
</div>
@endsection
