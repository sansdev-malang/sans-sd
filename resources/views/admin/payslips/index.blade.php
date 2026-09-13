@php
    $isSuperAdmin = auth()->user() && auth()->user()->hasRole('super_admin');
    $currentDate = \Carbon\Carbon::parse($month . '-01');
    $prevMonth = $currentDate->copy()->subMonth()->format('Y-m');
    $nextMonth = $currentDate->copy()->addMonth()->format('Y-m');
@endphp
<x-admin-layout>
    <style>
        input[type="month"]::-webkit-calendar-picker-indicator {
            filter: brightness(0) !important;
            opacity: 0.6 !important;
            cursor: pointer;
        }

        .dark input[type="month"] {
            color-scheme: dark;
        }

        .dark input[type="month"]::-webkit-calendar-picker-indicator {
            filter: brightness(0) invert(1) !important;
            opacity: 0.7 !important;
        }
    </style>

    <div class="p-4 sm:p-6 space-y-6 w-full">
        <!-- HEADER -->
        <section class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-100 dark:border-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0 shadow-xs">
                    <i data-lucide="receipt" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">Slip Gaji</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Unduh slip gaji dan lampiran bulanan dari HRD.</p>
                </div>
            </div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs text-slate-600 dark:text-slate-400">
                <i data-lucide="calendar" class="w-3.5 h-3.5 text-indigo-500"></i>
                <span>Periode: <strong class="text-slate-900 dark:text-slate-100">{{ $currentDate->translatedFormat('F Y') }}</strong></span>
            </div>
        </section>

        <!-- FILTERS & NAVIGATION -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-3.5 sm:p-4 shadow-xs">
            <form method="GET" action="{{ route('payslips.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-2.5 flex-1">
                    @if($isSuperAdmin)
                    <div class="w-full sm:w-64 shrink-0">
                        <div class="relative flex items-center">
                            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 pointer-events-none"></i>
                            <input type="text" id="searchInput" placeholder="Cari nama pegawai..." class="w-full h-9 pl-9 pr-3 text-xs bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:border-indigo-500 transition-colors">
                        </div>
                    </div>
                    @endif

                    <!-- Quick Month Navigator -->
                    <div class="inline-flex items-center gap-1 bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 p-1 rounded-xl shadow-xs">
                        <a href="{{ route('payslips.index', ['month' => $prevMonth]) }}" title="Bulan Sebelumnya ({{ \Carbon\Carbon::parse($prevMonth . '-01')->translatedFormat('M Y') }})" class="h-7 w-7 inline-flex items-center justify-center rounded-lg text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-white dark:hover:bg-slate-800 transition-colors">
                            <i data-lucide="chevron-left" class="w-4 h-4"></i>
                        </a>
                        <input type="month" id="month" name="month" value="{{ $month }}" class="h-7 px-2 text-xs font-semibold bg-transparent text-slate-800 dark:text-slate-200 border-0 focus:ring-0 cursor-pointer text-center" onchange="this.form.submit()">
                        <a href="{{ route('payslips.index', ['month' => $nextMonth]) }}" title="Bulan Selanjutnya ({{ \Carbon\Carbon::parse($nextMonth . '-01')->translatedFormat('M Y') }})" class="h-7 w-7 inline-flex items-center justify-center rounded-lg text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-white dark:hover:bg-slate-800 transition-colors">
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>

                <div class="text-xs text-slate-500 dark:text-slate-400 self-end sm:self-center">
                    Total: <span class="font-bold text-slate-800 dark:text-slate-200">{{ $employees->count() }} Pegawai</span>
                </div>
            </form>
        </div>

        <!-- TABLE LIST (Desktop & Super Admin) -->
        <div class="{{ !$isSuperAdmin ? 'hidden md:block' : '' }} bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto" style="max-height: calc(100vh - 280px); overflow-y: auto;">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-950/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[11px] sticky top-0 z-10">
                            <th class="px-5 py-3.5 text-left min-w-[200px]">Nama Pegawai</th>
                            <th class="px-5 py-3.5 text-left min-w-[150px]">Tipe Pegawai</th>
                            <th class="px-5 py-3.5 text-center w-36">Periode</th>
                            <th class="px-5 py-3.5 text-center w-32">Status Slip</th>
                            <th class="px-5 py-3.5 text-right w-48">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300 font-medium">
                        @forelse($employees as $emp)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-5 py-3.5 text-left">
                                    <div class="flex flex-col min-w-0">
                                        <span class="text-slate-900 dark:text-slate-100 font-semibold truncate">{{ $emp->name }}</span>
                                        <span class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">{{ $emp->nik ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-left text-slate-600 dark:text-slate-300">
                                    {{ $emp->employeeType->name ?? '-' }}
                                </td>
                                <td class="px-5 py-3.5 text-center text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                    {{ $currentDate->translatedFormat('F Y') }}
                                </td>
                                <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                    @if($emp->payslip_url)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Tersedia
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            Belum Ada
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                    @if($emp->payslip_url)
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ $emp->payslip_url }}" target="_blank" class="inline-flex items-center justify-center gap-1.5 h-8 px-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition-colors shadow-xs">
                                                <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                                Slip Gaji
                                            </a>
                                            @if($emp->attachment_url)
                                                <a href="{{ $emp->attachment_url }}" target="_blank" class="inline-flex items-center justify-center gap-1.5 h-8 px-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl transition-colors border border-slate-200 dark:border-slate-700">
                                                    <i data-lucide="paperclip" class="w-3.5 h-3.5"></i>
                                                    Lampiran
                                                </a>
                                            @endif
                                        </div>
                                    @else
                                        <button disabled class="inline-flex items-center justify-center h-8 px-3 bg-slate-100 dark:bg-slate-800/80 text-slate-400 dark:text-slate-500 text-xs font-medium rounded-xl cursor-not-allowed border border-slate-200/50 dark:border-slate-800">
                                            Menunggu HRD
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                                            <i data-lucide="inbox" class="w-6 h-6"></i>
                                        </div>
                                        <span class="text-sm font-medium">Tidak ada data slip gaji untuk periode ini.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(!$isSuperAdmin)
        <!-- MOBILE / CARD VIEW (Regular Employee) -->
        <div class="block md:hidden space-y-4">
            @forelse($employees as $emp)
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-xs space-y-4 text-left">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 truncate">{{ $emp->name }}</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-mono mt-0.5">NIK: {{ $emp->nik ?? '-' }}</p>
                        </div>
                        <div class="shrink-0">
                            @if($emp->payslip_url)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Tersedia
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                    Belum Ada
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 p-3 bg-slate-50 dark:bg-slate-950/50 border border-slate-100 dark:border-slate-800/80 rounded-xl text-xs">
                        <div>
                            <span class="text-slate-400 dark:text-slate-500 block text-[11px]">Tipe Pegawai</span>
                            <span class="font-medium text-slate-800 dark:text-slate-200 truncate block mt-0.5">{{ $emp->employeeType->name ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 dark:text-slate-500 block text-[11px]">Periode</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200 truncate block mt-0.5">{{ $currentDate->translatedFormat('F Y') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 dark:text-slate-500 block text-[11px]">Jabatan Utama</span>
                            <span class="font-medium text-slate-800 dark:text-slate-200 truncate block mt-0.5">{{ $emp->position ?? '-' }}</span>
                        </div>
                        @if(!empty($emp->additional_position))
                            <div>
                                <span class="text-slate-400 dark:text-slate-500 block text-[11px]">Jabatan Tambahan</span>
                                <span class="font-medium text-slate-800 dark:text-slate-200 truncate block mt-0.5">{{ $emp->additional_position }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="pt-2 flex flex-col gap-2">
                        @if($emp->payslip_url)
                            <a href="{{ $emp->payslip_url }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 h-10 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition-colors shadow-xs">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                                Slip Gaji
                            </a>
                            @if($emp->attachment_url)
                                <a href="{{ $emp->attachment_url }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 h-10 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl transition-colors border border-slate-200 dark:border-slate-700">
                                    <i data-lucide="paperclip" class="w-4 h-4"></i>
                                    Lampiran
                                </a>
                            @endif
                        @else
                            <button disabled class="w-full inline-flex items-center justify-center h-10 bg-slate-100 dark:bg-slate-800/80 text-slate-400 dark:text-slate-500 text-xs font-medium rounded-xl cursor-not-allowed border border-slate-200/50 dark:border-slate-800">
                                Menunggu HRD
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-8 text-center text-slate-500 dark:text-slate-400">
                    <div class="flex flex-col items-center justify-center gap-2">
                        <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                            <i data-lucide="inbox" class="w-6 h-6"></i>
                        </div>
                        <span class="text-sm font-medium">Tidak ada data slip gaji untuk periode ini.</span>
                    </div>
                </div>
            @endforelse
        </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    let filter = this.value.toLowerCase();
                    let rows = document.querySelectorAll('tbody tr');
                    
                    rows.forEach(row => {
                        if (row.children.length > 1) { // Skip empty state row
                            let name = row.children[0].innerText.toLowerCase();
                            if (name.includes(filter)) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        }
                    });
                });
            }
        });
    </script>
</x-admin-layout>
