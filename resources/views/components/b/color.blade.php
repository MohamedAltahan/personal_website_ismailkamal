@props(['key', 'label', 'obj' => 'sel.style'])
<div>
    <label class="block text-xs font-semibold text-muted mb-1.5">{{ $label }}</label>
    <div class="flex items-center gap-2">
        <label class="relative w-10 h-10 shrink-0 rounded-field border border-line overflow-hidden checkerboard cursor-pointer">
            <span class="absolute inset-0" :style="{{ $obj }}.{{ $key }} ? `background:${ {{ $obj }}.{{ $key }} }` : ''"></span>
            <input type="color" class="absolute inset-0 opacity-0 cursor-pointer" :value="{{ $obj }}.{{ $key }} || '#ffffff'" @input="{{ $obj }}.{{ $key }} = $event.target.value">
        </label>
        <input type="text" class="field text-sm h-10 py-0 font-mono" dir="ltr" placeholder="{{ __('None') }}" x-model.lazy="{{ $obj }}.{{ $key }}">
        <button type="button" x-show="{{ $obj }}.{{ $key }}" @click="{{ $obj }}.{{ $key }} = null" class="btn-icon shrink-0"><x-icon name="x" :size="15" /></button>
    </div>
</div>
