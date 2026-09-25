<?php

namespace App\Support\Blocks;

use App\Support\HtmlSanitizer;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Single source of truth for page-builder blocks.
 *
 * A block is stored as:
 *   { id, type, data: {...}, style: {...} }
 * Translatable strings inside `data` are {ar, en} objects. Media are referenced by id
 * under the keys listed in MEDIA_KEYS (at any depth, e.g. data.items[].media).
 */
class BlockRegistry
{
    public const MEDIA_KEYS = ['media', 'poster', 'before', 'after'];

    /** Fields holding rich HTML (sanitised on save). */
    private const HTML_KEYS = ['html'];

    public const HERO_EFFECTS = ['none', 'aurora', 'particles', 'spotlight', 'grid', 'waves', 'stars'];

    public const TEXT_EFFECTS = ['none', 'rise', 'scramble', 'typewriter', 'shimmer'];

    public const STYLE_DEFAULTS = [
        'bg' => null,              // hex colour or null (transparent)
        'text' => null,            // hex colour or null (inherit)
        'pt' => 'md',              // none | sm | md | lg | xl
        'pb' => 'md',
        'width' => 'container',    // full | wide | container | narrow
        'animate' => 'fade',       // none | fade | slide | zoom
        'hide_mobile' => false,
        'hide_desktop' => false,
        'anchor' => null,
    ];

