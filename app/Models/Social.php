<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Social extends Model
{
    protected $fillable = ['name', 'link', 'icon', 'status', 'sort_order'];

    /** Platforms with a bundled SVG icon (resources/views/components/social-icon.blade.php). */
    public const PLATFORMS = [
        'behance', 'dribbble', 'instagram', 'facebook', 'x', 'twitter', 'linkedin', 'youtube',
        'vimeo', 'tiktok', 'telegram', 'whatsapp', 'pinterest', 'snapchat', 'threads', 'artstation', 'email', 'website',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')->orderBy('sort_order')->orderBy('id');
    }

    /** Icon key used by <x-social-icon>, derived from the stored name. */
    public function platform(): string
    {
        $name = strtolower((string) $this->name);

        return in_array($name, self::PLATFORMS, true) ? $name : 'website';
    }
}
