@props(['key', 'label', 'media' => null, 'hint' => null, 'dark' => false])
@php [$group, $name] = explode('.', $key, 2); @endphp
<div x-data="mediaField(@js($media), ['image'])">
    <label class="label">{{ $label }}</label>
    <input type="hidden" name="s[{{ $group }}][{{ $name }}]" :value="media ? media.id : ''">
    <div class="flex items-center gap-4">
        <button type="button" @click="choose()" class="relative w-32 h-24 shrink-0 rounded-xl border border-line overflow-hidden grid place-items-center {{ $dark ? 'bg-secondary-900' : 'checkerboard' }}">
            <template x-if="media"><img :src="media.thumb || media.url" class="max-w-full max-h-full object-contain p-2" alt=""></template>
            <template x-if="!media"><span class="text-subtle"><x-icon name="image" :size="26" /></span></template>
        </button>
        <div class="space-y-2">
            <button type="button" @click="choose()" class="btn-ghost h-9"><x-icon name="upload" :size="15" /> <span x-text="media ? @js(__('Replace')) : @js(__('Choose'))"></span></button>
            <button type="button" x-show="media" @click="clear()" class="btn-ghost h-9 hover:text-danger"><x-icon name="trash" :size="15" /> {{ __('Remove') }}</button>
        </div>
    </div>
    @if ($hint)<p class="mt-1.5 text-xs text-subtle">{{ $hint }}</p>@endif
</div>
