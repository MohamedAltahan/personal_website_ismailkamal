{{-- Needs an enclosing x-data="uploadQueue(...)". --}}
<div @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false" @drop.prevent="onDrop($event)" @click="pick()"
     class="cursor-pointer rounded-2xl border-2 border-dashed px-6 py-12 text-center transition"
     :class="dragging ? 'border-accent-500 bg-accent-500/10' : 'border-line hover:border-subtle bg-canvas/50'">
    <div class="grid place-items-center w-14 h-14 mx-auto rounded-full bg-accent-500 text-secondary-900 mb-4">
        <x-icon name="upload" :size="24" />
    </div>
    <p class="font-bold">{{ __('Drag & drop images or videos') }}</p>
    <p class="text-sm text-muted mt-1">{{ __('or click to choose files — up to :size MB each', ['size' => setting('media.max_upload_mb')]) }}</p>
    <p class="text-xs text-subtle mt-3">JPG · PNG · WEBP · GIF · AVIF · SVG · MP4 · WEBM · MOV</p>
    <input type="file" x-ref="fileInput" class="hidden" multiple :accept="accept" @change="add($event.target.files)" @click.stop>
</div>

<div class="mt-4 space-y-2" x-show="queue.length">
    <template x-for="item in queue" :key="item.id">
        <div class="flex items-center gap-3 rounded-xl border border-line bg-surface px-3 py-2.5 text-sm">
            <x-icon name="file" :size="18" class="text-subtle shrink-0" />
            <span class="truncate flex-1" x-text="item.name"></span>
            <div class="w-40 h-1.5 rounded-full bg-canvas overflow-hidden">
                <div class="h-full transition-all" :class="item.status === 'error' ? 'bg-danger' : 'bg-accent-500'" :style="`width:${item.status === 'error' ? 100 : item.progress}%`"></div>
            </div>
            <span class="w-24 text-end text-xs" :class="item.status === 'error' ? 'text-danger' : 'text-muted'"
                  x-text="item.status === 'error' ? item.error : (item.status === 'done' ? '✓' : item.progress + '%')"></span>
        </div>
    </template>
</div>
