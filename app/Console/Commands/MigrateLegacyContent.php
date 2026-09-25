<?php

namespace App\Console\Commands;

use App\Models\Page;
use App\Models\Project;
use App\Services\MediaService;
use App\Support\Blocks\BlockRegistry;
use App\Support\HtmlSanitizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Converts the old portfolio data (designs + images/videos tables, single-row settings)
 * into the block-based model. Safe to run repeatedly: files are adopted once
 * (media.legacy_path) and already-converted records are skipped unless --force.
 */
class MigrateLegacyContent extends Command
{
    protected $signature = 'portfolio:migrate-legacy {--force : Rebuild blocks of already converted projects/pages} {--dry-run : Only report what would happen}';

    protected $description = 'Convert legacy designs, media and settings into the new block-based portfolio';

    public function handle(MediaService $media): int
    {
        $dry = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');

        $this->migrateSettings($media, $dry);
        $this->translateCategories($dry);
        $this->migrateProjects($media, $dry, $force);
        $this->migrateHome($media, $dry, $force);
        $this->migrateAbout($dry, $force);

        $this->newLine();
        $this->info($dry ? 'Dry run finished — nothing was written.' : 'Legacy content converted.');

        return self::SUCCESS;
    }

    private function migrateSettings(MediaService $media, bool $dry): void
    {
        $this->components->task('Settings', function () use ($media, $dry) {
            if (DB::table('site_settings')->exists() && ! $this->option('force')) {
                return true; // Already configured from the new dashboard; don't overwrite.
            }

            $values = [];

            if (Schema::hasTable('settings') && $row = DB::table('settings')->first()) {
                $values['general.site_name'] = ['ar' => 'إسماعيل كمال', 'en' => \Illuminate\Support\Str::title($row->site_name)];
                $values['general.contact_email'] = $row->contact_email;
                $values['general.contact_phone'] = $row->contact_phone;
                $values['general.whatsapp'] = $row->contact_phone;
                $values['general.contact_address'] = ['ar' => $row->contact_address, 'en' => $row->contact_address];
            }

            if (Schema::hasTable('website_colors') && $row = DB::table('website_colors')->latest('id')->first()) {
                if (preg_match('/^#[0-9a-f]{6}$/i', (string) $row->btn)) {
                    $values['appearance.primary_color'] = strtolower($row->btn);
                }
            }

            if (Schema::hasTable('logo_settings') && $row = DB::table('logo_settings')->latest('id')->first()) {
                if (! $dry) {
                    // The legacy logo is white (made for the old black site): use it on dark backgrounds;
                    // the light theme shows it inverted until a dedicated light logo is uploaded.
                    // Fully transparent placeholders (used to hide the logo) are skipped.
                    $adoptVisible = fn ($path) => $path && ! $this->isBlankImage(public_path('uploads/'.$path))
                        ? $media->adopt($path, 'branding')?->id : null;
                    $values['branding.logo_dark'] = $adoptVisible($row->main_logo);
                    $values['branding.logo'] = null;
                    $values['branding.favicon'] = $adoptVisible($row->icon);
                }
            }

            if (! $dry && $values) {
                setting()->set($values);
            }

            return true;
        });
    }

    /** Legacy categories only had one (English) name: give the known ones an Arabic name. */
    private function translateCategories(bool $dry): void
    {
        $arabic = [
            '3d' => 'ثري دي', 'motion' => 'موشن جرافيك', 'social media' => 'سوشيال ميديا', 'intros' => 'مقدمات',
            'live+motion' => 'لايف أكشن + موشن', 'whiteboard' => 'وايت بورد', 'logo animation' => 'تحريك الشعارات',
            'events' => 'فعاليات',
        ];

        $this->components->task('Category names', function () use ($arabic, $dry) {
            foreach (\App\Models\Category::all() as $category) {
                $en = $category->getTranslation('name', 'en', false);
                $ar = $category->getTranslation('name', 'ar', false);
                $key = mb_strtolower(trim((string) $en));

                if ($ar === $en && isset($arabic[$key]) && ! $dry) {
                    $category->setTranslation('name', 'ar', $arabic[$key])->save();
                }
            }

            return true;
        });
    }

