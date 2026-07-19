<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $query = Attendance::where('date', $date)->with('employee');

        $schoolUnit = config('app.school_unit');
        if ($schoolUnit) {
            $query->whereHas('employee', function ($q) use ($schoolUnit) {
                $q->where('unit', $schoolUnit);
            });
        } elseif ($request->filled('unit')) {
            $unit = $request->input('unit');
            $query->whereHas('employee', function ($q) use ($unit) {
                $q->where('unit', $unit);
            });
        }

        if ($request->filled('type')) {
            $type = $request->input('type');
            $query->whereHas('employee.employeeType', function ($q) use ($type) {
                if (is_numeric($type)) {
                    $q->where('id', $type);
                } else {
                    $q->where('code', $type)->orWhere('name', $type);
                }
            });
        }

        $attendances = $query->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'date' => $date,
                'data' => $attendances,
            ]);
        }

        return view('admin.attendances.index', compact('attendances', 'date'));
    }

    /**
     * Fetch attendance recap. SD & SMP records are combined/shared.
     */
    public function recap(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $search = $request->input('search');

        $query = Attendance::where('date', $date)->with('employee');

        $schoolUnit = config('app.school_unit');
        if ($schoolUnit) {
            $query->whereHas('employee', function ($q) use ($schoolUnit) {
                $q->where('unit', $schoolUnit);
            });
        }

        if ($request->filled('search')) {
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        $attendances = $query->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'date' => $date,
                'data' => $attendances,
            ]);
        }

        return view('admin.attendances.recap', compact('attendances', 'date', 'search'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'clock_in' => 'nullable',
            'clock_out' => 'nullable',
            'status' => 'sometimes|nullable|string',
            'notes' => 'nullable|string|max:255',
        ]);

        $calc = $this->calculateAttendance(
            $validated['employee_id'],
            $validated['date'],
            $validated['clock_in'] ?? null,
            $validated['clock_out'] ?? null,
            $validated['status'] ?? null,
            $validated['notes'] ?? null
        );

        $validated['status'] = $calc['status'];
        $validated['calculated_bonus'] = $calc['calculated_bonus'];

        $attendance = Attendance::updateOrCreate(
            [
                'employee_id' => $validated['employee_id'],
                'date' => $validated['date'],
            ],
            $validated
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Attendance logged successfully.',
                'data' => $attendance,
            ], 201);
        }

        return redirect()->back()->with('success', 'Kehadiran berhasil dicatat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        return response()->json([
            'success' => true,
            'data' => $attendance->load('employee'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'clock_in' => 'nullable',
            'clock_out' => 'nullable',
            'status' => 'sometimes|nullable|string',
            'notes' => 'nullable|string|max:255',
        ]);

        $clockIn = array_key_exists('clock_in', $validated) ? $validated['clock_in'] : $attendance->clock_in;
        $clockOut = array_key_exists('clock_out', $validated) ? $validated['clock_out'] : $attendance->clock_out;
        $status = array_key_exists('status', $validated) ? $validated['status'] : null;

        $calc = $this->calculateAttendance(
            $attendance->employee_id,
            $attendance->date,
            $clockIn,
            $clockOut,
            $status,
            $validated['notes'] ?? $attendance->notes
        );

        $validated['status'] = $calc['status'];
        $validated['calculated_bonus'] = $calc['calculated_bonus'];

        $attendance->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Attendance updated successfully.',
                'data' => $attendance,
            ]);
        }

        return redirect()->back()->with('success', 'Kehadiran berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Attendance deleted successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Log kehadiran berhasil dihapus!');
    }

    /**
     * Recalculate attendance for a date.
     */
    public function recalculate(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $attendances = Attendance::where('date', $date)->get();

        foreach ($attendances as $att) {
            $calc = $this->calculateAttendance(
                $att->employee_id,
                $att->date,
                $att->clock_in,
                $att->clock_out,
                in_array($att->status, ['Sick', 'Leave']) ? $att->status : null,
                $att->notes
            );

            $att->status = $calc['status'];
            $att->calculated_bonus = $calc['calculated_bonus'];
            $att->save();
        }

        return redirect()->back()->with('success', 'Rekalkulasi absensi tanggal ' . $date . ' selesai.');
    }

    /**
     * Dynamic calculations logic.
     */
    public function calculateAttendance($employeeId, $date, $clockIn, $clockOut, $manualStatus = null, $notes = null)
    {
        $carbonDate = Carbon::parse($date);
        $dayOfWeek = $carbonDate->dayOfWeek; // 0=Sunday, 1=Monday, ..., 6=Saturday
        $employee = Employee::findOrFail($employeeId);

        // 1. Check Holiday Adjustment or global holiday
        $isHoliday = false;
        
        $adjustment = \App\Models\HolidayAdjustment::where('adjusted_date', $date)->first();

        if ($adjustment) {
            $isHoliday = true;
        } else {
            $holiday = \App\Models\Holiday::where('original_date', $date)->first();
            if ($holiday) {
                $wasRescheduled = \App\Models\HolidayAdjustment::where('holiday_id', $holiday->id)
                    ->where('original_date', $date)
                    ->exists();
                if (!$wasRescheduled) {
                    $isHoliday = true;
                }
            }
        }

        // 2. Find Assigned Shift or Default
        $activeShiftAssigned = \App\Models\EmployeeWorkingShift::where('employee_id', $employeeId)
            ->where('start_date', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', $date);
            })->first();

        $shift = null;
        if ($activeShiftAssigned) {
            $shift = \App\Models\WorkingShift::find($activeShiftAssigned->working_shift_id);
        }

        if (!$shift) {
            $shift = \App\Models\WorkingShift::where('code', 'default')->first();
        }

        $shiftDetail = null;
        if ($shift) {
            $shiftDetail = \App\Models\WorkingShiftDetail::where('working_shift_id', $shift->id)
                ->where('day_of_week', $dayOfWeek)
                ->first();
        }

        $isOffDay = ($shiftDetail && $shiftDetail->is_off) || $isHoliday;

        // 3. Check Approved Leave Requests
        $leave = \App\Models\LeaveRequest::where('employee_id', $employeeId)
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->where('status', 'Approved')
            ->first();

        // 4. Calculate Status and Bonus
        $status = $manualStatus;
        $calculatedBonus = 0.00;

        if ($leave) {
            $status = $leave->type === 'Sakit' ? 'Sick' : 'Leave';
            if ($leave->type === 'Dinas') {
                $activeSchema = \App\Models\BonusSchema::where('is_active', true)->first();
                if ($activeSchema) {
                    $maxTier = \App\Models\BonusTier::where('bonus_schema_id', $activeSchema->id)
                        ->orderBy('nominal', 'desc')
                        ->first();
                    if ($maxTier) {
                        $calculatedBonus = $maxTier->nominal;
                    }
                }
            } else {
                $calculatedBonus = 0.00;
            }
        } elseif ($isOffDay) {
            if (!$clockIn) {
                $status = 'Off';
                $calculatedBonus = 0.00;
            } else {
                $status = 'Present';
                $activeSchema = \App\Models\BonusSchema::where('is_active', true)->first();
                if ($activeSchema) {
                    $maxTier = \App\Models\BonusTier::where('bonus_schema_id', $activeSchema->id)
                        ->orderBy('nominal', 'desc')
                        ->first();
                    if ($maxTier) {
                        $calculatedBonus = $maxTier->nominal;
                    }
                }
            }
        } else {
            // Work day
            if (!$clockIn) {
                if (!$status || $status === 'Present') {
                    $status = 'Absent';
                }
                $calculatedBonus = 0.00;
            } else {
                $status = 'Present';
                $lateMinutes = 0;

                if ($shiftDetail && $shiftDetail->start_time) {
                    $shiftStart = Carbon::parse($date . ' ' . $shiftDetail->start_time);
                    $actualIn = Carbon::parse($date . ' ' . $clockIn);

                    if ($actualIn->gt($shiftStart)) {
                        $lateMinutes = $actualIn->diffInMinutes($shiftStart);
                        $status = 'Late';
                    }
                }

                $activeSchema = \App\Models\BonusSchema::where('is_active', true)->first();
                if ($activeSchema) {
                    $matchingTier = \App\Models\BonusTier::where('bonus_schema_id', $activeSchema->id)
                        ->where('max_late_minutes', '>=', $lateMinutes)
                        ->orderBy('nominal', 'desc')
                        ->first();

                    if ($matchingTier) {
                        $calculatedBonus = $matchingTier->nominal;
                    } else {
                        $calculatedBonus = 0.00;
                    }
                }
            }
        }

        return [
            'status' => $status ?? 'Present',
            'calculated_bonus' => $calculatedBonus,
        ];
    }
}
