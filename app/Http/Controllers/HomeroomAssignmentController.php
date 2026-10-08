<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassLevel;
use App\Models\Classroom;
use App\Models\Employee;
use App\Models\EmployeeType;
use App\Models\HomeroomAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeroomAssignmentController extends Controller
{
    /**
     * Display a listing of homeroom and teacher assignments with filters.
     */
    public function index(Request $request)
    {
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $activeAcademicYear = $academicYears->firstWhere('is_active', true) ?? $academicYears->first();
        
        $selectedYearId = $request->get('academic_year_id', $activeAcademicYear?->id);
        if (!$selectedYearId || $selectedYearId === 'all') {
            $selectedYearId = $activeAcademicYear?->id;
        }

        $selectedClassLevelId = $request->get('class_level_id', 'all');
        $selectedClassroomId = $request->get('classroom_id', 'all');
        $selectedRole = $request->get('role', 'all');
        $search = $request->get('search');

        $query = HomeroomAssignment::with([
            'academicYear',
            'classLevel',
            'classroom',
            'teacher.user'
        ]);

        if ($selectedYearId) {
            $query->where('academic_year_id', $selectedYearId);
        }

        if ($selectedClassLevelId && $selectedClassLevelId !== 'all') {
            $query->where(function ($q) use ($selectedClassLevelId) {
                $q->where('class_level_id', $selectedClassLevelId)
                  ->orWhereHas('classroom', function ($cq) use ($selectedClassLevelId) {
                      $cq->where('class_level_id', $selectedClassLevelId);
                  });
            });
        }

        if ($selectedClassroomId && $selectedClassroomId !== 'all') {
            $query->where('classroom_id', $selectedClassroomId);
        }

        if ($selectedRole && $selectedRole !== 'all') {
            $query->where('role', $selectedRole);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('teacher', function ($tq) use ($search) {
                    $tq->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%")
                       ->orWhere('nik', 'like', "%{$search}%")
                       ->orWhere('nuptk', 'like', "%{$search}%");
                })->orWhereHas('classroom', function ($cq) use ($search) {
                    $cq->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            });
        }

        $perPage = $request->get('per_page', 15);
        $perPageCount = ($perPage === 'all' || $perPage == 99999 || $perPage === '99999') ? 99999 : (int) $perPage;
        if ($perPageCount <= 0) {
            $perPageCount = 15;
        }

        $assignments = $query->orderBy('academic_year_id', 'desc')
            ->orderBy('class_level_id', 'asc')
            ->orderBy('classroom_id', 'asc')
            ->orderByRaw("CASE 
                WHEN role = 'koordinator_tingkat' THEN 1 
                WHEN role = 'pendamping_tingkat' THEN 2 
                WHEN role = 'wali_kelas' THEN 3 
                WHEN role = 'guru_kelas' THEN 4 
                WHEN role = 'gpk' THEN 5 
                WHEN role = 'gpq' THEN 6 
                ELSE 7 END")
            ->paginate($perPageCount)
            ->withQueryString();

        // Master data for dropdowns
        $classLevels = ClassLevel::orderBy('order')->get();
        $classrooms = Classroom::with('classLevel')->where('is_active', true)->orderBy('code')->get();

        // Teachers (Active employees)
        $teachers = Employee::where('status', 'Active')
            ->orderBy('name', 'asc')
            ->get();

        $statsQuery = HomeroomAssignment::query();
        if ($selectedYearId) {
            $statsQuery->where('academic_year_id', $selectedYearId);
        }

        $stats = [
            'total_assignments' => (clone $statsQuery)->count(),
            'total_homeroom' => (clone $statsQuery)->where('role', 'wali_kelas')->where('is_active', true)->count(),
            'total_gpk' => (clone $statsQuery)->where('role', 'gpk')->where('is_active', true)->count(),
            'total_teachers' => (clone $statsQuery)->where('is_active', true)->distinct('employee_id')->count('employee_id'),
        ];

        return view('admin.homeroom-assignments.index', compact(
            'assignments',
            'academicYears',
            'activeAcademicYear',
            'selectedYearId',
            'selectedClassLevelId',
            'selectedClassroomId',
            'selectedRole',
            'classLevels',
            'classrooms',
            'teachers',
            'stats'
        ));
    }

    /**
     * Store a newly created homeroom assignment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'class_level_id' => 'nullable|exists:class_levels,id',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'employee_id' => 'required|exists:employees,id',
            'role' => 'required|string|in:wali_kelas,guru_kelas,gpk,gpq,koordinator_tingkat,pendamping_tingkat',
            'is_active' => 'required|boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        if (!empty($validated['classroom_id'])) {
            $classroom = Classroom::findOrFail($validated['classroom_id']);
            $validated['class_level_id'] = $classroom->class_level_id;
        }

        $assignment = HomeroomAssignment::create($validated);

        // If Wali Kelas, synchronize with classroom.homeroom_teacher_id
        if ($assignment->role === 'wali_kelas' && $assignment->classroom_id && $assignment->is_active) {
            Classroom::where('id', $assignment->classroom_id)->update([
                'homeroom_teacher_id' => $assignment->employee_id,
            ]);
        }

        return redirect()->route('homeroom-assignments.index', ['academic_year_id' => $assignment->academic_year_id])
            ->with('success', 'Penugasan guru berhasil ditambahkan.');
    }

    /**
     * Update the specified homeroom assignment.
     */
    public function update(Request $request, $id)
    {
        $assignment = HomeroomAssignment::findOrFail($id);

        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'class_level_id' => 'nullable|exists:class_levels,id',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'employee_id' => 'required|exists:employees,id',
            'role' => 'required|string|in:wali_kelas,guru_kelas,gpk,gpq,koordinator_tingkat,pendamping_tingkat',
            'is_active' => 'required|boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        if (!empty($validated['classroom_id'])) {
            $classroom = Classroom::findOrFail($validated['classroom_id']);
            $validated['class_level_id'] = $classroom->class_level_id;
        }

        $assignment->update($validated);

        if ($assignment->role === 'wali_kelas' && $assignment->classroom_id && $assignment->is_active) {
            Classroom::where('id', $assignment->classroom_id)->update([
                'homeroom_teacher_id' => $assignment->employee_id,
            ]);
        }

        return redirect()->route('homeroom-assignments.index', ['academic_year_id' => $assignment->academic_year_id])
            ->with('success', 'Penugasan guru berhasil diperbarui.');
    }

    /**
     * Toggle assignment active status.
     */
    public function toggleStatus($id): JsonResponse
    {
        $assignment = HomeroomAssignment::findOrFail($id);
        $assignment->is_active = !$assignment->is_active;
        $assignment->save();

        return response()->json([
            'success' => true,
            'is_active' => $assignment->is_active,
            'message' => 'Status penugasan berhasil diubah.',
        ]);
    }

    /**
     * Remove the specified homeroom assignment.
     */
    public function destroy($id)
    {
        $assignment = HomeroomAssignment::findOrFail($id);
        $yearId = $assignment->academic_year_id;
        
        // If Wali Kelas, unlink from classroom
        if ($assignment->role === 'wali_kelas' && $assignment->classroom_id) {
            Classroom::where('id', $assignment->classroom_id)
                ->where('homeroom_teacher_id', $assignment->employee_id)
                ->update(['homeroom_teacher_id' => null]);
        }

        $assignment->delete();

        return redirect()->route('homeroom-assignments.index', ['academic_year_id' => $yearId])
            ->with('success', 'Penugasan guru berhasil dihapus.');
    }
}
