@php
    $start = \Carbon\Carbon::parse($startDate);
    $end = \Carbon\Carbon::parse($endDate);
    $formatAttendanceDuration = function ($minutes) {
        $minutes = abs((int) $minutes);
        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        return $hours > 0
            ? $hours . ' jam' . ($remainingMinutes > 0 ? ' ' . $remainingMinutes . ' menit' : '')
            : $minutes . ' menit';
    };
    $getEarlyCheckoutData = function ($detail, $dateStr) {
        $isEarly = !empty($detail['is_early_checkout']);
        $minutes = (int) ($detail['early_checkout_minutes'] ?? 0);

        if (!array_key_exists('is_early_checkout', $detail) || ($isEarly && $minutes === 0)) {
            $checkOut = !empty($detail['check_out']) ? substr($detail['check_out'], 0, 5) : null;
            $shiftStart = !empty($detail['shift_start']) ? substr($detail['shift_start'], 0, 5) : null;
            $shiftEnd = !empty($detail['shift_end']) ? substr($detail['shift_end'], 0, 5) : null;

            if ($checkOut && $checkOut !== '-' && $shiftEnd) {
                $expectedOut = \Carbon\Carbon::parse($dateStr . ' ' . $shiftEnd);
                $actualOut = \Carbon\Carbon::parse($dateStr . ' ' . $checkOut);
                $isNightShift = $shiftStart && $shiftStart > $shiftEnd;

                if ($isNightShift) {
                    $expectedOut->addDay();
                    if ($checkOut < $shiftStart) {
                        $actualOut->addDay();
                    }
                }

                if ($actualOut->lessThan($expectedOut)) {
                    $minutes = (int) $actualOut->diffInMinutes($expectedOut);
                    $isEarly = true;
                }
            }
        }

        return ['is_early_checkout' => $isEarly, 'early_checkout_minutes' => $minutes];
    };
    $cycleDateData = [];
    while($start <= $end) {
        $cycleDateData[] = [
            'dateStr' => $start->format('Y-m-d'),
            'day' => (int)$start->format('d'),
            'dayOfWeek' => (int)$start->format('N'),
        ];
        $start->addDay();
    }
