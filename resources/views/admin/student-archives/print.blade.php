<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Berkas Arsip Murid - {{ $className }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1cm 1.2cm 1cm 1.2cm;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1e293b;
            line-height: 1.35;
            font-size: 8.5pt;
            margin: 0;
            padding: 12px;
            background: #fff;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header h1 {
            font-size: 13pt;
            margin: 0 0 3px 0;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header h2 {
            font-size: 10.5pt;
            margin: 0 0 3px 0;
            font-weight: 600;
            color: #334155;
            text-transform: uppercase;
        }
        .header p {
            font-size: 8pt;
            margin: 0;
            color: #64748b;
        }
        .meta-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            font-size: 8.5pt;
        }
        .meta-info span.label {
            font-weight: 600;
            color: #475569;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 8pt;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            vertical-align: middle;
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
        .check-yes {
            color: #0f766e;
            font-weight: bold;
        }
        .check-no {
            color: #be123c;
            font-weight: 600;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            page-break-inside: avoid;
            font-size: 8.5pt;
        }
        .signature-box {
            width: 240px;
            text-align: center;
        }
        .signature-space {
            height: 60px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }
        .no-print {
            margin-bottom: 12px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }
        .btn-print {
            padding: 6px 12px;
            background: #0f766e;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 9pt;
        }
        .btn-close {
            padding: 6px 12px;
            background: #64748b;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 9pt;
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

    <!-- Non-print Action Toolbar -->
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">Cetak Dokumen (Ctrl+P)</button>
        <button class="btn-close" onclick="window.close()">Tutup Jendela</button>
    </div>

    <!-- Kop Lembaga -->
    <div class="header">
        <h1>{{ strtoupper(setting('unit_name', setting('app_name', 'SMP ISLAM TERPADU ANAK SALEH MALANG'))) }}</h1>
        <h2>REKAPITULASI KELENGKAPAN BERKAS ARSIP MURID</h2>
        <p>{{ setting('app_address', setting('school_address', 'Jl. Danau Ranau, Sawojajar, Kota Malang, Jawa Timur 65139')) }} | Telp/WA: {{ setting('app_phone', setting('school_phone', '0812-3456-7890')) }}</p>
    </div>

    <!-- Metadata Informasi -->
    <div class="meta-info">
        <div>
            <p><span class="label">Rombel / Kelas:</span> <strong>{{ $className }}</strong></p>
            <p><span class="label">Fokus Dokumen:</span> Akta Kelahiran, Kartu Keluarga, Pas Foto, KTP Orang Tua, & Buku KIA</p>
        </div>
        <div style="text-align: right;">
            <p><span class="label">Jumlah Murid:</span> <strong>{{ $students->count() }} Anak</strong></p>
            <p><span class="label">Dicetak Pada:</span> {{ date('d/m/Y H:i') }} WIB</p>
        </div>
    </div>

    <!-- Tabel Matriks Ceklis -->
    <table>
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 80px;">NIS</th>
                <th style="width: 180px;">Nama Lengkap Murid</th>
                <th style="width: 35px;">L/P</th>
                <th style="width: 75px;">Akta Lahir</th>
                <th style="width: 75px;">Kartu Keluarga</th>
                <th style="width: 65px;">Pas Foto</th>
                <th style="width: 65px;">KTP Ortu</th>
                <th style="width: 75px;">Buku KIA</th>
                <th style="width: 65px;">Lainnya</th>
                <th>Status Kelengkapan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $idx => $s)
                @php
                    $cl = $s->document_checklist;
                @endphp
                <tr>
                    <td class="center">{{ $idx + 1 }}</td>
                    <td class="center font-mono">{{ $s->nis ?: '-' }}</td>
                    <td><strong>{{ $s->full_name }}</strong></td>
                    <td class="center">{{ $s->gender ?: '-' }}</td>

                    <!-- Akta -->
                    <td class="center">
                        @if($cl['akta'])
                            <span class="check-yes">&#10003; Ada</span>
                        @else
                            <span class="check-no">- Kurang</span>
                        @endif
                    </td>

                    <!-- KK -->
                    <td class="center">
                        @if($cl['kk'])
                            <span class="check-yes">&#10003; Ada</span>
                        @else
                            <span class="check-no">- Kurang</span>
                        @endif
                    </td>

                    <!-- Foto -->
                    <td class="center">
                        @if($cl['foto'])
                            <span class="check-yes">&#10003; Ada</span>
                        @else
                            <span class="check-no">- Kurang</span>
                        @endif
                    </td>

                    <!-- KTP Ortu -->
                    <td class="center">
                        @if($cl['ktp'])
                            <span class="check-yes">&#10003; Ada</span>
                        @else
                            <span style="color:#94a3b8;">-</span>
                        @endif
                    </td>

                    <!-- KIA -->
                    <td class="center">
                        @if($cl['kia'])
                            <span class="check-yes">&#10003; Ada</span>
                        @else
                            <span style="color:#94a3b8;">-</span>
                        @endif
                    </td>

                    <!-- Lainnya -->
                    <td class="center">
                        {{ count($cl['other_docs']) > 0 ? count($cl['other_docs']) . ' Berkas' : '-' }}
                    </td>

                    <!-- Status -->
                    <td>
                        @if($cl['is_complete'])
                            <strong style="color: #047857;">Lengkap (Akta, KK & Foto Ada)</strong>
                        @else
                            <span style="color: #be123c;">Belum Lengkap (Kurang {{ 3 - $cl['core_count'] }} Dok)</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="center" style="padding: 20px; color: #64748b;">
                        Belum ada data murid pada rombel ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tanda Tangan -->
    <div class="signature-section">
        <div class="signature-box">
            <p>Wali Kelas / Petugas Administrasi,</p>
            <div class="signature-space"></div>
            <p class="signature-name">( ............................................ )</p>
            <p>NIP/NIY: ....................................</p>
        </div>

        <div class="signature-box">
            <p>{{ setting('school_city', 'Malang') }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p>Mengetahui,<br>Kepala {{ setting('unit_name', setting('app_name', 'SD Anak Saleh')) }}</p>
            <div class="signature-space"></div>
            <p class="signature-name">{{ $headmasterName ?? setting('headmaster_name', 'Andreas Setiyono, S.Pd.Gr., M.Kom.') }}</p>
            <p>NIP/NIY: {{ $headmasterNip ?? setting('headmaster_nip', '-') }}</p>
        </div>
    </div>

</body>
</html>
