<?php

return [

    /*
    | Supported interface / content languages.
    */
    'locales' => [
        'ar' => ['name' => 'العربية', 'native' => 'ع', 'dir' => 'rtl'],
        'en' => ['name' => 'English', 'native' => 'EN', 'dir' => 'ltr'],
    ],

    'media_disk' => 'uploads',

    /*
    | Fonts the client can pick from in Settings → Appearance (Google Fonts families).
    */
    'fonts' => [
        'arabic' => ['IBM Plex Sans Arabic', 'Cairo', 'Tajawal', 'Almarai', 'Readex Pro', 'Noto Kufi Arabic', 'Rubik', 'El Messiri', 'Alexandria', 'Changa'],
        'latin' => ['Space Grotesk', 'Syne', 'Unbounded', 'Manrope', 'Inter', 'DM Sans', 'Outfit', 'Plus Jakarta Sans', 'Sora', 'Bricolage Grotesque', 'Archivo', 'Instrument Sans'],
    ],

    /*
    | Default values for every setting. Stored values override these.
    | Keys are "group.key"; translatable values are {ar, en} arrays.
    */
    'defaults' => [
        // General
        'general.site_name' => ['ar' => 'إسماعيل كمال', 'en' => 'Ismail Kamal'],
        'general.tagline' => ['ar' => 'مصمم موشن جرافيك', 'en' => 'Motion Graphic Designer'],
        'general.contact_email' => null,
        'general.contact_phone' => null,
        'general.whatsapp' => null,
        'general.contact_address' => ['ar' => null, 'en' => null],
        'general.available_for_work' => true,

        // Branding
        'branding.logo' => null,       // media id
        'branding.logo_dark' => null,  // media id (used on dark theme)
        'branding.favicon' => null,    // media id
        'branding.og_image' => null,   // media id

        // Appearance
        'appearance.primary_color' => '#ffd200',
        'appearance.ink_color' => '#0b0b0c',
        'appearance.font_arabic' => 'IBM Plex Sans Arabic',
        'appearance.font_latin' => 'Space Grotesk', // display (headings) in English
        'appearance.font_latin_body' => 'Manrope',
        'appearance.default_theme' => 'dark',    // website: system | light | dark
        'appearance.default_locale' => 'en',     // website language for first-time visitors
        'appearance.detect_browser_locale' => false, // use the visitor's browser language instead of the default
        'appearance.admin_locale' => 'ar',       // dashboard default language
        'appearance.admin_theme' => 'light',     // dashboard default theme: system | light | dark
        'appearance.protect_media' => true,      // disable right-click / drag on media
        'appearance.animations' => true,
        'appearance.respect_reduced_motion' => true, // honour the OS "reduce motion" preference
        'appearance.custom_cursor' => true,
        'appearance.cursor_scope' => 'hero',      // hero | site — where the custom cursor is shown
        'appearance.projects_per_page' => 12,
        'appearance.grid_style' => 'editorial',  // editorial | uniform | masonry

        // Media processing
        'media.quality' => 82,
        'media.format' => 'webp',                // webp | avif | jpg | original
        'media.widths' => [480, 960, 1600, 2400],
        'media.keep_original' => true,
        'media.max_upload_mb' => 512,
        'media.max_dimension' => 3200,           // longest side of the "full" rendition
        'media.watermark_enabled' => false,
        'media.watermark' => null,               // media id
        'media.watermark_position' => 'bottom-right',
        'media.watermark_opacity' => 40,
        'media.watermark_scale' => 15,           // % of image width

        // SEO
        'seo.meta_description' => ['ar' => null, 'en' => null],
        'seo.keywords' => ['ar' => null, 'en' => null],
        'seo.google_analytics' => null,
        'seo.indexable' => true,

        // Contact
        'contact.notify' => false,
        'contact.notify_email' => null,
    ],
];
