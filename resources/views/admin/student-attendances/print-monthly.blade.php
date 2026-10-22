<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi Murid - {{ $classroom->name }} - {{ $monthName }} {{ $year }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.5cm 1.5cm 1.5cm 1.5cm;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1e293b;
            line-height: 1.4;
            font-size: 10pt;
            margin: 0;
            padding: 20px;
            background: #fff;
        }
        .header {
            position: relative;
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 18px;
            min-height: 60px;
        }
        .header-logo {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 60px;
            height: 60px;
            object-fit: contain;
        }
        .header h1 {
            font-size: 13pt;
            margin: 0 0 4px 0;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header h2 {
            font-size: 11pt;
            margin: 0 0 4px 0;
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
        }
        .header p {
            font-size: 8.5pt;
            margin: 0;
            color: #475569;
        }
        .meta-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 14px;
            font-size: 9pt;
        }
        .meta-info div p {
            margin: 2px 0;
        }
        .meta-info span.label {
            font-weight: 600;
            color: #475569;
            display: inline-block;
            width: 100px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9pt;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
        }
        th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
            color: #0f172a;
        }
        td.center {
            text-align: center;
        }
        .total-row {
            background-color: #f8fafc;
            font-weight: bold;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            page-break-inside: avoid;
            font-size: 9.5pt;
        }
        .signature-box {
            width: 200px;
            text-align: center;
        }
        .signature-space {
            height: 70px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }
        .no-print {
            margin-bottom: 16px;
            text-align: right;
        }
        .btn-print {
            padding: 8px 16px;
            background: #0f172a;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 9pt;
            cursor: pointer;
            font-weight: bold;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="btn-print" onclick="window.print()">Cetak Dokumen / Simpan PDF</button>
        <button class="btn-print" style="background: #475569; margin-left: 8px;" onclick="window.close()">Tutup Jendela</button>
    </div>

    <!-- KOP / HEADER -->
    <div class="header">
        @if(setting('app_logo'))
            <img src="{{ asset('storage/' . setting('app_logo')) }}" alt="Logo" class="header-logo">
        @endif
        <div class="header-text" style="{{ setting('app_logo') ? 'padding: 0 70px;' : '' }}">
            <h1>REKAPITULASI PRESENSI KEHADIRAN MURID</h1>
            <h2>{{ strtoupper(setting('unit_name', setting('app_name', 'SD ANAK SALEH MALANG'))) }}</h2>
            <p>{{ setting('app_address', setting('school_address', 'Jl. Danau Ranau, Sawojajar, Kota Malang, Jawa Timur 65139')) }}</p>
            <p style="margin-top: 2px;">
                @if(setting('app_phone') || setting('school_phone'))
                    Telp/WA: {{ setting('app_phone', setting('school_phone')) }}
                @endif
                @if((setting('app_phone') || setting('school_phone')) && setting('app_email'))
                    &bull;
                @endif
                @if(setting('app_email'))
                    Email: {{ setting('app_email') }}
                @endif
            </p>
        </div>
    </div>

    <!-- META INFORMATION -->
    <div class="meta-info">
        <div>
            <p><span class="label">Kelompok/Rombel</span>: <strong>{{ $classroom->name }}</strong> ({{ $classroom->classLevel?->name ?? 'SMP' }})</p>
            <p><span class="label">Wali Kelas</span>: {{ $classroom->homeroomTeacher?->name ?? 'Belum Ditugaskan' }}</p>
        </div>
        <div style="text-align: right;">
            <p><span class="label" style="text-align: left;">Periode Bulan</span>: <strong>{{ $monthName }} {{ $year }}</strong></p>
            <p><span class="label" style="text-align: left;">Tanggal Cetak</span>: {{ date('d/m/Y H:i') }} WIB</p>
        </div>
    </div>

    <!-- TABEL REKAP -->
    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 80px;">NIS</th>
                <th style="text-align: left;">Nama Lengkap Murid</th>
                <th style="width: 45px;">L/P</th>
                <th style="width: 45px;">Hadir</th>
                <th style="width: 45px;">Sakit</th>
                <th style="width: 45px;">Izin</th>
                <th style="width: 45px;">Alpa</th>
                <th style="width: 65px;">Total Pertemuan</th>
                <th style="width: 65px;">% Kehadiran</th>
            </tr>
        </thead>
        <tbody>
            @forelse($monthlyRecap as $item)
                @php
                    $st = $item['student'];
                @endphp
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td class="center" style="font-family: monospace;">{{ $st->nis ?: '-' }}</td>
                    <td>{{ $st->full_name }}</td>
                    <td class="center">{{ $st->gender === 'L' || $st->gender === 'Laki-laki' ? 'L' : 'P' }}</td>
                    <td class="center" style="font-weight: bold; color: #059669;">{{ $item['h'] }}</td>
                    <td class="center" style="{{ $item['s'] > 0 ? 'font-weight: bold; color: #d97706;' : '' }}">{{ $item['s'] }}</td>
                    <td class="center" style="{{ $item['i'] > 0 ? 'font-weight: bold; color: #0284c7;' : '' }}">{{ $item['i'] }}</td>
                    <td class="center" style="{{ $item['a'] > 0 ? 'font-weight: bold; color: #e11d48;' : '' }}">{{ $item['a'] }}</td>
                    <td class="center" style="font-weight: 600;">{{ $item['total'] }}</td>
                    <td class="center" style="font-weight: bold;">{{ $item['rate'] }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="center" style="padding: 20px; color: #64748b;">
                        Tidak ada data murid pada rombel ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="center">TOTAL KELAS</td>
                <td class="center" style="color: #059669;">{{ $summaryTotals['h'] }}</td>
                <td class="center" style="color: #d97706;">{{ $summaryTotals['s'] }}</td>
                <td class="center" style="color: #0284c7;">{{ $summaryTotals['i'] }}</td>
                <td class="center" style="color: #e11d48;">{{ $summaryTotals['a'] }}</td>
                <td class="center">{{ $summaryTotals['meetings'] }}</td>
                <td class="center">{{ $summaryTotals['avg_rate'] }}%</td>
            </tr>
        </tfoot>
    </table>

    <!-- SIGNATURE SECTION -->
    <div class="signature-section">
        <div class="signature-box">
            <p>Mengetahui,</p>
            <p><strong>Kepala Sekolah</strong></p>
            <div class="signature-space"></div>
            <p class="signature-name">{{ $headmasterName ?? setting('headmaster_name', setting('school_principal_name', 'Andreas Setiyono, S.Pd.Gr., M.Kom.')) }}</p>
            <p style="font-size: 8pt; color: #64748b; margin-top: 2px;">
                NIP/NIY: {{ $headmasterNip ?? setting('headmaster_nip', setting('school_principal_nip', '-')) }}
            </p>
        </div>

        <div class="signature-box">
            <p>{{ setting('school_city', 'Malang') }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p><strong>Wali Kelas</strong></p>
            <div class="signature-space"></div>
            <p class="signature-name">{{ $classroom->homeroomTeacher?->name ?? '( ........................................ )' }}</p>
            <p style="font-size: 8pt; color: #64748b; margin-top: 2px;">
                NIP/NIY: {{ $classroom->homeroomTeacher?->nip ?? $classroom->homeroomTeacher?->niy ?? $classroom->homeroomTeacher?->nuptk ?? '-' }}
            </p>
        </div>
    </div>

</body>
</html>
