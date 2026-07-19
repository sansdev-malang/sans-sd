<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class TeacherController extends Controller
{
    /**
     * Get the teacher type model.
     */
    private function getTeacherType()
    {
        return EmployeeType::firstOrCreate(
            ['code' => 'teacher'],
            ['name' => 'Guru', 'description' => 'Tenaga Pendidik']
        );
    }

    /**
     * Display a listing of teachers.
     */
    public function index(Request $request)
    {
        $teacherType = $this->getTeacherType();
        $query = Employee::where('employee_type_id', $teacherType->id);

        // Filter by school unit if configured
        $schoolUnit = config('app.school_unit');
        if ($schoolUnit) {
            $query->where('unit', $schoolUnit);
        }

        // Apply Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nuptk_nip_nik', 'like', "%{$search}%")
                  ->orWhere('subject_position', 'like', "%{$search}%");
            });
        }

        // Apply Status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Calculate statistics
        $statsQuery = Employee::where('employee_type_id', $teacherType->id);
        if ($schoolUnit) {
            $statsQuery->where('unit', $schoolUnit);
        }

        $totalGuru = (clone $statsQuery)->count();
        $guruMale = (clone $statsQuery)->where('gender', 'Male')->count();
        $guruFemale = (clone $statsQuery)->where('gender', 'Female')->count();
        
        $certifiedCount = (clone $statsQuery)->whereNotNull('nuptk_nip_nik')->where('nuptk_nip_nik', '!=', '')->count();
        $certifiedPercent = $totalGuru > 0 ? round(($certifiedCount / $totalGuru) * 100) : 0;

        $teachers = $query->latest()->paginate(10);

        return view('admin.teachers.index', compact('teachers', 'totalGuru', 'guruMale', 'guruFemale', 'certifiedPercent'));
    }

    /**
     * Show the form for creating a new teacher.
     */
    public function create()
    {
        return view('admin.teachers.create');
    }

    /**
     * Store a newly created teacher in storage.
     */
    public function store(Request $request)
    {
        $messages = [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email sudah digunakan oleh pegawai/guru lain.',
            'nuptk_nip_nik.unique' => 'NIP / NUPTK / NIK sudah digunakan oleh pegawai/guru lain.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'employment_status.required' => 'Status kepegawaian wajib dipilih.',
            'zkteco_uid.unique' => 'ID ZKTeco / PIN Fingerprint sudah digunakan oleh pegawai/guru lain.',
            'status.required' => 'Status keaktifan wajib dipilih.',
        ];

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'nuptk_nip_nik' => 'nullable|string|unique:employees,nuptk_nip_nik',
            'subject_position' => 'nullable|string|max:255',
            'gender' => 'required|string|in:Male,Female',
            'employment_status' => 'required|string|max:255',
            'zkteco_uid' => 'nullable|string|unique:employees,zkteco_uid',
            'status' => 'required|string|in:Active,Inactive,Leave',
            'photo' => 'nullable|image|max:2048',
        ], $messages);

        // Handle file upload
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('photos', $filename, 'public');
            $validated['photo'] = $path;
        }

        $teacherType = $this->getTeacherType();
        $validated['employee_type_id'] = $teacherType->id;
        $validated['unit'] = config('app.school_unit') ?: 'sd';

        Employee::create($validated);

        return redirect()->route('teachers.index')->with('success', 'Berhasil menambahkan data guru baru!');
    }

    /**
     * Show the form for editing the specified teacher.
     */
    public function edit($id)
    {
        $teacherType = $this->getTeacherType();
        $teacher = Employee::where('employee_type_id', $teacherType->id)->findOrFail($id);
        return view('admin.teachers.edit', compact('teacher'));
    }

    /**
     * Update the specified teacher in storage.
     */
    public function update(Request $request, $id)
    {
        $teacherType = $this->getTeacherType();
        $teacher = Employee::where('employee_type_id', $teacherType->id)->findOrFail($id);

        $messages = [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email sudah digunakan oleh pegawai/guru lain.',
            'nuptk_nip_nik.unique' => 'NIP / NUPTK / NIK sudah digunakan oleh pegawai/guru lain.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'employment_status.required' => 'Status kepegawaian wajib dipilih.',
            'zkteco_uid.unique' => 'ID ZKTeco / PIN Fingerprint sudah digunakan oleh pegawai/guru lain.',
            'status.required' => 'Status keaktifan wajib dipilih.',
        ];

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $id,
            'nuptk_nip_nik' => 'nullable|string|unique:employees,nuptk_nip_nik,' . $id,
            'subject_position' => 'nullable|string|max:255',
            'gender' => 'required|string|in:Male,Female',
            'employment_status' => 'required|string|max:255',
            'zkteco_uid' => 'nullable|string|unique:employees,zkteco_uid,' . $id,
            'status' => 'required|string|in:Active,Inactive,Leave',
            'photo' => 'nullable|image|max:2048',
        ], $messages);

        // Handle file upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($teacher->photo) {
                $oldPath = str_contains($teacher->photo, 'photos/') ? $teacher->photo : 'photos/' . $teacher->photo;
                Storage::disk('public')->delete($oldPath);
            }

            $file = $request->file('photo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('photos', $filename, 'public');
            $validated['photo'] = $path;
        }

        $teacher->update($validated);

        return redirect()->route('teachers.index')->with('success', 'Berhasil memperbarui data guru!');
    }

    /**
     * Remove the specified teacher from storage.
     */
    public function destroy($id)
    {
        $teacherType = $this->getTeacherType();
        $teacher = Employee::where('employee_type_id', $teacherType->id)->findOrFail($id);

        // Delete photo file if exists
        if ($teacher->photo) {
            $oldPath = str_contains($teacher->photo, 'photos/') ? $teacher->photo : 'photos/' . $teacher->photo;
            Storage::disk('public')->delete($oldPath);
        }

        $teacher->delete();

        return redirect()->route('teachers.index')->with('success', 'Berhasil menghapus data guru!');
    }

    /**
     * Download Excel template for teachers.
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Title and columns
        $headers = [
            'Nama Lengkap',
            'Email',
            'NUPTK/NIP/NIK',
            'Mata Pelajaran',
            'Jenis Kelamin (Male/Female)',
            'Status Kepegawaian',
            'ID ZKTeco (Alfanumerik)',
            'Status (Active/Leave/Inactive)'
        ];

        foreach ($headers as $colIdx => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
            $sheet->setCellValue($colLetter . '1', $header);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // Example row
        $sheet->setCellValue('A2', 'Retno Lestari, S.Pd');
        $sheet->setCellValue('B2', 'retno.lestari@sans.dev');
        $sheet->setCellValue('C2', '198204152009042003');
        $sheet->setCellValue('D2', 'Bahasa Inggris');
        $sheet->setCellValue('E2', 'Female');
        $sheet->setCellValue('F2', 'PNS');
        $sheet->setCellValue('G2', '102');
        $sheet->setCellValue('H2', 'Active');

        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="template_impor_guru.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Import teachers from Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'file.mimes' => 'Format file wajib berupa .xlsx atau .xls.'
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $errors = [];
        $importedCount = 0;
        $teacherType = $this->getTeacherType();

        // Process data starting from row index 1 (skip header)
        for ($index = 1; $index < count($rows); $index++) {
            $row = $rows[$index];
            if (empty(array_filter($row))) {
                continue; // Skip empty rows
            }

            $name = trim($row[0] ?? '');
            $email = trim($row[1] ?? '');
            $nuptk_nip_nik = trim($row[2] ?? '');
            $subject_position = trim($row[3] ?? '');
            $gender = trim($row[4] ?? '');
            $employment_status = trim($row[5] ?? '');
            $zkteco_uid = trim($row[6] ?? '');
            $status = trim($row[7] ?? '');

            // Fallback default status
            if (!$status) {
                $status = 'Active';
            }

            // Normalise status
            if (strtolower($status) == 'active') {
                $status = 'Active';
            } elseif (strtolower($status) == 'leave') {
                $status = 'Leave';
            } else {
                $status = 'Inactive';
            }

            // Validation
            if (!$name) {
                $errors[] = "Baris " . ($index + 1) . ": Nama Lengkap wajib diisi.";
                continue;
            }

            if (!$email) {
                $errors[] = "Baris " . ($index + 1) . ": Alamat Email wajib diisi.";
                continue;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Baris " . ($index + 1) . ": Format email '{$email}' tidak valid.";
                continue;
            }

            // Email uniqueness
            if (Employee::where('email', $email)->exists()) {
                $errors[] = "Baris " . ($index + 1) . ": Email '{$email}' sudah terdaftar di database.";
                continue;
            }

            // NUPTK uniqueness
            if ($nuptk_nip_nik && Employee::where('nuptk_nip_nik', $nuptk_nip_nik)->exists()) {
                $errors[] = "Baris " . ($index + 1) . ": NIP/NUPTK/NIK '{$nuptk_nip_nik}' sudah terdaftar.";
                continue;
            }

            // ZKTeco UID uniqueness
            if ($zkteco_uid && Employee::where('zkteco_uid', $zkteco_uid)->exists()) {
                $errors[] = "Baris " . ($index + 1) . ": ID ZKTeco '{$zkteco_uid}' sudah terdaftar.";
                continue;
            }

            // Normalise gender
            if (strtolower($gender) == 'male' || strtolower($gender) == 'laki-laki' || strtolower($gender) == 'l') {
                $gender = 'Male';
            } else {
                $gender = 'Female';
            }

            // Create teacher
            Employee::create([
                'name' => $name,
                'email' => $email,
                'nuptk_nip_nik' => $nuptk_nip_nik,
                'employee_type_id' => $teacherType->id,
                'unit' => config('app.school_unit') ?: 'sd',
                'subject_position' => $subject_position,
                'gender' => $gender,
                'employment_status' => $employment_status,
                'zkteco_uid' => $zkteco_uid ?: null,
                'status' => $status,
            ]);

            $importedCount++;
        }

        if (count($errors) > 0) {
            return redirect()->route('teachers.index')
                ->with('success', "Impor selesai. Berhasil mengimpor {$importedCount} data guru.")
                ->with('import_errors', $errors);
        }

        return redirect()->route('teachers.index')->with('success', "Berhasil mengimpor {$importedCount} data guru!");
    }
}
