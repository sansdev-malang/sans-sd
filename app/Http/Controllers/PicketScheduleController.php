<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\PicketArea;
use App\Models\PicketSchedule;
use App\Models\PicketSwap;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PicketScheduleController extends Controller
{
    /**
     * Display the main weekly matrix board for teachers.
     */
    public function index()
    {
        $todayDayOfWeek = Carbon::now()->dayOfWeek; // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
        $myEmployeeId = auth()->user()->employee_id;

        // Fetch Picket Areas with schedules
        $areas = PicketArea::with(['schedules.employee'])->where('is_active', true)->get();

        // Get my picket duty today (if any)
        $myPicketToday = null;
        if ($myEmployeeId && $todayDayOfWeek >= 1 && $todayDayOfWeek <= 6) {
            $myPicketToday = PicketSchedule::where('employee_id', $myEmployeeId)
                ->where('day_of_week', $todayDayOfWeek)
                ->with('picketArea')
                ->first();
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
        $schoolUnit = config('app.school_unit', 'sd');
        $employees = Employee::where('unit', $schoolUnit)
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        return view('admin.picket-schedules.index', compact(
            'areas',
            'myPicketToday',
            'pendingSwapsForMe',
            'mySubmittedSwaps',
            'employees'
        ));
    }

    /**
     * Display the admin panel for picket scheduling.
     */
    public function adminDashboard()
    {
        $schoolUnit = config('app.school_unit', 'sd');

        $areas = PicketArea::all();
        $employees = Employee::where('unit', $schoolUnit)
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        $schedules = PicketSchedule::with(['picketArea', 'employee'])->get();

        $swaps = PicketSwap::with(['requester', 'targetEmployee'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.picket-schedules.admin', compact('areas', 'employees', 'schedules', 'swaps'));
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
        ]);

        $schedule = PicketSchedule::updateOrCreate(
            [
                'picket_area_id' => $validated['picket_area_id'],
                'day_of_week' => $validated['day_of_week'],
                'employee_id' => $validated['employee_id'],
            ],
            $validated
        );

        $schedule->load(['employee', 'picketArea']);

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
        $schedule->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Penugasan piket berhasil dihapus.'
            ]);
        }

        return back()->with('success', 'Penugasan piket berhasil dihapus.');
    }

    /**
     * Store a new picket area.
     */
    public function storeArea(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'jobs' => 'nullable|string',
            'duty_hours' => 'required|string|max:100',
        ]);

        PicketArea::create($validated);

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
            'duty_hours' => 'required|string|max:100',
            'is_active' => 'required|boolean',
        ]);

        $area->update($validated);

        return back()->with('success', 'Data area piket berhasil diperbarui.');
    }

    /**
     * Delete a picket area.
     */
    public function destroyArea($id)
    {
        $area = PicketArea::findOrFail($id);
        $area->delete();

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

        PicketSwap::create([
            'requester_id' => $myEmployeeId,
            'requested_date' => $validated['requested_date'],
            'target_employee_id' => $validated['target_employee_id'],
            'target_date' => $validated['target_date'],
            'status' => 'pending',
            'notes' => $validated['notes'],
        ]);

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

        // Perform actual schedule swap!
        $reqDayOfWeek = Carbon::parse($swap->requested_date)->dayOfWeek;
        $targetDayOfWeek = Carbon::parse($swap->target_date)->dayOfWeek;

        $reqSchedule = PicketSchedule::where('employee_id', $swap->requester_id)
            ->where('day_of_week', $reqDayOfWeek)
            ->first();

        $targetSchedule = PicketSchedule::where('employee_id', $swap->target_employee_id)
            ->where('day_of_week', $targetDayOfWeek)
            ->first();

        if ($reqSchedule && $targetSchedule) {
            // Swap employee_ids in the schedules
            $tempEmp = $reqSchedule->employee_id;
            $reqSchedule->update(['employee_id' => $targetSchedule->employee_id]);
            $targetSchedule->update(['employee_id' => $tempEmp]);
        }

        $swap->update([
            'status' => 'approved',
            'approved_by_id' => auth()->id(),
        ]);

        return back()->with('success', 'Tukar jadwal piket berhasil disetujui secara resmi. Penugasan jadwal di matriks telah otomatis diperbarui.');
    }

    /**
     * Reject swap request.
     */
    public function rejectSwap($id)
    {
        $myEmployeeId = auth()->user()->employee_id;
        $isAdmin = auth()->user()->hasRole('super_admin') || 
                   auth()->user()->hasRole('admin_sd') || 
                   auth()->user()->hasRole('kepala_sekolah') || 
                   auth()->user()->hasRole('waka');

        $swap = PicketSwap::findOrFail($id);

        // Verify authorization
        if ($swap->target_employee_id != $myEmployeeId && $swap->requester_id != $myEmployeeId && !$isAdmin) {
            abort(403);
        }

        $swap->update(['status' => 'rejected']);

        return back()->with('success', 'Permohonan tukar piket berhasil ditolak/dibatalkan.');
    }

    /**
     * Download the weekly picket schedule matrix as PDF.
     */
    public function downloadPdf()
    {
        $areas = PicketArea::with(['schedules.employee'])->where('is_active', true)->get();
        $days = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday'];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.picket-schedules.export-pdf', compact('areas', 'days'));
        return $pdf->setPaper('a4', 'landscape')->download('Jadwal_Piket_Guru_SD.pdf');
    }
}
