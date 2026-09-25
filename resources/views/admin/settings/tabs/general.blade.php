<x-s.field key="general.site_name" :label="__('Site name')" translatable />
<x-s.field key="general.tagline" :label="__('Tagline')" translatable :hint="__('Shown under your name and in search results, e.g. “Motion Graphic Designer”.')" />
<div class="grid sm:grid-cols-2 gap-5">
    <x-s.field key="general.contact_email" :label="__('Contact email')" type="email" dir="ltr" />
    <x-s.field key="general.contact_phone" :label="__('Phone')" dir="ltr" />
    <x-s.field key="general.whatsapp" :label="__('WhatsApp number')" dir="ltr" :hint="__('Used for the WhatsApp button on the contact page.')" />
</div>
<x-s.field key="general.contact_address" :label="__('Location')" translatable />
<div class="border-t border-line pt-2">
    <x-s.toggle key="general.available_for_work" :label="__('Available for work')" :hint="__('Shows a pulsing “Available for new projects” badge in the footer.')" />
</div>
