@extends('layouts.dashboard')

@section('title', $message->name)
@section('breadcrumb', __('Messages'))
@section('page-title', $message->subject ?: __('Message from :name', ['name' => $message->name]))

@section('content')
    <div class="max-w-3xl">
        <div class="flex items-center gap-2 mb-4">
            <a href="{{ route('admin.messages.index') }}" class="btn-ghost h-9"><x-icon name="chevron-start" :size="16" class="rtl:-scale-x-100" /> {{ __('Inbox') }}</a>
            <span class="ms-auto"></span>
            @if ($previous)<a href="{{ route('admin.messages.show', $previous) }}" class="btn-icon" title="{{ __('Newer') }}"><x-icon name="chevron-up" /></a>@endif
            @if ($next)<a href="{{ route('admin.messages.show', $next) }}" class="btn-icon" title="{{ __('Older') }}"><x-icon name="chevron-down" /></a>@endif
        </div>

        <div class="card">
            <div class="flex flex-wrap items-center gap-4 p-5 border-b border-line">
                <span class="grid place-items-center w-12 h-12 rounded-full bg-secondary-900 text-accent-500 text-lg font-bold">{{ mb_substr($message->name, 0, 1) }}</span>
                <div class="min-w-0 flex-1">
                    <p class="font-bold">{{ $message->name }}</p>
                    <p class="text-sm text-muted" dir="ltr">{{ $message->email }} @if ($message->phone) · {{ $message->phone }} @endif</p>
                </div>
                <p class="text-xs text-subtle">{{ $message->created_at?->translatedFormat('j F Y — H:i') }}</p>
            </div>
            <div class="p-6 text-[15px] leading-8 whitespace-pre-line" dir="auto">{{ $message->message }}</div>
            <div class="flex flex-wrap items-center gap-2 p-5 border-t border-line">
                <a href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: '.($message->subject ?: setting()->text('general.site_name'))) }}" class="btn-primary">
                    <x-icon name="mail" :size="16" /> {{ __('Reply by email') }}
                </a>
                @if ($message->phone)
                    <a href="https://wa.me/{{ preg_replace('/\D+/', '', $message->phone) }}" target="_blank" class="btn-ghost"><x-icon name="whatsapp" :size="16" /> WhatsApp</a>
                @endif
                <button type="button" class="btn-ghost" onclick="http.patch(@js(route('admin.messages.unread', $message))).then(() => location = @js(route('admin.messages.index')))">
                    {{ __('Mark as unread') }}
                </button>
                <button type="button" class="btn-ghost ms-auto hover:text-danger"
                        onclick="deleteResource(@js(route('admin.messages.destroy', $message)), { title: @js(__('Delete this message?')), redirect: @js(route('admin.messages.index')) })">
                    <x-icon name="trash" :size="16" /> {{ __('Delete') }}
                </button>
            </div>
        </div>
    </div>
@endsection
