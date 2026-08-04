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

        $leaves = LeaveRequest::where('employee_id', $user->employee->id)
            ->orderBy('start_date', 'desc')
            ->get();

        return view('employee.leaves.index', compact('leaves'));
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
            'type' => 'required|string|in:Sakit,Izin,Cuti,Dinas',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,png,jpg,jpeg,doc,docx|max:2048',
        ]);

        $validated['employee_id'] = $user->employee->id;
        $validated['status'] = 'Pending';

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
            ->with('success', 'Pengajuan izin/cuti berhasil diajukan dan menunggu persetujuan HRD Pusat.');
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
