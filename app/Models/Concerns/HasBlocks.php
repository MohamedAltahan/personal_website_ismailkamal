<?php

namespace App\Models\Concerns;

use App\Models\Media;
use App\Support\Blocks\BlockRegistry;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Models whose content is a list of builder blocks stored in a `blocks` JSON column.
 * Keeps the `mediables` pivot in sync so the media library knows what is in use.
 */
trait HasBlocks
{
    public static function bootHasBlocks(): void
    {
        static::saved(function ($model) {
            if ($model->wasChanged('blocks') || $model->wasRecentlyCreated) {
                $model->media()->sync(BlockRegistry::mediaIds($model->blocks ?? []));
            }
        });

        static::deleting(fn ($model) => $model->media()->detach());
    }

    public function media(): MorphToMany
    {
        return $this->morphToMany(Media::class, 'mediable');
    }

    /** Eager-load every media referenced by the blocks, keyed by id. */
    public function blockMedia(): \Illuminate\Support\Collection
    {
        $ids = BlockRegistry::mediaIds($this->blocks ?? []);

        return Media::with('poster')->whereIn('id', $ids)->get()->keyBy('id');
    }
}
