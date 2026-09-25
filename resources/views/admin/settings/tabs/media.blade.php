@php
    $widths = setting('media.widths', []);
@endphp

{{-- Quality with live, in-browser preview --}}
<div x-data="{
        quality: {{ (int) setting('media.quality') }},
        format: @js(setting('media.format')),
        sample: @js($sample ? parse_url($sample->url(null), PHP_URL_PATH) : null),
        previewUrl: null, previewSize: null, originalSize: @js($sample?->size), busy: false,
        async render() {
            if (!this.sample) return;
            this.busy = true;
            const img = await new Promise((ok) => { const i = new Image(); i.onload = () => ok(i); i.onerror = () => ok(null); i.src = this.sample; });
            if (!img) { this.busy = false; this.sample = null; return; }
            const scale = Math.min(1, 1600 / img.naturalWidth);
            const c = document.createElement('canvas'); c.width = Math.round(img.naturalWidth * scale); c.height = Math.round(img.naturalHeight * scale);
            c.getContext('2d').drawImage(img, 0, 0, c.width, c.height);
            const type = { webp: 'image/webp', avif: 'image/avif', jpg: 'image/jpeg', original: 'image/jpeg' }[this.format];
            c.toBlob(blob => {
                if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
                this.previewUrl = URL.createObjectURL(blob); this.previewSize = blob.size; this.busy = false;
            }, type, this.quality / 100);
        },
        kb(b) { return b ? (b > 1048576 ? (b / 1048576).toFixed(2) + ' MB' : Math.round(b / 1024) + ' KB') : '—' },
     }" x-init="render(); $watch('quality', () => render()); $watch('format', () => render())" class="space-y-5">

    <div class="grid md:grid-cols-2 gap-6 items-start">
        <div class="space-y-5">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="text-sm font-bold">{{ __('Image quality') }}</label>
                    <span class="text-2xl font-bold tabular-nums" x-text="quality + '%'"></span>
                </div>
                <input type="range" name="s[media][quality]" min="10" max="100" x-model.number="quality" class="w-full">
                <div class="flex justify-between text-[11px] text-subtle mt-1">
                    <span>{{ __('Smaller files') }}</span><span>{{ __('Recommended 75–85') }}</span><span>{{ __('Best quality') }}</span>
                </div>
            </div>
            <div>
                <label class="label">{{ __('Output format') }}</label>
                <div class="grid grid-cols-4 gap-2">
                    @foreach (['webp' => 'WebP', 'avif' => 'AVIF', 'jpg' => 'JPG', 'original' => __('Original')] as $v => $l)
                        <label class="cursor-pointer">
                            <input type="radio" name="s[media][format]" value="{{ $v }}" x-model="format" class="peer sr-only">
                            <span class="grid place-items-center h-11 rounded-xl border border-line text-sm font-bold peer-checked:border-accent-500 peer-checked:bg-accent-500 peer-checked:text-secondary-900">{{ $l }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="mt-1.5 text-xs text-subtle">{{ __('WebP is supported everywhere and ~30% lighter than JPG. AVIF is even lighter but slower to process.') }}</p>
            </div>
        </div>

        <div class="rounded-xl border border-line overflow-hidden">
            <template x-if="sample">
                <div>
                    <div class="relative checkerboard aspect-[4/3] overflow-hidden">
                        <img :src="previewUrl" x-show="previewUrl" class="w-full h-full object-cover" alt="">
                        <span x-show="busy" class="absolute top-2 end-2 badge bg-black/60 text-white">…</span>
                    </div>
                    <div class="grid grid-cols-2 text-center text-xs divide-x divide-line rtl:divide-x-reverse border-t border-line">
                        <div class="p-2.5"><p class="text-subtle">{{ __('Original') }}</p><p class="font-bold" dir="ltr" x-text="kb(originalSize)"></p></div>
                        <div class="p-2.5"><p class="text-subtle">{{ __('After processing (1600px)') }}</p><p class="font-bold text-success" dir="ltr" x-text="kb(previewSize)"></p></div>
                    </div>
                </div>
            </template>
            <template x-if="!sample">
                <p class="p-8 text-center text-sm text-muted">{{ __('Upload a large image to see a live quality preview here.') }}</p>
            </template>
        </div>
    </div>
</div>

<div class="grid md:grid-cols-2 gap-5 border-t border-line pt-5">
    <div x-data="{ widths: @js($widths), add: '' }">
        <label class="label">{{ __('Responsive sizes (px)') }}</label>
        <div class="flex flex-wrap gap-2">
            <template x-for="(w, i) in widths" :key="w">
                <span class="badge bg-canvas text-ink h-9 px-3">
                    <input type="hidden" name="s[media][widths][]" :value="w">
                    <span x-text="w" dir="ltr"></span>
                    <button type="button" @click="widths.splice(i, 1)" class="text-subtle hover:text-danger">×</button>
                </span>
            </template>
            <input type="number" x-model.number="add" @keydown.enter.prevent="add && !widths.includes(add) && widths.push(add) && widths.sort((a, b) => a - b); add = ''"
                   placeholder="+ 1200" class="field w-28 h-9 py-0" dir="ltr">
        </div>
        <p class="mt-1.5 text-xs text-subtle">{{ __('Each image is saved in these widths so phones download small files and big screens get sharp ones.') }}</p>
    </div>
    <x-s.field key="media.max_dimension" :label="__('Maximum size (longest side, px)')" type="number" min="800" max="8000" dir="ltr" :hint="__('Larger images are scaled down to this size.')" />
    <x-s.field key="media.max_upload_mb" :label="__('Maximum upload size (MB)')" type="number" min="1" max="4096" dir="ltr" :hint="__('Large videos are uploaded in chunks, so server limits don’t apply.')" />
</div>

<div class="border-t border-line">
    <x-s.toggle key="media.keep_original" :label="__('Keep original files')" :hint="__('Recommended — lets you re-process images later with different settings.')" />
</div>

{{-- Watermark --}}
<div class="border-t border-line pt-5 space-y-5" x-data="{ on: @js((bool) setting('media.watermark_enabled')) }">
    <label class="flex items-start justify-between gap-4 cursor-pointer">
        <span>
            <span class="block text-sm font-bold">{{ __('Watermark images') }}</span>
            <span class="block text-xs text-muted mt-0.5">{{ __('Adds your logo on top of every processed image (not videos).') }}</span>
        </span>
        <input type="hidden" name="s[media][watermark_enabled]" :value="on ? 1 : 0">
        <button type="button" @click="on = !on" class="relative shrink-0 w-11 h-6 rounded-full transition" :class="on ? 'bg-primary-900 dark:bg-accent-500' : 'bg-gray-300 dark:bg-white/15'">
            <span class="absolute top-1 w-4 h-4 rounded-full bg-white shadow transition-all" :class="on ? 'start-6' : 'start-1'"></span>
        </button>
    </label>
    <div x-show="on" x-collapse class="grid md:grid-cols-2 gap-5">
        <x-s.media key="media.watermark" :media="$pickerMedia['media.watermark']" :label="__('Watermark image')" :hint="__('A transparent PNG works best.')" />
        <div class="space-y-4">
            <div>
                <label class="label">{{ __('Position') }}</label>
                <select name="s[media][watermark_position]" class="field">
                    @foreach (['bottom-right' => __('Bottom right'), 'bottom-left' => __('Bottom left'), 'top-right' => __('Top right'), 'top-left' => __('Top left'), 'center' => __('Center')] as $v => $l)
                        <option value="{{ $v }}" @selected(setting('media.watermark_position') === $v)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div x-data="{ v: {{ (int) setting('media.watermark_opacity') }} }">
                <label class="label flex justify-between">{{ __('Opacity') }} <span x-text="v + '%'"></span></label>
                <input type="range" name="s[media][watermark_opacity]" min="5" max="100" x-model="v" class="w-full">
            </div>
            <div x-data="{ v: {{ (int) setting('media.watermark_scale') }} }">
                <label class="label flex justify-between">{{ __('Size (% of image width)') }} <span x-text="v + '%'"></span></label>
                <input type="range" name="s[media][watermark_scale]" min="5" max="60" x-model="v" class="w-full">
            </div>
        </div>
    </div>
</div>

{{-- Re-process --}}
<div class="rounded-2xl bg-canvas p-5" x-data="{
        running: false, done: 0, total: {{ $imageCount }}, failed: [],
        async run() {
            if (!await confirmAction({ title: @js(__('Re-process all images?')), message: @js(__('Save your settings first. This rebuilds every image with the current settings and can take a few minutes.')), danger: false, confirmLabel: @js(__('Start')) })) return;
            this.running = true; this.done = 0; this.failed = [];
            let after = 0;
            while (after !== null) {
                try {
                    const { data } = await http.post(@js(route('admin.media.regenerate')), { after });
                    this.done = data.done; this.total = data.total; this.failed.push(...data.failed); after = data.next;
                } catch (e) { toast(errorMessage(e), 'error'); break; }
            }
            this.running = false;
            toast(@js(__('All images were re-processed.')));
        },
     }">
    <div class="flex flex-wrap items-center gap-4">
        <div class="flex-1 min-w-60">
            <p class="font-bold text-sm">{{ __('Re-process all images') }}</p>
            <p class="text-xs text-muted">{{ __('Apply the saved quality, format and sizes to the :n existing images.', ['n' => $imageCount]) }}</p>
        </div>
        <button type="button" @click="run()" :disabled="running" class="btn-ghost h-10"><x-icon name="refresh" :size="16" ::class="running && 'animate-spin'" /> {{ __('Start') }}</button>
    </div>
    <div x-show="running || done" x-cloak class="mt-4">
        <div class="h-2 rounded-full bg-surface overflow-hidden"><div class="h-full bg-accent-500 transition-all" :style="`width:${total ? done / total * 100 : 0}%`"></div></div>
        <p class="mt-1.5 text-xs text-muted" dir="ltr"><span x-text="done"></span> / <span x-text="total"></span></p>
        <p x-show="failed.length" class="mt-1 text-xs text-danger" x-text="failed.join(', ')"></p>
    </div>
</div>
