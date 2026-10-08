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

        // Always filter per Tahun Ajaran, defaulting to currently active Tapel
        $selectedYearId = $request->filled('academic_year_id')
            ? (int) $request->get('academic_year_id')
            : ($activeYear?->id ?? null);

        $selectedYear = $academicYears->firstWhere('id', $selectedYearId) ?? $activeYear;

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

        $classrooms = $query->join('class_levels', 'classrooms.class_level_id', '=', 'class_levels.id')
            ->orderBy('class_levels.order', 'asc')
            ->orderBy('classrooms.code', 'asc')
            ->orderBy('classrooms.name', 'asc')
            ->select('classrooms.*')
            ->get();

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
        
        $schoolUnit = config('app.school_unit');
        $teachers = Employee::where('status', 'Active')
            ->where(function ($q) {
                $q->whereHas('employeeType', function ($typeQ) {
                    $typeQ->where('code', 'teacher')
                          ->orWhere('name', 'like', '%guru%')
                          ->orWhere('name', 'like', '%pendidik%');
                })
                ->orWhere('position', 'like', '%guru%')
                ->orWhere('position', 'like', '%wali kelas%');
            })
            ->when($schoolUnit, function ($q) use ($schoolUnit) {
                $q->where(function ($sub) use ($schoolUnit) {
                    $sub->where('unit', $schoolUnit)->orWhereNull('unit');
                });
            })
            ->select(['id', 'name', 'front_title', 'back_title', 'photo', 'position'])
            ->orderBy('name')
            ->get();

        return view('admin.classrooms.index', compact(
            'classrooms',
            'stats',
            'classLevels',
            'academicYears',
            'teachers',
            'selectedYearId',
            'selectedYear'
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
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;

        $classroom = Classroom::create($validated);

        return response()->json([
            'success' => true,
            'message' => "Rombongan Belajar {$classroom->full_name} berhasil ditambahkan.",
            'classroom' => $classroom->load(['classLevel', 'academicYear', 'homeroomTeacher']),
        ]);
    }

    /**
     * Show single classroom (JSON).
     */
    public function show($id): JsonResponse
    {
        $classroom = Classroom::with(['classLevel', 'academicYear', 'homeroomTeacher'])->findOrFail($id);

        return response()->json([
            'success' => true,
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
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : $classroom->is_active;

        $classroom->update($validated);

        return response()->json([
            'success' => true,
            'message' => "Rombongan Belajar {$classroom->full_name} berhasil diperbarui.",
            'classroom' => $classroom->load(['classLevel', 'academicYear', 'homeroomTeacher']),
        ]);
    }

    /**
     * Delete classroom.
     */
    public function destroy($id): JsonResponse
    {
        $classroom = Classroom::findOrFail($id);

        $studentsCount = $classroom->students()->count();
        if ($studentsCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Rombongan Belajar ini masih memiliki {$studentsCount} siswa terdaftar. Pindahkan siswa terlebih dahulu sebelum menghapus rombel.",
            ], 422);
        }

        $name = $classroom->full_name;
        $classroom->delete();

        return response()->json([
            'success' => true,
            'message' => "Rombongan Belajar {$name} berhasil dihapus.",
        ]);
    }
}
