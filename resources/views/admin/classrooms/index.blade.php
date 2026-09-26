<x-admin-layout>
    <div class="p-6 space-y-6" x-data="rombelApp()">

        <!-- GREETING / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <div class="flex items-center gap-2.5">
                    <div class="p-2 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-xl border border-emerald-500/20">
                        <i data-lucide="university" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 flex items-center gap-2">
                            Rombongan Belajar
                            <span class="text-xs px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 font-semibold border border-emerald-200 dark:border-emerald-800">
                                {{ setting('app_name', 'SD Anak Saleh') }}
                            </span>
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Kelola rombongan belajar, alokasi wali kelas, dan kuota kapasitas kelas SD.</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <button type="button" @click="openCreateModal()"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all duration-100 cursor-pointer">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    Tambah Rombel
                </button>
            </div>
        </section>

        <!-- STATS CARDS GRID -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Stat 1: Total Rombel -->
            <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Rombel</p>
                        <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                            {{ number_format($stats['total_classrooms']) }}
                        </h3>
                    </div>
                    <div class="p-2.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-xl border border-emerald-100 dark:border-emerald-900/50">
                        <i data-lucide="layout-grid" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="mt-3 text-[11px] text-slate-500 dark:text-slate-400">
                    Kelas aktif terdaftar di SD
                </div>
            </div>

            <!-- Stat 2: Total Kapasitas Kuota -->
            <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Kapasitas</p>
                        <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                            {{ number_format($stats['total_capacity']) }}
                        </h3>
                    </div>
                    <div class="p-2.5 bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 rounded-xl border border-blue-100 dark:border-blue-900/50">
                        <i data-lucide="layers" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="mt-3 text-[11px] text-slate-500 dark:text-slate-400">
                    Maksimal daya tampung seluruh rombel
                </div>
            </div>

            <!-- Stat 3: Siswa Terisi -->
            <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Siswa Terisi</p>
                        <h3 class="text-2xl font-bold tracking-tight text-indigo-600 dark:text-indigo-400 mt-1">
                            {{ number_format($stats['total_enrolled']) }}
                        </h3>
                    </div>
                    <div class="p-2.5 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 rounded-xl border border-indigo-100 dark:border-indigo-900/50">
                        <i data-lucide="users" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="mt-3 text-[11px] text-slate-500 dark:text-slate-400">
                    Siswa aktif yang sudah masuk rombel
                </div>
            </div>

            <!-- Stat 4: Persentase Keterisian -->
            <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tingkat Keterisian</p>
                        <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                            {{ $stats['occupancy_rate'] }}%
                        </h3>
                    </div>
                    <div class="p-2.5 bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 rounded-xl border border-amber-100 dark:border-amber-900/50">
                        <i data-lucide="pie-chart" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="mt-3 text-[11px] text-slate-500 dark:text-slate-400">
                    Rasio pemenuhan kuota rombel
                </div>
            </div>
        </section>

        <!-- SEARCH & FILTERS -->
        <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-xs w-full">
            <form method="GET" action="{{ route('classrooms.index') }}" class="flex flex-col md:flex-row gap-3 items-stretch md:items-center justify-between">
                <!-- Search Box -->
                <div class="relative w-full md:max-w-md">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 dark:text-slate-500"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama rombel..."
                        style="padding-left: 2.25rem;"
                        class="w-full h-9 pr-4 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-slate-900 dark:text-slate-50 placeholder-slate-400 dark:placeholder-slate-500 transition-all shadow-inner">
                </div>

                <!-- Filter Toolbar -->
                <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                    <!-- Filter Tahun Pelajaran -->
                    <select name="academic_year_id" onchange="this.form.submit()"
                        class="h-9 px-3 text-xs font-medium bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 cursor-pointer shadow-xs">
                        @foreach($uniqueAcademicYears as $ay)
                            <option value="{{ $ay->id }}" {{ ($selectedYear && $selectedYear->name === $ay->name) || $selectedYearId == $ay->id ? 'selected' : '' }}>
                                {{ $ay->name }} {{ $ay->has_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Filter Tingkat Kelas -->
                    <select name="class_level_id" onchange="this.form.submit()"
                        class="h-9 px-3 text-xs font-medium bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 cursor-pointer shadow-xs">
                        <option value="all">Semua Tingkat</option>
                        @foreach($classLevels as $lvl)
                            <option value="{{ $lvl->id }}" {{ request('class_level_id') == $lvl->id ? 'selected' : '' }}>
                                {{ $lvl->name }}
                            </option>
                        @endforeach
                    </select>

                    @if(request()->hasAny(['search', 'class_level_id']) || (request()->filled('academic_year_id') && request('academic_year_id') != ($uniqueAcademicYears->firstWhere('has_active', true)?->id ?? '')))
                        <a href="{{ route('classrooms.index') }}" 
                            class="h-9 px-3 inline-flex items-center justify-center text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 rounded-lg border border-slate-200 dark:border-slate-700 transition-colors"
                            title="Reset Filter">
                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                        </a>
                    @endif
                </div>
            </form>
        </section>

        <!-- ROMBEL TABLE -->
        <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs overflow-hidden w-full">
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="flex items-center gap-2.5">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        <i data-lucide="layers" class="w-4 h-4 text-emerald-600"></i>
                        Daftar Rombongan Belajar (Rombel)
                    </h3>
                    @if($selectedYear)
                        @php
                            $isCurrentYearActive = $academicYears->where('name', $selectedYear->name)->contains('is_active', true);
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $isCurrentYearActive ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700' }}">
                            <i data-lucide="calendar" class="w-3 h-3"></i>
                            {{ $selectedYear->name }} {{ $isCurrentYearActive ? '(Aktif)' : '' }}
                        </span>
                    @endif
                </div>
                <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                    Total: <strong class="text-slate-800 dark:text-slate-200">{{ $classrooms->count() }}</strong> rombel
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-900/50">
                            <th class="px-4 py-3.5 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-12 whitespace-nowrap">No</th>
                            <th class="px-4 py-3.5 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-28 whitespace-nowrap">Tingkat</th>
                            <th class="px-4 py-3.5 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Nama Rombel</th>
                            <th class="px-4 py-3.5 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-44 whitespace-nowrap">Tahun Pelajaran</th>
                            <th class="px-4 py-3.5 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Wali Kelas</th>
                            <th class="px-4 py-3.5 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-48 whitespace-nowrap">Kapasitas & Kuota</th>
                            <th class="px-4 py-3.5 text-center text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-32 whitespace-nowrap">Daftar Siswa</th>
                            <th class="px-4 py-3.5 text-right text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-24 whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        @forelse($classrooms as $index => $c)
                            @php
                                $filled = $c->active_students_count;
                                $cap = $c->capacity ?: 28;
                                $pct = min(100, round(($filled / $cap) * 100));
                            @endphp
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors group">
                                <td class="px-4 py-3 text-slate-400 font-mono text-[11px] whitespace-nowrap">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                        {{ $c->classLevel->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="font-bold text-slate-900 dark:text-slate-100 text-xs tracking-tight">
                                        {{ $c->full_name }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded text-xs font-semibold bg-indigo-50/70 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-400 border border-indigo-200/80 dark:border-indigo-800 whitespace-nowrap">
                                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-indigo-500 shrink-0"></i>
                                        <span>{{ $c->academicYear->name ?? '-' }}</span>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($c->homeroomTeacher)
                                        <div class="flex items-center gap-2.5">
                                            @if($c->homeroomTeacher->photo_url)
                                                <img src="{{ $c->homeroomTeacher->photo_url }}" alt="{{ $c->homeroomTeacher->name }}" class="w-8 h-8 rounded-full object-cover shrink-0 ring-1 ring-slate-200 dark:ring-slate-700 shadow-xs">
                                            @else
                                                <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs">
                                                    {{ substr($c->homeroomTeacher->raw_name, 0, 1) }}
                                                </div>
                                            @endif
                                            <div class="overflow-hidden">
                                                <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate">
                                                    {{ $c->homeroomTeacher->name }}
                                                </p>
                                                <p class="text-[10px] text-slate-400 truncate font-mono">
                                                    {{ $c->homeroomTeacher->phone ?: ($c->homeroomTeacher->nip ?: 'Wali Kelas') }}
                                                </p>
                                            </div>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[11px] text-slate-400 italic">
                                            <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                            Belum ditentukan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="w-full max-w-xs">
                                        <div class="flex justify-between items-center text-[11px] mb-1">
                                            <span class="font-bold text-slate-800 dark:text-slate-200">
                                                <span class="text-indigo-600 dark:text-indigo-400 font-bold">{{ $filled }}</span> / {{ $cap }} Siswa
                                            </span>
                                            <span class="text-[10px] font-semibold text-slate-400">{{ $pct }}%</span>
                                        </div>
                                        <div class="w-full h-1.5 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                                            <div class="h-full rounded-full transition-all duration-300 {{ $pct >= 90 ? 'bg-rose-500' : ($pct >= 70 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                                style="width: {{ $pct }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" @click="openStudentsModal({{ $c->id }})"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-950/40 dark:hover:text-indigo-400 rounded-lg text-xs font-semibold text-slate-700 dark:text-slate-300 transition-colors cursor-pointer"
                                        title="Lihat Daftar Siswa di Rombel">
                                        <i data-lucide="users" class="w-3.5 h-3.5 text-indigo-500"></i>
                                        <span>{{ $filled }} Siswa</span>
                                    </button>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="button" @click="openEditModal({{ $c->id }}, '{{ addslashes($c->name) }}', '{{ $c->code }}', {{ $c->class_level_id }}, {{ $uniqueAcademicYears->firstWhere('name', $c->academicYear?->name)?->id ?? ($c->academic_year_id ?? 'null') }}, {{ $c->homeroom_teacher_id ?? 'null' }}, {{ $c->capacity }})"
                                            class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 rounded-lg transition-colors cursor-pointer"
                                            title="Edit Rombel">
                                            <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                        <button type="button" @click="confirmDeleteRombel({{ $c->id }}, '{{ addslashes($c->name) }}')"
                                            class="p-1.5 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 rounded-lg transition-colors cursor-pointer"
                                            title="Hapus Rombel">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center">
                                    <div class="max-w-sm mx-auto flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 flex items-center justify-center text-emerald-500 mb-3 border border-emerald-100 dark:border-emerald-900/50">
                                            <i data-lucide="university" class="w-6 h-6"></i>
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">Belum Ada Rombongan Belajar</h4>
                                        <p class="text-xs text-slate-400 mt-1 mb-4 text-center">
                                            Silakan tambahkan rombongan belajar baru untuk mengelompokkan siswa SD.
                                        </p>
                                        <button type="button" @click="openCreateModal()" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-xs cursor-pointer">
                                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                            Tambah Rombel Sekarang
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- MODAL DAFTAR SISWA DI ROMBEL -->
        <div x-show="studentsModalOpen" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4" style="display: none; margin-top: 0px !important; z-index: 9999; background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);">
            <div @click.outside="studentsModalOpen = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl w-full max-w-3xl max-h-[85vh] overflow-hidden shadow-2xl flex flex-col">
                
                <!-- Modal Header -->
                <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between sticky top-0 bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm z-10">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-200 dark:border-indigo-800 text-indigo-600 dark:text-indigo-400 font-bold text-sm flex items-center justify-center font-mono">
                            <span x-text="selectedClassroom?.code || 'R'"></span>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-slate-50 flex items-center gap-2">
                                <span x-text="selectedClassroom?.full_name || (selectedClassroom?.code ? selectedClassroom.code + ' ' + selectedClassroom.name : 'Rombel')"></span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-400" x-text="classroomStudents.length + ' Siswa'"></span>
                            </h3>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <template x-if="selectedClassroom?.homeroom_teacher?.photo_url">
                                    <img :src="selectedClassroom.homeroom_teacher.photo_url" class="w-4 h-4 rounded-full object-cover ring-1 ring-slate-200">
                                </template>
                                <p class="text-xs text-slate-400">Wali Kelas: <span class="font-semibold text-slate-700 dark:text-slate-300" x-text="selectedClassroom?.homeroom_teacher?.name || 'Belum Ditentukan'"></span></p>
                            </div>
                        </div>
                    </div>
                    <button type="button" @click="studentsModalOpen = false" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Table Container -->
                <div class="p-4 flex-1 overflow-y-auto text-xs">
                    <table class="w-full text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-900/50">
                                <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider w-10">No</th>
                                <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider w-24">NIS</th>
                                <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Nama Siswa</th>
                                <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider w-20">L/P</th>
                                <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider w-36">Kontak Ortu</th>
                                <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider w-20">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                            <template x-for="(s, index) in classroomStudents" :key="s.id">
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="px-4 py-2.5 text-slate-400 font-mono text-[11px]" x-text="index + 1"></td>
                                    <td class="px-4 py-2.5 font-mono font-bold text-indigo-600 dark:text-indigo-400" x-text="s.nis"></td>
                                    <td class="px-4 py-2.5">
                                        <div class="flex items-center gap-2.5">
                                            <template x-if="s.student_photo_url">
                                                <img :src="s.student_photo_url" class="w-7 h-7 rounded-full object-cover">
                                            </template>
                                            <template x-if="!s.student_photo_url">
                                                <div class="w-7 h-7 rounded-full bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 font-bold text-xs flex items-center justify-center" x-text="s.full_name ? s.full_name.charAt(0) : 'S'"></div>
                                            </template>
                                            <span class="font-bold text-slate-800 dark:text-slate-200" x-text="s.full_name"></span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2.5 text-slate-600 dark:text-slate-300" x-text="s.gender === 'L' ? 'Laki-laki' : (s.gender === 'P' ? 'Perempuan' : s.gender)"></td>
                                    <td class="px-4 py-2.5 font-mono text-slate-600 dark:text-slate-300" x-text="s.parent_phone || '-'"></td>
                                    <td class="px-4 py-2.5">
                                        <span class="inline-flex items-center px-2 py-0.2 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400" x-text="s.status"></span>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="classroomStudents.length === 0">
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                                        Belum ada siswa yang ditempatkan pada rombel ini.
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Modal Footer -->
                <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex justify-end">
                    <button type="button" @click="studentsModalOpen = false" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL TAMBAH / EDIT ROMBEL -->
        <div x-show="formModalOpen" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4" style="display: none; margin-top: 0px !important; z-index: 9999; background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);">
            <div @click.outside="formModalOpen = false" 
                class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl w-full max-w-xl shadow-2xl flex flex-col relative overflow-visible">
                
                <form @submit.prevent="submitForm">
                    <!-- Modal Header -->
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800/80 flex items-start justify-between gap-4">
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200/80 dark:border-emerald-800/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="layers" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-slate-50 tracking-tight" x-text="isEdit ? 'Edit Rombongan Belajar' : 'Tambah Rombel Baru'"></h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola tingkat, kode kelas Dapodik, julukan rombel, dan wali kelas.</p>
                            </div>
                        </div>
                        <button type="button" @click="formModalOpen = false" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors shrink-0">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 space-y-5 text-xs">
                        
                        <!-- Grid 1: Tingkat & Tahun Pelajaran -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Tingkat / Level <span class="text-rose-500">*</span>
                                </label>
                                <select x-model="formData.class_level_id" required
                                    class="w-full h-10 px-3.5 text-xs bg-slate-50/50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-slate-900 dark:text-slate-100 cursor-pointer transition-all">
                                    <option value="">Pilih Level...</option>
                                    @foreach($classLevels as $lvl)
                                        <option value="{{ $lvl->id }}">{{ $lvl->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Tahun Pelajaran <span class="text-rose-500">*</span>
                                </label>
                                <select x-model="formData.academic_year_id" required
                                    class="w-full h-10 px-3.5 text-xs bg-slate-50/50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-slate-900 dark:text-slate-100 cursor-pointer transition-all">
                                    <option value="">Pilih Tahun Pelajaran...</option>
                                    @foreach($uniqueAcademicYears as $ay)
                                        <option value="{{ $ay->id }}">{{ $ay->name }} {{ $ay->has_active ? '(Aktif)' : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Grid 2: Kode Kelas (Dapodik) & Nama Kelas / Julukan -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Kelas / Grade <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" x-model="formData.code" required placeholder="Contoh: 1A, 2B, 6D"
                                    class="w-full h-10 px-3.5 text-xs bg-slate-50/50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-slate-900 dark:text-slate-100 font-mono uppercase tracking-wider transition-all">
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Nama Kelas / Julukan <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" x-model="formData.name" required placeholder="Contoh: Berlian, Mutiara, Ibnu Sina"
                                    class="w-full h-10 px-3.5 text-xs bg-slate-50/50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-slate-900 dark:text-slate-100 transition-all">
                            </div>
                        </div>

                        <!-- Live Preview Box: Sleek, integrated, comfortable -->
                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60 flex items-center justify-between transition-all">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-xs shrink-0 font-mono"
                                    x-text="formData.code || 'R'">
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider block leading-none">Pratinjau Nama Resmi Rombel</span>
                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-1 block truncate" 
                                        x-text="(formData.code ? formData.code + ' ' : '') + (formData.name || 'Nama Rombel')"></span>
                                </div>
                            </div>
                            <span class="text-[11px] font-medium text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200/60 dark:border-emerald-800/50 px-2 py-0.5 rounded-full shrink-0">
                                Otomatis
                            </span>
                        </div>

                        <!-- Grid 3: Wali Kelas (Combobox) & Kapasitas Kuota -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Searchable Select: Wali Kelas -->
                            <div class="relative" @click.outside="teacherDropdownOpen = false" @keydown.escape.window="teacherDropdownOpen = false">
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Wali Kelas</label>
                                
                                <!-- Combobox Trigger Button -->
                                <div @click="teacherDropdownOpen = !teacherDropdownOpen; if(teacherDropdownOpen) $nextTick(() => { $refs.teacherSearchInput.focus(); if (window.lucide) lucide.createIcons(); })"
                                    class="w-full h-10 px-3.5 text-xs bg-slate-50/50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl flex items-center justify-between cursor-pointer transition-all"
                                    :class="teacherDropdownOpen ? 'ring-2 ring-emerald-500/20 border-emerald-500 bg-white dark:bg-slate-900' : 'hover:border-slate-300 dark:hover:border-slate-600'">
                                    
                                    <div class="flex items-center gap-2 overflow-hidden min-w-0">
                                        <!-- Selected Teacher Preview -->
                                        <template x-if="selectedTeacher">
                                            <div class="flex items-center gap-2 truncate">
                                                <template x-if="selectedTeacher.photo_url">
                                                    <img :src="selectedTeacher.photo_url" class="w-5 h-5 rounded-full object-cover shrink-0 border border-slate-200 dark:border-slate-700" alt="">
                                                </template>
                                                <template x-if="!selectedTeacher.photo_url">
                                                    <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 flex items-center justify-center text-[10px] font-bold shrink-0">
                                                        <span x-text="(selectedTeacher.name || 'G').charAt(0)"></span>
                                                    </div>
                                                </template>
                                                <span class="font-semibold text-slate-800 dark:text-slate-100 truncate" x-text="selectedTeacher.name"></span>
                                            </div>
                                        </template>
                                        <template x-if="!selectedTeacher">
                                            <span class="text-slate-400 dark:text-slate-500">Pilih Wali Kelas...</span>
                                        </template>
                                    </div>

                                    <div class="flex items-center gap-1.5 shrink-0 ml-1.5">
                                        <!-- Clear Button (Without tooltip) -->
                                        <button type="button" x-show="formData.homeroom_teacher_id" @click.stop="clearTeacher()"
                                            class="w-5 h-5 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded transition-colors">
                                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                        </button>
                                        <!-- Dropdown Chevron -->
                                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200"
                                            :class="teacherDropdownOpen ? 'transform rotate-180 text-emerald-600' : ''"></i>
                                    </div>
                                </div>

                                <!-- Hidden Input for Form Submission -->
                                <input type="hidden" name="homeroom_teacher_id" :value="formData.homeroom_teacher_id">

                                <!-- Searchable Dropdown Popover (Positioned UPWARD with full space & smooth elevation) -->
                                <div x-show="teacherDropdownOpen" x-cloak
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute left-0 right-0 bottom-full mb-2 z-[100] bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/80 rounded-2xl shadow-2xl overflow-hidden text-xs">
                                    
                                    <!-- Search Input Bar at Top -->
                                    <div class="p-2.5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/90 dark:bg-slate-900/90">
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none text-slate-400">
                                                <i data-lucide="search" class="w-3.5 h-3.5"></i>
                                            </span>
                                            <input type="text" x-model="teacherSearch" x-ref="teacherSearchInput"
                                                @input="$nextTick(() => { if (window.lucide) lucide.createIcons(); })"
                                                placeholder="Ketik nama guru..."
                                                style="padding-left: 2rem;"
                                                class="w-full h-8.5 pr-3 text-xs bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 text-slate-900 dark:text-slate-100 placeholder-slate-400">
                                        </div>
                                    </div>

                                    <!-- Teachers Scrollable List -->
                                    <div class="max-h-52 overflow-y-auto divide-y divide-slate-50 dark:divide-slate-800/40 p-1.5">
                                        <!-- Option: Tanpa Wali -->
                                        <div @click="selectTeacher('')"
                                            class="flex items-center justify-between px-3 py-2 rounded-xl cursor-pointer transition-colors"
                                            :class="!formData.homeroom_teacher_id ? 'bg-emerald-50/80 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 font-semibold' : 'hover:bg-slate-50 dark:hover:bg-slate-800/60 text-slate-500 dark:text-slate-400'">
                                            <span class="italic text-[11px] flex items-center gap-1.5">
                                                <i data-lucide="user-minus" class="w-3.5 h-3.5 opacity-60"></i>
                                                Tanpa Wali Kelas
                                            </span>
                                            <template x-if="!formData.homeroom_teacher_id">
                                                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400"></i>
                                            </template>
                                        </div>

                                        <!-- List Teachers Loop -->
                                        <template x-for="teacher in filteredTeachers" :key="teacher.id">
                                            <div @click="selectTeacher(teacher.id)"
                                                class="flex items-center justify-between px-3 py-2 rounded-xl cursor-pointer transition-colors group"
                                                :class="formData.homeroom_teacher_id == teacher.id ? 'bg-emerald-50/80 dark:bg-emerald-950/50 text-emerald-800 dark:text-emerald-300 font-bold' : 'hover:bg-slate-50 dark:hover:bg-slate-800/60 text-slate-700 dark:text-slate-300'">
                                                
                                                <div class="flex items-center gap-2.5 min-w-0">
                                                    <!-- Photo or Initials Avatar -->
                                                    <template x-if="teacher.photo_url">
                                                        <img :src="teacher.photo_url" class="w-6 h-6 rounded-full object-cover shrink-0 border border-slate-200 dark:border-slate-700" alt="">
                                                    </template>
                                                    <template x-if="!teacher.photo_url">
                                                        <div class="w-6 h-6 rounded-full bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300 flex items-center justify-center text-[10px] font-bold shrink-0 group-hover:bg-emerald-100 group-hover:text-emerald-700 dark:group-hover:bg-emerald-900 dark:group-hover:text-emerald-300 transition-colors">
                                                            <span x-text="(teacher.name || 'G').charAt(0)"></span>
                                                        </div>
                                                    </template>
                                                    <div class="truncate">
                                                        <p class="truncate text-xs" x-text="teacher.name"></p>
                                                    </div>
                                                </div>

                                                <template x-if="formData.homeroom_teacher_id == teacher.id">
                                                    <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0 ml-2"></i>
                                                </template>
                                            </div>
                                        </template>

                                        <!-- Empty Search State -->
                                        <div x-show="filteredTeachers.length === 0" class="py-5 text-center text-xs text-slate-400 dark:text-slate-500">
                                            <i data-lucide="user-x" class="w-5 h-5 mx-auto mb-1 opacity-50"></i>
                                            Guru tidak ditemukan
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Kapasitas Kuota -->
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Kapasitas Kuota (Siswa) <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" x-model.number="formData.capacity" min="1" max="100" required
                                    class="w-full h-10 px-3.5 text-xs bg-slate-50/50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-slate-900 dark:text-slate-100 transition-all">
                                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">Standar kuota SD: 32 siswa per rombel</p>
                            </div>
                        </div>

                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800/80 bg-slate-50/60 dark:bg-slate-900/60 flex items-center justify-end gap-2.5 rounded-b-2xl">
                        <button type="button" @click="formModalOpen = false" 
                            class="h-9 px-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-semibold transition-colors cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" :disabled="saving" 
                            class="h-9 px-5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-2 cursor-pointer">
                            <span x-show="saving" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                            <i data-lucide="check" class="w-3.5 h-3.5" x-show="!saving"></i>
                            <span x-text="saving ? 'Menyimpan...' : (isEdit ? 'Simpan Perubahan' : 'Tambah Rombel')"></span>
                        </button>
                    </div>
                </form>

            </div>
        </div>

        <!-- MODAL KONFIRMASI IN-APP (Aman dari native alert/confirm loop) -->
        <div x-show="confirmModal.open" x-cloak style="display: none; margin-top: 0px !important; z-index: 9999;"
            class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
            @click.self="confirmModal.open = false"
            @keydown.escape.window="confirmModal.open = false">
            
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl w-full max-w-md overflow-hidden shadow-2xl flex flex-col p-6 text-center animate-in fade-in zoom-in-95 duration-150"
                @click.stop>
                
                <div class="w-12 h-12 rounded-full mx-auto flex items-center justify-center mb-4 bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400">
                    <i data-lucide="trash-2" class="w-6 h-6"></i>
                </div>

                <h3 class="text-base font-bold text-slate-900 dark:text-slate-50" x-text="confirmModal.title"></h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed" x-html="confirmModal.message"></p>

                <div class="mt-6 flex items-center justify-center gap-3">
                    <button type="button" @click="confirmModal.open = false" :disabled="confirmModal.loading"
                        class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="executeConfirmDelete()" :disabled="confirmModal.loading"
                        class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                        <span x-show="confirmModal.loading" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                        <span>Hapus Permanen</span>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- Alpine.js Application Logic for Rombongan Belajar -->
    <script>
        function rombelApp() {
            return {
                studentsModalOpen: false,
                formModalOpen: false,
                isEdit: false,
                saving: false,
                selectedClassroom: null,
                classroomStudents: [],
                teachersList: @json($teachers),
                teacherSearch: '',
                teacherDropdownOpen: false,

                get selectedTeacher() {
                    if (!this.formData.homeroom_teacher_id) return null;
                    return this.teachersList.find(t => t.id == this.formData.homeroom_teacher_id) || null;
                },

                get filteredTeachers() {
                    if (!this.teacherSearch) return this.teachersList;
                    const q = this.teacherSearch.toLowerCase();
                    return this.teachersList.filter(t => (t.name || '').toLowerCase().includes(q) || (t.position || '').toLowerCase().includes(q));
                },

                selectTeacher(id) {
                    this.formData.homeroom_teacher_id = id;
                    this.teacherDropdownOpen = false;
                    this.teacherSearch = '';
                    this.$nextTick(() => {
                        if (window.lucide) lucide.createIcons();
                    });
                },

                clearTeacher() {
                    this.formData.homeroom_teacher_id = '';
                    this.teacherSearch = '';
                    this.$nextTick(() => {
                        if (window.lucide) lucide.createIcons();
                    });
                },

                formData: {
                    id: null,
                    name: '',
                    code: '',
                    class_level_id: '',
                    academic_year_id: '{{ $uniqueAcademicYears->firstWhere('has_active', true)?->id ?? ($uniqueAcademicYears->first()?->id ?? '') }}',
                    homeroom_teacher_id: '',
                    capacity: 32,
                },
                confirmModal: {
                    open: false,
                    id: null,
                    title: '',
                    message: '',
                    loading: false
                },

                openStudentsModal(id) {
                    fetch(`/classrooms/${id}/students`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            this.selectedClassroom = res.classroom;
                            this.classroomStudents = res.students;
                            this.studentsModalOpen = true;
                            this.$nextTick(() => {
                                if (window.lucide) lucide.createIcons();
                            });
                        }
                    })
                    .catch(err => {
                        if (window.showToastNotification) {
                            window.showToastNotification("Gagal memuat siswa rombel: " + err.message, "error");
                        } else {
                            alert("Gagal memuat siswa rombel: " + err.message);
                        }
                    });
                },

                openCreateModal() {
                    this.isEdit = false;
                    this.teacherSearch = '';
                    this.teacherDropdownOpen = false;
                    this.formData = {
                        id: null,
                        name: '',
                        code: '',
                        class_level_id: '',
                        academic_year_id: '{{ $uniqueAcademicYears->firstWhere('has_active', true)?->id ?? ($uniqueAcademicYears->first()?->id ?? '') }}',
                        homeroom_teacher_id: '',
                        capacity: 32,
                    };
                    this.formModalOpen = true;
                    this.$nextTick(() => {
                        if (window.lucide) lucide.createIcons();
                    });
                },

                openEditModal(id, name, code, classLevelId, academicYearId, teacherId, capacity) {
                    this.isEdit = true;
                    this.teacherSearch = '';
                    this.teacherDropdownOpen = false;
                    this.formData = {
                        id: id,
                        name: name,
                        code: code || '',
                        class_level_id: classLevelId,
                        academic_year_id: academicYearId || '',
                        homeroom_teacher_id: teacherId || '',
                        capacity: capacity || 32,
                    };
                    this.formModalOpen = true;
                    this.$nextTick(() => {
                        if (window.lucide) lucide.createIcons();
                    });
                },

                submitForm() {
                    if (this.saving) return;
                    this.saving = true;

                    const url = this.isEdit ? `/classrooms/${this.formData.id}` : '/classrooms';
                    const method = this.isEdit ? 'PUT' : 'POST';

                    fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.formData)
                    })
                    .then(async res => {
                        this.saving = false;
                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.formModalOpen = false;
                            if (window.setPendingToast) {
                                window.setPendingToast(data.message || 'Rombel berhasil disimpan!', 'success');
                            }
                            window.location.reload();
                        } else {
                            const errMsg = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Terjadi kesalahan saat menyimpan.');
                            if (window.showToastNotification) {
                                window.showToastNotification(errMsg, 'error');
                            } else {
                                alert(errMsg);
                            }
                        }
                    })
                    .catch(err => {
                        this.saving = false;
                        if (window.showToastNotification) {
                            window.showToastNotification('Error: ' + err.message, 'error');
                        } else {
                            alert('Error: ' + err.message);
                        }
                    });
                },

                confirmDeleteRombel(id, name) {
                    this.confirmModal = {
                        open: true,
                        id: id,
                        title: 'Hapus Rombongan Belajar?',
                        message: `Apakah Anda yakin ingin menghapus rombel <strong>${name}</strong>? Data yang telah dihapus tidak dapat dipulihkan.`,
                        loading: false
                    };
                    this.$nextTick(() => {
                        if (window.lucide) lucide.createIcons();
                    });
                },

                executeConfirmDelete() {
                    if (this.confirmModal.loading) return;
                    this.confirmModal.loading = true;

                    fetch(`/classrooms/${this.confirmModal.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(async res => {
                        this.confirmModal.loading = false;
                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.confirmModal.open = false;
                            if (window.setPendingToast) {
                                window.setPendingToast(data.message || 'Rombel berhasil dihapus!', 'success');
                            }
                            window.location.reload();
                        } else {
                            const errMsg = data.message || 'Gagal menghapus rombel.';
                            if (window.showToastNotification) {
                                window.showToastNotification(errMsg, 'error');
                            } else {
                                alert(errMsg);
                            }
                        }
                    })
                    .catch(err => {
                        this.confirmModal.loading = false;
                        if (window.showToastNotification) {
                            window.showToastNotification('Error: ' + err.message, 'error');
                        } else {
                            alert('Error: ' + err.message);
                        }
                    });
                }
            }
        }
    </script>
</x-admin-layout>
