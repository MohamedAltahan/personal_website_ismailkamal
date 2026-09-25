@php $h = ['sm' => '1.5rem', 'md' => '4rem', 'lg' => '8rem', 'xl' => '12rem'][$data['size']] ?? '4rem'; @endphp
<div class="{{ $wrap }} flex items-center" style="height: {{ $h }}">
    @if ($data['line'])<hr class="w-full border-line">@endif
</div>
