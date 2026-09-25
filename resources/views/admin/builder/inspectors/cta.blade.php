<x-b.text key="title" :label="__('Title')" multiline :rows="2" />
<x-b.text key="text" :label="__('Text (optional)')" multiline />
<div class="grid grid-cols-2 gap-3">
    <x-b.text key="button_label" :label="__('Button text')" />
    <x-b.text key="button_url" :label="__('Button link')" plain dir="ltr" :placeholder="__('Contact page')" />
</div>
