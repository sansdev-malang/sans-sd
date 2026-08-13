<x-admin-layout>
<div class="p-6">
    <div class="max-w-7xl mx-auto space-y-6">
        
        <!-- HEADER -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Laporan Bonus</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Periode: {{ Carbon\Carbon::parse($startDateReq)->translatedFormat('d M Y') }} - {{ Carbon\Carbon::parse($endDateReq)->translatedFormat('d M Y') }}
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('bonus-reports.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    Refresh
                </a>
            </div>
        </div>

        @if(session('error'))
        <div class="p-4 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-sm rounded-xl border border-red-200 dark:border-red-800 flex items-start gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
            <div>{{ session('error') }}</div>
        </div>
        @endif

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
                        
                        <!-- Clear Button (x) -->
                        <button type="button" x-show="searchVal.trim() !== ''" @click="searchVal = ''; $el.closest('form').submit();" class="h-9 px-2.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer bg-transparent border-0 flex items-center justify-center" title="Bersihkan pencarian">
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
                    <input type="month" name="month" value="{{ $month }}" onchange="this.form.submit()"
                        class="h-9 px-2.5 flex-1 sm:flex-initial sm:w-36 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 shadow-inner focus:outline-none focus:ring-0 focus:ring-transparent focus:border-slate-300 dark:focus:border-slate-700">

                    @if(auth()->user()->role !== 'employee')
                    <!-- Tipe Pegawai -->
                    <select name="type" onchange="this.form.submit()"
                        class="h-9 pl-2.5 pr-8 flex-1 sm:flex-initial sm:w-44 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 shadow-inner focus:outline-none focus:ring-0 focus:ring-transparent focus:border-slate-300 dark:focus:border-slate-700 cursor-pointer text-ellipsis overflow-hidden whitespace-nowrap">
                        <option value="">Semua Tipe</option>
                        @foreach($employeeTypes ?? [] as $empType)
                            <option value="{{ $empType->code }}" {{ request('type') == $empType->code ? 'selected' : '' }}>{{ $empType->name }}</option>
                        @endforeach
                    </select>
                    @endif

                    @if(request()->anyFilled(['search', 'type']))
                        <a href="{{ route('bonus-reports.index', ['month' => $month]) }}" class="h-9 px-3 flex items-center justify-center bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-300 rounded-lg transition-colors reset-filter-btn" title="Reset Filter">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>

                <!-- Right Side (Aligned to Match Layout) -->
                <div class="flex items-center gap-2 w-full md:w-auto shrink-0 self-end md:self-auto justify-end">
                </div>
            </form>
        </section>

        <!-- TABLE -->
        <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden w-full">
            <div class="overflow-x-auto">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-900/30 border-b border-slate-200 dark:border-slate-800">
                            <th class="px-6 py-4 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-12 sticky top-0 bg-slate-50 dark:bg-slate-900">No</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider sticky top-0 bg-slate-50 dark:bg-slate-900">Nama Pegawai</th>
                            <th class="px-6 py-4 text-center font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-24 sticky top-0 bg-slate-50 dark:bg-slate-900">Hadir</th>
                            <th class="px-6 py-4 text-center font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-24 sticky top-0 bg-slate-50 dark:bg-slate-900">Terlambat</th>
                            <th class="px-6 py-4 text-center font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-24 sticky top-0 bg-slate-50 dark:bg-slate-900">Alpha</th>
                            <th class="px-6 py-4 text-right font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-36 sticky top-0 bg-slate-50 dark:bg-slate-900">Total Bonus</th>
                            <th class="px-4 py-4 sticky top-0 bg-slate-50 dark:bg-slate-900 w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60" x-data="{ expanded: null }">
                        @forelse($paginatedReports as $index => $report)
                            @php
                                $emp = $report['employee'] ?? [];
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-colors cursor-pointer" @click="expanded = expanded === {{ $index }} ? null : {{ $index }}">
                                <td class="px-6 py-4 whitespace-nowrap text-slate-500 dark:text-slate-400">
                                    {{ $paginatedReports->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold shrink-0">
                                            {{ substr($emp['name'] ?? '?', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-slate-900 dark:text-white line-clamp-1">
                                                {{ $emp['name'] ?? '-' }}
                                            </div>
                                            <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">
                                                {{ $emp['nuptk_nip_nik'] ?? '-' }}
                                                @if(!empty($emp['employee_type']['name']))
                                                    • <span class="px-1.5 py-0.5 rounded text-[9px] font-medium bg-slate-100 dark:bg-slate-800">{{ $emp['employee_type']['name'] }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-1 rounded-md bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-semibold">
                                        {{ $report['total_present'] ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex flex-col items-center justify-center">
                                        <span class="px-2 py-1 rounded-md {{ ($report['total_late_minutes'] ?? 0) > 0 ? 'bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400' : 'text-slate-400' }} font-semibold">
                                            {{ $report['total_late_minutes'] ?? 0 }} mnt
                                        </span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-1 rounded-md {{ ($report['total_absent'] ?? 0) > 0 ? 'bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400' : 'text-slate-400' }} font-semibold">
                                        {{ $report['total_absent'] ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="font-bold text-slate-900 dark:text-white text-sm">
                                        Rp {{ number_format($report['bonus_nominal'] ?? 0, 0, ',', '.') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center text-slate-400">
                                    <i data-lucide="chevron-down" class="w-5 h-5 transition-transform duration-200" :class="{'rotate-180': expanded === {{ $index }}}"></i>
                                </td>
                            </tr>
                            <!-- EXPANDED ROW -->
                            <tr x-show="expanded === {{ $index }}" x-collapse class="bg-slate-50/30 dark:bg-slate-900/10">
                                <td colspan="7" class="p-0 border-b border-slate-200 dark:border-slate-800">
                                    <div class="p-6">
                                        <h4 class="text-sm font-semibold text-slate-800 dark:text-slate-200 mb-4 flex items-center gap-2">
                                            <i data-lucide="calendar-days" class="w-4 h-4 text-blue-500"></i>
                                            Detail Kehadiran Harian
                                        </h4>
                                        <div class="overflow-x-auto rounded-lg border border-slate-200 dark:border-slate-800">
                                            <table class="w-full text-xs text-left">
                                                <thead class="bg-slate-100 dark:bg-slate-800/50 text-slate-600 dark:text-slate-300">
                                                    <tr>
                                                        <th class="px-4 py-2">Tanggal</th>
                                                        <th class="px-4 py-2 text-center">Shift</th>
                                                        <th class="px-4 py-2 text-center">Check-In</th>
                                                        <th class="px-4 py-2 text-center">Status</th>
                                                        <th class="px-4 py-2 text-center">Telat (Mnt)</th>
                                                        <th class="px-4 py-2 text-center">Tier Harian</th>
                                                        <th class="px-4 py-2 text-right">Bonus Harian</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                                    @forelse($report['daily_details'] ?? [] as $detail)
                                                    <tr class="hover:bg-white dark:hover:bg-slate-900">
                                                        <td class="px-4 py-2 font-medium">{{ \Carbon\Carbon::parse($detail['date'])->translatedFormat('d M Y') }}</td>
                                                        <td class="px-4 py-2 text-center font-mono">{{ $detail['shift_start'] ? \Carbon\Carbon::parse($detail['shift_start'])->format('H:i') : '-' }}</td>
                                                        <td class="px-4 py-2 text-center font-mono text-emerald-600 dark:text-emerald-400">{{ $detail['check_in'] ? \Carbon\Carbon::parse($detail['check_in'])->format('H:i') : '-' }}</td>
                                                        <td class="px-4 py-2 text-center">
                                                            @if($detail['status'] == 'Present')
                                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Hadir</span>
                                                            @elseif(isset($detail['status']) && $detail['status'] == 'Dinas')
                                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">Dinas</span>
                                                            @else
                                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Alfa</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-2 text-center">
                                                            @if($detail['late_minutes'] > 0)
                                                                <span class="text-red-600 dark:text-red-400 font-bold">{{ $detail['late_minutes'] }}</span>
                                                            @else
                                                                <span class="text-slate-400">-</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-2 text-center">
                                                            @if(isset($detail['tier_level']) && $detail['tier_level'])
                                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Tier {{ $detail['tier_level'] }}</span>
                                                            @else
                                                                <span class="text-slate-400">-</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-2 text-right font-bold text-emerald-600 dark:text-emerald-400">
                                                            {{ $detail['bonus_nominal'] > 0 ? 'Rp ' . number_format($detail['bonus_nominal'], 0, ',', '.') : '-' }}
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="7" class="px-4 py-4 text-center text-slate-500">Tidak ada detail kehadiran pada periode ini.</td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-slate-400 dark:text-slate-500">
                                        <i data-lucide="inbox" class="w-12 h-12 mb-3 text-slate-300 dark:text-slate-600"></i>
                                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Tidak ada data laporan bonus</p>
                                        <p class="text-xs mt-1 text-slate-500">Sesuaikan filter pencarian atau pastikan API HRD terhubung</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            @if($paginatedReports->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $paginatedReports->links('pagination::tailwind') }}
            </div>
            @endif
        </section>

    </div>
</div>
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
    }
    .dark input[type="month"]::-webkit-calendar-picker-indicator {
        filter: invert(1) !important;
    }
</style>
</x-admin-layout>