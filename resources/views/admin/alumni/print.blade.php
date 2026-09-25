<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Buku Induk Alumni - {{ $student->nis }} - {{ $student->full_name }}</title>
    <style>
        @page {
            size: A4;
            margin: 15mm 15mm 15mm 15mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.35;
            color: #111;
            background: #fff;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header h2 {
            margin: 0;
            font-size: 14pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header h3 {
            margin: 2px 0;
            font-size: 12pt;
            font-weight: normal;
        }
        .header p {
            margin: 0;
            font-size: 9pt;
            color: #444;
        }
        .title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin: 12px 0 6px 0;
        }
        .subtitle {
            text-align: center;
            font-size: 10pt;
            margin-bottom: 14px;
        }
        .section-title {
            font-weight: bold;
            font-size: 10.5pt;
            background-color: #f0f0f0;
            padding: 3px 6px;
            margin-top: 10px;
            margin-bottom: 4px;
            border-left: 4px solid #333;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin-bottom: 6px;
        }
        table.data-table td {
            padding: 2.5px 4px;
            vertical-align: top;
        }
        table.data-table td.label {
            width: 32%;
        }
        table.data-table td.colon {
            width: 2%;
            text-align: center;
        }
        table.data-table td.value {
            width: 66%;
            font-weight: 500;
        }
        table.bordered {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
            font-size: 9.5pt;
        }
        table.bordered th, table.bordered td {
            border: 1px solid #333;
            padding: 4px 6px;
            text-align: left;
        }
        table.bordered th {
            background-color: #f5f5f5;
            text-align: center;
        }
        .photo-box {
            width: 3cm;
            height: 4cm;
            border: 1px solid #777;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 8pt;
            color: #777;
            float: right;
            margin-left: 15px;
            margin-bottom: 10px;
        }
        .signatures {
            margin-top: 25px;
            width: 100%;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .sign-col {
            width: 40%;
            text-align: center;
            font-size: 10pt;
        }
        .sign-space {
            height: 60px;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <!-- Print Action Bar -->
    <div class="no-print" style="background: #eff6ff; border: 1px solid #bfdbfe; padding: 10px 15px; margin-bottom: 20px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <strong style="color: #1e40af; font-family: sans-serif; font-size: 13px;">Pratinjau Lembar Buku Induk Alumni Resmi</strong>
            <p style="margin: 2px 0 0 0; color: #1d4ed8; font-family: sans-serif; font-size: 11px;">Format standar A4 Buku Induk Register Kesiswaan SD Anak Saleh.</p>
        </div>
        <div>
            <button onclick="window.print()" style="background: #2563eb; color: white; border: none; padding: 7px 18px; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 12px; font-family: sans-serif;">
                🖨️ Cetak Dokumen (Print / PDF)
            </button>
        </div>
    </div>

    <!-- Kop Sekolah -->
    <div class="header">
        <h2>{{ setting('app_name', 'SD ANAK SALEH') }} MALANG</h2>
        <h3>YAYASAN PENDIDIKAN ANAK SALEH</h3>
        <p>Jl. Arumba No. 31, Tunggulwulung, Lowokwaru, Kota Malang, Jawa Timur | Telp: (0341) 480111</p>
    </div>

    <div class="title">LEMBAR BUKU INDUK PESERTA DIDIK (ALUMNI)</div>
    <div class="subtitle">Nomor Induk Siswa (NIS): <strong>{{ $student->nis }}</strong> &bull; NISN: <strong>{{ $student->nisn ?: '-' }}</strong></div>

    <!-- Photo Box -->
    <div class="photo-box">
        @if($student->student_photo_url)
            <img src="{{ $student->student_photo_url }}" style="width: 100%; height: 100%; object-fit: cover;">
        @else
            Pas Foto 3 x 4
        @endif
    </div>

    <!-- A. KETERANGAN PRIBADI SISWA -->
    <div class="section-title">A. KETERANGAN PESERTA DIDIK</div>
    <table class="data-table" style="width: calc(100% - 3.5cm);">
        <tr>
            <td class="label">1. Nama Lengkap</td>
            <td class="colon">:</td>
            <td class="value"><strong>{{ strtoupper($student->full_name) }}</strong></td>
        </tr>
        <tr>
            <td class="label">2. Nama Panggilan</td>
            <td class="colon">:</td>
            <td class="value">{{ $student->nickname ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">3. Jenis Kelamin</td>
            <td class="colon">:</td>
            <td class="value">{{ $student->formatted_gender }}</td>
        </tr>
        <tr>
            <td class="label">4. Tempat, Tanggal Lahir</td>
            <td class="colon">:</td>
            <td class="value">{{ $student->birth_place ?: '-' }}, {{ $student->birth_date ? $student->birth_date->translatedFormat('d F Y') : '-' }}</td>
        </tr>
        <tr>
            <td class="label">5. Agama</td>
            <td class="colon">:</td>
            <td class="value">{{ $student->religion ?: 'Islam' }}</td>
        </tr>
        <tr>
            <td class="label">6. Kewarganegaraan</td>
            <td class="colon">:</td>
            <td class="value">{{ $student->citizenship ?: 'WNI' }}</td>
        </tr>
        <tr>
            <td class="label">7. NIK / No. Akta Kelahiran</td>
            <td class="colon">:</td>
            <td class="value">{{ $student->nik ?: '-' }} / {{ $student->birth_certificate_no ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">8. Tipe Peserta Didik</td>
            <td class="colon">:</td>
            <td class="value">{{ $student->student_type ?: 'REGULER' }} {{ $student->special_needs_type ? "({$student->special_needs_type})" : '' }}</td>
        </tr>
    </table>

    <div style="clear: both;"></div>

    <!-- B. ALAMAT & DOMISILI -->
    <div class="section-title">B. KETERANGAN TEMPAT TINGGAL</div>
    <table class="data-table">
        <tr>
            <td class="label">1. Alamat Lengkap</td>
            <td class="colon">:</td>
            <td class="value">{{ $student->address ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">2. RT / RW / Kelurahan / Kecamatan</td>
            <td class="colon">:</td>
            <td class="value">RT {{ $student->rt ?: '-' }} / RW {{ $student->rw ?: '-' }}, Kel. {{ $student->village ?: '-' }}, Kec. {{ $student->district ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">3. Kabupaten/Kota / Kode Pos</td>
            <td class="colon">:</td>
            <td class="value">{{ $student->city ?: 'Malang' }}, {{ $student->province ?: 'Jawa Timur' }} (Kode Pos: {{ $student->postal_code ?: '-' }})</td>
        </tr>
        <tr>
            <td class="label">4. Tinggal Dengan / Jarak ke Sekolah</td>
            <td class="colon">:</td>
            <td class="value">{{ $student->residence_status ?: 'Orang Tua' }} / {{ $student->distance_to_school ?: '-' }}</td>
        </tr>
    </table>

    <!-- C. KESEHATAN & FISIK -->
    <div class="section-title">C. KESEHATAN & JASMANI</div>
    <table class="data-table">
        <tr>
            <td class="label">1. Golongan Darah / TB / BB</td>
            <td class="colon">:</td>
            <td class="value">Gol. {{ $student->blood_type ?: '-' }} &bull; Tinggi: {{ $student->height ? $student->height . ' cm' : '-' }} &bull; Berat: {{ $student->weight ? $student->weight . ' kg' : '-' }}</td>
        </tr>
        <tr>
            <td class="label">2. Riwayat Penyakit Berat / Alergi</td>
            <td class="colon">:</td>
            <td class="value">{{ $student->severe_disease_history ?: 'Tidak ada riwayat berat' }}</td>
        </tr>
    </table>

    <!-- D. ORANG TUA & WALI -->
    <div class="section-title">D. KETERANGAN ORANG TUA / WALI</div>
    <table class="data-table">
        <tr>
            <td class="label">1. Nama Ayah Kandung</td>
            <td class="colon">:</td>
            <td class="value"><strong>{{ $student->father_name ?: '-' }}</strong> (NIK: {{ $student->father_nik ?: '-' }})</td>
        </tr>
        <tr>
            <td class="label">2. Pekerjaan & Instansi Ayah</td>
            <td class="colon">:</td>
            <td class="value">{{ $student->father_job ?: '-' }} &bull; {{ $student->father_company ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">3. Nama Ibu Kandung</td>
            <td class="colon">:</td>
            <td class="value"><strong>{{ $student->mother_name ?: '-' }}</strong> (NIK: {{ $student->mother_nik ?: '-' }})</td>
        </tr>
        <tr>
            <td class="label">4. Pekerjaan & Instansi Ibu</td>
            <td class="colon">:</td>
            <td class="value">{{ $student->mother_job ?: '-' }} &bull; {{ $student->mother_company ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">5. Nomor Telepon / WhatsApp Ortu</td>
            <td class="colon">:</td>
            <td class="value">{{ $student->parent_phone ?: ($student->father_phone ?: ($student->mother_phone ?: '-')) }}</td>
        </tr>
    </table>

    <!-- E. ASAL SEKOLAH & PENERIMAAN -->
    <div class="section-title">E. PENDIDIKAN SEBELUMNYA & PENERIMAAN</div>
    <table class="data-table">
        <tr>
            <td class="label">1. Sekolah Asal (TK / SD Asal)</td>
            <td class="colon">:</td>
            <td class="value">{{ $student->previous_school ?: '-' }} (No. STTB: {{ $student->sttb_number_date ?: '-' }})</td>
        </tr>
        <tr>
            <td class="label">2. Diterima di Sekolah Ini Tanggal</td>
            <td class="colon">:</td>
            <td class="value">{{ $student->enrolled_date ? $student->enrolled_date->translatedFormat('d F Y') : '-' }}</td>
        </tr>
    </table>

    <!-- F. RIWAYAT ROMBEL / PERKEMBANGAN KELAS -->
    <div class="section-title">F. RIWAYAT PERJALANAN KELAS / AKADEMIK</div>
    <table class="bordered">
        <thead>
            <tr>
                <th style="width: 25%;">Tahun Pelajaran</th>
                <th style="width: 25%;">Tingkat / Rombel</th>
                <th style="width: 30%;">Wali Kelas</th>
                <th style="width: 20%;">Status Akhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($student->classroomHistories as $h)
                <tr>
                    <td style="text-align: center;">{{ $h->academicYear ? $h->academicYear->name : '-' }}</td>
                    <td><strong>{{ $h->classroom_name ?: ($h->classroom ? $h->classroom->name : '-') }}</strong></td>
                    <td>{{ $h->homeroom_teacher_name ?: ($h->classroom && $h->classroom->homeroomTeacher ? $h->classroom->homeroomTeacher->name : '-') }}</td>
                    <td style="text-align: center;">{{ ucfirst($h->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #777;">Belum ada catatan riwayat mutasi kelas</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- G. KELULUSAN & KELUAR -->
    <div class="section-title">G. KETERANGAN MENINGGALKAN SEKOLAH / KELULUSAN</div>
    <table class="data-table">
        <tr>
            <td class="label">1. Status Akhir Kesiswaan</td>
            <td class="colon">:</td>
            <td class="value"><strong>LULUS (ALUMNI)</strong></td>
        </tr>
        <tr>
            <td class="label">2. Tahun Pelajaran Kelulusan</td>
            <td class="colon">:</td>
            <td class="value"><strong>TP {{ $student->academicYear ? $student->academicYear->name : ($student->graduation_year ? "Tahun {$student->graduation_year}" : '-') }}</strong></td>
        </tr>
        <tr>
            <td class="label">3. Nomor Seri Ijazah</td>
            <td class="colon">:</td>
            <td class="value"><strong>{{ $student->diploma_number ?: '-' }}</strong></td>
        </tr>
        <tr>
            <td class="label">4. Melanjutkan ke Sekolah (SMP/MTs)</td>
            <td class="colon">:</td>
            <td class="value"><strong>{{ $student->continued_school ?: '-' }}</strong></td>
        </tr>
        @if($student->notes)
            <tr>
                <td class="label">Catatan Kelulusan</td>
                <td class="colon">:</td>
                <td class="value">{{ $student->notes }}</td>
            </tr>
        @endif
    </table>

    <!-- Tanda Tangan Resmi -->
    <div class="signatures">
        <div class="sign-col">
            <p>Mengetahui,<br>Kepala Sekolah</p>
            <div class="sign-space"></div>
            <p style="text-decoration: underline; font-weight: bold; margin-bottom: 2px;">( .................................................. )</p>
            <p style="margin: 0; font-size: 9pt;">NIP/NIY. .................................</p>
        </div>
        <div class="sign-col">
            <p>Malang, {{ now()->translatedFormat('d F Y') }}<br>Petugas Tata Usaha / Kesiswaan</p>
            <div class="sign-space"></div>
            <p style="text-decoration: underline; font-weight: bold; margin-bottom: 2px;">( .................................................. )</p>
            <p style="margin: 0; font-size: 9pt;">Staf Administrasi Kesiswaan</p>
        </div>
    </div>

</body>
</html>
