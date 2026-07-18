<x-admin-layout>
    <div class="p-6 space-y-6">

        <!-- GREETING / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Dashboard</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Selamat datang kembali! Aktivitas sekolah SANS Malang terpantau kondusif.</p>
            </div>
        </section>

        <!-- STAT CARDS GRID -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Stat Card 1 -->
            <div class="animate-card bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Siswa Aktif</p>
                        <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                            <span class="stat-counter" data-target="1248">0</span>
                        </h3>
                    </div>
                    <div class="p-2 bg-slate-50 dark:bg-slate-900 rounded-lg">
                        <i data-lucide="users" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                    </div>
                </div>
                <div class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">+4.5%</span> dari bulan lalu
                </div>
            </div>

            <!-- Stat Card 2 -->
            <div class="animate-card bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Guru & Staf</p>
                        <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                            @if(isset($employeeCount))
                                <span class="stat-counter" data-target="{{ $employeeCount }}">0</span>
                            @else
                                <span>-</span>
                            @endif
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
            <div class="animate-card bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Rombel / Kelas</p>
                        <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                            <span class="stat-counter" data-target="36">0</span>
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
            <div class="animate-card bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Presensi Hari Ini</p>
                        <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                            <span class="stat-counter" data-target="96">0</span>%
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

        <!-- DETAILED SECTIONS: CHARTS & ACTIVITIES -->
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Graph Card (SVG) -->
            <div class="animate-card lg:col-span-2 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-6 relative flex flex-col justify-between overflow-hidden shadow-sm">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50">Ikhtisar Kehadiran Bulanan</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Tingkat kehadiran siswa pada 7 bulan terakhir</p>
                        </div>
                    </div>
                    <!-- Mini Graphic SVG Container -->
                    <div class="relative w-full h-44 mt-4 flex items-end">
                        <svg viewBox="0 0 500 150" class="w-full h-full chart-line overflow-visible">
                            <!-- Grids -->
                            <line x1="0" y1="30" x2="500" y2="30" stroke="currentColor" class="text-slate-100 dark:text-slate-900" stroke-width="1" />
                            <line x1="0" y1="75" x2="500" y2="75" stroke="currentColor" class="text-slate-100 dark:text-slate-900" stroke-width="1" />
                            <line x1="0" y1="120" x2="500" y2="120" stroke="currentColor" class="text-slate-100 dark:text-slate-900" stroke-width="1" />
                            
                            <!-- Area path -->
                            <path d="M 0,150 L 0,110 L 80,120 L 160,85 L 240,95 L 320,60 L 400,45 L 500,30 L 500,150 Z" 
                                  fill="url(#grad-area)" opacity="0.15"></path>
                            
                            <!-- Animated line path -->
                            <path d="M 0,110 L 80,120 L 160,85 L 240,95 L 320,60 L 400,45 L 500,30" 
                                  fill="none" stroke="currentColor" class="text-slate-800 dark:text-slate-100" stroke-width="2" stroke-linecap="round"></path>
                            
                            <!-- Gradients defs -->
                            <defs>
                                <linearGradient id="grad-area" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="currentColor" class="text-slate-800 dark:text-slate-100" />
                                    <stop offset="100%" stop-color="currentColor" class="text-slate-800 dark:text-slate-100" stop-opacity="0" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </div>
                
                <!-- Month labels -->
                <div class="flex justify-between text-xs text-slate-400 dark:text-slate-500 font-semibold uppercase px-1 mt-4">
                    <span>Jan</span>
                    <span>Feb</span>
                    <span>Mar</span>
                    <span>Apr</span>
                    <span>Mei</span>
                    <span>Jun</span>
                    <span>Jul</span>
                </div>
            </div>

            <!-- Announcements / Information System -->
            <div class="animate-card bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-6 flex flex-col justify-between shadow-sm">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50">Pengumuman Sekolah</h3>
                        <a href="#" class="text-xs text-slate-500 dark:text-slate-400 font-semibold hover:underline">Lihat Semua</a>
                    </div>

                    <!-- List of updates -->
                    <div class="space-y-3.5">
                        <div class="flex gap-2.5 items-start">
                            <div class="w-1.5 h-1.5 rounded-full bg-slate-900 dark:bg-slate-50 mt-1.5 shrink-0"></div>
                            <div>
                                <h4 class="text-xs font-semibold text-slate-900 dark:text-slate-50">Ujian Akhir Semester</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">UAS Semester Genap dimulai serentak pada 15 Juli 2026.</p>
                            </div>
                        </div>
                        <div class="flex gap-2.5 items-start">
                            <div class="w-1.5 h-1.5 rounded-full bg-slate-400 dark:bg-slate-600 mt-1.5 shrink-0"></div>
                            <div>
                                <h4 class="text-xs font-semibold text-slate-900 dark:text-slate-50">Rapat Wali Murid</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pertemuan sosialisasi kurikulum Merdeka Belajar Kelas X.</p>
                            </div>
                        </div>
                        <div class="flex gap-2.5 items-start">
                            <div class="w-1.5 h-1.5 rounded-full bg-slate-450 dark:bg-slate-550 mt-1.5 shrink-0"></div>
                            <div>
                                <h4 class="text-xs font-semibold text-slate-900 dark:text-slate-50">Prestasi Robotika</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">SANS Robotik Club meraih Juara 1 tingkat Provinsi Jawa Timur.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info tag footer -->
                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-455">
                    <span>Admin Update: Hari Ini</span>
                </div>
            </div>
        </section>

        <!-- QUICK ACTIONS & ACTIVITY FEED -->
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Quick Actions Grid -->
            <div class="animate-card bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50 mb-4">Aksi Cepat</h3>
                <div class="grid grid-cols-2 gap-2">
                    <button class="flex flex-col items-center justify-center p-3 border border-slate-200 dark:border-slate-850 hover:bg-slate-50 dark:hover:bg-slate-900 rounded-lg group transition-all duration-150 cursor-pointer">
                        <i data-lucide="user-plus" class="w-4 h-4 text-slate-650 dark:text-slate-450 group-hover:scale-105 transition-transform"></i>
                        <span class="text-xs font-medium text-slate-700 dark:text-slate-300 mt-1.5">Tambah Siswa</span>
                    </button>
                    <button class="flex flex-col items-center justify-center p-3 border border-slate-200 dark:border-slate-850 hover:bg-slate-50 dark:hover:bg-slate-900 rounded-lg group transition-all duration-150 cursor-pointer">
                        <i data-lucide="clipboard-list" class="w-4 h-4 text-slate-655 dark:text-slate-455 group-hover:scale-105 transition-transform"></i>
                        <span class="text-xs font-medium text-slate-700 dark:text-slate-300 mt-1.5">Input Nilai</span>
                    </button>
                    <button class="flex flex-col items-center justify-center p-3 border border-slate-200 dark:border-slate-850 hover:bg-slate-50 dark:hover:bg-slate-900 rounded-lg group transition-all duration-150 cursor-pointer">
                        <i data-lucide="send" class="w-4 h-4 text-slate-650 dark:text-slate-455 group-hover:scale-105 transition-transform"></i>
                        <span class="text-xs font-medium text-slate-700 dark:text-slate-300 mt-1.5">Kirim Pesan</span>
                    </button>
                    <button class="flex flex-col items-center justify-center p-3 border border-slate-200 dark:border-slate-850 hover:bg-slate-50 dark:hover:bg-slate-900 rounded-lg group transition-all duration-150 cursor-pointer">
                        <i data-lucide="receipt" class="w-4 h-4 text-slate-650 dark:text-slate-455 group-hover:scale-105 transition-transform"></i>
                        <span class="text-xs font-medium text-slate-700 dark:text-slate-300 mt-1.5">Cek SPP</span>
                    </button>
                </div>
            </div>

            <!-- Recent Activity Logs -->
            <div class="animate-card lg:col-span-2 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50 mb-4">Log Aktivitas Terbaru</h3>
                <div class="space-y-3.5">
                    <div class="flex items-center justify-between py-1 border-b border-slate-50 dark:border-slate-900/60 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-lg bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-600 dark:text-slate-400">
                                <i data-lucide="key" class="w-3.5 h-3.5"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-800 dark:text-slate-200">Login Wali Kelas XI-IPA</p>
                                <p class="text-xs text-slate-500">Guru: Drs. Eko Prasetyo</p>
                            </div>
                        </div>
                        <span class="text-xs text-slate-450">10 mnt yang lalu</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-slate-50 dark:border-slate-900/60 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-lg bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-600 dark:text-slate-400">
                                <i data-lucide="file-plus" class="w-3.5 h-3.5"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-800 dark:text-slate-200">Unggah Materi Fisika Kuantum</p>
                                <p class="text-xs text-slate-500">Kelas: XII-IPA</p>
                            </div>
                        </div>
                        <span class="text-xs text-slate-450">24 mnt yang lalu</span>
                    </div>
                    <div class="flex items-center justify-between py-1">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-lg bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-600 dark:text-slate-400">
                                <i data-lucide="user-check" class="w-3.5 h-3.5"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-800 dark:text-slate-200">Verifikasi Berkas Pendaftaran</p>
                                <p class="text-xs text-slate-500">Gelombang 2 SANS Malang</p>
                            </div>
                        </div>
                        <span class="text-xs text-slate-455">45 mnt yang lalu</span>
                    </div>
                </div>
            </div>
        </section>

    </div>
</x-admin-layout>
