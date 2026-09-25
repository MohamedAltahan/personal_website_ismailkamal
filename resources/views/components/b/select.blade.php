@props(['key', 'label', 'options' => [], 'obj' => 'sel.data', 'number' => false])
<div>
    <label class="block text-xs font-semibold text-muted mb-1.5">{{ $label }}</label>
    <select class="field text-sm h-10 py-0" x-model{{ $number ? '.number' : '' }}="{{ $obj }}.{{ $key }}">
        @foreach ($options as $value => $text)
            <option value="{{ $value }}">{{ $text }}</option>
        @endforeach
    </select>
</div>