    /** @return array<string, array{group: string, icon: string, label: array, data: array, style?: array}> */
    public static function types(): array
    {
        $t = fn () => ['ar' => '', 'en' => ''];

        return [
            'hero' => [
                'group' => 'layout', 'icon' => 'hero',
                'label' => ['ar' => 'واجهة رئيسية', 'en' => 'Hero'],
                'data' => [
                    'eyebrow' => $t(), 'title' => $t(), 'subtitle' => $t(),
                    'media' => null, 'overlay' => 35, 'height' => 'large', 'align' => 'start',
                    'button_label' => $t(), 'button_url' => null, 'scroll_hint' => true,
                    // Visual effects (see resources/js/site/hero-effects.js)
                    'effect' => 'aurora',        // none | aurora | particles | spotlight | grid | waves | stars
                    'effect_intensity' => 60,    // 10–100
                    'effect_color' => null,      // hex; null = brand colour
                    'text_effect' => 'rise',     // none | rise | scramble | typewriter | shimmer
                    'grain' => true,             // animated film grain
                    'interactive' => true,       // mouse parallax + magnetic button
                    'marquee' => $t(),           // comma separated words shown as a moving strip
                ],
                'style' => ['width' => 'full', 'pt' => 'none', 'pb' => 'none'],
            ],
            'heading' => [
                'group' => 'text', 'icon' => 'heading',
                'label' => ['ar' => 'عنوان', 'en' => 'Heading'],
                'data' => ['eyebrow' => $t(), 'text' => $t(), 'size' => 'lg', 'align' => 'start'],
            ],
            'text' => [
                'group' => 'text', 'icon' => 'text',
                'label' => ['ar' => 'نص', 'en' => 'Text'],
                'data' => ['html' => $t(), 'size' => 'base', 'align' => 'start', 'columns' => 1],
                'style' => ['width' => 'narrow'],
            ],
            'image' => [
                'group' => 'media', 'icon' => 'image',
                'label' => ['ar' => 'صورة', 'en' => 'Image'],
                'data' => ['media' => null, 'caption' => $t(), 'rounded' => false, 'link' => null, 'lightbox' => true],
            ],
            'gallery' => [
                'group' => 'media', 'icon' => 'gallery',
                'label' => ['ar' => 'معرض صور', 'en' => 'Gallery'],
                'data' => ['items' => [], 'layout' => 'grid', 'columns' => 3, 'gap' => 'md', 'ratio' => 'auto', 'lightbox' => true, 'rounded' => false],
            ],
            'video' => [
                'group' => 'media', 'icon' => 'video',
                'label' => ['ar' => 'فيديو', 'en' => 'Video'],
                'data' => [
                    'media' => null, 'poster' => null, 'caption' => $t(),
                    'autoplay' => false, 'loop' => false, 'muted' => false, 'controls' => true, 'ratio' => 'auto', 'rounded' => false,
                ],
            ],
            'split' => [
                'group' => 'layout', 'icon' => 'split',
                'label' => ['ar' => 'صورة + نص', 'en' => 'Media + text'],
                'data' => [
                    'media' => null, 'eyebrow' => $t(), 'heading' => $t(), 'html' => $t(),
                    'media_side' => 'start', 'ratio' => '50', 'valign' => 'center', 'rounded' => false,
                    'button_label' => $t(), 'button_url' => null,
                ],
                'style' => ['width' => 'wide'],
            ],
            'columns' => [
                'group' => 'layout', 'icon' => 'columns',
                'label' => ['ar' => 'أعمدة', 'en' => 'Columns'],
                'data' => [
                    'count' => 2, 'gap' => 'md', 'valign' => 'start',
                    'cols' => [
                        ['kind' => 'text', 'media' => null, 'html' => $t(), 'caption' => $t()],
                        ['kind' => 'image', 'media' => null, 'html' => $t(), 'caption' => $t()],
                    ],
                ],
                'style' => ['width' => 'wide'],
            ],
            'before_after' => [
                'group' => 'media', 'icon' => 'compare',
                'label' => ['ar' => 'قبل / بعد', 'en' => 'Before / after'],
                'data' => [
                    'before' => null, 'after' => null,
                    'label_before' => ['ar' => 'قبل', 'en' => 'Before'], 'label_after' => ['ar' => 'بعد', 'en' => 'After'],
                ],
            ],
            'quote' => [
                'group' => 'text', 'icon' => 'quote',
                'label' => ['ar' => 'اقتباس', 'en' => 'Quote'],
                'data' => ['text' => $t(), 'author' => $t(), 'role' => $t()],
                'style' => ['width' => 'narrow'],
            ],
            'credits' => [
                'group' => 'text', 'icon' => 'list',
                'label' => ['ar' => 'تفاصيل / فريق العمل', 'en' => 'Credits / facts'],
                'data' => ['items' => [
                    ['label' => ['ar' => 'العميل', 'en' => 'Client'], 'value' => $t()],
                    ['label' => ['ar' => 'الدور', 'en' => 'Role'], 'value' => $t()],
                ], 'columns' => 3],
            ],
            'cta' => [
                'group' => 'layout', 'icon' => 'cta',
                'label' => ['ar' => 'دعوة لإجراء', 'en' => 'Call to action'],
                'data' => [
                    'title' => ['ar' => 'لنصنع شيئاً مميزاً معاً', 'en' => "Let's make something great"],
                    'text' => $t(),
                    'button_label' => ['ar' => 'تواصل معي', 'en' => 'Get in touch'], 'button_url' => null,
                ],
            ],
            'projects' => [
                'group' => 'layout', 'icon' => 'grid',
                'label' => ['ar' => 'شبكة أعمال', 'en' => 'Projects grid'],
                'data' => [
                    'title' => $t(), 'source' => 'featured', 'category_id' => null, 'limit' => 6,
                    'layout' => 'editorial', 'show_all_link' => true,
                ],
                'style' => ['width' => 'wide'],
            ],
            'embed' => [
                'group' => 'media', 'icon' => 'embed',
                'label' => ['ar' => 'تضمين (YouTube/Vimeo)', 'en' => 'Embed (YouTube/Vimeo)'],
                'data' => ['media' => null, 'autoplay' => false, 'loop' => false, 'controls' => true, 'ratio' => '16:9', 'caption' => $t()],
            ],
            'spacer' => [
                'group' => 'layout', 'icon' => 'spacer',
                'label' => ['ar' => 'مسافة / فاصل', 'en' => 'Spacer / divider'],
                'data' => ['size' => 'md', 'line' => false],
                'style' => ['pt' => 'none', 'pb' => 'none', 'animate' => 'none'],
            ],
        ];
    }

    /** Blank block of the given type (used by the editor and legacy conversion). */
    public static function make(string $type, array $data = [], array $style = []): array
    {
        $def = static::types()[$type] ?? throw new \InvalidArgumentException("Unknown block type [$type]");

        return [
            'id' => 'b_'.Str::lower(Str::random(10)),
            'type' => $type,
            'data' => array_replace($def['data'], $data),
            'style' => array_replace(static::STYLE_DEFAULTS, $def['style'] ?? [], $style),
        ];
    }

