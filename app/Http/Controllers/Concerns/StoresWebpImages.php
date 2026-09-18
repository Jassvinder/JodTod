<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait StoresWebpImages
{
    /**
     * Convert an uploaded image to WebP (80% quality) and store it on the
     * public disk, returning the stored path.
     */
    private function storeImageAsWebp(UploadedFile $file, string $directory = 'expenses'): string
    {
        $image = imagecreatefromstring(file_get_contents($file->getRealPath()));
        if ($image === false) {
            throw new \RuntimeException('Unable to process uploaded image.');
        }

        $filename = $directory . '/' . uniqid() . '.webp';
        ob_start();
        imagewebp($image, null, 80);
        $webpData = ob_get_clean();

        Storage::disk('public')->put($filename, $webpData);

        return $filename;
    }
}
