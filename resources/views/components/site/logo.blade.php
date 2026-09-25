@props(['class' => 'h-8'])
@php
    $light = setting()->media('branding.logo');
    $dark = setting()->media('branding.logo_dark');
    $name = setting()->text('general.site_name');
@endphp
@if ($light || $dark)
    {{-- Light theme: dedicated logo, or the dark-theme logo inverted. --}}
    <img src="{{ ($light ?? $dark)->url(480) }}" alt="{{ $name }}"
         class="{{ $class }} w-auto object-contain dark:hidden {{ $light ? '' : 'invert' }}">
    <img src="{{ ($dark ?? $light)->url(480) }}" alt="{{ $name }}"
         class="{{ $class }} w-auto object-contain hidden dark:block {{ $dark ? '' : 'invert' }}">
@else
    <span class="font-display text-xl font-bold tracking-tight flex items-center gap-1.5">
        {{ $name }}<span class="w-2 h-2 rounded-full bg-brand inline-block"></span>
    </span>
@endif
