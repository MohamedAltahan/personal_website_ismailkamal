@extends('layouts.guest')

@section('title', __('Forgot password'))

@section('content')
    <h2 class="text-2xl font-bold mb-1.5">{{ __('Forgot password') }}</h2>
    <p class="text-sm text-muted mb-8">{{ __('Enter your email and we will send you a link to choose a new password.') }}</p>

    <form method="POST" action="{{ route('admin.password.email') }}" class="space-y-5">
        @csrf
        <div>
            <label class="label" for="email">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus dir="ltr" class="field h-12">
            @error('email')<p class="mt-1.5 text-xs text-danger">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="btn-primary w-full h-12">{{ __('Send reset link') }}</button>
        <a href="{{ route('admin.login') }}" class="block text-center text-sm text-muted hover:text-ink">{{ __('Back to sign in') }}</a>
    </form>
@endsection
