<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Attendance;

$employeeId = 18;
$start = '2026-08-26';
$end = '2026-08-31';

$attendances = Attendance::where('employee_id', $employeeId)
    ->whereBetween('date', [$start, $end])
    ->get();

echo "Local Attendances found for Employee $employeeId from $start to $end:\n";
print_r($attendances->toArray());
