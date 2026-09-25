<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\Project;
use App\Models\User;
use App\Services\MediaService;
use App\Support\Blocks\BlockRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('uploads');
    }

    /** A noisy photo-like JPEG so quality settings make a measurable difference. */
    private function photo(int $width = 2000, int $height = 1300): UploadedFile
    {
        $img = imagecreatetruecolor($width, $height);
        for ($y = 0; $y < $height; $y += 4) {
            for ($x = 0; $x < $width; $x += 4) {
                imagefilledrectangle($img, $x, $y, $x + 3, $y + 3, imagecolorallocate($img, ($x * 7 + $y) % 256, ($y * 3) % 256, random_int(0, 255)));
            }
        }
        $path = tempnam(sys_get_temp_dir(), 'img').'.jpg';
        imagejpeg($img, $path, 95);

        return new UploadedFile($path, 'photo.jpg', 'image/jpeg', null, true);
    }

    public function test_variants_follow_quality_format_and_widths_settings(): void
    {
        $service = app(MediaService::class);

        setting()->set(['media.quality' => 90, 'media.format' => 'webp', 'media.widths' => [480, 960]]);
        $high = $service->store($this->photo());

        setting()->set(['media.quality' => 40]);
        $low = $service->store($this->photo());

        $this->assertSame(['full', '960', '480'], array_map('strval', array_keys($high->variants)));
        $this->assertStringEndsWith('.webp', $high->variants['960']);
        $this->assertSame(2000, $high->width);

        $disk = Storage::disk('uploads');
        $this->assertLessThan($disk->size($high->variants['960']), $disk->size($low->variants['960']));
        $this->assertSame(90, $high->meta['processed_with']['quality']);

        [$w] = getimagesize($disk->path($high->variants['480']));
        $this->assertSame(480, $w);
    }

    public function test_regenerate_rebuilds_with_new_settings(): void
    {
        $media = app(MediaService::class)->store($this->photo());
        setting()->set(['media.format' => 'jpg', 'media.widths' => [600]]);

        $this->artisan('media:regenerate')->assertSuccessful();

        $media->refresh();
        $this->assertSame(['full', '600'], array_map('strval', array_keys($media->variants)));
        $this->assertStringEndsWith('.jpg', $media->variants['600']);
    }

    public function test_chunked_upload_creates_media(): void
    {
        $this->actingAs(User::factory()->create());
        $bytes = file_get_contents($this->photo(800, 600)->getRealPath());
        $half = intdiv(strlen($bytes), 2);

        foreach ([substr($bytes, 0, $half), substr($bytes, $half)] as $i => $part) {
            $tmp = tempnam(sys_get_temp_dir(), 'chunk');
            file_put_contents($tmp, $part);
            $response = $this->post('/admin/media/upload', [
                'upload_id' => 'abc123', 'chunk_index' => $i, 'total_chunks' => 2, 'file_name' => 'split.jpg',
                'chunk' => new UploadedFile($tmp, 'split.jpg', null, null, true),
            ], ['Accept' => 'application/json']);
        }

        $response->assertOk()->assertJsonPath('media.type', 'image')->assertJsonPath('media.width', 800);
        $this->assertSame(1, Media::count());
    }

    public function test_media_in_use_cannot_be_deleted(): void
    {
        $this->actingAs(User::factory()->create());
        $media = app(MediaService::class)->store($this->photo(600, 400));
        Project::create(['slug' => 'x', 'title' => ['en' => 'X'], 'status' => 'active', 'blocks' => [BlockRegistry::make('image', ['media' => $media->id])]]);

        $this->deleteJson("/admin/media/{$media->id}")->assertStatus(422);

        Project::first()->update(['blocks' => []]);
        $this->deleteJson("/admin/media/{$media->id}")->assertOk();
        $this->assertSame(0, Media::count());
    }

    public function test_embed_links_are_parsed(): void
    {
        $service = app(MediaService::class);

        $this->assertSame(['youtube', 'dQw4w9WgXcQ'], $service->parseEmbed('https://youtu.be/dQw4w9WgXcQ'));
        $this->assertSame(['youtube', 'dQw4w9WgXcQ'], $service->parseEmbed('https://www.youtube.com/watch?v=dQw4w9WgXcQ&t=3'));
        $this->assertSame(['vimeo', '76979871'], $service->parseEmbed('https://vimeo.com/76979871'));
        $this->assertSame([null, null], $service->parseEmbed('https://example.com/video'));
    }
}
