<x-admin-layout>
    <div class="p-3.5 sm:p-5 lg:p-6 space-y-4 sm:space-y-5 lg:space-y-6">

        <!-- GREETING / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-2 sm:gap-3">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-base sm:text-lg font-bold tracking-tight text-slate-900 dark:text-slate-50 font-nasalization">Dashboard</h2>
                @if($isAdmin)
                    <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 font-medium">Selamat datang kembali! Aktivitas sekolah terpantau kondusif.</p>
                @else
                    <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 font-medium">Selamat datang kembali, {{ auth()->user()->name }}! Pantau performa kehadiran harian Anda disini.</p>
                @endif
            </div>
        </section>

        <!-- STAT CARDS GRID -->
        @if($isAdmin)
            <section class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-3 sm:gap-3.5 lg:gap-4">
                <!-- Admin Card 1: Total Siswa -->
                <div onclick="window.location='{{ route('students.index') }}'" class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 lg:p-4 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700 cursor-pointer">
                    <div class="flex justify-between items-start">
                        <div class="min-w-0 pr-1">
                            <p class="text-[10px] sm:text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Total Siswa Aktif</p>
                            <h3 class="text-lg sm:text-xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                                <span class="stat-counter" data-target="{{ $studentCount ?? 0 }}">{{ $studentCount ?? 0 }}</span>
                            </h3>
                        </div>
                        <div class="p-1.5 sm:p-2 bg-slate-50 dark:bg-slate-800/60 rounded-lg shrink-0">
                            <i data-lucide="users" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-slate-500 dark:text-slate-400"></i>
                        </div>
                    </div>
                    <div class="mt-2.5 text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 truncate">
                        Peserta didik aktif
                    </div>
                </div>

                <!-- Admin Card 2: Guru & Staf -->
                <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 lg:p-4 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                    <div class="flex justify-between items-start">
                        <div class="min-w-0 pr-1">
                            <p class="text-[10px] sm:text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Guru & Staf</p>
                            <h3 class="text-lg sm:text-xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                                @if(isset($employeeCount))
                                    <span class="stat-counter" data-target="{{ $employeeCount }}">{{ $employeeCount }}</span>
                                @else
                                    <span>-</span>
                                @endif
                            </h3>
                        </div>
                        <div class="p-1.5 sm:p-2 bg-slate-50 dark:bg-slate-800/60 rounded-lg shrink-0">
                            <i data-lucide="graduation-cap" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-slate-500 dark:text-slate-400"></i>
                        </div>
                    </div>
                    <div class="mt-2.5 text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 truncate">
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold">{{ $employeeAttendancePercent ?? 0 }}%</span> kehadiran
                    </div>
                </div>

                <!-- Admin Card 3: GPK -->
                <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 lg:p-4 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                    <div class="flex justify-between items-start">
                        <div class="min-w-0 pr-1">
                            <p class="text-[10px] sm:text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate" title="Guru Pendamping Khusus">GPK</p>
                            <h3 class="text-lg sm:text-xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                                @if(isset($gpkCount))
                                    <span class="stat-counter" data-target="{{ $gpkCount }}">{{ $gpkCount }}</span>
                                @else
                                    <span>-</span>
                                @endif
                            </h3>
                        </div>
                        <div class="p-1.5 sm:p-2 bg-slate-50 dark:bg-slate-800/60 rounded-lg shrink-0">
                            <i data-lucide="sun" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-slate-500 dark:text-slate-400"></i>
                        </div>
                    </div>
                    <div class="mt-2.5 text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 truncate">
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold">{{ $gpkAttendancePercent ?? 0 }}%</span> kehadiran
                    </div>
                </div>

                <!-- Admin Card 4: GPQ -->
                <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 lg:p-4 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                    <div class="flex justify-between items-start">
                        <div class="min-w-0 pr-1">
                            <p class="text-[10px] sm:text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate" title="Guru Pengajar Qur'an">GPQ</p>
                            <h3 class="text-lg sm:text-xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                                @if(isset($gpqCount))
                                    <span class="stat-counter" data-target="{{ $gpqCount }}">{{ $gpqCount }}</span>
                                @else
                                    <span>-</span>
                                @endif
                            </h3>
                        </div>
                        <div class="p-1.5 sm:p-2 bg-slate-50 dark:bg-slate-800/60 rounded-lg shrink-0">
                            <i data-lucide="book-open" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-slate-500 dark:text-slate-400"></i>
                        </div>
                    </div>
                    <div class="mt-2.5 text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 truncate">
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold">{{ $gpqAttendancePercent ?? 0 }}%</span> kehadiran
                    </div>
                </div>

                <!-- Admin Card 5: Total Rombel / Kelas -->
                <div onclick="window.location='{{ route('classrooms.index') }}'" class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 lg:p-4 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700 cursor-pointer">
                    <div class="flex justify-between items-start">
                        <div class="min-w-0 pr-1">
                            <p class="text-[10px] sm:text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Total Rombel</p>
                            <h3 class="text-lg sm:text-xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                                <span class="stat-counter" data-target="{{ $classroomCount ?? 0 }}">{{ $classroomCount ?? 0 }}</span>
                            </h3>
                        </div>
                        <div class="p-1.5 sm:p-2 bg-slate-50 dark:bg-slate-800/60 rounded-lg shrink-0">
                            <i data-lucide="layout-grid" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-slate-500 dark:text-slate-400"></i>
                        </div>
                    </div>
                    <div class="mt-2.5 text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 truncate">
                        Rombel / kelas aktif
                    </div>
                </div>

                <!-- Admin Card 6: Presensi Hari Ini -->
                <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 lg:p-4 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                    <div class="flex justify-between items-start">
                        <div class="min-w-0 pr-1">
                            <p class="text-[10px] sm:text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Presensi Hari Ini</p>
                            <h3 class="text-lg sm:text-xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                                <span class="stat-counter" data-target="{{ $todayOverallPercent ?? 0 }}">{{ $todayOverallPercent ?? 0 }}</span>%
                            </h3>
                        </div>
                        <div class="p-1.5 sm:p-2 bg-slate-50 dark:bg-slate-800/60 rounded-lg shrink-0">
                            <i data-lucide="clock" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-slate-500 dark:text-slate-400"></i>
                        </div>
                    </div>
                    <div class="mt-2.5 text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 truncate">
                        @if(($diffPercent ?? 0) > 0)
                            <span class="text-emerald-600 dark:text-emerald-400 font-bold">+{{ $diffPercent }}%</span> kemarin
                        @elseif(($diffPercent ?? 0) < 0)
                            <span class="text-red-600 dark:text-red-400 font-bold">{{ $diffPercent }}%</span> kemarin
                        @else
                            <span class="text-slate-500 dark:text-slate-400 font-bold">Sama dg kemarin</span>
                        @endif
                    </div>
                </div>
            </section>
        @else
            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <!-- Pegawai Card 1: Kehadiran Bulan Ini -->
                <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 lg:p-4 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                    <div class="flex justify-between items-start">
                        <div class="min-w-0 pr-1">
                            <p class="text-[10px] sm:text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Kehadiran Bulan Ini</p>
                            <h3 class="text-lg sm:text-xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                                <span>{{ $myReport['total_present'] ?? 0 }}</span> <span class="text-xs sm:text-sm font-medium text-slate-500">Hari</span>
                            </h3>
                        </div>
                        <div class="p-1.5 sm:p-2 bg-slate-50 dark:bg-slate-800/60 rounded-lg shrink-0">
                            <i data-lucide="calendar" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-slate-500 dark:text-slate-400"></i>
                        </div>
                    </div>
                    <div class="mt-2.5 text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 truncate">
                        Siklus aktif terhitung hadir
                    </div>
                </div>

                <!-- Pegawai Card 2: Total Keterlambatan -->
                <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 lg:p-4 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                    <div class="flex justify-between items-start">
                        <div class="min-w-0 pr-1">
                            <p class="text-[10px] sm:text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Total Keterlambatan</p>
                            <h3 class="text-lg sm:text-xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                                <span>{{ $totalLateDays ?? 0 }}</span> <span class="text-xs sm:text-sm font-medium text-slate-500">Hari</span>
                            </h3>
                        </div>
                        <div class="p-1.5 sm:p-2 bg-slate-50 dark:bg-slate-800/60 rounded-lg shrink-0">
                            <i data-lucide="hourglass" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-slate-500 dark:text-slate-400"></i>
                        </div>
                    </div>
                    <div class="mt-2.5 text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 truncate">
                        @if(($totalLateDays ?? 0) > 0)
                            <span class="text-amber-500 font-bold">Terlambat</span> pada siklus aktif
                        @elseif(($myReport['total_present'] ?? 0) == 0)
                            <span class="text-slate-400 dark:text-slate-500 font-medium">Belum ada presensi</span>
                        @else
                            <span class="text-emerald-600 dark:text-emerald-400 font-bold">Tepat Waktu</span>
                        @endif
                    </div>
                </div>

                <!-- Pegawai Card 3: Total Izin & Cuti -->
                <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 lg:p-4 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                    <div class="flex justify-between items-start">
                        <div class="min-w-0 pr-1">
                            <p class="text-[10px] sm:text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Total Izin & Cuti</p>
                            <h3 class="text-lg sm:text-xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                                <span>{{ $totalLeavesThisYear }}</span> <span class="text-xs sm:text-sm font-medium text-slate-500">Hari</span>
                            </h3>
                        </div>
                        <div class="p-1.5 sm:p-2 bg-slate-50 dark:bg-slate-800/60 rounded-lg shrink-0">
                            <i data-lucide="calendar-days" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-slate-500 dark:text-slate-400"></i>
                        </div>
                    </div>
                    <div class="mt-2.5 text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 truncate">
                        Akumulasi persetujuan tahun ini
                    </div>
                </div>

                <!-- Pegawai Card 4: Estimasi Uang Kehadiran -->
                <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 lg:p-4 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                    <div class="flex justify-between items-start">
                        <div class="min-w-0 pr-1">
                            <p class="text-[10px] sm:text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Uang Kehadiran</p>
                            <h3 class="text-lg sm:text-xl font-bold tracking-tight text-emerald-600 dark:text-emerald-400 mt-1">
                                Rp {{ number_format($myReport['bonus_nominal'] ?? 0, 0, ',', '.') }}
                            </h3>
                        </div>
                        <div class="p-1.5 sm:p-2 bg-emerald-50 dark:bg-emerald-950/50 rounded-lg shrink-0">
                            <i data-lucide="banknote" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-emerald-600 dark:text-emerald-400"></i>
                        </div>
                    </div>
                    <div class="mt-2.5 text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 truncate">
                        Akumulasi uang kehadiran bulan ini
                    </div>
                </div>
            @endif
        </section>

        <!-- DETAILED SECTIONS: CHARTS & ACTIVITIES -->
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Graph Card (SVG) -->
            <div class="animate-card lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 relative flex flex-col justify-between overflow-visible shadow-sm transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            @if($isAdmin)
                                <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50 font-nasalization">Tren Kehadiran Guru & Staf</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Tingkat kehadiran harian guru & staf pada periode berjalan</p>
                            @else
                                <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50 font-nasalization">Jadwal Kerja</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Informasi jam kerja dan kalender jadwal kerja Anda pada bulan ini</p>
                            @endif
                        </div>
                    </div>

                    <!-- Informational Box: Shift Active Summary (Concept 3) -->
                    @if(!$isAdmin && !empty($myActiveShifts))
                        <style>
                            .calendar-cell:hover .calendar-tooltip,
                            .calendar-cell:focus .calendar-tooltip {
                                display: block !important;
                            }
                            @media (max-width: 639px) {
                                .desktop-time { display: none !important; }
                                .mobile-time { display: block !important; }
                            }
                            @media (min-width: 640px) {
                                .desktop-time { display: inline-block !important; }
                                .mobile-time { display: none !important; }
                            }
                        </style>
                        <div class="mb-4 p-3.5 bg-slate-50 dark:bg-slate-950/70 border border-slate-200 dark:border-slate-800/80 rounded-lg text-sm">
                            <div class="flex items-center gap-1.5 font-semibold text-slate-900 dark:text-slate-200 mb-3">
                                <i data-lucide="info" class="w-3.5 h-3.5 text-indigo-500 shrink-0"></i>
                                <span>Informasi Jadwal Kerja Bulan Ini:</span>
                            </div>
                            
                            <div class="grid grid-cols-1 {{ !empty($myCalendarDays) ? 'md:grid-cols-3' : '' }} gap-4">
                                <!-- Left Column: Shift List (List Jam Kerja) -->
                                <div class="{{ !empty($myCalendarDays) ? 'md:col-span-1' : '' }} bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 rounded-md p-3 shadow-sm flex flex-col justify-between">
                                    <div>
                                        <p class="text-sm font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-3">Daftar Jam Kerja</p>
                                        <div class="space-y-3">
                                            @foreach($myActiveShifts as $index => $shift)
                                                <div class="{{ $index > 0 ? 'pt-3 border-t border-slate-100 dark:border-slate-800/50' : '' }}">
                                                    <div class="font-bold text-slate-700 dark:text-slate-200 mb-1 flex items-center justify-between">
                                                        <span>{{ $shift['name'] }}</span>
                                                        @if(!empty($shift['short_code']))
                                                            @php
                                                                $scUpper = strtoupper($shift['short_code']);
                                                                $scBadge = 'bg-blue-600 text-white';
                                                                if ($scUpper === 'P') {
                                                                    $scBadge = 'bg-cyan-500 text-white';
                                                                } elseif ($scUpper === 'PS-1' || $scUpper === 'SP1') {
                                                                    $scBadge = 'bg-blue-600 text-white';
                                                                } elseif ($scUpper === 'PS-1B') {
                                                                    $scBadge = 'bg-emerald-600 text-white';
                                                                } elseif ($scUpper === 'PS-2' || $scUpper === 'SP2') {
                                                                    $scBadge = 'bg-orange-500 text-white';
                                                                } elseif ($scUpper === 'PB' || stripos($shift['name'], 'toko belakang') !== false) {
                                                                    $scBadge = 'bg-purple-600 text-white';
                                                                } elseif (stripos($shift['name'], 'malam') !== false || stripos($scUpper, 'M') === 0 || $scUpper === 'SP3') {
                                                                    $scBadge = 'bg-indigo-800 text-white';
                                                                } elseif (stripos($shift['name'], 'siang') !== false || stripos($scUpper, 'S') === 0) {
                                                                    $scBadge = 'bg-amber-500 text-white';
                                                                }
                                                            @endphp
                                                            <span class="text-[10px] font-black px-1.5 py-0.5 rounded shadow-xs {{ $scBadge }}">{{ $shift['short_code'] }}</span>
                                                        @endif
                                                    </div>
                                                    @if(!empty($shift['description']))
                                                        <div class="text-xs text-slate-450 dark:text-slate-500 mb-1.5 leading-snug">{{ $shift['description'] }}</div>
                                                    @endif
                                                    
                                                    @php
                                                        $daysName = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 0 => 'Minggu'];
                                                        $groupedDetails = [];
                                                        if (!empty($shift['details']) && is_array($shift['details'])) {
                                                            foreach($shift['details'] as $dt) {
                                                                if(empty($dt['is_off'])) {
                                                                    $startTime = isset($dt['start_time']) ? substr($dt['start_time'], 0, 5) : '-';
                                                                    $endTime = isset($dt['end_time']) ? substr($dt['end_time'], 0, 5) : '-';
                                                                    $timeRange = $startTime . ' - ' . $endTime;
                                                                    $groupedDetails[$timeRange][] = $dt['day_of_week'] ?? 0;
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    
                                                    @if(!empty($groupedDetails))
                                                        <div class="space-y-1 mt-1">
                                                            @foreach($groupedDetails as $timeRange => $days)
                                                                @php
                                                                    usort($days, function($a, $b) {
                                                                        $valA = $a == 0 ? 7 : $a;
                                                                        $valB = $b == 0 ? 7 : $b;
                                                                        return $valA <=> $valB;
                                                                    });
                                                                    $dayLabels = array_map(function($d) use ($daysName) {
                                                                        return $daysName[$d] ?? '';
                                                                    }, $days);
                                                                    $daysStr = implode(', ', array_filter($dayLabels));
                                                                @endphp
                                                                <div class="flex flex-col gap-0.5 pb-1 border-b border-slate-50 dark:border-slate-800/40 last:border-0 last:pb-0">
                                                                    <span class="text-xs text-slate-500 dark:text-slate-400 font-medium leading-relaxed">{{ $daysStr }}</span>
                                                                    <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 font-mono">{{ $timeRange }}</span>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @elseif(!empty($shift['schedule']))
                                                        <div class="text-xs text-slate-600 dark:text-slate-400 font-mono mt-1">
                                                            {{ $shift['schedule'] }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>

                                        @if(isset($myPicketSchedules) && $myPicketSchedules->isNotEmpty())
                                            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/60">
                                                <div class="flex items-center justify-between mb-2">
                                                    <div class="flex items-center gap-1.5">
                                                        <i data-lucide="shield-check" class="w-4 h-4 text-amber-500"></i>
                                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider">Jadwal Piket</span>
                                                    </div>
                                                    <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded-full bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200/60 dark:border-amber-800/50">
                                                        {{ $myPicketSchedules->count() }} Hari
                                                    </span>
                                                </div>

                                                <div class="space-y-2">
                                                    @php
                                                        $daysName = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 0 => 'Minggu'];
                                                    @endphp
                                                    @foreach($myPicketSchedules as $picket)
                                                        @php
                                                            $isTodayPicket = now()->dayOfWeek == $picket->day_of_week;
                                                        @endphp
                                                        <div class="p-2 rounded-md border transition-all {{ $isTodayPicket ? 'bg-amber-50/70 dark:bg-amber-950/20 border-amber-300 dark:border-amber-800/60 shadow-xs' : 'bg-slate-50/70 dark:bg-slate-800/40 border-slate-200/60 dark:border-slate-800/50' }}">
                                                            <div class="flex items-center justify-between text-xs mb-1">
                                                                <span class="font-bold {{ $isTodayPicket ? 'text-amber-800 dark:text-amber-300' : 'text-slate-700 dark:text-slate-200' }}">
                                                                    {{ $daysName[$picket->day_of_week] ?? 'Hari' }}
                                                                </span>
                                                                @if($isTodayPicket)
                                                                    <span class="text-[8.5px] font-black uppercase px-1.5 py-0.5 rounded bg-amber-500 text-white animate-pulse">Hari Ini</span>
                                                                @endif
                                                            </div>
                                                            <div class="text-[11px] text-slate-600 dark:text-slate-350 flex items-center gap-1.5 mb-1">
                                                                <i data-lucide="map-pin" class="w-3 h-3 text-slate-400 shrink-0"></i>
                                                                <span class="font-medium truncate" title="{{ $picket->picketArea?->name ?? 'Area Piket' }}">{{ $picket->picketArea?->name ?? 'Area Piket' }}</span>
                                                            </div>
                                                            @if($picket->picketArea?->duty_hours)
                                                                <div class="text-[10px] text-amber-600 dark:text-amber-400 font-mono font-semibold flex items-center gap-1.5">
                                                                    <i data-lucide="clock" class="w-3 h-3 text-amber-500 shrink-0"></i>
                                                                    <span>{{ $picket->picketArea->duty_hours }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Right Column: Calendar Roster (Kalender Jadwal Kerja Roster) -->
                                @if(!empty($myCalendarDays))
                                    <div class="md:col-span-2 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 rounded-md p-3 shadow-sm flex flex-col justify-between">
                                        <div>
                                            <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-3">Kalender Jadwal Anda Bulan Ini</p>
                                            
                                            <div class="w-full max-w-sm sm:max-w-md">
                                                <!-- Calendar Grid Header -->
                                                <div class="grid grid-cols-7 gap-1 sm:gap-1.5 text-center font-bold text-[9px] sm:text-[10px] text-slate-400 dark:text-slate-500 uppercase mb-1.5">
                                                    <div>Sen</div>
                                                    <div>Sel</div>
                                                    <div>Rab</div>
                                                    <div>Kam</div>
                                                    <div>Jum</div>
                                                    <div>Sab</div>
                                                    <div>Min</div>
                                                </div>
                                                
                                                <!-- Calendar Days Grid -->
                                                <div class="grid grid-cols-7 gap-1 sm:gap-1.5 items-start">
                                                    @foreach($myCalendarDays as $day)
                                                        @if($day['is_empty'])
                                                            <div class="aspect-square bg-slate-50/30 dark:bg-slate-900/10 rounded-md" style="aspect-ratio: 1 / 1;"></div>
                                                        @else
                                                            @php
                                                                $cellClass = 'bg-slate-100 hover:bg-slate-200/70 dark:bg-slate-800/60 dark:hover:bg-slate-800 border-slate-200/70 dark:border-slate-800 text-slate-400 dark:text-slate-500';
                                                                $numClass = 'text-slate-400 dark:text-slate-500';
                                                                $labelClass = 'text-slate-400 dark:text-slate-400 font-bold';

                                                                switch($day['type'] ?? 'default') {
                                                                    case 'ps1':
                                                                        $cellClass = 'bg-blue-600 hover:bg-blue-700 text-white border-blue-700 shadow-xs';
                                                                        $numClass = 'text-blue-100';
                                                                        $labelClass = 'text-white font-black';
                                                                        break;
                                                                    case 'ps1b':
                                                                        $cellClass = 'bg-emerald-600 hover:bg-emerald-700 text-white border-emerald-700 shadow-xs';
                                                                        $numClass = 'text-emerald-100';
                                                                        $labelClass = 'text-white font-black';
                                                                        break;
                                                                    case 'ps2':
                                                                        $cellClass = 'bg-orange-500 hover:bg-orange-600 text-white border-orange-600 shadow-xs';
                                                                        $numClass = 'text-orange-100';
                                                                        $labelClass = 'text-white font-black';
                                                                        break;
                                                                    case 'pb':
                                                                        $cellClass = 'bg-purple-600 hover:bg-purple-700 text-white border-purple-700 shadow-xs';
                                                                        $numClass = 'text-purple-100';
                                                                        $labelClass = 'text-white font-black';
                                                                        break;
                                                                    case 'malam':
                                                                        $cellClass = 'bg-indigo-800 hover:bg-indigo-900 text-white border-indigo-900 shadow-xs';
                                                                        $numClass = 'text-indigo-200';
                                                                        $labelClass = 'text-white font-black';
                                                                        break;
                                                                    case 'pagi':
                                                                        $cellClass = 'bg-cyan-500 hover:bg-cyan-600 text-white border-cyan-600 shadow-xs';
                                                                        $numClass = 'text-cyan-100';
                                                                        $labelClass = 'text-white font-black';
                                                                        break;
                                                                    case 'siang':
                                                                        $cellClass = 'bg-amber-500 hover:bg-amber-600 text-white border-amber-600 shadow-xs';
                                                                        $numClass = 'text-amber-100';
                                                                        $labelClass = 'text-white font-black';
                                                                        break;
                                                                    case 'other':
                                                                        $cellClass = 'bg-cyan-600 hover:bg-cyan-700 text-white border-cyan-700 shadow-xs';
                                                                        $numClass = 'text-cyan-100';
                                                                        $labelClass = 'text-white font-black';
                                                                        break;
                                                                    case 'off':
                                                                        $cellClass = 'bg-slate-100 hover:bg-slate-200/70 dark:bg-slate-800/60 dark:hover:bg-slate-800 border-slate-200/70 dark:border-slate-800 text-slate-400 dark:text-slate-500';
                                                                        $numClass = 'text-slate-400 dark:text-slate-500';
                                                                        $labelClass = 'text-slate-400 dark:text-slate-400 font-bold';
                                                                        break;
                                                                    case 'sakit':
                                                                        $cellClass = 'bg-rose-600 hover:bg-rose-700 text-white border-rose-700 shadow-xs';
                                                                        $numClass = 'text-rose-100';
                                                                        $labelClass = 'text-white font-black';
                                                                        break;
                                                                    case 'izin':
                                                                        $cellClass = 'bg-yellow-400 hover:bg-yellow-500 text-yellow-950 border-yellow-500 shadow-xs';
                                                                        $numClass = 'text-yellow-900';
                                                                        $labelClass = 'text-yellow-950 font-black';
                                                                        break;
                                                                    case 'cuti':
                                                                        $cellClass = 'bg-teal-600 hover:bg-teal-700 text-white border-teal-700 shadow-xs';
                                                                        $numClass = 'text-teal-100';
                                                                        $labelClass = 'text-white font-black';
                                                                        break;
                                                                }

                                                                $codeLen = mb_strlen($day['short_label'] ?? '');
                                                                if ($codeLen <= 1) {
                                                                    $codeSizeClass = 'text-[10px] sm:text-xs';
                                                                } elseif ($codeLen <= 2) {
                                                                    $codeSizeClass = 'text-[9.5px] sm:text-[11px]';
                                                                } elseif ($codeLen <= 4) {
                                                                    $codeSizeClass = 'text-[8.5px] sm:text-[10px] tracking-tight';
                                                                } else {
                                                                    $codeSizeClass = 'text-[7px] sm:text-[8.5px] tracking-tighter';
                                                                }
                                                            @endphp
                                                            <div class="calendar-cell aspect-square flex flex-col justify-between p-1 sm:p-1.5 border rounded-md relative group outline-none cursor-pointer transition-all duration-150 shadow-xs hover:scale-[1.04] hover:z-10 {{ !empty($day['is_picket']) ? 'ring-2 ring-amber-400 ring-offset-1 dark:ring-offset-slate-900 border-amber-400' : '' }} {{ $cellClass }}" style="aspect-ratio: 1 / 1;" tabindex="0">
                                                                <!-- Date Number & Picket Indicator -->
                                                                <div class="flex items-center justify-between w-full leading-none">
                                                                    <span class="text-[9px] sm:text-[10px] font-bold {{ $numClass }}">{{ $day['day_num'] }}</span>
                                                                    @if(!empty($day['is_picket']))
                                                                        <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-amber-400 border border-white dark:border-slate-900 shadow-xs animate-pulse shrink-0" title="Jadwal Piket"></span>
                                                                    @endif
                                                                </div>
                                                                
                                                                <!-- Shift Code (Center & Responsive) -->
                                                                <div class="flex-1 flex items-center justify-center">
                                                                    <span class="{{ $codeSizeClass }} {{ $labelClass }} text-center leading-none select-none max-w-full whitespace-nowrap">
                                                                        {{ $day['short_label'] }}
                                                                    </span>
                                                                </div>
                                                                
                                                                <!-- Tooltip on hover/tap -->
                                                                <div class="calendar-tooltip absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 w-36 hidden group-hover:block group-focus:block z-50 pointer-events-none">
                                                                    <div class="bg-white/95 dark:bg-slate-900/95 text-slate-800 dark:text-white p-2 rounded-lg shadow-lg text-[9px] sm:text-[10px] leading-snug border border-slate-200 dark:border-slate-800/80 backdrop-blur-sm relative">
                                                                        <div class="font-semibold border-b border-slate-200 dark:border-slate-800/50 pb-0.5 mb-1 flex justify-between">
                                                                            <span class="text-slate-950 dark:text-white">{{ \Carbon\Carbon::parse($day['date'])->translatedFormat('d M Y') }}</span>
                                                                        </div>
                                                                        @if($day['shift_name'])
                                                                            <div class="font-semibold text-indigo-600 dark:text-indigo-400">{{ $day['shift_name'] }}</div>
                                                                            <div class="text-[8px] text-slate-500 dark:text-slate-400">Jam: {{ $day['shift_start'] }} - {{ $day['shift_end'] }}</div>
                                                                        @else
                                                                            <div class="text-slate-400 dark:text-slate-500">Libur / Off</div>
                                                                        @endif

                                                                        @if(!empty($day['is_picket']))
                                                                            <div class="mt-1 pt-1 border-t border-amber-200 dark:border-amber-800/60 text-amber-600 dark:text-amber-400">
                                                                                <div class="font-bold flex items-center gap-1">
                                                                                    <i data-lucide="shield-check" class="w-2.5 h-2.5 shrink-0"></i>
                                                                                    <span>Tugas Piket:</span>
                                                                                </div>
                                                                                <div class="text-[8.5px] font-semibold text-slate-800 dark:text-slate-200">{{ $day['picket_area_name'] ?? 'Piket' }}</div>
                                                                                @if(!empty($day['picket_duty_hours']))
                                                                                    <div class="text-[8px] font-mono text-amber-600 dark:text-amber-400">{{ $day['picket_duty_hours'] }}</div>
                                                                                @endif
                                                                            </div>
                                                                        @endif
                                                                        <!-- Arrow -->
                                                                        <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-white dark:bg-slate-900 border-r border-b border-slate-200 dark:border-slate-800/80 rotate-45"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Footer Note & Legend -->
                                        <div class="mt-4 pt-2 border-t border-slate-100 dark:border-slate-800/50 flex flex-wrap items-center justify-between gap-2 text-[9px] text-slate-500 dark:text-slate-400 leading-normal">
                                            <div class="flex items-center gap-1.5">
                                                <i data-lucide="info" class="w-3 h-3 text-amber-500 shrink-0"></i>
                                                <span><strong>Catatan:</strong> Pengajuan tukar shift minimal dilakukan 1 hari sebelumnya.</span>
                                            </div>
                                            @if(isset($myPicketSchedules) && $myPicketSchedules->isNotEmpty())
                                                <div class="flex items-center gap-1.5 px-2 py-0.5 rounded bg-amber-50 dark:bg-amber-950/40 border border-amber-200/60 dark:border-amber-800/50 text-amber-700 dark:text-amber-300 font-semibold shrink-0">
                                                    <span class="w-2 h-2 rounded-full bg-amber-400 border border-white dark:border-slate-900 shadow-xs animate-pulse"></span>
                                                    <span>Jadwal Piket</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                    <!-- Scrollable wrapper for chart and labels -->
                    <div class="overflow-x-auto hide-scrollbar pb-2 mt-4 w-full">
                        @php
                            $minCardWidth = 850; // typical width to fill desktop card
                            $minSpacing = 60;
                            $svgWidth = 500;
                            
                            if ($isAdmin && count($adminChartPoints) > 0) {
                                $count = count($adminChartPoints);
                                $requiredWidth = ($count - 1) * $minSpacing + 60; // 30px padding each side
                                $svgWidth = max($minCardWidth, $requiredWidth);
                                $spacing = ($svgWidth - 60) / max(1, $count - 1);
                                
                                $adminAreaD = "";
                                $adminLineD = "";
                                
                                foreach($adminChartPoints as $i => &$pt) {
                                    $pt['x'] = $i * $spacing + 30;
                                    $prefix = $i == 0 ? "M" : "L";
                                    $adminLineD .= "{$prefix} {$pt['x']},{$pt['y']} ";
                                    $adminAreaD .= "L {$pt['x']},{$pt['y']} ";
                                }
                                unset($pt);
                                
                                $firstX = $adminChartPoints[0]['x'];
                                $lastX = end($adminChartPoints)['x'];
                                $adminAreaD = "M {$firstX},150 " . $adminAreaD . " L {$lastX},150 Z";
                            } elseif (!$isAdmin && count($chartPoints) > 0) {
                                $count = count($chartPoints);
                                $requiredWidth = ($count - 1) * $minSpacing + 60; // 30px padding each side
                                $svgWidth = max($minCardWidth, $requiredWidth);
                                $spacing = ($svgWidth - 60) / max(1, $count - 1);
                                
                                $areaD = "";
                                $lineD = "";
                                
                                foreach($chartPoints as $i => &$pt) {
                                    $pt['x'] = $i * $spacing + 30;
                                    $prefix = $i == 0 ? "M" : "L";
                                    $lineD .= "{$prefix} {$pt['x']},{$pt['y']} ";
                                    $areaD .= "L {$pt['x']},{$pt['y']} ";
                                }
                                unset($pt);
                                
                                $firstX = $chartPoints[0]['x'];
                                $lastX = end($chartPoints)['x'];
                                $areaD = "M {$firstX},150 " . $areaD . " L {$lastX},150 Z";
                            }
                        @endphp
                        
                        <div style="min-width: {{ $svgWidth }}px; width: {{ $svgWidth }}px;">
                            <!-- Mini Graphic SVG Container -->
                            <div class="relative w-full h-44 flex items-end">
                                <svg viewBox="0 0 {{ $svgWidth }} 150" class="w-full h-full chart-line overflow-visible">
                                    <!-- Grids -->
                                    <line x1="0" y1="30" x2="{{ $svgWidth }}" y2="30" stroke="currentColor" class="text-slate-100 dark:text-slate-900" stroke-width="1" />
                                    <line x1="0" y1="75" x2="{{ $svgWidth }}" y2="75" stroke="currentColor" class="text-slate-100 dark:text-slate-900" stroke-width="1" />
                                    <line x1="0" y1="120" x2="{{ $svgWidth }}" y2="120" stroke="currentColor" class="text-slate-100 dark:text-slate-900" stroke-width="1" />
                                    
                                    @if($isAdmin)
                                        <!-- Dynamic Area path for Admin -->
                                        @if(count($adminChartPoints) > 0)
                                        <path d="{{ $adminAreaD }}" fill="url(#grad-area)" opacity="0.15"></path>
                                        
                                        <!-- Dynamic line path for Admin -->
                                        <path d="{{ $adminLineD }}" fill="none" stroke="currentColor" class="text-indigo-600 dark:text-indigo-400" stroke-width="2.5" stroke-linecap="round"></path>
                                        
                                        <!-- Circles & Tooltips for Admin -->
                                        @foreach($adminChartPoints as $pt)
                                            <g class="group relative cursor-pointer outline-none" tabindex="0">
                                                <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="3.5" class="fill-indigo-600 dark:fill-indigo-400 stroke-white dark:stroke-slate-900" stroke-width="1" />
                                                
                                                <text x="{{ $pt['x'] }}" y="{{ $pt['y'] - 8 }}" text-anchor="middle" class="text-[8px] sm:text-[9px] font-bold fill-slate-600 dark:fill-slate-400">
                                                    {{ $pt['percent'] }}%
                                                </text>

                                                <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] - 10 }}" r="18" fill="transparent" class="cursor-pointer" />

                                                <foreignObject x="{{ $pt['x'] - 65 }}" y="{{ $pt['y'] - 75 }}" width="130" height="65" class="pointer-events-none invisible opacity-0 group-hover:visible group-hover:opacity-100 group-focus:visible group-focus:opacity-100 group-active:visible group-active:opacity-100 transition-all duration-200 overflow-visible z-50">
                                                    <div class="bg-white/95 dark:bg-slate-950/95 text-slate-800 dark:text-white p-2 rounded-lg shadow-lg text-[9px] sm:text-[10px] leading-snug border border-slate-200 dark:border-slate-800/80 backdrop-blur-sm relative">
                                                        <div class="font-semibold border-b border-slate-200 dark:border-slate-800/50 pb-0.5 mb-1 flex justify-between">
                                                            <span class="text-slate-950 dark:text-white">{{ $pt['date'] }}</span>
                                                            <span class="text-indigo-600 dark:text-indigo-400 font-bold">{{ $pt['percent'] }}% Hadir</span>
                                                        </div>
                                                        <div>Pegawai Hadir: <span class="font-semibold text-slate-950 dark:text-white">{{ $pt['count'] }} orang</span></div>
                                                        <div class="text-[8px] text-slate-500 dark:text-slate-400 mt-0.5">Basis data kepegawaian SAANS SD</div>
                                                        <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-white dark:bg-slate-950 border-r border-b border-slate-200 dark:border-slate-800/80 rotate-45"></div>
                                                    </div>
                                                </foreignObject>
                                            </g>
                                        @endforeach
                                        @endif
                                    @else
                                        <!-- Dynamic Area path -->
                                        @if(count($chartPoints) > 0)
                                        <path d="{{ $areaD }}" fill="url(#grad-area)" opacity="0.15"></path>
                                        
                                        <!-- Dynamic line path -->
                                        <path d="{{ $lineD }}" fill="none" stroke="currentColor" class="text-indigo-600 dark:text-indigo-400" stroke-width="2.5" stroke-linecap="round"></path>
                                        
                                        <!-- Circles & Text Labels on Points -->
                                        @foreach($chartPoints as $pt)
                                            <g class="group relative cursor-pointer outline-none" tabindex="0">
                                                <!-- Point Circle -->
                                                <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="3.5" class="fill-indigo-600 dark:fill-indigo-400 stroke-white dark:stroke-slate-900" style="{{ !empty($pt['is_late']) ? 'fill: #f59e0b;' : '' }}" stroke-width="1" />
                                                
                                                <!-- Time Text -->
                                                <text x="{{ $pt['x'] }}" y="{{ $pt['y'] - 8 }}" text-anchor="middle" class="text-[8px] sm:text-[9px] font-bold fill-slate-600 dark:fill-slate-300" style="{{ !empty($pt['is_late']) ? 'fill: #f59e0b;' : '' }}">
                                                    {{ $pt['time'] }}
                                                </text>

                                                <!-- Invisible interactive area for hover/touch (makes it easy to tap on mobile!) -->
                                                <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] - 10 }}" r="18" fill="transparent" class="cursor-pointer" />

                                                <!-- Styled Tooltip using foreignObject -->
                                                <foreignObject x="{{ $pt['x'] - 65 }}" y="{{ $pt['y'] - 75 }}" width="130" height="65" class="pointer-events-none invisible opacity-0 group-hover:visible group-hover:opacity-100 group-focus:visible group-focus:opacity-100 group-active:visible group-active:opacity-100 transition-all duration-200 overflow-visible z-50">
                                                    <div class="bg-white/95 dark:bg-slate-950/95 text-slate-800 dark:text-white p-2 rounded-lg shadow-lg text-[9px] sm:text-[10px] leading-snug border border-slate-200 dark:border-slate-800/80 backdrop-blur-sm relative">
                                                        <div class="font-semibold border-b border-slate-200 dark:border-slate-800/50 pb-0.5 mb-1 flex justify-between">
                                                            <span class="text-slate-950 dark:text-white">{{ $pt['date'] }}</span>
                                                            <span class="{{ !empty($pt['is_late']) ? 'text-amber-500 dark:text-amber-400 font-bold' : 'text-emerald-600 dark:text-emerald-450 font-bold' }}">{{ $pt['status'] }}</span>
                                                        </div>
                                                        <div>Jam Masuk: <span class="font-semibold text-slate-950 dark:text-white">{{ $pt['check_in'] !== '-' ? $pt['check_in'] : 'Belum absen' }}</span></div>
                                                        <div class="text-[8px] text-slate-500 dark:text-slate-400 mt-0.5">Jadwal: {{ $pt['shift_start'] ? $pt['shift_start'] . ' - ' . ($pt['shift_end'] ?? 'Selesai') : 'Libur/Off' }}</div>
                                                        <!-- Tooltip Arrow -->
                                                        <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-white dark:bg-slate-950 border-r border-b border-slate-200 dark:border-slate-800/80 rotate-45"></div>
                                                    </div>
                                                </foreignObject>
                                            </g>
                                        @endforeach
                                        @endif
                                    @endif
                                    
                                    <!-- Gradients defs -->
                                    <defs>
                                        <linearGradient id="grad-area" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0%" stop-color="currentColor" class="text-indigo-600 dark:text-indigo-400" />
                                            <stop offset="100%" stop-color="currentColor" class="text-indigo-600 dark:text-indigo-400" stop-opacity="0" />
                                        </linearGradient>
                                    </defs>
                                </svg>
                            </div>
                            
                            <!-- Chart Labels -->
                            <div class="flex justify-between text-xs text-slate-400 dark:text-slate-500 font-semibold uppercase mt-4 w-full px-[15px]">
                                @if($isAdmin)
                                    @foreach($adminChartPoints as $pt)
                                         <span class="w-[30px] text-center">{{ $pt['short_date'] }}</span>
                                    @endforeach
                                @else
                                    @foreach($chartPoints as $pt)
                                        <span class="w-[30px] text-center">{{ $pt['short_date'] }}</span>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                <!-- Announcements / Information System -->
            <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 sm:p-6 flex flex-col justify-between shadow-sm transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50">Pengumuman Sekolah</h3>
                        <a href="{{ route('announcements.index') }}" class="text-xs text-slate-500 dark:text-slate-400 font-semibold hover:underline">Lihat Semua</a>
                    </div>

                    <!-- List of updates -->
                    <div class="space-y-3.5">
                        @forelse($latestAnnouncements ?? collect() as $announcement)
                            <div class="flex gap-2.5 items-start">
                                <div class="w-1.5 h-1.5 rounded-full {{ $announcement->category == 'penting' ? 'bg-red-500' : 'bg-slate-400 dark:bg-slate-500' }} mt-1.5 shrink-0"></div>
                                <div class="flex-1">
                                    <h4 class="text-xs font-semibold {{ $announcement->category == 'penting' ? 'text-red-600 dark:text-red-400' : 'text-slate-900 dark:text-slate-50' }}">
                                        <a href="{{ route('announcements.show', $announcement) }}" class="hover:underline">{{ $announcement->title }}</a>
                                    </h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-2">{{ Str::limit(html_entity_decode(strip_tags(str_replace('&nbsp;', ' ', $announcement->content)), ENT_QUOTES, 'UTF-8'), 100) }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">{{ $announcement->created_at->translatedFormat('d M Y, H:i') }}</span>
                                        @if($announcement->attachment)
                                            <span class="text-[9px] text-slate-350 dark:text-slate-700 select-none">•</span>
                                            <a href="{{ Storage::url($announcement->attachment) }}" target="_blank" class="text-[10px] text-blue-500 hover:underline inline-flex items-center gap-0.5"><i data-lucide="paperclip" class="w-3 h-3"></i> Lampiran</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-xs text-slate-500 text-center py-4">Belum ada pengumuman terbaru.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Info tag footer -->
                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-400">
                    <span>Diperbarui secara real-time</span>
                </div>
            </div>
        </section>

        @if($isAdmin)
        <!-- RIWAYAT IZIN/CUTI PEGAWAI & AKTIVITAS KEHADIRAN TERKINI (ADMIN ONLY) -->
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-5 lg:gap-6">
            
            <!-- Card 1: Riwayat Izin/Cuti Pegawai -->
            <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden flex flex-col transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                <div class="px-4 sm:px-5 py-3.5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-900/50">
                    <div class="flex items-center gap-2">
                        <i data-lucide="calendar-clock" class="w-4 h-4 text-indigo-500"></i>
                        <h3 class="font-bold text-xs sm:text-sm text-slate-800 dark:text-slate-200 font-nasalization">Riwayat Izin/Cuti Pegawai</h3>
                    </div>
                    <a href="{{ route('leaves.index') }}" class="text-[11px] sm:text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">Lihat Semua</a>
                </div>
                <div class="flex-1 overflow-y-auto max-h-[340px]">
                    <div class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        @if(isset($recentLeaves) && count($recentLeaves) > 0)
                            @foreach($recentLeaves as $leave)
                                @php
                                    $empName = $leave->employee->name ?? 'Pegawai';
                                    $empPos = $leave->employee->position ?? null;
                                    $start = $leave->start_date ? \Carbon\Carbon::parse($leave->start_date)->startOfDay() : null;
                                    $end = $leave->end_date ? \Carbon\Carbon::parse($leave->end_date)->startOfDay() : null;
                                    $diff = ($start && $end) ? (int) round($start->diffInDays($end)) + 1 : 1;
                                    $rangeString = $start ? $start->translatedFormat('d M Y') : '-';
                                    if ($start && $end && $start->format('Y-m-d') !== $end->format('Y-m-d')) {
                                        $rangeString .= ' s/d ' . $end->translatedFormat('d M Y');
                                    }
                                    $statusClass = [
                                        'Pending' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 border-amber-200/60 dark:border-amber-900/40',
                                        'Approved' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border-emerald-200/60 dark:border-emerald-900/40',
                                        'Rejected' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400 border-rose-200/60 dark:border-rose-900/40',
                                    ][$leave->status] ?? 'bg-slate-50 text-slate-700 border-slate-200';
                                @endphp
                                <a href="{{ route('leaves.index') }}" class="block p-3.5 sm:p-4 hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-all border-b last:border-b-0 border-slate-100 dark:border-slate-800/40">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 sm:gap-4">
                                        <!-- Left Side: Employee Details & Leave Info -->
                                        <div class="flex-1 min-w-0 text-left">
                                            <div class="flex items-center gap-2 mb-1">
                                                <p class="font-bold text-xs sm:text-sm text-slate-900 dark:text-slate-100 hover:text-indigo-600 transition-colors truncate">
                                                    {{ $empName }}
                                                </p>
                                                @if($empPos)
                                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-full border uppercase tracking-wider bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-400 border-indigo-200/50 dark:border-indigo-800/40 shrink-0">
                                                        {{ $empPos }}
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 text-[11px] sm:text-xs text-slate-600 dark:text-slate-400">
                                                <span class="flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 shrink-0"></span>
                                                    Mengajukan <strong class="text-slate-800 dark:text-slate-200 font-semibold">{{ $leave->leaveType->name ?? $leave->type }}</strong>
                                                </span>
                                                <span class="text-slate-300 dark:text-slate-700">|</span>
                                                <span class="text-slate-500 dark:text-slate-400">
                                                    {{ $rangeString }} <span class="font-semibold text-slate-700 dark:text-slate-300">({{ $diff }} Hari)</span>
                                                </span>
                                            </div>
                                            
                                            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1.5 flex items-center gap-1">
                                                <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                Diajukan pada: <span class="font-mono text-slate-500 dark:text-slate-400 font-semibold">{{ \Carbon\Carbon::parse($leave->created_at)->translatedFormat('d M Y, H:i') }} WIB</span>
                                            </p>
                                        </div>

                                        <!-- Right Side: Status Badge -->
                                        <div class="flex items-center sm:items-end justify-between sm:justify-center sm:flex-col gap-1.5 shrink-0">
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-lg border flex items-center gap-1.5 tracking-wider {{ $statusClass }}">
                                                <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $leave->status === 'Pending' ? 'bg-amber-500' : ($leave->status === 'Approved' ? 'bg-emerald-500' : 'bg-rose-500') }}"></span>
                                                {{ $leave->status }}
                                            </span>
                                            
                                            @if($leave->notes)
                                                <span class="text-[9px] text-slate-400 dark:text-slate-500 truncate max-w-[140px] italic" title="{{ $leave->notes }}">
                                                    "{{ $leave->notes }}"
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        @else
                            <div class="p-8 text-center text-slate-400">
                                <p class="text-xs">Belum ada riwayat pengajuan izin/cuti.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Card 2: Aktivitas Kehadiran Terkini -->
            <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden flex flex-col transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                <div class="px-4 sm:px-5 py-3.5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-900/50">
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <i data-lucide="activity" class="w-4 h-4 text-emerald-500"></i>
                        <h3 class="font-bold text-xs sm:text-sm text-slate-800 dark:text-slate-200 font-nasalization">Aktivitas Kehadiran Terkini</h3>
                    </div>
                    <span class="text-[10px] sm:text-[11px] text-slate-400 font-medium">Hari ini, {{ now()->translatedFormat('d M Y') }}</span>
                </div>
                <div class="p-4 sm:p-5 flex-1 overflow-hidden flex flex-col">
                    <div class="max-h-[340px] overflow-y-auto pr-2 custom-scrollbar flex-1">
                        @if(isset($recentAttendancesToday) && count($recentAttendancesToday) > 0)
                            <div class="border-l-2 border-slate-200 dark:border-slate-800 ml-2 space-y-4">
                                @foreach($recentAttendancesToday as $att)
                                    @php
                                        $isCheckout = !empty($att['check_out']);
                                        $empName = $att['name'] ?? 'Pegawai';
                                        $empPhoto = $att['photo'] ?? null;
                                        $photoSrc = null;
                                        if ($empPhoto) {
                                            $photoSrc = str_starts_with($empPhoto, 'http') ? $empPhoto : asset('storage/' . ltrim($empPhoto, '/'));
                                        }
                                    @endphp
                                    <div class="relative pl-5">
                                        <span class="absolute -left-[9px] top-2 w-3.5 h-3.5 rounded-full border-4 border-white dark:border-slate-900 {{ $isCheckout ? 'bg-blue-500' : 'bg-emerald-500' }}"></span>
                                        <div class="flex items-center gap-3">
                                            <div class="relative shrink-0 w-8 h-8">
                                                @if($photoSrc)
                                                    <img src="{{ $photoSrc }}" onerror="this.remove(); document.getElementById('att-init-{{ $loop->index }}').classList.remove('hidden');" class="w-8 h-8 rounded-full object-cover border border-slate-200 dark:border-slate-700">
                                                @endif
                                                <div id="att-init-{{ $loop->index }}" class="w-8 h-8 rounded-full flex items-center justify-center text-[11px] font-bold text-slate-700 dark:text-slate-300 bg-slate-200 dark:bg-slate-800 shrink-0 {{ $photoSrc ? 'hidden' : '' }}">
                                                    {{ strtoupper(substr($empName, 0, 2)) }}
                                                </div>
                                            </div>
                                            <div class="min-w-0 flex-1 text-left">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="font-bold text-xs sm:text-sm text-slate-900 dark:text-slate-100 truncate">{{ $empName }}</span>
                                                    @if(!empty($att['position']))
                                                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">({{ $att['position'] }})</span>
                                                    @endif
                                                </div>
                                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                                                    @if(!empty($att['check_in']))
                                                        Hadir <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $att['check_in'] }}</span>
                                                    @endif
                                                    @if(!empty($att['check_out']))
                                                        @if(!empty($att['check_in']))
                                                            <span class="mx-1">&bull;</span>
                                                        @endif
                                                        Pulang <span class="font-bold text-blue-600 dark:text-blue-400">{{ $att['check_out'] }}</span>
                                                    @endif
                                                    @if(!empty($att['shift_name']))
                                                        <span class="text-slate-400 ml-1">({{ $att['shift_name'] }})</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <i data-lucide="clock" class="w-7 h-7 text-slate-300 dark:text-slate-700 mx-auto mb-2"></i>
                                <p class="text-xs text-slate-400">Belum ada aktivitas kehadiran terekam hari ini.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
        @else
        <!-- AKSI CEPAT & RIWAYAT IZIN/CUTI SAYA (PEGAWAI ONLY) -->
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-5 lg:gap-6">
            
            <!-- Card 1: Aksi Cepat -->
            <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden flex flex-col transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                <div class="px-4 sm:px-5 py-3.5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-900/50">
                    <div class="flex items-center gap-2">
                        <i data-lucide="zap" class="w-4 h-4 text-amber-500"></i>
                        <h3 class="font-bold text-xs sm:text-sm text-slate-800 dark:text-slate-200 font-nasalization">Aksi Cepat</h3>
                    </div>
                    <span class="text-[10px] sm:text-[11px] font-medium text-slate-400 dark:text-slate-500">Pintasan Layanan</span>
                </div>
                <div class="p-4 sm:p-5 flex-1 flex flex-col justify-center">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        
                        <!-- 1. Ajukan Izin / Cuti -->
                        <a href="{{ route('my-leaves.index') }}" class="group flex items-start gap-3 p-3.5 rounded-xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 hover:bg-indigo-50/60 dark:hover:bg-indigo-950/30 hover:border-indigo-200 dark:hover:border-indigo-900/50 transition-all duration-200 shadow-2xs hover:shadow-xs">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-100 dark:border-indigo-800/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 group-hover:scale-105 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                                <i data-lucide="file-signature" class="w-5 h-5"></i>
                            </div>
                            <div class="min-w-0 text-left">
                                <h4 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors leading-tight">Ajukan Izin / Cuti</h4>
                                <p class="text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 mt-1 line-clamp-1">Formulir izin & cuti mandiri</p>
                            </div>
                        </a>

                        <!-- 2. Presensi & Kalender -->
                        <a href="{{ route('attendances.index') }}" class="group flex items-start gap-3 p-3.5 rounded-xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 hover:bg-emerald-50/60 dark:hover:bg-emerald-950/30 hover:border-emerald-200 dark:hover:border-emerald-900/50 transition-all duration-200 shadow-2xs hover:shadow-xs">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-100 dark:border-emerald-800/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 group-hover:scale-105 group-hover:bg-emerald-600 group-hover:text-white transition-all">
                                <i data-lucide="calendar-check-2" class="w-5 h-5"></i>
                            </div>
                            <div class="min-w-0 text-left">
                                <h4 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors leading-tight">Presensi & Kalender</h4>
                                <p class="text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 mt-1 line-clamp-1">Riwayat & jadwal kerja</p>
                            </div>
                        </a>

                        <!-- 3. Slip Gaji -->
                        <a href="{{ route('payslips.index') }}" class="group flex items-start gap-3 p-3.5 rounded-xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 hover:bg-sky-50/60 dark:hover:bg-sky-950/30 hover:border-sky-200 dark:hover:border-sky-900/50 transition-all duration-200 shadow-2xs hover:shadow-xs">
                            <div class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-950/60 border border-sky-100 dark:border-sky-800/50 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0 group-hover:scale-105 group-hover:bg-sky-600 group-hover:text-white transition-all">
                                <i data-lucide="receipt" class="w-5 h-5"></i>
                            </div>
                            <div class="min-w-0 text-left">
                                <h4 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200 group-hover:text-sky-600 dark:group-hover:text-sky-400 transition-colors leading-tight">Slip Gaji</h4>
                                <p class="text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 mt-1 line-clamp-1">Lihat & unduh rincian gaji</p>
                            </div>
                        </a>

                        <!-- 4. Jadwal Piket -->
                        <a href="{{ route('picket-schedules.index') }}" class="group flex items-start gap-3 p-3.5 rounded-xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 hover:bg-amber-50/60 dark:hover:bg-amber-950/30 hover:border-amber-200 dark:hover:border-amber-900/50 transition-all duration-200 shadow-2xs hover:shadow-xs">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 border border-amber-100 dark:border-amber-800/50 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 group-hover:scale-105 group-hover:bg-amber-600 group-hover:text-white transition-all">
                                <i data-lucide="shield-check" class="w-5 h-5"></i>
                            </div>
                            <div class="min-w-0 text-left">
                                <h4 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors leading-tight">Jadwal Piket</h4>
                                <p class="text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 mt-1 line-clamp-1">Roster & area tugas piket</p>
                            </div>
                        </a>

                    </div>
                </div>
            </div>

            <!-- Card 2: Riwayat Izin/Cuti Saya -->
            <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden flex flex-col transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                <div class="px-4 sm:px-5 py-3.5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-900/50">
                    <div class="flex items-center gap-2">
                        <i data-lucide="calendar-clock" class="w-4 h-4 text-indigo-500"></i>
                        <h3 class="font-bold text-xs sm:text-sm text-slate-800 dark:text-slate-200 font-nasalization">Riwayat Izin/Cuti Saya</h3>
                    </div>
                    <a href="{{ route('my-leaves.index') }}" class="text-[11px] sm:text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">Lihat Semua</a>
                </div>
                <div class="flex-1 overflow-y-auto max-h-[340px]">
                    <div class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        @if(isset($myRecentLeaves) && count($myRecentLeaves) > 0)
                            @foreach($myRecentLeaves as $leave)
                                @php
                                    $start = $leave->start_date ? \Carbon\Carbon::parse($leave->start_date)->startOfDay() : null;
                                    $end = $leave->end_date ? \Carbon\Carbon::parse($leave->end_date)->startOfDay() : null;
                                    $diff = ($start && $end) ? (int) round($start->diffInDays($end)) + 1 : 1;
                                    $rangeString = $start ? $start->translatedFormat('d M Y') : '-';
                                    if ($start && $end && $start->format('Y-m-d') !== $end->format('Y-m-d')) {
                                        $rangeString .= ' s/d ' . $end->translatedFormat('d M Y');
                                    }
                                    $statusClass = [
                                        'Pending' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 border-amber-200/60 dark:border-amber-900/40',
                                        'Approved' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border-emerald-200/60 dark:border-emerald-900/40',
                                        'Rejected' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400 border-rose-200/60 dark:border-rose-900/40',
                                    ][$leave->status] ?? 'bg-slate-50 text-slate-700 border-slate-200';
                                @endphp
                                <a href="{{ route('my-leaves.index') }}" class="block p-3.5 sm:p-4 hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-all border-b last:border-b-0 border-slate-100 dark:border-slate-800/40">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 sm:gap-4">
                                        <!-- Left Side: Leave Info -->
                                        <div class="flex-1 min-w-0 text-left">
                                            <div class="flex items-center gap-2 mb-1">
                                                <p class="font-bold text-xs sm:text-sm text-slate-900 dark:text-slate-100 hover:text-indigo-600 transition-colors truncate">
                                                    {{ $leave->leaveType->name ?? $leave->type }}
                                                </p>
                                            </div>
                                            
                                            <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 text-[11px] sm:text-xs text-slate-600 dark:text-slate-400">
                                                <span class="text-slate-500 dark:text-slate-400">
                                                    {{ $rangeString }} <span class="font-semibold text-slate-700 dark:text-slate-300">({{ $diff }} Hari)</span>
                                                </span>
                                            </div>
                                            
                                            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1.5 flex items-center gap-1">
                                                <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                Diajukan pada: <span class="font-mono text-slate-500 dark:text-slate-400 font-semibold">{{ \Carbon\Carbon::parse($leave->created_at)->translatedFormat('d M Y, H:i') }} WIB</span>
                                            </p>
                                        </div>

                                        <!-- Right Side: Status Badge -->
                                        <div class="flex items-center sm:items-end justify-between sm:justify-center sm:flex-col gap-1.5 shrink-0">
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-lg border flex items-center gap-1.5 tracking-wider {{ $statusClass }}">
                                                <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $leave->status === 'Pending' ? 'bg-amber-500' : ($leave->status === 'Approved' ? 'bg-emerald-500' : 'bg-rose-500') }}"></span>
                                                {{ $leave->status }}
                                            </span>
                                            
                                            @if($leave->notes)
                                                <span class="text-[9px] text-slate-400 dark:text-slate-500 truncate max-w-[140px] italic" title="{{ $leave->notes }}">
                                                    "{{ $leave->notes }}"
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        @else
                            <div class="p-8 text-center text-slate-400">
                                <p class="text-xs">Belum ada riwayat pengajuan izin/cuti.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
        @endif

        <!-- ACTIVITY FEED & JADWAL PIKET -->
        @if($isAdmin || (isset($myPicketSchedules) && $myPicketSchedules->isNotEmpty()))
        <section class="grid grid-cols-1 {{ ($isAdmin && isset($myPicketSchedules) && $myPicketSchedules->isNotEmpty()) ? 'lg:grid-cols-3' : 'lg:grid-cols-1' }} gap-4 sm:gap-5 lg:gap-6">
            @if(isset($myPicketSchedules) && $myPicketSchedules->isNotEmpty())
                <!-- Jadwal Piket Saya Card -->
                <div class="animate-card {{ $isAdmin ? '' : 'lg:col-span-1' }} bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 sm:p-6 shadow-sm transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700 flex flex-col justify-between" x-data="{ openJobs: null }">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50 mb-4 font-nasalization">Jadwal Piket Saya</h3>
                        
                        @if($myPicketToday)
                            <!-- Hari Ini Ada Piket -->
                            <div class="bg-gradient-to-br from-indigo-500/10 via-indigo-600/5 to-transparent dark:from-indigo-950/30 dark:via-indigo-900/10 dark:to-transparent border border-indigo-100 dark:border-indigo-900/40 rounded-2xl p-4 mb-4 relative overflow-hidden">
                                <div class="absolute -right-4 -bottom-4 w-20 h-20 bg-indigo-500/10 rounded-full blur-xl pointer-events-none"></div>
                                
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-indigo-600 dark:bg-indigo-500 text-white flex items-center justify-center shrink-0 shadow-sm shadow-indigo-500/30 mt-0.5">
                                        <i data-lucide="bell-ring" class="w-4 h-4"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-indigo-600 text-white uppercase tracking-wider animate-pulse">TUGAS HARI INI</span>
                                            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">{{ [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'][$myPicketToday->day_of_week] ?? '-' }}</span>
                                        </div>
                                        <h4 class="text-xs font-extrabold text-slate-900 dark:text-slate-100 mt-1 leading-snug break-words pr-2">
                                            {{ $myPicketToday->picketArea->name }}
                                        </h4>
                                        
                                        <div class="flex items-center gap-1.5 font-bold text-indigo-600 dark:text-indigo-400 text-[10px] mt-1.5">
                                            <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                            <span>{{ $myPicketToday->picketArea->duty_hours }} WIB</span>
                                        </div>
                                    </div>
                                </div>

                                @if($myPicketToday->picketArea->jobs)
                                    <div class="mt-3.5 pt-3.5 border-t border-indigo-100/50 dark:border-indigo-900/30">
                                        <p class="text-[9px] font-black text-slate-700 dark:text-slate-350 uppercase tracking-widest mb-1.5">TUPOKSI ANDA:</p>
                                        <div class="space-y-2">
                                            @foreach(explode("\n", $myPicketToday->picketArea->jobs) as $job)
                                                @if(trim($job))
                                                    <div class="flex items-start gap-2 text-[10px] text-slate-655 dark:text-slate-400 leading-relaxed font-medium">
                                                        <div class="w-3.5 h-3.5 rounded-md bg-indigo-50 dark:bg-indigo-955 text-indigo-600 dark:text-indigo-450 flex items-center justify-center shrink-0 shadow-3xs border border-indigo-100/10 mt-0.5">
                                                            <i data-lucide="check" class="w-2.5 h-2.5 stroke-[3]"></i>
                                                        </div>
                                                        <p class="flex-1">{{ trim($job) }}</p>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- List Roster Jadwal Mingguan -->
                        <div class="space-y-2.5">
                            @php
                                $groupedSchedules = $myPicketSchedules->groupBy('picket_area_id');
                            @endphp
                            @forelse($groupedSchedules as $areaId => $schedulesGroup)
                                @php
                                    $area = $schedulesGroup->first()->picketArea;
                                    $daysOfWeek = $schedulesGroup->pluck('day_of_week')->sort()->toArray();
                                    $hasTodayInGroup = in_array($todayDayOfWeek, $daysOfWeek);
                                @endphp
                                <div class="p-3 border rounded-2xl transition-all duration-200 cursor-pointer {{ $hasTodayInGroup ? 'bg-indigo-500/5 dark:bg-indigo-500/5 border-indigo-200 dark:border-indigo-950' : 'bg-slate-50/50 dark:bg-slate-950/30 border-slate-200/50 dark:border-slate-850 hover:bg-slate-50 dark:hover:bg-slate-950' }}"
                                    @click="openJobs = (openJobs === {{ $areaId }} ? null : {{ $areaId }})">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex items-start gap-2.5 min-w-0">
                                            <div class="w-7 h-7 rounded-lg {{ $hasTodayInGroup ? 'bg-indigo-650 text-white shadow-3xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-550 dark:text-slate-400' }} flex items-center justify-center shrink-0 border border-indigo-150/10 mt-0.5">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                                            </div>
                                            <div class="flex flex-col min-w-0">
                                                <span class="font-bold text-[11px] {{ $hasTodayInGroup ? 'text-indigo-950 dark:text-indigo-300' : 'text-slate-800 dark:text-slate-200' }} leading-tight break-words">{{ $area->name }}</span>
                                                <span class="text-[9px] text-slate-500 dark:text-slate-300 mt-0.5">Jam: {{ $area->duty_hours }}</span>
                                                
                                                <div class="flex items-center gap-1.5 flex-wrap mt-2">
                                                    @foreach($daysOfWeek as $dayNum)
                                                        @php
                                                             $isDayToday = ($dayNum === $todayDayOfWeek);
                                                        @endphp
                                                        <span class="px-2 py-0.5 rounded-lg text-[9px] font-bold {{ $isDayToday ? 'bg-indigo-600 text-white shadow-2xs' : 'bg-white dark:bg-slate-900 border border-slate-200/50 dark:border-slate-800 text-slate-700 dark:text-slate-350' }} shrink-0">
                                                            {{ [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'][$dayNum] }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        @if($area->jobs)
                                            <button type="button" class="shrink-0 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors p-1 cursor-pointer border-0 bg-transparent flex items-center justify-center" title="Lihat Tugas/Tupoksi">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform duration-200" :class="openJobs === {{ $areaId }} && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                                            </button>
                                        @endif
                                    </div>
                                    
                                    @if($area->jobs)
                                        <div x-show="openJobs === {{ $areaId }}" @click.stop x-collapse x-cloak class="mt-3 pt-3 border-t border-slate-200/60 dark:border-slate-800/60 text-[10px] text-slate-550 dark:text-slate-400 space-y-2">
                                            <p class="font-black text-slate-700 dark:text-slate-350 uppercase tracking-widest text-[8px]">TUGAS & TUPOKSI:</p>
                                            <div class="space-y-1.5 pl-1">
                                                @foreach(explode("\n", $area->jobs) as $job)
                                                    @if(trim($job))
                                                        <div class="flex items-start gap-1.5 leading-relaxed font-medium">
                                                            <span class="w-1 h-1 rounded-full {{ $hasTodayInGroup ? 'bg-indigo-500' : 'bg-slate-400 dark:bg-slate-600' }} mt-1.5 shrink-0"></span>
                                                            <p>{{ trim($job) }}</p>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                @if(!$myPicketToday)
                                    <div class="text-xs text-slate-500 text-center py-8">
                                        <i data-lucide="calendar" class="w-6 h-6 text-slate-300 dark:text-slate-750 mx-auto mb-2"></i>
                                        Anda tidak memiliki jadwal piket minggu ini.
                                    </div>
                                @endif
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif

            @if($isAdmin)
            <!-- Recent Activity Logs (Admin Only) -->
            <div class="animate-card {{ (isset($myPicketSchedules) && $myPicketSchedules->isNotEmpty()) ? 'lg:col-span-2' : '' }} bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 sm:p-6 shadow-sm transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50 mb-4 font-nasalization">Log Aktivitas Terbaru</h3>
                <div class="space-y-3.5 max-h-[380px] overflow-y-auto pr-1.5 scrollbar-thin">
                    @forelse($activityLogs as $log)
                        <div class="flex items-start justify-between gap-3 py-1 border-b border-slate-50 dark:border-slate-900/60 pb-3 last:border-b-0 last:pb-0">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-7 h-7 rounded-lg {{ $log['icon_color'] }} flex items-center justify-center shrink-0">
                                    <i data-lucide="{{ $log['icon'] }}" class="w-3.5 h-3.5"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate">{{ $log['title'] }}</p>
                                    <p class="text-[10px] text-slate-500 mt-0.5 leading-snug break-words pr-2">{{ $log['description'] }}</p>
                                    <span class="mobile-time text-[9px] text-slate-400 dark:text-slate-500 font-medium block mt-1">{{ $log['time']->diffForHumans() }}</span>
                                </div>
                            </div>
                            <span class="desktop-time text-[10px] text-slate-400 dark:text-slate-500 font-medium shrink-0 mt-0.5">{{ $log['time']->diffForHumans() }}</span>
                        </div>
                    @empty
                        <div class="text-xs text-slate-500 text-center py-6">
                            <i data-lucide="activity" class="w-6 h-6 text-slate-300 dark:text-slate-700 mx-auto mb-2"></i>
                            Belum ada aktivitas terbaru hari ini.
                        </div>
                    @endforelse
                </div>
            </div>
            @endif
        </section>
        @endif

    </div>
</x-admin-layout>
