@props(['key', 'label', 'hint' => null])
@php
    [$group, $name] = explode('.', $key, 2);
    $on = (bool) setting($key);
@endphp
<label class="flex items-start justify-between gap-4 py-3 cursor-pointer" x-data="{ on: @js($on) }">
    <span>
        <span class="block text-sm font-bold">{{ $label }}</span>
        @if ($hint)<span class="block text-xs text-muted mt-0.5">{{ $hint }}</span>@endif
    </span>
    <input type="hidden" name="s[{{ $group }}][{{ $name }}]" :value="on ? 1 : 0">
    <button type="button" role="switch" :aria-checked="on" @click="on = !on" class="relative shrink-0 w-11 h-6 rounded-full transition mt-0.5"
            :class="on ? 'bg-primary-900 dark:bg-accent-500' : 'bg-gray-300 dark:bg-white/15'">
        <span class="absolute top-1 w-4 h-4 rounded-full bg-white shadow transition-all" :class="on ? 'start-6' : 'start-1'"></span>
    </button>
</label>
