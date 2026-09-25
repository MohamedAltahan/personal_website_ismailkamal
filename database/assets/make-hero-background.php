<?php
/**
 * Generates a dark abstract hero background (2560×1440 JPEG):
 * near-black base, soft brand-yellow and indigo light glows, diagonal light sweep,
 * vignette and fine film grain. Usage: php make-bg.php <output.jpg>
 */
$out = $argv[1] ?? 'hero-dark.jpg';
[$W, $H] = [2560, 1440];
[$w, $h] = [640, 360]; // light field is computed small and upscaled (it is smooth anyway)

// Glow sources: [x, y, radius, r, g, b, strength] in normalised coordinates.
$glows = [
    [0.72, 1.02, 0.42, 255, 170, 0, 0.42],   // golden glow rising from the bottom
    [0.30, 1.05, 0.30, 255, 120, 30, 0.14],  // warm ember, bottom-left
    [0.10, 0.05, 0.55, 45, 55, 170, 0.45],   // deep indigo, top-left
    [0.92, 0.10, 0.40, 70, 40, 150, 0.30],   // violet, top-right
];

$small = imagecreatetruecolor($w, $h);
for ($y = 0; $y < $h; $y++) {
    for ($x = 0; $x < $w; $x++) {
        $nx = $x / $w;
        $ny = $y / $h;
        // Base: near black with a slight cool tint towards the top.
        $r = 8 + 4 * (1 - $ny);
        $g = 8 + 4 * (1 - $ny);
        $b = 12 + 8 * (1 - $ny);

        foreach ($glows as [$gx, $gy, $gr, $cr, $cg, $cb, $s]) {
            $dx = ($nx - $gx) * 1.78; // aspect correction
            $dy = $ny - $gy;
            $d = sqrt($dx * $dx + $dy * $dy) / $gr;
            $f = $s * exp(-$d * $d * 2.2);
            $r += $cr * $f;
            $g += $cg * $f;
            $b += $cb * $f;
        }

        // Soft diagonal light sweep.
        $band = exp(-pow(($nx * 0.9 + $ny * 0.5 - 0.95) / 0.07, 2)) * 0.06;
        $r += 255 * $band;
        $g += 230 * $band;
        $b += 180 * $band;

        // Vignette.
        $v = 1 - 0.55 * pow(sqrt(pow(($nx - 0.5) * 1.3, 2) + pow(($ny - 0.5) * 1.1, 2)), 2.2);
        $v = max(0.25, $v);

        imagesetpixel($small, $x, $y, imagecolorallocate($small, min(255, (int) ($r * $v)), min(255, (int) ($g * $v)), min(255, (int) ($b * $v))));
    }
}

$img = imagecreatetruecolor($W, $H);
imagecopyresampled($img, $small, 0, 0, 0, 0, $W, $H, $w, $h);
imagefilter($img, IMG_FILTER_GAUSSIAN_BLUR);
imagefilter($img, IMG_FILTER_GAUSSIAN_BLUR);

// Fine grain (also removes banding in the dark gradients).
mt_srand(7);
for ($y = 0; $y < $H; $y++) {
    for ($x = 0; $x < $W; $x += 1) {
        $c = imagecolorat($img, $x, $y);
        $n = mt_rand(-7, 7);
        $r = max(0, min(255, (($c >> 16) & 255) + $n));
        $g = max(0, min(255, (($c >> 8) & 255) + $n));
        $b = max(0, min(255, ($c & 255) + $n));
        imagesetpixel($img, $x, $y, ($r << 16) | ($g << 8) | $b);
    }
}

imagejpeg($img, $out, 92);
echo "written $out (" . round(filesize($out) / 1024) . " KB)\n";
