<x-admin-layout>
    <div class="p-6 space-y-6">


        <!-- GREETING / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 font-nasalization">Dashboard</h2>
                @if($isAdmin)
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Selamat datang kembali! Aktivitas sekolah terpantau kondusif.</p>
                @else
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Selamat datang kembali, {{ auth()->user()->name }}! Pantau performa kehadiran harian Anda disini.</p>
                @endif
            </div>
        </section>

        <!-- STAT CARDS GRID -->
        <section class="grid {{ $isAdmin ? 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6' : 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4' }} gap-4">
            @if($isAdmin)
                <!-- Admin Card 1: Total Siswa -->
                <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Siswa Aktif</p>
                            <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                                <span class="stat-counter" data-target="1248">1248</span>
                            </h3>
                        </div>
                        <div class="p-2 bg-slate-50 dark:bg-slate-900 rounded-lg">
                            <i data-lucide="users" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold">+4.5%</span> dari bulan lalu
                    </div>
                </div>

                <!-- Admin Card 2: Guru & Staf -->
                <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Guru & Staf</p>
                            <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                                @if(isset($employeeCount))
                                    <span class="stat-counter" data-target="{{ $employeeCount }}">{{ $employeeCount }}</span>
                                @else
                                    <span>-</span>
                                @endif
                            </h3>
                        </div>
                        <div class="p-2 bg-slate-50 dark:bg-slate-900 rounded-lg">
                            <i data-lucide="graduation-cap" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold">{{ $employeeAttendancePercent ?? 0 }}%</span> tingkat kehadiran
                    </div>
                </div>

                <!-- Admin Card 2: GPK -->
                <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Guru Pendamping Khusus</p>
                            <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                                @if(isset($gpkCount))
                                    <span class="stat-counter" data-target="{{ $gpkCount }}">{{ $gpkCount }}</span>
                                @else
                                    <span>-</span>
                                @endif
                            </h3>
                        </div>
                        <div class="p-2 bg-slate-50 dark:bg-slate-900 rounded-lg">
                            <i data-lucide="sun" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold">{{ $gpkAttendancePercent ?? 0 }}%</span> tingkat kehadiran
                    </div>
                </div>

                <!-- Admin Card 2: GPQ -->
                <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Guru Pengajar Qur'an</p>
                            <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                                @if(isset($gpqCount))
                                    <span class="stat-counter" data-target="{{ $gpqCount }}">{{ $gpqCount }}</span>
                                @else
                                    <span>-</span>
                                @endif
                            </h3>
                        </div>
                        <div class="p-2 bg-slate-50 dark:bg-slate-900 rounded-lg">
                            <i data-lucide="book-open" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold">{{ $gpqAttendancePercent ?? 0 }}%</span> tingkat kehadiran
                    </div>
                </div>

                <!-- Admin Card 3: Total Rombel / Kelas -->
                <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Rombel / Kelas</p>
                            <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                                <span class="stat-counter" data-target="36">36</span>
                            </h3>
                        </div>
                        <div class="p-2 bg-slate-50 dark:bg-slate-900 rounded-lg">
                            <i data-lucide="layout-grid" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                        Semua kelas terisi hari ini
                    </div>
                </div>

                <!-- Admin Card 4: Presensi Hari Ini -->
                <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Presensi Hari Ini</p>
                            <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                                <span class="stat-counter" data-target="{{ $todayOverallPercent ?? 0 }}">{{ $todayOverallPercent ?? 0 }}</span>%
                            </h3>
                        </div>
                        <div class="p-2 bg-slate-50 dark:bg-slate-900 rounded-lg">
                            <i data-lucide="clock" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                        @if(($diffPercent ?? 0) > 0)
                            <span class="text-emerald-600 dark:text-emerald-400 font-bold">+{{ $diffPercent }}%</span> dari kemarin
                        @elseif(($diffPercent ?? 0) < 0)
                            <span class="text-red-600 dark:text-red-400 font-bold">{{ $diffPercent }}%</span> dari kemarin
                        @else
                            <span class="text-slate-500 dark:text-slate-400 font-bold">Sama seperti kemarin</span>
                        @endif
                    </div>
                </div>
            @else
                <!-- Pegawai Card 1: Kehadiran Bulan Ini -->
                <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kehadiran Bulan Ini</p>
                            <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                                <span>{{ $myReport['total_present'] ?? 0 }}</span> Hari
                            </h3>
                        </div>
                        <div class="p-2 bg-slate-50 dark:bg-slate-900 rounded-lg">
                            <i data-lucide="calendar" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                        Siklus aktif terhitung hadir
                    </div>
                </div>

                <!-- Pegawai Card 2: Menit Terlambat -->
                <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Keterlambatan</p>
                            <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                                <span>{{ $totalLateDays ?? 0 }}</span> Hari
                            </h3>
                        </div>
                        <div class="p-2 bg-slate-50 dark:bg-slate-900 rounded-lg">
                            <i data-lucide="hourglass" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-slate-500 dark:text-slate-400">
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
                <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Izin & Cuti</p>
                            <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                                <span>{{ $totalLeavesThisYear }}</span> Hari
                            </h3>
                        </div>
                        <div class="p-2 bg-slate-50 dark:bg-slate-900 rounded-lg">
                            <i data-lucide="calendar-days" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                        Akumulasi persetujuan tahun ini
                    </div>
                </div>

                <!-- Pegawai Card 4: Estimasi Bonus -->
                <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Estimasi Bonus</p>
                            <h3 class="text-2xl font-bold tracking-tight text-emerald-600 dark:text-emerald-400 mt-1">
                                Rp {{ number_format($myReport['bonus_nominal'] ?? 0, 0, ',', '.') }}
                            </h3>
                        </div>
                        <div class="p-2 bg-slate-50 dark:bg-slate-900 rounded-lg">
                            <i data-lucide="award" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                        Akumulasi bonus bulan ini
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
                                <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50">Ikhtisar Kehadiran Bulanan</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Tingkat kehadiran siswa pada 7 bulan terakhir</p>
                            @else
                                <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50 font-nasalization">Riwayat Absensi Harian</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Tren waktu kedatangan (jam masuk) Anda pada bulan ini</p>
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
                        <div class="mb-4 p-3.5 bg-slate-50 dark:bg-emerald-600 border border-slate-200 dark:border-slate-800/80 rounded-lg text-sm">
                            <div class="flex items-center gap-1.5 font-semibold text-slate-900 dark:text-slate-200 mb-3">
                                <i data-lucide="info" class="w-3.5 h-3.5 text-indigo-500 shrink-0"></i>
                                <span>Informasi Shift Kerja Bulan Ini:</span>
                            </div>
                            
                            <div class="grid grid-cols-1 {{ count($myActiveShifts) > 1 && !empty($myCalendarDays) ? 'md:grid-cols-3' : '' }} gap-4">
                                <!-- Left Column: Shift List (List Jam Kerja) -->
                                <div class="{{ count($myActiveShifts) > 1 && !empty($myCalendarDays) ? 'md:col-span-1' : '' }} bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 rounded-md p-3 shadow-sm flex flex-col justify-between">
                                    <div>
                                        <p class="text-sm font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-3">Daftar Jam Kerja</p>
                                        <div class="space-y-3">
                                            @foreach($myActiveShifts as $index => $shift)
                                                <div class="{{ $index > 0 ? 'pt-3 border-t border-slate-100 dark:border-slate-800/50' : '' }}">
                                                    @php
                                                        $code = '';
                                                        if (stripos($shift['name'], 'malam') !== false) {
                                                            $code = 'M';
                                                        } elseif (stripos($shift['name'], 'pagi') !== false) {
                                                            $code = 'P';
                                                        } elseif (stripos($shift['name'], 'siang') !== false) {
                                                            $code = 'S';
                                                        } else {
                                                            $code = strtoupper(substr($shift['name'], 0, 1));
                                                        }
                                                    @endphp
                                                    <div class="font-bold text-slate-700 dark:text-slate-200 mb-1 flex items-center justify-between">
                                                        <span>{{ $shift['name'] }} ({{ $code }})</span>
                                                    </div>
                                                    @if(!empty($shift['description']))
                                                        <div class="text-xs text-slate-450 dark:text-slate-500 mb-1.5 leading-snug">{{ $shift['description'] }}</div>
                                                    @endif
                                                    
                                                    @php
                                                        $daysName = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 0 => 'Minggu'];
                                                        $groupedDetails = [];
                                                        foreach($shift['details'] as $dt) {
                                                            if(!$dt['is_off']) {
                                                                $timeRange = $dt['start_time'] . ' - ' . $dt['end_time'];
                                                                $groupedDetails[$timeRange][] = $dt['day_of_week'];
                                                            }
                                                        }
                                                    @endphp
                                                    
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
                                                                $daysStr = implode(', ', $dayLabels);
                                                            @endphp
                                                            <div class="flex flex-col gap-0.5 pb-1 border-b border-slate-50 dark:border-slate-800/40 last:border-0 last:pb-0">
                                                                <span class="text-xs text-slate-500 dark:text-slate-400 font-medium leading-relaxed">{{ $daysStr }}</span>
                                                                <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 font-mono">{{ $timeRange }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Right Column: Calendar Roster (Kalender Jadwal Kerja Roster) -->
                                @if(count($myActiveShifts) > 1 && !empty($myCalendarDays))
                                    <div class="md:col-span-2 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/50 rounded-md p-3 shadow-sm flex flex-col justify-between">
                                        <div>
                                            <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-3">Kalender Jadwal Anda Bulan Ini</p>
                                            
                                            <div class="max-w-xs sm:max-w-sm">
                                                <!-- Calendar Grid Header -->
                                                <div class="grid grid-cols-7 gap-1 text-center font-bold text-[9px] text-slate-400 dark:text-slate-500 uppercase mb-1.5">
                                                    <div>Sen</div>
                                                    <div>Sel</div>
                                                    <div>Rab</div>
                                                    <div>Kam</div>
                                                    <div>Jum</div>
                                                    <div>Sab</div>
                                                    <div>Min</div>
                                                </div>
                                                
                                                <!-- Calendar Days Grid -->
                                                <div class="grid grid-cols-7 gap-1 items-start">
                                                    @foreach($myCalendarDays as $day)
                                                        @if($day['is_empty'])
                                                            <div class="aspect-square bg-slate-50/30 dark:bg-slate-900/10 rounded-md" style="aspect-ratio: 1 / 1;"></div>
                                                        @else
                                                            @php
                                                                $typeClass = 'bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500';
                                                                switch($day['type'] ?? 'default') {
                                                                    case 'malam':
                                                                        $typeClass = 'bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400';
                                                                        break;
                                                                    case 'pagi':
                                                                        $typeClass = 'bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400';
                                                                        break;
                                                                    case 'siang':
                                                                        $typeClass = 'bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400';
                                                                        break;
                                                                    case 'other':
                                                                        $typeClass = 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400';
                                                                        break;
                                                                    case 'off':
                                                                        $typeClass = 'bg-slate-100 dark:bg-slate-800/50 text-slate-400 dark:text-slate-500';
                                                                        break;
                                                                    case 'sakit':
                                                                        $typeClass = 'bg-red-50 dark:bg-red-950/60 text-red-600 dark:text-red-400';
                                                                        break;
                                                                    case 'izin':
                                                                        $typeClass = 'bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400';
                                                                        break;
                                                                    case 'cuti':
                                                                        $typeClass = 'bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400';
                                                                        break;
                                                                }
                                                            @endphp
                                                            <div class="calendar-cell aspect-square flex flex-col justify-between p-1 border border-slate-100 dark:border-slate-800/60 rounded-md bg-white dark:bg-slate-900 shadow-sm relative group outline-none cursor-pointer" style="aspect-ratio: 1 / 1;" tabindex="0">
                                                                <!-- Date Number -->
                                                                <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 leading-none">{{ $day['day_num'] }}</span>
                                                                
                                                                <!-- Shift Label Badge -->
                                                                <span class="text-[8px] font-bold text-center py-0.5 rounded {{ $typeClass }} block w-full truncate leading-none">
                                                                    {{ $day['short_label'] }}
                                                                </span>
                                                                
                                                                <!-- Tooltip on hover/tap -->
                                                                <div class="calendar-tooltip absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 w-32 hidden group-hover:block group-focus:block z-50 pointer-events-none">
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
                                        
                                        <!-- Footer Note -->
                                        <div class="mt-4 pt-2 border-t border-slate-100 dark:border-slate-800/50 flex items-center gap-1.5 text-[9px] text-slate-500 dark:text-slate-400 leading-normal">
                                            <i data-lucide="info" class="w-3 h-3 text-amber-500 shrink-0"></i>
                                            <span><strong>Catatan:</strong> Pengajuan tukar shift minimal dilakukan 1 hari sebelumnya.</span>
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
                            
                            if (!$isAdmin && count($chartPoints) > 0) {
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
                                        <!-- Area path -->
                                        <path d="M 0,150 L 0,110 L 80,120 L 160,85 L 240,95 L 320,60 L 400,45 L 500,30 L 500,150 Z" 
                                              fill="url(#grad-area)" opacity="0.15"></path>
                                        
                                        <!-- Animated line path -->
                                        <path d="M 0,110 L 80,120 L 160,85 L 240,95 L 320,60 L 400,45 L 500,30" 
                                              fill="none" stroke="currentColor" class="text-slate-800 dark:text-slate-100" stroke-width="2" stroke-linecap="round"></path>
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
                                    <span class="w-[30px] text-center">Jan</span>
                                    <span class="w-[30px] text-center">Feb</span>
                                    <span class="w-[30px] text-center">Mar</span>
                                    <span class="w-[30px] text-center">Apr</span>
                                    <span class="w-[30px] text-center">Mei</span>
                                    <span class="w-[30px] text-center">Jun</span>
                                    <span class="w-[30px] text-center">Jul</span>
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

        <!-- QUICK ACTIONS & ACTIVITY FEED -->
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Quick Actions Grid -->
            <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 sm:p-6 shadow-sm transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50 mb-4">Aksi Cepat</h3>
                <div class="grid grid-cols-2 gap-2">
                    @if($isAdmin)
                        <button onclick="window.location='{{ route('coming-soon') }}'" class="flex flex-col items-center justify-center p-3 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-900 rounded-lg group transition-all duration-100 cursor-pointer">
                            <i data-lucide="user-plus" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover:scale-105 transition-transform"></i>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300 mt-1.5">Tambah Siswa</span>
                        </button>
                        <button onclick="window.location='{{ route('coming-soon') }}'" class="flex flex-col items-center justify-center p-3 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-900 rounded-lg group transition-all duration-100 cursor-pointer">
                            <i data-lucide="clipboard-list" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover:scale-105 transition-transform"></i>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300 mt-1.5">Input Nilai</span>
                        </button>
                        <button onclick="window.location='{{ route('coming-soon') }}'" class="flex flex-col items-center justify-center p-3 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-900 rounded-lg group transition-all duration-100 cursor-pointer">
                            <i data-lucide="send" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover:scale-105 transition-transform"></i>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300 mt-1.5">Kirim Pesan</span>
                        </button>
                        <button onclick="window.location='{{ route('coming-soon') }}'" class="flex flex-col items-center justify-center p-3 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-900 rounded-lg group transition-all duration-100 cursor-pointer">
                            <i data-lucide="receipt" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover:scale-105 transition-transform"></i>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300 mt-1.5">Cek SPP</span>
                        </button>
                    @else
                        <button onclick="window.location='{{ route('my-leaves.index') }}'" class="flex flex-col items-center justify-center p-3 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-900 rounded-lg group transition-all duration-100 cursor-pointer">
                            <i data-lucide="file-text" class="w-4 h-4 text-indigo-600 dark:text-indigo-400 group-hover:scale-105 transition-transform"></i>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300 mt-1.5">Ajukan Cuti/Izin</span>
                        </button>
                        <button onclick="window.location='{{ route('attendances.index') }}'" class="flex flex-col items-center justify-center p-3 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-900 rounded-lg group transition-all duration-100 cursor-pointer">
                            <i data-lucide="calendar-check" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover:scale-105 transition-transform"></i>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300 mt-1.5">Data Absensi</span>
                        </button>
                        <button onclick="window.location='{{ route('my-employee-profile.edit') }}'" class="flex flex-col items-center justify-center p-3 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-900 rounded-lg group transition-all duration-100 cursor-pointer">
                            <i data-lucide="user" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover:scale-105 transition-transform"></i>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300 mt-1.5">Profile</span>
                        </button>
                        <button onclick="window.location='{{ route('payslips.index') }}'" class="flex flex-col items-center justify-center p-3 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-900 rounded-lg group transition-all duration-100 cursor-pointer">
                            <i data-lucide="receipt" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover:scale-105 transition-transform"></i>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300 mt-1.5">Lihat Slip Gaji</span>
                        </button>
                    @endif
                </div>
                

            </div>

            <!-- Jadwal Piket Saya Card (Hanya untuk Pegawai Biasa di SD) -->
            @if(!$isAdmin && config('app.school_unit') === 'sd' && $myPicketSchedules->isNotEmpty())
            <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 sm:p-6 shadow-sm transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700 flex flex-col justify-between" x-data="{ openJobs: null }">
                <div>
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50 mb-4 font-nasalization">Jadwal Piket Saya</h3>
                    
                    @if($myPicketToday)
                        <!-- Hari Ini Ada Piket -->
                        <div class="bg-gradient-to-br from-indigo-500/10 via-indigo-600/5 to-transparent dark:from-indigo-950/30 dark:via-indigo-900/10 dark:to-transparent border border-indigo-100 dark:border-indigo-900/40 rounded-2xl p-4 mb-4 relative overflow-hidden">
                            <!-- Subtle decorative gradient circle -->
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
                        @forelse($myPicketSchedules as $sched)
                            @php
                                $isToday = ($sched->day_of_week === $todayDayOfWeek);
                            @endphp
                            <div class="p-3 border rounded-2xl transition-all duration-200 {{ $isToday ? 'bg-indigo-500/5 dark:bg-indigo-500/5 border-indigo-200 dark:border-indigo-950' : 'bg-slate-50/50 dark:bg-slate-950/30 border-slate-200/50 dark:border-slate-850 hover:bg-slate-50 dark:hover:bg-slate-950' }}">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <span class="px-2 py-1 rounded-xl {{ $isToday ? 'bg-indigo-600 text-white font-extrabold shadow-sm' : 'bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 font-bold' }} text-[10px] shrink-0">
                                            {{ [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'][$sched->day_of_week] ?? '-' }}
                                        </span>
                                        <div class="flex flex-col min-w-0">
                                            <span class="font-bold text-[11px] {{ $isToday ? 'text-indigo-950 dark:text-indigo-300' : 'text-slate-800 dark:text-slate-200' }} leading-tight break-words">{{ $sched->picketArea->name }}</span>
                                            <span class="text-[9px] text-slate-500 dark:text-slate-300 mt-0.5">Jam: {{ $sched->picketArea->duty_hours }}</span>
                                        </div>
                                    </div>
                                    @if($sched->picketArea->jobs)
                                        <button type="button" @click="openJobs = (openJobs === {{ $sched->id }} ? null : {{ $sched->id }})" class="shrink-0 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors p-1 cursor-pointer border-0 bg-transparent flex items-center justify-center" title="Lihat Tugas/Tupoksi">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform duration-200" :class="openJobs === {{ $sched->id }} && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                                        </button>
                                    @endif
                                </div>
                                
                                <!-- Expandable Jobs/Tupoksi List -->
                                @if($sched->picketArea->jobs)
                                    <div x-show="openJobs === {{ $sched->id }}" x-collapse x-cloak class="mt-3 pt-3 border-t border-slate-200/60 dark:border-slate-800/60 text-[10px] text-slate-550 dark:text-slate-400 space-y-2">
                                        <p class="font-black text-slate-700 dark:text-slate-300 uppercase tracking-widest text-[8px]">TUGAS & TUPOKSI:</p>
                                        <div class="space-y-1.5 pl-1">
                                            @foreach(explode("\n", $sched->picketArea->jobs) as $job)
                                                @if(trim($job))
                                                    <div class="flex items-start gap-1.5 leading-relaxed font-medium">
                                                        <span class="w-1 h-1 rounded-full {{ $isToday ? 'bg-indigo-500' : 'bg-slate-400 dark:bg-slate-600' }} mt-1.5 shrink-0"></span>
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

            <!-- Recent Activity Logs -->
            <div class="animate-card {{ ($isAdmin || config('app.school_unit') !== 'sd' || $myPicketSchedules->isEmpty()) ? 'lg:col-span-2' : 'lg:col-span-1' }} bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 sm:p-6 shadow-sm transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                @if($isAdmin)
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50 mb-4">Log Aktivitas Terbaru</h3>
                     <div class="space-y-3.5">
                        <div class="flex items-center justify-between gap-3 py-1 border-b border-slate-50 dark:border-slate-900/60 pb-3 flex-wrap sm:flex-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-lg bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-600 dark:text-slate-400">
                                    <i data-lucide="key" class="w-3.5 h-3.5"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-slate-800 dark:text-slate-200">Login Wali Kelas XI-IPA</p>
                                    <p class="text-xs text-slate-500">Guru: Drs. Eko Prasetyo</p>
                                </div>
                            </div>
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium shrink-0 mt-0.5">10 mnt yang lalu</span>
                        </div>
                        <div class="flex items-start justify-between gap-3 py-1 border-b border-slate-50 dark:border-slate-900/60 pb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-lg bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-600 dark:text-slate-400">
                                    <i data-lucide="file-plus" class="w-3.5 h-3.5"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-slate-800 dark:text-slate-200">Unggah Materi Fisika Kuantum</p>
                                    <p class="text-xs text-slate-500">Kelas: XII-IPA</p>
                                </div>
                            </div>
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium shrink-0 mt-0.5">24 mnt yang lalu</span>
                        </div>
                        <div class="flex items-start justify-between gap-3 py-1">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-lg bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-600 dark:text-slate-400">
                                    <i data-lucide="user-check" class="w-3.5 h-3.5"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-slate-800 dark:text-slate-200">Verifikasi Berkas Pendaftaran</p>
                                    <p class="text-xs text-slate-500">Gelombang 2 SANS Malang</p>
                                </div>
                            </div>
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium shrink-0 mt-0.5">45 mnt yang lalu</span>
                        </div>
                    </div>
                @else
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50 mb-4 font-nasalization">Aktivitas Saya (Pengajuan Terakhir)</h3>
                     <div class="space-y-3.5">
                        @forelse($myRecentLeaves as $leave)
                            <div class="flex items-start justify-between gap-3 py-1 border-b border-slate-50 dark:border-slate-900/60 pb-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-7 h-7 rounded-lg bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-600 dark:text-slate-400 shrink-0">
                                        <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate">Pengajuan Izin / Cuti ({{ $leave->type }})</p>
                                        <!-- Time on mobile -->
                                        <span class="mobile-time text-[10px] text-slate-400 dark:text-slate-500 font-medium block mt-0.5">{{ $leave->created_at->translatedFormat('d M Y, H:i') }}</span>
                                    </div>
                                </div>
                                <!-- Time on desktop -->
                                <span class="desktop-time text-[10px] text-slate-400 dark:text-slate-500 font-medium shrink-0 mt-0.5">{{ $leave->created_at->translatedFormat('d M Y, H:i') }}</span>
                            </div>
                        @empty
                            <div class="text-xs text-slate-500 text-center py-4">Belum ada riwayat aktivitas pengajuan cuti/izin.</div>
                        @endforelse
                    </div>
                @endif
            </div>
        </section>

    </div>
</x-admin-layout>
