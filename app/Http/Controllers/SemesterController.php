<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    /**
     * Display a listing of semesters (JSON).
     */
    public function index(): JsonResponse
    {
        $semesters = Semester::orderBy('order', 'asc')->get();

        return response()->json([
            'success' => true,
            'semesters' => $semesters,
        ]);
    }

    /**
     * Show single semester detail (JSON).
     */
    public function show($id): JsonResponse
    {
        $semester = Semester::findOrFail($id);

        return response()->json([
            'success' => true,
            'semester' => $semester,
        ]);
    }

    /**
     * Store new semester.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:30',
            'order' => 'nullable|integer|min:1|max:99',
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string',
        ]);

        if (empty($validated['order'])) {
            $validated['order'] = (Semester::max('order') ?? 0) + 1;
        }

        $isActive = $request->boolean('is_active');
        $validated['is_active'] = $isActive;

        if ($isActive) {
            Semester::where('is_active', true)->update(['is_active' => false]);
            $this->syncAcademicYearSemester($validated['name']);
        }

        $semester = Semester::create($validated);

        return response()->json([
            'success' => true,
            'message' => "Semester {$semester->name} berhasil ditambahkan.",
            'semester' => $semester,
        ]);
    }

    /**
     * Update existing semester.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $semester = Semester::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:30',
            'order' => 'nullable|integer|min:1|max:99',
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string',
        ]);

        $isActive = $request->boolean('is_active');
        $validated['is_active'] = $isActive;

        if ($isActive && !$semester->is_active) {
            Semester::where('is_active', true)->update(['is_active' => false]);
            $this->syncAcademicYearSemester($validated['name']);
        }

        $semester->update($validated);

        return response()->json([
            'success' => true,
            'message' => "Semester {$semester->name} berhasil diperbarui.",
            'semester' => $semester,
        ]);
    }

    /**
     * Set specific semester as active.
     */
    public function setActive($id): JsonResponse
    {
        $semester = Semester::findOrFail($id);

        Semester::where('is_active', true)->update(['is_active' => false]);
        $semester->is_active = true;
        $semester->save();

        $this->syncAcademicYearSemester($semester->name);

        return response()->json([
            'success' => true,
            'message' => "Semester {$semester->name} sekarang aktif sebagai acuan sistem.",
            'semester' => $semester,
        ]);
    }

    /**
     * Delete semester.
     */
    public function destroy($id): JsonResponse
    {
        $semester = Semester::findOrFail($id);

        if ($semester->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Semester yang sedang aktif tidak dapat dihapus. Aktifkan semester lain terlebih dahulu.',
            ], 422);
        }

        $name = $semester->name;
        $semester->delete();

        return response()->json([
            'success' => true,
            'message' => "Semester {$name} berhasil dihapus.",
        ]);
    }

    /**
     * Helper to keep active academic year's semester field aligned.
     */
    protected function syncAcademicYearSemester(string $semesterName): void
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        if ($activeYear) {
            $isGenap = stripos($semesterName, 'genap') !== false;
            $activeYear->semester = $isGenap ? 'genap' : 'ganjil';
            $activeYear->save();
        }
    }
}
