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
            'status_code' => 'required|string|in:S,I,C,H',
            'requires_attendance' => 'required|boolean',
            'requires_approval' => 'required|boolean',
            'gets_presence_bonus' => 'required|boolean',
        ]);

        // Auto-generate code
        $validated['code'] = Str::slug($validated['name']);

        // Ensure slug code is unique
        $originalSlug = $validated['code'];
        $counter = 1;
        while (LeaveType::where('code', $validated['code'])->exists()) {
            $validated['code'] = $originalSlug . '-' . $counter;
            $counter++;
        }

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
            'status_code' => 'sometimes|required|string|in:S,I,C,H',
            'requires_attendance' => 'sometimes|required|boolean',
            'requires_approval' => 'sometimes|required|boolean',
            'gets_presence_bonus' => 'sometimes|required|boolean',
        ]);

        if (isset($validated['name'])) {
            $code = Str::slug($validated['name']);
            $originalSlug = $code;
            $counter = 1;
            while (LeaveType::where('code', $code)->where('id', '!=', $leaveType->id)->exists()) {
                $code = $originalSlug . '-' . $counter;
                $counter++;
            }
            $validated['code'] = $code;
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
