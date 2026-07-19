<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LeaveApprovalController extends Controller
{
    /**
     * Display a listing of leave requests for approval.
     */
    public function index()
    {
        $schoolUnit = config('app.school_unit');
        
        $query = LeaveRequest::with('employee')->orderBy('created_at', 'desc');

        if ($schoolUnit) {
            $query->whereHas('employee', function ($q) use ($schoolUnit) {
                $q->where('unit', $schoolUnit);
            });
        }

        $leaves = $query->get();

        return view('admin.leave-approvals.index', compact('leaves'));
    }

    /**
     * Approve the specified leave request.
     */
    public function approve(Request $request, $id)
    {
        $leave = LeaveRequest::findOrFail($id);
        
        $user = auth()->user();
        $roleLabels = [
            'super_admin' => 'Super Admin',
            'admin_paud' => 'Admin PAUD',
            'admin_sd' => 'Admin SD',
            'admin_smp' => 'Admin SMP',
            'kepala_sekolah' => 'Kepala Sekolah',
            'waka' => 'Wakil Kepala Sekolah',
        ];
        $roleLabel = $roleLabels[$user->role] ?? $user->role;
        $decisionMaker = "{$user->name} ({$roleLabel})";

        $leave->status = 'Approved';
        $leave->notes = $request->input('notes', "Disetujui oleh {$decisionMaker}.");
        $leave->save();

        // Update attendance table automatically for those days
        $startDate = Carbon::parse($leave->start_date);
        $endDate = Carbon::parse($leave->end_date);
        
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            Attendance::updateOrCreate(
                [
                    'employee_id' => $leave->employee_id,
                    'date' => $date->format('Y-m-d')
                ],
                [
                    'clock_in' => null,
                    'clock_out' => null,
                    'status' => $leave->type === 'Sakit' ? 'Sick' : 'Leave',
                    'calculated_bonus' => 0.00
                ]
            );
        }

        return redirect()->route('leave-approvals.index')
            ->with('success', 'Pengajuan izin berhasil disetujui.');
    }

    /**
     * Reject the specified leave request.
     */
    public function reject(Request $request, $id)
    {
        $leave = LeaveRequest::findOrFail($id);

        $user = auth()->user();
        $roleLabels = [
            'super_admin' => 'Super Admin',
            'admin_paud' => 'Admin PAUD',
            'admin_sd' => 'Admin SD',
            'admin_smp' => 'Admin SMP',
            'kepala_sekolah' => 'Kepala Sekolah',
            'waka' => 'Wakil Kepala Sekolah',
        ];
        $roleLabel = $roleLabels[$user->role] ?? $user->role;
        $decisionMaker = "{$user->name} ({$roleLabel})";

        $leave->status = 'Rejected';
        $leave->notes = $request->input('notes', "Ditolak oleh {$decisionMaker}.");
        $leave->save();

        return redirect()->route('leave-approvals.index')
            ->with('success', 'Pengajuan izin berhasil ditolak.');
    }
}
