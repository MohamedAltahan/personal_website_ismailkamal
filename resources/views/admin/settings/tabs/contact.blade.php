<div class="divide-y divide-line">
    <x-s.toggle key="contact.notify" :label="__('Email me new messages')" :hint="__('Every contact form message is always saved in Messages; this also sends a copy by email.')" />
</div>
<x-s.field key="contact.notify_email" :label="__('Send notifications to')" type="email" dir="ltr" :placeholder="setting('general.contact_email')"
           :hint="__('Leave empty to use the contact email. Requires mail settings (MAIL_*) in the server .env file.')" />
