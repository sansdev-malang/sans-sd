<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Siswa - {{ $selectedYearName ?? 'SD Anak Saleh' }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm 12mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8pt;
            color: #1e293b;
            line-height: 1.35;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .header-text {
            text-align: center;
        }

        .header-text h3 {
            font-size: 8pt;
            font-weight: normal;
            letter-spacing: 1px;
            color: #475569;
            text-transform: uppercase;
            margin: 0;
        }

        .header-text h1 {
            font-size: 13pt;
            font-weight: bold;
            color: #0f172a;
            margin: 2px 0;
            text-transform: uppercase;
        }

        .header-text p {
            font-size: 7.5pt;
            color: #64748b;
            margin: 0;
        }

        .doc-title {
            text-align: center;
            margin-bottom: 10px;
        }

        .doc-title h2 {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #1e293b;
            margin: 0;
        }

        .doc-title p {
            font-size: 7.5pt;
            color: #475569;
            margin-top: 2px;
        }

        .summary-bar {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
            font-size: 7.5pt;
            margin-bottom: 10px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table.data-table th, 
        table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 5px;
            vertical-align: top;
            font-size: 7pt;
        }

        table.data-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 6.5pt;
        }

        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }

        .badge-pdbk {
            color: #86198f;
            font-weight: bold;
        }

        .signatures-table {
            width: 100%;
            margin-top: 15px;
            border: none;
        }

        .signatures-table td {
            border: none;
            text-align: center;
            font-size: 7.5pt;
        }

        .sig-space {
            height: 45px;
        }

        .sig-name {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT -->
    <table class="header-table">
        <tr>
            <td class="header-text">
                <h3>YAYASAN PENDIDIKAN ANAK SALEH MALANG</h3>
                <h1>{{ strtoupper(setting('unit_name', 'SD Anak Saleh')) }}</h1>
                <p>NPSN: 20539745 &bull; Terakreditasi "A" &bull; Jl. Candi Panggung No. 54, Mojolangu, Lowokwaru, Kota Malang</p>
                <p>Telp: (0341) 480170 &bull; Website: www.sdanaksaleh.sch.id &bull; Email: info@sdanaksaleh.sch.id</p>
            </td>
        </tr>
    </table>

    <!-- DOCUMENT TITLE -->
    <div class="doc-title">
        <h2>LAPORAN DATA PESERTA DIDIK</h2>
        <p>
            Tahun Pelajaran: {{ $selectedYearName }}
            @if($selectedClassroom)
                &bull; Rombel: {{ $selectedClassroom->full_name }}
            @elseif($selectedClassLevel)
                &bull; Tingkat: {{ $selectedClassLevel->name }}
            @endif
            @if($selectedStudentType)
                &bull; Kategori: {{ $selectedStudentType }}
            @endif
            @if($selectedStatus && $selectedStatus !== 'all')
                &bull; Status: {{ ucfirst($selectedStatus) }}
            @endif
        </p>
    </div>

    @php
        $maleCount = $students->whereIn('gender', ['L', 'Laki-laki', 'Male'])->count();
        $femaleCount = $students->whereIn('gender', ['P', 'Perempuan', 'Female'])->count();
        $pdbkCount = $students->filter(function($s) {
            return ($s->student_type && (str_contains(strtoupper($s->student_type), 'PDBK') || str_contains(strtoupper($s->student_type), 'KHUSUS') || str_contains(strtoupper($s->student_type), 'INKLUSI'))) || !empty($s->special_needs_type) || !empty($s->gpk_employee_id);
        })->count();
    @endphp

    <div class="summary-bar">
        <strong>Total Siswa:</strong> {{ count($students) }} Siswa &nbsp;|&nbsp;
        <strong>Laki-laki (L):</strong> {{ $maleCount }} Siswa &nbsp;|&nbsp;
        <strong>Perempuan (P):</strong> {{ $femaleCount }} Siswa &nbsp;|&nbsp;
        <strong>PDBK (Inklusi):</strong> {{ $pdbkCount }} Siswa &nbsp;|&nbsp;
        <strong>Tanggal Cetak:</strong> {{ date('d/m/Y H:i') }} WIB
    </div>

    <!-- TABLE -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="9%">NIS / NISN</th>
                <th width="20%">Nama Lengkap Siswa</th>
                <th width="4%">L/P</th>
                <th width="12%">Tingkat & Rombel</th>
                <th width="14%">Kategori & Inklusi</th>
                <th width="16%">Orang Tua & No. WA</th>
                <th width="16%">Alamat Domisili</th>
                <th width="6%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $index => $student)
                @php
                    $isFemale = in_array(strtoupper((string)$student->gender), ['P', 'PEREMPUAN', 'FEMALE']);
                    $genderCode = $isFemale ? 'P' : 'L';
                    $isPdbk = ($student->student_type && (str_contains(strtoupper($student->student_type), 'PDBK') || str_contains(strtoupper($student->student_type), 'KHUSUS') || str_contains(strtoupper($student->student_type), 'INKLUSI'))) || !empty($student->special_needs_type) || !empty($student->gpk_employee_id);
                @endphp
                <tr>
                    <td class="text-center font-bold">{{ $index + 1 }}</td>
                    <td class="text-center">
                        <strong>{{ $student->nis ?? '-' }}</strong>
                        @if($student->nisn)
                            <br><span style="color: #64748b; font-size: 6pt;">{{ $student->nisn }}</span>
                        @endif
                    </td>
                    <td>
                        <strong>{{ strtoupper($student->full_name) }}</strong>
                        @if($student->birth_date)
                            <br><span style="color: #64748b; font-size: 6pt;">{{ $student->birth_place ? $student->birth_place . ', ' : '' }}{{ \Carbon\Carbon::parse($student->birth_date)->format('d/m/Y') }}</span>
                        @endif
                    </td>
                    <td class="text-center font-bold">{{ $genderCode }}</td>
                    <td>
                        {{ $student->classroom?->full_name ?? ($student->classroom?->name ?? '-') }}
                        @if($student->classroom?->homeroomTeacher)
                            <br><span style="color: #64748b; font-size: 6pt;">Wali: {{ $student->classroom->homeroomTeacher->name }}</span>
                        @endif
                    </td>
                    <td>
                        @if($isPdbk)
                            <span class="badge-pdbk">PDBK (Inklusi)</span>
                            @if($student->special_needs_type)
                                <br><span style="font-size: 6pt; color: #6b21a8;">{{ $student->special_needs_type }}</span>
                            @endif
                            @if($student->gpkTeacher)
                                <br><span style="font-size: 6pt; color: #64748b;">GPK: {{ $student->gpkTeacher->name }}</span>
                            @endif
                        @else
                            Reguler
                        @endif
                    </td>
                    <td>
                        @if($student->father_name)
                            A: {{ $student->father_name }}
                        @elseif($student->mother_name)
                            I: {{ $student->mother_name }}
                        @else
                            -
                        @endif
                        @if($student->parent_phone || $student->father_phone || $student->mother_phone)
                            <br><span style="color: #059669; font-size: 6pt;">WA: {{ $student->parent_phone ?: ($student->father_phone ?: $student->mother_phone) }}</span>
                        @endif
                    </td>
                    <td>
                        {{ $student->address ?? '-' }}
                        @if($student->city)
                            <br><span style="color: #64748b; font-size: 6pt;">{{ $student->district ? $student->district . ', ' : '' }}{{ $student->city }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        {{ strtoupper($student->status ?: 'AKTIF') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 15px;">
                        Tidak ada data siswa yang sesuai dengan filter yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- SIGNATURES -->
    <table class="signatures-table">
        <tr>
            <td width="50%">
                {{ $tuSigner['title'] ?? 'Tata Usaha,' }}
                <div class="sig-space"></div>
                <div class="sig-name">{{ $tuSigner['name'] ?? 'Admin SD Anak Saleh' }}</div>
                <div>{{ !empty($tuSigner['niy']) ? 'NIY. ' . $tuSigner['niy'] : (!empty($tuSigner['nip']) ? 'NIP. ' . $tuSigner['nip'] : '') }}</div>
            </td>
            <td width="50%">
                Malang, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}<br>
                {{ $headmasterSigner['title'] ?? ('Kepala ' . setting('unit_name', 'SD Anak Saleh') . ',') }}
                <div class="sig-space"></div>
                <div class="sig-name">{{ $headmasterSigner['name'] ?? 'Andreas Setiyono, S.Pd.Gr., M.Kom.' }}</div>
                <div>{{ !empty($headmasterSigner['niy']) ? 'NIY. ' . $headmasterSigner['niy'] : (!empty($headmasterSigner['nip']) ? 'NIP. ' . $headmasterSigner['nip'] : '') }}</div>
            </td>
        </tr>
    </table>

</body>
</html>
