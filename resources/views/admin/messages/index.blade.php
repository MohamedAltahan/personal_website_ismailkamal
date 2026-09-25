@extends('layouts.dashboard')

@section('title', __('Messages'))
@section('page-title', __('Messages'))

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
        <div>
            <h2 class="text-xl font-bold">{{ __('Messages') }}</h2>
            <p class="text-sm text-muted">{{ __('Messages sent from the contact form on your website.') }}</p>
        </div>
    </div>

    <form method="GET" class="card p-3 mb-5 flex flex-wrap items-center gap-2">
        <div class="relative flex-1 min-w-52">
            <x-icon name="search" :size="16" class="absolute top-1/2 -translate-y-1/2 start-3.5 text-subtle" />
            <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="{{ __('Search name, email or message…') }}" class="field ps-10 h-10 py-0">
        </div>
        <div class="flex rounded-full bg-canvas p-1">
            <a href="{{ request()->fullUrlWithQuery(['filter' => null]) }}" class="tab h-8 {{ empty($filters['filter']) ? 'tab-active' : '' }}">{{ __('All') }}</a>
            <a href="{{ request()->fullUrlWithQuery(['filter' => 'unread']) }}" class="tab h-8 {{ ($filters['filter'] ?? null) === 'unread' ? 'tab-active' : '' }}">{{ __('Unread') }}</a>
        </div>
    </form>

    <div class="card overflow-hidden divide-y divide-line">
        @forelse ($messages as $message)
            <div id="message-{{ $message->id }}" class="group flex items-start gap-4 px-5 py-4 hover:bg-canvas/60 transition {{ $message->read_at ? '' : 'bg-accent-500/[0.04]' }}">
                <span class="grid place-items-center w-10 h-10 shrink-0 rounded-full bg-secondary-900 text-accent-500 font-bold">{{ mb_substr($message->name, 0, 1) }}</span>
                <a href="{{ route('admin.messages.show', $message) }}" class="min-w-0 flex-1">
                    <span class="flex items-center gap-2">
                        <span class="{{ $message->read_at ? 'font-semibold' : 'font-bold' }} truncate">{{ $message->name }}</span>
                        @unless ($message->read_at)<span class="badge bg-accent-500 text-secondary-900 py-0.5">{{ __('New') }}</span>@endunless
                        <span class="text-xs text-subtle truncate" dir="ltr">{{ $message->email }}</span>
                    </span>
                    @if ($message->subject)<span class="block text-sm font-semibold mt-0.5 truncate">{{ $message->subject }}</span>@endif
                    <span class="block text-sm text-muted truncate">{{ \Illuminate\Support\Str::limit($message->message, 140) }}</span>
                </a>
                <span class="text-xs text-subtle shrink-0" title="{{ $message->created_at }}">{{ $message->created_at?->diffForHumans() }}</span>
                <button type="button" class="btn-icon w-8 h-8 opacity-0 group-hover:opacity-100 hover:text-danger"
                        onclick="deleteResource(@js(route('admin.messages.destroy', $message)), { title: @js(__('Delete this message?')), el: document.getElementById('message-{{ $message->id }}') })">
                    <x-icon name="trash" :size="16" />
                </button>
            </div>
        @empty
            <div class="py-20 text-center">
                <div class="grid place-items-center w-16 h-16 mx-auto rounded-full bg-canvas text-subtle mb-4"><x-icon name="inbox" :size="28" /></div>
                <p class="font-bold">{{ __('No messages yet') }}</p>
                <p class="text-sm text-muted">{{ __('Messages from your contact page will appear here.') }}</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $messages->links() }}</div>
@endsection
