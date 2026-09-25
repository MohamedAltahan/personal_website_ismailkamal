<div class="grid grid-cols-2 gap-3">
    <x-b.media key="before" :label="__('Before')" />
    <x-b.media key="after" :label="__('After')" />
</div>
<div class="grid grid-cols-2 gap-3">
    <x-b.text key="label_before" :label="__('Before label')" />
    <x-b.text key="label_after" :label="__('After label')" />
</div>
<p class="text-[11px] text-subtle">{{ __('Use two images with the same size for the best result.') }}</p>
