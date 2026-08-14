@php
    $isSuperAdmin = auth()->user() && auth()->user()->hasRole('super_admin');
@endphp
<x-admin-layout>
    <style>
        .dark input[type="month"] {
            color-scheme: dark;
        }
    </style>
    <div class="p-6 space-y-6">
        <!-- HEADER -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Slip Gaji Pegawai</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Unduh slip gaji bulanan dari HRD pusat.</p>
            </div>
            <div class="flex items-center gap-2"></div>
        </section>


    <!-- Filters -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-sm mb-6 flex flex-col md:flex-row md:items-end gap-4">
        @if($isSuperAdmin)
        <div class="w-full md:w-64">
            <label for="searchInput" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1 uppercase tracking-wide">Cari Pegawai</label>
            <input type="text" id="searchInput" placeholder="Ketik nama..." class="w-full text-xs rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:ring-blue-500 focus:border-blue-500">
        </div>
        @endif
        <form method="GET" action="{{ route('payslips.index') }}" class="flex flex-col sm:flex-row gap-4 flex-1">
            <div class="w-full sm:w-64">
                <input type="month" id="month" name="month" value="{{ $month }}" class="w-full text-xs rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
            </div>
        </form>
    </div>

    <!-- TABLE LIST -->
    <div class="{{ !$isSuperAdmin ? 'hidden sm:block' : '' }} bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden text-left w-full flex flex-col justify-between">
        <!-- <div class="p-5 border-b border-slate-100 dark:border-slate-900 flex justify-between items-center flex-wrap gap-2 bg-white dark:bg-slate-900">
            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                Daftar Slip Gaji
            </h3>
        </div> -->
        <div class="overflow-x-auto overflow-y-auto custom-scrollbar" style="max-height: calc(100vh - 240px);">
            <table class="w-full text-xs border-collapse">
                <thead class="z-10">
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                        <th class="sticky top-0 z-10 bg-slate-50 dark:bg-slate-900 px-6 py-3 text-left w-64 min-w-[200px]">Nama Pegawai</th>
                        <th class="sticky top-0 z-10 bg-slate-50 dark:bg-slate-900 px-6 py-3 text-left w-48 min-w-[150px]">Tipe Pegawai</th>
                        <th class="sticky top-0 z-10 bg-slate-50 dark:bg-slate-900 px-6 py-3 text-center w-40">Periode</th>
                        <th class="sticky top-0 z-10 bg-slate-50 dark:bg-slate-900 px-6 py-3 text-center w-32">Status Slip</th>
                        <th class="sticky top-0 z-10 bg-slate-50 dark:bg-slate-900 px-6 py-3 text-right w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-900 text-slate-700 dark:text-slate-300 font-medium">
                    @forelse($employees as $emp)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/20 transition-colors">
                            <td class="px-6 py-3 text-left">
                                <div class="flex flex-col min-w-0">
                                    <span class="text-slate-900 dark:text-slate-200 font-bold tracking-tight truncate">{{ $emp->name }}</span>
                                    <span class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold truncate">{{ $emp->nik ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3 text-left text-slate-600 dark:text-slate-300">
                                {{ $emp->employeeType->name ?? '-' }}
                            </td>
                            <td class="px-6 py-3 text-center text-slate-600 dark:text-slate-300">
                                {{ \Carbon\Carbon::parse($month . '-01')->translatedFormat('F Y') }}
                            </td>
                            <td class="px-6 py-3 text-center">
                                @if($emp->payslip_url)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-700/30 uppercase">
                                        Tersedia
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-700/30 uppercase">
                                        Belum Ada
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-right">
                                @if($emp->payslip_url)
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ $emp->payslip_url }}" target="_blank" class="inline-flex items-center justify-center gap-1.5 h-8 px-3 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-bold rounded-lg transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            Download Slip
                                        </a>
                                        @if($emp->attachment_url)
                                            <a href="{{ $emp->attachment_url }}" target="_blank" class="inline-flex items-center justify-center gap-1.5 h-8 px-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-[10px] font-bold rounded-lg transition-colors border border-slate-200 dark:border-slate-700">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                Lampiran
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <button disabled class="inline-flex items-center justify-center h-8 px-3 bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 text-[10px] font-bold rounded-lg cursor-not-allowed">
                                        Menunggu HRD
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">
                                Tidak ada data pegawai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(!$isSuperAdmin)
        <!-- Mobile View for Regular Employee -->
        <div class="block sm:hidden space-y-4">
            @forelse($employees as $emp)
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-sm space-y-4 text-left">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-slate-50">{{ $emp->name }}</h3>
                            <p class="text-xs text-slate-500 mt-1">NIK: {{ $emp->nik ?? '-' }}</p>
                        </div>
                        <div class="shrink-0 ml-3">
                            @if($emp->payslip_url)
                                <span class="inline-flex items-center justify-center text-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200/20">
                                    Tersedia
                                </span>
                            @else
                                <span class="inline-flex items-center justify-center text-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 border border-rose-200/20">
                                    Belum Ada
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="border-t border-slate-100 dark:border-slate-800/60 pt-3.5 space-y-2">
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-400 dark:text-slate-500">Tipe Pegawai:</span>
                            <span class="font-medium text-slate-800 dark:text-slate-200">{{ $emp->employeeType->name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-400 dark:text-slate-500">Jabatan Utama:</span>
                            <span class="font-medium text-slate-800 dark:text-slate-200">{{ $emp->position ?? '-' }}</span>
                        </div>
                        @if(!empty($emp->additional_position))
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-400 dark:text-slate-500">Jabatan Tambahan:</span>
                                <span class="font-medium text-slate-800 dark:text-slate-200">{{ $emp->additional_position }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-400 dark:text-slate-500">Periode:</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ \Carbon\Carbon::parse($month . '-01')->translatedFormat('F Y') }}</span>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800/60 flex flex-col gap-2">
                        @if($emp->payslip_url)
                            <a href="{{ $emp->payslip_url }}" target="_blank" class="w-full inline-flex items-center justify-center h-10 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-700 transition-colors shadow-sm gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Download Slip Gaji (PDF)
                            </a>
                            @if($emp->attachment_url)
                                <a href="{{ $emp->attachment_url }}" target="_blank" class="w-full inline-flex items-center justify-center h-10 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-750 dark:text-slate-300 text-xs font-semibold rounded-lg transition-colors gap-2 border border-slate-200 dark:border-slate-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                    Download Lampiran
                                </a>
                            @endif
                        @else
                            <button disabled class="w-full inline-flex items-center justify-center h-10 bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 text-xs font-semibold rounded-lg cursor-not-allowed">
                                Menunggu Pengiriman dari HRD
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 text-center text-slate-500 dark:text-slate-400">
                    Tidak ada data pegawai.
                </div>
            @endforelse
        </div>
    @endif
</div>

<script>
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
</script>
</x-admin-layout>