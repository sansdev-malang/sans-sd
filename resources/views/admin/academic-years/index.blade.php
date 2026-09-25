<x-admin-layout>
    <div class="p-6 space-y-6" x-data="academicYearApp()">

        <!-- GREETING / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <div class="flex items-center gap-2.5">
                    <div class="p-2 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-xl border border-indigo-500/20">
                        <i data-lucide="calendar-range" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 flex items-center gap-2">
                            Tahun Pelajaran (Tapel)
                            <span class="text-xs px-2.5 py-0.5 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-400 font-semibold border border-indigo-200 dark:border-indigo-800">
                                Master Akademik
                            </span>
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Kelola master tahun pelajaran (Tapel), semester, dan status periode aktif di SANS SD sesuai standar Dapodik.</p>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <!-- Info Badge Tapel Aktif -->
                <div class="inline-flex items-center gap-2 px-3.5 py-2 bg-indigo-50/80 dark:bg-indigo-950/40 border border-indigo-200/80 dark:border-indigo-800/60 rounded-xl text-xs shadow-xs">
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Tapel Berjalan:</span>
                    <span class="font-bold text-indigo-700 dark:text-indigo-300">
                        {{ $activeYear ? $activeYear->name : 'Belum Diatur' }} ({{ $activeYear?->semester ?? 'Ganjil' }})
                    </span>
                </div>

                <a href="{{ route('classrooms.index') }}"
                    class="inline-flex items-center justify-center gap-2 px-3.5 py-2 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 shadow-xs transition-all duration-100 cursor-pointer">
                    <i data-lucide="university" class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400"></i>
                    Rombongan Belajar
                </a>
                <button type="button" @click="openCreateModal()"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all duration-100 cursor-pointer">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    Tambah Tahun Pelajaran
                </button>
            </div>
        </section>

        <!-- STATS CARDS GRID -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Stat 1: Total Tapel -->
            <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Tahun Pelajaran</p>
                        <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">{{ number_format($stats['total_years']) }}</h3>
                    </div>
                    <div class="p-2.5 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 rounded-xl border border-indigo-100 dark:border-indigo-900/50">
                        <i data-lucide="calendar" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="mt-3 text-[11px] text-slate-500 dark:text-slate-400">
                    Periode Tapel tersimpan
                </div>
            </div>

            <!-- Stat 2: Tapel Aktif -->
            <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tapel Sedang Berjalan</p>
                        <h3 class="text-xl font-bold tracking-tight text-emerald-600 dark:text-emerald-400 mt-1 truncate">{{ $stats['active_year'] }}</h3>
                    </div>
                    <div class="p-2.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-xl border border-emerald-100 dark:border-emerald-900/50">
                        <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="mt-3 text-[11px] text-slate-500 dark:text-slate-400">
                    Semester: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $stats['active_semester'] }}</span>
                </div>
            </div>

            <!-- Stat 3: Total Rombel -->
            <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Rombel Aktif</p>
                        <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">{{ number_format($stats['total_classrooms']) }}</h3>
                    </div>
                    <div class="p-2.5 bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 rounded-xl border border-blue-100 dark:border-blue-900/50">
                        <i data-lucide="university" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="mt-3 text-[11px] text-slate-500 dark:text-slate-400">
                    Kelas & rombongan belajar
                </div>
            </div>

            <!-- Stat 4: Total Siswa Aktif -->
            <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Siswa Aktif</p>
                        <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">{{ number_format($stats['total_students']) }}</h3>
                    </div>
                    <div class="p-2.5 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 rounded-xl border border-rose-100 dark:border-rose-900/50">
                        <i data-lucide="users" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="mt-3 text-[11px] text-slate-500 dark:text-slate-400">
                    Siswa terdaftar di seluruh SD
                </div>
            </div>
        </section>

        <!-- TABLE DAFTAR TAHUN PELAJARAN -->
        <section class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs overflow-hidden transition-all w-full">
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                    <i data-lucide="list" class="w-4 h-4 text-indigo-600"></i>
                    Daftar Periode & Tahun Pelajaran (Tapel)
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-900/50">
                            <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-12">No</th>
                            <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Tahun Pelajaran</th>
                            <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-32">Semester</th>
                            <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-44">Periode Tanggal</th>
                            <th class="px-5 py-3.5 text-center text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-36">Status Aktif</th>
                            <th class="px-5 py-3.5 text-center text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-28">Rombel</th>
                            <th class="px-5 py-3.5 text-center text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-28">Siswa</th>
                            <th class="px-5 py-3.5 text-right text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        @forelse($academicYears as $index => $year)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors group {{ $year->is_active ? 'bg-indigo-50/20 dark:bg-indigo-950/10' : '' }}">
                                <td class="px-5 py-3.5 text-slate-400 font-mono text-[11px]">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg {{ $year->is_active ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }} flex items-center justify-center font-bold text-xs shrink-0">
                                            <i data-lucide="calendar" class="w-4 h-4"></i>
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-900 dark:text-slate-100 text-xs">
                                                Tapel {{ $year->name }}
                                            </span>
                                            @if($year->description)
                                                <p class="text-[11px] text-slate-400 mt-0.5 truncate max-w-xs">{{ $year->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold {{ $year->semester === 'Ganjil' ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-400 border border-blue-200 dark:border-blue-800' : 'bg-purple-50 text-purple-700 dark:bg-purple-950/50 dark:text-purple-400 border border-purple-200 dark:border-purple-800' }}">
                                        {{ $year->semester }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-slate-600 dark:text-slate-300 font-mono text-[11px]">
                                    @if($year->start_date && $year->end_date)
                                        {{ $year->start_date->format('d/m/Y') }} - {{ $year->end_date->format('d/m/Y') }}
                                    @else
                                        <span class="text-slate-400 italic">Belum diset</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    @if($year->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-800 shadow-2xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            TAPEL AKTIF
                                        </span>
                                    @else
                                        <button type="button" @click="confirmSetActive({{ $year->id }}, '{{ $year->name }}', '{{ $year->semester }}')"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-semibold bg-white dark:bg-slate-800 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300 dark:hover:bg-emerald-950/40 dark:hover:text-emerald-400 dark:hover:border-emerald-700 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 shadow-2xs transition-all duration-150 cursor-pointer"
                                            title="Klik untuk menjadikan Tahun Pelajaran ini Aktif">
                                            <i data-lucide="check-circle" class="w-3.5 h-3.5 text-slate-400 group-hover:text-emerald-500"></i>
                                            <span>Jadikan Aktif</span>
                                        </button>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="font-bold text-slate-800 dark:text-slate-200 font-mono">{{ $year->classrooms_count }}</span>
                                    <span class="text-slate-400 text-[10px] block">rombel</span>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="font-bold text-indigo-600 dark:text-indigo-400 font-mono">{{ $year->active_students_count }}</span>
                                    <span class="text-slate-400 text-[10px] block">siswa aktif</span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="button" @click="openEditModal({{ $year->id }})"
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
                                <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                    Belum ada data Tahun Pelajaran.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- MODAL TAMBAH / EDIT TAHUN PELAJARAN (Non-bubbling backdrop overlay) -->
        <div x-show="modalOpen" x-cloak style="display: none; margin-top: 0px !important; z-index: 9999;"
            class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto"
            @click.self="modalOpen = false"
            @keydown.escape.window="modalOpen = false">
            
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl flex flex-col relative my-auto"
                @click.stop>
                
                <form @submit.prevent="submitForm">
                    <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between sticky top-0 bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm z-10">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-slate-50" x-text="isEdit ? 'Edit Tahun Pelajaran' : 'Tambah Tahun Pelajaran Baru'"></h3>
                            <p class="text-xs text-slate-400 mt-0.5">Atur nama periode Tapel dan semester akademik.</p>
                        </div>
                        <button type="button" @click="modalOpen = false" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <div class="p-6 space-y-4 text-xs">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Tahun Pelajaran (Tapel) <span class="text-rose-500">*</span></label>
                                <input type="text" x-model="formData.name" required placeholder="Contoh: 2026/2027"
                                    class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 dark:text-slate-50 font-mono">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Semester <span class="text-rose-500">*</span></label>
                                <select x-model="formData.semester" required
                                    class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 dark:text-slate-50 cursor-pointer">
                                    <option value="Ganjil">Ganjil</option>
                                    <option value="Genap">Genap</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Mulai</label>
                                <input type="date" x-model="formData.start_date"
                                    class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 dark:text-slate-50">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Selesai</label>
                                <input type="date" x-model="formData.end_date"
                                    class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 dark:text-slate-50">
                            </div>
                        </div>

                        <div class="pt-2 p-3 bg-indigo-50/50 dark:bg-indigo-950/30 rounded-xl border border-indigo-100 dark:border-indigo-900/40">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" x-model="formData.is_active" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300 dark:border-slate-700">
                                <span class="font-semibold text-slate-800 dark:text-slate-200">Set sebagai Tahun Pelajaran Aktif (Tapel Utama)</span>
                            </label>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 ml-6 mt-0.5">Jika dicentang, tahun pelajaran lain otomatis dinonaktifkan sebagai acuan utama sistem.</p>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Keterangan / Catatan</label>
                            <textarea x-model="formData.description" rows="3" placeholder="Catatan tambahan periode tahun pelajaran ini..."
                                class="w-full p-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 dark:text-slate-50"></textarea>
                        </div>
                    </div>

                    <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex justify-end gap-2">
                        <button type="button" @click="modalOpen = false" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold transition-colors cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" :disabled="saving" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white rounded-lg text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                            <span x-show="saving" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                            <span x-text="saving ? 'Menyimpan...' : (isEdit ? 'Simpan Perubahan' : 'Tambah Tahun Pelajaran')"></span>
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
                
                <div class="w-12 h-12 rounded-full mx-auto flex items-center justify-center mb-4"
                    :class="confirmModal.type === 'delete' ? 'bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400' : 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400'">
                    <i :data-lucide="confirmModal.type === 'delete' ? 'trash-2' : 'check-circle'" class="w-6 h-6"></i>
                </div>

                <h3 class="text-base font-bold text-slate-900 dark:text-slate-50" x-text="confirmModal.title"></h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed" x-html="confirmModal.message"></p>

                <div class="mt-6 flex items-center justify-center gap-3">
                    <button type="button" @click="confirmModal.open = false" :disabled="confirmModal.loading"
                        class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="executeConfirmAction()" :disabled="confirmModal.loading"
                        class="px-5 py-2 text-white rounded-lg text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer"
                        :class="confirmModal.type === 'delete' ? 'bg-rose-600 hover:bg-rose-700' : 'bg-emerald-600 hover:bg-emerald-700'">
                        <span x-show="confirmModal.loading" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                        <span x-text="confirmModal.confirmText"></span>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- Alpine.js Application Logic -->
    <script>
        function academicYearApp() {
            return {
                modalOpen: false,
                isEdit: false,
                saving: false,
                formData: {
                    id: null,
                    name: '',
                    semester: 'Ganjil',
                    start_date: '',
                    end_date: '',
                    is_active: false,
                    description: '',
                },
                confirmModal: {
                    open: false,
                    type: 'activate',
                    id: null,
                    title: '',
                    message: '',
                    confirmText: 'Ya, Lanjutkan',
                    loading: false
                },

                openCreateModal() {
                    this.isEdit = false;
                    this.formData = {
                        id: null,
                        name: '',
                        semester: 'Ganjil',
                        start_date: '',
                        end_date: '',
                        is_active: false,
                        description: '',
                    };
                    this.modalOpen = true;
                    this.$nextTick(() => {
                        if (window.lucide) lucide.createIcons();
                    });
                },

                openEditModal(id) {
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
                            this.isEdit = true;
                            const semStr = (y.semester || '').toString().toLowerCase();
                            this.formData = {
                                id: y.id,
                                name: y.name || '',
                                semester: semStr === 'genap' ? 'Genap' : 'Ganjil',
                                start_date: y.start_date ? y.start_date.substring(0, 10) : '',
                                end_date: y.end_date ? y.end_date.substring(0, 10) : '',
                                is_active: Boolean(y.is_active),
                                description: y.description || '',
                            };
                            this.modalOpen = true;
                            this.$nextTick(() => {
                                if (window.lucide) lucide.createIcons();
                            });
                        }
                    })
                    .catch(err => {
                        if (window.showToastNotification) {
                            window.showToastNotification("Gagal mengambil data: " + err.message, "error");
                        } else {
                            alert("Gagal mengambil data: " + err.message);
                        }
                    });
                },

                submitForm() {
                    if (this.saving) return;
                    this.saving = true;

                    const url = this.isEdit ? `/academic-years/${this.formData.id}` : '/academic-years';
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
                    .then(async (res) => {
                        this.saving = false;
                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.modalOpen = false;
                            if (window.showToastNotification) {
                                window.showToastNotification(data.message || 'Tahun pelajaran berhasil disimpan!', 'success');
                            }
                            setTimeout(() => window.location.reload(), 500);
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

                confirmSetActive(id, name, semester) {
                    this.confirmModal = {
                        open: true,
                        type: 'activate',
                        id: id,
                        title: 'Jadikan Periode Aktif Utama?',
                        message: `Tahun Pelajaran <strong>${name} (${semester})</strong> akan dijadikan sebagai periode aktif utama sistem. Seluruh filter data siswa, rombel, dan SPMB secara default akan mengacu pada periode ini.`,
                        confirmText: 'Ya, Jadikan Aktif',
                        loading: false
                    };
                    this.$nextTick(() => {
                        if (window.lucide) lucide.createIcons();
                    });
                },

                confirmDeleteYear(id, name) {
                    this.confirmModal = {
                        open: true,
                        type: 'delete',
                        id: id,
                        title: 'Hapus Tahun Pelajaran?',
                        message: `Apakah Anda yakin ingin menghapus Tahun Pelajaran <strong>${name}</strong>? Data yang telah dihapus tidak dapat dipulihkan.`,
                        confirmText: 'Hapus Permanen',
                        loading: false
                    };
                    this.$nextTick(() => {
                        if (window.lucide) lucide.createIcons();
                    });
                },

                executeConfirmAction() {
                    if (this.confirmModal.loading) return;
                    this.confirmModal.loading = true;

                    if (this.confirmModal.type === 'activate') {
                        fetch(`/academic-years/${this.confirmModal.id}/set-active`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(async (res) => {
                            this.confirmModal.loading = false;
                            const data = await res.json();
                            if (res.ok && data.success) {
                                this.confirmModal.open = false;
                                if (window.showToastNotification) {
                                    window.showToastNotification(data.message || 'Tahun pelajaran berhasil diaktifkan!', 'success');
                                }
                                setTimeout(() => window.location.reload(), 500);
                            } else {
                                if (window.showToastNotification) {
                                    window.showToastNotification(data.message || 'Gagal mengubah status aktif.', 'error');
                                } else {
                                    alert(data.message || 'Gagal mengubah status aktif.');
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
                    } else if (this.confirmModal.type === 'delete') {
                        fetch(`/academic-years/${this.confirmModal.id}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(async (res) => {
                            this.confirmModal.loading = false;
                            const data = await res.json();
                            if (res.ok && data.success) {
                                this.confirmModal.open = false;
                                if (window.showToastNotification) {
                                    window.showToastNotification(data.message || 'Tahun pelajaran berhasil dihapus!', 'success');
                                }
                                setTimeout(() => window.location.reload(), 500);
                            } else {
                                if (window.showToastNotification) {
                                    window.showToastNotification(data.message || 'Gagal menghapus tahun pelajaran.', 'error');
                                } else {
                                    alert(data.message || 'Gagal menghapus tahun pelajaran.');
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
        }
    </script>
</x-admin-layout>
