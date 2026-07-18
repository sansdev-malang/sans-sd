<x-admin-layout>
    <div class="p-6 space-y-6">

        <!-- GREETING / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Daftar Guru</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Kelola dan pantau data akademis guru SANS Malang.</p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <button class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-855 text-slate-700 dark:text-slate-355 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm transition-all duration-150 cursor-pointer">
                    <i data-lucide="download" class="w-3.5 h-3.5 text-slate-500"></i>
                    Unduh Laporan
                </button>
                <button class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-900 dark:bg-slate-50 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-all duration-150 cursor-pointer">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    Tambah Guru
                </button>
            </div>
        </section>

        <!-- SECTION 2: STATS CARDS GRID -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Stat Card 1 -->
            <div
                class="animate-card bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total
                            Guru Aktif</p>
                        <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                            <span class="stat-counter" data-target="86">0</span>
                        </h3>
                    </div>
                    <div class="p-2 bg-slate-50 dark:bg-slate-900 rounded-lg">
                        <i data-lucide="users" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                    </div>
                </div>
                <div class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">+2.4%</span> dari bulan lalu
                </div>
            </div>

            <!-- Stat Card 2 -->
            <div
                class="animate-card bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Guru Laki-laki</p>
                        <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                            <span class="stat-counter" data-target="38">0</span>
                        </h3>
                    </div>
                    <div class="p-2 bg-slate-50 dark:bg-slate-900 rounded-lg">
                        <i data-lucide="graduation-cap" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                    </div>
                </div>
                <div class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">98.2%</span> tingkat kehadiran
                </div>
            </div>

            <!-- Stat Card 3 -->
            <div
                class="animate-card bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Guru Perempuan</p>
                        <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                            <span class="stat-counter" data-target="48">0</span>
                        </h3>
                    </div>
                    <div class="p-2 bg-slate-50 dark:bg-slate-900 rounded-lg">
                        <i data-lucide="layout-grid" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                    </div>
                </div>
                <div class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                    Semua kelas terisi hari ini
                </div>
            </div>

            <!-- Stat Card 4 -->
            <div
                class="animate-card bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Telah Sertifikasi</p>
                        <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                            <span class="stat-counter" data-target="71">0</span>%
                        </h3>
                    </div>
                    <div class="p-2 bg-slate-50 dark:bg-slate-900 rounded-lg">
                        <i data-lucide="clock" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                    </div>
                </div>
                <div class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">+1.2%</span> dari kemarin
                </div>
            </div>
        </section>

        <!-- SECTION 3: SEARCH & FILTERS -->
        <section
            class="animate-card bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-sm w-full">
            <div class="flex flex-col md:flex-row gap-4 items-stretch md:items-center justify-between">
                <!-- Search Box -->
                <div class="relative w-full md:max-w-md">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 dark:text-slate-500"></i>
                    </span>
                    <input type="text" id="table-search" placeholder="Cari berdasarkan Nama, NIS, atau NISN..."
                        style="padding-left: 2.25rem;"
                        class="w-full h-9 pr-4 text-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 focus:border-slate-400 dark:focus:border-slate-600 text-slate-900 dark:text-slate-50 placeholder-slate-400 dark:placeholder-slate-500 transition-all shadow-inner">
                </div>

                <!-- Filter Select Toolbar -->
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <!-- Filter Kelas -->
                    <select id="filter-kelas"
                        class="h-9 px-2 flex-1 sm:flex-initial sm:w-32 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 cursor-pointer transition-all shadow-sm">
                        <option value="">Semua Kelas</option>
                        <option value="10">Kelas 10</option>
                        <option value="11">Kelas 11</option>
                        <option value="12">Kelas 12</option>
                    </select>

                    <!-- Filter Rombel -->
                    <select id="filter-rombel"
                        class="h-9 px-2 flex-1 sm:flex-initial sm:w-32 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 cursor-pointer transition-all shadow-sm">
                        <option value="">Semua Rombel</option>
                        <option value="X-A">X-A</option>
                        <option value="XI-IPA">XI-IPA</option>
                        <option value="XII-IPS">XII-IPS</option>
                    </select>

                    <!-- Filter Status -->
                    <select id="filter-status"
                        class="h-9 px-2 flex-1 sm:flex-initial sm:w-32 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 cursor-pointer transition-all shadow-sm">
                        <option value="">Semua Status</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Cuti">Cuti</option>
                        <option value="Alumni">Alumni</option>
                    </select>
                </div>
            </div>
        </section>

        <!-- SECTION 4: TABLE (PREMIUM DESIGN) -->
        <section class="animate-card bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden transition-all w-full">
            <div class="overflow-x-auto">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-16">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-48">NUPTK</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider">Nama Guru</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-40">Mata Pelajaran</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-36">Jenis Kelamin</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-40">Kepegawaian</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-28">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-900">
                        <!-- Row 1 -->
                        <tr class="teacher-row hover:bg-slate-50/40 dark:hover:bg-slate-900/20 transition-colors">
                            <td class="px-6 py-4 text-slate-900 dark:text-slate-50 font-medium">1</td>
                            <td class="teacher-nip px-6 py-4 text-slate-500 dark:text-slate-400 font-mono text-xs">197508122003121002</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-xs font-bold text-slate-700 dark:text-slate-350 shrink-0">
                                        EW
                                    </div>
                                    <span class="teacher-name text-slate-900 dark:text-slate-50 font-semibold tracking-tight">Drs. Eko Wibowo, M.Pd</span>
                                </div>
                            </td>
                            <td class="teacher-mapel px-6 py-4 text-slate-600 dark:text-slate-400 font-medium">Matematika</td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400">Laki-laki</td>
                            <td class="teacher-kepegawaian px-6 py-4 text-slate-600 dark:text-slate-400">PNS</td>
                            <td class="px-6 py-4">
                                <span class="teacher-status inline-flex items-center px-4 py-1 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-800/40 shadow-sm">
                                    Aktif
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-405 hover:text-slate-955 dark:hover:text-slate-100 transition-colors cursor-pointer" title="Edit Data">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </button>
                                    <button class="p-1.5 hover:bg-red-50 dark:hover:bg-red-955/20 rounded-lg text-red-655 dark:text-red-400 hover:text-red-700 transition-colors cursor-pointer" title="Hapus Data">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Row 2 -->
                        <tr class="teacher-row hover:bg-slate-50/40 dark:hover:bg-slate-900/20 transition-colors">
                            <td class="px-6 py-4 text-slate-900 dark:text-slate-50 font-medium">2</td>
                            <td class="teacher-nip px-6 py-4 text-slate-500 dark:text-slate-400 font-mono text-xs">198204152009042003</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-xs font-bold text-slate-700 dark:text-slate-355 shrink-0">
                                        RL
                                    </div>
                                    <span class="teacher-name text-slate-900 dark:text-slate-50 font-semibold tracking-tight">Retno Lestari, S.Pd</span>
                                </div>
                            </td>
                            <td class="teacher-mapel px-6 py-4 text-slate-600 dark:text-slate-400 font-medium">Bahasa Inggris</td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400">Perempuan</td>
                            <td class="teacher-kepegawaian px-6 py-4 text-slate-600 dark:text-slate-400">PNS</td>
                            <td class="px-6 py-4">
                                <span class="teacher-status inline-flex items-center px-4 py-1 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-955/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-800/40 shadow-sm">
                                    Aktif
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-850 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-955 dark:hover:text-slate-100 transition-colors cursor-pointer" title="Edit Data">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </button>
                                    <button class="p-1.5 hover:bg-red-50 dark:hover:bg-red-955/20 rounded-lg text-red-655 dark:text-red-400 hover:text-red-700 transition-colors cursor-pointer" title="Hapus Data">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Row 3 -->
                        <tr class="teacher-row hover:bg-slate-50/40 dark:hover:bg-slate-900/20 transition-colors">
                            <td class="px-6 py-4 text-slate-900 dark:text-slate-50 font-medium">3</td>
                            <td class="teacher-nip px-6 py-4 text-slate-550 dark:text-slate-400 font-mono text-xs">-</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-xs font-bold text-slate-700 dark:text-slate-350 shrink-0">
                                        AF
                                    </div>
                                    <span class="teacher-name text-slate-900 dark:text-slate-50 font-semibold tracking-tight">Ahmad Fauzi, S.Si</span>
                                </div>
                            </td>
                            <td class="teacher-mapel px-6 py-4 text-slate-600 dark:text-slate-400 font-medium">Fisika</td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400">Laki-laki</td>
                            <td class="teacher-kepegawaian px-6 py-4 text-slate-600 dark:text-slate-400">Honorer</td>
                            <td class="px-6 py-4">
                                <span class="teacher-status inline-flex items-center px-4 py-1 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-955/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-800/40 shadow-sm">
                                    Aktif
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-850 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-955 dark:hover:text-slate-100 transition-colors cursor-pointer" title="Edit Data">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </button>
                                    <button class="p-1.5 hover:bg-red-50 dark:hover:bg-red-955/20 rounded-lg text-red-655 dark:text-red-400 hover:text-red-700 transition-colors cursor-pointer" title="Hapus Data">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Row 4 -->
                        <tr class="teacher-row hover:bg-slate-50/40 dark:hover:bg-slate-900/20 transition-colors">
                            <td class="px-6 py-4 text-slate-900 dark:text-slate-50 font-medium">4</td>
                            <td class="teacher-nip px-6 py-4 text-slate-500 dark:text-slate-400 font-mono text-xs">199011032022212009</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-xs font-bold text-slate-700 dark:text-slate-350 shrink-0">
                                        SA
                                    </div>
                                    <span class="teacher-name text-slate-900 dark:text-slate-50 font-semibold tracking-tight">Siti Aminah, S.Pd</span>
                                </div>
                            </td>
                            <td class="teacher-mapel px-6 py-4 text-slate-600 dark:text-slate-400 font-medium">Bahasa Indonesia</td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400">Perempuan</td>
                            <td class="teacher-kepegawaian px-6 py-4 text-slate-600 dark:text-slate-400">Tetap Yayasan</td>
                            <td class="px-6 py-4">
                                <span class="teacher-status inline-flex items-center px-4 py-1 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-955/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-800/40 shadow-sm">
                                    Aktif
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-850 rounded-lg text-slate-600 dark:text-slate-455 hover:text-slate-955 dark:hover:text-slate-100 transition-colors cursor-pointer" title="Edit Data">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </button>
                                    <button class="p-1.5 hover:bg-red-55 dark:hover:bg-red-955/20 rounded-lg text-red-655 dark:text-red-400 hover:text-red-700 transition-colors cursor-pointer" title="Hapus Data">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Row 5 -->
                        <tr class="teacher-row hover:bg-slate-50/40 dark:hover:bg-slate-900/20 transition-colors">
                            <td class="px-6 py-4 text-slate-900 dark:text-slate-50 font-medium">5</td>
                            <td class="teacher-nip px-6 py-4 text-slate-500 dark:text-slate-400 font-mono text-xs">196803241995121001</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-xs font-bold text-slate-700 dark:text-slate-355 shrink-0">
                                        BH
                                    </div>
                                    <span class="teacher-name text-slate-900 dark:text-slate-50 font-semibold tracking-tight">Bambang Hermawan, M.T</span>
                                </div>
                            </td>
                            <td class="teacher-mapel px-6 py-4 text-slate-600 dark:text-slate-400 font-medium">Informatika</td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400">Laki-laki</td>
                            <td class="teacher-kepegawaian px-6 py-4 text-slate-600 dark:text-slate-400">PNS</td>
                            <td class="px-6 py-4">
                                <span class="teacher-status inline-flex items-center px-4 py-1 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-955/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-800/40 shadow-sm">
                                    Cuti
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-855 rounded-lg text-slate-600 dark:text-slate-455 hover:text-slate-955 dark:hover:text-slate-100 transition-colors cursor-pointer" title="Edit Data">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </button>
                                    <button class="p-1.5 hover:bg-red-55 dark:hover:bg-red-955/20 rounded-lg text-red-655 dark:text-red-400 hover:text-red-700 transition-colors cursor-pointer" title="Hapus Data">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800/80 bg-slate-50/20 dark:bg-slate-900/10 flex items-center justify-between gap-4">
                <span class="text-xs text-slate-500 dark:text-slate-400">
                    Menampilkan <span class="font-semibold text-slate-700 dark:text-slate-300">1-5</span> dari <span class="font-semibold text-slate-700 dark:text-slate-300">86</span> guru
                </span>

                <div class="flex items-center gap-1.5">
                    <button
                        style="width: 36px; height: 36px; border-radius: 8px;"
                        class="inline-flex items-center justify-center text-xs font-semibold text-slate-550 dark:text-slate-400 border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-955 hover:bg-slate-50 dark:hover:bg-slate-900 transition-all disabled:opacity-50 cursor-pointer shadow-sm"
                        disabled>
                        <i data-lucide="chevron-left" class="w-4 h-4"></i>
                    </button>
                    <button
                        style="width: 36px; height: 36px; border-radius: 8px;"
                        class="inline-flex items-center justify-center text-xs font-bold text-white dark:text-slate-900 bg-slate-900 dark:bg-slate-50 shadow-sm">1</button>
                    <button
                        style="width: 36px; height: 36px; border-radius: 8px;"
                        class="inline-flex items-center justify-center text-xs font-semibold text-slate-700 dark:text-slate-355 border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-955 hover:bg-slate-50 dark:hover:bg-slate-900 transition-all cursor-pointer shadow-sm">2</button>
                    <button
                        style="width: 36px; height: 36px; border-radius: 8px;"
                        class="inline-flex items-center justify-center text-xs font-semibold text-slate-700 dark:text-slate-355 border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-955 hover:bg-slate-50 dark:hover:bg-slate-900 transition-all cursor-pointer shadow-sm">3</button>
                    <button
                        style="width: 36px; height: 36px; border-radius: 8px;"
                        class="inline-flex items-center justify-center text-xs font-semibold text-slate-550 dark:text-slate-400 border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-955 hover:bg-slate-50 dark:hover:bg-slate-900 transition-all cursor-pointer shadow-sm">
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </section>

    </div>

    <!-- Client-side real-time Search & Filter Javascript -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('table-search');
            const filterMapel = document.getElementById('filter-mapel');
            const filterKepegawaian = document.getElementById('filter-kepegawaian');
            const filterStatus = document.getElementById('filter-status');
            const rows = document.querySelectorAll('.teacher-row');

            function filterTable() {
                const searchQuery = searchInput.value.toLowerCase().trim();
                const mapelQuery = filterMapel.value.toLowerCase();
                const kepegawaianQuery = filterKepegawaian.value.toLowerCase();
                const statusQuery = filterStatus.value.toLowerCase();

                rows.forEach(row => {
                    const name = row.querySelector('.teacher-name').textContent.toLowerCase();
                    const nip = row.querySelector('.teacher-nip').textContent.toLowerCase();
                    const mapel = row.querySelector('.teacher-mapel').textContent.toLowerCase();
                    const kepegawaian = row.querySelector('.teacher-kepegawaian').textContent.toLowerCase();
                    const status = row.querySelector('.teacher-status').textContent.trim().toLowerCase();

                    const matchesSearch = name.includes(searchQuery) || nip.includes(searchQuery) || mapel.includes(searchQuery);
                    const matchesMapel = !mapelQuery || mapel.includes(mapelQuery);
                    const matchesKepegawaian = !kepegawaianQuery || kepegawaian.includes(kepegawaianQuery);
                    const matchesStatus = !statusQuery || status.includes(statusQuery);

                    if (matchesSearch && matchesMapel && matchesKepegawaian && matchesStatus) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            if (searchInput) searchInput.addEventListener('input', filterTable);
            if (filterMapel) filterMapel.addEventListener('change', filterTable);
            if (filterKepegawaian) filterKepegawaian.addEventListener('change', filterTable);
            if (filterStatus) filterStatus.addEventListener('change', filterTable);
        });
    </script>
</x-admin-layout>
