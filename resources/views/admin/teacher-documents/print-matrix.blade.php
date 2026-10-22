<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matriks Kelengkapan Berkas Guru - T.A {{ $yearName }}</title>
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
            color: #94a3b8;
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
            background: #4f46e5;
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
        <h2>LEMBAR SUPERVISI & REKAPITULASI KELENGKAPAN BERKAS GURU</h2>
        <p>{{ setting('app_address', setting('school_address', 'Jl. Danau Ranau, Sawojajar, Kota Malang, Jawa Timur 65139')) }} | Telp/WA: {{ setting('app_phone', setting('school_phone', '0812-3456-7890')) }}</p>
    </div>

    <!-- Metadata Informasi -->
    <div class="meta-info">
        <div>
            <p><span class="label">Tahun Ajaran:</span> <strong>{{ $yearName }}</strong></p>
            <p><span class="label">Fokus Supervisi:</span> Perangkat Tahunan (PROTA, PROSEM), Pembelajaran (RPP/Modul Ajar), & Anggaran (RKAS, LPJ)</p>
        </div>
        <div style="text-align: right;">
            <p><span class="label">Jumlah Tenaga Pendidik:</span> <strong>{{ count($matrixData) }} Orang</strong></p>
            <p><span class="label">Dicetak Pada:</span> {{ date('d/m/Y H:i') }} WIB</p>
        </div>
    </div>

    <!-- Tabel Matriks Ceklis -->
    <table>
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 170px;">Nama Guru / Pendidik</th>
                <th style="width: 90px;">NIP / NIY</th>
                <th style="width: 100px;">Jabatan</th>
                <th style="width: 65px;">PROTA</th>
                <th style="width: 65px;">PROSEM</th>
                <th style="width: 65px;">RPP</th>
                <th style="width: 65px;">RKAS</th>
                <th style="width: 65px;">LPJ</th>
                <th style="width: 65px;">Lainnya</th>
                <th style="width: 75px;">Total File</th>
                <th>Status Supervisi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($matrixData as $idx => $m)
                @php
                    $isComplete = ($m['categories']['PROTA']['count'] > 0) &&
                                  ($m['categories']['PROSEM']['count'] > 0) &&
                                  ($m['categories']['RPP']['count'] > 0);
                @endphp
                <tr>
                    <td class="center">{{ $idx + 1 }}</td>
                    <td><strong>{{ $m['teacher']->name }}</strong></td>
                    <td class="center">{{ $m['teacher']->niy ?: ($m['teacher']->nik ?: '-') }}</td>
                    <td>{{ $m['teacher']->position ?: 'Guru' }}</td>

                    @foreach($categories as $cat)
                        @php
                            $c = $m['categories'][$cat]['count'];
                            $app = $m['categories'][$cat]['approved'];
                        @endphp
                        <td class="center">
                            @if($c > 0)
                                <span class="check-yes">&#10003; ({{ $c }})</span>
                            @else
                                <span class="check-no">-</span>
                            @endif
                        </td>
                    @endforeach

                    <td class="center"><strong>{{ $m['total_uploaded'] }}</strong></td>
                    <td>
                        @if($isComplete)
                            <strong style="color: #047857;">Lengkap (Perangkat Inti Ada)</strong>
                        @elseif($m['total_uploaded'] > 0)
                            <span style="color: #b45309;">Sebagian Terunggah</span>
                        @else
                            <span style="color: #be123c;">Belum Mengumpulkan</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="center" style="padding: 20px; color: #64748b;">
                        Belum ada data guru pada tahun ajaran ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tanda Tangan -->
    <div class="signature-section">
        <div class="signature-box">
            <p>Waka Kurikulum / Tim Supervisi,</p>
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
