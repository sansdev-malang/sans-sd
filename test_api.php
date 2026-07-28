<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$response = Illuminate\Support\Facades\Http::get('http://sans-hrd.test/api/attendance-matrix', ['month' => '2026-07', 'unit_id' => 'sd']);
echo json_encode(array_slice($response->json()['data'] ?? [], 0, 1), JSON_PRETTY_PRINT);
