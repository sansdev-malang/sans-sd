<x-admin-layout>
    <div class="p-6 space-y-6">
        <!-- SUCCESS/ERROR ALERT -->
        @if(session('success'))
            <div class="bg-emerald-50 dark:bg-emerald-955/40 border border-emerald-200 dark:border-emerald-900/60 rounded-xl p-4 flex items-center gap-3">
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
            <div class="bg-rose-50 dark:bg-rose-955/40 border border-rose-200 dark:border-rose-900/60 rounded-xl p-4 flex items-center gap-3">
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
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Data Riwayat Absensi</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Memantau waktu kedatangan dan kepulangan pegawai secara komprehensif.</p>
            </div>
            
            <div class="flex items-center gap-2">
                                
            </div>
        </section>

        <!-- FILTERS -->
        <section class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm p-4 w-full">
            <form method="GET" action="{{ route('attendances.index') }}" class="flex flex-row flex-wrap items-end gap-3 w-full">
                
                <!-- Bulan -->
                <div class="space-y-1 w-40">
                    <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Bulan</label>
                    <input type="month" name="month" value="{{ request('month', $month) }}" class="w-full h-9 px-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
                </div>

                <!-- Cut-off -->
                <div class="flex flex-col justify-end pb-1.5">
                    <span class="text-[10px] text-slate-500 font-medium whitespace-nowrap">Siklus: <br><strong class="text-slate-700 dark:text-slate-300 text-xs">{{ \Carbon\Carbon::parse($startDate)->format('d M') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M') }}</strong></span>
                </div>

                @if(auth()->user()->role !== 'employee')
                <!-- Search -->
                <div class="space-y-1 w-60 ml-2">
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
                    @if(request()->has('search') && request('search') != '')
                        <a href="{{ route('attendances.index', ['month' => request('month', $month)]) }}" class="inline-flex items-center justify-center h-9 px-4 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs rounded-lg shadow-sm transition-colors">
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
                    <div x-data="{ open: false }" class="relative w-full sm:w-auto">
                        <button type="button" @click="open = !open" @click.outside="open = false" class="w-full sm:w-auto justify-center h-9 px-4 bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 border border-slate-200/50 dark:border-slate-800/40 font-semibold text-xs rounded-lg shadow-sm transition-all cursor-pointer whitespace-nowrap flex items-center gap-2">
                            <i data-lucide="download" class="w-4 h-4"></i>
                            <span>Ekspor</span>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 opacity-70"></i>
                        </button>
                        
                        <div x-show="open" x-transition.opacity.duration.200ms style="display: none;" class="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl shadow-lg overflow-hidden z-50">
                            <a href="{{ route('attendances.export', array_merge(request()->query(), ['format' => 'excel'])) }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors border-b border-slate-100 dark:border-slate-800">
                                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-600 dark:text-emerald-500"></i>
                                Excel (.xlsx)
                            </a>
                            <a href="{{ route('attendances.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-rose-50 dark:hover:bg-rose-900/30 hover:text-rose-700 dark:hover:text-rose-400 transition-colors">
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

        <section class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden w-full p-0">
            <div class="overflow-x-auto" style="max-height: calc(100vh - 280px); overflow-y: auto;">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30">
                            <th class="px-4 py-3 bg-slate-50 dark:bg-slate-900/60 text-left sticky top-0 left-0 z-40 border-r border-slate-200 dark:border-slate-800 min-w-[150px]">
                                <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Profil Pegawai</span>
                            </th>

                            @foreach($dates as $date)
                            @php
                                $isToday = $date->isToday();
                                $isSunday = $date->isSunday();
                            @endphp
                            <th class="py-2 px-1 text-center sticky top-0 z-30 bg-slate-50 dark:bg-slate-900/60 min-w-[32px] border-r border-slate-100 dark:border-slate-800/60">
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
                                
                                
                                                                <!-- KOLOM 3+: TANGGAL -->
                                @foreach($dates as $date)
                                @php
                                    $dateStr = $date->format('Y-m-d');
                                    $detail = $report['daily_details'][$dateStr] ?? null;
                                @endphp
                                <td class="py-1 px-1 text-center border-r border-slate-50 dark:border-slate-800/30">
                                    @if($detail)
                                        @if($detail['status'] === 'Hadir')
                                            <div class="flex flex-col gap-0.5 items-center justify-center">
                                                <span class="text-[10px] font-bold {{ (!empty($detail['is_late'])) ? 'text-amber-500' : 'text-emerald-600 dark:text-emerald-400' }}" title="Masuk">{{ $detail['check_in'] ?? '-' }}</span>
                                                <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500" title="Pulang">{{ $detail['check_out'] ?? '-' }}</span>
                                            </div>
                                        @elseif($detail['status'] === 'Alfa')
                                            <div class="mx-auto w-full h-full min-h-[28px] flex items-center justify-center bg-red-50 dark:bg-red-900/20 text-red-500 font-bold text-xs" title="Alfa">A</div>
                                        @elseif($detail['status'] === 'Libur')
                                            <div class="mx-auto w-full h-full min-h-[28px] flex items-center justify-center text-red-200 dark:text-red-900/30 font-bold text-xs" title="Libur">-</div>
                                        @elseif($detail['status'] === 'Off')
                                            <div class="mx-auto w-full h-full min-h-[28px] flex items-center justify-center bg-slate-100 dark:bg-slate-800/50 text-slate-400 font-bold text-[9px]" title="Off Shift">OFF</div>
                                        @elseif($detail['status'] === 'Cuti/Izin')
                                            <div class="mx-auto w-full h-full min-h-[28px] flex items-center justify-center bg-blue-50 dark:bg-blue-900/20 text-blue-500 font-bold text-[9px]" title="{{ $detail['leave_type'] ?? 'Izin/Cuti' }}">{{ $detail['leave_type'] ?? 'IZIN' }}</div>
                                        @else
                                            <div class="text-xs text-slate-300">-</div>
                                        @endif
                                    @else
                                        @if($date->isSunday())
                                            <div class="mx-auto flex items-center justify-center text-red-200 dark:text-red-900/30 font-bold text-xs" title="Minggu">-</div>
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
            
                        @if($reports instanceof \Illuminate\Pagination\LengthAwarePaginator && $reports->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30">
                    {{ $reports->appends(request()->query())->links('pagination::tailwind') }}
                </div>
            @endif
        </section>
    </div>
</x-admin-layout>
