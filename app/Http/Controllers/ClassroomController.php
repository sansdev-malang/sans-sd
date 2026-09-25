<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassLevel;
use App\Models\Classroom;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    /**
     * Display a listing of classrooms with student statistics.
     */
    public function index(Request $request)
    {
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $activeYear = $academicYears->firstWhere('is_active', true) ?? $academicYears->first();

        // Always filter per Tapel, defaulting to currently active Tapel
        $selectedYearId = $request->filled('academic_year_id')
            ? $request->get('academic_year_id')
            : ($activeYear?->id ?? null);

        $query = Classroom::with(['classLevel', 'academicYear', 'homeroomTeacher'])
            ->withCount(['students as active_students_count' => function ($q) {
                $q->where('status', 'aktif');
            }]);

        if ($selectedYearId) {
            $query->where('academic_year_id', $selectedYearId);
        }

        if ($classLevelId = $request->get('class_level_id')) {
            if ($classLevelId !== 'all') {
                $query->where('class_level_id', $classLevelId);
            }
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $classrooms = $query->orderBy('class_level_id')->orderBy('code')->orderBy('name')->get();

        // Calculate statistics
        $totalClassrooms = $classrooms->count();
        $totalCapacity = $classrooms->sum('capacity');
        $totalEnrolled = $classrooms->sum('active_students_count');
        $occupancyRate = $totalCapacity > 0 ? round(($totalEnrolled / $totalCapacity) * 100, 1) : 0;

        $stats = [
            'total_classrooms' => $totalClassrooms,
            'total_capacity' => $totalCapacity,
            'total_enrolled' => $totalEnrolled,
            'occupancy_rate' => $occupancyRate,
        ];

        $classLevels = ClassLevel::orderBy('order')->get();
        $teachers = Employee::where('status', 'Active')->orderBy('name')->get();

        return view('admin.classrooms.index', compact(
            'classrooms',
            'stats',
            'classLevels',
            'academicYears',
            'teachers',
            'selectedYearId'
        ));
    }

    /**
     * Get list of students in specific classroom (JSON).
     */
    public function students($id): JsonResponse
    {
        $classroom = Classroom::with(['classLevel', 'academicYear', 'homeroomTeacher'])->findOrFail($id);
        $students = $classroom->students()->with('spmbCandidate')->orderBy('full_name')->get();

        return response()->json([
            'success' => true,
            'classroom' => $classroom,
            'students' => $students,
            'count' => $students->count(),
        ]);
    }

    /**
     * Store new classroom.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50',
            'class_level_id' => 'required|exists:class_levels,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'homeroom_teacher_id' => 'nullable|exists:employees,id',
            'capacity' => 'required|integer|min:1|max:100',
            'description' => 'nullable|string',
        ]);

        $classroom = Classroom::create($validated);

        return response()->json([
            'success' => true,
            'message' => "Rombel {$classroom->full_name} berhasil dibuat.",
            'classroom' => $classroom,
        ]);
    }

    /**
     * Update classroom.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $classroom = Classroom::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50',
            'class_level_id' => 'required|exists:class_levels,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'homeroom_teacher_id' => 'nullable|exists:employees,id',
            'capacity' => 'required|integer|min:1|max:100',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $classroom->update($validated);

        return response()->json([
            'success' => true,
            'message' => "Rombel {$classroom->full_name} berhasil diperbarui.",
            'classroom' => $classroom,
        ]);
    }

    /**
     * Delete classroom.
     */
    public function destroy($id): JsonResponse
    {
        $classroom = Classroom::findOrFail($id);
        
        $activeCount = $classroom->students()->where('status', 'aktif')->count();
        if ($activeCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Rombel tidak dapat dihapus karena masih memiliki {$activeCount} siswa aktif. Pindahkan siswa terlebih dahulu.",
            ], 422);
        }

        $name = $classroom->name;
        $classroom->delete();

        return response()->json([
            'success' => true,
            'message' => "Rombel {$name} berhasil dihapus.",
        ]);
    }
}
