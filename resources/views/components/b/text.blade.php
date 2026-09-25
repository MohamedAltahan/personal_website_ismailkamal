{{-- Translatable text field bound to  {{ $obj }}.{{ $key }}[lang]  (obj defaults to the selected block's data). --}}
@props(['key', 'label', 'obj' => 'sel.data', 'multiline' => false, 'rows' => 3, 'placeholder' => '', 'plain' => false, 'dir' => null])
<div>
    <div class="flex items-center justify-between mb-1.5">
        <label class="text-xs font-semibold text-muted">{{ $label }}</label>
        @unless ($plain)
            <span class="flex items-center gap-1">
                <span class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded bg-canvas text-subtle" x-text="lang"></span>
                <button type="button" x-show="!{{ $obj }}.{{ $key }}[lang] && {{ $obj }}.{{ $key }}[other]" @click="copyFromOther({{ $obj }}, '{{ $key }}')"
                        class="text-[10px] font-bold text-primary-600 dark:text-accent-500 hover:underline">{{ __('Copy from other language') }}</button>
            </span>
        @endunless
    </div>
    @if ($multiline)
        <textarea rows="{{ $rows }}" class="field text-sm" placeholder="{{ $placeholder }}"
                  @if ($plain) x-model="{{ $obj }}.{{ $key }}" @else x-model="{{ $obj }}.{{ $key }}[lang]" :dir="lang === 'ar' ? 'rtl' : 'ltr'" @endif></textarea>
    @else
        <input type="text" class="field text-sm h-10 py-0" placeholder="{{ $placeholder }}"
               @if ($dir) dir="{{ $dir }}" @endif
               @if ($plain) x-model="{{ $obj }}.{{ $key }}" @else x-model="{{ $obj }}.{{ $key }}[lang]" :dir="lang === 'ar' ? 'rtl' : 'ltr'" @endif>
    @endif
</div>
