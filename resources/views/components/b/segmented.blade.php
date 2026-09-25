{{-- Button group for short option lists. $options: value => label (label may be an <x-icon> name when $icons). --}}
@props(['key', 'label' => null, 'options' => [], 'obj' => 'sel.data', 'icons' => false, 'number' => false])
<div>
    @if ($label)<label class="block text-xs font-semibold text-muted mb-1.5">{{ $label }}</label>@endif
    <div class="flex rounded-field bg-canvas p-1 gap-1">
        @foreach ($options as $value => $text)
            <button type="button" @click="{{ $obj }}.{{ $key }} = {{ $number ? $value : "'".$value."'" }}"
                    class="flex-1 h-8 rounded-md text-xs font-semibold transition grid place-items-center"
                    :class="{{ $obj }}.{{ $key }} == {{ $number ? $value : "'".$value."'" }} ? 'bg-surface text-ink shadow-sm' : 'text-muted hover:text-ink'"
                    @if ($icons) title="{{ $text[1] ?? '' }}" @endif>
                @if ($icons)<x-icon :name="$text[0]" :size="16" />@else{{ $text }}@endif
            </button>
        @endforeach
    </div>
</div>
