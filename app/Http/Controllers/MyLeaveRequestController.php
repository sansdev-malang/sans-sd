<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class MyLeaveRequestController extends Controller
{
    /**
     * Display employee's own leave requests.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user->employee) {
            return redirect()->route('dashboard')->with('error', 'Akun Anda tidak terhubung dengan data pegawai.');
        }

        if (request()->has('clear_all')) {
            if ($user) {
                $user->unreadNotifications->markAsRead();
            }
            if ($user->employee) {
                $recentIds = LeaveRequest::where('employee_id', $user->employee->id)
                    ->where('created_at', '>=', now()->subDays(3))
                    ->pluck('id')
                    ->toArray();
                $readIds = \Illuminate\Support\Facades\Cache::get('read_leave_ids_' . $user->id, []);
                $newReadIds = array_unique(array_merge($readIds, $recentIds));
                \Illuminate\Support\Facades\Cache::forever('read_leave_ids_' . $user->id, $newReadIds);
            }
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true]);
            }
            return redirect()->route('my-leaves.index');
        }

        if (request()->has('read_id')) {
            $readIds = \Illuminate\Support\Facades\Cache::get('read_leave_ids_' . $user->id, []);
            $readIds[] = (int) request('read_id');
            \Illuminate\Support\Facades\Cache::forever('read_leave_ids_' . $user->id, array_unique($readIds));
        }
        $leaves = LeaveRequest::where('employee_id', $user->employee->id)
            ->with('leaveType')
            ->orderBy('created_at', 'desc')
            ->get();
        $leaveTypes = \App\Models\LeaveType::orderBy('name')->get();

        return view('employee.leaves.index', compact('leaves', 'leaveTypes'));
    }

    /**
     * Store employee's own leave request.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->employee) {
            return redirect()->route('dashboard')->with('error', 'Akun Anda tidak terhubung dengan data pegawai.');
        }

        $validated = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,png,jpg,jpeg,doc,docx|max:2048',
        ], [
            'leave_type_id.required' => 'Jenis izin / cuti wajib dipilih.',
            'leave_type_id.exists' => 'Jenis izin / cuti yang dipilih tidak valid.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'start_date.date' => 'Tanggal mulai harus berupa tanggal yang valid.',
            'start_date.after_or_equal' => 'Tanggal mulai tidak boleh sebelum hari ini.',
            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'end_date.date' => 'Tanggal selesai harus berupa tanggal yang valid.',
            'end_date.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'attachment.file' => 'Berkas lampiran harus berupa file.',
            'attachment.mimes' => 'Format file lampiran harus berupa: pdf, png, jpg, jpeg, doc, docx.',
            'attachment.max' => 'Ukuran file lampiran tidak boleh lebih dari 2MB.',
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
        $validated['employee_id'] = $user->employee->id;
        $validated['status'] = $leaveType->requires_approval ? 'Pending' : 'Approved';

        // 2. Intelligent Conflict / Overlap Check:
        // Full-Day: requires_attendance = false (Sakit, Cuti, Tidak Bekerja, Duka)
        // Partial-Day: requires_attendance = true (Terlambat, Pulang Awal, Keluar Jam Efektif, Kedinasan)
        $isNewFullDay = !$leaveType->requires_attendance;

        $conflictingLeaves = LeaveRequest::with('leaveType')
            ->where('employee_id', $user->employee->id)
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
                        'start_date' => "Sudah terdapat pengajuan {$conflict->type} ({$statusLabel}) pada tanggal {$dateStr}. Izin seharian penuh tidak dapat digabungkan dengan izin lain di tanggal yang sama."
                    ]);
            }

            // Conflict B: Same Partial Leave Type Twice on the same date
            if ($conflict->leave_type_id == $validated['leave_type_id']) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'start_date' => "Anda sudah memiliki pengajuan {$conflict->type} ({$statusLabel}) pada tanggal {$dateStr}. Tidak dapat mengajukan jenis izin parsial yang sama dua kali."
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

        // Prevent accidental double submit
        $existingRecent = LeaveRequest::where('employee_id', $validated['employee_id'])
            ->where('leave_type_id', $validated['leave_type_id'])
            ->where('start_date', $validated['start_date'])
            ->where('end_date', $validated['end_date'])
            ->where('created_at', '>=', now()->subSeconds(10))
            ->first();

        if ($existingRecent) {
            return redirect()->route('my-leaves.index')
                ->with('success', 'Izin / Cuti berhasil diajukan.');
        }

        LeaveRequest::create($validated);

        return redirect()->route('my-leaves.index')
            ->with('success', 'Izin / Cuti berhasil diajukan.');
    }

    /**
     * Cancel pending leave request.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user->employee) {
            return redirect()->route('dashboard');
        }

        $leave = LeaveRequest::where('employee_id', $user->employee->id)
            ->where('id', $id)
            ->firstOrFail();

        if ($leave->status !== 'Pending') {
            return redirect()->route('my-leaves.index')
                ->with('error', 'Tidak dapat membatalkan pengajuan yang sudah diproses oleh HRD.');
        }

        $leave->delete();

        return redirect()->route('my-leaves.index')
            ->with('success', 'Pengajuan izin/cuti berhasil dibatalkan.');
    }
}
