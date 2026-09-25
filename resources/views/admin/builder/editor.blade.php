@php
    use App\Support\Blocks\BlockRegistry;

    $isProject = $mode === 'project';
    $rtl = is_rtl();
    $config['types'] = BlockRegistry::forEditor();
    $config['previewUrl'] = route('admin.builder.preview');
    $config['i18n'] = [
        'deleteBlock' => __('Delete this block?'),
        'deleteBlockHint' => __('You can undo with Ctrl+Z.'),
    ];
    $groups = ['layout' => __('Layout'), 'text' => __('Text'), 'media' => __('Media')];
    $title = $isProject ? ($record->exists ? $record->title : __('New project')) : $record->title;
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $rtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <meta name="upload-chunk-size" content="{{ \App\Support\Uploads::chunkSize() }}">
    <meta name="media-upload-url" content="{{ route('admin.media.upload') }}">
    <meta name="media-list-url" content="{{ route('admin.media.list') }}">
    <meta name="media-embed-url" content="{{ route('admin.media.embed') }}">
    <title>{{ $title ?: __('Untitled') }} — {{ __('Editor') }}</title>
    @include('partials.theme-script', ['key' => 'admin-theme', 'default' => setting('appearance.admin_theme', 'light')])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/js/builder/index.js', 'resources/css/dashboard.css', 'resources/js/dashboard.js'])
</head>
<body class="antialiased overflow-hidden">

