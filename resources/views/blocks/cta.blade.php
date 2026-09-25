<div class="{{ $wrap }}">
    <div class="relative overflow-hidden rounded-[clamp(1rem,3vw,2.5rem)] bg-ink text-paper px-[clamp(1.5rem,6vw,6rem)] py-[clamp(3rem,8vw,7rem)]" {{ $reveal }}>
        <div class="absolute -top-24 -end-24 w-80 h-80 rounded-full bg-brand/20 blur-3xl"></div>
        <div class="relative flex flex-col lg:flex-row lg:items-end gap-10 justify-between">
            <div class="max-w-3xl">
                <h2 class="text-display font-display">{{ tr($data['title']) }}</h2>
                @if (tr($data['text']))
                    <p class="mt-6 text-lg opacity-70 max-w-xl">{{ tr($data['text']) }}</p>
                @endif
            </div>
            <a href="{{ $data['button_url'] ?: lroute('contact') }}" class="group shrink-0 inline-flex items-center gap-3 h-16 ps-8 pe-2 rounded-full bg-brand text-brand-ink text-lg font-semibold">
                {{ tr($data['button_label']) ?: __('Get in touch') }}
                <span class="grid place-items-center w-12 h-12 rounded-full bg-brand-ink text-brand group-hover:rotate-45 transition-transform duration-500">
                    <x-icon name="arrow-up-end" :size="20" class="rtl:-scale-x-100" />
                </span>
            </a>
        </div>
    </div>
</div>
