<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$response = Illuminate\Support\Facades\Http::get('http://sans-hrd.test/api/attendance-matrix', ['month' => '2026-07', 'unit_id' => 'sd']);
$data = $response->json()['data'] ?? [];
$maydia = collect($data)->filter(function($i) { return stripos($i['employee']['name'] ?? '', 'Maydia') !== false; })->first();
echo json_encode($maydia['employee'] ?? null, JSON_PRETTY_PRINT);
