<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Employee;
use App\Models\PicketArea;
use App\Models\PicketSchedule;
use App\Models\PicketSwap;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\PicketSwapNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class PicketScheduleController extends Controller
{
    /**
     * Display the main weekly matrix board for teachers.
     */
    public function index(Request $request)
    {
        $todayDayOfWeek = Carbon::now()->dayOfWeek; // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
        $myEmployeeId = auth()->user()->employee_id;
        $schoolUnit = config('app.school_unit', 'sd');

        // Academic Year Resolution
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $activeYear = AcademicYear::where('is_active', true)->first() ?? $academicYears->first();
        $selectedYearId = $request->input('academic_year_id', $activeYear?->id);
        $selectedYear = $academicYears->firstWhere('id', $selectedYearId) ?? $activeYear;

        // Fetch Picket Areas with schedules filtered by academic year date range
        $areasQuery = PicketArea::with(['schedules' => function($q) use ($selectedYear) {
            $q->with('employee');
            if ($selectedYear && $selectedYear->start_date && $selectedYear->end_date) {
                $q->where(function($sq) use ($selectedYear) {
                    $sq->whereNull('start_date')
                       ->orWhere('start_date', '<=', $selectedYear->end_date->format('Y-m-d'));
                })->where(function($sq) use ($selectedYear) {
                    $sq->whereNull('end_date')
                       ->orWhere('end_date', '>=', $selectedYear->start_date->format('Y-m-d'));
                });
            }
        }])->where('is_active', true);

        $areas = $areasQuery->get();

        // Get my picket duty today (if any)
        $myPicketToday = null;
        if ($myEmployeeId && $todayDayOfWeek >= 1 && $todayDayOfWeek <= 6) {
            $myPicketTodayQuery = PicketSchedule::where('employee_id', $myEmployeeId)
                ->where('day_of_week', $todayDayOfWeek)
                ->with('picketArea');

            if ($selectedYear && $selectedYear->start_date && $selectedYear->end_date) {
                $todayDateStr = Carbon::today()->format('Y-m-d');
                $myPicketTodayQuery->where(function($sq) use ($todayDateStr) {
                    $sq->whereNull('start_date')
                       ->orWhere('start_date', '<=', $todayDateStr);
                })->where(function($sq) use ($todayDateStr) {
                    $sq->whereNull('end_date')
                       ->orWhere('end_date', '>=', $todayDateStr);
                });
            }

            $myPicketToday = $myPicketTodayQuery->first();
        }

        // Fetch Swap Requests
        $pendingSwapsForMe = [];
        $mySubmittedSwaps = [];
        if ($myEmployeeId) {
            $pendingSwapsForMe = PicketSwap::where('target_employee_id', $myEmployeeId)
                ->where('status', 'pending')
                ->with(['requester'])
                ->get();

            $mySubmittedSwaps = PicketSwap::where('requester_id', $myEmployeeId)
                ->with(['targetEmployee'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Fetch all employees in current unit for swap options
        $employees = Employee::where('unit', $schoolUnit)
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        return view('admin.picket-schedules.index', compact(
            'areas',
            'myPicketToday',
            'pendingSwapsForMe',
            'mySubmittedSwaps',
            'employees',
            'academicYears',
            'selectedYear'
        ));
    }

    /**
     * Display the admin panel for picket scheduling.
     */
    public function adminDashboard(Request $request)
    {
        $schoolUnit = config('app.school_unit', 'sd');

        // Academic Year Resolution
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $activeYear = AcademicYear::where('is_active', true)->first() ?? $academicYears->first();
        $selectedYearId = $request->input('academic_year_id', $activeYear?->id);
        $selectedYear = $academicYears->firstWhere('id', $selectedYearId) ?? $activeYear;

        $areas = PicketArea::all();
        $employees = Employee::where('unit', $schoolUnit)
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        $schedulesQuery = PicketSchedule::with(['picketArea', 'employee']);
        if ($selectedYear && $selectedYear->start_date && $selectedYear->end_date) {
            $schedulesQuery->where(function($sq) use ($selectedYear) {
                $sq->whereNull('start_date')
                   ->orWhere('start_date', '<=', $selectedYear->end_date->format('Y-m-d'));
            })->where(function($sq) use ($selectedYear) {
                $sq->whereNull('end_date')
                   ->orWhere('end_date', '>=', $selectedYear->start_date->format('Y-m-d'));
            });
        }
        $schedules = $schedulesQuery->get();

        $swaps = PicketSwap::with(['requester', 'targetEmployee'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.picket-schedules.admin', compact(
            'areas',
            'employees',
            'schedules',
            'swaps',
            'academicYears',
            'selectedYear'
        ));
    }

    /**
     * Store a new picket assignment.
     */
    public function storeAssignment(Request $request)
    {
        $validated = $request->validate([
            'picket_area_id' => 'required|exists:picket_areas,id',
            'day_of_week' => 'required|integer|between:1,6',
            'employee_id' => 'required|exists:employees,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
        ]);

        $year = null;
        if (!empty($validated['academic_year_id'])) {
            $year = AcademicYear::find($validated['academic_year_id']);
        } else {
            $year = AcademicYear::where('is_active', true)->first();
        }

        $startDate = $year?->start_date ? $year->start_date->format('Y-m-d') : '2026-07-01';
        $endDate = $year?->end_date ? $year->end_date->format('Y-m-d') : '2027-06-30';

        $schedule = PicketSchedule::updateOrCreate(
            [
                'picket_area_id' => $validated['picket_area_id'],
                'day_of_week' => $validated['day_of_week'],
                'employee_id' => $validated['employee_id'],
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            [
                'picket_area_id' => $validated['picket_area_id'],
                'day_of_week' => $validated['day_of_week'],
                'employee_id' => $validated['employee_id'],
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]
        );

        $schedule->load(['employee', 'picketArea']);

        // Invalidate employee picket cache & dashboard caches
        Cache::forget('user_has_picket_' . $validated['employee_id']);
        $this->invalidateUnitCaches();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Jadwal piket berhasil ditambahkan/diperbarui.',
                'schedule' => [
                    'id' => $schedule->id,
                    'picket_area_id' => $schedule->picket_area_id,
                    'picket_area_name' => $schedule->picketArea->name,
                    'day_of_week' => $schedule->day_of_week,
                    'employee_id' => $schedule->employee_id,
                    'employee_name' => $schedule->employee->name,
                    'employee_position' => $schedule->employee->position ?? $schedule->employee->employeeType->name ?? '-'
                ]
            ]);
        }

        return back()->with('success', 'Jadwal piket berhasil ditambahkan/diperbarui.');
    }

    /**
     * Delete a picket assignment.
     */
    public function destroyAssignment(Request $request, $id)
    {
        $schedule = PicketSchedule::findOrFail($id);
        $empId = $schedule->employee_id;
        $schedule->delete();

        // Invalidate employee picket cache & dashboard caches
        Cache::forget('user_has_picket_' . $empId);
        $this->invalidateUnitCaches();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Penugasan piket berhasil dihapus.'
            ]);
        }

        return back()->with('success', 'Penugasan piket berhasil dihapus.');
    }

    /**
     * Clone all picket schedules from previous academic year to the new academic year.
     */
    public function clonePreviousYearSchedules(Request $request)
    {
        $validated = $request->validate([
            'source_academic_year_id' => 'required|exists:academic_years,id',
            'target_academic_year_id' => 'required|exists:academic_years,id|different:source_academic_year_id',
        ]);

        $sourceYear = AcademicYear::findOrFail($validated['source_academic_year_id']);
        $targetYear = AcademicYear::findOrFail($validated['target_academic_year_id']);

        $sourceSchedules = PicketSchedule::where(function($q) use ($sourceYear) {
            $q->where(function($sq) use ($sourceYear) {
                $sq->whereNull('start_date')
                   ->orWhere('start_date', '<=', $sourceYear->end_date->format('Y-m-d'));
            })->where(function($sq) use ($sourceYear) {
                $sq->whereNull('end_date')
                   ->orWhere('end_date', '>=', $sourceYear->start_date->format('Y-m-d'));
            });
        })->get();

        if ($sourceSchedules->isEmpty()) {
            return back()->with('error', "Tidak ditemukan jadwal piket pada Tahun Ajaran {$sourceYear->name} untuk disalin.");
        }

        $targetStartDate = $targetYear->start_date->format('Y-m-d');
        $targetEndDate = $targetYear->end_date->format('Y-m-d');
        $clonedCount = 0;

        foreach ($sourceSchedules as $src) {
            PicketSchedule::updateOrCreate(
                [
                    'picket_area_id' => $src->picket_area_id,
                    'day_of_week' => $src->day_of_week,
                    'employee_id' => $src->employee_id,
                    'start_date' => $targetStartDate,
                    'end_date' => $targetEndDate,
                ],
                [
                    'picket_area_id' => $src->picket_area_id,
                    'day_of_week' => $src->day_of_week,
                    'employee_id' => $src->employee_id,
                    'start_date' => $targetStartDate,
                    'end_date' => $targetEndDate,
                ]
            );

            Cache::forget('user_has_picket_' . $src->employee_id);
            $clonedCount++;
        }

        $this->invalidateUnitCaches();

        return back()->with('success', "Berhasil menyalin {$clonedCount} penugasan piket dari {$sourceYear->name} ke {$targetYear->name}.");
    }

    /**
     * Store a new picket area.
     */
    public function storeArea(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'jobs' => 'nullable|string',
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
            'duty_hours' => 'nullable|string|max:100',
        ]);

        if (empty($validated['duty_hours'])) {
            $validated['duty_hours'] = (!empty($validated['start_time']) && !empty($validated['end_time']))
                ? $validated['start_time'] . ' - ' . $validated['end_time']
                : '06.30 - 07.00';
        }

        PicketArea::create($validated);
        $this->invalidateUnitCaches();

        return back()->with('success', 'Area piket baru berhasil ditambahkan.');
    }

    /**
     * Update a picket area.
     */
    public function updateArea(Request $request, $id)
    {
        $area = PicketArea::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'jobs' => 'nullable|string',
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
            'duty_hours' => 'nullable|string|max:100',
            'is_active' => 'required|boolean',
        ]);

        if (empty($validated['duty_hours'])) {
            $validated['duty_hours'] = (!empty($validated['start_time']) && !empty($validated['end_time']))
                ? $validated['start_time'] . ' - ' . $validated['end_time']
                : ($area->duty_hours ?? '06.30 - 07.00');
        }

        $area->update($validated);
        $this->invalidateUnitCaches();

        return back()->with('success', 'Data area piket berhasil diperbarui.');
    }

    /**
     * Delete a picket area.
     */
    public function destroyArea($id)
    {
        $area = PicketArea::findOrFail($id);
        $area->delete();
        $this->invalidateUnitCaches();

        return back()->with('success', 'Area piket berhasil dihapus.');
    }

    /**
     * Submit a new swap request.
     */
    public function requestSwap(Request $request)
    {
        $myEmployeeId = auth()->user()->employee_id;
        if (!$myEmployeeId) {
            return back()->with('error', 'Akun Anda tidak terikat dengan data pegawai.');
        }

        $validated = $request->validate([
            'requested_date' => 'required|date|after_or_equal:today',
            'target_employee_id' => [
                'required',
                'exists:employees,id',
                function ($attribute, $value, $fail) use ($myEmployeeId) {
                    if ($value == $myEmployeeId) {
                        $fail('Guru target tidak boleh sama dengan diri Anda sendiri.');
                    }
                }
            ],
            'target_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string|max:500',
        ]);

        // Prevent duplicate pending swap requests on the same requested_date
        $hasPendingDuplicate = PicketSwap::where('requester_id', $myEmployeeId)
            ->whereDate('requested_date', $validated['requested_date'])
            ->whereIn('status', ['pending', 'approved_by_target'])
            ->exists();

        if ($hasPendingDuplicate) {
            return back()->with('error', 'Anda sudah memiliki permohonan tukar piket yang sedang berjalan pada tanggal ' . Carbon::parse($validated['requested_date'])->translatedFormat('d M Y') . '.');
        }

        // Validate requester picket schedule day of week matches requested_date
        $reqDayOfWeek = Carbon::parse($validated['requested_date'])->dayOfWeek;
        if ($reqDayOfWeek === 0) {
            return back()->with('error', 'Hari Minggu tidak ada jadwal piket.');
        }
        $hasRequesterPicket = PicketSchedule::where('employee_id', $myEmployeeId)
            ->where('day_of_week', $reqDayOfWeek)
            ->exists();

        if (!$hasRequesterPicket) {
            return back()->with('error', 'Anda tidak memiliki jadwal piket pada hari ' . Carbon::parse($validated['requested_date'])->translatedFormat('l') . '.');
        }

        // Validate target picket schedule day of week matches target_date
        $targetDayOfWeek = Carbon::parse($validated['target_date'])->dayOfWeek;
        if ($targetDayOfWeek === 0) {
            return back()->with('error', 'Hari Minggu tidak ada jadwal piket.');
        }
        $hasTargetPicket = PicketSchedule::where('employee_id', $validated['target_employee_id'])
            ->where('day_of_week', $targetDayOfWeek)
            ->exists();

        if (!$hasTargetPicket) {
            return back()->with('error', 'Guru target tidak memiliki jadwal piket pada hari ' . Carbon::parse($validated['target_date'])->translatedFormat('l') . '.');
        }

        $swap = PicketSwap::create([
            'requester_id' => $myEmployeeId,
            'requested_date' => $validated['requested_date'],
            'target_employee_id' => $validated['target_employee_id'],
            'target_date' => $validated['target_date'],
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        // Send Notification to Target Teacher
        try {
            $targetUser = User::where('employee_id', $validated['target_employee_id'])->first();
            if ($targetUser) {
                $targetUser->notify(new PicketSwapNotification($swap, 'requested'));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Failed sending swap notification to target teacher: " . $e->getMessage());
        }

        return back()->with('success', 'Permohonan tukar jadwal piket berhasil diajukan.');
    }

    /**
     * Approve swap request as target teacher.
     */
    public function approveSwapTarget($id)
    {
        $myEmployeeId = auth()->user()->employee_id;
        $swap = PicketSwap::where('target_employee_id', $myEmployeeId)
            ->where('status', 'pending')
            ->findOrFail($id);

        $swap->update(['status' => 'approved_by_target']);

        // Send Notification to Admins / Waka / Kepsek
        try {
            $admins = User::whereIn('role', ['super_admin', 'admin_sd', 'admin_smp', 'admin_paud', 'kepala_sekolah', 'waka'])->get();
            Notification::send($admins, new PicketSwapNotification($swap, 'approved_by_target'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Failed sending swap notification to admins: " . $e->getMessage());
        }

        return back()->with('success', 'Persetujuan Anda telah disimpan. Menunggu verifikasi akhir dari Kepala Sekolah/Waka.');
    }

    /**
     * Final approve swap request as Admin/Waka.
     */
    public function approveSwapAdmin(Request $request, $id)
    {
        $swap = PicketSwap::findOrFail($id);

        if ($swap->status !== 'approved_by_target') {
            return back()->with('error', 'Permohonan ini harus disetujui terlebih dahulu oleh guru target.');
        }

        // Record official swap approval without permanently mutating master routine
        $swap->update([
            'status' => 'approved',
            'approved_by_id' => auth()->id(),
        ]);

        $this->invalidateUnitCaches();

        // Send Notification to both Requester and Target
        try {
            $users = User::whereIn('employee_id', [$swap->requester_id, $swap->target_employee_id])->get();
            Notification::send($users, new PicketSwapNotification($swap, 'approved'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Failed sending swap approval notification: " . $e->getMessage());
        }

        return back()->with('success', 'Tukar jadwal piket berhasil disetujui secara resmi. Penyesuaian jam kedatangan 06.30 WIB akan otomatis berlaku untuk tanggal bersangkutan.');
    }

    /**
     * Reject or Cancel swap request.
     */
    public function rejectSwap($id)
    {
        $myEmployeeId = auth()->user()->employee_id;
        $userRole = auth()->user()->role ?? '';
        $isAdmin = in_array($userRole, ['super_admin', 'admin_sd', 'admin_smp', 'admin_paud', 'kepala_sekolah', 'waka']);

        $swap = PicketSwap::findOrFail($id);

        // Verify authorization (requester, target, or admin)
        if ($swap->target_employee_id != $myEmployeeId && $swap->requester_id != $myEmployeeId && !$isAdmin) {
            abort(403);
        }

        $swap->update(['status' => 'rejected']);
        $this->invalidateUnitCaches();

        // Send Notification
        try {
            $users = User::whereIn('employee_id', [$swap->requester_id, $swap->target_employee_id])
                ->where('id', '!=', auth()->id())
                ->get();
            Notification::send($users, new PicketSwapNotification($swap, 'rejected'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Failed sending swap rejection notification: " . $e->getMessage());
        }

        return back()->with('success', 'Permohonan tukar piket berhasil ditolak/dibatalkan.');
    }

    /**
     * Download the weekly picket schedule matrix as PDF.
     */
    public function downloadPdf(Request $request)
    {
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $activeYear = AcademicYear::where('is_active', true)->first() ?? $academicYears->first();
        $selectedYearId = $request->input('academic_year_id', $activeYear?->id);
        $selectedYear = $academicYears->firstWhere('id', $selectedYearId) ?? $activeYear;

        $areas = PicketArea::with(['schedules' => function($q) use ($selectedYear) {
            $q->with('employee');
            if ($selectedYear && $selectedYear->start_date && $selectedYear->end_date) {
                $q->where(function($sq) use ($selectedYear) {
                    $sq->whereNull('start_date')
                       ->orWhere('start_date', '<=', $selectedYear->end_date->format('Y-m-d'));
                })->where(function($sq) use ($selectedYear) {
                    $sq->whereNull('end_date')
                       ->orWhere('end_date', '>=', $selectedYear->start_date->format('Y-m-d'));
                });
            }
        }])->where('is_active', true)->get();

        $days = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday'];
        $unit = config('app.school_unit') ?: 'sd';
        $unitUpper = strtoupper($unit);

        // Fetch Principal Name
        $principal = Employee::where(function($q) {
            $q->where('position', 'like', '%Kepala Sekolah%')
              ->orWhere('position', 'like', '%Kepsek%');
        })->where('unit', $unit)->first();

        $principalName = $principal ? $principal->name : Setting::get('school_principal_name', 'Kepala Sekolah');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.picket-schedules.export-pdf', compact(
            'areas',
            'days',
            'selectedYear',
            'principalName',
            'unitUpper'
        ));

        return $pdf->setPaper('a4', 'landscape')->download("Jadwal_Piket_Guru_{$unitUpper}.pdf");
    }

    /**
     * Invalidate dashboard caches on picket changes.
     */
    protected function invalidateUnitCaches()
    {
        $schoolUnitId = config('app.school_unit_id', 2);
        Cache::forget('dashboard_master_counts_' . $schoolUnitId);
        Cache::forget('hrd_matrix_unit_' . $schoolUnitId . '_' . date('Y-m-d'));
    }
}
