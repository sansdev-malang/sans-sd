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

        return redirect()->back()->with('success', 'Pengaturan sistem berhasil diperbarui!');
    }
}
