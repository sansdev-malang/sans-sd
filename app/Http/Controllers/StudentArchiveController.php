<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\StudentDocument;
use App\Services\FileOptimizerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class StudentArchiveController extends Controller
{
    /**
     * Display the Student Archive & Completeness Matrix.
     */
    public function index(Request $request)
    {
        // 1. Academic Years
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $activeAcademicYear = $academicYears->firstWhere('is_active', true) ?? $academicYears->first();
        $selectedYearId = $request->get('academic_year_id', $activeAcademicYear?->id);

        // 2. Classrooms
        $classroomsQuery = Classroom::where('is_active', true)
            ->with(['classLevel']);

        if ($selectedYearId && $selectedYearId !== 'all') {
            $classroomsQuery->where('academic_year_id', $selectedYearId);
        }

        $classrooms = $classroomsQuery
            ->withCount(['students' => function ($q) use ($selectedYearId) {
                $q->where('status', 'aktif');
                if ($selectedYearId && $selectedYearId !== 'all') {
                    $q->where('academic_year_id', $selectedYearId);
                }
            }])
            ->orderBy('name', 'asc')
            ->get();

        $selectedClassroomId = $request->get('classroom_id');
        if (!$selectedClassroomId && $classrooms->isNotEmpty()) {
            $selectedClassroomId = $classrooms->first()->id;
        }

        $selectedClassroom = $classrooms->firstWhere('id', $selectedClassroomId);

        // 3. Status filter: 'all', 'complete', 'incomplete'
        $statusFilter = $request->get('status', 'all');
        $search = $request->get('search');

        // 4. Query Students
        $query = Student::where('status', 'aktif')
            ->with([
                'classroom.classLevel',
                'academicYear',
                'spmbCandidate',
                'uploadedDocuments',
            ]);

        if ($selectedClassroomId && $selectedClassroomId !== 'all') {
            $query->where('classroom_id', $selectedClassroomId);
        }

        if ($selectedYearId && $selectedYearId !== 'all') {
            $query->where('academic_year_id', $selectedYearId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nickname', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $allStudents = $query->orderBy('full_name', 'asc')->get();

        // Compute checklist and stats
        $students = $allStudents->filter(function ($student) use ($statusFilter) {
            $cl = $student->document_checklist;
            if ($statusFilter === 'complete') {
                return $cl['is_complete'] === true;
            } elseif ($statusFilter === 'incomplete') {
                return $cl['is_complete'] === false;
            }
            return true;
        });

        $totalCount = $allStudents->count();
        $completeCount = $allStudents->filter(fn($s) => $s->document_checklist['is_complete'])->count();
        $missingAkta = $allStudents->filter(fn($s) => empty($s->document_checklist['akta']))->count();
        $missingKk = $allStudents->filter(fn($s) => empty($s->document_checklist['kk']))->count();
        $missingFoto = $allStudents->filter(fn($s) => empty($s->document_checklist['foto']))->count();

        $stats = [
            'total' => $totalCount,
            'complete' => $completeCount,
            'incomplete' => $totalCount - $completeCount,
            'missing_akta' => $missingAkta,
            'missing_kk' => $missingKk,
            'missing_foto' => $missingFoto,
            'percentage' => $totalCount > 0 ? round(($completeCount / $totalCount) * 100) : 0,
        ];

        $documentTypes = [
            'akta_kelahiran' => 'Akta Kelahiran',
            'kartu_keluarga' => 'Kartu Keluarga (KK)',
            'ktp_ortu' => 'KTP Orang Tua',
            'pas_foto' => 'Pas Foto Murid',
            'kia_imunisasi' => 'Buku KIA / Kartu Imunisasi',
            'piagam_prestasi' => 'Piagam / Sertifikat Prestasi',
            'ijazah_stsb' => 'Ijazah / STSB Kelulusan',
            'surat_mutasi' => 'Surat Pindah / Mutasi',
            'lainnya' => 'Dokumen Lainnya',
        ];

        return view('admin.student-archives.index', compact(
            'students',
            'classrooms',
            'selectedClassroom',
            'selectedClassroomId',
            'academicYears',
            'activeAcademicYear',
            'selectedYearId',
            'statusFilter',
            'search',
            'stats',
            'documentTypes'
        ));
    }

    /**
     * Store additional/follow-up document for a student.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'document_type' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:20480|mimes:pdf,jpg,jpeg,png,webp,doc,docx',
            'notes' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $optimizer = app(FileOptimizerService::class);
        $path = $optimizer->optimizeAndStoreRelative($file, 'student-documents');
        $fullPath = storage_path('app/public/' . $path);
        $size = file_exists($fullPath) ? filesize($fullPath) : $file->getSize();
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        StudentDocument::create([
            'student_id' => $validated['student_id'],
            'document_type' => $validated['document_type'],
            'title' => $validated['title'],
            'file_path' => $path,
            'file_name' => $originalName,
            'file_size' => $size,
            'file_extension' => $ext,
            'notes' => $validated['notes'] ?? null,
            'uploaded_by' => auth()->id(),
        ]);

        $student = Student::find($validated['student_id']);

        return redirect()->back()->with('success', "Berkas \"{$validated['title']}\" untuk murid {$student->full_name} berhasil diarsipkan.");
    }

    /**
     * Download document file.
     */
    public function download($id)
    {
        $doc = StudentDocument::findOrFail($id);
        $cleanPath = ltrim(str_replace('/storage/', '', $doc->file_path), '/');

        if (!$cleanPath || !Storage::disk('public')->exists($cleanPath)) {
            abort(404, 'File berkas tidak ditemukan di server.');
        }

        return Storage::disk('public')->download($cleanPath, $doc->file_name ?? basename($cleanPath));
    }

    /**
     * Delete an uploaded document.
     */
    public function destroy($id)
    {
        $doc = StudentDocument::findOrFail($id);
        $cleanPath = ltrim(str_replace('/storage/', '', $doc->file_path), '/');

        if ($cleanPath && Storage::disk('public')->exists($cleanPath)) {
            Storage::disk('public')->delete($cleanPath);
        }

        $doc->delete();

        return redirect()->back()->with('success', 'Berkas arsip berhasil dihapus.');
    }

    /**
     * Export completeness matrix to Excel.
     */
    public function exportExcel(Request $request)
    {
        $classroomId = $request->get('classroom_id');
        $academicYearId = $request->get('academic_year_id');

        $classroom = Classroom::find($classroomId);
        $className = $classroom ? $classroom->name : 'Semua Rombel';

        $query = Student::where('status', 'aktif')
            ->with(['classroom', 'academicYear', 'spmbCandidate', 'uploadedDocuments']);

        if ($classroomId && $classroomId !== 'all') {
            $query->where('classroom_id', $classroomId);
        }
        if ($academicYearId && $academicYearId !== 'all') {
            $query->where('academic_year_id', $academicYearId);
        }

        $students = $query->orderBy('full_name', 'asc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Arsip Dokumen Murid');

        // Header Title
        $sheet->setCellValue('A1', 'REKAPITULASI KELENGKAPAN BERKAS ARSIP MURID');
        $sheet->setCellValue('A2', strtoupper(setting('unit_name', setting('app_name', 'SD ANAK SALEH MALANG'))));
        $sheet->setCellValue('A3', "Rombel: {$className} | Dicetak: " . date('d/m/Y H:i'));

        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(9.5)->getColor()->setARGB('FF475569');

        $headers = ['No', 'NIS', 'Nama Lengkap Murid', 'L/P', 'Rombel', 'Akta Kelahiran', 'Kartu Keluarga', 'Pas Foto', 'KTP Ortu', 'Buku KIA/Imunisasi', 'Total Dokumen', 'Status Kelengkapan'];
        $startRow = 5;

        foreach ($headers as $colIdx => $h) {
            $colLetter = Coordinate::stringFromColumnIndex($colIdx + 1);
            $sheet->setCellValue($colLetter . $startRow, $h);
        }

        $lastCol = Coordinate::stringFromColumnIndex(count($headers));
        $headerRange = "A{$startRow}:{$lastCol}{$startRow}";
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFFFF'));
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0D9488'); // Teal 600
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension($startRow)->setRowHeight(24);

        $currentRow = $startRow + 1;
        $no = 1;

        foreach ($students as $s) {
            $cl = $s->document_checklist;
            $sheet->setCellValue("A{$currentRow}", $no++);
            $sheet->setCellValueExplicit("B{$currentRow}", $s->nis ?: '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue("C{$currentRow}", $s->full_name);
            $sheet->setCellValue("D{$currentRow}", $s->gender ?: '-');
            $sheet->setCellValue("E{$currentRow}", $s->classroom ? $s->classroom->name : '-');
            $sheet->setCellValue("F{$currentRow}", $cl['akta'] ? 'Ada (' . $cl['akta']['source'] . ')' : '-');
            $sheet->setCellValue("G{$currentRow}", $cl['kk'] ? 'Ada (' . $cl['kk']['source'] . ')' : '-');
            $sheet->setCellValue("H{$currentRow}", $cl['foto'] ? 'Ada' : '-');
            $sheet->setCellValue("I{$currentRow}", $cl['ktp'] ? 'Ada' : '-');
            $sheet->setCellValue("J{$currentRow}", $cl['kia'] ? 'Ada' : '-');
            $sheet->setCellValue("K{$currentRow}", $cl['total_count']);
            $sheet->setCellValue("L{$currentRow}", $cl['is_complete'] ? 'Lengkap' : 'Belum Lengkap');

            $sheet->getStyle("A{$currentRow}:B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$currentRow}:L{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            if ($cl['is_complete']) {
                $sheet->getStyle("L{$currentRow}")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF047857'))->setBold(true);
            } else {
                $sheet->getStyle("L{$currentRow}")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFBE123C'))->setBold(true);
            }

            if ($no % 2 === 0) {
                $sheet->getStyle("A{$currentRow}:{$lastCol}{$currentRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
            }

            $currentRow++;
        }

        $sheet->getStyle("A{$startRow}:{$lastCol}" . ($currentRow - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFCBD5E1'));

        foreach (range(1, count($headers)) as $colIndex) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colIndex))->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $cleanClass = str_replace(['/', '\\'], '-', $className);
        $filename = "Arsip_Dokumen_Murid_{$cleanClass}.xlsx";

        if ($request->filled('download_token')) {
            if (!headers_sent()) {
                @setcookie('download_token', $request->query('download_token'), time() + 60, '/', '', false, false);
            }
        }

        $response = response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);

        if ($request->filled('download_token')) {
            $response->headers->setCookie(cookie('download_token', $request->query('download_token'), 1, '/', null, false, false));
        }

        return $response;
    }

    /**
     * Print view of completeness matrix.
     */
    public function print(Request $request)
    {
        $classroomId = $request->get('classroom_id');
        $academicYearId = $request->get('academic_year_id');

        $classroom = Classroom::find($classroomId);
        $className = $classroom ? $classroom->name : 'Semua Rombel';

        $query = Student::where('status', 'aktif')
            ->with(['classroom', 'academicYear', 'spmbCandidate', 'uploadedDocuments']);

        if ($classroomId && $classroomId !== 'all') {
            $query->where('classroom_id', $classroomId);
        }
        if ($academicYearId && $academicYearId !== 'all') {
            $query->where('academic_year_id', $academicYearId);
        }

        $students = $query->orderBy('full_name', 'asc')->get();

        $principal = \App\Models\Employee::where(function($q) {
            $q->where('position', 'like', '%Kepala Sekolah%')
              ->orWhere('position', 'like', '%Kepsek%');
        })->first();
        if (!$principal) {
            $ksUser = \App\Models\User::where('role', 'kepala_sekolah')->with('employee')->first();
            $principal = $ksUser?->employee;
        }
        $headmasterName = $principal?->name ?? setting('headmaster_name', setting('school_principal_name', 'Andreas Setiyono, S.Pd.Gr., M.Kom.'));
        $headmasterNip = $principal?->niy ?? $principal?->nuptk ?? $principal?->nik ?? setting('headmaster_nip', setting('school_principal_nip', '-'));

        return view('admin.student-archives.print', compact(
            'students',
            'classroom',
            'className',
            'headmasterName',
            'headmasterNip'
        ));
    }
}
