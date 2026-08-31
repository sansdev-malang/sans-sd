<x-admin-layout>
    <div class="p-6 space-y-6" x-data="attendanceLogs">
        
        <!-- HEADER -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Rekap Bonus Kehadiran</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Evaluasi kehadiran pegawai berdasarkan skema bonus aktif.</p>
            </div>
            
            <!-- EXPORT DATA (Pusat Style & Position) -->
            <div x-data="{ open: false }" class="relative inline-block text-left w-full md:w-auto">
                <button type="button" @click="open = !open" @click.outside="open = false" class="w-full md:w-auto justify-center h-9 px-4 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 font-semibold text-xs rounded-lg shadow-sm transition-all cursor-pointer whitespace-nowrap flex items-center gap-2">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    <span>Ekspor Data</span>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 opacity-70"></i>
                </button>
                
                <div x-show="open" x-transition.opacity.duration.200ms style="display: none;" class="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-lg z-50">
                    <a href="{{ route('bonus-reports.export', array_merge(request()->query(), ['format' => 'excel'])) }}" class="flex items-center gap-3 px-4 py-3 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors border-b border-slate-100 dark:border-slate-800">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-600 dark:text-emerald-500"></i>
                        Excel (.xlsx)
                    </a>
                    <a href="{{ route('bonus-reports.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" class="flex items-center gap-3 px-4 py-3 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-rose-50 dark:hover:bg-rose-900/30 hover:text-rose-700 dark:hover:text-rose-450 transition-colors">
                        <i data-lucide="file-text" class="w-4 h-4 text-rose-600 dark:text-rose-500"></i>
                        PDF (.pdf)
                    </a>
                </div>
            </div>
        </section>

        <!-- FILTERS -->
        <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm p-4 w-full text-left">
            <form method="GET" action="{{ route('bonus-reports.index') }}" class="flex flex-col md:flex-row gap-4 items-stretch md:items-center justify-between">
                
                <!-- Left Side: Search & Filters -->
                <div class="flex flex-wrap items-center gap-2 flex-1">
                    @if(auth()->user()->role !== 'employee')
                    <!-- Search Box Welded with Cari Button (Premium Input Group) -->
                    <div x-data="{ searchVal: '{{ request('search') }}' }" class="flex items-center w-full search-container bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg overflow-hidden shadow-inner focus-within:ring-0 focus-within:border-slate-300 dark:focus-within:border-slate-700">
                        <input type="text" name="search" x-model="searchVal" placeholder="Cari pegawai..."
                            style="border: none !important; outline: none !important; box-shadow: none !important;"
                            class="w-full h-9 px-3 text-xs bg-transparent text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-0">
                        <button type="submit" class="h-9 px-4 bg-slate-55 dark:bg-slate-855 text-slate-700 dark:text-slate-300 font-bold text-xs transition-all duration-150 cursor-pointer whitespace-nowrap flex items-center justify-center border-l border-slate-200 dark:border-slate-800">
                            Cari
                        </button>
                    </div>
                    @endif

                    <!-- Bulan -->
                    <input type="month" name="month" value="{{ request('month', $month) }}" onchange="this.form.submit()"
                        class="h-9 px-2.5 flex-1 sm:flex-initial sm:w-36 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 shadow-inner focus:outline-none focus:ring-0 focus:ring-transparent focus:border-slate-300 dark:focus:border-slate-700">

                    @if(auth()->user()->role !== 'employee')
                    <!-- Jabatan -->
                    <select name="position" onchange="this.form.submit()"
                        class="h-9 pl-2.5 pr-8 flex-1 sm:flex-initial sm:w-44 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 shadow-inner focus:outline-none focus:ring-0 focus:ring-transparent focus:border-slate-300 dark:focus:border-slate-700 cursor-pointer text-ellipsis overflow-hidden whitespace-nowrap">
                        <option value="">Semua Jabatan</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos }}" {{ request('position') == $pos ? 'selected' : '' }}>{{ $pos }}</option>
                        @endforeach
                    </select>
                    @endif

                    @if(request()->anyFilled(['search', 'position']) || request()->filled('month') && request('month') != now()->format('Y-m') || request()->filled('per_page') && request('per_page') != 50)
                        <a href="{{ route('bonus-reports.index') }}" class="h-9 px-3 flex items-center justify-center bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-300 rounded-lg transition-colors reset-filter-btn" title="Reset Filter">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>

                <!-- Right Side: Per Page Options -->
                <div class="flex items-center gap-2 w-full md:w-auto shrink-0 self-end md:self-auto justify-end">
                    @if(auth()->user()->role !== 'employee')
                    <select name="per_page" onchange="this.form.submit()"
                        class="h-9 pl-2.5 pr-8 flex-1 sm:flex-initial sm:w-28 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 shadow-inner focus:outline-none focus:ring-0 focus:ring-transparent focus:border-slate-300 dark:focus:border-slate-700 cursor-pointer text-ellipsis overflow-hidden whitespace-nowrap">
                        <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10 baris</option>
                        <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25 baris</option>
                        <option value="50" {{ request('per_page', '50') == '50' ? 'selected' : '' }}>50 baris</option>
                        <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 baris</option>
                        <option value="500" {{ request('per_page') == '500' ? 'selected' : '' }}>500 baris</option>
                        <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>Semua</option>
                    </select>
                    @endif
                </div>
            </form>
        </section>
        
        <!-- MAIN TABLE (MATRIX LAYOUT) -->
        @php
            $start = \Carbon\Carbon::parse($startDateReq);
            $end = \Carbon\Carbon::parse($endDateReq);
            $dates = [];
            $cycleDateData = [];
            while($start <= $end) {
                $dates[] = $start->copy();
                $cycleDateData[] = [
                    'dateStr' => $start->format('Y-m-d'),
                    'day' => (int)$start->format('d'),
                    'dayOfWeek' => (int)$start->format('N'),
                ];
                $start->addDay();
            }
            
            if (!function_exists('getInitials')) {
                function getInitials($name) {
                    if (empty($name)) return '?';
                    $words = explode(' ', $name);
                    if (count($words) >= 2) {
                        return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                    }
                    return strtoupper(substr($name, 0, 2));
                }
            }
            
            $colors = ['#6366f1', '#ec4899', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6', '#14b8a6', '#f43f5e', '#0ea5e9', '#d946ef'];
        @endphp

        <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden w-full p-0">
            <div class="overflow-x-auto" style="max-height: calc(100vh - 280px); overflow-y: auto;">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900">
                            <th class="px-4 py-3 bg-slate-50 dark:bg-slate-900 text-left sticky top-0 left-0 z-40 border-r border-slate-200 dark:border-slate-800 min-w-[200px]">
                                <div class="flex items-center gap-2 justify-between">
                                    <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Profil Pegawai</span>
                                    <span class="text-[9px] text-slate-400 dark:text-slate-550 font-bold whitespace-nowrap bg-slate-100 dark:bg-slate-800/40 px-1.5 py-0.5 rounded border border-slate-200/40 dark:border-slate-800/50" title="Siklus Cut-off Payroll">
                                        {{ \Carbon\Carbon::parse($startDateReq)->format('d M') }} - {{ \Carbon\Carbon::parse($endDateReq)->format('d M') }}
                                    </span>
                                </div>
                            </th>
                            <th class="px-4 py-3 bg-slate-50 dark:bg-slate-900 text-center sticky top-0 left-[200px] z-40 border-r border-slate-200 dark:border-slate-800 min-w-[120px]">
                                <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Bonus</span>
                            </th>
                            @foreach($dates as $date)
                            @php
                                $isToday = $date->isToday();
                                $isSunday = $date->isSunday();
                                $dayColor = $isSunday && !$isToday ? 'text-red-400' : ($isToday ? '' : 'text-slate-400 dark:text-slate-500');
                                $numColor = $isSunday && !$isToday ? 'text-red-500 dark:text-red-400' : ($isToday ? 'text-white' : 'text-slate-700 dark:text-slate-200');
                                $bgToday = $isToday ? 'bg-indigo-600 dark:bg-indigo-500 w-6 h-6 flex items-center justify-center rounded-full' : '';
                            @endphp
                            <th class="py-2 px-1 text-center sticky top-0 z-30 bg-slate-50 dark:bg-slate-900 min-w-[48px] max-w-[48px] border-r border-slate-100 dark:border-slate-800/60">
                                <div class="flex flex-col items-center gap-0.5 py-0.5">
                                    <span class="text-[9px] font-semibold {{ $dayColor }} uppercase tracking-wider">{{ $date->translatedFormat('D') }}</span>
                                    <span class="text-[11px] font-bold {{ $numColor }} {{ $bgToday }}">{{ $date->format('d') }}</span>
                                </div>
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        @forelse($paginatedReports as $index => $report)
                            @php
                                $empName = $report['employee']['name'] ?? 'Tidak Diketahui';
                                $color = $colors[$index % count($colors)];
                                $initial = getInitials($empName);
                            @endphp
                            <tr class="group hover:bg-slate-50/60 dark:hover:bg-slate-900/30 transition-colors">
                                <!-- KOLOM 1: PROFIL -->
                                <td class="px-4 py-2 sticky left-0 z-10 bg-white dark:bg-slate-900 group-hover:bg-slate-50/60 dark:group-hover:bg-slate-900/30 border-r border-slate-100 dark:border-slate-800/60 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <!-- Clickable Avatar -->
                                        <div @click='openCalendarModal(@json($report))' class="cursor-pointer hover:scale-105 transform transition-all active:scale-95 duration-150 shrink-0">
                                            @if(!empty($report['employee']['photo']))
                                                <img src="{{ str_contains($report['employee']['photo'], 'photos/') ? '/storage/' . $report['employee']['photo'] : '/storage/photos/' . $report['employee']['photo'] }}" class="w-8 h-8 rounded-full object-cover border border-slate-200/50 dark:border-slate-800/40">
                                            @else
                                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white shadow-sm" style="background:{{ $color }}">{{ $initial }}</div>
                                            @endif
                                        </div>
                                        <div class="flex flex-col min-w-0">
                                            <span @click='openCalendarModal(@json($report))' class="text-xs font-bold text-slate-900 dark:text-slate-100 truncate cursor-pointer hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ $empName }}</span>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[10px] text-slate-500 dark:text-slate-400 truncate">{{ $report['employee']['position'] ?? ($report['employee']['subject_position'] ?? '-') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <!-- KOLOM 2: TOTAL BONUS -->
                                <td class="px-4 py-2 sticky left-[200px] z-10 bg-white dark:bg-slate-900 group-hover:bg-slate-50/60 dark:group-hover:bg-slate-900/30 border-r border-slate-100 dark:border-slate-800/60 text-center transition-colors">
                                    @if($report['bonus_nominal'] > 0)
                                        <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($report['bonus_nominal'], 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-sm font-bold text-slate-400 dark:text-slate-500">Rp 0</span>
                                    @endif
                                </td>
                                
                                <!-- KOLOM 3+: TANGGAL -->
                                @foreach($dates as $date)
                                @php
                                    $dateStr = $date->format('Y-m-d');
                                    $detail = $report['daily_details'][$dateStr] ?? null;
                                @endphp
                                <td class="py-1 px-1 text-center border-r border-slate-50 dark:border-slate-800/30">
                                    @if($detail && !in_array($detail['status'] ?? '', ['Off', 'Libur']))
                                        @if($detail['bonus_nominal'] > 0)
                                            @php 
                                                $nominal = $detail['bonus_nominal'];
                                                $shortNominal = ($nominal >= 1000) ? ($nominal / 1000) . 'k' : $nominal;
                                            @endphp
                                            <div class="mx-auto w-9 h-6 flex items-center justify-center bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 font-bold text-[10px] rounded shadow-sm border border-emerald-200 dark:border-emerald-800/50" title="Rp {{ number_format($nominal, 0, ',', '.') }}">
                                                {{ $shortNominal }}
                                            </div>
                                        @else
                                            @if($dateStr > date('Y-m-d'))
                                                <div class="mx-auto flex items-center justify-center text-slate-300 dark:text-slate-700 font-medium text-[10px]">-</div>
                                            @else
                                                <div class="mx-auto w-9 h-6 flex items-center justify-center bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 font-bold text-[10px] rounded shadow-sm border border-red-200 dark:border-red-800/50" title="Tidak ada bonus">
                                                    0K
                                                </div>
                                            @endif
                                        @endif
                                    @else
                                        @if($date->isSunday())
                                            <div class="mx-auto flex items-center justify-center text-red-400/80 dark:text-red-900/50 font-bold text-[10px]" title="Hari Minggu (OFF)">OFF</div>
                                        @else
                                            <div class="mx-auto flex items-center justify-center text-slate-400 dark:text-slate-500 font-bold text-[10px]" title="Jadwal Off/Libur">OFF</div>
                                        @endif
                                    @endif
                                </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($dates) + 2 }}" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-slate-500 dark:text-slate-400">
                                        <i data-lucide="file-search" class="w-12 h-12 mb-4 text-slate-300 dark:text-slate-600"></i>
                                        <p class="text-sm font-medium">Tidak ada data pegawai yang ditemukan</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                                        </tbody>
                    <tfoot class="bg-slate-50 dark:bg-slate-900 sticky bottom-0 z-30 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
                        <tr>
                            <td class="px-4 py-3 font-bold text-slate-700 dark:text-slate-200 text-right sticky left-0 z-40 bg-slate-50 dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800">
                                TOTAL KESELURUHAN
                            </td>
                            <td class="px-4 py-3 font-bold text-emerald-600 dark:text-emerald-400 text-center sticky left-[200px] z-40 bg-slate-50 dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800">
                                Rp {{ number_format($totalSemuaBonus ?? 0, 0, ',', '.') }}
                            </td>
                            <td colspan="{{ count($dates) }}" class="bg-slate-50 dark:bg-slate-900"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            @if($paginatedReports->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30">
                    {{ $paginatedReports->links('pagination::tailwind') }}
                </div>
            @endif
        </section>

        <!-- CALENDAR DETAIL MODAL -->
        <div x-show="showCalendarModal" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
             
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showCalendarModal = false"></div>

            <!-- Modal Wrapper -->
            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-900 text-left shadow-2xl transition-all w-full max-w-lg border border-slate-200 dark:border-slate-800"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                     
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800/60 px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <!-- Circular Photo / Initials in Header -->
                            <template x-if="selectedReport && selectedReport.employee.photo">
                                <img :src="selectedReport.employee.photo.includes('photos/') ? '/storage/' + selectedReport.employee.photo : '/storage/photos/' + selectedReport.employee.photo" 
                                     class="w-9 h-9 rounded-full object-cover border border-slate-200 dark:border-slate-800 shrink-0">
                            </template>
                            <template x-if="selectedReport && !selectedReport.employee.photo">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-white text-xs shrink-0"
                                     style="background: #6366f1"
                                     x-text="getInitials(selectedReport.employee.name)"></div>
                            </template>
                            
                            <div class="text-left min-w-0">
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white truncate" x-text="selectedReport ? selectedReport.employee.name : ''"></h3>
                                <p class="text-[10px] text-slate-500 dark:text-slate-450 truncate">
                                    <span x-text="selectedReport ? (selectedReport.employee.position || '-') : ''"></span>
                                    &bull;
                                    <span x-text="selectedReport ? (selectedReport.employee.unit_name || '-') : ''" class="font-semibold text-indigo-650 dark:text-indigo-400"></span>
                                </p>
                            </div>
                        </div>
                        <button type="button" @click="showCalendarModal = false" class="rounded-lg p-1 text-slate-400 hover:bg-slate-55 dark:hover:bg-slate-800 hover:text-slate-655 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-4">
                        <!-- Calendar Header (Month Name) -->
                        <div class="text-center mb-3">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider" x-text="selectedReport ? selectedReport.month_name : ''"></span>
                        </div>

                        <!-- Days of Week Headers -->
                        <div class="grid grid-cols-7 gap-1 text-center mb-1">
                            <template x-for="dayName in ['Sn', 'Sl', 'Rb', 'Km', 'Jm', 'Sb', 'Mg']">
                                <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500" x-text="dayName"></span>
                            </template>
                        </div>

                        <!-- Calendar Grid -->
                        <div class="grid grid-cols-7 gap-1">
                            <template x-for="day in calendarDays" :key="day.dateStr">
                                <div class="aspect-square border border-slate-100 dark:border-slate-800/40 rounded-lg p-1 sm:p-1.5 flex flex-col justify-between"
                                     :class="[
                                         day.isCurrentMonth ? (day.dateStr && new Date(day.dateStr).getDay() === 0 ? 'bg-red-50/50 dark:bg-red-95/15' : 'bg-white dark:bg-slate-900') : 'bg-slate-50/50 dark:bg-slate-955/20 opacity-40'
                                     ]">
                                     
                                    <!-- Day Number -->
                                    <span class="text-[10px] font-bold"
                                          :class="[
                                              day.isCurrentMonth ? 'text-slate-700 dark:text-slate-300' : 'text-slate-400',
                                              (new Date(day.dateStr).getDay() === 0) ? 'text-red-505 font-bold' : ''
                                          ]"
                                          x-text="day.day"></span>
                                          
                                    <!-- Status Indicator inside day -->
                                    <div class="mt-auto w-full">
                                        <template x-if="selectedReport && selectedReport.daily_details[day.dateStr]">
                                            <div class="w-full">
                                                <template x-if="selectedReport.daily_details[day.dateStr].bonus_nominal > 0">
                                                    <div class="flex flex-col items-center justify-center leading-none py-1 bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 rounded-md">
                                                        <span class="text-[9px] font-extrabold">Rp</span>
                                                        <span class="text-[10px] font-black" x-text="formatRupiah(selectedReport.daily_details[day.dateStr].bonus_nominal)"></span>
                                                    </div>
                                                </template>
                                                <template x-if="!(selectedReport.daily_details[day.dateStr].bonus_nominal > 0)">
                                                    <div class="w-full py-1 text-center rounded-md text-[9px] font-bold">
                                                        <template x-if="selectedReport.daily_details[day.dateStr].status === 'Off'">
                                                            <span class="text-slate-400 dark:text-slate-500 font-extrabold">OFF</span>
                                                        </template>
                                                        <template x-if="selectedReport.daily_details[day.dateStr].status === 'Alfa'">
                                                            <span class="text-rose-500 dark:text-rose-400 font-extrabold">A</span>
                                                        </template>
                                                        <template x-if="selectedReport.daily_details[day.dateStr].status === 'Libur'">
                                                            <span class="text-slate-300 dark:text-slate-600 font-bold">-</span>
                                                        </template>
                                                        <template x-if="selectedReport.daily_details[day.dateStr].status !== 'Off' && selectedReport.daily_details[day.dateStr].status !== 'Alfa' && selectedReport.daily_details[day.dateStr].status !== 'Libur'">
                                                            <span class="text-slate-400 dark:text-slate-500 font-bold">Rp0</span>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Modal Footer (Stats Summary) -->
                    <div class="border-t border-slate-100 dark:border-slate-800/60 bg-slate-50/50 dark:bg-slate-900/30 px-5 py-3 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <!-- Stats & Total Bonus -->
                        <div class="flex flex-col gap-1.5 text-left w-full sm:w-auto">
                            <!-- Stats Grid -->
                            <div class="flex flex-wrap gap-2 text-[10px] text-slate-500 dark:text-slate-400">
                                <span>Hadir: <strong class="text-slate-700 dark:text-slate-300" x-text="stats.hadir"></strong></span>
                                <span>&bull;</span>
                                <span>Telat: <strong class="text-amber-500" x-text="stats.telat"></strong></span>
                                <span>&bull;</span>
                                <span>Alfa: <strong class="text-rose-500" x-text="stats.alfa"></strong></span>
                                <span>&bull;</span>
                                <span>Izin/Cuti: <strong class="text-purple-500" x-text="stats.izin"></strong></span>
                            </div>
                            <!-- Total Bonus -->
                            <div class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                Total Bonus: <span class="text-emerald-600 dark:text-emerald-400 text-sm font-black">Rp<span x-text="formatRupiah(selectedReport ? selectedReport.bonus_nominal : 0)"></span></span>
                            </div>
                        </div>
                        <button type="button" @click="showCalendarModal = false" class="w-full sm:w-auto h-8 px-4 bg-slate-100 hover:bg-slate-200 dark:bg-slate-850 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs rounded-lg transition-colors cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('attendanceLogs', () => ({
            showCalendarModal: false,
            selectedReport: null,
            startDateStr: '{{ \Carbon\Carbon::parse($startDateReq)->translatedFormat("d M Y") }}',
            endDateStr: '{{ \Carbon\Carbon::parse($endDateReq)->translatedFormat("d M Y") }}',
            cycleDates: @json($cycleDateData),
            calendarDays: [],
            stats: { hadir: 0, telat: 0, alfa: 0, izin: 0, off: 0 },
            
            formatRupiah(val) {
                if (!val) return '0';
                return new Intl.NumberFormat('id-ID').format(val);
            },
            
            getClassForLeave(day) {
                if (!day) return '';
                if (day.is_pending) {
                    return day.leave_code === 'S' ? 'bg-amber-50/40 text-amber-505 border border-dashed border-amber-200/50' :
                           day.leave_code === 'I' ? 'bg-purple-50/40 text-purple-505 border border-dashed border-purple-200/50' :
                           day.leave_code === 'C' ? 'bg-blue-50/40 text-blue-505 border border-dashed border-blue-200/50' :
                           day.leave_code === 'H' ? 'bg-emerald-50/40 text-emerald-505 border border-dashed border-emerald-200/50' : '';
                }
                return day.leave_code === 'S' ? 'bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-900/30' :
                       day.leave_code === 'I' ? 'bg-purple-50 dark:bg-purple-950/20 text-purple-600 dark:text-purple-400 border border-purple-100 dark:border-purple-900/30' :
                       day.leave_code === 'C' ? 'bg-blue-50 dark:bg-blue-950/20 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/30' :
                       day.leave_code === 'H' ? 'bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/30' : '';
            },
            openCalendarModal(report) {
                this.selectedReport = report;
                this.calendarDays = this.buildCutOffCalendar(this.cycleDates);
                this.stats = this.calculateStats(report);
                this.showCalendarModal = true;
            },
            calculateStats(report) {
                let stats = { hadir: 0, telat: 0, alfa: 0, izin: 0, off: 0 };
                if (!report || !report.daily_details) return stats;
                
                Object.values(report.daily_details).forEach(function(day) {
                    if (day.status === 'Hadir' || day.status === 'Present') {
                        stats.hadir++;
                        if (day.is_late) {
                            stats.telat++;
                        }
                    } else if (day.status === 'Alfa') {
                        stats.alfa++;
                    } else if (day.status === 'Cuti/Izin' || day.status === 'Dinas') {
                        stats.izin++;
                    } else if (day.status === 'Off') {
                        stats.off++;
                    }
                });
                return stats;
            },
            buildCutOffCalendar(cycleDates) {
                if (!cycleDates || cycleDates.length === 0) return [];
                
                const days = [];
                const firstDate = cycleDates[0];
                const lastDate = cycleDates[cycleDates.length - 1];
                
                // Pad start of the week (Monday is 1, Sunday is 7)
                for (let i = 1; i < firstDate.dayOfWeek; i++) {
                    days.push({ day: '', isCurrentMonth: false, dateStr: 'pad-start-' + i });
                }
                
                // Add all dates from the cycle
                cycleDates.forEach(function(d) {
                    days.push({ day: d.day, isCurrentMonth: true, dateStr: d.dateStr });
                });
                
                // Pad end of the week
                for (let i = 1; i <= (7 - lastDate.dayOfWeek); i++) {
                    days.push({ day: '', isCurrentMonth: false, dateStr: 'pad-end-' + i });
                }
                
                return days;
            },
            getInitials(name) {
                if (!name) return '?';
                const words = name.split(' ');
                if (words.length >= 2) {
                    return (words[0].substring(0, 1) + words[1].substring(0, 1)).toUpperCase();
                }
                return name.substring(0, 2).toUpperCase();
            }
        }));
    });
</script>
<style>
    @media (min-width: 768px) {
        .search-container {
            max-width: 280px !important;
        }
    }
    .search-container button:hover {
        background-color: #0f172a !important; /* bg-slate-900 */
        color: #ffffff !important; /* text-white */
    }
    .dark .search-container button:hover {
        background-color: #f8fafc !important; /* bg-slate-105 */
        color: #0f172a !important; /* text-slate-900 */
    }
    .dark input[type="month"]::-webkit-calendar-picker-indicator {
        filter: invert(1) !important;
    }
</style>
</x-admin-layout>
