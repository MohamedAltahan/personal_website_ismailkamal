@props(['key', 'label', 'min' => 0, 'max' => 100, 'step' => 1, 'suffix' => '', 'obj' => 'sel.data'])
<div>
    <div class="flex items-center justify-between mb-1.5">
        <label class="text-xs font-semibold text-muted">{{ $label }}</label>
        <span class="text-xs font-bold tabular-nums" x-text="{{ $obj }}.{{ $key }} + '{{ $suffix }}'"></span>
    </div>
    <input type="range" min="{{ $min }}" max="{{ $max }}" step="{{ $step }}" x-model.number="{{ $obj }}.{{ $key }}" class="w-full">
</div>
