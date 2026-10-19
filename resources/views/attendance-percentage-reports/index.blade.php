<x-admin-layout>
    <style>
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.3); border-radius: 9999px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(148, 163, 184, 0.5); }
    </style>

    <div class="p-3.5 sm:p-5 lg:p-6 space-y-4 sm:space-y-5 lg:space-y-6 relative text-left" x-data="{
        activeCategoryFilter: 'all',
        selectedReport: null,
        isDrawerOpen: false,
        openDrawer(report) {
            this.selectedReport = report;
            this.isDrawerOpen = true;
        },
        closeDrawer() {
            this.isDrawerOpen = false;
        }
    }" x-ref="container">
        
        <!-- HEADER -->
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-base sm:text-lg font-bold tracking-tight text-slate-900 dark:text-slate-50">Persentase Kehadiran</h2>
                <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400">Evaluasi performa disiplin kehadiran pegawai berdasarkan data absensi terverifikasi HRD.</p>
            </div>

            <!-- Refresh Button -->
            <div class="flex items-center gap-2 self-stretch md:self-auto justify-end">
                <a href="{{ route('attendance-percentage-reports.index', array_merge(request()->query(), ['refresh' => 1])) }}" 
                   class="h-9 px-3.5 inline-flex items-center justify-center gap-1.5 text-xs font-semibold rounded-lg bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 shadow-2xs transition-all cursor-pointer"
                   title="Sinkronkan data terbaru dari server HRD">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400"></i>
                    <span>Sinkron Data</span>
                </a>
            </div>
        </header>

        <div id="attendance-report-container" class="space-y-5">

            <!-- DUAL SUMMARY BANNERS: KEHADIRAN PER JABATAN & PEMETAAN KETERLAMBATAN -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 w-full text-left">
                <!-- BANNER 1: RATA-RATA KEHADIRAN (KESELURUHAN & PER JABATAN) -->
                @php
                    $unitAvg = $unitAveragePercentage ?? ($summary['unit_average_percentage'] ?? ($reports->isNotEmpty() ? round($reports->avg('percentage'), 1) : 0));
                    if ($unitAvg >= 95) {
                        $avgColorClass = 'text-emerald-600 dark:text-emerald-400';
                        $avgBarClass = 'bg-gradient-to-r from-emerald-500 to-teal-400';
                        $avgBadge = 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border-emerald-200/40';
                        $avgStatusText = 'Zona Aman (Prima)';
                    } elseif ($unitAvg >= 90) {
                        $avgColorClass = 'text-amber-500 dark:text-amber-400';
                        $avgBarClass = 'bg-gradient-to-r from-amber-400 to-amber-500';
                        $avgBadge = 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border-amber-200/40';
                        $avgStatusText = 'Zona Waspada';
                    } else {
                        $avgColorClass = 'text-rose-600 dark:text-rose-400';
                        $avgBarClass = 'bg-gradient-to-r from-rose-500 to-red-600';
                        $avgBadge = 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border-rose-200/40';
                        $avgStatusText = 'Zona Kritis';
                    }
                @endphp
                <section class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-xl p-4 sm:p-5 flex flex-col justify-between gap-3 shadow-sm">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800/60">
                        <div class="flex items-center gap-2.5">
                            <div class="p-2 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-650 dark:text-indigo-400 rounded-lg shrink-0">
                                <i data-lucide="percent" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-100 leading-tight">Rata-rata Kehadiran</h4>
                                <p class="text-[10px] text-slate-400 dark:text-slate-500">Persentase kehadiran pegawai keseluruhan & per jabatan</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold border {{ $avgBadge }}">
                            {{ $avgStatusText }}
                        </span>
                    </div>

                    <!-- Highlight: Persentase Kehadiran Keseluruhan -->
                    <div class="p-2.5 rounded-xl bg-slate-50/80 dark:bg-slate-950/60 border border-slate-100 dark:border-slate-800/80 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl sm:text-3xl font-black font-mono {{ $avgColorClass }} tracking-tight">{{ $unitAvg }}%</span>
                            <div class="space-y-0.5">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block leading-tight">Kehadiran Keseluruhan</span>
                                <span class="text-[10px] text-slate-400 dark:text-slate-500 block leading-tight">Total {{ $countTotal }} pegawai aktif (target &ge;95%)</span>
                            </div>
                        </div>
                        <div class="hidden sm:block w-36 space-y-1">
                            <div class="h-2 w-full bg-slate-200/70 dark:bg-slate-800 rounded-full overflow-hidden">
                                <div class="h-full {{ $avgBarClass }} rounded-full transition-all duration-500" style="width: {{ min(100, max(0, $unitAvg)) }}%"></div>
                            </div>
                            <div class="flex justify-between text-[8px] text-slate-400 font-mono">
                                <span>0%</span>
                                <span>Target 95%</span>
                                <span>100%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Sub-Header: Rincian per Jabatan -->
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300">Rincian per Bidang / Jabatan:</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-500 font-mono">{{ $positionStats->count() }} Jabatan</span>
                    </div>

                    <!-- Scrollable Position List -->
                    <div class="space-y-1.5 max-h-[110px] overflow-y-auto pr-1.5 custom-scrollbar">
                        @forelse($positionStats as $pos)
                            @php
                                $pct = $pos['avg_percentage'];
                                if ($pct >= 95) {
                                    $barGradient = 'bg-gradient-to-r from-emerald-500 to-teal-400';
                                    $textColor = 'text-emerald-600 dark:text-emerald-400';
                                    $badgeStyle = 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border-emerald-200/40';
                                    $badgeLabel = 'Aman';
                                } elseif ($pct >= 90) {
                                    $barGradient = 'bg-gradient-to-r from-amber-400 to-amber-500';
                                    $textColor = 'text-amber-500 dark:text-amber-400';
                                    $badgeStyle = 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border-amber-200/40';
                                    $badgeLabel = 'Waspada';
                                } else {
                                    $barGradient = 'bg-gradient-to-r from-rose-500 to-red-600';
                                    $textColor = 'text-rose-600 dark:text-rose-400';
                                    $badgeStyle = 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border-rose-200/40';
                                    $badgeLabel = 'Kritis';
                                }
                            @endphp
                            <div class="p-1.5 px-2 rounded-lg bg-slate-50/70 dark:bg-slate-950/50 border border-slate-100 dark:border-slate-800/80 hover:bg-slate-50 dark:hover:bg-slate-950 transition-colors">
                                <div class="flex items-center justify-between gap-2 mb-1">
                                    <div class="min-w-0 flex items-center gap-1.5">
                                        <span class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate">{{ $pos['position'] }}</span>
                                        <span class="text-[10px] text-slate-400 dark:text-slate-500 shrink-0 font-medium">({{ $pos['staff_count'] }} pegawai)</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <span class="text-xs font-black font-mono {{ $textColor }}">{{ $pct }}%</span>
                                        <span class="text-[8px] font-bold px-1.5 py-0.2 rounded border {{ $badgeStyle }} uppercase tracking-wider">{{ $badgeLabel }}</span>
                                    </div>
                                </div>
                                <div class="h-1 w-full bg-slate-200/70 dark:bg-slate-800 rounded-full overflow-hidden">
                                    <div class="h-full {{ $barGradient }} rounded-full transition-all duration-300" style="width: {{ min(100, max(0, $pct)) }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-3 text-xs text-slate-400">Tidak ada data jabatan.</div>
                        @endforelse
                    </div>

                    <!-- Mini KPI Status Breakdown -->
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800/80">
                        <div class="flex items-center justify-between mb-1.5 text-[10px] text-slate-400">
                            <span>Akumulasi Presensi Periode Ini:</span>
                            <span class="font-mono font-medium">{{ number_format($summary['total_effective_work_days'] ?? $reports->sum('effective_work_days'), 0, ',', '.') }} total hari kerja</span>
                        </div>
                        <div class="grid grid-cols-3 sm:grid-cols-6 gap-1.5 text-center font-mono">
                            <div class="p-1.5 rounded-lg bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-850">
                                <span class="block text-[8.5px] text-slate-400 font-sans uppercase font-bold">Pegawai</span>
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-100">{{ $countTotal }} <span class="text-[9px] font-normal font-sans text-slate-400">org</span></span>
                            </div>
                            <div class="p-1.5 rounded-lg bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-100/60 dark:border-emerald-900/30">
                                <span class="block text-[8.5px] text-emerald-600 dark:text-emerald-400 font-sans uppercase font-bold">Hadir</span>
                                <span class="text-xs font-bold text-emerald-700 dark:text-emerald-300">{{ number_format($summary['total_present'] ?? $reports->sum('total_present'), 0, ',', '.') }} <span class="text-[9px] font-normal font-sans text-emerald-600/70">hari</span></span>
                            </div>
                            <div class="p-1.5 rounded-lg bg-amber-50/60 dark:bg-amber-950/20 border border-amber-100/60 dark:border-amber-900/30">
                                <span class="block text-[8.5px] text-amber-600 dark:text-amber-400 font-sans uppercase font-bold">Izin</span>
                                <span class="text-xs font-bold text-amber-700 dark:text-amber-300">{{ number_format($summary['total_izin'] ?? $reports->sum('total_izin'), 0, ',', '.') }} <span class="text-[9px] font-normal font-sans text-amber-600/70">hari</span></span>
                            </div>
                            <div class="p-1.5 rounded-lg bg-rose-50/60 dark:bg-rose-950/20 border border-rose-100/60 dark:border-rose-900/30">
                                <span class="block text-[8.5px] text-rose-600 dark:text-rose-400 font-sans uppercase font-bold">Sakit</span>
                                <span class="text-xs font-bold text-rose-700 dark:text-rose-300">{{ number_format($summary['total_sakit'] ?? $reports->sum('total_sakit'), 0, ',', '.') }} <span class="text-[9px] font-normal font-sans text-rose-600/70">hari</span></span>
                            </div>
                            <div class="p-1.5 rounded-lg bg-blue-50/60 dark:bg-blue-950/20 border border-blue-100/60 dark:border-blue-900/30">
                                <span class="block text-[8.5px] text-blue-600 dark:text-blue-400 font-sans uppercase font-bold">Cuti</span>
                                <span class="text-xs font-bold text-blue-700 dark:text-blue-300">{{ number_format($summary['total_cuti'] ?? $reports->sum('total_cuti'), 0, ',', '.') }} <span class="text-[9px] font-normal font-sans text-blue-600/70">hari</span></span>
                            </div>
                            <div class="p-1.5 rounded-lg bg-red-50/60 dark:bg-red-950/20 border border-red-100/60 dark:border-red-900/30">
                                <span class="block text-[8.5px] text-red-600 dark:text-red-400 font-sans uppercase font-bold">Alpa</span>
                                <span class="text-xs font-bold text-red-700 dark:text-red-300">{{ number_format($summary['total_absent'] ?? $reports->sum('total_absent'), 0, ',', '.') }} <span class="text-[9px] font-normal font-sans text-red-600/70">hari</span></span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- BANNER 2: PEMETAAN KETERLAMBATAN -->
                @php
                    $lateEvents = $lateAnalysis['total_late_events'] ?? 0;
                    $lateStaffCount = $lateAnalysis['late_staff_count'] ?? 0;
                    if ($lateEvents === 0) {
                        $statusBadge = 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border-emerald-200/40';
                        $statusText = 'Semua Pegawai Tepat Waktu';
                    } elseif ($lateEvents > 50 || ($lateAnalysis['dur_over_30'] ?? 0) > 10) {
                        $statusBadge = 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border-rose-200/40';
                        $statusText = 'Tingkat Keterlambatan Tinggi';
                    } else {
                        $statusBadge = 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border-amber-200/40';
                        $statusText = 'Perlu Evaluasi Kedisiplinan';
                    }
                @endphp
                <section class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-xl p-4 sm:p-5 flex flex-col justify-between gap-3 shadow-sm">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800/60">
                        <div class="flex items-center gap-2.5">
                            <div class="p-2 bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 rounded-lg shrink-0">
                                <i data-lucide="clock" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-100 leading-tight">Pemetaan Keterlambatan</h4>
                                <p class="text-[10px] text-slate-400 dark:text-slate-500">Distribusi durasi dan monitoring kedisiplinan jam masuk</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold border {{ $statusBadge }}">
                            {{ $statusText }}
                        </span>
                    </div>

                    <!-- Highlight: Total Durasi & Frekuensi Keterlambatan -->
                    <div class="p-2.5 rounded-xl bg-slate-50/80 dark:bg-slate-950/60 border border-slate-100 dark:border-slate-800/80 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl sm:text-3xl font-black font-mono text-amber-600 dark:text-amber-400 tracking-tight">{{ number_format($lateAnalysis['total_late_minutes'] ?? 0, 0, ',', '.') }}</span>
                            <div class="space-y-0.5">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block leading-tight">Total Menit Terlambat</span>
                                <span class="text-[10px] text-slate-400 dark:text-slate-500 block leading-tight">Dari {{ $lateEvents }}x kejadian (rata-rata ~{{ $countTotal > 0 ? number_format($lateEvents / $countTotal, 1) : 0 }}x / pegawai)</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] text-slate-400 block font-medium">Rata-rata Durasi:</span>
                            <span class="text-xs font-bold font-mono text-slate-800 dark:text-slate-200">{{ $lateAnalysis['avg_late_minutes'] ?? 0 }} mnt / kejadian</span>
                        </div>
                    </div>

                    <!-- Sub-Header: Distribusi Durasi -->
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300">Rincian Durasi Keterlambatan:</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-500 font-mono">3 Kategori</span>
                    </div>

                    <!-- 3 Kategori Durasi Keterlambatan (Jelas & Mudah Dipahami) -->
                    <div class="grid grid-cols-3 gap-2">
                        <!-- 1 - 15 Menit (Ringan) -->
                        <div class="p-2 rounded-xl bg-amber-50/40 dark:bg-amber-950/20 border border-amber-200/50 dark:border-amber-900/30 flex flex-col justify-between text-left">
                            <div>
                                <div class="flex items-center justify-between mb-0.5">
                                    <span class="text-[10px] font-bold text-amber-800 dark:text-amber-300">1 - 15 Mnt</span>
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                </div>
                                <span class="text-[8.5px] text-slate-500 dark:text-slate-400 block leading-tight">Terlambat Ringan</span>
                            </div>
                            <div class="mt-1.5 text-left">
                                <span class="text-base sm:text-lg font-black font-mono text-amber-600 dark:text-amber-400">{{ $lateAnalysis['dur_1_to_15'] ?? 0 }}</span>
                                <span class="text-[9px] text-slate-400 font-sans ml-0.5">kali</span>
                                @if($countTotal > 0)
                                    <span class="block text-[8.5px] text-amber-700/80 dark:text-amber-300/80 font-mono mt-0.5">~{{ number_format(($lateAnalysis['dur_1_to_15'] ?? 0) / $countTotal, 1) }}x / pegawai</span>
                                @endif
                            </div>
                        </div>

                        <!-- 16 - 30 Menit (Sedang) -->
                        <div class="p-2 rounded-xl bg-orange-50/40 dark:bg-orange-950/20 border border-orange-200/50 dark:border-orange-900/30 flex flex-col justify-between text-left">
                            <div>
                                <div class="flex items-center justify-between mb-0.5">
                                    <span class="text-[10px] font-bold text-orange-800 dark:text-orange-300">16 - 30 Mnt</span>
                                    <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                                </div>
                                <span class="text-[8.5px] text-slate-500 dark:text-slate-400 block leading-tight">Terlambat Sedang</span>
                            </div>
                            <div class="mt-1.5 text-left">
                                <span class="text-base sm:text-lg font-black font-mono text-orange-600 dark:text-orange-400">{{ $lateAnalysis['dur_16_to_30'] ?? 0 }}</span>
                                <span class="text-[9px] text-slate-400 font-sans ml-0.5">kali</span>
                                @if($countTotal > 0)
                                    <span class="block text-[8.5px] text-orange-700/80 dark:text-orange-300/80 font-mono mt-0.5">~{{ number_format(($lateAnalysis['dur_16_to_30'] ?? 0) / $countTotal, 1) }}x / pegawai</span>
                                @endif
                            </div>
                        </div>

                        <!-- > 30 Menit (Berat) -->
                        <div class="p-2 rounded-xl bg-rose-50/40 dark:bg-rose-955/20 border border-rose-200/50 dark:border-rose-900/30 flex flex-col justify-between text-left">
                            <div>
                                <div class="flex items-center justify-between mb-0.5">
                                    <span class="text-[10px] font-bold text-rose-800 dark:text-rose-300">&gt; 30 Mnt</span>
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                </div>
                                <span class="text-[8.5px] text-slate-500 dark:text-slate-400 block leading-tight">Terlambat Berat</span>
                            </div>
                            <div class="mt-1.5 text-left">
                                <span class="text-base sm:text-lg font-black font-mono text-rose-600 dark:text-rose-450">{{ $lateAnalysis['dur_over_30'] ?? 0 }}</span>
                                <span class="text-[9px] text-slate-400 font-sans ml-0.5">kali</span>
                                @if($countTotal > 0)
                                    <span class="block text-[8.5px] text-rose-700/80 dark:text-rose-300/80 font-mono mt-0.5">~{{ number_format(($lateAnalysis['dur_over_30'] ?? 0) / $countTotal, 1) }}x / pegawai</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Ringkasan Pegawai Terlambat vs Tepat Waktu -->
                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100 dark:border-slate-800/80 text-[11px]">
                        <div class="flex flex-col gap-0.5 text-left">
                            <span class="text-[9.5px] text-slate-400 dark:text-slate-500 font-medium">Pegawai Terlambat:</span>
                            <span class="font-bold text-rose-600 dark:text-rose-400 font-mono text-xs">
                                {{ $lateAnalysis['late_staff_count'] ?? 0 }} orang <span class="font-normal font-sans text-slate-400 text-[10px]">(pernah terlambat)</span>
                            </span>
                        </div>
                        <div class="flex flex-col gap-0.5 text-right">
                            <span class="text-[9.5px] text-slate-400 dark:text-slate-500 font-medium">Pegawai Tepat Waktu:</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400 font-mono text-xs">
                                {{ $lateAnalysis['on_time_staff_count'] ?? 0 }} orang <span class="font-normal font-sans text-slate-400 text-[10px]">(selalu tepat waktu)</span>
                            </span>
                        </div>
                    </div>
                </section>
            </div>

            <!-- ULTRA-COMPACT KPI FILTER STRIP -->
            <section class="flex flex-wrap items-center gap-2 sm:gap-2.5 w-full text-xs">
                <!-- Semua Pegawai -->
                <button type="button" @click="activeCategoryFilter = 'all'" 
                    :class="activeCategoryFilter === 'all' ? 'bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900 shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/60'"
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all cursor-pointer">
                    <i data-lucide="users" class="w-3.5 h-3.5 opacity-80 shrink-0"></i>
                    <span class="text-[11px] font-medium">Semua Pegawai:</span>
                    <span class="font-bold font-mono">{{ $countTotal }}</span>
                </button>

                <!-- Zona Hijau (Aman) -->
                <button type="button" @click="activeCategoryFilter = 'green'" 
                    :class="activeCategoryFilter === 'green' ? 'bg-emerald-600 text-white ring-2 ring-emerald-500/20' : 'bg-emerald-50/70 dark:bg-emerald-950/30 text-emerald-800 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-900/40 hover:bg-emerald-100/70'"
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all cursor-pointer shadow-2xs">
                    <i data-lucide="check-circle" class="w-3.5 h-3.5 shrink-0" :class="activeCategoryFilter === 'green' ? 'text-white' : 'text-emerald-600 dark:text-emerald-400'"></i>
                    <span class="text-[11px] font-medium">Zona Aman (&ge;95%):</span>
                    <span class="font-bold font-mono">{{ $countGreen }}</span>
                </button>

                <!-- Zona Kuning (Waspada) -->
                <button type="button" @click="activeCategoryFilter = 'yellow'" 
                    :class="activeCategoryFilter === 'yellow' ? 'bg-amber-500 text-white ring-2 ring-amber-500/20' : 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-800 dark:text-amber-300 border border-amber-200/60 dark:border-amber-900/40 hover:bg-amber-100/70'"
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all cursor-pointer shadow-2xs">
                    <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0" :class="activeCategoryFilter === 'yellow' ? 'text-white' : 'text-amber-600 dark:text-amber-400'"></i>
                    <span class="text-[11px] font-medium">Zona Waspada (90-94%):</span>
                    <span class="font-bold font-mono">{{ $countYellow }}</span>
                </button>

                <!-- Zona Merah (Kritis) -->
                <button type="button" @click="activeCategoryFilter = 'red'" 
                    :class="activeCategoryFilter === 'red' ? 'bg-rose-600 text-white ring-2 ring-rose-500/20' : 'bg-rose-50/70 dark:bg-rose-955/30 text-rose-800 dark:text-rose-300 border border-rose-200/60 dark:border-rose-900/40 hover:bg-rose-100/70'"
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all cursor-pointer shadow-2xs">
                    <i data-lucide="alert-triangle" class="w-3.5 h-3.5 shrink-0" :class="activeCategoryFilter === 'red' ? 'text-white' : 'text-rose-600 dark:text-rose-400'"></i>
                    <span class="text-[11px] font-medium">Zona Kritis (&lt;90%):</span>
                    <span class="font-bold font-mono">{{ $countRed }}</span>
                </button>
            </section>

            <!-- MODERN FILTERS & SEARCH -->
            <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm p-4 w-full text-left">
                <form method="GET" action="{{ route('attendance-percentage-reports.index') }}" id="attendance-filter-form" data-no-loader="true">
                    
                    <div class="flex flex-col lg:flex-row items-stretch lg:items-end gap-3 w-full">
                        <!-- Search Input -->
                        <div class="flex-grow min-w-0">
                            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1.5">Cari Pegawai</label>
                            <div x-data="{ searchVal: '{{ request('search') }}' }" class="w-full flex items-center bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg overflow-hidden shadow-inner focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:border-indigo-500">
                                <div class="pl-3 text-slate-450 dark:text-slate-500">
                                    <i data-lucide="search" class="w-4 h-4"></i>
                                </div>
                                <input type="text" name="search" x-model="searchVal" x-ref="searchInput" placeholder="Cari nama atau NIP pegawai..."
                                    style="border: none !important; outline: none !important; box-shadow: none !important;"
                                    class="w-full h-10 px-2.5 text-xs bg-transparent text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-550 focus:ring-0">
                                
                                <button type="button" x-show="searchVal.trim() !== ''" @click="searchVal = ''; $refs.searchInput.focus();" class="h-10 px-2.5 text-slate-400 hover:text-slate-650 dark:hover:text-slate-250 transition-colors cursor-pointer bg-transparent border-0 flex items-center justify-center">
                                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Periode Bulan -->
                        <div class="w-full lg:w-44 shrink-0">
                            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1.5">Bulan Penggajian</label>
                            <input type="month" name="month" value="{{ $month }}" class="w-full text-xs h-10 px-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 cursor-pointer font-mono dark:[color-scheme:dark]">
                        </div>

                        <!-- Filter Jabatan (Multi-Select Checkbox) -->
                        @if(isset($positions) && count($positions) > 0)
                        <div class="w-full lg:w-56 shrink-0 relative" x-data="{
                            open: false,
                            selectedPositions: {{ json_encode($selectedPositions ?? []) }},
                            allPositions: {{ json_encode($positions ?? []) }},
                            togglePosition(pos) {
                                if (this.selectedPositions.includes(pos)) {
                                    this.selectedPositions = this.selectedPositions.filter(p => p !== pos);
                                } else {
                                    this.selectedPositions.push(pos);
                                }
                            },
                            selectAll() {
                                this.selectedPositions = [...this.allPositions];
                            },
                            clearAll() {
                                this.selectedPositions = [];
                            }
                        }" @click.outside="open = false">
                            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                                <span>Jabatan / Posisi</span>
                                <span x-show="selectedPositions.length > 0" class="text-indigo-600 dark:text-indigo-400 font-mono font-bold" x-text="selectedPositions.length + ' dipilih'"></span>
                            </label>
                            
                            <!-- Trigger Button -->
                            <button type="button" @click="open = !open" 
                                    class="w-full text-xs h-10 px-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 cursor-pointer flex items-center justify-between text-left shadow-2xs hover:border-slate-300 dark:hover:border-slate-700 transition-colors">
                                <span class="truncate pr-2 font-medium" x-text="selectedPositions.length === 0 ? 'Semua Jabatan' : (selectedPositions.length === 1 ? selectedPositions[0] : selectedPositions.length + ' Jabatan Terpilih')"></span>
                                <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 shrink-0 transition-transform duration-150" :class="open ? 'rotate-180' : ''"></i>
                            </button>

                            <!-- Hidden real inputs for form submission -->
                            <template x-for="pos in selectedPositions" :key="pos">
                                <input type="hidden" name="positions[]" :value="pos">
                            </template>

                            <!-- Dropdown Panel -->
                            <div x-show="open" x-transition.opacity.duration.150ms style="display: none;"
                                 class="absolute z-50 left-0 mt-1 w-72 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl p-2.5 space-y-2">
                                
                                <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800 text-[11px]">
                                    <span class="font-bold text-slate-700 dark:text-slate-300">Pilih Jabatan</span>
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="selectAll()" class="text-indigo-600 dark:text-indigo-400 hover:underline font-bold text-[10px] cursor-pointer border-0 bg-transparent">Pilih Semua</button>
                                        <span class="text-slate-300 dark:text-slate-700">&bull;</span>
                                        <button type="button" @click="clearAll()" class="text-slate-400 hover:text-rose-500 font-bold text-[10px] cursor-pointer border-0 bg-transparent">Reset</button>
                                    </div>
                                </div>

                                <div class="max-h-56 overflow-y-auto space-y-0.5 pr-1 custom-scrollbar">
                                    @foreach($positions as $pos)
                                        <label class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/60 cursor-pointer text-xs select-none transition-colors">
                                            <input type="checkbox" value="{{ $pos }}" 
                                                   :checked="selectedPositions.includes('{{ $pos }}')"
                                                   @change="togglePosition('{{ $pos }}')"
                                                   class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                            <span class="text-slate-700 dark:text-slate-200 font-medium truncate">{{ $pos }}</span>
                                        </label>
                                    @endforeach
                                </div>

                                <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                                    <button type="button" @click="open = false; triggerFilterForm()" class="h-7 px-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold transition-all cursor-pointer border-0">
                                        Terapkan
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2 justify-end w-full lg:w-auto lg:ml-auto pb-0.5">
                            <button type="submit" class="h-10 px-5 bg-slate-900 dark:bg-slate-100 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 font-semibold text-xs rounded-lg shadow-sm transition-colors cursor-pointer flex items-center gap-1.5">
                                <i data-lucide="filter" class="w-4 h-4"></i>
                                Terapkan
                            </button>

                            @if(request()->hasAny(['search', 'month', 'start_date', 'end_date', 'position', 'positions']) && count(request()->except('page')) > 0)
                                <a href="{{ route('attendance-percentage-reports.index') }}" class="inline-flex items-center justify-center h-10 px-4 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs rounded-lg shadow-sm transition-colors gap-1.5 reset-filter-btn" data-no-loader="true">
                                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                                    Reset
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </section>

            <!-- TABLE SECTION -->
            <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden w-full text-left">
                <!-- Table Header Legend -->
                <div class="px-6 py-3.5 border-b border-slate-200 dark:border-slate-800 bg-slate-500/5 dark:bg-slate-900/50 flex flex-wrap items-center justify-between gap-3 text-[11px] font-semibold text-slate-500">
                    <span class="font-bold text-slate-900 dark:text-slate-200 flex items-center gap-2">
                        <i data-lucide="table-properties" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                        Tabel Evaluasi Kehadiran Pegawai
                    </span>
                    
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/30 text-indigo-700 dark:text-indigo-400 border border-indigo-200/30 dark:border-indigo-900/30">
                        <i data-lucide="info" class="w-3.5 h-3.5 animate-bounce-slow"></i>
                        Klik baris pegawai untuk melihat rincian detail harian
                    </span>
                </div>

                <!-- SCROLLABLE TABLE CONTAINER -->
                <div class="overflow-x-auto max-h-[600px] overflow-y-auto block relative rounded-b-xl border-t border-slate-100 dark:border-slate-800/60 custom-scrollbar">
                    <table class="w-full text-xs">
                        <thead class="sticky top-0 z-10 border-b border-slate-200 dark:border-slate-800 shadow-xs">
                            <tr class="text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                                <th class="px-6 py-3.5 text-center w-12 bg-slate-50 dark:bg-[#0d0d10]">No</th>
                                <th class="px-6 py-3.5 text-left bg-slate-50 dark:bg-[#0d0d10]">Pegawai</th>
                                <th class="px-6 py-3.5 text-center bg-slate-50 dark:bg-[#0d0d10]">Jadwal Kerja</th>
                                <th class="px-6 py-3.5 text-left bg-slate-50 dark:bg-[#0d0d10]">Rincian Absensi &amp; Izin</th>
                                <th class="px-6 py-3.5 text-right w-52 bg-slate-50 dark:bg-[#0d0d10]">Evaluasi Persentase</th>
                                <th class="px-6 py-3.5 text-center w-28 bg-slate-50 dark:bg-[#0d0d10]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-slate-700 dark:text-slate-350 font-medium">
                            @forelse($reports as $index => $rep)
                                <tr data-report="{{ json_encode($rep) }}"
                                    @click="openDrawer(JSON.parse($el.getAttribute('data-report')))"
                                    x-show="activeCategoryFilter === 'all' || 
                                            (activeCategoryFilter === 'green' && {{ $rep['percentage'] }} >= 95) || 
                                            (activeCategoryFilter === 'yellow' && {{ $rep['percentage'] }} >= 90 && {{ $rep['percentage'] }} < 95) || 
                                            (activeCategoryFilter === 'red' && {{ $rep['percentage'] }} < 90)"
                                    class="hover:bg-slate-50/50 dark:hover:bg-slate-850/30 transition-all cursor-pointer group">
                                    
                                    <td class="px-6 py-4 text-center text-slate-400 dark:text-slate-650 font-mono">{{ $index + 1 }}</td>
                                    
                                    <td class="px-6 py-4 text-left">
                                        <div class="flex items-center gap-3">
                                            @if($rep['employee']['photo'] ?? null)
                                                @php
                                                    $photoPath = str_contains($rep['employee']['photo'], 'photos/') ? $rep['employee']['photo'] : 'photos/' . $rep['employee']['photo'];
                                                    $photoUrl = rtrim($rep['employee']['unit_url'], '/') . '/storage/' . $photoPath;
                                                @endphp
                                                <img src="{{ $photoUrl }}" class="w-10 h-10 rounded-full object-cover shrink-0 ring-2 ring-slate-100 dark:ring-slate-800 shadow-xs" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($rep['employee']['name']) }}&background=6366f1&color=fff';">
                                            @else
                                                <div class="w-10 h-10 rounded-full bg-indigo-50 dark:bg-indigo-950/30 flex items-center justify-center text-xs font-bold text-indigo-700 dark:text-indigo-400 shrink-0 border border-indigo-100/30 shadow-xs">
                                                    {{ strtoupper(substr($rep['employee']['name'] ?? 'P', 0, 2)) }}
                                                </div>
                                            @endif
                                            <div class="space-y-0.5">
                                                <span class="text-slate-900 dark:text-slate-50 font-bold tracking-tight block group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $rep['employee']['name'] }}</span>
                                                <div class="text-[10.5px] text-slate-500 dark:text-slate-400 font-medium">
                                                    {{ $rep['employee']['position'] ?? $rep['employee']['subject_position'] ?? 'Pegawai' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <div class="inline-flex flex-col items-center">
                                            <span class="text-xs font-bold text-slate-900 dark:text-slate-50 font-mono">{{ $rep['total_work_days'] }} Hari</span>
                                            <span class="text-[9px] text-slate-400 dark:text-slate-500">Kerja Wajib</span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-left">
                                        <div class="flex flex-wrap gap-1.5 items-center">
                                            @php
                                                $hasAbsenceData = $rep['total_present'] > 0 || $rep['total_sakit'] > 0 || $rep['total_izin'] > 0 || $rep['total_cuti'] > 0 || $rep['total_absent'] > 0;
                                            @endphp

                                            @if(!$hasAbsenceData || ($rep['total_present'] == $rep['total_work_days'] && $rep['total_absent'] == 0 && $rep['total_sakit'] == 0 && $rep['total_izin'] == 0 && $rep['total_cuti'] == 0))
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-100/50 dark:border-emerald-900/30">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Hadir Penuh ({{ $rep['total_present'] }} Hari)
                                                </span>
                                                @if(($rep['reward_count'] ?? 0) > 0)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-400 border border-indigo-200/30 dark:border-indigo-900/30" title="Reward Libur: {{ implode(', ', $rep['reward_dates']) }}">
                                                        Reward: {{ $rep['reward_count'] }}
                                                    </span>
                                                @endif
                                                @if(($rep['waspada_late_count'] ?? 0) > 0)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 border border-amber-200/30 dark:border-amber-900/30">
                                                        Terlambat: {{ $rep['waspada_late_count'] }}x
                                                    </span>
                                                @endif
                                                @if(($rep['kritis_late_count'] ?? 0) > 0)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-450 border border-rose-200/30 dark:border-rose-900/30">
                                                        Kritis: {{ $rep['kritis_late_count'] }}x
                                                    </span>
                                                @endif
                                            @else
                                                @if($rep['total_present'] > 0)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200/30 dark:border-emerald-900/30">
                                                        Hadir: {{ $rep['total_present'] }}
                                                    </span>
                                                @endif
                                                @if(($rep['reward_count'] ?? 0) > 0)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-400 border border-indigo-200/30 dark:border-indigo-900/30" title="Reward Libur: {{ implode(', ', $rep['reward_dates']) }}">
                                                        Reward: {{ $rep['reward_count'] }}
                                                    </span>
                                                @endif
                                                @if(($rep['waspada_late_count'] ?? 0) > 0)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 border border-amber-200/30 dark:border-amber-900/30">
                                                        Terlambat: {{ $rep['waspada_late_count'] }}x
                                                    </span>
                                                @endif
                                                @if(($rep['kritis_late_count'] ?? 0) > 0)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-450 border border-rose-200/30 dark:border-rose-900/30">
                                                        Kritis: {{ $rep['kritis_late_count'] }}x
                                                    </span>
                                                @endif
                                                @if($rep['total_sakit'] > 0)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-red-50 dark:bg-red-950/30 text-red-700 dark:text-red-400 border border-red-200/30 dark:border-red-900/30">
                                                        Sakit: {{ $rep['total_sakit'] }}
                                                    </span>
                                                @endif
                                                @if($rep['total_izin'] > 0)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 border border-amber-200/30 dark:border-amber-900/30">
                                                        Izin: {{ $rep['total_izin'] }}
                                                    </span>
                                                @endif
                                                @if($rep['total_cuti'] > 0)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-400 border border-blue-200/30 dark:border-blue-900/30">
                                                        Cuti: {{ $rep['total_cuti'] }}
                                                    </span>
                                                @endif
                                                @if($rep['total_absent'] > 0)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-black bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-450 border border-rose-200/30 dark:border-rose-900/30">
                                                        Alpa: {{ $rep['total_absent'] }}
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        @php
                                            $percent = $rep['percentage'];
                                            if ($percent >= 95) {
                                                $percentClass = 'text-emerald-600 dark:text-emerald-450';
                                                $barColorClass = 'bg-gradient-to-r from-emerald-400 to-emerald-600';
                                                $badgeTheme = 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border-emerald-200/20';
                                                $badgeText = 'Aman';
                                            } elseif ($percent >= 90) {
                                                $percentClass = 'text-amber-500 dark:text-amber-450';
                                                $barColorClass = 'bg-gradient-to-r from-amber-400 to-amber-500';
                                                $badgeTheme = 'bg-amber-50 dark:bg-amber-950/40 text-amber-750 dark:text-amber-400 border-amber-200/20';
                                                $badgeText = 'Pantauan';
                                            } else {
                                                $percentClass = 'text-rose-650 dark:text-rose-455';
                                                $barColorClass = 'bg-gradient-to-r from-rose-400 to-rose-600';
                                                $badgeTheme = 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-450 border-rose-200/20';
                                                $badgeText = 'Evaluasi';
                                            }
                                        @endphp
                                        <div class="inline-flex flex-col items-end gap-1.5 min-w-[130px]">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-extrabold uppercase border {{ $badgeTheme }}">
                                                    {{ $badgeText }}
                                                </span>
                                                <span class="text-sm font-black font-mono {{ $percentClass }} tracking-wide">{{ $percent }}%</span>
                                            </div>
                                            <div class="w-24 h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden shadow-inner">
                                                <div class="h-full {{ $barColorClass }} rounded-full transition-all duration-300" style="width: {{ min(100, max(0, $percent)) }}%"></div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-150/30 rounded-md group-hover:bg-indigo-600 group-hover:text-white dark:group-hover:bg-indigo-650 dark:group-hover:text-white transition-all duration-150">
                                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                            Detail
                                        </span>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center text-slate-500 dark:text-slate-400">
                                        <div class="flex flex-col items-center justify-center gap-2.5">
                                            <i data-lucide="database" class="w-8 h-8 text-slate-300 dark:text-slate-700"></i>
                                            <p class="font-medium text-xs">Tidak ada data pegawai untuk kriteria pencarian ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- SLIDE-OVER DRAWER (LACI DETAIL) -->
        <div x-cloak x-show="isDrawerOpen" @keydown.window.escape="closeDrawer()" class="fixed inset-0 z-[9999] overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
            <div class="absolute inset-0 overflow-hidden">
                <!-- Backdrop overlay -->
                <div x-show="isDrawerOpen" 
                     x-transition:enter="ease-out duration-150" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100" 
                     x-transition:leave="ease-in duration-100" 
                     x-transition:leave-start="opacity-100" 
                     x-transition:leave-end="opacity-0" 
                     @click="closeDrawer()"
                     class="fixed inset-0 transition-opacity z-[9999]" 
                     style="background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);" 
                     aria-hidden="true"></div>

                <!-- Content Panel -->
                <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex z-[9999]">
                    <div x-show="isDrawerOpen" 
                         x-transition:enter="transform transition ease-out duration-150" 
                         x-transition:enter-start="translate-x-full" 
                         x-transition:enter-end="translate-x-0" 
                         x-transition:leave="transform transition ease-in duration-100" 
                         x-transition:leave-start="translate-x-0" 
                         x-transition:leave-end="translate-x-full" 
                         class="w-screen max-w-md bg-white dark:bg-slate-900 shadow-2xl border-l border-slate-200 dark:border-slate-800 flex flex-col justify-between text-left">
                        
                        <!-- Header -->
                        <div class="px-6 py-5 bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 flex justify-between items-start">
                            <div class="flex items-center gap-3" x-show="selectedReport !== null">
                                <template x-if="selectedReport && selectedReport.employee.photo">
                                    <img :src="selectedReport.employee.photo.startsWith('photos/') 
                                                ? selectedReport.employee.unit_url.replace(/\/$/, '') + '/storage/' + selectedReport.employee.photo
                                                : selectedReport.employee.unit_url.replace(/\/$/, '') + '/storage/photos/' + selectedReport.employee.photo" 
                                         class="w-11 h-11 rounded-full object-cover shrink-0 ring-2 ring-slate-100 dark:ring-slate-800 shadow-sm">
                                </template>
                                <template x-if="!selectedReport || !selectedReport.employee.photo">
                                    <div class="w-11 h-11 rounded-full bg-indigo-50 dark:bg-indigo-950/30 flex items-center justify-center text-sm font-bold text-indigo-700 dark:text-indigo-400 shrink-0 border border-indigo-100/30 shadow-sm" x-text="selectedReport ? selectedReport.employee.name.substr(0,2).toUpperCase() : ''"></div>
                                </template>

                                <div class="space-y-0.5">
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-50 leading-tight" x-text="selectedReport ? selectedReport.employee.name : ''"></h3>
                                    <span class="text-[9px] text-slate-450 dark:text-slate-500 font-medium" x-text="selectedReport ? 'Jabatan: ' + (selectedReport.employee.position || selectedReport.employee.subject_position || 'Pegawai') : ''"></span>
                                </div>
                            </div>
                            <button @click="closeDrawer()" class="p-1 rounded-lg text-slate-450 hover:bg-slate-100 dark:hover:bg-slate-850 hover:text-slate-650 cursor-pointer">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>

                        <!-- Body (Timeline) -->
                        <div class="flex-1 overflow-y-auto px-6 py-5 space-y-4 custom-scrollbar">
                            <div class="grid grid-cols-3 gap-2 mb-4 bg-slate-100/90 dark:bg-slate-800/80 p-3.5 rounded-xl border border-slate-200 dark:border-slate-700/70 shadow-xs">
                                <div>
                                    <span class="text-[10px] text-slate-500 dark:text-slate-400 uppercase tracking-wider block font-bold">Persentase</span>
                                    <span class="text-xl font-mono font-black" :class="selectedReport && selectedReport.percentage >= 95 ? 'text-emerald-600 dark:text-emerald-400' : (selectedReport && selectedReport.percentage >= 90 ? 'text-amber-500 dark:text-amber-400' : 'text-rose-600 dark:text-rose-400')" x-text="selectedReport ? selectedReport.percentage + '%' : ''"></span>
                                </div>
                                <div class="text-center">
                                    <span class="text-[10px] text-slate-500 dark:text-slate-400 uppercase tracking-wider block font-bold">Keterlambatan</span>
                                    <span class="text-xs font-bold font-mono" :class="selectedReport && selectedReport.total_late_minutes > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-700 dark:text-slate-300'" x-text="selectedReport ? (selectedReport.total_late_minutes > 0 ? selectedReport.total_late_minutes + ' mnt (' + selectedReport.late_count + 'x)' : '0 mnt') : ''"></span>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] text-slate-500 dark:text-slate-400 uppercase tracking-wider block font-bold">Total Kehadiran</span>
                                    <span class="text-xs font-bold font-mono text-slate-800 dark:text-slate-100" x-text="selectedReport ? selectedReport.total_present + ' / ' + selectedReport.total_work_days + ' Hari' : ''"></span>
                                </div>
                            </div>

                            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2.5">Histori Harian Absensi</h4>
                            
                            <div class="relative border-l border-slate-200 dark:border-slate-800 ml-2.5 pl-5.5 space-y-5 py-2">
                                <template x-if="selectedReport">
                                    <template x-for="day in selectedReport.day_details">
                                        <div class="relative">
                                            <!-- Bullet marker on line -->
                                            <span class="absolute -left-7.5 top-1.5 h-3.5 w-3.5 rounded-full border-2 border-white dark:border-slate-900 flex items-center justify-center shrink-0 shadow-xs"
                                                  :class="{
                                                      'bg-emerald-500': day.color === 'emerald',
                                                      'bg-red-500': day.color === 'red',
                                                      'bg-amber-500': day.color === 'amber',
                                                      'bg-blue-500': day.color === 'blue',
                                                      'bg-indigo-500': day.color === 'indigo',
                                                      'bg-rose-500': day.color === 'rose',
                                                      'bg-slate-400': day.color === 'slate'
                                                  }"></span>
                                            
                                            <!-- Details Card -->
                                            <div class="p-3.5 rounded-lg border border-slate-200/60 dark:border-slate-800 bg-slate-50/20 dark:bg-slate-900/40 text-xs flex flex-col gap-2 shadow-2xs">
                                                <div class="flex justify-between items-start gap-2">
                                                    <span class="font-bold text-slate-900 dark:text-slate-50 text-xs" x-text="day.date"></span>
                                                    
                                                    <!-- Badge Status -->
                                                    <span x-text="day.label" 
                                                          :class="{
                                                              'bg-emerald-500/10 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400 border border-emerald-200/20': day.color === 'emerald',
                                                              'bg-red-500/10 text-red-700 dark:bg-red-950/30 dark:text-red-400 border border-red-200/20': day.color === 'red',
                                                              'bg-amber-500/10 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400 border border-amber-200/20': day.color === 'amber',
                                                              'bg-blue-500/10 text-blue-700 dark:bg-blue-950/30 dark:text-blue-400 border border-blue-200/20': day.color === 'blue',
                                                              'bg-indigo-500/10 text-indigo-700 dark:bg-indigo-950/30 dark:text-indigo-400 border border-indigo-200/20': day.color === 'indigo',
                                                              'bg-rose-500/10 text-rose-700 dark:bg-rose-950/30 dark:text-rose-400 border border-rose-200/20': day.color === 'rose',
                                                              'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400 border border-slate-200/30': day.color === 'slate'
                                                          }"
                                                          class="px-2.5 py-0.5 rounded text-[10px] font-black uppercase tracking-wider font-mono"></span>
                                                </div>

                                                <div class="space-y-1 text-[11px] text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-slate-800/60 pt-2 font-mono">
                                                    <!-- Jadwal -->
                                                    <div class="flex items-start gap-1">
                                                        <span class="text-slate-400 dark:text-slate-500 shrink-0 w-20">Jadwal</span>
                                                        <span class="text-slate-400 dark:text-slate-500">:</span>
                                                        <span class="text-slate-800 dark:text-slate-200 font-semibold" x-text="(day.shift_name && day.shift_name !== 'Non-Shift') ? day.shift_name + (day.shift_schedule ? ' (' + day.shift_schedule + ')' : '') : (day.status === 'Libur' ? 'Libur' : 'Non-Shift')"></span>
                                                    </div>

                                                    <!-- Masuk & Pulang -->
                                                    <template x-if="day.status === 'Hadir' || day.status === 'Reward Libur'">
                                                        <div class="flex items-start gap-1">
                                                            <span class="text-slate-400 dark:text-slate-500 shrink-0 w-20">Masuk</span>
                                                            <span class="text-slate-400 dark:text-slate-500">:</span>
                                                            <span class="text-slate-800 dark:text-slate-200 font-medium" x-text="(day.in_time || '-') + '  •  Pulang : ' + (day.out_time || '-')"></span>
                                                        </div>
                                                    </template>

                                                    <!-- Terlambat -->
                                                    <template x-if="day.status === 'Hadir'">
                                                        <div class="flex items-start gap-1">
                                                            <span class="text-slate-400 dark:text-slate-500 shrink-0 w-20">Terlambat</span>
                                                            <span class="text-slate-400 dark:text-slate-500">:</span>
                                                            <span :class="day.late_minutes > 0 ? 'text-amber-600 dark:text-amber-400 font-bold' : 'text-slate-500 dark:text-slate-400'" x-text="day.late_minutes > 0 ? day.late_minutes + ' mnt' : '-'"></span>
                                                        </div>
                                                    </template>

                                                    <!-- Keterangan -->
                                                    <template x-if="day.status !== 'Hadir' && day.notes">
                                                        <div class="flex items-start gap-1">
                                                            <span class="text-slate-400 dark:text-slate-500 shrink-0 w-20">Keterangan</span>
                                                            <span class="text-slate-400 dark:text-slate-500">:</span>
                                                            <span class="text-slate-700 dark:text-slate-300 font-medium" x-text="day.notes"></span>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </template>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950 border-t border-slate-200 dark:border-slate-800 text-right">
                            <button @click="closeDrawer()" class="h-9 px-4 bg-slate-900 dark:bg-slate-100 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">
                                Tutup Detail
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-admin-layout>

