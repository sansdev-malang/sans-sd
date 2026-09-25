<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use Illuminate\Validation\Rule;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of academic years with stats & active toggle.
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

        // Ensure all records have start_date and end_date populated
        foreach ($academicYears as $ay) {
            if (!$ay->start_date || !$ay->end_date) {
                $dates = $this->calculatePeriodDates($ay->name, $ay->semester);
                $ay->update($dates);
                $ay->start_date = $dates['start_date'];
                $ay->end_date = $dates['end_date'];
            }
        }

        $activeYear = $academicYears->firstWhere('is_active', true);
        $totalYears = $academicYears->count();
        $totalClassrooms = Classroom::where('is_active', true)->count();
        $totalStudents = Student::where('status', 'aktif')->count();

        $stats = [
            'total_years' => $totalYears,
            'active_year' => $activeYear?->name ?? 'Belum Diatur',
            'active_semester' => $activeYear?->semester ?? '-',
            'total_classrooms' => $totalClassrooms,
            'total_students' => $totalStudents,
        ];

        return view('admin.academic-years.index', compact('academicYears', 'activeYear', 'stats'));
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
        $semester = strtolower($request->input('semester', 'ganjil'));

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('academic_years', 'name')->where(function ($query) use ($semester) {
                    return $query->where('semester', $semester);
                }),
            ],
            'code' => 'nullable|string|max:20',
            'semester' => 'required|string|in:Ganjil,Genap,ganjil,genap',
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string',
        ], [
            'name.unique' => "Tahun Pelajaran {$request->input('name')} untuk semester {$request->input('semester')} sudah ada.",
        ]);

        $validated['semester'] = $semester;
        $isActive = $request->boolean('is_active');
        $validated['is_active'] = $isActive;

        // Auto-calculate start_date and end_date (July-Dec for Ganjil, Jan-June for Genap)
        $dates = $this->calculatePeriodDates($validated['name'], $validated['semester']);
        $validated['start_date'] = $dates['start_date'];
        $validated['end_date'] = $dates['end_date'];

        // If newly created is active, deactivate others
        if ($isActive) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
        }

        $academicYear = AcademicYear::create($validated);

        return response()->json([
            'success' => true,
            'message' => "Tahun Ajaran {$academicYear->name} ({$academicYear->semester}) berhasil ditambahkan.",
            'academic_year' => $academicYear,
        ]);
    }

    /**
     * Update existing academic year.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $academicYear = AcademicYear::findOrFail($id);
        $semester = strtolower($request->input('semester', $academicYear->semester));

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('academic_years', 'name')->ignore($academicYear->id)->where(function ($query) use ($semester) {
                    return $query->where('semester', $semester);
                }),
            ],
            'code' => 'nullable|string|max:20',
            'semester' => 'required|string|in:Ganjil,Genap,ganjil,genap',
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string',
        ], [
            'name.unique' => "Tahun Pelajaran {$request->input('name')} untuk semester {$request->input('semester')} sudah ada.",
        ]);

        $validated['semester'] = $semester;
        $isActive = $request->boolean('is_active');
        $validated['is_active'] = $isActive;

        // Auto-calculate start_date and end_date
        $dates = $this->calculatePeriodDates($validated['name'], $validated['semester']);
        $validated['start_date'] = $dates['start_date'];
        $validated['end_date'] = $dates['end_date'];

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
     * Calculate default start and end dates based on Tapel name and semester.
     * Ganjil: 1 July - 31 December (Year 1)
     * Genap: 1 January - 30 June (Year 2)
     */
    private function calculatePeriodDates(string $name, string $semester): array
    {
        if (preg_match('/(\d{4})[\/\-](\d{4})/', $name, $matches)) {
            $y1 = (int) $matches[1];
            $y2 = (int) $matches[2];
        } elseif (preg_match('/(\d{4})/', $name, $matches)) {
            $y1 = (int) $matches[1];
            $y2 = $y1 + 1;
        } else {
            $y1 = (int) date('Y');
            $y2 = $y1 + 1;
        }

        if (strtolower($semester) === 'genap') {
            return [
                'start_date' => "{$y2}-01-01",
                'end_date' => "{$y2}-06-30",
            ];
        }

        return [
            'start_date' => "{$y1}-07-01",
            'end_date' => "{$y1}-12-31",
        ];
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
            'message' => "Tahun Ajaran {$academicYear->name} ({$academicYear->semester}) sekarang aktif sebagai acuan sistem.",
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
        $studentsCount = $academicYear->students()->count();

        if ($classroomsCount > 0 || $studentsCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Tahun Ajaran tidak dapat dihapus karena masih terhubung dengan {$classroomsCount} rombel dan {$studentsCount} siswa.",
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
