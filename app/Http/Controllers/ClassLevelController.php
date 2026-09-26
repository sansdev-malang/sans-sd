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
        $academicYears = \App\Models\AcademicYear::orderBy('name', 'desc')->orderBy('semester', 'asc')->get();
        $activeYear = $academicYears->firstWhere('is_active', true) ?? $academicYears->first();

        // Unique yearly academic years for annual entities (Tahunan - Opsi A)
        $uniqueAcademicYears = $academicYears->groupBy('name')->map(function ($group) {
            $activeInGroup = $group->firstWhere('is_active', true);
            $chosen = $activeInGroup ?: $group->first();
            $chosen->has_active = (bool) $activeInGroup;
            return $chosen;
        })->values();

        // Filter per Tapel, defaulting to active Tapel
        $selectedYearId = $request->filled('academic_year_id')
            ? (int) $request->get('academic_year_id')
            : ($activeYear?->id ?? null);

        $selectedYear = $academicYears->firstWhere('id', $selectedYearId) ?? $activeYear;
        $selectedYearName = $selectedYear?->name;
        $matchingYearIds = $academicYears->where('name', $selectedYearName)->pluck('id');

        $classLevels = ClassLevel::with(['classrooms' => function ($q) use ($matchingYearIds) {
                if ($matchingYearIds->isNotEmpty()) {
                    $q->whereIn('academic_year_id', $matchingYearIds);
                }
                $q->orderBy('name');
            }])
            ->orderBy('order', 'asc')
            ->get();

        // 1-Query optimization for student counts per class level in selected Tapel
        $studentCounts = Student::query()
            ->join('classrooms', 'students.classroom_id', '=', 'classrooms.id')
            ->where('students.status', 'aktif')
            ->when($matchingYearIds->isNotEmpty(), function ($q) use ($matchingYearIds) {
                $q->whereIn('classrooms.academic_year_id', $matchingYearIds);
            })
            ->groupBy('classrooms.class_level_id')
            ->selectRaw('classrooms.class_level_id, count(students.id) as count')
            ->pluck('count', 'class_level_id');

        foreach ($classLevels as $lvl) {
            $lvl->active_students_count = (int) ($studentCounts->get($lvl->id, 0));
        }

        $totalLevels = $classLevels->count();
        
        $rombelQuery = Classroom::query();
        if ($matchingYearIds->isNotEmpty()) {
            $rombelQuery->whereIn('academic_year_id', $matchingYearIds);
        }
        $rombelStats = (clone $rombelQuery)
            ->selectRaw('COUNT(*) as total_classrooms, COALESCE(SUM(capacity), 0) as total_capacity')
            ->first();

        $totalStudents = (int) $studentCounts->sum();

        $stats = [
            'total_levels' => $totalLevels,
            'total_classrooms' => (int) ($rombelStats->total_classrooms ?? 0),
            'total_capacity' => (int) ($rombelStats->total_capacity ?? 0),
            'total_students' => $totalStudents,
        ];

        return view('admin.class-levels.index', [
            'classLevels' => $classLevels,
            'stats' => $stats,
            'academicYears' => $uniqueAcademicYears,
            'selectedYearId' => $selectedYearId,
            'selectedYear' => $selectedYear,
            'selectedYearName' => $selectedYearName,
        ]);
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
