<x-admin-layout>
    <div class="p-6 space-y-6">

        <!-- HEADER -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Laporan Absensi Terpadu</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Monitoring absensi dinamis terintegrasi. Khusus unit SD dan SMP, data ditarik secara bersamaan dari database terpadu.</p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <button onclick="window.print()" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-900 dark:bg-slate-50 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-all duration-150 cursor-pointer">
                    <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                    Cetak Laporan
                </button>
            </div>
        </section>


        <!-- FILTERS -->
        <section class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-sm w-full">
            <form method="GET" action="{{ route('attendances.recap') }}" class="flex flex-col sm:flex-row gap-4 items-stretch sm:items-center justify-between">
                <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center flex-1">
                    <!-- Date Filter -->
                    <div class="flex flex-col gap-1 text-left">
                        <label class="text-[10px] font-bold text-slate-400 dark:text-slate-550 uppercase">Tanggal</label>
                        <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                            class="h-9 px-3 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none cursor-pointer">
                    </div>

                    <!-- Unit Filter -->
                    <div class="flex flex-col gap-1 text-left">
                        <label class="text-[10px] font-bold text-slate-400 dark:text-slate-550 uppercase">Unit Sekolah</label>
                        <select name="unit" onchange="this.form.submit()"
                            class="h-9 px-3 w-48 text-xs font-semibold bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none cursor-pointer">
                            <option value="">Semua Unit</option>
                            <option value="paud" {{ $unit === 'paud' ? 'selected' : '' }}>PAUD & TK</option>
                            <option value="sd" {{ $unit === 'sd' ? 'selected' : '' }}>Sekolah Dasar (SD)</option>
                            <option value="smp" {{ $unit === 'smp' ? 'selected' : '' }}>SMP</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-end">
                    @if(request()->anyFilled(['date', 'unit']))
                        <a href="{{ route('attendances.recap') }}" class="h-9 px-4 flex items-center justify-center bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-300 rounded-lg transition-colors">
                            Reset Filter
                        </a>
                    @endif
                </div>
            </form>
        </section>

        <!-- RECAP TABLE -->
        <section class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden w-full">
            <div class="overflow-x-auto">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-16">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider">Nama Pegawai</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-32">Unit</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-32">Tipe</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-24">Jam Masuk</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-24">Jam Pulang</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-24">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-900">
                        @forelse($attendances as $index => $attendance)
                            <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-900/20 transition-colors">
                                <td class="px-6 py-4 text-slate-900 dark:text-slate-50 font-medium">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-900 flex items-center justify-center text-xs font-bold text-slate-700 dark:text-slate-355 shrink-0">
                                            {{ strtoupper(substr($attendance->employee->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <span class="text-slate-900 dark:text-slate-50 font-bold tracking-tight block">{{ $attendance->employee->name }}</span>
                                            <span class="text-[10px] text-slate-500 dark:text-slate-400 block">{{ $attendance->employee->subject_position }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($attendance->employee->unit == 'paud')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-teal-50 dark:bg-teal-950/30 text-teal-700 dark:text-teal-400 border border-teal-200/50 dark:border-teal-800/40 uppercase">PAUD & TK</span>
                                    @elseif($attendance->employee->unit == 'sd')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-400 border border-blue-200/50 dark:border-blue-800/40 uppercase">SD</span>
                                    @elseif($attendance->employee->unit == 'smp')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 dark:bg-purple-950/30 text-purple-700 dark:text-purple-400 border border-purple-200/50 dark:border-purple-800/40 uppercase">SMP</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($attendance->employee->type == 'teacher')
                                        <span class="text-slate-700 dark:text-slate-300 font-medium">Guru</span>
                                    @else
                                        <span class="text-slate-500 dark:text-slate-400">Karyawan / Staf</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-900 dark:text-slate-100 font-mono font-medium">{{ $attendance->clock_in ?? '-- : --' }}</td>
                                <td class="px-6 py-4 text-slate-900 dark:text-slate-100 font-mono font-medium">{{ $attendance->clock_out ?? '-- : --' }}</td>
                                <td class="px-6 py-4">
                                    @if($attendance->status == 'Present')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-800/40">Hadir</span>
                                    @elseif($attendance->status == 'Sick')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-red-50 dark:bg-red-955/20 text-red-700 dark:text-red-400 border border-red-200/50 dark:border-red-800/40">Sakit</span>
                                    @elseif($attendance->status == 'Permit')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 dark:bg-amber-955/20 text-amber-700 dark:text-amber-400 border border-amber-200/50 dark:border-amber-800/40">Izin</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-150 dark:bg-slate-900 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800">Alfa</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-500 dark:text-slate-400 italic">{{ $attendance->notes ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-10 text-center text-slate-500 dark:text-slate-400">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <i data-lucide="calendar" class="w-8 h-8 text-slate-300 dark:text-slate-700"></i>
                                        <p class="font-medium text-xs">Tidak ada data kehadiran yang tercatat untuk tanggal dan filter ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </div>
</x-admin-layout>
