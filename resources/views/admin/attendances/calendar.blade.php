<x-admin-layout>
    <style>
        .calendar-header-container {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .calendar-header-left {
            order: 1;
        }
        .calendar-header-center {
            order: 2;
            text-align: center;
        }
        .calendar-header-right {
            order: 3;
        }
        @media (min-width: 768px) {
            .calendar-header-container {
                display: grid !important;
                grid-template-columns: 1fr auto 1fr !important;
                align-items: center !important;
                gap: 16px !important;
            }
            .calendar-header-left {
                order: 1 !important;
                justify-self: start !important;
            }
            .calendar-header-center {
                order: 2 !important;
                justify-self: center !important;
            }
            .calendar-header-right {
                order: 3 !important;
                justify-self: end !important;
            }
        }
    </style>
    <div class="p-6 space-y-6 w-full relative" x-data="{
        tooltipTimer: null,
        tooltip: {
            show: false,
            x: 0,
            y: 0,
            placement: 'top',
            arrowOffset: 0,
            data: null,
            activeCell: '',
        },
        showTooltip(e, data) {
            if (!data) return;
            if (this.tooltipTimer) clearTimeout(this.tooltipTimer);
            const targetRect = e.currentTarget.getBoundingClientRect();
            this.tooltipTimer = setTimeout(() => {
                this.renderTooltip(targetRect, data);
            }, 75);
        },
        showTooltipInstant(e, data) {
            if (!data) return;
            if (this.tooltipTimer) clearTimeout(this.tooltipTimer);
            const targetRect = e.currentTarget.getBoundingClientRect();
            this.renderTooltip(targetRect, data);
        },
        renderTooltip(targetRect, data) {
            this.tooltip.data = data;
            const tooltipWidth = 285;
            const tooltipHeight = 285;
            const padding = 12;
            
            const rawX = targetRect.left + (targetRect.width / 2);
            const minX = (tooltipWidth / 2) + padding;
            const maxX = window.innerWidth - (tooltipWidth / 2) - padding;
            
            const clampedX = Math.max(minX, Math.min(rawX, maxX));
            this.tooltip.x = clampedX;
            
            // Calculate arrow offset relative to clamped tooltip center
            const rawArrowOffset = rawX - clampedX;
            const maxArrowOffset = (tooltipWidth / 2) - 24;
            this.tooltip.arrowOffset = Math.max(-maxArrowOffset, Math.min(maxArrowOffset, rawArrowOffset));
            
            // Check if cell is too close to top of viewport
            if (targetRect.top < tooltipHeight + 20) {
                this.tooltip.y = targetRect.bottom + 8;
                this.tooltip.placement = 'bottom';
            } else {
                this.tooltip.y = targetRect.top - 8;
                this.tooltip.placement = 'top';
            }
            
            this.tooltip.show = true;
        },
        hideTooltip() {
            if (this.tooltipTimer) clearTimeout(this.tooltipTimer);
            this.tooltip.show = false;
            this.tooltip.activeCell = '';
        },
        toggleTooltip(e, data) {
            const key = (data?.employee?.name || '') + '_' + (data?.date_display || '');
            if (this.tooltip.show && this.tooltip.activeCell === key) {
                this.hideTooltip();
            } else {
                this.showTooltipInstant(e, data);
                this.tooltip.activeCell = key;
            }
        }
    }" x-ref="container" @click="hideTooltip()">
        <!-- DATA KEHADIRAN / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-2 sm:gap-3">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-base sm:text-lg font-bold tracking-tight text-slate-900 dark:text-slate-50 font-nasalization">Data Kehadiran</h2>
                <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 font-medium">Memantau waktu kedatangan, kepulangan, dan estimasi uang kehadiran ketepatan waktu Anda.</p>
            </div>
            <div class="flex items-center flex-wrap gap-2">
                <a href="{{ route('attendances.index', array_merge(request()->query(), ['refresh' => 1])) }}" 
                   x-data="{ syncing: false }" 
                   @click="syncing = true" 
                   :class="{ 'opacity-75 pointer-events-none': syncing }"
                   class="h-9 px-3.5 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 font-semibold text-xs rounded-xl shadow-sm transition-all cursor-pointer whitespace-nowrap flex items-center gap-2" 
                   title="Sinkronkan data terbaru langsung dari HRD / Mesin Absensi">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" :class="{ 'animate-spin': syncing }"></i>
                    <span x-text="syncing ? 'Menyinkronkan...' : 'Sinkronkan Data'">Sinkronkan Data</span>
                </a>

                @php
                    $loggedEmployeeName = $reports->first()['employee']['name'] ?? auth()->user()->employee?->name ?? auth()->user()->name ?? 'Pegawai';
                    $loggedEmployeeNip = $reports->first()['employee']['nuptk_nip_nik'] ?? auth()->user()->employee?->nip ?? auth()->user()->employee?->nuptk ?? null;
                @endphp
                <div class="h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl flex items-center gap-2 shadow-sm text-xs" title="Nama Pegawai: {{ $loggedEmployeeName }}{{ $loggedEmployeeNip ? ' (' . $loggedEmployeeNip . ')' : '' }}">
                    <div class="w-5 h-5 rounded-full bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                        <i data-lucide="user" class="w-3 h-3"></i>
                    </div>
                    <span class="font-bold text-slate-800 dark:text-slate-200 truncate max-w-[170px] sm:max-w-[260px]">{{ $loggedEmployeeName }}</span>
                    @if($loggedEmployeeNip)
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-mono hidden sm:inline">({{ $loggedEmployeeNip }})</span>
                    @endif
                </div>
            </div>
        </section>

        @php
            $start = \Carbon\Carbon::parse($startDate);
            $end = \Carbon\Carbon::parse($endDate);
            $report = $reports->first(); 
            $dailyDetails = $report['daily_details'] ?? [];
            
            \Carbon\Carbon::setLocale('id');
            $selectedMonth = \Carbon\Carbon::parse(($month ?? date('Y-m')) . '-01');
            $cutoffDateVal = (int) \App\Models\Setting::get('payroll_cutoff_date', 26);
            $cutOffStart = $selectedMonth->copy()->subMonthNoOverflow()->setDay($cutoffDateVal + 1)->format('Y-m-d');
            $cutOffEnd = $selectedMonth->copy()->setDay($cutoffDateVal)->format('Y-m-d');
            
            $empName = $report['employee']['name'] ?? ($loggedEmployeeName ?? 'Pegawai');
            $unitName = $report['employee']['unit']['name'] 
                ?? ($report['employee']['unit_name'] 
                ?? (auth()->user()->employee?->unit?->name 
                ?? (auth()->user()->unit?->name ?? 'SANS')));
            $position = $report['employee']['position'] 
                ?? ($report['employee']['subject_position'] 
                ?? (auth()->user()->employee?->position ?? '-'));
            
            $prevMonthUrl = route('attendances.index', array_merge(request()->query(), ['month' => $selectedMonth->copy()->subMonthNoOverflow()->format('Y-m')]));
            $nextMonthUrl = route('attendances.index', array_merge(request()->query(), ['month' => $selectedMonth->copy()->addMonthNoOverflow()->format('Y-m')]));
            
            $cutoffBonus = 0;
            $countHadir = 0;
            $countTelat = 0;
            $countIzin = 0;
            $countSakit = 0;
            $countCuti = 0;
            $countAlfa = 0;

            $myPicketSchedules = collect();
            if (auth()->check() && auth()->user()->employee_id) {
                $myPicketSchedules = \App\Models\PicketSchedule::with('picketArea')
                    ->where('employee_id', auth()->user()->employee_id)
                    ->get();
            }
            
            foreach ($dailyDetails as $dateStr => $detail) {
                if ($dateStr >= $cutOffStart && $dateStr <= $cutOffEnd) {
                    $cutoffBonus += $detail['calculated_bonus'] ?? 0;
                    $st = $detail['status'] ?? '';
                    if ($st === 'Hadir' || $st === 'Reward Libur' || !empty($detail['is_reward'])) {
                        if (!empty($detail['is_late'])) {
                            $countTelat++;
                        } else {
                            $countHadir++;
                        }
                    } elseif ($st === 'Izin') {
                        $countIzin++;
                    } elseif ($st === 'Sakit') {
                        $countSakit++;
                    } elseif ($st === 'Cuti') {
                        $countCuti++;
                    } elseif ($st === 'Alfa' || $st === 'Absent') {
                        $countAlfa++;
                    }
                }
            }
            $countTotalIzin = $countIzin + $countSakit + $countCuti;

            $calendarStart = $selectedMonth->copy()->startOfMonth();
            $months = [
                $calendarStart->copy()->subMonthNoOverflow(),
                $calendarStart->copy(),
            ];

            $buildMonthGrid = function (\Carbon\Carbon $monthStart) {
                $monthEnd = $monthStart->copy()->endOfMonth();
                $firstDayOfWeek = $monthStart->dayOfWeek;

                $paddingStart = [];
                for ($i = 0; $i < $firstDayOfWeek; $i++) {
                    $paddingStart[] = $monthStart->copy()->subDays($firstDayOfWeek - $i);
                }

                $dates = [];
                $curr = $monthStart->copy();
                while ($curr <= $monthEnd) {
                    $dates[] = $curr->copy();
                    $curr->addDay();
                }

                $totalCells = count($paddingStart) + count($dates);
                $paddingEndCount = (7 - ($totalCells % 7)) % 7;
                $paddingEnd = [];
                $currEnd = $monthEnd->copy()->addDay();
                for ($i = 0; $i < $paddingEndCount; $i++) {
                    $paddingEnd[] = $currEnd->copy();
                    $currEnd->addDay();
                }

                return [
                    'paddingStart' => $paddingStart,
                    'dates' => $dates,
                    'paddingEnd' => $paddingEnd,
                ];
            };
        @endphp

        <!-- MINIMAL CALENDAR -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-[18px] shadow-sm w-full p-4 sm:p-5">
                <div class="calendar-header-container pb-4 border-b border-slate-200 dark:border-slate-800">
                    <!-- Kiri: Total Uang Kehadiran Periode Ini -->
                    <div class="calendar-header-left flex items-center gap-3">
                        <span class="w-10 h-10 rounded-xl bg-emerald-500/10 dark:bg-emerald-500/15 border border-emerald-500/20 flex items-center justify-center shrink-0">
                            <i data-lucide="banknote" class="w-5 h-5 text-emerald-600 dark:text-emerald-400"></i>
                        </span>
                        <div class="text-left">
                            <span class="block text-base sm:text-lg font-extrabold text-slate-900 dark:text-slate-50 leading-tight">
                                Rp {{ number_format($cutoffBonus, 0, ',', '.') }}
                            </span>
                            <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-100/80 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300">
                                    Total Uang Kehadiran Periode Ini
                                </span>
                                <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500">
                                    ({{ $cutoffDateVal + 1 }} {{ $selectedMonth->copy()->subMonthNoOverflow()->translatedFormat('M') }} - {{ $cutoffDateVal }} {{ $selectedMonth->translatedFormat('M Y') }})
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tengah: Filter Periode dengan Navigasi Cepat -->
                    <div class="calendar-header-center flex flex-col items-center">
                        <div class="flex items-center gap-1.5 w-full sm:w-auto justify-center">
                            <a href="{{ $prevMonthUrl }}" class="h-9 w-9 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 transition-colors" title="Bulan Sebelumnya">
                                <i data-lucide="chevron-left" class="w-4 h-4"></i>
                            </a>
                            
                            <form method="GET" action="{{ route('attendances.index') }}" class="m-0">
                                <input type="month" name="month" lang="id-ID" value="{{ $month }}" onchange="this.form.submit()" class="h-9 px-3 text-xs sm:text-sm font-bold bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-300 dark:focus:ring-slate-700 cursor-pointer text-center">
                            </form>
                            
                            <a href="{{ $nextMonthUrl }}" class="h-9 w-9 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 transition-colors" title="Bulan Berikutnya">
                                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                            </a>
                        </div>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 text-center">Pilih periode penggajian</p>
                    </div>
                    
                    <!-- Kanan: Quick Kehadiran Summary Badges -->
                    <div class="calendar-header-right flex items-center md:justify-end gap-1.5 flex-wrap">
                        <div class="flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200/50 dark:border-emerald-800/40 text-[11px] font-bold text-emerald-700 dark:text-emerald-400" title="Hadir Tepat Waktu">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>{{ $countHadir }} Hadir</span>
                        </div>
                        
                        @if($countTelat > 0)
                        <div class="flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 dark:bg-amber-950/30 border border-amber-200/50 dark:border-amber-800/40 text-[11px] font-bold text-amber-700 dark:text-amber-400" title="Terlambat">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <span>{{ $countTelat }} Telat</span>
                        </div>
                        @endif

                        @if($countTotalIzin > 0)
                        <div class="flex items-center gap-1 px-2.5 py-1 rounded-lg bg-purple-50 dark:bg-purple-950/30 border border-purple-200/50 dark:border-purple-800/40 text-[11px] font-bold text-purple-700 dark:text-purple-400" title="Izin / Sakit / Cuti">
                            <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                            <span>{{ $countTotalIzin }} Izin</span>
                        </div>
                        @endif

                        @if($countAlfa > 0)
                        <div class="flex items-center gap-1 px-2.5 py-1 rounded-lg bg-rose-50 dark:bg-rose-950/30 border border-rose-200/50 dark:border-rose-800/40 text-[11px] font-bold text-rose-700 dark:text-rose-400" title="Tanpa Keterangan">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            <span>{{ $countAlfa }} Alfa</span>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="mt-3 overflow-hidden rounded-[18px] border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950">
                    <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x lg:divide-slate-200 dark:lg:divide-slate-800">
                    @foreach($months as $monthIdx => $monthStart)
                        @php
                            $grid = $buildMonthGrid($monthStart);
                            $monthName = $monthStart->translatedFormat('F');
                            $monthYear = $monthStart->format('Y');
                        @endphp

                        <div class="p-3.5 sm:p-4 {{ $loop->first ? 'lg:border-r lg:border-slate-200 dark:lg:border-slate-800' : '' }}">
                            <div class="flex items-center justify-between gap-3 mb-3.5">
                                <span class="text-[14px] sm:text-[16px] font-bold tracking-[-0.01em] text-slate-900 dark:text-slate-50">{{ $monthName }} {{ $monthYear }}</span>
                                @if($monthIdx === 0)
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 dark:bg-slate-900 dark:text-slate-400 border border-slate-200/60 dark:border-slate-800">Cut-off mulai tgl {{ $cutoffDateVal + 1 }}</span>
                                @else
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/40">Cut-off s/d tgl {{ $cutoffDateVal }}</span>
                                @endif
                            </div>

                            <div class="grid grid-cols-[repeat(7,minmax(0,1fr))] pb-1.5">
                                @foreach([
                                    ['name' => 'Su', 'color' => 'text-slate-400 dark:text-slate-500'],
                                    ['name' => 'Mo', 'color' => 'text-slate-400 dark:text-slate-500'],
                                    ['name' => 'Tu', 'color' => 'text-slate-400 dark:text-slate-500'],
                                    ['name' => 'We', 'color' => 'text-slate-400 dark:text-slate-500'],
                                    ['name' => 'Th', 'color' => 'text-slate-400 dark:text-slate-500'],
                                    ['name' => 'Fr', 'color' => 'text-slate-400 dark:text-slate-500'],
                                    ['name' => 'Sa', 'color' => 'text-slate-400 dark:text-slate-500']
                                ] as $day)
                                    <div class="text-center">
                                        <span class="text-[10px] sm:text-[11px] font-medium uppercase tracking-[0.08em] {{ $day['color'] }}">{{ $day['name'] }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="grid grid-cols-[repeat(7,minmax(0,1fr))] gap-0">
                                @foreach($grid['paddingStart'] as $pDate)
                                    <div class="min-h-[28px] sm:min-h-[34px] px-0.5 sm:px-1"></div>
                                @endforeach

                                @foreach($grid['dates'] as $date)
                                    @php
                                        $dateStr = $date->format('Y-m-d');
                                        $isToday = $date->isToday();
                                        $isSunday = $date->isSunday();
                                        $isFuture = $dateStr > date('Y-m-d');
                                        $isInCutoff = ($dateStr >= $cutOffStart && $dateStr <= $cutOffEnd);
                                        $detail = $dailyDetails[$dateStr] ?? null;

                                        $isPicket = !empty($detail['is_picket']);
                                        $picketArea = $detail['picket_area'] ?? '';

                                        if (!$isPicket && isset($myPicketSchedules) && $myPicketSchedules->isNotEmpty()) {
                                            $pSched = $myPicketSchedules->firstWhere('day_of_week', $date->dayOfWeek);
                                            if ($pSched) {
                                                $inRange = true;
                                                if ($pSched->start_date && $date->lt($pSched->start_date)) {
                                                    $inRange = false;
                                                }
                                                if ($pSched->end_date && $date->gt($pSched->end_date)) {
                                                    $inRange = false;
                                                }
                                                if ($inRange) {
                                                    $isPicket = true;
                                                    $picketArea = $pSched->picketArea?->name ?? 'Piket';
                                                }
                                            }
                                        }

                                        if ($detail) {
                                            $statusName = $detail['status'] ?? '';
                                            $isReward = ($statusName === 'Reward Libur' || !empty($detail['is_reward']));
                                            $hasCheckIn = !empty($detail['check_in']) && $detail['check_in'] !== '-';
                                            $isLate = $hasCheckIn && !empty($detail['is_late']);
                                            $isRedLate = $hasCheckIn && !empty($detail['is_red_late']);
                                            $lateMnt = $hasCheckIn ? (int) abs($detail['late_minutes'] ?? 0) : 0;
                                            $earlyMnt = $hasCheckIn ? (int) abs($detail['early_minutes'] ?? 0) : 0;

                                            if ($isFuture && empty($detail['check_in'])) {
                                                if ($statusName === 'Cuti/Izin' || $statusName === 'Izin' || $statusName === 'Sakit' || $statusName === 'Cuti') {
                                                    $statusBadge = (!empty($detail['is_pending']) ? 'Pending: ' : '') . ($detail['leave_type'] ?? 'Izin');
                                                    $statusColor = ($detail['leave_code'] ?? '') === 'S' ? 'amber' : (($detail['leave_code'] ?? '') === 'C' ? 'blue' : 'purple');
                                                    $noteText = 'Pengajuan ' . ($detail['leave_type'] ?? 'izin') . (!empty($detail['is_pending']) ? ' menunggu persetujuan.' : ' disetujui.');
                                                } else {
                                                    $statusBadge = 'Belum Terlaksana';
                                                    $statusColor = 'slate';
                                                    $noteText = 'Tanggal dan presensi belum berjalan.';
                                                }
                                            } elseif ($statusName === 'Hadir') {
                                                if ($isReward) {
                                                    $statusBadge = 'Libur Reward';
                                                    $statusColor = 'emerald';
                                                    $noteText = 'Bebas keterlambatan (' . ($detail['reward_name'] ?? 'Reward Libur') . ').';
                                                } elseif ($isRedLate) {
                                                    $statusBadge = 'Terlambat Berat';
                                                    $statusColor = 'rose';
                                                    $noteText = 'Terlambat ' . ($lateMnt > 0 ? $lateMnt . ' menit ' : '') . '(melewati batas toleransi).';
                                                } elseif ($isLate) {
                                                    $statusBadge = 'Terlambat';
                                                    $statusColor = 'amber';
                                                    $noteText = 'Terlambat ' . ($lateMnt > 0 ? $lateMnt . ' menit ' : '') . 'dari batas shift.';
                                                } else {
                                                    $statusBadge = 'Tepat Waktu';
                                                    $statusColor = 'emerald';
                                                    $noteText = $earlyMnt > 0 ? 'Datang ' . $earlyMnt . ' menit lebih awal.' : 'Tepat waktu sesuai jadwal shift.';
                                                }
                                            } elseif ($statusName === 'Reward Libur') {
                                                $statusBadge = 'Libur Reward';
                                                $statusColor = 'emerald';
                                                $noteText = 'Mendapat reward libur (' . ($detail['reward_name'] ?? 'Libur Reward') . ').';
                                            } elseif ($statusName === 'Alfa') {
                                                $statusBadge = 'Tidak Hadir (Alfa)';
                                                $statusColor = 'rose';
                                                $noteText = 'Tidak tercatat data presensi kedatangan.';
                                                if (!empty($detail['rejected_leave'])) {
                                                    $noteText .= ' Pengajuan ' . $detail['rejected_leave']['leave_type'] . ' ditolak.';
                                                }
                                            } elseif ($statusName === 'Libur') {
                                                $statusBadge = $isSunday ? 'Akhir Pekan' : 'Hari Libur';
                                                $statusColor = $isSunday ? 'rose' : 'slate';
                                                $noteText = $isSunday ? 'Hari libur akhir pekan.' : 'Hari libur nasional/bersama.';
                                            } elseif ($statusName === 'Off') {
                                                $statusBadge = 'Jadwal Libur (OFF)';
                                                $statusColor = 'slate';
                                                $noteText = 'Jadwal libur rutin kerja pegawai.';
                                            } elseif ($statusName === 'Cuti/Izin' || $statusName === 'Izin' || $statusName === 'Sakit' || $statusName === 'Cuti') {
                                                $leaveType = $detail['leave_type'] ?? 'Izin';
                                                $isPending = !empty($detail['is_pending']);
                                                $lCode = $detail['leave_code'] ?? 'I';
                                                $colorMap = ['S' => 'amber', 'I' => 'purple', 'C' => 'blue', 'H' => 'emerald'];
                                                $statusColor = $colorMap[$lCode] ?? 'purple';
                                                $statusBadge = ($isPending ? 'Pending: ' : '') . $leaveType;
                                                $noteText = ($isPending ? 'Menunggu persetujuan: ' : 'Disetujui: ') . $leaveType . '.';
                                                if (!empty($detail['check_in']) || !empty($detail['check_out'])) {
                                                    $noteText .= ' Disertai scan (' . ($detail['check_in'] ?? '-') . ' - ' . ($detail['check_out'] ?? '-') . ').';
                                                }
                                            } elseif ($statusName === 'Pending') {
                                                $statusBadge = 'Menunggu Jam Kerja';
                                                $statusColor = 'slate';
                                                $noteText = 'Shift belum dimulai atau sedang berlangsung.';
                                            } else {
                                                $statusBadge = $statusName ?: '-';
                                                $statusColor = 'slate';
                                                $noteText = 'Data status kehadiran.';
                                            }

                                            if ($isReward) {
                                                $shiftLabel = 'Libur Reward';
                                                $jadwalStr = 'Bebas Jam';
                                            } elseif ($isPicket) {
                                                $shiftLabel = $picketArea ?: 'Piket';
                                                $startJam = !empty($detail['shift_start']) ? substr($detail['shift_start'], 0, 5) : '06:30';
                                                $jadwalStr = $startJam;
                                            } else {
                                                $shiftLabel = $detail['shift_name'] ?? ($isSunday ? 'Akhir Pekan' : 'Reguler');
                                                if (!empty($detail['shift_start'])) {
                                                    $startJam = substr($detail['shift_start'], 0, 5);
                                                    $endJam = !empty($detail['shift_end']) ? substr($detail['shift_end'], 0, 5) : '';
                                                    $jadwalStr = $endJam ? "{$startJam} - {$endJam}" : $startJam;
                                                } else {
                                                    $jadwalStr = ($statusBadge === 'Akhir Pekan' || $statusBadge === 'Jadwal Libur (OFF)' || $statusName === 'Libur' || $statusName === 'Off') ? 'OFF' : '-';
                                                }
                                            }

                                            $isOff = ($statusName === 'Libur' || $statusName === 'Off' || ($isSunday && empty($detail['check_in'])));
                                            $inStr = $isFuture ? '-' : ($detail['check_in'] ?? '-');
                                            $outStr = $isFuture ? '-' : ($detail['check_out'] ?? '-');

                                            $cellData = [
                                                'employee' => [
                                                    'name' => $empName,
                                                    'unit' => $unitName,
                                                    'position' => $position,
                                                ],
                                                'date_display' => $date->translatedFormat('d F Y'),
                                                'day_name' => $date->translatedFormat('l'),
                                                'is_today' => $isToday,
                                                'is_sunday' => $isSunday,
                                                'is_future' => $isFuture,
                                                'is_in_cutoff' => $isInCutoff,
                                                'is_off' => $isOff,
                                                'status' => $statusBadge,
                                                'status_color' => $statusColor,
                                                'shift_name' => $shiftLabel,
                                                'jadwal' => $jadwalStr,
                                                'check_in' => $inStr,
                                                'check_out' => $outStr,
                                                'is_late' => $isLate,
                                                'is_red_late' => $isRedLate,
                                                'late_minutes' => $lateMnt,
                                                'early_minutes' => $earlyMnt,
                                                'is_reward' => $isReward,
                                                'reward_name' => $detail['reward_name'] ?? null,
                                                'leave_type' => $detail['leave_type'] ?? null,
                                                'leave_code' => $detail['leave_code'] ?? null,
                                                'is_pending' => !empty($detail['is_pending']),
                                                'rejected_leave' => $detail['rejected_leave'] ?? null,
                                                'is_picket' => $isPicket,
                                                'picket_area' => $picketArea,
                                                'bonus_nominal' => $detail['calculated_bonus'] ?? ($detail['bonus_nominal'] ?? 0),
                                                'tier_level' => $detail['tier_level'] ?? null,
                                                'schema_name' => $detail['schema_name'] ?? null,
                                                'note' => $noteText,
                                            ];
                                        } else {
                                            $isOff = true;
                                            $shiftLabel = $isSunday ? 'Akhir Pekan' : 'Jadwal Libur Rutin';
                                            $jadwalStr = 'OFF';
                                            $cellData = [
                                                'employee' => [
                                                    'name' => $empName,
                                                    'unit' => $unitName,
                                                    'position' => $position,
                                                ],
                                                'date_display' => $date->translatedFormat('d F Y'),
                                                'day_name' => $date->translatedFormat('l'),
                                                'is_today' => $isToday,
                                                'is_sunday' => $isSunday,
                                                'is_future' => $isFuture,
                                                'is_in_cutoff' => $isInCutoff,
                                                'is_off' => true,
                                                'status' => $isSunday ? 'Akhir Pekan' : 'Jadwal Libur (OFF)',
                                                'status_color' => $isSunday ? 'rose' : 'slate',
                                                'shift_name' => $shiftLabel,
                                                'jadwal' => $jadwalStr,
                                                'check_in' => '-',
                                                'check_out' => '-',
                                                'is_late' => false,
                                                'is_red_late' => false,
                                                'late_minutes' => 0,
                                                'early_minutes' => 0,
                                                'is_reward' => false,
                                                'reward_name' => null,
                                                'leave_type' => null,
                                                'leave_code' => null,
                                                'is_pending' => false,
                                                'rejected_leave' => null,
                                                'is_picket' => false,
                                                'picket_area' => null,
                                                'bonus_nominal' => 0,
                                                'tier_level' => null,
                                                'schema_name' => null,
                                                'note' => $isSunday ? 'Hari libur akhir pekan.' : 'Hari libur rutin pegawai.',
                                            ];
                                        }

                                        if (!empty($cellData['is_off'])) {
                                            $numberColor = 'text-red-500';
                                        } elseif ($cellData['status_color'] === 'emerald') {
                                            $numberColor = 'text-emerald-600 dark:text-emerald-400';
                                        } elseif ($cellData['status_color'] === 'amber') {
                                            $numberColor = 'text-amber-600 dark:text-amber-400';
                                        } elseif ($cellData['status_color'] === 'rose') {
                                            $numberColor = 'text-rose-600 dark:text-rose-400';
                                        } elseif ($cellData['status_color'] === 'blue') {
                                            $numberColor = 'text-blue-500 dark:text-blue-400';
                                        } elseif ($cellData['status_color'] === 'purple') {
                                            $numberColor = 'text-purple-600 dark:text-purple-400';
                                        } else {
                                            $numberColor = $isSunday ? 'text-red-500' : 'text-slate-800 dark:text-slate-100';
                                        }
                                    @endphp

                                    <div
                                        @mouseenter="showTooltip($event, @js($cellData))"
                                        @mouseleave="hideTooltip()"
                                        @click.stop="toggleTooltip($event, @js($cellData))"
                                        class="group relative flex min-h-[28px] sm:min-h-[34px] items-center justify-center px-0.5 sm:px-1 transition-all {{ $isToday ? 'bg-slate-950 text-white dark:bg-slate-100 dark:text-slate-900 rounded-[200px] font-bold shadow-sm' : 'hover:bg-slate-100 dark:hover:bg-slate-900/80 rounded-lg' }} {{ $isInCutoff ? 'opacity-100 font-semibold' : 'opacity-30 hover:opacity-100' }} cursor-pointer">
                                        @if($isPicket)
                                            <span class="absolute -top-1 -right-0.5 flex h-2 w-2 z-10 pointer-events-none" title="Piket: {{ $picketArea }}">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                            </span>
                                        @endif
                                        <span class="text-[12px] sm:text-[13px] leading-none tracking-[0.01em] {{ $isToday ? 'text-inherit' : $numberColor }}">
                                            {{ $date->format('d') }}
                                        </span>
                                    </div>
                                @endforeach

                                @foreach($grid['paddingEnd'] as $pDate)
                                    <div class="min-h-[28px] sm:min-h-[34px] px-0.5 sm:px-1"></div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                    </div>
                </div>

                @if(!empty($myActiveShifts))
                    <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-800">
                        <div class="flex flex-col sm:flex-row sm:items-start gap-y-2" style="gap: 12px 16px;">
                            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mt-0.5 shrink-0">Jam Kerja:</span>
                            <div class="flex flex-col sm:flex-row sm:items-center sm:flex-wrap gap-y-1.5" style="gap: 8px 20px;">
                                @foreach($myActiveShifts as $index => $shift)
                                    @php
                                        $daysName = [1 => 'Sen', 2 => 'Sel', 3 => 'Rab', 4 => 'Kam', 5 => 'Jum', 6 => 'Sab', 0 => 'Min'];
                                        
                                        // Group active details by time range
                                        $groupedDetails = [];
                                        if (!empty($shift['details']) && is_array($shift['details'])) {
                                            foreach($shift['details'] as $dt) {
                                                if(empty($dt['is_off'])) {
                                                    $timeRange = substr($dt['start_time'] ?? '', 0, 5) . ' - ' . substr($dt['end_time'] ?? '', 0, 5);
                                                    $groupedDetails[$timeRange][] = $dt['day_of_week'] ?? 0;
                                                }
                                            }
                                        }
                                        
                                        $scheduleParts = [];
                                        foreach($groupedDetails as $timeRange => $days) {
                                            // Map Sunday to 7 to sort chronologically from Monday (1) to Sunday (7)
                                            $sortableDays = array_map(function($d) {
                                                return $d == 0 ? 7 : $d;
                                            }, $days);
                                            sort($sortableDays);
                                            
                                            // Check if consecutive
                                            $isConsecutive = true;
                                            for ($i = 0; $i < count($sortableDays) - 1; $i++) {
                                                if ($sortableDays[$i+1] - $sortableDays[$i] !== 1) {
                                                    $isConsecutive = false;
                                                    break;
                                                }
                                            }
                                            
                                            // Map back to original values
                                            $originalDays = array_map(function($sd) {
                                                return $sd == 7 ? 0 : $sd;
                                            }, $sortableDays);
                                            
                                            if (count($originalDays) >= 3 && $isConsecutive) {
                                                $dayStr = ($daysName[$originalDays[0]] ?? '') . ' - ' . ($daysName[$originalDays[count($originalDays)-1]] ?? '');
                                            } else {
                                                $dayStr = implode(',', array_map(function($d) use ($daysName) {
                                                    return $daysName[$d] ?? '';
                                                }, $originalDays));
                                            }
                                            
                                            $scheduleParts[] = $dayStr . ' ' . $timeRange;
                                        }
                                    @endphp
                                    @if(!empty($scheduleParts))
                                        <div class="flex items-center gap-1.5 text-[11px] text-slate-600 dark:text-slate-400 font-medium">
                                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $shift['name'] }}</span>
                                            <span class="text-slate-450 dark:text-slate-500">({{ implode(', ', $scheduleParts) }})</span>
                                        </div>
                                    @elseif(!empty($shift['schedule']))
                                        <div class="flex items-center gap-1.5 text-[11px] text-slate-600 dark:text-slate-400 font-medium">
                                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $shift['name'] }}</span>
                                            <span class="text-slate-450 dark:text-slate-500">({{ $shift['schedule'] }})</span>
                                        </div>
                                    @endif
                                    @if($index < count($myActiveShifts) - 1)
                                        <span class="text-slate-300 dark:text-slate-700 text-xs hidden sm:inline">•</span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- ASIDE KETERANGAN & PANDUAN -->
            <aside class="md:col-span-1 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-[18px] shadow-sm p-4 sm:p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-3 pb-3 border-b border-slate-200 dark:border-slate-800">
                        <div>
                            <h4 class="text-sm sm:text-base font-bold text-slate-900 dark:text-slate-50">Keterangan & Panduan</h4>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500">Panduan warna dan periode absensi</p>
                        </div>
                    </div>

                    <div class="mt-4 space-y-3">
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 inline-flex h-2.5 w-2.5 rounded-full bg-slate-950 dark:bg-slate-100"></span>
                            <div>
                                <p class="text-xs font-semibold text-slate-900 dark:text-slate-50">Tanggal Hari Ini</p>
                                <p class="text-[11px] leading-4 text-slate-500 dark:text-slate-400 mt-0.5">Ditandai dengan lingkaran tebal gelap/terang.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                            <div>
                                <p class="text-xs font-semibold text-slate-900 dark:text-slate-50">Hadir Tepat Waktu / Libur Reward</p>
                                <p class="text-[11px] leading-4 text-slate-500 dark:text-slate-400 mt-0.5">Memenuhi syarat bonus ketepatan waktu penuh.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 inline-flex h-2.5 w-2.5 rounded-full bg-amber-500"></span>
                            <div>
                                <p class="text-xs font-semibold text-slate-900 dark:text-slate-50">Terlambat</p>
                                <p class="text-[11px] leading-4 text-slate-500 dark:text-slate-400 mt-0.5">Jam masuk melewati toleransi jam shift kerja.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 inline-flex h-2.5 w-2.5 rounded-full bg-purple-500"></span>
                            <div>
                                <p class="text-xs font-semibold text-slate-900 dark:text-slate-50">Izin / Sakit / Cuti</p>
                                <p class="text-[11px] leading-4 text-slate-500 dark:text-slate-400 mt-0.5">Pengajuan izin resmi yang disetujui unit.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 inline-flex h-2.5 w-2.5 rounded-full bg-red-500"></span>
                            <div>
                                <p class="text-xs font-semibold text-slate-900 dark:text-slate-50">Hari Libur / Minggu</p>
                                <p class="text-[11px] leading-4 text-slate-500 dark:text-slate-400 mt-0.5">Hari libur resmi atau hari libur mingguan.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 rounded-xl bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/30 p-3">
                    <p class="text-xs font-bold text-indigo-900 dark:text-indigo-300 flex items-center gap-1.5">
                        <i data-lucide="info" class="w-3.5 h-3.5"></i>
                        <span>Info Periode Cut-Off</span>
                    </p>
                    <p class="text-[11px] leading-4 text-indigo-700/80 dark:text-indigo-400 mt-1">
                        Angka tanggal yang <b>terang</b> masuk dalam perhitungan gaji bulan ini. Tanggal yang <b>redup</b> merupakan bagian dari periode sebelum atau sesudahnya.
                    </p>
                </div>
            </aside>
        </section>

        <!-- RICH TOOLTIP CARD (Identical to Admin Matrix) -->
        <div 
            x-show="tooltip.show"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            :style="`left: ${tooltip.x}px; top: ${tooltip.y}px; transform: translate(-50%, ${tooltip.placement === 'bottom' ? '0%' : '-100%'});`"
            class="fixed z-50 pointer-events-none w-[285px] max-w-[calc(100vw-24px)] bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl shadow-2xl p-3.5 text-left text-xs select-none backdrop-blur-sm"
            style="display: none;"
            @click.stop
        >
            <!-- Pointer Arrow -->
            <div 
                class="absolute w-2.5 h-2.5 rotate-45 bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 transition-all duration-75"
                :class="tooltip.placement === 'bottom' 
                    ? '-top-1.5 border-t border-l' 
                    : '-bottom-1.5 border-b border-r'"
                :style="`left: calc(50% + ${tooltip.arrowOffset || 0}px); transform: translateX(-50%) rotate(45deg);`"
            ></div>

            <!-- Header: Pegawai & Tanggal -->
            <div class="border-b border-slate-100 dark:border-slate-800 pb-2 mb-2.5">
                <div class="flex items-center justify-between gap-2 mb-1">
                    <span class="font-bold text-[12px] text-slate-900 dark:text-slate-100 truncate" x-text="tooltip.data?.employee?.name"></span>
                    <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 shrink-0 truncate max-w-[100px]" x-text="tooltip.data?.employee?.unit"></span>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                    <div class="flex items-center gap-1.5 truncate">
                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="truncate" x-text="(tooltip.data?.day_name ? tooltip.data.day_name + ', ' : '') + (tooltip.data?.date_display || '')"></span>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <template x-if="tooltip.data?.is_today">
                            <span class="text-[8px] font-bold px-1.5 py-0.5 rounded bg-indigo-50 text-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-400 border border-indigo-200/50">Hari Ini</span>
                        </template>
                        <template x-if="tooltip.data && !tooltip.data.is_in_cutoff">
                            <span class="text-[8px] font-semibold px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400 border border-slate-200/60 dark:border-slate-700/60">Luar Cut-off</span>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Shift & Jadwal Info -->
            <div class="flex items-center justify-between gap-2 px-2.5 py-1.5 mb-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 text-[10px]">
                <div class="flex items-center gap-1.5 truncate text-slate-600 dark:text-slate-400">
                    <template x-if="tooltip.data?.is_picket">
                        <span class="text-amber-600 dark:text-amber-400 font-bold shrink-0">⚡ Piket:</span>
                    </template>
                    <template x-if="!tooltip.data?.is_picket && tooltip.data?.is_reward">
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold shrink-0">🎁 Reward</span>
                    </template>
                    <template x-if="!tooltip.data?.is_picket && !tooltip.data?.is_reward">
                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </template>
                    <span class="truncate font-semibold text-slate-700 dark:text-slate-300" x-text="tooltip.data?.is_picket ? (tooltip.data?.picket_area || tooltip.data?.shift_name || 'Area Piket') : (tooltip.data?.shift_name || 'Reguler')"></span>
                </div>
                <span class="font-mono font-bold text-slate-600 dark:text-slate-300 shrink-0" x-text="tooltip.data?.jadwal"></span>
            </div>

            <!-- Box Masuk & Pulang (SANS Unit Style) -->
            <template x-if="tooltip.data?.is_off">
                <div class="px-3 py-2.5 mb-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-800 text-center">
                    <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400" x-text="tooltip.data?.status"></span>
                </div>
            </template>
            <template x-if="!tooltip.data?.is_off">
                <div class="grid grid-cols-2 gap-2 mb-2.5">
                    <!-- MASUK -->
                    <div class="flex flex-col bg-slate-50 dark:bg-slate-800/50 rounded-xl p-2 border border-slate-100 dark:border-slate-800 text-center">
                        <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5">Masuk</span>
                        <span class="font-extrabold text-[13px] tracking-tight"
                              :class="(tooltip.data?.check_in && tooltip.data?.check_in !== '-') ? (tooltip.data?.is_red_late ? 'text-rose-600 dark:text-rose-400' : (tooltip.data?.is_late ? 'text-amber-600 dark:text-amber-400' : 'text-slate-800 dark:text-slate-100')) : 'text-slate-800 dark:text-slate-100'"
                              x-text="tooltip.data?.check_in || '-'">
                        </span>
                        <template x-if="tooltip.data?.check_in && tooltip.data?.check_in !== '-' && tooltip.data?.is_red_late">
                            <span class="text-[8px] font-bold text-rose-700 bg-rose-100 dark:bg-rose-950/60 dark:text-rose-300 px-1.5 py-0.5 rounded-md mt-1 mx-auto leading-none"
                                  x-text="'Telat ' + (tooltip.data?.late_minutes || '') + ' mnt'">
                            </span>
                        </template>
                        <template x-if="tooltip.data?.check_in && tooltip.data?.check_in !== '-' && !tooltip.data?.is_red_late && tooltip.data?.is_late && tooltip.data?.late_minutes > 0">
                            <span class="text-[8px] font-bold text-amber-700 bg-amber-100 dark:bg-amber-950/60 dark:text-amber-300 px-1.5 py-0.5 rounded-md mt-1 mx-auto leading-none"
                                  x-text="'Telat ' + (tooltip.data?.late_minutes || '') + ' mnt'">
                            </span>
                        </template>
                        <template x-if="tooltip.data?.check_in && tooltip.data?.check_in !== '-' && !tooltip.data?.is_late && tooltip.data?.early_minutes > 0">
                            <span class="text-[8px] font-bold text-emerald-700 bg-emerald-100 dark:bg-emerald-950/60 dark:text-emerald-300 px-1.5 py-0.5 rounded-md mt-1 mx-auto leading-none"
                                  x-text="'Awal ' + tooltip.data?.early_minutes + ' mnt'">
                            </span>
                        </template>
                        <template x-if="tooltip.data?.check_in && tooltip.data?.check_in !== '-' && tooltip.data?.check_in !== 'Tidak Hadir' && !tooltip.data?.is_late && (!tooltip.data?.early_minutes || tooltip.data?.early_minutes <= 0)">
                            <span class="text-[8px] font-bold text-emerald-700 bg-emerald-100 dark:bg-emerald-950/60 dark:text-emerald-300 px-1.5 py-0.5 rounded-md mt-1 mx-auto leading-none">
                                Tepat Waktu
                            </span>
                        </template>
                    </div>

                    <!-- PULANG -->
                    <div class="flex flex-col bg-slate-50 dark:bg-slate-800/50 rounded-xl p-2 border border-slate-100 dark:border-slate-800 text-center">
                        <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5">Pulang</span>
                        <span class="font-extrabold text-[13px] tracking-tight text-slate-800 dark:text-slate-100"
                              x-text="tooltip.data?.check_out || '-'">
                        </span>
                        <template x-if="tooltip.data?.check_out && tooltip.data?.check_out !== '-'">
                            <span class="text-[8px] font-medium text-slate-400 dark:text-slate-500 mt-1 leading-none">
                                Tap Pulang
                            </span>
                        </template>
                    </div>
                </div>
            </template>

            <!-- Uang Kehadiran Card -->
            <div class="rounded-xl p-2.5 mb-2.5 border flex items-center justify-between"
                 :class="(tooltip.data?.bonus_nominal > 0) ? 'bg-emerald-50/80 dark:bg-emerald-950/30 border-emerald-200/80 dark:border-emerald-800/50' : 'bg-slate-50 dark:bg-slate-800/40 border-slate-100 dark:border-slate-800'">
                <div class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 shrink-0" :class="(tooltip.data?.bonus_nominal > 0) ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-[10px] font-bold" :class="(tooltip.data?.bonus_nominal > 0) ? 'text-emerald-800 dark:text-emerald-300' : 'text-slate-500 dark:text-slate-400'">Uang Kehadiran</span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="text-[12px] font-extrabold font-mono tracking-tight"
                          :class="(tooltip.data?.bonus_nominal > 0) ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400'"
                          x-text="'Rp ' + Number(tooltip.data?.bonus_nominal || 0).toLocaleString('id-ID')">
                    </span>
                </div>
            </div>

            <!-- Keterangan & Catatan Presensi Block -->
            <div class="rounded-xl p-2.5 border"
                 :class="{
                     'bg-emerald-50/80 dark:bg-emerald-950/30 border-emerald-200/80 dark:border-emerald-800/50 text-emerald-900 dark:text-emerald-200': tooltip.data?.status_color === 'emerald',
                     'bg-amber-50/80 dark:bg-amber-950/30 border-amber-200/80 dark:border-amber-800/50 text-amber-900 dark:text-amber-200': tooltip.data?.status_color === 'amber',
                     'bg-rose-50/80 dark:bg-rose-950/30 border-rose-200/80 dark:border-rose-800/50 text-rose-900 dark:text-rose-200': tooltip.data?.status_color === 'rose',
                     'bg-blue-50/80 dark:bg-blue-950/30 border-blue-200/80 dark:border-blue-800/50 text-blue-900 dark:text-blue-200': tooltip.data?.status_color === 'blue',
                     'bg-purple-50/80 dark:bg-purple-950/30 border-purple-200/80 dark:border-purple-800/50 text-purple-900 dark:text-purple-200': tooltip.data?.status_color === 'purple',
                     'bg-slate-50 dark:bg-slate-800/40 border-slate-100 dark:border-slate-800 text-slate-700 dark:text-slate-300': tooltip.data?.status_color === 'slate'
                 }">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1.5 font-bold text-[10px]">
                        <svg class="w-3.5 h-3.5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span x-text="tooltip.data?.leave_type ? 'Pengajuan ' + tooltip.data.leave_type : 'Keterangan Presensi'"></span>
                    </div>
                    <template x-if="tooltip.data?.leave_code">
                        <span class="text-[8.5px] font-black px-1.5 py-0.5 rounded leading-none uppercase bg-white/80 dark:bg-slate-900/80 border border-current shadow-xs"
                              x-text="tooltip.data?.leave_code + (tooltip.data?.is_pending ? ' (Pending)' : ' (OK)')">
                        </span>
                    </template>
                </div>

                <!-- Catatan informatif tambahan -->
                <template x-if="tooltip.data?.note">
                    <div class="mt-1.5 pt-1.5 border-t border-current/15 text-[9.5px] leading-snug"
                         x-text="tooltip.data.note">
                    </div>
                </template>
            </div>
        </div>
    </div>
</x-admin-layout>
