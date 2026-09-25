{{-- Single media slot. accept: JS array literal of types. --}}
@props(['key' => 'media', 'label', 'obj' => 'sel.data', 'accept' => "['image']", 'hint' => null])
<div>
    <label class="block text-xs font-semibold text-muted mb-1.5">{{ $label }}</label>
    <template x-if="m({{ $obj }}.{{ $key }})">
        <div class="group relative rounded-field overflow-hidden border border-line checkerboard">
            <img :src="m({{ $obj }}.{{ $key }}).thumb" x-show="m({{ $obj }}.{{ $key }}).thumb" class="w-full h-36 object-cover" alt="">
            <div x-show="!m({{ $obj }}.{{ $key }}).thumb" class="w-full h-36 grid place-items-center bg-secondary-900 text-white/60"><x-icon name="video" :size="30" /></div>
            <span x-show="m({{ $obj }}.{{ $key }}).type !== 'image'" class="absolute top-2 start-2 badge bg-black/60 text-white" x-text="m({{ $obj }}.{{ $key }}).provider || 'video'"></span>
            <div class="absolute inset-x-0 bottom-0 flex items-center gap-1 p-2 bg-gradient-to-t from-black/70 to-transparent">
                <span class="flex-1 text-[11px] text-white truncate" x-text="m({{ $obj }}.{{ $key }}).name"></span>
                <button type="button" @click="pickMedia({{ $obj }}, '{{ $key }}', {{ $accept }})" class="h-7 px-2.5 rounded-full bg-white/90 text-secondary-900 text-[11px] font-bold">{{ __('Replace') }}</button>
                <button type="button" @click="{{ $obj }}.{{ $key }} = null" class="grid place-items-center w-7 h-7 rounded-full bg-white/90 text-danger"><x-icon name="trash" :size="13" /></button>
            </div>
        </div>
    </template>
    <template x-if="!m({{ $obj }}.{{ $key }})">
        <button type="button" @click="pickMedia({{ $obj }}, '{{ $key }}', {{ $accept }})"
                class="w-full h-28 rounded-field border-2 border-dashed border-line hover:border-accent-500 hover:bg-accent-500/5 grid place-items-center text-muted transition">
            <span class="flex flex-col items-center gap-1.5 text-xs font-semibold">
                <x-icon name="plus" :size="20" /> {{ __('Choose media') }}
            </span>
        </button>
    </template>
    @if ($hint)<p class="mt-1 text-[11px] text-subtle">{{ $hint }}</p>@endif
</div>
