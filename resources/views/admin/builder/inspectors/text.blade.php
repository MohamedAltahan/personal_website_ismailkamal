<x-b.rich key="html" :label="__('Text')" />
<x-b.segmented key="size" :label="__('Text size')" :options="['sm' => 'S', 'base' => 'M', 'lg' => 'L', 'xl' => 'XL']" />
<x-b.segmented key="align" :label="__('Alignment')" :options="['start' => __('Start'), 'center' => __('Center'), 'justify' => __('Justify')]" />
<x-b.segmented key="columns" :label="__('Columns')" :options="[1 => '1', 2 => '2']" number />