<!-- AJAX NAVIGATION & FILTER SCRIPT -->
<script>
    function triggerFilterForm(el) {
        const form = document.getElementById('attendance-filter-form');
        if (form) {
            form.requestSubmit();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('attendance-report-container');

        function loadTableContent(url) {
            if (container) {
                container.style.opacity = '0.5';
                container.style.pointerEvents = 'none';
            }
            
            if (typeof NProgress !== 'undefined') {
                NProgress.start();
            }

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.getElementById('attendance-report-container');
                
                if (newContent && container) {
                    container.innerHTML = newContent.innerHTML;
                    container.style.opacity = '1';
                    container.style.pointerEvents = 'auto';

                    // Reinitialize Lucide Icons
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }

                    // Sync URL in address bar without reload
                    window.history.pushState({}, '', url);
                } else {
                    window.location.href = url;
                }
            })
            .catch(err => {
                console.error('AJAX loading failed:', err);
                window.location.href = url;
            })
            .finally(() => {
                if (typeof NProgress !== 'undefined') {
                    NProgress.done();
                }
                const globalLoader = document.getElementById('global-loading-overlay');
                if (globalLoader) {
                    globalLoader.classList.add('hidden');
                }
            });
        }

        // Delegate submit event
        document.addEventListener('submit', function (e) {
            const form = e.target.closest('#attendance-filter-form');
            if (!form) return;

            e.preventDefault();
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            const action = form.getAttribute('action') || window.location.pathname;
            const url = new URL(action, window.location.origin);
            url.search = params.toString();

            loadTableContent(url);
        });

        // Delegate click on reset/filter links inside container
        document.addEventListener('click', function (e) {
            const link = e.target.closest('#attendance-report-container a');
            if (!link) return;

            const href = link.getAttribute('href');
            if (href && (href.startsWith(window.location.origin) || href.startsWith('/'))) {
                e.preventDefault();
                loadTableContent(href);
            }
        });
    });
</script>
