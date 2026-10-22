<x-admin-layout>
    @php
        $initStatuses = [];
        $initNotes = [];
        foreach ($students as $st) {
            $initStatuses[$st->id] = $existingDailyAttendances[$st->id]->status ?? '';
            $initNotes[$st->id] = $existingDailyAttendances[$st->id]->notes ?? '';
        }
    @endphp

    <div class="p-3.5 sm:p-5 lg:p-6 space-y-4" x-data="studentAttendanceApp()">

        <!-- HEADER SECTION -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center p-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                        <i data-lucide="clipboard-check" class="w-5 h-5"></i>
                    </span>
                    <h2 class="text-base sm:text-lg font-bold tracking-tight text-slate-900 dark:text-slate-50">Presensi Siswa</h2>
                </div>
                <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400">
                    Pencatatan presensi harian per rombel dan laporan rekapitulasi kehadiran bulanan siswa SMP.
                </p>
            </div>

            <!-- T.A. & Active Info Badges -->
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-50/80 dark:bg-indigo-950/40 border border-indigo-200/80 dark:border-indigo-800/60 rounded-xl text-[11px] sm:text-xs shadow-xs">
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Tahun Ajaran:</span>
                    <span class="font-bold text-indigo-700 dark:text-indigo-300">
                        {{ $activeAcademicYear ? $activeAcademicYear->name : 'Aktif' }}
                    </span>
                </div>
            </div>
        </section>



        @if(isset($errors) && $errors->any())
            <div class="bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 p-3 sm:p-3.5 rounded-xl flex items-start gap-2.5 text-xs shadow-xs">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0 mt-0.5"></i>
                <div>
                    <span class="font-bold">Terjadi kesalahan:</span>
                    <ul class="list-disc list-inside mt-1 space-y-0.5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- TAB NAVIGATION -->
        <div class="border-b border-slate-200 dark:border-slate-800 flex items-center justify-between gap-4">
            <nav class="flex space-x-1 sm:space-x-2" aria-label="Tabs">
                <button type="button"
                    @click="setTab('daily')"
                    :class="activeTab === 'daily' 
                        ? 'border-emerald-600 text-emerald-600 dark:text-emerald-400 dark:border-emerald-400 bg-emerald-50/40 dark:bg-emerald-950/20' 
                        : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 hover:border-slate-300'"
                    class="group inline-flex items-center gap-2 py-2.5 px-3 sm:px-4 border-b-2 font-semibold text-xs sm:text-sm rounded-t-lg transition-colors cursor-pointer">
                    <i data-lucide="calendar" class="w-4 h-4"></i>
                    <span>Input Harian</span>
                    @if($dailyStats['recorded'] > 0)
                        <span class="ml-1.5 px-1.5 py-0.5 text-[10px] rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-300 font-bold">
                            {{ $dailyStats['recorded'] }}/{{ $dailyStats['total'] }}
                        </span>
                    @endif
                </button>

                <button type="button"
                    @click="setTab('monthly')"
                    :class="activeTab === 'monthly' 
                        ? 'border-emerald-600 text-emerald-600 dark:text-emerald-400 dark:border-emerald-400 bg-emerald-50/40 dark:bg-emerald-950/20' 
                        : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 hover:border-slate-300'"
                    class="group inline-flex items-center gap-2 py-2.5 px-3 sm:px-4 border-b-2 font-semibold text-xs sm:text-sm rounded-t-lg transition-colors cursor-pointer">
                    <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                    <span>Rekap Bulanan</span>
                    @if(isset($monthlyStats['effective_days']) && $monthlyStats['effective_days'] > 0)
                        <span class="ml-1.5 px-1.5 py-0.5 text-[10px] rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900/60 dark:text-indigo-300 font-bold">
                            {{ $monthlyStats['effective_days'] }} Hari
                        </span>
                    @endif
                </button>
            </nav>

            <!-- Classroom badge info -->
            @if($selectedClassroom)
                <div class="hidden sm:flex items-center gap-2 text-xs text-slate-600 dark:text-slate-400 pb-1">
                    <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold border border-slate-200 dark:border-slate-700">
                        {{ $selectedClassroom->classLevel?->name ?? 'SMP' }} - {{ $selectedClassroom->name }}
                    </span>
                    @if($selectedClassroom->homeroomTeacher)
                        <span class="text-[11px] text-slate-500 dark:text-slate-400 truncate max-w-[200px]">
                            Wali: <strong>{{ $selectedClassroom->homeroomTeacher->name }}</strong>
                        </span>
                    @endif
                </div>
            @endif
        </div>

        <!-- ========================================================================= -->
        <!-- TAB 1: INPUT HARIAN                                                      -->
        <!-- ========================================================================= -->
        <div x-show="activeTab === 'daily'" x-cloak class="space-y-4">
            
            <!-- FILTER BAR: KELAS & TANGGAL -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-4 shadow-xs">
                <form method="GET" action="{{ route('student-attendances.index') }}" id="dailyFilterForm" class="flex flex-col md:flex-row md:items-end justify-between gap-3">
                    <input type="hidden" name="tab" value="daily">

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 flex-1">
                        <!-- Filter Kelas / Rombel -->
                        <div>
                            <label for="classroom_id" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                Rombel / Kelompok Belajar <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="classroom_id" id="classroom_id" onchange="document.getElementById('dailyFilterForm').submit()"
                                    class="w-full pl-3 pr-8 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors cursor-pointer">
                                    @php
                                        $groupedClasses = $classrooms->groupBy(fn($c) => $c->classLevel?->name ?? 'Kelas');
                                    @endphp
                                    @foreach($groupedClasses as $unit => $classes)
                                        <optgroup label="{{ $unit }}">
                                            @foreach($classes as $c)
                                                <option value="{{ $c->id }}" {{ $selectedClassroomId == $c->id ? 'selected' : '' }}>
                                                    {{ $c->name }} ({{ $c->students_count ?? $c->active_students_count }} Murid)
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Filter Tanggal -->
                        <div>
                            <label for="date" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                Tanggal Absensi <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex items-center gap-1.5">
                                <input type="date" name="date" id="date" value="{{ $selectedDate }}" onchange="document.getElementById('dailyFilterForm').submit()"
                                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors cursor-pointer">
                                
                                <button type="button" @click="setToday()" title="Set ke Hari Ini"
                                    class="shrink-0 px-2.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-medium border border-slate-200 dark:border-slate-700 transition-colors cursor-pointer">
                                    Hari Ini
                                </button>
                            </div>
                        </div>

                        <!-- Action Submit Filter (Mobile friendly / desktop fallback) -->
                        <div class="flex items-end gap-2">
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-1.5 w-full sm:w-auto px-4 py-2 bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white text-xs font-semibold rounded-lg transition-colors cursor-pointer shadow-xs">
                                <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                                Terapkan Filter
                            </button>
                        </div>
                    </div>

                    <!-- Date label pill -->
                    <div class="flex items-center gap-2 pt-1 md:pt-0 shrink-0">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-emerald-800 dark:text-emerald-300 text-xs font-medium">
                            <i data-lucide="calendar-days" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400"></i>
                            <span>{{ $carbonDate->translatedFormat('l, d F Y') }}</span>
                        </span>
                    </div>
                </form>
            </div>

            <!-- ULTRA-COMPACT DAILY STATUS SUMMARY STRIP -->
            <section class="flex flex-wrap items-center gap-2 sm:gap-2.5 w-full text-xs">
                <!-- Total Murid -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 shadow-2xs">
                    <i data-lucide="users" class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400 shrink-0"></i>
                    <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Total Murid:</span>
                    <span class="font-bold font-mono text-slate-900 dark:text-slate-50" x-text="stats.total">{{ $dailyStats['total'] }}</span>
                </div>

                <!-- Hadir (H) -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200/60 dark:border-emerald-900/40 rounded-lg text-emerald-800 dark:text-emerald-300 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                    <span class="text-[11px] font-medium">Hadir:</span>
                    <span class="font-bold font-mono" x-text="stats.h">{{ $dailyStats['h'] }}</span>
                    <span class="text-[10px] font-semibold text-emerald-600/80 dark:text-emerald-400/80" x-text="'(' + calcPct(stats.h) + '%)'">({{ $dailyStats['total'] > 0 ? round(($dailyStats['h'] / $dailyStats['total']) * 100) : 0 }}%)</span>
                </div>

                <!-- Sakit (S) -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200/60 dark:border-amber-900/40 rounded-lg text-amber-800 dark:text-amber-300 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                    <span class="text-[11px] font-medium">Sakit:</span>
                    <span class="font-bold font-mono" x-text="stats.s">{{ $dailyStats['s'] }}</span>
                    <span class="text-[10px] font-semibold text-amber-600/80 dark:text-amber-400/80" x-text="'(' + calcPct(stats.s) + '%)'">({{ $dailyStats['total'] > 0 ? round(($dailyStats['s'] / $dailyStats['total']) * 100) : 0 }}%)</span>
                </div>

                <!-- Izin (I) -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-sky-50/70 dark:bg-sky-950/30 border border-sky-200/60 dark:border-sky-900/40 rounded-lg text-sky-800 dark:text-sky-300 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-sky-500 shrink-0"></span>
                    <span class="text-[11px] font-medium">Izin:</span>
                    <span class="font-bold font-mono" x-text="stats.i">{{ $dailyStats['i'] }}</span>
                    <span class="text-[10px] font-semibold text-sky-600/80 dark:text-sky-400/80" x-text="'(' + calcPct(stats.i) + '%)'">({{ $dailyStats['total'] > 0 ? round(($dailyStats['i'] / $dailyStats['total']) * 100) : 0 }}%)</span>
                </div>

                <!-- Alpa (A) -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-rose-50/70 dark:bg-rose-950/30 border border-rose-200/60 dark:border-rose-900/40 rounded-lg text-rose-800 dark:text-rose-300 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span>
                    <span class="text-[11px] font-medium">Alpa:</span>
                    <span class="font-bold font-mono" x-text="stats.a">{{ $dailyStats['a'] }}</span>
                    <span class="text-[10px] font-semibold text-rose-600/80 dark:text-rose-400/80" x-text="'(' + calcPct(stats.a) + '%)'">({{ $dailyStats['total'] > 0 ? round(($dailyStats['a'] / $dailyStats['total']) * 100) : 0 }}%)</span>
                </div>

                <!-- Belum Diisi -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-100/80 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 rounded-lg text-slate-700 dark:text-slate-300 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-slate-400 shrink-0"></span>
                    <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Belum Diisi:</span>
                    <span class="font-bold font-mono text-slate-800 dark:text-slate-200" x-text="stats.unrecorded">{{ $dailyStats['unrecorded'] }}</span>
                </div>
            </section>

            <!-- INLINE FORM & TABLE SECTION -->
            @if($students->isEmpty())
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-8 text-center">
                    <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mb-3">
                        <i data-lucide="users-round" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Tidak ada murid aktif dalam rombel ini</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                        Silakan pilih rombel/kelompok lain atau tambahkan murid aktif pada rombel ini terlebih dahulu.
                    </p>
                </div>
            @else
                <form method="POST" action="{{ route('student-attendances.store') }}" id="dailyAttendanceForm">
                    @csrf
                    <input type="hidden" name="classroom_id" value="{{ $selectedClassroomId }}">
                    <input type="hidden" name="date" value="{{ $selectedDate }}">

                    <!-- FORM ACTION TOOLBAR -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 shadow-xs flex flex-wrap items-center justify-between gap-2.5 mb-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <!-- Quick Action: Tandai Semua Hadir -->
                            <button type="button" @click="markAllAs('H')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 rounded-lg text-xs font-semibold transition-colors cursor-pointer shadow-2xs">
                                <i data-lucide="check-check" class="w-3.5 h-3.5 text-emerald-600"></i>
                                <span>Tandai Semua Hadir (H)</span>
                            </button>

                            <!-- Quick Action: Reset Pilihan -->
                            <button type="button" @click="resetAll()"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-medium transition-colors cursor-pointer">
                                <i data-lucide="rotate-ccw" class="w-3 h-3"></i>
                                <span>Kosongkan Pilihan</span>
                            </button>
                        </div>

                        <!-- Top Simpan Button -->
                        <div class="flex items-center gap-2">
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-sm hover:shadow transition-all cursor-pointer">
                                <i data-lucide="save" class="w-4 h-4"></i>
                                <span>Simpan Absensi</span>
                            </button>
                        </div>
                    </div>

                    <!-- INLINE ATTENDANCE TABLE -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-xs">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <!-- ROW 1: HEADER (2 ROW MERGED) -->
                                    <tr class="bg-slate-50/90 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 text-[11px] font-bold border-b border-slate-200 dark:border-slate-800">
                                        <th rowspan="2" class="py-3 px-3 w-12 text-center uppercase tracking-wider border-r border-slate-200 dark:border-slate-800">
                                            No
                                        </th>
                                        <th rowspan="2" class="py-3 px-4 min-w-[220px] uppercase tracking-wider border-r border-slate-200 dark:border-slate-800">
                                            Nama Murid
                                        </th>
                                        <!-- 2 row di merge, row 1 Status Kehadiran, row 2 H S I A -->
                                        <th colspan="4" class="py-2.5 px-2 text-center uppercase tracking-wider border-b border-r border-slate-200 dark:border-slate-800 bg-slate-100/70 dark:bg-slate-800">
                                            Status Kehadiran
                                        </th>
                                        <th rowspan="2" class="py-3 px-4 min-w-[220px] uppercase tracking-wider">
                                            Keterangan
                                        </th>
                                    </tr>
                                    <!-- ROW 2: SUB-HEADER (H, S, I, A) -->
                                    <tr class="bg-slate-50/80 dark:bg-slate-800/60 text-[11px] font-bold border-b border-slate-200 dark:border-slate-800">
                                        <th class="py-2 px-2.5 w-16 text-center text-emerald-700 dark:text-emerald-400 bg-emerald-50/40 dark:bg-emerald-950/20 border-r border-slate-200 dark:border-slate-800">
                                            <span class="inline-flex items-center justify-center gap-1">
                                                <span>H</span>
                                                <span class="text-[9px] font-normal opacity-70">(Hadir)</span>
                                            </span>
                                        </th>
                                        <th class="py-2 px-2.5 w-16 text-center text-amber-700 dark:text-amber-400 bg-amber-50/40 dark:bg-amber-950/20 border-r border-slate-200 dark:border-slate-800">
                                            <span class="inline-flex items-center justify-center gap-1">
                                                <span>S</span>
                                                <span class="text-[9px] font-normal opacity-70">(Sakit)</span>
                                            </span>
                                        </th>
                                        <th class="py-2 px-2.5 w-16 text-center text-sky-700 dark:text-sky-400 bg-sky-50/40 dark:bg-sky-950/20 border-r border-slate-200 dark:border-slate-800">
                                            <span class="inline-flex items-center justify-center gap-1">
                                                <span>I</span>
                                                <span class="text-[9px] font-normal opacity-70">(Izin)</span>
                                            </span>
                                        </th>
                                        <th class="py-2 px-2.5 w-16 text-center text-rose-700 dark:text-rose-400 bg-rose-50/40 dark:bg-rose-950/20 border-r border-slate-200 dark:border-slate-800">
                                            <span class="inline-flex items-center justify-center gap-1">
                                                <span>A</span>
                                                <span class="text-[9px] font-normal opacity-70">(Alpa)</span>
                                            </span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 text-xs">
                                    @foreach($students as $st)
                                        @php
                                            $att = $existingDailyAttendances[$st->id] ?? null;
                                            $curStatus = $att ? $att->status : '';
                                            $curNotes = $att ? $att->notes : '';
                                        @endphp
                                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors"
                                            :class="{
                                                'bg-emerald-50/20 dark:bg-emerald-950/10': statuses[{{ $st->id }}] === 'H',
                                                'bg-amber-50/20 dark:bg-amber-950/10': statuses[{{ $st->id }}] === 'S',
                                                'bg-sky-50/20 dark:bg-sky-950/10': statuses[{{ $st->id }}] === 'I',
                                                'bg-rose-50/20 dark:bg-rose-950/10': statuses[{{ $st->id }}] === 'A',
                                            }">
                                            
                                            <!-- NO -->
                                            <td class="py-3 px-3 text-center text-slate-400 dark:text-slate-500 font-mono text-[11px] border-r border-slate-100 dark:border-slate-800">
                                                {{ $loop->iteration }}
                                            </td>

                                            <!-- NAMA MURID -->
                                            <td class="py-3 px-4 border-r border-slate-100 dark:border-slate-800">
                                                <div class="flex items-center gap-3">
                                                    <!-- Avatar -->
                                                    @if($st->student_photo_url)
                                                        <img src="{{ asset('storage/' . $st->student_photo_url) }}" alt="{{ $st->full_name }}"
                                                            class="w-8 h-8 rounded-full object-cover shrink-0 border border-slate-200 dark:border-slate-700">
                                                    @else
                                                        <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-[11px] flex items-center justify-center shrink-0 border border-slate-200 dark:border-slate-700">
                                                            {{ $st->avatar_initials }}
                                                        </div>
                                                    @endif

                                                    <div class="min-w-0">
                                                        <div class="flex items-center gap-1.5">
                                                            <span class="font-bold text-slate-900 dark:text-slate-100 truncate">
                                                                {{ $st->full_name }}
                                                            </span>
                                                            <!-- Gender Badge -->
                                                            @if($st->gender === 'L' || $st->gender === 'Laki-laki')
                                                                <span class="px-1.5 py-0.2 rounded text-[10px] font-semibold bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300">
                                                                    L
                                                                </span>
                                                            @else
                                                                <span class="px-1.5 py-0.2 rounded text-[10px] font-semibold bg-pink-50 text-pink-700 dark:bg-pink-950/50 dark:text-pink-300">
                                                                    P
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="flex items-center gap-2 text-[10.5px] text-slate-400 dark:text-slate-500 font-mono mt-0.5">
                                                            <span>NIS: {{ $st->nis ?: '-' }}</span>
                                                            @if($st->nickname)
                                                                <span>&bull;</span>
                                                                <span class="font-sans italic">"{{ $st->nickname }}"</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- STATUS: HADIR (H) -->
                                            <td class="py-2.5 px-2 text-center border-r border-slate-100 dark:border-slate-800 bg-emerald-50/10 dark:bg-emerald-950/5">
                                                <label class="inline-flex items-center justify-center cursor-pointer p-1 rounded-lg group">
                                                    <input type="radio" 
                                                        name="attendances[{{ $st->id }}][status]" 
                                                        value="H" 
                                                        x-model="statuses[{{ $st->id }}]"
                                                        @change="updateCounters()"
                                                        class="sr-only peer">
                                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs transition-all border
                                                        border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800
                                                        peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-emerald-600 peer-checked:shadow-sm
                                                        hover:border-emerald-300 dark:hover:border-emerald-700">
                                                        H
                                                    </div>
                                                </label>
                                            </td>

                                            <!-- STATUS: SAKIT (S) -->
                                            <td class="py-2.5 px-2 text-center border-r border-slate-100 dark:border-slate-800 bg-amber-50/10 dark:bg-amber-950/5">
                                                <label class="inline-flex items-center justify-center cursor-pointer p-1 rounded-lg group">
                                                    <input type="radio" 
                                                        name="attendances[{{ $st->id }}][status]" 
                                                        value="S" 
                                                        x-model="statuses[{{ $st->id }}]"
                                                        @change="updateCounters()"
                                                        class="sr-only peer">
                                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs transition-all border
                                                        border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800
                                                        peer-checked:bg-amber-500 peer-checked:text-white peer-checked:border-amber-500 peer-checked:shadow-sm
                                                        hover:border-amber-300 dark:hover:border-amber-700">
                                                        S
                                                    </div>
                                                </label>
                                            </td>

                                            <!-- STATUS: IZIN (I) -->
                                            <td class="py-2.5 px-2 text-center border-r border-slate-100 dark:border-slate-800 bg-sky-50/10 dark:bg-sky-950/5">
                                                <label class="inline-flex items-center justify-center cursor-pointer p-1 rounded-lg group">
                                                    <input type="radio" 
                                                        name="attendances[{{ $st->id }}][status]" 
                                                        value="I" 
                                                        x-model="statuses[{{ $st->id }}]"
                                                        @change="updateCounters()"
                                                        class="sr-only peer">
                                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs transition-all border
                                                        border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800
                                                        peer-checked:bg-sky-500 peer-checked:text-white peer-checked:border-sky-500 peer-checked:shadow-sm
                                                        hover:border-sky-300 dark:hover:border-sky-700">
                                                        I
                                                    </div>
                                                </label>
                                            </td>

                                            <!-- STATUS: ALPA (A) -->
                                            <td class="py-2.5 px-2 text-center border-r border-slate-100 dark:border-slate-800 bg-rose-50/10 dark:bg-rose-950/5">
                                                <label class="inline-flex items-center justify-center cursor-pointer p-1 rounded-lg group">
                                                    <input type="radio" 
                                                        name="attendances[{{ $st->id }}][status]" 
                                                        value="A" 
                                                        x-model="statuses[{{ $st->id }}]"
                                                        @change="updateCounters()"
                                                        class="sr-only peer">
                                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs transition-all border
                                                        border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800
                                                        peer-checked:bg-rose-600 peer-checked:text-white peer-checked:border-rose-600 peer-checked:shadow-sm
                                                        hover:border-rose-300 dark:hover:border-rose-700">
                                                        A
                                                    </div>
                                                </label>
                                            </td>

                                            <!-- KETERANGAN INLINE INPUT -->
                                            <td class="py-2.5 px-4 min-w-[240px]">
                                                <div class="relative w-full flex items-center">
                                                    <input type="text" 
                                                        name="attendances[{{ $st->id }}][notes]" 
                                                        x-model="notes[{{ $st->id }}]"
                                                        placeholder="Catatan sakit, izin, atau alasan..." 
                                                        class="w-full pl-3 pr-8 py-1.5 text-xs bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-800 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                                                    
                                                    <!-- Clear student button (absolute inside input, zero layout shift) -->
                                                    <button type="button" @click="clearStudent({{ $st->id }})" title="Kosongkan status & keterangan murid ini"
                                                        x-show="hasValue({{ $st->id }})"
                                                        x-cloak
                                                        class="absolute right-1.5 p-1 text-slate-400 hover:text-rose-500 transition-colors rounded cursor-pointer">
                                                        <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Table Footer -->
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/40 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400">
                            <span>Total Murid: <strong>{{ $students->count() }}</strong> anak</span>
                            <span>Rombel: <strong>{{ $selectedClassroom ? $selectedClassroom->name : 'Semua Rombel' }}</strong></span>
                        </div>
                    </div>
                </form>
            @endif
        </div>

        <!-- ========================================================================= -->
        <!-- TAB 2: REKAP BULANAN                                                     -->
        <!-- ========================================================================= -->
        <div x-show="activeTab === 'monthly'" x-cloak class="space-y-4">
            
            <!-- FILTER BAR REKAP BULANAN: KELAS, BULAN, TAHUN -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-4 shadow-xs">
                <form method="GET" action="{{ route('student-attendances.index') }}" id="monthlyFilterForm" class="flex flex-col lg:flex-row lg:items-end justify-between gap-3">
                    <input type="hidden" name="tab" value="monthly">
                    <input type="hidden" name="date" value="{{ $selectedDate }}">

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 flex-1">
                        <!-- Filter Kelas -->
                        <div>
                            <label for="monthly_classroom_id" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                Rombel / Kelompok <span class="text-rose-500">*</span>
                            </label>
                            <select name="classroom_id" id="monthly_classroom_id" onchange="document.getElementById('monthlyFilterForm').submit()"
                                class="w-full pl-3 pr-8 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors cursor-pointer">
                                @foreach($groupedClasses as $unit => $classes)
                                    <optgroup label="{{ $unit }}">
                                        @foreach($classes as $c)
                                            <option value="{{ $c->id }}" {{ $selectedClassroomId == $c->id ? 'selected' : '' }}>
                                                {{ $c->name }} ({{ $c->students_count ?? $c->active_students_count }} Murid)
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Bulan -->
                        <div>
                            <label for="month" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                Periode Bulan <span class="text-rose-500">*</span>
                            </label>
                            <select name="month" id="month" onchange="document.getElementById('monthlyFilterForm').submit()"
                                class="w-full pl-3 pr-8 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors cursor-pointer">
                                @foreach($monthNames as $mNum => $mLabel)
                                    <option value="{{ $mNum }}" {{ $selectedMonth == $mNum ? 'selected' : '' }}>
                                        {{ $mLabel }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Tahun -->
                        <div>
                            <label for="year" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                Tahun <span class="text-rose-500">*</span>
                            </label>
                            <select name="year" id="year" onchange="document.getElementById('monthlyFilterForm').submit()"
                                class="w-full pl-3 pr-8 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors cursor-pointer">
                                @foreach($yearsList as $yr)
                                    <option value="{{ $yr }}" {{ $selectedYear == $yr ? 'selected' : '' }}>
                                        {{ $yr }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- EXPORT & PRINT BUTTONS -->
                    <div class="flex flex-wrap items-center gap-2 pt-1 lg:pt-0 shrink-0">
                        <!-- Tombol Ekspor Excel -->
                        <a href="{{ route('student-attendances.export.excel', ['classroom_id' => $selectedClassroomId, 'month' => $selectedMonth, 'year' => $selectedYear]) }}"
                            class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-2xs transition-colors cursor-pointer">
                            <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5"></i>
                            <span>Ekspor Excel</span>
                        </a>

                        <!-- Tombol Cetak / PDF -->
                        <a href="{{ route('student-attendances.print', ['classroom_id' => $selectedClassroomId, 'month' => $selectedMonth, 'year' => $selectedYear]) }}"
                            target="_blank"
                            class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-700 shadow-2xs transition-colors cursor-pointer">
                            <i data-lucide="printer" class="w-3.5 h-3.5 text-slate-500"></i>
                            <span>Cetak PDF</span>
                        </a>
                    </div>
                </form>
            </div>

            <!-- ULTRA-COMPACT MONTHLY SUMMARY STRIP -->
            <section class="flex flex-wrap items-center gap-2 sm:gap-2.5 w-full text-xs">
                <!-- Total Murid -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 shadow-2xs">
                    <i data-lucide="users" class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400 shrink-0"></i>
                    <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Total Murid:</span>
                    <span class="font-bold font-mono text-slate-900 dark:text-slate-50">{{ $monthlyStats['total_students'] }}</span>
                </div>

                <!-- Hari Tercatat -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-50/70 dark:bg-indigo-950/30 border border-indigo-200/60 dark:border-indigo-900/40 rounded-lg text-indigo-800 dark:text-indigo-300 shadow-2xs">
                    <i data-lucide="calendar-check" class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400 shrink-0"></i>
                    <span class="text-[11px] font-medium">Hari Tercatat:</span>
                    <span class="font-bold font-mono">{{ $monthlyStats['effective_days'] }} Hari</span>
                </div>

                <!-- Total Hadir -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200/60 dark:border-emerald-900/40 rounded-lg text-emerald-800 dark:text-emerald-300 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                    <span class="text-[11px] font-medium">Total Hadir:</span>
                    <span class="font-bold font-mono">{{ $monthlyStats['total_h'] }}</span>
                </div>

                <!-- Total Sakit -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200/60 dark:border-amber-900/40 rounded-lg text-amber-800 dark:text-amber-300 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                    <span class="text-[11px] font-medium">Total Sakit:</span>
                    <span class="font-bold font-mono">{{ $monthlyStats['total_s'] }}</span>
                </div>

                <!-- Total Izin -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-sky-50/70 dark:bg-sky-950/30 border border-sky-200/60 dark:border-sky-900/40 rounded-lg text-sky-800 dark:text-sky-300 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-sky-500 shrink-0"></span>
                    <span class="text-[11px] font-medium">Total Izin:</span>
                    <span class="font-bold font-mono">{{ $monthlyStats['total_i'] }}</span>
                </div>

                <!-- Total Alpa -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-rose-50/70 dark:bg-rose-950/30 border border-rose-200/60 dark:border-rose-900/40 rounded-lg text-rose-800 dark:text-rose-300 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span>
                    <span class="text-[11px] font-medium">Total Alpa:</span>
                    <span class="font-bold font-mono">{{ $monthlyStats['total_a'] }}</span>
                </div>

                <!-- Rata-rata Kehadiran -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200/60 dark:border-emerald-900/40 rounded-lg text-emerald-800 dark:text-emerald-300 shadow-2xs">
                    <i data-lucide="percent" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0"></i>
                    <span class="text-[11px] font-medium">Rata-rata:</span>
                    <span class="font-bold font-mono">{{ $monthlyStats['avg_attendance_rate'] }}%</span>
                </div>
            </section>

            <!-- REKAP TABLE: NIS, NAMA MURID, HADIR, SAKIT, IZIN, ALPA -->
            @if(empty($monthlyRecap))
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-8 text-center">
                    <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mb-3">
                        <i data-lucide="bar-chart-2" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Tidak ada data rekapitulasi</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                        Belum ada pencatatan kehadiran murid untuk bulan {{ $monthNames[$selectedMonth] ?? $selectedMonth }} {{ $selectedYear }}.
                    </p>
                </div>
            @else
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-xs">
                    <!-- Table Title Sub-bar -->
                    <div class="px-4 py-3 bg-slate-50/70 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                Rekap Kehadiran: {{ $selectedClassroom->name }} &mdash; {{ $monthNames[$selectedMonth] }} {{ $selectedYear }}
                            </span>
                        </div>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400">
                            Rata-rata Kehadiran Kelas: <strong>{{ $monthlyStats['avg_attendance_rate'] }}%</strong>
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 text-[11px] font-bold border-b border-slate-200 dark:border-slate-800">
                                    <th class="py-3 px-3 w-12 text-center uppercase tracking-wider">No</th>
                                    <th class="py-3 px-3 w-32 uppercase tracking-wider">NIS</th>
                                    <th class="py-3 px-4 min-w-[200px] uppercase tracking-wider">Nama Murid</th>
                                    <th class="py-3 px-3 text-center uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Hadir</th>
                                    <th class="py-3 px-3 text-center uppercase tracking-wider text-amber-700 dark:text-amber-400">Sakit</th>
                                    <th class="py-3 px-3 text-center uppercase tracking-wider text-sky-700 dark:text-sky-400">Izin</th>
                                    <th class="py-3 px-3 text-center uppercase tracking-wider text-rose-700 dark:text-rose-400">Alpa</th>
                                    <th class="py-3 px-3 text-center uppercase tracking-wider text-slate-700 dark:text-slate-300">Total Pertemuan</th>
                                    <th class="py-3 px-4 text-center uppercase tracking-wider">% Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 text-xs">
                                @foreach($monthlyRecap as $item)
                                    @php
                                        $st = $item['student'];
                                    @endphp
                                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                                        <!-- NO -->
                                        <td class="py-2.5 px-3 text-center text-slate-400 dark:text-slate-500 font-mono text-[11px]">
                                            {{ $loop->iteration }}
                                        </td>

                                        <!-- NIS -->
                                        <td class="py-2.5 px-3 font-mono font-semibold text-slate-700 dark:text-slate-300 text-[11.5px]">
                                            {{ $st->nis ?: '-' }}
                                        </td>

                                        <!-- NAMA MURID -->
                                        <td class="py-2.5 px-4 font-medium text-slate-900 dark:text-slate-100">
                                            <div class="flex items-center gap-2">
                                                <span>{{ $st->full_name }}</span>
                                                @if($st->gender === 'L' || $st->gender === 'Laki-laki')
                                                    <span class="px-1.5 py-0.2 rounded text-[9.5px] font-semibold bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300">
                                                        L
                                                    </span>
                                                @else
                                                    <span class="px-1.5 py-0.2 rounded text-[9.5px] font-semibold bg-pink-50 text-pink-700 dark:bg-pink-950/50 dark:text-pink-300">
                                                        P
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- HADIR -->
                                        <td class="py-2.5 px-3 text-center">
                                            <span class="inline-flex items-center justify-center min-w-[24px] px-1.5 py-0.5 rounded font-mono font-bold text-xs bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                                {{ $item['h'] }}
                                            </span>
                                        </td>

                                        <!-- SAKIT -->
                                        <td class="py-2.5 px-3 text-center">
                                            <span class="inline-flex items-center justify-center min-w-[24px] px-1.5 py-0.5 rounded font-mono font-bold text-xs {{ $item['s'] > 0 ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800' : 'text-slate-400' }}">
                                                {{ $item['s'] }}
                                            </span>
                                        </td>

                                        <!-- IZIN -->
                                        <td class="py-2.5 px-3 text-center">
                                            <span class="inline-flex items-center justify-center min-w-[24px] px-1.5 py-0.5 rounded font-mono font-bold text-xs {{ $item['i'] > 0 ? 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300 border border-sky-200 dark:border-sky-800' : 'text-slate-400' }}">
                                                {{ $item['i'] }}
                                            </span>
                                        </td>

                                        <!-- ALPA -->
                                        <td class="py-2.5 px-3 text-center">
                                            <span class="inline-flex items-center justify-center min-w-[24px] px-1.5 py-0.5 rounded font-mono font-bold text-xs {{ $item['a'] > 0 ? 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300 border border-rose-200 dark:border-rose-800' : 'text-slate-400' }}">
                                                {{ $item['a'] }}
                                            </span>
                                        </td>

                                        <!-- TOTAL PERTEMUAN -->
                                        <td class="py-2.5 px-3 text-center font-mono font-semibold text-slate-700 dark:text-slate-300">
                                            {{ $item['total'] }}
                                        </td>

                                        <!-- % KEHADIRAN -->
                                        <td class="py-2.5 px-4 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <div class="w-16 bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                                    <div class="h-full rounded-full {{ $item['rate'] >= 85 ? 'bg-emerald-500' : ($item['rate'] >= 70 ? 'bg-amber-500' : 'bg-rose-500') }}"
                                                        style="width: {{ min(100, $item['rate']) }}%"></div>
                                                </div>
                                                <span class="font-mono font-bold text-xs {{ $item['rate'] >= 85 ? 'text-emerald-700 dark:text-emerald-400' : ($item['rate'] >= 70 ? 'text-amber-600' : 'text-rose-600') }}">
                                                    {{ $item['rate'] }}%
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <!-- SUMMARY TOTAL FOOTER ROW -->
                            <tfoot>
                                <tr class="bg-slate-100/90 dark:bg-slate-800 font-bold text-xs text-slate-800 dark:text-slate-200 border-t-2 border-slate-300 dark:border-slate-700">
                                    <td colspan="3" class="py-3 px-4 text-center uppercase tracking-wider">
                                        TOTAL KELAS
                                    </td>
                                    <td class="py-3 px-3 text-center font-mono text-emerald-700 dark:text-emerald-400">
                                        {{ $monthlyStats['total_h'] }}
                                    </td>
                                    <td class="py-3 px-3 text-center font-mono text-amber-700 dark:text-amber-400">
                                        {{ $monthlyStats['total_s'] }}
                                    </td>
                                    <td class="py-3 px-3 text-center font-mono text-sky-700 dark:text-sky-400">
                                        {{ $monthlyStats['total_i'] }}
                                    </td>
                                    <td class="py-3 px-3 text-center font-mono text-rose-700 dark:text-rose-400">
                                        {{ $monthlyStats['total_a'] }}
                                    </td>
                                    <td class="py-3 px-3 text-center font-mono">
                                        {{ $monthlyStats['total_h'] + $monthlyStats['total_s'] + $monthlyStats['total_i'] + $monthlyStats['total_a'] }}
                                    </td>
                                    <td class="py-3 px-4 text-center font-mono text-emerald-700 dark:text-emerald-400">
                                        {{ $monthlyStats['avg_attendance_rate'] }}%
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endif
        </div>

    </div>

    <!-- ALPINE.JS COMPONENT LOGIC -->
    <script>
        function studentAttendanceApp() {
            const config = {
                initialTab: @json($activeTab),
                classroomId: @json($selectedClassroomId),
                selectedDate: @json($selectedDate),
                initialStatuses: @json($initStatuses),
                initialNotes: @json($initNotes)
            };

            return {
                activeTab: config.initialTab || 'daily',
                classroomId: config.classroomId,
                selectedDate: config.selectedDate,
                statuses: Object.assign({}, config.initialStatuses),
                notes: Object.assign({}, config.initialNotes),
                stats: {
                    total: 0,
                    h: 0,
                    s: 0,
                    i: 0,
                    a: 0,
                    recorded: 0,
                    unrecorded: 0
                },

                init() {
                    this.updateCounters();
                },

                hasValue(studentId) {
                    const s = this.statuses[studentId];
                    const n = this.notes[studentId];
                    return (s && s !== '') || (n && String(n).trim() !== '');
                },

                setTab(tabName) {
                    this.activeTab = tabName;
                    const url = new URL(window.location.href);
                    url.searchParams.set('tab', tabName);
                    window.history.replaceState({}, '', url);
                },

                setToday() {
                    const today = new Date().toISOString().split('T')[0];
                    const dateInput = document.getElementById('date');
                    if (dateInput) {
                        dateInput.value = today;
                        document.getElementById('dailyFilterForm').submit();
                    }
                },

                markAllAs(status) {
                    for (const id in this.statuses) {
                        this.statuses[id] = status;
                    }
                    this.updateCounters();
                },

                resetAll() {
                    for (const id in this.statuses) {
                        this.statuses[id] = '';
                    }
                    for (const id in this.notes) {
                        this.notes[id] = '';
                    }
                    this.updateCounters();
                },

                clearStudent(studentId) {
                    this.statuses[studentId] = '';
                    this.notes[studentId] = '';
                    this.updateCounters();
                },

                updateCounters() {
                    let total = 0;
                    let h = 0, s = 0, i = 0, a = 0;
                    for (const id in this.statuses) {
                        total++;
                        const st = this.statuses[id];
                        if (st === 'H') h++;
                        else if (st === 'S') s++;
                        else if (st === 'I') i++;
                        else if (st === 'A') a++;
                    }
                    const recorded = h + s + i + a;
                    this.stats = {
                        total: total,
                        h: h,
                        s: s,
                        i: i,
                        a: a,
                        recorded: recorded,
                        unrecorded: Math.max(0, total - recorded)
                    };
                },

                calcPct(count) {
                    if (!this.stats.total || this.stats.total === 0) return 0;
                    return Math.round((count / this.stats.total) * 100);
                }
            };
        }
    </script>
</x-admin-layout>
