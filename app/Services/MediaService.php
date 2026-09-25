<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Laravel\Facades\Image;

/**
 * Stores uploads in the media library and renders responsive image variants
 * according to Settings → Media (quality, format, widths, watermark…).
 */
class MediaService
{
    public const IMAGE_MIMES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/avif', 'image/svg+xml'];

    public const VIDEO_MIMES = ['video/mp4', 'video/webm', 'video/quicktime', 'video/x-m4v', 'video/ogg'];

    private function disk(): \Illuminate\Contracts\Filesystem\Filesystem
    {
        return Storage::disk(config('site.media_disk'));
    }

    /** Store an uploaded file (or an assembled chunked upload). */
    public function store(UploadedFile|string $file, ?string $originalName = null, ?string $folder = null, array $attributes = []): Media
    {
        $path = $file instanceof UploadedFile ? $file->getRealPath() : $file;
        $name = $originalName ?? ($file instanceof UploadedFile ? $file->getClientOriginalName() : basename($path));
        $mime = mime_content_type($path) ?: 'application/octet-stream';
        $size = filesize($path);

        $maxBytes = (int) setting('media.max_upload_mb', 512) * 1024 * 1024;
        if ($size > $maxBytes) {
            throw ValidationException::withMessages(['file' => __('The file is larger than :size MB.', ['size' => setting('media.max_upload_mb')])]);
        }

        $type = match (true) {
            in_array($mime, self::IMAGE_MIMES, true) => 'image',
            in_array($mime, self::VIDEO_MIMES, true) => 'video',
            default => throw ValidationException::withMessages(['file' => __('Unsupported file type: :type', ['type' => $mime])]),
        };

        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION)) ?: ($type === 'video' ? 'mp4' : 'jpg');
        $base = 'media/'.now()->format('Y/m').'/'.Str::lower(Str::random(24));
        $stored = $base.'.'.$ext;

        $stream = fopen($path, 'r');
        $this->disk()->writeStream($stored, $stream);
        if (is_resource($stream)) {
            fclose($stream);
        }

        $media = Media::create(array_merge([
            'type' => $type,
            'disk' => config('site.media_disk'),
            'path' => $stored,
            'name' => Str::limit($name, 180, ''),
            'mime' => $mime,
            'size' => $size,
            'folder' => $folder,
        ], $attributes));

        if ($type === 'image') {
            $this->generateVariants($media);
        }

        return $media->fresh();
    }

    /** Register an existing file already on the uploads disk (legacy conversion). */
    public function adopt(string $relativePath, ?string $folder = null, bool $process = true): ?Media
    {
        if ($existing = Media::where('legacy_path', $relativePath)->first()) {
            return $existing;
        }

        $disk = $this->disk();
        if (! $disk->exists($relativePath)) {
            return null;
        }

        $absolute = $disk->path($relativePath);
        $mime = mime_content_type($absolute) ?: 'application/octet-stream';
        $type = str_starts_with($mime, 'video/') ? 'video' : (str_starts_with($mime, 'image/') ? 'image' : null);

        if (! $type) {
            return null;
        }

        $media = Media::create([
            'type' => $type,
            'disk' => config('site.media_disk'),
            'path' => $relativePath,
            'name' => basename($relativePath),
            'mime' => $mime,
            'size' => filesize($absolute),
            'folder' => $folder,
            'legacy_path' => $relativePath,
        ]);

        if ($type === 'image' && $process) {
            $this->generateVariants($media);
        }

        return $media;
    }

    /** Create an embed (YouTube / Vimeo) media from a URL. */
    public function embed(string $url): Media
    {
        [$provider, $id] = $this->parseEmbed($url);

        if (! $provider) {
            throw ValidationException::withMessages(['url' => __('Only YouTube and Vimeo links are supported.')]);
        }

        $thumbnail = $provider === 'youtube' ? "https://i.ytimg.com/vi/{$id}/hqdefault.jpg" : null;

        if ($provider === 'vimeo') {
            try {
                $info = json_decode(@file_get_contents('https://vimeo.com/api/oembed.json?url='.urlencode($url)) ?: '[]', true);
                $thumbnail = $info['thumbnail_url'] ?? null;
            } catch (\Throwable) {
                // Thumbnail is optional.
            }
        }

        return Media::create([
            'type' => 'embed',
            'disk' => config('site.media_disk'),
            'name' => ucfirst($provider).' '.$id,
            'embed_provider' => $provider,
            'embed_url' => $url,
            'embed_id' => $id,
            'width' => 16,
            'height' => 9,
            'meta' => ['thumbnail' => $thumbnail],
        ]);
    }

    /** @return array{0: ?string, 1: ?string} */
    public function parseEmbed(string $url): array
    {
        if (preg_match('~(?:youtube\.com/(?:watch\?v=|embed/|shorts/|live/)|youtu\.be/)([\w-]{6,})~i', $url, $m)) {
            return ['youtube', $m[1]];
        }

        if (preg_match('~vimeo\.com/(?:video/|channels/[\w-]+/|groups/[\w-]+/videos/)?(\d+)(?:/([\w]+))?~i', $url, $m)) {
            return ['vimeo', $m[1].(isset($m[2]) ? ':'.$m[2] : '')];
        }

        return [null, null];
    }

    /**
     * (Re)build the responsive renditions of an image with the current settings.
     * Animated GIFs and SVGs are served as-is.
     */
    public function generateVariants(Media $media): Media
    {
        if (! $media->isImage()) {
            return $media;
        }

        $disk = $this->disk();
        $source = $disk->path($media->path);

        if (! is_file($source)) {
            return $media;
        }

        $this->deleteVariants($media);

        if (in_array($media->mime, ['image/gif', 'image/svg+xml'], true)) {
            [$w, $h] = @getimagesize($source) ?: [null, null];
            $media->update(['width' => $w, 'height' => $h, 'variants' => null, 'meta' => array_merge($media->meta ?? [], ['variants_size' => 0])]);

            return $media;
        }

        $quality = max(1, min(100, (int) setting('media.quality', 82)));
        $format = setting('media.format', 'webp');
        $widths = collect(setting('media.widths', [480, 960, 1600, 2400]))->map(fn ($w) => (int) $w)->filter()->sort()->values();
        $maxDimension = (int) setting('media.max_dimension', 3200);

        // Very large artwork (e.g. 90 MP posters) needs room to decode with GD.
        $this->raiseMemoryLimit();

        $image = Image::read($this->trimmedSource($media, $source));

        $width = $image->width();
        $height = $image->height();

        $extension = match ($format) {
            'avif' => 'avif',
            'jpg' => 'jpg',
            'original' => in_array($media->mime, ['image/png', 'image/webp', 'image/avif'], true) ? explode('/', $media->mime)[1] : 'jpg',
            default => 'webp',
        };

        $watermark = $this->watermark();
        $base = preg_replace('/\.[^.]+$/', '', $media->path);
        $variants = [];
        $bytes = 0;

        // Full rendition, capped at max dimension.
        $renditions = ['full' => min($width, $maxDimension > 0 ? $maxDimension : $width)];
        foreach ($widths->reverse() as $w) {
            if ($w < $width && $w < $renditions['full']) {
                $renditions[(string) $w] = $w;
            }
        }

        // Largest first, shrinking the same image in place: no full-size clones in memory.
        // The watermark is applied once on the full rendition and scales down with it.
        foreach ($renditions as $key => $targetWidth) {
            $image->scaleDown(width: $targetWidth, height: $key === 'full' && $maxDimension > 0 ? $maxDimension : null);

            if ($key === 'full' && $watermark) {
                $this->applyWatermark($image, $watermark);
            }

            $encoded = (string) match ($extension) {
                'avif' => $image->toAvif($quality),
                'jpg', 'jpeg' => $image->toJpeg($quality),
                'png' => $image->toPng(),
                default => $image->toWebp($quality),
            };

            $path = $base.'-'.$key.'.'.$extension;
            $disk->put($path, $encoded);
            $variants[$key] = $path;
            $bytes += strlen($encoded);
            unset($encoded);
        }

        $color = null;
        try {
            $color = $image->resize(1, 1)->pickColor(0, 0)->toHex('#');
        } catch (\Throwable) {
            // Placeholder colour is cosmetic.
        }
        unset($image);

        $media->update([
            'width' => $width,
            'height' => $height,
            'variants' => $variants,
            'color' => $color,
            'meta' => array_merge($media->meta ?? [], [
                'variants_size' => $bytes,
                'processed_with' => compact('quality', 'format'),
            ]),
        ]);

        if (! setting('media.keep_original', true) && ! $media->legacy_path) {
            // Replace the original by the full rendition to save space.
            $disk->delete($media->path);
            $media->update(['path' => $variants['full'], 'size' => $disk->size($variants['full'])]);
        }

        return $media;
    }

    /**
     * Logos / favicons often ship with big transparent margins (e.g. a small mark on a
     * 1080² canvas). For branding images, crop to the visible pixels.
     */
    private function trimmedSource(Media $media, string $source): mixed
    {
        if ($media->folder !== 'branding' || ! function_exists('imagecropauto')) {
            return $source;
        }

        $gd = @imagecreatefromstring((string) file_get_contents($source));
        if (! $gd) {
            return $source;
        }

        imagesavealpha($gd, true);
        $cropped = @imagecropauto($gd, IMG_CROP_TRANSPARENT);

        if (! $cropped || (imagesx($cropped) === imagesx($gd) && imagesy($cropped) === imagesy($gd))) {
            return $source;
        }

        imagesavealpha($cropped, true);

        return $cropped;
    }

    private function raiseMemoryLimit(): void
    {
        $current = ini_get('memory_limit');
        $bytes = $current === '-1' ? PHP_INT_MAX : (int) $current * match (strtoupper(substr((string) $current, -1))) {
            'G' => 1024 ** 3, 'M' => 1024 ** 2, 'K' => 1024, default => 1,
        };

        if ($bytes < 1024 ** 3 * 1.5) {
            @ini_set('memory_limit', '1536M');
        }
    }

    private function watermark(): ?ImageInterface
    {
        if (! setting('media.watermark_enabled') || ! ($id = setting('media.watermark'))) {
            return null;
        }

        $mark = Media::find($id);
        if (! $mark || ! $mark->isImage() || ! is_file($this->disk()->path($mark->path))) {
            return null;
        }

        return Image::read($this->disk()->path($mark->path));
    }

    private function applyWatermark(ImageInterface $image, ImageInterface $mark): void
    {
        $scale = max(5, min(60, (int) setting('media.watermark_scale', 15))) / 100;
        $opacity = max(5, min(100, (int) setting('media.watermark_opacity', 40)));
        $position = setting('media.watermark_position', 'bottom-right');
        $offset = (int) round($image->width() * 0.03);

        $copy = (clone $mark)->scaleDown(width: (int) round($image->width() * $scale));
        $image->place($copy, $position, $position === 'center' ? 0 : $offset, $position === 'center' ? 0 : $offset, $opacity);
    }

    public function deleteVariants(Media $media): void
    {
        $disk = $this->disk();

        foreach ($media->variants ?? [] as $path) {
            if ($path !== $media->path) {
                $disk->delete($path);
            }
        }
    }

    public function delete(Media $media): void
    {
        $this->deleteVariants($media);

        // Legacy originals are still referenced by the old tables until they are dropped.
        if ($media->path && ! $media->isEmbed() && ! $media->legacy_path) {
            $this->disk()->delete($media->path);
        }

        $media->delete();
    }
}
