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
        $academicYears = AcademicYear::orderBy('name', 'desc')->orderBy('semester', 'asc')->get();
        $activeAcademicYear = $academicYears->firstWhere('is_active', true) ?? $academicYears->first();

        // Unique yearly academic years for annual entities (Tahunan - Opsi A)
        $uniqueAcademicYears = $academicYears->groupBy('name')->map(function ($group) {
            $activeInGroup = $group->firstWhere('is_active', true);
            $chosen = $activeInGroup ?: $group->first();
            $chosen->has_active = (bool) $activeInGroup;
            return $chosen;
        })->values();

        // Default to active academic year if not explicitly selected
        $selectedYearId = $request->filled('academic_year_id')
            ? (int) $request->get('academic_year_id')
            : ($activeAcademicYear?->id ?? null);

        $selectedYear = $academicYears->firstWhere('id', $selectedYearId) ?? $activeAcademicYear;
        $selectedYearName = $selectedYear?->name;
        $matchingYearIds = $academicYears->where('name', $selectedYearName)->pluck('id');

        $query = Student::with(['classroom.classLevel', 'academicYear', 'spmbCandidate']);

        // Academic Year Filter (covers all semester records of the selected annual year)
        if ($matchingYearIds->isNotEmpty()) {
            $query->whereIn('academic_year_id', $matchingYearIds);
        }

        // Search query
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('no_kk', 'like', "%{$search}%")
                  ->orWhere('parent_phone', 'like', "%{$search}%")
                  ->orWhere('father_name', 'like', "%{$search}%")
                  ->orWhere('mother_name', 'like', "%{$search}%")
                  ->orWhere('special_needs_type', 'like', "%{$search}%");
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

        // Filter: Tipe Siswa (Reguler / Inklusi PDBK)
        if ($studentType = $request->get('student_type')) {
            if ($studentType === 'PDBK') {
                $query->where(function($q) {
                    $q->where('student_type', 'like', '%PDBK%')
                      ->orWhere('student_type', 'like', '%KHUSUS%')
                      ->orWhereNotNull('special_needs_type');
                });
            } elseif ($studentType === 'REGULER') {
                $query->where(function($q) {
                    $q->where('student_type', 'like', '%REGULER%')
                      ->orWhereNull('student_type');
                })->whereNull('special_needs_type');
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
        if ($matchingYearIds->isNotEmpty()) {
            $statsQuery->whereIn('academic_year_id', $matchingYearIds);
        }

        $totalStudents = (clone $statsQuery)->count();
        $activeStudents = (clone $statsQuery)->where('status', 'aktif')->count();
        $maleStudents = (clone $statsQuery)->where('status', 'aktif')->whereIn('gender', ['L', 'Laki-laki', 'Male'])->count();
        $femaleStudents = (clone $statsQuery)->where('status', 'aktif')->whereIn('gender', ['P', 'Perempuan', 'Female'])->count();
        $pdbkStudents = (clone $statsQuery)->where('status', 'aktif')->where(function($q) {
            $q->where('student_type', 'like', '%PDBK%')
              ->orWhere('student_type', 'like', '%KHUSUS%')
              ->orWhereNotNull('special_needs_type');
        })->count();
        
        $rombelQuery = Classroom::where('is_active', true);
        if ($matchingYearIds->isNotEmpty()) {
            $rombelQuery->whereIn('academic_year_id', $matchingYearIds);
        }
        $totalClassrooms = $rombelQuery->count();

        $stats = [
            'total_active' => $activeStudents,
            'total_all' => $totalStudents,
            'male' => $maleStudents,
            'female' => $femaleStudents,
            'pdbk' => $pdbkStudents,
            'classrooms' => $totalClassrooms,
        ];

        // Master lists for filter dropdowns & modal selects
        $classLevels = ClassLevel::orderBy('order')->get();
        
        $classroomListQuery = Classroom::with(['classLevel', 'academicYear'])->where('is_active', true);
        if ($matchingYearIds->isNotEmpty()) {
            $classroomListQuery->whereIn('academic_year_id', $matchingYearIds);
        }
        $classrooms = $classroomListQuery->orderBy('class_level_id')->orderBy('code')->orderBy('name')->get();
        $allClassrooms = Classroom::with(['classLevel', 'academicYear'])->where('is_active', true)->orderBy('academic_year_id', 'desc')->orderBy('name')->get();

        $students = $query->orderBy('status', 'asc')->orderBy('full_name', 'asc')->paginate(15)->withQueryString();

        return view('admin.students.index', [
            'students' => $students,
            'stats' => $stats,
            'classLevels' => $classLevels,
            'classrooms' => $classrooms,
            'allClassrooms' => $allClassrooms,
            'academicYears' => $uniqueAcademicYears,
            'activeAcademicYear' => $activeAcademicYear,
            'selectedYearId' => $selectedYearId,
            'selectedYear' => $selectedYear,
            'selectedYearName' => $selectedYearName,
        ]);
    }

    /**
     * Show single student detail (JSON) with full profile & lifecycle history.
     */
    public function show($id): JsonResponse
    {
        $student = Student::with([
            'classroom.classLevel', 
            'classroom.homeroomTeacher', 
            'academicYear', 
            'spmbCandidate',
            'classroomHistories.classroom.classLevel',
            'classroomHistories.classroom.homeroomTeacher',
            'classroomHistories.academicYear'
        ])->findOrFail($id);

        // Hitung kelengkapan data (Completeness %)
        $requiredFields = [
            'nis', 'nik', 'full_name', 'gender', 'birth_place', 'birth_date', 
            'religion', 'address', 'father_name', 'mother_name', 'parent_phone',
            'no_kk', 'birth_certificate_no', 'blood_type'
        ];
        $filledCount = 0;
        foreach ($requiredFields as $field) {
            if (!empty($student->$field)) {
                $filledCount++;
            }
        }
        $completenessPercent = round(($filledCount / count($requiredFields)) * 100);

        return response()->json([
            'success' => true,
            'student' => $student,
            'completeness_percent' => $completenessPercent,
            'formatted_gender' => $student->formatted_gender,
            'age' => $student->age,
            'whatsapp_url' => $student->whatsapp_url,
            'clean_phone' => $student->clean_parent_phone,
            'classroom_histories' => $student->classroomHistories,
        ]);
    }

    /**
     * Store a newly created student in storage (All 7 categories).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            // 1. Identitas & Legalitas
            'nis' => 'required|string|max:50|unique:students,nis',
            'nisn' => 'nullable|string|max:50',
            'nik' => 'nullable|string|max:50',
            'no_kk' => 'nullable|string|max:50',
            'birth_certificate_no' => 'nullable|string|max:100',
            'citizenship' => 'nullable|string|max:100',
            'full_name' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:100',
            'gender' => 'required|string|in:L,P,Laki-laki,Perempuan',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'religion' => 'nullable|string|max:50',
            
            // 2. Inklusi & Kekhususan
            'student_type' => 'nullable|string|max:100',
            'special_needs_type' => 'nullable|string|max:255',
            'special_needs_notes' => 'nullable|string',

            // 3. Alamat & Domisili
            'address' => 'nullable|string',
            'rt' => 'nullable|string|max:20',
            'rw' => 'nullable|string|max:20',
            'village' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'district_category' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'residence_status' => 'nullable|string|max:100',
            'distance_to_school' => 'nullable|string|max:50',
            'home_phone' => 'nullable|string|max:50',

            // 4. Keluarga & Saudara
            'child_number' => 'nullable|integer',
            'siblings_count' => 'nullable|integer',
            'step_siblings_count' => 'nullable|integer',
            'adoptive_siblings_count' => 'nullable|integer',
            'home_language' => 'nullable|string|max:100',

            // 5. Kesehatan & UKS
            'weight' => 'nullable|string|max:20',
            'height' => 'nullable|string|max:20',
            'blood_type' => 'nullable|string|max:10',
            'severe_disease_history' => 'nullable|string',
            'frequent_disease' => 'nullable|string',

            // 6. Orang Tua & Wali
            'father_name' => 'nullable|string|max:255',
            'father_nik' => 'nullable|string|max:50',
            'father_birth_place' => 'nullable|string|max:100',
            'father_birth_date' => 'nullable|date',
            'father_religion' => 'nullable|string|max:50',
            'father_phone' => 'nullable|string|max:50',
            'father_education' => 'nullable|string|max:100',
            'father_job' => 'nullable|string|max:100',
            'father_company' => 'nullable|string|max:255',
            'father_income' => 'nullable|string|max:100',
            'father_email' => 'nullable|email|max:100',

            'mother_name' => 'nullable|string|max:255',
            'mother_nik' => 'nullable|string|max:50',
            'mother_birth_place' => 'nullable|string|max:100',
            'mother_birth_date' => 'nullable|date',
            'mother_religion' => 'nullable|string|max:50',
            'mother_phone' => 'nullable|string|max:50',
            'mother_education' => 'nullable|string|max:100',
            'mother_job' => 'nullable|string|max:100',
            'mother_company' => 'nullable|string|max:255',
            'mother_income' => 'nullable|string|max:100',
            'mother_email' => 'nullable|email|max:100',

            'guardian_name' => 'nullable|string|max:255',
            'guardian_relation' => 'nullable|string|max:100',
            'guardian_phone' => 'nullable|string|max:50',
            'guardian_job' => 'nullable|string|max:100',
            'guardian_address' => 'nullable|string',

            'parent_phone' => 'nullable|string|max:50',
            'parent_email' => 'nullable|email|max:100',

            // 7. Riwayat Asal Sekolah & Dokumen
            'previous_school' => 'nullable|string|max:255',
            'origin_category' => 'nullable|string|max:100',
            'previous_school_address' => 'nullable|string',
            'sttb_number_date' => 'nullable|string|max:255',
            'checklist_documents' => 'nullable|array',

            // Penempatan Kelas & Status
            'classroom_id' => 'required|exists:classrooms,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
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

        // WhatsApp / Parent Phone fallback
        if (empty($validated['parent_phone'])) {
            $validated['parent_phone'] = $validated['father_phone'] ?? ($validated['mother_phone'] ?? ($validated['guardian_phone'] ?? null));
        }

        $student = Student::create($validated);

        // Catat riwayat kelas awal (Lifecycle History)
        if ($student->classroom_id && $student->academic_year_id) {
            \App\Models\StudentClassroomHistory::firstOrCreate([
                'student_id' => $student->id,
                'academic_year_id' => $student->academic_year_id,
            ], [
                'classroom_id' => $student->classroom_id,
                'status' => 'naik_kelas',
                'notes' => 'Pendaftaran / Penempatan Rombel Awal',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Data siswa {$student->full_name} berhasil ditambahkan.",
            'student' => $student,
        ]);
    }

    /**
     * Update student details (All 7 categories).
     */
    public function update(Request $request, $id): JsonResponse
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            // 1. Identitas & Legalitas
            'nis' => 'required|string|max:50|unique:students,nis,' . $student->id,
            'nisn' => 'nullable|string|max:50',
            'nik' => 'nullable|string|max:50',
            'no_kk' => 'nullable|string|max:50',
            'birth_certificate_no' => 'nullable|string|max:100',
            'citizenship' => 'nullable|string|max:100',
            'full_name' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:100',
            'gender' => 'required|string|in:L,P,Laki-laki,Perempuan',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'religion' => 'nullable|string|max:50',
            
            // 2. Inklusi & Kekhususan
            'student_type' => 'nullable|string|max:100',
            'special_needs_type' => 'nullable|string|max:255',
            'special_needs_notes' => 'nullable|string',

            // 3. Alamat & Domisili
            'address' => 'nullable|string',
            'rt' => 'nullable|string|max:20',
            'rw' => 'nullable|string|max:20',
            'village' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'district_category' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'residence_status' => 'nullable|string|max:100',
            'distance_to_school' => 'nullable|string|max:50',
            'home_phone' => 'nullable|string|max:50',

            // 4. Keluarga & Saudara
            'child_number' => 'nullable|integer',
            'siblings_count' => 'nullable|integer',
            'step_siblings_count' => 'nullable|integer',
            'adoptive_siblings_count' => 'nullable|integer',
            'home_language' => 'nullable|string|max:100',

            // 5. Kesehatan & UKS
            'weight' => 'nullable|string|max:20',
            'height' => 'nullable|string|max:20',
            'blood_type' => 'nullable|string|max:10',
            'severe_disease_history' => 'nullable|string',
            'frequent_disease' => 'nullable|string',

            // 6. Orang Tua & Wali
            'father_name' => 'nullable|string|max:255',
            'father_nik' => 'nullable|string|max:50',
            'father_birth_place' => 'nullable|string|max:100',
            'father_birth_date' => 'nullable|date',
            'father_religion' => 'nullable|string|max:50',
            'father_phone' => 'nullable|string|max:50',
            'father_education' => 'nullable|string|max:100',
            'father_job' => 'nullable|string|max:100',
            'father_company' => 'nullable|string|max:255',
            'father_income' => 'nullable|string|max:100',
            'father_email' => 'nullable|email|max:100',

            'mother_name' => 'nullable|string|max:255',
            'mother_nik' => 'nullable|string|max:50',
            'mother_birth_place' => 'nullable|string|max:100',
            'mother_birth_date' => 'nullable|date',
            'mother_religion' => 'nullable|string|max:50',
            'mother_phone' => 'nullable|string|max:50',
            'mother_education' => 'nullable|string|max:100',
            'mother_job' => 'nullable|string|max:100',
            'mother_company' => 'nullable|string|max:255',
            'mother_income' => 'nullable|string|max:100',
            'mother_email' => 'nullable|email|max:100',

            'guardian_name' => 'nullable|string|max:255',
            'guardian_relation' => 'nullable|string|max:100',
            'guardian_phone' => 'nullable|string|max:50',
            'guardian_job' => 'nullable|string|max:100',
            'guardian_address' => 'nullable|string',

            'parent_phone' => 'nullable|string|max:50',
            'parent_email' => 'nullable|email|max:100',

            // 7. Riwayat Asal Sekolah & Dokumen
            'previous_school' => 'nullable|string|max:255',
            'origin_category' => 'nullable|string|max:100',
            'previous_school_address' => 'nullable|string',
            'sttb_number_date' => 'nullable|string|max:255',
            'checklist_documents' => 'nullable|array',

            // Penempatan Kelas & Status
            'classroom_id' => 'required|exists:classrooms,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'status' => 'required|string|in:aktif,lulus,mutasi,keluar,nonaktif',
            'notes' => 'nullable|string',
        ]);

        // WhatsApp / Parent Phone fallback
        if (empty($validated['parent_phone'])) {
            $validated['parent_phone'] = $validated['father_phone'] ?? ($validated['mother_phone'] ?? ($validated['guardian_phone'] ?? null));
        }

        $student->update($validated);

        // Update / create history record for current academic year & classroom
        if ($student->classroom_id && $student->academic_year_id) {
            \App\Models\StudentClassroomHistory::updateOrCreate([
                'student_id' => $student->id,
                'academic_year_id' => $student->academic_year_id,
            ], [
                'classroom_id' => $student->classroom_id,
                'status' => $student->status === 'lulus' ? 'lulus' : ($student->status === 'mutasi' ? 'mutasi' : 'naik_kelas'),
            ]);
        }

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

        // Clean any linked classroom history
        \App\Models\StudentClassroomHistory::where('student_id', $student->id)->delete();

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
        
        // Sheet 1: Data Siswa (Template Input)
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Siswa');

        // Headers (Separated Grade Dapodik & Nama Kelas Julukan)
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
            'Kelas / Grade (e.g. 1A)',
            'Nama Kelas (e.g. Berlian)',
            'Tahun Pelajaran (e.g. 2026/2027)',
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
            '1A',
            'Berlian',
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

        // Put example in row 2 with string data types for numbers (preserves leading zeros & prevents scientific notation)
        foreach ($example as $colIndex => $val) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValueExplicit($colLetter . '2', $val, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
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

        // Sheet 2: Referensi Rombel & Tapel
        $refSheet = $spreadsheet->createSheet();
        $refSheet->setTitle('Referensi Rombel & Tapel');

        $refHeaders = ['No', 'Tingkat', 'Kelas / Grade', 'Nama Kelas (Julukan)', 'Nama Rombel Resmi', 'Tahun Pelajaran', 'Wali Kelas'];
        foreach ($refHeaders as $idx => $rh) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($idx + 1);
            $refSheet->setCellValue($colLetter . '1', $rh);
        }
        $refSheet->getStyle('A1:G1')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $refSheet->getStyle('A1:G1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF10B981'); // Emerald color

        $classrooms = Classroom::with(['classLevel', 'academicYear', 'homeroomTeacher'])
            ->orderBy('academic_year_id', 'desc')
            ->orderBy('class_level_id')
            ->orderBy('code')
            ->get();

        $rowIdx = 2;
        foreach ($classrooms as $no => $cr) {
            $refSheet->setCellValue('A' . $rowIdx, $no + 1);
            $refSheet->setCellValue('B' . $rowIdx, $cr->classLevel?->name ?: '-');
            $refSheet->setCellValue('C' . $rowIdx, $cr->code ?: '-');
            $refSheet->setCellValue('D' . $rowIdx, $cr->name ?: '-');
            $refSheet->setCellValue('E' . $rowIdx, $cr->full_name ?: '-');
            $refSheet->setCellValue('F' . $rowIdx, $cr->academicYear?->name ?: '-');
            $refSheet->setCellValue('G' . $rowIdx, $cr->homeroomTeacher?->name ?: '-');
            $rowIdx++;
        }

        foreach (range(1, 7) as $colIndex) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $refSheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // Set active sheet back to Sheet 1
        $spreadsheet->setActiveSheetIndex(0);

        if (request()->filled('download_token')) {
            setcookie('download_token', request()->query('download_token'), time() + 60, '/', '', false, false);
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

        // Smart column mapping from header text
        $map = [
            'nis' => 0,
            'full_name' => 1,
            'nickname' => 2,
            'gender' => 3,
            'birth_place' => 4,
            'birth_date' => 5,
            'nisn' => 6,
            'nik' => 7,
            'religion' => 8,
            'code' => 9,          // Grade (1A)
            'class_name' => 10,   // Classname (Berlian)
            'rombel_combined' => null,
            'academic_year' => 11,
            'address' => 12,
            'father_name' => 13,
            'father_phone' => 14,
            'father_job' => 15,
            'mother_name' => 16,
            'mother_phone' => 17,
            'mother_job' => 18,
            'parent_phone' => 19,
            'status' => 20,
        ];

        if (is_array($header) && count($header) > 0) {
            $hasSeparateRombel = false;
            foreach ($header as $cIdx => $cVal) {
                $clean = strtolower(trim((string)$cVal));
                
                if (str_contains($clean, 'nisn')) {
                    $map['nisn'] = $cIdx;
                } elseif ((preg_match('/\bnis\b/', $clean) || str_starts_with($clean, 'nis')) && !str_contains($clean, 'jenis')) {
                    $map['nis'] = $cIdx;
                } elseif (str_contains($clean, 'nama lengkap')) {
                    $map['full_name'] = $cIdx;
                } elseif (str_contains($clean, 'panggilan')) {
                    $map['nickname'] = $cIdx;
                } elseif (str_contains($clean, 'kelamin') || str_contains($clean, 'jenis') || $clean === 'jk' || $clean === 'l/p') {
                    $map['gender'] = $cIdx;
                } elseif (str_contains($clean, 'tempat lahir')) {
                    $map['birth_place'] = $cIdx;
                } elseif (str_contains($clean, 'tanggal lahir') || str_contains($clean, 'tgl lahir')) {
                    $map['birth_date'] = $cIdx;
                } elseif (preg_match('/\bnik\b/', $clean) || str_starts_with($clean, 'nik')) {
                    $map['nik'] = $cIdx;
                } elseif (str_contains($clean, 'agama')) {
                    $map['religion'] = $cIdx;
                } elseif (str_contains($clean, 'grade') || str_contains($clean, 'kode rombel') || str_contains($clean, 'kode kelas') || $clean === 'kelas') {
                    $map['code'] = $cIdx;
                    $hasSeparateRombel = true;
                } elseif (str_contains($clean, 'nama kelas') || str_contains($clean, 'julukan') || str_contains($clean, 'classname')) {
                    $map['class_name'] = $cIdx;
                    $hasSeparateRombel = true;
                } elseif (str_contains($clean, 'rombel') && !$hasSeparateRombel) {
                    $map['rombel_combined'] = $cIdx;
                } elseif (str_contains($clean, 'tahun') || str_contains($clean, 'tapel')) {
                    $map['academic_year'] = $cIdx;
                } elseif (str_contains($clean, 'alamat')) {
                    $map['address'] = $cIdx;
                } elseif (str_contains($clean, 'ayah') && (str_contains($clean, 'hp') || str_contains($clean, 'telepon') || str_contains($clean, 'telp') || str_contains($clean, 'wa'))) {
                    $map['father_phone'] = $cIdx;
                } elseif (str_contains($clean, 'ayah') && str_contains($clean, 'pekerjaan')) {
                    $map['father_job'] = $cIdx;
                } elseif (str_contains($clean, 'nama ayah') || (str_contains($clean, 'ayah') && !str_contains($clean, 'ibu'))) {
                    $map['father_name'] = $cIdx;
                } elseif (str_contains($clean, 'ibu') && (str_contains($clean, 'hp') || str_contains($clean, 'telepon') || str_contains($clean, 'telp') || str_contains($clean, 'wa'))) {
                    $map['mother_phone'] = $cIdx;
                } elseif (str_contains($clean, 'ibu') && str_contains($clean, 'pekerjaan')) {
                    $map['mother_job'] = $cIdx;
                } elseif (str_contains($clean, 'nama ibu') || (str_contains($clean, 'ibu') && !str_contains($clean, 'ayah'))) {
                    $map['mother_name'] = $cIdx;
                } elseif (str_contains($clean, 'whatsapp') || str_contains($clean, 'kontak') || str_contains($clean, 'hp ortu')) {
                    $map['parent_phone'] = $cIdx;
                } elseif (str_contains($clean, 'status')) {
                    $map['status'] = $cIdx;
                }
            }
        }

        $errors = [];
        $importedCount = 0;
        $updatedCount = 0;

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            // Skip completely empty row
            if (empty(array_filter($row, fn($v) => !is_null($v) && trim((string)$v) !== ''))) {
                continue;
            }

            $nis = !empty($row[$map['nis']]) ? trim((string)$row[$map['nis']]) : null;
            $fullName = !empty($row[$map['full_name']]) ? trim((string)$row[$map['full_name']]) : null;
            $nickname = !empty($row[$map['nickname']]) ? trim((string)$row[$map['nickname']]) : null;
            $gender = !empty($row[$map['gender']]) ? trim((string)$row[$map['gender']]) : null;
            $birthPlace = !empty($row[$map['birth_place']]) ? trim((string)$row[$map['birth_place']]) : null;
            $birthDateRaw = !empty($row[$map['birth_date']]) ? trim((string)$row[$map['birth_date']]) : null;
            $nisn = !empty($row[$map['nisn']]) ? trim((string)$row[$map['nisn']]) : null;
            $nik = !empty($row[$map['nik']]) ? trim((string)$row[$map['nik']]) : null;
            $religion = !empty($row[$map['religion']]) ? trim((string)$row[$map['religion']]) : 'Islam';

            $gradeCode = !empty($row[$map['code']]) ? trim((string)$row[$map['code']]) : null;
            $className = !empty($row[$map['class_name']]) ? trim((string)$row[$map['class_name']]) : null;
            $combinedRombel = isset($map['rombel_combined']) && !empty($row[$map['rombel_combined']]) ? trim((string)$row[$map['rombel_combined']]) : null;

            $academicYearStr = !empty($row[$map['academic_year']]) ? trim((string)$row[$map['academic_year']]) : null;
            $address = !empty($row[$map['address']]) ? trim((string)$row[$map['address']]) : null;
            $fatherName = !empty($row[$map['father_name']]) ? trim((string)$row[$map['father_name']]) : null;
            $fatherPhone = !empty($row[$map['father_phone']]) ? trim((string)$row[$map['father_phone']]) : null;
            $fatherJob = !empty($row[$map['father_job']]) ? trim((string)$row[$map['father_job']]) : null;
            $motherName = !empty($row[$map['mother_name']]) ? trim((string)$row[$map['mother_name']]) : null;
            $motherPhone = !empty($row[$map['mother_phone']]) ? trim((string)$row[$map['mother_phone']]) : null;
            $motherJob = !empty($row[$map['mother_job']]) ? trim((string)$row[$map['mother_job']]) : null;
            $parentPhone = !empty($row[$map['parent_phone']]) ? trim((string)$row[$map['parent_phone']]) : null;
            $status = !empty($row[$map['status']]) ? strtolower(trim((string)$row[$map['status']])) : 'aktif';

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

            // Normalisasi Tanggal Lahir (Mendukung dd/mm/yyyy, dd-mm-yyyy, yyyy-mm-dd, Serial Excel, dsb.)
            $birthDate = null;
            if ($birthDateRaw) {
                try {
                    $raw = trim((string)$birthDateRaw);
                    
                    // 1. Cek jika serial numeric Excel (misal: 43637 / 44002)
                    if (is_numeric($raw) && (int)$raw > 10000 && (int)$raw < 70000) {
                        $birthDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float)$raw)->format('Y-m-d');
                    }
                    // 2. Format dd/mm/yyyy atau dd-mm-yyyy (Contoh gambar: 20/06/2020, 03/06/2020)
                    elseif (preg_match('/^(\d{1,2})[\/\-\.](\d{1,2})[\/\-\.](\d{4})$/', $raw, $m)) {
                        $day = str_pad($m[1], 2, '0', STR_PAD_LEFT);
                        $month = str_pad($m[2], 2, '0', STR_PAD_LEFT);
                        $year = $m[3];
                        if ((int)$day <= 31 && (int)$month <= 12) {
                            $birthDate = "{$year}-{$month}-{$day}";
                        } else {
                            $birthDate = \Carbon\Carbon::parse($raw)->format('Y-m-d');
                        }
                    }
                    // 3. Format yyyy-mm-dd atau yyyy/mm/dd (Contoh: 2020-06-20)
                    elseif (preg_match('/^(\d{4})[\/\-\.](\d{1,2})[\/\-\.](\d{1,2})$/', $raw, $m)) {
                        $year = $m[1];
                        $month = str_pad($m[2], 2, '0', STR_PAD_LEFT);
                        $day = str_pad($m[3], 2, '0', STR_PAD_LEFT);
                        $birthDate = "{$year}-{$month}-{$day}";
                    }
                    // 4. Fallback jika ada nama bulan teks Indonesia (misal: 20 Juni 2020)
                    else {
                        $indoMonths = [
                            'januari' => 'january', 'februari' => 'february', 'maret' => 'march',
                            'april' => 'april', 'mei' => 'may', 'juni' => 'june',
                            'juli' => 'july', 'agustus' => 'august', 'september' => 'september',
                            'oktober' => 'october', 'november' => 'november', 'desember' => 'december',
                            'agu' => 'aug', 'okt' => 'oct', 'des' => 'dec'
                        ];
                        $cleanDate = strtolower($raw);
                        foreach ($indoMonths as $idm => $enm) {
                            $cleanDate = str_replace($idm, $enm, $cleanDate);
                        }
                        $birthDate = \Carbon\Carbon::parse($cleanDate)->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    $birthDate = null;
                }
            }

            // Resolusi Tahun Ajaran (Mendukung Opsi A: Tahunan)
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

            // Dapatkan semua ID semester dalam tahun ajaran yang sama
            $targetAY = $academicYearId ? AcademicYear::find($academicYearId) : null;
            $targetYearIds = $targetAY ? AcademicYear::where('name', $targetAY->name)->pluck('id')->toArray() : ($academicYearId ? [$academicYearId] : []);

            // Resolusi Rombel (Classroom) - Case-Insensitive (BERLIAN / Berlian / berlian)
            $classroomId = null;

            if ($gradeCode && $className) {
                $cr = Classroom::whereRaw('LOWER(code) = ?', [strtolower($gradeCode)])
                    ->whereRaw('LOWER(name) = ?', [strtolower($className)])
                    ->when(!empty($targetYearIds), function($q) use ($targetYearIds) {
                        $q->whereIn('academic_year_id', $targetYearIds);
                    })
                    ->first();
                if (!$cr) {
                    $cr = Classroom::whereRaw('LOWER(code) = ?', [strtolower($gradeCode)])
                        ->when(!empty($targetYearIds), function($q) use ($targetYearIds) {
                            $q->whereIn('academic_year_id', $targetYearIds);
                        })
                        ->first();
                }
                if (!$cr) {
                    $cr = Classroom::whereRaw('LOWER(name) = ?', [strtolower($className)])
                        ->when(!empty($targetYearIds), function($q) use ($targetYearIds) {
                            $q->whereIn('academic_year_id', $targetYearIds);
                        })
                        ->first();
                }
                if ($cr) $classroomId = $cr->id;
            } elseif ($gradeCode) {
                $cr = Classroom::where(function($q) use ($gradeCode) {
                        $q->whereRaw('LOWER(code) = ?', [strtolower($gradeCode)])
                          ->orWhereRaw('LOWER(name) = ?', [strtolower($gradeCode)]);
                    })
                    ->when(!empty($targetYearIds), function($q) use ($targetYearIds) {
                        $q->whereIn('academic_year_id', $targetYearIds);
                    })
                    ->first();
                if ($cr) $classroomId = $cr->id;
            } elseif ($className) {
                $cr = Classroom::where(function($q) use ($className) {
                        $q->whereRaw('LOWER(name) = ?', [strtolower($className)])
                          ->orWhere('name', 'like', "%{$className}%");
                    })
                    ->when(!empty($targetYearIds), function($q) use ($targetYearIds) {
                        $q->whereIn('academic_year_id', $targetYearIds);
                    })
                    ->first();
                if ($cr) $classroomId = $cr->id;
            } elseif ($combinedRombel) {
                $cleanCombined = trim(preg_replace('/[^a-zA-Z0-9\s]/', ' ', $combinedRombel));
                $parts = preg_split('/\s+/', $cleanCombined);
                $firstPart = strtolower($parts[0] ?? '');
                $secondPart = strtolower($parts[1] ?? '');

                $cr = Classroom::where(function($q) use ($combinedRombel, $firstPart, $secondPart) {
                        $q->whereRaw('LOWER(name) = ?', [strtolower($combinedRombel)])
                          ->orWhereRaw('LOWER(code) = ?', [strtolower($combinedRombel)])
                          ->orWhereRaw('LOWER(code) = ?', [$firstPart])
                          ->orWhereRaw('LOWER(name) = ?', [$secondPart])
                          ->orWhere('name', 'like', "%{$combinedRombel}%");
                    })
                    ->when(!empty($targetYearIds), function($q) use ($targetYearIds) {
                        $q->whereIn('academic_year_id', $targetYearIds);
                    })
                    ->first();
                if ($cr) $classroomId = $cr->id;
            }

            if (!$classroomId && $defaultClassroom) {
                $classroomId = $defaultClassroom->id;
                if (!$academicYearId && $defaultClassroom->academic_year_id) {
                    $academicYearId = $defaultClassroom->academic_year_id;
                }
            }

            if (!$classroomId) {
                $identifier = $gradeCode ? ($gradeCode . ' ' . $className) : ($className ?: ($combinedRombel ?: 'tidak terdefinisi'));
                $errors[] = "Baris {$rowNumber}: Rombel '{$identifier}' tidak ditemukan di sistem. Harap periksa nama rombel atau pilih Rombel default.";
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
