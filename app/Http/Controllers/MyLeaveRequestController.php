<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            ->orderBy('start_date', 'desc')
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

        $leaveType = \App\Models\LeaveType::findOrFail($validated['leave_type_id']);
        $validated['type'] = $leaveType->name;
        $validated['employee_id'] = $user->employee->id;
        $validated['status'] = $leaveType->requires_approval ? 'Pending' : 'Approved';

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
