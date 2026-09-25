@extends('layouts.dashboard')

@section('title', __('Social links'))
@section('page-title', __('Social links'))

@section('content')
<div class="max-w-3xl" x-data="{
        form: { id: null, name: 'instagram', link: '' },
        start(s = null) { this.form = s ? { ...s } : { id: null, name: 'instagram', link: '' }; $dispatch('open-modal', 'social') },
        async saveOrder() {
            const ids = [...$refs.list.querySelectorAll('[data-id]')].map(el => +el.dataset.id);
            try { const { data } = await http.post(@js(route('admin.socials.reorder')), { ids }); toast(data.message) } catch (e) { toast(errorMessage(e), 'error') }
        },
     }">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
        <div>
            <h2 class="text-xl font-bold">{{ __('Social links') }}</h2>
            <p class="text-sm text-muted">{{ __('Shown in the footer, contact page and mobile menu. Drag to reorder.') }}</p>
        </div>
        <button type="button" @click="start()" class="btn-primary h-11"><x-icon name="plus" :stroke="2.4" /> {{ __('Add link') }}</button>
    </div>

    <div class="card divide-y divide-line overflow-hidden" x-ref="list" x-sort="saveOrder()">
        @forelse ($socials as $social)
            <div id="social-{{ $social->id }}" data-id="{{ $social->id }}" x-sort:item="{{ $social->id }}" class="flex items-center gap-4 px-4 py-3 bg-surface">
                <span x-sort:handle class="text-subtle hover:text-ink cursor-grab"><x-icon name="drag" /></span>
                <span class="grid place-items-center w-10 h-10 rounded-full bg-secondary-900 text-accent-500"><x-social-icon :platform="$social->platform()" /></span>
                <div class="min-w-0 flex-1">
                    <p class="font-bold capitalize">{{ $social->name }}</p>
                    <a href="{{ $social->link }}" target="_blank" class="block text-xs text-muted truncate hover:underline" dir="ltr">{{ $social->link }}</a>
                </div>
                <button type="button" x-data="ajaxToggle(@js(route('admin.socials.toggle', $social)), @js($social->status === 'active'))" @click="toggle()"
                        class="badge" :class="value ? 'bg-success-soft text-success' : 'bg-canvas text-muted'" x-text="value ? @js(__('Visible')) : @js(__('Hidden'))"></button>
                <button type="button" @click="start(@js(['id' => $social->id, 'name' => $social->platform(), 'link' => $social->link]))" class="btn-icon"><x-icon name="edit" :size="17" /></button>
                <button type="button" class="btn-icon hover:text-danger" @click="deleteResource(@js(route('admin.socials.destroy', $social)), { title: @js(__('Delete this link?')), el: document.getElementById('social-{{ $social->id }}') })"><x-icon name="trash" :size="17" /></button>
            </div>
        @empty
            <p class="py-14 text-center text-muted">{{ __('No links yet.') }}</p>
        @endforelse
    </div>

    <x-modal name="social" :title="__('Social link')" maxWidth="lg">
        <form method="POST" :action="form.id ? @js(url('admin/socials')) + '/' + form.id : @js(route('admin.socials.store'))" class="p-6 space-y-4">
            @csrf
            <template x-if="form.id"><input type="hidden" name="_method" value="PUT"></template>
            <div>
                <label class="label">{{ __('Platform') }}</label>
                <div class="grid grid-cols-6 gap-2">
                    @foreach ($platforms as $platform)
                        @continue($platform === 'twitter')
                        <button type="button" @click="form.name = '{{ $platform }}'" title="{{ ucfirst($platform) }}"
                                class="grid place-items-center h-11 rounded-xl border transition"
                                :class="form.name === '{{ $platform }}' ? 'border-accent-500 bg-accent-500 text-secondary-900' : 'border-line hover:border-subtle text-muted'">
                            <x-social-icon :platform="$platform" :size="18" />
                        </button>
                    @endforeach
                </div>
                <input type="hidden" name="name" :value="form.name">
                <p class="mt-2 text-sm font-semibold capitalize" x-text="form.name"></p>
            </div>
            <div>
                <label class="label">{{ __('Link') }}</label>
                <input name="link" x-model="form.link" required class="field" dir="ltr" :placeholder="form.name === 'email' ? 'name@example.com' : 'https://'">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="$dispatch('close-modal', 'social')" class="btn-ghost">{{ __('Cancel') }}</button>
                <button class="btn-primary">{{ __('Save') }}</button>
            </div>
        </form>
    </x-modal>
</div>
@endsection
