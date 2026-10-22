<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Employee;
use App\Models\EmployeeType;
use App\Models\TeacherDocument;
use App\Services\FileOptimizerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class TeacherDocumentController extends Controller
{
    /**
     * Categories of Teacher Documents.
     */
    private array $categories = ['PROTA', 'PROSEM', 'RPP', 'RKAS', 'LPJ', 'Lainnya'];

    /**
     * Check if authenticated user has admin-level access (Super Admin, Admin PAUD, Kepala Sekolah, Waka).
     */
    private function isUserAdmin(): bool
    {
        return auth()->user() && (
            auth()->user()->isAdmin() || 
            auth()->user()->hasAnyRole(['super_admin', 'admin_sd', 'admin_paud', 'admin_smp', 'kepala_sekolah', 'waka'])
        );
    }

    /**
     * Check if authenticated user is a teacher / tenaga pendidik.
     */
    private function isTeacher(): bool
    {
        return auth()->user()?->isTeacher() ?? false;
    }

    /**
     * Get the authenticated user's associated Employee ID if any.
     */
    private function getMyEmployeeId(): ?int
    {
        return auth()->user()?->employee_id 
            ?? auth()->user()?->employee?->id 
            ?? Employee::where('email', auth()->user()?->email)->value('id');
    }

    /**
     * Display a listing of Teacher Documents (Tab 1) or Completeness Matrix (Tab 2).
     */
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'files'); // 'files' or 'matrix'

        // Check if user is Admin, Principal, or Teacher
        $isAdmin = $this->isUserAdmin();
        $isTeacher = $this->isTeacher();

        // Hanya Administrator / Kepala Sekolah atau Guru yang berhak mengakses menu ini
        if (!$isAdmin && !$isTeacher) {
            abort(403, 'Akses ditolak: Menu Berkas Guru hanya dapat diakses oleh Guru / Tenaga Pendidik dan Administrator.');
        }

        $myEmployeeId = $this->getMyEmployeeId();
        $userId = auth()->id();

        // Academic Years
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $activeAcademicYear = $academicYears->firstWhere('is_active', true) ?? $academicYears->first();
        $selectedYearId = $request->get('academic_year_id', $activeAcademicYear?->id);
        if (!$selectedYearId) {
            $selectedYearId = $activeAcademicYear?->id;
        }

        // Active Teachers
        $teacherTypeId = EmployeeType::where('code', 'teacher')->value('id');
        $teachers = Employee::where('status', 'Active')
            ->when($teacherTypeId, function ($q) use ($teacherTypeId) {
                $q->where(function ($sq) use ($teacherTypeId) {
                    $sq->where('employee_type_id', $teacherTypeId)
                       ->orWhere('position', 'like', '%guru%')
                       ->orWhere('position', 'like', '%wali%');
                });
            })
            ->orderBy('name', 'asc')
            ->get();

        if ($teachers->isEmpty()) {
            $teachers = Employee::where('status', 'Active')->orderBy('name', 'asc')->get();
        }

        // Filter variables: Guru hanya bisa melihat berkas miliknya sendiri
        $selectedTeacherId = $isAdmin ? $request->get('teacher_id', 'all') : ($myEmployeeId ?: -1);
        $selectedCategory = $request->get('category', 'all');
        $selectedSemester = $request->get('semester', 'all');
        $selectedStatus = $request->get('status', 'all');
        $search = $request->get('search');

        // Query Documents
        $query = TeacherDocument::with(['teacher', 'academicYear', 'uploadedBy', 'reviewedBy']);

        if (!$isAdmin) {
            // Guru HANYA bisa melihat berkas yang mereka buat sendiri
            $query->where(function ($q) use ($userId, $myEmployeeId) {
                $q->where('uploaded_by', $userId);
                if ($myEmployeeId) {
                    $q->orWhere('teacher_id', $myEmployeeId);
                }
            });
        } elseif ($selectedTeacherId && $selectedTeacherId !== 'all') {
            $query->where('teacher_id', $selectedTeacherId);
        }

        if ($selectedYearId && $selectedYearId !== 'all') {
            $query->where('academic_year_id', $selectedYearId);
        }

        if ($selectedCategory && $selectedCategory !== 'all') {
            $query->where('category', $selectedCategory);
        }

        if ($selectedSemester && $selectedSemester !== 'all') {
            $query->where('semester', $selectedSemester);
        }

        if ($selectedStatus && $selectedStatus !== 'all') {
            $query->where('status', $selectedStatus);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('teacher', fn($tq) => $tq->where('name', 'like', "%{$search}%"));
            });
        }

        $perPage = $request->get('per_page', 15);
        $perPageCount = ($perPage === 'all' || $perPage == 99999 || $perPage === '99999') ? 99999 : (int) $perPage;
        if ($perPageCount <= 0) {
            $perPageCount = 15;
        }

        $documents = $query->orderBy('created_at', 'desc')
            ->paginate($perPageCount)
            ->withQueryString();

        // Overall Stats for selected academic year
        $allYearQuery = TeacherDocument::when($selectedYearId && $selectedYearId !== 'all', function ($q) use ($selectedYearId) {
            $q->where('academic_year_id', $selectedYearId);
        });

        if (!$isAdmin) {
            $allYearQuery->where(function ($q) use ($userId, $myEmployeeId) {
                $q->where('uploaded_by', $userId);
                if ($myEmployeeId) {
                    $q->orWhere('teacher_id', $myEmployeeId);
                }
            });
        }

        $allYearDocs = $allYearQuery->get();

        $stats = [
            'total' => $allYearDocs->count(),
            'pending' => $allYearDocs->where('status', 'Menunggu Review')->count(),
            'approved' => $allYearDocs->where('status', 'Disetujui')->count(),
            'needs_revision' => $allYearDocs->where('status', 'Perlu Revisi')->count(),
            'teachers_count' => $allYearDocs->pluck('teacher_id')->unique()->count(),
        ];

        // Jika Guru (bukan admin), hanya tampilkan dirinya sendiri pada opsi guru / matriks
        if (!$isAdmin) {
            $teachers = $teachers->where('id', $myEmployeeId);
        }

        // Completeness Matrix Data for Tab 2
        $matrixData = [];
        foreach ($teachers as $teacher) {
            $teacherDocs = $allYearDocs->where('teacher_id', $teacher->id);
            $categoriesStatus = [];

            foreach ($this->categories as $cat) {
                $docsInCat = $teacherDocs->where('category', $cat);
                $categoriesStatus[$cat] = [
                    'count' => $docsInCat->count(),
                    'approved' => $docsInCat->where('status', 'Disetujui')->count(),
                    'pending' => $docsInCat->where('status', 'Menunggu Review')->count(),
                    'needs_revision' => $docsInCat->where('status', 'Perlu Revisi')->count(),
                    'latest' => $docsInCat->sortByDesc('created_at')->first(),
                ];
            }

            $matrixData[] = [
                'teacher' => $teacher,
                'total_uploaded' => $teacherDocs->count(),
                'categories' => $categoriesStatus,
            ];
        }

        $categories = $this->categories;
        $semesters = ['Tahunan', 'Ganjil', 'Genap'];

        return view('admin.teacher-documents.index', compact(
            'documents',
            'matrixData',
            'activeTab',
            'academicYears',
            'activeAcademicYear',
            'selectedYearId',
            'teachers',
            'selectedTeacherId',
            'selectedCategory',
            'selectedSemester',
            'selectedStatus',
            'search',
            'stats',
            'categories',
            'semesters',
            'isAdmin',
            'myEmployeeId',
            'perPage'
        ));
    }

    /**
     * Store newly uploaded Teacher Document (1 berkas record can contain multiple attached documents).
     */
    public function store(Request $request)
    {
        $isAdmin = $this->isUserAdmin();
        $isTeacher = $this->isTeacher();

        if (!$isAdmin && !$isTeacher) {
            abort(403, 'Akses ditolak: Hanya Guru atau Administrator yang dapat mengunggah berkas.');
        }

        $myEmployeeId = $this->getMyEmployeeId();

        $hasMultiDocs = $request->has('documents') && is_array($request->input('documents'));

        $rules = [
            'teacher_id' => $isAdmin ? 'required|exists:employees,id' : 'nullable|exists:employees,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'semester' => 'required|in:Tahunan,Ganjil,Genap',
            'category' => 'required|in:RKAS,LPJ,PROTA,PROSEM,RPP,Lainnya',
            'title' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ];

        if ($hasMultiDocs) {
            $rules['documents'] = 'required|array|min:1';
            $rules['documents.*.title'] = 'nullable|string|max:255';
            $rules['documents.*.file'] = 'nullable|file|max:25600|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,jpg,jpeg,png';
        } else {
            $rules['file'] = 'nullable|file|max:25600|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,jpg,jpeg,png';
        }

        $validated = $request->validate($rules);

        $teacherId = $isAdmin ? $validated['teacher_id'] : $myEmployeeId;
        if (!$teacherId) {
            return redirect()->back()->with('error', 'Akun Anda belum terhubung dengan data pegawai/guru.');
        }

        $storedFiles = [];
        $firstTitle = '';

        if ($hasMultiDocs) {
            foreach ($request->input('documents', []) as $index => $docData) {
                $file = $request->file("documents.{$index}.file");
                if (!$file) {
                    continue;
                }

                $originalName = $file->getClientOriginalName();
                $optimizer = app(FileOptimizerService::class);
                $path = $optimizer->optimizeAndStoreRelative($file, 'teacher-documents');
                $fullPath = storage_path('app/public/' . $path);
                $size = file_exists($fullPath) ? filesize($fullPath) : $file->getSize();
                $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

                $rawTitle = $docData['title'] ?? null;
                $itemTitle = !empty(trim($rawTitle ?? '')) 
                    ? trim($rawTitle) 
                    : pathinfo($originalName, PATHINFO_FILENAME);

                if (empty($firstTitle)) {
                    $firstTitle = $itemTitle;
                }

                $storedFiles[] = [
                    'title' => $itemTitle,
                    'file_path' => $path,
                    'file_name' => $originalName,
                    'file_size' => $size,
                    'file_extension' => $ext,
                ];
            }
        } elseif ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $optimizer = app(FileOptimizerService::class);
            $path = $optimizer->optimizeAndStoreRelative($file, 'teacher-documents');
            $fullPath = storage_path('app/public/' . $path);
            $size = file_exists($fullPath) ? filesize($fullPath) : $file->getSize();
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $itemTitle = !empty(trim($validated['title'] ?? '')) 
                ? trim($validated['title']) 
                : pathinfo($originalName, PATHINFO_FILENAME);
            $firstTitle = $itemTitle;

            $storedFiles[] = [
                'title' => $itemTitle,
                'file_path' => $path,
                'file_name' => $originalName,
                'file_size' => $size,
                'file_extension' => $ext,
            ];
        }

        if (empty($storedFiles)) {
            return redirect()->back()->with('error', 'Harap lampirkan minimal 1 file dokumen yang valid.');
        }

        // Main Berkas title
        $mainTitle = !empty(trim($request->input('title', ''))) 
            ? trim($request->input('title')) 
            : $firstTitle;

        $firstFile = $storedFiles[0];

        // SIMPAN HANYA 1 DATA BERKAS (bukan buat baris baru untuk setiap dokumen)
        TeacherDocument::create([
            'teacher_id' => $teacherId,
            'academic_year_id' => $validated['academic_year_id'] ?? null,
            'semester' => $validated['semester'],
            'category' => $validated['category'],
            'title' => $mainTitle,
            'files' => $storedFiles,
            'file_path' => $firstFile['file_path'],
            'file_name' => $firstFile['file_name'],
            'file_size' => $firstFile['file_size'],
            'file_extension' => $firstFile['file_extension'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'Menunggu Review',
            'uploaded_by' => auth()->id(),
        ]);

        $fileCount = count($storedFiles);
        $msg = $fileCount > 1 
            ? "1 berkas \"{$mainTitle}\" dengan {$fileCount} dokumen berhasil diunggah."
            : "Berkas \"{$mainTitle}\" berhasil diunggah.";

        return redirect()->route('teacher-documents.index', [
            'academic_year_id' => $validated['academic_year_id'] ?? null,
            'category' => $validated['category'],
        ])->with('success', $msg);
    }

    /**
     * Update existing document metadata, manage documents (add, edit title/file, remove).
     */
    public function update(Request $request, $id)
    {
        $doc = TeacherDocument::findOrFail($id);
        $isAdmin = $this->isUserAdmin();
        $myEmployeeId = $this->getMyEmployeeId();

        // Guru hanya boleh mengedit berkas miliknya sendiri
        if (!$isAdmin && $doc->uploaded_by != auth()->id() && $doc->teacher_id != $myEmployeeId) {
            abort(403, 'Akses ditolak: Anda hanya dapat mengubah berkas yang Anda buat sendiri.');
        }

        $rules = [
            'teacher_id' => $isAdmin ? 'required|exists:employees,id' : 'nullable|exists:employees,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'semester' => 'required|in:Tahunan,Ganjil,Genap',
            'category' => 'required|in:RKAS,LPJ,PROTA,PROSEM,RPP,Lainnya',
            'title' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ];

        $hasMultiDocs = $request->has('documents') && is_array($request->input('documents'));
        if ($hasMultiDocs) {
            $rules['documents'] = 'required|array|min:1';
            $rules['documents.*.title'] = 'nullable|string|max:255';
            $rules['documents.*.file'] = 'nullable|file|max:25600|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,jpg,jpeg,png';
            $rules['documents.*.existing_path'] = 'nullable|string';
        } else {
            $rules['file'] = 'nullable|file|max:25600|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,jpg,jpeg,png';
        }

        $validated = $request->validate($rules);

        // Previous stored paths in this document
        $oldPaths = array_filter(array_column($doc->files_list, 'file_path'));

        $newStoredFiles = [];
        $keptPaths = [];
        $hasNewUpload = false;

        if ($hasMultiDocs) {
            foreach ($request->input('documents', []) as $index => $docData) {
                $hasFile = $request->hasFile("documents.{$index}.file");
                $existingPath = $docData['existing_path'] ?? null;
                $rawTitle = $docData['title'] ?? null;

                if ($hasFile) {
                    // New or replaced file uploaded
                    $file = $request->file("documents.{$index}.file");
                    $originalName = $file->getClientOriginalName();
                    $optimizer = app(FileOptimizerService::class);
                    $path = $optimizer->optimizeAndStoreRelative($file, 'teacher-documents');
                    $fullPath = storage_path('app/public/' . $path);
                    $size = file_exists($fullPath) ? filesize($fullPath) : $file->getSize();
                    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                    $hasNewUpload = true;

                    $itemTitle = !empty(trim($rawTitle ?? '')) 
                        ? trim($rawTitle) 
                        : pathinfo($originalName, PATHINFO_FILENAME);

                    $newStoredFiles[] = [
                        'title' => $itemTitle,
                        'file_path' => $path,
                        'file_name' => $originalName,
                        'file_size' => $size,
                        'file_extension' => $ext,
                    ];
                } elseif (!empty($existingPath)) {
                    // Retain existing file, possibly with updated title
                    $itemTitle = !empty(trim($rawTitle ?? '')) 
                        ? trim($rawTitle) 
                        : ($docData['existing_name'] ?? basename($existingPath));

                    $newStoredFiles[] = [
                        'title' => $itemTitle,
                        'file_path' => $existingPath,
                        'file_name' => $docData['existing_name'] ?? basename($existingPath),
                        'file_size' => (int) ($docData['existing_size'] ?? 0),
                        'file_extension' => $docData['existing_ext'] ?? pathinfo($existingPath, PATHINFO_EXTENSION),
                    ];

                    $keptPaths[] = $existingPath;
                }
            }
        } elseif ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $optimizer = app(FileOptimizerService::class);
            $newPath = $optimizer->optimizeAndStoreRelative($file, 'teacher-documents');
            $fullPath = storage_path('app/public/' . $newPath);
            $size = file_exists($fullPath) ? filesize($fullPath) : $file->getSize();
            $ext = strtolower(pathinfo($newPath, PATHINFO_EXTENSION));
            $hasNewUpload = true;

            $newStoredFiles[] = [
                'title' => $validated['title'],
                'file_path' => $newPath,
                'file_name' => $originalName,
                'file_size' => $size,
                'file_extension' => $ext,
            ];
        } else {
            $newStoredFiles = $doc->files_list;
            $keptPaths = $oldPaths;
        }

        if (empty($newStoredFiles)) {
            return redirect()->back()->with('error', 'Berkas harus memiliki minimal 1 file dokumen.');
        }

        // Delete removed old files from disk
        foreach ($oldPaths as $oldPath) {
            if (!in_array($oldPath, $keptPaths)) {
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
        }

        $teacherId = $isAdmin ? $validated['teacher_id'] : $doc->teacher_id;
        $firstFile = $newStoredFiles[0];

        $updateData = [
            'teacher_id' => $teacherId,
            'academic_year_id' => $validated['academic_year_id'] ?? null,
            'semester' => $validated['semester'],
            'category' => $validated['category'],
            'title' => $validated['title'],
            'notes' => $validated['notes'] ?? null,
            'files' => $newStoredFiles,
            'file_path' => $firstFile['file_path'],
            'file_name' => $firstFile['file_name'],
            'file_size' => $firstFile['file_size'],
            'file_extension' => $firstFile['file_extension'],
        ];

        // Reset review status ke 'Menunggu Review' jika berkas sebelumnya berstatus 'Perlu Revisi'
        // atau jika terdapat unggahan / perubahan file dokumen baru.
        $wasNeedsRevision = ($doc->status === 'Perlu Revisi');
        if ($wasNeedsRevision || $hasNewUpload) {
            $updateData['status'] = 'Menunggu Review';
        }

        $doc->update($updateData);

        $msg = $wasNeedsRevision
            ? "Revisi berkas \"{$doc->title}\" berhasil disimpan dan status dikembalikan ke Menunggu Review."
            : "Data berkas \"{$doc->title}\" berhasil diperbarui.";

        return redirect()->route('teacher-documents.index', [
            'academic_year_id' => $doc->academic_year_id,
            'category' => $doc->category,
        ])->with('success', $msg);
    }

    /**
     * Update Document Review Status (Approved / Needs Revision).
     */
    public function updateStatus(Request $request, $id)
    {
        $isAdmin = $this->isUserAdmin();
        if (!$isAdmin) {
            abort(403, 'Akses ditolak: Hanya Administrator atau Kepala Sekolah yang dapat melakukan supervisi berkas.');
        }

        $doc = TeacherDocument::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:Menunggu Review,Disetujui,Perlu Revisi',
            'feedback' => 'nullable|string',
        ]);

        $doc->update([
            'status' => $validated['status'],
            'feedback' => $validated['feedback'] ?? null,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', "Status berkas \"{$doc->title}\" berhasil diubah menjadi {$validated['status']}.");
    }

    /**
     * Download the document file (or a specific file by index).
     */
    public function download(Request $request, $id)
    {
        $doc = TeacherDocument::findOrFail($id);
        $isAdmin = $this->isUserAdmin();
        $myEmployeeId = $this->getMyEmployeeId();

        // Guru hanya boleh mengunduh berkas miliknya sendiri
        if (!$isAdmin && $doc->uploaded_by != auth()->id() && $doc->teacher_id != $myEmployeeId) {
            abort(403, 'Akses ditolak: Anda hanya dapat mengunduh berkas yang Anda buat sendiri.');
        }

        $fileIndex = $request->query('file');
        $filesList = $doc->files_list;

        if ($fileIndex !== null && is_numeric($fileIndex) && isset($filesList[(int)$fileIndex])) {
            $target = $filesList[(int)$fileIndex];
            $path = $target['file_path'] ?? '';
            $name = $target['file_name'] ?? basename($path);
        } else {
            $target = $filesList[0] ?? null;
            $path = $target ? ($target['file_path'] ?? '') : $doc->file_path;
            $name = $target ? ($target['file_name'] ?? basename($path)) : ($doc->file_name ?? basename($path));
        }

        $cleanPath = ltrim(str_replace('/storage/', '', $path), '/');

        if (!$cleanPath || !Storage::disk('public')->exists($cleanPath)) {
            abort(404, 'File berkas tidak ditemukan di server penyimpanan.');
        }

        if ($request->filled('download_token')) {
            if (!headers_sent()) {
                @setcookie('download_token', $request->query('download_token'), time() + 60, '/', '', false, false);
            }
        }

        $response = Storage::disk('public')->download($cleanPath, $name);

        if ($request->filled('download_token')) {
            $response->headers->setCookie(cookie('download_token', $request->query('download_token'), 1, '/', null, false, false));
        }

        return $response;
    }

    /**
     * Delete document and all its attached physical files.
     */
    public function destroy($id)
    {
        $doc = TeacherDocument::findOrFail($id);
        $isAdmin = $this->isUserAdmin();
        $myEmployeeId = $this->getMyEmployeeId();

        // Guru hanya boleh menghapus berkas miliknya sendiri
        if (!$isAdmin && $doc->uploaded_by != auth()->id() && $doc->teacher_id != $myEmployeeId) {
            abort(403, 'Akses ditolak: Anda hanya dapat menghapus berkas yang Anda buat sendiri.');
        }

        // Delete all attached files in storage
        foreach ($doc->files_list as $f) {
            $fp = ltrim(str_replace('/storage/', '', $f['file_path'] ?? ''), '/');
            if (!empty($fp) && Storage::disk('public')->exists($fp)) {
                Storage::disk('public')->delete($fp);
            }
        }

        $mainFp = ltrim(str_replace('/storage/', '', $doc->file_path ?? ''), '/');
        if ($mainFp && Storage::disk('public')->exists($mainFp)) {
            Storage::disk('public')->delete($mainFp);
        }

        $doc->delete();

        return redirect()->back()->with('success', 'Catatan dan file berkas guru berhasil dihapus.');
    }

    /**
     * Export Completeness Matrix to Excel.
     */
    public function exportMatrixExcel(Request $request)
    {
        if (!$this->isUserAdmin()) {
            abort(403, 'Akses ditolak: Hanya Administrator atau Kepala Sekolah yang dapat mengekspor matriks.');
        }

        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $activeAcademicYear = $academicYears->firstWhere('is_active', true) ?? $academicYears->first();
        $selectedYearId = $request->get('academic_year_id', $activeAcademicYear?->id);

        $selectedYear = AcademicYear::find($selectedYearId);
        $yearName = $selectedYear ? $selectedYear->name : 'Aktif';

        $teacherTypeId = EmployeeType::where('code', 'teacher')->value('id');
        $teachers = Employee::where('status', 'Active')
            ->when($teacherTypeId, function ($q) use ($teacherTypeId) {
                $q->where(function ($sq) use ($teacherTypeId) {
                    $sq->where('employee_type_id', $teacherTypeId)
                       ->orWhere('position', 'like', '%guru%')
                       ->orWhere('position', 'like', '%wali%');
                });
            })
            ->orderBy('name', 'asc')
            ->get();

        if ($teachers->isEmpty()) {
            $teachers = Employee::where('status', 'Active')->orderBy('name', 'asc')->get();
        }

        $allYearDocs = TeacherDocument::when($selectedYearId, function ($q) use ($selectedYearId) {
            $q->where('academic_year_id', $selectedYearId);
        })->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Matriks Berkas Guru');

        // Header Title
        $sheet->setCellValue('A1', 'MATRIKS KELENGKAPAN BERKAS & ADMINISTRASI GURU');
        $sheet->setCellValue('A2', strtoupper(setting('app_name', 'PAUD ANAK SALEH MALANG')));
        $sheet->setCellValue('A3', "Tahun Ajaran: {$yearName} | Tanggal Cetak: " . date('d/m/Y H:i'));

        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(9.5)->getColor()->setARGB('FF475569');

        // Headers
        $headers = ['No', 'Nama Guru / Pendidik', 'NIP / NIY', 'Jabatan', 'PROTA', 'PROSEM', 'RPP', 'RKAS', 'LPJ', 'Lainnya', 'Total Berkas'];
        $startRow = 5;

        foreach ($headers as $colIdx => $h) {
            $colLetter = Coordinate::stringFromColumnIndex($colIdx + 1);
            $sheet->setCellValue($colLetter . $startRow, $h);
        }

        $lastCol = Coordinate::stringFromColumnIndex(count($headers));
        $headerRange = "A{$startRow}:{$lastCol}{$startRow}";
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFFFF'));
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF4F46E5'); // Indigo 600
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension($startRow)->setRowHeight(24);

        $currentRow = $startRow + 1;
        $no = 1;

        foreach ($teachers as $t) {
            $teacherDocs = $allYearDocs->where('teacher_id', $t->id);
            $sheet->setCellValue("A{$currentRow}", $no++);
            $sheet->setCellValue("B{$currentRow}", $t->name);
            $sheet->setCellValueExplicit("C{$currentRow}", $t->niy ?: ($t->nik ?: '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue("D{$currentRow}", $t->position ?: 'Guru');

            $colIndex = 5;
            foreach ($this->categories as $cat) {
                $count = $teacherDocs->where('category', $cat)->count();
                $colLetter = Coordinate::stringFromColumnIndex($colIndex++);
                $statusText = $count > 0 ? "Ada ({$count})" : "-";
                $sheet->setCellValue("{$colLetter}{$currentRow}", $statusText);
                $sheet->getStyle("{$colLetter}{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            // Total
            $totCol = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue("{$totCol}{$currentRow}", $teacherDocs->count());
            $sheet->getStyle("{$totCol}{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("{$totCol}{$currentRow}")->getFont()->setBold(true);

            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            if ($no % 2 === 0) {
                $sheet->getStyle("A{$currentRow}:{$lastCol}{$currentRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
            }

            $currentRow++;
        }

        // Borders
        $sheet->getStyle("A{$startRow}:{$lastCol}" . ($currentRow - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFCBD5E1'));

        foreach (range(1, count($headers)) as $colIndex) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colIndex))->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $cleanYear = str_replace(['/', '\\'], '-', $yearName);
        $filename = "Matriks_Berkas_Guru_{$cleanYear}.xlsx";

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
     * Printable view of Completeness Matrix.
     */
    public function printMatrix(Request $request)
    {
        if (!$this->isUserAdmin()) {
            abort(403, 'Akses ditolak: Hanya Administrator atau Kepala Sekolah yang dapat mencetak matriks.');
        }

        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $activeAcademicYear = $academicYears->firstWhere('is_active', true) ?? $academicYears->first();
        $selectedYearId = $request->get('academic_year_id', $activeAcademicYear?->id);

        $selectedYear = AcademicYear::find($selectedYearId);
        $yearName = $selectedYear ? $selectedYear->name : 'Aktif';

        $teacherTypeId = EmployeeType::where('code', 'teacher')->value('id');
        $teachers = Employee::where('status', 'Active')
            ->when($teacherTypeId, function ($q) use ($teacherTypeId) {
                $q->where(function ($sq) use ($teacherTypeId) {
                    $sq->where('employee_type_id', $teacherTypeId)
                       ->orWhere('position', 'like', '%guru%')
                       ->orWhere('position', 'like', '%wali%');
                });
            })
            ->orderBy('name', 'asc')
            ->get();

        if ($teachers->isEmpty()) {
            $teachers = Employee::where('status', 'Active')->orderBy('name', 'asc')->get();
        }

        $allYearDocs = TeacherDocument::when($selectedYearId, function ($q) use ($selectedYearId) {
            $q->where('academic_year_id', $selectedYearId);
        })->get();

        $matrixData = [];
        foreach ($teachers as $teacher) {
            $teacherDocs = $allYearDocs->where('teacher_id', $teacher->id);
            $categoriesStatus = [];

            foreach ($this->categories as $cat) {
                $docsInCat = $teacherDocs->where('category', $cat);
                $categoriesStatus[$cat] = [
                    'count' => $docsInCat->count(),
                    'approved' => $docsInCat->where('status', 'Disetujui')->count(),
                    'pending' => $docsInCat->where('status', 'Menunggu Review')->count(),
                    'needs_revision' => $docsInCat->where('status', 'Perlu Revisi')->count(),
                ];
            }

            $matrixData[] = [
                'teacher' => $teacher,
                'total_uploaded' => $teacherDocs->count(),
                'categories' => $categoriesStatus,
            ];
        }

        $categories = $this->categories;

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

        return view('admin.teacher-documents.print-matrix', compact(
            'matrixData',
            'categories',
            'yearName',
            'selectedYear',
            'headmasterName',
            'headmasterNip'
        ));
    }
}