    /**
     * Normalise and validate blocks coming from the editor: unknown types are rejected,
     * keys are limited to the schema, HTML is sanitised and media ids are cast to int.
     *
     * @throws ValidationException
     */
    public static function sanitize(mixed $blocks): array
    {
        if (! is_array($blocks)) {
            throw ValidationException::withMessages(['blocks' => __('Invalid page content.')]);
        }

        $types = static::types();
        $clean = [];

        foreach (array_values($blocks) as $i => $block) {
            $type = $block['type'] ?? null;

            if (! is_string($type) || ! isset($types[$type])) {
                throw ValidationException::withMessages(['blocks' => __('Unknown block type at position :n.', ['n' => $i + 1])]);
            }

            $def = $types[$type];
            $data = static::cleanValue($def['data'], $block['data'] ?? []);
            $style = static::cleanValue(array_replace(static::STYLE_DEFAULTS, $def['style'] ?? []), $block['style'] ?? []);

            $clean[] = [
                'id' => preg_match('/^b_[a-z0-9]{4,20}$/', (string) ($block['id'] ?? '')) ? $block['id'] : 'b_'.Str::lower(Str::random(10)),
                'type' => $type,
                'data' => $data,
                'style' => $style,
            ];
        }

        return $clean;
    }

    /**
     * Recursively coerce $value to the shape of $schema. Lists (like gallery items)
     * accept any number of entries shaped like the first schema entry, or free-form rows.
     */
    private static function cleanValue(mixed $schema, mixed $value, ?string $key = null): mixed
    {
        if (in_array($key, static::MEDIA_KEYS, true)) {
            return is_numeric($value) && (int) $value > 0 ? (int) $value : null;
        }

        if (is_array($schema) && static::isTranslation($schema)) {
            $value = is_array($value) ? $value : [];
            $out = [];
            foreach (array_keys(config('site.locales')) as $locale) {
                $text = (string) ($value[$locale] ?? '');
                $out[$locale] = in_array($key, static::HTML_KEYS, true) ? HtmlSanitizer::clean($text) : strip_tags($text);
            }

            return $out;
        }

        if (is_array($schema) && array_is_list($schema)) {
            if (! is_array($value)) {
                return [];
            }
            $rowSchema = $schema[0] ?? null;

            if (! is_array($rowSchema)) {
                // Free-form media rows (gallery items): rows without a media are useless.
                return array_values(array_filter(
                    array_map(fn ($row) => static::cleanRow(is_array($row) ? $row : []), array_slice($value, 0, 200)),
                    fn ($row) => $row['media'] !== null,
                ));
            }

            return array_values(array_map(
                fn ($row) => static::cleanValue($rowSchema, is_array($row) ? $row : []),
                array_slice($value, 0, 200),
            ));
        }

        if (is_array($schema)) {
            $value = is_array($value) ? $value : [];
            $out = [];
            foreach ($schema as $k => $default) {
                $out[$k] = array_key_exists($k, $value) ? static::cleanValue($default, $value[$k], $k) : $default;
            }

            return $out;
        }

        // Scalars: keep type of the default where it is known.
        return match (true) {
            is_bool($schema) => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            is_int($schema) => is_numeric($value) ? (int) $value : $schema,
            default => $value === null || $value === '' ? null : Str::limit(strip_tags((string) $value), 500, ''),
        };
    }

    /** Free-form list rows (gallery items): media id + caption. */
    private static function cleanRow(array $row): array
    {
        return [
            'media' => is_numeric($row['media'] ?? null) ? (int) $row['media'] : null,
            'caption' => static::cleanValue(['ar' => '', 'en' => ''], $row['caption'] ?? []),
            'span' => in_array($row['span'] ?? null, ['1', '2', 'full'], true) ? $row['span'] : '1',
        ];
    }

    private static function isTranslation(array $schema): bool
    {
        return $schema !== [] && array_keys($schema) === array_keys(config('site.locales'));
    }

    /** Every media id referenced anywhere in the blocks. */
    public static function mediaIds(array $blocks): array
    {
        $ids = [];

        array_walk_recursive($blocks, function ($value, $key) use (&$ids) {
            if (in_array($key, static::MEDIA_KEYS, true) && is_numeric($value)) {
                $ids[] = (int) $value;
            }
        });

        return array_values(array_unique($ids));
    }

    /** Payload handed to the JS editor. */
    public static function forEditor(): array
    {
        return collect(static::types())->map(fn ($def, $type) => [
            'type' => $type,
            'group' => $def['group'],
            'icon' => $def['icon'],
            'label' => $def['label'],
            'blank' => static::make($type),
        ])->values()->all();
    }
}
