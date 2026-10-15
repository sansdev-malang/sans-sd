@php
    $isAdmin = auth()->user() && (
        auth()->user()->hasRole('super_admin') || 
        auth()->user()->hasRole('admin_sd') || 
        auth()->user()->hasRole('admin_paud') || 
        auth()->user()->hasRole('admin_smp') || 
        auth()->user()->hasRole('kepala_sekolah') || 
        auth()->user()->hasRole('waka')
    );
    $isSuperAdmin = auth()->user() && auth()->user()->hasRole('super_admin');
    $empId = auth()->user()?->employee_id;
    $showPicketMenu = $isAdmin || ($empId && \Illuminate\Support\Facades\Cache::remember('user_has_picket_' . $empId, 300, function() use ($empId) {
        return \App\Models\PicketSchedule::where('employee_id', $empId)->exists();
    }));
    $canAccessRapor = auth()->user()?->canAccessRapor();
    $raporLink = '#';
    $cbtLink = '#';
    if ($canAccessRapor) {
        $ssoSecret = \App\Models\Setting::get('rapor_sso_secret', env('SSO_SECRET_KEY', 'sans_rapor_secret_sso_key_2026'));
        $raporUrl = \App\Models\Setting::get('rapor_url', env('SANS_RAPOR_URL', 'http://sans-rapor.test'));
        $cbtSecret = \App\Models\Setting::get('cbt_sso_secret', env('SSO_CBT_SECRET_KEY', 'sans_cbt_secret_sso_key_2026'));
        $cbtUrl = \App\Models\Setting::get('cbt_url', env('SANS_CBT_URL', 'http://sans-cbt.test'));
        $curUser = auth()->user();
        $ssoRole = $curUser?->role;
        if ($curUser?->hasRole('super_admin')) {
            $ssoRole = 'super_admin';
        } elseif ($curUser?->hasRole('kepala_sekolah')) {
            $ssoRole = 'kepala_sekolah';
        } elseif ($curUser?->isActiveHomeroomTeacher()) {
            $ssoRole = 'wali_kelas';
        }
        $ssoPayload = base64_encode(json_encode([
            'id' => $curUser?->id,
            'name' => $curUser?->name,
            'email' => $curUser?->email,
            'employee_id' => $curUser?->employee_id,
            'role' => $ssoRole ?? 'guru',
            'unit' => 'sd',
            'timestamp' => time(),
        ]));
        $ssoSig = hash_hmac('sha256', $ssoPayload, $ssoSecret);
        $raporLink = "{$raporUrl}/sso/login?data=" . urlencode($ssoPayload) . "&signature=" . urlencode($ssoSig);

        $cbtSig = hash_hmac('sha256', $ssoPayload, $cbtSecret);
        $cbtLink = "{$cbtUrl}/sso/login?data=" . urlencode($ssoPayload) . "&signature=" . urlencode($cbtSig);
    }
