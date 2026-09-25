<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Translatable\HasTranslations;

class Media extends Model
{
    use HasTranslations;

    protected $fillable = [
        'type', 'disk', 'path', 'name', 'mime', 'size', 'width', 'height', 'variants', 'poster_id',
        'embed_provider', 'embed_url', 'embed_id', 'alt', 'caption', 'folder', 'color', 'meta', 'legacy_path',
    ];

    public array $translatable = ['alt', 'caption'];

    protected $casts = [
        'variants' => 'array',
        'meta' => 'array',
    ];

    public function poster(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'poster_id');
    }

    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    public function isEmbed(): bool
    {
        return $this->type === 'embed';
    }

    public function isAnimated(): bool
    {
        return $this->mime === 'image/gif';
    }

    public function scopeImages(Builder $query): Builder
    {
        return $query->where('type', 'image');
    }

    /**
     * Public URL. For images, $size is a target width (the closest generated
     * variant ≥ that width is used) or "full"; null returns the original file.
     */
    public function url(int|string|null $size = 'full'): ?string
    {
        if ($this->isEmbed()) {
            return $this->embed_url;
        }

        $path = $this->path;

        if ($this->isImage() && $size !== null && $variants = $this->variants) {
            $path = $variants['full'] ?? $path;

            if (is_int($size)) {
                $widths = collect($variants)->except('full')->keys()->map(fn ($w) => (int) $w)->sort()->values();
                $pick = $widths->first(fn ($w) => $w >= $size) ?? $widths->last();
                $path = $pick ? $variants[(string) $pick] : $path;
            }
        }

        return $path ? Storage::disk($this->disk)->url($path) : null;
    }

    public function srcset(): ?string
    {
        if (! $this->isImage() || empty($this->variants)) {
            return null;
        }

        $disk = Storage::disk($this->disk);

        return collect($this->variants)->except('full')
            ->map(fn ($path, $width) => $disk->url($path).' '.$width.'w')
            ->when(isset($this->variants['full']) && $this->width, fn ($set) => $set->push($disk->url($this->variants['full']).' '.$this->width.'w'))
            ->implode(', ');
    }

    /** Thumbnail URL for admin grids (poster for videos). */
    public function thumbUrl(): ?string
    {
        return match ($this->type) {
            'image' => $this->url(480),
            'video' => $this->poster?->url(480),
            'embed' => $this->meta['thumbnail'] ?? null,
            default => null,
        };
    }

    public function aspectRatio(): ?float
    {
        return $this->width && $this->height ? $this->width / $this->height : null;
    }

    public function embedSrc(array $options = []): ?string
    {
        if (! $this->isEmbed()) {
            return null;
        }

        $autoplay = ! empty($options['autoplay']);
        $loop = ! empty($options['loop']);
        $muted = $autoplay || ! empty($options['muted']);

        return match ($this->embed_provider) {
            'youtube' => 'https://www.youtube-nocookie.com/embed/'.$this->embed_id.'?'.http_build_query(array_filter([
                'rel' => 0, 'modestbranding' => 1, 'playsinline' => 1,
                'autoplay' => $autoplay ? 1 : null, 'mute' => $muted ? 1 : null,
                'loop' => $loop ? 1 : null, 'playlist' => $loop ? $this->embed_id : null,
                'controls' => ($options['controls'] ?? true) ? null : 0,
            ], fn ($v) => $v !== null)),
            'vimeo' => 'https://player.vimeo.com/video/'.$this->embed_id.'?'.http_build_query(array_filter([
                'dnt' => 1, 'title' => 0, 'byline' => 0, 'portrait' => 0,
                'autoplay' => $autoplay ? 1 : null, 'muted' => $muted ? 1 : null, 'loop' => $loop ? 1 : null,
                'background' => ($options['controls'] ?? true) ? null : 1,
            ], fn ($v) => $v !== null)),
            default => $this->embed_url,
        };
    }

    /** Compact JSON used by the dashboard (media picker, builder, library). */
    public function toPicker(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'name' => $this->name,
            'mime' => $this->mime,
            'url' => $this->isImage() ? $this->url(1600) : $this->url(null),
            'thumb' => $this->thumbUrl(),
            'width' => $this->width,
            'height' => $this->height,
            'size' => $this->humanSize(),
            'provider' => $this->embed_provider,
            'embed' => $this->isEmbed() ? $this->embedSrc() : null,
            'poster' => $this->poster ? ['id' => $this->poster->id, 'url' => $this->poster->url(960)] : null,
            'alt' => $this->getTranslations('alt'),
            'color' => $this->color,
            'duration' => $this->meta['duration'] ?? null,
            'created' => $this->created_at?->toDateString(),
        ];
    }

    /** Number of projects/pages that reference this media. */
    public function usageCount(): int
    {
        return DB::table('mediables')->where('media_id', $this->id)->count()
            + DB::table('designs')->where('cover_media_id', $this->id)->orWhere('hover_media_id', $this->id)->count();
    }

    public function humanSize(): string
    {
        $bytes = (int) $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = $bytes > 0 ? (int) floor(log($bytes, 1024)) : 0;

        return round($bytes / (1024 ** $i), $i > 1 ? 1 : 0).' '.$units[$i];
    }

    /** Total bytes on disk including variants (stored in meta on processing). */
    public function totalSize(): int
    {
        return (int) ($this->meta['variants_size'] ?? 0) + (int) $this->size;
    }
}
