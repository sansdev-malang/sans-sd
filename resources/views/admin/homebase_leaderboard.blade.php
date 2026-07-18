<x-admin-layout>
    <div class="p-6 space-y-6">

        <!-- GREETING / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Leaderboard Homebase</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Selamat datang kembali! Aktivitas sekolah SANS Malang terpantau kondusif.</p>
            </div>
        </section>

        <!-- STAT CARDS GRID -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
            
            <!-- Stat Card 1 -->
            <div class="animate-card border-t-[4px] dark:border-t-[4px] border-t-red-600 dark:border-t-red-600 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Homebase Merah</p>
                        <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                            <span class="stat-counter" data-target="1248">0</span>
                        </h3>
                    </div>
                    <span class="inline-flex items-center rounded-xl bg-red-400/10 px-2 py-1 text-xs font-bold text-red-400 inset-ring inset-ring-red-400/20">1st Place</span>
                </div>
                <div class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">Akumulasi Poin</span>
                </div>
            </div>

            <!-- Stat Card 2 -->
            <div class="animate-card border-t-[4px] dark:border-t-[4px] border-t-yellow-600 dark:border-t-yellow-600 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Homebase Kuning</p>
                        <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                            <span class="stat-counter" data-target="1248">0</span>
                        </h3>
                    </div>
                    <span class="inline-flex items-center rounded-xl bg-yellow-400/10 px-2 py-1 text-xs font-bold text-yellow-400 inset-ring inset-ring-yellow-400/20">2nd Place</span>
                </div>
                <div class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">Akumulasi Poin</span>
                </div>
            </div>
            
            <!-- Stat Card 3 -->
            <div class="animate-card border-t-[4px] dark:border-t-[4px] border-t-green-600 dark:border-t-green-600 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Homebase Hijau</p>
                        <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                            <span class="stat-counter" data-target="1248">0</span>
                        </h3>
                    </div>
                    <span class="inline-flex items-center rounded-xl bg-green-400/10 px-2 py-1 text-xs font-bold text-green-400 inset-ring inset-ring-green-400/20">3rd Place</span>
                </div>
                <div class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">Akumulasi Poin</span>
                </div>
            </div>

            <!-- Stat Card 4 -->
            <div class="animate-card border-t-[4px] dark:border-t-[4px] border-t-blue-600 dark:border-t-blue-600 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Homebase Biru</p>
                        <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                            <span class="stat-counter" data-target="1248">0</span>
                        </h3>
                    </div>
                    <span class="inline-flex items-center rounded-xl bg-blue-400/10 px-2 py-1 text-xs font-bold text-blue-400 inset-ring inset-ring-blue-400/20">4th Place</span>
                </div>
                <div class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">Akumulasi Poin</span>
                </div>
            </div>
            
            <!-- Stat Card 5 -->
            <div class="animate-card border-t-[4px] dark:border-t-[4px] border-t-purple-600 dark:border-t-purple-600 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Homebase Ungu</p>
                        <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                            <span class="stat-counter" data-target="1248">0</span>
                        </h3>
                    </div>
                    <span class="inline-flex items-center rounded-xl bg-purple-400/10 px-2 py-1 text-xs font-bold text-purple-400 inset-ring inset-ring-purple-400/20">5th Place</span>
                </div>
                <div class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">Akumulasi Poin</span>
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
                            <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50">Komparasi Poin</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Perbandingan jumlah poin per Homebase di Bulan Ini</p>
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

                <!-- Info tag footer -->
                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-455">
                    <span>Admin Update: Hari Ini</span>
                </div>
            </div>
        </section>

        <!-- TOP STUDENTS PER HOMEBASE -->
        <section class="space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-slate-50">Siswa Berprestasi per Homebase (Poin Tertinggi)</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                
                <!-- Homebase Merah -->
                <div class="animate-card relative overflow-hidden border-l-[4px] border-l-red-600 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-sm flex flex-col justify-between">
                    <!-- Background Star Watermark -->
                    <div class="absolute -right-4 -top-4 text-red-500/10 dark:text-red-500/5 pointer-events-none transform rotate-12">
                        <i data-lucide="star" class="w-16 h-16 fill-current"></i>
                    </div>
                    <div class="flex items-center gap-3 relative z-10">
                        <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-950/30 text-red-600 dark:text-red-400 flex items-center justify-center text-sm font-bold shrink-0">
                            AF
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-xs font-bold text-slate-900 dark:text-slate-50 truncate">Ahmad Fauzi</h4>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400">Kelas 10 / X-A</p>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-900 flex justify-between items-center relative z-10">
                        <div class="text-[10px] text-slate-400 dark:text-slate-550">
                            <p>NIS: 1234</p>
                            <p>NISN: 1234567890</p>
                        </div>
                        <span class="inline-flex items-center rounded-lg bg-red-400/10 px-2.5 py-1 text-[10px] font-bold text-red-500">450 Poin</span>
                    </div>
                </div>

                <!-- Homebase Kuning -->
                <div class="animate-card relative overflow-hidden border-l-[4px] border-l-yellow-500 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-sm flex flex-col justify-between">
                    <!-- Background Star Watermark -->
                    <div class="absolute -right-4 -top-4 text-yellow-500/10 dark:text-yellow-500/5 pointer-events-none transform rotate-12">
                        <i data-lucide="star" class="w-16 h-16 fill-current"></i>
                    </div>
                    <div class="flex items-center gap-3 relative z-10">
                        <div class="w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-950/30 text-yellow-600 dark:text-yellow-400 flex items-center justify-center text-sm font-bold shrink-0">
                            SA
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-xs font-bold text-slate-900 dark:text-slate-50 truncate">Sarah Amelia</h4>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400">Kelas 11 / XI-IPA</p>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-900 flex justify-between items-center relative z-10">
                        <div class="text-[10px] text-slate-400 dark:text-slate-550">
                            <p>NIS: 1235</p>
                            <p>NISN: 1234567891</p>
                        </div>
                        <span class="inline-flex items-center rounded-lg bg-yellow-400/10 px-2.5 py-1 text-[10px] font-bold text-yellow-600">420 Poin</span>
                    </div>
                </div>

                <!-- Homebase Hijau -->
                <div class="animate-card relative overflow-hidden border-l-[4px] border-l-green-600 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-sm flex flex-col justify-between">
                    <!-- Background Star Watermark -->
                    <div class="absolute -right-4 -top-4 text-green-500/10 dark:text-green-500/5 pointer-events-none transform rotate-12">
                        <i data-lucide="star" class="w-16 h-16 fill-current"></i>
                    </div>
                    <div class="flex items-center gap-3 relative z-10">
                        <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-950/30 text-green-600 dark:text-green-400 flex items-center justify-center text-sm font-bold shrink-0">
                            RH
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-xs font-bold text-slate-900 dark:text-slate-50 truncate">Rian Hidayat</h4>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400">Kelas 12 / XII-IPS</p>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-900 flex justify-between items-center relative z-10">
                        <div class="text-[10px] text-slate-400 dark:text-slate-550">
                            <p>NIS: 1236</p>
                            <p>NISN: 1234567892</p>
                        </div>
                        <span class="inline-flex items-center rounded-lg bg-green-400/10 px-2.5 py-1 text-[10px] font-bold text-green-600">395 Poin</span>
                    </div>
                </div>

                <!-- Homebase Biru -->
                <div class="animate-card relative overflow-hidden border-l-[4px] border-l-blue-600 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-sm flex flex-col justify-between">
                    <!-- Background Star Watermark -->
                    <div class="absolute -right-4 -top-4 text-blue-500/10 dark:text-blue-500/5 pointer-events-none transform rotate-12">
                        <i data-lucide="star" class="w-16 h-16 fill-current"></i>
                    </div>
                    <div class="flex items-center gap-3 relative z-10">
                        <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-sm font-bold shrink-0">
                            NP
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-xs font-bold text-slate-900 dark:text-slate-50 truncate">Nadia Putri</h4>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400">Kelas 10 / X-B</p>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-900 flex justify-between items-center relative z-10">
                        <div class="text-[10px] text-slate-400 dark:text-slate-550">
                            <p>NIS: 1237</p>
                            <p>NISN: 1234567893</p>
                        </div>
                        <span class="inline-flex items-center rounded-lg bg-blue-400/10 px-2.5 py-1 text-[10px] font-bold text-blue-600">380 Poin</span>
                    </div>
                </div>

                <!-- Homebase Ungu -->
                <div class="animate-card relative overflow-hidden border-l-[4px] border-l-indigo-600 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-sm flex flex-col justify-between">
                    <!-- Background Star Watermark -->
                    <div class="absolute -right-4 -top-4 text-indigo-500/10 dark:text-indigo-500/5 pointer-events-none transform rotate-12">
                        <i data-lucide="star" class="w-16 h-16 fill-current"></i>
                    </div>
                    <div class="flex items-center gap-3 relative z-10">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-950/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-sm font-bold shrink-0">
                            FP
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-xs font-bold text-slate-900 dark:text-slate-50 truncate">Farhan Pratama</h4>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400">Kelas 11 / XI-IPS</p>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-900 flex justify-between items-center relative z-10">
                        <div class="text-[10px] text-slate-400 dark:text-slate-550">
                            <p>NIS: 1238</p>
                            <p>NISN: 1234567894</p>
                        </div>
                        <span class="inline-flex items-center rounded-lg bg-indigo-400/10 px-2.5 py-1 text-[10px] font-bold text-indigo-600">375 Poin</span>
                    </div>
                </div>

            </div>
        </section>

    </div>
</x-admin-layout>
