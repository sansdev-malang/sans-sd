<x-admin-layout>
    <div class="p-6 space-y-6" x-data="academicYearApp()">

        <!-- GREETING / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-2 sm:gap-3 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-base sm:text-lg font-bold tracking-tight text-slate-900 dark:text-slate-50">Tahun Pelajaran & Semester</h2>
                <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400">Kelola master periode tahun pelajaran (Tapel) dan semester evaluasi di {{ setting('unit_name', 'SD Anak Saleh') }}.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <!-- Info Badge Periode Berjalan -->
                <div class="inline-flex items-center gap-2 px-3.5 py-2 bg-indigo-50/80 dark:bg-indigo-950/40 border border-indigo-200/80 dark:border-indigo-800/60 rounded-xl text-xs shadow-xs">
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Periode Aktif:</span>
                    <span class="font-bold text-indigo-700 dark:text-indigo-300">
                        {{ $activeYear ? $activeYear->name : 'Belum Diatur' }}
                    </span>
                    <span class="text-slate-300 dark:text-slate-700">&bull;</span>
                    <span class="font-semibold text-emerald-600 dark:text-emerald-400">
                        {{ $activeSemester ? $activeSemester->name : ($activeYear?->semester ?? 'Ganjil') }}
                    </span>
                </div>

                <!-- Tombol Aksi Tambah Sesuai Tab -->
                <button type="button" x-show="activeTab === 'years'" @click="openCreateYearModal()"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all duration-100 cursor-pointer">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    Tambah Tahun Pelajaran
                </button>
                <button type="button" x-show="activeTab === 'semesters'" @click="openCreateSemesterModal()"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all duration-100 cursor-pointer">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    Tambah Semester
                </button>
            </div>
        </section>

        <!-- STATS CARDS GRID -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-3.5">
            <!-- Stat 1: Total Tahun Pelajaran -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 shadow-xs flex items-center gap-3 transition-colors">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-100 dark:border-indigo-900/40">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10.5px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Total Tahun Pelajaran</p>
                    <div class="flex items-baseline gap-1.5 mt-0.5">
                        <h3 class="text-xl font-bold tracking-tight text-slate-900 dark:text-slate-50 font-mono">
                            {{ number_format($stats['total_years']) }}
                        </h3>
                        <span class="text-[10px] text-slate-400 font-medium truncate">Periode</span>
                    </div>
                </div>
            </div>

            <!-- Stat 2: Tahun Pelajaran Aktif -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 shadow-xs flex items-center gap-3 transition-colors">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-100 dark:border-emerald-900/40">
                    <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10.5px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Tapel Berjalan</p>
                    <div class="flex items-baseline gap-1.5 mt-0.5">
                        <h3 class="text-base font-bold tracking-tight text-emerald-600 dark:text-emerald-400 font-mono truncate">
                            {{ $stats['active_year'] }}
                        </h3>
                    </div>
                </div>
            </div>

            <!-- Stat 3: Total Semester Terdaftar -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 shadow-xs flex items-center gap-3 transition-colors">
                <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0 border border-purple-100 dark:border-purple-900/40">
                    <i data-lucide="calendar-days" class="w-5 h-5"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10.5px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Total Semester</p>
                    <div class="flex items-baseline gap-1.5 mt-0.5">
                        <h3 class="text-xl font-bold tracking-tight text-purple-600 dark:text-purple-400 font-mono">
                            {{ number_format($stats['total_semesters']) }}
                        </h3>
                        <span class="text-[10px] text-slate-400 font-medium truncate">Terdaftar</span>
                    </div>
                </div>
            </div>

            <!-- Stat 4: Semester Aktif -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 shadow-xs flex items-center gap-3 transition-colors">
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 border border-amber-100 dark:border-amber-900/40">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10.5px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Semester Berjalan</p>
                    <div class="flex items-baseline gap-1.5 mt-0.5">
                        <h3 class="text-base font-bold tracking-tight text-amber-600 dark:text-amber-400 truncate">
                            {{ $stats['active_semester'] }}
                        </h3>
                    </div>
                </div>
            </div>
        </section>

        <!-- MAIN TABS NAVIGATION (1 MENU BEDA TAB) -->
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-3">
            <button type="button" @click="switchTab('years')"
                :class="activeTab === 'years' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800'"
                class="px-4 py-2 rounded-lg text-xs font-semibold transition-all cursor-pointer inline-flex items-center gap-2">
                <i data-lucide="calendar" class="w-4 h-4"></i>
                <span>Tahun Pelajaran</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                    :class="activeTab === 'years' ? 'bg-indigo-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400'">
                    {{ $academicYears->count() }}
                </span>
            </button>

            <button type="button" @click="switchTab('semesters')"
                :class="activeTab === 'semesters' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800'"
                class="px-4 py-2 rounded-lg text-xs font-semibold transition-all cursor-pointer inline-flex items-center gap-2">
                <i data-lucide="calendar-days" class="w-4 h-4"></i>
                <span>Semester</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                    :class="activeTab === 'semesters' ? 'bg-indigo-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400'">
                    {{ $semesters->count() }}
                </span>
            </button>
        </div>

        <!-- ============================================================= -->
        <!-- TAB 1: TABLE TAHUN PELAJARAN                                  -->
        <!-- Kolom: tahun pelajaran, keterangan, status, aksi              -->
        <!-- ============================================================= -->
        <section x-show="activeTab === 'years'" class="space-y-4">
            <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs overflow-hidden transition-all w-full">
                <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        <i data-lucide="list" class="w-4 h-4 text-indigo-600"></i>
                        Daftar Tahun Pelajaran
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-900/50">
                                <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tahun Pelajaran</th>
                                <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Keterangan</th>
                                <th class="px-5 py-3.5 text-center text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-40">Status</th>
                                <th class="px-5 py-3.5 text-right text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                            @forelse($academicYears as $year)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors group {{ $year->is_active ? 'bg-indigo-50/20 dark:bg-indigo-950/10' : '' }}">
                                    <!-- 1. Tahun Pelajaran -->
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg {{ $year->is_active ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }} flex items-center justify-center font-bold text-xs shrink-0">
                                                <i data-lucide="calendar" class="w-4 h-4"></i>
                                            </div>
                                            <div>
                                                <span class="font-bold text-slate-900 dark:text-slate-100 text-xs">
                                                    {{ $year->name }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- 2. Keterangan -->
                                    <td class="px-5 py-3.5 text-slate-600 dark:text-slate-400">
                                        {{ $year->description ?: '-' }}
                                    </td>

                                    <!-- 3. Status -->
                                    <td class="px-5 py-3.5 text-center">
                                        @if($year->is_active)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-800 shadow-2xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <button type="button" @click="confirmSetActiveYear({{ $year->id }}, '{{ $year->name }}')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[11px] font-semibold bg-white dark:bg-slate-800 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 dark:hover:bg-emerald-950/40 dark:hover:text-emerald-400 dark:hover:border-emerald-700 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 shadow-2xs transition-all duration-150 cursor-pointer"
                                                title="Klik untuk mengaktifkan Tahun Pelajaran ini">
                                                <i data-lucide="check-circle" class="w-3.5 h-3.5 text-slate-400 group-hover:text-emerald-500"></i>
                                                <span>Jadikan Aktif</span>
                                            </button>
                                        @endif
                                    </td>

                                    <!-- 4. Aksi -->
                                    <td class="px-5 py-3.5 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <button type="button" @click="openEditYearModal({{ $year->id }})"
                                                class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 rounded-lg transition-colors cursor-pointer"
                                                title="Edit Tahun Pelajaran">
                                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                                            </button>
                                            @if(!$year->is_active)
                                                <button type="button" @click="confirmDeleteYear({{ $year->id }}, '{{ $year->name }}')"
                                                    class="p-1.5 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 rounded-lg transition-colors cursor-pointer"
                                                    title="Hapus Tahun Pelajaran">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                        Belum ada data Tahun Pelajaran.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ============================================================= -->
        <!-- TAB 2: TABLE SEMESTER                                         -->
        <!-- Kolom: semester, keterangan, status, aksi                    -->
        <!-- ============================================================= -->
        <section x-show="activeTab === 'semesters'" class="space-y-4" style="display: none;">
            <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs overflow-hidden transition-all w-full">
                <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        <i data-lucide="calendar-days" class="w-4 h-4 text-indigo-600"></i>
                        Daftar Semester
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-900/50">
                                <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Semester</th>
                                <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Keterangan</th>
                                <th class="px-5 py-3.5 text-center text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-40">Status</th>
                                <th class="px-5 py-3.5 text-right text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                            @forelse($semesters as $sem)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors group {{ $sem->is_active ? 'bg-indigo-50/20 dark:bg-indigo-950/10' : '' }}">
                                    <!-- 1. Semester -->
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-2.5">
                                             <div class="w-8 h-8 rounded-lg {{ $sem->is_active ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }} flex items-center justify-center font-bold text-xs shrink-0">
                                                <i data-lucide="book-open-check" class="w-4 h-4"></i>
                                            </div>
                                            <div>
                                                <span class="font-bold text-slate-900 dark:text-slate-100 text-xs">
                                                    {{ $sem->name }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- 2. Keterangan -->
                                    <td class="px-5 py-3.5 text-slate-600 dark:text-slate-400">
                                        {{ $sem->description ?: '-' }}
                                    </td>

                                    <!-- 3. Status -->
                                    <td class="px-5 py-3.5 text-center">
                                        @if($sem->is_active)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-800 shadow-2xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <button type="button" @click="confirmSetActiveSemester({{ $sem->id }}, '{{ $sem->name }}')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[11px] font-semibold bg-white dark:bg-slate-800 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 dark:hover:bg-emerald-950/40 dark:hover:text-emerald-400 dark:hover:border-emerald-700 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 shadow-2xs transition-all duration-150 cursor-pointer"
                                                title="Klik untuk mengaktifkan Semester ini">
                                                <i data-lucide="check-circle" class="w-3.5 h-3.5 text-slate-400 group-hover:text-emerald-500"></i>
                                                <span>Jadikan Aktif</span>
                                            </button>
                                        @endif
                                    </td>

                                    <!-- 4. Aksi -->
                                    <td class="px-5 py-3.5 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <button type="button" @click="openEditSemesterModal({{ $sem->id }})"
                                                class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 rounded-lg transition-colors cursor-pointer"
                                                title="Edit Semester">
                                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                                            </button>
                                            @if(!$sem->is_active)
                                                <button type="button" @click="confirmDeleteSemester({{ $sem->id }}, '{{ $sem->name }}')"
                                                    class="p-1.5 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 rounded-lg transition-colors cursor-pointer"
                                                    title="Hapus Semester">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                        Belum ada data Semester.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ============================================================= -->
        <!-- MODAL TAMBAH / EDIT TAHUN PELAJARAN                           -->
        <!-- ============================================================= -->
        <div x-show="yearModalOpen" x-cloak style="display: none; margin-top: 0px !important; z-index: 9999;"
            class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto"
            @click.self="yearModalOpen = false"
            @keydown.escape.window="yearModalOpen = false">
            
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl flex flex-col relative my-auto text-left"
                @click.stop>
                
                <form @submit.prevent="submitYearForm">
                    <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between sticky top-0 bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm z-10">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-slate-50" x-text="isYearEdit ? 'Edit Tahun Pelajaran' : 'Tambah Tahun Pelajaran Baru'"></h3>
                            <p class="text-xs text-slate-400 mt-0.5">Atur nama tahun pelajaran dan keterangan.</p>
                        </div>
                        <button type="button" @click="yearModalOpen = false" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <div class="p-6 space-y-4 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tahun Pelajaran <span class="text-rose-500">*</span></label>
                            <input type="text" x-model="yearFormData.name" required placeholder="Contoh: 2026/2027"
                                class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 dark:text-slate-50 font-mono">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Keterangan</label>
                            <textarea x-model="yearFormData.description" rows="3" placeholder="Keterangan opsional untuk tahun pelajaran ini..."
                                class="w-full p-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 dark:text-slate-50"></textarea>
                        </div>

                        <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700/50 flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-slate-800 dark:text-slate-200">Status Aktif</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Jadikan tahun pelajaran ini sebagai periode aktif.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" x-model="yearFormData.is_active" class="sr-only peer">
                                <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-slate-600 peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>
                    </div>

                    <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex justify-end gap-2">
                        <button type="button" @click="yearModalOpen = false" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold transition-colors">
                            Batal
                        </button>
                        <button type="submit" :disabled="saving" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white rounded-lg text-xs font-bold transition-all shadow-xs flex items-center gap-1.5">
                            <span x-text="saving ? 'Menyimpan...' : (isYearEdit ? 'Simpan Perubahan' : 'Tambah Tahun Pelajaran')"></span>
                        </button>
                    </div>
                </form>

            </div>
        </div>

        <!-- ============================================================= -->
        <!-- MODAL TAMBAH / EDIT SEMESTER                                  -->
        <!-- ============================================================= -->
        <div x-show="semesterModalOpen" x-cloak style="display: none; margin-top: 0px !important; z-index: 9999;"
            class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto"
            @click.self="semesterModalOpen = false"
            @keydown.escape.window="semesterModalOpen = false">
            
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl flex flex-col relative my-auto text-left"
                @click.stop>
                
                <form @submit.prevent="submitSemesterForm">
                    <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between sticky top-0 bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm z-10">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-slate-50" x-text="isSemesterEdit ? 'Edit Semester' : 'Tambah Semester Baru'"></h3>
                            <p class="text-xs text-slate-400 mt-0.5">Konfigurasi nama semester dan keterangan.</p>
                        </div>
                        <button type="button" @click="semesterModalOpen = false" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <div class="p-6 space-y-4 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Semester <span class="text-rose-500">*</span></label>
                            <input type="text" x-model="semesterFormData.name" required placeholder="Contoh: Tengah Semester Ganjil / Semester Ganjil"
                                class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 dark:text-slate-50">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Keterangan</label>
                            <textarea x-model="semesterFormData.description" rows="3" placeholder="Keterangan opsional untuk semester ini..."
                                class="w-full p-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 dark:text-slate-50"></textarea>
                        </div>

                        <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700/50 flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-slate-800 dark:text-slate-200">Status Aktif</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Jadikan semester ini sebagai periode aktif rapor murid.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" x-model="semesterFormData.is_active" class="sr-only peer">
                                <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-slate-600 peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>
                    </div>

                    <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex justify-end gap-2">
                        <button type="button" @click="semesterModalOpen = false" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold transition-colors">
                            Batal
                        </button>
                        <button type="submit" :disabled="saving" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white rounded-lg text-xs font-bold transition-all shadow-xs flex items-center gap-1.5">
                            <span x-text="saving ? 'Menyimpan...' : (isSemesterEdit ? 'Simpan Perubahan' : 'Tambah Semester')"></span>
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>

    <!-- Alpine.js Application Logic -->
    <script>
        function academicYearApp() {
            const urlParams = new URLSearchParams(window.location.search);
            const initialTab = urlParams.get('tab') === 'semesters' ? 'semesters' : 'years';

            return {
                activeTab: initialTab,
                saving: false,

                // Tapel State
                yearModalOpen: false,
                isYearEdit: false,
                yearFormData: {
                    id: null,
                    name: '',
                    description: '',
                    is_active: false,
                },

                // Semester State
                semesterModalOpen: false,
                isSemesterEdit: false,
                semesterFormData: {
                    id: null,
                    name: '',
                    description: '',
                    is_active: false,
                },

                switchTab(tab) {
                    this.activeTab = tab;
                    const url = new URL(window.location);
                    url.searchParams.set('tab', tab);
                    window.history.replaceState({}, '', url);
                },

                // ==================== TAHUN PELAJARAN CRUD ====================
                openCreateYearModal() {
                    this.isYearEdit = false;
                    this.yearFormData = {
                        id: null,
                        name: '',
                        description: '',
                        is_active: false,
                    };
                    this.yearModalOpen = true;
                },

                openEditYearModal(id) {
                    fetch(`/academic-years/${id}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            const y = res.academic_year;
                            this.isYearEdit = true;
                            this.yearFormData = {
                                id: y.id,
                                name: y.name,
                                description: y.description || '',
                                is_active: !!y.is_active,
                            };
                            this.yearModalOpen = true;
                        }
                    })
                    .catch(err => {
                        if (typeof window.showToast === 'function') {
                            window.showToast('Perhatian!', "Gagal mengambil data tahun pelajaran: " + err.message, 'error');
                        }
                    });
                },

                submitYearForm() {
                    if (this.saving) return;
                    this.saving = true;

                    const url = this.isYearEdit ? `/academic-years/${this.yearFormData.id}` : '/academic-years';
                    const method = this.isYearEdit ? 'PUT' : 'POST';

                    fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.yearFormData)
                    })
                    .then(res => res.json())
                    .then(res => {
                        this.saving = false;
                        if (res.success) {
                            this.yearModalOpen = false;
                            if (typeof window.setPendingToast === 'function') {
                                window.setPendingToast(res.message || 'Tahun Pelajaran berhasil disimpan!', 'success');
                            }
                            const url = new URL(window.location);
                            url.searchParams.set('tab', 'years');
                            window.location.href = url.toString();
                        } else {
                            if (typeof window.showToast === 'function') {
                                window.showToast('Perhatian!', res.message || 'Terjadi kesalahan saat menyimpan.', 'error');
                            }
                        }
                    })
                    .catch(err => {
                        this.saving = false;
                        if (typeof window.showToast === 'function') {
                            window.showToast('Perhatian!', 'Error: ' + err.message, 'error');
                        }
                    });
                },

                confirmSetActiveYear(id, name) {
                    const action = () => {
                        fetch(`/academic-years/${id}/set-active`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(res => res.json())
                        .then(res => {
                            if (res.success) {
                                if (typeof window.setPendingToast === 'function') {
                                    window.setPendingToast(res.message || 'Tahun Pelajaran berhasil diaktifkan!', 'success');
                                }
                                const url = new URL(window.location);
                                url.searchParams.set('tab', 'years');
                                window.location.href = url.toString();
                            } else {
                                if (typeof window.showToast === 'function') {
                                    window.showToast('Perhatian!', res.message || 'Gagal mengubah tahun aktif.', 'error');
                                }
                            }
                        })
                        .catch(err => {
                            if (typeof window.showToast === 'function') {
                                window.showToast('Perhatian!', 'Error: ' + err.message, 'error');
                            }
                        });
                    };

                    if (typeof showGlobalConfirmModal === 'function') {
                        showGlobalConfirmModal(`Jadikan Tahun Pelajaran "${name}" sebagai periode aktif acuan sistem?`, action, false);
                    } else if (confirm(`Jadikan Tahun Pelajaran "${name}" sebagai periode aktif acuan sistem?`)) {
                        action();
                    }
                },

                confirmDeleteYear(id, name) {
                    const action = () => {
                        fetch(`/academic-years/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(res => res.json())
                        .then(res => {
                            if (res.success) {
                                if (typeof window.setPendingToast === 'function') {
                                    window.setPendingToast(res.message || 'Tahun Pelajaran berhasil dihapus!', 'success');
                                }
                                const url = new URL(window.location);
                                url.searchParams.set('tab', 'years');
                                window.location.href = url.toString();
                            } else {
                                if (typeof window.showToast === 'function') {
                                    window.showToast('Perhatian!', res.message || 'Gagal menghapus tahun pelajaran.', 'error');
                                }
                            }
                        })
                        .catch(err => {
                            if (typeof window.showToast === 'function') {
                                window.showToast('Perhatian!', 'Error: ' + err.message, 'error');
                            }
                        });
                    };

                    if (typeof showGlobalConfirmModal === 'function') {
                        showGlobalConfirmModal(`Apakah Anda yakin ingin menghapus Tahun Pelajaran "${name}"?`, action, true);
                    } else if (confirm(`Apakah Anda yakin ingin menghapus Tahun Pelajaran "${name}"?`)) {
                        action();
                    }
                },

                // ==================== SEMESTER CRUD ====================
                openCreateSemesterModal() {
                    this.isSemesterEdit = false;
                    this.semesterFormData = {
                        id: null,
                        name: '',
                        description: '',
                        is_active: false,
                    };
                    this.semesterModalOpen = true;
                },

                openEditSemesterModal(id) {
                    fetch(`/semesters/${id}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            const s = res.semester;
                            this.isSemesterEdit = true;
                            this.semesterFormData = {
                                id: s.id,
                                name: s.name,
                                description: s.description || '',
                                is_active: !!s.is_active,
                            };
                            this.semesterModalOpen = true;
                        }
                    })
                    .catch(err => {
                        if (typeof window.showToast === 'function') {
                            window.showToast('Perhatian!', "Gagal mengambil data semester: " + err.message, 'error');
                        }
                    });
                },

                submitSemesterForm() {
                    if (this.saving) return;
                    this.saving = true;

                    const url = this.isSemesterEdit ? `/semesters/${this.semesterFormData.id}` : '/semesters';
                    const method = this.isSemesterEdit ? 'PUT' : 'POST';

                    fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.semesterFormData)
                    })
                    .then(res => res.json())
                    .then(res => {
                        this.saving = false;
                        if (res.success) {
                            this.semesterModalOpen = false;
                            if (typeof window.setPendingToast === 'function') {
                                window.setPendingToast(res.message || 'Semester berhasil disimpan!', 'success');
                            }
                            const url = new URL(window.location);
                            url.searchParams.set('tab', 'semesters');
                            window.location.href = url.toString();
                        } else {
                            if (typeof window.showToast === 'function') {
                                window.showToast('Perhatian!', res.message || 'Terjadi kesalahan saat menyimpan semester.', 'error');
                            }
                        }
                    })
                    .catch(err => {
                        this.saving = false;
                        if (typeof window.showToast === 'function') {
                            window.showToast('Perhatian!', 'Error: ' + err.message, 'error');
                        }
                    });
                },

                confirmSetActiveSemester(id, name) {
                    const action = () => {
                        fetch(`/semesters/${id}/set-active`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(res => res.json())
                        .then(res => {
                            if (res.success) {
                                if (typeof window.setPendingToast === 'function') {
                                    window.setPendingToast(res.message || 'Semester berhasil diaktifkan!', 'success');
                                }
                                const url = new URL(window.location);
                                url.searchParams.set('tab', 'semesters');
                                window.location.href = url.toString();
                            } else {
                                if (typeof window.showToast === 'function') {
                                    window.showToast('Perhatian!', res.message || 'Gagal mengubah semester aktif.', 'error');
                                }
                            }
                        })
                        .catch(err => {
                            if (typeof window.showToast === 'function') {
                                window.showToast('Perhatian!', 'Error: ' + err.message, 'error');
                            }
                        });
                    };

                    if (typeof showGlobalConfirmModal === 'function') {
                        showGlobalConfirmModal(`Jadikan Semester "${name}" sebagai periode aktif rapor murid?`, action, false);
                    } else if (confirm(`Jadikan Semester "${name}" sebagai periode aktif rapor murid?`)) {
                        action();
                    }
                },

                confirmDeleteSemester(id, name) {
                    const action = () => {
                        fetch(`/semesters/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(res => res.json())
                        .then(res => {
                            if (res.success) {
                                if (typeof window.setPendingToast === 'function') {
                                    window.setPendingToast(res.message || 'Semester berhasil dihapus!', 'success');
                                }
                                const url = new URL(window.location);
                                url.searchParams.set('tab', 'semesters');
                                window.location.href = url.toString();
                            } else {
                                if (typeof window.showToast === 'function') {
                                    window.showToast('Perhatian!', res.message || 'Gagal menghapus semester.', 'error');
                                }
                            }
                        })
                        .catch(err => {
                            if (typeof window.showToast === 'function') {
                                window.showToast('Perhatian!', 'Error: ' + err.message, 'error');
                            }
                        });
                    };

                    if (typeof showGlobalConfirmModal === 'function') {
                        showGlobalConfirmModal(`Apakah Anda yakin ingin menghapus Semester "${name}"?`, action, true);
                    } else if (confirm(`Apakah Anda yakin ingin menghapus Semester "${name}"?`)) {
                        action();
                    }
                }
            };
        }
    </script>
</x-admin-layout>
