<x-admin-layout>
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
            x: 0,
            y: 0
        },
        showTooltip(e, date, status, checkIn, checkOut, isLate, color, bonus) {
            if (!status) return;
            this.tooltip.date = date;
            this.tooltip.status = status;
            this.tooltip.checkIn = checkIn;
            this.tooltip.checkOut = checkOut;
            this.tooltip.isLate = isLate;
            this.tooltip.color = color;
            this.tooltip.bonus = bonus || 0;
            
            const containerRect = this.$refs.container.getBoundingClientRect();
            const targetRect = e.currentTarget.getBoundingClientRect();
            
            this.tooltip.x = targetRect.left - containerRect.left + (targetRect.width / 2);
            this.tooltip.y = targetRect.top - containerRect.top - 8;
            this.tooltip.show = true;
        },
        hideTooltip() {
            this.tooltip.show = false;
        }
    }" x-ref="container">
        <!-- DATA RIWAYAT ABSENSI / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Data Riwayat Absensi</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Memantau waktu kedatangan dan
                    kepulangan Anda secara komprehensif.</p>
            </div>
        </section>


        @php
            $start = \Carbon\Carbon::parse($startDate);
            $end = \Carbon\Carbon::parse($endDate);
            $report = $reports->first(); 
            $dailyDetails = $report['daily_details'] ?? [];
            
            $totalBonus = 0;
            foreach ($dailyDetails as $detail) {
                $totalBonus += $detail['calculated_bonus'] ?? 0;
            }

            \Carbon\Carbon::setLocale('id');

            $calendarStart = $start->copy()->startOfMonth();
            $months = [
                $calendarStart->copy()->subMonthNoOverflow(),
                $calendarStart->copy(),
            ];
            $startMonthName = $start->translatedFormat('F');
            $startYear = $start->format('Y');

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
            <div class="md:col-span-2 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-[18px] shadow-sm w-full p-3 sm:p-4">
                <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-[1fr_auto] sm:items-center pb-3 border-b border-slate-200 dark:border-slate-800">
                    <div class="sm:justify-self-center">
                        <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-slate-50 text-left sm:text-center">{{ $startMonthName }} {{ $startYear }}</h3>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Tampilan dua bulan kalender absensi</p>
                    </div>
                    <form method="GET" action="{{ route('attendances.index') }}" class="m-0 w-full sm:w-auto sm:justify-self-end">
                        <input type="month" name="month" lang="id-ID" value="{{ $month }}" max="{{ now()->format('Y-m') }}" onchange="this.form.submit()" class="w-full sm:w-auto h-9 px-3.5 text-xs font-medium bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-200 dark:focus:ring-slate-700 cursor-pointer">
                    </form>
                </div>

                <div class="mt-3 overflow-hidden rounded-[18px] border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950">
                    <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x lg:divide-slate-200 dark:lg:divide-slate-800">
                    @foreach($months as $monthStart)
                        @php
                            $grid = $buildMonthGrid($monthStart);
                            $monthName = $monthStart->translatedFormat('F');
                            $monthYear = $monthStart->format('Y');
                        @endphp

                        <div class="p-3.5 sm:p-4 {{ $loop->first ? 'lg:border-r lg:border-slate-200 dark:lg:border-slate-800' : '' }}">
                            <div class="flex items-center justify-between gap-3 mb-3.5">
                                <span class="text-[14px] sm:text-[16px] font-medium tracking-[-0.01em] text-slate-900 dark:text-slate-50">{{ $monthName }} {{ $monthYear }}</span>
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
                                    <div class="min-h-[26px] sm:min-h-[34px] px-0.5 sm:px-1"></div>
                                @endforeach

                                @foreach($grid['dates'] as $date)
                                    @php
                                        $dateStr = $date->format('Y-m-d');
                                        $isToday = $date->isToday();
                                        $isSunday = $date->isSunday();
                                        $detail = $dailyDetails[$dateStr] ?? null;

                                        $status = $detail['status'] ?? '';
                                        $checkIn = $detail['check_in'] ?? '--:--';
                                        $checkOut = $detail['check_out'] ?? '--:--';
                                        $isLate = !empty($detail['is_late']);

                                        $tooltip = $date->translatedFormat('d F Y');
                                        if ($status === 'Hadir') {
                                            $tooltip .= ' - Masuk: ' . $checkIn . ' - Pulang: ' . $checkOut . ($isLate ? ' - Terlambat' : '');
                                        } elseif (!empty($status)) {
                                            $tooltip .= ' - Status: ' . $status;
                                        }

                                        if ($status === 'Hadir') {
                                            $numberColor = $isLate ? 'text-amber-600' : 'text-emerald-600';
                                            $modalColor = $isLate ? 'amber' : 'emerald';
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
                                    @endphp

                                    @php
                                        $bonus = $detail['calculated_bonus'] ?? 0.00;
                                    @endphp
                                    <div
                                        @mouseenter="showTooltip($event, '{{ $date->translatedFormat('d F Y') }}', '{{ $status }}', '{{ $checkIn }}', '{{ $checkOut }}', {{ $isLate ? 'true' : 'false' }}, '{{ $modalColor }}', {{ $bonus }})"
                                        @mouseleave="hideTooltip()"
                                        class="group relative flex min-h-[26px] sm:min-h-[34px] items-center justify-center px-0.5 sm:px-1 transition-colors {{ $isToday ? 'bg-slate-950 text-white dark:bg-slate-100 dark:text-slate-900 rounded-[200px]' : 'hover:bg-slate-100 dark:hover:bg-slate-900/60' }} cursor-pointer">
                                        <span class="text-[12px] sm:text-[13px] font-normal leading-none tracking-[0.01em] {{ $isToday ? 'text-inherit' : $numberColor }}">
                                            {{ $date->format('d') }}
                                        </span>
                                    </div>
                                @endforeach

                                @foreach($grid['paddingEnd'] as $pDate)
                                    <div class="min-h-[26px] sm:min-h-[34px] px-0.5 sm:px-1"></div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <aside class="md:col-span-1 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-[18px] shadow-sm p-4 sm:p-5 flex flex-col justify-between">
                <div>
                    <!-- Total Bonus Card -->
                    <div class="mb-5 rounded-2xl bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/30 p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-6 h-6 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center shrink-0">
                                <i data-lucide="banknote" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400"></i>
                            </span>
                            <h5 class="text-[10px] font-bold text-emerald-800 dark:text-emerald-400 uppercase tracking-wider">Total Bonus Bulan Ini</h5>
                        </div>
                        <div class="mt-1">
                            <span class="text-xl font-extrabold text-slate-900 dark:text-slate-50">
                                Rp {{ number_format($totalBonus, 0, ',', '.') }}
                            </span>
                        </div>
                        <p class="text-[9px] text-slate-400 dark:text-slate-500 mt-1.5">
                            *Akumulasi bonus kehadiran periode cut-off.
                        </p>
                    </div>

                    <div class="flex items-center justify-between gap-3 pb-3 border-b border-slate-200 dark:border-slate-800">
                        <div>
                            <h4 class="text-sm sm:text-base font-semibold text-slate-900 dark:text-slate-50">Keterangan</h4>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500">Panduan warna dan detail absensi</p>
                        </div>
                    </div>

                    <div class="mt-4 space-y-3.5">
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 inline-flex h-2.5 w-2.5 rounded-full bg-slate-950 dark:bg-slate-100"></span>
                            <div>
                                <p class="text-xs font-medium text-slate-900 dark:text-slate-50">Tanggal hari ini</p>
                                <p class="text-[11px] leading-5 text-slate-500 dark:text-slate-400">Ditandai dengan blok gelap seperti pada template.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                            <div>
                                <p class="text-xs font-medium text-slate-900 dark:text-slate-50">Hadir</p>
                                <p class="text-[11px] leading-5 text-slate-500 dark:text-slate-400">Arahkan kursor untuk melihat jam masuk dan jam pulang.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 inline-flex h-2.5 w-2.5 rounded-full bg-amber-500"></span>
                            <div>
                                <p class="text-xs font-medium text-slate-900 dark:text-slate-50">Terlambat</p>
                                <p class="text-[11px] leading-5 text-slate-500 dark:text-slate-400">Jam masuk terlambat akan ditandai warna amber.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 inline-flex h-2.5 w-2.5 rounded-full bg-red-500"></span>
                            <div>
                                <p class="text-xs font-medium text-slate-900 dark:text-slate-50">Minggu / Libur</p>
                                <p class="text-[11px] leading-5 text-slate-500 dark:text-slate-400">Warna merah dipakai untuk hari Minggu atau status libur.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 p-3">
                    <p class="text-[11px] font-medium text-slate-900 dark:text-slate-50">Tooltip tanggal</p>
                    <p class="text-[11px] leading-5 text-slate-500 dark:text-slate-400 mt-1">
                        Detail jam masuk dan jam pulang muncul saat kursor diarahkan ke tanggal.
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
            :style="`left: ${tooltip.x}px; top: ${tooltip.y}px; transform: translate(-50%, -100%);`"
            class="absolute z-50 pointer-events-none min-w-[200px] bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl p-3.5 text-left text-xs transition-all duration-150"
            style="display: none;"
        >
            <!-- Date header -->
            <div class="font-bold text-slate-850 dark:text-slate-100 mb-2 border-b border-slate-100 dark:border-slate-800 pb-1.5" x-text="tooltip.date"></div>
            
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
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Bonus Kehadiran</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400" x-text="tooltip.bonus > 0 ? 'Rp ' + Number(tooltip.bonus).toLocaleString('id-ID') : 'Rp 0'"></span>
                    </div>
                </div>
            </template>
        </div>
    </div>
</x-admin-layout>
