<x-admin-layout>
    <div class="p-6 space-y-6">
        
        <!-- HEADER -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Rekap Bonus Kehadiran</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Evaluasi kehadiran pegawai berdasarkan skema bonus aktif.</p>
            </div>
            
            
        </section>

        <!-- FILTERS -->
        <section class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm p-4 w-full">
            <form method="GET" action="{{ route('bonus-reports.index') }}" class="flex flex-row flex-wrap items-end gap-3 w-full">
                
                <!-- Bulan -->
                <div class="space-y-1 w-40">
                    <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Bulan</label>
                    <input type="month" name="month" value="{{ request('month', $month) }}" class="w-full h-9 px-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
                </div>

                <!-- Cut-off -->
                <div class="flex flex-col justify-end pb-1.5 pr-2">
                    <span class="text-[10px] text-slate-500 font-medium whitespace-nowrap">Siklus: <br><strong class="text-slate-700 dark:text-slate-300 text-xs">{{ \Carbon\Carbon::parse($startDateReq)->format('d M') }} - {{ \Carbon\Carbon::parse($endDateReq)->format('d M') }}</strong></span>
                </div>

                

                @if(auth()->user()->role !== 'employee')
                <!-- Search -->
                <div class="space-y-1 w-60">
                    <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Pencarian</label>
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                            <i data-lucide="search" class="w-4 h-4"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIP..." class="w-full h-9 pl-9 pr-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
                    </div>
                </div>

                @endif
                <!-- Apply Buttons (Left side) -->
                <div class="flex items-center gap-2.5">
                    <button type="submit" class="h-9 px-5 bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 font-semibold text-xs rounded-lg shadow-sm transition-colors cursor-pointer whitespace-nowrap">
                        Terapkan
                    </button>
                    @if(request()->hasAny(['unit_id', 'search']) && count(request()->except('page')) > 0)
                        <a href="{{ route('bonus-reports.index') }}" class="inline-flex items-center justify-center h-9 px-4 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs rounded-lg shadow-sm transition-colors">
                            Reset
                        </a>
                    @endif
                </div>

                <!-- Tools (Right side) -->
                <div class="flex items-center gap-2.5 ml-auto">
                    @if(auth()->user()->role !== 'employee')
                    <!-- Per Page -->
                    <div>
                        <select name="per_page" onchange="this.form.submit()" class="h-9 px-3 text-xs font-semibold border border-slate-200 dark:border-slate-800 rounded-lg bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15 Baris</option>
                            <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 Baris</option>
                            <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 Baris</option>
                            <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>Semua Data</option>
                        </select>
                    </div>
                    
                    @endif
                    <div class="w-px h-6 bg-slate-200 dark:bg-slate-700 mx-1"></div>
                    
                    <!-- EXPORT DROPDOWN -->
                    <div x-data="{ open: false }" class="relative">
                        <button type="button" @click="open = !open" @click.outside="open = false" class="h-9 px-4 bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 font-semibold text-xs rounded-lg shadow-sm transition-all cursor-pointer whitespace-nowrap flex items-center gap-2">
                            <i data-lucide="download" class="w-4 h-4"></i>
                            <span>Ekspor</span>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 opacity-70"></i>
                        </button>
                        
                        <div x-show="open" x-transition.opacity.duration.200ms style="display: none;" class="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl shadow-lg z-50">
                            <a href="{{ route('bonus-reports.export', array_merge(request()->query(), ['format' => 'excel'])) }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors border-b border-slate-100 dark:border-slate-800">
                                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-600 dark:text-emerald-500"></i>
                                Excel (.xlsx)
                            </a>
                            <a href="{{ route('bonus-reports.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-rose-50 dark:hover:bg-rose-900/30 hover:text-rose-700 dark:hover:text-rose-400 transition-colors">
                                <i data-lucide="file-text" class="w-4 h-4 text-rose-600 dark:text-rose-500"></i>
                                PDF (.pdf)
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </section>
        
        <!-- MAIN TABLE (MATRIX LAYOUT) -->
        @php
            $start = \Carbon\Carbon::parse($startDateReq);
            $end = \Carbon\Carbon::parse($endDateReq);
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

        <section class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden w-full p-0">
            <div class="overflow-x-auto" style="max-height: calc(100vh - 280px); overflow-y: auto;">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30">
                            <th class="px-4 py-3 bg-slate-50 dark:bg-slate-900/60 text-left sticky top-0 left-0 z-40 border-r border-slate-200 dark:border-slate-800 min-w-[200px]">
                                <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Profil Pegawai</span>
                            </th>
                            <th class="px-4 py-3 bg-slate-50 dark:bg-slate-900/60 text-center sticky top-0 left-[200px] z-40 border-r border-slate-200 dark:border-slate-800 min-w-[120px]">
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
                            <th class="py-2 px-1 text-center sticky top-0 z-30 bg-slate-50 dark:bg-slate-900/60 min-w-[48px] max-w-[48px] border-r border-slate-100 dark:border-slate-800/60">
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
                                <td class="px-4 py-2 sticky left-0 z-10 bg-white dark:bg-slate-950 group-hover:bg-slate-50/60 dark:group-hover:bg-slate-900/30 border-r border-slate-100 dark:border-slate-800/60 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0 shadow-sm" style="background:{{ $color }}">{{ $initial }}</div>
                                        <div class="flex flex-col min-w-0">
                                            <span class="text-xs font-bold text-slate-900 dark:text-slate-100 truncate">{{ $empName }}</span>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[10px] text-slate-500 dark:text-slate-400 truncate">{{ $report['employee']['nuptk'] ?? '-' }}</span>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <!-- KOLOM 2: TOTAL BONUS -->
                                <td class="px-4 py-2 sticky left-[200px] z-10 bg-white dark:bg-slate-950 group-hover:bg-slate-50/60 dark:group-hover:bg-slate-900/30 border-r border-slate-100 dark:border-slate-800/60 text-center transition-colors">
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
                                    @if($detail)
                                        @if($detail['bonus_nominal'] > 0)
                                            @php 
                                                $nominal = $detail['bonus_nominal'];
                                                $shortNominal = ($nominal >= 1000) ? ($nominal / 1000) . 'k' : $nominal;
                                            @endphp
                                            <div class="mx-auto w-9 h-6 flex items-center justify-center bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 font-bold text-[10px] rounded shadow-sm border border-emerald-200 dark:border-emerald-800/50" title="Rp {{ number_format($nominal, 0, ',', '.') }}">
                                                {{ $shortNominal }}
                                            </div>
                                        @else
                                            <div class="mx-auto flex items-center justify-center text-slate-300 dark:text-slate-600 font-bold text-xs" title="Tidak ada bonus">-</div>
                                        @endif
                                    @else
                                        @if($date->isSunday())
                                            <div class="mx-auto flex items-center justify-center text-red-200 dark:text-red-900/30 font-bold text-xs" title="Hari Minggu">-</div>
                                        @else
                                            <div class="mx-auto flex items-center justify-center text-slate-100 dark:text-slate-800/50 font-bold text-[10px]">-</div>
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
    </div>
</x-admin-layout>