    private function migrateProjects(MediaService $media, bool $dry, bool $force): void
    {
        $designs = DB::table('designs')->orderBy('id')->get();
        $this->info("Projects: {$designs->count()}");

        foreach ($designs as $index => $design) {
            $this->components->task("#{$design->id} {$design->name}", function () use ($media, $dry, $force, $design, $index) {
                if ($design->blocks !== null && ! $force) {
                    return true;
                }

                $videos = DB::table('videos')->where('design_id', $design->id)->orderBy('id')->get();
                $images = DB::table('images')->where('design_id', $design->id)->orderBy('id')->get();

                if ($dry) {
                    $this->line("    → {$videos->count()} video block(s), gallery of {$images->count()} image(s)");

                    return true;
                }

                $blocks = [];

                foreach ($videos as $video) {
                    $file = $media->adopt($video->name, 'projects');
                    if (! $file) {
                        continue;
                    }
                    if ($video->video_thumbnail && $poster = $media->adopt($video->video_thumbnail, 'projects')) {
                        $file->update(['poster_id' => $poster->id]);
                    }
                    $blocks[] = BlockRegistry::make('video', ['media' => $file->id, 'controls' => true], ['width' => 'wide', 'pt' => 'sm', 'pb' => 'sm']);
                }

                $items = $images->map(fn ($image) => $media->adopt($image->name, 'projects'))
                    ->filter()
                    ->map(fn ($file) => ['media' => $file->id, 'caption' => ['ar' => '', 'en' => ''], 'span' => '1'])
                    ->values()->all();

                if ($items) {
                    $blocks[] = BlockRegistry::make('gallery', ['items' => $items, 'layout' => 'stack', 'columns' => 1, 'gap' => 'sm'], ['width' => 'wide', 'pt' => 'sm']);
                }

                $cover = $design->thumbnail ? $media->adopt($design->thumbnail, 'covers') : null;

                $project = Project::findOrFail($design->id);
                $project->fill([
                    'slug' => $project->slug ?: $this->uniqueSlug((string) $design->name, $design->id),
                    'title' => ['ar' => $design->name, 'en' => $design->name],
                    'blocks' => $blocks,
                    'cover_media_id' => $cover?->id,
                    'year' => $project->year ?: substr((string) $design->created_at, 0, 4),
                    'published_at' => $project->published_at ?: $design->created_at,
                    'sort_order' => $project->sort_order ?: $index + 1,
                ]);
                $project->save();

                return true;
            });
        }
    }

