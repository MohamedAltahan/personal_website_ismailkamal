<x-b.media :label="__('Video (upload or YouTube / Vimeo)')" accept="['video', 'embed']" />
<template x-if="m(sel.data.media)?.type === 'video'">
    <x-b.media key="poster" :label="__('Cover image (optional)')" :hint="__('Shown before the video plays. The frame captured on upload is used by default.')" />
</template>
<x-b.text key="caption" :label="__('Caption (optional)')" />
<x-b.select key="ratio" :label="__('Shape')" :options="['auto' => __('Original'), '16:9' => '16:9', '21:9' => '21:9', '1:1' => '1:1', '4:5' => '4:5', '9:16' => '9:16']" />
<x-b.toggle key="autoplay" :label="__('Autoplay (muted)')" :hint="__('Plays silently when visible — great for loops.')" />
<x-b.toggle key="loop" :label="__('Loop')" />
<template x-if="!sel.data.autoplay">
    <x-b.toggle key="controls" :label="__('Show play button & controls')" />
</template>
<x-b.toggle key="rounded" :label="__('Rounded corners')" />
