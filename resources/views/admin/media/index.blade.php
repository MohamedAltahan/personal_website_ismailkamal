@extends('layouts.dashboard')

@section('title', __('Media library'))
@section('page-title', __('Media library'))

@php
    $gb = $totals['bytes'] > 1024 ** 3;
    $size = $gb ? round($totals['bytes'] / 1024 ** 3, 2).' GB' : round($totals['bytes'] / 1024 ** 2).' MB';
@endphp

@section('content')
<div x-data="{
        selected: [],
        detail: null,
        toggle(id) { this.selected = this.selected.includes(id) ? this.selected.filter(i => i !== id) : [...this.selected, id] },
        async bulkDelete() {
            if (!await confirmAction({ title: @js(__('Delete selected files?')), message: @js(__('Files that are used in projects or pages will be skipped.')) })) return;
            try { const { data } = await http.post(@js(route('admin.media.bulk-destroy')), { ids: this.selected }); toast(data.message); setTimeout(() => location.reload(), 600) }
            catch (e) { toast(errorMessage(e), 'error') }
        },
        async saveDetail() {
            try { const { data } = await http.put(@js(url('admin/media')) + '/' + this.detail.id, { alt: this.detail.alt, name: this.detail.name }); toast(data.message) }
            catch (e) { toast(errorMessage(e), 'error') }
        },
     }">

    <div class="grid sm:grid-cols-3 gap-4 mb-5">
        <div class="card p-4 flex items-center gap-3">
            <span class="grid place-items-center w-11 h-11 rounded-xl bg-accent-100 text-accent-800 dark:bg-accent-500/15 dark:text-accent-400"><x-icon name="image" :size="20" /></span>
            <div><p class="text-2xl font-bold tabular-nums">{{ $totals['images'] }}</p><p class="text-xs text-muted">{{ __('Images') }}</p></div>
        </div>
        <div class="card p-4 flex items-center gap-3">
            <span class="grid place-items-center w-11 h-11 rounded-xl bg-brand-soft text-primary-700 dark:text-primary-300"><x-icon name="video" :size="20" /></span>
            <div><p class="text-2xl font-bold tabular-nums">{{ $totals['videos'] }}</p><p class="text-xs text-muted">{{ __('Videos & embeds') }}</p></div>
        </div>
        <div class="card p-4 flex items-center gap-3">
            <span class="grid place-items-center w-11 h-11 rounded-xl bg-success-soft text-success"><x-icon name="folder" :size="20" /></span>
            <div><p class="text-2xl font-bold tabular-nums" dir="ltr">{{ $size }}</p><p class="text-xs text-muted">{{ __('Original files') }}</p></div>
        </div>
    </div>

    {{-- Upload --}}
    <div class="card p-5 mb-5" x-data="uploadQueue({ folder: 'library' })" @media-uploaded.window.debounce.1200ms="location.reload()">
        @include('admin.partials.dropzone')
    </div>

    {{-- Filters --}}
    <form method="GET" class="card p-3 mb-5 flex flex-wrap items-center gap-2">
        <div class="relative flex-1 min-w-52">
            <x-icon name="search" :size="16" class="absolute top-1/2 -translate-y-1/2 start-3.5 text-subtle" />
            <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="{{ __('Search by file name…') }}" class="field ps-10 h-10 py-0">
        </div>
        <select name="type" class="field w-auto h-10 py-0" onchange="this.form.submit()">
            <option value="">{{ __('All types') }}</option>
            <option value="image" @selected(($filters['type'] ?? '') === 'image')>{{ __('Images') }}</option>
            <option value="video" @selected(($filters['type'] ?? '') === 'video')>{{ __('Videos') }}</option>
            <option value="embed" @selected(($filters['type'] ?? '') === 'embed')>{{ __('Embeds') }}</option>
        </select>
        <label class="flex items-center gap-2 text-sm text-muted px-2">
            <input type="checkbox" name="unused" value="1" @checked($filters['unused'] ?? false) onchange="this.form.submit()"> {{ __('Unused only') }}
        </label>
        @if (array_filter($filters))
            <a href="{{ route('admin.media.index') }}" class="btn-ghost h-10">{{ __('Reset') }}</a>
        @endif
        <button x-show="selected.length" x-cloak type="button" @click="bulkDelete()" class="btn-danger h-10 ms-auto">
            <x-icon name="trash" :size="16" /> {{ __('Delete') }} (<span x-text="selected.length"></span>)
        </button>
    </form>

    {{-- Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 2xl:grid-cols-7 gap-3">
        @forelse ($items as $item)
            @php $uses = ($usage[$item->id] ?? 0) + ($covers[$item->id] ?? 0); @endphp
            <div id="media-{{ $item->id }}" class="group card overflow-hidden" :class="selected.includes({{ $item->id }}) && 'ring-4 ring-accent-500/40 border-accent-500'">
                <button type="button" class="relative block w-full aspect-square checkerboard" @click="detail = @js($item->toPicker() + ['uses' => $uses, 'original' => $item->url(null)])">
                    @if ($item->thumbUrl())
                        <img src="{{ $item->thumbUrl() }}" alt="" loading="lazy" class="w-full h-full object-cover">
                    @else
                        <span class="absolute inset-0 grid place-items-center bg-secondary-900 text-white/50"><x-icon name="video" :size="30" /></span>
                    @endif
                    @if (! $item->isImage())
                        <span class="absolute bottom-2 start-2 badge bg-black/65 text-white"><x-icon name="play" :size="11" /> {{ $item->embed_provider ?? 'video' }}</span>
                    @endif
                </button>
                <div class="relative px-2.5 py-2">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" :checked="selected.includes({{ $item->id }})" @change="toggle({{ $item->id }})" class="shrink-0">
                        <p class="text-[11px] font-semibold truncate flex-1 text-start" dir="ltr" title="{{ $item->name }}">{{ $item->name }}</p>
                    </div>
                    <p class="text-[10px] text-subtle mt-0.5 flex items-center gap-1.5">
                        <span dir="ltr">{{ $item->isEmbed() ? '—' : $item->humanSize() }}</span>
                        @if ($item->width)<span dir="ltr">· {{ $item->width }}×{{ $item->height }}</span>@endif
                        @if ($uses)<span class="ms-auto text-success">● {{ $uses }}</span>@endif
                    </p>
                </div>
            </div>
        @empty
            <div class="col-span-full card py-16 text-center text-muted">{{ __('No media found.') }}</div>
        @endforelse
    </div>

    <div class="mt-6">{{ $items->links() }}</div>

    {{-- Detail drawer --}}
    <div x-show="detail" x-cloak class="fixed inset-0 z-50 flex justify-end bg-secondary-900/50" @keydown.escape.window="detail = null" @click.self="detail = null">
        <template x-if="detail">
            <div class="w-full max-w-md h-full bg-raised border-s border-line shadow-2xl flex flex-col" x-transition>
                <div class="flex items-center justify-between px-5 h-14 border-b border-line">
                    <h3 class="font-bold truncate" x-text="detail.name"></h3>
                    <button type="button" @click="detail = null" class="btn-icon"><x-icon name="x" /></button>
                </div>
                <div class="flex-1 overflow-y-auto p-5 space-y-4">
                    <div class="rounded-xl overflow-hidden checkerboard">
                        <template x-if="detail.type === 'image'"><img :src="detail.url" class="w-full" alt=""></template>
                        <template x-if="detail.type === 'video'"><video :src="detail.original" :poster="detail.poster?.url" controls class="w-full bg-black"></video></template>
                        <template x-if="detail.type === 'embed'"><iframe :src="detail.embed" class="w-full aspect-video" allowfullscreen></iframe></template>
                    </div>
                    <dl class="grid grid-cols-2 gap-3 text-sm">
                        <div><dt class="text-xs text-muted">{{ __('Type') }}</dt><dd class="font-semibold" x-text="detail.mime || detail.provider"></dd></div>
                        <div><dt class="text-xs text-muted">{{ __('Size') }}</dt><dd class="font-semibold" dir="ltr" x-text="detail.size"></dd></div>
                        <div><dt class="text-xs text-muted">{{ __('Dimensions') }}</dt><dd class="font-semibold" dir="ltr" x-text="detail.width ? detail.width + ' × ' + detail.height : '—'"></dd></div>
                        <div><dt class="text-xs text-muted">{{ __('Used in') }}</dt><dd class="font-semibold" x-text="detail.uses + ' ' + @js(__('places'))"></dd></div>
                    </dl>
                    <div><label class="label">{{ __('File name') }}</label><input x-model="detail.name" class="field" dir="auto"></div>
                    <div><label class="label">{{ __('Alt text') }} (عربي)</label><input x-model="detail.alt.ar" class="field" dir="rtl" placeholder="{{ __('Describe the image for accessibility & SEO') }}"></div>
                    <div><label class="label">{{ __('Alt text') }} (English)</label><input x-model="detail.alt.en" class="field" dir="ltr"></div>
                    <a :href="detail.original" target="_blank" class="text-sm text-primary-600 dark:text-accent-500 hover:underline inline-flex items-center gap-1"><x-icon name="external" :size="14" /> {{ __('Open original') }}</a>
                </div>
                <div class="flex items-center gap-2 p-4 border-t border-line">
                    <button type="button" @click="saveDetail()" class="btn-primary flex-1">{{ __('Save') }}</button>
                    <button type="button" class="btn-ghost hover:text-danger" :disabled="detail.uses > 0" :title="detail.uses ? @js(__('In use — remove it from projects first')) : ''"
                            @click="deleteResource(@js(url('admin/media')) + '/' + detail.id, { title: @js(__('Delete this file?')), el: document.getElementById('media-' + detail.id) }).then(ok => ok && (detail = null))">
                        <x-icon name="trash" :size="16" />
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>
@endsection
