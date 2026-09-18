@php
    $isAdmin = auth()->user() && (
        auth()->user()->hasRole('super_admin') || 
        auth()->user()->hasRole('admin_sd') || 
        auth()->user()->hasRole('admin_paud') || 
        auth()->user()->hasRole('admin_smp') || 
        auth()->user()->hasRole('kepala_sekolah') || 
        auth()->user()->hasRole('waka')
    );
@endphp
<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-[60] md:z-20 flex flex-col w-64 bg-white dark:bg-[#09090b] border-r border-slate-200 dark:border-slate-800 p-3 shrink-0 transition-transform duration-300 -translate-x-full md:translate-x-0 md:relative shadow-sm md:shadow-none">

    <!-- App Brand / Dashboard Link -->
    <a href="{{ route('dashboard') }}"
        class="workspace-selector flex items-center justify-between p-2 mb-4 hover:bg-slate-100 dark:hover:bg-slate-900 rounded-lg cursor-pointer transition-colors relative group">
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
    <div id="sidebar-nav-container" class="flex-1 space-y-4 overflow-y-auto px-1 py-2 no-scrollbar">
        <!-- Dashboard Link (At the very top, outside groups) -->
        <div class="space-y-1">
            <a href="{{ route('dashboard') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg
                {{ Request::routeIs('dashboard') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }}
                text-xs relative group">
                <i data-lucide="layout-dashboard" class="menu-icon w-4 h-4"></i>
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
                @if(auth()->user()->employee_id)
                <a href="{{ route('my-employee-profile.edit') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::routeIs('my-employee-profile.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }} text-xs relative group">
                    <i data-lucide="user" class="menu-icon w-4 h-4"></i>
                    <span class="menu-text">Profil Pegawai</span>
                    <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                        Profil Pegawai
                    </span>
                </a>
                @endif

                <a href="{{ route('attendances.index') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::routeIs('attendances.index') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }} text-xs relative group">
                    <i data-lucide="scan-face" class="menu-icon w-4 h-4"></i>
                    <span class="menu-text">Data Absensi</span>
                    <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                        Data Absensi
                    </span>
                </a>

                @if(auth()->user()->employee_id)
                <a href="{{ route('my-leaves.index') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::routeIs('my-leaves.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }} text-xs relative group">
                    <i data-lucide="file-signature" class="menu-icon w-4 h-4"></i>
                    <span class="menu-text">Pengajuan Izin</span>
                    <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                        Pengajuan Izin
                    </span>
                </a>
                @endif

                <a href="{{ route('payslips.index') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::routeIs('payslips.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }} text-xs relative group">
                    <i data-lucide="file-text" class="menu-icon w-4 h-4"></i>
                    <span class="menu-text">Slip Gaji</span>
                    <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                        Slip Gaji
                    </span>
                </a>

                @if(config('app.school_unit') === 'sd')
                    @php
                        $empId = auth()->user()->employee_id;
                        $showPicketMenu = $isAdmin || ($empId && \Illuminate\Support\Facades\Cache::remember('user_has_picket_' . $empId, 300, function() use ($empId) {
                            return \App\Models\PicketSchedule::where('employee_id', $empId)->exists();
                        }));
                    @endphp
                    @if($showPicketMenu)
                    <a href="{{ route('picket-schedules.index') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::routeIs('picket-schedules.index') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }} text-xs relative group">
                        <i data-lucide="calendar-days" class="menu-icon w-4 h-4"></i>
                        <span class="menu-text">Jadwal Piket</span>
                        <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                            Jadwal Piket
                        </span>
                    </a>
                    @endif
                @endif
            </nav>
        </div>

        @if($isAdmin)
        <!-- Group 2: Akademik & Kesiswaan -->
        <div>
            <h3 class="school-info px-2 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">
                Akademik & Kesiswaan
            </h3>
            <nav class="space-y-1">
                <a href="{{ route('students.index') }}" class="menu-item flex items-center justify-between gap-3 px-3 py-2 rounded-lg
                    {{ Request::routeIs('students.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }}
                    text-xs relative group">
                    <div class="flex items-center gap-3">
                        <i data-lucide="users" class="menu-icon w-4 h-4"></i>
                        <span class="menu-text">Data Siswa</span>
                    </div>
                    <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                        Data Siswa
                    </span>
                </a>

                <a href="{{ route('spmb.candidates.index') }}" class="menu-item flex items-center justify-between gap-3 px-3 py-2 rounded-lg
                    {{ Request::routeIs('spmb.candidates.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }}
                    text-xs relative group">
                    <div class="flex items-center gap-3">
                        <i data-lucide="user-plus" class="menu-icon w-4 h-4 text-emerald-600 dark:text-emerald-400"></i>
                        <span class="menu-text">SPMB</span>
                    </div>
                    <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                        SPMB
                    </span>
                </a>

                <a href="{{ route('classrooms.index') }}" class="menu-item flex items-center justify-between gap-3 px-3 py-2 rounded-lg 
                    {{ Request::routeIs('classrooms.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }} 
                    text-xs font-medium relative group">
                    <div class="flex items-center gap-3">
                        <i data-lucide="university" class="menu-icon w-4 h-4"></i>
                        <span class="menu-text">Rombongan Belajar</span>
                    </div>
                    <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                        Rombongan Belajar
                    </span>
                </a>

                <!-- Dropdown: Master Akademik -->
                <div x-data="{ openAcademic: {{ Request::routeIs('class-levels.*', 'academic-years.*') ? 'true' : 'false' }} }">
                    <button @click="openAcademic = !openAcademic"
                        class="menu-item w-full flex items-center justify-between px-3 py-2 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-colors text-xs font-medium relative group cursor-pointer {{ Request::routeIs('class-levels.*', 'academic-years.*') ? 'text-slate-900 dark:text-slate-50 bg-slate-50/50 dark:bg-slate-900/30' : '' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="layers" class="menu-icon w-4 h-4"></i>
                            <span class="menu-text">Master Akademik</span>
                        </div>
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 transition-transform duration-200"
                            :style="openAcademic ? 'transform: rotate(90deg);' : ''"></i>
                    </button>

                    <!-- Dropdown content with line connector -->
                    <div x-show="openAcademic" x-collapse
                        class="mt-1 ml-5 pl-4 border-l border-slate-200 dark:border-slate-800 space-y-1"
                        style="margin-left:20px">
                        <a href="{{ route('class-levels.index') }}"
                            class="flex items-center justify-between gap-2 py-1.5 text-xs font-medium {{ Request::routeIs('class-levels.*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100' }} transition-colors">
                            <span>Tingkat Kelas</span>
                        </a>
                        <a href="{{ route('academic-years.index') }}"
                            class="flex items-center justify-between gap-2 py-1.5 text-xs font-medium {{ Request::routeIs('academic-years.*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100' }} transition-colors">
                            <span>Tahun Ajaran</span>
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Group 3: Kepegawaian (HRD) -->
        <div>
            <h3 class="school-info px-2 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">
                Kepegawaian (HRD)
            </h3>
            <nav class="space-y-1">
                <!-- Data Pegawai -->
                <a href="{{ route('employees.index') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::routeIs('employees.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }} text-xs relative group">
                    <i data-lucide="users-2" class="menu-icon w-4 h-4"></i>
                    <span class="menu-text">Data Pegawai</span>
                    <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-805 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                        Data Pegawai
                    </span>
                </a>

                <!-- Data Guru -->
                <a href="{{ route('teachers.index') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::routeIs('teachers.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }} text-xs relative group">
                    <i data-lucide="graduation-cap" class="menu-icon w-4 h-4"></i>
                    <span class="menu-text">Data Guru</span>
                    <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                        Data Guru
                    </span>
                </a>

                <!-- Rekap Bonus -->
                <a href="{{ route('bonus-reports.index') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::routeIs('bonus-reports.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }} text-xs relative group">
                    <i data-lucide="gift" class="menu-icon w-4 h-4"></i>
                    <span class="menu-text">Rekap Bonus</span>
                    <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                        Rekap Bonus
                    </span>
                </a>

                <!-- Dropdown: Izin & Cuti -->
                <div x-data="{ openLeaves: {{ Request::routeIs('leaves.*', 'leave-types.*') ? 'true' : 'false' }} }">
                    <button @click="openLeaves = !openLeaves"
                        class="menu-item w-full flex items-center justify-between px-3 py-2 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-colors text-xs font-medium relative group cursor-pointer {{ Request::routeIs('leaves.*', 'leave-types.*') ? 'text-slate-900 dark:text-slate-50 bg-slate-50/50 dark:bg-slate-900/30' : '' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="calendar-clock" class="menu-icon w-4 h-4"></i>
                            <span class="menu-text">Izin & Cuti</span>
                        </div>
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 transition-transform duration-200"
                            :style="openLeaves ? 'transform: rotate(90deg);' : ''"></i>
                    </button>

                    <!-- Dropdown content with line connector -->
                    <div x-show="openLeaves" x-collapse
                        class="mt-1 ml-5 pl-4 border-l border-slate-200 dark:border-slate-800 space-y-1"
                        style="margin-left:20px">
                        <a href="{{ route('leaves.index') }}"
                            class="flex items-center justify-between gap-2 py-1.5 text-xs font-medium {{ Request::routeIs('leaves.*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100' }} transition-colors">
                            <span>Riwayat Izin & Cuti</span>
                        </a>
                        <a href="{{ route('leave-types.index') }}"
                            class="flex items-center justify-between gap-2 py-1.5 text-xs font-medium {{ Request::routeIs('leave-types.*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100' }} transition-colors">
                            <span>Tipe Izin</span>
                        </a>
                    </div>
                </div>

                @if(config('app.school_unit') === 'sd')
                <!-- Kelola Piket -->
                <a href="{{ route('picket-schedules.admin') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::routeIs('picket-schedules.admin') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }} text-xs relative group">
                    <i data-lucide="calendar-range" class="menu-icon w-4 h-4"></i>
                    <span class="menu-text">Kelola Piket</span>
                    <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-850 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                        Kelola Piket
                    </span>
                </a>
                @endif

                <!-- Tipe Pegawai -->
                <a href="{{ route('employee-types.index') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::routeIs('employee-types.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }} text-xs relative group">
                    <i data-lucide="tag" class="menu-icon w-4 h-4"></i>
                    <span class="menu-text">Tipe Pegawai</span>
                    <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-805 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                        Tipe Pegawai
                    </span>
                </a>
            </nav>
        </div>
        @endif

        <!-- Group 4: Pengaturan & Sistem -->
        <div>
            <h3 class="school-info px-2 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">
                Pengaturan & Sistem
            </h3>
            <nav class="space-y-1">
                <a href="{{ route('announcements.index') }}"
                    class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg {{ Request::routeIs('announcements.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }} transition-colors text-xs relative group">
                    <i data-lucide="bell" class="menu-icon w-4 h-4"></i>
                    <span class="menu-text">Pengumuman</span>
                    <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                        Pengumuman
                    </span>
                </a>

                @if(auth()->user()->hasRole('super_admin'))
                <!-- Dropdown: Setting System -->
                <div x-data="{ openSystem: {{ Request::routeIs('users.*', 'system-logs.*', 'settings') ? 'true' : 'false' }} }">
                    <button @click="openSystem = !openSystem"
                        class="menu-item w-full flex items-center justify-between px-3 py-2 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-colors text-xs font-medium relative group cursor-pointer {{ Request::routeIs('users.*', 'system-logs.*', 'settings') ? 'text-slate-900 dark:text-slate-50 bg-slate-50/50 dark:bg-slate-900/30' : '' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="sliders" class="menu-icon w-4 h-4"></i>
                            <span class="menu-text">Setting System</span>
                        </div>
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 transition-transform duration-200"
                            :style="openSystem ? 'transform: rotate(90deg);' : ''"></i>
                    </button>

                    <!-- Dropdown content with line connector -->
                    <div x-show="openSystem" x-collapse
                        class="mt-1 ml-5 pl-4 border-l border-slate-200 dark:border-slate-800 space-y-1"
                        style="margin-left:20px">
                        <a href="{{ route('users.index') }}"
                            class="flex items-center justify-between gap-2 py-1.5 text-xs font-medium {{ Request::routeIs('users.*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100' }} transition-colors">
                            <span>Setting User</span>
                        </a>
                        <a href="{{ route('settings') }}"
                            class="flex items-center justify-between gap-2 py-1.5 text-xs font-medium {{ Request::routeIs('settings') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100' }} transition-colors">
                            <span>Setting App</span>
                        </a>
                        <a href="{{ route('system-logs.index') }}"
                            class="flex items-center justify-between gap-2 py-1.5 text-xs font-medium {{ Request::routeIs('system-logs.*') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100' }} transition-colors">
                            <span>Log System</span>
                        </a>
                    </div>
                </div>
                @endif
            </nav>
        </div>
    </div>

    <!-- Bottom User Account Profile Menu (dropdown lookalike at bottom of sidebar-07) -->
    <div class="pt-2 border-t border-slate-200 dark:border-slate-800 relative" x-data="{ open: false }">
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
