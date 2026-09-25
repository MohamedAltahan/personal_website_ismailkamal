@extends('layouts.dashboard')

@section('title', __('Profile'))
@section('page-title', __('Profile'))

@section('content')
<div class="max-w-3xl space-y-5">
    <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="card"
          x-data="{ preview: @js($user->avatarUrl()) }">
        @csrf
        @method('PUT')
        <div class="px-6 py-4 border-b border-line">
            <h2 class="font-bold">{{ __('Account') }}</h2>
        </div>
        <div class="p-6 space-y-5">
            <div class="flex items-center gap-5">
                <label class="relative w-20 h-20 rounded-full overflow-hidden bg-secondary-900 text-accent-500 grid place-items-center text-2xl font-bold cursor-pointer group">
                    <img x-show="preview" :src="preview" class="absolute inset-0 w-full h-full object-cover" alt="">
                    <span x-show="!preview">{{ mb_substr($user->name, 0, 1) }}</span>
                    <span class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 grid place-items-center text-white transition"><x-icon name="upload" :size="20" /></span>
                    <input type="file" name="image" accept="image/*" class="hidden" @change="preview = URL.createObjectURL($event.target.files[0])">
                </label>
                <div>
                    <p class="font-bold">{{ $user->name }}</p>
                    <p class="text-sm text-muted">{{ __('Click the photo to change it.') }}</p>
                </div>
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div><label class="label">{{ __('Name') }}</label><input name="name" value="{{ old('name', $user->name) }}" class="field" required></div>
                <div><label class="label">{{ __('Email') }}</label><input name="email" type="email" value="{{ old('email', $user->email) }}" class="field" dir="ltr" required></div>
                <div>
                    <label class="label">{{ __('Dashboard language') }}</label>
                    <select name="locale" class="field">
                        @foreach (locales() as $code => $info)
                            <option value="{{ $code }}" @selected(($user->locale ?? app()->getLocale()) === $code)>{{ $info['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-line flex justify-end">
            <button class="btn-primary">{{ __('Save changes') }}</button>
        </div>
    </form>

    <form id="password" method="POST" action="{{ route('admin.profile.password') }}" class="card">
        @csrf
        @method('PUT')
        <div class="px-6 py-4 border-b border-line">
            <h2 class="font-bold">{{ __('Change password') }}</h2>
        </div>
        <div class="p-6 grid sm:grid-cols-3 gap-4">
            <div><label class="label">{{ __('Current password') }}</label><input name="current_password" type="password" class="field" dir="ltr" autocomplete="current-password" required></div>
            <div><label class="label">{{ __('New password') }}</label><input name="password" type="password" class="field" dir="ltr" autocomplete="new-password" required></div>
            <div><label class="label">{{ __('Confirm password') }}</label><input name="password_confirmation" type="password" class="field" dir="ltr" autocomplete="new-password" required></div>
        </div>
        <div class="px-6 py-4 border-t border-line flex justify-end">
            <button class="btn-primary">{{ __('Update password') }}</button>
        </div>
    </form>
</div>
@endsection
