<?php

namespace App\Services\Shop;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/** Photos produit : redimensionnées à 1600 px de large au plus et converties en WebP. */
class ProductImages
{
    public function store(UploadedFile $file): string
    {
        $path = 'products/'.now()->format('Y/m').'/'.Str::random(32).'.webp';
        $source = @imagecreatefromstring((string) file_get_contents($file->getRealPath()));

        if (! $source) {
            return $file->store('products/'.now()->format('Y/m'), 'public');
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $max = 1600;

        if ($width > $max) {
            $resized = imagescale($source, $max, (int) round($height * $max / $width), IMG_BICUBIC);
            imagedestroy($source);
            $source = $resized;
        }

        imagepalettetotruecolor($source);
        ob_start();
        imagewebp($source, null, 82);
        $data = (string) ob_get_clean();
        imagedestroy($source);

        Storage::disk('public')->put($path, $data);

        return $path;
    }

    public function delete(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'images/')) {
            Storage::disk('public')->delete($path);
        }
    }
}
