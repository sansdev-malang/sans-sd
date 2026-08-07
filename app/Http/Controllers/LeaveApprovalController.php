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
        $leave = LeaveRequest::with('leaveType')->findOrFail($id);
        
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
            $attendance = Attendance::where('employee_id', $leave->employee_id)
                ->where('date', $date->format('Y-m-d'))
                ->first();

            $status = 'Leave';
            if ($leave->leaveType) {
                if ($leave->leaveType->status_code === 'S') {
                    $status = 'Sick';
                }
            } else {
                if ($leave->type === 'Sakit') {
                    $status = 'Sick';
                }
            }

            $getsBonus = $leave->leaveType ? $leave->leaveType->gets_presence_bonus : ($leave->type === 'Dinas');
            $calculatedBonus = 0.00;
            if ($getsBonus) {
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

            if ($attendance) {
                $attendance->update([
                    'status' => $status,
                    'calculated_bonus' => $calculatedBonus,
                ]);
            } else {
                Attendance::create([
                    'employee_id' => $leave->employee_id,
                    'date' => $date->format('Y-m-d'),
                    'clock_in' => null,
                    'clock_out' => null,
                    'status' => $status,
                    'calculated_bonus' => $calculatedBonus,
                ]);
            }
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
