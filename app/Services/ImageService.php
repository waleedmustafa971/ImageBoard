<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class ImageService
{
    const THUMBNAIL_MAX_WIDTH = 250;
    const THUMBNAIL_MAX_HEIGHT = 250;

    public function store(UploadedFile $image, string $directory = 'images'): array
    {
        // Generate unique filename: timestamp + random hash
        $timestamp = now()->timestamp;
        $randomHash = bin2hex(random_bytes(8));
        $extension = $image->getClientOriginalExtension();
        $filename = "{$timestamp}_{$randomHash}.{$extension}";

        // Store original image
        $imagePath = $image->storeAs($directory, $filename, 'public');

        // Create and store thumbnail
        $thumbnailPath = $this->createThumbnail($image, $directory, $filename);

        return [
            'image_path' => $imagePath,
            'thumbnail_path' => $thumbnailPath,
        ];
    }

    protected function createThumbnail(UploadedFile $image, string $directory, string $filename): string
    {
        $thumbnailFilename = 'thumb_' . $filename;
        $thumbnailPath = "{$directory}/{$thumbnailFilename}";

        // Create thumbnail using Intervention Image
        $img = Image::read($image->getRealPath());

        // Resize while maintaining aspect ratio (scale down)
        $img->scale(width: self::THUMBNAIL_MAX_WIDTH, height: self::THUMBNAIL_MAX_HEIGHT);

        // Save thumbnail to storage
        Storage::disk('public')->put($thumbnailPath, (string) $img->encode());

        return $thumbnailPath;
    }

    public function delete(?string $imagePath, ?string $thumbnailPath): void
    {
        if ($imagePath && Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }

        if ($thumbnailPath && Storage::disk('public')->exists($thumbnailPath)) {
            Storage::disk('public')->delete($thumbnailPath);
        }
    }
}
