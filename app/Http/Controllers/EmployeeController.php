<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
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
                  ->orWhere('nuptk_nip_nik', 'like', "%{$search}%");
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

        $employees = $query->with('employeeType')->latest()->get();
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
    public function store(Request $request)
    {
        $schoolUnit = config('app.school_unit');
        if ($schoolUnit) {
            $request->merge(['unit' => $schoolUnit]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'nuptk_nip_nik' => 'nullable|string|max:255',
            'employee_type_id' => 'required|exists:employee_types,id',
            'unit' => 'required|in:paud,sd,smp',
            'subject_position' => 'nullable|string|max:255',
            'gender' => 'required|in:Male,Female',
            'employment_status' => 'nullable|string|max:255',
            'zkteco_uid' => 'nullable|string|max:255|unique:employees,zkteco_uid',
            'status' => 'nullable|in:Active,Leave,Inactive',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 'public');
            $validated['photo'] = $path;
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
            'name' => 'sometimes|required|string|max:255',
            'email' => 'nullable|email|max:255',
            'nuptk_nip_nik' => 'nullable|string|max:255',
            'employee_type_id' => 'sometimes|required|exists:employee_types,id',
            'unit' => 'sometimes|required|in:paud,sd,smp',
            'subject_position' => 'nullable|string|max:255',
            'gender' => 'sometimes|required|in:Male,Female',
            'employment_status' => 'nullable|string|max:255',
            'zkteco_uid' => 'nullable|string|max:255|unique:employees,zkteco_uid,' . $employee->id,
            'status' => 'nullable|in:Active,Leave,Inactive',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($employee->photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($employee->photo);
            }
            $path = $request->file('photo')->store('photos', 'public');
            $validated['photo'] = $path;
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
            'NUPTK/NIP/NIK',
            'Kode Tipe Pegawai (contoh: teacher, employee)',
            'Unit Sekolah (paud/sd/smp)',
            'Jabatan / Mapel',
            'Jenis Kelamin (Male/Female)',
            'Status Kepegawaian',
            'ID ZKTeco (Alfanumerik)',
            'Status (Active/Leave/Inactive)'
        ];

        // Example data row
        $example = [
            'Budi Santoso',
            'budi@example.com',
            '198501012010121002',
            'teacher',
            'sd',
            'Guru Matematika',
            'Male',
            'PNS',
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
        $headerRange = 'A1:J1';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

        // Auto-size columns
        foreach (range(1, 10) as $colIndex) {
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
            $nuptk_nip_nik = !empty($row[2]) ? trim($row[2]) : null;
            $type = strtolower(trim($row[3]));
            $unit = strtolower(trim($row[4]));
            $subject_position = !empty($row[5]) ? trim($row[5]) : null;
            $gender = trim($row[6]);
            $employment_status = !empty($row[7]) ? trim($row[7]) : null;
            $zkteco_uid = !empty($row[8]) ? trim($row[8]) : null;
            $status = !empty($row[9]) ? trim($row[9]) : 'Active';

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
                'nuptk_nip_nik' => $nuptk_nip_nik,
                'employee_type_id' => $typeObj->id,
                'unit' => $unit,
                'subject_position' => $subject_position,
                'gender' => $gender,
                'employment_status' => $employment_status,
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
                  ->orWhere('nuptk_nip_nik', 'like', "%{$search}%")
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
        $certifiedCount = (clone $statsQuery)->whereNotNull('nuptk_nip_nik')->where('nuptk_nip_nik', '!=', '')->count();
        $certifiedPercent = $totalGuru > 0 ? round(($certifiedCount / $totalGuru) * 100) : 0;

        $teachers = $query->latest()->paginate(10);
        $employeeTypes = \App\Models\EmployeeType::all();

        return view('admin.guru', compact(
            'teachers', 'totalGuru', 'guruMale', 'guruFemale', 'certifiedPercent', 'employeeTypes'
        ));
    }
}
