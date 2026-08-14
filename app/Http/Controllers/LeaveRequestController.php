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

        $leaveType = \App\Models\LeaveType::findOrFail($validated['leave_type_id']);
        $validated['type'] = $leaveType->name;

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

        $validated['status'] = 'Approved';
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
