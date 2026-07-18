<?php

namespace App\Http\Controllers;

use App\Models\EmployeeType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmployeeTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $employeeTypes = EmployeeType::all();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $employeeTypes,
            ]);
        }

        return view('admin.employee_types.index', compact('employeeTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255|unique:employee_types,code',
            'description' => 'nullable|string',
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
        while (EmployeeType::where('code', $validated['code'])->exists()) {
            $validated['code'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        $employeeType = EmployeeType::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Employee type created successfully.',
                'data' => $employeeType,
            ], 201);
        }

        return redirect()->route('employee-types.index')->with('success', 'Tipe pegawai berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(EmployeeType $employeeType)
    {
        return response()->json([
            'success' => true,
            'data' => $employeeType,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmployeeType $employeeType)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'code' => 'nullable|string|max:255|unique:employee_types,code,' . $employeeType->id,
            'description' => 'nullable|string',
        ]);

        if (isset($validated['code']) && !empty($validated['code'])) {
            $validated['code'] = Str::slug($validated['code']);
        }

        $employeeType->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Employee type updated successfully.',
                'data' => $employeeType,
            ]);
        }

        return redirect()->route('employee-types.index')->with('success', 'Tipe pegawai berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeType $employeeType)
    {
        $employeeType->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Employee type deleted successfully.',
            ]);
        }

        return redirect()->route('employee-types.index')->with('success', 'Tipe pegawai berhasil dihapus!');
    }
}
