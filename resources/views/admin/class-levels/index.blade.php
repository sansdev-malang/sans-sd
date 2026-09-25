<x-admin-layout>
    <div class="p-6 space-y-6" x-data="classLevelApp()">

        <!-- GREETING / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <div class="flex items-center gap-2.5">
                    <div class="p-2 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-xl border border-indigo-500/20">
                        <i data-lucide="layers" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 flex items-center gap-2">
                            Tingkat Kelas
                            <span class="text-xs px-2.5 py-0.5 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-400 font-semibold border border-indigo-200 dark:border-indigo-800">
                                Master Akademik
                            </span>
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Kelola master jenjang dan tingkatan kelas di {{ setting('app_name', 'SD Anak Saleh') }} (Kelas 1 s/d Kelas 6).</p>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <a href="{{ route('classrooms.index') }}"
                    class="inline-flex items-center justify-center gap-2 px-3.5 py-2 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 shadow-xs transition-all duration-100 cursor-pointer">
                    <i data-lucide="university" class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400"></i>
                    Rombongan Belajar
                </a>
                <button type="button" @click="openCreateModal()"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all duration-100 cursor-pointer">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    Tambah Tingkat Kelas
                </button>
            </div>
        </section>

        <!-- STATS CARDS GRID -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Stat 1: Total Tingkat -->
            <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Tingkat Kelas</p>
                        <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">{{ number_format($stats['total_levels']) }}</h3>
                    </div>
                    <div class="p-2.5 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 rounded-xl border border-indigo-100 dark:border-indigo-900/50">
                        <i data-lucide="layers" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="mt-3 text-[11px] text-slate-500 dark:text-slate-400">
                    Jenjang terdaftar di SD
                </div>
            </div>

            <!-- Stat 2: Total Rombel -->
            <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Rombel</p>
                        <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">{{ number_format($stats['total_classrooms']) }}</h3>
                    </div>
                    <div class="p-2.5 bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 rounded-xl border border-blue-100 dark:border-blue-900/50">
                        <i data-lucide="university" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="mt-3 text-[11px] text-slate-500 dark:text-slate-400">
                    Rombel aktif di semua jenjang
                </div>
            </div>

            <!-- Stat 3: Total Kapasitas -->
            <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Kapasitas Kursi</p>
                        <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">{{ number_format($stats['total_capacity']) }}</h3>
                    </div>
                    <div class="p-2.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-xl border border-emerald-100 dark:border-emerald-900/50">
                        <i data-lucide="door-open" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="mt-3 text-[11px] text-slate-500 dark:text-slate-400">
                    Kapasitas daya tampung siswa
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
                    Siswa terdaftar saat ini
                </div>
            </div>
        </section>

        <!-- TABLE DAFTAR TINGKAT KELAS -->
        <section class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs overflow-hidden transition-all w-full">
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        <i data-lucide="list-ordered" class="w-4 h-4 text-indigo-600"></i>
                        Daftar Tingkat & Jenjang Kelas
                    </h3>
                    @php
                        $currentSelectedYear = $academicYears->firstWhere('id', $selectedYearId);
                    @endphp
                    @if($currentSelectedYear)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $currentSelectedYear->is_active ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950/70 dark:text-indigo-300 border border-indigo-300 dark:border-indigo-800' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700' }}">
                            <i data-lucide="calendar" class="w-3 h-3"></i>
                            Tapel {{ $currentSelectedYear->name }} {{ $currentSelectedYear->is_active ? '(Aktif)' : '' }}
                        </span>
                    @endif
                </div>

                <!-- Filter Tahun Pelajaran (Tapel) -->
                <form method="GET" action="{{ route('class-levels.index') }}" class="flex items-center gap-2">
                    <label class="text-xs text-slate-500 dark:text-slate-400 font-medium whitespace-nowrap">Filter Tapel:</label>
                    <select name="academic_year_id" onchange="this.form.submit()"
                        class="h-9 px-3 text-xs font-medium bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer shadow-xs">
                        @foreach($academicYears as $ay)
                            <option value="{{ $ay->id }}" {{ $selectedYearId == $ay->id ? 'selected' : '' }}>
                                Tapel {{ $ay->name }} {{ $ay->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-900/50">
                            <th class="px-5 py-3.5 text-center text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-16">Urutan</th>
                            <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Tingkat Kelas</th>
                            <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-28">Kode</th>
                            <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Rombel Terdaftar</th>
                            <th class="px-5 py-3.5 text-center text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-28">Siswa Aktif</th>
                            <th class="px-5 py-3.5 text-right text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        @forelse($classLevels as $lvl)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors group">
                                <td class="px-5 py-3.5 text-center">
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-800 font-mono font-bold text-slate-700 dark:text-slate-300 text-xs">
                                        {{ $lvl->order }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-100 dark:border-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ $lvl->code }}
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-900 dark:text-slate-100 text-xs">
                                                {{ $lvl->name }}
                                            </span>
                                            @if($lvl->description)
                                                <p class="text-[11px] text-slate-400 mt-0.5">{{ $lvl->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="px-2.5 py-1 rounded-md text-[11px] font-mono font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                        {{ $lvl->code }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex flex-wrap gap-1.5">
                                        @forelse($lvl->classrooms as $rombel)
                                            <a href="{{ route('classrooms.index', ['academic_year_id' => $selectedYearId, 'class_level_id' => $lvl->id]) }}"
                                                class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/40 dark:hover:bg-indigo-900/60 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900/50 transition-colors"
                                                title="Lihat Rombel {{ $rombel->name }}">
                                                {{ $rombel->name }}
                                            </a>
                                        @empty
                                            <span class="text-slate-400 italic text-[11px]">Tidak ada rombel di Tapel ini</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="font-bold text-indigo-600 dark:text-indigo-400 font-mono text-sm">{{ $lvl->active_students_count }}</span>
                                    <span class="text-slate-400 text-[10px] block">siswa</span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="button" @click="openEditModal({{ $lvl->id }})"
                                            class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 rounded-lg transition-colors cursor-pointer"
                                            title="Edit Tingkat Kelas">
                                            <i data-lucide="edit-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    Belum ada data Tingkat Kelas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- MODAL TAMBAH / EDIT TINGKAT KELAS -->
        <div x-show="modalOpen" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto" style="display: none; margin-top: 0px !important; z-index: 9999;"
            @click.self="modalOpen = false"
            @keydown.escape.window="modalOpen = false">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl flex flex-col relative my-auto"
                @click.stop>
                
                <form @submit.prevent="submitForm">
                    <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between sticky top-0 bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm z-10">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-slate-50" x-text="isEdit ? 'Edit Tingkat Kelas' : 'Tambah Tingkat Kelas Baru'"></h3>
                            <p class="text-xs text-slate-400 mt-0.5">Atur nama jenjang, kode, dan nomor urutan tampil.</p>
                        </div>
                        <button type="button" @click="modalOpen = false" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <div class="p-6 space-y-4 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Tingkat Kelas <span class="text-rose-500">*</span></label>
                            <input type="text" x-model="formData.name" required placeholder="Contoh: Kelas 1"
                                class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 dark:text-slate-50">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode Singkatan <span class="text-rose-500">*</span></label>
                                <input type="text" x-model="formData.code" required placeholder="Contoh: 1"
                                    class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 dark:text-slate-50 font-mono uppercase">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. Urut Tampil <span class="text-rose-500">*</span></label>
                                <input type="number" x-model.number="formData.order" required min="1" max="99" placeholder="1, 2, 3..."
                                    class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 dark:text-slate-50">
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Keterangan / Sasaran Usia</label>
                            <textarea x-model="formData.description" rows="3" placeholder="Contoh: Jenjang Sekolah Dasar Kelas 1..."
                                class="w-full p-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 dark:text-slate-50"></textarea>
                        </div>
                    </div>

                    <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex justify-end gap-2">
                        <button type="button" @click="modalOpen = false" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold transition-colors cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" :disabled="saving" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white rounded-lg text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                            <span x-show="saving" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                            <span x-text="saving ? 'Menyimpan...' : (isEdit ? 'Simpan Perubahan' : 'Tambah Tingkat Kelas')"></span>
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

    <!-- Alpine.js Application Logic -->
    <script>
        function classLevelApp() {
            return {
                modalOpen: false,
                isEdit: false,
                saving: false,
                formData: {
                    id: null,
                    name: '',
                    code: '',
                    order: 1,
                    description: '',
                },
                confirmModal: {
                    open: false,
                    id: null,
                    title: '',
                    message: '',
                    loading: false
                },

                openCreateModal() {
                    this.isEdit = false;
                    this.formData = {
                        id: null,
                        name: '',
                        code: '',
                        order: 1,
                        description: '',
                    };
                    this.modalOpen = true;
                    this.$nextTick(() => {
                        if (window.lucide) lucide.createIcons();
                    });
                },

                openEditModal(id) {
                    fetch(`/class-levels/${id}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            const l = res.class_level;
                            this.isEdit = true;
                            this.formData = {
                                id: l.id,
                                name: l.name,
                                code: l.code,
                                order: l.order || 1,
                                description: l.description || '',
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

                    const url = this.isEdit ? `/class-levels/${this.formData.id}` : '/class-levels';
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
                            this.modalOpen = false;
                            if (window.showToastNotification) {
                                window.showToastNotification(data.message || 'Tingkat kelas berhasil disimpan!', 'success');
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

                confirmDeleteLevel(id, name) {
                    this.confirmModal = {
                        open: true,
                        id: id,
                        title: 'Hapus Tingkat Kelas?',
                        message: `Apakah Anda yakin ingin menghapus Tingkat Kelas <strong>${name}</strong>? Data yang telah dihapus tidak dapat dipulihkan.`,
                        loading: false
                    };
                    this.$nextTick(() => {
                        if (window.lucide) lucide.createIcons();
                    });
                },

                executeConfirmDelete() {
                    if (this.confirmModal.loading) return;
                    this.confirmModal.loading = true;

                    fetch(`/class-levels/${this.confirmModal.id}`, {
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
                            if (window.showToastNotification) {
                                window.showToastNotification(data.message || 'Tingkat kelas berhasil dihapus!', 'success');
                            }
                            setTimeout(() => window.location.reload(), 500);
                        } else {
                            const errMsg = data.message || 'Gagal menghapus tingkat kelas.';
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
