@props(['key', 'label', 'obj' => 'sel.data', 'hint' => null])
<label class="flex items-center justify-between gap-3 cursor-pointer py-1">
    <span>
        <span class="block text-sm font-semibold">{{ $label }}</span>
        @if ($hint)<span class="block text-xs text-subtle">{{ $hint }}</span>@endif
    </span>
    <button type="button" role="switch" :aria-checked="!!{{ $obj }}.{{ $key }}" @click="{{ $obj }}.{{ $key }} = !{{ $obj }}.{{ $key }}"
            class="relative shrink-0 w-10 h-6 rounded-full transition"
            :class="{{ $obj }}.{{ $key }} ? 'bg-primary-900 dark:bg-accent-500' : 'bg-gray-300 dark:bg-white/15'">
        <span class="absolute top-1 w-4 h-4 rounded-full bg-white shadow transition-all"
              :class="{{ $obj }}.{{ $key }} ? 'start-5' : 'start-1'"></span>
    </button>
</label>
