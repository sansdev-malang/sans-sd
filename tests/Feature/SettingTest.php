<?php

use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('guest cannot access settings page', function () {
    $response = $this->get('/settings');
    $response->assertRedirect('/login');
});

test('settings page is displayed', function () {
    $user = User::factory()->create(['role' => 'admin_sd']);

    $response = $this
        ->actingAs($user)
        ->get('/settings');

    $response->assertOk();
    $response->assertSee('Pengaturan Sistem');
});

test('settings can be updated', function () {
    $user = User::factory()->create(['role' => 'admin_sd']);
    Storage::fake('public');

    $logo = UploadedFile::fake()->image('logo.png');
    $favicon = UploadedFile::fake()->image('favicon.ico');

    $response = $this
        ->actingAs($user)
        ->post('/settings', [
            'app_name' => 'Sans App Baru',
            'unit_name' => 'Sans Unit Baru',
            'app_copyright' => 'Copyright 2026',
            'app_email' => 'new@sans.dev',
            'app_phone' => '0812345678',
            'app_address' => 'Malang Baru',
            'app_logo' => $logo,
            'app_favicon' => $favicon,
        ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();

    $this->assertEquals('Sans App Baru', setting('app_name'));
    $this->assertEquals('Sans Unit Baru', setting('unit_name'));
    $this->assertEquals('Copyright 2026', setting('app_copyright'));
    $this->assertEquals('new@sans.dev', setting('app_email'));
    $this->assertEquals('0812345678', setting('app_phone'));
    $this->assertEquals('Malang Baru', setting('app_address'));

    // Check files are uploaded
    $logoPath = Setting::get('app_logo');
    $faviconPath = Setting::get('app_favicon');

    Storage::disk('public')->assertExists($logoPath);
    Storage::disk('public')->assertExists($faviconPath);
});
