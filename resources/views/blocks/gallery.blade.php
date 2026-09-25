@php
    $items = collect($data['items'] ?? [])->map(fn ($i) => $i + ['m' => $media[$i['media'] ?? 0] ?? null])->filter(fn ($i) => $i['m'])->values();
    $cols = max(1, min(6, (int) $data['columns']));
    $gap = ['none' => '0px', 'sm' => '0.5rem', 'md' => 'clamp(0.75rem,1.5vw,1.25rem)', 'lg' => 'clamp(1.25rem,3vw,2.5rem)'][$data['gap']] ?? '1rem';
    $ratio = ['auto' => null, 'square' => '1/1', '4:3' => '4/3', '3:4' => '3/4', '16:9' => '16/9', '4:5' => '4/5'][$data['ratio']] ?? null;
    $rounded = $data['rounded'] ? 'rounded-[clamp(0.5rem,1.2vw,1rem)]' : '';
    $sizes = '(min-width: 768px) '.round(100 / $cols).'vw, 100vw';
@endphp
@if ($items->count())
    <div class="{{ $wrap }}">
        @switch($data['layout'])
            @case('stack')
                <div class="flex flex-col" style="gap: {{ $gap }}">
                    @foreach ($items as $item)
                        <figure {{ $reveal }}>
                            <x-media :media="$item['m']" fit="natural" :lightbox="$data['lightbox']" :alt="tr($item['caption'] ?? '') ?: null" class="{{ $rounded }}" />
                            @if (tr($item['caption'] ?? ''))<figcaption class="mt-2 text-sm text-mute">{{ tr($item['caption']) }}</figcaption>@endif
                        </figure>
                    @endforeach
                </div>
                @break

            @case('masonry')
                <div style="columns: {{ $cols }} 260px; column-gap: {{ $gap }}">
                    @foreach ($items as $item)
                        <figure class="break-inside-avoid" style="margin-bottom: {{ $gap }}" {{ $reveal }}>
                            <x-media :media="$item['m']" fit="natural" :lightbox="$data['lightbox']" :sizes="$sizes" class="{{ $rounded }}" />
                            @if (tr($item['caption'] ?? ''))<figcaption class="mt-2 text-sm text-mute">{{ tr($item['caption']) }}</figcaption>@endif
                        </figure>
                    @endforeach
                </div>
                @break

            @case('carousel')
                <div x-data="{ scroll(dir) { const el = $refs.track; el.scrollBy({ left: dir * el.clientWidth * 0.8 * (document.dir === 'rtl' ? -1 : 1), behavior: 'smooth' }) } }" class="relative">
                    <div x-ref="track" class="flex overflow-x-auto snap-x snap-mandatory [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" style="gap: {{ $gap }}">
                        @foreach ($items as $item)
                            <figure class="snap-start shrink-0" style="width: calc((100% - {{ $gap }} * {{ $cols - 1 }}) / {{ $cols }} * {{ $cols > 1 ? 1 : 0.9 }}); min-width: 70%;" >
                                <x-media :media="$item['m']" :ratio="$ratio ?? '4/3'" :lightbox="$data['lightbox']" :sizes="$sizes" class="{{ $rounded }}" />
                                @if (tr($item['caption'] ?? ''))<figcaption class="mt-2 text-sm text-mute">{{ tr($item['caption']) }}</figcaption>@endif
                            </figure>
                        @endforeach
                    </div>
                    <div class="flex justify-end gap-2 mt-5">
                        <button type="button" @click="scroll(-1)" class="grid place-items-center w-12 h-12 rounded-full border border-line hover:bg-brand hover:text-brand-ink hover:border-brand transition" aria-label="{{ __('Previous') }}"><x-icon name="chevron-start" /></button>
                        <button type="button" @click="scroll(1)" class="grid place-items-center w-12 h-12 rounded-full border border-line hover:bg-brand hover:text-brand-ink hover:border-brand transition" aria-label="{{ __('Next') }}"><x-icon name="chevron-end" /></button>
                    </div>
                </div>
                @break

            @default
                <div class="grid grid-cols-2 md:grid-cols-[repeat(var(--cols),minmax(0,1fr))]" style="--cols: {{ $cols }}; gap: {{ $gap }}">
                    @foreach ($items as $item)
                        @php $span = $item['span'] ?? '1'; @endphp
                        <figure class="{{ $span === 'full' ? 'col-span-full' : ($span === '2' ? 'col-span-2' : ($cols === 1 ? 'col-span-2 md:col-span-1' : '')) }}" {{ $reveal }}>
                            <x-media :media="$item['m']" :ratio="$ratio" :fit="$ratio ? 'cover' : 'natural'" :lightbox="$data['lightbox']" :sizes="$sizes" class="{{ $rounded }} {{ $ratio ? '' : 'h-full' }}" />
                            @if (tr($item['caption'] ?? ''))<figcaption class="mt-2 text-sm text-mute">{{ tr($item['caption']) }}</figcaption>@endif
                        </figure>
                    @endforeach
                </div>
        @endswitch
    </div>
@endif
