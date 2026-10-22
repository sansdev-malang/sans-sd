<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileOptimizerService
{
    /**
     * Maximum width or height for document / photo scans.
     */
    protected const MAX_DIMENSION = 1600;

    /**
     * WebP / JPEG compression quality (0 - 100).
     */
    protected const QUALITY = 80;

    /**
     * Optimize and store an uploaded file (Image or PDF).
     *
     * @param UploadedFile $file
     * @param string $directory (relative to disk 'public')
     * @param string|null $prefix
     * @return string Public URL path (e.g., /storage/students/documents/abc.webp)
     */
    public function optimizeAndStore(UploadedFile $file, string $directory = 'students/documents', ?string $prefix = null): string
    {
        $mime = strtolower($file->getMimeType());
        $isImage = str_starts_with($mime, 'image/') && !str_contains($mime, 'svg');

        $storageDisk = Storage::disk('public');
        $cleanDir = trim($directory, '/');
        
        // Ensure directory exists
        if (!$storageDisk->exists($cleanDir)) {
            $storageDisk->makeDirectory($cleanDir);
        }

        $baseName = ($prefix ? Str::slug($prefix) . '_' : '') . time() . '_' . Str::random(8);

        if ($isImage && extension_loaded('gd')) {
            $filename = $baseName . '.webp';
            $targetPath = storage_path('app/public/' . $cleanDir . '/' . $filename);

            $this->compressImageToWebp($file->getRealPath(), $targetPath, $mime);

            return '/storage/' . $cleanDir . '/' . $filename;
        }

        // PDF or non-GD fallback: store directly
        $extension = $file->getClientOriginalExtension() ?: ($mime === 'application/pdf' ? 'pdf' : 'bin');
        $filename = $baseName . '.' . strtolower($extension);
        $file->storeAs($cleanDir, $filename, 'public');

        return '/storage/' . $cleanDir . '/' . $filename;
    }

    /**
     * Optimize and store an uploaded file returning relative path to disk 'public' (e.g., students/documents/abc.webp).
     */
    public function optimizeAndStoreRelative(UploadedFile $file, string $directory = 'students/documents', ?string $prefix = null): string
    {
        $url = $this->optimizeAndStore($file, $directory, $prefix);
        return ltrim(str_replace('/storage/', '', $url), '/');
    }

    /**
     * Compress and downscale image using GD, saving as WebP.
     */
    protected function compressImageToWebp(string $sourcePath, string $destPath, string $mime): void
    {
        $raw = file_get_contents($sourcePath);
        if ($raw === false) {
            throw new \RuntimeException("Gagal membaca file gambar sumber.");
        }

        $img = @imagecreatefromstring($raw);
        if (!$img) {
            // Fallback: copy file directly if GD fails to decode
            copy($sourcePath, $destPath);
            return;
        }

        // Handle EXIF orientation if JPEG
        if (function_exists('exif_read_data') && ($mime === 'image/jpeg' || $mime === 'image/jpg')) {
            try {
                $exif = @exif_read_data($sourcePath);
                if (!empty($exif['Orientation'])) {
                    $img = match ($exif['Orientation']) {
                        3 => imagerotate($img, 180, 0),
                        6 => imagerotate($img, -90, 0),
                        8 => imagerotate($img, 90, 0),
                        default => $img,
                    };
                }
            } catch (\Throwable $e) {
                // Ignore EXIF read errors
            }
        }

        $origW = imagesx($img);
        $origH = imagesy($img);

        // Calculate proportional scale
        $scale = 1.0;
        if ($origW > self::MAX_DIMENSION || $origH > self::MAX_DIMENSION) {
            $scale = min(self::MAX_DIMENSION / $origW, self::MAX_DIMENSION / $origH);
        }

        $newW = max(1, (int)round($origW * $scale));
        $newH = max(1, (int)round($origH * $scale));

        $resized = imagecreatetruecolor($newW, $newH);

        // Preserve alpha transparency for PNG/WebP
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
        imagefilledrectangle($resized, 0, 0, $newW, $newH, $transparent);
        imagealphablending($resized, true);

        imagecopyresampled($resized, $img, 0, 0, 0, 0, $newW, $newH, $origW, $origH);

        // Output as WebP
        if (function_exists('imagewebp')) {
            imagewebp($resized, $destPath, self::QUALITY);
        } else {
            imagejpeg($resized, $destPath, self::QUALITY);
        }

        imagedestroy($img);
        imagedestroy($resized);
    }

    /**
     * Delete previous file if stored locally in public disk.
     */
    public function deleteIfStoredLocally(?string $publicUrl): void
    {
        if (empty($publicUrl)) return;

        if (str_starts_with($publicUrl, '/storage/')) {
            $relativePath = substr($publicUrl, strlen('/storage/'));
            if (Storage::disk('public')->exists($relativePath)) {
                Storage::disk('public')->delete($relativePath);
            }
        }
    }
}
