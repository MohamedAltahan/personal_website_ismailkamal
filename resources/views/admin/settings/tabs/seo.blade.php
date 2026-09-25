<x-s.field key="seo.meta_description" :label="__('Site description')" translatable multiline :hint="__('Shown under your site name in Google results (max ~160 characters).')" />
<x-s.field key="seo.keywords" :label="__('Keywords')" translatable :hint="__('Comma separated, e.g. motion graphics, 2D animation, Cairo.')" />
<x-s.field key="seo.google_analytics" :label="__('Google Analytics ID')" dir="ltr" placeholder="G-XXXXXXXXXX" />
<div class="border-t border-line">
    <x-s.toggle key="seo.indexable" :label="__('Allow search engines')" :hint="__('Turn off while the site is being prepared to hide it from Google.')" />
</div>
<p class="text-xs text-subtle">{{ __('Sitemap:') }} <a href="{{ route('sitemap') }}" target="_blank" class="underline" dir="ltr">{{ route('sitemap') }}</a></p>
