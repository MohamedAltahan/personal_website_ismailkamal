@php
    $items = collect($data['items'] ?? [])->filter(fn ($i) => tr($i['value'] ?? ''));
    $cols = max(1, min(6, (int) $data['columns']));
@endphp
@if ($items->count())
    <div class="{{ $wrap }}">
        <dl class="grid grid-cols-2 md:grid-cols-[repeat(var(--cols),minmax(0,1fr))] gap-x-8 gap-y-8 border-t border-line pt-8" style="--cols: {{ $cols }}" {{ $reveal }}>
            @foreach ($items as $item)
                <div>
                    <dt class="text-xs uppercase tracking-widest text-mute mb-2">{{ tr($item['label'] ?? '') }}</dt>
                    <dd class="text-lg font-semibold">{{ tr($item['value']) }}</dd>
                </div>
            @endforeach
        </dl>
    </div>
@endif
