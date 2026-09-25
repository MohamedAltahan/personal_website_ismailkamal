<x-b.segmented key="width" obj="sel.style" :label="__('Width')" :options="['full' => __('Full'), 'wide' => __('Wide'), 'container' => __('Normal'), 'narrow' => __('Narrow')]" />
<div class="grid grid-cols-2 gap-3">
    <x-b.select key="pt" obj="sel.style" :label="__('Space above')" :options="['none' => '0', 'sm' => 'S', 'md' => 'M', 'lg' => 'L', 'xl' => 'XL']" />
    <x-b.select key="pb" obj="sel.style" :label="__('Space below')" :options="['none' => '0', 'sm' => 'S', 'md' => 'M', 'lg' => 'L', 'xl' => 'XL']" />
</div>
<x-b.color key="bg" :label="__('Background colour')" />
<x-b.color key="text" :label="__('Text colour')" />
<x-b.segmented key="animate" obj="sel.style" :label="__('Entrance animation')" :options="['none' => __('None'), 'fade' => __('Fade'), 'slide' => __('Slide'), 'zoom' => __('Zoom')]" />
<x-b.toggle key="hide_mobile" obj="sel.style" :label="__('Hide on mobile')" />
<x-b.toggle key="hide_desktop" obj="sel.style" :label="__('Hide on desktop')" />
<x-b.text key="anchor" obj="sel.style" :label="__('Anchor (for #links)')" plain dir="ltr" placeholder="work" />
