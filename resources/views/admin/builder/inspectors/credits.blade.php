<div class="space-y-2">
    <label class="block text-xs font-semibold text-muted">{{ __('Items') }}</label>
    <template x-for="(item, i) in sel.data.items" :key="sel.id + '-cr-' + i">
        <div class="rounded-xl border border-line p-3 space-y-2 relative">
            <button type="button" @click="sel.data.items.splice(i, 1)" class="absolute top-2 end-2 btn-icon w-7 h-7 text-danger"><x-icon name="x" :size="14" /></button>
            <x-b.text key="label" obj="item" :label="__('Label')" />
            <x-b.text key="value" obj="item" :label="__('Value')" />
        </div>
    </template>
    <button type="button" @click="sel.data.items.push({ label: { ar: '', en: '' }, value: { ar: '', en: '' } })" class="btn-ghost w-full h-9">
        <x-icon name="plus" :size="15" /> {{ __('Add item') }}
    </button>
</div>
<x-b.segmented key="columns" :label="__('Columns')" :options="[2 => '2', 3 => '3', 4 => '4', 5 => '5']" number />
