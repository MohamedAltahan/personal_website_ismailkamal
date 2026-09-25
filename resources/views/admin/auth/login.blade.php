@extends('layouts.guest')

@section('title', __('Sign in'))

@section('content')
    <h2 class="text-2xl font-bold mb-1.5">{{ __('Welcome back') }} 👋</h2>
    <p class="text-sm text-muted mb-8">{{ __('Sign in to manage your portfolio.') }}</p>

    <form method="POST" action="{{ route('admin.login') }}" class="space-y-5" x-data="{ show: false }">
        @csrf
        <div>
            <label class="label" for="email">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" dir="ltr" class="field h-12">
            @error('email')<p class="mt-1.5 text-xs text-danger">{{ $message }}</p>@enderror
        </div>

        <div>
            <div class="flex items-center justify-between">
                <label class="label" for="password">{{ __('Password') }}</label>
                <a href="{{ route('admin.password.request') }}" class="text-xs text-primary-600 dark:text-accent-500 hover:underline mb-1.5">{{ __('Forgot password?') }}</a>
            </div>
            <div class="relative">
                <input id="password" name="password" :type="show ? 'text' : 'password'" required autocomplete="current-password" dir="ltr" class="field h-12 pe-11">
                <button type="button" @click="show = !show" class="absolute top-1/2 -translate-y-1/2 end-3 text-subtle hover:text-ink">
                    <span x-show="!show"><x-icon name="eye" /></span>
                    <span x-show="show" x-cloak><x-icon name="eye-off" /></span>
                </button>
            </div>
        </div>

        <label class="flex items-center gap-2.5 text-sm text-muted">
            <input type="checkbox" name="remember" value="1"> {{ __('Remember me') }}
        </label>

        <button type="submit" class="btn-primary w-full h-12 text-base">{{ __('Sign in') }}</button>
    </form>
@endsection
