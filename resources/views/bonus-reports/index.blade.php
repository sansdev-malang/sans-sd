<x-admin-layout>
    <div class="p-3.5 sm:p-5 lg:p-6 space-y-3 sm:space-y-4" x-data="attendanceLogs" @scroll.window="hideMatrixTooltip()">
        
        <!-- HEADER -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex flex-col gap-0.5">
                <h1 class="text-base sm:text-lg font-bold tracking-tight text-slate-900 dark:text-slate-50 font-nasalization">Uang Kehadiran</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400">Evaluasi ketepatan waktu kedatangan pegawai berdasarkan skema uang kehadiran aktif.</p>
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
        <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm p-2.5 sm:p-3 w-full text-left">
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
            <div class="overflow-x-auto" style="max-height: calc(100vh - 195px); overflow-y: auto;">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900">
                            <th class="px-3 py-2 bg-slate-50 dark:bg-slate-900 text-left sticky top-0 left-0 z-40 border-r border-slate-200 dark:border-slate-800 min-w-[200px]">
                                <div class="flex items-center gap-2 justify-between">
                                    <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Profil Pegawai</span>
                                    <span class="text-[9px] text-slate-400 dark:text-slate-550 font-bold whitespace-nowrap bg-slate-100 dark:bg-slate-800/40 px-1.5 py-0.5 rounded border border-slate-200/40 dark:border-slate-800/50" title="Siklus Cut-off Payroll">
                                        {{ \Carbon\Carbon::parse($startDateReq)->format('d M') }} - {{ \Carbon\Carbon::parse($endDateReq)->format('d M') }}
                                    </span>
                                </div>
                            </th>
                            <th class="px-3 py-2 bg-slate-50 dark:bg-slate-900 text-center sticky top-0 left-[200px] z-40 border-r border-slate-200 dark:border-slate-800 min-w-[120px]">
                                <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Uang Kehadiran</span>
                            </th>
                            @foreach($dates as $date)
                            @php
                                $isToday = $date->isToday();
                                $isSunday = $date->isSunday();
                                $dayColor = $isSunday && !$isToday ? 'text-red-400' : ($isToday ? '' : 'text-slate-400 dark:text-slate-500');
                                $numColor = $isSunday && !$isToday ? 'text-red-500 dark:text-red-400' : ($isToday ? 'text-white' : 'text-slate-700 dark:text-slate-200');
                                $bgToday = $isToday ? 'bg-indigo-600 dark:bg-indigo-500 w-5 h-5 flex items-center justify-center rounded-full' : '';
                            @endphp
                            <th class="py-1.5 px-0.5 text-center sticky top-0 z-30 bg-slate-50 dark:bg-slate-900 min-w-[44px] max-w-[48px] border-r border-slate-100 dark:border-slate-800/60">
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
                                <td class="px-3 py-1.5 sticky left-0 md:z-20 z-10 bg-white dark:bg-slate-900 group-hover:bg-slate-50 dark:group-hover:bg-[#162033] border-r border-slate-100 dark:border-slate-800/60 transition-colors">
                                    <div class="flex items-center gap-2.5">
                                        <!-- Clickable Avatar -->
                                        <div @click='openCalendarModal(@json($report))' class="cursor-pointer hover:scale-105 transform transition-all active:scale-95 duration-150 shrink-0">
                                            @if(!empty($report['employee']['photo']))
                                                <img src="{{ str_contains($report['employee']['photo'], 'photos/') ? '/storage/' . $report['employee']['photo'] : '/storage/photos/' . $report['employee']['photo'] }}" class="w-7 h-7 rounded-full object-cover border border-slate-200/50 dark:border-slate-800/40">
                                            @else
                                                <div class="w-7 h-7 rounded-full flex items-center justify-center text-[10px] font-bold text-white shadow-sm" style="background:{{ $color }}">{{ $initial }}</div>
                                            @endif
                                        </div>
                                        <div class="flex flex-col min-w-0">
                                            <span @click='openCalendarModal(@json($report))' class="text-xs font-bold text-slate-900 dark:text-slate-100 truncate cursor-pointer hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors leading-tight">{{ $empName }}</span>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[10px] text-slate-500 dark:text-slate-400 truncate leading-tight">{{ $report['employee']['position'] ?? ($report['employee']['subject_position'] ?? '-') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <!-- KOLOM 2: TOTAL BONUS -->
                                <td class="px-3 py-1.5 sticky left-[200px] md:z-20 z-10 bg-white dark:bg-slate-900 group-hover:bg-slate-50 dark:group-hover:bg-[#162033] border-r border-slate-100 dark:border-slate-800/60 text-center transition-colors">
                                    @if($report['bonus_nominal'] > 0)
                                        <span class="text-xs sm:text-sm font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($report['bonus_nominal'], 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-xs sm:text-sm font-bold text-slate-400 dark:text-slate-500">Rp 0</span>
                                    @endif
                                </td>
                                
                                <!-- KOLOM 3+: TANGGAL -->
                                @foreach($dates as $date)
                                @php
                                    $dateStr = $date->format('Y-m-d');
                                    $detail = $report['daily_details'][$dateStr] ?? null;
                                    $isPicket = $detail['is_picket'] ?? false;
                                    $picketArea = $detail['picket_area'] ?? 'Area Piket';
                                    $statusName = $detail['status'] ?? '';
                                    $isReward = $statusName === 'Reward Libur' || !empty($detail['is_reward_holiday']);
                                    $isFuture = $dateStr > date('Y-m-d');
                                    $isKedinasanStatus = $statusName === 'Dinas' || 
                                        str_contains(strtolower($statusName), 'dinas') || 
                                        str_contains(strtolower($statusName), 'kedinasan');

                                    if ($detail) {
                                        if ($isFuture) {
                                            $statusBadge = 'Belum Terlaksana';
                                            $statusColor = 'slate';
                                            $noteText = 'Tanggal dan presensi belum berjalan.';
                                        } elseif ($isReward) {
                                            $statusBadge = 'Libur Reward';
                                            $statusColor = 'emerald';
                                            $noteText = 'Bebas keterlambatan (' . ($detail['reward_name'] ?? 'Libur Reward') . ').';
                                        } elseif ($isKedinasanStatus) {
                                            $shortStatus = str_contains($statusName, '(') ? trim(explode('(', $statusName)[0]) : $statusName;
                                            $statusBadge = 'Tugas Dinas';
                                            $statusColor = 'blue';
                                            $noteText = 'Dispensasi absensi tugas dinas: ' . $shortStatus . '.';
                                        } elseif ($isPicket) {
                                            $isLate = !empty($detail['late_minutes']) && $detail['late_minutes'] > 0;
                                            $statusBadge = $isLate ? 'Piket (Telat)' : 'Piket Hadir';
                                            $statusColor = $isLate ? 'amber' : 'emerald';
                                            $noteText = 'Jadwal piket di ' . $picketArea . '.';
                                        } elseif (!empty($detail['late_minutes']) && $detail['late_minutes'] > 0) {
                                            $statusBadge = 'Terlambat';
                                            $statusColor = 'amber';
                                            $noteText = 'Terlambat ' . $detail['late_minutes'] . ' menit dari batas shift.';
                                        } elseif (!empty($detail['check_in']) && $detail['check_in'] !== 'Tidak Hadir') {
                                            $statusBadge = 'Tepat Waktu';
                                            $statusColor = 'emerald';
                                            $early = $detail['early_minutes'] ?? 0;
                                            $noteText = $early > 0 ? 'Datang ' . $early . ' menit lebih awal.' : 'Tepat waktu sesuai jadwal.';
                                        } else {
                                            $statusBadge = 'Tidak Hadir';
                                            $statusColor = 'rose';
                                            $noteText = 'Tidak tercatat data presensi kedatangan.';
                                        }

                                        if ($isReward) {
                                            $shiftLabel = 'Libur Reward';
                                            $jadwalStr = 'Bebas Jam';
                                        } elseif ($isPicket) {
                                            $shiftLabel = 'Piket: ' . $picketArea;
                                            $startJam = !empty($detail['shift_start']) ? substr($detail['shift_start'], 0, 5) : '06:30';
                                            $jadwalStr = $startJam;
                                        } else {
                                            $shiftLabel = !empty($detail['shift_name']) ? $detail['shift_name'] : 'Reguler';
                                            $startJam = !empty($detail['shift_start']) ? substr($detail['shift_start'], 0, 5) : '-';
                                            $endJam = !empty($detail['shift_end']) ? substr($detail['shift_end'], 0, 5) : '';
                                            $jadwalStr = $endJam ? "{$startJam} - {$endJam}" : $startJam;
                                        }

                                        if ($isFuture) {
                                            $inStr = '-';
                                            $outStr = '-';
                                        } else {
                                            $inRaw = !empty($detail['check_in']) ? $detail['check_in'] : 'Tidak Hadir';
                                            $inStr = (strlen($inRaw) > 5 && !in_array($inRaw, ['Tidak Hadir', 'REWARD', 'DINAS'])) ? substr($inRaw, 0, 5) : $inRaw;
                                            $outRaw = !empty($detail['check_out']) ? $detail['check_out'] : '-';
                                            $outStr = (strlen($outRaw) > 5 && !in_array($outRaw, ['REWARD', 'DINAS', '-'])) ? substr($outRaw, 0, 5) : $outRaw;
                                        }

                                        $cellData = [
                                            'employee' => [
                                                'name' => $empName,
                                                'unit' => $report['employee']['unit']['name'] ?? ($report['employee']['unit_name'] ?? 'SD'),
                                                'position' => $report['employee']['position'] ?? ($report['employee']['subject_position'] ?? '-'),
                                            ],
                                            'date_display' => $date->translatedFormat('d F Y'),
                                            'day_name' => $date->translatedFormat('l'),
                                            'is_today' => $date->isToday(),
                                            'is_sunday' => $date->isSunday(),
                                            'is_future' => $isFuture,
                                            'is_off' => false,
                                            'status' => $statusBadge,
                                            'status_color' => $statusColor,
                                            'is_picket' => $isPicket,
                                            'picket_area' => $picketArea,
                                            'is_reward' => $isReward,
                                            'reward_name' => $detail['reward_name'] ?? null,
                                            'is_dinas' => $isKedinasanStatus,
                                            'shift_name' => $shiftLabel,
                                            'jadwal' => $jadwalStr,
                                            'check_in' => $inStr,
                                            'check_out' => $outStr,
                                            'is_late' => (!empty($detail['late_minutes']) && $detail['late_minutes'] > 0),
                                            'late_minutes' => (int)($detail['late_minutes'] ?? 0),
                                            'early_minutes' => (int)($detail['early_minutes'] ?? 0),
                                            'bonus' => (int)($detail['bonus_nominal'] ?? 0),
                                            'tier_level' => $detail['tier_level'] ?? null,
                                            'schema_name' => !empty($detail['schema_name']) ? trim(preg_replace('/\s*\([^)]*toleransi[^)]*\)/i', '', $detail['schema_name'])) : null,
                                            'note' => $noteText,
                                        ];
                                    } else {
                                        $cellData = [
                                            'employee' => [
                                                'name' => $empName,
                                                'unit' => $report['employee']['unit']['name'] ?? ($report['employee']['unit_name'] ?? 'SD'),
                                                'position' => $report['employee']['position'] ?? ($report['employee']['subject_position'] ?? '-'),
                                            ],
                                            'date_display' => $date->translatedFormat('d F Y'),
                                            'day_name' => $date->translatedFormat('l'),
                                            'is_today' => $date->isToday(),
                                            'is_sunday' => $date->isSunday(),
                                            'is_future' => $isFuture,
                                            'is_off' => true,
                                            'status' => $date->isSunday() ? 'Akhir Pekan' : 'Jadwal Libur (OFF)',
                                            'status_color' => $date->isSunday() ? 'rose' : 'slate',
                                            'is_picket' => false,
                                            'picket_area' => null,
                                            'is_reward' => false,
                                            'reward_name' => null,
                                            'is_dinas' => false,
                                            'shift_name' => 'Jadwal Libur Rutin',
                                            'jadwal' => 'OFF',
                                            'check_in' => '-',
                                            'check_out' => '-',
                                            'is_late' => false,
                                            'late_minutes' => 0,
                                            'early_minutes' => 0,
                                            'bonus' => 0,
                                            'tier_level' => null,
                                            'schema_name' => null,
                                            'note' => $date->isSunday() ? 'Hari libur akhir pekan.' : 'Hari libur rutin pegawai.',
                                        ];
                                    }
                                @endphp
                                <td class="group/cell py-0.5 px-0.5 text-center border-r border-slate-50 dark:border-slate-800/30 cursor-pointer transition-all duration-75 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:ring-1.5 hover:ring-inset hover:ring-indigo-400 dark:hover:ring-indigo-500/70 {{ $date->isSunday() ? 'bg-red-50/30 dark:bg-red-950/10' : '' }}"
                                    @mouseenter="showMatrixTooltip($event, @js($cellData))"
                                    @mouseleave="hideMatrixTooltip()"
                                    @click.stop="toggleMatrixTooltip($event, @js($cellData))">
                                    @if($detail)
                                        @if(($detail['bonus_nominal'] ?? 0) > 0)
                                            @php 
                                                $nominal = $detail['bonus_nominal'];
                                                $shortNominal = ($nominal >= 1000) ? ($nominal / 1000) . 'k' : $nominal;
                                            @endphp
                                            <div class="relative mx-auto w-7 h-5 flex items-center justify-center bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 font-bold text-[9px] rounded border group-hover/cell:scale-110 group-hover/cell:shadow-xs transition-transform duration-75 {{ $isPicket ? 'border-amber-400 dark:border-amber-500 shadow-sm' : 'border-emerald-200 dark:border-emerald-800/50' }}">
                                                {{ $shortNominal }}
                                                @if($isPicket)
                                                    <span class="absolute -top-1 -right-1 flex h-2 w-2">
                                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            @if($dateStr > date('Y-m-d'))
                                                <div class="mx-auto flex items-center justify-center text-slate-300 dark:text-slate-700 font-medium text-[9px] group-hover/cell:scale-110 transition-transform duration-75 {{ $isPicket ? 'text-amber-500 font-bold' : '' }}">-</div>
                                            @else
                                                <div class="relative mx-auto w-7 h-5 flex items-center justify-center bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 font-bold text-[9px] rounded border group-hover/cell:scale-110 group-hover/cell:shadow-xs transition-transform duration-75 {{ $isPicket ? 'border-amber-400 dark:border-amber-500' : 'border-red-200 dark:border-red-800/50' }}">
                                                    0k
                                                    @if($isPicket)
                                                        <span class="absolute -top-1 -right-1 flex h-2 w-2">
                                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        @endif
                                    @else
                                        @if($date->isSunday())
                                            <div class="mx-auto flex items-center justify-center text-red-400/80 dark:text-red-900/50 font-bold text-[9px]">OFF</div>
                                        @else
                                            <div class="mx-auto flex items-center justify-center text-slate-400 dark:text-slate-500 font-bold text-[9px]">OFF</div>
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
                            <td class="px-3 py-2 font-bold text-slate-700 dark:text-slate-200 text-right sticky left-0 z-40 bg-slate-50 dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 text-xs">
                                TOTAL KESELURUHAN
                            </td>
                            <td class="px-3 py-2 font-bold text-emerald-600 dark:text-emerald-400 text-center sticky left-[200px] z-40 bg-slate-50 dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 text-xs sm:text-sm">
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
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800/60 px-5 py-3.5 gap-3">
                        <div class="flex items-center gap-3 min-w-0">
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
                        <div class="flex items-center gap-2.5 shrink-0">
                            <div class="text-right px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/80 dark:border-emerald-800/60 shadow-xs">
                                <span class="text-[9px] font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider block leading-none">Total Uang Kehadiran</span>
                                <span class="text-xs sm:text-sm font-extrabold text-emerald-600 dark:text-emerald-400 font-mono tracking-tight leading-tight block mt-0.5" x-text="selectedReport ? 'Rp ' + Number(selectedReport.bonus_nominal || 0).toLocaleString('id-ID') : 'Rp 0'"></span>
                            </div>
                            <button type="button" @click="showCalendarModal = false" class="rounded-lg p-1 text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-600 transition-colors">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
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
                                <div class="aspect-square border border-slate-100 dark:border-slate-800/40 rounded-lg p-0.5 sm:p-1 flex flex-col justify-between relative cursor-default"
                                     :class="[
                                         day.isCurrentMonth ? (day.dateStr && new Date(day.dateStr).getDay() === 0 ? 'bg-red-50/50 dark:bg-red-95/15' : 'bg-white dark:bg-slate-900') : 'bg-slate-50/50 dark:bg-slate-955/20 opacity-40',
                                         (selectedReport && selectedReport.daily_details[day.dateStr] && selectedReport.daily_details[day.dateStr].is_picket) ? 'border-amber-400 dark:border-amber-500/80 ring-1 ring-amber-400/30' : ''
                                     ]">
                                     
                                    <!-- Header Tanggal & Picket Icon -->
                                    <div class="flex items-center justify-between w-full px-0.5">
                                        <span class="text-[10px] font-bold"
                                              :class="[
                                                  day.isCurrentMonth ? 'text-slate-700 dark:text-slate-300' : 'text-slate-400',
                                                  (new Date(day.dateStr).getDay() === 0) ? 'text-red-505 font-bold' : ''
                                              ]"
                                              x-text="day.day"></span>
                                        <template x-if="selectedReport && selectedReport.daily_details[day.dateStr] && selectedReport.daily_details[day.dateStr].is_picket">
                                            <span class="text-[9px] leading-none text-amber-500 dark:text-amber-400 font-bold">⚡</span>
                                        </template>
                                    </div>
                                          
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
                                                    <div class="w-full py-0.5 text-center rounded-md text-[8px] font-bold flex flex-col items-center justify-center leading-tight">
                                                        <template x-if="selectedReport.daily_details[day.dateStr].status === 'Off'">
                                                            <span class="text-slate-400 dark:text-slate-500 font-extrabold text-[8px]">OFF</span>
                                                        </template>
                                                        <template x-if="selectedReport.daily_details[day.dateStr].status === 'Alfa'">
                                                            <span class="text-rose-500 dark:text-rose-400 font-extrabold text-[8px]">Alfa</span>
                                                        </template>
                                                        <template x-if="selectedReport.daily_details[day.dateStr].status === 'Libur'">
                                                            <span class="text-slate-400 dark:text-slate-500 font-bold text-[8px]">Libur</span>
                                                        </template>
                                                        <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500">Rp 0</span>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                        <template x-if="day.isCurrentMonth && (!selectedReport || !selectedReport.daily_details[day.dateStr])">
                                            <div class="w-full py-0.5 text-center text-slate-400 dark:text-slate-500 rounded-md text-[8px] font-bold flex flex-col items-center justify-center leading-tight">
                                                <span class="text-[8px] font-extrabold">OFF</span>
                                                <span class="text-[9px] font-bold">Rp 0</span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>                    <!-- Modal Footer (Stats Summary) -->
                    <div class="border-t border-slate-100 dark:border-slate-800/60 bg-slate-50/50 dark:bg-slate-900/30 px-5 py-3 flex flex-row items-center justify-between gap-4">
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
                        <button type="button" @click="showCalendarModal = false" class="w-full sm:w-auto h-8 px-4 bg-slate-100 hover:bg-slate-200 dark:bg-slate-850 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs rounded-lg transition-colors cursor-pointer">
                             Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- FLOATING RICH TOOLTIP CARD -->
        <div 
            x-show="matrixTooltip.show && matrixTooltip.data"
            x-cloak
            :style="`position: fixed; left: ${matrixTooltip.x}px; top: ${matrixTooltip.y}px; transform: translate(-50%, ${matrixTooltip.placement === 'bottom' ? '0%' : '-100%'}); z-index: 9999;`"
            class="pointer-events-none w-[285px] max-w-[calc(100vw-24px)] bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl shadow-2xl p-3.5 text-left text-xs select-none backdrop-blur-sm"
        >
            <!-- Pointer Arrow -->
            <div 
                class="absolute w-2.5 h-2.5 rotate-45 bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 transition-all duration-75"
                :class="matrixTooltip.placement === 'bottom' 
                    ? '-top-1.5 border-t border-l' 
                    : '-bottom-1.5 border-b border-r'"
                :style="`left: calc(50% + ${matrixTooltip.arrowOffset || 0}px); transform: translateX(-50%) rotate(45deg);`"
            ></div>

            <!-- Header: Pegawai & Tanggal -->
            <div class="border-b border-slate-100 dark:border-slate-800 pb-2 mb-2.5">
                <div class="flex items-center justify-between gap-2 mb-1">
                    <span class="font-bold text-[12px] text-slate-900 dark:text-slate-100 truncate" x-text="matrixTooltip.data?.employee?.name"></span>
                    <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 shrink-0 truncate max-w-[100px]" x-text="matrixTooltip.data?.employee?.unit"></span>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                    <div class="flex items-center gap-1.5 truncate">
                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="truncate" x-text="(matrixTooltip.data?.day_name ? matrixTooltip.data.day_name + ', ' : '') + (matrixTooltip.data?.date_display || '')"></span>
                    </div>
                    <template x-if="matrixTooltip.data?.is_today">
                        <span class="text-[8px] font-bold px-1.5 py-0.5 rounded bg-indigo-50 text-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-400 border border-indigo-200/50 shrink-0">Hari Ini</span>
                    </template>
                </div>
            </div>


            <!-- Shift & Jadwal Info -->
            <div class="flex items-center justify-between gap-2 px-2.5 py-1.5 mb-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 text-[10px]">
                <div class="flex items-center gap-1.5 truncate text-slate-600 dark:text-slate-400">
                    <template x-if="matrixTooltip.data?.is_picket">
                        <span class="text-amber-500 font-bold shrink-0">⚡ Piket</span>
                    </template>
                    <template x-if="!matrixTooltip.data?.is_picket">
                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </template>
                    <span class="truncate font-semibold text-slate-700 dark:text-slate-300" x-text="matrixTooltip.data?.shift_name || 'Reguler'"></span>
                </div>
                <span class="font-mono font-bold text-slate-600 dark:text-slate-300 shrink-0" x-text="matrixTooltip.data?.jadwal"></span>
            </div>

            <!-- Box Masuk & Pulang (SANS Unit Style) -->
            <template x-if="matrixTooltip.data?.is_off">
                <div class="px-3 py-2.5 mb-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-800 text-center">
                    <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">Jadwal Libur Rutin / Off</span>
                </div>
            </template>
            <template x-if="!matrixTooltip.data?.is_off">
                <div class="grid grid-cols-2 gap-2 mb-2.5">
                    <!-- MASUK -->
                    <div class="flex flex-col bg-slate-50 dark:bg-slate-800/50 rounded-xl p-2 border border-slate-100 dark:border-slate-800 text-center">
                        <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5">Masuk</span>
                        <span class="font-extrabold text-[13px] tracking-tight"
                              :class="matrixTooltip.data?.is_late ? 'text-amber-600 dark:text-amber-400' : 'text-slate-800 dark:text-slate-100'"
                              x-text="matrixTooltip.data?.check_in || '-'">
                        </span>
                        <template x-if="matrixTooltip.data?.is_late">
                            <span class="text-[8px] font-bold text-amber-700 bg-amber-100 dark:bg-amber-950/60 dark:text-amber-300 px-1.5 py-0.5 rounded-md mt-1 mx-auto leading-none"
                                  x-text="'Telat ' + matrixTooltip.data?.late_minutes + ' mnt'">
                            </span>
                        </template>
                        <template x-if="!matrixTooltip.data?.is_late && matrixTooltip.data?.early_minutes > 0">
                            <span class="text-[8px] font-bold text-emerald-700 bg-emerald-100 dark:bg-emerald-950/60 dark:text-emerald-300 px-1.5 py-0.5 rounded-md mt-1 mx-auto leading-none"
                                  x-text="'Awal ' + matrixTooltip.data?.early_minutes + ' mnt'">
                            </span>
                        </template>
                        <template x-if="!matrixTooltip.data?.is_late && (!matrixTooltip.data?.early_minutes || matrixTooltip.data?.early_minutes <= 0) && matrixTooltip.data?.check_in && matrixTooltip.data?.check_in !== '-' && matrixTooltip.data?.check_in !== 'Tidak Hadir'">
                            <span class="text-[8px] font-bold text-emerald-700 bg-emerald-100 dark:bg-emerald-950/60 dark:text-emerald-300 px-1.5 py-0.5 rounded-md mt-1 mx-auto leading-none">
                                Tepat Waktu
                            </span>
                        </template>
                    </div>

                    <!-- PULANG -->
                    <div class="flex flex-col bg-slate-50 dark:bg-slate-800/50 rounded-xl p-2 border border-slate-100 dark:border-slate-800 text-center">
                        <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5">Pulang</span>
                        <span class="font-extrabold text-[13px] tracking-tight text-slate-800 dark:text-slate-100"
                              x-text="matrixTooltip.data?.check_out || '-'">
                        </span>
                        <template x-if="matrixTooltip.data?.check_out && matrixTooltip.data?.check_out !== '-' && matrixTooltip.data?.check_out !== 'REWARD' && matrixTooltip.data?.check_out !== 'DINAS'">
                            <span class="text-[8px] font-medium text-slate-400 dark:text-slate-500 mt-1 leading-none">
                                Tap Pulang
                            </span>
                        </template>
                    </div>
                </div>
            </template>

            <!-- Uang Kehadiran / Bonus Block -->
            <div class="rounded-xl p-2.5 border"
                 :class="(matrixTooltip.data?.bonus > 0) 
                     ? 'bg-emerald-50/80 dark:bg-emerald-950/30 border-emerald-200/80 dark:border-emerald-800/50' 
                     : 'bg-slate-50 dark:bg-slate-800/40 border-slate-100 dark:border-slate-800'">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <div class="text-[10px] font-bold"
                             :class="(matrixTooltip.data?.bonus > 0) ? 'text-emerald-800 dark:text-emerald-300' : 'text-slate-600 dark:text-slate-400'">
                            Uang Kehadiran
                        </div>
                        <div class="text-[9px] text-slate-400 dark:text-slate-500 font-medium truncate max-w-[190px]">
                            <span x-text="cleanSchemaName(matrixTooltip.data?.schema_name) || 'Skema Uang Kehadiran'"></span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="font-extrabold text-sm"
                              :class="(matrixTooltip.data?.bonus > 0) ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500'"
                              x-text="(matrixTooltip.data?.bonus > 0) ? 'Rp ' + Number(matrixTooltip.data.bonus).toLocaleString('id-ID') : 'Rp 0'">
                        </span>
                    </div>
                </div>

                <!-- Catatan informatif tambahan -->
                <template x-if="matrixTooltip.data?.note">
                    <div class="mt-1.5 pt-1.5 border-t text-[9.5px] leading-snug"
                         :class="(matrixTooltip.data?.bonus > 0) 
                             ? 'border-emerald-200/60 dark:border-emerald-800/40 text-emerald-700 dark:text-emerald-300' 
                             : 'border-slate-200/60 dark:border-slate-700/60 text-slate-500 dark:text-slate-400'"
                         x-text="matrixTooltip.data.note">
                    </div>
                </template>
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
            
            // Rich Matrix Tooltip State & Handlers
            matrixTooltipTimer: null,
            matrixTooltip: {
                show: false,
                x: 0,
                y: 0,
                placement: 'top',
                arrowOffset: 0,
                data: null,
                activeCell: '',
            },
            showMatrixTooltip(e, data) {
                if (!data) return;
                if (this.matrixTooltipTimer) clearTimeout(this.matrixTooltipTimer);
                const targetRect = e.currentTarget.getBoundingClientRect();
                
                this.matrixTooltipTimer = setTimeout(() => {
                    this.renderMatrixTooltip(targetRect, data);
                }, 75);
            },
            showMatrixTooltipInstant(e, data) {
                if (!data) return;
                if (this.matrixTooltipTimer) clearTimeout(this.matrixTooltipTimer);
                const targetRect = e.currentTarget.getBoundingClientRect();
                this.renderMatrixTooltip(targetRect, data);
            },
            renderMatrixTooltip(targetRect, data) {
                this.matrixTooltip.data = data;
                const tooltipWidth = 285;
                const tooltipHeight = 285;
                const padding = 12;
                
                const rawX = targetRect.left + (targetRect.width / 2);
                const minX = (tooltipWidth / 2) + padding;
                const maxX = window.innerWidth - (tooltipWidth / 2) - padding;
                
                const clampedX = Math.max(minX, Math.min(rawX, maxX));
                this.matrixTooltip.x = clampedX;
                
                // Calculate arrow offset relative to clamped tooltip center
                const rawArrowOffset = rawX - clampedX;
                const maxArrowOffset = (tooltipWidth / 2) - 24;
                this.matrixTooltip.arrowOffset = Math.max(-maxArrowOffset, Math.min(maxArrowOffset, rawArrowOffset));
                
                // Check if cell is too close to top of viewport
                if (targetRect.top < tooltipHeight + 20) {
                    this.matrixTooltip.y = targetRect.bottom + 8;
                    this.matrixTooltip.placement = 'bottom';
                } else {
                    this.matrixTooltip.y = targetRect.top - 8;
                    this.matrixTooltip.placement = 'top';
                }
                
                this.matrixTooltip.show = true;
            },
            hideMatrixTooltip() {
                if (this.matrixTooltipTimer) clearTimeout(this.matrixTooltipTimer);
                this.matrixTooltip.show = false;
                this.matrixTooltip.activeCell = '';
            },
            cleanSchemaName(name) {
                if (!name) return '';
                return name.replace(/\s*\([^)]*toleransi[^)]*\)/gi, '').trim();
            },
            toggleMatrixTooltip(e, data) {
                const key = (data?.employee?.name || '') + '_' + (data?.date_display || '');
                if (this.matrixTooltip.show && this.matrixTooltip.activeCell === key) {
                    this.hideMatrixTooltip();
                } else {
                    this.showMatrixTooltipInstant(e, data);
                    this.matrixTooltip.activeCell = key;
                }
            },
            showMatrixTooltipFromModal(e, day) {
                if (!day || !day.isCurrentMonth || !this.selectedReport) return;
                const detail = (this.selectedReport.daily_details && this.selectedReport.daily_details[day.dateStr]) 
                    ? this.selectedReport.daily_details[day.dateStr] 
                    : null;
                const empName = this.selectedReport.employee ? (this.selectedReport.employee.name || 'Pegawai') : 'Pegawai';
                const unitName = (this.selectedReport.employee && this.selectedReport.employee.unit) 
                    ? this.selectedReport.employee.unit.name 
                    : (this.selectedReport.employee ? (this.selectedReport.employee.unit_name || 'SD') : 'SD');
                const position = this.selectedReport.employee ? (this.selectedReport.employee.position || (this.selectedReport.employee.subject_position || '-')) : '-';
                
                const todayStr = new Date().toLocaleDateString('en-CA');
                const isFuture = day.dateStr > todayStr;
                const dObj = new Date(day.dateStr);
                const isSunday = dObj.getDay() === 0;

                const daysId = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const monthsId = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                const dayName = daysId[dObj.getDay()];
                const dateDisplay = ('0' + dObj.getDate()).slice(-2) + ' ' + monthsId[dObj.getMonth()] + ' ' + dObj.getFullYear();
                
                let modalData;
                if (detail) {
                    const statusName = detail.status || '';
                    const isReward = statusName === 'Reward Libur' || !!detail.is_reward_holiday;
                    const isPicket = !!detail.is_picket;
                    const isKedinasan = statusName === 'Dinas' || statusName.toLowerCase().includes('dinas') || statusName.toLowerCase().includes('kedinasan');
                    const isLate = (detail.late_minutes && detail.late_minutes > 0);

                    let statusBadge = 'Tepat Waktu';
                    let statusColor = 'emerald';
                    let noteText = 'Tepat waktu sesuai jadwal.';

                    if (isFuture) {
                        statusBadge = 'Belum Terlaksana';
                        statusColor = 'slate';
                        noteText = 'Tanggal dan presensi belum berjalan.';
                    } else if (isReward) {
                        statusBadge = 'Libur Reward';
                        statusColor = 'emerald';
                        noteText = 'Bebas keterlambatan (' + (detail.reward_name || 'Libur Reward') + ').';
                    } else if (isKedinasan) {
                        statusBadge = 'Tugas Dinas';
                        statusColor = 'blue';
                        noteText = 'Dispensasi absensi kedinasan.';
                    } else if (isPicket) {
                        statusBadge = isLate ? 'Piket (Telat)' : 'Piket Hadir';
                        statusColor = isLate ? 'amber' : 'emerald';
                        noteText = 'Jadwal piket di ' + (detail.picket_area || 'Area Piket') + '.';
                    } else if (isLate) {
                        statusBadge = 'Terlambat';
                        statusColor = 'amber';
                        noteText = 'Terlambat ' + detail.late_minutes + ' menit dari batas shift.';
                    } else if (detail.check_in && detail.check_in !== 'Tidak Hadir') {
                        statusBadge = 'Tepat Waktu';
                        statusColor = 'emerald';
                        noteText = (detail.early_minutes > 0) ? 'Datang ' + detail.early_minutes + ' menit lebih awal.' : 'Tepat waktu sesuai toleransi.';
                    } else {
                        statusBadge = 'Tidak Hadir';
                        statusColor = 'rose';
                        noteText = 'Tidak tercatat data presensi kedatangan.';
                    }

                    let shiftLabel = 'Reguler';
                    let jadwalStr = '-';
                    if (isReward) {
                        shiftLabel = 'Libur Reward';
                        jadwalStr = 'Bebas Jam';
                    } else if (isPicket) {
                        shiftLabel = 'Piket: ' + (detail.picket_area || 'Area Piket');
                        jadwalStr = detail.shift_start ? detail.shift_start.substring(0, 5) : '06:30';
                    } else {
                        shiftLabel = detail.shift_name || 'Reguler';
                        const sJam = detail.shift_start ? detail.shift_start.substring(0, 5) : '-';
                        const eJam = detail.shift_end ? detail.shift_end.substring(0, 5) : '';
                        jadwalStr = eJam ? (sJam + ' - ' + eJam) : sJam;
                    }

                    modalData = {
                        employee: { name: empName, unit: unitName, position: position },
                        date_display: dateDisplay,
                        day_name: dayName,
                        is_today: day.dateStr === todayStr,
                        is_sunday: isSunday,
                        is_future: isFuture,
                        is_off: false,
                        status: statusBadge,
                        status_color: statusColor,
                        is_picket: isPicket,
                        picket_area: detail.picket_area || 'Area Piket',
                        is_reward: isReward,
                        reward_name: detail.reward_name || null,
                        is_dinas: isKedinasan,
                        shift_name: shiftLabel,
                        jadwal: jadwalStr,
                        check_in: isFuture ? '-' : (detail.check_in || 'Tidak Hadir'),
                        check_out: isFuture ? '-' : (detail.check_out || '-'),
                        is_late: isLate,
                        late_minutes: detail.late_minutes || 0,
                        early_minutes: detail.early_minutes || 0,
                        bonus: detail.bonus_nominal || 0,
                        tier_level: detail.tier_level || null,
                        schema_name: detail.schema_name || null,
                        note: noteText,
                    };
                } else {
                    modalData = {
                        employee: { name: empName, unit: unitName, position: position },
                        date_display: dateDisplay,
                        day_name: dayName,
                        is_today: day.dateStr === todayStr,
                        is_sunday: isSunday,
                        is_future: isFuture,
                        is_off: true,
                        status: isSunday ? 'Akhir Pekan' : 'Jadwal Libur (OFF)',
                        status_color: isSunday ? 'rose' : 'slate',
                        is_picket: false,
                        picket_area: null,
                        is_reward: false,
                        reward_name: null,
                        is_dinas: false,
                        shift_name: 'Jadwal Libur Rutin',
                        jadwal: 'OFF',
                        check_in: '-',
                        check_out: '-',
                        is_late: false,
                        late_minutes: 0,
                        early_minutes: 0,
                        bonus: 0,
                        tier_level: null,
                        schema_name: null,
                        note: isSunday ? 'Hari libur akhir pekan.' : 'Hari libur rutin pegawai.',
                    };
                }
                
                this.showMatrixTooltipInstant(e, modalData);
            },
            
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
