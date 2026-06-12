<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ImageSanitizerService
{
    /**
     * Decode and re-encode image to strip metadata and reduce file size.
     */
    public function sanitizeAndOptimize(string $disk, string $path, ?int $maxWidth = null, ?int $maxHeight = null): bool
    {
        if ($path === '' || ! Storage::disk($disk)->exists($path)) {
            return false;
        }

        $absolutePath = Storage::disk($disk)->path($path);
        $raw = @file_get_contents($absolutePath);

        if ($raw === false || $raw === '') {
            return false;
        }

        if (! function_exists('imagecreatefromstring') || ! function_exists('imagecreatetruecolor') || ! function_exists('imagecopyresampled')) {
            return false;
        }

        $source = @imagecreatefromstring($raw);

        if (! $source) {
            return false;
        }

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);

        if ($sourceWidth <= 0 || $sourceHeight <= 0) {
            imagedestroy($source);

            return false;
        }

        [$targetWidth, $targetHeight] = $this->resolveTargetSize($sourceWidth, $sourceHeight, $maxWidth, $maxHeight);

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefill($canvas, 0, 0, $transparent);

        imagecopyresampled(
            $canvas,
            $source,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $sourceWidth,
            $sourceHeight,
        );

        $mimeType = Storage::disk($disk)->mimeType($path);

        if ($mimeType === 'image/png') {
            imagepng($canvas, $absolutePath, 8);
        } elseif ($mimeType === 'image/webp' && function_exists('imagewebp')) {
            imagewebp($canvas, $absolutePath, 82);
        } else {
            imagejpeg($canvas, $absolutePath, 84);
        }

        imagedestroy($canvas);
        imagedestroy($source);

        return true;
    }

    /**
     * @return array{0: int, 1: int}
     */
    private function resolveTargetSize(int $width, int $height, ?int $maxWidth, ?int $maxHeight): array
    {
        $maxWidth = $maxWidth && $maxWidth > 0 ? $maxWidth : $width;
        $maxHeight = $maxHeight && $maxHeight > 0 ? $maxHeight : $height;

        if ($width <= $maxWidth && $height <= $maxHeight) {
            return [$width, $height];
        }

        $scale = min($maxWidth / $width, $maxHeight / $height);

        return [
            max(1, (int) floor($width * $scale)),
            max(1, (int) floor($height * $scale)),
        ];
    }
}
