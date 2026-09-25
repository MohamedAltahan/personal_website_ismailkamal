{{-- Global media picker modal (opened with Alpine.store('picker').open({...})). --}}
<div x-data="mediaPicker" x-cloak x-show="$store.picker.isOpen" x-transition.opacity.duration.150ms
     class="fixed inset-0 z-[60] flex items-center justify-center p-2 sm:p-6 bg-secondary-900/60 backdrop-blur-[3px]"
     @keydown.escape.window="$store.picker.isOpen && cancel()">
    <div class="relative w-full max-w-6xl h-full max-h-[52rem] flex flex-col rounded-2xl bg-raised border border-line shadow-2xl overflow-hidden"
         x-trap.noscroll="$store.picker.isOpen">

        <div class="flex flex-wrap items-center gap-3 px-5 py-4 border-b border-line">
            <h3 class="font-bold text-ink me-auto" x-text="$store.picker.options.title || @js(__('Media library'))"></h3>
            <div class="flex items-center gap-1 rounded-full bg-canvas p-1">
                <button type="button" class="tab" :class="tab === 'library' && 'tab-active'" @click="tab = 'library'">
                    <x-icon name="images" :size="16" /> {{ __('Library') }}
                </button>
                <button type="button" class="tab" :class="tab === 'upload' && 'tab-active'" @click="tab = 'upload'"
                        x-show="accept.includes('image') || accept.includes('video')">
                    <x-icon name="upload" :size="16" /> {{ __('Upload') }}
                </button>
                <button type="button" class="tab" :class="tab === 'embed' && 'tab-active'" @click="tab = 'embed'" x-show="accept.includes('embed')">
                    <x-icon name="link" :size="16" /> YouTube / Vimeo
                </button>
            </div>
            <button type="button" @click="cancel()" class="btn-icon"><x-icon name="x" /></button>
        </div>

        {{-- Library --}}
        <div x-show="tab === 'library'" class="flex-1 min-h-0 flex flex-col">
            <div class="flex flex-wrap items-center gap-2 px-5 py-3 border-b border-line">
                <div class="relative flex-1 min-w-48">
                    <x-icon name="search" :size="16" class="absolute top-1/2 -translate-y-1/2 start-3.5 text-subtle" />
                    <input type="search" x-model.debounce.350ms="q" @input.debounce.350ms="load(true)" placeholder="{{ __('Search by file name…') }}" class="field ps-10 h-10 py-0">
                </div>
                <select x-model="type" @change="load(true)" class="field w-auto h-10 py-0" x-show="accept.length > 1">
                    <option value="">{{ __('All types') }}</option>
                    <template x-for="t in accept" :key="t">
                        <option :value="t" x-text="{ image: @js(__('Images')), video: @js(__('Videos')), embed: @js(__('Embeds')) }[t]"></option>
                    </template>
                </select>
            </div>

            <div class="flex-1 overflow-y-auto p-5 scrollbar-thin" @scroll.debounce.100ms="($el.scrollTop + $el.clientHeight > $el.scrollHeight - 300) && more()"
                 x-data="uploadQueue({ folder: 'library' })" @media-uploaded="uploaded($event.detail)"
                 @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false" @drop.prevent="onDrop($event)">
                <div x-show="dragging" class="absolute inset-0 z-10 m-3 rounded-2xl border-2 border-dashed border-accent-500 bg-accent-500/10 grid place-items-center text-lg font-bold">
                    {{ __('Drop files to upload') }}
                </div>

                <template x-if="queue.length">
                    <div class="mb-4 space-y-2">
                        <template x-for="item in queue" :key="item.id">
                            <div class="flex items-center gap-3 text-xs">
                                <span class="truncate w-48" x-text="item.name"></span>
                                <div class="flex-1 h-1.5 rounded-full bg-canvas overflow-hidden">
                                    <div class="h-full bg-accent-500 transition-all" :style="`width:${item.progress}%`"></div>
                                </div>
                                <span x-text="item.status === 'error' ? item.error : item.progress + '%'" :class="item.status === 'error' && 'text-danger'"></span>
                            </div>
                        </template>
                    </div>
                </template>

                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 xl:grid-cols-6 gap-3">
                    <template x-for="item in items" :key="item.id">
                        <button type="button" @click="toggle(item)" @dblclick="toggle(item); !$store.picker.options.multiple && confirm()"
                                class="group relative aspect-square rounded-xl overflow-hidden checkerboard border-2 transition text-start"
                                :class="isSelected(item) ? 'border-accent-500 ring-4 ring-accent-500/25' : 'border-transparent hover:border-line'">
                            <img x-show="item.thumb" :src="item.thumb" alt="" loading="lazy" class="w-full h-full object-cover">
                            <div x-show="!item.thumb" class="w-full h-full grid place-items-center bg-secondary-900 text-white/60">
                                <x-icon name="video" :size="28" />
                            </div>
                            <span x-show="item.type !== 'image'" class="absolute top-2 start-2 grid place-items-center w-7 h-7 rounded-full bg-black/60 text-white">
                                <x-icon name="play" :size="13" />
                            </span>
                            <span x-show="isSelected(item)" class="absolute top-2 end-2 grid place-items-center min-w-7 h-7 px-1 rounded-full bg-accent-500 text-secondary-900 text-xs font-bold"
                                  x-text="$store.picker.options.multiple ? order(item) : '✓'"></span>
                            <span class="absolute inset-x-0 bottom-0 px-2 py-1.5 text-[11px] text-white bg-gradient-to-t from-black/70 to-transparent truncate opacity-0 group-hover:opacity-100 transition"
                                  x-text="item.name"></span>
                        </button>
                    </template>
                </div>

                <div x-show="!loading && !items.length" class="py-16 text-center text-muted">
                    <x-icon name="images" :size="40" class="mx-auto mb-3 text-subtle" />
                    <p class="font-bold">{{ __('No media yet') }}</p>
                    <p class="text-sm">{{ __('Drag files here or use the Upload tab.') }}</p>
                </div>
                <div x-show="loading" class="py-8 text-center text-muted text-sm">{{ __('Loading…') }}</div>
                <input type="file" x-ref="fileInput" class="hidden" multiple :accept="accept" @change="add($event.target.files)">
            </div>
        </div>

        {{-- Upload --}}
        <div x-show="tab === 'upload'" class="flex-1 min-h-0 p-5 overflow-y-auto"
             x-data="uploadQueue({ folder: 'library' })" @media-uploaded="uploaded($event.detail)">
            @include('admin.partials.dropzone')
        </div>

        {{-- Embed --}}
        <div x-show="tab === 'embed'" class="flex-1 min-h-0 p-5">
            <div class="max-w-xl mx-auto mt-10 text-center">
                <div class="grid place-items-center w-16 h-16 mx-auto rounded-full bg-brand-soft text-primary-700 dark:text-accent-500 mb-4">
                    <x-icon name="link" :size="26" />
                </div>
                <h4 class="font-bold mb-1">{{ __('Add a YouTube or Vimeo video') }}</h4>
                <p class="text-sm text-muted mb-6">{{ __('Paste the video link. It plays inside your site without uploading the file.') }}</p>
                <form @submit.prevent="addEmbed()" class="flex gap-2">
                    <input type="url" x-model="embedUrl" dir="ltr" placeholder="https://youtu.be/… / https://vimeo.com/…" class="field flex-1">
                    <button type="submit" class="btn-primary h-auto" :disabled="embedLoading">{{ __('Add') }}</button>
                </form>
            </div>
        </div>

        <div class="flex items-center gap-3 px-5 py-3.5 border-t border-line bg-canvas/60">
            <p class="text-sm text-muted me-auto">
                <span x-show="selected.length" x-text="selected.length + ' ' + @js(__('selected'))"></span>
            </p>
            <button type="button" @click="cancel()" class="btn-ghost">{{ __('Cancel') }}</button>
            <button type="button" @click="confirm()" class="btn-primary" :disabled="!selected.length">{{ __('Use selected') }}</button>
        </div>
    </div>
</div>
