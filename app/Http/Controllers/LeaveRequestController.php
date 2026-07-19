<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Employee;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of leave requests in this unit.
     */
    public function index()
    {
        $leaves = LeaveRequest::with('employee')->orderBy('start_date', 'desc')->get();
        $employees = Employee::orderBy('name')->get();
        return view('admin.leaves.index', compact('leaves', 'employees'));
    }

    /**
     * Store a newly created leave request in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'type' => 'required|string|in:Sakit,Izin,Cuti,Dinas',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,png,jpg,jpeg,doc,docx|max:2048',
        ]);

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('attachments/leaves', 'public');
            $validated['attachment'] = $path;
        }

        LeaveRequest::create($validated);

        return redirect()->route('leaves.index')
            ->with('success', 'Pengajuan izin berhasil diajukan dan menunggu persetujuan HRD Pusat.');
    }

    /**
     * Remove the specified leave request.
     */
    public function destroy($id)
    {
        $leave = LeaveRequest::findOrFail($id);

        if ($leave->status !== 'Pending') {
            return redirect()->route('leaves.index')
                ->with('error', 'Tidak dapat menghapus pengajuan izin yang sudah diproses.');
        }

        $leave->delete();

        return redirect()->route('leaves.index')
            ->with('success', 'Pengajuan izin berhasil dibatalkan.');
    }

    /**
     * Display the history of leave requests.
     */
    public function history(Request $request)
    {
        $schoolUnit = config('app.school_unit');
        
        $query = LeaveRequest::with('employee')->orderBy('start_date', 'desc');

        if ($schoolUnit) {
            $query->whereHas('employee', function ($q) use ($schoolUnit) {
                $q->where('unit', $schoolUnit);
            });
        }

        // Add search filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $leaves = $query->get();

        return view('admin.leaves.history', compact('leaves'));
    }
}
