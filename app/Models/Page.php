<?php

namespace App\Models;

use App\Models\Concerns\HasBlocks;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Page extends Model
{
    use HasBlocks, HasTranslations;

    public const HOME = 'home';
    public const ABOUT = 'about';

    protected $fillable = ['slug', 'title', 'blocks', 'seo', 'is_system', 'in_menu', 'status', 'sort_order'];

    public array $translatable = ['title'];

    protected $casts = [
        'blocks' => 'array',
        'seo' => 'array',
        'is_system' => 'boolean',
        'in_menu' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public static function findSlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }

    public function url(?string $locale = null): string
    {
        return match ($this->slug) {
            self::HOME => lroute('home', [], $locale),
            self::ABOUT => lroute('about', [], $locale),
            default => lroute('page', $this->slug, $locale),
        };
    }
}
