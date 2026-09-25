@php
    $email = setting('general.contact_email');
    $phone = setting('general.contact_phone');
@endphp
<footer class="mt-24 border-t border-line">
    @unless (request()->routeIs('contact'))
        <div class="container-site pt-20 pb-16" data-reveal>
            <p class="flex items-center gap-2 text-sm text-mute mb-6">
                @if (setting('general.available_for_work'))
                    <span class="relative flex w-2.5 h-2.5">
                        <span class="absolute inline-flex h-full w-full rounded-full bg-brand opacity-75 animate-ping"></span>
                        <span class="relative inline-flex w-2.5 h-2.5 rounded-full bg-brand"></span>
                    </span>
                    {{ __('Available for new projects') }}
                @else
                    {{ __('Have a project in mind?') }}
                @endif
            </p>
            <a href="{{ lroute('contact') }}" data-cursor="{{ __('Say hi') }}" class="group inline-flex items-end gap-4 sm:gap-8">
                <span class="text-display font-display max-w-4xl group-hover:text-brand transition-colors duration-500">{{ __("Let's create something remarkable") }}</span>
                <span class="shrink-0 grid place-items-center w-14 h-14 sm:w-24 sm:h-24 rounded-full bg-brand text-brand-ink group-hover:rotate-45 transition-transform duration-500 mb-1">
                    <x-icon name="arrow-up-end" :size="36" :stroke="1.6" class="rtl:-scale-x-100" />
                </span>
            </a>
            @if ($email)
                <a href="mailto:{{ $email }}" class="block mt-8 text-xl sm:text-2xl link-underline w-fit" dir="ltr">{{ $email }}</a>
            @endif
        </div>
    @endunless

    <div class="container-site py-8 border-t border-line flex flex-col md:flex-row md:items-center gap-6 justify-between">
        <div class="flex items-center gap-4">
            <x-site.logo class="h-7" />
            <span class="text-sm text-mute">© {{ date('Y') }} — {{ __('All rights reserved.') }}</span>
        </div>
        @include('site.partials.socials')
        <button type="button" onclick="window.scrollTo({ top: 0, behavior: 'smooth' })" class="hidden md:inline-flex items-center gap-2 text-sm text-mute hover:text-ink">
            {{ __('Back to top') }} <x-icon name="arrow-up" :size="16" />
        </button>
    </div>
</footer>
