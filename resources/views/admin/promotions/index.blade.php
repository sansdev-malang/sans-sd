<x-admin-layout>
    <div class="p-6 space-y-6" x-data="promotionApp()">

        <!-- GREETING / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <div class="flex items-center gap-2.5">
                    <div class="p-2 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-xl border border-indigo-500/20">
                        <i data-lucide="arrow-up-circle" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 flex items-center gap-2">
                            Kenaikan & Kelulusan Kelas
                            <span class="text-xs px-2.5 py-0.5 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-400 font-semibold border border-indigo-200 dark:border-indigo-800">
                                Transisi T.A.
                            </span>
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Pindahkan rombel siswa secara massal, kelola siswa tinggal kelas, dan proses kelulusan angkatan kelas 6.</p>
                    </div>
                </div>
            </div>
            <!-- ACTION CONTROLS -->
            <div class="flex items-center gap-2.5">
                <a href="{{ route('students.index') }}"
                    class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 shadow-xs transition-all">
                    <i data-lucide="users" class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400"></i>
                    Kembali ke Data Siswa
                </a>
            </div>
        </section>

        <!-- MAIN TABS: 1. Kenaikan Kelas (Kelas 1-5), 2. Kelulusan (Kelas 6) -->
        <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800">
            <button type="button" @click="activeTab = 'promotion'"
                :class="activeTab === 'promotion' ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400'"
                class="px-4 py-3 border-b-2 font-bold text-xs flex items-center gap-2 transition-all">
                <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                1. Kenaikan Kelas Massal (Kelas 1–5)
            </button>
            <button type="button" @click="activeTab = 'graduation'"
                :class="activeTab === 'graduation' ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400'"
                class="px-4 py-3 border-b-2 font-bold text-xs flex items-center gap-2 transition-all">
                <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                2. Kelulusan Siswa (Kelas 6)
            </button>
        </div>

        <!-- ========================================================= -->
        <!-- TAB 1: KENAIKAN KELAS MASSAL (KELAS 1 - 5) -->
        <!-- ========================================================= -->
        <div x-show="activeTab === 'promotion'" class="space-y-6">

            <!-- SELECTION TOOLBAR CARD -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-xs space-y-4">
                <h3 class="font-bold text-slate-900 dark:text-slate-50 text-xs uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                    <span class="w-5 h-5 rounded-full bg-indigo-600 text-white text-[11px] flex items-center justify-center font-bold">1</span>
                    Pilih Rombel Asal & Rombel Tujuan Kenaikan Kelas
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- 1. Tahun Pelajaran Asal -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Tapel Asal</label>
                        <select x-model="sourceYearId" @change="onSourceYearChange()"
                            class="w-full h-9 px-3 text-xs bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-semibold cursor-pointer">
                            @foreach($academicYears as $y)
                                <option value="{{ $y->id }}">{{ $y->name }} {{ $y->is_active ? '(Aktif)' : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 2. Rombel Asal -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Rombel Asal (Kelas 1–5) <span class="text-rose-500">*</span></label>
                        <select x-model="sourceClassroomId" @change="fetchStudents()"
                            class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-semibold cursor-pointer">
                            <option value="">-- Pilih Rombel Asal --</option>
                            <template x-for="cr in filteredSourceClassrooms" :key="cr.id">
                                <option :value="cr.id" x-text="cr.name + ' (' + (cr.class_level ? cr.class_level.name : 'Tingkat') + ')'"></option>
                            </template>
                        </select>
                    </div>

                    <!-- 3. Tahun Pelajaran Tujuan -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Tapel Tujuan Baru <span class="text-rose-500">*</span></label>
                        <select x-model="targetYearId" @change="onTargetYearChange()"
                            class="w-full h-9 px-3 text-xs bg-indigo-50/50 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-bold text-indigo-900 dark:text-indigo-300 cursor-pointer">
                            @foreach($academicYears as $y)
                                <option value="{{ $y->id }}">Tapel {{ $y->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 4. Rombel Tujuan Default -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Rombel Tujuan Naik Kelas <span class="text-rose-500">*</span></label>
                        <select x-model="defaultTargetClassroomId" @change="applyDefaultTarget()"
                            class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-semibold cursor-pointer">
                            <option value="">-- Pilih Rombel Tujuan --</option>
                            <template x-for="cr in filteredTargetClassrooms" :key="cr.id">
                                <option :value="cr.id" x-text="cr.name + ' (' + (cr.class_level ? cr.class_level.name : 'Tingkat') + ')'"></option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>

            <!-- STUDENT LIST FOR PROMOTION -->
            <div x-show="sourceClassroomId" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs overflow-hidden space-y-0">
                
                <!-- Table Header & Summary Bar -->
                <div class="p-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/80 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-900 dark:text-slate-50 text-xs">
                            Daftar Siswa (<span x-text="students.length"></span> siswa)
                        </span>
                        <span class="text-[11px] text-slate-400" x-text="currentClassroomName"></span>
                    </div>

                    <!-- Summary Badges -->
                    <div class="flex items-center gap-2 text-xs flex-wrap">
                        <span class="px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 font-semibold border border-emerald-200 dark:border-emerald-800">
                            Naik Kelas: <strong x-text="countPromoted()"></strong>
                        </span>
                        <span class="px-2.5 py-1 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 font-semibold border border-amber-200 dark:border-amber-800">
                            Tinggal Kelas: <strong x-text="countStayed()"></strong>
                        </span>
                        <span class="px-2.5 py-1 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 font-semibold border border-rose-200 dark:border-rose-800">
                            Mutasi: <strong x-text="countTransferred()"></strong>
                        </span>
                    </div>
                </div>

                <!-- Students Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/40 dark:bg-slate-900/40">
                                <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase w-12">No</th>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase w-24">NIS</th>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase">Nama Siswa & Tipe</th>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase w-28">L/P</th>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase w-72">Status Kenaikan</th>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase w-60">Rombel Tujuan Spesifik</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                            <template x-for="(s, idx) in students" :key="s.id">
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="px-4 py-3 text-slate-400 font-mono text-[11px]" x-text="idx + 1"></td>
                                    <td class="px-4 py-3 font-mono font-bold text-indigo-600 dark:text-indigo-400" x-text="s.nis"></td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-slate-900 dark:text-slate-100" x-text="s.full_name"></span>
                                            <template x-if="s.student_type && s.student_type.includes('PDBK')">
                                                <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                                    PDBK
                                                </span>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400" x-text="s.gender === 'L' ? 'Laki-laki' : 'Perempuan'"></td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-1.5">
                                            <button type="button" @click="s.action = 'promote'"
                                                :class="s.action === 'promote' ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200'"
                                                class="px-2.5 py-1 rounded-md text-[11px] transition-all">
                                                🟢 Naik
                                            </button>
                                            <button type="button" @click="s.action = 'stay'"
                                                :class="s.action === 'stay' ? 'bg-amber-600 text-white font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200'"
                                                class="px-2.5 py-1 rounded-md text-[11px] transition-all">
                                                🟡 Tinggal Kelas
                                            </button>
                                            <button type="button" @click="s.action = 'transfer_out'"
                                                :class="s.action === 'transfer_out' ? 'bg-rose-600 text-white font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200'"
                                                class="px-2.5 py-1 rounded-md text-[11px] transition-all">
                                                🔴 Mutasi
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <template x-if="s.action === 'promote'">
                                            <select x-model="s.target_classroom_id"
                                                class="w-full h-8 px-2 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg">
                                                <option value="">Gunakan Rombel Default</option>
                                                <template x-for="cr in filteredTargetClassrooms" :key="cr.id">
                                                    <option :value="cr.id" x-text="cr.name"></option>
                                                </template>
                                            </select>
                                        </template>
                                        <template x-if="s.action === 'stay'">
                                            <select x-model="s.target_classroom_id"
                                                class="w-full h-8 px-2 text-xs bg-amber-50/50 dark:bg-amber-950/30 border border-amber-300 dark:border-amber-700 rounded-lg text-amber-900 dark:text-amber-300 font-semibold">
                                                <option value="">Pilih Rombel Mengulang...</option>
                                                <template x-for="cr in allClassroomsForStay" :key="cr.id">
                                                    <option :value="cr.id" x-text="cr.name"></option>
                                                </template>
                                            </select>
                                        </template>
                                        <template x-if="s.action === 'transfer_out'">
                                            <span class="text-[11px] font-semibold text-rose-600 dark:text-rose-400 italic">Siswa keluar/mutasi sekolah</span>
                                        </template>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="students.length === 0 && !loading">
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                                        Tidak ada siswa aktif di rombel ini.
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Footer Execution Bar -->
                <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/80 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Pastikan seluruh status dan rombel tujuan telah sesuai sebelum mengeksekusi kenaikan kelas.
                    </p>
                    <button type="button" @click="submitPromotion()" :disabled="processing || students.length === 0"
                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white rounded-xl text-xs font-bold transition-all shadow-md flex items-center gap-2 cursor-pointer">
                        <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                        <span x-text="processing ? 'Memproses Kenaikan...' : 'Eksekusi Kenaikan Kelas Massal'"></span>
                    </button>
                </div>

            </div>

        </div>

        <!-- ========================================================= -->
        <!-- TAB 2: KELULUSAN SISWA KELAS 6 -->
        <!-- ========================================================= -->
        <div x-show="activeTab === 'graduation'" class="space-y-6">

            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-xs space-y-4">
                <h3 class="font-bold text-slate-900 dark:text-slate-50 text-xs uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                    <span class="w-5 h-5 rounded-full bg-blue-600 text-white text-[11px] flex items-center justify-center font-bold">🎓</span>
                    Proses Kelulusan Angkatan Kelas 6
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Rombel Kelas 6 <span class="text-rose-500">*</span></label>
                        <select x-model="gradClassroomId" @change="fetchGradStudents()"
                            class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 font-semibold cursor-pointer">
                            <option value="">-- Pilih Rombel Kelas 6 --</option>
                            @foreach($grade6Classrooms as $cr)
                                <option value="{{ $cr->id }}">{{ $cr->name }} (Tapel {{ $cr->academicYear->name ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Tahun Pelajaran Kelulusan (Tapel) <span class="text-rose-500">*</span></label>
                        <select x-model="gradAcademicYearId"
                            class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 font-bold text-blue-900 dark:text-blue-300 cursor-pointer">
                            @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}">Tapel {{ $ay->name }} {{ $ay->is_active ? '(Aktif)' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- GRADE 6 STUDENTS LIST -->
            <div x-show="gradClassroomId" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs overflow-hidden space-y-0">
                
                <div class="p-4 border-b border-slate-200 dark:border-slate-800 bg-blue-50/40 dark:bg-blue-950/20 flex justify-between items-center">
                    <span class="font-bold text-blue-900 dark:text-blue-200 text-xs">
                        Calon Wisudawan / Siswa Kelas 6 (<span x-text="gradStudents.length"></span> siswa)
                    </span>
                    <span class="text-[11px] text-blue-600 dark:text-blue-400 font-semibold" x-text="'TP Kelulusan: ' + (allAcademicYears.find(y => y.id == gradAcademicYearId)?.name || '-')"></span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/40 dark:bg-slate-900/40">
                                <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase w-12">No</th>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase w-24">NIS</th>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase">Nama Siswa</th>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase w-28">L/P</th>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase w-72">Nomor Ijazah (Opsional)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                            <template x-for="(s, idx) in gradStudents" :key="s.id">
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="px-4 py-3 text-slate-400 font-mono text-[11px]" x-text="idx + 1"></td>
                                    <td class="px-4 py-3 font-mono font-bold text-blue-600 dark:text-blue-400" x-text="s.nis"></td>
                                    <td class="px-4 py-3 font-bold text-slate-900 dark:text-slate-100" x-text="s.full_name"></td>
                                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400" x-text="s.gender === 'L' ? 'Laki-laki' : 'Perempuan'"></td>
                                    <td class="px-4 py-3">
                                        <input type="text" x-model="s.diploma_number" placeholder="No. Seri Ijazah..."
                                            class="w-full h-8 px-2.5 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg font-mono">
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/80 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Siswa yang diluluskan akan diubah statusnya menjadi <strong>Lulus</strong> dan otomatis tercatat di Buku Induk Alumni.
                    </p>
                    <button type="button" @click="submitGraduation()" :disabled="gradProcessing || gradStudents.length === 0"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white rounded-xl text-xs font-bold transition-all shadow-md flex items-center gap-2 cursor-pointer">
                        <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                        <span x-text="gradProcessing ? 'Memproses Kelulusan...' : 'Proses Kelulusan Angkatan'"></span>
                    </button>
                </div>

            </div>

        </div>

    </div>

    <!-- Alpine.js Application Logic -->
    <script>
        function promotionApp() {
            const allClassroomsData = @json($allClassrooms);
            const sourceClassroomsData = @json($sourceClassrooms);
            const allAcademicYearsData = @json($academicYears);

            return {
                activeTab: 'promotion',
                sourceYearId: '{{ $sourceYear?->id }}',
                targetYearId: '{{ $academicYears->count() > 1 ? $academicYears[0]->id : ($activeAcademicYear?->id ?? "") }}',
                sourceClassroomId: '',
                defaultTargetClassroomId: '',
                currentClassroomName: '',
                students: [],
                loading: false,
                processing: false,

                // Graduation
                gradClassroomId: '',
                gradAcademicYearId: '{{ $sourceYear?->id ?? $activeAcademicYear?->id }}',
                gradStudents: [],
                gradProcessing: false,

                allAcademicYears: allAcademicYearsData,
                allClassrooms: allClassroomsData,
                sourceClassrooms: sourceClassroomsData,

                get filteredSourceClassrooms() {
                    return this.allClassrooms.filter(c => c.academic_year_id == this.sourceYearId);
                },

                get filteredTargetClassrooms() {
                    return this.allClassrooms.filter(c => c.academic_year_id == this.targetYearId);
                },

                get allClassroomsForStay() {
                    return this.allClassrooms.filter(c => c.academic_year_id == this.targetYearId);
                },

                onSourceYearChange() {
                    this.sourceClassroomId = '';
                    this.students = [];
                },

                onTargetYearChange() {
                    this.defaultTargetClassroomId = '';
                },

                fetchStudents() {
                    if (!this.sourceClassroomId) {
                        this.students = [];
                        return;
                    }

                    this.loading = true;
                    fetch(`/class-promotions/students?classroom_id=${this.sourceClassroomId}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => res.json())
                    .then(res => {
                        this.loading = false;
                        if (res.success) {
                            this.currentClassroomName = res.classroom?.name || '';
                            this.students = res.students.map(s => ({
                                ...s,
                                action: 'promote',
                                target_classroom_id: this.defaultTargetClassroomId || '',
                            }));
                        } else {
                            alert(res.message || 'Gagal memuat siswa.');
                        }
                    })
                    .catch(err => {
                        this.loading = false;
                        alert('Error: ' + err.message);
                    });
                },

                applyDefaultTarget() {
                    this.students.forEach(s => {
                        if (s.action === 'promote') {
                            s.target_classroom_id = this.defaultTargetClassroomId;
                        }
                    });
                },

                countPromoted() {
                    return this.students.filter(s => s.action === 'promote').length;
                },

                countStayed() {
                    return this.students.filter(s => s.action === 'stay').length;
                },

                countTransferred() {
                    return this.students.filter(s => s.action === 'transfer_out').length;
                },

                submitPromotion() {
                    if (!this.sourceClassroomId || !this.targetYearId) {
                        alert('Silakan pilih Rombel Asal dan Tahun Pelajaran Tujuan.');
                        return;
                    }

                    if (!confirm(`Apakah Anda yakin ingin memproses kenaikan kelas untuk ${this.students.length} siswa ini?`)) {
                        return;
                    }

                    this.processing = true;

                    const payload = {
                        source_classroom_id: this.sourceClassroomId,
                        target_academic_year_id: this.targetYearId,
                        default_target_classroom_id: this.defaultTargetClassroomId || null,
                        students: this.students.map(s => ({
                            student_id: s.id,
                            action: s.action,
                            target_classroom_id: s.target_classroom_id || this.defaultTargetClassroomId || null,
                        }))
                    };

                    fetch('/class-promotions/process', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(res => res.json())
                    .then(res => {
                        this.processing = false;
                        if (res.success) {
                            alert(res.message);
                            window.location.reload();
                        } else {
                            alert('Gagal: ' + res.message);
                        }
                    })
                    .catch(err => {
                        this.processing = false;
                        alert('Error: ' + err.message);
                    });
                },

                // Graduation
                fetchGradStudents() {
                    if (!this.gradClassroomId) {
                        this.gradStudents = [];
                        return;
                    }

                    fetch(`/class-promotions/students?classroom_id=${this.gradClassroomId}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            this.gradStudents = res.students.map(s => ({
                                ...s,
                                diploma_number: s.diploma_number || '',
                            }));
                        }
                    })
                    .catch(err => alert('Error: ' + err.message));
                },

                submitGraduation() {
                    if (!this.gradClassroomId || !this.gradAcademicYearId) {
                        alert('Silakan pilih Rombel Kelas 6 dan Tahun Pelajaran Kelulusan.');
                        return;
                    }

                    if (!confirm(`Apakah Anda yakin ingin meluluskan ${this.gradStudents.length} siswa kelas 6 ini?`)) {
                        return;
                    }

                    this.gradProcessing = true;

                    const payload = {
                        classroom_id: this.gradClassroomId,
                        academic_year_id: this.gradAcademicYearId,
                        students: this.gradStudents.map(s => ({
                            student_id: s.id,
                            diploma_number: s.diploma_number,
                        }))
                    };

                    fetch('/class-promotions/graduate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(res => res.json())
                    .then(res => {
                        this.gradProcessing = false;
                        if (res.success) {
                            alert(res.message);
                            window.location.reload();
                        } else {
                            alert('Gagal: ' + res.message);
                        }
                    })
                    .catch(err => {
                        this.gradProcessing = false;
                        alert('Error: ' + err.message);
                    });
                }
            };
        }
    </script>
</x-admin-layout>
