<?php

namespace App\Observers;

use App\Models\Employee;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmployeeObserver
{
    /**
     * Set to true to run events after the database transaction commits.
     */
    public $afterCommit = true;

    /**
     * Handle the Employee "saved" event (covers created and updated).
     */
    public function saved(Employee $employee)
    {
        $this->clearCentralCache($employee);
    }

    /**
     * Handle the Employee "deleted" event.
     */
    public function deleted(Employee $employee)
    {
        $this->clearCentralCache($employee);
    }

    /**
     * Handle the Employee "restored" event.
     */
    public function restored(Employee $employee)
    {
        $this->clearCentralCache($employee);
    }

    /**
     * Clear employee list cache on central HRD.
     */
    protected function clearCentralCache(Employee $employee)
    {
        try {
            $hrdUrl = \App\Models\Setting::get('hrd_api_url', config('app.hrd_url', 'http://sans-hrd.test'));
            if (!$hrdUrl) {
                Log::warning('Webhook clear central cache skipped: HRD URL not configured.');
                return;
            }

            $schoolUnitCode = strtolower(config('app.school_unit', 'sd'));
            $schoolUnitId = config('app.school_unit_id') ?? ([
                'paud' => 1,
                'sd' => 2,
                'smp' => 3,
            ][$schoolUnitCode] ?? 2);

            $apiToken = \App\Models\Setting::get('hrd_api_token', config('app.hrd_api_token'));

            $response = Http::timeout(5)->withHeaders([
                'X-API-TOKEN' => $apiToken
            ])->post(rtrim($hrdUrl, '/') . '/api/sync/clear-employee-cache', [
                'school_unit_id' => $schoolUnitId,
            ]);

            if ($response->successful()) {
                Log::info('Successfully cleared central HRD employee cache via webhook.', [
                    'school_unit_id' => $schoolUnitId,
                    'employee_id' => $employee->id,
                ]);
            } else {
                Log::error('Failed to clear central HRD employee cache via webhook.', [
                    'school_unit_id' => $schoolUnitId,
                    'employee_id' => $employee->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception triggered when trying to clear central HRD employee cache.', [
                'school_unit_id' => $schoolUnitId ?? null,
                'employee_id' => $employee->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
