<?php

namespace App\Models;

use App\Models\Concerns\HasBlocks;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

/** A portfolio project (legacy table "designs"). */
class Project extends Model
{
    use HasBlocks, HasTranslations;

    protected $table = 'designs';

    protected $fillable = [
        'slug', 'title', 'excerpt', 'blocks', 'category_id', 'sub_category_id', 'status',
        'cover_media_id', 'hover_media_id', 'client', 'year', 'tags', 'is_featured', 'sort_order', 'published_at', 'seo',
    ];

    public array $translatable = ['title', 'excerpt'];

    protected $casts = [
        'blocks' => 'array',
        'tags' => 'array',
        'seo' => 'array',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function cover(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_media_id');
    }

    public function hover(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'hover_media_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'active')
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('category_id')->orWhereHas('category', fn ($c) => $c->active()));
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('id');
    }

    public function isPublished(): bool
    {
        return $this->status === 'active' && (! $this->published_at || $this->published_at->isPast());
    }

    public function url(?string $locale = null): string
    {
        return lroute('work.show', $this->slug, $locale);
    }
}
