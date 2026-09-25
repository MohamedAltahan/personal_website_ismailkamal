@php
    $count = max(1, min(4, (int) $data['count']));
    $cols = array_slice($data['cols'] ?? [], 0, $count);
    $gap = ['sm' => '1rem', 'md' => 'clamp(1.25rem,3vw,2.5rem)', 'lg' => 'clamp(2rem,5vw,5rem)'][$data['gap']] ?? '2rem';
    $valign = ['start' => 'items-start', 'center' => 'items-center', 'end' => 'items-end'][$data['valign']] ?? 'items-start';
@endphp
<div class="{{ $wrap }}">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[repeat(var(--cols),minmax(0,1fr))] {{ $valign }}" style="--cols: {{ $count }}; gap: {{ $gap }}">
        @foreach ($cols as $i => $col)
            <div {{ $reveal }} style="--reveal-delay: {{ $i * 0.08 }}s">
                @if (in_array($col['kind'] ?? 'text', ['image', 'video'], true) && ($item = $media[$col['media'] ?? 0] ?? null))
                    <x-media :media="$item" fit="natural" :lightbox="$item->isImage()" :sizes="'(min-width: 1024px) '.round(100 / $count).'vw, 100vw'"
                             :video="['controls' => true]" class="{{ $item->isImage() ? '' : 'aspect-video' }}" />
                    @if (tr($col['caption'] ?? ''))<p class="mt-2 text-sm text-mute">{{ tr($col['caption']) }}</p>@endif
                @else
                    <div class="prose-site">{!! tr($col['html'] ?? '') !!}</div>
                @endif
            </div>
        @endforeach
    </div>
</div>
