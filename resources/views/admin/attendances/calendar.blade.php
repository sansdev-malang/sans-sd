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
         tooltip: {
            show: false,
            date: '',
            status: '',
            checkIn: '',
            checkOut: '',
            isLate: false,
            color: '',
            bonus: 0,
            isInCutoff: true,
            x: 0,
            y: 0,
            placement: 'top',
            activeDate: ''
        },
        showTooltip(e, date, status, checkIn, checkOut, isLate, color, bonus, isInCutoff = true) {
            if (!status) return;
            this.tooltip.date = date;
            this.tooltip.status = status;
            this.tooltip.checkIn = checkIn;
            this.tooltip.checkOut = checkOut;
            this.tooltip.isLate = isLate;
            this.tooltip.color = color;
            this.tooltip.bonus = bonus || 0;
            this.tooltip.isInCutoff = isInCutoff;
            this.tooltip.activeDate = date;
            
            const containerRect = this.$refs.container.getBoundingClientRect();
            const targetRect = e.currentTarget.getBoundingClientRect();
            
            const rawX = targetRect.left - containerRect.left + (targetRect.width / 2);
            const rawY = targetRect.top - containerRect.top - 8;
            
            // Width of tooltip card is approx 220px. Safe padding 10px from container edges.
            const tooltipWidth = 220;
            const halfWidth = tooltipWidth / 2;
            const padding = 10;
            
            const minX = halfWidth + padding;
            const maxX = containerRect.width - halfWidth - padding;
            
            if (containerRect.width <= tooltipWidth + padding * 2) {
                this.tooltip.x = containerRect.width / 2;
            } else {
                this.tooltip.x = Math.max(minX, Math.min(rawX, maxX));
            }
            
            // If the cell is too close to top of container, place tooltip below the cell
            if (rawY < 130) {
                this.tooltip.y = targetRect.bottom - containerRect.top + 8;
                this.tooltip.placement = 'bottom';
            } else {
                this.tooltip.y = rawY;
                this.tooltip.placement = 'top';
            }
            
            this.tooltip.show = true;
        },
        toggleTooltip(e, date, status, checkIn, checkOut, isLate, color, bonus, isInCutoff = true) {
            if (this.tooltip.show && this.tooltip.activeDate === date) {
                this.hideTooltip();
            } else {
                this.showTooltip(e, date, status, checkIn, checkOut, isLate, color, bonus, isInCutoff);
            }
        },
        hideTooltip() {
            this.tooltip.show = false;
            this.tooltip.activeDate = '';
        }
    }" x-ref="container" @click="hideTooltip()">
        <!-- DATA RIWAYAT ABSENSI / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Data Riwayat Absensi</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Memantau waktu kedatangan, kepulangan, dan estimasi bonus ketepatan waktu Anda.</p>
            </div>
            <div>
                <a href="{{ route('attendances.index', array_merge(request()->query(), ['refresh' => 1])) }}" 
                   x-data="{ syncing: false }" 
                   @click="syncing = true" 
                   :class="{ 'opacity-75 pointer-events-none': syncing }"
                   class="h-9 px-3.5 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 font-semibold text-xs rounded-xl shadow-sm transition-all cursor-pointer whitespace-nowrap flex items-center gap-2" 
                   title="Sinkronkan data terbaru langsung dari HRD / Mesin Absensi">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" :class="{ 'animate-spin': syncing }"></i>
                    <span x-text="syncing ? 'Menyinkronkan...' : 'Sinkronkan Data'">Sinkronkan Data</span>
                </a>
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
            
            $prevMonthUrl = route('attendances.index', array_merge(request()->query(), ['month' => $selectedMonth->copy()->subMonthNoOverflow()->format('Y-m')]));
            $nextMonthUrl = route('attendances.index', array_merge(request()->query(), ['month' => $selectedMonth->copy()->addMonthNoOverflow()->format('Y-m')]));
            
            $cutoffBonus = 0;
            $countHadir = 0;
            $countTelat = 0;
            $countIzin = 0;
            $countSakit = 0;
            $countCuti = 0;
            $countAlfa = 0;
            
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
                    <!-- Kiri: Total Bonus Periode Ini -->
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
                                    Total Bonus Periode Ini
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
                                        $isInCutoff = ($dateStr >= $cutOffStart && $dateStr <= $cutOffEnd);
                                        $detail = $dailyDetails[$dateStr] ?? null;

                                        $status = $detail['status'] ?? '';
                                        $checkIn = $detail['check_in'] ?? '--:--';
                                        $checkOut = $detail['check_out'] ?? '--:--';
                                        $isLate = !empty($detail['is_late']);

                                        if ($status === 'Hadir') {
                                            $numberColor = $isLate ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400';
                                            $modalColor = $isLate ? 'amber' : 'emerald';
                                        } elseif ($status === 'Reward Libur' || !empty($detail['is_reward'])) {
                                            $numberColor = 'text-emerald-600 dark:text-emerald-400';
                                            $modalColor = 'emerald';
                                            $status = 'Reward Libur';
                                        } elseif ($status === 'Off' || $status === 'Libur' || strtolower($status) === 'x') {
                                            $numberColor = 'text-red-500';
                                            $modalColor = 'red';
                                            $status = 'OFF';
                                        } elseif ($status === 'Alfa') {
                                            $numberColor = 'text-rose-600 dark:text-rose-400';
                                            $modalColor = 'rose';
                                        } elseif ($status === 'Izin') {
                                            $numberColor = 'text-amber-600';
                                            $modalColor = 'amber';
                                        } elseif ($status === 'Sakit') {
                                            $numberColor = 'text-blue-500';
                                            $modalColor = 'blue';
                                        } elseif ($status === 'Cuti') {
                                            $numberColor = 'text-purple-500';
                                            $modalColor = 'purple';
                                        } else {
                                            $numberColor = $isSunday ? 'text-red-500' : 'text-slate-800 dark:text-slate-100';
                                            $modalColor = 'slate';
                                        }
                                        
                                        $bonus = $detail['calculated_bonus'] ?? 0.00;
                                    @endphp

                                    <div
                                        @mouseenter="showTooltip($event, '{{ $date->translatedFormat('d F Y') }}', '{{ $status }}', '{{ $checkIn }}', '{{ $checkOut }}', {{ $isLate ? 'true' : 'false' }}, '{{ $modalColor }}', {{ $bonus }}, {{ $isInCutoff ? 'true' : 'false' }})"
                                        @mouseleave="hideTooltip()"
                                        @click.stop="toggleTooltip($event, '{{ $date->translatedFormat('d F Y') }}', '{{ $status }}', '{{ $checkIn }}', '{{ $checkOut }}', {{ $isLate ? 'true' : 'false' }}, '{{ $modalColor }}', {{ $bonus }}, {{ $isInCutoff ? 'true' : 'false' }})"
                                        class="group relative flex min-h-[28px] sm:min-h-[34px] items-center justify-center px-0.5 sm:px-1 transition-all {{ $isToday ? 'bg-slate-950 text-white dark:bg-slate-100 dark:text-slate-900 rounded-[200px] font-bold shadow-sm' : 'hover:bg-slate-100 dark:hover:bg-slate-900/80 rounded-lg' }} {{ $isInCutoff ? 'opacity-100 font-semibold' : 'opacity-30 hover:opacity-100' }} cursor-pointer">
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
                                        foreach($shift['details'] as $dt) {
                                            if(!$dt['is_off']) {
                                                $timeRange = substr($dt['start_time'], 0, 5) . ' - ' . substr($dt['end_time'], 0, 5);
                                                $groupedDetails[$timeRange][] = $dt['day_of_week'];
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
                                                $dayStr = $daysName[$originalDays[0]] . ' - ' . $daysName[$originalDays[count($originalDays)-1]];
                                            } else {
                                                $dayStr = implode(',', array_map(function($d) use ($daysName) {
                                                    return $daysName[$d];
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

        <!-- RICH TOOLTIP CARD -->
        <div 
            x-show="tooltip.show"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            :style="`left: ${tooltip.x}px; top: ${tooltip.y}px; transform: translate(-50%, ${tooltip.placement === 'bottom' ? '0%' : '-100%'});`"
            class="absolute z-50 pointer-events-none w-[220px] max-w-[calc(100vw-32px)] bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-2xl p-3.5 text-left text-xs transition-all duration-150"
            style="display: none;"
            @click.stop
        >
            <!-- Date header -->
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-1.5 mb-2">
                <div class="font-bold text-slate-900 dark:text-slate-100" x-text="tooltip.date"></div>
            </div>

            <template x-if="!tooltip.isInCutoff">
                <div class="mb-2 px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800/80 text-[10px] font-medium text-slate-500 dark:text-slate-400 text-center border border-slate-200/50 dark:border-slate-700/50">
                    Di luar cut-off periode ini
                </div>
            </template>
            
            <!-- Status Badge -->
            <div class="flex items-center justify-between gap-4 mb-2.5">
                <span class="text-slate-400 dark:text-slate-500 font-medium">Status</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider"
                      :class="{
                          'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400': tooltip.color === 'emerald',
                          'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': tooltip.color === 'red',
                          'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400': tooltip.color === 'amber',
                          'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400': tooltip.color === 'blue',
                          'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400': tooltip.color === 'purple',
                          'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400': tooltip.color === 'rose',
                          'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300': tooltip.color === 'slate'
                      }"
                      x-text="tooltip.status">
                </span>
            </div>

            <!-- Details (Check In & Check Out) -->
            <template x-if="tooltip.status === 'Hadir'">
                <div class="space-y-2">
                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <div class="flex flex-col bg-slate-50 dark:bg-slate-800/40 rounded-lg p-1.5 border border-slate-100/50 dark:border-slate-800 text-center">
                            <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5">Masuk</span>
                            <span class="font-bold text-[11px]" :class="tooltip.isLate ? 'text-amber-600 dark:text-amber-500' : 'text-slate-700 dark:text-slate-200'" x-text="tooltip.checkIn"></span>
                            <template x-if="tooltip.isLate">
                                <span class="text-[8px] font-bold text-amber-700 bg-amber-100 dark:bg-amber-950/30 dark:text-amber-400 px-1 py-0.5 rounded mt-1 mx-auto w-max leading-none">Telat</span>
                            </template>
                        </div>
                        <div class="flex flex-col bg-slate-50 dark:bg-slate-800/40 rounded-lg p-1.5 border border-slate-100/50 dark:border-slate-800 text-center">
                            <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5">Pulang</span>
                            <span class="font-bold text-[11px] text-slate-700 dark:text-slate-200" x-text="tooltip.checkOut"></span>
                        </div>
                    </div>
                    <!-- Attendance Bonus Section -->
                    <div class="flex items-center justify-between bg-emerald-50/50 dark:bg-emerald-950/10 border border-emerald-100/30 dark:border-emerald-900/30 rounded-lg px-2 py-1.5 text-[10px]">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Bonus Ketepatan Waktu</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400" x-text="tooltip.bonus > 0 ? 'Rp ' + Number(tooltip.bonus).toLocaleString('id-ID') : 'Rp 0'"></span>
                    </div>
                </div>
            </template>
            <template x-if="tooltip.status !== 'Hadir' && tooltip.bonus > 0">
                <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                    <div class="flex items-center justify-between bg-emerald-50/50 dark:bg-emerald-950/10 border border-emerald-100/30 dark:border-emerald-900/30 rounded-lg px-2 py-1.5 text-[10px]">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Bonus Ketepatan Waktu</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400" x-text="'Rp ' + Number(tooltip.bonus).toLocaleString('id-ID')"></span>
                    </div>
                </div>
            </template>
        </div>
    </div>
</x-admin-layout>
