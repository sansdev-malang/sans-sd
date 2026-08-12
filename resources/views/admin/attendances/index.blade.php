@php
    $start = \Carbon\Carbon::parse($startDate);
    $end = \Carbon\Carbon::parse($endDate);
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
    <div class="p-6 space-y-6" x-data="attendanceLogs">
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
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-55">Data Riwayat Absensi</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Memantau waktu kedatangan dan kepulangan pegawai secara komprehensif.</p>
            </div>
            
            <!-- EXPORT DATA (Pusat Style & Position) -->
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
        </section>

        <!-- FILTERS -->
        <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm p-4 w-full text-left">
            <form method="GET" action="{{ route('attendances.index') }}" class="flex flex-col md:flex-row gap-4 items-stretch md:items-center justify-between">
                
                <!-- Left Side: Search & Filters -->
                <div class="flex flex-wrap items-center gap-2 flex-1">
                    @if(auth()->user()->role !== 'employee')
                    <!-- Search Box Welded with Cari Button (Premium Input Group) -->
                    <div x-data="{ searchVal: '{{ request('search') }}' }" class="flex items-center w-full search-container bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg overflow-hidden shadow-inner focus-within:ring-0 focus-within:border-slate-300 dark:focus-within:border-slate-700">
                        <input type="text" name="search" x-model="searchVal" placeholder="Cari pegawai..."
                            style="border: none !important; outline: none !important; box-shadow: none !important;"
                            class="w-full h-9 px-3 text-xs bg-transparent text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-0">
                        <button type="submit" class="h-9 px-4 bg-slate-50 dark:bg-slate-850 text-slate-700 dark:text-slate-300 font-bold text-xs transition-all duration-150 cursor-pointer whitespace-nowrap flex items-center justify-center border-l border-slate-200 dark:border-slate-800">
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
            <div class="overflow-x-auto" style="max-height: calc(100vh - 280px); overflow-y: auto;">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900">
                            <th class="px-4 py-3 bg-slate-50 dark:bg-slate-900 text-left sticky top-0 left-auto md:left-0 z-30 md:z-40 border-r border-slate-200 dark:border-slate-800 min-w-[200px]">
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
                                <td class="px-4 py-2 static md:sticky md:left-0 md:z-10 bg-white dark:bg-slate-900 group-hover:bg-slate-50/60 dark:group-hover:bg-slate-900/30 border-r border-slate-100 dark:border-slate-800/60 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <!-- Clickable Avatar -->
                                        <div @click='openCalendarModal(@json($report))' class="cursor-pointer hover:scale-105 transform transition-all active:scale-95 duration-150 shrink-0">
                                            @if(!empty($report['employee']['photo']))
                                                <img src="{{ str_contains($report['employee']['photo'], 'photos/') ? '/storage/' . $report['employee']['photo'] : '/storage/photos/' . $report['employee']['photo'] }}" class="w-8 h-8 rounded-full object-cover border border-slate-200/50 dark:border-slate-800/40">
                                            @else
                                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white shadow-sm" style="background:{{ $color }}">{{ $initial }}</div>
                                            @endif
                                        </div>
                                        <div class="flex flex-col min-w-0 text-left">
                                            <span @click='openCalendarModal(@json($report))' class="text-xs font-bold text-slate-900 dark:text-slate-100 truncate cursor-pointer hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ $empName }}</span>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[10px] text-slate-500 dark:text-slate-400 truncate">{{ $report['employee']['position'] ?? ($report['employee']['subject_position'] ?? '-') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                
                                
                                                                <!-- KOLOM 3+: TANGGAL -->
                                @foreach($dates as $date)
                                @php
                                    $dateStr = $date->format('Y-m-d');
                                    $detail = $report['daily_details'][$dateStr] ?? null;
                                    
                                    $cellTooltip = '';
                                    if ($detail) {
                                        if ($detail['status'] === 'Hadir') {
                                            $cellTooltip = "Hadir | Masuk: " . ($detail['check_in'] ?? '-') . " | Pulang: " . ($detail['check_out'] ?? '-');
                                            if (!empty($detail['is_late'])) {
                                                $cellTooltip .= " (Terlambat)";
                                            }
                                            if (!empty($detail['rejected_leave'])) {
                                                $cellTooltip .= " | Ditolak: " . $detail['rejected_leave']['leave_type'];
                                            }
                                        } elseif ($detail['status'] === 'Alfa') {
                                            $cellTooltip = "Alfa (Tanpa Keterangan)";
                                            if (!empty($detail['rejected_leave'])) {
                                                $cellTooltip .= " | Ditolak: " . $detail['rejected_leave']['leave_type'];
                                            }
                                        } elseif ($detail['status'] === 'Libur') {
                                            $cellTooltip = "Hari Libur";
                                        } elseif ($detail['status'] === 'Off') {
                                            $cellTooltip = "Jadwal Off Kerja";
                                        } elseif ($detail['status'] === 'Cuti/Izin') {
                                            $leaveName = $detail['leave_type'] ?? 'Izin/Cuti';
                                            $isPending = !empty($detail['is_pending']);
                                            $prefix = $isPending ? 'Pending: ' : 'Disetujui: ';
                                            
                                            if (!empty($detail['check_in']) || !empty($detail['check_out'])) {
                                                $cellTooltip = $prefix . $leaveName . " | Masuk: " . ($detail['check_in'] ?? '-') . " | Pulang: " . ($detail['check_out'] ?? '-');
                                                if (!empty($detail['is_late'])) {
                                                    $cellTooltip .= " (Terlambat)";
                                                }
                                            } else {
                                                $cellTooltip = $prefix . $leaveName . " (Tidak Scan)";
                                            }
                                        }
                                    } elseif ($date->isSunday()) {
                                        $cellTooltip = "Hari Minggu";
                                    }
                                @endphp
                                <td class="py-1 px-1 text-center border-r border-slate-50 dark:border-slate-800/30 {{ $date->isSunday() ? 'bg-red-50/30 dark:bg-red-950/10' : '' }}" title="{{ $cellTooltip }}">
                                    @if($detail)
                                        @if($detail['status'] === 'Hadir')
                                            <div class="flex flex-col gap-0.5 items-center justify-center">
                                                <span class="text-[10px] font-bold {{ (!empty($detail['is_late'])) ? 'text-amber-500' : 'text-emerald-600 dark:text-emerald-400' }}">{{ $detail['check_in'] ?? '-' }}</span>
                                                @if(!empty($detail['pending_leave']))
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
                                                <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500">{{ $detail['check_out'] ?? '-' }}</span>
                                            </div>
                                        @elseif($detail['status'] === 'Alfa')
                                            <div class="flex flex-col gap-0.5 items-center justify-center">
                                                <div class="mx-auto w-full h-full min-h-[28px] flex items-center justify-center bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-400 border border-rose-100 dark:border-rose-900/30 rounded font-bold text-[10px]">A</div>
                                                @if(!empty($detail['pending_leave']))
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
                                            </div>
                                        @elseif($detail['status'] === 'Libur')
                                            <div class="mx-auto w-full h-full min-h-[28px] flex items-center justify-center text-red-200 dark:text-red-900/30 font-bold text-xs">-</div>
                                        @elseif($detail['status'] === 'Off')
                                            <div class="mx-auto w-full h-full min-h-[28px] flex items-center justify-center bg-slate-55 dark:bg-slate-900/40 text-slate-400 dark:text-slate-500 border border-slate-100 dark:border-slate-800/40 rounded font-bold text-[9px]">OFF</div>
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
                                                    <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500">{{ $detail['check_out'] }}</span>
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
                                                    <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500">{{ $detail['check_out'] }}</span>
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
                                <p class="text-[10px] text-slate-500 dark:text-slate-450 truncate">
                                    <span x-text="selectedReport ? (selectedReport.employee.position || '-') : ''"></span>
                                    &bull;
                                    <span x-text="selectedReport ? (selectedReport.employee.unit_name || '-') : ''" class="font-semibold text-indigo-650 dark:text-indigo-400"></span>
                                </p>
                            </div>
                        </div>
                        <button type="button" @click="showCalendarModal = false" class="rounded-lg p-1 text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-600 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-4">
                        <!-- Calendar Header (Month Name) -->
                        <div class="text-center mb-3">
                            <h4 class="text-[9px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider">
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
                                <div class="aspect-square border border-slate-100 dark:border-slate-800/40 rounded-lg p-1 sm:p-1.5 flex flex-col justify-between"
                                     :class="[
                                         day.isCurrentMonth ? (day.dateStr && new Date(day.dateStr).getDay() === 0 ? 'bg-red-50/50 dark:bg-red-95/15' : 'bg-white dark:bg-slate-900') : 'bg-slate-50/50 dark:bg-slate-955/20 opacity-40'
                                     ]">
                                     
                                    <!-- Day Number -->
                                    <span class="text-[10px] font-bold"
                                          :class="[
                                              day.isCurrentMonth ? 'text-slate-700 dark:text-slate-300' : 'text-slate-400',
                                              (new Date(day.dateStr).getDay() === 0) ? 'text-red-500 font-bold' : ''
                                          ]"
                                          x-text="day.day"></span>
                                          
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
                                                        <span class="text-[10px] font-semibold text-slate-450 dark:text-slate-550" x-text="selectedReport.daily_details[day.dateStr].check_out || '-'"></span>
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
                                                                <span class="text-[10px] text-slate-450 dark:text-slate-550" x-text="selectedReport.daily_details[day.dateStr].check_out"></span>
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
            stats: { hadir: 0, telat: 0, alfa: 0, izin: 0, off: 0 },
            
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
                this.selectedReport = report;
                this.calendarDays = this.buildCutOffCalendar(this.cycleDates);
                this.stats = this.calculateStats(report);
                this.showCalendarModal = true;
            },
            calculateStats(report) {
                let stats = { hadir: 0, telat: 0, alfa: 0, izin: 0, off: 0 };
                if (!report || !report.daily_details) return stats;
                
                Object.values(report.daily_details).forEach(function(day) {
                    if (day.status === 'Hadir') {
                        stats.hadir++;
                        if (day.is_late) {
                            stats.telat++;
                        }
                    } else if (day.status === 'Alfa') {
                          stats.alfa++;
                    } else if (day.status === 'Cuti/Izin') {
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
