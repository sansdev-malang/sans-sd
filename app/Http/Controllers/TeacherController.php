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
                  ->orWhere('nik', 'like', "%{$search}%")->orWhere('nuptk', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
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
        
        $certifiedCount = (clone $statsQuery)->whereNotNull('nuptk')->where('nuptk', '!=', '')->count();
        $certifiedPercent = $totalGuru > 0 ? round(($certifiedCount / $totalGuru) * 100) : 0;

        $teachers = $query->orderBy('name', 'asc')->paginate($request->input('per_page', 10))->withQueryString();

        return view('admin.teachers.index', compact('teachers', 'totalGuru', 'guruMale', 'guruFemale', 'certifiedPercent'));
    }

    /**
     * Show the form for creating a new teacher.
     */
    public function create()
    {
        return redirect()->route('teachers.index');
    }

    /**
     * Store a newly created teacher in storage.
     */
    public function store(Request $request)
    {
                $messages = [
            'front_title.max' => 'Gelar depan tidak boleh lebih dari 50 karakter.',
            'back_title.max' => 'Gelar belakang tidak boleh lebih dari 50 karakter.',
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Alamat email sudah terdaftar.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'gender.in' => 'Pilihan jenis kelamin tidak valid.',
            'birth_place.max' => 'Tempat lahir terlalu panjang (maks. 255 karakter).',
            'birth_date.date' => 'Format tanggal lahir tidak valid.',
            'address.max' => 'Alamat terlalu panjang (maks. 255 karakter).',
            'phone.max' => 'Nomor HP tidak boleh lebih dari 20 karakter.',
            'nik.max' => 'NIK tidak boleh lebih dari 255 karakter.',
            'niy.max' => 'NIY tidak boleh lebih dari 255 karakter.',
            'nuptk.max' => 'NUPTK tidak boleh lebih dari 255 karakter.',
            'last_education.max' => 'Pendidikan terakhir terlalu panjang.',
            'major.max' => 'Jurusan terlalu panjang.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.max' => 'Ukuran foto tidak boleh lebih dari 2MB.',
        ];
        $validated = $request->validate([
            'front_title' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'back_title' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255|unique:employees,email',
            'gender' => 'required|in:Male,Female,L,P',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'nik' => 'nullable|string|max:255',
            'niy' => 'nullable|string|max:255',
            'nuptk' => 'nullable|string|max:255',
            'no_ukg' => 'nullable|string|max:255',
            'nrg' => 'nullable|string|max:255',
            'pangkat_golongan' => 'nullable|string|max:255',
            'last_education' => 'nullable|string|max:255',
            'major' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'additional_position' => 'nullable|string|max:255',
            'task_start_date' => 'nullable|date',
            'employment_status' => 'nullable|string|max:255',
            'appointment_date' => 'nullable|date',
            'last_sk_date' => 'nullable|date',
            'last_sk_number' => 'nullable|string|max:255',
            'work_period' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'zkteco_uid' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'status' => 'required|in:Active,Leave,Inactive',
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
            'front_title.max' => 'Gelar depan tidak boleh lebih dari 50 karakter.',
            'back_title.max' => 'Gelar belakang tidak boleh lebih dari 50 karakter.',
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Alamat email sudah terdaftar.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'gender.in' => 'Pilihan jenis kelamin tidak valid.',
            'birth_place.max' => 'Tempat lahir terlalu panjang (maks. 255 karakter).',
            'birth_date.date' => 'Format tanggal lahir tidak valid.',
            'address.max' => 'Alamat terlalu panjang (maks. 255 karakter).',
            'phone.max' => 'Nomor HP tidak boleh lebih dari 20 karakter.',
            'nik.max' => 'NIK tidak boleh lebih dari 255 karakter.',
            'niy.max' => 'NIY tidak boleh lebih dari 255 karakter.',
            'nuptk.max' => 'NUPTK tidak boleh lebih dari 255 karakter.',
            'last_education.max' => 'Pendidikan terakhir terlalu panjang.',
            'major.max' => 'Jurusan terlalu panjang.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.max' => 'Ukuran foto tidak boleh lebih dari 2MB.',
        ];
        $validated = $request->validate([
            'front_title' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'back_title' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255|unique:employees,email,' . $id,
            'gender' => 'required|in:Male,Female,L,P',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'nik' => 'nullable|string|max:255',
            'niy' => 'nullable|string|max:255',
            'nuptk' => 'nullable|string|max:255',
            'no_ukg' => 'nullable|string|max:255',
            'nrg' => 'nullable|string|max:255',
            'pangkat_golongan' => 'nullable|string|max:255',
            'last_education' => 'nullable|string|max:255',
            'major' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'additional_position' => 'nullable|string|max:255',
            'task_start_date' => 'nullable|date',
            'employment_status' => 'nullable|string|max:255',
            'appointment_date' => 'nullable|date',
            'last_sk_date' => 'nullable|date',
            'last_sk_number' => 'nullable|string|max:255',
            'work_period' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'zkteco_uid' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'status' => 'required|in:Active,Leave,Inactive',
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
            'Nama Lengkap', 'Email', 'Jenis Kelamin (Male/Female)', 'Tempat Lahir', 'Tanggal Lahir (YYYY-MM-DD)',
            'NIK', 'NIY', 'NUPTK', 'No UKG', 'NRG', 'Pangkat/Golongan',
            'Pendidikan Terakhir', 'Jurusan', 'Jabatan Utama', 'Jabatan Tambahan',
            'Tanggal Mulai Tugas (YYYY-MM-DD)', 'Status Kepegawaian', 'Tanggal Pengangkatan (YYYY-MM-DD)',
            'Tanggal SK Terakhir (YYYY-MM-DD)', 'Nomor SK Terakhir', 'Masa Kerja',
            'Alamat', 'No. HP', 'Catatan Tambahan', 'ID ZKTeco (Alfanumerik)', 'Status (Active/Leave/Inactive)'
        ];

        $example = [
            'Retno Lestari, S.Pd', 'retno@sans.dev', 'Female', 'Malang', '1982-04-15',
            '3573012345678901', '123456', '198204152009042003', '2015023912', '123984', 'III/a',
            'S1', 'Pendidikan Bahasa Inggris', 'Guru Kelas', 'Wali Kelas',
            '2010-07-15', 'PNS', '2010-07-15',
            '2022-01-01', '800/123/2022', '12 Tahun',
            'Jl. Mawar No. 12', '081234567890', 'Guru tetap', '102', 'Active'
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

        // Format headers (bold text & light gray background fill)
        $headerRange = 'A1:Z1';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

        // Auto-size columns
        foreach (range(1, 26) as $colIndex) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'template_impor_guru.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
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
            $email = trim($row[1] ?? '') ? trim($row[1] ?? '') : null;
            $gender = trim($row[2] ?? '');
            $birth_place = trim($row[3] ?? '') ? trim($row[3] ?? '') : null;
            $birth_date = trim($row[4] ?? '') ? date('Y-m-d', strtotime(trim($row[4] ?? ''))) : null;
            $nik = trim($row[5] ?? '') ? trim($row[5] ?? '') : null;
            $niy = trim($row[6] ?? '') ? trim($row[6] ?? '') : null;
            $nuptk = trim($row[7] ?? '') ? trim($row[7] ?? '') : null;
            $no_ukg = trim($row[8] ?? '') ? trim($row[8] ?? '') : null;
            $nrg = trim($row[9] ?? '') ? trim($row[9] ?? '') : null;
            $pangkat_golongan = trim($row[10] ?? '') ? trim($row[10] ?? '') : null;
            $last_education = trim($row[11] ?? '') ? trim($row[11] ?? '') : null;
            $major = trim($row[12] ?? '') ? trim($row[12] ?? '') : null;
            $position = trim($row[13] ?? '') ? trim($row[13] ?? '') : null;
            $additional_position = trim($row[14] ?? '') ? trim($row[14] ?? '') : null;
            $task_start_date = trim($row[15] ?? '') ? date('Y-m-d', strtotime(trim($row[15] ?? ''))) : null;
            $employment_status = trim($row[16] ?? '') ? trim($row[16] ?? '') : null;
            $appointment_date = trim($row[17] ?? '') ? date('Y-m-d', strtotime(trim($row[17] ?? ''))) : null;
            $last_sk_date = trim($row[18] ?? '') ? date('Y-m-d', strtotime(trim($row[18] ?? ''))) : null;
            $last_sk_number = trim($row[19] ?? '') ? trim($row[19] ?? '') : null;
            $work_period = trim($row[20] ?? '') ? trim($row[20] ?? '') : null;
            $address = trim($row[21] ?? '') ? trim($row[21] ?? '') : null;
            $phone = trim($row[22] ?? '') ? trim($row[22] ?? '') : null;
            $notes = trim($row[23] ?? '') ? trim($row[23] ?? '') : null;
            $zkteco_uid = trim($row[24] ?? '') ? trim($row[24] ?? '') : null;
            $status = trim($row[25] ?? '') ? trim($row[25] ?? '') : 'Active';

            if (!$name) {
                $errors[] = "Baris " . ($index + 1) . ": Nama Lengkap wajib diisi.";
                continue;
            }

            if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                if (Employee::where('email', $email)->exists()) {
                    $errors[] = "Baris " . ($index + 1) . ": Email '{$email}' sudah terdaftar.";
                    continue;
                }
            }

            if ($nik && Employee::where('nik', $nik)->exists()) {
                $errors[] = "Baris " . ($index + 1) . ": NIK '{$nik}' sudah terdaftar.";
                continue;
            }

            if ($nuptk && Employee::where('nuptk', $nuptk)->exists()) {
                $errors[] = "Baris " . ($index + 1) . ": NUPTK '{$nuptk}' sudah terdaftar.";
                continue;
            }

            if ($zkteco_uid && Employee::where('zkteco_uid', $zkteco_uid)->exists()) {
                $errors[] = "Baris " . ($index + 1) . ": ID ZKTeco '{$zkteco_uid}' sudah terdaftar.";
                continue;
            }

            if (strtolower($gender) == 'male' || strtolower($gender) == 'laki-laki' || strtolower($gender) == 'l') {
                $gender = 'Male';
            } else {
                $gender = 'Female';
            }
            
            if (strtolower($status) == 'active') {
                $status = 'Active';
            } elseif (strtolower($status) == 'leave') {
                $status = 'Leave';
            } else {
                $status = 'Inactive';
            }

            // Create teacher
            Employee::create([
                'name' => $name,
                'email' => $email,
                'employee_type_id' => $teacherType->id,
                'unit' => config('app.school_unit') ?: 'sd',
                'gender' => $gender,
                'birth_place' => $birth_place,
                'birth_date' => $birth_date,
                'nik' => $nik,
                'niy' => $niy,
                'nuptk' => $nuptk,
                'no_ukg' => $no_ukg,
                'nrg' => $nrg,
                'pangkat_golongan' => $pangkat_golongan,
                'last_education' => $last_education,
                'major' => $major,
                'position' => $position,
                'additional_position' => $additional_position,
                'task_start_date' => $task_start_date,
                'employment_status' => $employment_status,
                'appointment_date' => $appointment_date,
                'last_sk_date' => $last_sk_date,
                'last_sk_number' => $last_sk_number,
                'work_period' => $work_period,
                'address' => $address,
                'phone' => $phone,
                'notes' => $notes,
                'zkteco_uid' => $zkteco_uid,
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
