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
                $readIds = session('read_leave_ids_' . $user->id, []);
                $newReadIds = array_unique(array_merge($readIds, $recentIds));
                session(['read_leave_ids_' . $user->id => $newReadIds]);
            }
            return redirect()->route('my-leaves.index');
        }

        if (request()->has('read_id')) {
            $readIds = session('read_leave_ids_' . $user->id, []);
            $readIds[] = (int) request('read_id');
            session(['read_leave_ids_' . $user->id => array_unique($readIds)]);
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
        ]);

        $leaveType = \App\Models\LeaveType::findOrFail($validated['leave_type_id']);
        $validated['type'] = $leaveType->name;
        $validated['employee_id'] = $user->employee->id;
        $validated['status'] = 'Approved';

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
