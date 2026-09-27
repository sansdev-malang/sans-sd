<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Peserta Didik - {{ $selectedYearName ?? 'SD Anak Saleh' }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        body {
            background-color: #f8fafc;
            color: #0f172a;
            padding: 24px;
            font-size: 10px;
            line-height: 1.4;
        }

        .no-print {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            max-width: 1280px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: #4338ca;
            color: #ffffff;
        }
        .btn-primary:hover {
            background-color: #3730a3;
        }

        .btn-success {
            background-color: #059669;
            color: #ffffff;
        }
        .btn-success:hover {
            background-color: #047857;
        }

        .btn-secondary {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }
        .btn-secondary:hover {
            background-color: #e2e8f0;
        }

        .print-page {
            background: #ffffff;
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 30px 35px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }

        /* HEADER / KOP SURAT */
        .header {
            display: flex;
            align-items: center;
            gap: 20px;
            border-bottom: 3px double #0f172a;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .header-logo {
            width: 65px;
            height: 65px;
            object-fit: contain;
        }

        .header-text {
            flex: 1;
            text-align: center;
        }

        .header-text h3 {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1px;
            color: #475569;
            text-transform: uppercase;
        }

        .header-text h1 {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.5px;
            margin: 2px 0;
            text-transform: uppercase;
        }

        .header-text p {
            font-size: 9.5px;
            color: #64748b;
        }

        /* DOCUMENT TITLE & META */
        .doc-title {
            text-align: center;
            margin-bottom: 14px;
        }

        .doc-title h2 {
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #1e293b;
            text-decoration: underline;
        }

        .doc-title p {
            font-size: 10px;
            color: #475569;
            font-weight: 600;
            margin-top: 3px;
        }

        /* METRIC SUMMARY PILLS */
        .metrics-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 14px;
            padding: 8px 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }

        .metric-pill {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 10px;
            color: #334155;
        }

        .metric-pill strong {
            font-weight: 700;
            color: #0f172a;
        }

        .metric-divider {
            color: #cbd5e1;
        }

        /* DATA TABLE */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9.5px;
        }

        .report-table th, 
        .report-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 6px;
            vertical-align: middle;
        }

        .report-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-weight: 700;
            text-align: center;
            text-transform: uppercase;
            font-size: 8.5px;
            letter-spacing: 0.3px;
            padding: 7px 5px;
        }

        .report-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: 700; }

        .badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-reguler {
            background-color: #e0e7ff;
            color: #3730a3;
        }

        .badge-pdbk {
            background-color: #fae8ff;
            color: #86198f;
            border: 1px solid #f0abfc;
        }

        .badge-aktif {
            background-color: #dcfce7;
            color: #166534;
        }

        .badge-nonaktif {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* SIGNATURE SECTION */
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            page-break-inside: avoid;
        }

        .signature-box {
            text-align: center;
            width: 250px;
        }

        .signature-space {
            height: 55px;
        }

        .signature-name {
            font-weight: 700;
            text-decoration: underline;
            color: #0f172a;
            font-size: 10.5px;
        }

        .signature-title {
            font-size: 9px;
            color: #64748b;
        }

        /* PRINT STYLES */
        @media print {
            @page {
                size: A4 landscape;
                margin: 10mm 12mm;
            }

            body {
                background: #ffffff;
                padding: 0;
                font-size: 8.5pt;
                color: #000000;
            }

            .no-print {
                display: none !important;
            }

            .print-page {
                border: none;
                box-shadow: none;
                padding: 0;
                max-width: 100%;
                width: 100%;
            }

            .report-table th {
                background-color: #1e293b !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .report-table tr:nth-child(even) {
                background-color: #f8fafc !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .badge-pdbk {
                background-color: #fae8ff !important;
                color: #86198f !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .badge-reguler {
                background-color: #e0e7ff !important;
                color: #3730a3 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .badge-aktif {
                background-color: #dcfce7 !important;
                color: #166534 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <!-- ACTION CONTROLS (HIDDEN WHEN PRINTING) -->
    <div class="no-print">
        <div>
            <a href="{{ route('students.index', request()->all()) }}" class="btn btn-secondary">
                &larr; Kembali ke Daftar Siswa
            </a>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('students.export.excel', request()->all()) }}" class="btn btn-success">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                Ekspor Excel (.xlsx)
            </a>
            <a href="{{ route('students.export.pdf', request()->all()) }}" class="btn btn-secondary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                Unduh PDF
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                Cetak / Print
            </button>
        </div>
    </div>

    <!-- PRINTABLE PAGE WRAPPER -->
    <div class="print-page">
        
        <!-- KOP SURAT RESMI -->
        <div class="header">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Sekolah" class="header-logo" onerror="this.src='{{ asset('logo.png') }}'; this.onerror=null;">
            <div class="header-text">
                <h3>YAYASAN PENDIDIKAN ISLAM ANAK SALEH MALANG</h3>
                <h1>SD ANAK SALEH</h1>
                <p>NPSN: 20539745 &bull; Terakreditasi "A" &bull; Jl. Candi Panggung No. 54, Mojolangu, Lowokwaru, Kota Malang</p>
                <p>Telp: (0341) 480170 &bull; Website: www.sdanaksaleh.sch.id &bull; Email: info@sdanaksaleh.sch.id</p>
            </div>
        </div>

        <!-- DOCUMENT TITLE & FILTER INFORMATION -->
        <div class="doc-title">
            <h2>LAPORAN DATA PESERTA DIDIK</h2>
            <p>
                Tahun Pelajaran: {{ $selectedYearName ?: '2026/2027' }}
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
                @if($searchQuery)
                    &bull; Pencarian: "{{ $searchQuery }}"
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

        <!-- SUMMARY METRICS -->
        <div class="metrics-grid">
            <div class="metric-pill">
                <span>Total Terdata:</span>
                <strong>{{ count($students) }} Siswa</strong>
            </div>
            <span class="metric-divider">|</span>
            <div class="metric-pill">
                <span>Laki-laki (L):</span>
                <strong>{{ $maleCount }} Siswa ({{ count($students) > 0 ? round(($maleCount / count($students)) * 100) : 0 }}%)</strong>
            </div>
            <span class="metric-divider">|</span>
            <div class="metric-pill">
                <span>Perempuan (P):</span>
                <strong>{{ $femaleCount }} Siswa ({{ count($students) > 0 ? round(($femaleCount / count($students)) * 100) : 0 }}%)</strong>
            </div>
            <span class="metric-divider">|</span>
            <div class="metric-pill">
                <span>PDBK (Inklusi):</span>
                <strong>{{ $pdbkCount }} Siswa</strong>
            </div>
            <span class="metric-divider">|</span>
            <div class="metric-pill" style="margin-left: auto;">
                <span>Dicetak pada:</span>
                <strong>{{ \Carbon\Carbon::now()->isoFormat('D MMMM Y, HH:mm') }} WIB</strong>
            </div>
        </div>

        <!-- MAIN TABLE -->
        <table class="report-table">
            <thead>
                <tr>
                    <th width="3%">No</th>
                    <th width="10%">NIS / NISN</th>
                    <th width="18%">Nama Lengkap Siswa</th>
                    <th width="4%">L/P</th>
                    <th width="12%">Tingkat & Rombel</th>
                    <th width="14%">Kategori & Inklusi</th>
                    <th width="15%">Orang Tua & No. WA</th>
                    <th width="18%">Alamat Domisili</th>
                    <th width="6%">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $index => $student)
                    @php
                        $isFemale = in_array(strtoupper((string)$student->gender), ['P', 'PEREMPUAN', 'FEMALE']);
                        $genderCode = $isFemale ? 'P' : 'L';
                        
                        $isPdbk = ($student->student_type && (str_contains(strtoupper($student->student_type), 'PDBK') || str_contains(strtoupper($student->student_type), 'KHUSUS') || str_contains(strtoupper($student->student_type), 'INKLUSI'))) || !empty($student->special_needs_type) || !empty($student->gpk_employee_id);
                        $statusRaw = strtolower($student->status ?: 'aktif');
                    @endphp
                    <tr>
                        <td class="text-center font-bold">{{ $index + 1 }}</td>
                        <td class="text-center">
                            <span class="font-bold">{{ $student->nis ?? '-' }}</span>
                            @if($student->nisn)
                                <br><span style="color: #64748b; font-size: 8.5px;">{{ $student->nisn }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="font-bold" style="color: #0f172a;">{{ strtoupper($student->full_name) }}</span>
                            @if($student->nickname)
                                <span style="color: #64748b;">({{ $student->nickname }})</span>
                            @endif
                            @if($student->birth_date)
                                <br><span style="color: #64748b; font-size: 8.5px;">{{ $student->birth_place ? $student->birth_place . ', ' : '' }}{{ \Carbon\Carbon::parse($student->birth_date)->format('d/m/Y') }} ({{ $student->age }})</span>
                            @endif
                        </td>
                        <td class="text-center font-bold" style="color: {{ $genderCode === 'L' ? '#2563eb' : '#db2777' }};">
                            {{ $genderCode }}
                        </td>
                        <td>
                            <span class="font-bold">{{ $student->classroom?->full_name ?? ($student->classroom?->name ?? '-') }}</span>
                            @if($student->classroom?->homeroomTeacher)
                                <br><span style="color: #64748b; font-size: 8.5px;">Wali: {{ $student->classroom->homeroomTeacher->name }}</span>
                            @endif
                        </td>
                        <td>
                            @if($isPdbk)
                                <span class="badge badge-pdbk">PDBK (Inklusi)</span>
                                @if($student->special_needs_type)
                                    <br><span style="color: #6b21a8; font-size: 8.5px; font-weight: 600;">{{ $student->special_needs_type }}</span>
                                @endif
                                @if($student->gpkTeacher)
                                    <br><span style="color: #64748b; font-size: 8px;">GPK: {{ $student->gpkTeacher->name }}</span>
                                @endif
                            @else
                                <span class="badge badge-reguler">Reguler</span>
                            @endif
                        </td>
                        <td>
                            @if($student->father_name)
                                <span style="font-weight: 600;">A: {{ $student->father_name }}</span>
                            @elseif($student->mother_name)
                                <span style="font-weight: 600;">I: {{ $student->mother_name }}</span>
                            @else
                                <span style="color: #94a3b8;">-</span>
                            @endif
                            @if($student->parent_phone || $student->father_phone || $student->mother_phone)
                                <br><span style="color: #059669; font-size: 8.5px; font-weight: 600;">WA: {{ $student->parent_phone ?: ($student->father_phone ?: $student->mother_phone) }}</span>
                            @endif
                        </td>
                        <td style="font-size: 8.5px; color: #334155;">
                            {{ $student->address ?? '-' }}
                            @if($student->rt || $student->rw)
                                (RT {{ $student->rt ?? '-' }}/RW {{ $student->rw ?? '-' }})
                            @endif
                            @if($student->city)
                                <br><span style="color: #64748b;">{{ $student->district ? $student->district . ', ' : '' }}{{ $student->city }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($statusRaw === 'aktif')
                                <span class="badge badge-aktif">Aktif</span>
                            @else
                                <span class="badge badge-nonaktif">{{ ucfirst($statusRaw) }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center" style="padding: 20px; color: #64748b;">
                            Tidak ada data siswa yang sesuai dengan filter yang dipilih.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- SIGNATURES SECTION -->
        <div class="signatures">
            <div class="signature-box">
                <p class="signature-title">Petugas Kesiswaan / Tata Usaha,</p>
                <div class="signature-space"></div>
                <p class="signature-name">Tri Wulandari, S.Pd</p>
                <p class="signature-title">NIY. 201208 2 045</p>
            </div>
            <div class="signature-box">
                <p class="signature-title">Malang, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
                <p class="signature-title">Kepala SD Anak Saleh,</p>
                <div class="signature-space"></div>
                <p class="signature-name">Drs. H. M. Naim, M.Pd</p>
                <p class="signature-title">NIY. 200507 1 001</p>
            </div>
        </div>

    </div>

</body>
</html>
