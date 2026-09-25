<?php

namespace App\Support;

class Color
{
    /** Readable text colour (near-black or white) on top of the given background. */
    public static function contrast(?string $hex, string $dark = '#0b0b0c', string $light = '#ffffff'): string
    {
        [$r, $g, $b] = static::rgb($hex ?? '#000000');

        $lum = fn ($c) => ($c /= 255) <= 0.03928 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4;
        $l = 0.2126 * $lum($r) + 0.7152 * $lum($g) + 0.0722 * $lum($b);

        return $l > 0.36 ? $dark : $light;
    }

    /** @return array{0:int,1:int,2:int} */
    public static function rgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        return [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
    }

    public static function valid(?string $hex): bool
    {
        return (bool) preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', (string) $hex);
    }
}
