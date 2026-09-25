@if (($socials ?? collect())->count())
    <div class="flex flex-wrap items-center gap-2 {{ $class ?? '' }}">
        @foreach ($socials as $social)
            <a href="{{ $social->link }}" target="_blank" rel="noopener me" aria-label="{{ ucfirst($social->name) }}" title="{{ ucfirst($social->name) }}"
               class="grid place-items-center w-11 h-11 rounded-full border border-line hover:bg-brand hover:text-brand-ink hover:border-brand transition-colors">
                <x-social-icon :platform="$social->platform()" />
            </a>
        @endforeach
    </div>
@endif
