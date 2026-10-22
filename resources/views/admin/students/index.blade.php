<x-admin-layout>
    <!-- Alpine.js Application Logic (Loaded before DOM element for seamless initialization) -->
    <script>
        function studentApp() {
            return {
                importModalOpen: false,
                exportDropdownOpen: false,
                downloadingTemplate: false,

                downloadTemplate() {
                    this.downloadingTemplate = true;
                    window.location.href = "{{ route('students.download-template') }}";
                    setTimeout(() => {
                        this.downloadingTemplate = false;
                    }, 2000);
                },

                confirmModal: {
                    open: false,
                    id: null,
                    title: '',
                    message: '',
                    loading: false
                },

                confirmDeleteStudent(id, name) {
                    this.confirmModal = {
                        open: true,
                        id: id,
                        title: 'Hapus Data Siswa?',
                        message: `Apakah Anda yakin ingin menghapus data siswa <strong>${name}</strong>? Data yang telah dihapus tidak dapat dipulihkan.`,
                        loading: false
                    };
                    this.$nextTick(() => {
                        if (window.lucide) lucide.createIcons();
                    });
                },

                executeConfirmDelete() {
                    if (this.confirmModal.loading) return;
                    this.confirmModal.loading = true;

                    fetch(`/students/${this.confirmModal.id}`, {
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
                                window.setPendingToast(data.message || 'Data siswa berhasil dihapus!', 'success');
                            }
                            window.location.reload();
                        } else {
                            const errMsg = data.message || 'Gagal menghapus data siswa.';
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
            };
        }
        window.studentApp = studentApp;
        window.downloadTemplate = function() {
            window.location.href = "{{ route('students.download-template') }}";
        };
        document.addEventListener('alpine:init', () => {
            if (typeof Alpine !== 'undefined' && Alpine.data) {
                Alpine.data('studentApp', studentApp);
            }
        });
    </script>

    <div class="p-4 sm:p-5 lg:p-6 space-y-4 lg:space-y-5" x-data="studentApp()">

        <!-- GREETING / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3.5 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-xl border border-indigo-500/20 flex items-center justify-center shrink-0">
                        <i data-lucide="users" class="w-5 h-5 shrink-0"></i>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold tracking-tight text-slate-900 dark:text-slate-50">
                            Daftar Siswa
                        </h2>
                        <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 mt-0.5">Database komprehensif peserta didik, riwayat kelas, inklusi, dan orang tua {{ setting('unit_name', 'SD Anak Saleh') }}.</p>
                    </div>
                </div>
            </div>
            <!-- ACTION CONTROLS: INFO TAHUN AJARAN AKTIF & ACTION BUTTONS -->
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <!-- Info Badge Tahun Ajaran Aktif -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-50/80 dark:bg-indigo-950/40 border border-indigo-200/80 dark:border-indigo-800/60 rounded-xl text-xs shadow-xs shrink-0">
                    <span class="flex h-2 w-2 relative shrink-0">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Tahun Ajaran Aktif:</span>
                    <span class="font-bold text-indigo-700 dark:text-indigo-300">
                        {{ $activeAcademicYear ? $activeAcademicYear->name : '2026/2027' }}
                    </span>
                </div>

                <button type="button" @click="importModalOpen = true"
                    class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 shadow-xs transition-colors cursor-pointer shrink-0">
                    <i data-lucide="upload" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0"></i>
                    <span>Impor Excel</span>
                </button>

                <!-- DROPDOWN EKSPOR & CETAK -->
                <div class="relative shrink-0" @click.outside="exportDropdownOpen = false">
                    <button type="button" @click="exportDropdownOpen = !exportDropdownOpen"
                        class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 shadow-xs transition-colors cursor-pointer">
                        <i data-lucide="download" class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400 shrink-0"></i>
                        <span>Ekspor & Cetak</span>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200 shrink-0" :class="{ 'rotate-180': exportDropdownOpen }"></i>
                    </button>

                    <div x-show="exportDropdownOpen"
                        x-cloak
                        style="display: none;"
                        class="absolute right-0 mt-1.5 w-60 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl py-1.5 z-50 divide-y divide-slate-100 dark:divide-slate-800/60">
                        
                        <div class="px-3 py-1.5 bg-slate-50/50 dark:bg-slate-800/30">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Pilihan Format Dokumen</p>
                        </div>

                        <div class="py-1">
                            <!-- Ekspor Excel -->
                            <a href="{{ route('students.export.excel', request()->all()) }}" @click="exportDropdownOpen = false"
                                class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors group">
                                <div class="w-7 h-7 flex items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/60 shrink-0">
                                    <i data-lucide="file-spreadsheet" class="w-4 h-4 shrink-0"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <span class="font-semibold block text-slate-800 dark:text-slate-200 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 truncate">Ekspor File Excel</span>
                                    <span class="text-[10px] text-slate-400 block truncate">Format .xlsx lengkap 75+ kolom</span>
                                </div>
                            </a>

                            <!-- Unduh PDF -->
                            <a href="{{ route('students.export.pdf', request()->all()) }}" @click="exportDropdownOpen = false"
                                class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-rose-50 dark:hover:bg-rose-950/40 hover:text-rose-700 dark:hover:text-rose-400 transition-colors group">
                                <div class="w-7 h-7 flex items-center justify-center rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200/60 dark:border-rose-800/60 shrink-0">
                                    <i data-lucide="file-text" class="w-4 h-4 shrink-0"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <span class="font-semibold block text-slate-800 dark:text-slate-200 group-hover:text-rose-600 dark:group-hover:text-rose-400 truncate">Unduh Dokumen PDF</span>
                                    <span class="text-[10px] text-slate-400 block truncate">Format landscape siap cetak</span>
                                </div>
                            </a>
                        </div>

                        <div class="py-1">
                            <!-- Cetak / Print Browser -->
                            <a href="{{ route('students.print', request()->all()) }}" target="_blank" @click="exportDropdownOpen = false"
                                class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 hover:text-indigo-700 dark:hover:text-indigo-400 transition-colors group">
                                <div class="w-7 h-7 flex items-center justify-center rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200/60 dark:border-indigo-800/60 shrink-0">
                                    <i data-lucide="printer" class="w-4 h-4 shrink-0"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <span class="font-semibold block text-slate-800 dark:text-slate-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 truncate">Cetak / Print Dialog</span>
                                    <span class="text-[10px] text-slate-400 block truncate">Preview kop surat & print browser</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <a href="{{ route('students.create') }}"
                    class="inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer shrink-0">
                    <i data-lucide="plus" class="w-3.5 h-3.5 shrink-0"></i>
                    <span>Tambah Siswa</span>
                </a>
            </div>
        </section>

        <!-- IMPORT ERRORS NOTIFICATION (IF ANY ROWS SKIPPED) -->
        @if(session('import_errors'))
            <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs space-y-2 shadow-xs">
                <div class="flex items-center gap-2 font-bold">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0"></i>
                    <span>Catatan Impor (Beberapa baris dilewati):</span>
                </div>
                <ul class="list-disc list-inside space-y-1 pl-2 text-[11px] text-rose-700 dark:text-rose-300 max-h-40 overflow-y-auto">
                    @foreach(session('import_errors') as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- STATS CARDS GRID -->
        <section class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5 sm:gap-3 lg:gap-3.5">
            <!-- Stat Card 1: Total Siswa Aktif -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 lg:p-4 shadow-xs flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Siswa Aktif</p>
                        <h3 class="text-lg sm:text-xl font-black tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                            {{ number_format($stats['total_active']) }}
                        </h3>
                    </div>
                    <div class="w-8 h-8 flex items-center justify-center bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 rounded-lg border border-indigo-100 dark:border-indigo-900/50 shrink-0">
                        <i data-lucide="users" class="w-4 h-4 shrink-0"></i>
                    </div>
                </div>
                <div class="mt-2 text-[10px] text-slate-400">
                    Total terdata: <span class="font-semibold text-slate-600 dark:text-slate-300">{{ number_format($stats['total_all']) }}</span>
                </div>
            </div>

            <!-- Stat Card 2: Laki-laki -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 lg:p-4 shadow-xs flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Putra (L)</p>
                        <h3 class="text-lg sm:text-xl font-black tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                            {{ number_format($stats['male']) }}
                        </h3>
                    </div>
                    <div class="w-8 h-8 flex items-center justify-center bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 rounded-lg border border-blue-100 dark:border-blue-900/50 shrink-0">
                        <i data-lucide="user" class="w-4 h-4 shrink-0"></i>
                    </div>
                </div>
                <div class="mt-2 text-[10px] text-slate-400">
                    {{ $stats['total_active'] > 0 ? round(($stats['male'] / $stats['total_active']) * 100) : 0 }}% dari total aktif
                </div>
            </div>

            <!-- Stat Card 3: Perempuan -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 lg:p-4 shadow-xs flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Putri (P)</p>
                        <h3 class="text-lg sm:text-xl font-black tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                            {{ number_format($stats['female']) }}
                        </h3>
                    </div>
                    <div class="w-8 h-8 flex items-center justify-center bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 rounded-lg border border-rose-100 dark:border-rose-900/50 shrink-0">
                        <i data-lucide="user-check" class="w-4 h-4 shrink-0"></i>
                    </div>
                </div>
                <div class="mt-2 text-[10px] text-slate-400">
                    {{ $stats['total_active'] > 0 ? round(($stats['female'] / $stats['total_active']) * 100) : 0 }}% dari total aktif
                </div>
            </div>

            <!-- Stat Card 4: Inklusi (PDBK) -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 lg:p-4 shadow-xs flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-purple-600 dark:text-purple-400 uppercase tracking-wider">Inklusi (PDBK)</p>
                        <h3 class="text-lg sm:text-xl font-black tracking-tight text-purple-700 dark:text-purple-300 mt-1">
                            {{ number_format($stats['pdbk']) }}
                        </h3>
                    </div>
                    <div class="w-8 h-8 flex items-center justify-center bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 rounded-lg border border-purple-100 dark:border-purple-900/50 shrink-0">
                        <i data-lucide="heart-handshake" class="w-4 h-4 shrink-0"></i>
                    </div>
                </div>
                <div class="mt-2 text-[10px] text-purple-600/80 dark:text-purple-400/80">
                    Berkebutuhan khusus
                </div>
            </div>

            <!-- Stat Card 5: Rombongan Belajar -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 lg:p-4 shadow-xs flex flex-col justify-between col-span-2 sm:col-span-1">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Rombel</p>
                        <h3 class="text-lg sm:text-xl font-black tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                            {{ number_format($stats['classrooms']) }}
                        </h3>
                    </div>
                    <div class="w-8 h-8 flex items-center justify-center bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-lg border border-emerald-100 dark:border-emerald-900/50 shrink-0">
                        <i data-lucide="layout-grid" class="w-4 h-4 shrink-0"></i>
                    </div>
                </div>
                <div class="mt-2 text-[10px] text-slate-400">
                    {{ $stats['classrooms'] }} Rombel (Kelas 1 - 6)
                </div>
            </div>
        </section>

        <!-- SEARCH & FILTERS -->
        <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 shadow-xs w-full">
            <form method="GET" action="{{ route('students.index') }}" class="flex flex-col lg:flex-row gap-2.5 items-stretch lg:items-center justify-between">
                <!-- Search Box -->
                <div class="relative w-full lg:max-w-xs">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIS, NIK, No. Ortu..."
                        style="padding-left: 2.25rem;"
                        class="w-full h-8.5 pr-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 dark:text-slate-50 placeholder-slate-400 shadow-inner">
                </div>

                <!-- Filter Select Toolbar -->
                <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                    <!-- Filter Tahun Ajaran -->
                    <select name="academic_year_id" onchange="this.form.submit()"
                        class="h-8.5 px-2.5 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer shadow-xs">
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ ($selectedYearName ?? '') == $year->name || $selectedYearId == $year->id ? 'selected' : '' }}>
                                TA {{ $year->name }} {{ $year->has_active || $year->is_active ? '★' : '' }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Filter Tingkat Kelas -->
                    <select name="class_level_id" onchange="this.form.submit()"
                        class="h-8.5 px-2.5 text-xs font-medium bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer shadow-xs">
                        <option value="all">Semua Tingkat</option>
                        @foreach($classLevels as $lvl)
                            <option value="{{ $lvl->id }}" {{ request('class_level_id') == $lvl->id ? 'selected' : '' }}>
                                {{ $lvl->name }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Filter Rombel -->
                    <select name="classroom_id" onchange="this.form.submit()"
                        class="h-8.5 px-2.5 text-xs font-medium bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer shadow-xs max-w-[150px] truncate">
                        <option value="all">Semua Rombel</option>
                        @foreach($classrooms as $rombel)
                            <option value="{{ $rombel->id }}" {{ request('classroom_id') == $rombel->id ? 'selected' : '' }}>
                                {{ $rombel->full_name }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Filter Tipe Siswa (Reguler / Inklusi) -->
                    <select name="student_type" onchange="this.form.submit()"
                        class="h-8.5 px-2.5 text-xs font-medium bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer shadow-xs">
                        <option value="">Semua Tipe</option>
                        <option value="REGULER" {{ request('student_type') == 'REGULER' ? 'selected' : '' }}>Reguler</option>
                        <option value="PDBK" {{ request('student_type') == 'PDBK' ? 'selected' : '' }}>PDBK (Inklusi)</option>
                    </select>

                    <!-- Filter Status -->
                    <select name="status" onchange="this.form.submit()"
                        class="h-8.5 px-2.5 text-xs font-medium bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer shadow-xs">
                        <option value="all">Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="lulus" {{ request('status') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                        <option value="mutasi" {{ request('status') == 'mutasi' ? 'selected' : '' }}>Mutasi</option>
                        <option value="keluar" {{ request('status') == 'keluar' ? 'selected' : '' }}>Keluar</option>
                        <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>

                    <!-- Filter Jumlah Baris (Per Page) -->
                    <select name="per_page" onchange="this.form.submit()"
                        class="h-8.5 px-2.5 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer shadow-xs"
                        title="Tampilkan jumlah baris per halaman">
                        <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15 baris</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 baris</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 baris</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 baris</option>
                        <option value="all" {{ request('per_page') === 'all' ? 'selected' : '' }}>Semua Baris</option>
                    </select>

                    @if(request()->hasAny(['search', 'academic_year_id', 'class_level_id', 'classroom_id', 'student_type', 'status', 'gender']) || (request('per_page') && request('per_page') != 15))
                        <a href="{{ route('students.index') }}" 
                            class="h-8.5 px-2.5 inline-flex items-center justify-center text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 rounded-lg border border-slate-200 dark:border-slate-700 transition-colors"
                            title="Reset Filter">
                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                        </a>
                    @endif
                </div>
            </form>
        </section>

        <!-- TABLE LIST SISWA -->
        <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs overflow-hidden w-full">
            <div class="overflow-x-auto">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-900/50">
                            <th class="w-12 sm:w-14 px-2 py-2.5 sm:py-3 text-center text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider shrink-0" style="width: 52px; max-width: 52px;">No</th>
                            <th class="px-3 py-2.5 sm:px-4 sm:py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-28 whitespace-nowrap">NIS</th>
                            <th class="px-3 py-2.5 sm:px-4 sm:py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-full min-w-[240px]">Nama Siswa & Tipe</th>
                            <th class="px-3 py-2.5 sm:px-4 sm:py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider min-w-[200px] sm:min-w-[220px]">Tingkat & Rombel</th>
                            <th class="px-3 py-2.5 sm:px-4 sm:py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-24 sm:w-28 whitespace-nowrap">L/P & Usia</th>
                            <th class="px-3 py-2.5 sm:px-4 sm:py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider min-w-[160px]">Orang Tua & WA</th>
                            <th class="px-3 py-2.5 sm:px-4 sm:py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-24 whitespace-nowrap">Status</th>
                            <th class="px-3 py-2.5 sm:px-4 sm:py-3 text-right text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-24 sm:w-28 whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        @forelse($students as $index => $s)
                            @php
                                $isPdbk = ($s->student_type && (str_contains(strtoupper($s->student_type), 'PDBK') || str_contains(strtoupper($s->student_type), 'KHUSUS'))) || !empty($s->special_needs_type);
                            @endphp
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors group">
                                <td class="w-12 sm:w-14 px-2 py-2.5 sm:py-3 text-center text-slate-400 font-mono text-[11px] whitespace-nowrap shrink-0" style="width: 52px; max-width: 52px;">
                                    {{ $students->firstItem() + $index }}
                                </td>
                                <td class="px-3 py-2.5 sm:px-4 sm:py-3 font-mono text-xs font-bold text-indigo-600 dark:text-indigo-400 whitespace-nowrap">
                                    {{ $s->nis }}
                                </td>
                                <td class="px-3 py-2.5 sm:px-4 sm:py-3">
                                    <div class="flex items-center gap-2.5 sm:gap-3">
                                        @if($s->student_photo_url)
                                            <img src="{{ $s->student_photo_url }}" width="32" height="32" alt="{{ $s->full_name }}" class="w-7 h-7 sm:w-8 sm:h-8 rounded-full object-cover ring-1 ring-slate-200 dark:ring-slate-700 shrink-0 aspect-square">
                                        @else
                                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-100 dark:border-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-xs shrink-0 aspect-square">
                                                {{ $s->avatar_initials }}
                                            </div>
                                        @endif
                                        <div class="flex flex-col">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <a href="{{ route('students.show', $s->id) }}" class="font-bold text-slate-900 dark:text-slate-100 text-xs tracking-tight hover:text-indigo-600 dark:hover:text-indigo-400">
                                                    {{ $s->full_name }}
                                                </a>
                                                @if($isPdbk)
                                                    <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800 shrink-0" title="{{ $s->special_needs_type ?: 'PDBK' }}">
                                                        PDBK
                                                    </span>
                                                    @if($s->gpkTeacher)
                                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded text-[10px] font-medium bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/60 shrink-0" title="Guru Pendamping Khusus: {{ $s->gpkTeacher->full_name }}">
                                                            <i data-lucide="user-check" class="w-2.5 h-2.5 shrink-0"></i>
                                                            <span>GPK: {{ Str::limit($s->gpkTeacher->name, 14) }}</span>
                                                        </span>
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-2 mt-0.5 text-[10px] text-slate-400">
                                                @if($s->nik)
                                                    <span>NIK: {{ $s->nik }}</span>
                                                @endif
                                                @if($s->spmb_candidate_id)
                                                    <span class="px-1.5 py-0.2 rounded bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 font-medium">SPMB</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-2.5 sm:px-4 sm:py-3 min-w-[200px] sm:min-w-[220px]">
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-slate-800 dark:text-slate-200 whitespace-nowrap">
                                            {{ $s->classroom ? $s->classroom->full_name : 'Belum Ditentukan' }}
                                        </span>
                                        <div class="flex items-center gap-1.5 mt-0.5 whitespace-nowrap">
                                            <span class="text-[11px] text-slate-400">
                                                {{ $s->classroom && $s->classroom->classLevel ? $s->classroom->classLevel->name : '-' }}
                                            </span>
                                            @if($s->academicYear)
                                                <span class="text-[10px] px-1.5 py-0.2 rounded bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 font-medium font-mono border border-indigo-100 dark:border-indigo-900/50 shrink-0">
                                                    {{ $s->academicYear->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-2.5 sm:px-4 sm:py-3">
                                    <div class="flex flex-col text-slate-600 dark:text-slate-300">
                                        <span class="font-medium">{{ $s->formatted_gender }}</span>
                                        <span class="text-[10px] text-slate-400">{{ $s->age ?: '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-3 py-2.5 sm:px-4 sm:py-3">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-slate-800 dark:text-slate-200 truncate max-w-[160px]">
                                            {{ $s->father_name ?: ($s->mother_name ?: ($s->guardian_name ?: '-')) }}
                                        </span>
                                        @if($s->clean_parent_phone)
                                            <a href="{{ $s->whatsapp_url }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-emerald-600 dark:text-emerald-400 hover:underline font-mono mt-0.5">
                                                <i data-lucide="message-circle" class="w-3 h-3 shrink-0"></i>
                                                {{ $s->parent_phone }}
                                            </a>
                                        @else
                                            <span class="text-[11px] text-slate-400">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-3 py-2.5 sm:px-4 sm:py-3">
                                    @if($s->status === 'aktif')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/40">
                                            Aktif
                                        </span>
                                    @elseif($s->status === 'lulus')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800/40">
                                            Lulus
                                        </span>
                                    @elseif($s->status === 'mutasi')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/40">
                                            Mutasi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                            {{ ucfirst($s->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 sm:px-4 sm:py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('students.show', $s->id) }}"
                                            class="w-7 h-7 inline-flex items-center justify-center hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-lg transition-colors cursor-pointer shrink-0"
                                            title="Lihat Detail Siswa (Dossier)">
                                            <i data-lucide="eye" class="w-4 h-4 shrink-0"></i>
                                        </a>
                                        <a href="{{ route('students.edit', $s->id) }}"
                                            class="w-7 h-7 inline-flex items-center justify-center hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 hover:text-amber-600 dark:hover:text-amber-400 rounded-lg transition-colors cursor-pointer shrink-0"
                                            title="Edit Data Siswa">
                                            <i data-lucide="edit-2" class="w-4 h-4 shrink-0"></i>
                                        </a>
                                        <button type="button" @click="confirmDeleteStudent({{ $s->id }}, '{{ addslashes($s->full_name) }}')"
                                            class="w-7 h-7 inline-flex items-center justify-center hover:bg-rose-50 dark:hover:bg-rose-950/40 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 rounded-lg transition-colors cursor-pointer shrink-0"
                                            title="Hapus Data Siswa">
                                            <i data-lucide="trash-2" class="w-4 h-4 shrink-0"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <i data-lucide="users" class="w-8 h-8 text-slate-300 dark:text-slate-600 mb-2"></i>
                                        <p class="font-semibold text-slate-600 dark:text-slate-400">Tidak ada data siswa ditemukan</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Coba sesuaikan filter atau tambahkan siswa baru / impor dari Excel.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500 dark:text-slate-400">
                <div>
                    Menampilkan <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $students->firstItem() ?? 0 }}</span> - <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $students->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $students->total() }}</span> siswa
                    @if(request('per_page') === 'all')
                        <span class="ml-1 text-indigo-600 dark:text-indigo-400 font-medium">(Semua ditampilkan)</span>
                    @endif
                </div>
                @if($students->hasPages())
                    <div>
                        {{ $students->links() }}
                    </div>
                @endif
            </div>
        </section>

        <!-- MODAL IMPOR EXCEL SISWA -->
        <!-- ========================================================= -->
        <div x-show="importModalOpen" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4" style="display: none; margin-top: 0px !important; z-index: 9999; background-color: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px);">
            <div @click.outside="importModalOpen = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl flex flex-col">
                
                <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- Header -->
                    <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between sticky top-0 bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm z-10">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400 font-bold text-sm flex items-center justify-center">
                                <i data-lucide="file-spreadsheet" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-slate-50">Impor Siswa Massal (Excel)</h3>
                                <p class="text-xs text-slate-400 mt-0.5">Unggah database siswa untuk kelas berjalan (Kelas 1–6).</p>
                            </div>
                        </div>
                        <button type="button" @click="importModalOpen = false" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="p-6 space-y-4 text-xs">
                        <div class="p-4 rounded-xl bg-indigo-50/60 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/50 space-y-2.5">
                            <div class="flex items-center justify-between gap-3">
                                <span class="font-bold text-indigo-900 dark:text-indigo-300">Format Template Excel</span>
                                <a href="{{ route('students.download-template') }}" 
                                    class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold transition-all shadow-xs cursor-pointer">
                                    <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                    <span>Unduh Template (.xlsx)</span>
                                </a>
                            </div>
                            <p class="text-[11px] text-indigo-800/80 dark:text-indigo-300/80 leading-relaxed">
                                Gunakan template resmi untuk mengisi data siswa. Di dalam file Excel terdapat lembar <strong>"Referensi Rombel & Tahun Ajaran"</strong> untuk melihat daftar nama rombel yang aktif di sistem.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Rombel Tujuan (Default)</label>
                                <select name="default_classroom_id"
                                    class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-slate-900 dark:text-slate-50 cursor-pointer">
                                    <option value="">Gunakan kolom di Excel (Otomatis)</option>
                                    @foreach($allClassrooms as $r)
                                        <option value="{{ $r->id }}">{{ $r->full_name }} (TA {{ $r->academicYear->name ?? '-' }})</option>
                                    @endforeach
                                </select>
                                <p class="text-[10px] text-slate-400 mt-1">Pilih rombel jika file tidak memuat kolom kelas, atau pilih "Gunakan kolom di Excel" jika rombel tercantum per baris.</p>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tahun Ajaran Default</label>
                                <select name="default_academic_year_id"
                                    class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-slate-900 dark:text-slate-50 cursor-pointer">
                                    @foreach($academicYears as $y)
                                        <option value="{{ $y->id }}" {{ $y->has_active || $y->is_active ? 'selected' : '' }}>TA {{ $y->name }} {{ ($y->has_active || $y->is_active) ? '(Aktif)' : '' }}</option>
                                    @endforeach
                                </select>
                                <p class="text-[10px] text-slate-400 mt-1">Tahun ajaran acuan jika kolom tahun ajaran di Excel kosong.</p>
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Pilih File Excel (.xlsx, .xls, .csv) <span class="text-rose-500">*</span></label>
                            <input type="file" name="file" required accept=".xlsx,.xls,.csv"
                                class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-950/60 dark:file:text-indigo-300 hover:file:bg-indigo-100 border border-slate-200 dark:border-slate-800 rounded-lg p-1.5 bg-white dark:bg-slate-900">
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex justify-end gap-2">
                        <button type="button" @click="importModalOpen = false" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition-all shadow-xs flex items-center gap-1.5">
                            <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                            Mulai Impor Siswa
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
</x-admin-layout>
