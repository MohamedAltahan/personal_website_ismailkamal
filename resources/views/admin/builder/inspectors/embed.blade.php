<x-b.media :label="__('YouTube / Vimeo video')" accept="['embed']" />
<x-b.text key="caption" :label="__('Caption (optional)')" />
<x-b.select key="ratio" :label="__('Shape')" :options="['16:9' => '16:9', '21:9' => '21:9', '1:1' => '1:1', '9:16' => '9:16']" />
<x-b.toggle key="autoplay" :label="__('Autoplay (muted)')" />
<x-b.toggle key="loop" :label="__('Loop')" />
<x-b.toggle key="controls" :label="__('Show controls')" />
