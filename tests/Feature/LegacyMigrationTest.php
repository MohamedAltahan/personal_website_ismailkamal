<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\Page;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LegacyMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_designs_become_block_projects_and_rerun_is_idempotent(): void
    {
        Storage::fake('uploads');
        $disk = Storage::disk('uploads');

        $jpeg = function (string $path) use ($disk) {
            $img = imagecreatetruecolor(640, 400);
            ob_start();
            imagejpeg($img);
            $disk->put($path, ob_get_clean());
        };

        $jpeg('thumbnails/cover.jpg');
        $jpeg('images/one.jpg');
        $jpeg('images/two.jpg');
        $jpeg('video_thumbnail/poster.jpg');
        $disk->put('videos/clip.mp4', "\x00\x00\x00\x18ftypmp42\x00\x00\x00\x00mp42isom");

        $now = now();
        DB::table('categories')->insert(['id' => 1, 'name' => json_encode(['ar' => 'Motion', 'en' => 'Motion']), 'slug' => 'motion', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now]);
        DB::table('designs')->insert(['id' => 7, 'name' => 'Breast Cancer', 'thumbnail' => 'thumbnails/cover.jpg', 'category_id' => 1, 'status' => 'active', 'created_at' => '2024-05-04 23:27:04', 'updated_at' => $now]);
        DB::table('images')->insert([
            ['name' => 'images/one.jpg', 'design_id' => 7, 'at_home' => 'no', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'images/two.jpg', 'design_id' => 7, 'at_home' => 'no', 'created_at' => $now, 'updated_at' => $now],
        ]);
        DB::table('videos')->insert(['name' => 'videos/clip.mp4', 'video_thumbnail' => 'video_thumbnail/poster.jpg', 'design_id' => 7, 'at_home' => 'no', 'created_at' => $now, 'updated_at' => $now]);
        DB::table('abouts')->insert(['content' => '<h1 style="color:red">Ismail</h1><p>Motion designer</p>', 'created_at' => $now, 'updated_at' => $now]);
        DB::table('settings')->insert(['site_name' => 'Ismail', 'contact_email' => 'me@example.com', 'contact_phone' => '0100', 'created_at' => $now, 'updated_at' => $now]);

        $this->artisan('portfolio:migrate-legacy')->assertSuccessful();

        $project = Project::find(7);
        $this->assertSame('breast-cancer', $project->slug);
        $this->assertSame('Breast Cancer', $project->getTranslation('title', 'en'));
        $this->assertSame('2024', $project->year);
        $this->assertSame(['video', 'gallery'], array_column($project->blocks, 'type'));
        $this->assertCount(2, $project->blocks[1]['data']['items']);
        $this->assertNotNull($project->cover_media_id);

        $video = Media::find($project->blocks[0]['data']['media']);
        $this->assertSame('video', $video->type);
        $this->assertNotNull($video->poster_id);

        $this->assertNotNull(Page::findSlug('home'));
        $this->assertStringContainsString('Motion designer', Page::findSlug('about')->blocks[1]['data']['html']['en']);
        $this->assertSame('me@example.com', setting('general.contact_email'));

        $mediaCount = Media::count();
        $this->artisan('portfolio:migrate-legacy')->assertSuccessful();
        $this->artisan('portfolio:migrate-legacy', ['--force' => true])->assertSuccessful();
        $this->assertSame($mediaCount, Media::count(), 'Re-running must not duplicate media');

        // Legacy originals are never deleted from disk.
        $this->assertTrue($disk->exists('images/one.jpg'));
    }
}
