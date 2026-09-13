<x-admin-layout>
    <div x-data="{ 
            showEditModal: {{ $errors->any() ? 'true' : 'false' }},
            photoPreview: null,
            isSaving: false,
            frontTitle: '{{ addslashes(old('front_title', $employee->front_title)) }}',
            rawName: '{{ addslashes(old('name', $employee->raw_name)) }}',
            backTitle: '{{ addslashes(old('back_title', $employee->back_title)) }}',
            get formattedFullName() {
                let f = this.frontTitle ? this.frontTitle.trim() : '';
                let n = this.rawName ? this.rawName.trim() : '';
                let b = this.backTitle ? this.backTitle.trim() : '';
                if (!n) return '(Nama belum diisi)';
                let res = '';
                if (f) res += f + ' ';
                res += n;
                if (b) res += ', ' + b;
                return res;
            },
            get hasTitleInName() {
                if (!this.rawName) return false;
                const titleRegex = /(^|[\s,\.])(dr\.|drg\.|dra\.|drs\.|prof\.|kh\.|kh\b|hj\.|hj\b|h\.|h\b|ust\.|ustad\b|ustadz\b|s\.pd|m\.pd|s\.kom|m\.kom|s\.e|m\.m|s\.si|m\.si|s\.ag|m\.ag|s\.t|m\.t|s\.h|m\.h|s\.sos|m\.sos|s\.ked|s\.psi|m\.psi|l\.c|ph\.d|b\.a|m\.a)([\s,\.]|$)/i;
                return titleRegex.test(this.rawName) || this.rawName.includes(',');
            }
         }" 
         class="p-4 sm:p-6 space-y-6 w-full">
         
        <!-- PROFIL PEGAWAI / PAGE TITLE -->
        <section class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                        <i data-lucide="user-check" class="w-4 h-4"></i>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Profil Pegawai</h2>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400">Informasi biodata diri, identitas resmi pendidik, dan data kepegawaian Anda.</p>
            </div>
            <button @click="showEditModal = true; photoPreview = null;"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 dark:bg-slate-100 px-4 py-2.5 text-xs font-semibold text-white dark:text-slate-900 shadow-sm hover:bg-slate-800 dark:hover:bg-slate-200 transition-all cursor-pointer">
                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                <span>Edit Profil</span>
            </button>
        </section>

        <!-- BENTO GRID PROFIL -->
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">

            <!-- KARTU IDENTITAS UTAMA (KOLOM KIRI) -->
            <div class="lg:col-span-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xs flex flex-col items-center text-center gap-4 transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                <!-- Foto Profil -->
                <div class="relative group">
                    <div class="w-28 h-28 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 shrink-0 overflow-hidden shadow-md ring-4 ring-slate-100 dark:ring-slate-800/80">
                        @if($employee->photo)
                            <img src="{{ Storage::url($employee->photo) }}" alt="Foto {{ $employee->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-3xl font-extrabold uppercase text-slate-600 dark:text-slate-300">{{ substr($employee->raw_name ?: $employee->name, 0, 2) }}</span>
                        @endif
                    </div>
                    <button @click="showEditModal = true; photoPreview = null;" class="absolute bottom-0 right-0 w-8 h-8 rounded-full bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 flex items-center justify-center shadow-md hover:scale-105 transition-transform" title="Ganti Foto Profil">
                        <i data-lucide="camera" class="w-3.5 h-3.5"></i>
                    </button>
                </div>

                <!-- Nama & Jabatan -->
                <div class="w-full">
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-slate-50 leading-tight">
                        {{ $employee->name }}
                    </h3>
                    @if($employee->position || $employee->additional_position)
                        <div class="inline-flex flex-wrap items-center justify-center gap-1.5 mt-2">
                            @if($employee->position)
                                <span class="px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-800/50">
                                    {{ $employee->position }}
                                </span>
                            @endif
                            @if($employee->additional_position)
                                <span class="px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/50">
                                    {{ $employee->additional_position }}
                                </span>
                            @endif
                        </div>
                    @endif
                    
                    <div class="mt-2.5">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700/80">
                            {{ $employee->employeeType->name ?? 'Pegawai' }}
                        </span>
                    </div>
                </div>

                <!-- Quick Action Buttons -->
                <div class="w-full grid grid-cols-2 gap-2 pt-2">
                    @if($employee->phone)
                        @php
                            $cleanPhone = preg_replace('/[^0-9]/', '', $employee->phone);
                            if (str_starts_with($cleanPhone, '0')) {
                                $cleanPhone = '62' . substr($cleanPhone, 1);
                            }
                        @endphp
                        <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/50 text-xs font-semibold hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors">
                            <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                            <span>WhatsApp</span>
                        </a>
                    @else
                        <button disabled class="flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 text-slate-400 border border-slate-200/40 dark:border-slate-800 text-xs font-semibold opacity-60 cursor-not-allowed">
                            <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                            <span>WhatsApp</span>
                        </button>
                    @endif

                    @if($employee->email)
                        <a href="mailto:{{ $employee->email }}" class="flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 text-xs font-semibold hover:bg-slate-100 dark:hover:bg-slate-750 transition-colors">
                            <i data-lucide="mail" class="w-3.5 h-3.5"></i>
                            <span>Email</span>
                        </a>
                    @else
                        <button disabled class="flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 text-slate-400 border border-slate-200/40 dark:border-slate-800 text-xs font-semibold opacity-60 cursor-not-allowed">
                            <i data-lucide="mail" class="w-3.5 h-3.5"></i>
                            <span>Email</span>
                        </button>
                    @endif
                </div>

                <!-- Info Singkat Bawah -->
                <div class="w-full border-t border-slate-100 dark:border-slate-800 pt-4 space-y-2.5 text-left text-xs">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0 text-slate-500 dark:text-slate-400">
                            <i data-lucide="phone" class="w-3.5 h-3.5"></i>
                        </span>
                        <span class="text-slate-600 dark:text-slate-300 truncate font-medium">{{ $employee->phone ?: 'Belum diisi' }}</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0 text-slate-500 dark:text-slate-400">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                        </span>
                        <span class="text-slate-600 dark:text-slate-300 truncate font-medium">{{ $employee->address ?: 'Belum diisi' }}</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0 text-slate-500 dark:text-slate-400">
                            <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                        </span>
                        <span class="text-slate-600 dark:text-slate-300 truncate font-medium">
                            @if($employee->birth_date)
                                {{ \Carbon\Carbon::parse($employee->birth_date)->translatedFormat('d F Y') }}
                                <span class="text-slate-400 text-[11px]">({{ \Carbon\Carbon::parse($employee->birth_date)->age }} th)</span>
                            @else
                                Belum diisi
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: BENTO TILES -->
            <div class="lg:col-span-2 space-y-5">

                <!-- BARIS ATAS: 2 TILE BENTO -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                    <!-- TILE 1: DATA PRIBADI & KONTAK -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-xs transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3 mb-4">
                            <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2 uppercase tracking-wider">
                                <span class="w-6 h-6 rounded-lg bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                                    <i data-lucide="user" class="w-3.5 h-3.5"></i>
                                </span>
                                <span>Data Pribadi</span>
                            </h4>
                        </div>

                        <dl class="space-y-3.5 text-xs">
                            <div class="flex flex-col gap-0.5">
                                <dt class="font-medium text-slate-400 dark:text-slate-500">Tempat Lahir</dt>
                                <dd class="font-semibold text-slate-900 dark:text-slate-100">{{ $employee->birth_place ?: '-' }}</dd>
                            </div>
                            <div class="flex flex-col gap-0.5">
                                <dt class="font-medium text-slate-400 dark:text-slate-500">Tanggal Lahir</dt>
                                <dd class="font-semibold text-slate-900 dark:text-slate-100">
                                    {{ $employee->birth_date ? \Carbon\Carbon::parse($employee->birth_date)->translatedFormat('d F Y') : '-' }}
                                    @if($employee->birth_date)
                                        <span class="text-slate-400 dark:text-slate-500 font-normal">({{ \Carbon\Carbon::parse($employee->birth_date)->age }} Tahun)</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="flex flex-col gap-0.5">
                                <dt class="font-medium text-slate-400 dark:text-slate-500">Jenis Kelamin</dt>
                                <dd class="font-semibold text-slate-900 dark:text-slate-100">
                                    {{ in_array($employee->gender, ['Male', 'L']) ? 'Laki-laki' : (in_array($employee->gender, ['Female', 'P']) ? 'Perempuan' : '-') }}
                                </dd>
                            </div>
                            <div class="flex flex-col gap-0.5">
                                <dt class="font-medium text-slate-400 dark:text-slate-500">Nomor HP / WhatsApp</dt>
                                <dd class="font-semibold text-slate-900 dark:text-slate-100">{{ $employee->phone ?: '-' }}</dd>
                            </div>
                            <div class="flex flex-col gap-0.5">
                                <dt class="font-medium text-slate-400 dark:text-slate-500">Alamat Tempat Tinggal</dt>
                                <dd class="font-semibold text-slate-900 dark:text-slate-100 leading-relaxed">{{ $employee->address ?: '-' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- TILE 2: IDENTITAS RESMI & PENDIDIK (DENGAN SALIN CEPAT) -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-xs transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3 mb-4">
                            <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2 uppercase tracking-wider">
                                <span class="w-6 h-6 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                                    <i data-lucide="badge-check" class="w-3.5 h-3.5"></i>
                                </span>
                                <span>Identitas Resmi</span>
                            </h4>
                        </div>

                        <dl class="space-y-3 text-xs">
                            <!-- NIK -->
                            <div x-data="{ copied: false }" class="flex flex-col gap-0.5">
                                <dt class="font-medium text-slate-400 dark:text-slate-500">NIK (KTP)</dt>
                                <dd class="flex items-center justify-between">
                                    <span class="font-semibold font-mono tracking-wide text-slate-900 dark:text-slate-100">{{ $employee->nik ?: '-' }}</span>
                                    @if($employee->nik)
                                        <button type="button" @click="navigator.clipboard.writeText('{{ $employee->nik }}'); copied = true; setTimeout(() => copied = false, 2000)" class="p-1 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="Salin NIK">
                                            <i x-show="!copied" data-lucide="copy" class="w-3.5 h-3.5"></i>
                                            <span x-show="copied" style="display: none;" class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                                <i data-lucide="check" class="w-3 h-3"></i> Disalin
                                            </span>
                                        </button>
                                    @endif
                                </dd>
                            </div>

                            <!-- NIY -->
                            <div x-data="{ copied: false }" class="flex flex-col gap-0.5">
                                <dt class="font-medium text-slate-400 dark:text-slate-500">NIY (Nomor Induk Yayasan)</dt>
                                <dd class="flex items-center justify-between">
                                    <span class="font-semibold font-mono tracking-wide text-slate-900 dark:text-slate-100">{{ $employee->niy ?: '-' }}</span>
                                    @if($employee->niy)
                                        <button type="button" @click="navigator.clipboard.writeText('{{ $employee->niy }}'); copied = true; setTimeout(() => copied = false, 2000)" class="p-1 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="Salin NIY">
                                            <i x-show="!copied" data-lucide="copy" class="w-3.5 h-3.5"></i>
                                            <span x-show="copied" style="display: none;" class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                                <i data-lucide="check" class="w-3 h-3"></i> Disalin
                                            </span>
                                        </button>
                                    @endif
                                </dd>
                            </div>

                            <!-- NUPTK -->
                            <div x-data="{ copied: false }" class="flex flex-col gap-0.5">
                                <dt class="font-medium text-slate-400 dark:text-slate-500">NUPTK</dt>
                                <dd class="flex items-center justify-between">
                                    <span class="font-semibold font-mono tracking-wide text-slate-900 dark:text-slate-100">{{ $employee->nuptk ?: '-' }}</span>
                                    @if($employee->nuptk)
                                        <button type="button" @click="navigator.clipboard.writeText('{{ $employee->nuptk }}'); copied = true; setTimeout(() => copied = false, 2000)" class="p-1 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="Salin NUPTK">
                                            <i x-show="!copied" data-lucide="copy" class="w-3.5 h-3.5"></i>
                                            <span x-show="copied" style="display: none;" class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                                <i data-lucide="check" class="w-3 h-3"></i> Disalin
                                            </span>
                                        </button>
                                    @endif
                                </dd>
                            </div>

                            <!-- NO UKG -->
                            <div x-data="{ copied: false }" class="flex flex-col gap-0.5">
                                <dt class="font-medium text-slate-400 dark:text-slate-500">No. UKG</dt>
                                <dd class="flex items-center justify-between">
                                    <span class="font-semibold font-mono tracking-wide text-slate-900 dark:text-slate-100">{{ $employee->no_ukg ?: '-' }}</span>
                                    @if($employee->no_ukg)
                                        <button type="button" @click="navigator.clipboard.writeText('{{ $employee->no_ukg }}'); copied = true; setTimeout(() => copied = false, 2000)" class="p-1 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="Salin No UKG">
                                            <i x-show="!copied" data-lucide="copy" class="w-3.5 h-3.5"></i>
                                            <span x-show="copied" style="display: none;" class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                                <i data-lucide="check" class="w-3 h-3"></i> Disalin
                                            </span>
                                        </button>
                                    @endif
                                </dd>
                            </div>

                            <!-- NRG -->
                            <div x-data="{ copied: false }" class="flex flex-col gap-0.5">
                                <dt class="font-medium text-slate-400 dark:text-slate-500">NRG (Nomor Registrasi Guru)</dt>
                                <dd class="flex items-center justify-between">
                                    <span class="font-semibold font-mono tracking-wide text-slate-900 dark:text-slate-100">{{ $employee->nrg ?: '-' }}</span>
                                    @if($employee->nrg)
                                        <button type="button" @click="navigator.clipboard.writeText('{{ $employee->nrg }}'); copied = true; setTimeout(() => copied = false, 2000)" class="p-1 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="Salin NRG">
                                            <i x-show="!copied" data-lucide="copy" class="w-3.5 h-3.5"></i>
                                            <span x-show="copied" style="display: none;" class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                                <i data-lucide="check" class="w-3 h-3"></i> Disalin
                                            </span>
                                        </button>
                                    @endif
                                </dd>
                            </div>

                            <!-- PANGKAT / GOLONGAN -->
                            <div class="flex flex-col gap-0.5">
                                <dt class="font-medium text-slate-400 dark:text-slate-500">Pangkat / Golongan</dt>
                                <dd class="font-semibold text-slate-900 dark:text-slate-100">{{ $employee->pangkat_golongan ?: '-' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- BARIS BAWAH: TILE 3 PENDIDIKAN & PENUGASAN (LEBAR PENUH) -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-xs transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3 mb-4">
                        <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2 uppercase tracking-wider">
                            <span class="w-6 h-6 rounded-lg bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                                <i data-lucide="graduation-cap" class="w-3.5 h-3.5"></i>
                            </span>
                            <span>Pendidikan & Penugasan</span>
                        </h4>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                        <div class="flex flex-col gap-1 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                            <dt class="font-medium text-slate-400 dark:text-slate-500">Pendidikan Terakhir</dt>
                            <dd class="font-bold text-slate-900 dark:text-slate-100 text-sm">{{ $employee->last_education ?: '-' }}</dd>
                        </div>
                        <div class="flex flex-col gap-1 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                            <dt class="font-medium text-slate-400 dark:text-slate-500">Jurusan / Program Studi</dt>
                            <dd class="font-bold text-slate-900 dark:text-slate-100 text-sm">{{ $employee->major ?: '-' }}</dd>
                        </div>
                        <div class="flex flex-col gap-1 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                            <dt class="font-medium text-slate-400 dark:text-slate-500">Status Kepegawaian</dt>
                            <dd class="font-bold text-slate-900 dark:text-slate-100 text-sm">
                                {{ $employee->employment_status ?: ($employee->employeeType->name ?? '-') }}
                            </dd>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- MODAL FORM EDIT PROFIL -->
        <template x-teleport="body">
            <div x-show="showEditModal" 
                 x-init="if (window.lucide) window.lucide.createIcons()" 
                 style="display: none; z-index: 9999;" 
                 class="fixed inset-0 z-50 overflow-y-auto"
                 aria-labelledby="modal-title" 
                 role="dialog" 
                 aria-modal="true">
                 
                <!-- Backdrop -->
                <div x-show="showEditModal" 
                     x-transition.opacity
                     class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity"
                     @click="if('{{ $errors->any() }}') { window.location.href = window.location.pathname; } else { showEditModal = false; photoPreview = null; $refs.profileForm.reset(); }">
                </div>

                <div class="flex min-h-full items-center justify-center p-3 sm:p-4 text-center">
                    <div x-show="showEditModal" 
                         x-transition.opacity.scale.95
                         class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-900 text-left shadow-2xl transition-all sm:my-8 w-full sm:max-w-3xl border border-slate-200 dark:border-slate-800">
                        
                        <div class="flex flex-col max-h-[88vh]">
                            <!-- Header Modal -->
                            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-800 shrink-0 bg-slate-50/50 dark:bg-slate-900/50">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold text-slate-900 dark:text-slate-50" id="modal-title">Edit Profil Pegawai</h3>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Perbarui data diri dan informasi resmi kepegawaian Anda.</p>
                                    </div>
                                </div>
                                <button type="button"
                                    @click="if('{{ $errors->any() }}') { window.location.href = window.location.pathname; } else { showEditModal = false; photoPreview = null; $refs.profileForm.reset(); }"
                                    class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-500 transition-colors cursor-pointer">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                            </div>

                            <!-- Body Form -->
                            <div class="px-6 py-5 overflow-y-auto custom-scrollbar flex-1">
                                <form x-ref="profileForm" 
                                      action="{{ route('my-employee-profile.update') }}"
                                      method="POST" 
                                      enctype="multipart/form-data" 
                                      @submit="isSaving = true"
                                      class="space-y-6 text-xs">
                                    @csrf
                                    @method('PUT')

                                    <!-- SECTION 1: FOTO PROFIL (LIVE PREVIEW) -->
                                    <div class="border border-slate-200 dark:border-slate-800 rounded-2xl p-4 sm:p-5 flex flex-col sm:flex-row items-center gap-5 bg-slate-50/40 dark:bg-slate-800/20">
                                        <div class="relative">
                                            <!-- Preview State -->
                                            <template x-if="photoPreview">
                                                <div class="w-20 h-20 rounded-full overflow-hidden ring-4 ring-indigo-500 shadow-md">
                                                    <img :src="photoPreview" alt="Preview Foto" class="w-full h-full object-cover">
                                                </div>
                                            </template>
                                            <template x-if="!photoPreview">
                                                <div class="w-20 h-20 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 shrink-0 overflow-hidden shadow-md ring-4 ring-slate-200 dark:ring-slate-700">
                                                    @if($employee->photo)
                                                        <img src="{{ Storage::url($employee->photo) }}" alt="Foto" class="w-full h-full object-cover">
                                                    @else
                                                        <span class="text-2xl font-bold uppercase">{{ substr($employee->raw_name ?: $employee->name, 0, 2) }}</span>
                                                    @endif
                                                </div>
                                            </template>
                                        </div>
                                        <div class="flex-1 text-center sm:text-left">
                                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Unggah Foto Profil Baru</label>
                                            <input type="file" 
                                                   name="photo" 
                                                   accept="image/*"
                                                   @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => { photoPreview = e.target.result; }; reader.readAsDataURL(file); }"
                                                   class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-950/40 dark:file:text-indigo-300 cursor-pointer">
                                            @error('photo') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                            <p class="text-[11px] text-slate-400 mt-1.5">Format didukung: JPG, PNG, WEBP. Maksimal ukuran file: 2MB.</p>
                                        </div>
                                    </div>

                                    <!-- SECTION 2: GELAR & NAMA LENGKAP -->
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between">
                                            <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                                <span>Nama & Gelar</span>
                                            </h4>
                                            <span class="text-[11px] text-slate-400 dark:text-slate-500">Gelar depan & belakang dipisah</span>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3.5">
                                            <div>
                                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                                    Gelar Depan
                                                </label>
                                                <input type="text" name="front_title" placeholder="Contoh: Dr. / Dra. / Hj."
                                                    x-model="frontTitle"
                                                    value="{{ old('front_title', $employee->front_title) }}"
                                                    class="w-full text-xs h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                                @error('front_title') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                            </div>

                                            <div class="sm:col-span-2">
                                                <label class="block text-[11px] font-bold text-slate-900 dark:text-slate-100 mb-1 flex items-center justify-between">
                                                    <span>Nama Lengkap (Wajib Tanpa Gelar) <span class="text-rose-500">*</span></span>
                                                    <span class="text-[10px] font-semibold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 px-1.5 py-0.5 rounded border border-rose-200 dark:border-rose-900/50">Hanya Nama Asli</span>
                                                </label>
                                                <input type="text" name="name" required placeholder="Contoh: Sri Yudiyanti"
                                                    x-model="rawName"
                                                    value="{{ old('name', $employee->raw_name) }}"
                                                    :class="{ 'border-amber-400 dark:border-amber-500 ring-2 ring-amber-400/20': hasTitleInName }"
                                                    class="w-full text-xs h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                                @error('name') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                            </div>

                                            <div>
                                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                                    Gelar Belakang
                                                </label>
                                                <input type="text" name="back_title" placeholder="Contoh: S.Pd / M.M"
                                                    x-model="backTitle"
                                                    value="{{ old('back_title', $employee->back_title) }}"
                                                    class="w-full text-xs h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                                @error('back_title') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                            </div>
                                        </div>

                                        <!-- REAL-TIME SMART WARNING IF TITLE DETECTED IN RAW NAME -->
                                        <div x-show="hasTitleInName" 
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="opacity-0 -translate-y-1"
                                             x-transition:enter-end="opacity-100 translate-y-0"
                                             style="display: none;" 
                                             class="p-2.5 rounded-xl bg-amber-50/90 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 text-amber-800 dark:text-amber-300 flex items-start gap-2.5 text-[11px]">
                                            <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5"></i>
                                            <div>
                                                <div class="font-bold">Perhatian: Terdeteksi gelar atau tanda koma di kolom Nama Lengkap!</div>
                                                <p class="text-[10px] text-amber-700/90 dark:text-amber-400 mt-0.5">
                                                    Mohon hapus singkatan gelar dari kolom nama ini. Pindahkan gelar depan (Dr., Hj., Drs., dll) ke kolom <b>Gelar Depan</b> dan gelar akademik (S.Pd, M.M, dll) ke kolom <b>Gelar Belakang</b>.
                                                </p>
                                            </div>
                                        </div>

                                        <!-- REAL-TIME LIVE NAME PREVIEW BOX -->
                                        <div class="p-3 rounded-xl bg-slate-100/80 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/60 flex flex-col sm:flex-row sm:items-center justify-between gap-1.5">
                                            <div class="flex items-center gap-1.5 text-[11px] font-medium text-slate-500 dark:text-slate-400">
                                                <i data-lucide="eye" class="w-3.5 h-3.5 text-indigo-500"></i>
                                                <span>Pratinjau Nama Resmi Sistem:</span>
                                            </div>
                                            <div class="text-xs font-bold text-slate-900 dark:text-slate-100 tracking-wide truncate" x-text="formattedFullName"></div>
                                        </div>
                                    </div>

                                    <!-- SECTION 3: DATA PRIBADI & KONTAK -->
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 mb-3 flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                            <span>Data Pribadi & Kontak</span>
                                        </h4>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Tempat Lahir</label>
                                                <input type="text" name="birth_place" placeholder="Kota / Kabupaten lahir"
                                                    value="{{ old('birth_place', $employee->birth_place) }}"
                                                    class="w-full text-xs h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                                @error('birth_place') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Tanggal Lahir</label>
                                                <input type="date" name="birth_date"
                                                    value="{{ old('birth_date', $employee->birth_date) }}"
                                                    class="w-full text-xs h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                                @error('birth_date') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                                                <select name="gender" required
                                                    class="w-full text-xs h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                                    <option value="Male" {{ in_array(old('gender', $employee->gender), ['Male', 'L']) ? 'selected' : '' }}>Laki-laki</option>
                                                    <option value="Female" {{ in_array(old('gender', $employee->gender), ['Female', 'P']) ? 'selected' : '' }}>Perempuan</option>
                                                </select>
                                                @error('gender') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Nomor HP / WhatsApp</label>
                                                <input type="text" name="phone" placeholder="Contoh: 08123456789"
                                                    value="{{ old('phone', $employee->phone) }}"
                                                    class="w-full text-xs h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                                @error('phone') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Alamat Email</label>
                                                <input type="email" name="email" placeholder="nama@email.com"
                                                    value="{{ old('email', $employee->email) }}"
                                                    class="w-full text-xs h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                                @error('email') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Alamat Domisili Lengkap</label>
                                                <input type="text" name="address" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota"
                                                    value="{{ old('address', $employee->address) }}"
                                                    class="w-full text-xs h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                                @error('address') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- SECTION 4: IDENTITAS RESMI & PENDIDIK -->
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 mb-3 flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            <span>Identitas Resmi & Pendidik</span>
                                        </h4>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">NIK (KTP)</label>
                                                <input type="text" name="nik" placeholder="16 digit NIK"
                                                    value="{{ old('nik', $employee->nik) }}"
                                                    class="w-full text-xs h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-mono">
                                                @error('nik') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">NIY (Nomor Induk Yayasan)</label>
                                                <input type="text" name="niy" placeholder="Nomor Induk Yayasan"
                                                    value="{{ old('niy', $employee->niy) }}"
                                                    class="w-full text-xs h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-mono">
                                                @error('niy') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">NUPTK</label>
                                                <input type="text" name="nuptk" placeholder="16 digit NUPTK"
                                                    value="{{ old('nuptk', $employee->nuptk) }}"
                                                    class="w-full text-xs h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-mono">
                                                @error('nuptk') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">No. UKG</label>
                                                <input type="text" name="no_ukg" placeholder="Nomor Peserta UKG"
                                                    value="{{ old('no_ukg', $employee->no_ukg) }}"
                                                    class="w-full text-xs h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-mono">
                                                @error('no_ukg') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">NRG (Nomor Registrasi Guru)</label>
                                                <input type="text" name="nrg" placeholder="Nomor Registrasi Guru"
                                                    value="{{ old('nrg', $employee->nrg) }}"
                                                    class="w-full text-xs h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-mono">
                                                @error('nrg') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Pangkat / Golongan</label>
                                                <input type="text" name="pangkat_golongan" placeholder="Contoh: Penata Muda / III/a"
                                                    value="{{ old('pangkat_golongan', $employee->pangkat_golongan) }}"
                                                    class="w-full text-xs h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                                @error('pangkat_golongan') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- SECTION 5: PENDIDIKAN -->
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 mb-3 flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                            <span>Pendidikan Terakhir</span>
                                        </h4>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Jenjang Pendidikan</label>
                                                <input type="text" name="last_education" placeholder="Contoh: S1 / S2 / SMA"
                                                    value="{{ old('last_education', $employee->last_education) }}"
                                                    class="w-full text-xs h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                                @error('last_education') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Jurusan / Program Studi</label>
                                                <input type="text" name="major" placeholder="Contoh: Pendidikan Bahasa Inggris"
                                                    value="{{ old('major', $employee->major) }}"
                                                    class="w-full text-xs h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                                @error('major') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons Modal -->
                                    <div class="mt-8 flex items-center justify-end gap-x-3 pt-5 border-t border-slate-200 dark:border-slate-800">
                                        <button type="button"
                                            :disabled="isSaving"
                                            @click="if('{{ $errors->any() }}') { window.location.href = window.location.pathname; } else { showEditModal = false; photoPreview = null; $refs.profileForm.reset(); }"
                                            class="inline-flex justify-center rounded-xl px-4 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                                            Batal
                                        </button>
                                        <button type="submit"
                                            :disabled="isSaving"
                                            class="inline-flex items-center gap-2 justify-center rounded-xl bg-slate-900 dark:bg-slate-100 px-6 py-2.5 text-xs font-semibold text-white dark:text-slate-900 shadow-sm hover:bg-slate-800 dark:hover:bg-slate-200 transition-all cursor-pointer disabled:opacity-75 disabled:cursor-not-allowed">
                                            <i x-show="isSaving" data-lucide="loader-2" class="w-3.5 h-3.5 animate-spin" style="display: none;"></i>
                                            <span x-text="isSaving ? 'Menyimpan...' : 'Simpan Perubahan'">Simpan Perubahan</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-admin-layout>