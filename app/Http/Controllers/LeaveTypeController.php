<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $leaveTypes = LeaveType::orderBy('id', 'asc')->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $leaveTypes,
            ]);
        }

        return view('admin.leave_types.index', compact('leaveTypes'));
    }

    /**
     * Store a newly created resource in storage (Read-Only: Centralized in HRD).
     */
    public function store(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Pengelolaan tipe izin terpusat di HRD Yayasan.',
            ], 403);
        }

        return redirect()->route('leave-types.index')
            ->with('error', 'Tipe izin dikelola secara terpusat melalui portal HRD Yayasan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(LeaveType $leaveType)
    {
        return response()->json([
            'success' => true,
            'data' => $leaveType,
        ]);
    }

    /**
     * Update the specified resource in storage (Read-Only: Centralized in HRD).
     */
    public function update(Request $request, LeaveType $leaveType)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Pengelolaan tipe izin terpusat di HRD Yayasan.',
            ], 403);
        }

        return redirect()->route('leave-types.index')
            ->with('error', 'Tipe izin dikelola secara terpusat melalui portal HRD Yayasan.');
    }

    /**
     * Remove the specified resource from storage (Read-Only: Centralized in HRD).
     */
    public function destroy(LeaveType $leaveType)
    {
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Pengelolaan tipe izin terpusat di HRD Yayasan.',
            ], 403);
        }

        return redirect()->route('leave-types.index')
            ->with('error', 'Tipe izin dikelola secara terpusat melalui portal HRD Yayasan.');
    }
}