    private function migrateHome(MediaService $media, bool $dry, bool $force): void
    {
        $this->components->task('Home page', function () use ($media, $dry, $force) {
            $page = Page::findSlug(Page::HOME);
            if (($page && ! $force) || $dry) {
                return true;
            }

            $legacy = Schema::hasTable('home_page_settings') ? DB::table('home_page_settings')->first() : null;
            $clean = fn ($html) => trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string) $html))));

            $blocks = [];
            $blocks[] = BlockRegistry::make('hero', [
                'eyebrow' => ['ar' => 'مصمم موشن جرافيك', 'en' => 'Motion Graphic Designer'],
                'title' => ['ar' => 'أهلاً، أنا إسماعيل', 'en' => $legacy ? rtrim($clean($legacy->main_title), ', ') : 'Hi, my name is Ismail'],
                'subtitle' => [
                    'ar' => 'مصمم موشن جرافيك محترف، ومع الوقت اكتشفت شغفي بالأنيميشن. عملت في وكالات واستوديوهات وشركات تصميم لأكثر من 6 سنوات، ومتاح للعمل خارج البلد أو بنظام العمل الحر.',
                    'en' => $legacy ? $clean($legacy->description) : '',
                ],
                'media' => $this->heroBackground($media),
                'overlay' => 10,
                'effect_intensity' => 35,
                'button_label' => ['ar' => 'شاهد أعمالي', 'en' => 'See my work'],
                'button_url' => '#work',
                'marquee' => ['ar' => 'موشن جرافيك، أنيميشن، مونتاج، إعلانات، فعاليات', 'en' => 'Motion Graphics, Animation, Video Editing, Advertising, Events'],
            ]);

            foreach (DB::table('videos')->whereNull('design_id')->where('at_home', 'yes')->orderBy('id')->get() as $video) {
                if ($file = $media->adopt($video->name, 'home')) {
                    if ($video->video_thumbnail && $poster = $media->adopt($video->video_thumbnail, 'home')) {
                        $file->update(['poster_id' => $poster->id]);
                    }
                    $blocks[] = BlockRegistry::make('heading', ['eyebrow' => ['ar' => 'شوريل', 'en' => 'Showreel'], 'text' => ['ar' => 'مختارات من أعمالي', 'en' => 'A glimpse of my work']]);
                    $blocks[] = BlockRegistry::make('video', ['media' => $file->id, 'controls' => true], ['width' => 'wide', 'pt' => 'none']);
                }
            }

            $homeImages = DB::table('images')->whereNull('design_id')->where('at_home', 'yes')->orderBy('id')->get()
                ->map(fn ($image) => $media->adopt($image->name, 'home'))->filter()
                ->map(fn ($file) => ['media' => $file->id, 'caption' => ['ar' => '', 'en' => ''], 'span' => '1'])->values()->all();
            if ($homeImages) {
                $blocks[] = BlockRegistry::make('gallery', ['items' => $homeImages, 'layout' => 'masonry', 'columns' => 3]);
            }

            $blocks[] = BlockRegistry::make('projects', [
                'title' => ['ar' => 'أحدث الأعمال', 'en' => 'Selected work'], 'source' => 'latest', 'limit' => 9,
            ], ['anchor' => 'work']);
            $blocks[] = BlockRegistry::make('cta');

            Page::updateOrCreate(['slug' => Page::HOME], [
                'title' => ['ar' => 'الرئيسية', 'en' => 'Home'],
                'blocks' => $blocks,
                'is_system' => true,
                'status' => 'active',
            ]);

            return true;
        });
    }

    private function migrateAbout(bool $dry, bool $force): void
    {
        $this->components->task('About page', function () use ($dry, $force) {
            $page = Page::findSlug(Page::ABOUT);
            if (($page && ! $force) || $dry) {
                return true;
            }

            $legacy = Schema::hasTable('abouts') ? DB::table('abouts')->first() : null;
            $html = $legacy ? HtmlSanitizer::clean($legacy->content) : '';
            // Legacy content is one centred H1 + text; flatten headings into paragraphs.
            $html = preg_replace(['~<h[1-6][^>]*>~', '~</h[1-6]>~'], ['<p>', '</p>'], $html);
            $html = preg_replace('~<p>\s*</p>~', '', $html);

            Page::updateOrCreate(['slug' => Page::ABOUT], [
                'title' => ['ar' => 'عني', 'en' => 'About'],
                'blocks' => [
                    BlockRegistry::make('heading', ['eyebrow' => ['ar' => 'عني', 'en' => 'About'], 'text' => ['ar' => 'إسماعيل كمال', 'en' => 'Ismail Kamal'], 'size' => 'xl']),
                    BlockRegistry::make('text', ['html' => [
                        'ar' => '<p>أنا فنان موشن جرافيك متخصص في المونتاج والإعلانات. محترف في Audition و After Effects و Photoshop و Illustrator، وأصنع محتوى متحركاً وتصاميم جذابة تناسب احتياجات العملاء.</p><p>دعنا نحوّل فكرتك إلى واقع بإبداع!</p>',
                        'en' => $html,
                    ], 'size' => 'lg']),
                    BlockRegistry::make('cta'),
                ],
                'is_system' => true,
                'status' => 'active',
            ]);

            return true;
        });
    }

    /**
     * Dark abstract background for the home hero (database/assets/hero-dark.jpg, generated by
     * database/assets/make-hero-background.php). Added to the media library once.
     */
    private function heroBackground(MediaService $media): ?int
    {
        $existing = \App\Models\Media::where('name', 'hero-dark-background.jpg')->value('id');
        if ($existing) {
            return $existing;
        }

        $source = database_path('assets/hero-dark.jpg');
        if (! is_file($source)) {
            return null;
        }

        // store() writes a copy into the uploads disk; work on a temp copy of the asset.
        $tmp = tempnam(sys_get_temp_dir(), 'hero').'.jpg';
        copy($source, $tmp);

        try {
            return $media->store($tmp, 'hero-dark-background.jpg', 'home', [
                'alt' => ['ar' => 'خلفية داكنة مجردة', 'en' => 'Dark abstract background'],
            ])->id;
        } finally {
            @unlink($tmp);
        }
    }

    /** True when every sampled pixel is fully transparent. */
    private function isBlankImage(string $path): bool
    {
        if (! is_file($path) || ! ($gd = @imagecreatefromstring((string) file_get_contents($path)))) {
            return false;
        }

        $w = imagesx($gd);
        $h = imagesy($gd);
        $step = max(1, (int) floor(min($w, $h) / 200));

        for ($y = 0; $y < $h; $y += $step) {
            for ($x = 0; $x < $w; $x += $step) {
                if (((imagecolorat($gd, $x, $y) >> 24) & 0x7F) < 120) {
                    return false;
                }
            }
        }

        return true;
    }

    private function uniqueSlug(string $name, int $id): string
    {
        $slug = Str::slug($name) ?: 'project';
        $candidate = $slug;
        $i = 2;

        while (Project::where('slug', $candidate)->where('id', '!=', $id)->exists()) {
            $candidate = $slug.'-'.$i++;
        }

        return $candidate;
    }
}
