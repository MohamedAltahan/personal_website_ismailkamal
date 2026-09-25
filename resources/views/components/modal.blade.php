@props(['name', 'title' => null, 'maxWidth' => '2xl'])
@php
    $mw = ['md' => 'max-w-md', 'lg' => 'max-w-lg', 'xl' => 'max-w-xl', '3xl' => 'max-w-3xl', '4xl' => 'max-w-4xl'][$maxWidth] ?? 'max-w-2xl';
@endphp
<div x-data="{ open: false }"
     x-on:open-modal.window="$event.detail === '{{ $name }}' && (open = true)"
     x-on:close-modal.window="$event.detail === '{{ $name }}' && (open = false)"
     x-on:keydown.escape.window="open = false"
     x-show="open" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-secondary-900/55 backdrop-blur-[2px]" @click="open = false"></div>
    <div class="relative w-full {{ $mw }} bg-raised border border-line rounded-card shadow-2xl max-h-[90vh] overflow-y-auto" x-show="open" x-transition>
        @if ($title)
            <div class="flex items-center justify-between px-6 py-4 border-b border-line">
                <h3 class="font-bold">{{ $title }}</h3>
                <button type="button" @click="open = false" class="btn-icon"><x-icon name="x" /></button>
            </div>
        @endif
        {{ $slot }}
    </div>
</div>
