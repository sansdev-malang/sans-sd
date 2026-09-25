<?php

namespace App\Http\Controllers;

use App\Models\ClassLevel;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClassLevelController extends Controller
{
    /**
     * Display a listing of class levels with stats per academic year.
     */
    public function index(Request $request)
    {
        $academicYears = \App\Models\AcademicYear::orderBy('name', 'desc')->get();
        $activeYear = $academicYears->firstWhere('is_active', true) ?? $academicYears->first();

        // Filter per Tapel, defaulting to active Tapel
        $selectedYearId = $request->filled('academic_year_id')
            ? $request->get('academic_year_id')
            : ($activeYear?->id ?? null);

        $classLevels = ClassLevel::with(['classrooms' => function ($q) use ($selectedYearId) {
                if ($selectedYearId) {
                    $q->where('academic_year_id', $selectedYearId);
                }
                $q->orderBy('name');
            }])
            ->orderBy('order', 'asc')
            ->get();

        // Calculate student counts per class level via classrooms in selected Tapel
        foreach ($classLevels as $lvl) {
            $classLevelId = $lvl->id;
            $lvl->active_students_count = Student::whereHas('classroom', function ($q) use ($classLevelId, $selectedYearId) {
                $q->where('class_level_id', $classLevelId);
                if ($selectedYearId) {
                    $q->where('academic_year_id', $selectedYearId);
                }
            })->where('status', 'aktif')->count();
        }

        $totalLevels = $classLevels->count();
        
        $rombelQuery = Classroom::query();
        if ($selectedYearId) {
            $rombelQuery->where('academic_year_id', $selectedYearId);
        }
        $totalClassrooms = (clone $rombelQuery)->count();
        $totalCapacity = (clone $rombelQuery)->sum('capacity');

        $totalStudents = Student::whereHas('classroom', function ($q) use ($selectedYearId) {
            if ($selectedYearId) {
                $q->where('academic_year_id', $selectedYearId);
            }
        })->where('status', 'aktif')->count();

        $stats = [
            'total_levels' => $totalLevels,
            'total_classrooms' => $totalClassrooms,
            'total_capacity' => $totalCapacity,
            'total_students' => $totalStudents,
        ];

        return view('admin.class-levels.index', compact('classLevels', 'stats', 'academicYears', 'selectedYearId'));
    }

    /**
     * Show single class level (JSON).
     */
    public function show($id): JsonResponse
    {
        $classLevel = ClassLevel::with('classrooms')->findOrFail($id);

        return response()->json([
            'success' => true,
            'class_level' => $classLevel,
        ]);
    }

    /**
     * Store new class level.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:class_levels,code',
            'order' => 'required|integer|min:1|max:99',
            'description' => 'nullable|string',
        ]);

        $classLevel = ClassLevel::create($validated);

        return response()->json([
            'success' => true,
            'message' => "Tingkat Kelas {$classLevel->name} berhasil ditambahkan.",
            'class_level' => $classLevel,
        ]);
    }

    /**
     * Update class level.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $classLevel = ClassLevel::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:class_levels,code,' . $classLevel->id,
            'order' => 'required|integer|min:1|max:99',
            'description' => 'nullable|string',
        ]);

        $classLevel->update($validated);

        return response()->json([
            'success' => true,
            'message' => "Tingkat Kelas {$classLevel->name} berhasil diperbarui.",
            'class_level' => $classLevel,
        ]);
    }

    /**
     * Delete class level.
     */
    public function destroy($id): JsonResponse
    {
        $classLevel = ClassLevel::findOrFail($id);

        $classroomsCount = $classLevel->classrooms()->count();
        if ($classroomsCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Tingkat Kelas tidak dapat dihapus karena masih digunakan oleh {$classroomsCount} rombel.",
            ], 422);
        }

        $name = $classLevel->name;
        $classLevel->delete();

        return response()->json([
            'success' => true,
            'message' => "Tingkat Kelas {$name} berhasil dihapus.",
        ]);
    }
}
