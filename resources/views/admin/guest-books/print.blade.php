<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Tamu & Kunjungan Lembaga - {{ $monthName }} {{ $selectedYear }}</title>
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
            padding: 5px 6px;
            vertical-align: top;
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
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 7.5pt;
            font-weight: bold;
            border: 1px solid #94a3b8;
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

    <!-- Tombol Aksi Non-Print -->
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">Cetak Dokumen (Ctrl+P)</button>
        <button class="btn-close" onclick="window.close()">Tutup Jendela</button>
    </div>

    <!-- Kop Laporan -->
    <div class="header">
        @php
            $logoPath = null;
            if (setting('app_logo')) {
                $candidate = public_path('storage/' . setting('app_logo'));
                if (file_exists($candidate)) {
                    $logoPath = asset('storage/' . setting('app_logo'));
                }
            }
        @endphp
        @if($logoPath)
            <img src="{{ $logoPath }}" alt="Logo" class="header-logo" style="max-height: 50px; float: left; margin-right: 15px;">
        @endif
        <h1>{{ strtoupper($appName ?? setting('unit_name', setting('app_name', 'SAANS SMP MALANG'))) }}</h1>
        <h2>BUKU REGISTER KUNJUNGAN TAMU & KEDINASAN</h2>
        <p>{{ $appAddress ?? setting('app_address') }} | Telp/WA: {{ $appPhone ?? setting('app_phone') }}</p>
    </div>

    <!-- Metadata Informasi Periode -->
    <div class="meta-info">
        <div>
            <p><span class="label">Bulan / Periode:</span> {{ $monthName }} {{ $selectedYear }}</p>
            <p><span class="label">Kategori Layanan:</span> {{ $selectedCategory !== 'all' ? $selectedCategory : 'Semua Kategori (Umum, Kedinasan, Puskesmas/Dinkes, SPMB)' }}</p>
        </div>
        <div style="text-align: right;">
            <p><span class="label">Total Tamu:</span> <strong>{{ $guests->count() }} Orang</strong></p>
            <p><span class="label">Waktu Cetak:</span> {{ date('d/m/Y H:i') }} WIB</p>
        </div>
    </div>

    <!-- Tabel Data Buku Tamu -->
    <table>
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 70px;">Tanggal</th>
                <th style="width: 45px;">Waktu</th>
                <th style="width: 120px;">Nama Tamu</th>
                <th style="width: 80px;">No. HP</th>
                <th style="width: 65px;">Kategori</th>
                <th style="width: 120px;">Institusi / Instansi</th>
                <th style="width: 80px;">Jabatan / Pekerjaan</th>
                <th style="width: 90px;">Kota / Provinsi</th>
                <th>Keperluan / Maksud Kunjungan</th>
                <th style="width: 90px;">Penerima Tamu</th>
                <th style="width: 100px;">Kesan & Pesan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($guests as $idx => $g)
                <tr>
                    <td class="center">{{ $idx + 1 }}</td>
                    <td class="center">{{ \Carbon\Carbon::parse($g->date)->format('d/m/Y') }}</td>
                    <td class="center">{{ $g->formatted_time }}</td>
                    <td>
                        <strong>{{ $g->name }}</strong>
                        @if($g->category === 'SPMB' && $g->student_name)
                            <div style="margin-top: 2px; font-size: 7.5pt; color: #0f766e;">
                                <strong>Calon Murid:</strong> {{ $g->student_name }}
                                @if($g->student_birth_date)
                                    <br><span style="color: #64748b;">Tgl Lahir: {{ \Carbon\Carbon::parse($g->student_birth_date)->format('d/m/Y') }}</span>
                                @endif
                                @if($g->therapy_history)
                                    <br><span style="color: #64748b;">Terapi: {{ $g->therapy_history }} {{ $g->therapy_diagnosis ? '(' . $g->therapy_diagnosis . ')' : '' }}</span>
                                @endif
                            </div>
                        @endif
                    </td>
                    <td class="center">{{ $g->phone }}</td>
                    <td class="center">
                        <span class="badge">{{ $g->category }}</span>
                    </td>
                    <td>{{ $g->institution ?: '-' }}</td>
                    <td>
                        {{ $g->position ?: '-' }}
                        @if($g->occupation && $g->occupation !== $g->position)
                            <br><small style="color:#64748b;">({{ $g->occupation }})</small>
                        @endif
                    </td>
                    <td>{{ $g->city ?: '-' }}{{ $g->province ? ', ' . $g->province : '' }}</td>
                    <td>{{ $g->purpose }}</td>
                    <td>{{ $g->host_recipient ?: '-' }}</td>
                    <td><em>{{ $g->feedback ?: '-' }}</em></td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="center" style="padding: 25px; color: #64748b;">
                        Tidak ada data kunjungan tamu pada periode dan kategori yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tanda Tangan Mengetahui -->
    <div class="signature-section">
        <div class="signature-box">
            <p>Petugas Piket / Penerima Tamu,</p>
            <div class="signature-space"></div>
            <p class="signature-name">( ............................................ )</p>
            <p>NIP/NIY: ....................................</p>
        </div>

        <div class="signature-box">
            <p>{{ $schoolCity }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p>Mengetahui,<br>Kepala Sekolah</p>
            <div class="signature-space"></div>
            <p class="signature-name">{{ $headmasterName }}</p>
            <p>NIP: {{ $headmasterNip }}</p>
        </div>
    </div>

</body>
</html>
