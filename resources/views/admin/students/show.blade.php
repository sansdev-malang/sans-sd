<x-admin-layout>
    <div class="p-3.5 sm:p-5 lg:p-6 space-y-5" x-data="{ 
        activeTab: 'identitas',
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
        }
    }" x-init="$nextTick(() => { checkTabScroll(); setTimeout(() => checkTabScroll(), 100); setTimeout(() => checkTabScroll(), 400); }); window.addEventListener('resize', () => checkTabScroll())">

        <!-- BREADCRUMBS & TOP BAR -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <nav class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Dashboard</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400"></i>
                <a href="{{ route('students.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Data Siswa</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400"></i>
                <span class="font-semibold text-slate-800 dark:text-slate-200 truncate max-w-[200px] sm:max-w-xs">{{ $student->full_name }}</span>
            </nav>

            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('students.index') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 shadow-xs transition-colors">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    Kembali
                </a>

                <!-- DROPDOWN UNDUH / CETAK DOSSIER -->
                <div x-data="{ exportOpen: false }" class="relative">
                    <button type="button" @click="exportOpen = !exportOpen" @click.outside="exportOpen = false"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 shadow-xs transition-all cursor-pointer">
                        <i data-lucide="download" class="w-3.5 h-3.5 text-slate-500"></i>
                        <span>Unduh / Cetak Dossier</span>
                        <i data-lucide="chevron-down" class="w-3 h-3 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': exportOpen }"></i>
                    </button>

                    <div x-show="exportOpen" x-cloak
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 mt-1.5 w-60 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl z-50 py-1.5 overflow-hidden">
                        
                        <div class="px-3.5 py-1 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                            Pilihan Berkas Dossier
                        </div>

                        <!-- 1. Download PDF Dossier -->
                        <a href="{{ route('students.dossier.pdf', $student->id) }}"
                            @click="exportOpen = false"
                            class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-rose-50 dark:hover:bg-rose-950/40 hover:text-rose-700 dark:hover:text-rose-400 transition-colors">
                            <i data-lucide="file-text" class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0"></i>
                            <div>
                                <div class="font-semibold text-slate-800 dark:text-slate-100">Unduh PDF (.pdf)</div>
                                <div class="text-[10px] text-slate-500 dark:text-slate-400">Lembar Dossier Resmi / Buku Induk</div>
                            </div>
                        </a>

                        <!-- 2. Download Excel -->
                        <a href="{{ route('students.dossier.excel', $student->id) }}"
                            @click="exportOpen = false"
                            class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors">
                            <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0"></i>
                            <div>
                                <div class="font-semibold text-slate-800 dark:text-slate-100">Unduh Excel (.xlsx)</div>
                                <div class="text-[10px] text-slate-500 dark:text-slate-400">Rincian Lengkap Data Siswa</div>
                            </div>
                        </a>

                        <div class="my-1 border-t border-slate-100 dark:border-slate-800"></div>

                        <!-- 3. Cetak Langsung (Print) -->
                        <a href="{{ route('students.dossier.print', $student->id) }}"
                            target="_blank"
                            @click="exportOpen = false"
                            class="w-full flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors text-left cursor-pointer">
                            <i data-lucide="printer" class="w-4 h-4 text-slate-500 shrink-0"></i>
                            <div>
                                <div class="font-semibold text-slate-800 dark:text-slate-100">Cetak Dossier (Print)</div>
                                <div class="text-[10px] text-slate-500 dark:text-slate-400">Format resmi A4 siap cetak</div>
                            </div>
                        </a>
                    </div>
                </div>

                <a href="{{ route('students.edit', $student->id) }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors">
                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                    Edit Data Siswa
                </a>
            </div>
        </div>

        <!-- FLASH NOTIFICATION -->
        @if(session('success'))
            <div class="bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 px-4 py-3 rounded-xl flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 dark:text-emerald-400"></i>
                    <span class="text-xs sm:text-sm font-medium">{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        <!-- HERO PROFILE HEADER -->
        <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 sm:p-6 shadow-xs relative overflow-hidden">
            <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-500/5 rounded-full blur-3xl pointer-events-none -mr-20 -mt-20"></div>

            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-5 relative z-10">
                <!-- Avatar & Identity -->
                <div class="flex items-center gap-4 sm:gap-5 min-w-0">
                    <div class="relative shrink-0">
                        @if($student->student_photo_url)
                            <img src="{{ $student->student_photo_url }}" alt="{{ $student->full_name }}"
                                class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl object-cover shadow-md border-2 border-indigo-200 dark:border-indigo-800">
                        @else
                            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl flex items-center justify-center font-black text-xl sm:text-2xl shadow-md border-2
                                {{ $student->gender === 'P' 
                                    ? 'bg-rose-50 dark:bg-rose-950/60 border-rose-300 dark:border-rose-800 text-rose-600 dark:text-rose-400' 
                                    : 'bg-indigo-50 dark:bg-indigo-950/60 border-indigo-300 dark:border-indigo-800 text-indigo-600 dark:text-indigo-400' }}">
                                {{ $student->avatar_initials ?? strtoupper(substr($student->full_name, 0, 2)) }}
                            </div>
                        @endif
                        <span class="absolute -bottom-1 -right-1 px-1.5 py-0.5 rounded-full text-[10px] font-bold border shadow-xs
                            {{ $student->gender === 'P' ? 'bg-pink-100 text-pink-700 border-pink-300' : 'bg-blue-100 text-blue-700 border-blue-300' }}">
                            {{ $student->gender === 'P' ? 'P' : 'L' }}
                        </span>
                    </div>

                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-lg sm:text-2xl font-black tracking-tight text-slate-900 dark:text-slate-50 truncate">
                                {{ $student->full_name }}
                            </h1>
                            @if($student->nickname)
                                <span class="text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400">
                                    "{{ $student->nickname }}"
                                </span>
                            @endif

                            <!-- Status Badge -->
                            @if($student->status && $student->status !== 'aktif')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold 
                                    {{ $student->status === 'lulus' ? 'bg-blue-100 text-blue-700 border border-blue-200' : 
                                      ($student->status === 'mutasi' ? 'bg-amber-100 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-700 border border-slate-200') }}">
                                    {{ ucfirst($student->status) }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                    Aktif
                                </span>
                            @endif
                        </div>

                        <!-- Sub-details: NIS, NISN, Rombel, T.A. -->
                        <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs text-slate-600 dark:text-slate-400">
                            <div class="inline-flex items-center gap-1.5">
                                <span class="font-medium text-slate-400">NIS:</span>
                                <span class="font-bold text-slate-700 dark:text-slate-200">{{ $student->nis ?? '-' }}</span>
                            </div>
                            <div class="inline-flex items-center gap-1.5">
                                <span class="font-medium text-slate-400">NISN:</span>
                                <span class="font-bold text-slate-700 dark:text-slate-200">{{ $student->nisn ?? '-' }}</span>
                            </div>
                            <div class="inline-flex items-center gap-1.5">
                                <span class="font-medium text-slate-400">Rombel:</span>
                                <span class="font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ $student->classroom?->name ?? 'Belum Ditentukan' }}
                                </span>
                            </div>
                            <div class="inline-flex items-center gap-1.5">
                                <span class="font-medium text-slate-400">T.A:</span>
                                <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $student->academicYear?->name ?? '2026/2027' }}</span>
                            </div>
                        </div>

                        <!-- Badges: Kategori Siswa, GPK, Kontak Orang Tua -->
                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            @if($student->student_type === 'PDBK' || !empty($student->special_needs_type))
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-400 border border-purple-200 dark:border-purple-800">
                                    <i data-lucide="award" class="w-3.5 h-3.5 text-purple-600"></i>
                                    PDBK / Inklusi ({{ $student->special_needs_type ?? 'Kebutuhan Khusus' }})
                                </span>
                                @if($student->gpkTeacher)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-medium bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                                        <i data-lucide="user-check" class="w-3.5 h-3.5 text-indigo-600"></i>
                                        GPK: {{ $student->gpkTeacher->name }}
                                    </span>
                                @endif
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                    <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-500"></i>
                                    Siswa Reguler
                                </span>
                            @endif

                            @if($student->parent_phone)
                                <a href="{{ $student->whatsapp_url }}" target="_blank"
                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-medium bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100 transition-colors">
                                    <i data-lucide="message-circle" class="w-3.5 h-3.5 text-emerald-500"></i>
                                    WA Ortu: {{ $student->parent_phone }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quick Stats Box -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 w-full md:w-auto shrink-0 bg-slate-50 dark:bg-slate-800/60 p-3 rounded-xl border border-slate-100 dark:border-slate-800">
                    <div class="flex flex-col text-center px-3 py-1">
                        <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Usia</span>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->age ?? '-' }}</span>
                    </div>
                    <div class="flex flex-col text-center px-3 py-1 border-x border-slate-200 dark:border-slate-700">
                        <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Gol. Darah</span>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->blood_type ?? '-' }}</span>
                    </div>
                    <div class="flex flex-col text-center px-3 py-1 col-span-2 sm:col-span-1">
                        <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Fisik (TB/BB)</span>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-0.5">
                            {{ $student->height ? $student->height . ' cm' : '-' }} / {{ $student->weight ? $student->weight . ' kg' : '-' }}
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- HORIZONTAL TAB NAVIGATION (STICKY WITH ARROW SCROLL INDICATORS) -->
        <div class="sticky top-0 z-20 bg-slate-50 dark:bg-slate-950/90 backdrop-blur-md pt-2 pb-1 border-b border-slate-200 dark:border-slate-800 -mx-3.5 sm:-mx-5 lg:-mx-6 px-3.5 sm:px-5 lg:px-6 tab-scroll-container group relative">
            
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
                <button type="button" @click="activeTab = 'identitas'; $el.scrollIntoView({ behavior: 'smooth', inline: 'nearest', block: 'nearest' })"
                    :class="activeTab === 'identitas' 
                        ? 'bg-indigo-600 text-white shadow-xs font-semibold' 
                        : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium'"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition-all border border-slate-200/80 dark:border-slate-800/80 cursor-pointer">
                    <i data-lucide="user" class="w-3.5 h-3.5"></i>
                    1. Identitas &amp; Rombel
                </button>

                <button type="button" @click="activeTab = 'domisili'; $el.scrollIntoView({ behavior: 'smooth', inline: 'nearest', block: 'nearest' })"
                    :class="activeTab === 'domisili' 
                        ? 'bg-indigo-600 text-white shadow-xs font-semibold' 
                        : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium'"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition-all border border-slate-200/80 dark:border-slate-800/80 cursor-pointer">
                    <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                    2. Domisili &amp; Lingkungan
                </button>

                <button type="button" @click="activeTab = 'keluarga'; $el.scrollIntoView({ behavior: 'smooth', inline: 'nearest', block: 'nearest' })"
                    :class="activeTab === 'keluarga' 
                        ? 'bg-indigo-600 text-white shadow-xs font-semibold' 
                        : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium'"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition-all border border-slate-200/80 dark:border-slate-800/80 cursor-pointer">
                    <i data-lucide="users" class="w-3.5 h-3.5"></i>
                    3. Orang Tua &amp; Keluarga
                </button>

                <button type="button" @click="activeTab = 'kesehatan'; $el.scrollIntoView({ behavior: 'smooth', inline: 'nearest', block: 'nearest' })"
                    :class="activeTab === 'kesehatan' 
                        ? 'bg-indigo-600 text-white shadow-xs font-semibold' 
                        : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium'"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition-all border border-slate-200/80 dark:border-slate-800/80 cursor-pointer">
                    <i data-lucide="heart-pulse" class="w-3.5 h-3.5"></i>
                    4. Fisik &amp; Medis
                </button>

                <button type="button" @click="activeTab = 'psikologi'; $el.scrollIntoView({ behavior: 'smooth', inline: 'nearest', block: 'nearest' })"
                    :class="activeTab === 'psikologi' 
                        ? 'bg-indigo-600 text-white shadow-xs font-semibold' 
                        : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium'"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition-all border border-slate-200/80 dark:border-slate-800/80 cursor-pointer">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                    5. Minat &amp; Karakter
                </button>

                <button type="button" @click="activeTab = 'dokumen'; $el.scrollIntoView({ behavior: 'smooth', inline: 'nearest', block: 'nearest' })"
                    :class="activeTab === 'dokumen' 
                        ? 'bg-indigo-600 text-white shadow-xs font-semibold' 
                        : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium'"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition-all border border-slate-200/80 dark:border-slate-800/80 cursor-pointer">
                    <i data-lucide="folder-archive" class="w-3.5 h-3.5"></i>
                    6. Asal Sekolah &amp; Berkas
                </button>
            </div>
        </div>

        @php
            $fd = $student->family_data ?? [];
            $hh = $student->health_habits ?? [];
            $dp = $student->development_psychology ?? [];
            $docs = $student->documents ?? [];
            $checklist = $student->checklist_documents ?? [];

            $formatStringOrArray = function($val, $default = '-') {
                if (is_null($val) || $val === '') return $default;
                if (is_array($val)) {
                    if (isset($val['summary']) && !empty($val['summary'])) return $val['summary'];
                    $items = array_filter(array_map(function($v) {
                        return is_array($v) ? implode(', ', array_filter($v)) : trim((string)$v);
                    }, $val));
                    return !empty($items) ? implode(', ', $items) : $default;
                }
                return (string) $val;
            };

            $allergyDisplay = $formatStringOrArray($student->allergy_history ?: ($hh['allergies'] ?? null), 'Tidak Ada');
            $illnessDisplay = $formatStringOrArray($student->severe_disease_history ?: ($hh['illnesses_accidents'] ?? null), 'Tidak Ada');
        @endphp

        <!-- TAB 1: IDENTITAS & ROMBEL -->
        <div x-show="activeTab === 'identitas'" x-cloak class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Card 1: Biodata Siswa -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                        <i data-lucide="user-check" class="w-5 h-5 text-indigo-600 dark:text-indigo-400"></i>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Biodata Pokok Siswa</h3>
                    </div>

                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3 text-xs">
                        <div>
                            <dt class="text-slate-400 font-medium">Nama Lengkap</dt>
                            <dd class="font-bold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->full_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Nama Panggilan</dt>
                            <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->nickname ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">NIS</dt>
                            <dd class="font-bold text-indigo-600 dark:text-indigo-400 mt-0.5">{{ $student->nis ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">NISN</dt>
                            <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->nisn ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">NIK Siswa</dt>
                            <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->nik ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">No. Kartu Keluarga (KK)</dt>
                            <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->no_kk ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">No. Akta Kelahiran</dt>
                            <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->birth_certificate_no ?? ($student->no_akta ?? '-') }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Jenis Kelamin</dt>
                            <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Tempat, Tanggal Lahir</dt>
                            <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">
                                {{ $student->birth_place ?? '-' }}, {{ $student->birth_date ? $student->birth_date->translatedFormat('d F Y') : '-' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Agama</dt>
                            <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->religion ?? 'Islam' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Kewarganegaraan</dt>
                            <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->citizenship ?? 'Indonesia (WNI)' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Suku Bangsa</dt>
                            <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->ethnicity ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Anak Ke- / Jml Saudara</dt>
                            <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">
                                Ke-{{ $student->child_order ?? '-' }} dari {{ $student->siblings_count ?? '-' }} bersaudara
                            </dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">PIN Akses Kios Presensi</dt>
                            <dd class="font-bold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->pin_access ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Card 2: Penempatan Rombel & Kategori Siswa -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                        <i data-lucide="school" class="w-5 h-5 text-indigo-600 dark:text-indigo-400"></i>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Penempatan Kelas &amp; Layanan Akademik</h3>
                    </div>

                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3 text-xs">
                        <div>
                            <dt class="text-slate-400 font-medium">Tahun Ajaran</dt>
                            <dd class="font-bold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->academicYear?->name ?? '2026/2027' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Tingkat / Grade</dt>
                            <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">
                                {{ $student->classroom?->classLevel?->name ?? 'Tingkat 7' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Rombongan Belajar (Rombel)</dt>
                            <dd class="font-bold text-indigo-600 dark:text-indigo-400 mt-0.5">{{ $student->classroom?->name ?? 'Belum Ditentukan' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Wali Kelas</dt>
                            <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->classroom?->homeroomTeacher?->name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Kategori Siswa</dt>
                            <dd class="font-bold mt-0.5 {{ $student->student_type === 'PDBK' ? 'text-purple-600 dark:text-purple-400' : 'text-slate-800 dark:text-slate-200' }}">
                                {{ $student->student_type ?? 'Reguler' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Jenis Kebutuhan Khusus</dt>
                            <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->special_needs_type ?? 'Tidak Ada' }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-slate-400 font-medium">Guru Pendamping Khusus (GPK / Shadow Teacher)</dt>
                            <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->gpkTeacher?->name ?? 'Tidak Ada GPK Khusus' }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-slate-400 font-medium">Catatan / Kebutuhan Pembelajaran Khusus</dt>
                            <dd class="text-slate-700 dark:text-slate-300 mt-0.5">{{ $student->special_needs_description ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- TAB 2: DOMISILI & LINGKUNGAN -->
        <div x-show="activeTab === 'domisili'" x-cloak class="space-y-5">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                    <i data-lucide="map-pin" class="w-5 h-5 text-indigo-600 dark:text-indigo-400"></i>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Alamat Tempat Tinggal &amp; Lingkungan Domisili</h3>
                </div>

                <dl class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-4 gap-y-3.5 text-xs">
                    <div class="sm:col-span-2 md:col-span-3">
                        <dt class="text-slate-400 font-medium">Alamat Rumah Lengkap</dt>
                        <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->address ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">Dusun / Lingkungan</dt>
                        <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->dusun ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">RT / RW</dt>
                        <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">RT {{ $student->rt ?? '-' }} / RW {{ $student->rw ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">Kelurahan / Desa</dt>
                        <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->village ?? ($student->kelurahan ?? '-') }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">Kecamatan</dt>
                        <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->district ?? ($student->kecamatan ?? '-') }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">Kota / Kabupaten</dt>
                        <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->city ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">Provinsi</dt>
                        <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->province ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">Kode Pos</dt>
                        <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->postal_code ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">Status Tempat Tinggal</dt>
                        <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->residence_status ?? 'Milik Sendiri' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">Tinggal Bersama</dt>
                        <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->living_with ?? 'Orang Tua' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">Transportasi ke Sekolah</dt>
                        <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->transportation ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">Jarak ke Sekolah</dt>
                        <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->distance_to_school ? $student->distance_to_school . ' km' : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">Waktu Tempuh</dt>
                        <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->travel_time_minutes ? $student->travel_time_minutes . ' menit' : '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- TAB 3: ORANG TUA & KELUARGA -->
        <div x-show="activeTab === 'keluarga'" x-cloak class="space-y-5">
            <!-- Komparasi Ayah & Ibu -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Data Ayah -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                        <i data-lucide="user" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Data Ayah Kandung</h3>
                    </div>

                    <dl class="space-y-3 text-xs">
                        <div>
                            <dt class="text-slate-400 font-medium">Nama Lengkap</dt>
                            <dd class="font-bold text-slate-800 dark:text-slate-200 mt-0.5">
                                {{ trim(($student->father_title_front ?? '') . ' ' . ($student->father_name ?? '-') . ' ' . ($student->father_title_back ?? '')) ?: '-' }}
                            </dd>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <dt class="text-slate-400 font-medium">NIK Ayah</dt>
                                <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->father_nik ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-400 font-medium">Agama Ayah</dt>
                                <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->father_religion ?? '-' }}</dd>
                            </div>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Tempat, Tgl Lahir Ayah</dt>
                            <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">
                                {{ $student->father_birth_place ?? '-' }}, {{ $student->father_birth_date ? $student->father_birth_date->format('d/m/Y') : '-' }}
                            </dd>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <dt class="text-slate-400 font-medium">Pendidikan</dt>
                                <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->father_education ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-400 font-medium">Pekerjaan</dt>
                                <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->father_job ?? '-' }}</dd>
                            </div>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Instansi / Tempat Bekerja</dt>
                            <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->father_company ?? '-' }}</dd>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <dt class="text-slate-400 font-medium">Penghasilan Bulanan</dt>
                                <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->father_income ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-400 font-medium">No. Telepon / WA</dt>
                                <dd class="font-bold text-indigo-600 dark:text-indigo-400 mt-0.5">{{ $student->father_phone ?? '-' }}</dd>
                            </div>
                        </div>
                    </dl>
                </div>

                <!-- Data Ibu -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                        <i data-lucide="user" class="w-5 h-5 text-rose-600 dark:text-rose-400"></i>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Data Ibu Kandung</h3>
                    </div>

                    <dl class="space-y-3 text-xs">
                        <div>
                            <dt class="text-slate-400 font-medium">Nama Lengkap</dt>
                            <dd class="font-bold text-slate-800 dark:text-slate-200 mt-0.5">
                                {{ trim(($student->mother_title_front ?? '') . ' ' . ($student->mother_name ?? '-') . ' ' . ($student->mother_title_back ?? '')) ?: '-' }}
                            </dd>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <dt class="text-slate-400 font-medium">NIK Ibu</dt>
                                <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->mother_nik ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-400 font-medium">Agama Ibu</dt>
                                <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->mother_religion ?? '-' }}</dd>
                            </div>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Tempat, Tgl Lahir Ibu</dt>
                            <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">
                                {{ $student->mother_birth_place ?? '-' }}, {{ $student->mother_birth_date ? $student->mother_birth_date->format('d/m/Y') : '-' }}
                            </dd>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <dt class="text-slate-400 font-medium">Pendidikan</dt>
                                <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->mother_education ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-400 font-medium">Pekerjaan</dt>
                                <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->mother_job ?? '-' }}</dd>
                            </div>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Instansi / Tempat Bekerja</dt>
                            <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->mother_company ?? '-' }}</dd>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <dt class="text-slate-400 font-medium">Penghasilan Bulanan</dt>
                                <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->mother_income ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-400 font-medium">No. Telepon / WA</dt>
                                <dd class="font-bold text-rose-600 dark:text-rose-400 mt-0.5">{{ $student->mother_phone ?? '-' }}</dd>
                            </div>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Wali & Kontak Darurat -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Data Wali -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                        <i data-lucide="shield-check" class="w-5 h-5 text-emerald-600 dark:text-emerald-400"></i>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Data Wali Murid (Opsional)</h3>
                    </div>

                    @if($student->guardian_name)
                        <dl class="space-y-3 text-xs">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <dt class="text-slate-400 font-medium">Nama Wali</dt>
                                    <dd class="font-bold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->guardian_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-slate-400 font-medium">Hubungan</dt>
                                    <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->guardian_relation ?? 'Wali' }}</dd>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <dt class="text-slate-400 font-medium">NIK Wali</dt>
                                    <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->guardian_nik ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-slate-400 font-medium">No. Telepon / WA</dt>
                                    <dd class="font-bold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->guardian_phone ?? '-' }}</dd>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <dt class="text-slate-400 font-medium">Pendidikan</dt>
                                    <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->guardian_education ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-slate-400 font-medium">Pekerjaan</dt>
                                    <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->guardian_job ?? '-' }}</dd>
                                </div>
                            </div>
                        </dl>
                    @else
                        <p class="text-xs text-slate-400 italic">Siswa ini tidak memiliki data wali (tinggal bersama orang tua kandung).</p>
                    @endif
                </div>

                <!-- Kontak Utama & Darurat -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                        <i data-lucide="phone-call" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Kontak Utama &amp; Darurat</h3>
                    </div>

                    <dl class="space-y-3 text-xs">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <dt class="text-slate-400 font-medium">No. WA Utama Ortu</dt>
                                <dd class="font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">{{ $student->parent_phone ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-400 font-medium">Email Orang Tua</dt>
                                <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->parent_email ?? '-' }}</dd>
                            </div>
                        </div>
                        <div class="border-t border-slate-100 dark:border-slate-800 pt-3">
                            <div class="font-semibold text-slate-600 dark:text-slate-300 mb-2">Kontak Darurat (Emergency Contact):</div>
                            <div class="grid grid-cols-3 gap-2">
                                <div>
                                    <dt class="text-slate-400 font-medium">Nama Kontak</dt>
                                    <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->emergency_contact_name ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-slate-400 font-medium">Hubungan</dt>
                                    <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->emergency_contact_relation ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-slate-400 font-medium">Nomor Telepon</dt>
                                    <dd class="font-bold text-amber-600 dark:text-amber-400 mt-0.5">{{ $student->emergency_contact_phone ?? '-' }}</dd>
                                </div>
                            </div>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- TAB 4: FISIK & MEDIS -->
        <div x-show="activeTab === 'kesehatan'" x-cloak class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Antropometri & Fisik -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                        <i data-lucide="activity" class="w-5 h-5 text-indigo-600 dark:text-indigo-400"></i>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Antropometri &amp; Ciri Fisik</h3>
                    </div>

                    <dl class="grid grid-cols-2 sm:grid-cols-3 gap-3.5 text-xs">
                        <div>
                            <dt class="text-slate-400 font-medium">Tinggi Badan</dt>
                            <dd class="font-bold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->height ? $student->height . ' cm' : '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Berat Badan</dt>
                            <dd class="font-bold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->weight ? $student->weight . ' kg' : '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Golongan Darah</dt>
                            <dd class="font-bold text-rose-600 dark:text-rose-400 mt-0.5">{{ $student->blood_type ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Warna Kulit</dt>
                            <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->skin_color ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Bentuk Rambut</dt>
                            <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->hair_type ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Warna Rambut</dt>
                            <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->hair_color ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Riwayat Kesehatan & UKS -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                        <i data-lucide="heart" class="w-5 h-5 text-rose-600 dark:text-rose-400"></i>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Riwayat Kesehatan &amp; Rekam Medis UKS</h3>
                    </div>

                    <dl class="space-y-3 text-xs">
                        <div>
                            <dt class="text-slate-400 font-medium">Riwayat Alergi (Makanan / Obat / Dingin)</dt>
                            <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $allergyDisplay }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Riwayat Penyakit Berat / Pernah Dirawat</dt>
                            <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $illnessDisplay }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Penyakit yang Sering Diderita</dt>
                            <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->frequent_disease ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Kelainan Fisik / Catatan Khusus</dt>
                            <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->physical_abnormality ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- TAB 5: MINAT & KARAKTER -->
        <div x-show="activeTab === 'psikologi'" x-cloak class="space-y-5">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                    <i data-lucide="sparkles" class="w-5 h-5 text-indigo-600 dark:text-indigo-400"></i>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Minat, Bakat &amp; Karakteristik Siswa</h3>
                </div>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <dt class="text-slate-400 font-medium">Hobi / Kegemaran Siswa</dt>
                        <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->student_hobby ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">Cita-cita Siswa</dt>
                        <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->student_aspiration ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">Minat Ekstrakurikuler</dt>
                        <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->extracurricular_interest ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">Harapan Orang Tua Terhadap Sekolah</dt>
                        <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->parent_aspiration ?? '-' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-slate-400 font-medium">Karakter / Kemandirian Siswa</dt>
                        <dd class="text-slate-700 dark:text-slate-300 mt-0.5">{{ $dp['independence'] ?? 'Mampu beradaptasi secara baik dengan lingkungan belajar SMP.' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- TAB 6: ASAL SEKOLAH & BERKAS DOKUMEN -->
        <div x-show="activeTab === 'dokumen'" x-cloak class="space-y-5">
            <!-- Asal Sekolah -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                    <i data-lucide="graduation-cap" class="w-5 h-5 text-indigo-600 dark:text-indigo-400"></i>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Riwayat Sekolah Asal (SD / MI)</h3>
                </div>

                <dl class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                    <div class="sm:col-span-2">
                        <dt class="text-slate-400 font-medium">Nama Sekolah Asal (SD/MI)</dt>
                        <dd class="font-bold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->previous_school ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">NPSN Sekolah Asal</dt>
                        <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->previous_school_npsn ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">Alamat Sekolah Asal</dt>
                        <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->previous_school_address ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">No. Sertifikat/Ijazah TK/MI</dt>
                        <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->diploma_number ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">No. SKHUN / Kelulusan</dt>
                        <dd class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ $student->skhun_number ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">Nilai Rata-rata Ujian</dt>
                        <dd class="font-bold text-indigo-600 dark:text-indigo-400 mt-0.5">{{ $student->previous_school_score ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Berkas & Checklist Dokumen -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                    <i data-lucide="files" class="w-5 h-5 text-indigo-600 dark:text-indigo-400"></i>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Kelengkapan Berkas &amp; Dokumen Digital</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @php
                        $docKeys = [
                            'kk' => 'Kartu Keluarga (KK)',
                            'akta' => 'Akta Kelahiran',
                            'ktp_ayah' => 'KTP Ayah',
                            'ktp_ibu' => 'KTP Ibu',
                            'ijazah' => 'Ijazah SD / MI',
                            'skl' => 'Surat Keterangan Lulus (SKL)',
                            'rapor' => 'Rapor SD Semester Terakhir',
                            'kia' => 'Kartu Identitas Anak (KIA)',
                            'kip' => 'Kartu KIP / PIP / PKH',
                        ];
                    @endphp

                    @foreach($docKeys as $k => $label)
                        @php
                            $isUploaded = !empty($docs[$k]);
                            $isChecked = !empty($checklist[$k]) || $isUploaded;
                        @endphp
                        <div class="p-3 rounded-xl border {{ $isChecked ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/60' : 'bg-slate-50 dark:bg-slate-800/40 border-slate-200 dark:border-slate-800' }} flex items-start gap-2.5">
                            <i data-lucide="{{ $isChecked ? 'check-circle' : 'circle-dashed' }}" 
                                class="w-4 h-4 mt-0.5 shrink-0 {{ $isChecked ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }}"></i>
                            <div class="min-w-0 flex-1">
                                <div class="text-xs font-semibold text-slate-800 dark:text-slate-200">{{ $label }}</div>
                                <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">
                                    @if($isUploaded)
                                        <a href="{{ asset('storage/' . $docs[$k]) }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1 font-medium">
                                            <i data-lucide="external-link" class="w-3 h-3"></i>
                                            Buka Dokumen
                                        </a>
                                    @elseif($isChecked)
                                        <span class="text-emerald-600 dark:text-emerald-400 font-medium">Fisik Telah Diverifikasi</span>
                                    @else
                                        <span class="text-slate-400">Belum diserahkan</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</x-admin-layout>
