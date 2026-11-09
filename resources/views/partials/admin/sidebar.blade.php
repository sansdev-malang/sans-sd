@php
    $isAdmin = auth()->user() && (
        auth()->user()->hasRole('super_admin') || 
        auth()->user()->hasRole('admin_sd') || 
        auth()->user()->hasRole('admin_paud') || 
        auth()->user()->hasRole('admin_smp') || 
        auth()->user()->hasRole('kepala_sekolah') || 
        auth()->user()->hasRole('waka')
    );
    $isSuperAdmin = auth()->user() && (auth()->user()->hasRole('super_admin') || auth()->user()->role === 'superadmin');
    $isExecutive = auth()->user() && (
        auth()->user()->hasRole('kepala_sekolah') || 
        auth()->user()->role === 'kepala_sekolah' || 
        auth()->user()->hasRole('waka') || 
        auth()->user()->role === 'waka'
    );
    $empId = auth()->user()?->employee_id;
    $showPicketMenu = $isAdmin || ($empId && \Illuminate\Support\Facades\Cache::remember('user_has_picket_' . $empId, 300, function() use ($empId) {
        return \App\Models\PicketSchedule::where('employee_id', $empId)->exists();
    }));
    // Integrasi Rapor & CBT
    $canAccessRapor = $isSuperAdmin || $isExecutive || (auth()->user()?->canAccessRapor() ?? false);
    $canAccessCbt = auth()->user()?->canAccessCbt();
    $raporLink = route('report-cards.index');
    $cbtLink = route('cbt.launcher');
    $curUser = auth()->user();
