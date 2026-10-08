<x-admin-layout>
    <div class="p-4 sm:p-5 lg:p-6 space-y-4 lg:space-y-5" x-data="{ exportDropdownOpen: false, activeReportTab: 'rekap' }">
        <!-- GREETING / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3.5 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <div class="flex items-center gap-2.5">
                    <div class="p-2 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-xl border border-indigo-500/20">
                        <i data-lucide="file-bar-chart-2" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">
                                Rekapitulasi Rombel
                            </h2>
                            @if($selectedYear)
                                <span class="text-xs px-2.5 py-0.5 rounded-full font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                    {{ $selectedYear->name }}
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Laporan komprehensif distribusi peserta didik, rasio gender, PDBK, dan penugasan wali kelas {{ function_exists('setting') ? setting('unit_name', 'SD Anak Saleh') : 'SD Anak Saleh' }}.</p>
                    </div>
                </div>
            </div>

            <!-- ACTION CONTROLS & FILTER -->
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <!-- Filter Tapel -->
                <form action="{{ route('student-reports.index') }}" method="GET" class="flex items-center">
                    <div class="relative">
                        <select name="academic_year_id" onchange="this.form.submit()"
                            class="h-8.5 pl-8 pr-8 text-xs font-semibold rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-200 hover:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 shadow-xs cursor-pointer appearance-none transition-colors">
                            @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}" {{ ($selectedYear && $selectedYear->id === $ay->id) || $selectedYearId == $ay->id ? 'selected' : '' }}>
                                    Tapel {{ $ay->name }} {{ $ay->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    </div>
                </form>

                <!-- DROPDOWN EKSPOR & CETAK -->
                <div class="relative" @click.outside="exportDropdownOpen = false">
                    <button type="button" @click="exportDropdownOpen = !exportDropdownOpen"
                        class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 shadow-xs transition-all cursor-pointer">
                        <i data-lucide="download" class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400"></i>
                        <span>Ekspor & Cetak</span>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': exportDropdownOpen }"></i>
                    </button>

                    <div x-show="exportDropdownOpen"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        x-cloak
                        class="absolute right-0 mt-1.5 w-60 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl py-1.5 z-50 divide-y divide-slate-100 dark:divide-slate-800/60">
                        
                        <div class="px-3 py-1.5 bg-slate-50/50 dark:bg-slate-800/30">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Pilihan Format Dokumen</p>
                        </div>

                        <div class="py-1">
                            <!-- Ekspor Excel -->
                            <a href="{{ route('student-reports.export', ['academic_year_id' => $selectedYearId]) }}" @click="exportDropdownOpen = false"
                                class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors group">
                                <div class="p-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/60 group-hover:scale-105 transition-transform">
                                    <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                                </div>
                                <div class="flex-1">
                                    <span class="font-semibold block text-slate-800 dark:text-slate-200 group-hover:text-emerald-600 dark:group-hover:text-emerald-400">Ekspor File Excel</span>
                                    <span class="text-[10px] text-slate-400 block">Format .xlsx Rekapitulasi TU</span>
                                </div>
                            </a>
                        </div>

                        <div class="py-1">
                            <!-- Cetak / Print Browser -->
                            <a href="{{ route('student-reports.print', ['academic_year_id' => $selectedYearId]) }}" target="_blank" @click="exportDropdownOpen = false"
                                class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-700 dark:hover:text-indigo-400 transition-colors group">
                                <div class="p-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200/60 dark:border-indigo-800/60 group-hover:scale-105 transition-transform">
                                    <i data-lucide="printer" class="w-4 h-4"></i>
                                </div>
                                <div class="flex-1">
                                    <span class="font-semibold block text-slate-800 dark:text-slate-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">Cetak Laporan Resmi</span>
                                    <span class="text-[10px] text-slate-400 block">Preview kop surat & tanda tangan</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- NAVIGATION TABS (TAB 1: REKAPITULASI, TAB 2: SHEET REPORT TU) -->
        <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-2 overflow-x-auto text-xs font-semibold">
            <!-- TAB 1 (Urutan Pertama) -->
            <button type="button" @click="activeReportTab = 'rekap'"
                :class="activeReportTab === 'rekap' ? 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800 border-transparent'"
                class="px-3.5 py-1.5 rounded-lg border transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer">
                <i data-lucide="file-bar-chart-2" class="w-3.5 h-3.5" :class="activeReportTab === 'rekap' ? 'text-indigo-600 dark:text-indigo-400' : ''"></i>
                <span>Rekapitulasi Distribusi Siswa & Rombel</span>
            </button>

            <!-- TAB 2 (Sheet REPORT TU) -->
            <button type="button" @click="activeReportTab = 'sheet_report'"
                :class="activeReportTab === 'sheet_report' ? 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800 border-transparent'"
                class="px-3.5 py-1.5 rounded-lg border transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer">
                <i data-lucide="table-2" class="w-3.5 h-3.5" :class="activeReportTab === 'sheet_report' ? 'text-indigo-600 dark:text-indigo-400' : ''"></i>
                <span>Data Peserta Didik & Formasi Guru</span>
            </button>
        </div>

        <!-- ========================================================================= -->
        <!-- TAB 1: REKAPITULASI DISTRIBUSI SISWA & ROMBEL (8 KOLOM BERSIH) -->
        <!-- ========================================================================= -->
        <div x-show="activeReportTab === 'rekap'" class="space-y-4 lg:space-y-5">
            <!-- KPI SUMMARY CARDS (Compact) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5 sm:gap-3 lg:gap-3.5">
                <!-- Total Peserta Didik -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 shadow-xs flex items-center gap-3 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-100 dark:border-indigo-900/40">
                        <i data-lucide="users" class="w-5 h-5"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10.5px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Total Siswa</p>
                        <div class="flex items-baseline gap-1.5 mt-0.5">
                            <h3 class="text-xl font-bold tracking-tight text-indigo-600 dark:text-indigo-400 font-mono">{{ $grandTotalStudents }}</h3>
                            <span class="text-[10px] text-slate-400 font-medium truncate">Siswa</span>
                        </div>
                    </div>
                </div>

                <!-- Laki-laki -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 shadow-xs flex items-center gap-3 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 border border-blue-100 dark:border-blue-900/40">
                        <i data-lucide="user" class="w-5 h-5"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10.5px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Putra (L)</p>
                        <div class="flex items-baseline gap-1.5 mt-0.5">
                            <h3 class="text-xl font-bold tracking-tight text-blue-700 dark:text-blue-300 font-mono">{{ $grandTotalMale }}</h3>
                            <span class="text-[10px] text-slate-400 font-medium truncate">({{ $malePercent }}%)</span>
                        </div>
                    </div>
                </div>

                <!-- Perempuan -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 shadow-xs flex items-center gap-3 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-pink-50 dark:bg-pink-950/50 text-pink-600 dark:text-pink-400 flex items-center justify-center shrink-0 border border-pink-100 dark:border-pink-900/40">
                        <i data-lucide="user-check" class="w-5 h-5"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10.5px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Putri (P)</p>
                        <div class="flex items-baseline gap-1.5 mt-0.5">
                            <h3 class="text-xl font-bold tracking-tight text-pink-700 dark:text-pink-300 font-mono">{{ $grandTotalFemale }}</h3>
                            <span class="text-[10px] text-slate-400 font-medium truncate">({{ $femalePercent }}%)</span>
                        </div>
                    </div>
                </div>

                <!-- Inklusi / PDBK -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 shadow-xs flex items-center gap-3 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0 border border-purple-100 dark:border-purple-900/40">
                        <i data-lucide="heart-handshake" class="w-5 h-5"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10.5px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">PDBK (Inklusi)</p>
                        <div class="flex items-baseline gap-1.5 mt-0.5">
                            <h3 class="text-xl font-bold tracking-tight text-purple-700 dark:text-purple-300 font-mono">{{ $grandTotalPdbk }}</h3>
                            <span class="text-[10px] text-slate-400 font-medium truncate">({{ $pdbkPercent }}%)</span>
                        </div>
                    </div>
                </div>

                <!-- Total Rombel -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 shadow-xs flex items-center gap-3 transition-colors col-span-2 sm:col-span-1">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-100 dark:border-emerald-900/40">
                        <i data-lucide="layout-grid" class="w-5 h-5"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10.5px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Total Rombel</p>
                        <div class="flex items-baseline gap-1.5 mt-0.5">
                            <h3 class="text-xl font-bold tracking-tight text-slate-900 dark:text-slate-50 font-mono">{{ $grandTotalClassrooms }}</h3>
                            <span class="text-[10px] text-slate-400 font-medium truncate">Kelas</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- UNASSIGNED STUDENTS ALERT (IF ANY) -->
            @if(!empty($unassignedStudents) && $unassignedTotal > 0)
                <div class="p-3.5 sm:p-4 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 flex items-start gap-3 shadow-xs">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/60 text-amber-700 dark:text-amber-300 flex items-center justify-center shrink-0 mt-0.5">
                        <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    </div>
                    <div class="flex-1 text-xs text-amber-900 dark:text-amber-200">
                        <div class="font-bold text-sm text-amber-800 dark:text-amber-100 mb-0.5 flex flex-wrap items-center gap-2">
                            <span>Perhatian: Terdapat {{ $unassignedTotal }} Siswa Aktif Belum Memiliki Rombel</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-200 dark:bg-amber-900 text-amber-900 dark:text-amber-100">
                                Perlu Penempatan Rombel
                            </span>
                        </div>
                        <p class="text-amber-700 dark:text-amber-300">
                            Siswa di bawah ini berstatus aktif namun belum dialokasikan ke dalam kelas/rombel. Siswa tetap dihitung dalam total rekapitulasi ({{ $grandTotalStudents }} Siswa) dan ditampilkan di baris tabel paling bawah agar Admin dapat segera menentukan rombelnya.
                        </p>
                    </div>
                </div>
            @endif

            <!-- MAIN REPORT TABLE (8 KOLOM BERSIH) -->
            <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs overflow-hidden w-full">
                <!-- Table Header Info Bar -->
                <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-900/50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <i data-lucide="table" class="w-4 h-4 text-indigo-600 dark:text-indigo-400"></i>
                        <h2 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-slate-100">
                            Tabel Rekapitulasi Distribusi Siswa & Wali Kelas per Rombel
                        </h2>
                    </div>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">
                        Tahun Ajaran: {{ $selectedYear ? $selectedYear->name : '-' }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-100/80 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300">
                                <th class="px-3 py-2.5 text-center text-[11px] font-bold uppercase tracking-wider w-12">No</th>
                                <th class="px-3 py-2.5 text-left text-[11px] font-bold uppercase tracking-wider min-w-[140px]">Nama Kelas</th>
                                <th class="px-3 py-2.5 text-center text-[11px] font-bold uppercase tracking-wider w-20">Kode</th>
                                <th class="px-3 py-2.5 text-center text-[11px] font-bold uppercase tracking-wider w-24 text-blue-600 dark:text-blue-400">Laki-Laki</th>
                                <th class="px-3 py-2.5 text-center text-[11px] font-bold uppercase tracking-wider w-24 text-pink-600 dark:text-pink-400">Perempuan</th>
                                <th class="px-3 py-2.5 text-center text-[11px] font-bold uppercase tracking-wider w-28 text-slate-900 dark:text-slate-100">Jml Siswa</th>
                                <th class="px-3 py-2.5 text-center text-[11px] font-bold uppercase tracking-wider w-28 text-purple-600 dark:text-purple-400">PDBK</th>
                                <th class="px-3 py-2.5 text-left text-[11px] font-bold uppercase tracking-wider min-w-[200px]">Wali Kelas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                            @php $rowCounter = 1; @endphp
                            @forelse($levelReports as $lvl)
                                <!-- LEVEL SECTION HEADER -->
                                <tr class="bg-slate-50 dark:bg-slate-800/40 border-t-2 border-b border-slate-200 dark:border-slate-700/80">
                                    <td colspan="8" class="px-3 py-2">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full bg-indigo-600 dark:bg-indigo-400"></span>
                                                <span class="font-bold text-xs uppercase tracking-wide text-slate-800 dark:text-slate-200">
                                                    {{ $lvl['level']->name }}
                                                </span>
                                                <span class="text-[10px] px-1.5 py-0.2 rounded bg-slate-200/80 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-semibold font-mono">
                                                    {{ count($lvl['classrooms']) }} Rombel
                                                </span>
                                            </div>
                                            @if(count($lvl['classrooms']) > 0)
                                                <span class="text-[11px] font-semibold text-indigo-600 dark:text-indigo-400 font-mono">
                                                    Total: {{ $lvl['subtotal_students'] }} Siswa
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                <!-- CLASSROOM ROWS -->
                                @forelse($lvl['classrooms'] as $cr)
                                    <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition-colors group">
                                        <td class="px-3 py-2.5 text-center text-slate-400 font-mono text-[11px]">
                                            {{ $rowCounter++ }}
                                        </td>
                                        <td class="px-3 py-2.5 font-bold text-slate-900 dark:text-slate-100 text-xs">
                                            <div class="flex items-center gap-1.5">
                                                <span>{{ $cr['name'] }}</span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-2.5 text-center font-mono font-semibold text-xs text-indigo-600 dark:text-indigo-400">
                                            <span class="px-2 py-0.5 rounded bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-100 dark:border-indigo-900/50">
                                                {{ $cr['code'] }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2.5 text-center font-mono font-bold text-blue-600 dark:text-blue-400">
                                            {{ $cr['male'] }}
                                        </td>
                                        <td class="px-3 py-2.5 text-center font-mono font-bold text-pink-600 dark:text-pink-400">
                                            {{ $cr['female'] }}
                                        </td>
                                        <td class="px-3 py-2.5 text-center font-mono font-bold text-slate-900 dark:text-slate-100 bg-slate-50/50 dark:bg-slate-800/20">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs {{ $cr['total'] > 0 ? 'bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200' : 'text-slate-400' }}">
                                                {{ $cr['total'] }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2.5 text-center font-mono font-bold">
                                            @if($cr['pdbk'] > 0)
                                                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                                    {{ $cr['pdbk'] }}
                                                </span>
                                            @else
                                                <span class="text-slate-300 dark:text-slate-600 font-normal">0</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2.5">
                                            @if($cr['homeroom_teacher'] && $cr['homeroom_teacher'] !== '-')
                                                <div class="flex items-center gap-2">
                                                    <div class="w-6 h-6 rounded-full bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-[10px] shrink-0 border border-indigo-100 dark:border-indigo-900/50">
                                                        <i data-lucide="user-check" class="w-3 h-3"></i>
                                                    </div>
                                                    <span class="font-medium text-slate-800 dark:text-slate-200">
                                                        {{ $cr['homeroom_teacher'] }}
                                                    </span>
                                                </div>
                                            @else
                                                <span class="text-slate-400 italic text-[11px]">Belum Ditentukan</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-3 py-3 text-center text-slate-400 italic text-[11px]">
                                            Tidak ada rombongan belajar aktif pada {{ $lvl['level']->name }} untuk tahun ajaran ini.
                                        </td>
                                    </tr>
                                @endforelse

                                <!-- SUBTOTAL PER LEVEL ROW -->
                                @if(count($lvl['classrooms']) > 0)
                                    <tr class="bg-indigo-50/50 dark:bg-indigo-950/30 font-bold border-t border-indigo-100 dark:border-indigo-900/40 text-slate-800 dark:text-slate-200">
                                        <td colspan="3" class="px-3 py-2 text-right uppercase tracking-wider text-[11px] text-indigo-900 dark:text-indigo-200">
                                            Subtotal {{ $lvl['level']->name }}:
                                        </td>
                                        <td class="px-3 py-2 text-center font-mono text-blue-600 dark:text-blue-400">
                                            {{ $lvl['subtotal_male'] }}
                                        </td>
                                        <td class="px-3 py-2 text-center font-mono text-pink-600 dark:text-pink-400">
                                            {{ $lvl['subtotal_female'] }}
                                        </td>
                                        <td class="px-3 py-2 text-center font-mono text-indigo-700 dark:text-indigo-300 bg-indigo-100/50 dark:bg-indigo-900/40">
                                            {{ $lvl['subtotal_students'] }}
                                        </td>
                                        <td class="px-3 py-2 text-center font-mono text-purple-700 dark:text-purple-300">
                                            {{ $lvl['subtotal_pdbk'] }}
                                        </td>
                                        <td class="px-3 py-2 text-slate-400 font-normal italic text-[10px]">
                                            Total {{ count($lvl['classrooms']) }} rombel pada jenjang ini
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="8" class="px-3 py-8 text-center text-slate-400">
                                        Belum ada data tingkat kelas atau rombel yang terkonfigurasi.
                                    </td>
                                </tr>
                            @endforelse

                            <!-- SISWA AKTIF BELUM MEMILIKI ROMBEL (JIKA ADA) -->
                            @if(!empty($unassignedStudents) && $unassignedTotal > 0)
                                <tr class="bg-amber-50/80 dark:bg-amber-950/40 border-t-2 border-b border-amber-200 dark:border-amber-800/80">
                                    <td colspan="8" class="px-3 py-2">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                                <span class="font-bold text-xs uppercase tracking-wide text-amber-900 dark:text-amber-200">
                                                    SISWA BELUM MEMILIKI ROMBEL (PERLU PENEMPATAN KELAS)
                                                </span>
                                                <span class="text-[10px] px-1.5 py-0.2 rounded bg-amber-200 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 font-semibold font-mono">
                                                    {{ $unassignedTotal }} Siswa
                                                </span>
                                            </div>
                                            <span class="text-[11px] font-semibold text-amber-700 dark:text-amber-300 font-mono">
                                                Total: {{ $unassignedTotal }} Siswa
                                            </span>
                                        </div>
                                    </td>
                                </tr>

                                @foreach($unassignedStudents as $us)
                                    @php
                                        $isUsMale = in_array($us->gender, ['L', 'Laki-laki', 'Male', 'LAKI-LAKI']);
                                        $isUsFemale = in_array($us->gender, ['P', 'Perempuan', 'Female', 'PEREMPUAN']);
                                        $isUsPdbk = ($us->student_type && (str_contains(strtoupper($us->student_type), 'PDBK') || str_contains(strtoupper($us->student_type), 'KHUSUS') || str_contains(strtoupper($us->student_type), 'INKLUSI'))) || !empty($us->special_needs_type) || !empty($us->gpk_employee_id);
                                    @endphp
                                    <tr class="bg-amber-50/30 dark:bg-amber-950/10 hover:bg-amber-50/60 dark:hover:bg-amber-950/30 transition-colors group">
                                        <td class="px-3 py-2.5 text-center text-slate-400 font-mono text-[11px]">
                                            {{ $rowCounter++ }}
                                        </td>
                                        <td class="px-3 py-2.5 font-bold text-slate-900 dark:text-slate-100 text-xs">
                                            <div class="flex items-center gap-2">
                                                <span>{{ $us->full_name }}</span>
                                                <span class="text-[10px] font-mono text-slate-500 dark:text-slate-400">(NIS: {{ $us->nis ?: '-' }})</span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-2.5 text-center font-mono font-semibold text-xs">
                                            <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-200 border border-amber-200 dark:border-amber-800">
                                                Tanpa Rombel
                                            </span>
                                        </td>
                                        <td class="px-3 py-2.5 text-center font-mono font-bold text-blue-600 dark:text-blue-400">
                                            {{ $isUsMale ? 1 : 0 }}
                                        </td>
                                        <td class="px-3 py-2.5 text-center font-mono font-bold text-pink-600 dark:text-pink-400">
                                            {{ $isUsFemale ? 1 : 0 }}
                                        </td>
                                        <td class="px-3 py-2.5 text-center font-mono font-bold text-slate-900 dark:text-slate-100 bg-amber-50/50 dark:bg-amber-950/20">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 font-bold">
                                                1
                                            </span>
                                        </td>
                                        <td class="px-3 py-2.5 text-center font-mono font-bold">
                                            @if($isUsPdbk)
                                                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                                    1
                                                </span>
                                            @else
                                                <span class="text-slate-300 dark:text-slate-600 font-normal">0</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2.5">
                                            <a href="{{ route('students.index', ['edit_student_id' => $us->id, 'search' => $us->nis ?: $us->full_name]) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-600 hover:bg-amber-700 text-white shadow-xs transition-colors" title="Klik untuk menentukan rombel siswa ini">
                                                <i data-lucide="edit-3" class="w-3 h-3"></i>
                                                <span>Tentukan Rombel</span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach

                                <!-- SUBTOTAL UNASSIGNED -->
                                <tr class="bg-amber-100/50 dark:bg-amber-950/40 font-bold border-t border-amber-200 dark:border-amber-800 text-slate-800 dark:text-slate-200">
                                    <td colspan="3" class="px-3 py-2 text-right uppercase tracking-wider text-[11px] text-amber-900 dark:text-amber-200">
                                        Subtotal Belum Ada Rombel:
                                    </td>
                                    <td class="px-3 py-2 text-center font-mono text-blue-600 dark:text-blue-400">
                                        {{ $unassignedMale }}
                                    </td>
                                    <td class="px-3 py-2 text-center font-mono text-pink-600 dark:text-pink-400">
                                        {{ $unassignedFemale }}
                                    </td>
                                    <td class="px-3 py-2 text-center font-mono text-amber-800 dark:text-amber-200 bg-amber-200/50 dark:bg-amber-900/40">
                                        {{ $unassignedTotal }}
                                    </td>
                                    <td class="px-3 py-2 text-center font-mono text-purple-700 dark:text-purple-300">
                                        {{ $unassignedPdbk }}
                                    </td>
                                    <td class="px-3 py-2 text-amber-700 dark:text-amber-400 font-normal italic text-[10px]">
                                        Siswa aktif menunggu penempatan kelas oleh Admin
                                    </td>
                                </tr>
                            @endif

                            <!-- GRAND TOTAL FOOTER ROW -->
                            <tr class="bg-slate-900 text-white font-bold border-t-2 border-indigo-500 shadow-inner">
                                <td colspan="3" class="px-4 py-3.5 text-right uppercase tracking-wider text-xs font-bold text-slate-200">
                                    JUMLAH PESERTA DIDIK KESELURUHAN:
                                </td>
                                <td class="px-3 py-3.5 text-center font-mono text-sm text-blue-300">
                                    {{ $grandTotalMale }}
                                </td>
                                <td class="px-3 py-3.5 text-center font-mono text-sm text-pink-300">
                                    {{ $grandTotalFemale }}
                                </td>
                                <td class="px-3 py-3.5 text-center font-mono text-base text-white bg-indigo-700/80">
                                    {{ $grandTotalStudents }}
                                </td>
                                <td class="px-3 py-3.5 text-center font-mono text-sm text-purple-300">
                                    {{ $grandTotalPdbk }}
                                </td>
                                <td class="px-4 py-3.5 text-slate-300 font-normal text-xs">
                                    <span class="inline-flex items-center gap-2">
                                        <span>Rasio: <strong class="font-mono text-white">{{ $malePercent }}% L</strong> / <strong class="font-mono text-white">{{ $femalePercent }}% P</strong></span>
                                        <span>•</span>
                                        <span>Total Rombel: <strong class="font-mono text-white">{{ $grandTotalClassrooms }}</strong></span>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- ========================================================================= -->
        <!-- TAB 2: MATRIKS PENDIDIK & ROMBEL (STRUKTUR LENGKAP SHEET 2 / REPORT TU) -->
        <!-- ========================================================================= -->
        <div x-show="activeReportTab === 'sheet_report'" class="space-y-4 lg:space-y-5" style="display: none;">
            
            <!-- INFO CARD KETENAGAAN -->
            <div class="p-4 rounded-xl bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200/80 dark:border-indigo-800/60 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold shrink-0 shadow-xs">
                        <i data-lucide="award" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-indigo-950 dark:text-indigo-100">
                            Matriks Distribusi Pendidik & Tenaga Kependidikan per Rombel
                        </h3>
                        <p class="text-xs text-indigo-700/80 dark:text-indigo-300/80 mt-0.5">
                            Menampilkan alokasi Wali Kelas, Guru Kelas, Guru Pendamping Khusus (GPK), Guru Al-Qur'an (GPQ), serta Koordinator Tingkat sesuai format resmi Sheet REPORT Tata Usaha.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-xs px-3 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-indigo-200 dark:border-indigo-800 font-bold font-mono text-indigo-700 dark:text-indigo-300 shadow-xs">
                        6 Level • 24 Rombel
                    </span>
                </div>
            </div>

            <!-- SHEET REPORT TABLE (11 KOLOM LENGKAP) -->
            <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs overflow-hidden w-full">
                <!-- Table Header Info Bar -->
                <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-900/50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <i data-lucide="users-2" class="w-4 h-4 text-indigo-600 dark:text-indigo-400"></i>
                        <h2 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-slate-100">
                            Data Peserta Didik & Formasi Guru {{ function_exists('setting') ? setting('unit_name', 'SD ANAK SALEH') : 'SD ANAK SALEH' }}
                        </h2>
                    </div>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">
                        Tapel {{ $selectedYear ? $selectedYear->name : '-' }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-900 text-white font-bold">
                                <th class="px-2.5 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider w-10">No</th>
                                <th class="px-3 py-2.5 text-left text-[10px] font-bold uppercase tracking-wider min-w-[120px]">Nama Kelas</th>
                                <th class="px-2.5 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider w-16">Kode</th>
                                <th class="px-2.5 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider w-14 text-blue-300">Laki-Laki</th>
                                <th class="px-2.5 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider w-14 text-pink-300">Perempuan</th>
                                <th class="px-2.5 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider w-16 text-emerald-300">Jml Siswa</th>
                                <th class="px-2.5 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider w-16 text-purple-300">Inklusi</th>
                                <th class="px-3 py-2.5 text-left text-[10px] font-bold uppercase tracking-wider min-w-[170px] text-indigo-300">Wali Kelas</th>
                                <th class="px-3 py-2.5 text-left text-[10px] font-bold uppercase tracking-wider min-w-[160px] text-teal-300">Guru Kelas</th>
                                <th class="px-3 py-2.5 text-left text-[10px] font-bold uppercase tracking-wider min-w-[200px] text-amber-300">GPK (Inklusi)</th>
                                <th class="px-3 py-2.5 text-left text-[10px] font-bold uppercase tracking-wider min-w-[160px] text-rose-300">GPQ (Al-Qur'an)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                            @php $sheetRowCounter = 1; @endphp
                            @foreach($levelReports as $lvl)
                                <!-- LEVEL SECTION WITH COORDINATOR HEADER -->
                                <tr class="bg-indigo-50/70 dark:bg-indigo-950/40 border-t-2 border-b border-indigo-200 dark:border-indigo-800">
                                    <td colspan="11" class="px-3.5 py-2.5">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2.5 h-2.5 rounded-full bg-indigo-600 dark:bg-indigo-400"></span>
                                                <span class="font-extrabold text-xs uppercase tracking-wide text-indigo-950 dark:text-indigo-100">
                                                    {{ $lvl['level']->name }}
                                                </span>
                                                <span class="text-[10px] px-2 py-0.5 rounded bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 font-bold font-mono">
                                                    {{ count($lvl['classrooms']) }} Rombel
                                                </span>
                                            </div>

                                            <!-- Koordinator & Pendamping Jenjang Badge -->
                                            <div class="flex flex-wrap items-center gap-2 text-xs">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="text-slate-500 dark:text-slate-400 font-semibold text-[11px]">Koordinator:</span>
                                                    <span class="px-2.5 py-0.5 rounded-full bg-white dark:bg-slate-900 border border-indigo-200 dark:border-indigo-800 text-indigo-800 dark:text-indigo-200 font-bold text-xs shadow-xs">
                                                        {{ $lvl['coordinator'] ?? 'Koordinator ' . $lvl['level']->name }}
                                                    </span>
                                                </div>
                                                @if(!empty($lvl['assistants']))
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="text-slate-500 dark:text-slate-400 font-semibold text-[11px]">Pendamping:</span>
                                                        @foreach($lvl['assistants'] as $ast)
                                                            <span class="px-2 py-0.5 rounded-full bg-indigo-100/70 dark:bg-indigo-900/60 border border-indigo-200 dark:border-indigo-800 text-indigo-700 dark:text-indigo-300 font-medium text-[11px]">
                                                                {{ $ast }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <!-- CLASSROOM ROWS -->
                                @foreach($lvl['classrooms'] as $cr)
                                    <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition-colors group">
                                        <td class="px-2.5 py-2.5 text-center text-slate-400 font-mono text-[11px]">
                                            {{ $sheetRowCounter++ }}
                                        </td>
                                        <td class="px-3 py-2.5 font-bold text-slate-900 dark:text-slate-100 text-xs">
                                            <span>{{ strtoupper($cr['name']) }}</span>
                                        </td>
                                        <td class="px-2.5 py-2.5 text-center font-mono font-bold text-xs text-indigo-600 dark:text-indigo-400">
                                            <span class="px-2 py-0.5 rounded bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-100 dark:border-indigo-900/50">
                                                {{ $cr['code'] }}
                                            </span>
                                        </td>
                                        <td class="px-2.5 py-2.5 text-center font-mono font-bold text-blue-600 dark:text-blue-400">
                                            {{ $cr['male'] }}
                                        </td>
                                        <td class="px-2.5 py-2.5 text-center font-mono font-bold text-pink-600 dark:text-pink-400">
                                            {{ $cr['female'] }}
                                        </td>
                                        <td class="px-2.5 py-2.5 text-center font-mono font-bold text-slate-900 dark:text-slate-100 bg-slate-50/50 dark:bg-slate-800/20">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold {{ $cr['total'] > 0 ? 'bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200' : 'text-slate-400' }}">
                                                {{ $cr['total'] }}
                                            </span>
                                        </td>
                                        <td class="px-2.5 py-2.5 text-center font-mono font-bold">
                                            @if($cr['pdbk'] > 0)
                                                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                                    {{ $cr['pdbk'] }}
                                                </span>
                                            @else
                                                <span class="text-slate-300 dark:text-slate-600 font-normal">0</span>
                                            @endif
                                        </td>
                                        <!-- Wali Kelas -->
                                        <td class="px-3 py-2.5">
                                            @if($cr['homeroom_teacher'] && $cr['homeroom_teacher'] !== '-')
                                                <div class="flex items-center gap-1.5 font-semibold text-slate-800 dark:text-slate-200">
                                                    <div class="w-5 h-5 rounded-full bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-[10px] shrink-0 font-bold">
                                                        <i data-lucide="user-check" class="w-3 h-3"></i>
                                                    </div>
                                                    <span>{{ $cr['homeroom_teacher'] }}</span>
                                                </div>
                                            @else
                                                <span class="text-slate-400 italic text-[11px]">-</span>
                                            @endif
                                        </td>
                                        <!-- Guru Kelas -->
                                        <td class="px-3 py-2.5">
                                            @if(!empty($cr['class_teacher']) && $cr['class_teacher'] !== '-')
                                                <div class="flex items-center gap-1.5 text-teal-800 dark:text-teal-300 font-medium">
                                                    <div class="w-5 h-5 rounded-full bg-teal-50 dark:bg-teal-950 text-teal-600 dark:text-teal-400 flex items-center justify-center text-[10px] shrink-0 font-bold">
                                                        <i data-lucide="user" class="w-3 h-3"></i>
                                                    </div>
                                                    <span>{{ $cr['class_teacher'] }}</span>
                                                </div>
                                            @else
                                                <span class="text-slate-300 dark:text-slate-600">-</span>
                                            @endif
                                        </td>
                                        <!-- GPK -->
                                        <td class="px-3 py-2.5">
                                            @if(!empty($cr['gpk_teachers']))
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($cr['gpk_teachers'] as $gpk)
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-medium bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60">
                                                            <i data-lucide="heart-handshake" class="w-2.5 h-2.5 text-amber-600 dark:text-amber-400 shrink-0"></i>
                                                            <span>{{ $gpk }}</span>
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-slate-300 dark:text-slate-600">-</span>
                                            @endif
                                        </td>
                                        <!-- GPQ -->
                                        <td class="px-3 py-2.5">
                                            @if(!empty($cr['gpq']) && $cr['gpq'] !== '-')
                                                <div class="flex items-center gap-1.5 text-rose-800 dark:text-rose-300 font-medium">
                                                    <div class="w-5 h-5 rounded-full bg-rose-50 dark:bg-rose-950 text-rose-600 dark:text-rose-400 flex items-center justify-center text-[10px] shrink-0 font-bold">
                                                        <i data-lucide="book-open" class="w-3 h-3"></i>
                                                    </div>
                                                    <span>{{ $cr['gpq'] }}</span>
                                                </div>
                                            @else
                                                <span class="text-slate-300 dark:text-slate-600">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                                <!-- SUBTOTAL LEVEL ROW -->
                                <tr class="bg-indigo-50/50 dark:bg-indigo-950/30 font-bold border-t border-indigo-100 dark:border-indigo-900/40 text-slate-800 dark:text-slate-200">
                                    <td colspan="3" class="px-3 py-2 text-right uppercase tracking-wider text-[11px] text-indigo-900 dark:text-indigo-200">
                                        Jml Siswa @ {{ $lvl['level']->name }}:
                                    </td>
                                    <td class="px-2.5 py-2 text-center font-mono text-blue-600 dark:text-blue-400">
                                        {{ $lvl['subtotal_male'] }}
                                    </td>
                                    <td class="px-2.5 py-2 text-center font-mono text-pink-600 dark:text-pink-400">
                                        {{ $lvl['subtotal_female'] }}
                                    </td>
                                    <td class="px-2.5 py-2 text-center font-mono text-indigo-700 dark:text-indigo-300 bg-indigo-100/50 dark:bg-indigo-900/40">
                                        {{ $lvl['subtotal_students'] }}
                                    </td>
                                    <td class="px-2.5 py-2 text-center font-mono text-purple-700 dark:text-purple-300">
                                        {{ $lvl['subtotal_pdbk'] }}
                                    </td>
                                    <td colspan="4" class="px-3 py-2 text-slate-500 dark:text-slate-400 font-normal italic text-[10px]">
                                        Total {{ count($lvl['classrooms']) }} rombel pada jenjang ini
                                    </td>
                                </tr>
                            @endforeach

                            <!-- GRAND TOTAL FOOTER -->
                            <tr class="bg-slate-900 text-white font-bold border-t-2 border-indigo-500 shadow-inner">
                                <td colspan="3" class="px-4 py-3.5 text-right uppercase tracking-wider text-xs font-bold text-slate-200">
                                    JUMLAH PESERTA DIDIK KESELURUHAN:
                                </td>
                                <td class="px-2.5 py-3.5 text-center font-mono text-sm text-blue-300">
                                    {{ $grandTotalMale }}
                                </td>
                                <td class="px-2.5 py-3.5 text-center font-mono text-sm text-pink-300">
                                    {{ $grandTotalFemale }}
                                </td>
                                <td class="px-2.5 py-3.5 text-center font-mono text-base text-white bg-indigo-700/80">
                                    {{ $grandTotalStudents }}
                                </td>
                                <td class="px-2.5 py-3.5 text-center font-mono text-sm text-purple-300">
                                    {{ $grandTotalPdbk }}
                                </td>
                                <td colspan="4" class="px-4 py-3.5 text-slate-300 font-normal text-xs">
                                    <span class="inline-flex items-center gap-2">
                                        <span>Rasio: <strong class="font-mono text-white">{{ $malePercent }}% L</strong> / <strong class="font-mono text-white">{{ $femalePercent }}% P</strong></span>
                                        <span>•</span>
                                        <span>Total Rombel: <strong class="font-mono text-white">{{ $grandTotalClassrooms }}</strong></span>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

    </div>
</x-admin-layout>
