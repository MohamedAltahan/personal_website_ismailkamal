@extends('layouts.guest')

@section('title', __('Reset password'))

@section('content')
    <h2 class="text-2xl font-bold mb-8">{{ __('Choose a new password') }}</h2>

    <form method="POST" action="{{ route('admin.password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div>
            <label class="label" for="email">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required dir="ltr" class="field h-12">
            @error('email')<p class="mt-1.5 text-xs text-danger">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="label" for="password">{{ __('New password') }}</label>
            <input id="password" name="password" type="password" required autocomplete="new-password" dir="ltr" class="field h-12">
            @error('password')<p class="mt-1.5 text-xs text-danger">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="label" for="password_confirmation">{{ __('Confirm password') }}</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required dir="ltr" class="field h-12">
        </div>
        <button type="submit" class="btn-primary w-full h-12">{{ __('Reset password') }}</button>
    </form>
@endsection
