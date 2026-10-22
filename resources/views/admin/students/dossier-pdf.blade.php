<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Dossier Siswa - {{ $student->full_name }} ({{ $student->nis ?? $student->id }})</title>
    <style>
        @page {
            margin: 10mm 12mm 10mm 12mm;
            size: a4 portrait;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8pt;
            color: #1e293b;
            line-height: 1.35;
        }
        
        /* HEADER KOP */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        .kop-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }
        .school-name {
            font-size: 13pt;
            font-weight: bold;
            color: #1e1b4b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: center;
        }
        .school-subtitle {
            font-size: 8.5pt;
            font-weight: 600;
            color: #4f46e5;
            text-align: center;
            margin-top: 1px;
        }
        .school-address {
            font-size: 7.5pt;
            color: #64748b;
            text-align: center;
            margin-top: 2px;
        }
        .kop-line {
            border-top: 2px solid #4f46e5;
            border-bottom: 1px solid #94a3b8;
            height: 2px;
            margin: 6px 0 10px 0;
        }

        /* DOCUMENT TITLE */
        .doc-title-container {
            text-align: center;
            margin-bottom: 10px;
        }
        .doc-title {
            font-size: 11pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .doc-subtitle {
            font-size: 8pt;
            color: #475569;
            margin-top: 2px;
        }

        /* SECTION HEADER */
        .section-header {
            background-color: #4f46e5;
            color: #ffffff;
            font-weight: bold;
            font-size: 7.5pt;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 3.5px 6px;
            border-radius: 2px;
            margin-top: 8px;
            margin-bottom: 4px;
        }

        /* DATA TABLE */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        table.data-table td, table.data-table th {
            padding: 3.5px 5px;
            font-size: 7.8pt;
            vertical-align: top;
            border: 1px solid #e2e8f0;
        }
        .td-label {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            width: 25%;
        }
        .td-val {
            color: #0f172a;
            font-weight: normal;
            width: 25%;
        }
        .td-val-full {
            color: #0f172a;
            font-weight: normal;
            width: 75%;
        }

        /* PHOTO BOX */
        .photo-container {
            width: 85px;
            height: 110px;
            border: 1px solid #cbd5e1;
            padding: 2px;
            text-align: center;
            background-color: #ffffff;
        }
        .photo-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .photo-placeholder {
            width: 100%;
            height: 100%;
            background-color: #f1f5f9;
            display: table;
            text-align: center;
        }
        .photo-placeholder-text {
            display: table-cell;
            vertical-align: middle;
            font-size: 7pt;
            color: #94a3b8;
            font-weight: bold;
        }

        /* COMPARATIVE TABLE (ORANG TUA) */
        table.comp-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        table.comp-table th {
            background-color: #3b82f6;
            color: #ffffff;
            font-size: 7.5pt;
            text-transform: uppercase;
            font-weight: bold;
            padding: 4px 6px;
            text-align: left;
            border: 1px solid #93c5fd;
        }
        table.comp-table td {
            padding: 3.5px 6px;
            font-size: 7.8pt;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        table.comp-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        /* SIGNATURE SECTION */
        .sig-container {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .sig-container td {
            border: none;
            text-align: center;
            vertical-align: top;
            font-size: 8pt;
            padding: 0 10px;
        }
        .sig-space {
            height: 48px;
        }
        .sig-name {
            font-weight: bold;
            text-decoration: underline;
            color: #0f172a;
        }
        .sig-title {
            font-size: 7.5pt;
            color: #64748b;
        }

        .footer-note {
            margin-top: 10px;
            font-size: 6.8pt;
            color: #94a3b8;
            text-align: right;
            border-top: 1px dashed #cbd5e1;
            padding-top: 4px;
        }
    </style>
</head>
<body>

    @php
        $photoSrc = null;
        if (!empty($student->student_photo_url)) {
            if (filter_var($student->student_photo_url, FILTER_VALIDATE_URL)) {
                $photoSrc = $student->student_photo_url;
            } else {
                $filePath = public_path(ltrim($student->student_photo_url, '/'));
                if (file_exists($filePath)) {
                    $type = pathinfo($filePath, PATHINFO_EXTENSION);
                    $data = @file_get_contents($filePath);
                    if ($data) {
                        $photoSrc = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    }
                }
            }
        }
    @endphp

    <!-- KOP RESMI LEMBAGA -->
    <table class="kop-table">
        <tr>
            @if(!empty($logoSrc))
                <td style="width: 55px; vertical-align: middle; text-align: left;">
                    <img src="{{ $logoSrc }}" style="height: 46px; width: auto; max-width: 55px;" alt="Logo">
                </td>
            @endif
            <td style="vertical-align: middle; text-align: center;">
                <div class="school-name">{{ strtoupper($schoolName) }}</div>
                <div class="school-subtitle">SEKOLAH MENENGAH PERTAMA (SMP) ISLAM TERPADU</div>
                <div class="school-address">{{ $schoolAddress }} &bull; Telp: {{ $schoolPhone }} &bull; Email: {{ $schoolEmail }}</div>
            </td>
            @if(!empty($logoSrc))
                <td style="width: 55px;"></td>
            @endif
        </tr>
    </table>
    <div class="kop-line"></div>

    <!-- JUDUL DOKUMEN -->
    <div class="doc-title-container">
        <h1 class="doc-title">LEMBAR DOSSIER &amp; BUKU INDUK SISWA</h1>
        <div class="doc-subtitle">
            Nomor Induk Siswa (NIS): <strong>{{ $student->nis ?? '-' }}</strong> &bull; NISN: <strong>{{ $student->nisn ?? '-' }}</strong> &bull; Status: <strong>{{ strtoupper($student->status ?? 'Aktif') }}</strong>
        </div>
    </div>

    <!-- HERO PROFIL SINGKAT DENGAN PAS FOTO -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 6px;">
        <tr>
            <td style="width: 95px; vertical-align: top; border: none; padding: 0 10px 0 0;">
                <div class="photo-container">
                    @if($photoSrc)
                        <img src="{{ $photoSrc }}" class="photo-img" alt="Foto Siswa">
                    @else
                        <div class="photo-placeholder">
                            <div class="photo-placeholder-text">PAS FOTO<br>3 x 4 cm</div>
                        </div>
                    @endif
                </div>
            </td>
            <td style="vertical-align: top; border: none; padding: 0;">
                <table class="data-table">
                    <tr>
                        <td class="td-label">Nama Lengkap Siswa</td>
                        <td class="td-val-full" colspan="3" style="font-size: 9pt; font-weight: bold; color: #1e1b4b;">{{ strtoupper($student->full_name) }}</td>
                    </tr>
                    <tr>
                        <td class="td-label">Nama Panggilan</td>
                        <td class="td-val"><strong>{{ $student->nickname ?? '-' }}</strong></td>
                        <td class="td-label">Jenis Kelamin</td>
                        <td class="td-val">{{ $student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                    </tr>
                    <tr>
                        <td class="td-label">Tempat, Tanggal Lahir</td>
                        <td class="td-val">{{ $student->birth_place ?? '-' }}, {{ $student->birth_date ? $student->birth_date->translatedFormat('d F Y') : '-' }}</td>
                        <td class="td-label">Usia Saat Ini</td>
                        <td class="td-val">{{ $student->age }}</td>
                    </tr>
                    <tr>
                        <td class="td-label">Tahun Ajaran / Rombel</td>
                        <td class="td-val">{{ $student->academicYear?->name ?? '-' }} / <strong>{{ $student->classroom?->name ?? '-' }}</strong></td>
                        <td class="td-label">Kategori / Layanan</td>
                        <td class="td-val">{{ $student->student_type ?? 'Reguler' }}{{ $student->special_needs_type ? ' (' . $student->special_needs_type . ')' : '' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- 1. IDENTITAS & LEGALITAS KEPENDUDUKAN -->
    <div class="section-header">1. Identitas Pokok &amp; Legalitas Kependudukan (Dapodik)</div>
    <table class="data-table">
        <tr>
            <td class="td-label">Nomor Induk Kependudukan (NIK)</td>
            <td class="td-val"><strong>{{ $student->nik ?? '-' }}</strong></td>
            <td class="td-label">Nomor Kartu Keluarga (KK)</td>
            <td class="td-val"><strong>{{ $student->no_kk ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td class="td-label">No. Registrasi Akta Lahir</td>
            <td class="td-val">{{ $student->birth_certificate_no ?? ($student->no_akta ?? '-') }}</td>
            <td class="td-label">Agama / Kepercayaan</td>
            <td class="td-val">{{ $student->religion ?? 'Islam' }}</td>
        </tr>
        <tr>
            <td class="td-label">Kewarganegaraan</td>
            <td class="td-val">{{ $student->citizenship ?? 'Indonesia (WNI)' }}</td>
            <td class="td-label">Anak Ke- / Jml Saudara</td>
            <td class="td-val">Anak ke-{{ $student->child_order ?? '-' }} dari {{ $student->siblings_count ?? '-' }} bersaudara</td>
        </tr>
        <tr>
            <td class="td-label">Kebutuhan Khusus (PDBK)</td>
            <td class="td-val">
                @if($student->special_needs_type || $student->student_type === 'PDBK')
                    <span style="color: #dc2626; font-weight: bold;">{{ $student->special_needs_type ?? 'Inklusi PDBK' }}</span>
                @else
                    Tidak Ada
                @endif
            </td>
            <td class="td-label">PIN Akses Kios Presensi</td>
            <td class="td-val">{{ $student->pin_access ?? '-' }}</td>
        </tr>
    </table>

    <!-- 2. PENEMPATAN KELAS & AKADEMIK -->
    <div class="section-header">2. Penempatan Akademik &amp; Rombongan Belajar</div>
    <table class="data-table">
        <tr>
            <td class="td-label">Tahun Ajaran Aktif</td>
            <td class="td-val">{{ $student->academicYear?->name ?? '-' }}</td>
            <td class="td-label">Tingkat / Grade</td>
            <td class="td-val">{{ $student->classroom?->classLevel?->name ?? 'Tingkat 7' }}</td>
        </tr>
        <tr>
            <td class="td-label">Rombongan Belajar (Rombel)</td>
            <td class="td-val"><strong>{{ $student->classroom?->name ?? '-' }}</strong></td>
            <td class="td-label">Wali Kelas</td>
            <td class="td-val">{{ $student->classroom?->homeroomTeacher?->name ?? '-' }}</td>
        </tr>
        @if($student->gpkTeacher)
        <tr>
            <td class="td-label">Guru Pendamping Khusus (GPK)</td>
            <td class="td-val-full" colspan="3"><strong>{{ $student->gpkTeacher->name }}</strong> (Pendampingan Khusus PDBK)</td>
        </tr>
        @endif
    </table>

    <!-- 3. ALAMAT & DOMISILI -->
    <div class="section-header">3. Alamat Tinggal &amp; Lingkungan Domisili</div>
    <table class="data-table">
        <tr>
            <td class="td-label">Alamat Rumah Lengkap</td>
            <td class="td-val-full" colspan="3">{{ $student->address ?? '-' }}</td>
        </tr>
        <tr>
            <td class="td-label">RT / RW &bull; Dusun</td>
            <td class="td-val">RT {{ $student->rt ?? '-' }} / RW {{ $student->rw ?? '-' }} &bull; {{ $student->dusun ?? '-' }}</td>
            <td class="td-label">Kelurahan / Desa</td>
            <td class="td-val">{{ $student->village ?? ($student->kelurahan ?? '-') }}</td>
        </tr>
        <tr>
            <td class="td-label">Kecamatan</td>
            <td class="td-val">{{ $student->district ?? ($student->kecamatan ?? '-') }}</td>
            <td class="td-label">Kota / Kab &bull; Kode Pos</td>
            <td class="td-val">{{ $student->city ?? '-' }} &bull; {{ $student->postal_code ?? '-' }}</td>
        </tr>
        <tr>
            <td class="td-label">Tinggal Bersama</td>
            <td class="td-val">{{ $student->living_with ?? 'Orang Tua' }}</td>
            <td class="td-label">Transportasi &bull; Jarak</td>
            <td class="td-val">{{ $student->transportation ?? '-' }} ({{ $student->distance_to_school ? $student->distance_to_school . ' km' : '-' }}, &plusmn; {{ $student->travel_time_minutes ? $student->travel_time_minutes . ' mnt' : '-' }})</td>
        </tr>
    </table>

    <!-- 4. DATA ORANG TUA / WALI -->
    <div class="section-header">4. Data Orang Tua &amp; Wali Siswa</div>
    <table class="comp-table">
        <thead>
            <tr>
                <th width="20%">Rincian Data</th>
                <th width="40%">Data Ayah Kandung</th>
                <th width="40%">Data Ibu Kandung</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="font-weight: 600; background-color: #f8fafc;">Nama Lengkap</td>
                <td><strong>{{ trim(($student->father_title_front ?? '') . ' ' . ($student->father_name ?? '-') . ' ' . ($student->father_title_back ?? '')) ?: '-' }}</strong></td>
                <td><strong>{{ trim(($student->mother_title_front ?? '') . ' ' . ($student->mother_name ?? '-') . ' ' . ($student->mother_title_back ?? '')) ?: '-' }}</strong></td>
            </tr>
            <tr>
                <td style="font-weight: 600; background-color: #f8fafc;">NIK</td>
                <td>{{ $student->father_nik ?? '-' }}</td>
                <td>{{ $student->mother_nik ?? '-' }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600; background-color: #f8fafc;">Tempat, Tgl Lahir</td>
                <td>{{ $student->father_birth_place ?? '-' }}, {{ $student->father_birth_date ? $student->father_birth_date->format('d/m/Y') : '-' }}</td>
                <td>{{ $student->mother_birth_place ?? '-' }}, {{ $student->mother_birth_date ? $student->mother_birth_date->format('d/m/Y') : '-' }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600; background-color: #f8fafc;">Pendidikan Terakhir</td>
                <td>{{ $student->father_education ?? '-' }}</td>
                <td>{{ $student->mother_education ?? '-' }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600; background-color: #f8fafc;">Pekerjaan &bull; Instansi</td>
                <td>{{ $student->father_job ?? '-' }} &bull; {{ $student->father_company ?? '-' }}</td>
                <td>{{ $student->mother_job ?? '-' }} &bull; {{ $student->mother_company ?? '-' }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600; background-color: #f8fafc;">Penghasilan Bulanan</td>
                <td>{{ $student->father_income ?? '-' }}</td>
                <td>{{ $student->mother_income ?? '-' }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600; background-color: #f8fafc;">Nomor Telepon / WA</td>
                <td><strong>{{ $student->father_phone ?? '-' }}</strong></td>
                <td><strong>{{ $student->mother_phone ?? '-' }}</strong></td>
            </tr>
        </tbody>
    </table>

    @if($student->guardian_name)
    <table class="data-table" style="margin-top: -3px;">
        <tr>
            <td class="td-label" style="width: 20%;">Wali Siswa</td>
            <td class="td-val" style="width: 40%;"><strong>{{ $student->guardian_name }}</strong> (Hubungan: {{ $student->guardian_relation ?? 'Wali' }})</td>
            <td class="td-label" style="width: 20%;">Pekerjaan &bull; No. HP</td>
            <td class="td-val" style="width: 20%;">{{ $student->guardian_job ?? '-' }} &bull; {{ $student->guardian_phone ?? '-' }}</td>
        </tr>
    </table>
    @endif

    <table class="data-table" style="margin-top: -1px;">
        <tr>
            <td class="td-label" style="width: 20%;">Kontak Darurat</td>
            <td class="td-val" style="width: 40%;">{{ $student->emergency_contact_name ?? '-' }} ({{ $student->emergency_contact_relation ?? 'Keluarga' }})</td>
            <td class="td-label" style="width: 20%;">No. Telp Darurat</td>
            <td class="td-val" style="width: 20%;"><strong>{{ $student->emergency_contact_phone ?? '-' }}</strong></td>
        </tr>
    </table>

    <!-- 5. FISIK, KESEHATAN & ANTROPOMETRI -->
    <div class="section-header">5. Antropometri &amp; Catatan Rekam Medis (UKS)</div>
    <table class="data-table">
        <tr>
            <td class="td-label">Tinggi Badan (TB)</td>
            <td class="td-val"><strong>{{ $student->height ? $student->height . ' cm' : '-' }}</strong></td>
            <td class="td-label">Berat Badan (BB)</td>
            <td class="td-val"><strong>{{ $student->weight ? $student->weight . ' kg' : '-' }}</strong></td>
        </tr>
        <tr>
            <td class="td-label">Golongan Darah</td>
            <td class="td-val"><strong>{{ $student->blood_type ?? '-' }}</strong></td>
            <td class="td-label">Ciri Fisik</td>
            <td class="td-val">Kulit: {{ $student->skin_color ?? '-' }} &bull; Rambut: {{ $student->hair_type ?? '-' }}</td>
        </tr>
        <tr>
            <td class="td-label">Riwayat Penyakit Berat</td>
            <td class="td-val-full" colspan="3">{{ $student->severe_disease_history ?? 'Tidak Ada' }}</td>
        </tr>
        <tr>
            <td class="td-label">Riwayat Alergi / Sakit Sering</td>
            <td class="td-val-full" colspan="3">Alergi: {{ $student->allergy_history ?? 'Tidak Ada' }} &bull; Sering Diderita: {{ $student->frequent_disease ?? '-' }}</td>
        </tr>
    </table>

    <!-- 6. RIWAYAT SEKOLAH ASAL & MINAT -->
    <div class="section-header">6. Riwayat Sekolah Asal &amp; Minat Bakat</div>
    <table class="data-table">
        <tr>
            <td class="td-label">Sekolah Asal (TK/PAUD/RA)</td>
            <td class="td-val"><strong>{{ $student->previous_school ?? '-' }}</strong></td>
            <td class="td-label">NPSN &bull; Nilai Rata-rata</td>
            <td class="td-val">{{ $student->previous_school_npsn ?? '-' }} &bull; {{ $student->previous_school_score ?? '-' }}</td>
        </tr>
        <tr>
            <td class="td-label">No. Seri Sertifikat/Ijazah TK/MI</td>
            <td class="td-val">{{ $student->diploma_number ?? '-' }}</td>
            <td class="td-label">Hobi &bull; Cita-cita</td>
            <td class="td-val">{{ $student->student_hobby ?? '-' }} &bull; {{ $student->student_aspiration ?? '-' }}</td>
        </tr>
    </table>

    <!-- TANDA TANGAN PENGESAHAN -->
    <table class="sig-container">
        <tr>
            <td style="width: 33%;">
                Mengetahui,<br>
                Orang Tua / Wali Siswa
                <div class="sig-space"></div>
                <div class="sig-name">( {{ $student->father_name ?? ($student->mother_name ?? ($student->guardian_name ?? '..................................')) }} )</div>
                <div class="sig-title">Orang Tua / Wali</div>
            </td>
            <td style="width: 34%;">
                Wali Kelas
                <div class="sig-space"></div>
                <div class="sig-name">( {{ $student->classroom?->homeroomTeacher?->name ?? '..................................' }} )</div>
                <div class="sig-title">Kelas {{ $student->classroom?->name ?? '-' }}</div>
            </td>
            <td style="width: 33%;">
                Malang, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                Kepala Sekolah {{ $schoolName }}
                <div class="sig-space"></div>
                <div class="sig-name">( {{ $principalName ?? 'Kepala Sekolah' }} )</div>
                <div class="sig-title">NIY/NIP: {{ $principalNiy ?? '-' }}</div>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        Dokumen ini diterbitkan resmi melalui Sistem Informasi Akademik &amp; Administrasi Digital {{ $schoolName }} pada {{ date('d/m/Y H:i') }} WIB.
    </div>

</body>
</html>
