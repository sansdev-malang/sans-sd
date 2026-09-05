<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of leave requests in this unit.
     */
    public function index()
    {
        if (request()->has('clear_all')) {
            if (auth()->user()) {
                auth()->user()->unreadNotifications->markAsRead();
            }
            $schoolUnit = config('app.school_unit');
            $recentLeavesQuery = LeaveRequest::where('created_at', '>=', now()->subDays(3));
            if ($schoolUnit) {
                $recentLeavesQuery->whereHas('employee', function ($q) use ($schoolUnit) {
                    $q->where('unit', $schoolUnit);
                });
            }
            $recentIds = $recentLeavesQuery->pluck('id')->toArray();
            $readIds = \Illuminate\Support\Facades\Cache::get('read_leave_ids_' . auth()->id(), []);
            $newReadIds = array_unique(array_merge($readIds, $recentIds));
            \Illuminate\Support\Facades\Cache::forever('read_leave_ids_' . auth()->id(), $newReadIds);
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true]);
            }
            return redirect()->route('leaves.index');
        }

        if (request()->has('read_id')) {
            $readIds = \Illuminate\Support\Facades\Cache::get('read_leave_ids_' . auth()->id(), []);
            $readIds[] = (int) request('read_id');
            \Illuminate\Support\Facades\Cache::forever('read_leave_ids_' . auth()->id(), array_unique($readIds));
        }

        $schoolUnit = config('app.school_unit');
        
        // Base query for stats
        $statsQuery = LeaveRequest::query();
        if ($schoolUnit) {
            $statsQuery->whereHas('employee', function ($q) use ($schoolUnit) {
                $q->where('unit', $schoolUnit);
            });
        }
        $allLeaves = $statsQuery->get();
        $pendingCount = $allLeaves->where('status', 'Pending')->count();
        $approvedCount = $allLeaves->where('status', 'Approved')->count();
        $rejectedCount = $allLeaves->where('status', 'Rejected')->count();
        $processedCount = $approvedCount + $rejectedCount;
        $approvalRate = $processedCount > 0 ? round(($approvedCount / $processedCount) * 100, 1) : 0;

        // Base query for list
        $query = LeaveRequest::with(['employee', 'leaveType', 'processedBy'])->orderBy('created_at', 'desc');
        if ($schoolUnit) {
            $query->whereHas('employee', function ($q) use ($schoolUnit) {
                $q->where('unit', $schoolUnit);
            });
        }

        // Apply filters
        if (request()->filled('search')) {
            $search = request('search');
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        if (request()->filled('type')) {
            $typeFilter = request('type');
            $query->where(function($q) use ($typeFilter) {
                $q->where('leave_type_id', $typeFilter)
                  ->orWhere('type', $typeFilter);
            });
        }

        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        // Pagination
        $perPage = request('per_page', 50);
        if ($perPage === 'all') {
            $paginatedLeaves = $query->paginate($query->count())->appends(request()->query());
        } else {
            $paginatedLeaves = $query->paginate((int) $perPage)->appends(request()->query());
        }

        $employees = Employee::orderBy('name')->get();
        $leaveTypes = \App\Models\LeaveType::orderBy('name')->get();

        return view('admin.leaves.index', compact(
            'paginatedLeaves',
            'employees',
            'leaveTypes',
            'pendingCount',
            'processedCount',
            'approvalRate'
        ));
    }

    /**
     * Store a newly created leave request in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,png,jpg,jpeg,doc,docx|max:2048',
        ], [
            'employee_id.required' => 'Pegawai harus dipilih.',
            'employee_id.exists' => 'Pegawai yang dipilih tidak valid.',
            'leave_type_id.required' => 'Jenis izin harus dipilih.',
            'leave_type_id.exists' => 'Jenis izin tidak valid.',
            'start_date.required' => 'Tanggal mulai harus diisi.',
            'start_date.date' => 'Format tanggal mulai tidak valid.',
            'end_date.required' => 'Tanggal selesai harus diisi.',
            'end_date.date' => 'Format tanggal selesai tidak valid.',
            'end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'attachment.file' => 'Lampiran harus berupa file.',
            'attachment.mimes' => 'Format file lampiran harus berupa PDF, PNG, JPG, JPEG, DOC, atau DOCX.',
            'attachment.max' => 'Ukuran file lampiran maksimal 2MB.',
        ]);

        // 1. Cut-off payroll lock check
        $cutoffDay = (int) \App\Models\Setting::get('payroll_cutoff_date', 26);
        $today = Carbon::today();
        if ($today->day > $cutoffDay) {
            $minAllowedDate = $today->copy()->day($cutoffDay + 1);
        } else {
            $minAllowedDate = $today->copy()->subMonthNoOverflow()->day($cutoffDay + 1);
        }

        if (Carbon::parse($validated['start_date'])->lt($minAllowedDate)) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'start_date' => "Tanggal izin tidak boleh mendahului periode cut-off penggajian yang sudah ditutup (minimal tanggal " . $minAllowedDate->translatedFormat('d M Y') . ")."
                ]);
        }

        $leaveType = \App\Models\LeaveType::findOrFail($validated['leave_type_id']);
        $validated['type'] = $leaveType->name;

        // 2. Intelligent Conflict / Overlap Check:
        // Full-Day: requires_attendance = false (Sakit, Cuti, Tidak Bekerja, Duka)
        // Partial-Day: requires_attendance = true (Terlambat, Pulang Awal, Keluar Jam Efektif, Kedinasan)
        $isNewFullDay = !$leaveType->requires_attendance;

        $conflictingLeaves = LeaveRequest::with('leaveType')
            ->where('employee_id', $validated['employee_id'])
            ->whereIn('status', ['Pending', 'Approved'])
            ->where(function ($query) use ($validated) {
                $query->where('start_date', '<=', $validated['end_date'])
                      ->where('end_date', '>=', $validated['start_date']);
            })
            ->get();

        foreach ($conflictingLeaves as $conflict) {
            $isExistingFullDay = $conflict->leaveType ? !$conflict->leaveType->requires_attendance : true;
            $conflictStart = Carbon::parse($conflict->start_date)->translatedFormat('d M Y');
            $conflictEnd = Carbon::parse($conflict->end_date)->translatedFormat('d M Y');
            $dateStr = ($conflictStart === $conflictEnd) ? $conflictStart : "{$conflictStart} s.d. {$conflictEnd}";
            $statusLabel = $conflict->status === 'Approved' ? 'Disetujui' : 'Menunggu Persetujuan';

            // Conflict A: Full-Day vs Any Leave
            if ($isNewFullDay || $isExistingFullDay) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'start_date' => "Sudah terdapat data izin {$conflict->type} ({$statusLabel}) pada tanggal {$dateStr}. Izin seharian penuh tidak dapat digabungkan dengan izin lain di tanggal yang sama."
                    ]);
            }

            // Conflict B: Same Partial Leave Type Twice on the same date
            if ($conflict->leave_type_id == $validated['leave_type_id']) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'start_date' => "Pegawai sudah memiliki data izin {$conflict->type} ({$statusLabel}) pada tanggal {$dateStr}. Tidak dapat menambahkan jenis izin parsial yang sama dua kali."
                    ]);
            }
        }

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $extension = strtolower($file->extension());
            
            if (in_array($extension, ['png', 'jpg', 'jpeg'])) {
                // Compress image
                $manager = new ImageManager(new Driver());
                $image = $manager->decode($file);
                $image->scaleDown(width: 1000);
                
                $filename = 'attachments/leaves/' . uniqid() . '.webp';
                $fullPath = storage_path('app/public/' . $filename);
                
                if (!file_exists(dirname($fullPath))) {
                    mkdir(dirname($fullPath), 0755, true);
                }
                
                $image->save($fullPath, 80);
                $validated['attachment'] = $filename;
            } else {
                // Store non-images (PDF, DOC) normally
                $path = $file->store('attachments/leaves', 'public');
                $validated['attachment'] = $path;
            }
        }

        $validated['status'] = $leaveType->requires_approval ? 'Pending' : 'Approved';

        // Prevent accidental double submit
        $existingRecent = LeaveRequest::where('employee_id', $validated['employee_id'])
            ->where('leave_type_id', $validated['leave_type_id'])
            ->where('start_date', $validated['start_date'])
            ->where('end_date', $validated['end_date'])
            ->where('created_at', '>=', now()->subSeconds(10))
            ->first();

        if ($existingRecent) {
            return redirect()->route('leaves.index')
                ->with('success', 'Izin / Cuti pegawai berhasil dicatat.');
        }

        LeaveRequest::create($validated);

        return redirect()->route('leaves.index')
            ->with('success', 'Izin / Cuti pegawai berhasil dicatat.');
    }

    /**
     * Approve the specified leave request.
     */
    public function approve(Request $request, $id)
    {
        $leave = LeaveRequest::with('leaveType')->findOrFail($id);
        
        $leave->status = 'Approved';
        $leave->notes = $request->input('notes');
        $leave->processed_by_id = auth()->id();
        $leave->processed_by_name = null;
        $leave->save();

        return redirect()->back()
            ->with('success', 'Pengajuan izin berhasil disetujui.');
    }

    /**
     * Reject the specified leave request.
     */
    public function reject(Request $request, $id)
    {
        $leave = LeaveRequest::findOrFail($id);

        $leave->status = 'Rejected';
        $leave->notes = $request->input('notes');
        $leave->processed_by_id = auth()->id();
        $leave->processed_by_name = null;
        $leave->save();

        return redirect()->back()
            ->with('success', 'Pengajuan izin berhasil ditolak.');
    }

    /**
     * Update the decision (status/notes) for the specified leave request.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:Pending,Approved,Rejected',
            'notes' => 'nullable|string',
            'leave_type_id' => 'required|exists:leave_types,id',
        ]);

        $leave = LeaveRequest::findOrFail($id);

        $leaveType = \App\Models\LeaveType::findOrFail($validated['leave_type_id']);
        $leave->leave_type_id = $leaveType->id;
        $leave->type = $leaveType->name;

        $leave->status = $validated['status'];
        $leave->notes = $validated['notes'] ?? null;
        $leave->processed_by_id = auth()->id();
        $leave->processed_by_name = null;
        $leave->save();

        return redirect()->back()
            ->with('success', 'Keputusan izin berhasil diperbarui.');
    }

    /**
     * Remove the specified leave request and its automatic attendance logs.
     */
    public function destroy($id)
    {
        $leave = LeaveRequest::findOrFail($id);
        $leave->delete();

        return redirect()->route('leaves.index')
            ->with('success', 'Data izin berhasil dihapus.');
    }
}
