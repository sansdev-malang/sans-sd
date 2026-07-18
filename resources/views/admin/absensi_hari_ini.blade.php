<x-admin-layout>
    <div class="p-6 space-y-6">

        <!-- GREETING / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Absensi Hari Ini</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Pantau data kehadiran guru dan karyawan secara real-time untuk hari ini.</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg shadow-sm">
                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </span>
                </div>
            </div>
        </section>

        <!-- STATS CARDS -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 w-full">
            <!-- Hadir -->
            <div class="bg-white dark:bg-slate-950 p-4 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-lg">
                    <i data-lucide="user-check" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Hadir</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-slate-50">0</p>
                </div>
            </div>
            <!-- Terlambat -->
            <div class="bg-white dark:bg-slate-950 p-4 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-lg">
                    <i data-lucide="clock-alert" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Terlambat</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-slate-50">0</p>
                </div>
            </div>
            <!-- Izin/Cuti -->
            <div class="bg-white dark:bg-slate-950 p-4 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg">
                    <i data-lucide="calendar-clock" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Izin / Cuti</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-slate-50">0</p>
                </div>
            </div>
            <!-- Alpha -->
            <div class="bg-white dark:bg-slate-950 p-4 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-lg">
                    <i data-lucide="user-x" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Alpha</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-slate-50">0</p>
                </div>
            </div>
        </div>

        <!-- MAIN TABLE CARD -->
        <section class="animate-card bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden transition-all w-full p-6 space-y-6">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
                <div class="space-y-1">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-slate-50">Data Log Absensi</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Daftar kehadiran pegawai pada hari ini.</p>
                </div>
                <div class="flex items-center gap-2.5 shrink-0">
                    <button class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-900 dark:bg-slate-50 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-all duration-150 cursor-pointer">
                        <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                        Sinkronisasi ZKTeco
                    </button>
                </div>
            </div>

            <!-- Search & Filters Container -->
            <div class="flex flex-col md:flex-row gap-4 items-end justify-between w-full">
                <!-- Search Box -->
                <div class="space-y-1.5 w-full md:flex-1 md:max-w-md">
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Cari Pegawai</label>
                    <div class="relative w-full">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i data-lucide="search" class="w-4 h-4 text-slate-400 dark:text-slate-550"></i>
                        </span>
                        <input type="text" id="table-search" placeholder="Nama..."
                            style="padding-left: 2.25rem;"
                            class="w-full h-9 pr-4 text-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 focus:border-slate-400 dark:focus:border-slate-600 text-slate-900 dark:text-slate-50 placeholder-slate-400 dark:placeholder-slate-500 transition-all shadow-inner">
                    </div>
                </div>

                <!-- Filter Status -->
                <div class="flex flex-row items-end gap-2.5 w-full md:w-auto">
                    <div class="space-y-1.5 flex-1 sm:flex-initial">
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Status</label>
                        <select id="filter-status"
                            class="h-9 px-3 w-full sm:w-40 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 cursor-pointer transition-all shadow-sm">
                            <option value="">Semua Status</option>
                            <option value="Hadir">Hadir</option>
                            <option value="Terlambat">Terlambat</option>
                            <option value="Izin">Izin / Cuti</option>
                            <option value="Alpha">Alpha</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Table Container -->
            <div class="overflow-x-auto border border-slate-100 dark:border-slate-900 rounded-xl">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30">
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-16">No</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider">Nama Pegawai</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-36">Jam Masuk</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-36">Jam Pulang</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-32">Status</th>
                            <th class="px-6 py-4 text-center font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60" id="absensi-table-body">
                        <!-- Empty State -->
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i data-lucide="calendar-x-2" class="w-8 h-8 text-slate-300 dark:text-slate-600 mb-2"></i>
                                    <p class="font-medium">Belum ada data absensi hari ini</p>
                                    <p class="text-xs opacity-75">Silakan lakukan sinkronisasi mesin ZKTeco atau tunggu pegawai absen.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </section>
    </div>
</x-admin-layout>
