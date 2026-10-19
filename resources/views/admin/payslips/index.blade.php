@php
    $isSuperAdmin = auth()->user() && auth()->user()->hasRole('super_admin');
    $currentDate = \Carbon\Carbon::parse($month . '-01');
    $prevMonth = $currentDate->copy()->subMonth()->format('Y-m');
    $nextMonth = $currentDate->copy()->addMonth()->format('Y-m');

    $globalLines = array_values(array_filter(array_map('trim', explode("\n", (string)$globalNote)), fn($l) => $l !== ''));
    $periodLines = array_values(array_filter(array_map('trim', explode("\n", (string)$periodNote)), fn($l) => $l !== ''));
    $hasNotes = !empty($globalLines) || !empty($periodLines);
@endphp
<x-admin-layout>
    <div class="p-3.5 sm:p-5 lg:p-6 space-y-4 sm:space-y-5 lg:space-y-6 text-left"
         x-data="{
             search: '',
             statusFilter: 'all',
             filterEmployee(el) {
                 const text = (el.getAttribute('data-search') || '').toLowerCase();
                 const status = el.getAttribute('data-status') || '';
                 const matchText = this.search.trim() === '' || text.includes(this.search.toLowerCase());
                 const matchStatus = this.statusFilter === 'all' || status === this.statusFilter;
                 return matchText && matchStatus;
             }
         }">

        <!-- HEADER -->
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-2 sm:gap-3 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-base sm:text-lg font-bold tracking-tight text-slate-900 dark:text-slate-50">Slip Gaji</h2>
                <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400">
                    {{ $isSuperAdmin ? 'Kelola dan pantau berkas slip gaji resmi seluruh pegawai.' : 'Unduh slip gaji dan lampiran resmi dari HRD.' }}
                </p>
            </div>

            <!-- Month Navigator in Header -->
            <form method="GET" action="{{ route('payslips.index') }}" class="w-full md:w-auto">
                <div class="inline-flex items-center justify-between w-full md:w-auto bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-1 rounded-xl shadow-xs">
                    <a href="{{ route('payslips.index', ['month' => $prevMonth]) }}" title="Bulan Sebelumnya ({{ \Carbon\Carbon::parse($prevMonth . '-01')->translatedFormat('M Y') }})" class="h-7 w-7 inline-flex items-center justify-center rounded-lg text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors shrink-0">
                        <i data-lucide="chevron-left" class="w-4 h-4"></i>
                    </a>
                    <input type="month" id="month" name="month" value="{{ $month }}" class="h-7 px-2 text-xs font-bold bg-transparent text-slate-800 dark:text-slate-200 border-0 focus:ring-0 cursor-pointer text-center font-mono dark:[color-scheme:dark]" onchange="this.form.submit()">
                    <a href="{{ route('payslips.index', ['month' => $nextMonth]) }}" title="Bulan Selanjutnya ({{ \Carbon\Carbon::parse($nextMonth . '-01')->translatedFormat('M Y') }})" class="h-7 w-7 inline-flex items-center justify-center rounded-lg text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors shrink-0">
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </form>
        </header>

        @if($isSuperAdmin)
            <!-- MODERN FILTER & SEARCH SECTION -->
            <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3.5 sm:p-4 shadow-sm w-full text-left">
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                    <div class="flex flex-1 flex-col sm:flex-row items-stretch sm:items-center gap-2">
                        <!-- Search Box -->
                        <div class="relative flex-1 max-w-md">
                            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                            <input type="text" x-model="search" placeholder="Cari nama pegawai, NIY, atau jabatan..." class="w-full h-9 pl-9 pr-8 text-xs bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors">
                            <button type="button" x-show="search.trim() !== ''" @click="search = ''" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>

                        <!-- Status Filter Pills -->
                        <div class="inline-flex items-center gap-1 bg-slate-50 dark:bg-slate-950/60 p-1 border border-slate-200 dark:border-slate-800 rounded-lg text-xs font-semibold overflow-x-auto">
                            <button type="button" @click="statusFilter = 'all'" :class="statusFilter === 'all' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 shadow-xs' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400'" class="px-2.5 py-1 rounded-md transition-all cursor-pointer text-[11px] whitespace-nowrap">
                                Semua ({{ $employees->count() }})
                            </button>
                            <button type="button" @click="statusFilter = 'ready'" :class="statusFilter === 'ready' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400'" class="px-2.5 py-1 rounded-md transition-all cursor-pointer text-[11px] whitespace-nowrap">
                                Tersedia ({{ $employees->filter(fn($e) => !empty($e->payslip_url))->count() }})
                            </button>
                            <button type="button" @click="statusFilter = 'pending'" :class="statusFilter === 'pending' ? 'bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200 shadow-xs' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400'" class="px-2.5 py-1 rounded-md transition-all cursor-pointer text-[11px] whitespace-nowrap">
                                Belum Ada ({{ $employees->filter(fn($e) => empty($e->payslip_url))->count() }})
                            </button>
                        </div>
                    </div>

                    <div class="text-xs text-slate-500 dark:text-slate-400 shrink-0 self-end sm:self-auto">
                        Periode: <strong class="font-bold text-slate-800 dark:text-slate-200 font-mono">{{ $currentDate->translatedFormat('F Y') }}</strong>
                    </div>
                </div>
            </section>

            <!-- TABLE LIST FOR SUPER ADMIN (Desktop) -->
            <section class="hidden md:block bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden w-full text-left">
                <!-- Table Subheader Legend -->
                <div class="px-5 py-3 border-b border-slate-200 dark:border-slate-800 bg-slate-500/5 dark:bg-slate-900/50 flex flex-wrap items-center justify-between gap-3 text-[11px] font-semibold text-slate-500">
                    <span class="font-bold text-slate-900 dark:text-slate-200 flex items-center gap-2">
                        <i data-lucide="receipt" class="w-4 h-4 text-indigo-500"></i>
                        Daftar Slip Gaji Pegawai — {{ $currentDate->translatedFormat('F Y') }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/30 text-indigo-700 dark:text-indigo-400 border border-indigo-200/30 dark:border-indigo-900/30">
                        <i data-lucide="info" class="w-3.5 h-3.5"></i>
                        {{ $employees->whereNotNull('payslip_url')->count() }} dari {{ $employees->count() }} slip gaji tersedia
                    </span>
                </div>

                <div class="overflow-x-auto max-h-[calc(100vh-270px)] overflow-y-auto block relative rounded-b-xl border-t border-slate-100 dark:border-slate-800/60 custom-scrollbar">
                    <table class="w-full text-xs">
                        <thead class="sticky top-0 z-10 border-b border-slate-200 dark:border-slate-800 shadow-xs">
                            <tr class="text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                                <th class="px-4 py-3 text-center w-12 bg-slate-50 dark:bg-[#0d0d10]">No</th>
                                <th class="px-5 py-3 text-left min-w-[220px] bg-slate-50 dark:bg-[#0d0d10]">Pegawai</th>
                                <th class="px-4 py-3 text-left min-w-[130px] bg-slate-50 dark:bg-[#0d0d10]">Tipe Pegawai</th>
                                <th class="px-4 py-3 text-left min-w-[150px] bg-slate-50 dark:bg-[#0d0d10]">Jabatan</th>
                                <th class="px-4 py-3 text-center w-36 bg-slate-50 dark:bg-[#0d0d10]">Periode</th>
                                <th class="px-4 py-3 text-center w-32 bg-slate-50 dark:bg-[#0d0d10]">Status Slip</th>
                                <th class="px-5 py-3 text-right w-48 bg-slate-50 dark:bg-[#0d0d10]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-slate-700 dark:text-slate-350 font-medium">
                            @forelse($employees as $index => $emp)
                                <tr x-show="filterEmployee($el)"
                                    data-search="{{ strtolower($emp->name . ' ' . ($emp->niy ?? '') . ' ' . ($emp->position ?? '') . ' ' . ($emp->employeeType->name ?? '')) }}"
                                    data-status="{{ $emp->payslip_url ? 'ready' : 'pending' }}"
                                    class="hover:bg-slate-50/60 dark:hover:bg-slate-850/30 transition-colors">
                                    <td class="px-4 py-3.5 text-center text-slate-400 dark:text-slate-500 font-mono text-[11px]">{{ $index + 1 }}</td>
                                    <td class="px-5 py-3.5 text-left">
                                        <div class="flex items-center gap-2.5">
                                            @if(!empty($emp->photo_url))
                                                <img src="{{ $emp->photo_url }}" class="w-8 h-8 rounded-full object-cover shrink-0 ring-1 ring-slate-200 dark:ring-slate-700" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($emp->name) }}&background=6366f1&color=fff';">
                                            @else
                                                <div class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-100 dark:border-indigo-900/50 flex items-center justify-center text-[11px] font-bold text-indigo-600 dark:text-indigo-400 shrink-0">
                                                    {{ strtoupper(substr($emp->name, 0, 2)) }}
                                                </div>
                                            @endif
                                            <div class="flex flex-col min-w-0">
                                                <span class="text-xs font-bold text-slate-900 dark:text-slate-100 truncate">{{ $emp->name }}</span>
                                                <span class="text-[10px] text-slate-400 dark:text-slate-500 font-mono">NIY: {{ $emp->niy ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 text-left text-xs text-slate-600 dark:text-slate-300">
                                        {{ $emp->employeeType->name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3.5 text-left text-xs">
                                        <span class="font-medium text-slate-700 dark:text-slate-200 block truncate">{{ $emp->position ?? '-' }}</span>
                                        @if(!empty($emp->additional_position))
                                            <span class="text-[10px] text-slate-400 dark:text-slate-500 block truncate">{{ $emp->additional_position }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-center text-xs font-semibold text-slate-700 dark:text-slate-300 whitespace-nowrap font-mono">
                                        {{ $currentDate->translatedFormat('F Y') }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                        @if($emp->payslip_url)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-100/60 dark:border-emerald-900/40">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Tersedia
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                               Belum Ada
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                        @if($emp->payslip_url)
                                            <div class="flex items-center justify-end gap-1.5">
                                                <a href="{{ $emp->payslip_url }}" target="_blank" title="{{ $emp->original_filename ?? 'Slip Gaji PDF' }}" class="inline-flex items-center justify-center gap-1.5 h-8 px-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition-colors shadow-xs">
                                                    <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                                    <span>Slip Gaji</span>
                                                </a>
                                                @if($emp->attachment_url)
                                                    <a href="{{ $emp->attachment_url }}" target="_blank" title="{{ $emp->original_attachment_name ?? 'Lampiran Pendukung' }}" class="inline-flex items-center justify-center gap-1.5 h-8 px-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg transition-colors border border-slate-200 dark:border-slate-700">
                                                        <i data-lucide="paperclip" class="w-3.5 h-3.5"></i>
                                                        <span>Lampiran</span>
                                                    </a>
                                                @endif
                                            </div>
                                        @else
                                            <span class="inline-flex items-center justify-center h-8 px-3 bg-slate-50 dark:bg-slate-800/60 text-slate-400 dark:text-slate-500 text-xs font-medium rounded-lg border border-slate-200/60 dark:border-slate-800 cursor-not-allowed">
                                                Menunggu HRD
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                                                <i data-lucide="inbox" class="w-5 h-5"></i>
                                            </div>
                                            <span class="text-xs font-medium">Tidak ada data pegawai.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- CARD LIST FOR SUPER ADMIN (Mobile) -->
            <div class="block md:hidden space-y-3">
                @forelse($employees as $emp)
                    <div x-show="filterEmployee($el)"
                         data-search="{{ strtolower($emp->name . ' ' . ($emp->niy ?? '') . ' ' . ($emp->position ?? '') . ' ' . ($emp->employeeType->name ?? '')) }}"
                         data-status="{{ $emp->payslip_url ? 'ready' : 'pending' }}"
                         class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3.5 shadow-xs space-y-3 text-left">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-2.5 min-w-0">
                                @if(!empty($emp->photo_url))
                                    <img src="{{ $emp->photo_url }}" class="w-9 h-9 rounded-full object-cover shrink-0 ring-1 ring-slate-200 dark:ring-slate-700" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($emp->name) }}&background=6366f1&color=fff';">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-100 dark:border-indigo-900/50 flex items-center justify-center text-xs font-bold text-indigo-600 dark:text-indigo-400 shrink-0">
                                        {{ strtoupper(substr($emp->name, 0, 2)) }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 truncate">{{ $emp->name }}</h3>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 font-mono">NIY: {{ $emp->niy ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="shrink-0">
                                @if($emp->payslip_url)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-100/60 dark:border-emerald-900/40">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Tersedia
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        Belum Ada
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 p-2.5 bg-slate-50 dark:bg-slate-950/50 border border-slate-100 dark:border-slate-800/80 rounded-lg text-xs">
                            <div>
                                <span class="text-slate-400 dark:text-slate-500 block text-[9.5px]">Tipe Pegawai</span>
                                <span class="font-medium text-slate-800 dark:text-slate-200 truncate block mt-0.5 text-[11px]">{{ $emp->employeeType->name ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 dark:text-slate-500 block text-[9.5px]">Jabatan</span>
                                <span class="font-medium text-slate-800 dark:text-slate-200 truncate block mt-0.5 text-[11px]">{{ $emp->position ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="pt-0.5 flex gap-2">
                            @if($emp->payslip_url)
                                <a href="{{ $emp->payslip_url }}" target="_blank" title="{{ $emp->original_filename ?? 'Slip Gaji PDF' }}" class="flex-1 inline-flex items-center justify-center gap-1.5 h-8 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition-colors shadow-xs">
                                    <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                    <span>Slip Gaji</span>
                                </a>
                                @if($emp->attachment_url)
                                    <a href="{{ $emp->attachment_url }}" target="_blank" title="{{ $emp->original_attachment_name ?? 'Lampiran Pendukung' }}" class="inline-flex items-center justify-center gap-1.5 h-8 px-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg transition-colors border border-slate-200 dark:border-slate-700">
                                        <i data-lucide="paperclip" class="w-3.5 h-3.5"></i>
                                        <span>Lampiran</span>
                                    </a>
                                @endif
                            @else
                                <span class="w-full inline-flex items-center justify-center h-8 bg-slate-50 dark:bg-slate-800/60 text-slate-400 dark:text-slate-500 text-xs font-medium rounded-lg border border-slate-200/60 dark:border-slate-800 cursor-not-allowed">
                                    Menunggu HRD
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-8 text-center text-slate-500 dark:text-slate-400">
                        <span class="text-xs font-medium">Tidak ada data pegawai.</span>
                    </div>
                @endforelse
            </div>
        @endif

        @if(!$isSuperAdmin)
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-5 items-start">
                <!-- HERO PAYSLIP CARD FOR REGULAR EMPLOYEE -->
                <div class="{{ $hasNotes ? 'lg:col-span-7 xl:col-span-8' : 'col-span-full' }}">
                    @forelse($employees as $emp)
                        <div class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden text-left">
                            <!-- Top Gradient Stripe -->
                            <div class="h-1.5 w-full {{ $emp->payslip_url ? 'bg-gradient-to-r from-emerald-500 via-teal-500 to-indigo-500' : 'bg-gradient-to-r from-slate-300 via-slate-400 to-slate-300 dark:from-slate-700 dark:to-slate-800' }}"></div>
                            
                            <div class="p-5 sm:p-6 space-y-4">
                                <!-- Employee Info Header -->
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3.5 min-w-0">
                                        @if(!empty($emp->photo_url))
                                            <img src="{{ $emp->photo_url }}" class="w-12 h-12 rounded-full object-cover shrink-0 ring-2 ring-slate-100 dark:ring-slate-800 shadow-xs" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($emp->name) }}&background=6366f1&color=fff';">
                                        @else
                                            <div class="w-12 h-12 rounded-full {{ $emp->payslip_url ? 'bg-indigo-50 dark:bg-indigo-950/70 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900/60' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 border border-slate-200 dark:border-slate-700' }} flex items-center justify-center font-bold text-base shrink-0 shadow-xs">
                                                {{ strtoupper(substr($emp->name, 0, 2)) }}
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <h2 class="text-sm sm:text-base font-bold text-slate-900 dark:text-slate-100 truncate leading-tight">{{ $emp->name }}</h2>
                                            <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-mono">
                                                <span>NIY: {{ $emp->niy ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="shrink-0">
                                        @if($emp->payslip_url)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200/80 dark:border-emerald-500/20 shadow-3xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                Tersedia
                                            </span>
                                       @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                Belum Diterbitkan
                                            </span>
                                       @endif
                                    </div>
                                </div>

                                <!-- Meta Info Grid -->
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 p-3.5 bg-slate-50/80 dark:bg-slate-950/60 border border-slate-100 dark:border-slate-800/80 rounded-lg text-xs">
                                    <div>
                                        <span class="text-slate-400 dark:text-slate-500 block text-[10px] font-bold uppercase tracking-wider">Periode Gaji</span>
                                        <span class="font-bold text-slate-800 dark:text-slate-200 block mt-0.5 font-mono">{{ $currentDate->translatedFormat('F Y') }}</span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 dark:text-slate-500 block text-[10px] font-bold uppercase tracking-wider">Tipe Pegawai</span>
                                        <span class="font-semibold text-slate-800 dark:text-slate-200 truncate block mt-0.5">{{ $emp->employeeType->name ?? '-' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 dark:text-slate-500 block text-[10px] font-bold uppercase tracking-wider">Jabatan Utama</span>
                                        <span class="font-semibold text-slate-800 dark:text-slate-200 truncate block mt-0.5">{{ $emp->position ?? '-' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 dark:text-slate-500 block text-[10px] font-bold uppercase tracking-wider">Jabatan Tambahan</span>
                                        <span class="font-semibold text-slate-800 dark:text-slate-200 truncate block mt-0.5">{{ !empty($emp->additional_position) ? $emp->additional_position : '-' }}</span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="pt-1 flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                                    @if($emp->payslip_url)
                                        <a href="{{ $emp->payslip_url }}" target="_blank" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 h-9 sm:h-10 px-5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-xs font-semibold rounded-lg transition-all shadow-sm hover:shadow-indigo-500/20">
                                            <i data-lucide="file-text" class="w-4 h-4"></i>
                                            <span>Unduh Slip Gaji (PDF)</span>
                                        </a>
                                        @if($emp->attachment_url)
                                            <a href="{{ $emp->attachment_url }}" target="_blank" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 h-9 sm:h-10 px-4 bg-slate-100 hover:bg-slate-200/80 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-lg transition-all border border-slate-200 dark:border-slate-700 shadow-3xs">
                                                <i data-lucide="paperclip" class="w-4 h-4 text-slate-500"></i>
                                                <span>Unduh Lampiran</span>
                                            </a>
                                        @endif
                                    @else
                                        <div class="w-full flex items-center justify-center sm:justify-start gap-2.5 p-3 rounded-lg bg-slate-50 dark:bg-slate-950/40 border border-slate-200/60 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400">
                                            <i data-lucide="clock" class="w-4 h-4 text-slate-400"></i>
                                            <span>Slip gaji untuk periode ini belum diunggah oleh HRD.</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-8 text-center text-slate-500 dark:text-slate-400">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                                    <i data-lucide="inbox" class="w-5 h-5"></i>
                                </div>
                                <span class="text-xs font-medium">Data slip gaji tidak ditemukan.</span>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- CATATAN / KETENTUAN HRD -->
                @if($hasNotes)
                    <div class="lg:col-span-5 xl:col-span-4">
                        <div class="bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200/70 dark:border-amber-900/40 rounded-xl p-4 sm:p-5 text-left shadow-3xs space-y-3 sticky top-4">
                            @if(!empty($periodLines))
                                <!-- Catatan Khusus Periode -->
                                <div class="p-3 bg-white/95 dark:bg-slate-900/90 rounded-lg border border-amber-200/60 dark:border-amber-900/50 text-xs shadow-3xs space-y-1.5">
                                    <div class="font-bold text-amber-800 dark:text-amber-400 flex items-center gap-1.5 text-[11px]">
                                        <i data-lucide="bell-ring" class="w-3.5 h-3.5"></i>
                                        <span>Khusus Periode {{ $currentDate->translatedFormat('F Y') }}:</span>
                                    </div>
                                    <div class="space-y-1.5 pl-1">
                                        @foreach($periodLines as $idx => $line)
                                            @php
                                                $cleanLine = preg_replace('/^(\d+[\.\)]\s*|[\-\*\•]\s*)/u', '', $line);
                                            @endphp
                                            <div class="flex items-start gap-2 text-slate-700 dark:text-slate-300 leading-relaxed text-xs">
                                                @if(count($periodLines) > 1)
                                                    <span class="w-4 h-4 rounded-full bg-amber-100 dark:bg-amber-900/60 text-amber-700 dark:text-amber-300 flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">
                                                        {{ $idx + 1 }}
                                                    </span>
                                                @endif
                                                <span>{{ $cleanLine }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if(!empty($globalLines))
                                <!-- Catatan Umum / SOP -->
                                <div class="text-xs space-y-1.5 {{ !empty($periodLines) ? 'pt-1' : '' }}">
                                    <div class="font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5 text-[11px]">
                                        <i data-lucide="info" class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400"></i>
                                        <span>Ketentuan Umum HRD:</span>
                                    </div>
                                    <div class="space-y-1.5 pl-1">
                                        @foreach($globalLines as $idx => $line)
                                            @php
                                                $cleanLine = preg_replace('/^(\d+[\.\)]\s*|[\-\*\•]\s*)/u', '', $line);
                                            @endphp
                                            <div class="flex items-start gap-2 text-slate-600 dark:text-slate-400 leading-relaxed text-xs">
                                                @if(count($globalLines) > 1)
                                                    <span class="w-4 h-4 rounded-full bg-slate-200/80 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">
                                                        {{ $idx + 1 }}
                                                    </span>
                                                @endif
                                                <span>{{ $cleanLine }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-admin-layout>
