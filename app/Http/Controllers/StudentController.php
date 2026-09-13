<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassLevel;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StudentController extends Controller
{
    /**
     * Display a listing of students with filters & pagination.
     */
    public function index(Request $request)
    {
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $activeAcademicYear = $academicYears->firstWhere('is_active', true) ?? $academicYears->first();

        // Default to active academic year if not explicitly selected
        $selectedYearId = $request->get('academic_year_id', $activeAcademicYear?->id);
        $selectedYear = $academicYears->firstWhere('id', $selectedYearId) ?? $activeAcademicYear;
        $selectedYearId = $selectedYear?->id;

        $query = Student::with(['classroom.classLevel', 'academicYear', 'spmbCandidate']);

        // Academic Year Filter
        if ($selectedYearId) {
            $query->where('academic_year_id', $selectedYearId);
        }

        // Search query
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('parent_phone', 'like', "%{$search}%")
                  ->orWhere('father_name', 'like', "%{$search}%")
                  ->orWhere('mother_name', 'like', "%{$search}%");
            });
        }

        // Filter: Kelas (ClassLevel)
        if ($classLevelId = $request->get('class_level_id')) {
            if ($classLevelId !== 'all') {
                $query->whereHas('classroom', function ($q) use ($classLevelId) {
                    $q->where('class_level_id', $classLevelId);
                });
            }
        }

        // Filter: Rombel (Classroom)
        if ($classroomId = $request->get('classroom_id')) {
            if ($classroomId !== 'all') {
                $query->where('classroom_id', $classroomId);
            }
        }

        // Filter: Status
        if ($status = $request->get('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        // Filter: Gender
        if ($gender = $request->get('gender')) {
            if ($gender !== 'all') {
                $query->where('gender', $gender);
            }
        }

        // Stats calculation based on selected academic year
        $statsQuery = Student::query();
        if ($selectedYearId) {
            $statsQuery->where('academic_year_id', $selectedYearId);
        }

        $totalStudents = (clone $statsQuery)->count();
        $activeStudents = (clone $statsQuery)->where('status', 'aktif')->count();
        $maleStudents = (clone $statsQuery)->where('status', 'aktif')->whereIn('gender', ['L', 'Laki-laki', 'Male'])->count();
        $femaleStudents = (clone $statsQuery)->where('status', 'aktif')->whereIn('gender', ['P', 'Perempuan', 'Female'])->count();
        
        $rombelQuery = Classroom::where('is_active', true);
        if ($selectedYearId) {
            $rombelQuery->where('academic_year_id', $selectedYearId);
        }
        $totalClassrooms = $rombelQuery->count();

        $stats = [
            'total_active' => $activeStudents,
            'total_all' => $totalStudents,
            'male' => $maleStudents,
            'female' => $femaleStudents,
            'classrooms' => $totalClassrooms,
        ];

        // Master lists for filter dropdowns & modal selects
        $classLevels = ClassLevel::orderBy('order')->get();
        
        $classroomListQuery = Classroom::with(['classLevel', 'academicYear'])->where('is_active', true);
        if ($selectedYearId) {
            $classroomListQuery->where('academic_year_id', $selectedYearId);
        }
        $classrooms = $classroomListQuery->orderBy('class_level_id')->orderBy('name')->get();
        $allClassrooms = Classroom::with(['classLevel', 'academicYear'])->where('is_active', true)->orderBy('academic_year_id', 'desc')->orderBy('name')->get();

        $students = $query->orderBy('status', 'asc')->orderBy('full_name', 'asc')->paginate(15)->withQueryString();

        return view('admin.students.index', compact(
            'students',
            'stats',
            'classLevels',
            'classrooms',
            'allClassrooms',
            'academicYears',
            'activeAcademicYear',
            'selectedYearId',
            'selectedYear'
        ));
    }

    /**
     * Show single student detail (JSON).
     */
    public function show($id): JsonResponse
    {
        $student = Student::with(['classroom.classLevel', 'classroom.homeroomTeacher', 'academicYear', 'spmbCandidate'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'student' => $student,
            'formatted_gender' => $student->formatted_gender,
            'age' => $student->age,
            'whatsapp_url' => $student->whatsapp_url,
            'clean_phone' => $student->clean_parent_phone,
        ]);
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nis' => 'required|string|max:50|unique:students,nis',
            'nisn' => 'nullable|string|max:50',
            'nik' => 'nullable|string|max:50',
            'full_name' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:100',
            'gender' => 'required|string|in:L,P,Laki-laki,Perempuan',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'religion' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'previous_school' => 'nullable|string|max:255',
            'classroom_id' => 'required|exists:classrooms,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'father_name' => 'nullable|string|max:255',
            'father_phone' => 'nullable|string|max:50',
            'mother_name' => 'nullable|string|max:255',
            'mother_phone' => 'nullable|string|max:50',
            'parent_phone' => 'nullable|string|max:50',
            'status' => 'required|string|in:aktif,lulus,mutasi,keluar,nonaktif',
            'enrolled_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if (empty($validated['academic_year_id'])) {
            $classroom = Classroom::find($validated['classroom_id']);
            $validated['academic_year_id'] = $classroom?->academic_year_id;
            
            if (empty($validated['academic_year_id'])) {
                $activeAY = AcademicYear::where('is_active', true)->first();
                $validated['academic_year_id'] = $activeAY ? $activeAY->id : null;
            }
        }

        if (empty($validated['enrolled_date'])) {
            $validated['enrolled_date'] = now()->toDateString();
        }

        $student = Student::create($validated);

        return response()->json([
            'success' => true,
            'message' => "Data siswa {$student->full_name} berhasil ditambahkan.",
            'student' => $student,
        ]);
    }

    /**
     * Update student details.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'nis' => 'required|string|max:50|unique:students,nis,' . $student->id,
            'nisn' => 'nullable|string|max:50',
            'nik' => 'nullable|string|max:50',
            'full_name' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:100',
            'gender' => 'required|string|in:L,P,Laki-laki,Perempuan',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'religion' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'previous_school' => 'nullable|string|max:255',
            'classroom_id' => 'required|exists:classrooms,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'father_name' => 'nullable|string|max:255',
            'father_phone' => 'nullable|string|max:50',
            'mother_name' => 'nullable|string|max:255',
            'mother_phone' => 'nullable|string|max:50',
            'parent_phone' => 'nullable|string|max:50',
            'status' => 'required|string|in:aktif,lulus,mutasi,keluar,nonaktif',
            'notes' => 'nullable|string',
        ]);

        $student->update($validated);

        return response()->json([
            'success' => true,
            'message' => "Data siswa {$student->full_name} berhasil diperbarui.",
            'student' => $student,
        ]);
    }

    /**
     * Delete student.
     */
    public function destroy($id): JsonResponse
    {
        $student = Student::findOrFail($id);
        $name = $student->full_name;

        // If linked to SPMB candidate, unlink it
        if ($student->spmb_candidate_id) {
            $candidate = \App\Models\SpmbCandidate::find($student->spmb_candidate_id);
            if ($candidate) {
                $candidate->is_enrolled = false;
                $candidate->enrolled_at = null;
                $candidate->student_id = null;
                $candidate->save();
            }
        }

        $student->delete();

        return response()->json([
            'success' => true,
            'message' => "Data siswa {$name} berhasil dihapus.",
        ]);
    }

    /**
     * Download Excel template for Bulk Student Import.
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Siswa');

        // Headers
        $headers = [
            'NIS (Wajib)',
            'Nama Lengkap (Wajib)',
            'Nama Panggilan',
            'Jenis Kelamin (L/P)',
            'Tempat Lahir',
            'Tanggal Lahir (YYYY-MM-DD)',
            'NISN',
            'NIK',
            'Agama',
            'Rombel / Kelas (Nama atau Kode)',
            'Tahun Ajaran (e.g. 2026/2027)',
            'Alamat',
            'Nama Ayah',
            'No HP Ayah',
            'Pekerjaan Ayah',
            'Nama Ibu',
            'No HP Ibu',
            'Pekerjaan Ibu',
            'No WhatsApp Ortu (Primary)',
            'Status (aktif/lulus/mutasi/keluar)'
        ];

        // Sample Row
        $example = [
            '26.SD.001',
            'Ahmad Fauzi',
            'Fauzi',
            'L',
            'Malang',
            '2019-05-12',
            '0123456789',
            '3573010101190001',
            'Islam',
            '2-A (Ibnu Rusyd)',
            '2026/2027',
            'Jl. Soekarno Hatta No. 45, Malang',
            'Budi Santoso',
            '081234567890',
            'Wiraswasta',
            'Siti Aminah',
            '081234567891',
            'Guru',
            '081234567890',
            'aktif'
        ];

        // Put headers in row 1
        foreach ($headers as $colIndex => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue($colLetter . '1', $header);
        }

        // Put example in row 2
        foreach ($example as $colIndex => $val) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue($colLetter . '2', $val);
        }

        // Header styling
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A1:' . $lastCol . '1')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A1:' . $lastCol . '1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF4F46E5'); // Indigo color

        // Auto-size columns
        foreach (range(1, count($headers)) as $colIndex) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'Template_Import_Siswa_SD.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Import students from Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'default_classroom_id' => 'nullable|exists:classrooms,id',
            'default_academic_year_id' => 'nullable|exists:academic_years,id',
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'file.mimes' => 'Format file harus berupa .xlsx, .xls, atau .csv.'
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Remove header row
        $header = array_shift($rows);

        $defaultClassroom = $request->filled('default_classroom_id') ? Classroom::find($request->input('default_classroom_id')) : null;
        $defaultAcademicYear = $request->filled('default_academic_year_id') ? AcademicYear::find($request->input('default_academic_year_id')) : AcademicYear::where('is_active', true)->first();

        $errors = [];
        $importedCount = 0;
        $updatedCount = 0;

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            // Skip completely empty row
            if (empty(array_filter($row, fn($v) => !is_null($v) && trim((string)$v) !== ''))) {
                continue;
            }

            $nis = !empty($row[0]) ? trim((string)$row[0]) : null;
            $fullName = !empty($row[1]) ? trim((string)$row[1]) : null;
            $nickname = !empty($row[2]) ? trim((string)$row[2]) : null;
            $gender = !empty($row[3]) ? trim((string)$row[3]) : null;
            $birthPlace = !empty($row[4]) ? trim((string)$row[4]) : null;
            $birthDateRaw = !empty($row[5]) ? trim((string)$row[5]) : null;
            $nisn = !empty($row[6]) ? trim((string)$row[6]) : null;
            $nik = !empty($row[7]) ? trim((string)$row[7]) : null;
            $religion = !empty($row[8]) ? trim((string)$row[8]) : 'Islam';
            $classroomStr = !empty($row[9]) ? trim((string)$row[9]) : null;
            $academicYearStr = !empty($row[10]) ? trim((string)$row[10]) : null;
            $address = !empty($row[11]) ? trim((string)$row[11]) : null;
            $fatherName = !empty($row[12]) ? trim((string)$row[12]) : null;
            $fatherPhone = !empty($row[13]) ? trim((string)$row[13]) : null;
            $fatherJob = !empty($row[14]) ? trim((string)$row[14]) : null;
            $motherName = !empty($row[15]) ? trim((string)$row[15]) : null;
            $motherPhone = !empty($row[16]) ? trim((string)$row[16]) : null;
            $motherJob = !empty($row[17]) ? trim((string)$row[17]) : null;
            $parentPhone = !empty($row[18]) ? trim((string)$row[18]) : null;
            $status = !empty($row[19]) ? strtolower(trim((string)$row[19])) : 'aktif';

            if (empty($nis)) {
                $errors[] = "Baris {$rowNumber}: NIS wajib diisi.";
                continue;
            }

            if (empty($fullName)) {
                $errors[] = "Baris {$rowNumber}: Nama lengkap wajib diisi.";
                continue;
            }

            // Normalisasi Gender
            if ($gender) {
                $gUpper = strtoupper($gender);
                if (in_array($gUpper, ['L', 'LAKI-LAKI', 'MALE', 'PRIA'])) {
                    $gender = 'L';
                } elseif (in_array($gUpper, ['P', 'PEREMPUAN', 'FEMALE', 'WANITA'])) {
                    $gender = 'P';
                } else {
                    $gender = 'L';
                }
            } else {
                $gender = 'L';
            }

            // Normalisasi Tanggal Lahir
            $birthDate = null;
            if ($birthDateRaw) {
                try {
                    $birthDate = \Carbon\Carbon::parse($birthDateRaw)->format('Y-m-d');
                } catch (\Exception $e) {
                    $birthDate = null;
                }
            }

            // Resolusi Tahun Ajaran
            $academicYearId = null;
            if ($academicYearStr) {
                $cleanAY = str_replace('-', '/', $academicYearStr);
                $ay = AcademicYear::where('name', $cleanAY)->orWhere('code', $cleanAY)->first();
                if ($ay) {
                    $academicYearId = $ay->id;
                }
            }
            if (!$academicYearId) {
                $academicYearId = $defaultAcademicYear?->id;
            }

            // Resolusi Rombel (Classroom)
            $classroomId = null;
            if ($classroomStr) {
                $cr = Classroom::where('name', $classroomStr)
                    ->orWhere('code', $classroomStr)
                    ->when($academicYearId, function($q) use ($academicYearId) {
                        $q->where('academic_year_id', $academicYearId);
                    })
                    ->first();
                if (!$cr) {
                    // Coba cari fuzzy berdasarkan awalan nama
                    $cr = Classroom::where('name', 'like', "%{$classroomStr}%")
                        ->when($academicYearId, function($q) use ($academicYearId) {
                            $q->where('academic_year_id', $academicYearId);
                        })
                        ->first();
                }
                if ($cr) {
                    $classroomId = $cr->id;
                    if (!$academicYearId && $cr->academic_year_id) {
                        $academicYearId = $cr->academic_year_id;
                    }
                }
            }

            if (!$classroomId && $defaultClassroom) {
                $classroomId = $defaultClassroom->id;
                if (!$academicYearId && $defaultClassroom->academic_year_id) {
                    $academicYearId = $defaultClassroom->academic_year_id;
                }
            }

            if (!$classroomId) {
                $errors[] = "Baris {$rowNumber}: Rombel '{$classroomStr}' tidak ditemukan di sistem. Harap periksa nama rombel atau pilih Rombel default.";
                continue;
            }

            // Status Validation
            if (!in_array($status, ['aktif', 'lulus', 'mutasi', 'keluar', 'nonaktif'])) {
                $status = 'aktif';
            }

            // WhatsApp / Parent Phone fallback
            if (!$parentPhone) {
                $parentPhone = $fatherPhone ?: $motherPhone;
            }

            // Upsert student by NIS
            $student = Student::where('nis', $nis)->first();
            $dataToSave = [
                'nis' => $nis,
                'nisn' => $nisn,
                'nik' => $nik,
                'full_name' => $fullName,
                'nickname' => $nickname,
                'gender' => $gender,
                'birth_place' => $birthPlace,
                'birth_date' => $birthDate,
                'religion' => $religion,
                'classroom_id' => $classroomId,
                'academic_year_id' => $academicYearId,
                'address' => $address,
                'father_name' => $fatherName,
                'father_phone' => $fatherPhone,
                'father_job' => $fatherJob,
                'mother_name' => $motherName,
                'mother_phone' => $motherPhone,
                'mother_job' => $motherJob,
                'parent_phone' => $parentPhone,
                'status' => $status,
                'enrolled_date' => now()->toDateString(),
            ];

            if ($student) {
                $student->update($dataToSave);
                $updatedCount++;
            } else {
                Student::create($dataToSave);
                $importedCount++;
            }
        }

        $msg = "Proses impor selesai! {$importedCount} data siswa baru berhasil ditambahkan";
        if ($updatedCount > 0) {
            $msg .= ", {$updatedCount} data siswa diperbarui";
        }
        $msg .= ".";

        if (count($errors) > 0) {
            return redirect()->route('students.index')
                ->with('success', $msg)
                ->with('import_errors', $errors);
        }

        return redirect()->route('students.index')->with('success', $msg);
    }
}
