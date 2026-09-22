<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Models\Employee;

class PayslipController extends Controller
{
    public function index(Request $request)
    {
        $lastMonth = Carbon::now()->subMonth()->format('Y-m');
        $month = $request->input('month', $lastMonth);
        $schoolUnit = config('app.school_unit', 'sd');

        $hrdUrl = \App\Models\Setting::get('hrd_api_url', config('app.hrd_url', 'http://sans-hrd.test'));

        $payslips = [];
        $globalNote = '';
        $periodNote = '';

        try {
            $response = Http::timeout(10)->withHeaders([
                'X-API-TOKEN' => config('app.hrd_api_token')
            ])->get(rtrim($hrdUrl, '/') . '/api/payslips', [
                'school_unit_id' => config('app.school_unit_id', 2),
                'month' => $month
            ]);

            if ($response->successful()) {
                $payslips = $response->json('data') ?? [];
                $globalNote = $response->json('global_note') ?? '';
                $periodNote = $response->json('period_note') ?? '';
            }
        } catch (\Exception $e) {
            // Ignore for now
        }

        // Fetch local employees based on role
        $user = auth()->user();
        if ($user->hasRole('super_admin')) {
            $employees = Employee::with('employeeType')->get();
        } else {
            $employees = collect();
            if ($user->employee_id) {
                $employee = Employee::with('employeeType')->find($user->employee_id);
                if ($employee) {
                    $employees->push($employee);
                }
            }
        }

        $employees = $employees->map(function($emp) use ($payslips) {
            $emp->payslip_url = $payslips[$emp->id]['file_url'] ?? null;
            $emp->original_filename = $payslips[$emp->id]['original_filename'] ?? null;
            $emp->attachment_url = $payslips[$emp->id]['attachment_url'] ?? null;
            $emp->original_attachment_name = $payslips[$emp->id]['original_attachment_name'] ?? null;
            return $emp;
        })->sortBy('name');

        return view('admin.payslips.index', compact('employees', 'month', 'globalNote', 'periodNote'));
    }
}
