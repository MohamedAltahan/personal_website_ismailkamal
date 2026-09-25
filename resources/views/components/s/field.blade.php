{{-- Settings input. key = "group.key". translatable renders ar + en inputs side by side. --}}
@props(['key', 'label', 'type' => 'text', 'translatable' => false, 'hint' => null, 'dir' => null, 'placeholder' => null, 'multiline' => false])
@php
    [$group, $name] = explode('.', $key, 2);
    $value = setting($key);
    $inputName = fn ($locale = null) => "s[{$group}][{$name}]".($locale ? "[{$locale}]" : '');
    $classes = 'field';
@endphp
<div>
    <label class="label">{{ $label }}</label>
    @if ($translatable)
        <div class="grid sm:grid-cols-2 gap-3">
            @foreach (locales() as $locale => $info)
                <div class="relative">
                    @if ($multiline)
                        <textarea name="{{ $inputName($locale) }}" rows="3" dir="{{ $info['dir'] }}" class="{{ $classes }}" placeholder="{{ $info['name'] }}">{{ old("s.$group.$name.$locale", $value[$locale] ?? '') }}</textarea>
                    @else
                        <input type="{{ $type }}" name="{{ $inputName($locale) }}" value="{{ old("s.$group.$name.$locale", $value[$locale] ?? '') }}" dir="{{ $info['dir'] }}" class="{{ $classes }} pe-12" placeholder="{{ $placeholder }}">
                        <span class="absolute top-1/2 -translate-y-1/2 end-3 text-[10px] font-bold uppercase text-subtle">{{ $locale }}</span>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <input type="{{ $type }}" name="{{ $inputName() }}" value="{{ old("s.$group.$name", $value) }}" class="{{ $classes }}"
               @if ($dir) dir="{{ $dir }}" @endif placeholder="{{ $placeholder }}" {{ $attributes }}>
    @endif
    @if ($hint)<p class="mt-1.5 text-xs text-subtle">{{ $hint }}</p>@endif
</div>
