<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LeaveTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $leaveTypes = LeaveType::all();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $leaveTypes,
            ]);
        }

        return view('admin.leave_types.index', compact('leaveTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255|unique:leave_types,code',
            'status_code' => 'required|string|in:S,I,C,H',
        ]);

        // Auto-generate code if empty
        if (empty($validated['code'])) {
            $validated['code'] = Str::slug($validated['name']);
        } else {
            $validated['code'] = Str::slug($validated['code']);
        }

        // Ensure slug code is unique
        $originalSlug = $validated['code'];
        $counter = 1;
        while (LeaveType::where('code', $validated['code'])->exists()) {
            $validated['code'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Determine if it gets presence bonus
        $validated['gets_presence_bonus'] = ($validated['status_code'] === 'H');

        $leaveType = LeaveType::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Leave type created successfully.',
                'data' => $leaveType,
            ], 201);
        }

        return redirect()->route('leave-types.index')->with('success', 'Tipe izin berhasil ditambahkan!');
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, LeaveType $leaveType)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'code' => 'nullable|string|max:255|unique:leave_types,code,' . $leaveType->id,
            'status_code' => 'sometimes|required|string|in:S,I,C,H',
        ]);

        if (isset($validated['code']) && !empty($validated['code'])) {
            $validated['code'] = Str::slug($validated['code']);
        }

        if (isset($validated['status_code'])) {
            $validated['gets_presence_bonus'] = ($validated['status_code'] === 'H');
        }

        $leaveType->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Leave type updated successfully.',
                'data' => $leaveType,
            ]);
        }

        return redirect()->route('leave-types.index')->with('success', 'Tipe izin berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LeaveType $leaveType)
    {
        // Don't allow deletion of default system leave types if they are required
        $defaultCodes = ['sakit-pribadi', 'tidak-bekerja', 'cuti-tahunan', 'dinas-luar'];
        if (in_array($leaveType->code, $defaultCodes)) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipe izin default sistem tidak dapat dihapus.',
                ], 403);
            }
            return redirect()->route('leave-types.index')->with('error', 'Tipe izin default sistem tidak dapat dihapus!');
        }

        $leaveType->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Leave type deleted successfully.',
            ]);
        }

        return redirect()->route('leave-types.index')->with('success', 'Tipe izin berhasil dihapus!');
    }
}
