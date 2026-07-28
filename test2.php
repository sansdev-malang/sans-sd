<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$reports = collect([ ['employee' => ['id' => 80]], ['employee' => ['id' => 85]], ['employee' => ['id' => 90]] ]);
$user_id = 85;
$reports = $reports->filter(function ($item) use ($user_id) { 
    return ($item['employee']['id'] ?? 0) == $user_id; 
});
echo json_encode($reports);