@endphp
<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-[60] md:z-20 flex flex-col w-64 h-full md:h-screen max-h-screen bg-white dark:bg-[#09090b] border-r border-slate-200 dark:border-slate-800 p-3 shrink-0 transition-transform duration-300 -translate-x-full md:translate-x-0 md:relative shadow-sm md:shadow-none overflow-hidden">

    <style>
        .sidebar-collapsed .sidebar-dropdown-content {
            display: none !important;
        }
    </style>

    <!-- App Brand / Dashboard Link -->
    <a href="{{ route('dashboard') }}"
        class="workspace-selector flex items-center justify-between p-2 mb-4 hover:bg-slate-100 dark:hover:bg-slate-900 rounded-lg cursor-pointer transition-colors relative group shrink-0">
        <div class="flex items-center gap-2.5">
            @if (setting('app_logo'))
                <img src="{{ asset('storage/' . setting('app_logo')) }}" alt="Logo" class="w-8 h-8 rounded-lg object-cover shrink-0 shadow-sm">
            @else
                <div class="w-8 h-8 rounded-lg logo-gradient-bg flex items-center justify-center shrink-0 shadow-sm">
                    <span class="text-white text-lg font-bold" style="font-family: 'Nasalization Rg', sans-serif; font-weight: 400;">
                        {{ substr(setting('app_name', config('app.name', 'SANS SD')), 0, 1) }}
                    </span>
                </div>
            @endif
            <div class="school-info overflow-hidden">
                <h1 class="text-lg text-slate-900 dark:text-slate-50 truncate leading-normal tracking-wide" style="font-family: 'Nasalization Rg', sans-serif; font-weight: 400;">
                    {{ setting('app_name', config('app.name', 'SANS SD')) }}
                </h1>
            </div>
        </div>

        <!-- Tooltip for collapsed view -->
        <span
            class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
            {{ setting('unit_name', 'SANS Malang') }}
        </span>
    </a>

    <!-- Grouped Navigation Links (sidebar-07 style) -->
    <div id="sidebar-nav-container" class="flex-1 min-h-0 space-y-4 overflow-y-auto px-1 py-2 custom-sidebar-scroll">
        <!-- Dashboard Link (At the very top, outside groups) -->
        <div class="space-y-1">
            <a href="{{ route('dashboard') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg
                {{ Request::routeIs('dashboard') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }}
                text-xs relative group">
                <i data-lucide="layout-dashboard" class="menu-icon w-4 h-4 shrink-0"></i>
                <span class="menu-text">Dashboard</span>
                <span
                    class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                    Dashboard
                </span>
            </a>
        </div>

        <!-- Group 1: Layanan Pegawai (Personal Guru & Pegawai) -->
        <div>
            <h3 class="school-info px-2 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">
                Layanan Pegawai
            </h3>
            <nav class="space-y-1">
                @if($empId)
                <a href="{{ route('my-employee-profile.edit') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::routeIs('my-employee-profile.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }} text-xs relative group">
                    <i data-lucide="user" class="menu-icon w-4 h-4 shrink-0"></i>
                    <span class="menu-text">Profil Pegawai</span>
                    <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                        Profil Pegawai
                    </span>
                </a>
                @endif

                <a href="{{ route('attendances.index') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::routeIs('attendances.index') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }} text-xs relative group">
                    <i data-lucide="scan-face" class="menu-icon w-4 h-4 shrink-0"></i>
                    <span class="menu-text">Data Kehadiran</span>
                    <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                        Data Kehadiran
                    </span>
                </a>

                @if($empId)
                <a href="{{ route('my-leaves.index') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::routeIs('my-leaves.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }} text-xs relative group">
                    <i data-lucide="file-signature" class="menu-icon w-4 h-4 shrink-0"></i>
                    <span class="menu-text">Pengajuan Izin</span>
                    <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                        Pengajuan Izin
                    </span>
                </a>
                @endif

                @if(auth()->user()->role !== 'employee')
                <a href="{{ route('bonus-reports.index') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::routeIs('bonus-reports.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }} text-xs relative group">
                    <i data-lucide="award" class="menu-icon w-4 h-4 shrink-0"></i>
                    <span class="menu-text">{{ auth()->user()->hasRole('super_admin') ? 'Rekap Bonus' : 'Bonus Kehadiran' }}</span>
                    <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                        {{ auth()->user()->hasRole('super_admin') ? 'Rekap Bonus' : 'Bonus Kehadiran' }}
                    </span>
                </a>
                @endif

                <a href="{{ route('payslips.index') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::routeIs('payslips.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }} text-xs relative group">
                    <i data-lucide="file-text" class="menu-icon w-4 h-4 shrink-0"></i>
                    <span class="menu-text">Slip Gaji</span>
                    <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                        Slip Gaji
                    </span>
                </a>

                @if($showPicketMenu)
                <a href="{{ route('picket-schedules.index') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::routeIs('picket-schedules.index') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }} text-xs relative group">
                    <i data-lucide="calendar-days" class="menu-icon w-4 h-4 shrink-0"></i>
                    <span class="menu-text">Jadwal Piket</span>
                    <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                        Jadwal Piket
                    </span>
                </a>
                @endif

                <a href="{{ route('announcements.index') }}"
                    class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::routeIs('announcements.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }} transition-colors text-xs relative group">
                    <i data-lucide="bell" class="menu-icon w-4 h-4 shrink-0"></i>
                    <span class="menu-text">Pengumuman</span>
                    <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                        Pengumuman
                    </span>
                </a>
            </nav>
        </div>

        <!-- Group 2: Kesiswaan & Akademik -->
        @if($isAdmin || $empId || $canAccessRapor)
        <div>
            <h3 class="school-info px-2 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">
                Kesiswaan & Akademik
            </h3>
            <nav class="space-y-1">
                @if($isAdmin)
                <!-- SPMB -->
                <a href="{{ route('spmb.candidates.index') }}" class="menu-item flex items-center justify-between gap-3 px-3 py-2 rounded-lg
                    {{ Request::routeIs('spmb.candidates.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }}
                    text-xs relative group">
                    <div class="flex items-center gap-3">
                        <i data-lucide="user-plus" class="menu-icon w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0"></i>
                        <span class="menu-text">SPMB</span>
                    </div>
                    <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                        SPMB
                    </span>
                </a>

                <!-- Data Siswa -->
                <a href="{{ route('students.index') }}" class="menu-item flex items-center justify-between gap-3 px-3 py-2 rounded-lg
                    {{ Request::routeIs('students.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }}
                    text-xs relative group">
                    <div class="flex items-center gap-3">
                        <i data-lucide="users" class="menu-icon w-4 h-4 shrink-0"></i>
                        <span class="menu-text">Data Siswa</span>
                    </div>
                    <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                        Data Siswa
                    </span>
                </a>

                <!-- Rekapitulasi Rombel (Laporan Sheet 2 TU) -->
                <a href="{{ route('student-reports.index') }}" class="menu-item flex items-center justify-between gap-3 px-3 py-2 rounded-lg
                    {{ Request::routeIs('student-reports.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }}
                    text-xs relative group">
                    <div class="flex items-center gap-3">
                        <i data-lucide="file-bar-chart-2" class="menu-icon w-4 h-4 shrink-0"></i>
                        <span class="menu-text">Rekapitulasi Rombel</span>
                    </div>
                    <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                        Rekapitulasi Rombel
                    </span>
                </a>
                @endif

                @if($canAccessRapor)
                <!-- Dropdown: Rapor & Ujian (CBT) -->
                <div x-data="{ openRapor: false }">
                    <button @click="openRapor = !openRapor"
                        class="menu-item w-full flex items-center justify-between px-3 py-2 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-colors text-xs font-medium relative group cursor-pointer">
                        <div class="flex items-center gap-3">
                            <i data-lucide="award" class="menu-icon w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0"></i>
                            <span class="menu-text">Rapor & Ujian (CBT)</span>
                        </div>
                        <i data-lucide="chevron-right" class="chevron-icon w-3.5 h-3.5 transition-transform duration-200"
                            :style="openRapor ? 'transform: rotate(90deg);' : ''"></i>
                        <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                            Rapor & Ujian (CBT)
                        </span>
                    </button>

                    <!-- Dropdown content with line connector -->
                    <div x-show="openRapor" x-collapse
                        class="sidebar-dropdown-content mt-1 ml-5 pl-4 border-l border-slate-200 dark:border-slate-800 space-y-1">
                        <!-- Aplikasi E-Rapor -->
                        <a href="{{ $raporLink }}" target="_blank"
                            class="flex items-center justify-between gap-2 py-1 text-xs font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 transition-colors group/link">
                            <span class="flex items-center gap-1.5">
                                <span>Aplikasi E-Rapor</span>
                                <i data-lucide="external-link" class="w-3 h-3 text-slate-400 opacity-60 group-hover/link:opacity-100 transition-opacity"></i>
                            </span>
                            <span class="text-[9px] uppercase tracking-wider font-semibold px-1 py-0.5 rounded bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50 leading-none">SSO</span>
                        </a>

                        <!-- Aplikasi CBT -->
                        <a href="{{ $cbtLink }}" target="_blank"
                            class="flex items-center justify-between gap-2 py-1 text-xs font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 transition-colors group/link">
                            <span class="flex items-center gap-1.5">
                                <span>Aplikasi CBT</span>
                                <i data-lucide="external-link" class="w-3 h-3 text-slate-400 opacity-60 group-hover/link:opacity-100 transition-opacity"></i>
                            </span>
                            <span class="text-[9px] uppercase tracking-wider font-semibold px-1 py-0.5 rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800/50 leading-none">SSO</span>
                        </a>
                    </div>
                </div>
                @endif

                @if($isAdmin)
                <!-- Dropdown: Siklus & Alumni -->
                <div x-data="{ openLifecycle: {{ Request::routeIs('promotions.*', 'alumni.*') ? 'true' : 'false' }} }">
                    <button @click="openLifecycle = !openLifecycle"
                        class="menu-item w-full flex items-center justify-between px-3 py-2 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-colors text-xs font-medium relative group cursor-pointer {{ Request::routeIs('promotions.*', 'alumni.*') ? 'text-slate-900 dark:text-slate-50 bg-slate-100 dark:bg-slate-800 font-medium' : '' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="archive" class="menu-icon w-4 h-4 shrink-0"></i>
                            <span class="menu-text">Siklus & Alumni</span>
                        </div>
                        <i data-lucide="chevron-right" class="chevron-icon w-3.5 h-3.5 transition-transform duration-200"
                            :style="openLifecycle ? 'transform: rotate(90deg);' : ''"></i>
                        <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                            Siklus & Alumni
                        </span>
                    </button>

                    <!-- Dropdown content with line connector -->
                    <div x-show="openLifecycle" x-collapse
                        class="sidebar-dropdown-content mt-1 ml-5 pl-4 border-l border-slate-200 dark:border-slate-800 space-y-1">
                        <!-- Kenaikan & Kelulusan -->
                        <a href="{{ route('promotions.index') }}"
                            class="flex items-center justify-between gap-2 py-1 text-xs font-medium {{ Request::routeIs('promotions.*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100' }} transition-colors">
                            <span>Kenaikan & Kelulusan</span>
                        </a>

                        <!-- Buku Induk Alumni -->
                        <a href="{{ route('alumni.index') }}"
                            class="flex items-center justify-between gap-2 py-1 text-xs font-medium {{ Request::routeIs('alumni.*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100' }} transition-colors">
                            <span>Buku Induk Alumni</span>
                        </a>
                    </div>
                </div>
                @endif
            </nav>
        </div>
        @endif

        @if($isAdmin)
        <!-- Group 3: Pendidik & Kepegawaian -->
        <div>
            <h3 class="school-info px-2 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">
                Pendidik & Kepegawaian
            </h3>
            <nav class="space-y-1">
                <!-- Dropdown: Manajemen Guru -->
                <div x-data="{ openTeachers: {{ Request::routeIs('teachers.*', 'homeroom-assignments.*') ? 'true' : 'false' }} }">
                    <button @click="openTeachers = !openTeachers"
                        class="menu-item w-full flex items-center justify-between px-3 py-2 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-colors text-xs font-medium relative group cursor-pointer {{ Request::routeIs('teachers.*', 'homeroom-assignments.*') ? 'text-slate-900 dark:text-slate-50 bg-slate-100 dark:bg-slate-800 font-medium' : '' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="graduation-cap" class="menu-icon w-4 h-4 shrink-0"></i>
                            <span class="menu-text">Manajemen Guru</span>
                        </div>
                        <i data-lucide="chevron-right" class="chevron-icon w-3.5 h-3.5 transition-transform duration-200"
                            :style="openTeachers ? 'transform: rotate(90deg);' : ''"></i>
                        <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                            Manajemen Guru
                        </span>
                    </button>

                    <!-- Dropdown content with line connector -->
                    <div x-show="openTeachers" x-collapse
                        class="sidebar-dropdown-content mt-1 ml-5 pl-4 border-l border-slate-200 dark:border-slate-800 space-y-1">
                        <!-- Data Guru -->
                        <a href="{{ route('teachers.index') }}"
                            class="flex items-center justify-between gap-2 py-1 text-xs font-medium {{ Request::routeIs('teachers.*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100' }} transition-colors">
                            <span>Data Guru</span>
                        </a>

                        <!-- Wali Kelas & Formasi -->
                        <a href="{{ route('homeroom-assignments.index') }}"
                            class="flex items-center justify-between gap-2 py-1 text-xs font-medium {{ Request::routeIs('homeroom-assignments.*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100' }} transition-colors">
                            <span>Wali Kelas & Formasi</span>
                        </a>
                    </div>
                </div>

                <!-- Dropdown: Kepegawaian & HRD -->
                <div x-data="{ openHrd: {{ Request::routeIs('employees.*', 'leaves.*', 'leave-types.*', 'picket-schedules.admin', 'employee-types.*') ? 'true' : 'false' }} }">
                    <button @click="openHrd = !openHrd"
                        class="menu-item w-full flex items-center justify-between px-3 py-2 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-colors text-xs font-medium relative group cursor-pointer {{ Request::routeIs('employees.*', 'leaves.*', 'leave-types.*', 'picket-schedules.admin', 'employee-types.*') ? 'text-slate-900 dark:text-slate-50 bg-slate-100 dark:bg-slate-800 font-medium' : '' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="users-2" class="menu-icon w-4 h-4 shrink-0"></i>
                            <span class="menu-text">Kepegawaian & HRD</span>
                        </div>
                        <i data-lucide="chevron-right" class="chevron-icon w-3.5 h-3.5 transition-transform duration-200"
                            :style="openHrd ? 'transform: rotate(90deg);' : ''"></i>
                        <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                            Kepegawaian & HRD
                        </span>
                    </button>

                    <!-- Dropdown content with line connector -->
                    <div x-show="openHrd" x-collapse
                        class="sidebar-dropdown-content mt-1 ml-5 pl-4 border-l border-slate-200 dark:border-slate-800 space-y-1">
                        <!-- Data Pegawai -->
                        <a href="{{ route('employees.index') }}"
                            class="flex items-center justify-between gap-2 py-1 text-xs font-medium {{ Request::routeIs('employees.*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100' }} transition-colors">
                            <span>Data Pegawai</span>
                        </a>

                        <!-- Riwayat Izin & Cuti -->
                        <a href="{{ route('leaves.index') }}"
                            class="flex items-center justify-between gap-2 py-1 text-xs font-medium {{ Request::routeIs('leaves.*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100' }} transition-colors">
                            <span>Riwayat Izin & Cuti</span>
                        </a>

                        <!-- Kelola Piket -->
                        <a href="{{ route('picket-schedules.admin') }}"
                            class="flex items-center justify-between gap-2 py-1 text-xs font-medium {{ Request::routeIs('picket-schedules.admin') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100' }} transition-colors">
                            <span>Kelola Piket</span>
                        </a>

                        <!-- Tipe Pegawai -->
                        <a href="{{ route('employee-types.index') }}"
                            class="flex items-center justify-between gap-2 py-1 text-xs font-medium {{ Request::routeIs('employee-types.*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100' }} transition-colors">
                            <span>Tipe Pegawai</span>
                        </a>

                        <!-- Tipe Izin -->
                        <a href="{{ route('leave-types.index') }}"
                            class="flex items-center justify-between gap-2 py-1 text-xs font-medium {{ Request::routeIs('leave-types.*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100' }} transition-colors">
                            <span>Tipe Izin</span>
                        </a>
                    </div>
                </div>
            </nav>
        </div>
        @endif

        <!-- Group 4: Pusat Data & Pengaturan -->
        @if($isAdmin)
        <div>
            <h3 class="school-info px-2 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">
                Pusat Data & Pengaturan
            </h3>
            <nav class="space-y-1">
                <!-- Dropdown: Master Akademik -->
                <div x-data="{ openAcademic: {{ Request::routeIs('classrooms.*', 'class-levels.*', 'academic-years.*', 'semesters.*') ? 'true' : 'false' }} }">
                    <button @click="openAcademic = !openAcademic"
                        class="menu-item w-full flex items-center justify-between px-3 py-2 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-colors text-xs font-medium relative group cursor-pointer {{ Request::routeIs('classrooms.*', 'class-levels.*', 'academic-years.*', 'semesters.*') ? 'text-slate-900 dark:text-slate-50 bg-slate-100 dark:bg-slate-800 font-medium' : '' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="layers" class="menu-icon w-4 h-4 shrink-0"></i>
                            <span class="menu-text">Master Akademik</span>
                        </div>
                        <i data-lucide="chevron-right" class="chevron-icon w-3.5 h-3.5 transition-transform duration-200"
                            :style="openAcademic ? 'transform: rotate(90deg);' : ''"></i>
                        <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                            Master Akademik
                        </span>
                    </button>

                    <!-- Dropdown content with line connector -->
                    <div x-show="openAcademic" x-collapse
                        class="sidebar-dropdown-content mt-1 ml-5 pl-4 border-l border-slate-200 dark:border-slate-800 space-y-1">
                        <a href="{{ route('classrooms.index') }}"
                            class="flex items-center justify-between gap-2 py-1 text-xs font-medium {{ Request::routeIs('classrooms.*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100' }} transition-colors">
                            <span>Rombongan Belajar</span>
                        </a>
                        <a href="{{ route('class-levels.index') }}"
                            class="flex items-center justify-between gap-2 py-1 text-xs font-medium {{ Request::routeIs('class-levels.*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100' }} transition-colors">
                            <span>Tingkat Kelas</span>
                        </a>
                        <a href="{{ route('academic-years.index') }}"
                            class="flex items-center justify-between gap-2 py-1 text-xs font-medium {{ Request::routeIs('academic-years.*', 'semesters.*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100' }} transition-colors">
                            <span>Tahun Ajaran & Semester</span>
                        </a>
                    </div>
                </div>

                @if($isSuperAdmin)
                <!-- Dropdown: Pengaturan Sistem -->
                <div x-data="{ openSystem: {{ Request::routeIs('users.*', 'system-logs.*', 'settings') ? 'true' : 'false' }} }">
                    <button @click="openSystem = !openSystem"
                        class="menu-item w-full flex items-center justify-between px-3 py-2 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-colors text-xs font-medium relative group cursor-pointer {{ Request::routeIs('users.*', 'system-logs.*', 'settings') ? 'text-slate-900 dark:text-slate-50 bg-slate-100 dark:bg-slate-800 font-medium' : '' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="sliders" class="menu-icon w-4 h-4 shrink-0"></i>
                            <span class="menu-text">Pengaturan Sistem</span>
                        </div>
                        <i data-lucide="chevron-right" class="chevron-icon w-3.5 h-3.5 transition-transform duration-200"
                            :style="openSystem ? 'transform: rotate(90deg);' : ''"></i>
                        <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                            Pengaturan Sistem
                        </span>
                    </button>

                    <!-- Dropdown content with line connector -->
                    <div x-show="openSystem" x-collapse
                        class="sidebar-dropdown-content mt-1 ml-5 pl-4 border-l border-slate-200 dark:border-slate-800 space-y-1">
                        <a href="{{ route('users.index') }}"
                            class="flex items-center justify-between gap-2 py-1 text-xs font-medium {{ Request::routeIs('users.*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100' }} transition-colors">
                            <span>Setting User</span>
                        </a>
                        <a href="{{ route('settings') }}"
                            class="flex items-center justify-between gap-2 py-1 text-xs font-medium {{ Request::routeIs('settings') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100' }} transition-colors">
                            <span>Setting App</span>
                        </a>
                        <a href="{{ route('system-logs.index') }}"
                            class="flex items-center justify-between gap-2 py-1 text-xs font-medium {{ Request::routeIs('system-logs.*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100' }} transition-colors">
                            <span>Log System</span>
                        </a>
                    </div>
                </div>
                @endif
            </nav>
        </div>
        @endif
    </div>

    <!-- Bottom User Account Profile Menu (dropdown lookalike at bottom of sidebar-07) -->
    <div class="pt-2 border-t border-slate-200 dark:border-slate-800 relative shrink-0" x-data="{ open: false }">
        <!-- Dropdown menu -->
        <div x-show="open" @click.outside="open = false"
            class="absolute bottom-full left-0 w-60 mb-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl py-1.5 z-50 transition-all origin-bottom-left"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95" style="display: none;">

            <!-- Account Info -->
            <a href="{{ route('profile.edit') }}"
                class="flex items-center gap-2 px-3 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-900/50 hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                <i data-lucide="badge-check" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                <span>Pengaturan Akun</span>
            </a>

            <div class="border-t border-slate-100 dark:border-slate-900 my-1"></div>

            <!-- Log Out -->
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full text-left flex items-center gap-2 px-3 py-2 text-xs text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors cursor-pointer">
                    <i data-lucide="log-out" class="w-4 h-4 text-red-500 dark:text-red-400"></i>
                    <span>Keluar</span>
                </button>
            </form>
        </div>

        <div @click="open = !open"
            class="user-selector flex items-center justify-between p-2 hover:bg-slate-100 dark:hover:bg-slate-900 rounded-lg cursor-pointer transition-colors relative group">
            <div class="flex items-center gap-2.5 overflow-hidden">
                @php
                    $nameParts = explode(' ', Auth::user()->name);
                    $initials = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
                    $userId = Auth::id();
                    $photo = \Illuminate\Support\Facades\Cache::remember('user_sidebar_photo_' . $userId, 300, function () {
                        $u = Auth::user();
                        if (!$u) return null;
                        if ($u->relationLoaded('employee')) {
                            return $u->employee?->photo ?? $u->photo ?? null;
                        }
                        return $u->employee_id ? \App\Models\Employee::where('id', $u->employee_id)->value('photo') : ($u->photo ?? null);
                    });
                @endphp

                @if($photo)
                    <img src="{{ asset('storage/' . $photo) }}" alt="Avatar" class="w-7 h-7 rounded-lg object-cover ring-1 ring-slate-200 dark:ring-slate-800 shrink-0">
                @else
                    <div class="w-7 h-7 rounded-lg bg-indigo-900/30 border border-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold text-xs shrink-0">
                        {{ $initials }}
                    </div>
                @endif
                <div class="user-info overflow-hidden">
                    <h4 class="text-xs font-semibold text-slate-900 dark:text-slate-50 truncate leading-none">
                        {{ Auth::user()->name }}
                    </h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate mt-1">{{ Auth::user()->email }}</p>
                </div>
            </div>
            <i data-lucide="chevrons-up-down" class="chevron-icon w-4 h-4 text-slate-400 shrink-0 ml-1"></i>

            <!-- Tooltip for collapsed view -->
            <span
                class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                {{ Auth::user()->name }}
            </span>
        </div>
    </div>

</aside>
