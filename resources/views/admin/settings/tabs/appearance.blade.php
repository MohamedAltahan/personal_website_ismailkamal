@php
    $fonts = config('site.fonts');
    $allFonts = collect($fonts['arabic'])->merge($fonts['latin'])->unique()->map(fn ($f) => 'family='.str_replace(' ', '+', $f).':wght@600;700')->implode('&');
@endphp
@push('styles')
    <link href="https://fonts.googleapis.com/css2?{{ $allFonts }}&display=swap" rel="stylesheet">
@endpush

<div x-data="{
        primary: @js(setting('appearance.primary_color')),
        dark: @js(setting('appearance.ink_color')),
        ar: @js(setting('appearance.font_arabic')),
        display: @js(setting('appearance.font_latin')),
        body: @js(setting('appearance.font_latin_body')),
        presets: ['#ffd200', '#ff5a1f', '#00d68f', '#3d7bff', '#b56cff', '#ff3d7f', '#e8e6df', '#111111'],
     }" class="space-y-6">

    {{-- Live preview --}}
    <div class="rounded-2xl overflow-hidden border border-line grid sm:grid-cols-2">
        <div class="p-6 bg-[#f4f3ef] text-[#0e0e0f]">
            <p class="text-xs opacity-60 mb-2">{{ __('Light theme') }}</p>
            <p class="text-3xl font-bold leading-tight mb-2" :style="`font-family: '${display}'`">Selected work</p>
            <p class="text-2xl font-bold leading-tight mb-4" :style="`font-family: '${ar}'`" dir="rtl">أعمال مختارة</p>
            <p class="text-sm opacity-70 mb-4" :style="`font-family: '${body}'`">Motion design, animation & visual storytelling.</p>
            <span class="inline-flex items-center h-10 px-5 rounded-full text-sm font-bold" :style="`background:${primary}; color:#0b0b0c`">{{ __("Let's talk") }}</span>
        </div>
        <div class="p-6 text-[#f2f1ec]" :style="`background:${dark}`">
            <p class="text-xs opacity-60 mb-2">{{ __('Dark theme') }}</p>
            <p class="text-3xl font-bold leading-tight mb-2" :style="`font-family: '${display}'`">Selected work</p>
            <p class="text-2xl font-bold leading-tight mb-4" :style="`font-family: '${ar}'`" dir="rtl">أعمال مختارة</p>
            <p class="text-sm opacity-70 mb-4" :style="`font-family: '${body}'`">Motion design, animation & visual storytelling.</p>
            <span class="inline-flex items-center h-10 px-5 rounded-full text-sm font-bold" :style="`background:${primary}; color:#0b0b0c`">{{ __("Let's talk") }}</span>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        <div>
            <label class="label">{{ __('Brand / accent colour') }}</label>
            <div class="flex items-center gap-2">
                <label class="relative w-11 h-11 rounded-xl border border-line overflow-hidden shrink-0" :style="`background:${primary}`">
                    <input type="color" x-model="primary" class="absolute inset-0 opacity-0 cursor-pointer">
                </label>
                <input name="s[appearance][primary_color]" x-model="primary" class="field font-mono" dir="ltr">
            </div>
            <div class="flex flex-wrap gap-1.5 mt-2">
                <template x-for="c in presets" :key="c">
                    <button type="button" @click="primary = c" class="w-7 h-7 rounded-full border-2 transition" :class="primary === c ? 'border-ink scale-110' : 'border-line'" :style="`background:${c}`"></button>
                </template>
            </div>
        </div>
        <div>
            <label class="label">{{ __('Dark theme background') }}</label>
            <div class="flex items-center gap-2">
                <label class="relative w-11 h-11 rounded-xl border border-line overflow-hidden shrink-0" :style="`background:${dark}`">
                    <input type="color" x-model="dark" class="absolute inset-0 opacity-0 cursor-pointer">
                </label>
                <input name="s[appearance][ink_color]" x-model="dark" class="field font-mono" dir="ltr">
            </div>
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-5">
        <div>
            <label class="label">{{ __('Arabic font') }}</label>
            <select name="s[appearance][font_arabic]" x-model="ar" class="field">
                @foreach ($fonts['arabic'] as $font)<option value="{{ $font }}">{{ $font }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="label">{{ __('English headings font') }}</label>
            <select name="s[appearance][font_latin]" x-model="display" class="field">
                @foreach ($fonts['latin'] as $font)<option value="{{ $font }}">{{ $font }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="label">{{ __('English text font') }}</label>
            <select name="s[appearance][font_latin_body]" x-model="body" class="field">
                @foreach ($fonts['latin'] as $font)<option value="{{ $font }}">{{ $font }}</option>@endforeach
            </select>
        </div>
    </div>
</div>

<div class="grid md:grid-cols-2 gap-5 border-t border-line pt-5">
    @php
        $themeOptions = ['dark' => __('Dark'), 'light' => __('Light'), 'system' => __('Follow the device')];
    @endphp
    <div class="md:col-span-2 grid md:grid-cols-2 gap-5 rounded-2xl bg-canvas/60 border border-line p-4">
        <p class="md:col-span-2 flex items-center gap-2 text-sm font-bold"><x-icon name="globe" :size="16" /> {{ __('Website defaults') }}</p>
        <div>
            <label class="label">{{ __('Default theme for visitors') }}</label>
            <select name="s[appearance][default_theme]" class="field">
                @foreach ($themeOptions as $v => $l)
                    <option value="{{ $v }}" @selected(setting('appearance.default_theme') === $v)>{{ $l }}</option>
                @endforeach
            </select>
            <p class="mt-1.5 text-xs text-subtle">{{ __('Visitors can still switch with the toggle in the header; their choice is remembered.') }}</p>
        </div>
        <div>
            <label class="label">{{ __('Default language') }}</label>
            <select name="s[appearance][default_locale]" class="field">
                @foreach (locales() as $code => $info)
                    <option value="{{ $code }}" @selected(setting('appearance.default_locale') === $code)>{{ $info['name'] }}</option>
                @endforeach
            </select>
            <p class="mt-1.5 text-xs text-subtle">{{ __('Language for first-time visitors. A language they pick is remembered.') }}</p>
        </div>
        <div class="md:col-span-2">
            <x-s.toggle key="appearance.detect_browser_locale" :label="__('Use the visitor’s browser language')"
                        :hint="__('When on, first-time visitors whose browser is set to Arabic see the Arabic site, others the English one.')" />
        </div>
    </div>

    <div class="md:col-span-2 grid md:grid-cols-2 gap-5 rounded-2xl bg-canvas/60 border border-line p-4">
        <p class="md:col-span-2 flex items-center gap-2 text-sm font-bold"><x-icon name="grid" :size="16" /> {{ __('Dashboard defaults') }}</p>
        <div>
            <label class="label">{{ __('Dashboard theme') }}</label>
            <select name="s[appearance][admin_theme]" class="field">
                @foreach ($themeOptions as $v => $l)
                    <option value="{{ $v }}" @selected(setting('appearance.admin_theme') === $v)>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label">{{ __('Dashboard language') }}</label>
            <select name="s[appearance][admin_locale]" class="field">
                @foreach (locales() as $code => $info)
                    <option value="{{ $code }}" @selected(setting('appearance.admin_locale') === $code)>{{ $info['name'] }}</option>
                @endforeach
            </select>
            <p class="mt-1.5 text-xs text-subtle">{{ __('Each admin can still pick their own language from the top bar or their profile.') }}</p>
        </div>
    </div>
    <div>
        <label class="label">{{ __('Projects grid style') }}</label>
        <select name="s[appearance][grid_style]" class="field">
            @foreach (['editorial' => __('Editorial (mixed sizes)'), 'grid' => __('Uniform grid'), 'masonry' => __('Masonry')] as $v => $l)
                <option value="{{ $v }}" @selected(setting('appearance.grid_style') === $v)>{{ $l }}</option>
            @endforeach
        </select>
    </div>
    <x-s.field key="appearance.projects_per_page" :label="__('Projects per page')" type="number" min="3" max="60" />
</div>

<div class="border-t border-line divide-y divide-line">
    <x-s.toggle key="appearance.animations" :label="__('Scroll animations')" :hint="__('Elements fade and slide in as visitors scroll.')" />
    <x-s.toggle key="appearance.respect_reduced_motion" :label="__('Respect the device “reduce motion” setting')"
                :hint="__('When a visitor’s device has animations turned off (e.g. Windows “Animation effects”), the site shows everything still. Turn this off to always play the effects.')" />
    <div x-data="{ on: @js((bool) setting('appearance.custom_cursor')) }" @click="$nextTick(() => on = $el.querySelector('input[type=hidden]').value === '1')">
        <x-s.toggle key="appearance.custom_cursor" :label="__('Custom cursor')" :hint="__('A brand-coloured circle that follows the mouse (desktop only).')" />
        <div x-show="on" x-collapse class="pb-3">
            <label class="label">{{ __('Show the custom cursor') }}</label>
            <div class="grid grid-cols-2 gap-2">
                @foreach (['hero' => __('In the hero section only'), 'site' => __('Across the whole site')] as $value => $text)
                    <label class="cursor-pointer">
                        <input type="radio" name="s[appearance][cursor_scope]" value="{{ $value }}" class="peer sr-only" @checked(setting('appearance.cursor_scope', 'hero') === $value)>
                        <span class="flex items-center justify-center h-11 rounded-xl border border-line text-sm font-bold peer-checked:border-accent-500 peer-checked:bg-accent-500 peer-checked:text-secondary-900">{{ $text }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>
    <x-s.toggle key="appearance.protect_media" :label="__('Protect images & videos')" :hint="__('Disables right-click “Save as” and dragging on your work.')" />
</div>
