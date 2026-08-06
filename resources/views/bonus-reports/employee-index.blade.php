<x-admin-layout>
    <div class="p-6 space-y-6" x-data="{
        showModal: false,
        selectedDate: '',
        selectedStatus: '',
        selectedBonus: '',
        selectedColor: '',
        openDetails(date, status, bonus, colorClass) {
            this.selectedDate = date;
            this.selectedStatus = status;
            this.selectedBonus = bonus;
            this.selectedColor = colorClass;
            this.showModal = true;
        }
    }">
        <!-- HEADER SECTION -->
        <header class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Rekap Bonus Kehadiran</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Rincian perolehan bonus Anda berdasarkan kehadiran harian.</p>
            </div>
            <div class="flex flex-col md:items-end text-left md:text-right">
                <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Total Bulan Ini</span>
                <span class="text-2xl font-black text-emerald-600 dark:text-emerald-500">Rp {{ number_format($totalSemuaBonus ?? 0, 0, ',', '.') }}</span>
            </div>
        </header>

        @php
            $start = \Carbon\Carbon::parse($startDateReq);
            $end = \Carbon\Carbon::parse($endDateReq);
            $report = collect($paginatedReports->items())->first(); 
            $dailyDetails = $report['daily_details'] ?? [];

            // Get day of week of start date (0 = Sunday, 6 = Saturday)
            $startDayOfWeek = $start->dayOfWeek;
            
            // Generate blank items for padding start
            $paddingStart = [];
            for($i = 0; $i < $startDayOfWeek; $i++) {
                $paddingStart[] = $start->copy()->subDays($startDayOfWeek - $i);
            }

            // Generate main dates
            $dates = [];
            $curr = $start->copy();
            while($curr <= $end) {
                $dates[] = $curr->copy();
                $curr->addDay();
            }

            // Generate blank items for padding end to complete the grid (multiples of 7)
            $totalCells = count($paddingStart) + count($dates);
            $paddingEndCount = (7 - ($totalCells % 7)) % 7;
            $paddingEnd = [];
            $currEnd = $end->copy()->addDay();
            for($i = 0; $i < $paddingEndCount; $i++) {
                $paddingEnd[] = $currEnd->copy();
                $currEnd->addDay();
            }
            
            \Carbon\Carbon::setLocale('id');
            $reqMonthDate = \Carbon\Carbon::createFromFormat('Y-m', $month);
            $startMonthNum = $reqMonthDate->format('m');
            $startMonthName = $reqMonthDate->translatedFormat('F');
            $startYear = $reqMonthDate->format('Y');
        @endphp

        <!-- CALENDAR WALL-STYLE -->
        <section class="bg-[#fcfbf9] dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm w-full mx-auto p-4 sm:p-8 font-sans">
            <!-- Wall Calendar Header -->
            <div class="flex flex-col sm:flex-row items-center justify-between border-b-2 border-slate-300 dark:border-slate-700 mb-6 sm:gap-0" style="padding-bottom: 1.5rem !important;">
                <div class="flex flex-col gap-1">
                    <div class="flex flex-row items-center gap-1.5 sm:gap-2">
                        <span class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-slate-100 leading-none">{{ $startMonthNum }}</span>
                        <span class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-slate-100 leading-none">{{ $startMonthName }}</span>
                        <span class="text-2xl sm:text-3xl font-bold text-orange-600 dark:text-orange-500 leading-none">{{ $startYear }}</span>
                    </div>
                    <span class="text-xs font-semibold text-slate-500">Siklus: {{ $start->format('d/m/Y') }} - {{ $end->format('d/m/Y') }}</span>
                </div>
                
                <div class="w-full sm:w-auto mt-4 mb-6 sm:mt-0 sm:mb-0">
                    <form method="GET" action="{{ route('bonus-reports.index') }}" class="m-0 w-full">
                        <input type="month" name="month" lang="id-ID" value="{{ $month }}" onchange="this.form.submit()" class="w-full sm:w-auto h-10 px-4 text-sm font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 cursor-pointer shadow-sm transition-all">
                    </form>
                </div>
            </div>

            <!-- Calendar Days Header -->
            <div class="grid grid-cols-7 border-b border-slate-200 dark:border-slate-800 pb-2">
                @foreach([
                    ['name' => 'MINGGU', 'color' => 'text-red-600'],
                    ['name' => 'SENIN', 'color' => 'text-slate-800 dark:text-slate-200'],
                    ['name' => 'SELASA', 'color' => 'text-slate-800 dark:text-slate-200'],
                    ['name' => 'RABU', 'color' => 'text-slate-800 dark:text-slate-200'],
                    ['name' => 'KAMIS', 'color' => 'text-slate-800 dark:text-slate-200'],
                    ['name' => 'JUMAT', 'color' => 'text-slate-800 dark:text-slate-200'],
                    ['name' => 'SABTU', 'color' => 'text-slate-800 dark:text-slate-200']
                ] as $idx => $day)
                    <div class="text-center">
                        <span class="text-[10px] sm:text-sm font-bold uppercase tracking-wider sm:tracking-widest {{ $day['color'] }}">{{ $day['name'] }}</span>
                    </div>
                @endforeach
            </div>

            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 mt-4 gap-y-4 sm:gap-y-8">
                <!-- Padding Start -->
                @foreach($paddingStart as $pDate)
                    <div class="flex flex-col items-center justify-start min-h-[70px] sm:min-h-[100px] opacity-40">
                        <span class="text-xl sm:text-3xl font-bold text-slate-400">
                            {{ $pDate->format('d') }}
                        </span>
                    </div>
                @endforeach

                <!-- Main Dates -->
                @foreach($dates as $date)
                    @php
                        $dateStr = $date->format('Y-m-d');
                        $isToday = $date->isToday();
                        $isSunday = $date->isSunday();
                        $detail = $dailyDetails[$dateStr] ?? null;
                        
                        $status = $detail['status'] ?? 'Tidak ada data';
                        $bonusNominal = $detail['bonus_nominal'] ?? 0;
                        
                        $modalColor = 'slate';
                        if ($bonusNominal > 0) {
                            $numberColor = 'text-emerald-600 dark:text-emerald-400';
                            $modalColor = 'emerald';
                        } elseif ($status === 'Present' || $status === 'Hadir') {
                            $numberColor = 'text-slate-800 dark:text-slate-100';
                            $modalColor = 'slate';
                        } elseif ($status === 'Off' || $status === 'Libur' || strtolower($status) === 'x' || $isSunday) {
                            $numberColor = 'text-red-500';
                            $modalColor = 'red';
                            if (!$detail) $status = 'Libur / Akhir Pekan';
                        } elseif ($status === 'Alfa') {
                            $numberColor = 'text-red-600';
                            $modalColor = 'red';
                        } elseif (in_array($status, ['Izin', 'Sakit', 'Cuti'])) {
                            $numberColor = 'text-amber-500';
                            $modalColor = 'amber';
                        } else {
                            $numberColor = $isSunday ? 'text-red-600' : 'text-slate-800 dark:text-slate-100';
                        }
                    @endphp
                    
                    <button type="button" 
                        @click="openDetails('{{ $date->translatedFormat('l, d F Y') }}', '{{ $status }}', 'Rp {{ number_format($bonusNominal, 0, ',', '.') }}', '{{ $modalColor }}')"
                        class="group flex flex-col items-center justify-start pt-2 min-h-[70px] sm:min-h-[100px] relative w-full rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800/50 hover:shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all cursor-pointer {{ $isToday ? 'bg-indigo-50/50 ring-2 ring-indigo-200' : '' }}">
                        
                        <span class="text-xl sm:text-4xl font-bold {{ $numberColor }} group-hover:scale-105 transition-transform duration-200">
                            {{ $date->format('d') }}
                        </span>
                        
                        <!-- Bonus Badge -->
                        @if($bonusNominal > 0)
                            @php 
                                $shortNominal = ($bonusNominal >= 1000) ? ($bonusNominal / 1000) . 'k' : $bonusNominal;
                            @endphp
                            <div class="mt-2 text-[10px] sm:text-xs font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-100 dark:bg-emerald-900/50 px-2 py-0.5 rounded-full shadow-sm">
                                +{{ $shortNominal }}
                            </div>
                        @else
                            <div class="mt-2 text-[10px] sm:text-xs font-bold text-slate-300 dark:text-slate-600">
                                -
                            </div>
                        @endif
                        
                    </button>
                @endforeach

                <!-- Padding End -->
                @foreach($paddingEnd as $pDate)
                    <div class="flex flex-col items-center justify-start min-h-[70px] sm:min-h-[100px] opacity-40">
                        <span class="text-xl sm:text-3xl font-bold text-slate-400">
                            {{ $pDate->format('d') }}
                        </span>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-8 pt-4 border-t border-slate-200 dark:border-slate-800 flex flex-wrap items-center gap-1.5 text-[10px] font-semibold">
                <span class="text-slate-500 uppercase tracking-wider">Tip:</span>
                <span class="text-slate-600 dark:text-slate-400">Klik langsung pada angka tanggal untuk melihat informasi detail harian.</span>
            </div>
        </section>

        <!-- POPUP MODAL ALPINE -->
        <div x-show="showModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>
            
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div x-show="showModal" 
                         x-transition:enter="ease-out duration-300" 
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave="ease-in duration-200" 
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         @click.outside="showModal = false"
                         class="relative transform overflow-hidden rounded-xl bg-white dark:bg-slate-900 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm border border-slate-200 dark:border-slate-800">
                        
                        <!-- Modal Header -->
                        <div class="px-4 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-800/50">
                            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100" id="modal-title" x-text="selectedDate"></h3>
                            <button @click="showModal = false" class="text-slate-400 hover:text-slate-500 dark:hover:text-slate-300 transition-colors">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Modal Body -->
                        <div class="px-4 sm:px-6 space-y-5" style="padding-top: 1.5rem; padding-bottom: 1.5rem !important;">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-slate-500 dark:text-slate-400">Status Harian</span>
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider"
                                      :class="{'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400': selectedColor === 'emerald',
                                               'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': selectedColor === 'red',
                                               'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400': selectedColor === 'amber',
                                               'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400': selectedColor === 'blue',
                                               'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400': selectedColor === 'purple',
                                               'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300': selectedColor === 'slate'}"
                                      x-text="selectedStatus">
                                </span>
                            </div>
                            
                            <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-4 flex flex-col items-center justify-center text-center border border-emerald-100 dark:border-emerald-700/30">
                                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-500 uppercase tracking-wider mb-1">Perolehan Bonus</span>
                                <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400" x-text="selectedBonus"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
