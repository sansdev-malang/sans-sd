<?php

namespace App\Console\Commands;

use App\Models\AcademicYear;
use App\Models\ClassLevel;
use App\Models\Classroom;
use App\Models\Employee;
use App\Models\Student;
use App\Models\StudentClassroomHistory;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;

class ImportStudentsDatabase extends Command
{
    protected $signature = 'students:import-database {file? : Path to the excel file}';
    protected $description = 'Import 673 official students and 24 gemstone classrooms from master Excel 2026/2027';

    public function handle()
    {
        $filePath = $this->argument('file') ?: public_path('NEW DATABASE PESERTA DIDIK 26-27.xlsx');

        if (!file_exists($filePath)) {
            $this->error("File tidak ditemukan di: {$filePath}");
            return 1;
        }

        $this->info("Membaca file Excel: {$filePath}...");

        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);

        // 1. Setup Tahun Ajaran 2026/2027
        $academicYear = AcademicYear::firstOrCreate(
            ['name' => '2026/2027'],
            [
                'code' => '2627',
                'semester' => 'ganjil',
                'is_active' => true,
                'start_date' => '2026-07-15',
                'end_date' => '2027-06-25',
                'description' => 'Tahun Pelajaran 2026/2027 (Berjalan)',
            ]
        );

        // 2. Setup 6 Tingkat Kelas (1 - 6)
        $levels = [];
        for ($i = 1; $i <= 6; $i++) {
            $levels[$i] = ClassLevel::firstOrCreate(
                ['code' => (string)$i],
                [
                    'name' => "Kelas {$i}",
                    'order' => $i,
                    'description' => "Jenjang Pendidikan SD Kelas {$i}",
                ]
            );
        }

