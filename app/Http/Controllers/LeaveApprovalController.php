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

    /**
     * Update the decision (status/notes) for the specified leave request.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:Pending,Approved,Rejected',
            'notes' => 'nullable|string',
        ]);

        $leave = LeaveRequest::findOrFail($id);
        $oldStatus = $leave->status;

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

        $leave->status = $validated['status'];
        if (empty($validated['notes'])) {
            $leave->notes = $validated['status'] === 'Approved' 
                ? "Disetujui oleh {$decisionMaker}." 
                : ($validated['status'] === 'Rejected' ? "Ditolak oleh {$decisionMaker}." : null);
        } else {
            $leave->notes = $validated['notes'] . " (Keputusan oleh {$decisionMaker})";
        }
        $leave->save();

        // Handle attendance changes if changed from Approved or to Approved
        if ($oldStatus === 'Approved' && $leave->status !== 'Approved') {
            $startDate = Carbon::parse($leave->start_date);
            $endDate = Carbon::parse($leave->end_date);
            
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $attendance = Attendance::where('employee_id', $leave->employee_id)
                    ->where('date', $date->format('Y-m-d'))
                    ->first();
                
                if ($attendance) {
                    if (is_null($attendance->clock_in) && is_null($attendance->clock_out)) {
                        $attendance->delete();
                    } else {
                        $attendance->update([
                            'status' => 'Present',
                            'calculated_bonus' => 0.00,
                        ]);
                    }
                }
            }
        } elseif ($oldStatus !== 'Approved' && $leave->status === 'Approved') {
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
        }

        return redirect()->route('leave-approvals.index')
            ->with('success', 'Keputusan izin berhasil diperbarui.');
    }

    /**
     * Remove the specified leave request and its automatic attendance logs.
     */
    public function destroy($id)
    {
        $leave = LeaveRequest::findOrFail($id);

        if ($leave->status === 'Approved') {
            $startDate = Carbon::parse($leave->start_date);
            $endDate = Carbon::parse($leave->end_date);
            
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $attendance = Attendance::where('employee_id', $leave->employee_id)
                    ->where('date', $date->format('Y-m-d'))
                    ->first();
                
                if ($attendance && is_null($attendance->clock_in) && is_null($attendance->clock_out)) {
                    $attendance->delete();
                }
            }
        }

        $leave->delete();

        return redirect()->route('leave-approvals.index')
            ->with('success', 'Pengajuan izin berhasil dihapus.');
    }
}
