<x-admin-layout>
    <div class="p-3.5 sm:p-5 lg:p-6 space-y-4 sm:space-y-5" x-data="studentArchiveApp()">

        <!-- ========================================================================= -->
        <!-- HEADER SECTION                                                            -->
        <!-- ========================================================================= -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center p-1.5 rounded-lg bg-teal-50 dark:bg-teal-950/50 text-teal-600 dark:text-teal-400 border border-teal-200 dark:border-teal-800">
                        <i data-lucide="archive" class="w-5 h-5"></i>
                    </span>
                    <h2 class="text-base sm:text-lg font-bold tracking-tight text-slate-900 dark:text-slate-50">Arsip Murid</h2>
                </div>
                <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400">
                    Pusat rekapitulasi kelengkapan dokumen legalitas (Akta, KK, KTP, KIA) yang otomatis tersinkronisasi dari Data Murid & SPMB.
                </p>
            </div>

            <!-- Header Actions -->
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <!-- Class & Academic Year Chip -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-teal-50/80 dark:bg-teal-950/40 border border-teal-200/80 dark:border-teal-800/60 rounded-xl text-[11px] sm:text-xs shadow-xs">
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Rombel:</span>
                    <span class="font-bold text-teal-700 dark:text-teal-300">
                        {{ $selectedClassroom ? $selectedClassroom->name : 'Semua Rombel' }}
                    </span>
                </div>

                <!-- Export Excel -->
                <a href="{{ route('student-archives.export.excel', ['classroom_id' => $selectedClassroomId, 'academic_year_id' => $selectedYearId]) }}"
                    class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] sm:text-xs font-semibold rounded-lg shadow-sm transition-colors">
                    <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5"></i>
                    <span>Ekspor Excel</span>
                </a>

                <!-- Print -->
                <a href="{{ route('student-archives.print', ['classroom_id' => $selectedClassroomId, 'academic_year_id' => $selectedYearId]) }}" target="_blank"
                    class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 bg-slate-700 hover:bg-slate-800 text-white text-[11px] sm:text-xs font-semibold rounded-lg shadow-sm transition-colors">
                    <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                    <span>Cetak PDF</span>
                </a>
            </div>
        </section>

        <!-- ========================================================================= -->
        <!-- FLASH NOTIFICATIONS                                                       -->
        <!-- ========================================================================= -->
        @if(session('success'))
            <div class="bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 p-3 sm:p-3.5 rounded-xl flex items-center justify-between gap-3 text-xs shadow-xs transition-all">
                <div class="flex items-center gap-2.5">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0"></i>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 dark:hover:text-emerald-200">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 p-3 sm:p-3.5 rounded-xl flex items-start gap-2.5 text-xs shadow-xs">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0 mt-0.5"></i>
                <div>
                    <span class="font-bold">Terjadi kesalahan validasi:</span>
                    <ul class="list-disc list-inside mt-1 space-y-0.5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- ========================================================================= -->
        <!-- ULTRA-COMPACT KPI STRIP                                                   -->
        <!-- ========================================================================= -->
        <section class="flex flex-wrap items-center gap-2 sm:gap-2.5 w-full text-xs">
            <!-- Total Murid -->
            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 shadow-2xs">
                <i data-lucide="users" class="w-3.5 h-3.5 text-teal-600 dark:text-teal-400 shrink-0"></i>
                <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Total Murid:</span>
                <span class="font-bold font-mono text-slate-900 dark:text-slate-50">{{ $stats['total'] }}</span>
            </div>

            <!-- Berkas Lengkap -->
            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200/60 dark:border-emerald-900/40 rounded-lg text-emerald-800 dark:text-emerald-300 shadow-2xs">
                <i data-lucide="check-check" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0"></i>
                <span class="text-[11px] font-medium">Lengkap:</span>
                <span class="font-bold font-mono">{{ $stats['complete'] }}</span>
                <span class="text-[10px] font-semibold text-emerald-600/80 dark:text-emerald-400/80">({{ $stats['percentage'] }}%)</span>
            </div>

            <!-- Belum Lengkap -->
            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-rose-50/70 dark:bg-rose-955/30 border border-rose-200/60 dark:border-rose-900/40 rounded-lg text-rose-800 dark:text-rose-300 shadow-2xs">
                <i data-lucide="alert-circle" class="w-3.5 h-3.5 text-rose-600 dark:text-rose-400 shrink-0"></i>
                <span class="text-[11px] font-medium">Belum Lengkap:</span>
                <span class="font-bold font-mono">{{ $stats['incomplete'] }}</span>
            </div>

            <!-- Kurang Akta -->
            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200/60 dark:border-amber-900/40 rounded-lg text-amber-800 dark:text-amber-300 shadow-2xs">
                <i data-lucide="file-question" class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400 shrink-0"></i>
                <span class="text-[11px] font-medium">Kurang Akta:</span>
                <span class="font-bold font-mono">{{ $stats['missing_akta'] }}</span>
            </div>

            <!-- Kurang KK -->
            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200/60 dark:border-amber-900/40 rounded-lg text-amber-800 dark:text-amber-300 shadow-2xs">
                <i data-lucide="file-warning" class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400 shrink-0"></i>
                <span class="text-[11px] font-medium">Kurang KK:</span>
                <span class="font-bold font-mono">{{ $stats['missing_kk'] }}</span>
            </div>
        </section>

        <!-- ========================================================================= -->
        <!-- FILTER BAR: ROMBEL, TAHUN AJARAN, STATUS KELENGKAPAN, PENCARIAN          -->
        <!-- ========================================================================= -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-4 shadow-xs">
            <form method="GET" action="{{ route('student-archives.index') }}" id="archiveFilterForm" class="flex flex-col lg:flex-row lg:items-end justify-between gap-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 flex-1">
                    <!-- Rombel / Kelompok Belajar -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                            Rombel / Kelompok <span class="text-rose-500">*</span>
                        </label>
                        <select name="classroom_id" onchange="document.getElementById('archiveFilterForm').submit()"
                            class="w-full pl-3 pr-8 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 cursor-pointer">
                            <option value="all">Semua Rombel</option>
                            @php
                                $groupedClasses = $classrooms->groupBy('sub_unit');
                            @endphp
                            @foreach($groupedClasses as $unit => $classes)
                                <optgroup label="{{ $unit }}">
                                    @foreach($classes as $c)
                                        <option value="{{ $c->id }}" {{ $selectedClassroomId == $c->id ? 'selected' : '' }}>
                                            {{ $c->name }} ({{ $c->students_count }} Murid)
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tahun Ajaran -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                            Tahun Ajaran
                        </label>
                        <select name="academic_year_id" onchange="document.getElementById('archiveFilterForm').submit()"
                            class="w-full pl-3 pr-8 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 cursor-pointer">
                            @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}" {{ $selectedYearId == $ay->id ? 'selected' : '' }}>
                                    {{ $ay->name }} {{ $ay->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Kelengkapan -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                            Status Berkas
                        </label>
                        <select name="status" onchange="document.getElementById('archiveFilterForm').submit()"
                            class="w-full pl-3 pr-8 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 cursor-pointer">
                            <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>Semua Status</option>
                            <option value="complete" {{ $statusFilter === 'complete' ? 'selected' : '' }}>Lengkap (Akta, KK & Foto Ada)</option>
                            <option value="incomplete" {{ $statusFilter === 'incomplete' ? 'selected' : '' }}>Belum Lengkap (Ada Berkas Kurang)</option>
                        </select>
                    </div>

                    <!-- Pencarian -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                            Cari Murid
                        </label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ $search }}"
                                placeholder="Nama, NIS, atau NIK..."
                                class="w-full pl-8 pr-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-medium text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5 pointer-events-none"></i>
                        </div>
                    </div>
                </div>

                <!-- Submit / Reset -->
                <div class="flex items-center gap-2 shrink-0">
                    <button type="submit"
                        class="px-3.5 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors cursor-pointer">
                        Filter
                    </button>
                    @if($search || $statusFilter !== 'all')
                        <a href="{{ route('student-archives.index', ['classroom_id' => $selectedClassroomId, 'academic_year_id' => $selectedYearId]) }}"
                            class="px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-semibold rounded-lg transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- ========================================================================= -->
        <!-- TABLE OF STUDENT ARCHIVES & CHECKLIST                                     -->
        <!-- ========================================================================= -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 uppercase tracking-wider font-bold text-[10px]">
                            <th class="py-3 px-3.5 text-center w-10">No</th>
                            <th class="py-3 px-3.5 min-w-[200px]">Nama Murid & NIS</th>
                            <th class="py-3 px-3 text-center w-28">Akta Lahir</th>
                            <th class="py-3 px-3 text-center w-28">Kartu Keluarga</th>
                            <th class="py-3 px-3 text-center w-24">Pas Foto</th>
                            <th class="py-3 px-3 text-center w-24">KTP Ortu</th>
                            <th class="py-3 px-3 text-center w-24">KIA / Imunisasi</th>
                            <th class="py-3 px-3 text-center w-24">Lainnya</th>
                            <th class="py-3 px-3.5 text-center w-28">Kelengkapan</th>
                            <th class="py-3 px-3.5 text-center w-36">Aksi Berkas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium text-slate-700 dark:text-slate-300">
                        @forelse($students as $idx => $s)
                            @php
                                $cl = $s->document_checklist;
                            @endphp
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                                <!-- No -->
                                <td class="py-3 px-3.5 text-center text-slate-400 font-bold">
                                    {{ $idx + 1 }}
                                </td>

                                <!-- Nama Murid & NIS -->
                                <td class="py-3 px-3.5">
                                    <div class="flex items-center gap-2.5">
                                        @if($cl['foto'])
                                            <img src="{{ $cl['foto'] }}" alt="{{ $s->full_name }}" class="w-8 h-8 rounded-full object-cover border border-slate-200 shrink-0">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-teal-100 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 font-bold flex items-center justify-center text-xs shrink-0">
                                                {{ strtoupper(substr($s->full_name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-bold text-slate-900 dark:text-slate-100 text-xs">
                                                {{ $s->full_name }}
                                            </div>
                                            <div class="text-[11px] text-slate-400 font-mono">
                                                NIS: {{ $s->nis ?: '-' }} | {{ $s->gender ?: '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Akta Kelahiran -->
                                <td class="py-3 px-3 text-center">
                                    @if($cl['akta'])
                                        <a href="{{ $cl['akta']['url'] }}" target="_blank" title="Lihat Akta ({{ $cl['akta']['source'] }})"
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 transition-colors">
                                            <i data-lucide="check" class="w-3 h-3"></i>
                                            <span>Ada</span>
                                        </a>
                                    @else
                                        <button type="button" @click="openUploadModal({{ json_encode($s) }}, 'akta_kelahiran')" title="Upload Akta Susulan"
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 cursor-pointer">
                                            <i data-lucide="plus" class="w-2.5 h-2.5"></i>
                                            <span>Kurang</span>
                                        </button>
                                    @endif
                                </td>

                                <!-- Kartu Keluarga (KK) -->
                                <td class="py-3 px-3 text-center">
                                    @if($cl['kk'])
                                        <a href="{{ $cl['kk']['url'] }}" target="_blank" title="Lihat KK ({{ $cl['kk']['source'] }})"
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 transition-colors">
                                            <i data-lucide="check" class="w-3 h-3"></i>
                                            <span>Ada</span>
                                        </a>
                                    @else
                                        <button type="button" @click="openUploadModal({{ json_encode($s) }}, 'kartu_keluarga')" title="Upload KK Susulan"
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 cursor-pointer">
                                            <i data-lucide="plus" class="w-2.5 h-2.5"></i>
                                            <span>Kurang</span>
                                        </button>
                                    @endif
                                </td>

                                <!-- Pas Foto -->
                                <td class="py-3 px-3 text-center">
                                    @if($cl['foto'])
                                        <a href="{{ $cl['foto'] }}" target="_blank" title="Lihat Foto"
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 transition-colors">
                                            <i data-lucide="image" class="w-3 h-3"></i>
                                            <span>Ada</span>
                                        </a>
                                    @else
                                        <button type="button" @click="openUploadModal({{ json_encode($s) }}, 'pas_foto')" title="Upload Foto Susulan"
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 cursor-pointer">
                                            <i data-lucide="plus" class="w-2.5 h-2.5"></i>
                                            <span>Kurang</span>
                                        </button>
                                    @endif
                                </td>

                                <!-- KTP Orang Tua -->
                                <td class="py-3 px-3 text-center">
                                    @if($cl['ktp'])
                                        <a href="{{ $cl['ktp']['url'] }}" target="_blank"
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-50 hover:bg-sky-100 text-sky-700 border border-sky-200 transition-colors">
                                            <i data-lucide="check" class="w-3 h-3"></i>
                                            <span>Ada</span>
                                        </a>
                                    @else
                                        <span class="text-slate-300 dark:text-slate-600 font-mono">-</span>
                                    @endif
                                </td>

                                <!-- KIA / Imunisasi -->
                                <td class="py-3 px-3 text-center">
                                    @if($cl['kia'])
                                        <a href="{{ $cl['kia']['url'] }}" target="_blank"
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 transition-colors">
                                            <i data-lucide="check" class="w-3 h-3"></i>
                                            <span>Ada</span>
                                        </a>
                                    @else
                                        <span class="text-slate-300 dark:text-slate-600 font-mono">-</span>
                                    @endif
                                </td>

                                <!-- Dokumen Lainnya -->
                                <td class="py-3 px-3 text-center">
                                    @if(count($cl['other_docs']) > 0)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                            {{ count($cl['other_docs']) }} File
                                        </span>
                                    @else
                                        <span class="text-slate-300 dark:text-slate-600 font-mono">-</span>
                                    @endif
                                </td>

                                <!-- Status Kelengkapan -->
                                <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                    @if($cl['is_complete'])
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800">
                                            <i data-lucide="check-check" class="w-3 h-3"></i>
                                            <span>Lengkap</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800">
                                            <span>Kurang {{ 3 - $cl['core_count'] }} Dok</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- Aksi Berkas -->
                                <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- Open Student Vault -->
                                        <button type="button" @click="openVaultModal({{ json_encode($s) }})" title="Buka Lemari Berkas Murid"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-700 dark:bg-teal-950/40 dark:text-teal-300 text-[11px] font-bold transition-colors cursor-pointer border border-teal-200/60 dark:border-teal-800/60">
                                            <i data-lucide="folder-open" class="w-3.5 h-3.5"></i>
                                            <span>Lemari Berkas</span>
                                        </button>

                                        <!-- Quick Upload Button -->
                                        <button type="button" @click="openUploadModal({{ json_encode($s) }})" title="Unggah Berkas Tambahan"
                                            class="p-1 rounded-lg text-slate-400 hover:text-teal-600 hover:bg-teal-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                                            <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <i data-lucide="folder-x" class="w-8 h-8 text-slate-300 dark:text-slate-600"></i>
                                        <p class="font-medium text-xs">Tidak ada data murid di rombel ini sesuai filter.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Table Footer -->
            <div class="p-3 bg-slate-50 dark:bg-slate-800/40 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400">
                <span>Total Murid: <strong>{{ $students->count() }}</strong> anak</span>
                <span>Rombel: <strong>{{ $selectedClassroom ? $selectedClassroom->name : 'Semua' }}</strong></span>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL 1: LEMARI BERKAS MURID (VIEW ALL ATTACHMENTS & VAULT)               -->
        <!-- ========================================================================= -->
        <div x-show="vaultModalOpen" x-cloak
            style="display: none; margin-top: 0px !important; z-index: 9999;"
            class="fixed inset-0 z-[9999] !mt-0 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            
            <div @click.outside="vaultModalOpen = false"
                class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl max-w-xl w-full p-4 sm:p-6 text-left space-y-4">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <span class="p-1.5 rounded-lg bg-teal-50 dark:bg-teal-950/50 text-teal-600 dark:text-teal-400">
                            <i data-lucide="folder-heart" class="w-4 h-4"></i>
                        </span>
                        <div>
                            <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-slate-100"
                                x-text="'Lemari Berkas: ' + (activeStudent?.full_name || '')">
                            </h3>
                            <p class="text-[11px] text-slate-400" x-text="'NIS: ' + (activeStudent?.nis || '-') + ' | Rombel: ' + (activeStudent?.classroom?.name || '-')"></p>
                        </div>
                    </div>
                    <button type="button" @click="vaultModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <!-- Vault Content List -->
                <div class="space-y-3 max-h-[60vh] overflow-y-auto pr-1">
                    <!-- Status Banner -->
                    <div class="p-3 rounded-xl border flex items-center justify-between"
                        :class="activeStudent?.document_checklist?.is_complete ? 'bg-emerald-50/60 border-emerald-200 text-emerald-800' : 'bg-amber-50/60 border-amber-200 text-amber-800'">
                        <div class="flex items-center gap-2 text-xs font-bold">
                            <i data-lucide="check-circle" class="w-4 h-4" x-show="activeStudent?.document_checklist?.is_complete"></i>
                            <i data-lucide="alert-circle" class="w-4 h-4" x-show="!activeStudent?.document_checklist?.is_complete"></i>
                            <span x-text="activeStudent?.document_checklist?.is_complete ? 'Dokumen Legalitas Inti Sudah Lengkap' : 'Dokumen Legalitas Inti Masih Kurang'"></span>
                        </div>
                        <button type="button" @click="openUploadModal(activeStudent)"
                            class="px-2.5 py-1 bg-white hover:bg-slate-50 text-slate-800 text-[11px] font-bold rounded-lg border shadow-xs transition-colors">
                            + Unggah Berkas Baru
                        </button>
                    </div>

                    <!-- Dokumen Inti (Akta, KK, Foto) -->
                    <div class="space-y-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Dokumen Inti & Kependudukan:</span>

                        <!-- Akta Lahir -->
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <i data-lucide="file-text" class="w-4 h-4 text-teal-600"></i>
                                <div>
                                    <div class="font-bold text-xs text-slate-800 dark:text-slate-200">Akta Kelahiran</div>
                                    <div class="text-[10px] text-slate-400" x-text="activeStudent?.document_checklist?.akta ? 'Sumber: ' + activeStudent.document_checklist.akta.source : 'Belum Ada'"></div>
                                </div>
                            </div>
                            <template x-if="activeStudent?.document_checklist?.akta">
                                <a :href="activeStudent.document_checklist.akta.url" target="_blank"
                                    class="px-3 py-1 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-lg transition-colors inline-flex items-center gap-1">
                                    <i data-lucide="eye" class="w-3 h-3"></i> Buka File
                                </a>
                            </template>
                            <template x-if="!activeStudent?.document_checklist?.akta">
                                <button type="button" @click="openUploadModal(activeStudent, 'akta_kelahiran')"
                                    class="px-3 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold rounded-lg transition-colors">
                                    + Unggah
                                </button>
                            </template>
                        </div>

                        <!-- Kartu Keluarga -->
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <i data-lucide="users" class="w-4 h-4 text-teal-600"></i>
                                <div>
                                    <div class="font-bold text-xs text-slate-800 dark:text-slate-200">Kartu Keluarga (KK)</div>
                                    <div class="text-[10px] text-slate-400" x-text="activeStudent?.document_checklist?.kk ? 'Sumber: ' + activeStudent.document_checklist.kk.source : 'Belum Ada'"></div>
                                </div>
                            </div>
                            <template x-if="activeStudent?.document_checklist?.kk">
                                <a :href="activeStudent.document_checklist.kk.url" target="_blank"
                                    class="px-3 py-1 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-lg transition-colors inline-flex items-center gap-1">
                                    <i data-lucide="eye" class="w-3 h-3"></i> Buka File
                                </a>
                            </template>
                            <template x-if="!activeStudent?.document_checklist?.kk">
                                <button type="button" @click="openUploadModal(activeStudent, 'kartu_keluarga')"
                                    class="px-3 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold rounded-lg transition-colors">
                                    + Unggah
                                </button>
                            </template>
                        </div>

                        <!-- Pas Foto -->
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <i data-lucide="image" class="w-4 h-4 text-teal-600"></i>
                                <div>
                                    <div class="font-bold text-xs text-slate-800 dark:text-slate-200">Pas Foto Murid</div>
                                    <div class="text-[10px] text-slate-400" x-text="activeStudent?.document_checklist?.foto ? 'Tersimpan' : 'Belum Ada'"></div>
                                </div>
                            </div>
                            <template x-if="activeStudent?.document_checklist?.foto">
                                <a :href="activeStudent.document_checklist.foto" target="_blank"
                                    class="px-3 py-1 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-lg transition-colors inline-flex items-center gap-1">
                                    <i data-lucide="eye" class="w-3 h-3"></i> Buka Foto
                                </a>
                            </template>
                            <template x-if="!activeStudent?.document_checklist?.foto">
                                <button type="button" @click="openUploadModal(activeStudent, 'pas_foto')"
                                    class="px-3 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold rounded-lg transition-colors">
                                    + Unggah
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Dokumen Tambahan Tersimpan di student_documents -->
                    <template x-if="activeStudent?.uploaded_documents && activeStudent.uploaded_documents.length > 0">
                        <div class="space-y-2 pt-2">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Berkas Susulan & Sertifikat Lainnya:</span>
                            <template x-for="doc in activeStudent.uploaded_documents" :key="doc.id">
                                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <i data-lucide="file-check" class="w-4 h-4 text-purple-600"></i>
                                        <div>
                                            <div class="font-bold text-xs text-slate-800 dark:text-slate-200" x-text="doc.title"></div>
                                            <div class="text-[10px] text-slate-400 font-mono" x-text="doc.type_label + ' (' + doc.formatted_file_size + ')'"></div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <a :href="'/student-archives/' + doc.id + '/download'"
                                            class="px-2.5 py-1 bg-purple-50 hover:bg-purple-100 text-purple-700 text-[11px] font-bold rounded-lg transition-colors">
                                            Unduh
                                        </a>
                                        <form :action="'/student-archives/' + doc.id" method="POST" onsubmit="return confirm('Hapus berkas ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-slate-400 hover:text-rose-600">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <div class="flex items-center justify-end pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="vaultModalOpen = false"
                        class="px-4 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg transition-colors cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL 2: UNGGAH BERKAS SUSULAN MURID                                      -->
        <!-- ========================================================================= -->
        <div x-show="uploadModalOpen" x-cloak
            style="display: none; margin-top: 0px !important; z-index: 9999;"
            class="fixed inset-0 z-[9999] !mt-0 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
            
            <div @click.outside="uploadModalOpen = false"
                class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl max-w-md w-full p-4 sm:p-6 text-left space-y-4">
                
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2">
                        <span class="p-1.5 rounded-lg bg-teal-50 dark:bg-teal-950/50 text-teal-600 dark:text-teal-400">
                            <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                        </span>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-slate-100">
                            Unggah Berkas Susulan
                        </h3>
                    </div>
                    <button type="button" @click="uploadModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <form action="{{ route('student-archives.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3.5">
                    @csrf
                    <input type="hidden" name="student_id" :value="uploadStudent?.id">

                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Murid:</span>
                        <span class="font-bold text-xs text-slate-800 dark:text-slate-200 block" x-text="uploadStudent?.full_name"></span>
                        <span class="text-[11px] text-teal-600 dark:text-teal-400 font-mono" x-text="'NIS: ' + (uploadStudent?.nis || '-')"></span>
                    </div>

                    <!-- Jenis Dokumen -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                            Jenis Dokumen <span class="text-rose-500">*</span>
                        </label>
                        <select name="document_type" x-model="uploadDocType" required
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            @foreach($documentTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Judul Dokumen -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                            Nama / Judul Berkas <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="title" x-model="uploadTitle" required
                            placeholder="Contoh: Akta Kelahiran Resmi"
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    </div>

                    <!-- File -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                            Pilih File <span class="text-rose-500">*</span>
                            <span class="text-[10px] text-slate-400 font-normal lowercase">(PDF, JPG, PNG maks 20MB)</span>
                        </label>
                        <input type="file" name="file" required
                            class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer">
                    </div>

                    <!-- Keterangan -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                            Catatan Tambahan
                        </label>
                        <textarea name="notes" rows="2"
                            placeholder="Catatan jika diperlukan..."
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-medium text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 leading-relaxed"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="uploadModalOpen = false"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition-colors cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">
                            Simpan Berkas
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- ALPINE.JS SCRIPT -->
    <script>
        function studentArchiveApp() {
            return {
                vaultModalOpen: false,
                uploadModalOpen: false,
                activeStudent: null,
                uploadStudent: null,
                uploadDocType: 'akta_kelahiran',
                uploadTitle: 'Akta Kelahiran',

                openVaultModal(student) {
                    this.activeStudent = student;
                    this.vaultModalOpen = true;
                },

                openUploadModal(student, defaultType = 'akta_kelahiran') {
                    this.uploadStudent = student;
                    this.uploadDocType = defaultType;
                    const typeMap = {
                        'akta_kelahiran': 'Akta Kelahiran',
                        'kartu_keluarga': 'Kartu Keluarga (KK)',
                        'pas_foto': 'Pas Foto Murid',
                        'ktp_ortu': 'KTP Orang Tua',
                        'kia_imunisasi': 'Buku KIA / Imunisasi',
                        'piagam_prestasi': 'Piagam Prestasi',
                        'ijazah_stsb': 'Ijazah / STSB',
                        'surat_mutasi': 'Surat Pindah Mutasi'
                    };
                    this.uploadTitle = typeMap[defaultType] || 'Dokumen Tambahan';
                    this.uploadModalOpen = true;
                }
            };
        }
    </script>
</x-admin-layout>
