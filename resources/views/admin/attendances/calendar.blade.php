<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Riwayat Absensi') }}
        </h2>
    </x-slot>

    <div class="p-6 space-y-6" x-data="{
        showModal: false,
        selectedDate: '',
        selectedStatus: '',
        selectedCheckIn: '',
        selectedCheckOut: '',
        selectedIsLate: false,
        selectedColor: '',
        openDetails(date, status, checkIn, checkOut, isLate, colorClass) {
            this.selectedDate = date;
            this.selectedStatus = status;
            this.selectedCheckIn = checkIn;
            this.selectedCheckOut = checkOut;
            this.selectedIsLate = isLate;
            this.selectedColor = colorClass;
            this.showModal = true;
        }
    }">
        <!-- HEADER SECTION -->
        <header class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 bg-white dark:bg-slate-950 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Data Riwayat Absensi</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Memantau waktu kedatangan dan kepulangan Anda secara komprehensif.</p>
            </div>
        </header>


        @php
            $start = \Carbon\Carbon::parse($startDate);
            $end = \Carbon\Carbon::parse($endDate);
            $report = $reports->first(); 
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
            $startMonthNum = $start->format('m');
            $startMonthName = $start->translatedFormat('F');
            $startYear = $start->format('Y');
        @endphp

        <!-- CALENDAR WALL-STYLE -->
        <section class="bg-[#fcfbf9] dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm w-full mx-auto p-4 sm:p-8 font-sans">
            <!-- Wall Calendar Header -->
            <div class="flex flex-col sm:flex-row items-center justify-between border-b-2 border-slate-300 dark:border-slate-700 mb-6 sm:gap-0" style="padding-bottom: 1.5rem !important;">
                <div class="flex flex-row items-center gap-1.5 sm:gap-2">
                    <span class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-slate-100 leading-none">{{ $startMonthNum }}</span>
                    <span class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-slate-100 leading-none">{{ $startMonthName }}</span>
                    <span class="text-2xl sm:text-3xl font-bold text-orange-600 dark:text-orange-500 leading-none">{{ $startYear }}</span>
                </div>
                
                <div class="w-full sm:w-auto mt-4 mb-6 sm:mt-0 sm:mb-0">
                    <form method="GET" action="{{ route('attendances.index') }}" class="m-0 w-full">
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
                    <div class="flex flex-col items-center justify-start min-h-[60px] sm:min-h-[90px]">
                    </div>
                @endforeach

                <!-- Main Dates -->
                @foreach($dates as $date)
                    @php
                        $dateStr = $date->format('Y-m-d');
                        $isToday = $date->isToday();
                        $isSunday = $date->isSunday();
                        $isFriday = $date->isFriday();
                        $detail = $dailyDetails[$dateStr] ?? null;
                        
                        $status = $detail['status'] ?? '';
                        $checkIn = $detail['check_in'] ?? '--:--';
                        $checkOut = $detail['check_out'] ?? '--:--';
                        $isLate = !empty($detail['is_late']);
                        
                        $modalColor = 'slate';
                        if ($status === 'Hadir') {
                            $numberColor = $isLate ? 'text-amber-500' : 'text-emerald-500';
                            $modalColor = $isLate ? 'amber' : 'emerald';
                        } elseif ($status === 'Off' || $status === 'Libur' || strtolower($status) === 'x') {
                            $numberColor = 'text-red-500';
                            $modalColor = 'red';
                            $status = 'OFF';
                        } elseif ($status === 'Alfa') {
                            $numberColor = 'text-red-600';
                            $modalColor = 'red';
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
                            // Default colors if no specific status
                            if ($isSunday) {
                                $numberColor = 'text-red-600';
                            } else {
                                $numberColor = 'text-slate-800 dark:text-slate-100';
                            }
                        }
                    @endphp
                    
                    @if($status)
                        <button type="button" 
                            @click="openDetails('{{ $date->translatedFormat('l, d F Y') }}', '{{ $status }}', '{{ $checkIn }}', '{{ $checkOut }}', {{ $isLate ? 'true' : 'false' }}, '{{ $modalColor }}')"
                            class="group flex flex-col items-center justify-center min-h-[60px] sm:min-h-[90px] relative w-full rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800/50 hover:shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all cursor-pointer {{ $isToday ? 'bg-indigo-50/50 ring-2 ring-indigo-200' : '' }}">
                            
                            <span class="text-xl sm:text-4xl font-bold {{ $numberColor }} group-hover:scale-110 transition-transform duration-200">
                                {{ $date->format('d') }}
                            </span>
                            
                            <!-- Subtle indicator dot to show it has data -->
                            <span class="absolute bottom-1 sm:bottom-2 w-1 h-1 sm:w-1.5 sm:h-1.5 rounded-full bg-slate-300 dark:bg-slate-600 group-hover:bg-indigo-400 transition-colors"></span>
                        </button>
                    @else
                        <div class="flex flex-col items-center justify-center min-h-[60px] sm:min-h-[90px] relative rounded-xl {{ $isToday ? 'bg-indigo-50/50 ring-2 ring-indigo-200' : '' }}">
                            <span class="text-xl sm:text-4xl font-bold {{ $numberColor }}">
                                {{ $date->format('d') }}
                            </span>
                        </div>
                    @endif
                @endforeach

                <!-- Padding End -->
                @foreach($paddingEnd as $pDate)
                    <div class="flex flex-col items-center justify-start min-h-[60px] sm:min-h-[90px]">
                    </div>
                @endforeach
            </div>
            
            <div class="mt-8 pt-4 border-t border-slate-200 dark:border-slate-800 flex flex-wrap items-center gap-1.5 text-[10px] font-semibold">
                <span class="text-slate-500 uppercase tracking-wider">Tip:</span>
                <span class="text-slate-600 dark:text-slate-400">Klik langsung pada angka tanggal (berwarna) untuk melihat informasi detail absensi.</span>
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
                                <span class="text-sm font-medium text-slate-500 dark:text-slate-400">Status Kehadiran</span>
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
                            
                            <template x-if="selectedStatus === 'Hadir'">
                                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4 flex flex-col items-center justify-center text-center border border-slate-100 dark:border-slate-700/50">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Jam Masuk</span>
                                        <span class="text-xl font-bold" :class="selectedIsLate ? 'text-amber-600 dark:text-amber-500' : 'text-slate-700 dark:text-slate-200'" x-text="selectedCheckIn"></span>
                                        <template x-if="selectedIsLate">
                                            <span class="text-[9px] font-bold text-amber-700 bg-amber-100 px-1.5 py-0.5 rounded mt-1.5">Terlambat</span>
                                        </template>
                                    </div>
                                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4 flex flex-col items-center justify-center text-center border border-slate-100 dark:border-slate-700/50">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Jam Pulang</span>
                                        <span class="text-xl font-bold text-slate-700 dark:text-slate-200" x-text="selectedCheckOut"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>