        // 3. Setup 24 Rombongan Belajar Resmi SD Anak Saleh (Batu Mulia)
        $classroomDefinitions = [
            '1A' => ['level' => 1, 'gem' => 'BERLIAN', 'wali' => 'Nadia Fatma Yanti, S.Pd', 'gpk' => 'Mu Ida Nur Fahilah, S.Pd'],
            '1B' => ['level' => 1, 'gem' => 'MUTIARA', 'wali' => 'Dini Eko Wulandari, S.Psi', 'gpk' => 'Arik Wijayanto, S.Psi'],
            '1C' => ['level' => 1, 'gem' => 'SAFIR', 'wali' => 'Gita Noviria, S.Pd', 'gpk' => 'Ika Puspitasari, S.Psi'],
            '1D' => ['level' => 1, 'gem' => 'RUBY', 'wali' => 'Irma Wahyu Putri Yoditya, S.Pd', 'gpk' => 'Raga Cahya Taufikurrahman, S.Pd'],

            '2A' => ['level' => 2, 'gem' => 'GIOK', 'wali' => 'Irdayus Melindra, S.Pd', 'gpk' => 'Rikha Dwi Rachmawati, S.Psi'],
            '2B' => ['level' => 2, 'gem' => 'PIRUS', 'wali' => 'Miftakhul Jannah, S.Pd., S.Pd.Gr', 'gpk' => 'Nurhayati'],
            '2C' => ['level' => 2, 'gem' => 'AMETHYST', 'wali' => 'Aning Masyrufatin Furoida, S.Pd.I., S.Pd.Gr', 'gpk' => "Nur Laili Sa'adah, S.A"],
            '2D' => ['level' => 2, 'gem' => 'OPAL', 'wali' => 'Anis Amelia, S.Pd', 'gpk' => 'Akhmad Saiful, S.Pd'],

            '3A' => ['level' => 3, 'gem' => 'TOPAZ', 'wali' => "Ika Su'udia, S.Si.Gr", 'gpk' => 'Nurul Asri Fitriyah'],
            '3B' => ['level' => 3, 'gem' => 'AQUAMARINE', 'wali' => 'Hj. Noor Jeehan, S.Ag., M.Pd.I', 'gpk' => 'Desilfa Dwi Nursavitri, S.Psi'],
            '3C' => ['level' => 3, 'gem' => 'OBSIDIAN', 'wali' => 'Arif Nur Rahman, S.S., M.Pd.Gr', 'gpk' => 'Afriska Nur Azizah, S.Pd'],
            '3D' => ['level' => 3, 'gem' => 'ZAMRUD', 'wali' => 'Desty Ariani Mutiara, S.Pd.', 'gpk' => "M. Baha'ul Alamsyah Al Faini, S.Pd"],

            '4A' => ['level' => 4, 'gem' => 'JASPER', 'wali' => 'Miftakul Jannah, S.Pd', 'gpk' => null],
            '4B' => ['level' => 4, 'gem' => 'MALASIT', 'wali' => 'Ucik Sriwahyuni, S.Pd', 'gpk' => 'Kofifah Indar Khoiroh, S.Psi'],
            '4C' => ['level' => 4, 'gem' => 'GARNET', 'wali' => 'Sri Subakti, S.Pd.SD.Gr', 'gpk' => 'Nila Fadilah, S.Pd'],
            '4D' => ['level' => 4, 'gem' => 'CITRINE', 'wali' => 'Lailatul Munawaroh, S.Pd., M.Pd', 'gpk' => null],

            '5A' => ['level' => 5, 'gem' => 'BACAN', 'wali' => 'Ghoniyur Rohman, S.Pd., S.Pd.SD.Gr', 'gpk' => "Tursina Ainun Nisa' Caniago, S.Sos"],
            '5B' => ['level' => 5, 'gem' => 'TOURMALINE', 'wali' => 'Anida Nafis Qotrunnada, S.Pd', 'gpk' => null],
            '5C' => ['level' => 5, 'gem' => 'PERMATA', 'wali' => 'Moch. Yusroni, S.Pd.Gr', 'gpk' => null],
            '5D' => ['level' => 5, 'gem' => 'AGATE', 'wali' => 'Desi Ratnasari, S.Pd', 'gpk' => 'Venorica Afdela, S.Psi'],

            '6A' => ['level' => 6, 'gem' => 'EMERALD', 'wali' => 'Dara Eges Nuryana, S.Pd', 'gpk' => 'Yahya Firmansyah, S.Pd'],
            '6B' => ['level' => 6, 'gem' => 'ALEXANDRITE', 'wali' => 'Ainur Rifqi, M.Pd', 'gpk' => 'Ahmad Shobirin, S.Pd'],
            '6C' => ['level' => 6, 'gem' => 'ONIKS', 'wali' => 'Puri Wiranti, S.Pd', 'gpk' => 'Syaifud Dina Fitriana'],
            '6D' => ['level' => 6, 'gem' => 'KUARSA', 'wali' => 'Hj. Sri Yudianti, S.Pd', 'gpk' => null],
        ];

        $classroomMap = [];
        foreach ($classroomDefinitions as $code => $def) {
            $pureClassName = ucwords(strtolower($def['gem']));
            
            // Cek jika guru wali kelas ada di database employees
            $homeroomEmployee = null;
            if (!empty($def['wali'])) {
                $cleanWali = preg_replace('/\s+/', ' ', trim($def['wali']));
                $firstWord = explode(' ', $cleanWali)[0] ?? '';
                $homeroomEmployee = Employee::where('name', 'like', "%{$firstWord}%")->first();
            }

            $classroom = Classroom::updateOrCreate(
                [
                    'code' => $code,
                    'academic_year_id' => $academicYear->id,
                ],
                [
                    'name' => $pureClassName,
                    'class_level_id' => $levels[$def['level']]->id,
                    'homeroom_teacher_id' => $homeroomEmployee?->id,
                    'capacity' => 32,
                    'is_active' => true,
                    'description' => "Rombel {$code} {$pureClassName} SD Anak Saleh TA {$academicYear->name}",
                ]
            );

            $classroomMap[$code] = $classroom;
            $classroomMap[strtoupper($def['gem'])] = $classroom;
            $classroomMap[strtoupper("{$code} ({$def['gem']})")] = $classroom;
            $classroomMap[strtoupper("{$code} {$def['gem']}")] = $classroom;
        }

        $this->info("24 Rombel resmi SD Anak Saleh berhasil disiapkan!");

        // 4. Baca Sheet MASTER & Import Data Siswa
        $sheet = $spreadsheet->getSheetByName('MASTER');
        $highestRow = $sheet->getHighestRow();

        $this->info("Mengimpor data siswa dari Sheet MASTER (Total baris: {$highestRow})...");

