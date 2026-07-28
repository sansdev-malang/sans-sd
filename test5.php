<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('email', 'maydiaquliatul@sekolahanaksaleh.sch.id')->first();
$response = Illuminate\Support\Facades\Http::get('http://sans-hrd.test/api/attendance-matrix', ['month' => '2026-07', 'unit_id' => 'sd']);
$json = $response->json();
$reports = collect($json['data'] ?? []);

if ($user && $user->role === 'employee' && $user->employee_id) {
    $reports = $reports->filter(function ($item) use ($user) {
        return ($item['employee']['id'] ?? 0) == $user->employee_id;
    });
}
echo "Found: " . $reports->count() . "\n";
