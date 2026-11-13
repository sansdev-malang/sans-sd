<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassLevel;
use App\Models\Classroom;
use App\Models\Employee;
use App\Models\EmployeeType;
use App\Models\HomeroomAssignment;
use App\Models\Subject;
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
        $selectedSubjectId = $request->get('subject_id', 'all');
        $selectedRole = $request->get('role', 'all');
        $search = $request->get('search');

        $query = HomeroomAssignment::with([
            'academicYear',
            'classLevel',
            'classroom',
            'subject',
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

        if ($selectedSubjectId && $selectedSubjectId !== 'all') {
            $query->where('subject_id', $selectedSubjectId);
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
                })->orWhereHas('subject', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
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
                WHEN role = 'guru_mapel' THEN 5 
                WHEN role = 'gpq' THEN 6 
                WHEN role = 'gpk' THEN 7 
                ELSE 8 END")
            ->paginate($perPageCount)
            ->withQueryString();

        // Master data for dropdowns
        $classLevels = ClassLevel::orderBy('order')->get();
        $classrooms = Classroom::with('classLevel')->where('is_active', true)->orderBy('code')->get();
        $subjects = Subject::active()->orderBy('order', 'asc')->get();

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
            'total_mapel' => (clone $statsQuery)->where('role', 'guru_mapel')->where('is_active', true)->count(),
            'total_gpq' => (clone $statsQuery)->where('role', 'gpq')->where('is_active', true)->count(),
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
            'selectedSubjectId',
            'selectedRole',
            'classLevels',
            'classrooms',
            'subjects',
            'teachers',
            'stats'
        ));
    }

    /**
     * Store a newly created homeroom assignment.
     */
    public function store(Request $request)
    {
        // Sanitize nullable fields: convert empty strings to null
        foreach (['class_level_id', 'classroom_id', 'subject_id', 'semester', 'notes'] as $field) {
            if ($request->has($field) && ($request->input($field) === '' || ($field === 'classroom_id' && $request->input($field) === 'all'))) {
                $request->merge([$field => null]);
            }
        }
        if ($request->has('is_active')) {
            $val = $request->input('is_active');
            $request->merge([
                'is_active' => ($val === '1' || $val === 1 || $val === true || $val === 'true' || $val === 'Aktif') ? 1 : 0
            ]);
        }

        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'class_level_id' => 'nullable|exists:class_levels,id',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'employee_id' => 'required|exists:employees,id',
            'role' => 'required|string|in:wali_kelas,guru_kelas,guru_mapel,gpk,gpq,koordinator_tingkat,pendamping_tingkat',
            'semester' => 'nullable|string|in:all,ganjil,genap',
            'is_active' => 'required|boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        if (!empty($validated['classroom_id'])) {
            $classroom = Classroom::findOrFail($validated['classroom_id']);
            $validated['class_level_id'] = $classroom->class_level_id;
        }

        // Auto-link GPQ subject if role is gpq and subject_id not specified
        if ($validated['role'] === 'gpq' && empty($validated['subject_id'])) {
            $gpqSub = Subject::whereIn('code', ['QURAN', 'GPQ'])->first();
            if ($gpqSub) {
                $validated['subject_id'] = $gpqSub->id;
            }
        }

        $assignment = HomeroomAssignment::create($validated);

        // If Wali Kelas, synchronize with classroom.homeroom_teacher_id
        if ($assignment->role === 'wali_kelas' && $assignment->classroom_id && $assignment->is_active) {
            HomeroomAssignment::where('academic_year_id', $assignment->academic_year_id)
                ->where('classroom_id', $assignment->classroom_id)
                ->where('role', 'wali_kelas')
                ->where('id', '!=', $assignment->id)
                ->update(['is_active' => false]);

            Classroom::where('id', $assignment->classroom_id)->update([
                'homeroom_teacher_id' => $assignment->employee_id,
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Penugasan guru berhasil ditambahkan.',
                'data' => $assignment,
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

        foreach (['class_level_id', 'classroom_id', 'subject_id', 'semester', 'notes'] as $field) {
            if ($request->has($field) && ($request->input($field) === '' || ($field === 'classroom_id' && $request->input($field) === 'all'))) {
                $request->merge([$field => null]);
            }
        }
        if ($request->has('is_active')) {
            $val = $request->input('is_active');
            $request->merge([
                'is_active' => ($val === '1' || $val === 1 || $val === true || $val === 'true' || $val === 'Aktif') ? 1 : 0
            ]);
        }

        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'class_level_id' => 'nullable|exists:class_levels,id',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'employee_id' => 'required|exists:employees,id',
            'role' => 'required|string|in:wali_kelas,guru_kelas,guru_mapel,gpk,gpq,koordinator_tingkat,pendamping_tingkat',
            'semester' => 'nullable|string|in:all,ganjil,genap',
            'is_active' => 'required|boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        if (!empty($validated['classroom_id'])) {
            $classroom = Classroom::findOrFail($validated['classroom_id']);
            $validated['class_level_id'] = $classroom->class_level_id;
        }

        if ($validated['role'] === 'gpq' && empty($validated['subject_id'])) {
            $gpqSub = Subject::whereIn('code', ['QURAN', 'GPQ'])->first();
            if ($gpqSub) {
                $validated['subject_id'] = $gpqSub->id;
            }
        }

        $assignment->update($validated);

        if ($assignment->role === 'wali_kelas' && $assignment->classroom_id) {
            if ($assignment->is_active) {
                HomeroomAssignment::where('academic_year_id', $assignment->academic_year_id)
                    ->where('classroom_id', $assignment->classroom_id)
                    ->where('role', 'wali_kelas')
                    ->where('id', '!=', $assignment->id)
                    ->update(['is_active' => false]);

                Classroom::where('id', $assignment->classroom_id)->update([
                    'homeroom_teacher_id' => $assignment->employee_id,
                ]);
            } else {
                Classroom::where('id', $assignment->classroom_id)
                    ->where('homeroom_teacher_id', $assignment->employee_id)
                    ->update(['homeroom_teacher_id' => null]);
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Penugasan guru berhasil diperbarui.',
                'data' => $assignment,
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

        if ($assignment->role === 'wali_kelas' && $assignment->classroom_id) {
            if ($assignment->is_active) {
                HomeroomAssignment::where('academic_year_id', $assignment->academic_year_id)
                    ->where('classroom_id', $assignment->classroom_id)
                    ->where('role', 'wali_kelas')
                    ->where('id', '!=', $assignment->id)
                    ->update(['is_active' => false]);

                Classroom::where('id', $assignment->classroom_id)->update([
                    'homeroom_teacher_id' => $assignment->employee_id,
                ]);
            } else {
                Classroom::where('id', $assignment->classroom_id)
                    ->where('homeroom_teacher_id', $assignment->employee_id)
                    ->update(['homeroom_teacher_id' => null]);
            }
        }

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

    /**
     * Synchronize wali kelas assignments from existing active classrooms.
     */
    public function syncFromClassrooms(Request $request)
    {
        $selectedYearId = $request->get('academic_year_id');
        $activeTapel = AcademicYear::where('is_active', true)->first();
        $targetYearId = $selectedYearId ?: ($activeTapel?->id ?? null);

        $query = Classroom::whereNotNull('homeroom_teacher_id');
        if ($targetYearId && $targetYearId !== 'all') {
            $query->where('academic_year_id', $targetYearId);
        }

        $classrooms = $query->get();
        $syncedCount = 0;

        foreach ($classrooms as $c) {
            $assignment = HomeroomAssignment::updateOrCreate(
                [
                    'academic_year_id' => $c->academic_year_id,
                    'classroom_id' => $c->id,
                    'role' => 'wali_kelas',
                ],
                [
                    'class_level_id' => $c->class_level_id,
                    'employee_id' => $c->homeroom_teacher_id,
                    'is_active' => true,
                    'semester' => 'all',
                ]
            );

            // Deactivate any other wali_kelas assignment for this room
            HomeroomAssignment::where('academic_year_id', $c->academic_year_id)
                ->where('classroom_id', $c->id)
                ->where('role', 'wali_kelas')
                ->where('id', '!=', $assignment->id)
                ->update(['is_active' => false]);

            $syncedCount++;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Berhasil menyinkronkan {$syncedCount} penugasan wali kelas dari data rombel.",
                'count' => $syncedCount,
            ]);
        }

        return redirect()->back()->with('success', "Berhasil menyinkronkan {$syncedCount} penugasan wali kelas dari data rombel.");
    }
}