<div x-data="builder(@js($config))" class="h-dvh flex flex-col" x-cloak>

    {{-- ============================== Top bar ============================== --}}
    <header class="h-16 shrink-0 flex items-center gap-2 px-3 sm:px-4 bg-sidebar text-white">
        <a href="{{ $config['backUrl'] }}" class="grid place-items-center w-10 h-10 rounded-full hover:bg-white/10" title="{{ __('Back') }}">
            <x-icon name="chevron-start" :size="20" class="rtl:-scale-x-100" />
        </a>
        <div class="min-w-0 me-2">
            <p class="text-[11px] text-white/45">{{ $isProject ? __('Project') : __('Page') }}</p>
            <p class="text-sm font-bold truncate max-w-[16rem]" x-text="t(doc.title) || @js(__('Untitled'))"></p>
        </div>
        <span x-show="dirty" class="hidden sm:inline-flex badge bg-accent-500/15 text-accent-400">{{ __('Unsaved changes') }}</span>

        <div class="mx-auto hidden md:flex items-center gap-1 rounded-full bg-white/5 p-1">
            <button type="button" @click="device = 'desktop'" class="grid place-items-center w-9 h-8 rounded-full" :class="device === 'desktop' ? 'bg-white/15 text-white' : 'text-white/50 hover:text-white'" title="{{ __('Desktop') }}"><x-icon name="monitor" :size="17" /></button>
            <button type="button" @click="device = 'tablet'" class="grid place-items-center w-9 h-8 rounded-full" :class="device === 'tablet' ? 'bg-white/15 text-white' : 'text-white/50 hover:text-white'" title="{{ __('Tablet') }}"><x-icon name="tablet" :size="17" /></button>
            <button type="button" @click="device = 'mobile'" class="grid place-items-center w-9 h-8 rounded-full" :class="device === 'mobile' ? 'bg-white/15 text-white' : 'text-white/50 hover:text-white'" title="{{ __('Mobile') }}"><x-icon name="phone" :size="17" /></button>
            <span class="w-px h-5 bg-white/15 mx-1"></span>
            @foreach (locales() as $code => $info)
                <button type="button" @click="lang = '{{ $code }}'" class="h-8 px-3 rounded-full text-xs font-bold" :class="lang === '{{ $code }}' ? 'bg-accent-500 text-secondary-900' : 'text-white/60 hover:text-white'">{{ $info['name'] }}</button>
            @endforeach
        </div>

        <div class="ms-auto md:ms-0 flex items-center gap-1">
            <button type="button" @click="undo()" :disabled="!history.length" class="grid place-items-center w-9 h-9 rounded-full hover:bg-white/10 disabled:opacity-30" title="{{ __('Undo') }} (Ctrl+Z)"><x-icon name="undo" :size="18" /></button>
            <button type="button" @click="redo()" :disabled="!future.length" class="grid place-items-center w-9 h-9 rounded-full hover:bg-white/10 disabled:opacity-30" title="{{ __('Redo') }} (Ctrl+Y)"><x-icon name="redo" :size="18" /></button>
            <a x-show="liveUrl" :href="liveUrl" target="_blank" class="hidden sm:grid place-items-center w-9 h-9 rounded-full hover:bg-white/10" title="{{ __('View on site') }}"><x-icon name="external" :size="18" /></a>
            <div x-data="themeToggle" class="hidden sm:block">
                <button type="button" @click="cycle()" class="grid place-items-center w-9 h-9 rounded-full hover:bg-white/10">
                    <span x-show="pref === 'light'"><x-icon name="sun" :size="18" /></span>
                    <span x-show="pref === 'dark'"><x-icon name="moon" :size="18" /></span>
                    <span x-show="pref === 'system'"><x-icon name="monitor" :size="18" /></span>
                </button>
            </div>
            <label class="hidden lg:flex items-center gap-2 h-10 px-3 rounded-full bg-white/5 text-xs font-semibold cursor-pointer">
                <input type="checkbox" :checked="doc.status === 'active'" @change="doc.status = $event.target.checked ? 'active' : 'inactive'">
                <span x-text="doc.status === 'active' ? @js(__('Published')) : @js(__('Hidden'))"></span>
            </label>
            <button type="button" @click="save()" :disabled="saving" class="btn-gold h-10 ms-1">
                <x-icon name="save" :size="17" />
                <span x-show="!saving">{{ __('Save') }}</span>
                <span x-show="saving">{{ __('Saving…') }}</span>
            </button>
        </div>
    </header>

    {{-- Draft restore banner --}}
    <div x-show="draft" class="shrink-0 flex flex-wrap items-center gap-3 px-4 py-2.5 bg-accent-100 text-accent-900 dark:bg-accent-500/15 dark:text-accent-300 text-sm">
        <x-icon name="alert" :size="18" />
        <span class="flex-1">{{ __('You have unsaved changes from a previous session.') }}</span>
        <button type="button" @click="restoreDraft()" class="btn h-8 bg-accent-500 text-secondary-900">{{ __('Restore') }}</button>
        <button type="button" @click="discardDraft()" class="btn h-8 border border-current/30">{{ __('Discard') }}</button>
    </div>

    <div class="flex-1 min-h-0 flex">

        {{-- ============================== Left: structure & details ============================== --}}
        <aside class="w-[19rem] shrink-0 flex flex-col bg-surface border-e border-line">
            <div class="p-2 border-b border-line">
                <div class="flex rounded-full bg-canvas p-1">
                    <button type="button" @click="leftTab = 'blocks'" class="flex-1 tab h-9 justify-center" :class="leftTab === 'blocks' && 'tab-active'">
                        <x-icon name="layers" :size="16" /> {{ __('Content') }}
                    </button>
                    <button type="button" @click="leftTab = 'details'" class="flex-1 tab h-9 justify-center" :class="leftTab === 'details' && 'tab-active'">
                        <x-icon name="settings-sliders" :size="16" /> {{ __('Details') }}
                    </button>
                </div>
            </div>

            {{-- Blocks list --}}
            <div x-show="leftTab === 'blocks'" class="flex-1 min-h-0 overflow-y-auto scrollbar-thin p-3">
                <template x-if="!doc.blocks.length">
                    <div class="text-center py-10 px-4">
                        <div class="grid place-items-center w-14 h-14 mx-auto rounded-full bg-accent-100 text-accent-800 dark:bg-accent-500/15 dark:text-accent-400 mb-3"><x-icon name="sparkles" :size="24" /></div>
                        <p class="font-bold text-sm mb-1">{{ __('Start building') }}</p>
                        <p class="text-xs text-muted mb-5">{{ __('Add blocks — images, videos, text, galleries — and arrange them freely.') }}</p>
                    </div>
                </template>

                <div class="space-y-1.5" x-sort="(id, pos) => reorder(id, pos)">
                    <template x-for="(block, index) in doc.blocks" :key="block.id">
                        <div :id="'row-' + block.id" x-sort:item="block.id"
                             class="group relative flex items-center gap-2.5 rounded-xl border p-2 pe-1 cursor-pointer transition"
                             :class="selectedId === block.id ? 'border-accent-500 bg-accent-500/[0.07] ring-2 ring-accent-500/20' : 'border-line hover:border-subtle bg-surface'"
                             @click="select(block.id)">
                            <span x-sort:handle class="text-subtle hover:text-ink cursor-grab shrink-0" @click.stop><x-icon name="drag" :size="16" /></span>
                            <span class="relative grid place-items-center w-10 h-10 shrink-0 rounded-lg overflow-hidden bg-canvas text-muted">
                                <img x-show="thumb(block)" :src="thumb(block)" class="absolute inset-0 w-full h-full object-cover" alt="">
                                <template x-if="!thumb(block)">
                                    <span x-html="document.getElementById('icon-' + type(block.type).icon)?.innerHTML"></span>
                                </template>
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="flex items-center gap-1.5">
                                    <span class="text-[13px] font-bold truncate" x-text="label(block.type)"></span>
                                    <span x-show="missingLang(block)" class="w-1.5 h-1.5 rounded-full bg-warning shrink-0" title="{{ __('Missing translation') }}"></span>
                                    <span x-show="block.style.hide_mobile || block.style.hide_desktop" class="text-subtle" title="{{ __('Hidden on some devices') }}"><x-icon name="eye-off" :size="12" /></span>
                                </span>
                                <span class="block text-[11px] text-muted truncate" x-text="summary(block) || '—'"></span>
                            </span>
                            <span class="flex flex-col opacity-0 group-hover:opacity-100 transition">
                                <button type="button" @click.stop="move(index, -1)" class="grid place-items-center w-6 h-5 text-subtle hover:text-ink"><x-icon name="chevron-up" :size="14" /></button>
                                <button type="button" @click.stop="move(index, 1)" class="grid place-items-center w-6 h-5 text-subtle hover:text-ink"><x-icon name="chevron-down" :size="14" /></button>
                            </span>
                            {{-- Insert between --}}
                            <button type="button" @click.stop="openPalette(index + 1)"
                                    class="absolute -bottom-3 start-1/2 -translate-x-1/2 rtl:translate-x-1/2 z-10 hidden group-hover:grid place-items-center w-6 h-6 rounded-full bg-accent-500 text-secondary-900 shadow-md"
                                    title="{{ __('Insert block here') }}"><x-icon name="plus" :size="14" :stroke="2.6" /></button>
                        </div>
                    </template>
                </div>

                <button type="button" @click="openPalette()" class="mt-4 w-full h-12 rounded-xl border-2 border-dashed border-line hover:border-accent-500 hover:bg-accent-500/5 text-sm font-bold text-muted hover:text-ink transition inline-flex items-center justify-center gap-2">
                    <x-icon name="plus" :size="18" /> {{ __('Add block') }}
                </button>
                <p class="mt-4 text-[11px] text-subtle leading-relaxed">{{ __('Tip: click any part of the preview to edit it. Ctrl+S saves, Ctrl+Z undoes.') }}</p>
            </div>

            {{-- Details --}}
            <div x-show="leftTab === 'details'" class="flex-1 min-h-0 overflow-y-auto scrollbar-thin p-4 space-y-4">
                @include($isProject ? 'admin.builder.details-project' : 'admin.builder.details-page')
            </div>
        </aside>

        {{-- ============================== Center: live preview ============================== --}}
        <section class="flex-1 min-w-0 flex flex-col bg-canvas">
            <div class="md:hidden flex items-center justify-center gap-1 p-2 border-b border-line bg-surface">
                @foreach (locales() as $code => $info)
                    <button type="button" @click="lang = '{{ $code }}'" class="h-8 px-3 rounded-full text-xs font-bold" :class="lang === '{{ $code }}' ? 'bg-accent-500 text-secondary-900' : 'text-muted'">{{ $info['name'] }}</button>
                @endforeach
            </div>
            <div class="relative flex-1 min-h-0 overflow-auto p-3 sm:p-5 flex justify-center">
                <div class="relative h-full transition-all duration-300 shadow-2xl shadow-secondary-900/10 rounded-xl overflow-hidden bg-white"
                     :style="`width: ${frameWidth}; max-width: 100%`">
                    <iframe x-ref="preview" class="w-full h-full block" title="{{ __('Preview') }}"></iframe>
                    <div x-show="previewLoading" x-transition.opacity class="absolute top-3 end-3 badge bg-secondary-900/80 text-white">
                        <svg class="animate-spin" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M21 12a9 9 0 1 1-6.2-8.56"/></svg>
                        {{ __('Updating…') }}
                    </div>
                </div>
            </div>
        </section>

        {{-- ============================== Right: inspector ============================== --}}
        <aside class="w-[21rem] shrink-0 flex flex-col bg-surface border-s border-line max-xl:fixed max-xl:inset-y-16 max-xl:end-0 max-xl:z-40 max-xl:shadow-2xl transition-transform"
               :class="!sel && 'max-xl:translate-x-[110%] max-xl:rtl:-translate-x-[110%]'">
            <template x-if="!sel">
                <div class="flex-1 grid place-items-center p-8 text-center">
                    <div>
                        <div class="grid place-items-center w-14 h-14 mx-auto rounded-full bg-canvas text-subtle mb-3"><x-icon name="edit" :size="22" /></div>
                        <p class="text-sm font-bold mb-1">{{ __('Nothing selected') }}</p>
                        <p class="text-xs text-muted">{{ __('Select a block from the list or click it in the preview to edit its content and style.') }}</p>
                    </div>
                </div>
            </template>

            <template x-if="sel">
                <div class="flex-1 min-h-0 flex flex-col">
                    <div class="flex items-center gap-2 px-4 h-14 border-b border-line">
                        <span class="grid place-items-center w-8 h-8 rounded-lg bg-accent-500 text-secondary-900" x-html="document.getElementById('icon-' + type(sel.type).icon)?.innerHTML"></span>
                        <span class="font-bold text-sm flex-1 truncate" x-text="label(sel.type)"></span>
                        <button type="button" @click="duplicate(selIndex)" class="btn-icon w-8 h-8" title="{{ __('Duplicate') }}"><x-icon name="copy" :size="16" /></button>
                        <button type="button" @click="remove(selIndex)" class="btn-icon w-8 h-8 hover:text-danger" title="{{ __('Delete') }}"><x-icon name="trash" :size="16" /></button>
                        <button type="button" @click="selectedId = null" class="btn-icon w-8 h-8" title="{{ __('Close') }}"><x-icon name="x" :size="16" /></button>
                    </div>
                    <div class="px-3 pt-3">
                        <div class="flex rounded-full bg-canvas p-1">
                            <button type="button" @click="rightTab = 'content'" class="flex-1 tab h-8 justify-center" :class="rightTab === 'content' && 'tab-active'">{{ __('Content') }}</button>
                            <button type="button" @click="rightTab = 'style'" class="flex-1 tab h-8 justify-center" :class="rightTab === 'style' && 'tab-active'">{{ __('Style') }}</button>
                        </div>
                    </div>
                    <div class="flex-1 min-h-0 overflow-y-auto scrollbar-thin p-4">
                        <div x-show="rightTab === 'content'" class="space-y-4">
                            @foreach (array_keys(BlockRegistry::types()) as $type)
                                <template x-if="sel.type === '{{ $type }}'">
                                    <div class="space-y-4">@include('admin.builder.inspectors.'.$type)</div>
                                </template>
                            @endforeach
                        </div>
                        <div x-show="rightTab === 'style'" class="space-y-4">
                            @include('admin.builder.inspectors._style')
                        </div>
                    </div>
                </div>
            </template>
        </aside>
    </div>

    {{-- ============================== Block palette ============================== --}}
    <div x-show="palette.open" x-transition.opacity.duration.150ms class="fixed inset-0 z-50 grid place-items-center p-4 bg-secondary-900/60 backdrop-blur-[3px]"
         @keydown.escape.window="palette.open = false">
        <div @click.outside="palette.open = false" class="w-full max-w-2xl max-h-[85vh] flex flex-col rounded-2xl bg-raised border border-line shadow-2xl overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-line">
                <h3 class="font-bold flex-1">{{ __('Add a block') }}</h3>
                <div class="relative w-56">
                    <x-icon name="search" :size="15" class="absolute top-1/2 -translate-y-1/2 start-3 text-subtle" />
                    <input type="search" x-model="palette.q" placeholder="{{ __('Search…') }}" class="field h-9 py-0 ps-9 text-sm" x-effect="palette.open && $nextTick(() => $el.focus())">
                </div>
                <button type="button" @click="palette.open = false" class="btn-icon"><x-icon name="x" /></button>
            </div>
            <div class="overflow-y-auto p-5 space-y-5">
                <template x-for="(items, group) in paletteGroups()" :key="group">
                    <div>
                        <p class="text-xs font-bold text-subtle mb-2" x-text="@js($groups)[group] || group"></p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            <template x-for="t in items" :key="t.type">
                                <button type="button" @click="add(t.type)"
                                        class="group flex items-center gap-3 rounded-xl border border-line p-3 text-start hover:border-accent-500 hover:bg-accent-500/5 transition">
                                    <span class="grid place-items-center w-10 h-10 shrink-0 rounded-lg bg-canvas text-muted group-hover:bg-accent-500 group-hover:text-secondary-900 transition"
                                          x-html="document.getElementById('icon-' + t.icon)?.innerHTML"></span>
                                    <span class="text-sm font-bold" x-text="t.label[document.documentElement.lang] || t.type"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

{{-- Icon sprites used by x-html above --}}
<div hidden>
    @foreach (collect(BlockRegistry::types())->pluck('icon')->unique() as $icon)
        <span id="icon-{{ $icon }}"><x-icon :name="$icon" :size="18" /></span>
    @endforeach
</div>

@include('admin.partials.toasts')
@include('admin.partials.confirm')
@include('admin.partials.media-picker')
</body>
</html>
