<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Display the settings form.
     */
    public function index()
    {
        return view('admin.settings');
    }

    /**
     * Update the system settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'unit_name' => 'required|string|max:255',
            'app_copyright' => 'nullable|string|max:255',
            'app_email' => 'nullable|email|max:255',
            'app_phone' => 'nullable|string|max:50',
            'app_address' => 'nullable|string|max:500',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'app_favicon' => 'nullable|mimes:ico,png,jpg,jpeg,svg|max:1024',
            'hrd_api_url' => 'nullable|url|max:255',
            'hrd_api_token' => 'nullable|string|max:255',
            'spmb_api_url' => 'nullable|url|max:255',
            'spmb_api_token' => 'nullable|string|max:255',
            'spmb_webhook_secret' => 'nullable|string|max:255',
        ]);

        // Save text fields
        $fields = [
            'app_name',
            'unit_name',
            'app_copyright',
            'app_email',
            'app_phone',
            'app_address',
            'hrd_api_url',
            'hrd_api_token',
            'spmb_api_url',
            'spmb_api_token',
            'spmb_webhook_secret',
        ];

        foreach ($fields as $field) {
            Setting::set($field, $request->input($field));
        }

        // Handle logo upload
        if ($request->hasFile('app_logo')) {
            $oldLogo = Setting::get('app_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            $logoPath = $request->file('app_logo')->store('settings', 'public');
            Setting::set('app_logo', $logoPath);
        }

        // Handle favicon upload
        if ($request->hasFile('app_favicon')) {
            $oldFavicon = Setting::get('app_favicon');
            if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                Storage::disk('public')->delete($oldFavicon);
            }
            $faviconPath = $request->file('app_favicon')->store('settings', 'public');
            Setting::set('app_favicon', $faviconPath);
        }

        // Sync PWA icons and manifest.json with current app logo and name
        $this->syncPwaIconsAndManifest();

        return redirect()->back()->with('success', 'Pengaturan sistem berhasil diperbarui!');
    }

    /**
     * Synchronize PWA icons (192x192, 512x512, maskable) and manifest.json with current app settings.
     */
    public function syncPwaIconsAndManifest(): void
    {
        $appName = Setting::get('app_name', config('app.name', 'SANS SD'));
        $logoRelativePath = Setting::get('app_logo');
        $iconDir = public_path('icons');

        if (!is_dir($iconDir)) {
            @mkdir($iconDir, 0755, true);
        }

        if ($logoRelativePath && Storage::disk('public')->exists($logoRelativePath)) {
            $fullPath = Storage::disk('public')->path($logoRelativePath);
            $imgData = @file_get_contents($fullPath);
            $srcImg = $imgData ? @imagecreatefromstring($imgData) : null;

            if ($srcImg) {
                $srcW = imagesx($srcImg);
                $srcH = imagesy($srcImg);
                $sizes = [192, 512];

                foreach ($sizes as $size) {
                    // 1. Standard Icon (Any) - Transparent canvas
                    $destImg = imagecreatetruecolor($size, $size);
                    imagealphablending($destImg, false);
                    imagesavealpha($destImg, true);
                    $transparent = imagecolorallocatealpha($destImg, 255, 255, 255, 127);
                    imagefilledrectangle($destImg, 0, 0, $size, $size, $transparent);

                    $ratio = min($size / $srcW, $size / $srcH);
                    $dstW = (int)round($srcW * $ratio);
                    $dstH = (int)round($srcH * $ratio);
                    $dstX = (int)round(($size - $dstW) / 2);
                    $dstY = (int)round(($size - $dstH) / 2);

                    imagecopyresampled($destImg, $srcImg, $dstX, $dstY, 0, 0, $dstW, $dstH, $srcW, $srcH);
                    imagepng($destImg, "$iconDir/icon-{$size}x{$size}.png", 9);
                    imagedestroy($destImg);

                    // 2. Maskable Icon - 80% safe zone with white background for Android adaptive icons
                    $maskImg = imagecreatetruecolor($size, $size);
                    $white = imagecolorallocate($maskImg, 255, 255, 255);
                    imagefilledrectangle($maskImg, 0, 0, $size, $size, $white);

                    $safeSize = (int)round($size * 0.80);
                    $maskRatio = min($safeSize / $srcW, $safeSize / $srcH);
                    $mDstW = (int)round($srcW * $maskRatio);
                    $mDstH = (int)round($srcH * $maskRatio);
                    $mDstX = (int)round(($size - $mDstW) / 2);
                    $mDstY = (int)round(($size - $mDstH) / 2);

                    imagecopyresampled($maskImg, $srcImg, $mDstX, $mDstY, 0, 0, $mDstW, $mDstH, $srcW, $srcH);
                    imagepng($maskImg, "$iconDir/icon-maskable-{$size}x{$size}.png", 9);
                    imagedestroy($maskImg);
                }

                imagedestroy($srcImg);
            }
        }

        // Update public/manifest.json
        $manifestPath = public_path('manifest.json');
        $version = time();
        $manifest = [
            'name' => $appName,
            'short_name' => $appName,
            'description' => "Sistem Informasi Manajemen Terpadu {$appName}",
            'start_url' => '/',
            'scope' => '/',
            'display' => 'standalone',
            'orientation' => 'portrait-primary',
            'background_color' => '#ffffff',
            'theme_color' => '#4f46e5',
            'categories' => ['education', 'productivity', 'management'],
            'icons' => [
                [
                    'src' => "/icons/icon-192x192.png?v={$version}",
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'any'
                ],
                [
                    'src' => "/icons/icon-maskable-192x192.png?v={$version}",
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'maskable'
                ],
                [
                    'src' => "/icons/icon-512x512.png?v={$version}",
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'any'
                ],
                [
                    'src' => "/icons/icon-maskable-512x512.png?v={$version}",
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'maskable'
                ]
            ]
        ];

        @file_put_contents($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    /**
     * AJAX endpoint to test SPMB connection
     */
    public function testSpmbConnection(\App\Services\SpmbIntegrationService $service)
    {
        $result = $service->testConnection();
        return response()->json($result, $result['success'] ? 200 : 422);
    }
}
