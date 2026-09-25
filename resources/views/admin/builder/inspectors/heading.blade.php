<x-b.text key="eyebrow" :label="__('Small text above (optional)')" />
<x-b.text key="text" :label="__('Heading')" multiline :rows="2" />
<x-b.segmented key="size" :label="__('Size')" :options="['md' => 'M', 'lg' => 'L', 'xl' => 'XL', 'hero' => 'XXL']" />
<x-b.segmented key="align" :label="__('Alignment')" :options="['start' => __('Start'), 'center' => __('Center'), 'end' => __('End')]" />