        $imported = 0;
        $updated = 0;

        for ($r = 3; $r <= $highestRow; $r++) {
            $fullName = trim((string)$sheet->getCell('Q' . $r)->getValue());
            if (empty($fullName)) {
                continue;
            }

            $rawNis = trim((string)$sheet->getCell('M' . $r)->getValue());
            $nis = !empty($rawNis) ? str_pad($rawNis, 4, '0', STR_PAD_LEFT) : 'SD.' . $r;

            $rawGrade = strtoupper(trim((string)$sheet->getCell('R' . $r)->getValue()));
            $rawClassName = strtoupper(trim((string)$sheet->getCell('S' . $r)->getValue()));
            
            // Match classroom
            $classroom = $classroomMap[$rawGrade] ?? ($classroomMap[$rawClassName] ?? ($classroomMap["{$rawGrade} ({$rawClassName})"] ?? null));

            // Parse Dates Helper
            $parseDate = function ($val) {
                if (empty($val)) return null;
                if (is_numeric($val) && (int)$val > 1000) {
                    try {
                        return ExcelDate::excelToDateTimeObject($val)->format('Y-m-d');
                    } catch (\Throwable $e) {
                        return null;
                    }
                }
                try {
                    return Carbon::parse($val)->format('Y-m-d');
                } catch (\Throwable $e) {
                    return null;
                }
            };

            $genderRaw = strtoupper(trim((string)$sheet->getCell('U' . $r)->getValue()));
            $gender = str_contains($genderRaw, 'PEREMPUAN') || $genderRaw === 'P' || $genderRaw === 'FEMALE' ? 'P' : 'L';

            $studentTypeRaw = strtoupper(trim((string)$sheet->getCell('Y' . $r)->getValue()));
            $studentType = str_contains($studentTypeRaw, 'PDBK') || str_contains($studentTypeRaw, 'KHUSUS') || str_contains($studentTypeRaw, 'INKLUSI') ? 'PDBK (BERKEBUTUHAN KHUSUS)' : 'REGULER';

            $cleanPhone = function ($raw) {
                if (empty($raw) || trim((string)$raw) === '-' || trim((string)$raw) === '0') return null;
                $p = preg_replace('/[^0-9]/', '', (string)$raw);
                if (str_starts_with($p, '0')) {
                    $p = '62' . substr($p, 1);
                }
                return $p;
            };

            $studentData = [
                'nis' => $nis,
                'nisn' => trim((string)$sheet->getCell('N' . $r)->getValue()) ?: null,
                'no_kk' => trim((string)$sheet->getCell('O' . $r)->getValue()) ?: null,
                'nik' => trim((string)$sheet->getCell('P' . $r)->getValue()) ?: null,
                'birth_certificate_no' => trim((string)$sheet->getCell('G' . $r)->getValue()) ?: null,
                'classroom_id' => $classroom?->id,
                'academic_year_id' => $academicYear->id,
                'full_name' => $fullName,
                'nickname' => trim((string)$sheet->getCell('T' . $r)->getValue()) ?: null,
                'gender' => $gender,
                'student_type' => $studentType,
                'special_needs_type' => trim((string)$sheet->getCell('Z' . $r)->getValue()) ?: null,
                'birth_place' => trim((string)$sheet->getCell('V' . $r)->getValue()) ?: null,
                'birth_date' => $parseDate($sheet->getCell('W' . $r)->getValue()),
                'religion' => trim((string)$sheet->getCell('AA' . $r)->getValue()) ?: 'ISLAM',
                'citizenship' => trim((string)$sheet->getCell('AB' . $r)->getValue()) ?: 'WARGA NEGARA INDONESIA',

                // Alamat
                'address' => trim((string)$sheet->getCell('AC' . $r)->getValue()) ?: null,
                'rt' => trim((string)$sheet->getCell('AD' . $r)->getValue()) ?: null,
                'rw' => trim((string)$sheet->getCell('AE' . $r)->getValue()) ?: null,
                'village' => trim((string)$sheet->getCell('AF' . $r)->getValue()) ?: null,
                'district' => trim((string)$sheet->getCell('AG' . $r)->getValue()) ?: null,
                'district_category' => trim((string)$sheet->getCell('AH' . $r)->getValue()) ?: null,
                'postal_code' => trim((string)$sheet->getCell('AI' . $r)->getValue()) ?: null,
                'city' => trim((string)$sheet->getCell('AJ' . $r)->getValue()) ?: 'MALANG',
                'residence_status' => trim((string)$sheet->getCell('AK' . $r)->getValue()) ?: null,
                'distance_to_school' => trim((string)$sheet->getCell('AL' . $r)->getValue()) ?: null,
                'home_phone' => trim((string)$sheet->getCell('AM' . $r)->getValue()) ?: null,

                // Kontak
                'parent_phone' => $cleanPhone($sheet->getCell('AN' . $r)->getValue()),

                // Keluarga
                'child_number' => is_numeric($sheet->getCell('AO' . $r)->getValue()) ? (int)$sheet->getCell('AO' . $r)->getValue() : null,
                'siblings_count' => is_numeric($sheet->getCell('AP' . $r)->getValue()) ? (int)$sheet->getCell('AP' . $r)->getValue() : null,
                'step_siblings_count' => is_numeric($sheet->getCell('AQ' . $r)->getValue()) ? (int)$sheet->getCell('AQ' . $r)->getValue() : null,
                'adoptive_siblings_count' => is_numeric($sheet->getCell('AR' . $r)->getValue()) ? (int)$sheet->getCell('AR' . $r)->getValue() : null,
                'home_language' => trim((string)$sheet->getCell('AX' . $r)->getValue()) ?: null,

                // Kesehatan
                'weight' => trim((string)$sheet->getCell('AS' . $r)->getValue()) ?: null,
                'height' => trim((string)$sheet->getCell('AT' . $r)->getValue()) ?: null,
                'blood_type' => trim((string)$sheet->getCell('AU' . $r)->getValue()) ?: null,
                'severe_disease_history' => trim((string)$sheet->getCell('AV' . $r)->getValue()) ?: null,
                'frequent_disease' => trim((string)$sheet->getCell('AW' . $r)->getValue()) ?: null,

                // Asal Sekolah
                'origin_category' => trim((string)$sheet->getCell('I' . $r)->getValue()) ?: 'TK',
                'previous_school' => trim((string)$sheet->getCell('J' . $r)->getValue()) ?: null,
                'previous_school_address' => trim((string)$sheet->getCell('K' . $r)->getValue()) ?: null,
                'sttb_number_date' => trim((string)$sheet->getCell('L' . $r)->getValue()) ?: null,

                // Data Ayah
                'father_name' => trim((string)$sheet->getCell('AY' . $r)->getValue()) ?: null,
                'father_nik' => trim((string)$sheet->getCell('AZ' . $r)->getValue()) ?: null,
                'father_birth_place' => trim((string)$sheet->getCell('BA' . $r)->getValue()) ?: null,
                'father_birth_date' => $parseDate($sheet->getCell('BB' . $r)->getValue()),
                'father_religion' => trim((string)$sheet->getCell('BC' . $r)->getValue()) ?: null,
                'father_phone' => $cleanPhone($sheet->getCell('BD' . $r)->getValue()),
                'father_education' => trim((string)$sheet->getCell('BE' . $r)->getValue()) ?: null,
                'father_job' => trim((string)$sheet->getCell('BF' . $r)->getValue()) ?: null,
                'father_company' => trim((string)$sheet->getCell('BG' . $r)->getValue()) ?: null,
                'father_company_address' => trim((string)$sheet->getCell('BH' . $r)->getValue()) ?: null,
                'father_company_phone' => trim((string)$sheet->getCell('BI' . $r)->getValue()) ?: null,
                'father_income' => trim((string)$sheet->getCell('BJ' . $r)->getValue()) ?: null,
                'father_email' => trim((string)$sheet->getCell('BK' . $r)->getValue()) ?: null,

                // Data Ibu
                'mother_name' => trim((string)$sheet->getCell('BL' . $r)->getValue()) ?: null,
                'mother_nik' => trim((string)$sheet->getCell('BM' . $r)->getValue()) ?: null,
                'mother_birth_place' => trim((string)$sheet->getCell('BN' . $r)->getValue()) ?: null,
                'mother_birth_date' => $parseDate($sheet->getCell('BO' . $r)->getValue()),
                'mother_religion' => trim((string)$sheet->getCell('BP' . $r)->getValue()) ?: null,
                'mother_phone' => $cleanPhone($sheet->getCell('BQ' . $r)->getValue()),
                'mother_education' => trim((string)$sheet->getCell('BR' . $r)->getValue()) ?: null,
                'mother_job' => trim((string)$sheet->getCell('BS' . $r)->getValue()) ?: null,
                'mother_company' => trim((string)$sheet->getCell('BT' . $r)->getValue()) ?: null,
                'mother_company_address' => trim((string)$sheet->getCell('BU' . $r)->getValue()) ?: null,
                'mother_company_phone' => trim((string)$sheet->getCell('BV' . $r)->getValue()) ?: null,
                'mother_income' => trim((string)$sheet->getCell('BW' . $r)->getValue()) ?: null,
                'mother_email' => trim((string)$sheet->getCell('BX' . $r)->getValue()) ?: null,

                // Data Wali
                'guardian_name' => trim((string)$sheet->getCell('BY' . $r)->getValue()) ?: null,
                'guardian_birth_place' => trim((string)$sheet->getCell('BZ' . $r)->getValue()) ?: null,
                'guardian_birth_date' => $parseDate($sheet->getCell('CA' . $r)->getValue()),
                'guardian_relation' => trim((string)$sheet->getCell('CB' . $r)->getValue()) ?: null,
                'guardian_phone' => $cleanPhone($sheet->getCell('CC' . $r)->getValue()),
                'guardian_education' => trim((string)$sheet->getCell('CD' . $r)->getValue()) ?: null,
                'guardian_job' => trim((string)$sheet->getCell('CE' . $r)->getValue()) ?: null,
                'guardian_religion' => trim((string)$sheet->getCell('CF' . $r)->getValue()) ?: null,
                'guardian_address' => trim((string)$sheet->getCell('CG' . $r)->getValue()) ?: null,

                // Checklist Berkas
                'checklist_documents' => [
                    'akte' => !empty($sheet->getCell('B' . $r)->getValue()),
                    'kk' => !empty($sheet->getCell('C' . $r)->getValue()),
                    'suket_tk' => !empty($sheet->getCell('D' . $r)->getValue()),
                    'surat_kurang_6_th' => !empty($sheet->getCell('E' . $r)->getValue()),
                    'surat_pdbk' => !empty($sheet->getCell('F' . $r)->getValue()),
                ],

                'status' => 'aktif',
                'enrolled_date' => $parseDate($sheet->getCell('H' . $r)->getValue()) ?: '2026-07-15',
                'notes' => 'Import Database TU TA 2026/2027',
            ];

            // Primary Match: NIS or (Full Name & Birth Date)
            $existing = Student::where('nis', $nis)
                ->orWhere(function ($q) use ($fullName, $studentData) {
                    $q->where('full_name', $fullName);
                    if (!empty($studentData['birth_date'])) {
                        $q->where('birth_date', $studentData['birth_date']);
                    }
                })
                ->first();

            if ($existing) {
                $existing->update($studentData);
                $student = $existing;
                $updated++;
            } else {
                $student = Student::create($studentData);
                $imported++;
            }

            // Simpan Riwayat Kelas / Enrollment History
            if ($classroom) {
                StudentClassroomHistory::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'academic_year_id' => $academicYear->id,
                    ],
                    [
                        'classroom_id' => $classroom->id,
                        'classroom_name' => $classroom->name,
                        'grade_level' => (string)($classroomDefinitions[$rawGrade]['level'] ?? $rawGrade),
                        'homeroom_teacher_name' => $classroomDefinitions[$rawGrade]['wali'] ?? null,
                        'gpk_teacher_name' => $classroomDefinitions[$rawGrade]['gpk'] ?? null,
                        'status' => 'aktif',
                        'start_date' => '2026-07-15',
                        'notes' => 'Import Semester Ganjil 2026/2027',
                    ]
                );
            }
        }

        $this->info("==================================================");
        $this->info("SELESAI! Berhasil import: {$imported} siswa baru, update: {$updated} siswa.");
        $this->info("Total siswa aktif di database: " . Student::count());
        $this->info("==================================================");

        return 0;
    }
}
