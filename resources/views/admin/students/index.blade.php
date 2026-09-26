<x-admin-layout>
    <div class="p-6 space-y-6" x-data="studentApp()">

        <!-- GREETING / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <div class="flex items-center gap-2.5">
                    <div class="p-2 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-xl border border-indigo-500/20">
                        <i data-lucide="users" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 flex items-center gap-2">
                            Daftar Siswa
                            <span class="text-xs px-2.5 py-0.5 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-400 font-semibold border border-indigo-200 dark:border-indigo-800">
                                {{ setting('app_name', 'SD Anak Saleh') }}
                            </span>
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Database komprehensif peserta didik, riwayat kelas, inklusi, dan orang tua {{ setting('app_name', 'SD Anak Saleh') }}.</p>
                    </div>
                </div>
            </div>
            <!-- ACTION CONTROLS: INFO TAHUN AJARAN AKTIF & ACTION BUTTONS -->
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <!-- Info Badge Tahun Pelajaran Aktif -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-50/80 dark:bg-indigo-950/40 border border-indigo-200/80 dark:border-indigo-800/60 rounded-xl text-xs shadow-xs">
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Tapel Aktif:</span>
                    <span class="font-bold text-indigo-700 dark:text-indigo-300">
                        {{ $activeAcademicYear ? $activeAcademicYear->name : '2026/2027' }}
                    </span>
                </div>

                <button type="button" @click="importModalOpen = true"
                    class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 shadow-xs transition-all cursor-pointer">
                    <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400"></i>
                    Impor Excel
                </button>

                <a href="{{ route('spmb.candidates.index') }}"
                    class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 shadow-xs transition-all cursor-pointer">
                    <i data-lucide="user-check" class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400"></i>
                    Siswa SPMB
                </a>
                <button type="button" @click="openCreateModal()"
                    class="inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all cursor-pointer">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    Tambah Siswa
                </button>
            </div>
        </section>

        <!-- IMPORT ERRORS NOTIFICATION (IF ANY ROWS SKIPPED) -->
        @if(session('import_errors'))
            <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs space-y-2 shadow-xs">
                <div class="flex items-center gap-2 font-bold">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-600 dark:text-rose-400"></i>
                    <span>Catatan Impor (Beberapa baris dilewati):</span>
                </div>
                <ul class="list-disc list-inside space-y-1 pl-2 text-[11px] text-rose-700 dark:text-rose-300 max-h-40 overflow-y-auto">
                    @foreach(session('import_errors') as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- STATS CARDS GRID -->
        <section class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
            <!-- Stat Card 1: Total Siswa Aktif -->
            <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-xs flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Siswa Aktif</p>
                        <h3 class="text-xl font-black tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                            {{ number_format($stats['total_active']) }}
                        </h3>
                    </div>
                    <div class="p-2 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 rounded-lg border border-indigo-100 dark:border-indigo-900/50">
                        <i data-lucide="users" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-2 text-[10px] text-slate-400">
                    Total terdata: <span class="font-semibold text-slate-600 dark:text-slate-300">{{ number_format($stats['total_all']) }}</span>
                </div>
            </div>

            <!-- Stat Card 2: Laki-laki -->
            <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-xs flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Putra (L)</p>
                        <h3 class="text-xl font-black tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                            {{ number_format($stats['male']) }}
                        </h3>
                    </div>
                    <div class="p-2 bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 rounded-lg border border-blue-100 dark:border-blue-900/50">
                        <i data-lucide="user" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-2 text-[10px] text-slate-400">
                    {{ $stats['total_active'] > 0 ? round(($stats['male'] / $stats['total_active']) * 100) : 0 }}% dari total aktif
                </div>
            </div>

            <!-- Stat Card 3: Perempuan -->
            <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-xs flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Putri (P)</p>
                        <h3 class="text-xl font-black tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                            {{ number_format($stats['female']) }}
                        </h3>
                    </div>
                    <div class="p-2 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 rounded-lg border border-rose-100 dark:border-rose-900/50">
                        <i data-lucide="user-check" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-2 text-[10px] text-slate-400">
                    {{ $stats['total_active'] > 0 ? round(($stats['female'] / $stats['total_active']) * 100) : 0 }}% dari total aktif
                </div>
            </div>

            <!-- Stat Card 4: Inklusi (PDBK) -->
            <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-xs flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-purple-600 dark:text-purple-400 uppercase tracking-wider">Inklusi (PDBK)</p>
                        <h3 class="text-xl font-black tracking-tight text-purple-700 dark:text-purple-300 mt-1">
                            {{ number_format($stats['pdbk']) }}
                        </h3>
                    </div>
                    <div class="p-2 bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 rounded-lg border border-purple-100 dark:border-purple-900/50">
                        <i data-lucide="heart-handshake" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-2 text-[10px] text-purple-600/80 dark:text-purple-400/80">
                    Berkebutuhan khusus
                </div>
            </div>

            <!-- Stat Card 5: Rombongan Belajar -->
            <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-xs flex flex-col justify-between col-span-2 sm:col-span-1">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Rombel</p>
                        <h3 class="text-xl font-black tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                            {{ number_format($stats['classrooms']) }}
                        </h3>
                    </div>
                    <div class="p-2 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-lg border border-emerald-100 dark:border-emerald-900/50">
                        <i data-lucide="layout-grid" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-2 text-[10px] text-slate-400">
                    24 Rombel (1A - 6D)
                </div>
            </div>
        </section>

        <!-- SEARCH & FILTERS -->
        <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3.5 shadow-xs w-full">
            <form method="GET" action="{{ route('students.index') }}" class="flex flex-col lg:flex-row gap-2.5 items-stretch lg:items-center justify-between">
                <!-- Search Box -->
                <div class="relative w-full lg:max-w-xs">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIS, NIK, No. Ortu..."
                        style="padding-left: 2.25rem;"
                        class="w-full h-8.5 pr-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 dark:text-slate-50 placeholder-slate-400 shadow-inner">
                </div>

                <!-- Filter Select Toolbar -->
                <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                    <!-- Filter Tahun Pelajaran (Tapel) -->
                    <select name="academic_year_id" onchange="this.form.submit()"
                        class="h-8.5 px-2.5 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer shadow-xs">
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ ($selectedYearName ?? '') == $year->name || $selectedYearId == $year->id ? 'selected' : '' }}>
                                Tapel {{ $year->name }} {{ $year->has_active || $year->is_active ? '★' : '' }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Filter Tingkat Kelas -->
                    <select name="class_level_id" onchange="this.form.submit()"
                        class="h-8.5 px-2.5 text-xs font-medium bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer shadow-xs">
                        <option value="all">Semua Tingkat</option>
                        @foreach($classLevels as $lvl)
                            <option value="{{ $lvl->id }}" {{ request('class_level_id') == $lvl->id ? 'selected' : '' }}>
                                {{ $lvl->name }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Filter Rombel -->
                    <select name="classroom_id" onchange="this.form.submit()"
                        class="h-8.5 px-2.5 text-xs font-medium bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer shadow-xs max-w-[150px] truncate">
                        <option value="all">Semua Rombel</option>
                        @foreach($classrooms as $rombel)
                            <option value="{{ $rombel->id }}" {{ request('classroom_id') == $rombel->id ? 'selected' : '' }}>
                                {{ $rombel->full_name }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Filter Tipe Siswa (Reguler / Inklusi) -->
                    <select name="student_type" onchange="this.form.submit()"
                        class="h-8.5 px-2.5 text-xs font-medium bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer shadow-xs">
                        <option value="">Semua Tipe</option>
                        <option value="REGULER" {{ request('student_type') == 'REGULER' ? 'selected' : '' }}>Reguler</option>
                        <option value="PDBK" {{ request('student_type') == 'PDBK' ? 'selected' : '' }}>PDBK (Inklusi)</option>
                    </select>

                    <!-- Filter Status -->
                    <select name="status" onchange="this.form.submit()"
                        class="h-8.5 px-2.5 text-xs font-medium bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer shadow-xs">
                        <option value="all">Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="lulus" {{ request('status') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                        <option value="mutasi" {{ request('status') == 'mutasi' ? 'selected' : '' }}>Mutasi</option>
                        <option value="keluar" {{ request('status') == 'keluar' ? 'selected' : '' }}>Keluar</option>
                        <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>

                    @if(request()->hasAny(['search', 'academic_year_id', 'class_level_id', 'classroom_id', 'student_type', 'status', 'gender']))
                        <a href="{{ route('students.index') }}" 
                            class="h-8.5 px-2.5 inline-flex items-center justify-center text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 rounded-lg border border-slate-200 dark:border-slate-700 transition-colors"
                            title="Reset Filter">
                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                        </a>
                    @endif
                </div>
            </form>
        </section>

        <!-- TABLE LIST SISWA -->
        <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs overflow-hidden w-full">
            <div class="overflow-x-auto">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-900/50">
                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-12">No</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-28">NIS</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Siswa & Tipe</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-40">Tingkat & Rombel</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-28">L/P & Usia</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-44">Orang Tua & WA</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-24">Status</th>
                            <th class="px-4 py-3 text-right text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        @forelse($students as $index => $s)
                            @php
                                $isPdbk = ($s->student_type && (str_contains(strtoupper($s->student_type), 'PDBK') || str_contains(strtoupper($s->student_type), 'KHUSUS'))) || !empty($s->special_needs_type);
                            @endphp
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors group">
                                <td class="px-4 py-3 text-slate-400 font-mono text-[11px]">
                                    {{ $students->firstItem() + $index }}
                                </td>
                                <td class="px-4 py-3 font-mono text-xs font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ $s->nis }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        @if($s->student_photo_url)
                                            <img src="{{ $s->student_photo_url }}" alt="{{ $s->full_name }}" class="w-8 h-8 rounded-full object-cover ring-1 ring-slate-200 dark:ring-slate-700 shrink-0">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-100 dark:border-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-xs shrink-0">
                                                {{ $s->avatar_initials }}
                                            </div>
                                        @endif
                                        <div class="flex flex-col">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="font-bold text-slate-900 dark:text-slate-100 text-xs tracking-tight hover:text-indigo-600 dark:hover:text-indigo-400 cursor-pointer" @click="openDetailModal({{ $s->id }})">
                                                    {{ $s->full_name }}
                                                </span>
                                                @if($isPdbk)
                                                    <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800" title="{{ $s->special_needs_type ?: 'PDBK' }}">
                                                        PDBK
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-2 mt-0.5 text-[10px] text-slate-400">
                                                @if($s->nik)
                                                    <span>NIK: {{ $s->nik }}</span>
                                                @endif
                                                @if($s->spmb_candidate_id)
                                                    <span class="px-1.5 py-0.2 rounded bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 font-medium">SPMB</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-slate-800 dark:text-slate-200">
                                            {{ $s->classroom ? $s->classroom->full_name : 'Belum Ditentukan' }}
                                        </span>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span class="text-[11px] text-slate-400">
                                                {{ $s->classroom && $s->classroom->classLevel ? $s->classroom->classLevel->name : '-' }}
                                            </span>
                                            @if($s->academicYear)
                                                <span class="text-[10px] px-1.5 py-0.2 rounded bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 font-medium font-mono border border-indigo-100 dark:border-indigo-900/50">
                                                    {{ $s->academicYear->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col text-slate-600 dark:text-slate-300">
                                        <span class="font-medium">{{ $s->formatted_gender }}</span>
                                        <span class="text-[10px] text-slate-400">{{ $s->age ?: '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-slate-800 dark:text-slate-200 truncate max-w-[160px]">
                                            {{ $s->father_name ?: ($s->mother_name ?: ($s->guardian_name ?: '-')) }}
                                        </span>
                                        @if($s->clean_parent_phone)
                                            <a href="{{ $s->whatsapp_url }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-emerald-600 dark:text-emerald-400 hover:underline font-mono mt-0.5">
                                                <i data-lucide="message-circle" class="w-3 h-3"></i>
                                                {{ $s->parent_phone }}
                                            </a>
                                        @else
                                            <span class="text-[11px] text-slate-400">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($s->status === 'aktif')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/40">
                                            Aktif
                                        </span>
                                    @elseif($s->status === 'lulus')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800/40">
                                            Lulus
                                        </span>
                                    @elseif($s->status === 'mutasi')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/40">
                                            Mutasi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                            {{ ucfirst($s->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="button" @click="openDetailModal({{ $s->id }})"
                                            class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-lg transition-colors cursor-pointer"
                                            title="Lihat Detail Profil & Riwayat">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </button>
                                        <button type="button" @click="openEditModal({{ $s->id }})"
                                            class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 hover:text-amber-600 dark:hover:text-amber-400 rounded-lg transition-colors cursor-pointer"
                                            title="Edit 7 Kategori Data">
                                            <i data-lucide="edit-2" class="w-4 h-4"></i>
                                        </button>
                                        <button type="button" @click="confirmDeleteStudent({{ $s->id }}, '{{ addslashes($s->full_name) }}')"
                                            class="p-1.5 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 rounded-lg transition-colors cursor-pointer">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <i data-lucide="users" class="w-8 h-8 text-slate-300 dark:text-slate-600 mb-2"></i>
                                        <p class="font-semibold text-slate-600 dark:text-slate-400">Tidak ada data siswa ditemukan</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Coba sesuaikan filter atau tambahkan siswa baru / impor dari Excel.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($students->hasPages())
                <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between">
                    {{ $students->links() }}
                </div>
            @endif
        </section>

        <!-- ========================================================= -->
        <!-- MODAL DETAIL SISWA (7-TAB SYSTEM) -->
        <!-- ========================================================= -->
        <div x-show="detailModalOpen" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4" style="display: none; margin-top: 0px !important; z-index: 9999; background-color: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px);">
            <div @click.outside="detailModalOpen = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl w-full max-w-4xl max-h-[92vh] overflow-hidden shadow-2xl flex flex-col">
                
                <!-- Modal Top Header -->
                <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50/80 dark:bg-slate-900/80">
                    <div class="flex items-center gap-3.5">
                        <template x-if="selectedStudent?.student_photo_url">
                            <img :src="selectedStudent.student_photo_url" class="w-12 h-12 rounded-xl object-cover ring-2 ring-indigo-500/30">
                        </template>
                        <template x-if="!selectedStudent?.student_photo_url">
                            <div class="w-12 h-12 rounded-xl bg-indigo-600 text-white font-bold text-lg flex items-center justify-center shadow-xs" x-text="selectedStudent?.avatar_initials || 'S'"></div>
                        </template>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-black text-slate-900 dark:text-slate-50" x-text="selectedStudent?.full_name || 'Detail Siswa'"></h3>
                                <template x-if="selectedStudent?.student_type && selectedStudent.student_type.includes('PDBK')">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800">PDBK (Inklusi)</span>
                                </template>
                            </div>
                            <div class="flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                <span>NIS: <strong class="font-mono text-indigo-600 dark:text-indigo-400" x-text="selectedStudent?.nis"></strong></span>
                                <span>&bull;</span>
                                <span x-text="selectedStudent?.classroom?.name || 'Tanpa Rombel'"></span>
                                <span>&bull;</span>
                                <span x-text="selectedStudent?.academic_year?.name ? 'TA ' + selectedStudent.academic_year.name : '-'"></span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <template x-if="selectedStudent?.whatsapp_url">
                            <a :href="selectedStudent.whatsapp_url" target="_blank" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold text-xs flex items-center gap-1.5 shadow-xs transition-colors">
                                <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                                WA Ortu
                            </a>
                        </template>
                        <button type="button" @click="detailModalOpen = false" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>

                <!-- 7-Tab Navigation Bar -->
                <div class="flex items-center gap-1 px-4 border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-x-auto text-xs font-semibold scrollbar-thin">
                    <button type="button" @click="activeDetailTab = 1" :class="activeDetailTab === 1 ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'text-slate-500 border-transparent hover:text-slate-700'" class="px-3 py-3 border-b-2 flex items-center gap-1.5 whitespace-nowrap transition-colors">
                        <i data-lucide="id-card" class="w-4 h-4"></i> 1. Identitas & Legalitas
                    </button>
                    <button type="button" @click="activeDetailTab = 2" :class="activeDetailTab === 2 ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'text-slate-500 border-transparent hover:text-slate-700'" class="px-3 py-3 border-b-2 flex items-center gap-1.5 whitespace-nowrap transition-colors">
                        <i data-lucide="heart-handshake" class="w-4 h-4"></i> 2. Inklusi / PDBK
                    </button>
                    <button type="button" @click="activeDetailTab = 3" :class="activeDetailTab === 3 ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'text-slate-500 border-transparent hover:text-slate-700'" class="px-3 py-3 border-b-2 flex items-center gap-1.5 whitespace-nowrap transition-colors">
                        <i data-lucide="map-pin" class="w-4 h-4"></i> 3. Alamat & Domisili
                    </button>
                    <button type="button" @click="activeDetailTab = 4" :class="activeDetailTab === 4 ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'text-slate-500 border-transparent hover:text-slate-700'" class="px-3 py-3 border-b-2 flex items-center gap-1.5 whitespace-nowrap transition-colors">
                        <i data-lucide="users-round" class="w-4 h-4"></i> 4. Orang Tua & Wali
                    </button>
                    <button type="button" @click="activeDetailTab = 5" :class="activeDetailTab === 5 ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'text-slate-500 border-transparent hover:text-slate-700'" class="px-3 py-3 border-b-2 flex items-center gap-1.5 whitespace-nowrap transition-colors">
                        <i data-lucide="git-fork" class="w-4 h-4"></i> 5. Keluarga & Saudara
                    </button>
                    <button type="button" @click="activeDetailTab = 6" :class="activeDetailTab === 6 ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'text-slate-500 border-transparent hover:text-slate-700'" class="px-3 py-3 border-b-2 flex items-center gap-1.5 whitespace-nowrap transition-colors">
                        <i data-lucide="activity" class="w-4 h-4"></i> 6. Kesehatan & UKS
                    </button>
                    <button type="button" @click="activeDetailTab = 7" :class="activeDetailTab === 7 ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'text-slate-500 border-transparent hover:text-slate-700'" class="px-3 py-3 border-b-2 flex items-center gap-1.5 whitespace-nowrap transition-colors">
                        <i data-lucide="history" class="w-4 h-4"></i> 7. Asal & Riwayat Kelas
                    </button>
                </div>

                <!-- Tab Body Content -->
                <div class="p-6 overflow-y-auto max-h-[60vh] space-y-4 text-xs">
                    
                    <!-- TAB 1: IDENTITAS & LEGALITAS -->
                    <div x-show="activeDetailTab === 1" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 text-[10px] block font-bold uppercase">Nama Lengkap</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200 text-sm" x-text="selectedStudent?.full_name || '-'"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 text-[10px] block font-bold uppercase">Nama Panggilan</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedStudent?.nickname || '-'"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 text-[10px] block font-bold uppercase">Jenis Kelamin & Usia</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="(selectedStudent?.formatted_gender || '-') + ' (' + (selectedStudent?.age || '-') + ')'"></span>
                            </div>

                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 text-[10px] block font-bold uppercase">NIS (Nomor Induk Siswa)</span>
                                <span class="font-mono font-bold text-indigo-600 dark:text-indigo-400" x-text="selectedStudent?.nis || '-'"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 text-[10px] block font-bold uppercase">NISN</span>
                                <span class="font-mono font-semibold text-slate-800 dark:text-slate-200" x-text="selectedStudent?.nisn || '-'"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 text-[10px] block font-bold uppercase">NIK Siswa</span>
                                <span class="font-mono font-semibold text-slate-800 dark:text-slate-200" x-text="selectedStudent?.nik || '-'"></span>
                            </div>

                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 text-[10px] block font-bold uppercase">No. Kartu Keluarga (KK)</span>
                                <span class="font-mono font-semibold text-slate-800 dark:text-slate-200" x-text="selectedStudent?.no_kk || '-'"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 text-[10px] block font-bold uppercase">No. Akta Kelahiran</span>
                                <span class="font-mono font-semibold text-slate-800 dark:text-slate-200" x-text="selectedStudent?.birth_certificate_no || '-'"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 text-[10px] block font-bold uppercase">Kewarganegaraan</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedStudent?.citizenship || 'WNI'"></span>
                            </div>

                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 text-[10px] block font-bold uppercase">Tempat, Tanggal Lahir</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="(selectedStudent?.birth_place ? selectedStudent.birth_place + ', ' : '') + (selectedStudent?.birth_date ? selectedStudent.birth_date.substring(0, 10) : '-')"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 text-[10px] block font-bold uppercase">Agama</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedStudent?.religion || 'Islam'"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 text-[10px] block font-bold uppercase">Tanggal Terdaftar</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedStudent?.enrolled_date ? selectedStudent.enrolled_date.substring(0, 10) : '-'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: INKLUSI & KEKHUSUSAN -->
                    <div x-show="activeDetailTab === 2" class="space-y-4">
                        <div class="p-4 rounded-xl border border-purple-200 dark:border-purple-800/60 bg-purple-50/40 dark:bg-purple-950/20 space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="p-2 bg-purple-600 text-white rounded-lg">
                                        <i data-lucide="heart-handshake" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-purple-900 dark:text-purple-200 text-xs">Klasifikasi Peserta Didik</h4>
                                        <p class="text-[11px] text-purple-700 dark:text-purple-300">Status program inklusi & kebutuhan pendampingan khusus.</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 border border-purple-300 dark:border-purple-700" x-text="selectedStudent?.student_type || 'REGULER'"></span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                                <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-purple-100 dark:border-purple-900/40">
                                    <span class="text-slate-400 text-[10px] block font-bold uppercase">Jenis Ketunaan / Kekhususan</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200 text-xs" x-text="selectedStudent?.special_needs_type || 'Tidak Ada / Reguler'"></span>
                                </div>
                                <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-purple-100 dark:border-purple-900/40">
                                    <span class="text-slate-400 text-[10px] block font-bold uppercase">Catatan Pendampingan Guru / Shadow Teacher</span>
                                    <span class="font-medium text-slate-700 dark:text-slate-300 text-xs" x-text="selectedStudent?.special_needs_notes || selectedStudent?.notes || '-'"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: ALAMAT & DOMISILI -->
                    <div x-show="activeDetailTab === 3" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="sm:col-span-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 text-[10px] block font-bold uppercase">Alamat Jalan / Blok</span>
                                <span class="font-medium text-slate-800 dark:text-slate-200" x-text="selectedStudent?.address || '-'"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 text-[10px] block font-bold uppercase">RT / RW</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="(selectedStudent?.rt ? 'RT ' + selectedStudent.rt : '-') + ' / ' + (selectedStudent?.rw ? 'RW ' + selectedStudent.rw : '-')"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 text-[10px] block font-bold uppercase">Kelurahan / Desa</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedStudent?.village || '-'"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 text-[10px] block font-bold uppercase">Kecamatan</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedStudent?.district || '-'"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 text-[10px] block font-bold uppercase">Kabupaten / Kota</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedStudent?.city || 'Malang'"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 text-[10px] block font-bold uppercase">Provinsi & Kode Pos</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="(selectedStudent?.province || 'Jawa Timur') + ' (' + (selectedStudent?.postal_code || '-') + ')'"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 text-[10px] block font-bold uppercase">Status Tempat Tinggal & Jarak</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="(selectedStudent?.residence_status || '-') + ' &bull; ' + (selectedStudent?.distance_to_school || '-')"></span>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 4: ORANG TUA & WALI -->
                    <div x-show="activeDetailTab === 4" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Data Ayah -->
                            <div class="p-4 rounded-xl bg-blue-50/40 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-900/40 space-y-2.5">
                                <div class="flex items-center gap-2 border-b border-blue-100 dark:border-blue-900/40 pb-2">
                                    <div class="p-1.5 bg-blue-600 text-white rounded-lg"><i data-lucide="user" class="w-3.5 h-3.5"></i></div>
                                    <h4 class="font-bold text-blue-900 dark:text-blue-300 text-xs uppercase">Data Ayah Kandung</h4>
                                </div>
                                <div class="space-y-1.5 text-xs">
                                    <div><span class="text-slate-400 text-[10px] block">Nama:</span> <strong class="text-slate-800 dark:text-slate-200" x-text="selectedStudent?.father_name || '-'"></strong></div>
                                    <div><span class="text-slate-400 text-[10px] block">NIK Ayah:</span> <span class="font-mono text-slate-700 dark:text-slate-300" x-text="selectedStudent?.father_nik || '-'"></span></div>
                                    <div><span class="text-slate-400 text-[10px] block">No. HP / WA:</span> <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400" x-text="selectedStudent?.father_phone || '-'"></span></div>
                                    <div><span class="text-slate-400 text-[10px] block">Pendidikan & Pekerjaan:</span> <span class="text-slate-700 dark:text-slate-300" x-text="(selectedStudent?.father_education || '-') + ' / ' + (selectedStudent?.father_job || '-')"></span></div>
                                    <div><span class="text-slate-400 text-[10px] block">Instansi / Kantor:</span> <span class="text-slate-700 dark:text-slate-300" x-text="selectedStudent?.father_company || '-'"></span></div>
                                    <div><span class="text-slate-400 text-[10px] block">Penghasilan Bulanan:</span> <span class="text-slate-700 dark:text-slate-300" x-text="selectedStudent?.father_income || '-'"></span></div>
                                </div>
                            </div>

                            <!-- Data Ibu -->
                            <div class="p-4 rounded-xl bg-rose-50/40 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/40 space-y-2.5">
                                <div class="flex items-center gap-2 border-b border-rose-100 dark:border-rose-900/40 pb-2">
                                    <div class="p-1.5 bg-rose-600 text-white rounded-lg"><i data-lucide="user-check" class="w-3.5 h-3.5"></i></div>
                                    <h4 class="font-bold text-rose-900 dark:text-rose-300 text-xs uppercase">Data Ibu Kandung</h4>
                                </div>
                                <div class="space-y-1.5 text-xs">
                                    <div><span class="text-slate-400 text-[10px] block">Nama:</span> <strong class="text-slate-800 dark:text-slate-200" x-text="selectedStudent?.mother_name || '-'"></strong></div>
                                    <div><span class="text-slate-400 text-[10px] block">NIK Ibu:</span> <span class="font-mono text-slate-700 dark:text-slate-300" x-text="selectedStudent?.mother_nik || '-'"></span></div>
                                    <div><span class="text-slate-400 text-[10px] block">No. HP / WA:</span> <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400" x-text="selectedStudent?.mother_phone || '-'"></span></div>
                                    <div><span class="text-slate-400 text-[10px] block">Pendidikan & Pekerjaan:</span> <span class="text-slate-700 dark:text-slate-300" x-text="(selectedStudent?.mother_education || '-') + ' / ' + (selectedStudent?.mother_job || '-')"></span></div>
                                    <div><span class="text-slate-400 text-[10px] block">Instansi / Kantor:</span> <span class="text-slate-700 dark:text-slate-300" x-text="selectedStudent?.mother_company || '-'"></span></div>
                                    <div><span class="text-slate-400 text-[10px] block">Penghasilan Bulanan:</span> <span class="text-slate-700 dark:text-slate-300" x-text="selectedStudent?.mother_income || '-'"></span></div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Wali (Optional) -->
                        <template x-if="selectedStudent?.guardian_name">
                            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 text-[10px] block font-bold uppercase">Data Wali Murid</span>
                                <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 mt-0.5">
                                    <span x-text="selectedStudent?.guardian_name"></span> (<span x-text="selectedStudent?.guardian_relation || 'Wali'"></span>) &bull; Telp: <span class="font-mono" x-text="selectedStudent?.guardian_phone || '-'"></span>
                                </p>
                            </div>
                        </template>
                    </div>

                    <!-- TAB 5: KELUARGA & SAUDARA -->
                    <div x-show="activeDetailTab === 5" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 text-[10px] block font-bold uppercase">Anak Ke-</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200 text-sm" x-text="selectedStudent?.child_number || '-'"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 text-[10px] block font-bold uppercase">Jumlah Saudara Kandung</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedStudent?.siblings_count !== null ? selectedStudent?.siblings_count + ' orang' : '-'"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 text-[10px] block font-bold uppercase">Saudara Tiri / Angkat</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="(selectedStudent?.step_siblings_count || 0) + ' Tiri / ' + (selectedStudent?.adoptive_siblings_count || 0) + ' Angkat'"></span>
                            </div>
                            <div class="sm:col-span-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 text-[10px] block font-bold uppercase">Bahasa Sehari-hari di Rumah</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedStudent?.home_language || 'Bahasa Indonesia'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 6: KESEHATAN & UKS -->
                    <div x-show="activeDetailTab === 6" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 text-[10px] block font-bold uppercase">Golongan Darah</span>
                                <span class="font-black text-rose-600 dark:text-rose-400 text-sm" x-text="selectedStudent?.blood_type || '-'"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 text-[10px] block font-bold uppercase">Tinggi Badan (cm)</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedStudent?.height ? selectedStudent.height + ' cm' : '-'"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 text-[10px] block font-bold uppercase">Berat Badan (kg)</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedStudent?.weight ? selectedStudent.weight + ' kg' : '-'"></span>
                            </div>
                            <div class="sm:col-span-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 text-[10px] block font-bold uppercase">Riwayat Penyakit Berat / Alergi</span>
                                <span class="font-medium text-slate-800 dark:text-slate-200" x-text="selectedStudent?.severe_disease_history || 'Tidak ada riwayat'"></span>
                            </div>
                            <div class="sm:col-span-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 text-[10px] block font-bold uppercase">Penyakit yang Sering Diderita</span>
                                <span class="font-medium text-slate-800 dark:text-slate-200" x-text="selectedStudent?.frequent_disease || '-'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 7: ASAL SEKOLAH & LIFECYCLE -->
                    <div x-show="activeDetailTab === 7" class="space-y-4">
                        <!-- Asal Sekolah & Dokumen -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 text-[10px] block font-bold uppercase">Sekolah Asal (TK / PAUD / SD Asal)</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200" x-text="selectedStudent?.previous_school || '-'"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 text-[10px] block font-bold uppercase">Kategori Asal & No. STTB</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="(selectedStudent?.origin_category || 'TK') + ' &bull; ' + (selectedStudent?.sttb_number_date || '-')"></span>
                            </div>
                        </div>

                        <!-- Timeline Riwayat Rombel (Lifecycle Journey) -->
                        <div class="p-4 rounded-xl bg-indigo-50/40 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/40 space-y-3">
                            <div class="flex items-center gap-2">
                                <i data-lucide="history" class="w-4 h-4 text-indigo-600 dark:text-indigo-400"></i>
                                <h4 class="font-bold text-indigo-900 dark:text-indigo-200 text-xs">Perjalanan Akademis / Riwayat Kelas</h4>
                            </div>

                            <div class="relative pl-6 space-y-4 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-indigo-200 dark:before:bg-indigo-800">
                                <template x-for="hist in classroomHistories" :key="hist.id">
                                    <div class="relative">
                                        <div class="absolute -left-6 top-1 w-2.5 h-2.5 rounded-full bg-indigo-600 border-2 border-white dark:border-slate-900"></div>
                                        <div class="p-2.5 rounded-lg bg-white dark:bg-slate-900 border border-indigo-100 dark:border-indigo-900/40 text-xs">
                                            <div class="flex items-center justify-between">
                                                <span class="font-bold text-slate-800 dark:text-slate-200" x-text="hist.classroom ? hist.classroom.name : 'Rombel'"></span>
                                                <span class="text-[10px] font-mono px-1.5 py-0.2 rounded bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 font-semibold" x-text="hist.academic_year ? hist.academic_year.name : '-'"></span>
                                            </div>
                                            <p class="text-[11px] text-slate-400 mt-0.5" x-text="hist.notes || 'Status: ' + (hist.status || 'naik_kelas')"></p>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="classroomHistories.length === 0">
                                    <div class="text-xs text-slate-400 italic">Belum ada catatan riwayat kelas sebelumnya.</div>
                                </template>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/80 flex justify-end gap-2">
                    <button type="button" @click="detailModalOpen = false" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold transition-colors">
                        Tutup
                    </button>
                    <button type="button" @click="openEditModal(selectedStudent.id)" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold transition-colors flex items-center gap-1.5">
                        <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                        Edit 7 Kategori Data
                    </button>
                </div>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- MODAL FORM TAMBAH / EDIT SISWA (7-TAB WIZARD) -->
        <!-- ========================================================= -->
        <div x-show="formModalOpen" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4" style="display: none; margin-top: 0px !important; z-index: 9999; background-color: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px);">
            <div @click.outside="formModalOpen = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl w-full max-w-4xl max-h-[92vh] overflow-hidden shadow-2xl flex flex-col">
                
                <form @submit.prevent="submitForm">
                    <!-- Form Top Header -->
                    <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50/80 dark:bg-slate-900/80">
                        <div>
                            <h3 class="text-base font-black text-slate-900 dark:text-slate-50" x-text="isEdit ? 'Edit Data Siswa (7 Kategori)' : 'Tambah Siswa Baru (7 Kategori)'"></h3>
                            <p class="text-xs text-slate-400 mt-0.5">Kelola identitas Dapodik, inklusi, domisili, orang tua, fisik, dan asal sekolah.</p>
                        </div>
                        <button type="button" @click="formModalOpen = false" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <!-- 7-Tab Form Navigation -->
                    <div class="flex items-center gap-1 px-4 border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-x-auto text-xs font-semibold scrollbar-thin">
                        <button type="button" @click="activeFormTab = 1" :class="activeFormTab === 1 ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'text-slate-500 border-transparent hover:text-slate-700'" class="px-3 py-2.5 border-b-2 flex items-center gap-1.5 whitespace-nowrap transition-colors">
                            <i data-lucide="id-card" class="w-3.5 h-3.5"></i> 1. Identitas
                        </button>
                        <button type="button" @click="activeFormTab = 2" :class="activeFormTab === 2 ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'text-slate-500 border-transparent hover:text-slate-700'" class="px-3 py-2.5 border-b-2 flex items-center gap-1.5 whitespace-nowrap transition-colors">
                            <i data-lucide="heart-handshake" class="w-3.5 h-3.5"></i> 2. Inklusi (PDBK)
                        </button>
                        <button type="button" @click="activeFormTab = 3" :class="activeFormTab === 3 ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'text-slate-500 border-transparent hover:text-slate-700'" class="px-3 py-2.5 border-b-2 flex items-center gap-1.5 whitespace-nowrap transition-colors">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5"></i> 3. Alamat
                        </button>
                        <button type="button" @click="activeFormTab = 4" :class="activeFormTab === 4 ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'text-slate-500 border-transparent hover:text-slate-700'" class="px-3 py-2.5 border-b-2 flex items-center gap-1.5 whitespace-nowrap transition-colors">
                            <i data-lucide="users-round" class="w-3.5 h-3.5"></i> 4. Orang Tua & Wali
                        </button>
                        <button type="button" @click="activeFormTab = 5" :class="activeFormTab === 5 ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'text-slate-500 border-transparent hover:text-slate-700'" class="px-3 py-2.5 border-b-2 flex items-center gap-1.5 whitespace-nowrap transition-colors">
                            <i data-lucide="git-fork" class="w-3.5 h-3.5"></i> 5. Keluarga
                        </button>
                        <button type="button" @click="activeFormTab = 6" :class="activeFormTab === 6 ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'text-slate-500 border-transparent hover:text-slate-700'" class="px-3 py-2.5 border-b-2 flex items-center gap-1.5 whitespace-nowrap transition-colors">
                            <i data-lucide="activity" class="w-3.5 h-3.5"></i> 6. Kesehatan
                        </button>
                        <button type="button" @click="activeFormTab = 7" :class="activeFormTab === 7 ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'text-slate-500 border-transparent hover:text-slate-700'" class="px-3 py-2.5 border-b-2 flex items-center gap-1.5 whitespace-nowrap transition-colors">
                            <i data-lucide="history" class="w-3.5 h-3.5"></i> 7. Asal & Status
                        </button>
                    </div>

                    <!-- Form Body Fields -->
                    <div class="p-6 overflow-y-auto max-h-[60vh] space-y-4 text-xs">

                        <!-- TAB 1: IDENTITAS & LEGALITAS -->
                        <div x-show="activeFormTab === 1" class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">NIS (Wajib) <span class="text-rose-500">*</span></label>
                                    <input type="text" x-model="formData.nis" required placeholder="Contoh: 26.SD.001"
                                        class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-mono text-slate-900 dark:text-slate-50">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">NISN</label>
                                    <input type="text" x-model="formData.nisn" placeholder="10 digit NISN"
                                        class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-mono text-slate-900 dark:text-slate-50">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">NIK Siswa (16 digit)</label>
                                    <input type="text" x-model="formData.nik" placeholder="3573xxxxxxxxxxxx"
                                        class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-mono text-slate-900 dark:text-slate-50">
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap Siswa <span class="text-rose-500">*</span></label>
                                    <input type="text" x-model="formData.full_name" required placeholder="Nama lengkap sesuai akte"
                                        class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 dark:text-slate-50">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Panggilan</label>
                                    <input type="text" x-model="formData.nickname" placeholder="Nama panggilan"
                                        class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 dark:text-slate-50">
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                                    <select x-model="formData.gender" required
                                        class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer">
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tempat Lahir</label>
                                    <input type="text" x-model="formData.birth_place" placeholder="Kota lahir"
                                        class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 dark:text-slate-50">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Lahir</label>
                                    <input type="date" x-model="formData.birth_date"
                                        class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 dark:text-slate-50">
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. Kartu Keluarga (KK)</label>
                                    <input type="text" x-model="formData.no_kk" placeholder="16 digit No. KK"
                                        class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-mono text-slate-900 dark:text-slate-50">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. Akta Kelahiran</label>
                                    <input type="text" x-model="formData.birth_certificate_no" placeholder="No. Registrasi Akta"
                                        class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 dark:text-slate-50">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Agama</label>
                                    <select x-model="formData.religion"
                                        class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer">
                                        <option value="Islam">Islam</option>
                                        <option value="Kristen">Kristen</option>
                                        <option value="Katolik">Katolik</option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Buddha">Buddha</option>
                                        <option value="Konghucu">Konghucu</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: INKLUSI (PDBK) -->
                        <div x-show="activeFormTab === 2" class="space-y-4">
                            <div class="p-4 rounded-xl border border-purple-200 dark:border-purple-800/60 bg-purple-50/40 dark:bg-purple-950/20 space-y-3">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tipe Peserta Didik</label>
                                        <select x-model="formData.student_type"
                                            class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 cursor-pointer">
                                            <option value="REGULER">REGULER</option>
                                            <option value="PDBK (BERKEBUTUHAN KHUSUS)">PDBK (BERKEBUTUHAN KHUSUS)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis Kekhususan / Ketunaan</label>
                                        <input type="text" x-model="formData.special_needs_type" placeholder="Contoh: ADHD, Spektrum Autis, Slow Learner, Speech Delay..."
                                            class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Catatan Pendampingan Guru / Penanganan Khusus</label>
                                        <textarea x-model="formData.special_needs_notes" rows="3" placeholder="Deskripsi kebutuhan pendampingan / terapi yang sedang dijalani..."
                                            class="w-full p-2.5 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 3: ALAMAT & DOMISILI -->
                        <div x-show="activeFormTab === 3" class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div class="sm:col-span-3">
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Alamat Domisili Lengkap</label>
                                    <textarea x-model="formData.address" rows="2" placeholder="Jalan, No. Rumah, Blok, Dusun..."
                                        class="w-full p-2.5 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></textarea>
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">RT / RW</label>
                                    <div class="flex items-center gap-1.5">
                                        <input type="text" x-model="formData.rt" placeholder="RT" class="w-1/2 h-8.5 px-2.5 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg">
                                        <input type="text" x-model="formData.rw" placeholder="RW" class="w-1/2 h-8.5 px-2.5 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg">
                                    </div>
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kelurahan / Desa</label>
                                    <input type="text" x-model="formData.village" placeholder="Kelurahan"
                                        class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kecamatan</label>
                                    <input type="text" x-model="formData.district" placeholder="Kecamatan"
                                        class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg">
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kota / Kabupaten</label>
                                    <input type="text" x-model="formData.city" placeholder="Kota Malang"
                                        class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Provinsi</label>
                                    <input type="text" x-model="formData.province" placeholder="Jawa Timur"
                                        class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode Pos</label>
                                    <input type="text" x-model="formData.postal_code" placeholder="Kode Pos"
                                        class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg font-mono">
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Tempat Tinggal</label>
                                    <input type="text" x-model="formData.residence_status" placeholder="Bersama Orang Tua / Kos / Asrama"
                                        class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jarak ke Sekolah</label>
                                    <input type="text" x-model="formData.distance_to_school" placeholder="Contoh: < 1 km, 3 km..."
                                        class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. Telp Rumah</label>
                                    <input type="text" x-model="formData.home_phone" placeholder="0341-xxxx"
                                        class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg font-mono">
                                </div>
                            </div>
                        </div>

                        <!-- TAB 4: ORANG TUA & WALI -->
                        <div x-show="activeFormTab === 4" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Data Ayah -->
                                <div class="p-3.5 rounded-xl bg-blue-50/40 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-900/40 space-y-2.5">
                                    <div class="flex items-center gap-2 border-b border-blue-100 dark:border-blue-900/40 pb-1.5">
                                        <i data-lucide="user" class="w-3.5 h-3.5 text-blue-600"></i>
                                        <span class="font-bold text-blue-900 dark:text-blue-300 text-xs uppercase">Data Ayah</span>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Nama Ayah</label>
                                        <input type="text" x-model="formData.father_name" placeholder="Nama lengkap ayah" class="w-full h-8 px-2.5 text-xs rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">NIK Ayah</label>
                                        <input type="text" x-model="formData.father_nik" placeholder="16 digit NIK" class="w-full h-8 px-2.5 text-xs rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">No. HP / WA Ayah</label>
                                        <input type="text" x-model="formData.father_phone" placeholder="08xxxxxxxxxx" class="w-full h-8 px-2.5 text-xs rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 font-mono">
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Pendidikan</label>
                                            <input type="text" x-model="formData.father_education" placeholder="S1 / S2 / SMA..." class="w-full h-8 px-2.5 text-xs rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Pekerjaan</label>
                                            <input type="text" x-model="formData.father_job" placeholder="PNS / Swasta..." class="w-full h-8 px-2.5 text-xs rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Instansi / Perusahaan</label>
                                        <input type="text" x-model="formData.father_company" placeholder="Nama kantor/instansi" class="w-full h-8 px-2.5 text-xs rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Penghasilan Bulanan</label>
                                        <input type="text" x-model="formData.father_income" placeholder="Contoh: > 5 Juta" class="w-full h-8 px-2.5 text-xs rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
                                    </div>
                                </div>

                                <!-- Data Ibu -->
                                <div class="p-3.5 rounded-xl bg-rose-50/40 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/40 space-y-2.5">
                                    <div class="flex items-center gap-2 border-b border-rose-100 dark:border-rose-900/40 pb-1.5">
                                        <i data-lucide="user-check" class="w-3.5 h-3.5 text-rose-600"></i>
                                        <span class="font-bold text-rose-900 dark:text-rose-300 text-xs uppercase">Data Ibu</span>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Nama Ibu</label>
                                        <input type="text" x-model="formData.mother_name" placeholder="Nama lengkap ibu" class="w-full h-8 px-2.5 text-xs rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">NIK Ibu</label>
                                        <input type="text" x-model="formData.mother_nik" placeholder="16 digit NIK" class="w-full h-8 px-2.5 text-xs rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">No. HP / WA Ibu</label>
                                        <input type="text" x-model="formData.mother_phone" placeholder="08xxxxxxxxxx" class="w-full h-8 px-2.5 text-xs rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 font-mono">
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Pendidikan</label>
                                            <input type="text" x-model="formData.mother_education" placeholder="S1 / S2 / SMA..." class="w-full h-8 px-2.5 text-xs rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Pekerjaan</label>
                                            <input type="text" x-model="formData.mother_job" placeholder="IRT / Guru / PNS..." class="w-full h-8 px-2.5 text-xs rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Instansi / Perusahaan</label>
                                        <input type="text" x-model="formData.mother_company" placeholder="Nama kantor/instansi" class="w-full h-8 px-2.5 text-xs rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Penghasilan Bulanan</label>
                                        <input type="text" x-model="formData.mother_income" placeholder="Contoh: > 5 Juta" class="w-full h-8 px-2.5 text-xs rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
                                    </div>
                                </div>
                            </div>

                            <!-- Primary WhatsApp Contact -->
                            <div class="p-3.5 rounded-xl bg-emerald-50/60 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900/40">
                                <label class="block font-bold text-emerald-900 dark:text-emerald-300 mb-1">No. WhatsApp Utama (Notifikasi & Pengumuman Sekolah)</label>
                                <input type="text" x-model="formData.parent_phone" placeholder="08xxxxxxxxxx (WA aktif orang tua)"
                                    class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-emerald-300 dark:border-emerald-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/20 font-mono font-bold text-emerald-700 dark:text-emerald-300">
                            </div>
                        </div>

                        <!-- TAB 5: KELUARGA & SAUDARA -->
                        <div x-show="activeFormTab === 5" class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Anak Ke-</label>
                                    <input type="number" x-model="formData.child_number" min="1" placeholder="1"
                                        class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jml Sdr Kandung</label>
                                    <input type="number" x-model="formData.siblings_count" min="0" placeholder="0"
                                        class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jml Sdr Tiri</label>
                                    <input type="number" x-model="formData.step_siblings_count" min="0" placeholder="0"
                                        class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jml Sdr Angkat</label>
                                    <input type="number" x-model="formData.adoptive_siblings_count" min="0" placeholder="0"
                                        class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg">
                                </div>
                                <div class="sm:col-span-4">
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Bahasa Sehari-hari di Rumah</label>
                                    <input type="text" x-model="formData.home_language" placeholder="Bahasa Indonesia / Jawa / Inggris..."
                                        class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg">
                                </div>
                            </div>
                        </div>

                        <!-- TAB 6: KESEHATAN & UKS -->
                        <div x-show="activeFormTab === 6" class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Golongan Darah</label>
                                    <select x-model="formData.blood_type"
                                        class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg cursor-pointer">
                                        <option value="">Pilih / Tidak Tahu</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="AB">AB</option>
                                        <option value="O">O</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tinggi Badan (cm)</label>
                                    <input type="text" x-model="formData.height" placeholder="Contoh: 120"
                                        class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Berat Badan (kg)</label>
                                    <input type="text" x-model="formData.weight" placeholder="Contoh: 25"
                                        class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg">
                                </div>
                                <div class="sm:col-span-3">
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Riwayat Penyakit Berat / Alergi Makanan / Obat</label>
                                    <textarea x-model="formData.severe_disease_history" rows="2" placeholder="Catatan penyakit berat atau alergi penting untuk tim UKS..."
                                        class="w-full p-2.5 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg"></textarea>
                                </div>
                                <div class="sm:col-span-3">
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Penyakit yang Sering Diderita</label>
                                    <input type="text" x-model="formData.frequent_disease" placeholder="Contoh: Asma, Batuk, Demam..."
                                        class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg">
                                </div>
                            </div>
                        </div>

                        <!-- TAB 7: ASAL SEKOLAH & PENEMPATAN KELAS -->
                        <div x-show="activeFormTab === 7" class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Rombongan Belajar (Kelas) <span class="text-rose-500">*</span></label>
                                    <select x-model="formData.classroom_id" required
                                        class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 cursor-pointer">
                                        <option value="">Pilih Rombel...</option>
                                        @foreach($allClassrooms as $r)
                                            <option value="{{ $r->id }}">
                                                {{ $r->full_name }} (Tapel {{ $r->academicYear->name ?? '-' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Kesiswaan <span class="text-rose-500">*</span></label>
                                    <select x-model="formData.status" required
                                        class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 cursor-pointer">
                                        <option value="aktif">Aktif</option>
                                        <option value="lulus">Lulus</option>
                                        <option value="mutasi">Mutasi</option>
                                        <option value="keluar">Keluar</option>
                                        <option value="nonaktif">Nonaktif</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Asal Sekolah (TK / PAUD / SD Pindahan)</label>
                                    <input type="text" x-model="formData.previous_school" placeholder="Nama TK/SD Asal"
                                        class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. & Tanggal STTB / Ijazah</label>
                                    <input type="text" x-model="formData.sttb_number_date" placeholder="No. Ijazah TK"
                                        class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Catatan Tambahan TU / Catatan Mutasi</label>
                                    <textarea x-model="formData.notes" rows="2" placeholder="Catatan kesiswaan..."
                                        class="w-full p-2.5 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg"></textarea>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Form Footer Buttons -->
                    <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/80 flex items-center justify-between">
                        <div class="flex items-center gap-1.5">
                            <button type="button" x-show="activeFormTab > 1" @click="activeFormTab--" class="px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold flex items-center gap-1">
                                <i data-lucide="chevron-left" class="w-3.5 h-3.5"></i> Sebelumnya
                            </button>
                            <button type="button" x-show="activeFormTab < 7" @click="activeFormTab++" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold flex items-center gap-1">
                                Selanjutnya <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="formModalOpen = false" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold transition-colors">
                                Batal
                            </button>
                            <button type="submit" :disabled="saving" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white rounded-lg text-xs font-bold transition-all shadow-xs flex items-center gap-1.5">
                                <span x-text="saving ? 'Menyimpan...' : (isEdit ? 'Simpan Perubahan' : 'Simpan Siswa Baru')"></span>
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

        <!-- ========================================================= -->
        <!-- MODAL IMPOR EXCEL SISWA -->
        <!-- ========================================================= -->
        <div x-show="importModalOpen" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4" style="display: none; margin-top: 0px !important; z-index: 9999; background-color: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px);">
            <div @click.outside="importModalOpen = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl flex flex-col">
                
                <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- Header -->
                    <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between sticky top-0 bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm z-10">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400 font-bold text-sm flex items-center justify-center">
                                <i data-lucide="file-spreadsheet" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-slate-50">Impor Siswa Massal (Excel)</h3>
                                <p class="text-xs text-slate-400 mt-0.5">Unggah database siswa untuk kelas berjalan (Kelas 1–6).</p>
                            </div>
                        </div>
                        <button type="button" @click="importModalOpen = false" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="p-6 space-y-4 text-xs">
                        <div class="p-4 rounded-xl bg-indigo-50/60 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/50 space-y-2.5">
                            <div class="flex items-center justify-between gap-3">
                                <span class="font-bold text-indigo-900 dark:text-indigo-300">Format Template Excel</span>
                                <button type="button" 
                                    @click="downloadTemplate()" 
                                    :disabled="downloadingTemplate"
                                    class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-400 text-white rounded-lg text-xs font-semibold transition-all shadow-xs cursor-pointer disabled:cursor-not-allowed">
                                    <svg x-show="downloadingTemplate" class="animate-spin w-3.5 h-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <i x-show="!downloadingTemplate" data-lucide="download" class="w-3.5 h-3.5"></i>
                                    <span x-text="downloadingTemplate ? 'Mengunduh...' : 'Unduh Template (.xlsx)'"></span>
                                </button>
                            </div>
                            <p class="text-[11px] text-indigo-800/80 dark:text-indigo-300/80 leading-relaxed">
                                Gunakan template resmi untuk mengisi data siswa. Di dalam file Excel terdapat lembar <strong>"Referensi Rombel & Tapel"</strong> untuk melihat daftar nama rombel yang aktif di sistem.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Rombel Tujuan (Default)</label>
                                <select name="default_classroom_id"
                                    class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-slate-900 dark:text-slate-50 cursor-pointer">
                                    <option value="">Gunakan kolom di Excel (Otomatis)</option>
                                    @foreach($allClassrooms as $r)
                                        <option value="{{ $r->id }}">{{ $r->full_name }} (Tapel {{ $r->academicYear->name ?? '-' }})</option>
                                    @endforeach
                                </select>
                                <p class="text-[10px] text-slate-400 mt-1">Pilih rombel jika file tidak memuat kolom kelas, atau pilih "Gunakan kolom di Excel" jika rombel tercantum per baris.</p>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tahun Pelajaran Default</label>
                                <select name="default_academic_year_id"
                                    class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-slate-900 dark:text-slate-50 cursor-pointer">
                                    @foreach($academicYears as $y)
                                        <option value="{{ $y->id }}" {{ $y->has_active || $y->is_active ? 'selected' : '' }}>Tapel {{ $y->name }} {{ ($y->has_active || $y->is_active) ? '(Aktif)' : '' }}</option>
                                    @endforeach
                                </select>
                                <p class="text-[10px] text-slate-400 mt-1">Tahun pelajaran acuan jika kolom tahun pelajaran di Excel kosong.</p>
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Pilih File Excel (.xlsx, .xls, .csv) <span class="text-rose-500">*</span></label>
                            <input type="file" name="file" required accept=".xlsx,.xls,.csv"
                                class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-950/60 dark:file:text-indigo-300 hover:file:bg-indigo-100 border border-slate-200 dark:border-slate-800 rounded-lg p-1.5 bg-white dark:bg-slate-900">
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex justify-end gap-2">
                        <button type="button" @click="importModalOpen = false" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition-all shadow-xs flex items-center gap-1.5">
                            <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                            Mulai Impor Siswa
                        </button>
                    </div>
                </form>

            </div>
        </div>

        <!-- MODAL KONFIRMASI IN-APP (Aman dari native alert/confirm loop) -->
        <div x-show="confirmModal.open" x-cloak style="display: none; margin-top: 0px !important; z-index: 9999;"
            class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
            @click.self="confirmModal.open = false"
            @keydown.escape.window="confirmModal.open = false">
            
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl w-full max-w-md overflow-hidden shadow-2xl flex flex-col p-6 text-center animate-in fade-in zoom-in-95 duration-150"
                @click.stop>
                
                <div class="w-12 h-12 rounded-full mx-auto flex items-center justify-center mb-4 bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400">
                    <i data-lucide="trash-2" class="w-6 h-6"></i>
                </div>

                <h3 class="text-base font-bold text-slate-900 dark:text-slate-50" x-text="confirmModal.title"></h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed" x-html="confirmModal.message"></p>

                <div class="mt-6 flex items-center justify-center gap-3">
                    <button type="button" @click="confirmModal.open = false" :disabled="confirmModal.loading"
                        class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="executeConfirmDelete()" :disabled="confirmModal.loading"
                        class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                        <span x-show="confirmModal.loading" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                        <span>Hapus Permanen</span>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- Alpine.js Application Logic -->
    <script>
        function studentApp() {
            return {
                detailModalOpen: false,
                formModalOpen: false,
                importModalOpen: false,
                downloadingTemplate: false,
                activeDetailTab: 1,
                activeFormTab: 1,
                isEdit: false,
                saving: false,
                selectedStudent: null,
                classroomHistories: [],
                formData: {
                    id: null,
                    academic_year_id: '{{ $selectedYearId && $selectedYearId !== "all" ? $selectedYearId : ($academicYears->firstWhere("is_active", true)?->id ?? "") }}',
                    classroom_id: '',
                    nis: '',
                    nisn: '',
                    nik: '',
                    no_kk: '',
                    birth_certificate_no: '',
                    citizenship: 'WNI',
                    full_name: '',
                    nickname: '',
                    gender: 'L',
                    birth_place: '',
                    birth_date: '',
                    religion: 'Islam',

                    student_type: 'REGULER',
                    special_needs_type: '',
                    special_needs_notes: '',

                    address: '',
                    rt: '',
                    rw: '',
                    village: '',
                    district: '',
                    city: 'Malang',
                    province: 'Jawa Timur',
                    postal_code: '',
                    residence_status: '',
                    distance_to_school: '',
                    home_phone: '',

                    father_name: '',
                    father_nik: '',
                    father_phone: '',
                    father_education: '',
                    father_job: '',
                    father_company: '',
                    father_income: '',

                    mother_name: '',
                    mother_nik: '',
                    mother_phone: '',
                    mother_education: '',
                    mother_job: '',
                    mother_company: '',
                    mother_income: '',

                    guardian_name: '',
                    guardian_relation: '',
                    guardian_phone: '',

                    parent_phone: '',

                    child_number: '',
                    siblings_count: '',
                    step_siblings_count: '',
                    adoptive_siblings_count: '',
                    home_language: 'Bahasa Indonesia',

                    blood_type: '',
                    height: '',
                    weight: '',
                    severe_disease_history: '',
                    frequent_disease: '',

                    previous_school: '',
                    origin_category: 'TK',
                    sttb_number_date: '',

                    status: 'aktif',
                    notes: '',
                },

                async downloadTemplate() {
                    if (this.downloadingTemplate) return;
                    this.downloadingTemplate = true;
                    try {
                        if (typeof NProgress !== 'undefined') {
                            NProgress.start();
                        }
                        const response = await fetch('{{ route('students.download-template') }}');
                        if (!response.ok) throw new Error('Gagal mengunduh file template Excel.');
                        
                        const blob = await response.blob();
                        const blobUrl = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.style.display = 'none';
                        a.setAttribute('data-no-loader', 'true');
                        a.href = blobUrl;
                        a.download = 'Template_Import_Siswa_SD.xlsx';
                        document.body.appendChild(a);
                        a.click();
                        
                        setTimeout(() => {
                            window.URL.revokeObjectURL(blobUrl);
                            a.remove();
                        }, 2000);

                        if (window.showToastNotification) {
                            window.showToastNotification('Template Excel berhasil diunduh!', 'success');
                        }
                    } catch (err) {
                        if (window.showToastNotification) {
                            window.showToastNotification('Gagal mengunduh template: ' + err.message, 'error');
                        } else if (window.showToast) {
                            window.showToast('Gagal', err.message, 'error');
                        }
                    } finally {
                        this.downloadingTemplate = false;
                        if (typeof NProgress !== 'undefined') {
                            NProgress.done();
                        }
                    }
                },

                openDetailModal(id) {
                    this.activeDetailTab = 1;
                    fetch(`/students/${id}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            this.selectedStudent = res.student;
                            this.classroomHistories = res.classroom_histories || [];
                            this.detailModalOpen = true;
                            this.$nextTick(() => {
                                if (window.lucide) lucide.createIcons();
                            });
                        }
                    })
                    .catch(err => {
                        if (window.showToastNotification) {
                            window.showToastNotification("Gagal memuat detail siswa: " + err.message, "error");
                        } else if (window.showToast) {
                            window.showToast("Gagal", err.message, "error");
                        }
                    });
                },

                openCreateModal() {
                    this.isEdit = false;
                    this.activeFormTab = 1;
                    this.formData = {
                        id: null,
                        academic_year_id: '{{ $selectedYearId && $selectedYearId !== "all" ? $selectedYearId : ($academicYears->firstWhere("is_active", true)?->id ?? "") }}',
                        classroom_id: '',
                        nis: '',
                        nisn: '',
                        nik: '',
                        no_kk: '',
                        birth_certificate_no: '',
                        citizenship: 'WNI',
                        full_name: '',
                        nickname: '',
                        gender: 'L',
                        birth_place: '',
                        birth_date: '',
                        religion: 'Islam',

                        student_type: 'REGULER',
                        special_needs_type: '',
                        special_needs_notes: '',

                        address: '',
                        rt: '',
                        rw: '',
                        village: '',
                        district: '',
                        city: 'Malang',
                        province: 'Jawa Timur',
                        postal_code: '',
                        residence_status: '',
                        distance_to_school: '',
                        home_phone: '',

                        father_name: '',
                        father_nik: '',
                        father_phone: '',
                        father_education: '',
                        father_job: '',
                        father_company: '',
                        father_income: '',

                        mother_name: '',
                        mother_nik: '',
                        mother_phone: '',
                        mother_education: '',
                        mother_job: '',
                        mother_company: '',
                        mother_income: '',

                        guardian_name: '',
                        guardian_relation: '',
                        guardian_phone: '',

                        parent_phone: '',

                        child_number: '',
                        siblings_count: '',
                        step_siblings_count: '',
                        adoptive_siblings_count: '',
                        home_language: 'Bahasa Indonesia',

                        blood_type: '',
                        height: '',
                        weight: '',
                        severe_disease_history: '',
                        frequent_disease: '',

                        previous_school: '',
                        origin_category: 'TK',
                        sttb_number_date: '',

                        status: 'aktif',
                        notes: '',
                    };
                    this.formModalOpen = true;
                    this.$nextTick(() => {
                        if (window.lucide) lucide.createIcons();
                    });
                },

                openEditModal(id) {
                    this.activeFormTab = 1;
                    fetch(`/students/${id}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            const s = res.student;
                            this.isEdit = true;
                            this.formData = {
                                id: s.id,
                                academic_year_id: s.academic_year_id || '',
                                classroom_id: s.classroom_id || '',
                                nis: s.nis || '',
                                nisn: s.nisn || '',
                                nik: s.nik || '',
                                no_kk: s.no_kk || '',
                                birth_certificate_no: s.birth_certificate_no || '',
                                citizenship: s.citizenship || 'WNI',
                                full_name: s.full_name || '',
                                nickname: s.nickname || '',
                                gender: s.gender || 'L',
                                birth_place: s.birth_place || '',
                                birth_date: s.birth_date ? s.birth_date.substring(0, 10) : '',
                                religion: s.religion || 'Islam',

                                student_type: s.student_type || 'REGULER',
                                special_needs_type: s.special_needs_type || '',
                                special_needs_notes: s.special_needs_notes || '',

                                address: s.address || '',
                                rt: s.rt || '',
                                rw: s.rw || '',
                                village: s.village || '',
                                district: s.district || '',
                                city: s.city || 'Malang',
                                province: s.province || 'Jawa Timur',
                                postal_code: s.postal_code || '',
                                residence_status: s.residence_status || '',
                                distance_to_school: s.distance_to_school || '',
                                home_phone: s.home_phone || '',

                                father_name: s.father_name || '',
                                father_nik: s.father_nik || '',
                                father_phone: s.father_phone || '',
                                father_education: s.father_education || '',
                                father_job: s.father_job || '',
                                father_company: s.father_company || '',
                                father_income: s.father_income || '',

                                mother_name: s.mother_name || '',
                                mother_nik: s.mother_nik || '',
                                mother_phone: s.mother_phone || '',
                                mother_education: s.mother_education || '',
                                mother_job: s.mother_job || '',
                                mother_company: s.mother_company || '',
                                mother_income: s.mother_income || '',

                                guardian_name: s.guardian_name || '',
                                guardian_relation: s.guardian_relation || '',
                                guardian_phone: s.guardian_phone || '',

                                parent_phone: s.parent_phone || '',

                                child_number: s.child_number || '',
                                siblings_count: s.siblings_count !== null ? s.siblings_count : '',
                                step_siblings_count: s.step_siblings_count !== null ? s.step_siblings_count : '',
                                adoptive_siblings_count: s.adoptive_siblings_count !== null ? s.adoptive_siblings_count : '',
                                home_language: s.home_language || 'Bahasa Indonesia',

                                blood_type: s.blood_type || '',
                                height: s.height || '',
                                weight: s.weight || '',
                                severe_disease_history: s.severe_disease_history || '',
                                frequent_disease: s.frequent_disease || '',

                                previous_school: s.previous_school || '',
                                origin_category: s.origin_category || 'TK',
                                sttb_number_date: s.sttb_number_date || '',

                                status: s.status || 'aktif',
                                notes: s.notes || '',
                            };
                            this.detailModalOpen = false;
                            this.formModalOpen = true;
                            this.$nextTick(() => {
                                if (window.lucide) lucide.createIcons();
                            });
                        }
                    })
                    .catch(err => {
                        if (window.showToastNotification) {
                            window.showToastNotification("Gagal mengambil data siswa: " + err.message, "error");
                        } else if (window.showToast) {
                            window.showToast("Gagal", err.message, "error");
                        }
                    });
                },

                submitForm() {
                    if (this.saving) return;
                    this.saving = true;

                    const url = this.isEdit ? `/students/${this.formData.id}` : '/students';
                    const method = this.isEdit ? 'PUT' : 'POST';

                    fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.formData)
                    })
                    .then(res => res.json())
                    .then(res => {
                        this.saving = false;
                        if (res.success) {
                            this.formModalOpen = false;
                            if (window.setPendingToast) {
                                window.setPendingToast(res.message || 'Data siswa berhasil disimpan!', 'success');
                            }
                            window.location.reload();
                        } else {
                            if (window.showToastNotification) {
                                window.showToastNotification(res.message || 'Terjadi kesalahan saat menyimpan.', 'error');
                            } else if (window.showToast) {
                                window.showToast('Gagal', res.message || 'Terjadi kesalahan saat menyimpan.', 'error');
                            }
                        }
                    })
                    .catch(err => {
                        this.saving = false;
                        if (window.showToastNotification) {
                            window.showToastNotification('Error: ' + err.message, 'error');
                        } else if (window.showToast) {
                            window.showToast('Error', err.message, 'error');
                        }
                    });
                },

                confirmModal: {
                    open: false,
                    id: null,
                    title: '',
                    message: '',
                    loading: false
                },

                confirmDeleteStudent(id, name) {
                    this.confirmModal = {
                        open: true,
                        id: id,
                        title: 'Hapus Data Siswa?',
                        message: `Apakah Anda yakin ingin menghapus data siswa <strong>${name}</strong>? Data yang telah dihapus tidak dapat dipulihkan.`,
                        loading: false
                    };
                    this.$nextTick(() => {
                        if (window.lucide) lucide.createIcons();
                    });
                },

                executeConfirmDelete() {
                    if (this.confirmModal.loading) return;
                    this.confirmModal.loading = true;

                    fetch(`/students/${this.confirmModal.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(async res => {
                        this.confirmModal.loading = false;
                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.confirmModal.open = false;
                            if (window.setPendingToast) {
                                window.setPendingToast(data.message || 'Data siswa berhasil dihapus!', 'success');
                            }
                            window.location.reload();
                        } else {
                            const errMsg = data.message || 'Gagal menghapus data siswa.';
                            if (window.showToastNotification) {
                                window.showToastNotification(errMsg, 'error');
                            } else {
                                alert(errMsg);
                            }
                        }
                    })
                    .catch(err => {
                        this.confirmModal.loading = false;
                        if (window.showToastNotification) {
                            window.showToastNotification('Error: ' + err.message, 'error');
                        } else {
                            alert('Error: ' + err.message);
                        }
                    });
                }
            }
        }
    </script>
</x-admin-layout>
