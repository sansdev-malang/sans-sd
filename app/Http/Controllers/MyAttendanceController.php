<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MyAttendanceController extends Controller
{
    /**
     * Display a listing of the logged-in employee's attendances.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // If not mapped to an employee, redirect or abort
        if (!$user->employee_id) {
            abort(403, 'Akun Anda tidak terhubung dengan data pegawai.');
        }

        // Fetch attendances for this specific employee
        $query = Attendance::where('employee_id', $user->employee_id)->with('employee');

        // Optional date range filtering
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
        } else {
            // Default to current month
            $query->whereBetween('date', [
                Carbon::now()->startOfMonth()->toDateString(),
                Carbon::now()->endOfMonth()->toDateString()
            ]);
        }

        $attendances = $query->orderBy('date', 'desc')->get();

        return view('admin.my_attendance', compact('attendances'));
    }
}
