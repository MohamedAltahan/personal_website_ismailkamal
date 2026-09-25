@extends('layouts.site')

@section('title', __('Page not found'))

@section('content')
    <section class="container-site min-h-[80svh] flex flex-col justify-center pt-[var(--header-h)]">
        <p class="font-display font-bold leading-none text-[clamp(7rem,28vw,22rem)] text-brand select-none" aria-hidden="true">404</p>
        <h1 class="text-4xl sm:text-6xl font-display font-bold mt-4">{{ __('This frame is missing.') }}</h1>
        <p class="mt-5 text-lg text-mute max-w-xl">{{ __('The page you are looking for was moved or never existed. Let’s get you back to the work.') }}</p>
        <div class="mt-10 flex flex-wrap gap-3">
            <a href="{{ lroute('home') }}" class="inline-flex items-center gap-2 h-14 px-7 rounded-full bg-brand text-brand-ink font-semibold">{{ __('Home') }}</a>
            <a href="{{ lroute('work.index') }}" class="inline-flex items-center gap-2 h-14 px-7 rounded-full border border-ink font-semibold hover:bg-ink hover:text-paper transition-colors">{{ __('See the work') }}</a>
        </div>
    </section>
@endsection
