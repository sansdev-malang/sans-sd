<x-admin-layout>
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <!-- Page Header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-slate-800 dark:text-slate-100 font-bold">Slip Gaji Pegawai ✨</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Unduh slip gaji bulanan dari HRD pusat.</p>
        </div>
    </div>

    <!-- Filters -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-sm mb-6 flex flex-col md:flex-row md:items-end gap-4">
        @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
        <div class="w-full md:w-64">
            <label for="searchInput" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1 uppercase tracking-wide">Cari Pegawai</label>
            <input type="text" id="searchInput" placeholder="Ketik nama..." class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:ring-blue-500 focus:border-blue-500">
        </div>
        @endif
        <form method="GET" action="{{ route('payslips.index') }}" class="flex flex-col sm:flex-row gap-4 flex-1">
            <div class="w-full sm:w-64">
                <label for="month" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1 uppercase tracking-wide">Periode Bulan</label>
                <input type="month" id="month" name="month" value="{{ $month }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
            </div>
        </form>
    </div>

    <!-- Table List -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden w-full text-left">
        <div class="overflow-x-auto" style="max-height: calc(100vh - 280px); overflow-y: auto;">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-900 uppercase font-semibold border-b border-slate-200 dark:border-slate-800 sticky top-0 z-40">
                    <tr>
                        <th class="px-6 py-4">Nama Pegawai</th>
                        <th class="px-6 py-4">Tipe Pegawai</th>
                        <th class="px-6 py-4">Periode</th>
                        <th class="px-6 py-4 text-center">Status Slip</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($employees as $emp)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-900/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-800 dark:text-slate-200">{{ $emp->name }}</div>
                                <div class="text-xs text-slate-500 mt-1">{{ $emp->nik ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300">
                                {{ $emp->employeeType->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300">
                                {{ \Carbon\Carbon::parse($month . '-01')->translatedFormat('F Y') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($emp->payslip_url)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Tersedia
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        Belum Ada
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($emp->payslip_url)
                                    <a href="{{ $emp->payslip_url }}" target="_blank" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        Download PDF
                                    </a>
                                @else
                                    <button disabled class="inline-flex items-center justify-center px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 text-sm font-medium rounded-lg cursor-not-allowed">
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