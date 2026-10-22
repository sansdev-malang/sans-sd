@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag();
    $student = $student ?? new \App\Models\Student();
    $classLevels = $classLevels ?? \App\Models\ClassLevel::orderBy('order', 'asc')->get();
    $fd = old('family_data', $student->family_data ?? []);
    $hh = old('health_habits', $student->health_habits ?? []);
    $dp = old('development_psychology', $student->development_psychology ?? []);
    $docs = old('documents', $student->documents ?? []);
    $checklist = old('checklist_documents', $student->checklist_documents ?? []);
@endphp

<div x-data="{
    activeTab: '{{ (isset($errors) && $errors->any()) ? ($errors->hasAny(["address", "dusun", "rt", "rw", "village", "district", "city", "province", "postal_code", "residence_status", "living_with", "transportation"]) ? "domisili" : ($errors->hasAny(["father_name", "father_phone", "mother_name", "mother_phone", "guardian_name", "guardian_phone", "parent_phone", "parent_email"]) ? "keluarga" : ($errors->hasAny(["height", "weight", "blood_type", "severe_disease_history"]) ? "kesehatan" : "identitas"))) : "identitas" }}',
    isSaving: false,
    toastMessage: '',
    toastType: 'success',
    showToast: false,
    errorList: [],
    showErrorBox: false,
    selectedYear: '{{ old('academic_year_id', $student->academic_year_id ?? $activeAcademicYear?->id ?? '') }}',
    selectedLevel: '{{ old('class_level_id', $student->class_level_id ?? $student->classroom?->class_level_id ?? '') }}',
    selectedClassroom: '{{ old('classroom_id', $student->classroom_id ?? '') }}',
    studentType: '{{ old('student_type', $student->student_type ?? 'Reguler') }}',
    hasGuardian: {{ old('guardian_name', $student->guardian_name) ? 'true' : 'false' }},
    photoPreview: '{{ $student->student_photo_url ?? '' }}',
    classrooms: {{ json_encode($classrooms->map(fn($c) => [
        'id' => $c->id,
        'name' => $c->name,
        'code' => $c->code,
        'class_level_id' => $c->class_level_id,
        'academic_year_id' => $c->academic_year_id,
    ])) }},

    canScrollLeft: false,
    canScrollRight: false,
    checkTabScroll() {
        const el = this.$refs.tabScroll;
        if (!el) return;
        this.canScrollLeft = el.scrollLeft > 10;
        this.canScrollRight = el.scrollLeft < (el.scrollWidth - el.clientWidth - 10);
    },
    scrollTabsLeft() {
        if (this.$refs.tabScroll) {
            this.$refs.tabScroll.scrollBy({ left: -220, behavior: 'smooth' });
        }
    },
    scrollTabsRight() {
        if (this.$refs.tabScroll) {
            this.$refs.tabScroll.scrollBy({ left: 220, behavior: 'smooth' });
        }
    },

    onYearChange() {
        const currentClass = this.classrooms.find(c => c.id == this.selectedClassroom);
        if (currentClass && currentClass.academic_year_id != this.selectedYear) {
            this.selectedClassroom = '';
        }
    },

    async saveAndNext(nextTab, isFinal = false) {
        if (this.isSaving) return;
        this.isSaving = true;
        this.showErrorBox = false;
        this.errorList = [];

        const form = this.$el.closest('form');
        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            });

            const result = await response.json().catch(() => null);

            if (!response.ok) {
                if (response.status === 422 && result && result.errors) {
                    this.showErrorBox = true;
                    this.errorList = Object.values(result.errors).flat();
                    this.triggerToast('Terdapat data yang belum sesuai. Mohon periksa kembali kolom yang bertanda bintang.', 'error');
                } else {
                    this.triggerToast((result && result.message) || 'Gagal menyimpan data siswa. Silakan coba lagi.', 'error');
                }
                this.isSaving = false;
                window.scrollTo({ top: 0, behavior: 'smooth' });
                return;
            }

            // Success handling
            if (isFinal) {
                this.triggerToast('Seluruh data siswa berhasil disimpan! Mengalihkan ke profil...', 'success');
                setTimeout(() => {
                    if (result && result.redirect_url) {
                        window.location.href = result.redirect_url;
                    } else if (result && result.student && result.student.id) {
                        window.location.href = `/students/${result.student.id}`;
                    } else {
                        window.location.href = form.action;
                    }
                }, 700);
            } else {
                if (result && result.student && result.student.id) {
                    form.action = `/students/${result.student.id}`;
                    let methodInput = form.querySelector('input[name=\'_method\']');
                    if (!methodInput) {
                        methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'PUT';
                        form.appendChild(methodInput);
                    }
                }

                this.triggerToast('Data tab berhasil disimpan! Beralih ke langkah berikutnya...', 'success');
                if (nextTab) {
                    this.activeTab = nextTab;
                    this.$nextTick(() => {
                        const btn = this.$refs.tabScroll?.querySelector(`button[data-tab='${this.activeTab}']`);
                        if (btn) btn.scrollIntoView({ behavior: 'smooth', inline: 'nearest', block: 'nearest' });
                    });
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    setTimeout(() => {
                        if (window.lucide) window.lucide.createIcons();
                    }, 50);
                }
                this.isSaving = false;
            }
        } catch (err) {
            console.error('Save error:', err);
            this.triggerToast('Terjadi kendala jaringan saat menyimpan. Silakan coba lagi.', 'error');
            this.isSaving = false;
        }
    },

    triggerToast(msg, type = 'success') {
        this.toastMessage = msg;
        this.toastType = type;
        this.showToast = true;
        setTimeout(() => { this.showToast = false; }, 4000);
    }
}" x-init="$nextTick(() => { checkTabScroll(); setTimeout(() => checkTabScroll(), 100); setTimeout(() => checkTabScroll(), 400); }); window.addEventListener('resize', () => checkTabScroll())" class="space-y-6">

    <!-- HORIZONTAL STEPPER / TAB NAVIGATION -->
    <div class="sticky top-0 z-30 bg-slate-50/95 dark:bg-slate-950/95 backdrop-blur-md pt-2.5 pb-1 border-b border-slate-200 dark:border-slate-800 -mx-3.5 sm:-mx-5 lg:-mx-6 px-3.5 sm:px-5 lg:px-6 shadow-xs tab-scroll-container group relative">
        <!-- Left Arrow Indicator -->
        <button type="button" 
            x-show="canScrollLeft"
            x-cloak
            @click="scrollTabsLeft()"
            class="absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 z-30 w-7 h-7 flex items-center justify-center rounded-full bg-white/95 dark:bg-slate-900/95 shadow-md border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-200 hover:text-indigo-600 dark:hover:text-indigo-400 opacity-0 group-hover:opacity-100 transition-all cursor-pointer"
            title="Geser tab ke kiri">
            <i data-lucide="chevron-left" class="w-3.5 h-3.5"></i>
        </button>

        <!-- Right Arrow Indicator -->
        <button type="button" 
            x-show="canScrollRight"
            x-cloak
            @click="scrollTabsRight()"
            class="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 z-30 w-7 h-7 flex items-center justify-center rounded-full bg-white/95 dark:bg-slate-900/95 shadow-md border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-200 hover:text-indigo-600 dark:hover:text-indigo-400 opacity-0 group-hover:opacity-100 transition-all cursor-pointer"
            title="Geser tab ke kanan">
            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        </button>

        <div x-ref="tabScroll" 
            @scroll.passive="checkTabScroll()"
            class="flex items-center gap-1.5 custom-horizontal-scroll pb-1 scroll-smooth overflow-x-auto">
            <button type="button" data-tab="identitas" @click="activeTab = 'identitas'; $el.scrollIntoView({ behavior: 'smooth', inline: 'nearest', block: 'nearest' })"
                :class="activeTab === 'identitas' 
                    ? 'bg-indigo-600 text-white shadow-xs font-semibold' 
                    : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium'"
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs whitespace-nowrap transition-all border border-slate-200/80 dark:border-slate-800/80 cursor-pointer">
                <span class="w-4 h-4 rounded-full flex items-center justify-center text-[10px] font-bold"
                    :class="activeTab === 'identitas' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400'">1</span>
                Identitas & Rombel
            </button>

            <button type="button" data-tab="domisili" @click="activeTab = 'domisili'; $el.scrollIntoView({ behavior: 'smooth', inline: 'nearest', block: 'nearest' })"
                :class="activeTab === 'domisili' 
                    ? 'bg-indigo-600 text-white shadow-xs font-semibold' 
                    : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium'"
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs whitespace-nowrap transition-all border border-slate-200/80 dark:border-slate-800/80 cursor-pointer">
                <span class="w-4 h-4 rounded-full flex items-center justify-center text-[10px] font-bold"
                    :class="activeTab === 'domisili' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400'">2</span>
                Domisili & Lingkungan
            </button>

            <button type="button" data-tab="keluarga" @click="activeTab = 'keluarga'; $el.scrollIntoView({ behavior: 'smooth', inline: 'nearest', block: 'nearest' })"
                :class="activeTab === 'keluarga' 
                    ? 'bg-indigo-600 text-white shadow-xs font-semibold' 
                    : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium'"
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs whitespace-nowrap transition-all border border-slate-200/80 dark:border-slate-800/80 cursor-pointer">
                <span class="w-4 h-4 rounded-full flex items-center justify-center text-[10px] font-bold"
                    :class="activeTab === 'keluarga' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400'">3</span>
                Orang Tua & Keluarga
            </button>

            <button type="button" data-tab="kesehatan" @click="activeTab = 'kesehatan'; $el.scrollIntoView({ behavior: 'smooth', inline: 'nearest', block: 'nearest' })"
                :class="activeTab === 'kesehatan' 
                    ? 'bg-indigo-600 text-white shadow-xs font-semibold' 
                    : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium'"
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs whitespace-nowrap transition-all border border-slate-200/80 dark:border-slate-800/80 cursor-pointer">
                <span class="w-4 h-4 rounded-full flex items-center justify-center text-[10px] font-bold"
                    :class="activeTab === 'kesehatan' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400'">4</span>
                Fisik & Medis
            </button>

            <button type="button" data-tab="psikologi" @click="activeTab = 'psikologi'; $el.scrollIntoView({ behavior: 'smooth', inline: 'nearest', block: 'nearest' })"
                :class="activeTab === 'psikologi' 
                    ? 'bg-indigo-600 text-white shadow-xs font-semibold' 
                    : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium'"
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs whitespace-nowrap transition-all border border-slate-200/80 dark:border-slate-800/80 cursor-pointer">
                <span class="w-4 h-4 rounded-full flex items-center justify-center text-[10px] font-bold"
                    :class="activeTab === 'psikologi' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400'">5</span>
                Minat & Karakter
            </button>

            <button type="button" data-tab="dokumen" @click="activeTab = 'dokumen'; $el.scrollIntoView({ behavior: 'smooth', inline: 'nearest', block: 'nearest' })"
                :class="activeTab === 'dokumen' 
                    ? 'bg-indigo-600 text-white shadow-xs font-semibold' 
                    : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium'"
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs whitespace-nowrap transition-all border border-slate-200/80 dark:border-slate-800/80 cursor-pointer">
                <span class="w-4 h-4 rounded-full flex items-center justify-center text-[10px] font-bold"
                    :class="activeTab === 'dokumen' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400'">6</span>
                Asal Sekolah & Berkas
            </button>
        </div>
    </div>

    <!-- FLOATING REALTIME TOAST NOTIFICATION -->
    <div x-show="showToast" x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-3 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-3 scale-95"
        class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-4 py-3 rounded-2xl shadow-xl backdrop-blur-md text-xs font-semibold"
        :class="toastType === 'success' ? 'bg-emerald-600/95 text-white shadow-emerald-500/20' : 'bg-rose-600/95 text-white shadow-rose-500/20'">
        <template x-if="toastType === 'success'">
            <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-200 shrink-0"></i>
        </template>
        <template x-if="toastType === 'error'">
            <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-200 shrink-0"></i>
        </template>
        <span x-text="toastMessage"></span>
        <button type="button" @click="showToast = false" class="ml-2 opacity-70 hover:opacity-100 cursor-pointer">
            <i data-lucide="x" class="w-3.5 h-3.5"></i>
        </button>
    </div>

    <!-- DYNAMIC VALIDATION ERRORS DISPLAY -->
    <div x-show="showErrorBox && errorList.length > 0" x-cloak
        class="bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 p-4 rounded-2xl text-xs">
        <div class="flex items-center justify-between font-bold mb-2 text-sm text-rose-700 dark:text-rose-400">
            <div class="flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                Terdapat beberapa kesalahan pengisian formulir:
            </div>
            <button type="button" @click="showErrorBox = false" class="text-rose-400 hover:text-rose-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <ul class="list-disc list-inside space-y-1 opacity-90 pl-1">
            <template x-for="(err, idx) in errorList" :key="idx">
                <li x-text="err"></li>
            </template>
        </ul>
    </div>

    @if ($errors->any())
        <div class="bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 p-4 rounded-2xl text-xs">
            <div class="flex items-center gap-2 font-bold mb-2 text-sm text-rose-700 dark:text-rose-400">
                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                Terdapat beberapa kesalahan pengisian formulir:
            </div>
            <ul class="list-disc list-inside space-y-1 opacity-90 pl-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- TAB 1: IDENTITAS & ROMBEL                                                 -->
    <!-- ========================================================================= -->
    <div x-show="activeTab === 'identitas'" x-cloak class="space-y-6">
        <!-- 1.1 Penempatan Akademik & Rombel -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 sm:p-6 shadow-xs">
            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 dark:border-slate-800 mb-5">
                <i data-lucide="school" class="w-5 h-5 text-indigo-600 dark:text-indigo-400"></i>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Penempatan Akademik & Rombongan Belajar</h3>
                    <p class="text-[11px] text-slate-400">Atur tahun ajaran masuk, status aktif siswa, tingkat kelas, serta alokasi rombel SD.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs mb-5">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tahun Ajaran <span class="text-rose-500">*</span></label>
                    <select name="academic_year_id" x-model="selectedYear" @change="onYearChange()" required class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                        @foreach($academicYears as $ay)
                            <option value="{{ $ay->id }}" {{ old('academic_year_id', $student->academic_year_id ?? $activeAcademicYear?->id) == $ay->id ? 'selected' : '' }}>
                                {{ $ay->name }}{{ $ay->is_active ? ' ★ Aktif' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Siswa <span class="text-rose-500">*</span></label>
                    <select name="status" required class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                        <option value="aktif" {{ old('status', $student->status ?? 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="lulus" {{ old('status', $student->status) == 'lulus' ? 'selected' : '' }}>Lulus</option>
                        <option value="mutasi" {{ old('status', $student->status) == 'mutasi' ? 'selected' : '' }}>Mutasi</option>
                        <option value="keluar" {{ old('status', $student->status) == 'keluar' ? 'selected' : '' }}>Keluar</option>
                        <option value="nonaktif" {{ old('status', $student->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tingkat Kelas</label>
                    <select name="class_level_id" x-model="selectedLevel" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500 font-medium">
                        <option value="">-- Semua Tingkat --</option>
                        @foreach($classLevels as $lvl)
                            <option value="{{ $lvl->id }}">{{ $lvl->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Rombongan Belajar (Rombel)</label>
                    <select name="classroom_id" x-model="selectedClassroom" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500 font-medium">
                        <option value="">-- Pilih Rombel --</option>
                        @foreach($classrooms as $c)
                            <option value="{{ $c->id }}"
                                x-show="(!selectedLevel || selectedLevel == '{{ $c->class_level_id }}') && (!selectedYear || selectedYear == '{{ $c->academic_year_id }}')"
                                {{ old('classroom_id', $student->classroom_id) == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->classLevel?->name ?? 'Level' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Inklusi & GPK Section -->
            <div class="p-4 rounded-xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-5 h-5 rounded-md bg-indigo-600 text-white flex items-center justify-center text-[10px] font-bold">★</span>
                    <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Kategori Siswa & Pendampingan Khusus (Inklusi / PDBK)</h4>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori Siswa</label>
                        <select name="student_type" x-model="studentType" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                            <option value="Reguler" {{ old('student_type', $student->student_type ?? 'Reguler') == 'Reguler' ? 'selected' : '' }}>Reguler</option>
                            <option value="PDBK" {{ old('student_type', $student->student_type) == 'PDBK' ? 'selected' : '' }}>PDBK (Inklusi / Berkebutuhan Khusus)</option>
                        </select>
                    </div>

                    <div x-show="studentType === 'PDBK'">
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis Kebutuhan Khusus</label>
                        <input type="text" name="special_needs_type" value="{{ old('special_needs_type', $student->special_needs_type) }}" placeholder="Contoh: ADHD, Autism, Slow Learner, dll" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                    </div>

                    <div x-show="studentType === 'PDBK'">
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Guru Pendamping Khusus (GPK)</label>
                        <select name="gpk_employee_id" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                            <option value="">-- Pilih Guru GPK --</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}" {{ old('gpk_employee_id', $student->gpk_employee_id) == $t->id ? 'selected' : '' }}>
                                    {{ $t->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1.2 Biodata Pokok Siswa & Upload Foto Profil -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 sm:p-6 shadow-xs">
            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 dark:border-slate-800 mb-5">
                <i data-lucide="user-check" class="w-5 h-5 text-indigo-600 dark:text-indigo-400"></i>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Biodata Pokok & Legalitas Kependudukan</h3>
                    <p class="text-[11px] text-slate-400">Identitas resmi siswa sesuai Akta Kelahiran dan Kartu Keluarga (KK).</p>
                </div>
            </div>

            <!-- Foto Profil Siswa -->
            <div class="mb-5 p-4 rounded-xl bg-slate-50/60 dark:bg-slate-800/30 border border-slate-200/80 dark:border-slate-800 flex flex-col sm:flex-row items-center gap-4 text-xs">
                <div class="relative w-20 h-20 rounded-2xl overflow-hidden border-2 border-indigo-200 dark:border-indigo-900 shrink-0 bg-slate-100 dark:bg-slate-800 flex items-center justify-center shadow-inner">
                    <template x-if="photoPreview">
                        <img :src="photoPreview" alt="Foto Siswa" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!photoPreview">
                        <div class="text-slate-400 flex flex-col items-center">
                            <i data-lucide="camera" class="w-6 h-6 mb-1"></i>
                            <span class="text-[9px]">Belum Ada</span>
                        </div>
                    </template>
                </div>

                <div class="space-y-1.5 min-w-0 text-center sm:text-left">
                    <label class="block font-semibold text-slate-800 dark:text-slate-200 text-xs">Foto Resmi Peserta Didik (3x4)</label>
                    <p class="text-[11px] text-slate-400">Format: JPG, PNG, atau WebP. Gambar otomatis dikompresi agar menghemat kuota storage.</p>
                    <input type="file" name="student_photo" accept="image/*"
                        @change="const file = $event.target.files[0]; if (file) { photoPreview = URL.createObjectURL(file); }"
                        class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                </div>
            </div>

            <!-- Field Identitas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap Siswa <span class="text-rose-500">*</span></label>
                    <input type="text" name="full_name" value="{{ old('full_name', $student->full_name) }}" required placeholder="Nama lengkap sesuai akta" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Panggilan</label>
                    <input type="text" name="nickname" value="{{ old('nickname', $student->nickname) }}" placeholder="Contoh: Rian" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                    <select name="gender" required class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="L" {{ old('gender', $student->gender) == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                        <option value="P" {{ old('gender', $student->gender) == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">NIS (Nomor Induk Sekolah)</label>
                    <input type="text" name="nis" value="{{ old('nis', $student->nis) }}" placeholder="Contoh: 262701001" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500 font-mono">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">NISN</label>
                    <input type="text" name="nisn" value="{{ old('nisn', $student->nisn) }}" placeholder="10 digit nomor NISN" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500 font-mono">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">NIK (Nomor Induk Kependudukan)</label>
                    <input type="text" name="nik" value="{{ old('nik', $student->nik) }}" placeholder="16 digit NIK siswa" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500 font-mono">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. Kartu Keluarga (KK)</label>
                    <input type="text" name="no_kk" value="{{ old('no_kk', $student->no_kk) }}" placeholder="16 digit No. KK" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500 font-mono">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. Registrasi Akta Lahir</label>
                    <input type="text" name="birth_certificate_no" value="{{ old('birth_certificate_no', $student->birth_certificate_no) }}" placeholder="Nomor akta kelahiran" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Agama & Kepercayaan</label>
                    <select name="religion" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                        <option value="Islam" {{ old('religion', $student->religion ?? 'Islam') == 'Islam' ? 'selected' : '' }}>Islam</option>
                        <option value="Kristen" {{ old('religion', $student->religion) == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                        <option value="Katolik" {{ old('religion', $student->religion) == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                        <option value="Hindu" {{ old('religion', $student->religion) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                        <option value="Buddha" {{ old('religion', $student->religion) == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                        <option value="Konghucu" {{ old('religion', $student->religion) == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tempat Lahir</label>
                    <input type="text" name="birth_place" value="{{ old('birth_place', $student->birth_place) }}" placeholder="Kota kelahiran" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Lahir</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date', $student->birth_date ? $student->birth_date->format('Y-m-d') : '') }}" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kewarganegaraan</label>
                    <input type="text" name="citizenship" value="{{ old('citizenship', $student->citizenship ?? 'Indonesia (WNI)') }}" placeholder="WNI / WNA" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Suku Bangsa</label>
                    <input type="text" name="ethnic_group" value="{{ old('ethnic_group', $student->ethnic_group ?? 'Jawa') }}" placeholder="Contoh: Jawa, Madura, Sunda" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Bahasa Sehari-hari</label>
                    <input type="text" name="home_language" value="{{ old('home_language', $student->home_language ?? 'Bahasa Indonesia') }}" placeholder="Bahasa di rumah" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Anak Ke-</label>
                    <input type="number" name="child_number" value="{{ old('child_number', $student->child_number) }}" min="1" max="25" placeholder="Contoh: 1" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jml Saudara Kandung</label>
                    <input type="number" name="siblings_count" value="{{ old('siblings_count', $student->siblings_count) }}" min="0" max="25" placeholder="Contoh: 2" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jml Saudara Tiri</label>
                    <input type="number" name="step_siblings_count" value="{{ old('step_siblings_count', $student->step_siblings_count) }}" min="0" max="25" placeholder="0 jika tidak ada" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jml Saudara Angkat</label>
                    <input type="number" name="adoptive_siblings_count" value="{{ old('adoptive_siblings_count', $student->adoptive_siblings_count) }}" min="0" max="25" placeholder="0 jika tidak ada" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <!-- Tombol Lanjut Tab 1 -->
        <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
            <button type="button" @click="saveAndNext('domisili')" :disabled="isSaving" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs transition-all cursor-pointer">
                <span x-text="isSaving ? 'Menyimpan...' : 'Simpan & Lanjut ke Domisili'"></span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 2: DOMISILI & LINGKUNGAN                                              -->
    <!-- ========================================================================= -->
    <div x-show="activeTab === 'domisili'" x-cloak class="space-y-6">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 sm:p-6 shadow-xs">
            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 dark:border-slate-800 mb-5">
                <i data-lucide="map-pin" class="w-5 h-5 text-indigo-600 dark:text-indigo-400"></i>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Alamat Tempat Tinggal & Lingkungan Domisili</h3>
                    <p class="text-[11px] text-slate-400">Rincian alamat tempat tinggal siswa sesuai domisili saat ini.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Alamat Jalan / Blok / No. Rumah</label>
                    <textarea name="address" rows="2" placeholder="Nama jalan, nomor rumah, perumahan..." class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">{{ old('address', $student->address) }}</textarea>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Dusun / Lingkungan</label>
                    <input type="text" name="dusun" value="{{ old('dusun', $student->dusun) }}" placeholder="Nama dusun/lingkungan" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">RT / RW</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" name="rt" value="{{ old('rt', $student->rt) }}" placeholder="RT 01" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                        <input type="text" name="rw" value="{{ old('rw', $student->rw) }}" placeholder="RW 02" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kelurahan / Desa</label>
                    <input type="text" name="village" value="{{ old('village', $student->village) }}" placeholder="Contoh: Tunggulwulung" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kecamatan</label>
                    <input type="text" name="district" value="{{ old('district', $student->district) }}" placeholder="Contoh: Lowokwaru" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kota / Kabupaten</label>
                    <input type="text" name="city" value="{{ old('city', $student->city ?? 'Kota Malang') }}" placeholder="Contoh: Kota Malang" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Provinsi</label>
                    <input type="text" name="province" value="{{ old('province', $student->province ?? 'Jawa Timur') }}" placeholder="Contoh: Jawa Timur" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode Pos</label>
                    <input type="text" name="postal_code" value="{{ old('postal_code', $student->postal_code) }}" placeholder="Contoh: 65143" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500 font-mono">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Tempat Tinggal</label>
                    <select name="residence_status" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                        <option value="Milik Sendiri" {{ old('residence_status', $student->residence_status ?? 'Milik Sendiri') == 'Milik Sendiri' ? 'selected' : '' }}>Milik Sendiri</option>
                        <option value="Rumah Orang Tua" {{ old('residence_status', $student->residence_status) == 'Rumah Orang Tua' ? 'selected' : '' }}>Rumah Orang Tua</option>
                        <option value="Sewa / Kontrak" {{ old('residence_status', $student->residence_status) == 'Sewa / Kontrak' ? 'selected' : '' }}>Sewa / Kontrak</option>
                        <option value="Rumah Dinas" {{ old('residence_status', $student->residence_status) == 'Rumah Dinas' ? 'selected' : '' }}>Rumah Dinas</option>
                        <option value="Asrama / Pondok" {{ old('residence_status', $student->residence_status) == 'Asrama / Pondok' ? 'selected' : '' }}>Asrama / Pondok</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tinggal Bersama</label>
                    <select name="living_with" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                        <option value="Orang Tua" {{ old('living_with', $student->living_with ?? 'Orang Tua') == 'Orang Tua' ? 'selected' : '' }}>Orang Tua</option>
                        <option value="Wali" {{ old('living_with', $student->living_with) == 'Wali' ? 'selected' : '' }}>Wali</option>
                        <option value="Keluarga / Saudara" {{ old('living_with', $student->living_with) == 'Keluarga / Saudara' ? 'selected' : '' }}>Keluarga / Saudara</option>
                        <option value="Asrama / Pesantren" {{ old('living_with', $student->living_with) == 'Asrama / Pesantren' ? 'selected' : '' }}>Asrama / Pesantren</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Transportasi ke Sekolah</label>
                    <input type="text" name="transportation" value="{{ old('transportation', $student->transportation ?? 'Sepeda Motor / Antar Jemput') }}" placeholder="Contoh: Sepeda Motor, Mobil, Sepeda, Jalan Kaki" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jarak ke Sekolah (KM)</label>
                    <input type="text" name="distance_to_school" value="{{ old('distance_to_school', $student->distance_to_school) }}" placeholder="Contoh: 3 km" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Waktu Tempuh (Menit)</label>
                    <input type="number" name="travel_time_minutes" value="{{ old('travel_time_minutes', $student->travel_time_minutes) }}" placeholder="Contoh: 15" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <div class="flex justify-between gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
            <button type="button" @click="activeTab = 'identitas'" class="px-4 py-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold cursor-pointer">
                &larr; Kembali
            </button>
            <button type="button" @click="saveAndNext('keluarga')" :disabled="isSaving" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs transition-all cursor-pointer">
                <span x-text="isSaving ? 'Menyimpan...' : 'Simpan & Lanjut ke Orang Tua'"></span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 3: ORANG TUA & KELUARGA                                               -->
    <!-- ========================================================================= -->
    <div x-show="activeTab === 'keluarga'" x-cloak class="space-y-6">
        <!-- 3.1 Data Ayah Kandung -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 sm:p-6 shadow-xs">
            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 dark:border-slate-800 mb-5">
                <i data-lucide="user" class="w-5 h-5 text-indigo-600 dark:text-indigo-400"></i>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Data Ayah Kandung</h3>
                    <p class="text-[11px] text-slate-400">Informasi identitas, pekerjaan, kontak, dan instansi ayah.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap Ayah</label>
                    <input type="text" name="father_name" value="{{ old('father_name', $student->father_name) }}" placeholder="Nama lengkap ayah" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Gelar Depan / Belakang</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" name="father_title_prefix" value="{{ old('father_title_prefix', $student->father_title_prefix) }}" placeholder="Dr. / Ir." class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                        <input type="text" name="father_title_suffix" value="{{ old('father_title_suffix', $student->father_title_suffix) }}" placeholder="S.T., M.Kom." class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">NIK Ayah</label>
                    <input type="text" name="father_nik" value="{{ old('father_nik', $student->father_nik) }}" placeholder="16 digit NIK ayah" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500 font-mono">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. HP / WhatsApp Ayah</label>
                    <input type="text" name="father_phone" value="{{ old('father_phone', $student->father_phone) }}" placeholder="Contoh: 08123456789" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Email Ayah</label>
                    <input type="email" name="father_email" value="{{ old('father_email', $student->father_email) }}" placeholder="email@ayah.com" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Pendidikan Terakhir Ayah</label>
                    <input type="text" name="father_education" value="{{ old('father_education', $student->father_education) }}" placeholder="Contoh: S1 Teknik Informatika" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Pekerjaan Ayah</label>
                    <input type="text" name="father_job" value="{{ old('father_job', $student->father_job) }}" placeholder="Contoh: Karyawan Swasta, PNS, Wiraswasta" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Instansi / Perusahaan</label>
                    <input type="text" name="father_company" value="{{ old('father_company', $student->father_company) }}" placeholder="Nama kantor/tempat kerja" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Penghasilan Bulanan Ayah</label>
                    <input type="text" name="father_income" value="{{ old('father_income', $student->father_income) }}" placeholder="Contoh: Rp 5.000.000 - Rp 10.000.000" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <!-- 3.2 Data Ibu Kandung -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 sm:p-6 shadow-xs">
            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 dark:border-slate-800 mb-5">
                <i data-lucide="user" class="w-5 h-5 text-indigo-600 dark:text-indigo-400"></i>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Data Ibu Kandung</h3>
                    <p class="text-[11px] text-slate-400">Informasi identitas, pekerjaan, kontak, dan instansi ibu.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap Ibu</label>
                    <input type="text" name="mother_name" value="{{ old('mother_name', $student->mother_name) }}" placeholder="Nama lengkap ibu kandung" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Gelar Depan / Belakang</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" name="mother_title_prefix" value="{{ old('mother_title_prefix', $student->mother_title_prefix) }}" placeholder="Dr." class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                        <input type="text" name="mother_title_suffix" value="{{ old('mother_title_suffix', $student->mother_title_suffix) }}" placeholder="S.Pd., M.M." class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">NIK Ibu</label>
                    <input type="text" name="mother_nik" value="{{ old('mother_nik', $student->mother_nik) }}" placeholder="16 digit NIK ibu" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500 font-mono">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. HP / WhatsApp Ibu</label>
                    <input type="text" name="mother_phone" value="{{ old('mother_phone', $student->mother_phone) }}" placeholder="Contoh: 08123456789" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Email Ibu</label>
                    <input type="email" name="mother_email" value="{{ old('mother_email', $student->mother_email) }}" placeholder="email@ibu.com" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Pendidikan Terakhir Ibu</label>
                    <input type="text" name="mother_education" value="{{ old('mother_education', $student->mother_education) }}" placeholder="Contoh: S1 Manajemen" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Pekerjaan Ibu</label>
                    <input type="text" name="mother_job" value="{{ old('mother_job', $student->mother_job) }}" placeholder="Contoh: Ibu Rumah Tangga, Guru, PNS" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Instansi / Perusahaan</label>
                    <input type="text" name="mother_company" value="{{ old('mother_company', $student->mother_company) }}" placeholder="Nama kantor/tempat kerja ibu" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Penghasilan Bulanan Ibu</label>
                    <input type="text" name="mother_income" value="{{ old('mother_income', $student->mother_income) }}" placeholder="Contoh: Rp 3.000.000 - Rp 5.000.000" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <!-- 3.3 Data Wali & Kontak Darurat -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 sm:p-6 shadow-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-5">
                <div class="flex items-center gap-2.5">
                    <i data-lucide="shield-check" class="w-5 h-5 text-indigo-600 dark:text-indigo-400"></i>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Data Wali Siswa (Opsional) & Kontak Darurat</h3>
                        <p class="text-[11px] text-slate-400">Isi bila siswa tinggal bersama wali atau memiliki kontak darurat khusus.</p>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" x-model="hasGuardian" class="sr-only peer">
                    <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-slate-600 peer-checked:bg-indigo-600"></div>
                    <span class="ml-2 text-xs font-semibold text-slate-600 dark:text-slate-400">Aktifkan Wali</span>
                </label>
            </div>

            <div x-show="hasGuardian" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs mb-5">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap Wali</label>
                    <input type="text" name="guardian_name" value="{{ old('guardian_name', $student->guardian_name) }}" placeholder="Nama wali murid" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Hubungan dengan Siswa</label>
                    <input type="text" name="guardian_relation" value="{{ old('guardian_relation', $student->guardian_relation) }}" placeholder="Contoh: Paman, Kakek, Kakak" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. HP / WhatsApp Wali</label>
                    <input type="text" name="guardian_phone" value="{{ old('guardian_phone', $student->guardian_phone) }}" placeholder="Contoh: 08123456789" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Pekerjaan Wali</label>
                    <input type="text" name="guardian_job" value="{{ old('guardian_job', $student->guardian_job) }}" placeholder="Pekerjaan wali" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>
            </div>

            <!-- Kontak Darurat & WhatsApp Utama -->
            <div class="p-4 rounded-xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. WhatsApp Utama (Notifikasi) <span class="text-rose-500">*</span></label>
                    <input type="text" name="parent_phone" value="{{ old('parent_phone', $student->parent_phone) }}" placeholder="08xxxxxxxxxx" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500 font-mono">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Kontak Darurat</label>
                    <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $student->emergency_contact_name) }}" placeholder="Nama pihak yang bisa dihubungi saat darurat" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. Telp Kontak Darurat</label>
                    <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $student->emergency_contact_phone) }}" placeholder="08xxxxxxxxxx" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500 font-mono">
                </div>
            </div>
        </div>

        <div class="flex justify-between gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
            <button type="button" @click="activeTab = 'domisili'" class="px-4 py-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold cursor-pointer">
                &larr; Kembali
            </button>
            <button type="button" @click="saveAndNext('kesehatan')" :disabled="isSaving" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs transition-all cursor-pointer">
                <span x-text="isSaving ? 'Menyimpan...' : 'Simpan & Lanjut ke Fisik & Medis'"></span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 4: FISIK & MEDIS                                                      -->
    <!-- ========================================================================= -->
    <div x-show="activeTab === 'kesehatan'" x-cloak class="space-y-6">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 sm:p-6 shadow-xs">
            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 dark:border-slate-800 mb-5">
                <i data-lucide="heart-pulse" class="w-5 h-5 text-indigo-600 dark:text-indigo-400"></i>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Antropometri Fisik & Riwayat Medis UKS</h3>
                    <p class="text-[11px] text-slate-400">Data pertumbuhan fisik dan rekam riwayat kesehatan siswa.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs mb-5">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tinggi Badan (cm)</label>
                    <input type="number" step="0.1" name="height" value="{{ old('height', $student->height) }}" placeholder="Contoh: 155" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500 font-mono">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Berat Badan (kg)</label>
                    <input type="number" step="0.1" name="weight" value="{{ old('weight', $student->weight) }}" placeholder="Contoh: 48" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500 font-mono">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Golongan Darah</label>
                    <select name="blood_type" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                        <option value="">-- Tidak Tahu --</option>
                        <option value="A" {{ old('blood_type', $student->blood_type) == 'A' ? 'selected' : '' }}>A</option>
                        <option value="B" {{ old('blood_type', $student->blood_type) == 'B' ? 'selected' : '' }}>B</option>
                        <option value="AB" {{ old('blood_type', $student->blood_type) == 'AB' ? 'selected' : '' }}>AB</option>
                        <option value="O" {{ old('blood_type', $student->blood_type) == 'O' ? 'selected' : '' }}>O</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Warna Kulit</label>
                    <input type="text" name="skin_color" value="{{ old('skin_color', $student->skin_color ?? 'Sawo Matang') }}" placeholder="Sawo matang, kuning langsat..." class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Riwayat Penyakit Berat / Pernah Rawat Inap</label>
                    <textarea name="severe_disease_history" rows="3" placeholder="Tuliskan jika pernah sakit berat, operasi, atau patah tulang..." class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">{{ old('severe_disease_history', $student->severe_disease_history) }}</textarea>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Penyakit yang Sering Diderita / Alergi</label>
                    <textarea name="frequent_disease" rows="3" placeholder="Contoh: Maag, Asma, Alergi makanan laut, Alergi dingin..." class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">{{ old('frequent_disease', $student->frequent_disease) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-between gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
            <button type="button" @click="activeTab = 'keluarga'" class="px-4 py-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold cursor-pointer">
                &larr; Kembali
            </button>
            <button type="button" @click="saveAndNext('psikologi')" :disabled="isSaving" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs transition-all cursor-pointer">
                <span x-text="isSaving ? 'Menyimpan...' : 'Simpan & Lanjut ke Minat & Karakter'"></span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 5: MINAT & KARAKTER                                                   -->
    <!-- ========================================================================= -->
    <div x-show="activeTab === 'psikologi'" x-cloak class="space-y-6">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 sm:p-6 shadow-xs">
            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 dark:border-slate-800 mb-5">
                <i data-lucide="sparkles" class="w-5 h-5 text-indigo-600 dark:text-indigo-400"></i>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Bakat, Minat, Cita-cita & Harapan Orang Tua</h3>
                    <p class="text-[11px] text-slate-400">Informasi minat, potensi anak, serta visi pendidikan orang tua.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs mb-5">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Hobi / Kegemaran Siswa</label>
                    <input type="text" name="student_hobby" value="{{ old('student_hobby', $student->student_hobby) }}" placeholder="Contoh: Membaca, Robotik, Futsal, Coding" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Cita-cita Siswa</label>
                    <input type="text" name="student_aspiration" value="{{ old('student_aspiration', $student->student_aspiration) }}" placeholder="Contoh: Dokter, Software Engineer, Pengusaha" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Rencana Sekolah Lanjutan Setelah SD</label>
                    <input type="text" name="planned_school_after" value="{{ old('planned_school_after', $student->planned_school_after) }}" placeholder="Contoh: SMP Negeri / MTs Unggulan / Pondok Pesantren" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Harapan Orang Tua Terhadap Ananda</label>
                    <textarea name="parent_aspiration" rows="2" placeholder="Harapan pembentukan karakter & akhlak..." class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">{{ old('parent_aspiration', $student->parent_aspiration) }}</textarea>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Pesan Khusus Orang Tua ke Sekolah</label>
                    <textarea name="parent_message" rows="3" placeholder="Pesan penting yang perlu diperhatikan guru..." class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">{{ old('parent_message', $student->parent_message) }}</textarea>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Saran / Masukan Orang Tua</label>
                    <textarea name="parent_suggestion" rows="3" placeholder="Saran pengembangan kegiatan siswa..." class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">{{ old('parent_suggestion', $student->parent_suggestion) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-between gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
            <button type="button" @click="activeTab = 'kesehatan'" class="px-4 py-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold cursor-pointer">
                &larr; Kembali
            </button>
            <button type="button" @click="saveAndNext('dokumen')" :disabled="isSaving" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs transition-all cursor-pointer">
                <span x-text="isSaving ? 'Menyimpan...' : 'Simpan & Lanjut ke Berkas'"></span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 6: ASAL SEKOLAH & BERKAS DOKUMEN                                      -->
    <!-- ========================================================================= -->
    <div x-show="activeTab === 'dokumen'" x-cloak class="space-y-6">
        <!-- 6.1 Asal Sekolah SD/MI -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 sm:p-6 shadow-xs">
            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 dark:border-slate-800 mb-5">
                <i data-lucide="graduation-cap" class="w-5 h-5 text-indigo-600 dark:text-indigo-400"></i>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Riwayat Sekolah TK/PAUD/RA Asal</h3>
                    <p class="text-[11px] text-slate-400">Data kelulusan jenjang TK/PAUD sebelum masuk SD.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama TK/PAUD/RA Asal</label>
                    <input type="text" name="previous_school" value="{{ old('previous_school', $student->previous_school) }}" placeholder="Contoh: TK Anak Saleh Malang" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori Asal Sekolah</label>
                    <select name="origin_category" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                        <option value="TK Dalam Kota" {{ old('origin_category', $student->origin_category ?? 'TK Dalam Kota') == 'TK Dalam Kota' ? 'selected' : '' }}>TK Dalam Kota</option>
                        <option value="TK Luar Kota" {{ old('origin_category', $student->origin_category) == 'TK Luar Kota' ? 'selected' : '' }}>TK Luar Kota</option>
                        <option value="RA Dalam Kota" {{ old('origin_category', $student->origin_category) == 'RA Dalam Kota' ? 'selected' : '' }}>RA Dalam Kota</option>
                        <option value="PAUD/KB" {{ old('origin_category', $student->origin_category) == 'PAUD/KB' ? 'selected' : '' }}>PAUD / Kelompok Bermain</option>
                        <option value="Tanpa TK" {{ old('origin_category', $student->origin_category) == 'Tanpa TK' ? 'selected' : '' }}>Tanpa TK / Langsung SD</option>
                        <option value="Sekolah Internasional" {{ old('origin_category', $student->origin_category) == 'Sekolah Internasional' ? 'selected' : '' }}>Sekolah Internasional / Luar Negeri</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. Sertifikat / Ijazah TK</label>
                    <input type="text" name="diploma_number" value="{{ old('diploma_number', $student->diploma_number) }}" placeholder="Nomor sertifikat/ijazah TK" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500 font-mono">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tgl Kelulusan TK</label>
                    <input type="text" name="sttb_number_date" value="{{ old('sttb_number_date', $student->sttb_number_date) }}" placeholder="Contoh: 15 Juni 2026" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <!-- 6.2 Checklist Berkas Fisik & Unggah Berkas Digital -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 sm:p-6 shadow-xs">
            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 dark:border-slate-800 mb-5">
                <i data-lucide="file-check-2" class="w-5 h-5 text-indigo-600 dark:text-indigo-400"></i>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Checklist Berkas Fisik & Arsip Digital</h3>
                    <p class="text-[11px] text-slate-400">Centang kelengkapan berkas fisik dan unggah scan dokumen (otomatis dioptimasi).</p>
                </div>
            </div>

            @php
                $docChecklistItems = [
                    'akta_kelahiran' => 'Fotokopi Akta Kelahiran',
                    'kartu_keluarga' => 'Fotokopi Kartu Keluarga (KK)',
                    'ijazah_tk' => 'Ijazah / Sertifikat Kelulusan TK/PAUD/RA',
                    'kia_imunisasi' => 'Buku KIA / Riwayat Imunisasi Lengkap',
                    'ktp_orangtua' => 'Fotokopi KTP Kedua Orang Tua / Wali',
                    'pas_foto' => 'Pas Foto Resmi Siswa (3x4)',
                    'kip_pkh' => 'KIP / KKS / PKH (Bila Memiliki)',
                ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs mb-5">
                @foreach($docChecklistItems as $k => $label)
                    <div class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 flex flex-col justify-between gap-2.5">
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" name="checklist_documents[{{ $k }}]" value="1"
                                {{ !empty($checklist[$k]) ? 'checked' : '' }}
                                class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $label }}</span>
                        </label>
                        
                        <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-200/60 dark:border-slate-700/60">
                            <div class="text-[10px] text-slate-400 truncate">
                                @if(!empty($docs[$k]))
                                    <a href="{{ $docs[$k] }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline inline-flex items-center gap-1 font-semibold">
                                        <i data-lucide="eye" class="w-3 h-3"></i> Lihat Berkas Terunggah
                                    </a>
                                @else
                                    <span>Belum ada scan file</span>
                                @endif
                            </div>
                            <input type="file" name="document_files[{{ $k }}]" accept=".pdf,image/*"
                                class="text-[10px] text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-slate-200 dark:file:bg-slate-700 file:text-slate-700 dark:file:text-slate-300 cursor-pointer">
                        </div>
                    </div>
                @endforeach
            </div>

            <div>
                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1 text-xs">Catatan Tambahan untuk Tata Usaha / Guru</label>
                <textarea name="notes" rows="2" placeholder="Catatan internal opsional..." class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-xs focus:ring-indigo-500">{{ old('notes', $student->notes) }}</textarea>
            </div>
        </div>

        <!-- Tombol Aksi Akhir -->
        <div class="flex justify-between gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
            <button type="button" @click="activeTab = 'psikologi'" class="px-4 py-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold cursor-pointer">
                &larr; Kembali
            </button>
            <button type="button" @click="saveAndNext(null, true)" :disabled="isSaving" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md transition-all cursor-pointer">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                <span x-text="isSaving ? 'Menyimpan Semua Data...' : 'Simpan Seluruh Data Siswa'"></span>
            </button>
        </div>
    </div>

</div>
