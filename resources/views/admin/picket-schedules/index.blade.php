<x-admin-layout>
@section('title', 'Jadwal Piket')
<div class="p-6 space-y-6" x-data="{
    searchQuery: '',
    showSwapModal: false,
    selectedArea: ''
}">
    <!-- HEADER SECTION (Hidden during print) -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-200 dark:border-slate-800 pb-5 print:hidden">
        <div>
            <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Jadwal Piket</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Berlaku Periode: <span class="font-semibold text-indigo-600 dark:text-indigo-400">Agustus - Desember 2026</span>
            </p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('picket-schedules.download') }}" class="h-9 px-4 inline-flex items-center gap-2 bg-indigo-650 hover:bg-indigo-700 text-white rounded-xl shadow-xs text-xs font-semibold cursor-pointer transition-all duration-150">
                <i data-lucide="download" class="w-4 h-4"></i>
                Download Jadwal Piket
            </a>
            @if(auth()->user()->employee_id)
            <button @click="showSwapModal = true" class="h-9 px-4 inline-flex items-center gap-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl shadow-2xs text-xs font-semibold cursor-pointer transition-all duration-150">
                <i data-lucide="arrow-left-right" class="w-4 h-4 text-indigo-500"></i>
                Ajukan Tukar Hari
            </button>
            @endif
        </div>
    </div>

    <!-- DAILY TASK WIDGET FOR LOGGED IN TEACHER (Hidden during print) -->
    @if($myPicketToday)
    <div class="bg-gradient-to-r from-indigo-500/10 via-indigo-600/5 to-transparent border border-indigo-100 dark:border-indigo-900/30 rounded-2xl p-5 flex flex-col md:flex-row gap-5 items-start justify-between print:hidden">
        <div class="space-y-1">
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-400 border border-indigo-200/20 uppercase tracking-wide">
                Piket Hari Ini
            </div>
            <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">
                Tugas Piket Anda: {{ $myPicketToday->picketArea->name }}
            </h4>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Jam Tugas: <span class="font-bold text-slate-700 dark:text-slate-300">{{ $myPicketToday->picketArea->duty_hours }} WIB</span>
            </p>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800/80 rounded-xl p-4 w-full md:max-w-xl shadow-3xs">
            <h5 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2.5">Tupoksi Kerja (Jobs)</h5>
            <ul class="text-xs text-slate-600 dark:text-slate-400 space-y-2">
                @if($myPicketToday->picketArea->jobs)
                    @foreach(explode("\n", $myPicketToday->picketArea->jobs) as $job)
                        @if(trim($job))
                        <li class="flex items-start gap-2.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 mt-1.5 shrink-0"></span>
                            <span>{{ trim($job) }}</span>
                        </li>
                        @endif
                    @endforeach
                @else
                    <li class="italic text-slate-400 text-xs">Belum ada rincian tupoksi kerja untuk area ini.</li>
                @endif
            </ul>
        </div>
    </div>
    @endif

    <!-- SWAP NOTIFICATIONS & TABS (Hidden during print) -->
    @if(count($pendingSwapsForMe) > 0 || count($mySubmittedSwaps) > 0)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 print:hidden">
        <!-- Swap Requests Pending My Action -->
        @if(count($pendingSwapsForMe) > 0)
        <div class="border border-slate-200 dark:border-slate-800 rounded-2xl p-5 space-y-4">
            <h4 class="text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                <i data-lucide="bell-ring" class="w-4 h-4 text-rose-500"></i>
                Persetujuan Tukar Piket (Menunggu Anda)
            </h4>
            <div class="space-y-3">
                @foreach($pendingSwapsForMe as $swap)
                <div class="bg-slate-50 dark:bg-slate-900/50 p-4 border border-slate-100 dark:border-slate-850 rounded-xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <div class="text-xs space-y-1">
                        <p class="font-bold text-slate-800 dark:text-slate-200">
                            {{ $swap->requester->name }} mengajak bertukar piket.
                        </p>
                        <p class="text-slate-500">
                            Jadwal Anda: <span class="font-semibold text-slate-700 dark:text-slate-350">{{ $swap->target_date->translatedFormat('d M Y') }}</span> 
                            ↔️ Dia: <span class="font-semibold text-slate-700 dark:text-slate-350">{{ $swap->requested_date->translatedFormat('d M Y') }}</span>
                        </p>
                        @if($swap->notes)
                        <p class="text-[11px] italic text-slate-400">Ket: "{{ $swap->notes }}"</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <form action="{{ route('picket-schedules.swap.approve-target', $swap->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="h-7 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[10px] font-bold cursor-pointer transition-colors shadow-3xs">Setujui</button>
                        </form>
                        <form action="{{ route('picket-schedules.swap.reject', $swap->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="h-7 px-3 bg-rose-50 hover:bg-rose-600 dark:bg-rose-950/20 dark:hover:bg-rose-900/40 text-rose-700 dark:text-rose-450 hover:text-white rounded-lg text-[10px] font-bold cursor-pointer transition-colors border border-rose-100/10">Tolak</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- My Submitted Swaps -->
        @if(count($mySubmittedSwaps) > 0)
        <div class="border border-slate-200 dark:border-slate-800 rounded-2xl p-5 space-y-4">
            <h4 class="text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                <i data-lucide="history" class="w-4 h-4 text-slate-500"></i>
                Riwayat Pengajuan Tukar Piket Anda
            </h4>
            <div class="space-y-3 max-h-48 overflow-y-auto pr-1">
                @foreach($mySubmittedSwaps as $swap)
                <div class="p-3 bg-slate-50 dark:bg-slate-900/30 border border-slate-100 dark:border-slate-850 rounded-xl flex items-center justify-between gap-3 text-xs">
                    <div class="space-y-1">
                        <p class="font-medium text-slate-700 dark:text-slate-300">
                            Tukar dengan: <span class="font-bold text-slate-800 dark:text-slate-100">{{ $swap->targetEmployee->name }}</span>
                        </p>
                        <p class="text-[11px] text-slate-400">
                            Hari saya: {{ $swap->requested_date->translatedFormat('d M Y') }} ↔️ Dia: {{ $swap->target_date->translatedFormat('d M Y') }}
                        </p>
                    </div>
                    <div>
                        @if($swap->status === 'pending')
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 border border-amber-200/20">Menunggu Guru</span>
                        @elseif($swap->status === 'approved_by_target')
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-indigo-50 dark:bg-indigo-950/20 text-indigo-700 dark:text-indigo-400 border border-indigo-200/20">Menunggu Waka/Kepsek</span>
                        @elseif($swap->status === 'approved')
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200/20">Selesai/Tukar</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-rose-50 dark:bg-rose-950/20 text-rose-700 dark:text-rose-400 border border-rose-200/20">Ditolak</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- FILTER / SEARCH BAR (Hidden during print) -->
    <div class="flex flex-col sm:flex-row items-center gap-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-3xs print:hidden">
        <div class="relative w-full sm:max-w-xs">
            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-3.5"></i>
            <input type="text" x-model="searchQuery" placeholder="Cari nama guru piket..." 
                class="w-full text-xs pl-9 pr-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 dark:focus:ring-indigo-900/30 transition-all">
        </div>
        <div class="w-full sm:w-auto">
            <span class="text-xs text-slate-400 font-semibold" x-show="searchQuery.trim() !== ''">
                Menyaring jadwal untuk kata kunci "<span x-text="searchQuery" class="text-slate-800 dark:text-slate-200"></span>"
            </span>
        </div>
    </div>

    <!-- POSTER JADWAL PIKET / BOARD VIEW (Visible on screen and print) -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-xs print:border-none print:shadow-none">
        
        <!-- DESKTOP & PRINT ONLY CONTAINER -->
        <div class="hidden md:block print:block">
            <!-- PRINT-ONLY HEADER POSTER STYLE -->
            <div class="hidden print:block text-center mb-6" style="font-family: system-ui, sans-serif;">
                <h1 class="text-2xl font-black uppercase tracking-wider text-slate-900 leading-normal">THE DUTY SCHEDULE FOR TEACHERS</h1>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-1">APPLICABLE FOR AUGUST - DECEMBER 2026</p>
                <div class="inline-block mt-3 border border-amber-300 bg-amber-50 px-4 py-1.5 rounded-full text-xs font-extrabold text-amber-800 tracking-wider">
                    ⏰ DUTY HOURS: 06.30 - 07.00
                </div>
            </div>

            <div class="overflow-x-auto print:overflow-visible">
                <table class="w-full text-xs border-collapse print:text-[10px]">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800">
                            <th class="px-4 py-4 text-left font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 min-w-[160px] md:min-w-[180px] w-48">Area</th>
                            <th class="px-4 py-4 text-left font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 min-w-[340px] md:min-w-[420px] print:min-w-[260px] w-96">Jobs (Tupoksi)</th>
                            @php
                                $days = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday'];
                            @endphp
                            @foreach($days as $idx => $day)
                                <th class="px-4 py-4 text-center font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 min-w-[150px] md:min-w-[180px] print:min-w-[110px]">
                                    {{ $day }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-850">
                        @forelse($areas as $area)
                        <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-900/10 transition-colors">
                            <!-- AREA COLUMN -->
                            <td class="px-4 py-4 align-top font-bold text-slate-800 dark:text-slate-200">
                                <div class="flex items-start gap-2.5">
                                    <div class="w-6 h-6 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-100/10 shadow-3xs mt-0.5 print:hidden">
                                        <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span>{{ $area->name }}</span>
                                        <span class="text-[10px] text-slate-400 font-normal mt-0.5 print:mt-0.5">Jam: {{ $area->duty_hours }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- JOBS COLUMN -->
                            <td class="px-4 py-4 align-top text-slate-600 dark:text-slate-455 pr-4 border-r border-slate-100 dark:border-slate-850/50 leading-relaxed font-medium">
                                <div class="space-y-1.5 whitespace-normal break-words">
                                    @if($area->jobs)
                                        @foreach(explode("\n", $area->jobs) as $job)
                                            @if(trim($job))
                                            <div class="flex items-start gap-2">
                                                <span class="w-1 h-1 rounded-full bg-slate-400 dark:bg-slate-600 mt-1.5 shrink-0"></span>
                                                <p>{{ trim($job) }}</p>
                                            </div>
                                            @endif
                                        @endforeach
                                    @else
                                        <span class="italic text-slate-400">Tidak ada tupoksi khusus.</span>
                                    @endif
                                </div>
                            </td>

                            <!-- DAYS COLUMNS -->
                            @foreach($days as $idx => $day)
                                <td class="px-4 py-4 align-top border-r border-slate-100 dark:border-slate-850/30">
                                    <div class="flex flex-col gap-1.5">
                                        @php
                                            $daySchedules = $area->schedules->where('day_of_week', $idx);
                                        @endphp
                                        @forelse($daySchedules as $schedule)
                                            <!-- Teacher Card -->
                                            <div class="p-2 border rounded-xl flex items-center gap-2 transition-all shadow-3xs"
                                                :class="searchQuery.trim() !== '' && '{{ addslashes(strtolower($schedule->employee->name)) }}'.includes(searchQuery.toLowerCase())
                                                    ? 'bg-indigo-500/10 border-indigo-500 dark:border-indigo-500 text-indigo-700 dark:text-indigo-300 scale-[1.02] shadow-xs'
                                                    : 'bg-slate-50/50 dark:bg-slate-900/30 border-slate-200/50 dark:border-slate-850 text-slate-700 dark:text-slate-350'">
                                                
                                                <!-- Teacher avatar inside circle (hidden during print) -->
                                                <div class="w-6 h-6 rounded-full bg-slate-200 dark:bg-slate-800 text-slate-655 dark:text-slate-400 font-bold text-[9px] flex items-center justify-center shrink-0 uppercase border border-slate-300/10 print:hidden">
                                                    @if($schedule->employee->photo)
                                                        <img src="{{ asset('storage/' . $schedule->employee->photo) }}" class="w-full h-full object-cover rounded-full">
                                                    @else
                                                        {{ substr($schedule->employee->raw_name, 0, 2) }}
                                                    @endif
                                                </div>

                                                <div class="flex flex-col leading-tight min-w-0">
                                                    <span class="font-bold text-[11px] truncate" :class="searchQuery.trim() !== '' && '{{ addslashes(strtolower($schedule->employee->name)) }}'.includes(searchQuery.toLowerCase()) ? 'text-indigo-700 dark:text-indigo-200' : 'text-slate-800 dark:text-slate-200'">
                                                        {{ $schedule->employee->name }}
                                                    </span>
                                                    <span class="text-[9px] text-slate-400 font-medium truncate mt-0.5">{{ $schedule->employee->position ?? $schedule->employee->employeeType->name ?? '-' }}</span>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-2.5 border border-dashed border-slate-200 dark:border-slate-800 rounded-xl text-slate-400 dark:text-slate-600 text-[10px] select-none">Kosong</div>
                                        @endforelse
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-16 text-slate-455">Belum ada data area dan jadwal piket.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PRINT-ONLY RULES SECTION -->
            <div class="hidden print:block border-t border-slate-200 mt-6 pt-5" style="font-family: system-ui, sans-serif;">
                <div class="grid grid-cols-2 gap-6 text-[10px] text-slate-600">
                    <div>
                        <h5 class="font-bold text-slate-900 uppercase mb-1">NOTE:</h5>
                        <ol class="list-decimal pl-4 space-y-1">
                            <li>A duty is a compulsory (Piket bersifat wajib bagi seluruh guru).</li>
                            <li>It is possible to change your day as far as you get agreement with another teacher and inform it to the headmaster or vice headmaster (Pergantian hari piket dimungkinkan dengan bertukar hari dengan guru lain dan dilaporkan ke Kepala Sekolah/Waka).</li>
                        </ol>
                    </div>
                    <div class="text-right flex flex-col justify-end">
                        <p class="font-bold text-slate-800">Mengetahui,</p>
                        <p class="font-bold text-slate-900 mt-12">Kepala Sekolah</p>
                    </div>
                </div>
            </div>
        </div> <!-- End of DESKTOP & PRINT ONLY CONTAINER -->

        <!-- MOBILE VIEW (Pills & Day Cards) -->
        <div class="block md:hidden print:hidden p-4 space-y-4" x-data="{ mobileActiveDay: {{ Carbon\Carbon::now()->dayOfWeek === 0 ? 1 : Carbon\Carbon::now()->dayOfWeek }} }">
            <!-- Day Selector Buttons -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-2 no-scrollbar">
                @foreach($days as $idx => $day)
                    <button @click="mobileActiveDay = {{ $idx }}" 
                        :class="mobileActiveDay === {{ $idx }} 
                            ? 'bg-indigo-650 text-white font-bold shadow-xs' 
                            : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200/50'"
                        class="px-4 py-2 rounded-xl text-xs whitespace-nowrap transition-all duration-150 cursor-pointer border-0">
                        {{ $day }}
                    </button>
                @endforeach
            </div>

            <!-- Areas List for Active Day -->
            <div class="space-y-4 mt-2">
                @foreach($areas as $area)
                    <div class="bg-slate-50/50 dark:bg-slate-900/30 border border-slate-150 dark:border-slate-850 rounded-2xl p-4 space-y-3">
                        <!-- Area Header -->
                        <div class="flex justify-between items-start border-b border-slate-200/40 dark:border-slate-800 pb-2.5">
                            <div class="space-y-0.5 text-left">
                                <h4 class="font-bold text-slate-800 dark:text-slate-200 text-xs">{{ $area->name }}</h4>
                                <p class="text-[10px] text-slate-450 font-medium">⏰ {{ $area->duty_hours }}</p>
                            </div>
                        </div>

                        <!-- Teachers Assigned (for the active day) -->
                        <div class="space-y-2">
                            @foreach($days as $idx => $day)
                                <div x-show="mobileActiveDay === {{ $idx }}" class="space-y-2">
                                    @php
                                        $dayScheds = $area->schedules->where('day_of_week', $idx);
                                    @endphp
                                    @forelse($dayScheds as $schedule)
                                        <div class="p-3 border rounded-xl flex items-center justify-between gap-3 transition-all"
                                            :class="searchQuery.trim() !== '' && '{{ addslashes(strtolower($schedule->employee->name)) }}'.includes(searchQuery.toLowerCase())
                                                ? 'bg-indigo-500/10 border-indigo-500 text-indigo-700 dark:text-indigo-300'
                                                : 'bg-white dark:bg-slate-950 border-slate-200/80 dark:border-slate-850 text-slate-700 dark:text-slate-350'">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-800 text-slate-655 dark:text-slate-400 font-bold text-xs flex items-center justify-center shrink-0 uppercase border border-slate-300/10">
                                                    @if($schedule->employee->photo)
                                                        <img src="{{ asset('storage/' . $schedule->employee->photo) }}" class="w-full h-full object-cover rounded-full">
                                                    @else
                                                        {{ substr($schedule->employee->raw_name, 0, 2) }}
                                                    @endif
                                                </div>
                                                <div class="text-left leading-tight">
                                                    <p class="font-bold text-xs" :class="searchQuery.trim() !== '' && '{{ addslashes(strtolower($schedule->employee->name)) }}'.includes(searchQuery.toLowerCase()) ? 'text-indigo-700 dark:text-indigo-200' : 'text-slate-800 dark:text-slate-200'">
                                                        {{ $schedule->employee->name }}
                                                    </p>
                                                    <p class="text-[10px] text-slate-400 font-medium mt-0.5">{{ $schedule->employee->position ?? $schedule->employee->employeeType->name ?? '-' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-center py-4 text-slate-400 dark:text-slate-600 text-[11px] italic">Tidak ada jadwal piket hari ini</p>
                                    @endforelse
                                </div>
                            @endforeach
                        </div>

                        <!-- Jobs (Tupoksi) for this Area -->
                        @if($area->jobs)
                        <div x-data="{ openJobs: false }" class="pt-1.5">
                            <button @click="openJobs = !openJobs" class="flex items-center justify-between w-full text-[10px] font-bold text-slate-455 hover:text-slate-655 dark:hover:text-slate-200 cursor-pointer border-0 bg-transparent py-1">
                                <span>TULISAN TUPOKSI (JOBS)</span>
                                <i data-lucide="chevron-down" class="w-3.5 h-3.5 transition-transform duration-200" :style="openJobs ? 'transform: rotate(180deg);' : ''"></i>
                            </button>
                            <div x-show="openJobs" x-collapse class="mt-2 text-[11px] text-slate-600 dark:text-slate-400 space-y-1.5 pl-2 border-l border-slate-200 dark:border-slate-800 text-left">
                                @foreach(explode("\n", $area->jobs) as $job)
                                    @if(trim($job))
                                    <div class="flex items-start gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-450 mt-1.5 shrink-0"></span>
                                        <p class="leading-normal">{{ trim($job) }}</p>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- SWAP REQUEST MODAL (Hidden during print) -->
    <template x-teleport="body">
        <div x-cloak x-show="showSwapModal" class="fixed inset-0 z-[9999] overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen p-4 text-center">
                <!-- Backdrop -->
                <div x-show="showSwapModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showSwapModal = false" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity"></div>

                <!-- Modal Panel -->
                <div x-show="showSwapModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative w-full max-w-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl text-left shadow-xl overflow-hidden z-10 flex flex-col">
                    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-950">
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-200">Ajukan Permohonan Tukar Piket</h4>
                        <button @click="showSwapModal = false" class="p-1 rounded-lg text-slate-400 dark:text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-850 hover:text-slate-700 cursor-pointer border-0 bg-transparent flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        </button>
                    </div>

                    <form action="{{ route('picket-schedules.swap.request') }}" method="POST" class="p-6 space-y-4 text-xs">
                        @csrf
                        
                        <!-- Requested Date (My Picket Date) -->
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Tanggal Piket Saya Yang Ingin Ditukar</label>
                            <input type="date" name="requested_date" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500 transition-all">
                        </div>

                        <!-- Target Teacher -->
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Guru/Karyawan Yang Diajak Bertukar</label>
                            <select name="target_employee_id" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500 transition-all cursor-pointer">
                                <option value="">Pilih Guru...</option>
                                @foreach($employees as $emp)
                                    @if($emp->id != auth()->user()->employee_id)
                                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <!-- Target Date (Their Picket Date) -->
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Tanggal Piket Guru Target Yang Diinginkan</label>
                            <input type="date" name="target_date" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500 transition-all">
                        </div>

                        <!-- Notes / Reason -->
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Alasan / Catatan Tambahan (Opsional)</label>
                            <textarea name="notes" placeholder="Tulis alasan pertukaran..." rows="3" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500 transition-all resize-none"></textarea>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end gap-2.5 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" @click="showSwapModal = false" class="h-9 px-4 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-semibold cursor-pointer transition-colors">Batal</button>
                            <button type="submit" class="h-9 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold cursor-pointer transition-colors shadow-2xs">Kirim Permohonan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>

<!-- PRINT-ONLY CUSTOM STYLING -->
<style>
@media print {
    /* Hide general UI headers and main navigation */
    header, aside, #sidebar, #main-content > div:first-child, .print\:hidden {
        display: none !important;
    }
    
    #main-content {
        padding: 0 !important;
        margin: 0 !important;
        overflow: visible !important;
    }
    
    body {
        background-color: white !important;
        color: black !important;
        font-size: 8px !important;
    }
    
    /* Layout table landscaped correctly */
    @page {
        size: A4 landscape;
        margin: 1.2cm;
    }
    
    .print\:border-none {
        border: none !important;
    }
    
    /* Ensure clear text borders during print */
    table {
        border-collapse: collapse !important;
        width: 100% !important;
    }
    th, td {
        border: 1px solid #cbd5e1 !important;
        padding: 6px !important;
    }
}
</style>
</x-admin-layout>
