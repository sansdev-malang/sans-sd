<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Distribusi Peserta Didik & Ketenagaan Rombel - {{ $selectedYear ? $selectedYear->name : '' }}</title>
    
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
            font-size: 11px;
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
            transition: background-color 0.2s;
        }

        .btn-primary {
            background-color: #4338ca;
            color: #ffffff;
        }
        .btn-primary:hover {
            background-color: #3730a3;
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
            max-width: 1050px;
            margin: 0 auto;
            padding: 32px 40px;
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
            padding-bottom: 14px;
            margin-bottom: 20px;
        }

        .header-logo {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }

        .header-text {
            flex: 1;
            text-align: center;
        }

        .header-text h1 {
            font-size: 16px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .header-text h2 {
            font-size: 14px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 4px;
        }

        .header-text p {
            font-size: 10px;
            color: #64748b;
        }

        /* DOCUMENT TITLE */
        .doc-title {
            text-align: center;
            margin-bottom: 16px;
        }

        .doc-title h3 {
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .doc-title p {
            font-size: 11px;
            font-weight: 600;
            color: #475569;
        }

        /* TABLE STYLING */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 16px;
        }

        th, td {
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
            vertical-align: middle;
        }

        th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 700;
            text-align: center;
            text-transform: uppercase;
            font-size: 9.5px;
            letter-spacing: 0.3px;
        }

        .level-header {
            background-color: #f8fafc;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 10px;
            color: #1e293b;
        }

        .subtotal-row {
            background-color: #e2e8f0;
            font-weight: 700;
            color: #0f172a;
        }

        .grand-total-row {
            background-color: #1e293b;
            color: #ffffff;
            font-weight: 800;
            font-size: 11px;
        }
        .grand-total-row td {
            border-color: #0f172a;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: 700; }
        .font-mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; }

        /* SIGNATURES */
        .signatures {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }

        .signature-box {
            width: 250px;
            text-align: center;
        }

        .signature-box .date {
            margin-bottom: 8px;
            font-size: 10.5px;
        }

        .signature-box .role {
            font-size: 10.5px;
            font-weight: 600;
            margin-bottom: 60px;
        }

        .signature-box .name {
            font-size: 11px;
            font-weight: 700;
            text-decoration: underline;
        }

        .signature-box .nip {
            font-size: 9.5px;
            color: #475569;
        }

        @media print {
            body {
                background-color: #ffffff;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .print-page {
                border: none;
                box-shadow: none;
                padding: 10px;
                max-width: 100%;
            }
            th, td {
                border-color: #94a3b8 !important;
            }
            .grand-total-row {
                background-color: #334155 !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .subtotal-row {
                background-color: #e2e8f0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .level-header {
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <!-- NO PRINT CONTROLS -->
    <div class="no-print">
        <div>
            <span style="font-weight: 700; font-size: 13px;">Pratinjau Cetak Laporan Distribusi Rombel</span>
            <span style="color: #64748b; font-size: 11px; margin-left: 8px;">(Format resmi untuk Pelaporan & Arsip TU)</span>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn btn-primary">
                Cetak / Simpan PDF
            </button>
            <button onclick="window.close()" class="btn btn-secondary">
                Tutup
            </button>
        </div>
    </div>

    @php
        $logoBase64 = null;
        $logoSetting = setting('app_logo');
        $candidatePaths = [];
        if ($logoSetting) {
            $candidatePaths[] = storage_path('app/public/' . $logoSetting);
            $candidatePaths[] = public_path('storage/' . $logoSetting);
        }
        $candidatePaths[] = public_path('icons/icon-192x192.png');
        $candidatePaths[] = public_path('icons/icon-512x512.png');

        foreach ($candidatePaths as $p) {
            if (file_exists($p) && is_file($p)) {
                $ext = strtolower(pathinfo($p, PATHINFO_EXTENSION));
                $mime = match($ext) {
                    'png' => 'image/png',
                    'jpg', 'jpeg' => 'image/jpeg',
                    'svg' => 'image/svg+xml',
                    'ico' => 'image/x-icon',
                    'webp' => 'image/webp',
                    default => 'image/png',
                };
                $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($p));
                break;
            }
        }
    @endphp

    <!-- PRINT SHEET -->
    <div class="print-page">
        <!-- KOP SURAT -->
        <div class="header">
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" alt="Logo" class="header-logo">
            @else
                <div class="header-logo" style="display: flex; align-items: center; justify-content: center; background: #4338ca; color: white; border-radius: 8px; font-weight: 800; font-size: 24px;">
                    {{ substr(setting('unit_name', 'S'), 0, 1) }}
                </div>
            @endif
            <div class="header-text">
                <h1>{{ strtoupper(setting('unit_name', 'SD Anak Saleh')) }}</h1>
                <h2>YAYASAN PENDIDIKAN ANAK SALEH MALANG</h2>
                <p>{{ setting('school_address', 'Jl. Arumba No. 31, Tunggulwulung, Kec. Lowokwaru, Kota Malang, Jawa Timur 65143') }}</p>
                <p>Telp: {{ setting('school_phone', '(0341) 480280') }} | Email: {{ setting('school_email', 'info@sdanaksaleh.sch.id') }}</p>
            </div>
        </div>

        <!-- DOCUMENT TITLE -->
        <div class="doc-title">
            <h3>REKAPITULASI DISTRIBUSI PESERTA DIDIK & KETENAGAAN ROMBEL</h3>
            <p>TAHUN PELAJARAN: {{ $selectedYear ? strtoupper($selectedYear->name) : 'SEMUA TAHUN' }}</p>
        </div>

        <!-- TABLE -->
        <table>
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th style="width: 120px;">Nama Kelas</th>
                    <th style="width: 45px;">Kode</th>
                    <th style="width: 55px;">L</th>
                    <th style="width: 55px;">P</th>
                    <th style="width: 65px;">Jml Siswa</th>
                    <th style="width: 65px;">Inklusi (PDBK)</th>
                    <th>Wali Kelas</th>
                    <th>Guru Pendamping Khusus (GPK)</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($levelReports as $lvl)
                    @if(count($lvl['classrooms']) > 0)
                        <!-- LEVEL HEADER -->
                        <tr class="level-header">
                            <td colspan="9" style="padding-left: 8px;">
                                {{ strtoupper($lvl['level']->name) }}
                            </td>
                        </tr>

                        <!-- CLASSROOMS -->
                        @foreach($lvl['classrooms'] as $cr)
                            <tr>
                                <td class="text-center font-mono">{{ $no++ }}</td>
                                <td class="font-bold">{{ $cr['name'] }}</td>
                                <td class="text-center font-mono font-bold">{{ $cr['code'] }}</td>
                                <td class="text-center font-mono">{{ $cr['male'] }}</td>
                                <td class="text-center font-mono">{{ $cr['female'] }}</td>
                                <td class="text-center font-mono font-bold">{{ $cr['total'] }}</td>
                                <td class="text-center font-mono {{ $cr['pdbk'] > 0 ? 'font-bold' : '' }}">
                                    {{ $cr['pdbk'] }}
                                </td>
                                <td>{{ $cr['homeroom_teacher'] ?: '-' }}</td>
                                <td>
                                    @if(!empty($cr['gpk_teachers']))
                                        {{ implode(', ', $cr['gpk_teachers']) }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                        <!-- SUBTOTAL -->
                        <tr class="subtotal-row">
                            <td colspan="3" class="text-right" style="font-size: 9.5px;">
                                Jml Siswa @ {{ $lvl['level']->name }}:
                            </td>
                            <td class="text-center font-mono">{{ $lvl['subtotal_male'] }}</td>
                            <td class="text-center font-mono">{{ $lvl['subtotal_female'] }}</td>
                            <td class="text-center font-mono font-bold">{{ $lvl['subtotal_students'] }}</td>
                            <td class="text-center font-mono">{{ $lvl['subtotal_pdbk'] }}</td>
                            <td colspan="2" style="font-size: 9px; font-weight: normal; font-style: italic;">
                                Total {{ count($lvl['classrooms']) }} rombel
                            </td>
                        </tr>
                    @endif
                @endforeach

                <!-- GRAND TOTAL -->
                <tr class="grand-total-row">
                    <td colspan="3" class="text-right" style="padding: 7px 10px;">
                        JUMLAH PESERTA DIDIK KESELURUHAN:
                    </td>
                    <td class="text-center font-mono">{{ $grandTotalMale }}</td>
                    <td class="text-center font-mono">{{ $grandTotalFemale }}</td>
                    <td class="text-center font-mono font-bold" style="font-size: 12px;">{{ $grandTotalStudents }}</td>
                    <td class="text-center font-mono">{{ $grandTotalPdbk }}</td>
                    <td colspan="2" style="padding: 7px 10px; font-size: 9.5px; font-weight: normal;">
                        Rasio: {{ $malePercent }}% Laki-laki / {{ $femalePercent }}% Perempuan ({{ $grandTotalClassrooms }} Rombel)
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- SIGNATURES -->
        <div class="signatures">
            <div class="signature-box">
                <div class="role">Mengetahui,<br>Kepala Sekolah</div>
                <div class="name">{{ $headmasterName ?? setting('headmaster_name', 'Andreas Setiyono, S.Pd.Gr., M.Kom.') }}</div>
                <div class="nip">{{ !empty($headmasterNiy) ? 'NIY. ' . $headmasterNiy : (!empty(setting('headmaster_nip')) ? 'NIP. ' . setting('headmaster_nip') : '') }}</div>
            </div>

            <div class="signature-box">
                <div class="date">Malang, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</div>
                <div class="role">Waka Kesiswaan & Kurikulum</div>
                <div class="name">{{ setting('vice_headmaster_name', 'Waka Kurikulum / Kesiswaan') }}</div>
                <div class="nip">NIP. {{ setting('vice_headmaster_nip', '-') }}</div>
            </div>
        </div>
    </div>
</body>
</html>
