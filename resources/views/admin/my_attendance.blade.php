<x-admin-layout>
    <div class="p-6 space-y-6">

        <!-- HEADER -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Riwayat Absensi Saya</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Pantau seluruh catatan kehadiran, keterlambatan, dan status izin/sakit Anda.</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-semibold px-3 py-1.5 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300">
                <i data-lucide="user" class="w-3.5 h-3.5"></i>
                <span>{{ auth()->user()->name }}</span>
            </div>
        </section>

        <!-- SUMMARY STATS -->
        @php
            $hadir = $attendances->where('status', 'Present')->count();
            $izin = $attendances->where('status', 'Permit')->count();
            $sakit = $attendances->where('status', 'Sick')->count();
            $alpa = $attendances->where('status', 'Absent')->count();
        @endphp
        <section class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Hadir Card -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-sm text-left flex items-center justify-between">
                <div>
                    <span class="text-xs text-slate-500 dark:text-slate-400 block font-medium">Hadir</span>
                    <span class="text-2xl font-bold text-slate-900 dark:text-slate-50 mt-1 block">{{ $hadir }}</span>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                </div>
            </div>

            <!-- Izin Card -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-sm text-left flex items-center justify-between">
                <div>
                    <span class="text-xs text-slate-500 dark:text-slate-400 block font-medium">Izin</span>
                    <span class="text-2xl font-bold text-slate-900 dark:text-slate-50 mt-1 block">{{ $izin }}</span>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                    <i data-lucide="file-text" class="w-5 h-5"></i>
                </div>
            </div>

            <!-- Sakit Card -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-sm text-left flex items-center justify-between">
                <div>
                    <span class="text-xs text-slate-500 dark:text-slate-400 block font-medium">Sakit</span>
                    <span class="text-2xl font-bold text-slate-900 dark:text-slate-50 mt-1 block">{{ $sakit }}</span>
                </div>
                <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <i data-lucide="activity" class="w-5 h-5"></i>
                </div>
            </div>

            <!-- Alpa Card -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-sm text-left flex items-center justify-between">
                <div>
                    <span class="text-xs text-slate-500 dark:text-slate-400 block font-medium">Alpa</span>
                    <span class="text-2xl font-bold text-slate-900 dark:text-slate-50 mt-1 block">{{ $alpa }}</span>
                </div>
                <div class="w-10 h-10 rounded-lg bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                    <i data-lucide="x-circle" class="w-5 h-5"></i>
                </div>
            </div>
        </section>

        <!-- FILTERS -->
        <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-sm w-full">
            <form method="GET" action="{{ route('my-attendance') }}" class="flex flex-col md:flex-row gap-4 items-stretch md:items-center justify-between">
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Mulai Tanggal</label>
                        <input type="date" name="start_date" value="{{ request('start_date', \Carbon\Carbon::now()->startOfMonth()->toDateString()) }}"
                            class="h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ request('end_date', \Carbon\Carbon::now()->endOfMonth()->toDateString()) }}"
                            class="h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 cursor-pointer">
                    </div>
                    <div class="pt-5">
                        <button type="submit" class="h-9 px-4 bg-slate-900 dark:bg-slate-50 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-all duration-100 cursor-pointer flex items-center justify-center gap-2">
                            <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                            Filter
                        </button>
                    </div>
                </div>
                @if(request()->anyFilled(['start_date', 'end_date']))
                    <div class="pt-5 md:pt-0">
                        <a href="{{ route('my-attendance') }}" class="h-9 px-3 flex items-center justify-center bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-300 rounded-lg transition-colors">
                            Reset Filter
                        </a>
                    </div>
                @endif
            </form>
        </section>

        <!-- TABLE SECTION -->
        <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden w-full">
            <div class="overflow-x-auto">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-16">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-36">Hari & Tanggal</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-32">Jam Masuk</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-32">Jam Pulang</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-32">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse($attendances as $index => $attendance)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/10 transition-colors">
                                <td class="px-6 py-4 text-slate-500 dark:text-slate-400 font-mono">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 text-slate-900 dark:text-slate-100 font-medium">
                                    {{ \Carbon\Carbon::parse($attendance->date)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                                </td>
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-300 font-mono">
                                    {{ $attendance->clock_in ?? '-- : --' }}
                                </td>
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-300 font-mono">
                                    {{ $attendance->clock_out ?? '-- : --' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($attendance->status == 'Present')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200/40 dark:border-emerald-800/30 uppercase">Hadir</span>
                                    @elseif($attendance->status == 'Permit')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 border border-blue-200/40 dark:border-blue-800/30 uppercase">Izin</span>
                                    @elseif($attendance->status == 'Sick')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border border-amber-200/40 dark:border-amber-800/30 uppercase">Sakit</span>
                                    @elseif($attendance->status == 'Absent')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-rose-50 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 border border-rose-200/40 dark:border-rose-800/30 uppercase">Alpa</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-500 dark:text-slate-400 text-xs italic">
                                    {{ $attendance->notes ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <i data-lucide="calendar-x" class="w-8 h-8 text-slate-300 dark:text-slate-700"></i>
                                        <p class="text-xs">Tidak ada riwayat kehadiran pada rentang tanggal ini.</p>
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