@endphp
<x-admin-layout>
    <div class="p-6 space-y-6" x-data="attendanceLogs" @scroll.window="hideMatrixTooltip()">
        <!-- SUCCESS/ERROR ALERT -->
        @if(session('success'))
            <div class="bg-emerald-50 dark:bg-emerald-900/40 border border-emerald-200 dark:border-emerald-900/60 rounded-xl p-4 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <i data-lucide="check" class="w-4 h-4"></i>
                </div>
                <div>
                    <h5 class="text-xs font-bold text-slate-800 dark:text-slate-200">Sukses!</h5>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ session('success') }}</p>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-rose-50 dark:bg-rose-900/40 border border-rose-200 dark:border-rose-900/60 rounded-xl p-4 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                </div>
                <div>
                    <h5 class="text-xs font-bold text-slate-800 dark:text-slate-200">Gagal</h5>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ session('error') }}</p>
                </div>
            </div>
        @endif
        <!-- HEADER -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-2 sm:gap-3">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-base sm:text-lg font-bold tracking-tight text-slate-900 dark:text-slate-100 font-nasalization">Data Kehadiran</h2>
                <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 font-medium">Memantau waktu kedatangan dan kepulangan pegawai secara komprehensif.</p>
            </div>
            
            <!-- ACTIONS: SINKRONKAN & EKSPOR DATA -->
            <div class="flex items-center gap-2 w-full md:w-auto">
                <a href="{{ route('attendances.index', array_merge(request()->query(), ['refresh' => 1])) }}" 
                   x-data="{ syncing: false }" 
                   @click="syncing = true" 
                   :class="{ 'opacity-75 pointer-events-none': syncing }"
                   class="w-full md:w-auto justify-center h-9 px-3 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 font-semibold text-xs rounded-lg shadow-sm transition-all cursor-pointer whitespace-nowrap flex items-center gap-1.5" 
                   title="Sinkronkan data terbaru langsung dari HRD / Mesin ADMS">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" :class="{ 'animate-spin': syncing }"></i>
                    <span x-text="syncing ? 'Menyinkronkan...' : 'Sinkronkan'">Sinkronkan</span>
                </a>

                <div x-data="{ open: false }" class="relative inline-block text-left w-full md:w-auto">
                    <button type="button" @click="open = !open" @click.outside="open = false" class="w-full md:w-auto justify-center h-9 px-4 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 font-semibold text-xs rounded-lg shadow-sm transition-all cursor-pointer whitespace-nowrap flex items-center gap-2">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        <span>Ekspor Data</span>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 opacity-70"></i>
                    </button>
                    
                    <div x-show="open" x-transition.opacity.duration.200ms style="display: none;" class="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-lg z-50">
                        <a href="{{ route('attendances.export', array_merge(request()->query(), ['format' => 'excel'])) }}" class="flex items-center gap-3 px-4 py-3 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors border-b border-slate-100 dark:border-slate-800">
                            <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-600 dark:text-emerald-500"></i>
                            Excel (.xlsx)
                        </a>
                        <a href="{{ route('attendances.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" class="flex items-center gap-3 px-4 py-3 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-rose-50 dark:hover:bg-rose-900/30 hover:text-rose-700 dark:hover:text-rose-404 transition-colors">
                            <i data-lucide="file-text" class="w-4 h-4 text-rose-600 dark:text-rose-500"></i>
                            PDF (.pdf)
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- FILTERS -->
        <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm px-3.5 py-2.5 w-full text-left">
            <form method="GET" action="{{ route('attendances.index') }}" class="flex flex-col md:flex-row gap-4 items-stretch md:items-center justify-between">
                
                <!-- Left Side: Search & Filters -->
                <div class="flex flex-wrap items-center gap-2 flex-1">
                    @if(auth()->user()->role !== 'employee')
                    <!-- Search Box Welded with Cari Button (Premium Input Group) -->
                    <div x-data="{ searchVal: '{{ request('search') }}' }" class="flex items-center w-full search-container bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg overflow-hidden shadow-inner focus-within:ring-0 focus-within:border-slate-300 dark:focus-within:border-slate-700">
                        <input type="text" name="search" x-model="searchVal" placeholder="Cari pegawai..."
                            style="border: none !important; outline: none !important; box-shadow: none !important;"
                            class="w-full h-9 px-3 text-xs bg-transparent text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-0">
                        
                        <!-- Clear Button (x) -->
                        <button type="button" x-show="searchVal.trim() !== ''" @click="searchVal = ''; $el.closest('.search-container').querySelector('input').focus();" class="h-9 px-2.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer bg-transparent border-0 flex items-center justify-center" title="Bersihkan pencarian">
                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                        </button>

                        <button type="submit" 
                            :class="searchVal.trim() !== '' ? 'bg-indigo-600 text-white dark:bg-indigo-500 dark:text-white' : 'bg-slate-50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300'"
                            class="h-9 px-4 font-bold text-xs transition-all duration-150 cursor-pointer whitespace-nowrap flex items-center justify-center border-l border-slate-200 dark:border-slate-800">
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

                    @if(request()->anyFilled(['search', 'position']))
                        <a href="{{ route('attendances.index', ['month' => request('month', $month)]) }}" class="h-9 px-3 flex items-center justify-center bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-300 rounded-lg transition-colors reset-filter-btn" title="Reset Filter">
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
            $start = \Carbon\Carbon::parse($startDate);
            $end = \Carbon\Carbon::parse($endDate);
            $dates = [];
            while($start <= $end) {
                $dates[] = $start->copy();
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
                            <th class="px-3 py-2 bg-slate-50 dark:bg-slate-900 text-left sticky top-0 md:left-0 z-30 md:z-40 border-r border-slate-200 dark:border-slate-800 min-w-[200px]">
                                <div class="flex items-center gap-2 justify-between">
                                    <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Profil Pegawai</span>
                                    <span class="text-[9px] text-slate-400 dark:text-slate-500 font-bold whitespace-nowrap bg-slate-100 dark:bg-slate-800/40 px-1.5 py-0.5 rounded border border-slate-200/40 dark:border-slate-800/50" title="Siklus Cut-off Payroll">
                                        {{ \Carbon\Carbon::parse($startDate)->format('d M') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M') }}
                                    </span>
                                </div>
                            </th>

                            @foreach($dates as $date)
                            @php
                                $isToday = $date->isToday();
                                $isSunday = $date->isSunday();
                            @endphp
                            <th class="py-2 px-1 text-center sticky top-0 z-20 bg-slate-50 dark:bg-slate-900 min-w-[32px] border-r border-slate-100 dark:border-slate-800/60">
                                <div class="flex flex-col items-center justify-center gap-1 py-0.5">
                                    <span class="text-[9px] font-semibold {{ $isSunday && !$isToday ? 'text-red-400' : ($isToday ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500') }} uppercase tracking-wider leading-none">
                                        {{ $date->translatedFormat('D') }}
                                    </span>
                                    <div class="flex items-center justify-center w-6 h-6 {{ $isToday ? 'bg-indigo-600 dark:bg-indigo-500 rounded-full' : '' }}">
                                        <span class="text-[11px] font-bold leading-none {{ $isSunday && !$isToday ? 'text-red-500 dark:text-red-400' : ($isToday ? 'text-white' : 'text-slate-700 dark:text-slate-200') }}">
                                            {{ $date->format('d') }}
                                        </span>
                                    </div>
                                </div>
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        @forelse($reports as $index => $report)
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
                                        <div class="flex flex-col min-w-0 text-left">
                                            <span @click='openCalendarModal(@json($report))' class="text-xs font-bold text-slate-900 dark:text-slate-100 truncate cursor-pointer hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors leading-tight">{{ $empName }}</span>
                                            <div class="flex flex-col gap-0.5 mt-0.5">
                                                <span class="text-[10px] text-slate-500 dark:text-slate-400 truncate leading-tight">{{ $report['employee']['position'] ?? ($report['employee']['subject_position'] ?? '-') }}</span>
                                                <span class="text-[9.5px] font-mono font-bold text-emerald-600 dark:text-emerald-400 leading-tight" title="Total Uang Kehadiran">Rp {{ number_format($report['total_bonus_nominal'] ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                
                                
                                                                <!-- KOLOM 3+: TANGGAL -->
                                @foreach($dates as $date)
                                @php
                                    $dateStr = $date->format('Y-m-d');
                                    $detail = $report['daily_details'][$dateStr] ?? null;
                                    $isToday = $date->isToday();
                                    $isSunday = $date->isSunday();
                                    $isFuture = $dateStr > date('Y-m-d');

                                    if ($detail) {
                                        $statusName = $detail['status'] ?? '';
                                        $isReward = ($statusName === 'Reward Libur' || !empty($detail['is_reward']));
                                        $isPicket = !empty($detail['is_picket']);
                                        $picketArea = $detail['picket_area'] ?? 'Area Piket';
                                        $hasCheckIn = !empty($detail['check_in']) && $detail['check_in'] !== '-';
                                        $isLate = $hasCheckIn && !empty($detail['is_late']);
                                        $isRedLate = $hasCheckIn && !empty($detail['is_red_late']);
                                        $lateMnt = $hasCheckIn ? (int) abs($detail['late_minutes'] ?? 0) : 0;
                                        $earlyMnt = $hasCheckIn ? (int) abs($detail['early_minutes'] ?? 0) : 0;
                                        $earlyCheckout = $getEarlyCheckoutData($detail, $dateStr);

                                        if ($isFuture && empty($detail['check_in'])) {
                                            if ($statusName === 'Cuti/Izin') {
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
                                                $noteText = 'Terlambat ' . ($lateMnt > 0 ? $formatAttendanceDuration($lateMnt) . ' ' : '') . '(melewati batas toleransi).';
                                            } elseif ($isLate) {
                                                $statusBadge = 'Terlambat';
                                                $statusColor = 'amber';
                                                $noteText = 'Terlambat ' . ($lateMnt > 0 ? $formatAttendanceDuration($lateMnt) . ' ' : '') . 'dari batas shift.';
                                            } else {
                                                $statusBadge = 'Tepat Waktu';
                                                $statusColor = 'emerald';
                                                $noteText = $earlyMnt > 0 ? 'Datang ' . $formatAttendanceDuration($earlyMnt) . ' lebih awal.' : 'Tepat waktu sesuai jadwal shift.';
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
                                        } elseif ($statusName === 'Cuti/Izin') {
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
                                                'unit' => $report['employee']['unit']['name'] ?? ($report['employee']['unit_name'] ?? 'SD'),
                                                'position' => $report['employee']['position'] ?? ($report['employee']['subject_position'] ?? '-'),
                                            ],
                                            'date_display' => $date->translatedFormat('d F Y'),
                                            'day_name' => $date->translatedFormat('l'),
                                            'is_today' => $isToday,
                                            'is_sunday' => $isSunday,
                                            'is_future' => $isFuture,
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
                                            'is_early_checkout' => $earlyCheckout['is_early_checkout'],
                                            'early_checkout_minutes' => $earlyCheckout['early_checkout_minutes'],
                                            'is_reward' => $isReward,
                                            'reward_name' => $detail['reward_name'] ?? null,
                                            'leave_type' => $detail['leave_type'] ?? null,
                                            'leave_code' => $detail['leave_code'] ?? null,
                                            'is_pending' => !empty($detail['is_pending']),
                                            'rejected_leave' => $detail['rejected_leave'] ?? null,
                                            'is_picket' => $isPicket,
                                            'picket_area' => $picketArea,
                                            'bonus_nominal' => $detail['bonus_nominal'] ?? 0,
                                            'tier_level' => $detail['tier_level'] ?? null,
                                            'schema_name' => $detail['schema_name'] ?? null,
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
                                            'is_today' => $isToday,
                                            'is_sunday' => $isSunday,
                                            'is_future' => $isFuture,
                                            'is_off' => true,
                                            'status' => $isSunday ? 'Akhir Pekan' : 'Jadwal Libur (OFF)',
                                            'status_color' => $isSunday ? 'rose' : 'slate',
                                            'shift_name' => $isSunday ? 'Akhir Pekan' : 'Jadwal Libur Rutin',
                                            'jadwal' => 'OFF',
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
                                @endphp
                                <td class="group/cell py-1 px-1 text-center border-r border-slate-50 dark:border-slate-800/30 cursor-pointer transition-all duration-75 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:ring-1.5 hover:ring-inset hover:ring-indigo-400 dark:hover:ring-indigo-500/70 {{ $date->isSunday() ? 'bg-red-50/30 dark:bg-red-950/10' : '' }}"
                                    @mouseenter="showMatrixTooltip($event, @js($cellData))"
                                    @mouseleave="hideMatrixTooltip()"
                                    @click.stop="toggleMatrixTooltip($event, @js($cellData))">
                                    @if($detail)
                                        <div class="relative w-full h-full flex flex-col items-center justify-center {{ $isPicket ? 'border border-amber-400 dark:border-amber-500 bg-amber-50/35 dark:bg-amber-950/20 rounded py-0.5 px-0.5 shadow-2xs' : '' }}">
                                            @if($isPicket)
                                                <span class="absolute -top-1 -right-1 flex h-2 w-2 z-10 pointer-events-none" title="Piket: {{ $picketArea }}">
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                                </span>
                                            @endif
                                        @if($detail['status'] === 'Hadir')
                                            @php
                                                $isRedLate = !empty($detail['is_red_late']);
                                                $isLate = !empty($detail['is_late']);
                                            @endphp
                                            <div class="flex flex-col gap-0.5 items-center justify-center {{ $isRedLate ? 'bg-rose-50/70 dark:bg-rose-950/30 border border-rose-200/60 dark:border-rose-900/40 rounded px-1 py-0.5' : '' }}">
                                                <span class="text-[10px] font-bold {{ $isRedLate ? 'text-rose-600 dark:text-rose-400 font-extrabold' : ($isLate ? 'text-amber-500' : 'text-emerald-600 dark:text-emerald-400') }}">{{ $detail['check_in'] ?? '-' }}</span>
                                                @if(!empty($detail['is_reward']))
                                                    <div class="w-full flex items-center justify-center gap-0.5 scale-90">
                                                        <span class="px-1 py-0.2 rounded font-black text-[7.5px] leading-none uppercase bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 flex items-center gap-0.5">
                                                            <i data-lucide="gift" class="w-2.5 h-2.5 text-emerald-600 dark:text-emerald-400"></i>
                                                            <span>OFF</span>
                                                        </span>
                                                    </div>
                                                @elseif(!empty($detail['pending_leave']))
                                                    @php
                                                        $pCode = $detail['pending_leave']['leave_code'];
                                                        $colorMap = [
                                                            'S' => 'bg-amber-50/40 dark:bg-amber-955/10 text-amber-500 dark:text-amber-500 border border-dashed border-amber-200/50',
                                                            'I' => 'bg-purple-50/40 dark:bg-purple-955/10 text-purple-500 dark:text-purple-500 border border-dashed border-purple-200/50',
                                                            'C' => 'bg-blue-50/40 dark:bg-blue-955/10 text-blue-500 dark:text-blue-500 border border-dashed border-blue-200/50',
                                                            'H' => 'bg-emerald-50/40 dark:bg-emerald-955/10 text-emerald-500 dark:text-emerald-500 border border-dashed border-emerald-200/50'
                                                        ];
                                                        $pColorClass = $colorMap[$pCode] ?? 'bg-slate-50/40 text-slate-500 border border-dashed border-slate-200/50';
                                                    @endphp
                                                    <div class="w-full flex justify-center scale-75 opacity-60">
                                                        <span class="px-1.5 py-0.5 rounded font-extrabold text-[8px] leading-none uppercase {{ $pColorClass }}">{{ $pCode }}</span>
                                                    </div>
                                                @endif
                                                <span class="text-[10px] font-bold {{ !empty($earlyCheckout['is_early_checkout']) ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400 dark:text-slate-500' }}">{{ $detail['check_out'] ?? '-' }}</span>
                                            </div>
                                        @elseif($detail['status'] === 'Reward Libur')
                                            <div class="mx-auto w-full h-full min-h-[28px] flex flex-col items-center justify-center bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 rounded px-0.5 py-1">
                                                <div class="flex items-center gap-0.5">
                                                    <i data-lucide="gift" class="w-3 h-3 text-emerald-600 dark:text-emerald-400 shrink-0"></i>
                                                    <span class="font-extrabold text-[8.5px] leading-none tracking-tight">OFF</span>
                                                </div>
                                            </div>
                                        @elseif($detail['status'] === 'Alfa')
                                            @if(!empty($detail['pending_leave']))
                                                <div class="w-full flex flex-col gap-0.5 items-center justify-center">
                                                    <div class="mx-auto w-full h-full min-h-[28px] flex items-center justify-center bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-400 border border-rose-100 dark:border-rose-900/30 rounded font-bold text-[10px]">A</div>
                                                    @php
                                                        $pCode = $detail['pending_leave']['leave_code'];
                                                        $colorMap = [
                                                            'S' => 'bg-amber-50/40 dark:bg-amber-955/10 text-amber-500 dark:text-amber-500 border border-dashed border-amber-200/50',
                                                            'I' => 'bg-purple-50/40 dark:bg-purple-955/10 text-purple-500 dark:text-purple-500 border border-dashed border-purple-200/50',
                                                            'C' => 'bg-blue-50/40 dark:bg-blue-955/10 text-blue-500 dark:text-blue-500 border border-dashed border-blue-200/50',
                                                            'H' => 'bg-emerald-50/40 dark:bg-emerald-955/10 text-emerald-500 dark:text-emerald-500 border border-dashed border-emerald-200/50'
                                                        ];
                                                        $pColorClass = $colorMap[$pCode] ?? 'bg-slate-50/40 text-slate-500 border border-dashed border-slate-200/50';
                                                    @endphp
                                                    <div class="w-full flex justify-center scale-75 opacity-60">
                                                        <span class="px-1.5 py-0.5 rounded font-extrabold text-[8px] leading-none uppercase {{ $pColorClass }}">{{ $pCode }}</span>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="mx-auto w-full h-full min-h-[28px] flex items-center justify-center bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-400 border border-rose-100 dark:border-rose-900/30 rounded font-bold text-[10px]">A</div>
                                            @endif
                                        @elseif($detail['status'] === 'Libur')
                                            <div class="mx-auto w-full h-full min-h-[28px] flex items-center justify-center bg-red-50/50 dark:bg-red-950/20 text-rose-500 dark:text-rose-450 border border-red-100 dark:border-red-900/30 rounded font-bold text-[9px]">OFF</div>
                                        @elseif($detail['status'] === 'Off')
                                            <div class="mx-auto w-full h-full min-h-[28px] flex items-center justify-center bg-slate-100 dark:bg-slate-900/40 text-slate-400 dark:text-slate-500 border border-slate-100 dark:border-slate-800/40 rounded font-bold text-[9px]">OFF</div>
                                        @elseif($detail['status'] === 'Cuti/Izin')
                                            @php
                                                $leaveCode = $detail['leave_code'] ?? 'I';
                                                $isPending = !empty($detail['is_pending']);
                                                
                                                if ($isPending) {
                                                    $colorMap = [
                                                        'S' => 'bg-amber-50/40 dark:bg-amber-955/10 text-amber-500 dark:text-amber-500 border border-dashed border-amber-200/50',
                                                        'I' => 'bg-purple-50/40 dark:bg-purple-955/10 text-purple-500 dark:text-purple-500 border border-dashed border-purple-200/50',
                                                        'C' => 'bg-blue-50/40 dark:bg-blue-955/10 text-blue-500 dark:text-blue-500 border border-dashed border-blue-200/50',
                                                        'H' => 'bg-emerald-50/40 dark:bg-emerald-955/10 text-emerald-500 dark:text-emerald-500 border border-dashed border-emerald-200/50'
                                                    ];
                                                } else {
                                                    $colorMap = [
                                                        'S' => 'bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-900/30',
                                                        'I' => 'bg-purple-50 dark:bg-purple-950/20 text-purple-600 dark:text-purple-400 border border-purple-100 dark:border-purple-900/30',
                                                        'C' => 'bg-blue-50 dark:bg-blue-950/20 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/30',
                                                        'H' => 'bg-emerald-50 dark:bg-emerald-955/20 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/30'
                                                    ];
                                                }
                                                $colorClass = $colorMap[$leaveCode] ?? 'bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400';
                                            @endphp
                                            @if(!empty($detail['check_in']) && !empty($detail['check_out']))
                                                <!-- Both Check-In and Check-Out -->
                                                <div class="flex flex-col gap-0.5 items-center justify-center">
                                                    <span class="text-[10px] font-bold {{ (!empty($detail['is_late'])) ? 'text-amber-500' : 'text-emerald-600 dark:text-emerald-400' }}">{{ $detail['check_in'] }}</span>
                                                    <div class="w-full flex justify-center scale-90">
                                                        <span class="px-1.5 py-0.5 rounded font-extrabold text-[8px] leading-none uppercase {{ $colorClass }}">{{ $leaveCode }}</span>
                                                    </div>
                                                    <span class="text-[10px] font-bold {{ !empty($earlyCheckout['is_early_checkout']) ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400 dark:text-slate-500' }}">{{ $detail['check_out'] }}</span>
                                                </div>
                                            @elseif(!empty($detail['check_in']) && empty($detail['check_out']))
                                                <!-- Only Check-In -->
                                                <div class="flex flex-col gap-0.5 items-center justify-center">
                                                    <span class="text-[10px] font-bold {{ (!empty($detail['is_late'])) ? 'text-amber-500' : 'text-emerald-600 dark:text-emerald-400' }}">{{ $detail['check_in'] }}</span>
                                                    <div class="w-full flex justify-center scale-90">
                                                        <span class="px-1.5 py-0.5 rounded font-extrabold text-[8px] leading-none uppercase {{ $colorClass }}">{{ $leaveCode }}</span>
                                                    </div>
                                                    <span class="text-[10px] font-bold text-slate-350 dark:text-slate-650">-</span>
                                                </div>
                                            @elseif(!empty($detail['check_out']) && empty($detail['check_in']))
                                                <!-- Only Check-Out -->
                                                <div class="flex flex-col gap-0.5 items-center justify-center">
                                                    <span class="text-[10px] font-bold text-slate-350 dark:text-slate-650">-</span>
                                                    <div class="w-full flex justify-center scale-90">
                                                        <span class="px-1.5 py-0.5 rounded font-extrabold text-[8px] leading-none uppercase {{ $colorClass }}">{{ $leaveCode }}</span>
                                                    </div>
                                                    <span class="text-[10px] font-bold {{ !empty($earlyCheckout['is_early_checkout']) ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400 dark:text-slate-500' }}">{{ $detail['check_out'] }}</span>
                                                </div>
                                            @elseif($isPending)
                                                <!-- Pending and Neither Check-In nor Check-Out -->
                                                <div class="flex flex-col gap-0.5 items-center justify-center py-0.5">
                                                    <div class="w-full flex justify-center scale-90">
                                                        <span class="px-1.5 py-0.5 rounded font-extrabold text-[8px] leading-none uppercase {{ $colorClass }}">{{ $leaveCode }}</span>
                                                    </div>
                                                    <span class="text-[10px] font-bold text-slate-350 dark:text-slate-650">-</span>
                                                </div>
                                            @else
                                                <!-- Approved and Neither -->
                                                <div class="mx-auto w-full h-full min-h-[28px] flex items-center justify-center rounded font-bold text-[10px] {{ $colorClass }}">{{ $leaveCode }}</div>
                                            @endif
                                        @else
                                            <div class="text-xs text-slate-300">-</div>
                                        @endif
                                        </div>
                                    @else
                                        @if($date->isSunday())
                                            <div class="mx-auto flex items-center justify-center text-red-200 dark:text-red-900/30 font-bold text-xs">-</div>
                                        @else
                                            <div class="mx-auto flex items-center justify-center text-slate-100 dark:text-slate-800/50 font-bold text-[10px]">-</div>
                                        @endif
                                    @endif
                                </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($dates) + 1 }}" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-slate-500 dark:text-slate-400">
                                        <i data-lucide="file-search" class="w-12 h-12 mb-4 text-slate-300 dark:text-slate-600"></i>
                                        <p class="text-sm font-medium">Tidak ada data pegawai yang ditemukan</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                                        </tbody>
                    
                </table>
            </div>
            
            @if($reports instanceof \Illuminate\Pagination\LengthAwarePaginator && $reports->total() > 0)
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-900 bg-slate-50/30 dark:bg-slate-900/10 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-slate-500 dark:text-slate-400">
                    <div>
                        Menampilkan
                        <span class="font-bold text-slate-700 dark:text-slate-300">{{ $reports->firstItem() }}</span>
                        sampai
                        <span class="font-bold text-slate-700 dark:text-slate-300">{{ $reports->lastItem() }}</span>
                        dari
                        <span class="font-bold text-slate-700 dark:text-slate-300">{{ $reports->total() }}</span>
                        pegawai
                    </div>
                    <div class="flex items-center gap-1.5 font-semibold">
                        @if ($reports->onFirstPage())
                            <span class="h-8 px-3 rounded-lg border border-slate-200 dark:border-slate-800 text-slate-400 dark:text-slate-600 flex items-center justify-center cursor-not-allowed select-none bg-slate-50 dark:bg-slate-900/20">Sebelumnya</span>
                        @else
                            <a href="{{ $reports->appends(request()->query())->previousPageUrl() }}" class="h-8 px-3 rounded-lg border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-900 text-slate-700 dark:text-slate-300 flex items-center justify-center transition-all bg-white dark:bg-slate-900">Sebelumnya</a>
                        @endif

                        <span class="px-3 py-1 font-medium text-slate-700 dark:text-slate-300">
                            Halaman {{ $reports->currentPage() }} dari {{ $reports->lastPage() }}
                        </span>

                        @if ($reports->hasMorePages())
                            <a href="{{ $reports->appends(request()->query())->nextPageUrl() }}" class="h-8 px-3 rounded-lg border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-900 text-slate-700 dark:text-slate-300 flex items-center justify-center transition-all bg-white dark:bg-slate-900">Berikutnya</a>
                        @else
                            <span class="h-8 px-3 rounded-lg border border-slate-200 dark:border-slate-800 text-slate-400 dark:text-slate-600 flex items-center justify-center cursor-not-allowed select-none bg-slate-50 dark:bg-slate-900/20">Berikutnya</span>
                        @endif
                    </div>
                </div>
            @endif
        </section>

        <!-- LEGEND / STATUS EXPLANATION -->
        <div class="flex flex-wrap gap-4 items-center justify-center text-[10px] md:text-xs text-slate-500 dark:text-slate-400 mt-4 px-2">
            <span class="font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[10px]">Keterangan:</span>
            
            <!-- Alfa -->
            <div class="flex items-center gap-1.5">
                <div class="w-5 h-5 flex items-center justify-center bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-400 border border-rose-100 dark:border-rose-900/30 rounded font-bold text-[9px]">A</div>
                <span>Alfa (Tanpa Keterangan)</span>
            </div>
            <!-- Sakit -->
            <div class="flex items-center gap-1.5">
                <div class="w-5 h-5 flex items-center justify-center bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-900/30 rounded font-bold text-[9px]">S</div>
                <span>Sakit</span>
            </div>
            <!-- Izin -->
            <div class="flex items-center gap-1.5">
                <div class="w-5 h-5 flex items-center justify-center bg-purple-50 dark:bg-purple-950/20 text-purple-600 dark:text-purple-400 border border-purple-100 dark:border-purple-900/30 rounded font-bold text-[9px]">I</div>
                <span>Izin</span>
            </div>
            <!-- Cuti -->
            <div class="flex items-center gap-1.5">
                <div class="w-5 h-5 flex items-center justify-center bg-blue-50 dark:bg-blue-950/20 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/30 rounded font-bold text-[9px]">C</div>
                <span>Cuti</span>
            </div>
            <!-- Dinas/Dispensasi -->
            <div class="flex items-center gap-1.5">
                <div class="w-5 h-5 flex items-center justify-center bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/30 rounded font-bold text-[9px]">H</div>
                <span>Dinas / Tugas Luar</span>
            </div>
            <!-- Off -->
            <div class="flex items-center gap-1.5">
                <div class="w-7 h-5 flex items-center justify-center bg-slate-100 dark:bg-slate-900/40 text-slate-400 dark:text-slate-500 border border-slate-100 dark:border-slate-800/40 rounded font-bold text-[8px]">OFF</div>
                <span>Off (Hari Libur Kerja)</span>
            </div>
            <!-- Libur / Minggu -->
            <div class="flex items-center gap-1.5">
                <div class="w-5 h-5 flex items-center justify-center text-red-500 dark:text-red-400 font-bold text-xs">-</div>
                <span>Hari Minggu / Libur Nasional</span>
            </div>
            <!-- Piket -->
            <div class="flex items-center gap-1.5">
                <div class="relative w-7 h-5 flex items-center justify-center border border-amber-400/90 dark:border-amber-500/90 bg-amber-50/50 dark:bg-amber-950/30 rounded text-amber-700 dark:text-amber-300 font-bold text-[8px]">
                    <span class="absolute -top-1 -right-1 flex h-2 w-2">
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                    </span>
                    <span>PKT</span>
                </div>
                <span>Tugas Piket (06:30)</span>
            </div>
        </div>
        
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
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate">
                                    <span x-text="selectedReport ? (selectedReport.employee.position || '-') : ''"></span>
                                    &bull;
                                    <span x-text="selectedReport ? (selectedReport.employee.unit_name || '-') : ''" class="font-semibold text-indigo-650 dark:text-indigo-400"></span>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <div class="text-right px-2.5 py-1 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/80 dark:border-emerald-800/60 shadow-xs">
                                <span class="text-[8px] font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider block leading-none">Total Uang Kehadiran</span>
                                <span class="text-xs font-extrabold text-emerald-600 dark:text-emerald-400 font-mono tracking-tight leading-tight block mt-0.5" 
                                      x-text="selectedReport ? 'Rp ' + Number(selectedReport.total_bonus_nominal || stats.totalBonus || 0).toLocaleString('id-ID') : 'Rp 0'"></span>
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
                            <h4 class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                                Periode Kehadiran &bull; <span x-text="startDateStr" class="text-indigo-650 dark:text-indigo-405"></span> - <span x-text="endDateStr" class="text-indigo-650 dark:text-indigo-405"></span>
                            </h4>
                        </div>

                        <!-- 7 Column Days Header -->
                        <div class="grid grid-cols-7 gap-1 text-center text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">
                            <div>Sen</div>
                            <div>Sel</div>
                            <div>Rab</div>
                            <div>Kam</div>
                            <div>Jum</div>
                            <div class="text-red-400">Sab</div>
                            <div class="text-red-500">Min</div>
                        </div>

                        <!-- Calendar Grid -->
                        <div class="grid grid-cols-7 gap-1">
                            <template x-for="day in calendarDays" :key="day.dateStr">
                                <div class="aspect-square border border-slate-100 dark:border-slate-800/40 rounded-lg p-1 sm:p-1.5 flex flex-col justify-between cursor-default"
                                     :class="[
                                         (selectedReport && selectedReport.daily_details[day.dateStr]?.is_picket) ? 'ring-2 ring-amber-400 dark:ring-amber-500/80' : '',
                                         day.isCurrentMonth ? (day.dateStr && new Date(day.dateStr).getDay() === 0 ? 'bg-red-50/50 dark:bg-red-95/15' : 'bg-white dark:bg-slate-900') : 'bg-slate-50/50 dark:bg-slate-955/20 opacity-40'
                                     ]">
                                     
                                    <!-- Day Number -->
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-bold"
                                              :class="[
                                                  day.isCurrentMonth ? 'text-slate-700 dark:text-slate-300' : 'text-slate-400',
                                                  (new Date(day.dateStr).getDay() === 0) ? 'text-red-500 font-bold' : ''
                                              ]"
                                              x-text="day.day"></span>
                                        <template x-if="selectedReport && selectedReport.daily_details[day.dateStr]?.is_picket">
                                            <span class="text-[9px] text-amber-500 font-black leading-none" :title="'Tugas Piket: ' + (selectedReport.daily_details[day.dateStr].picket_area || 'Area Piket')">⚡</span>
                                        </template>
                                    </div>
                                          
                                    <!-- Status Indicator inside day -->
                                    <div class="mt-auto w-full">
                                        <template x-if="selectedReport && selectedReport.daily_details[day.dateStr]">
                                            <div class="w-full">
                                                <!-- Present -->
                                                <template x-if="selectedReport.daily_details[day.dateStr].status === 'Hadir'">
                                                    <div class="flex flex-col items-center leading-none">
                                                        <span class="text-[10px] font-bold" 
                                                              :class="selectedReport.daily_details[day.dateStr].is_late ? 'text-amber-500' : 'text-emerald-600 dark:text-emerald-400'"
                                                              x-text="selectedReport.daily_details[day.dateStr].check_in || '-'"></span>
                                                        <template x-if="selectedReport.daily_details[day.dateStr].is_reward">
                                                            <div class="flex items-center gap-0.5 py-0.5">
                                                                <i data-lucide="gift" class="w-2.5 h-2.5 text-emerald-600 dark:text-emerald-400"></i>
                                                                <span class="text-[7.5px] font-black text-emerald-600 dark:text-emerald-400">OFF</span>
                                                            </div>
                                                        </template>
                                                        <span class="text-[10px] font-semibold" :class="selectedReport.daily_details[day.dateStr].is_early_checkout ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400 dark:text-slate-400'" x-text="selectedReport.daily_details[day.dateStr].check_out || '-'"></span>
                                                    </div>
                                                </template>

                                                <!-- Reward Libur -->
                                                <template x-if="selectedReport.daily_details[day.dateStr].status === 'Reward Libur'">
                                                    <div class="w-full py-1 text-center bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 rounded-md flex items-center justify-center gap-1 font-extrabold text-[9px]"
                                                         :title="selectedReport.daily_details[day.dateStr].reward_name || 'Reward Libur'">
                                                        <i data-lucide="gift" class="w-3 h-3 text-emerald-600 dark:text-emerald-400"></i>
                                                        <span>OFF</span>
                                                    </div>
                                                </template>

                                                <!-- Alfa -->
                                                <template x-if="selectedReport.daily_details[day.dateStr].status === 'Alfa'">
                                                    <div class="w-full py-1 text-center bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-400 rounded-md text-[9px] font-extrabold">A</div>
                                                </template>

                                                <!-- Off -->
                                                <template x-if="selectedReport.daily_details[day.dateStr].status === 'Off'">
                                                    <div class="w-full py-1 text-center bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 rounded-md text-[9px] font-extrabold">OFF</div>
                                                </template>
  
                                                <!-- Cuti/Izin -->
                                                <template x-if="selectedReport.daily_details[day.dateStr].status === 'Cuti/Izin'">
                                                    <div class="flex flex-col items-center gap-0.5 w-full">
                                                        <!-- Both Check-In and Check-Out -->
                                                        <template x-if="selectedReport.daily_details[day.dateStr].check_in && selectedReport.daily_details[day.dateStr].check_out">
                                                            <div class="flex flex-col items-center w-full leading-none">
                                                                <span class="text-[10px] font-bold" 
                                                                      :class="selectedReport.daily_details[day.dateStr].is_late ? 'text-amber-500' : 'text-emerald-600 dark:text-emerald-400'"
                                                                      x-text="selectedReport.daily_details[day.dateStr].check_in"></span>
                                                                <div class="w-full py-0.5 text-center rounded text-[9px] font-extrabold"
                                                                     :class="getClassForLeave(selectedReport.daily_details[day.dateStr])"
                                                                     x-text="selectedReport.daily_details[day.dateStr].leave_code"></div>
                                                                <span class="text-[10px]" :class="selectedReport.daily_details[day.dateStr].is_early_checkout ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400 dark:text-slate-400'" x-text="selectedReport.daily_details[day.dateStr].check_out"></span>
                                                            </div>
                                                        </template>
                                                        
                                                        <!-- Only Check-In -->
                                                        <template x-if="selectedReport.daily_details[day.dateStr].check_in && !selectedReport.daily_details[day.dateStr].check_out">
                                                            <div class="flex flex-col items-center w-full leading-none">
                                                                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400" 
                                                                      x-text="selectedReport.daily_details[day.dateStr].check_in"></span>
                                                                <div class="w-full py-0.5 text-center rounded text-[9px] font-extrabold"
                                                                     :class="getClassForLeave(selectedReport.daily_details[day.dateStr])"
                                                                     x-text="selectedReport.daily_details[day.dateStr].leave_code"></div>
                                                                <span class="text-[10px] text-slate-350 dark:text-slate-650">-</span>
                                                            </div>
                                                        </template>
                                                        
                                                        <!-- Only Check-Out -->
                                                        <template x-if="selectedReport.daily_details[day.dateStr].check_out && !selectedReport.daily_details[day.dateStr].check_in">
                                                            <div class="flex flex-col items-center w-full leading-none">
                                                                <span class="text-[10px] text-slate-350 dark:text-slate-650">-</span>
                                                                <div class="w-full py-0.5 text-center rounded text-[9px] font-extrabold"
                                                                     :class="getClassForLeave(selectedReport.daily_details[day.dateStr])"
                                                                     x-text="selectedReport.daily_details[day.dateStr].leave_code"></div>
                                                                <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500" 
                                                                      x-text="selectedReport.daily_details[day.dateStr].check_out"></span>
                                                            </div>
                                                        </template>
  
                                                        <!-- Neither -->
                                                        <template x-if="!selectedReport.daily_details[day.dateStr].check_in && !selectedReport.daily_details[day.dateStr].check_out">
                                                            <div class="flex flex-col items-center w-full leading-none">
                                                                <div class="w-full py-1 text-center rounded-md text-[9px] font-extrabold"
                                                                     :class="getClassForLeave(selectedReport.daily_details[day.dateStr])"
                                                                     x-text="selectedReport.daily_details[day.dateStr].leave_code"></div>
                                                                <template x-if="selectedReport.daily_details[day.dateStr].is_pending">
                                                                    <span class="text-[10px] font-bold text-slate-350 dark:text-slate-655 mt-0.5">-</span>
                                                                </template>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
  
                                                <!-- Libur -->
                                                <template x-if="selectedReport.daily_details[day.dateStr].status === 'Libur'">
                                                    <div class="text-center text-red-300 dark:text-red-900/30 text-[10px] font-bold">-</div>
                                                </template>

                                                <!-- Bonus Nominal Badge -->
                                                <div class="mt-1 w-full pt-0.5 border-t border-slate-100 dark:border-slate-800/60">
                                                    <template x-if="selectedReport.daily_details[day.dateStr].bonus_nominal > 0">
                                                        <div class="w-full py-0.5 text-center bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded text-[7.5px] font-extrabold tracking-tight"
                                                             x-text="'Rp ' + (selectedReport.daily_details[day.dateStr].bonus_nominal >= 1000 ? (selectedReport.daily_details[day.dateStr].bonus_nominal / 1000) + 'k' : selectedReport.daily_details[day.dateStr].bonus_nominal)"></div>
                                                    </template>
                                                    <template x-if="!selectedReport.daily_details[day.dateStr].bonus_nominal || selectedReport.daily_details[day.dateStr].bonus_nominal <= 0">
                                                        <div class="w-full py-0.5 text-center">
                                                            <template x-if="day.dateStr > new Date().toLocaleDateString('en-CA')">
                                                                <div class="text-slate-300 dark:text-slate-700 text-[7.5px] font-medium">-</div>
                                                            </template>
                                                            <template x-if="day.dateStr <= new Date().toLocaleDateString('en-CA')">
                                                                <div class="bg-slate-100 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 rounded text-[7.5px] font-semibold">Rp 0</div>
                                                            </template>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Modal Footer (Stats Summary) -->
                    <div class="border-t border-slate-100 dark:border-slate-800/60 bg-slate-50/50 dark:bg-slate-900/30 px-5 py-3 flex flex-row items-center justify-between gap-4">
                        <!-- Stats Grid -->
                        <div class="flex flex-wrap gap-2.5 text-[9px] md:text-xs">
                            <span class="font-bold text-slate-700 dark:text-slate-350">Ringkasan:</span>
                            <span class="text-slate-600 dark:text-slate-400">Hadir: <strong class="text-emerald-650 dark:text-emerald-405" x-text="stats.hadir"></strong></span>
                            <template x-if="stats.reward > 0">
                                <span class="text-slate-600 dark:text-slate-400">Reward: <strong class="text-emerald-600 dark:text-emerald-400" x-text="stats.reward"></strong></span>
                            </template>
                            <template x-if="stats.piket > 0">
                                <span class="text-slate-600 dark:text-slate-400">Piket: <strong class="text-amber-500" x-text="stats.piket"></strong></span>
                            </template>
                            <span class="text-slate-600 dark:text-slate-400">Telat: <strong class="text-amber-500" x-text="stats.telat"></strong></span>
                            <span class="text-slate-600 dark:text-slate-400">Alfa: <strong class="text-rose-500" x-text="stats.alfa"></strong></span>
                            <span class="text-slate-600 dark:text-slate-400">Izin/Cuti: <strong class="text-purple-600 dark:text-purple-400" x-text="stats.izin"></strong></span>
                        </div>
                        <button type="button" @click="showCalendarModal = false" class="w-full sm:w-auto h-7 px-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs rounded-lg transition-colors cursor-pointer">
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
                        <span class="text-amber-600 dark:text-amber-400 font-bold shrink-0">⚡ Piket:</span>
                    </template>
                    <template x-if="!matrixTooltip.data?.is_picket && matrixTooltip.data?.is_reward">
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold shrink-0">🎁 Reward</span>
                    </template>
                    <template x-if="!matrixTooltip.data?.is_picket && !matrixTooltip.data?.is_reward">
                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </template>
                    <span class="truncate font-semibold text-slate-700 dark:text-slate-300" x-text="matrixTooltip.data?.is_picket ? (matrixTooltip.data?.picket_area || matrixTooltip.data?.shift_name || 'Area Piket') : (matrixTooltip.data?.shift_name || 'Reguler')"></span>
                </div>
                <span class="font-mono font-bold text-slate-600 dark:text-slate-300 shrink-0" x-text="matrixTooltip.data?.jadwal"></span>
            </div>

            <!-- Box Masuk & Pulang (SANS Unit Style) -->
            <template x-if="matrixTooltip.data?.is_off">
                <div class="px-3 py-2.5 mb-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-800 text-center">
                    <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400" x-text="matrixTooltip.data?.status"></span>
                </div>
            </template>
            <template x-if="!matrixTooltip.data?.is_off">
                <div class="grid grid-cols-2 gap-2 mb-2.5">
                    <!-- MASUK -->
                    <div class="flex flex-col bg-slate-50 dark:bg-slate-800/50 rounded-xl p-2 border border-slate-100 dark:border-slate-800 text-center">
                        <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5">Masuk</span>
                        <span class="font-extrabold text-[13px] tracking-tight"
                              :class="(matrixTooltip.data?.check_in && matrixTooltip.data?.check_in !== '-') ? (matrixTooltip.data?.is_red_late ? 'text-rose-600 dark:text-rose-400' : (matrixTooltip.data?.is_late ? 'text-amber-600 dark:text-amber-400' : 'text-slate-800 dark:text-slate-100')) : 'text-slate-800 dark:text-slate-100'"
                              x-text="matrixTooltip.data?.check_in || '-'">
                        </span>
                        <template x-if="matrixTooltip.data?.check_in && matrixTooltip.data?.check_in !== '-' && matrixTooltip.data?.is_red_late">
                            <span class="text-[8px] font-bold text-rose-700 bg-rose-100 dark:bg-rose-950/60 dark:text-rose-300 px-1.5 py-0.5 rounded-md mt-1 mx-auto leading-none"
                                  x-text="'Telat ' + formatDuration(matrixTooltip.data?.late_minutes)">
                            </span>
                        </template>
                        <template x-if="matrixTooltip.data?.check_in && matrixTooltip.data?.check_in !== '-' && !matrixTooltip.data?.is_red_late && matrixTooltip.data?.is_late && matrixTooltip.data?.late_minutes > 0">
                            <span class="text-[8px] font-bold text-amber-700 bg-amber-100 dark:bg-amber-950/60 dark:text-amber-300 px-1.5 py-0.5 rounded-md mt-1 mx-auto leading-none"
                                  x-text="'Telat ' + formatDuration(matrixTooltip.data?.late_minutes)">
                            </span>
                        </template>
                        <template x-if="matrixTooltip.data?.check_in && matrixTooltip.data?.check_in !== '-' && !matrixTooltip.data?.is_late && matrixTooltip.data?.early_minutes > 0">
                            <span class="text-[8px] font-bold text-emerald-700 bg-emerald-100 dark:bg-emerald-950/60 dark:text-emerald-300 px-1.5 py-0.5 rounded-md mt-1 mx-auto leading-none"
                                  x-text="'Awal ' + formatDuration(matrixTooltip.data?.early_minutes)">
                            </span>
                        </template>
                        <template x-if="matrixTooltip.data?.check_in && matrixTooltip.data?.check_in !== '-' && matrixTooltip.data?.check_in !== 'Tidak Hadir' && !matrixTooltip.data?.is_late && (!matrixTooltip.data?.early_minutes || matrixTooltip.data?.early_minutes <= 0)">
                            <span class="text-[8px] font-bold text-emerald-700 bg-emerald-100 dark:bg-emerald-950/60 dark:text-emerald-300 px-1.5 py-0.5 rounded-md mt-1 mx-auto leading-none">
                                Tepat Waktu
                            </span>
                        </template>
                    </div>

                    <!-- PULANG -->
                    <div class="flex flex-col bg-slate-50 dark:bg-slate-800/50 rounded-xl p-2 border border-slate-100 dark:border-slate-800 text-center">
                        <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5">Pulang</span>
                        <span class="font-extrabold text-[13px] tracking-tight"
                              :class="matrixTooltip.data?.is_early_checkout ? 'text-amber-600 dark:text-amber-400' : 'text-slate-800 dark:text-slate-100'"
                              x-text="matrixTooltip.data?.check_out || '-'">
                        </span>
                        <template x-if="matrixTooltip.data?.check_out && matrixTooltip.data?.check_out !== '-' && matrixTooltip.data?.is_early_checkout">
                            <span class="text-[8px] font-bold text-amber-700 bg-amber-100 dark:bg-amber-950/60 dark:text-amber-300 px-1.5 py-0.5 rounded-md mt-1 mx-auto leading-none"
                                  x-text="'Pulang awal ' + formatDuration(matrixTooltip.data?.early_checkout_minutes)">
                            </span>
                        </template>
                        <template x-if="matrixTooltip.data?.check_out && matrixTooltip.data?.check_out !== '-' && !matrixTooltip.data?.is_early_checkout">
                            <span class="text-[8px] font-medium text-slate-400 dark:text-slate-500 mt-1 leading-none">
                                Tap Pulang
                            </span>
                        </template>
                    </div>
                </div>
            </template>

            <!-- Uang Kehadiran Card -->
            <div class="rounded-xl p-2.5 mb-2.5 border flex items-center justify-between"
                 :class="(matrixTooltip.data?.bonus_nominal > 0) ? 'bg-emerald-50/80 dark:bg-emerald-950/30 border-emerald-200/80 dark:border-emerald-800/50' : 'bg-slate-50 dark:bg-slate-800/40 border-slate-100 dark:border-slate-800'">
                <div class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 shrink-0" :class="(matrixTooltip.data?.bonus_nominal > 0) ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-[10px] font-bold" :class="(matrixTooltip.data?.bonus_nominal > 0) ? 'text-emerald-800 dark:text-emerald-300' : 'text-slate-500 dark:text-slate-400'">Uang Kehadiran</span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="text-[12px] font-extrabold font-mono tracking-tight"
                          :class="(matrixTooltip.data?.bonus_nominal > 0) ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400'"
                          x-text="'Rp ' + Number(matrixTooltip.data?.bonus_nominal || 0).toLocaleString('id-ID')">
                    </span>
                </div>
            </div>

            <!-- Keterangan & Catatan Presensi Block -->
            <div class="rounded-xl p-2.5 border"
                 :class="{
                     'bg-emerald-50/80 dark:bg-emerald-950/30 border-emerald-200/80 dark:border-emerald-800/50 text-emerald-900 dark:text-emerald-200': matrixTooltip.data?.status_color === 'emerald',
                     'bg-amber-50/80 dark:bg-amber-950/30 border-amber-200/80 dark:border-amber-800/50 text-amber-900 dark:text-amber-200': matrixTooltip.data?.status_color === 'amber',
                     'bg-rose-50/80 dark:bg-rose-950/30 border-rose-200/80 dark:border-rose-800/50 text-rose-900 dark:text-rose-200': matrixTooltip.data?.status_color === 'rose',
                     'bg-blue-50/80 dark:bg-blue-950/30 border-blue-200/80 dark:border-blue-800/50 text-blue-900 dark:text-blue-200': matrixTooltip.data?.status_color === 'blue',
                     'bg-purple-50/80 dark:bg-purple-950/30 border-purple-200/80 dark:border-purple-800/50 text-purple-900 dark:text-purple-200': matrixTooltip.data?.status_color === 'purple',
                     'bg-slate-50 dark:bg-slate-800/40 border-slate-100 dark:border-slate-800 text-slate-700 dark:text-slate-300': matrixTooltip.data?.status_color === 'slate'
                 }">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1.5 font-bold text-[10px]">
                        <svg class="w-3.5 h-3.5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span x-text="matrixTooltip.data?.leave_type ? 'Pengajuan ' + matrixTooltip.data.leave_type : 'Keterangan Presensi'"></span>
                    </div>
                    <template x-if="matrixTooltip.data?.leave_code">
                        <span class="text-[8.5px] font-black px-1.5 py-0.5 rounded leading-none uppercase bg-white/80 dark:bg-slate-900/80 border border-current shadow-xs"
                              x-text="matrixTooltip.data?.leave_code + (matrixTooltip.data?.is_pending ? ' (Pending)' : ' (OK)')">
                        </span>
                    </template>
                </div>

                <!-- Catatan informatif tambahan -->
                <template x-if="matrixTooltip.data?.note">
                    <div class="mt-1.5 pt-1.5 border-t border-current/15 text-[9.5px] leading-snug"
                         x-text="matrixTooltip.data.note">
                    </div>
                </template>
            </div>
        </div>
    </div>
</x-admin-layout>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('attendanceLogs', () => ({
            showCalendarModal: false,
            selectedReport: null,
            startDateStr: '{{ \Carbon\Carbon::parse($startDate)->translatedFormat("d M Y") }}',
            endDateStr: '{{ \Carbon\Carbon::parse($endDate)->translatedFormat("d M Y") }}',
            cycleDates: @json($cycleDateData),
            calendarDays: [],
            stats: { hadir: 0, telat: 0, alfa: 0, izin: 0, off: 0, reward: 0, piket: 0, totalBonus: 0 },
            
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
            formatDuration(minutes) {
                const totalMinutes = Math.abs(Number(minutes) || 0);
                const hours = Math.floor(totalMinutes / 60);
                const remainingMinutes = totalMinutes % 60;

                return hours > 0
                    ? `${hours} jam${remainingMinutes > 0 ? ` ${remainingMinutes} menit` : ''}`
                    : `${totalMinutes} menit`;
            },
            getEarlyCheckoutData(detail) {
                let isEarly = !!detail.is_early_checkout;
                let minutes = Number(detail.early_checkout_minutes || 0);

                if (!Object.prototype.hasOwnProperty.call(detail, 'is_early_checkout') || (isEarly && minutes === 0)) {
                    const checkOut = detail.check_out ? detail.check_out.substring(0, 5) : '';
                    const shiftStart = detail.shift_start ? detail.shift_start.substring(0, 5) : '';
                    const shiftEnd = detail.shift_end ? detail.shift_end.substring(0, 5) : '';

                    if (checkOut && checkOut !== '-' && shiftEnd) {
                        const toMinutes = (time) => {
                            const [hours, mins] = time.split(':').map(Number);
                            return hours * 60 + mins;
                        };
                        let expectedOut = toMinutes(shiftEnd);
                        let actualOut = toMinutes(checkOut);
                        const isNightShift = shiftStart && shiftStart > shiftEnd;

                        if (isNightShift) {
                            expectedOut += 24 * 60;
                            if (checkOut < shiftStart) actualOut += 24 * 60;
                        }
                        if (actualOut < expectedOut) {
                            isEarly = true;
                            minutes = expectedOut - actualOut;
                        }
                    }
                }

                return { is_early_checkout: isEarly, early_checkout_minutes: minutes };
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
                    const isReward = statusName === 'Reward Libur' || !!detail.is_reward;
                    const isLate = !!detail.is_late;
                    const isRedLate = !!detail.is_red_late;
                    const lateMnt = Number(detail.late_minutes || 0);
                    const earlyMnt = Number(detail.early_minutes || 0);

                    let statusBadge = 'Tepat Waktu';
                    let statusColor = 'emerald';
                    let noteText = earlyMnt > 0 ? `Datang ${this.formatDuration(earlyMnt)} lebih awal.` : 'Tepat waktu sesuai jadwal.';

                    if (isFuture && !detail.check_in) {
                        if (statusName === 'Cuti/Izin') {
                            statusBadge = (detail.is_pending ? 'Pending: ' : 'Disetujui: ') + (detail.leave_type || 'Izin');
                            statusColor = detail.leave_code === 'S' ? 'amber' : (detail.leave_code === 'C' ? 'blue' : (detail.leave_code === 'H' ? 'emerald' : 'purple'));
                            noteText = 'Pengajuan ' + (detail.leave_type || 'izin') + (detail.is_pending ? ' menunggu persetujuan.' : ' disetujui.');
                        } else {
                            statusBadge = 'Belum Terlaksana';
                            statusColor = 'slate';
                            noteText = 'Tanggal dan presensi belum berjalan.';
                        }
                    } else if (statusName === 'Hadir') {
                        if (isReward) {
                            statusBadge = 'Libur Reward';
                            statusColor = 'emerald';
                            noteText = 'Bebas keterlambatan (' + (detail.reward_name || 'Libur Reward') + ').';
                        } else if (isRedLate) {
                            statusBadge = 'Terlambat Berat';
                            statusColor = 'rose';
                            noteText = 'Terlambat ' + (lateMnt > 0 ? this.formatDuration(lateMnt) + ' ' : '') + '(melewati batas toleransi).';
                        } else if (isLate) {
                            statusBadge = 'Terlambat';
                            statusColor = 'amber';
                            noteText = 'Terlambat ' + (lateMnt > 0 ? this.formatDuration(lateMnt) + ' ' : '') + 'dari batas shift.';
                        } else {
                            statusBadge = 'Tepat Waktu';
                            statusColor = 'emerald';
                            noteText = earlyMnt > 0 ? `Datang ${this.formatDuration(earlyMnt)} lebih awal.` : 'Tepat waktu sesuai jadwal.';
                        }
                    } else if (statusName === 'Reward Libur') {
                        statusBadge = 'Libur Reward';
                        statusColor = 'emerald';
                        noteText = 'Mendapat reward libur (' + (detail.reward_name || 'Libur Reward') + ').';
                    } else if (statusName === 'Alfa') {
                        statusBadge = 'Tidak Hadir (Alfa)';
                        statusColor = 'rose';
                        noteText = 'Tidak ada catatan presensi kedatangan maupun kepulangan.';
                        if (detail.rejected_leave) {
                            noteText += ' Pengajuan ' + (detail.rejected_leave.leave_type || 'izin') + ' ditolak.';
                        }
                    } else if (statusName === 'Libur') {
                        statusBadge = isSunday ? 'Akhir Pekan' : 'Hari Libur';
                        statusColor = isSunday ? 'rose' : 'slate';
                        noteText = isSunday ? 'Hari libur akhir pekan.' : 'Hari libur nasional/bersama.';
                    } else if (statusName === 'Off') {
                        statusBadge = 'Jadwal Libur (OFF)';
                        statusColor = 'slate';
                        noteText = 'Jadwal libur rutin kerja pegawai.';
                    } else if (statusName === 'Cuti/Izin') {
                        const lType = detail.leave_type || 'Izin/Cuti';
                        const isP = !!detail.is_pending;
                        statusBadge = (isP ? 'Pending: ' : '') + lType;
                        statusColor = detail.leave_code === 'S' ? 'amber' : (detail.leave_code === 'C' ? 'blue' : (detail.leave_code === 'H' ? 'emerald' : 'purple'));
                        noteText = (isP ? 'Menunggu persetujuan: ' : 'Disetujui: ') + lType + '.';
                        if (detail.check_in || detail.check_out) {
                            noteText += ' Disertai scan (' + (detail.check_in || '-') + ' - ' + (detail.check_out || '-') + ').';
                        }
                    } else if (statusName === 'Pending') {
                        statusBadge = 'Menunggu Jam Kerja';
                        statusColor = 'slate';
                        noteText = 'Shift belum dimulai.';
                    }

                    const isOff = (statusName === 'Libur' || statusName === 'Off' || (isSunday && !detail.check_in));
                    const shiftStart = detail.shift_start ? detail.shift_start.substring(0, 5) : '';
                    const shiftEnd = detail.shift_end ? detail.shift_end.substring(0, 5) : '';
                    const jadwalStr = shiftStart ? (shiftEnd ? `${shiftStart} - ${shiftEnd}` : shiftStart) : (isOff ? 'OFF' : '-');
                    const earlyCheckout = this.getEarlyCheckoutData(detail);

                    modalData = {
                        employee: { name: empName, unit: unitName, position: position },
                        date_display: dateDisplay,
                        day_name: dayName,
                        is_today: day.dateStr === todayStr,
                        is_sunday: isSunday,
                        is_future: isFuture,
                        is_off: isOff,
                        status: statusBadge,
                        status_color: statusColor,
                        shift_name: detail.shift_name || (isSunday ? 'Akhir Pekan' : 'Reguler'),
                        jadwal: jadwalStr,
                        check_in: isFuture ? '-' : (detail.check_in || '-'),
                        check_out: isFuture ? '-' : (detail.check_out || '-'),
                        is_late: isLate,
                        is_red_late: isRedLate,
                        late_minutes: lateMnt,
                        early_minutes: earlyMnt,
                        is_early_checkout: earlyCheckout.is_early_checkout,
                        early_checkout_minutes: earlyCheckout.early_checkout_minutes,
                        is_reward: isReward,
                        reward_name: detail.reward_name || null,
                        leave_type: detail.leave_type || null,
                        leave_code: detail.leave_code || null,
                        is_pending: !!detail.is_pending,
                        rejected_leave: detail.rejected_leave || null,
                        is_picket: !!detail.is_picket,
                        picket_area: detail.picket_area || 'Area Piket',
                        bonus_nominal: detail.bonus_nominal || 0,
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
                        shift_name: isSunday ? 'Akhir Pekan' : 'Jadwal Libur Rutin',
                        jadwal: 'OFF',
                        check_in: '-',
                        check_out: '-',
                        is_late: false,
                        is_red_late: false,
                        late_minutes: 0,
                        early_minutes: 0,
                        is_reward: false,
                        reward_name: null,
                        leave_type: null,
                        leave_code: null,
                        is_pending: false,
                        rejected_leave: null,
                        is_picket: false,
                        picket_area: null,
                        bonus_nominal: 0,
                        schema_name: null,
                        note: isSunday ? 'Hari libur akhir pekan.' : 'Hari libur rutin pegawai.',
                    };
                }
                
                this.showMatrixTooltipInstant(e, modalData);
            },
            
            getClassForLeave(day) {
                if (!day) return '';
                if (day.is_pending) {
                    return day.leave_code === 'S' ? 'bg-amber-50/40 text-amber-500 border border-dashed border-amber-200/50' :
                           day.leave_code === 'I' ? 'bg-purple-50/40 text-purple-500 border border-dashed border-purple-200/50' :
                           day.leave_code === 'C' ? 'bg-blue-50/40 text-blue-500 border border-dashed border-blue-200/50' :
                           day.leave_code === 'H' ? 'bg-emerald-50/40 text-emerald-500 border border-dashed border-emerald-200/50' : '';
                }
                return day.leave_code === 'S' ? 'bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-900/30' :
                       day.leave_code === 'I' ? 'bg-purple-50 dark:bg-purple-950/20 text-purple-600 dark:text-purple-400 border border-purple-100 dark:border-purple-900/30' :
                       day.leave_code === 'C' ? 'bg-blue-50 dark:bg-blue-950/20 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/30' :
                       day.leave_code === 'H' ? 'bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/30' : '';
            },
            openCalendarModal(report) {
                const dailyDetails = Object.fromEntries(Object.entries(report.daily_details || {}).map(([date, detail]) => [
                    date,
                    { ...detail, ...this.getEarlyCheckoutData(detail) },
                ]));
                this.selectedReport = { ...report, daily_details: dailyDetails };
                this.calendarDays = this.buildCutOffCalendar(this.cycleDates);
                this.stats = this.calculateStats(report);
                this.showCalendarModal = true;
                this.$nextTick(() => {
                    if (window.lucide) {
                        window.lucide.createIcons();
                    }
                });
            },
            calculateStats(report) {
                let stats = { hadir: 0, telat: 0, alfa: 0, izin: 0, off: 0, reward: 0, piket: 0, totalBonus: 0 };
                if (!report || !report.daily_details) return stats;
                
                Object.values(report.daily_details).forEach(function(day) {
                    if (day.bonus_nominal) {
                        stats.totalBonus = (stats.totalBonus || 0) + (Number(day.bonus_nominal) || 0);
                    }
                    if (day.is_picket) {
                        stats.piket = (stats.piket || 0) + 1;
                    }
                    if (day.status === 'Hadir') {
                        stats.hadir++;
                        if (day.is_late) {
                            stats.telat++;
                        }
                    } else if (day.status === 'Reward Libur') {
                        stats.reward = (stats.reward || 0) + 1;
                    } else if (day.status === 'Alfa') {
                        stats.alfa++;
                    } else if (day.status === 'Cuti/Izin') {
                        stats.izin++;
                    } else if (day.status === 'Off' || day.status === 'Libur') {
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
    /* removed in favor of global styling */ /* removed */ .search-container button:hover_disabled_disabled {
        background-color: #0f172a !important; /* bg-slate-900 */
        color: #ffffff !important; /* text-white */
    }
    .dark /* removed in favor of global styling */ /* removed */ .search-container button:hover_disabled_disabled {
        background-color: #f8fafc !important; /* bg-slate-105 */
        color: #0f172a !important; /* text-slate-900 */
</style>