@endphp
<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-[60] md:z-20 flex flex-col w-64 h-full md:h-screen max-h-screen bg-white dark:bg-[#09090b] border-r border-slate-200 dark:border-slate-800 p-3 shrink-0 transition-transform duration-300 md:transition-none -translate-x-full md:translate-x-0 md:relative shadow-sm md:shadow-none overflow-hidden">

    <style>
        .sidebar-collapsed .sidebar-dropdown-content {
            display: none !important;
        }
    </style>

    <!-- App Brand / Dashboard Link -->
    <a href="{{ route('dashboard') }}"
        class="workspace-selector flex items-center justify-between p-2 mb-3 hover:bg-slate-100 dark:hover:bg-slate-900 rounded-lg cursor-pointer transition-colors relative group shrink-0">
        <div class="flex items-center gap-2.5">
            @if (setting('app_logo'))
                <img src="{{ asset('storage/' . setting('app_logo')) }}" alt="Logo" class="w-8 h-8 rounded-lg object-cover shrink-0 shadow-sm">
            @else
                <div class="w-8 h-8 rounded-lg logo-gradient-bg flex items-center justify-center shrink-0 shadow-sm">
                    <span class="text-white text-lg font-bold" style="font-family: 'Nasalization Rg', sans-serif; font-weight: 400;">
                        {{ substr(setting('app_name', config('app.name', 'SAANS SD')), 0, 1) }}
                    </span>
                </div>
            @endif
            <div class="school-info overflow-hidden">
                <h1 class="text-lg text-slate-900 dark:text-slate-50 truncate leading-normal tracking-wide" style="font-family: 'Nasalization Rg', sans-serif; font-weight: 400;">
                    {{ setting('app_name', config('app.name', 'SAANS SD')) }}
                </h1>
            </div>
        </div>

        <!-- Tooltip for collapsed view -->
        <span
            class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
            {{ setting('unit_name', 'SAANS SD Malang') }}
        </span>
    </a>

    <!-- Grouped Navigation Links (Model A: 1-Level Clean Accordions) -->
    <div id="sidebar-nav-container" class="flex-1 min-h-0 space-y-1.5 overflow-y-auto px-1 py-1 custom-sidebar-scroll">
        
        <!-- 0. Dashboard (Direct Link) -->
        <div>
            <a href="{{ route('dashboard') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg
                {{ Request::routeIs('dashboard') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }}
                text-xs relative group transition-colors">
                <i data-lucide="layout-dashboard" class="menu-icon w-4 h-4 shrink-0 {{ Request::routeIs('dashboard') ? 'text-indigo-600 dark:text-indigo-400' : '' }}"></i>
                <span class="menu-text">Dashboard</span>
                <span
                    class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                    Dashboard
                </span>
            </a>
        </div>

        @if($isAdmin)
        <!-- ==========================================
             1. KATEGORI: AKADEMIK
             ========================================== -->
        @php
            $isAcademicActive = Request::routeIs(
                'academic-years.*', 'semesters.*', 'class-levels.*', 'classrooms.*', 
                'subjects.*', 'student-reports.*'
            );
        @endphp
        <div x-data="{ openAcademic: {{ $isAcademicActive ? 'true' : 'false' }} }">
            <button @click="openAcademic = !openAcademic"
                class="menu-item w-full flex items-center justify-between px-3 py-2 rounded-lg transition-colors text-xs font-medium relative group cursor-pointer {{ $isAcademicActive ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }}">
                <div class="flex items-center gap-3">
                    <i data-lucide="book-open" class="menu-icon w-4 h-4 shrink-0 {{ $isAcademicActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-sky-500/80 dark:text-sky-400' }}"></i>
                    <span class="menu-text">Akademik</span>
                </div>
                <i data-lucide="chevron-right" class="chevron-icon w-3.5 h-3.5 transition-transform duration-200 shrink-0"
                    :style="openAcademic ? 'transform: rotate(90deg);' : ''" @if($isAcademicActive) style="transform: rotate(90deg);" @endif></i>
                <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                    Akademik & Pembelajaran
                </span>
            </button>

            <div x-show="openAcademic" x-collapse
                @if(!$isAcademicActive) style="display: none;" x-cloak @endif
                class="sidebar-dropdown-content mt-1 ml-4 pl-2.5 border-l border-slate-200 dark:border-slate-800 space-y-0.5">
                @if(!$isExecutive)
                <a href="{{ route('academic-years.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('academic-years.*', 'semesters.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Tahun Ajaran & Semester</span>
                </a>
                <a href="{{ route('class-levels.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('class-levels.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Tingkat Kelas</span>
                </a>
                @endif
                <a href="{{ route('classrooms.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('classrooms.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Rombongan Belajar</span>
                </a>
                <a href="{{ route('subjects.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('subjects.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Mata Pelajaran</span>
                </a>
                <a href="{{ route('student-reports.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('student-reports.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Rekapitulasi Rombel</span>
                </a>

                @if($canAccessRapor)
                <a href="{{ $raporLink }}" target="_blank"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-colors group/link">
                    <span class="flex items-center gap-1.5">
                        <span>E-Rapor</span>
                        <i data-lucide="external-link" class="w-3 h-3 text-slate-400 opacity-60 group-hover/link:opacity-100 transition-opacity"></i>
                    </span>
                </a>
                @endif

                @if($canAccessCbt)
                <a href="{{ $cbtLink }}" target="_blank"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-colors group/link">
                    <span class="flex items-center gap-1.5">
                        <span>CBT</span>
                        <i data-lucide="external-link" class="w-3 h-3 text-slate-400 opacity-60 group-hover/link:opacity-100 transition-opacity"></i>
                    </span>
                </a>
                @endif
            </div>
        </div>

        <!-- ==========================================
             2. KATEGORI: KESISWAAN
             ========================================== -->
        @php
            $isStudentActive = Request::routeIs(
                'students.*', 'student-attendances.*', 'promotions.*', 
                'alumni.*', 'student-archives.*', 'spmb.candidates.*'
            );
        @endphp
        <div x-data="{ openStudent: {{ $isStudentActive ? 'true' : 'false' }} }">
            <button @click="openStudent = !openStudent"
                class="menu-item w-full flex items-center justify-between px-3 py-2 rounded-lg transition-colors text-xs font-medium relative group cursor-pointer {{ $isStudentActive ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }}">
                <div class="flex items-center gap-3">
                    <i data-lucide="users" class="menu-icon w-4 h-4 shrink-0 {{ $isStudentActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-blue-500/80 dark:text-blue-400' }}"></i>
                    <span class="menu-text">Kesiswaan</span>
                </div>
                <i data-lucide="chevron-right" class="chevron-icon w-3.5 h-3.5 transition-transform duration-200 shrink-0"
                    :style="openStudent ? 'transform: rotate(90deg);' : ''" @if($isStudentActive) style="transform: rotate(90deg);" @endif></i>
                <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                    Kesiswaan
                </span>
            </button>

            <div x-show="openStudent" x-collapse
                @if(!$isStudentActive) style="display: none;" x-cloak @endif
                class="sidebar-dropdown-content mt-1 ml-4 pl-2.5 border-l border-slate-200 dark:border-slate-800 space-y-0.5">
                <a href="{{ route('students.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('students.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Data Siswa Aktif</span>
                </a>
                <a href="{{ route('student-attendances.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('student-attendances.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Presensi Siswa</span>
                </a>
                <a href="{{ route('promotions.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('promotions.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Kenaikan & Kelulusan</span>
                </a>
                <a href="{{ route('alumni.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('alumni.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Buku Induk Alumni</span>
                </a>
                @if(!$isExecutive)
                <a href="{{ route('student-archives.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('student-archives.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Arsip Dokumen Siswa</span>
                </a>
                @endif
                <a href="{{ route('spmb.candidates.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('spmb.candidates.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span class="flex items-center gap-1.5">
                        <span>Pendaftar SPMB</span>
                    </span>
                    <span class="text-[9px] uppercase tracking-wider font-semibold px-1 py-0.5 rounded bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50 leading-none">Baru</span>
                </a>
            </div>
        </div>

        <!-- ==========================================
             3. KATEGORI: MANAJEMEN GURU
             ========================================== -->
        @php
            $isTeacherMgmtActive = Request::routeIs(
                'homeroom-assignments.*', 'teacher-documents.*', 'teachers.*', 'picket-schedules.admin'
            );
        @endphp
        <div x-data="{ openTeacherMgmt: {{ $isTeacherMgmtActive ? 'true' : 'false' }} }">
            <button @click="openTeacherMgmt = !openTeacherMgmt"
                class="menu-item w-full flex items-center justify-between px-3 py-2 rounded-lg transition-colors text-xs font-medium relative group cursor-pointer {{ $isTeacherMgmtActive ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }}">
                <div class="flex items-center gap-3">
                    <i data-lucide="graduation-cap" class="menu-icon w-4 h-4 shrink-0 {{ $isTeacherMgmtActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-indigo-500/80 dark:text-indigo-400' }}"></i>
                    <span class="menu-text">Manajemen Guru</span>
                </div>
                <i data-lucide="chevron-right" class="chevron-icon w-3.5 h-3.5 transition-transform duration-200 shrink-0"
                    :style="openTeacherMgmt ? 'transform: rotate(90deg);' : ''" @if($isTeacherMgmtActive) style="transform: rotate(90deg);" @endif></i>
                <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                    Manajemen Guru & Pendidik
                </span>
            </button>

            <div x-show="openTeacherMgmt" x-collapse
                @if(!$isTeacherMgmtActive) style="display: none;" x-cloak @endif
                class="sidebar-dropdown-content mt-1 ml-4 pl-2.5 border-l border-slate-200 dark:border-slate-800 space-y-0.5">
                <a href="{{ route('homeroom-assignments.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('homeroom-assignments.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Penugasan Guru (Formasi)</span>
                </a>
                <a href="{{ route('teacher-documents.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('teacher-documents.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Berkas Dokumen Guru</span>
                </a>
                <a href="{{ route('teachers.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('teachers.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Kualifikasi Guru</span>
                </a>
                <a href="{{ route('picket-schedules.admin') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('picket-schedules.admin') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Kelola Jadwal Piket</span>
                </a>
            </div>
        </div>

        <!-- ==========================================
             4. KATEGORI: KEPEGAWAIAN (SDM)
             ========================================== -->
        @php
            $isHrdActive = Request::routeIs(
                'employees.*', 'attendances.*', 'leaves.*', 'payslips.*', 
                'bonus-reports.*', 'attendance-percentage-reports.*', 
                'employee-types.*', 'leave-types.*'
            );
        @endphp
        <div x-data="{ openHrd: {{ $isHrdActive ? 'true' : 'false' }} }">
            <button @click="openHrd = !openHrd"
                class="menu-item w-full flex items-center justify-between px-3 py-2 rounded-lg transition-colors text-xs font-medium relative group cursor-pointer {{ $isHrdActive ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }}">
                <div class="flex items-center gap-3">
                    <i data-lucide="briefcase" class="menu-icon w-4 h-4 shrink-0 {{ $isHrdActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-amber-500/80 dark:text-amber-400' }}"></i>
                    <span class="menu-text">Kepegawaian (SDM)</span>
                </div>
                <i data-lucide="chevron-right" class="chevron-icon w-3.5 h-3.5 transition-transform duration-200 shrink-0"
                    :style="openHrd ? 'transform: rotate(90deg);' : ''" @if($isHrdActive) style="transform: rotate(90deg);" @endif></i>
                <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                    Kepegawaian & SDM
                </span>
            </button>

            <div x-show="openHrd" x-collapse
                @if(!$isHrdActive) style="display: none;" x-cloak @endif
                class="sidebar-dropdown-content mt-1 ml-4 pl-2.5 border-l border-slate-200 dark:border-slate-800 space-y-0.5">
                <a href="{{ route('employees.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('employees.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Data Pegawai & Guru</span>
                </a>
                <a href="{{ route('attendances.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('attendances.index') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Data Kehadiran</span>
                </a>
                <a href="{{ route('leaves.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('leaves.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Perizinan & Cuti</span>
                </a>
                <a href="{{ route('payslips.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('payslips.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Slip Gaji</span>
                </a>
                <a href="{{ route('bonus-reports.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('bonus-reports.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Uang Kehadiran</span>
                </a>
                <a href="{{ route('attendance-percentage-reports.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('attendance-percentage-reports.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Persentase Kehadiran</span>
                </a>
                @if(!$isExecutive)
                <a href="{{ route('employee-types.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('employee-types.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Tipe Pegawai</span>
                </a>
                <a href="{{ route('leave-types.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('leave-types.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Tipe Izin & Cuti</span>
                </a>
                @endif
            </div>
        </div>

        <!-- ==========================================
             5. KATEGORI: LAYANAN & INFORMASI
             ========================================== -->
        @php
            $isServicesActive = Request::routeIs('guest-books.*', 'announcements.*');
        @endphp
        <div x-data="{ openServices: {{ $isServicesActive ? 'true' : 'false' }} }">
            <button @click="openServices = !openServices"
                class="menu-item w-full flex items-center justify-between px-3 py-2 rounded-lg transition-colors text-xs font-medium relative group cursor-pointer {{ $isServicesActive ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }}">
                <div class="flex items-center gap-3">
                    <i data-lucide="building-2" class="menu-icon w-4 h-4 shrink-0 {{ $isServicesActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-teal-500/80 dark:text-teal-400' }}"></i>
                    <span class="menu-text">Layanan & Informasi</span>
                </div>
                <i data-lucide="chevron-right" class="chevron-icon w-3.5 h-3.5 transition-transform duration-200 shrink-0"
                    :style="openServices ? 'transform: rotate(90deg);' : ''" @if($isServicesActive) style="transform: rotate(90deg);" @endif></i>
                <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                    Layanan & Informasi Sekolah
                </span>
            </button>

            <div x-show="openServices" x-collapse
                @if(!$isServicesActive) style="display: none;" x-cloak @endif
                class="sidebar-dropdown-content mt-1 ml-4 pl-2.5 border-l border-slate-200 dark:border-slate-800 space-y-0.5">
                <a href="{{ route('guest-books.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('guest-books.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Buku Tamu Sekolah</span>
                </a>
                <a href="{{ route('announcements.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('announcements.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Papan Pengumuman</span>
                </a>
            </div>
        </div>

        @elseif($curUser && $curUser->isTeacher())
        <!-- ==========================================
             PORTAL PEMBELAJARAN (NON-ADMIN GURU / WALI KELAS)
             ========================================== -->
        @php
            $isTeacherPortalActive = Request::routeIs('student-attendances.*', 'teacher-documents.*');
        @endphp
        <div x-data="{ openTeacherPortal: {{ $isTeacherPortalActive ? 'true' : 'false' }} }">
            <button @click="openTeacherPortal = !openTeacherPortal"
                class="menu-item w-full flex items-center justify-between px-3 py-2 rounded-lg transition-colors text-xs font-medium relative group cursor-pointer {{ $isTeacherPortalActive ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }}">
                <div class="flex items-center gap-3">
                    <i data-lucide="graduation-cap" class="menu-icon w-4 h-4 shrink-0 {{ $isTeacherPortalActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-indigo-500/80 dark:text-indigo-400' }}"></i>
                    <span class="menu-text">Pembelajaran</span>
                </div>
                <i data-lucide="chevron-right" class="chevron-icon w-3.5 h-3.5 transition-transform duration-200 shrink-0"
                    :style="openTeacherPortal ? 'transform: rotate(90deg);' : ''" @if($isTeacherPortalActive) style="transform: rotate(90deg);" @endif></i>
                <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                    Pembelajaran
                </span>
            </button>

            <div x-show="openTeacherPortal" x-collapse
                @if(!$isTeacherPortalActive) style="display: none;" x-cloak @endif
                class="sidebar-dropdown-content mt-1 ml-4 pl-2.5 border-l border-slate-200 dark:border-slate-800 space-y-0.5">
                <a href="{{ route('student-attendances.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('student-attendances.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Presensi Siswa</span>
                </a>

                <a href="{{ route('teacher-documents.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('teacher-documents.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Berkas Dokumen Guru</span>
                </a>

                @if($canAccessRapor)
                <a href="{{ $raporLink }}" target="_blank"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-colors group/link">
                    <span class="flex items-center gap-1.5">
                        <span>E-Rapor</span>
                        <i data-lucide="external-link" class="w-3 h-3 text-slate-400 opacity-60 group-hover/link:opacity-100 transition-opacity"></i>
                    </span>
                </a>
                @endif

                @if($canAccessCbt)
                <a href="{{ $cbtLink }}" target="_blank"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-colors group/link">
                    <span class="flex items-center gap-1.5">
                        <span>CBT</span>
                        <i data-lucide="external-link" class="w-3 h-3 text-slate-400 opacity-60 group-hover/link:opacity-100 transition-opacity"></i>
                    </span>
                </a>
                @endif
            </div>
        </div>
        @endif

        @if(!$isSuperAdmin && ($empId || !$isAdmin))
        <!-- ==========================================
             SEPARATOR SEBELUM AKSES PRIBADI
             ========================================== -->
        <div class="pt-2 border-t border-slate-100 dark:border-slate-800/80"></div>

        <!-- ==========================================
             AKSES PRIBADI SAYA (Admin SD & Pegawai)
             ========================================== -->
        @php
            $isPersonalActive = Request::routeIs('my-leaves.*', 'picket-schedules.index') || (!$isAdmin && Request::routeIs('attendances.index', 'payslips.*'));
        @endphp
        <div x-data="{ openPersonal: {{ $isPersonalActive ? 'true' : 'false' }} }">
            <button @click="openPersonal = !openPersonal"
                class="menu-item w-full flex items-center justify-between px-3 py-2 rounded-lg transition-colors text-xs font-medium relative group cursor-pointer {{ $isPersonalActive ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }}">
                <div class="flex items-center gap-3">
                    <i data-lucide="user-check" class="menu-icon w-4 h-4 shrink-0 {{ $isPersonalActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-violet-500/80 dark:text-violet-400' }}"></i>
                    <span class="menu-text">Akses Pribadi Saya</span>
                </div>
                <i data-lucide="chevron-right" class="chevron-icon w-3.5 h-3.5 transition-transform duration-200 shrink-0"
                    :style="openPersonal ? 'transform: rotate(90deg);' : ''" @if($isPersonalActive) style="transform: rotate(90deg);" @endif></i>
                <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                    Akses Pribadi & Pengajuan
                </span>
            </button>

            <div x-show="openPersonal" x-collapse
                @if(!$isPersonalActive) style="display: none;" x-cloak @endif
                class="sidebar-dropdown-content mt-1 ml-4 pl-2.5 border-l border-slate-200 dark:border-slate-800 space-y-0.5">
                
                {{-- Presensi Mandiri hanya untuk staf/guru non-admin (Admin SD sudah punya di Kepegawaian) --}}
                @if(!$isAdmin)
                <a href="{{ route('attendances.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('attendances.index') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Presensi Mandiri</span>
                </a>
                @endif

                <!-- Pengajuan Izin Saya -->
                @if($empId)
                <a href="{{ route('my-leaves.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('my-leaves.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Pengajuan Izin Saya</span>
                </a>
                @endif

                {{-- Slip Gaji hanya untuk staf/guru non-admin (Admin SD sudah punya di Kepegawaian) --}}
                @if(!$isAdmin)
                <a href="{{ route('payslips.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('payslips.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Slip Gaji</span>
                </a>
                @endif

                <!-- Jadwal Piket Saya -->
                @if($showPicketMenu)
                <a href="{{ route('picket-schedules.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('picket-schedules.index') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Jadwal Piket Saya</span>
                </a>
                @endif

                @if(!$isAdmin)
                <a href="{{ route('announcements.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('announcements.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Pengumuman</span>
                </a>
                @endif
            </div>
        </div>
        @endif

        @if($isSuperAdmin)
        <!-- ==========================================
             7. KATEGORI: PENGATURAN SISTEM (Super Admin)
             ========================================== -->
        @php
            $isSystemActive = Request::routeIs('users.*', 'system-logs.*', 'settings');
        @endphp
        <div x-data="{ openSystem: {{ $isSystemActive ? 'true' : 'false' }} }">
            <button @click="openSystem = !openSystem"
                class="menu-item w-full flex items-center justify-between px-3 py-2 rounded-lg transition-colors text-xs font-medium relative group cursor-pointer {{ $isSystemActive ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/50' }}">
                <div class="flex items-center gap-3">
                    <i data-lucide="settings-2" class="menu-icon w-4 h-4 shrink-0 {{ $isSystemActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400' }}"></i>
                    <span class="menu-text">Pengaturan Sistem</span>
                </div>
                <i data-lucide="chevron-right" class="chevron-icon w-3.5 h-3.5 transition-transform duration-200 shrink-0"
                    :style="openSystem ? 'transform: rotate(90deg);' : ''" @if($isSystemActive) style="transform: rotate(90deg);" @endif></i>
                <span class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-50 dark:text-slate-100 text-xs font-semibold rounded-md shadow-md opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left duration-100 pointer-events-none whitespace-nowrap z-50">
                    Pengaturan Sistem & Hak Akses
                </span>
            </button>

            <div x-show="openSystem" x-collapse
                @if(!$isSystemActive) style="display: none;" x-cloak @endif
                class="sidebar-dropdown-content mt-1 ml-4 pl-2.5 border-l border-slate-200 dark:border-slate-800 space-y-0.5">
                <a href="{{ route('users.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('users.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Setting User</span>
                </a>
                <a href="{{ route('settings') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('settings') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Setting App</span>
                </a>
                <a href="{{ route('system-logs.index') }}"
                    class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-xs font-medium transition-colors {{ Request::routeIs('system-logs.*') ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/40 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-900/40' }}">
                    <span>Log System</span>
                </a>
            </div>
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

            @if($empId)
            <!-- Employee Profile -->
            <a href="{{ route('my-employee-profile.edit') }}"
                class="flex items-center gap-2 px-3 py-2 text-xs {{ Request::routeIs('my-employee-profile.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-900/50 hover:text-slate-900 dark:hover:text-slate-100' }} transition-colors">
                <i data-lucide="user" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                <span>Profil Pegawai</span>
            </a>
            @endif

            <!-- Account Info -->
            <a href="{{ route('profile.edit') }}"
                class="flex items-center gap-2 px-3 py-2 text-xs {{ Request::routeIs('profile.*') ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-50 font-medium' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-900/50 hover:text-slate-900 dark:hover:text-slate-100' }} transition-colors">
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
                    <p class="text-[10px] font-semibold text-indigo-600 dark:text-indigo-400 truncate mt-1">
                        {{ Auth::user()->getRoleDisplayName() }}
                    </p>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 truncate leading-tight">{{ Auth::user()->email }}</p>
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

    <!-- Preserve Sidebar Scroll Position across page navigations -->
    <script>
        (function() {
            const nav = document.getElementById('sidebar-nav-container');
            if (!nav) return;
            const savedScroll = sessionStorage.getItem('sans_sidebar_scroll');
            if (savedScroll !== null) {
                nav.scrollTop = parseInt(savedScroll, 10);
            }
            let scrollTimer;
            nav.addEventListener('scroll', function() {
                clearTimeout(scrollTimer);
                scrollTimer = setTimeout(() => {
                    sessionStorage.setItem('sans_sidebar_scroll', nav.scrollTop);
                }, 50);
            }, { passive: true });
        })();
    </script>
</aside>
