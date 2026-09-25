<x-b.media :label="__('Image or video')" accept="['image', 'video', 'embed']" />
<x-b.text key="eyebrow" :label="__('Small text above (optional)')" />
<x-b.text key="heading" :label="__('Heading')" multiline :rows="2" />
<x-b.rich key="html" :label="__('Text')" />
<div class="grid grid-cols-2 gap-3">
    <x-b.text key="button_label" :label="__('Button text')" />
    <x-b.text key="button_url" :label="__('Button link')" plain dir="ltr" />
</div>
<x-b.segmented key="media_side" :label="__('Media side')" :options="['start' => __('Start'), 'end' => __('End')]" />
<x-b.segmented key="ratio" :label="__('Media width')" :options="['40' => '40%', '50' => '50%', '60' => '60%']" />
<x-b.segmented key="valign" :label="__('Vertical alignment')" :options="['start' => __('Top'), 'center' => __('Middle'), 'end' => __('Bottom')]" />
<x-b.toggle key="rounded" :label="__('Rounded corners')" />
