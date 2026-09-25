<div class="{{ $wrap }}">
    <figure class="text-center" {{ $reveal }}>
        <span class="block font-display text-8xl leading-none text-brand h-12">“</span>
        <blockquote class="text-xl sm:text-3xl font-display leading-snug">{{ tr($data['text']) }}</blockquote>
        @if (tr($data['author']))
            <figcaption class="mt-8">
                <span class="font-semibold">{{ tr($data['author']) }}</span>
                @if (tr($data['role']))<span class="text-mute"> — {{ tr($data['role']) }}</span>@endif
            </figcaption>
        @endif
    </figure>
</div>
