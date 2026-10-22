<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Tamu - {{ $monthName }} {{ $selectedYear }}</title>
    <style>
        @page {
            size: a4 landscape;
            margin: 10mm 10mm 12mm 10mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #1e293b;
            line-height: 1.35;
            font-size: 8pt;
            background: #fff;
        }
        .header {
            position: relative;
            text-align: center;
            border-bottom: 2px solid #0f766e;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }
        .header-logo {
            position: absolute;
            top: 0;
            left: 0;
            max-height: 48px;
            width: auto;
        }
        .header h1 {
            font-size: 9pt;
            font-weight: bold;
            color: #0f766e;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin: 0;
        }
        .header h2 {
            font-size: 13pt;
            margin: 2px 0 0 0;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .meta-strip {
            display: table;
            width: 100%;
            margin-top: 6px;
            padding-top: 4px;
            border-top: 1px dashed #cbd5e1;
            font-size: 7.5pt;
            color: #475569;
        }
        .meta-cell {
            display: table-cell;
            vertical-align: middle;
        }
        .meta-left {
            text-align: left;
        }
        .meta-right {
            text-align: right;
        }
        .meta-tag {
            font-weight: bold;
            color: #0f172a;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 7.5pt;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 6px;
            vertical-align: top;
            text-align: left;
        }
        table.data-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
            color: #0f172a;
            text-transform: uppercase;
            font-size: 7pt;
            letter-spacing: 0.3px;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 6.5pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-umum {
            background-color: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
        }
        .badge-dinas {
            background-color: #e0e7ff;
            color: #3730a3;
            border: 1px solid #c7d2fe;
        }
        .badge-kesehatan {
            background-color: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }
        .badge-spmb {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            page-break-inside: avoid;
        }
        .signature-table td {
            border: none;
            padding: 0;
            vertical-align: top;
        }
        .footer-note {
            font-size: 7pt;
            color: #94a3b8;
            margin-top: 12px;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
        }
    </style>
</head>
<body>

    <!-- KOP LAPORAN -->
    <div class="header">
        @php
            $logoPath = null;
            if (setting('app_logo')) {
                $candidate = public_path('storage/' . setting('app_logo'));
                if (file_exists($candidate)) {
                    $logoPath = $candidate;
                }
            }
        @endphp
        @if($logoPath)
            <img src="{{ $logoPath }}" alt="Logo" class="header-logo">
        @endif
        <div style="{{ $logoPath ? 'padding: 0 55px;' : '' }}">
            <h1 style="font-size: 12pt; font-weight: bold; color: #0f172a; margin: 0;">{{ strtoupper($appName ?? setting('unit_name', setting('app_name', 'SAANS SMP MALANG'))) }}</h1>
            <h2 style="font-size: 9.5pt; color: #0f766e; margin: 2px 0 0 0;">BUKU REGISTER KUNJUNGAN TAMU & KEDINASAN</h2>
            <p style="font-size: 7.5pt; color: #475569; margin: 2px 0 0 0;">{{ $appAddress ?? setting('app_address') }} | Telp/WA: {{ $appPhone ?? setting('app_phone') }}</p>
        </div>
    </div>

    <!-- METADATA PERIODE -->
    <div class="meta-strip">
        <div class="meta-cell meta-left">
            <span>Periode: <span class="meta-tag">{{ $monthName }} {{ $selectedYear }}</span></span>
            &nbsp;|&nbsp;
            <span>Kategori: <span class="meta-tag">{{ $selectedCategory !== 'all' ? $selectedCategory : 'Semua Kategori' }}</span></span>
        </div>
        <div class="meta-cell meta-right">
            <span>Total Kunjungan: <span class="meta-tag">{{ $guests->count() }} Tamu</span></span>
            &nbsp;|&nbsp;
            <span>Dicetak: <span class="meta-tag">{{ date('d/m/Y H:i') }} WIB</span></span>
        </div>
    </div>

    <!-- TABEL DATA -->
    <table class="data-table" style="margin-top: 8px;">
        <thead>
            <tr>
                <th width="3%" class="text-center">No</th>
                <th width="10%">Tanggal & Waktu</th>
                <th width="16%">Nama Tamu & Kontak</th>
                <th width="18%">Instansi & Jabatan</th>
                <th width="14%">Kota & Alamat</th>
                <th width="18%">Keperluan / Tujuan</th>
                <th width="11%">Penerima</th>
                <th width="10%">Kesan & Pesan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($guests as $index => $g)
                @php
                    $badgeClass = 'badge-umum';
                    if (str_contains($g->category, 'Dinas')) {
                        $badgeClass = 'badge-dinas';
                    } elseif (str_contains($g->category, 'Puskesmas') || str_contains($g->category, 'Dinkes')) {
                        $badgeClass = 'badge-kesehatan';
                    } elseif (str_contains($g->category, 'SPMB')) {
                        $badgeClass = 'badge-spmb';
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ \Carbon\Carbon::parse($g->date)->format('d/m/Y') }}</strong><br>
                        <span style="color: #64748b; font-size: 7pt;">Pkl. {{ $g->formatted_time }} WIB</span>
                    </td>
                    <td>
                        <strong style="color: #0f172a;">{{ $g->name }}</strong><br>
                        <span style="color: #047857; font-family: monospace; font-size: 7pt;">{{ $g->phone ?: '-' }}</span><br>
                        <span class="badge {{ $badgeClass }}">{{ $g->category }}</span>
                        @if($g->category === 'SPMB' && $g->student_name)
                            <div style="margin-top: 3px; font-size: 6.8pt; color: #0f766e; background: #f0fdfa; padding: 2px 4px; border-radius: 2px; border: 1px solid #ccfbf1;">
                                <strong>Calon Murid:</strong> {{ $g->student_name }}
                                @if($g->student_birth_date)
                                    <br><span style="color: #475569;">Tgl Lahir: {{ \Carbon\Carbon::parse($g->student_birth_date)->format('d/m/Y') }}</span>
                                @endif
                                @if($g->therapy_history)
                                    <br><span style="color: #475569;">Terapi: {{ $g->therapy_history }} {{ $g->therapy_diagnosis ? '(' . $g->therapy_diagnosis . ')' : '' }}</span>
                                @endif
                            </div>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $g->institution ?: '-' }}</strong><br>
                        <span style="color: #475569; font-size: 7pt;">{{ $g->position ?: '-' }}</span>
                        @if($g->occupation && $g->occupation !== $g->position)
                            <span style="color: #94a3b8; font-size: 6.5pt;">({{ $g->occupation }})</span>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $g->city ?: '-' }}</strong>{{ $g->province ? ', ' . $g->province : '' }}<br>
                        <span style="color: #64748b; font-size: 6.5pt;">{{ $g->address ?: '-' }}</span>
                    </td>
                    <td>
                        {{ $g->purpose }}
                    </td>
                    <td>
                        <strong style="color: #334155;">{{ $g->host_recipient ?: '-' }}</strong>
                    </td>
                    <td>
                        <em style="color: #475569; font-size: 7pt;">{{ $g->feedback ?: '-' }}</em>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; color: #94a3b8;">
                        Tidak ada catatan kunjungan tamu pada periode dan kategori yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- TANDA TANGAN -->
    <table class="signature-table">
        <tr>
            <td width="50%" style="text-align: center;">
                <p style="margin: 0; font-size: 7.5pt; color: #475569;">Petugas Penerima Tamu / Piket,</p>
                <div style="height: 48px;"></div>
                <p style="margin: 0; font-size: 8pt; font-weight: bold; color: #0f172a;">( .................................................... )</p>
                <p style="margin: 2px 0 0 0; font-size: 7pt; color: #64748b;">NIP/NIY: ................................................</p>
            </td>
            <td width="50%" style="text-align: center;">
                <p style="margin: 0; font-size: 7.5pt; color: #475569;">{{ $schoolCity }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                <p style="margin: 2px 0 0 0; font-size: 7.5pt; font-weight: bold; color: #1e293b;">Mengetahui, Kepala Sekolah</p>
                <div style="height: 48px;"></div>
                <p style="margin: 0; font-size: 8pt; font-weight: bold; text-decoration: underline; color: #0f172a;">{{ $headmasterName }}</p>
                <p style="margin: 2px 0 0 0; font-size: 7pt; color: #64748b;">NIP. {{ $headmasterNip }}</p>
            </td>
        </tr>
    </table>

    <!-- FOOTER NOTE -->
    <div class="footer-note">
        Dokumen resmi Buku Tamu diterbitkan melalui Sistem Informasi & Manajemen Terpadu {{ $appName ?? setting('unit_name', setting('app_name', 'SAANS SMP')) }}.
    </div>

</body>
</html>
