<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Employee::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")->orWhere('nuptk', 'like', "%{$search}%");
            });
        }

        // Type filter (employee_type_id or code)
        if ($request->filled('type')) {
            $typeVal = $request->input('type');
            if (is_numeric($typeVal)) {
                $query->where('employee_type_id', $typeVal);
            } else {
                $query->whereHas('employeeType', function ($q) use ($typeVal) {
                    $q->where('code', $typeVal)->orWhere('name', $typeVal);
                });
            }
        }

        // Unit filter (paud / sd / smp) or default school unit config
        $schoolUnit = config('app.school_unit');
        if ($schoolUnit) {
            $query->where('unit', $schoolUnit);
        } elseif ($request->filled('unit')) {
            $query->where('unit', $request->input('unit'));
        }

        // Status filter (Active / Leave / Inactive)
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $employees = $query->with('employeeType')->orderBy('name', 'asc')->get();
        $employeeTypes = \App\Models\EmployeeType::all();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $employees,
            ]);
        }

        return view('admin.employees.index', compact('employees', 'employeeTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
        public function create()
    {
        $employeeTypes = \App\Models\EmployeeType::all();
        return view('admin.employees.create', compact('employeeTypes'));
    }

    public function edit(Employee $employee)
    {
        $employeeTypes = \App\Models\EmployeeType::all();
        return view('admin.employees.edit', compact('employee', 'employeeTypes'));
    }

    public function store(Request $request)
    {
        $schoolUnit = config('app.school_unit');
        if ($schoolUnit) {
            $request->merge(['unit' => $schoolUnit]);
        }

                $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
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
            'photo' => 'nullable|image|max:1024',
            'status' => 'required|in:Active,Leave,Inactive',
            'employee_type_id' => 'required|exists:employee_types,id',
        ]);

        if ($request->hasFile('photo')) {
            $manager = new ImageManager(new Driver());
            $image = $manager->decode($request->file('photo'));
            $image->scaleDown(width: 800);
            
            $filename = 'photos/' . uniqid() . '.webp';
            $fullPath = storage_path('app/public/' . $filename);
            
            if (!file_exists(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
            }
            
            $image->save($fullPath, 80);
            $validated['photo'] = $filename;
        }

        $employee = Employee::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Employee created successfully.',
                'data' => $employee,
            ], 201);
        }

        return redirect()->route('employees.index')->with('success', 'Pegawai baru berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        return response()->json([
            'success' => true,
            'data' => $employee->load('attendances'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $schoolUnit = config('app.school_unit');
        if ($schoolUnit) {
            $request->merge(['unit' => $schoolUnit]);
        }

                $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
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
            'photo' => 'nullable|image|max:1024',
            'status' => 'required|in:Active,Leave,Inactive',
            'employee_type_id' => 'required|exists:employee_types,id',
        ]);

        if ($request->hasFile('photo')) {
            if ($employee->photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($employee->photo);
            }
            $manager = new ImageManager(new Driver());
            $image = $manager->decode($request->file('photo'));
            $image->scaleDown(width: 800);
            
            $filename = 'photos/' . uniqid() . '.webp';
            $fullPath = storage_path('app/public/' . $filename);
            
            if (!file_exists(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
            }
            
            $image->save($fullPath, 80);
            $validated['photo'] = $filename;
        }

        $employee->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Employee updated successfully.',
                'data' => $employee,
            ]);
        }

        return redirect()->route('employees.index')->with('success', 'Data pegawai berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Employee deleted successfully.',
            ]);
        }

        return redirect()->route('employees.index')->with('success', 'Data pegawai berhasil dihapus!');
    }

    /**
     * Download XLSX format template for employees import.
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = [
            'Nama Lengkap',
            'Email',
            'Kode Tipe Pegawai',
            'Unit Sekolah (paud/sd/smp)',
            'Jenis Kelamin (Male/Female)',
            'Tempat Lahir',
            'Tanggal Lahir (YYYY-MM-DD)',
            'NIK',
            'NIY',
            'NUPTK',
            'NO UKG',
            'NRG',
            'Pangkat / Golongan',
            'Pendidikan Terakhir',
            'Jurusan',
            'Jabatan Utama',
            'Jabatan Tambahan',
            'Tanggal Mulai Tugas (YYYY-MM-DD)',
            'Status Kepegawaian',
            'Tanggal Diangkat (YYYY-MM-DD)',
            'Tanggal SK Terakhir (YYYY-MM-DD)',
            'Nomor SK Terakhir',
            'Masa Kerja Golongan',
            'Alamat',
            'No. HP / WA',
            'Catatan',
            'ID ZKTeco (Alfanumerik)',
            'Status (Active/Leave/Inactive)'
        ];

        // Example data row
        

        // Example data row
                $example = [
            'Budi Santoso',
            'budi@example.com',
            'employee',
            'sd',
            'Male',
            'Malang',
            '1985-01-01',
            '3573010101850001',
            '123456',
            '198501012010121002',
            'UKG-001',
            'NRG-123',
            'Gol III/A',
            'S1 Administrasi',
            'Administrasi Perkantoran',
            'Staf Administrasi',
            '-',
            '2010-01-01',
            'PTT',
            '2010-01-01',
            '2025-01-01',
            'SK-001/2025',
            '15 Tahun',
            'Jl. Merdeka No. 1, Malang',
            '081234567890',
            '-',
            '1001',
            'Active'
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
        $headerRange = 'A1:AB1';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

        // Auto-size columns
        foreach (range(1, 28) as $colIndex) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'template_pegawai.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Import employees from uploaded XLSX file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Remove header row
        $header = array_shift($rows);

        $importedCount = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            // Skip empty rows (must have name)
            if (empty($row[0])) {
                continue;
            }

                        // Map variables
            $name = trim($row[0]);
            $email = !empty($row[1]) ? trim($row[1]) : null;
            $type = strtolower(trim($row[2]));
            $unit = strtolower(trim($row[3]));
            $gender = trim($row[4]);
            $birth_place = !empty($row[5]) ? trim($row[5]) : null;
            $birth_date = !empty($row[6]) ? date('Y-m-d', strtotime(trim($row[6]))) : null;
            $nik = !empty($row[7]) ? trim($row[7]) : null;
            $niy = !empty($row[8]) ? trim($row[8]) : null;
            $nuptk = !empty($row[9]) ? trim($row[9]) : null;
            $no_ukg = !empty($row[10]) ? trim($row[10]) : null;
            $nrg = !empty($row[11]) ? trim($row[11]) : null;
            $pangkat_golongan = !empty($row[12]) ? trim($row[12]) : null;
            $last_education = !empty($row[13]) ? trim($row[13]) : null;
            $major = !empty($row[14]) ? trim($row[14]) : null;
            $position = !empty($row[15]) ? trim($row[15]) : null;
            $additional_position = !empty($row[16]) ? trim($row[16]) : null;
            $task_start_date = !empty($row[17]) ? date('Y-m-d', strtotime(trim($row[17]))) : null;
            $employment_status = !empty($row[18]) ? trim($row[18]) : null;
            $appointment_date = !empty($row[19]) ? date('Y-m-d', strtotime(trim($row[19]))) : null;
            $last_sk_date = !empty($row[20]) ? date('Y-m-d', strtotime(trim($row[20]))) : null;
            $last_sk_number = !empty($row[21]) ? trim($row[21]) : null;
            $work_period = !empty($row[22]) ? trim($row[22]) : null;
            $address = !empty($row[23]) ? trim($row[23]) : null;
            $phone = !empty($row[24]) ? trim($row[24]) : null;
            $notes = !empty($row[25]) ? trim($row[25]) : null;
            $zkteco_uid = !empty($row[26]) ? trim($row[26]) : null;
            $status = !empty($row[27]) ? trim($row[27]) : 'Active';

            // Validate dynamic EmployeeType lookup
            $typeObj = \App\Models\EmployeeType::where('code', $type)->orWhere('name', $type)->first();
            if (!$typeObj) {
                $errors[] = "Baris " . ($index + 2) . ": Tipe pegawai '{$type}' tidak terdaftar di database. Silakan buat tipe tersebut terlebih dahulu.";
                continue;
            }

            $schoolUnit = config('app.school_unit');
            if ($schoolUnit && $unit !== $schoolUnit) {
                $errors[] = "Baris " . ($index + 2) . ": Unit sekolah harus '{$schoolUnit}' sesuai konfigurasi sistem.";
                continue;
            }

            if (!in_array($unit, ['paud', 'sd', 'smp'])) {
                $errors[] = "Baris " . ($index + 2) . ": Unit sekolah harus 'paud', 'sd', atau 'smp'.";
                continue;
            }

            if (!in_array($gender, ['Male', 'Female'])) {
                $errors[] = "Baris " . ($index + 2) . ": Jenis kelamin harus 'Male' atau 'Female'.";
                continue;
            }

            if ($zkteco_uid) {
                // Check if ID ZKTeco is already taken by another employee (as string check)
                $exists = Employee::where('zkteco_uid', $zkteco_uid)->exists();
                if ($exists) {
                    $errors[] = "Baris " . ($index + 2) . ": ID ZKTeco '{$zkteco_uid}' sudah digunakan oleh pegawai lain.";
                    continue;
                }
            }

            // Create employee
                        Employee::create([
                'name' => $name,
                'email' => $email,
                'employee_type_id' => $typeObj->id,
                'unit' => $unit,
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
            return redirect()->route('employees.index')
                ->with('success', "Impor selesai. Berhasil mengimpor {$importedCount} data pegawai.")
                ->with('import_errors', $errors);
        }

        return redirect()->route('employees.index')->with('success', "Berhasil mengimpor {$importedCount} data pegawai!");
    }

    /**
     * Display a listing of teachers.
     */
    public function guru(Request $request)
    {
        $teacherType = \App\Models\EmployeeType::where('code', 'teacher')->first();
        $teacherTypeId = $teacherType ? $teacherType->id : 0;
        
        $query = Employee::where('employee_type_id', $teacherTypeId);

        // Apply school unit filter if config-constrained
        $schoolUnit = config('app.school_unit');
        if ($schoolUnit) {
            $query->where('unit', $schoolUnit);
        }

        // Apply search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")->orWhere('nuptk', 'like', "%{$search}%")
                  ->orWhere('subject_position', 'like', "%{$search}%");
            });
        }

        // Apply gender filter
        if ($request->filled('gender')) {
            $query->where('gender', $request->input('gender'));
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Get statistics (specific to the active school unit)
        $statsQuery = Employee::where('employee_type_id', $teacherTypeId);
        if ($schoolUnit) {
            $statsQuery->where('unit', $schoolUnit);
        }

        $totalGuru = (clone $statsQuery)->count();
        $guruMale = (clone $statsQuery)->where('gender', 'Male')->count();
        $guruFemale = (clone $statsQuery)->where('gender', 'Female')->count();
        
        // Count certification based on NUPTK/NIP/NIK filled
        $certifiedCount = (clone $statsQuery)->whereNotNull('nuptk')->where('nuptk', '!=', '')->count();
        $certifiedPercent = $totalGuru > 0 ? round(($certifiedCount / $totalGuru) * 100) : 0;

        $teachers = $query->orderBy('name', 'asc')->paginate(10);
        $employeeTypes = \App\Models\EmployeeType::all();

        return view('admin.guru', compact(
            'teachers', 'totalGuru', 'guruMale', 'guruFemale', 'certifiedPercent', 'employeeTypes'
        ));
    }

    public function generateAccounts()
    {
        $employees = \App\Models\Employee::whereNotNull('email')->where('email', '!=', '')->doesntHave('user')->get();
        $count = 0;

        foreach ($employees as $employee) {
            \App\Models\User::create([
                'name' => $employee->name,
                'email' => $employee->email,
                'password' => bcrypt('sans1234'),
                'role' => 'employee',
                'employee_id' => $employee->id,
            ]);
            $count++;
        }

        return back()->with('success', "$count akun berhasil digenerate otomatis menggunakan password default: sans1234");
    }

    public function generateSingleAccount(\App\Models\Employee $employee)
    {
        if ($employee->user) {
            return back()->with('error', 'Pegawai ini sudah memiliki akun.');
        }

        if (empty($employee->email)) {
            return back()->with('error', 'Pegawai belum memiliki email. Silakan edit data pegawai dan lengkapi emailnya terlebih dahulu.');
        }

        \App\Models\User::create([
            'name' => $employee->name,
            'email' => $employee->email,
            'password' => bcrypt('sans1234'),
            'role' => 'employee',
            'employee_id' => $employee->id,
        ]);

        return back()->with('success', "Akun untuk {$employee->name} berhasil dibuat dengan password default: sans1234");
    }
}
