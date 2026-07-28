<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-40 flex flex-col w-64 bg-white dark:bg-[#09090b] border-r border-slate-200 dark:border-slate-800 p-3 shrink-0 transition-transform duration-300 -translate-x-full md:translate-x-0 md:relative md:z-20 shadow-sm md:shadow-none">

    <!-- Workspace / School Selector (dropdown lookalike) -->
    <div
        class="workspace-selector flex items-center justify-between p-2 mb-4 hover:bg-slate-100 dark:hover:bg-slate-900 rounded-lg cursor-pointer transition-colors relative group">
        <div class="flex items-center gap-2.5">
            @if (setting('app_logo'))
                <img src="{{ asset('storage/' . setting('app_logo')) }}" alt="Logo" class="w-8 h-8 rounded-lg object-cover shrink-0 shadow-sm">
            @else
                <div class="w-8 h-8 rounded-lg logo-gradient-bg flex items-center justify-center shrink-0 shadow-sm">
                    <span class="text-white text-lg font-bold" style="font-family: 'Nasalization Rg', sans-serif; font-weight: 400;">
                        {{ substr(setting('app_name', 'SANS SD'), 0, 1) }}
                    </span>
                </div>
            @endif
            <div class="school-info overflow-hidden">
                <h1 class="text-lg text-slate-900 dark:text-slate-50 truncate leading-normal tracking-wide" style="font-family: 'Nasalization Rg', sans-serif; font-weight: 400;">
                    {{ setting('app_name', 'SANS SD') }}
                </h1>
            </div>
        </div>
        <!-- Dropdown selector arrow -->
        <i data-lucide="chevrons-up-down" class="chevron-icon w-4 h-4 text-slate-400 shrink-0 ml-1"></i>

        <!-- Tooltip for collapsed view -->
        <span
            class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-950 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-150 pointer-events-none whitespace-nowrap z-50">
            {{ setting('unit_name', 'SANS Malang') }}
        </span>
    </div>

    <!-- Grouped Navigation Links (sidebar-07 style) -->
    <div class="flex-1 space-y-4 overflow-y-auto px-1 py-2 no-scrollbar">
        <!-- Group 1: Platform -->
        <div>
            <h3
                class="school-info px-2 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">
                Platform</h3>
            <nav class="space-y-1">
                <a href="{{ route('dashboard') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg
                    {{ Request::routeIs('dashboard') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-650 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }}
                    text-xs relative group">
                    <i data-lucide="layout-dashboard" class="menu-icon w-4 h-4"></i>
                    <span class="menu-text">Dashboard</span>
                    <span
                        class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-950 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-150 pointer-events-none whitespace-nowrap z-50">
                        Dashboard
                    </span>
                </a>

                <a href="{{ route('siswa') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg
                    {{ Request::routeIs('siswa') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-650 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }}
                    text-xs relative group">
                    <i data-lucide="users" class="menu-icon w-4 h-4"></i>
                    <span class="menu-text">Data Siswa</span>
                    <span
                        class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-950 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-150 pointer-events-none whitespace-nowrap z-50">
                        Data Siswa
                    </span>
                </a>
                @if(auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin_sd') || auth()->user()->hasRole('admin_paud') || auth()->user()->hasRole('admin_smp') || auth()->user()->hasRole('kepala_sekolah') || auth()->user()->hasRole('waka'))
                <a href="{{ route('teachers.index') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg
                    {{ Request::routeIs('teachers.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-655 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }}
                    text-xs relative group">
                    <i data-lucide="graduation-cap" class="menu-icon w-4 h-4"></i>
                    <span class="menu-text">Data Guru</span>
                    <span
                        class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-950 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-150 pointer-events-none whitespace-nowrap z-50">
                        Data Guru
                    </span>
                </a>
                @endif
                <a href="{{ route('rombel') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg 
                    {{ Request::routeIs('rombel') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-655 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }} 
                    text-xs font-medium relative group">
                    <i data-lucide="university" class="menu-icon w-4 h-4"></i>
                    <span class="menu-text">Rombongan Belajar</span>
                    <span
                        class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-950 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-150 pointer-events-none whitespace-nowrap z-50">
                        Rombongan Belajar
                    </span>
                </a>
            </nav>
        </div>

        <!-- Group 2: Homebase (Dropdown style) -->
        <div>
            <h3
                class="school-info px-2 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">
                Homebase</h3>
            <nav class="space-y-1"></nav>
            <a href="{{ route('homebase_leaderboard') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg 
                    {{ Request::routeIs('homebase_leaderboard') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-655 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }} 
                    text-xs font-medium relative group">
                <i data-lucide="podium" class="menu-icon w-4 h-4"></i>
                <span class="menu-text">Leaderboard</span>
                <span
                    class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-950 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-150 pointer-events-none whitespace-nowrap z-50">
                    Leaderboard
                </span>
            </a>
            <div x-data="{ open1: false, open2: false }">
                <button @click="open1 = !open1"
                    class="menu-item w-full flex items-center justify-between px-3 py-2 rounded-lg text-slate-650 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-colors text-xs font-medium relative group cursor-pointer">
                    <div class="flex items-center gap-3">
                        <i data-lucide="database" class="menu-icon w-4 h-4"></i>
                        <span class="menu-text">Database</span>
                    </div>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 transition-transform duration-200"
                        :style="open1 ? 'transform: rotate(90deg);' : ''"></i>
                </button>

                <!-- Dropdown content with line connector -->
                <div x-show="open1" x-collapse
                    class="mt-1 ml-5 pl-4 border-l border-slate-200 dark:border-slate-800 space-y-1"
                    style="margin-left:20px">
                    <a href="{{ route('homebase_merah') }}"
                        class="flex items-center gap-2 py-1.5 text-xs font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                        <i data-lucide="sparkles" class="w-3 h-3 text-red-600"></i>
                        <span>Homebase Merah</span>
                    </a>
                    <a href="{{ route('homebase_kuning') }}"
                        class="flex items-center gap-2 py-1.5 text-xs font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                        <i data-lucide="sparkles" class="w-3 h-3 text-yellow-600"></i>
                        <span>Homebase Kuning</span>
                    </a>
                    <a href="{{ route('homebase_hijau') }}"
                        class="flex items-center gap-2 py-1.5 text-xs font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                        <i data-lucide="sparkles" class="w-3 h-3 text-green-600"></i>
                        <span>Homebase Hijau</span>
                    </a>
                    <a href="{{ route('homebase_biru') }}"
                        class="flex items-center gap-2 py-1.5 text-xs font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                        <i data-lucide="sparkles" class="w-3 h-3 text-blue-600"></i>
                        <span>Homebase Biru</span>
                    </a>
                    <a href="{{ route('homebase_ungu') }}"
                        class="flex items-center gap-2 py-1.5 text-xs font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                        <i data-lucide="sparkles" class="w-3 h-3 text-indigo-600"></i>
                        <span>Homebase Ungu</span>
                    </a>
                </div>
                <a href="{{ route('form_homebase') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg 
                    {{ Request::routeIs('form_homebase') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-655 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }} 
                    text-xs font-medium relative group">
                <i data-lucide="star-check" class="menu-icon w-4 h-4"></i>
                <span class="menu-text">Input Poin</span>
                <span
                    class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-950 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-150 pointer-events-none whitespace-nowrap z-50">
                    Input Poin
                </span>
            </a>
            </div>
        </div>

                        <!-- Group: Layanan Pegawai -->
        <div>
            <h3 class="school-info px-2 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2 mt-4">
                Layanan Pegawai
            </h3>
            <nav class="space-y-1 mb-2">
                <a href="{{ route('attendances.index') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::routeIs('attendances.index') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-655 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }} text-xs relative group">
                    <i data-lucide="scan-face" class="menu-icon w-4 h-4"></i>
                    <span class="menu-text">Data Absensi</span>
                    <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-950 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-150 pointer-events-none whitespace-nowrap z-50">
                        Data Absensi
                    </span>
                </a>
                <a href="{{ route('bonus-reports.index') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::routeIs('bonus-reports.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-655 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }} text-xs relative group">
                    <i data-lucide="gift" class="menu-icon w-4 h-4"></i>
                    <span class="menu-text">Rekap Bonus</span>
                    <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-950 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-150 pointer-events-none whitespace-nowrap z-50">
                        Rekap Bonus
                    </span>
                </a>
                <a href="{{ route('my-leaves.index') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::routeIs('my-leaves.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-655 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }} text-xs relative group">
                    <i data-lucide="file-signature" class="menu-icon w-4 h-4"></i>
                    <span class="menu-text">Izin & Cuti Saya</span>
                    <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-950 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-150 pointer-events-none whitespace-nowrap z-50">
                        Izin & Cuti Saya
                    </span>
                </a>
                <a href="{{ route('payslips.index') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::routeIs('payslips.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-655 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }} text-xs relative group">
                    <i data-lucide="file-text" class="menu-icon w-4 h-4"></i>
                    <span class="menu-text">Slip Gaji</span>
                    <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-950 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-150 pointer-events-none whitespace-nowrap z-50">
                        Slip Gaji
                    </span>
                </a>
            </nav>
        </div>

        @if(auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin_sd') || auth()->user()->hasRole('admin_paud') || auth()->user()->hasRole('admin_smp') || auth()->user()->hasRole('kepala_sekolah'))
        <!-- Group: Admin Absensi & Cuti -->
        <div>
            <h3 class="school-info px-2 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2 mt-4">
                Admin Absensi & Cuti
            </h3>
            
            <nav class="space-y-1 mb-2">
                <a href="{{ route('leaves.index') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::routeIs('leaves.*', 'leave-approvals.*', 'leave-history.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-655 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }} text-xs relative group">
                    <i data-lucide="calendar-clock" class="menu-icon w-4 h-4"></i>
                    <span class="menu-text">Persetujuan Izin / Cuti</span>
                    <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-950 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-150 pointer-events-none whitespace-nowrap z-50">
                        Persetujuan Izin / Cuti
                    </span>
                </a>
            </nav>
        </div>
        @endif

                @if(auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin_sd') || auth()->user()->hasRole('admin_paud') || auth()->user()->hasRole('admin_smp') || auth()->user()->hasRole('kepala_sekolah'))
        <!-- Group: Manajemen Pegawai -->
        <div>
            <h3
                class="school-info px-2 text-xs font-semibold text-slate-400 dark:text-slate-555 uppercase tracking-wider mb-2">
                Manajemen Pegawai</h3>
            <nav class="space-y-1">
                <!-- Data Pegawai -->
                <a href="{{ route('employees.index') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::routeIs('employees.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-655 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }} text-xs relative group">
                    <i data-lucide="users-2" class="menu-icon w-4 h-4"></i>
                    <span class="menu-text">Data Pegawai</span>
                    <span
                        class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-950 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-150 pointer-events-none whitespace-nowrap z-50">
                        Data Pegawai
                    </span>
                </a>

                <!-- Tipe Pegawai -->
                <a href="{{ route('employee-types.index') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::routeIs('employee-types.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-655 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }} text-xs relative group">
                    <i data-lucide="tag" class="menu-icon w-4 h-4"></i>
                    <span class="menu-text">Tipe Pegawai</span>
                    <span
                        class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-950 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-150 pointer-events-none whitespace-nowrap z-50">
                        Tipe Pegawai
                    </span>
                </a>
            </nav>
        </div>
        @endif

        <!-- Group 4: Manajemen -->
        <div>
            <h3
                class="school-info px-2 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">
                Manajemen</h3>
            <nav class="space-y-1">
                <a href="#"
                    class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg text-slate-650 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-colors text-xs font-medium relative group">
                    <i data-lucide="calendar" class="menu-icon w-4 h-4"></i>
                    <span class="menu-text">Jadwal Kelas</span>
                    <span
                        class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-950 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-150 pointer-events-none whitespace-nowrap z-50">
                        Jadwal Kelas
                    </span>
                </a>
                <a href="#"
                    class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg text-slate-650 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-colors text-xs font-medium relative group">
                    <i data-lucide="bell" class="menu-icon w-4 h-4"></i>
                    <span class="menu-text">Pengumuman</span>
                    <span
                        class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-950 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-150 pointer-events-none whitespace-nowrap z-50">
                        Pengumuman
                    </span>
                </a>
                @if(auth()->user()->hasRole('super_admin'))
                <a href="{{ route('users.index') }}"
                    class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg
                    {{ Request::routeIs('users.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-655 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }}
                    text-xs relative group">
                    <i data-lucide="user-cog" class="menu-icon w-4 h-4"></i>
                    <span class="menu-text">Manajemen User</span>
                    <span
                        class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-950 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-150 pointer-events-none whitespace-nowrap z-50">
                        Manajemen User
                    </span>
                </a>

                <a href="{{ route('settings') }}"
                    class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg
                    {{ Request::routeIs('settings') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-655 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }}
                    text-xs relative group">
                    <i data-lucide="settings" class="menu-icon w-4 h-4"></i>
                    <span class="menu-text">Pengaturan</span>
                    <span
                        class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-955 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-150 pointer-events-none whitespace-nowrap z-50">
                        Pengaturan
                    </span>
                </a>
                @endif
            </nav>
        </div>
    </div>



    <!-- Bottom User Account Profile Menu (dropdown lookalike at bottom of sidebar-07) -->
    <div class="pt-2 border-t border-slate-200 dark:border-slate-800 relative" x-data="{ open: false }">
        <!-- Dropdown menu -->
        <div x-show="open" @click.outside="open = false"
            class="absolute bottom-full left-0 w-60 mb-2 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl py-1.5 z-50 transition-all origin-bottom-left"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95" style="display: none;">

            <!-- Account Info -->
            <a href="{{ route('profile.edit') }}"
                class="flex items-center gap-2 px-3 py-2 text-xs text-slate-700 dark:text-slate-350 hover:bg-slate-50 dark:hover:bg-slate-900/50 hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                <i data-lucide="badge-check" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                <span>Account</span>
            </a>
            <a href="#"
                class="flex items-center gap-2 px-3 py-2 text-xs text-slate-700 dark:text-slate-350 hover:bg-slate-50 dark:hover:bg-slate-900/50 hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                <i data-lucide="bell" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                <span>Notifications</span>
            </a>

            <div class="border-t border-slate-100 dark:border-slate-900 my-1"></div>

            <!-- Log Out -->
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="flex items-center gap-2 px-3 py-2 text-xs text-red-650 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/20 transition-colors">
                    <i data-lucide="log-out" class="w-4 h-4 text-red-500 dark:text-red-450"></i>
                    <span>Log out</span>
                </button>
            </form>
        </div>

        <div @click="open = !open"
            class="user-selector flex items-center justify-between p-2 hover:bg-slate-100 dark:hover:bg-slate-900 rounded-lg cursor-pointer transition-colors relative group">
            <div class="flex items-center gap-2.5 overflow-hidden">
                <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=256"
                    alt="Avatar" class="w-7 h-7 rounded-lg object-cover ring-1 ring-slate-200 dark:ring-slate-800">
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
                class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-950 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-150 pointer-events-none whitespace-nowrap z-50">
                {{ Auth::user()->name }}
            </span>
        </div>
    </div>

</aside>