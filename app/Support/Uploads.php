<?php

namespace App\Support;

class Uploads
{
    /** Chunk size the browser uploader should use: safely below PHP's upload/post limits, max 8 MB. */
    public static function chunkSize(): int
    {
        $limit = min(static::toBytes(ini_get('upload_max_filesize')), static::toBytes(ini_get('post_max_size')));

        return (int) max(512 * 1024, min(8 * 1024 * 1024, $limit - 256 * 1024));
    }

    public static function toBytes(string|false $value): int
    {
        $value = trim((string) $value);
        if ($value === '' || $value === '-1' || $value === '0') {
            return PHP_INT_MAX;
        }

        $number = (float) $value;

        return (int) match (strtolower(substr($value, -1))) {
            'g' => $number * 1024 ** 3,
            'm' => $number * 1024 ** 2,
            'k' => $number * 1024,
            default => $number,
        };
    }
}
