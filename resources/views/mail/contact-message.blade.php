<x-mail::message>
# {{ __('New message from :name', ['name' => $contact->name]) }}

**{{ __('Email') }}:** {{ $contact->email }}
@if ($contact->phone)
**{{ __('Phone') }}:** {{ $contact->phone }}
@endif
@if ($contact->subject)
**{{ __('Subject') }}:** {{ $contact->subject }}
@endif

<x-mail::panel>
{{ $contact->message }}
</x-mail::panel>

<x-mail::button :url="route('admin.messages.show', $contact)">
{{ __('Open in dashboard') }}
</x-mail::button>
</x-mail::message>
