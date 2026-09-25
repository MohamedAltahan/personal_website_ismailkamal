@extends('layouts.site')

@section('title', __('Contact'))
@section('description', __("Have a project in mind? Let's talk."))

@php
    $email = setting('general.contact_email');
    $phone = setting('general.contact_phone');
    $whatsapp = preg_replace('/\D+/', '', (string) setting('general.whatsapp'));
    if ($whatsapp && str_starts_with($whatsapp, '0')) {
        $whatsapp = '2'.$whatsapp; // Egyptian local numbers (01…) → international.
    }
    $address = setting()->text('general.contact_address');
    $field = 'w-full bg-transparent border-0 border-b border-line focus:border-ink focus:ring-0 outline-none px-0 py-4 text-lg placeholder:text-mute/70 transition-colors';
@endphp

@section('content')
    <section class="container-site pt-[calc(var(--header-h)+clamp(3rem,8vw,7rem))]">
        <div class="grid lg:grid-cols-12 gap-[clamp(3rem,6vw,6rem)]">
            <div class="lg:col-span-5">
                <p class="flex items-center gap-2 text-sm text-mute mb-6" data-reveal>
                    <span class="w-2 h-2 rounded-full bg-brand"></span> {{ __('Contact') }}
                </p>
                <h1 class="text-display font-display" data-reveal style="--reveal-delay:.06s">{{ __("Let's talk about your next project") }}</h1>
                <p class="mt-8 text-lg text-mute max-w-md" data-reveal style="--reveal-delay:.12s">
                    {{ __('Tell me a little about what you have in mind — I usually reply within a day.') }}
                </p>

                <dl class="mt-12 space-y-7" data-reveal style="--reveal-delay:.18s">
                    @if ($email)
                        <div>
                            <dt class="text-xs uppercase tracking-widest text-mute mb-1.5">{{ __('Email') }}</dt>
                            <dd><a href="mailto:{{ $email }}" class="text-xl sm:text-2xl link-underline" dir="ltr">{{ $email }}</a></dd>
                        </div>
                    @endif
                    @if ($phone)
                        <div>
                            <dt class="text-xs uppercase tracking-widest text-mute mb-1.5">{{ __('Phone') }}</dt>
                            <dd class="flex flex-wrap items-center gap-3">
                                <a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}" class="text-xl sm:text-2xl link-underline" dir="ltr">{{ $phone }}</a>
                                @if ($whatsapp)
                                    <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 h-9 px-4 rounded-full bg-[#25d366] text-white text-sm font-semibold">
                                        <x-social-icon platform="whatsapp" :size="16" /> WhatsApp
                                    </a>
                                @endif
                            </dd>
                        </div>
                    @endif
                    @if ($address)
                        <div>
                            <dt class="text-xs uppercase tracking-widest text-mute mb-1.5">{{ __('Based in') }}</dt>
                            <dd class="text-xl">{{ $address }}</dd>
                        </div>
                    @endif
                </dl>

                @include('site.partials.socials', ['class' => 'mt-10'])
            </div>

            <div class="lg:col-span-7" data-reveal style="--reveal-delay:.1s">
                <div x-data="contactForm(@js(lroute('contact.store')))" class="relative">
                    <div x-show="sent" x-cloak x-transition class="rounded-[1.5rem] bg-ink text-paper p-[clamp(2rem,5vw,4rem)]">
                        <span class="grid place-items-center w-16 h-16 rounded-full bg-brand text-brand-ink mb-6"><x-icon name="check" :size="28" :stroke="2.4" /></span>
                        <h2 class="text-4xl font-display font-bold mb-3">{{ __('Message sent!') }}</h2>
                        <p class="opacity-70 text-lg">{{ __('Thank you for reaching out. I will get back to you soon.') }}</p>
                        <button type="button" @click="sent = false" class="mt-8 underline underline-offset-4">{{ __('Send another message') }}</button>
                    </div>

                    <form x-show="!sent" @submit.prevent="submit($event)" method="POST" action="{{ lroute('contact.store') }}" class="space-y-2">
                        @csrf
                        <div class="hidden" aria-hidden="true"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>

                        <div class="grid sm:grid-cols-2 gap-x-8">
                            <div>
                                <input name="name" required maxlength="120" placeholder="{{ __('Your name') }} *" class="{{ $field }}" autocomplete="name">
                                <p x-show="errors.name" x-text="errors.name" class="text-sm text-red-500 mt-1"></p>
                            </div>
                            <div>
                                <input name="email" type="email" required maxlength="180" placeholder="{{ __('Email address') }} *" class="{{ $field }}" autocomplete="email" dir="auto">
                                <p x-show="errors.email" x-text="errors.email" class="text-sm text-red-500 mt-1"></p>
                            </div>
                            <div>
                                <input name="phone" type="tel" maxlength="40" placeholder="{{ __('Phone (optional)') }}" class="{{ $field }}" autocomplete="tel" dir="auto">
                            </div>
                            <div>
                                <input name="subject" maxlength="180" placeholder="{{ __('What is it about?') }}" class="{{ $field }}">
                            </div>
                        </div>
                        <div>
                            <textarea name="message" required rows="6" maxlength="5000" placeholder="{{ __('Tell me about your project…') }} *" class="{{ $field }} resize-none"></textarea>
                            <p x-show="errors.message" x-text="errors.message" class="text-sm text-red-500 mt-1"></p>
                        </div>
                        <p x-show="errors.form" x-text="errors.form" class="text-sm text-red-500"></p>

                        <div class="pt-8">
                            <button type="submit" :disabled="busy"
                                    class="group inline-flex items-center gap-3 h-16 ps-8 pe-2 rounded-full bg-brand text-brand-ink text-lg font-semibold disabled:opacity-60">
                                <span x-show="!busy">{{ __('Send message') }}</span>
                                <span x-show="busy" x-cloak>{{ __('Sending…') }}</span>
                                <span class="grid place-items-center w-12 h-12 rounded-full bg-brand-ink text-brand group-hover:rotate-45 transition-transform duration-500">
                                    <x-icon name="arrow-up-end" :size="20" class="rtl:-scale-x-100" />
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
