<div class="grid md:grid-cols-2 gap-6">
    <x-s.media key="branding.logo" :media="$pickerMedia['branding.logo']" :label="__('Logo — light theme')" :hint="__('Dark logo for light backgrounds. PNG/SVG with transparency works best.')" />
    <x-s.media key="branding.logo_dark" :media="$pickerMedia['branding.logo_dark']" :label="__('Logo — dark theme')" dark :hint="__('Light logo for dark backgrounds. If only one logo is set, it is inverted automatically for the other theme.')" />
    <x-s.media key="branding.favicon" :media="$pickerMedia['branding.favicon']" :label="__('Favicon / app icon')" :hint="__('Square image, at least 512×512.')" />
    <x-s.media key="branding.og_image" :media="$pickerMedia['branding.og_image']" :label="__('Share image')" :hint="__('Shown when your site link is shared on WhatsApp, Facebook, X… (1200×630).')" />
</div>
<p class="text-xs text-subtle">{{ __('Without a logo, your site name is shown as text.') }}</p>
