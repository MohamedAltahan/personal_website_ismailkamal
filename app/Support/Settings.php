<?php

namespace App\Support;

use App\Models\Media;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Key/value site settings backed by the `site_settings` table, merged over
 * the defaults in config/site.php and cached forever until the next save.
 */
class Settings
{
    private const CACHE_KEY = 'site_settings';

    private ?array $values = null;

    public function all(): array
    {
        if ($this->values !== null) {
            return $this->values;
        }

        $stored = Cache::rememberForever(self::CACHE_KEY, function () {
            if (! Schema::hasTable('site_settings')) {
                return [];
            }

            return DB::table('site_settings')->pluck('value', 'key')
                ->map(fn ($value) => json_decode($value, true))
                ->all();
        });

        return $this->values = array_merge(config('site.defaults'), $stored);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->all(), $key, $default);
    }

    /** Current-locale string for a translatable setting. */
    public function text(string $key, ?string $locale = null): string
    {
        return tr($this->get($key), $locale);
    }

    public function media(string $key): ?Media
    {
        $id = $this->get($key);

        return $id ? Media::find($id) : null;
    }

    /** @param array<string, mixed> $values keyed by "group.key" */
    public function set(array $values): void
    {
        $now = now();

        foreach ($values as $key => $value) {
            DB::table('site_settings')->updateOrInsert(
                ['key' => $key],
                ['value' => json_encode($value, JSON_UNESCAPED_UNICODE), 'updated_at' => $now, 'created_at' => $now],
            );
        }

        $this->flush();
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
        $this->values = null;
    }
}
