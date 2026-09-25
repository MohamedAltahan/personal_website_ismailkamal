<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Support\Settings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingController extends Controller
{
    public const TABS = ['general', 'branding', 'appearance', 'media', 'seo', 'contact'];

    public function index(?string $tab = null): View
    {
        $tab = in_array($tab, self::TABS, true) ? $tab : 'general';

        $mediaKeys = ['branding.logo', 'branding.logo_dark', 'branding.favicon', 'branding.og_image', 'media.watermark'];
        $media = Media::whereIn('id', array_filter(array_map(fn ($k) => setting($k), $mediaKeys)))->get()->keyBy('id');

        return view('admin.settings.index', [
            'tab' => $tab,
            'tabs' => self::TABS,
            'pickerMedia' => collect($mediaKeys)->mapWithKeys(fn ($k) => [$k => ($m = $media[setting($k)] ?? null) ? $m->toPicker() : null]),
            'sample' => Media::images()->whereNotIn('mime', ['image/gif', 'image/svg+xml'])->whereNotNull('width')->where('width', '>=', 1200)->latest('id')->first(),
            'imageCount' => Media::images()->count(),
        ]);
    }

    public function update(Request $request, Settings $settings, string $tab): RedirectResponse
    {
        abort_unless(in_array($tab, self::TABS, true), 404);

        $t = fn (int $max = 200) => ['nullable', 'string', 'max:'.$max];
        $media = ['nullable', 'integer', 'exists:media,id'];
        $color = ['required', 'regex:/^#[0-9a-fA-F]{6}$/'];

        $rules = match ($tab) {
            'general' => [
                'general.site_name.ar' => $t(), 'general.site_name.en' => $t(),
                'general.tagline.ar' => $t(), 'general.tagline.en' => $t(),
                'general.contact_email' => ['nullable', 'email', 'max:200'],
                'general.contact_phone' => $t(50),
                'general.whatsapp' => $t(50),
                'general.contact_address.ar' => $t(250), 'general.contact_address.en' => $t(250),
                'general.available_for_work' => ['boolean'],
            ],
            'branding' => [
                'branding.logo' => $media, 'branding.logo_dark' => $media,
                'branding.favicon' => $media, 'branding.og_image' => $media,
            ],
            'appearance' => [
                'appearance.primary_color' => $color,
                'appearance.ink_color' => $color,
                'appearance.font_arabic' => ['required', Rule::in(config('site.fonts.arabic'))],
                'appearance.font_latin' => ['required', Rule::in(config('site.fonts.latin'))],
                'appearance.font_latin_body' => ['required', Rule::in(config('site.fonts.latin'))],
                'appearance.default_theme' => ['required', Rule::in(['system', 'light', 'dark'])],
                'appearance.default_locale' => ['required', Rule::in(array_keys(locales()))],
                'appearance.detect_browser_locale' => ['boolean'],
                'appearance.admin_locale' => ['required', Rule::in(array_keys(locales()))],
                'appearance.admin_theme' => ['required', Rule::in(['system', 'light', 'dark'])],
                'appearance.grid_style' => ['required', Rule::in(['editorial', 'grid', 'masonry'])],
                'appearance.projects_per_page' => ['required', 'integer', 'min:3', 'max:60'],
                'appearance.protect_media' => ['boolean'],
                'appearance.animations' => ['boolean'],
                'appearance.respect_reduced_motion' => ['boolean'],
                'appearance.custom_cursor' => ['boolean'],
                'appearance.cursor_scope' => ['required', Rule::in(['hero', 'site'])],
            ],
            'media' => [
                'media.quality' => ['required', 'integer', 'min:10', 'max:100'],
                'media.format' => ['required', Rule::in(['webp', 'avif', 'jpg', 'original'])],
                'media.widths' => ['required', 'array', 'min:1', 'max:8'],
                'media.widths.*' => ['integer', 'min:160', 'max:6000'],
                'media.max_dimension' => ['required', 'integer', 'min:800', 'max:8000'],
                'media.keep_original' => ['boolean'],
                'media.max_upload_mb' => ['required', 'integer', 'min:1', 'max:4096'],
                'media.watermark_enabled' => ['boolean'],
                'media.watermark' => $media,
                'media.watermark_position' => ['required', Rule::in(['top-left', 'top-right', 'bottom-left', 'bottom-right', 'center'])],
                'media.watermark_opacity' => ['required', 'integer', 'min:5', 'max:100'],
                'media.watermark_scale' => ['required', 'integer', 'min:5', 'max:60'],
            ],
            'seo' => [
                'seo.meta_description.ar' => $t(300), 'seo.meta_description.en' => $t(300),
                'seo.keywords.ar' => $t(300), 'seo.keywords.en' => $t(300),
                'seo.google_analytics' => ['nullable', 'regex:/^(G|UA)-[A-Z0-9-]+$/i'],
                'seo.indexable' => ['boolean'],
            ],
            'contact' => [
                'contact.notify' => ['boolean'],
                'contact.notify_email' => ['nullable', 'email'],
            ],
        };

        // Fields are posted as s[group][key] (checkboxes carry a hidden "0" fallback).
        $input = (array) $request->input('s', []);
        validator($input, $rules)->validate();

        // Flatten back to "group.key" => value, keeping {ar,en} arrays and lists intact.
        $values = [];
        foreach (array_keys($rules) as $ruleKey) {
            if (str_ends_with($ruleKey, '.*')) {
                continue;
            }
            $parts = explode('.', $ruleKey);
            $key = $parts[0].'.'.$parts[1];
            $values[$key] = data_get($input, $key);
        }

        if (isset($values['media.widths'])) {
            $values['media.widths'] = collect($values['media.widths'])->map(fn ($w) => (int) $w)->unique()->sort()->values()->all();
        }
        foreach ($rules as $key => $rule) {
            if (in_array('boolean', (array) $rule, true)) {
                $values[$key] = (bool) ($values[$key] ?? false);
            }
        }
        foreach (['appearance.projects_per_page','media.quality', 'media.max_dimension', 'media.max_upload_mb', 'media.watermark_opacity', 'media.watermark_scale'] as $int) {
            if (array_key_exists($int, $values)) {
                $values[$int] = (int) $values[$int];
            }
        }
        foreach (['branding.logo', 'branding.logo_dark', 'branding.favicon', 'branding.og_image', 'media.watermark'] as $mediaKey) {
            if (array_key_exists($mediaKey, $values)) {
                $values[$mediaKey] = $values[$mediaKey] ? (int) $values[$mediaKey] : null;
            }
        }

        $settings->set($values);
        site_changed();

        $message = __('Settings saved.');
        if ($tab === 'media') {
            $message .= ' '.__('New uploads use these settings. Use “Re-process all images” to apply them to existing images.');
        }

        return redirect()->route('admin.settings.index', $tab)->with('success', $message);
    }
}
