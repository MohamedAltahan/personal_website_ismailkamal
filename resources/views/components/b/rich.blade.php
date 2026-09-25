{{-- Translatable rich text (Tiptap). Re-created when the block or language changes. --}}
@props(['key', 'label', 'obj' => 'sel.data', 'uid' => 'sel.id'])
<div>
    <div class="flex items-center justify-between mb-1.5">
        <label class="text-xs font-semibold text-muted">{{ $label }}</label>
        <span class="flex items-center gap-1">
            <span class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded bg-canvas text-subtle" x-text="lang"></span>
            <button type="button" x-show="!{{ $obj }}.{{ $key }}[lang] && {{ $obj }}.{{ $key }}[other]" @click="copyFromOther({{ $obj }}, '{{ $key }}')"
                    class="text-[10px] font-bold text-primary-600 dark:text-accent-500 hover:underline">{{ __('Copy from other language') }}</button>
        </span>
    </div>
    <template x-for="k in [{{ $uid }} + '-{{ $key }}-' + lang]" :key="k">
        <div class="rte rounded-field border border-line bg-field focus-within:border-primary-500 dark:focus-within:border-accent-500 overflow-hidden"
             x-data="rte(() => {{ $obj }}.{{ $key }}[lang], v => {{ $obj }}.{{ $key }}[lang] = v, lang === 'ar' ? 'rtl' : 'ltr', @js(__('Write here…')))">
            <div class="flex flex-wrap items-center gap-0.5 px-1.5 py-1 border-b border-line bg-surface">
                @php
                    $btn = 'grid place-items-center min-w-7 h-7 px-1 rounded-md text-muted hover:bg-canvas hover:text-ink text-xs font-bold';
                @endphp
                <button type="button" class="{{ $btn }}" :class="active.bold && 'bg-canvas text-ink'" @click="cmd('toggleBold')" title="Bold">B</button>
                <button type="button" class="{{ $btn }} italic" :class="active.italic && 'bg-canvas text-ink'" @click="cmd('toggleItalic')" title="Italic">I</button>
                <button type="button" class="{{ $btn }} underline" :class="active.underline && 'bg-canvas text-ink'" @click="cmd('toggleUnderline')" title="Underline">U</button>
                <span class="w-px h-5 bg-line mx-0.5"></span>
                <button type="button" class="{{ $btn }}" :class="active.h2 && 'bg-canvas text-ink'" @click="cmd('toggleHeading', { level: 2 })">H2</button>
                <button type="button" class="{{ $btn }}" :class="active.h3 && 'bg-canvas text-ink'" @click="cmd('toggleHeading', { level: 3 })">H3</button>
                <button type="button" class="{{ $btn }}" :class="active.bullet && 'bg-canvas text-ink'" @click="cmd('toggleBulletList')" title="List"><x-icon name="list" :size="15" /></button>
                <button type="button" class="{{ $btn }}" :class="active.quote && 'bg-canvas text-ink'" @click="cmd('toggleBlockquote')" title="Quote"><x-icon name="quote" :size="14" /></button>
                <button type="button" class="{{ $btn }}" :class="active.link && 'bg-canvas text-ink'" @click="link()" title="Link"><x-icon name="link" :size="15" /></button>
                <span class="w-px h-5 bg-line mx-0.5"></span>
                <button type="button" class="{{ $btn }}" :class="active.right && 'bg-canvas text-ink'" @click="cmd('setTextAlign', 'right')" title="Right">⇥</button>
                <button type="button" class="{{ $btn }}" :class="active.center && 'bg-canvas text-ink'" @click="cmd('setTextAlign', 'center')" title="Center">≡</button>
                <button type="button" class="{{ $btn }}" :class="active.left && 'bg-canvas text-ink'" @click="cmd('setTextAlign', 'left')" title="Left">⇤</button>
            </div>
            <div x-ref="editor"></div>
        </div>
    </template>
</div>
