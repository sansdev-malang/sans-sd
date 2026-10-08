<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Semester;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of academic years and semesters with stats.
     */
    public function index()
    {
        $academicYears = AcademicYear::withCount([
            'classrooms',
            'students as active_students_count' => function ($q) {
                $q->where('status', 'aktif');
            },
            'students as total_students_count'
        ])
        ->orderBy('name', 'desc')
        ->get();

        $semesters = Semester::orderBy('order', 'asc')->get();

        $activeYear = $academicYears->firstWhere('is_active', true);
        $activeSemester = $semesters->firstWhere('is_active', true);
        $totalYears = $academicYears->count();
        $totalSemesters = $semesters->count();
        $totalClassrooms = Classroom::where('is_active', true)->count();
        $totalStudents = Student::where('status', 'aktif')->count();

        $stats = [
            'total_years' => $totalYears,
            'active_year' => $activeYear?->name ?? 'Belum Diatur',
            'active_semester' => $activeSemester?->name ?? ($activeYear?->semester ?? 'Belum Diatur'),
            'total_semesters' => $totalSemesters,
            'total_classrooms' => $totalClassrooms,
            'total_students' => $totalStudents,
        ];

        return view('admin.academic-years.index', compact('academicYears', 'semesters', 'activeYear', 'activeSemester', 'stats'));
    }

    /**
     * Show single academic year detail (JSON).
     */
    public function show($id): JsonResponse
    {
        $academicYear = AcademicYear::withCount(['classrooms', 'students'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'academic_year' => $academicYear,
        ]);
    }

    /**
     * Store new academic year.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:academic_years,name',
            'code' => 'nullable|string|max:20',
            'semester' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        $validated['semester'] = strtolower($validated['semester'] ?? 'ganjil');
        $isActive = $request->boolean('is_active');
        $validated['is_active'] = $isActive;

        // If newly created is active, deactivate others
        if ($isActive) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
        }

        $academicYear = AcademicYear::create($validated);

        return response()->json([
            'success' => true,
            'message' => "Tahun Ajaran {$academicYear->name} berhasil ditambahkan.",
            'academic_year' => $academicYear,
        ]);
    }

    /**
     * Update existing academic year.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $academicYear = AcademicYear::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:academic_years,name,' . $academicYear->id,
            'code' => 'nullable|string|max:20',
            'semester' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        if (isset($validated['semester'])) {
            $validated['semester'] = strtolower($validated['semester']);
        }
        $isActive = $request->boolean('is_active');
        $validated['is_active'] = $isActive;

        if ($isActive && !$academicYear->is_active) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
        }

        $academicYear->update($validated);

        return response()->json([
            'success' => true,
            'message' => "Tahun Ajaran {$academicYear->name} berhasil diperbarui.",
            'academic_year' => $academicYear,
        ]);
    }

    /**
     * Set specific academic year as active.
     */
    public function setActive($id): JsonResponse
    {
        $academicYear = AcademicYear::findOrFail($id);

        AcademicYear::where('is_active', true)->update(['is_active' => false]);
        $academicYear->is_active = true;
        $academicYear->save();

        return response()->json([
            'success' => true,
            'message' => "Tahun Ajaran {$academicYear->name} sekarang aktif sebagai acuan sistem.",
            'academic_year' => $academicYear,
        ]);
    }

    /**
     * Delete academic year.
     */
    public function destroy($id): JsonResponse
    {
        $academicYear = AcademicYear::findOrFail($id);

        if ($academicYear->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Tahun Ajaran yang sedang aktif tidak dapat dihapus. Aktifkan tahun ajaran lain terlebih dahulu.',
            ], 422);
        }

        $classroomsCount = $academicYear->classrooms()->count();
        if ($classroomsCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Tahun Ajaran ini masih memiliki {$classroomsCount} rombel terdaftar.",
            ], 422);
        }

        $studentsCount = $academicYear->students()->count();
        if ($studentsCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Tahun Ajaran ini masih memiliki {$studentsCount} siswa terdaftar.",
            ], 422);
        }

        $name = $academicYear->name;
        $academicYear->delete();

        return response()->json([
            'success' => true,
            'message' => "Tahun Ajaran {$name} berhasil dihapus.",
        ]);
    }
}
