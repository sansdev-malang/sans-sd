<x-admin-layout>
    <div class="p-4 sm:p-6 lg:p-8 max-w-4xl mx-auto space-y-6" x-init="if (window.lucide) window.lucide.createIcons()">
        <!-- HEADER -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="text-base sm:text-xl font-bold tracking-tight text-slate-900 dark:text-slate-100 font-nasalization">
                    Data Kehadiran Personal
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                    Kalender dan rekapitulasi kehadiran mandiri pegawai.
                </p>
            </div>
            
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm">
                <i data-lucide="arrow-left" class="w-4 h-4 text-slate-400"></i>
                <span>Kembali ke Dashboard</span>
            </a>
        </div>

        <!-- MAIN NOTICE CARD -->
        <div class="relative overflow-hidden rounded-2xl border border-amber-200/80 dark:border-amber-900/60 bg-gradient-to-br from-amber-50/60 via-white to-amber-50/30 dark:from-slate-900 dark:via-slate-900 dark:to-amber-950/20 p-6 sm:p-8 shadow-sm">
            <!-- Decorative background glow -->
            <div class="absolute -right-16 -top-16 w-56 h-56 rounded-full bg-amber-400/10 blur-3xl pointer-events-none"></div>

            <div class="relative flex flex-col md:flex-row items-start gap-6">
                <!-- Icon badge -->
                <div class="shrink-0 w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-600 text-white flex items-center justify-center shadow-lg shadow-amber-500/25 ring-4 ring-amber-100 dark:ring-amber-900/40">
                    <i data-lucide="user-x" class="w-7 h-7 sm:w-8 sm:h-8"></i>
                </div>

                <!-- Content -->
                <div class="flex-1 space-y-4">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold tracking-wide uppercase bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-900/60 mb-2">
                            <i data-lucide="shield-alert" class="w-3.5 h-3.5"></i>
                            Proteksi Akses Data
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-slate-100">
                            Akun Belum Ditautkan ke Data Pegawai
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 mt-1 leading-relaxed">
                            Akun Anda saat ini memiliki peran <span class="font-semibold text-slate-800 dark:text-slate-200">Pegawai (Employee)</span>, namun belum dihubungkan dengan profil pegawai aktif di unit sekolah ini (<strong class="text-indigo-600 dark:text-indigo-400">{{ strtoupper(config('app.school_unit', 'SD')) }}</strong>).
                        </p>
                    </div>

                    <!-- User Detail Summary -->
                    <div class="bg-white/90 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/60 rounded-xl p-4 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div>
                            <span class="text-slate-500 dark:text-slate-400 block text-[11px] uppercase tracking-wider font-semibold">Nama Akun</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ auth()->user()->name }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 dark:text-slate-400 block text-[11px] uppercase tracking-wider font-semibold">Email Login</span>
                            <span class="font-mono text-slate-700 dark:text-slate-300">{{ auth()->user()->email }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 dark:text-slate-400 block text-[11px] uppercase tracking-wider font-semibold">Unit Sekolah</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 uppercase">{{ config('app.school_unit', 'SD') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 dark:text-slate-400 block text-[11px] uppercase tracking-wider font-semibold">Status Profil Pegawai</span>
                            <span class="inline-flex items-center gap-1 font-semibold text-rose-600 dark:text-rose-400">
                                <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                Belum Terhubung (ID: Kosong)
                            </span>
                        </div>
                    </div>

                    <!-- Information alert -->
                    <div class="rounded-xl border border-sky-100 dark:border-sky-900/40 bg-sky-50/70 dark:bg-sky-950/30 p-3.5 text-xs text-slate-700 dark:text-slate-300 space-y-1">
                        <div class="flex items-center gap-1.5 font-bold text-sky-900 dark:text-sky-300">
                            <i data-lucide="info" class="w-4 h-4 text-sky-600 dark:text-sky-400 shrink-0"></i>
                            <span>Mengapa Halaman Ini Ditampilkan?</span>
                        </div>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed pl-5.5">
                            Demi menjaga keamanan dan privasi data seluruh staf, akun dengan peran pegawai tidak diizinkan membuka matriks rekapitulasi sekolah. Halaman kehadiran personal (kalender presensi) otomatis aktif begitu akun Anda ditautkan ke profil pegawai di unit ini.
                        </p>
                    </div>

                    <!-- Help contact -->
                    <div class="flex items-start gap-2.5 text-xs text-slate-600 dark:text-slate-400 pt-1">
                        <div class="w-6 h-6 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 mt-0.5">
                            <i data-lucide="help-circle" class="w-3.5 h-3.5"></i>
                        </div>
                        <p class="text-[11px] sm:text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                            Silakan hubungi <strong class="text-slate-700 dark:text-slate-200">Admin atau Tim Dev</strong> untuk menghubungkan akun login Anda dengan data profil pegawai di unit ini.
                        </p>
                    </div>

                    <!-- Action buttons -->
                    <div class="flex flex-wrap items-center gap-2.5 pt-2">
                        <a href="{{ route('dashboard') }}" 
                           class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-md shadow-indigo-500/20 transition-all cursor-pointer">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                            <span>Buka Dashboard</span>
                        </a>

                        @if(session()->has('impersonator_id'))
                            <form action="{{ route('impersonate.leave') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 hover:bg-slate-800 dark:hover:bg-slate-100 font-semibold text-xs rounded-xl shadow-sm transition-all cursor-pointer">
                                    <i data-lucide="arrow-left-circle" class="w-4 h-4 text-amber-400 dark:text-amber-600"></i>
                                    <span>Kembali ke Super Admin</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
