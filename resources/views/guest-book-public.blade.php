<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Buku Tamu & Kunjungan - {{ setting('unit_name', setting('app_name', 'SD Anak Saleh Malang')) }}</title>
    
    <!-- Tailwind CSS (Vite / CDN Fallback for Public Standalone) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Alpine.js Component Definition (Loaded before Alpine) -->
    <script>
        function kioskApp() {
            return {
                category: @json(old('category', 'Umum')),
                categoriesMap: @json($categories->pluck('form_type', 'name')),
                get currentFormType() {
                    if (this.categoriesMap && this.categoriesMap[this.category]) {
                        return this.categoriesMap[this.category];
                    }
                    if (this.category === 'SPMB') return 'spmb';
                    if (this.category === 'Dinas') return 'dinas';
                    if (['Puskesmas/Dinkes', 'Puskesmas', 'Dinkes'].includes(this.category)) return 'puskesmas';
                    if (['Offering/Marketing', 'Offering', 'Marketing'].includes(this.category)) return 'offering';
                    if (['Penelitian/Observasi', 'Penelitian', 'Observasi'].includes(this.category)) return 'penelitian';
                    if (this.category === 'Magang') return 'magang';
                    if (['Studi Banding/Studi Tiru', 'Studi Banding', 'Studi Tiru'].includes(this.category)) return 'studi_banding';
                    return 'umum';
                },
                therapy_history: @json(old('therapy_history', 'Tidak')),
                formData: {
                    name: @json(old('name', '')),
                    phone: @json(old('phone', '')),
                    student_name: @json(old('student_name', '')),
                    student_birth_date: @json(old('student_birth_date', '')),
                    therapy_diagnosis: @json(old('therapy_diagnosis', '')),
                    institution: @json(old('institution', '')),
                    position: @json(old('position', '')),
                    occupation: @json(old('occupation', '')),
                    city: @json(old('city', 'Kota Malang')),
                    province: @json(old('province', 'Jawa Timur')),
                    address: @json(old('address', '')),
                    purpose: @json(old('purpose', '')),
                    host_recipient: @json(old('host_recipient', '')),
                    feedback: @json(old('feedback', ''))
                },
                submitting: false
            };
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }
        body {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        /* Custom smooth scroll */
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-teal-50/30 to-indigo-50/40 min-h-screen text-slate-800 antialiased flex flex-col justify-between py-4 sm:py-8 px-3 sm:px-6 relative overflow-x-hidden">

    <!-- Ambient Glow Background Blobs (Modern Backdrop) -->
    <div class="fixed top-0 left-1/4 -translate-x-1/2 w-96 h-96 bg-teal-200/30 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="fixed bottom-0 right-1/4 translate-x-1/2 w-96 h-96 bg-indigo-200/25 rounded-full blur-3xl pointer-events-none -z-10"></div>

    <!-- Top Navigation / Brand Bar -->
    <header class="max-w-3xl w-full mx-auto flex items-center justify-between pb-3 sm:pb-5">
        <div class="flex items-center gap-2.5 sm:gap-3.5">
            @if(setting('app_logo'))
                <img src="{{ asset('storage/' . setting('app_logo')) }}" alt="Logo" class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl object-cover shadow-sm ring-1 ring-slate-200/60">
            @else
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-gradient-to-br from-teal-600 to-emerald-600 text-white font-bold flex items-center justify-center shadow-md">
                    <i data-lucide="sparkles" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                </div>
            @endif
            <div>
                <h1 class="font-extrabold text-sm sm:text-base text-slate-900 leading-tight">
                    {{ setting('unit_name', setting('app_name', 'SD Anak Saleh Malang')) }}
                </h1>
                <p class="text-[11px] sm:text-xs text-teal-700 dark:text-teal-600 font-semibold tracking-wide">
                    Buku Tamu & Kunjungan
                </p>
            </div>
        </div>

        <!-- Live Clock & Date Badge (Kiosk Experience) -->
        <div class="text-right flex items-center gap-2 px-3 py-1.5 bg-white/80 backdrop-blur-xs border border-slate-200/80 rounded-xl shadow-xs">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <div class="text-left sm:text-right">
                <div class="text-[11px] font-bold text-slate-800 leading-none" id="kiosk-live-time">
                    {{ \Carbon\Carbon::now()->format('H:i') }} WIB
                </div>
                <div class="text-[10px] text-slate-500 font-medium leading-none mt-0.5 hidden xs:block">
                    {{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-3xl w-full mx-auto my-auto">
        <!-- SUCCESS BANNER WITH AUTO-COUNTDOWN RESET FOR KIOSK -->
        @if(session('success_public'))
            <div x-data="{ countdown: 10, interval: null }"
                x-init="interval = setInterval(() => { countdown--; if(countdown <= 0) { clearInterval(interval); window.location.href = '{{ route('buku-tamu.public') }}'; } }, 1000)"
                class="mb-5 sm:mb-6 bg-white border border-emerald-200/90 text-emerald-950 p-5 sm:p-7 rounded-2xl sm:rounded-3xl shadow-xl shadow-emerald-900/5 flex flex-col sm:flex-row items-center gap-4 text-center sm:text-left relative overflow-hidden">
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-lg shadow-emerald-500/30">
                    <i data-lucide="check-check" class="w-8 h-8 stroke-[2.5]"></i>
                </div>
                <div class="flex-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 mb-1">
                        Pencatatan Berhasil
                    </span>
                    <h3 class="font-extrabold text-base sm:text-lg text-slate-900">Alhamdulillah! Data Berhasil Disimpan</h3>
                    <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                        {{ session('success_public') }}
                    </p>
                    <p class="text-[11px] text-emerald-700 font-medium mt-2 flex items-center justify-center sm:justify-start gap-1">
                        <i data-lucide="refresh-cw" class="w-3 h-3 animate-spin"></i>
                        <span>Formulir akan otomatis diperbarui dalam <strong x-text="countdown">10</strong> detik</span>
                    </p>
                </div>
                <a href="{{ route('buku-tamu.public') }}"
                    class="w-full sm:w-auto px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 transition-all whitespace-nowrap active:scale-95">
                    Catat Tamu Baru Sekarang
                </a>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div id="validation-errors-box" class="mb-5 bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl shadow-xs text-xs">
                <div class="flex items-center gap-2 font-bold mb-1">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 shrink-0"></i>
                    <span>Mohon periksa kembali formulir Anda:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 ml-2 text-rose-700">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Card Container -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-xl shadow-slate-200/50 overflow-hidden">
            <!-- Card Header: Hero Greeting (No "Formulir Kunjungan Tamu" badge, per request) -->
            <div class="bg-gradient-to-br from-teal-700 via-teal-800 to-emerald-800 p-5 sm:p-7 text-white text-left relative overflow-hidden">
                <!-- Decorative subtle background shapes -->
                <div class="absolute -right-8 -bottom-8 w-36 h-36 bg-white/5 rounded-full blur-xl pointer-events-none"></div>
                <div class="absolute right-12 top-2 w-20 h-20 bg-emerald-400/10 rounded-full blur-lg pointer-events-none"></div>

                <div class="relative z-10">
                    <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight">Selamat Datang di Lembaga Kami</h2>
                    <p class="text-xs sm:text-sm text-teal-100/90 mt-1 leading-relaxed max-w-xl">
                        Silakan mengisi buku tamu untuk kelancaran pelayanan dan pencatatan agenda kunjungan Anda.
                    </p>
                </div>
            </div>

            <!-- Form Body with Alpine.js -->
            <form action="{{ route('buku-tamu.submit') }}" method="POST"
                x-data="kioskApp()"
                @submit="submitting = true"
                class="p-4 sm:p-7 space-y-5 sm:space-y-6 text-left">
                @csrf

                <!-- ========================================================================= -->
                <!-- 1. KATEGORI LAYANAN SELECTION (RESPONSIVE CARDS)                           -->
                <!-- ========================================================================= -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700 tracking-wide">
                        Pilih Kategori Kunjungan <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3">
                        @foreach($categories as $cat)
                            @php
                                $catName = is_object($cat) ? $cat->name : $cat;
                                $iconName = is_object($cat) ? ($cat->icon ?: 'briefcase') : match($cat) {
                                    'SPMB' => 'user-plus',
                                    'Dinas' => 'landmark',
                                    'Puskesmas/Dinkes' => 'activity',
                                    'Offering/Marketing' => 'briefcase',
                                    'Penelitian/Observasi' => 'graduation-cap',
                                    'Magang' => 'user-check',
                                    'Studi Banding/Studi Tiru' => 'building-2',
                                    default => 'users',
                                };
                                $catSub = is_object($cat) ? ($cat->sub_label ?: $cat->name) : match($cat) {
                                    'SPMB' => 'Calon Murid',
                                    'Dinas' => 'Kedinasan',
                                    'Puskesmas/Dinkes' => 'Puskesmas',
                                    'Offering/Marketing' => 'Vendor / Penawaran',
                                    'Penelitian/Observasi' => 'Riset & Observasi',
                                    'Magang' => 'PKL / PPL / Magang',
                                    'Studi Banding/Studi Tiru' => 'Studi Tiru',
                                    default => 'Umum / Mitra',
                                };
                                $colorStyle = is_object($cat) ? $cat->text_color : match($cat) {
                                    'SPMB' => 'text-amber-600',
                                    'Dinas' => 'text-indigo-600',
                                    'Puskesmas/Dinkes' => 'text-emerald-600',
                                    'Offering/Marketing' => 'text-violet-600',
                                    'Penelitian/Observasi' => 'text-cyan-600',
                                    'Magang' => 'text-rose-600',
                                    'Studi Banding/Studi Tiru' => 'text-sky-600',
                                    default => 'text-teal-600',
                                };
                                $borderActive = is_object($cat) ? $cat->border_active_class : 'border-teal-600 bg-teal-50/70 shadow-sm ring-2 ring-teal-500/20';
                                $isSelected = old('category', 'Umum') === $catName;
                            @endphp
                            <label class="cursor-pointer relative flex flex-col items-center text-center p-3 sm:p-3.5 rounded-xl sm:rounded-2xl border transition-all duration-150 select-none active:scale-[0.98]"
                                :class="category === '{{ $catName }}' 
                                    ? '{{ $borderActive }}' 
                                    : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/80'">
                                <input type="radio" name="category" value="{{ $catName }}" x-model="category" {{ $isSelected ? 'checked' : '' }} class="sr-only">
                                
                                <!-- Active Check Badge -->
                                <span x-show="category === '{{ $catName }}'" x-cloak
                                    class="absolute top-2 right-2 w-4 h-4 rounded-full bg-teal-600 text-white flex items-center justify-center shadow-xs">
                                    <i data-lucide="check" class="w-2.5 h-2.5 stroke-[3]"></i>
                                </span>

                                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center mb-1.5 transition-colors"
                                    :class="category === '{{ $catName }}' ? 'bg-white shadow-xs {{ $colorStyle }}' : 'bg-slate-100 text-slate-500'">
                                    <i data-lucide="{{ $iconName }}" class="w-5 h-5"></i>
                                </div>
                                <span class="text-xs font-bold text-slate-900 truncate max-w-full" title="{{ $catName }}">{{ $catName }}</span>
                                <span class="text-[10px] text-slate-400 font-medium mt-0.5 truncate max-w-full">{{ $catSub }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- ========================================================================= -->
                <!-- 2. TANGGAL & WAKTU (KOMPAK & OTOMATIS)                                     -->
                <!-- ========================================================================= -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-3 p-3 sm:p-3.5 bg-slate-50/80 border border-slate-200/80 rounded-xl sm:rounded-2xl">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                            <i data-lucide="calendar" class="w-3.5 h-3.5 text-teal-600"></i>
                            <span>Tanggal Kunjungan</span>
                        </label>
                        <input type="date" name="date" value="{{ old('date', \Carbon\Carbon::now()->format('Y-m-d')) }}" required
                            class="w-full h-10 px-3 bg-white border {{ ($errors ?? null)?->has('date') ? 'border-rose-500 ring-1 ring-rose-500' : 'border-slate-200' }} rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                        @error('date')
                            <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                            <i data-lucide="clock" class="w-3.5 h-3.5 text-teal-600"></i>
                            <span>Waktu / Jam Datang</span>
                        </label>
                        <input type="time" name="time" value="{{ old('time', \Carbon\Carbon::now()->format('H:i')) }}"
                            class="w-full h-10 px-3 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                    </div>
                </div>

                <!-- ========================================================================= -->
                <!-- 3. DETAIL FORM PER KATEGORI                                               -->
                <!-- ========================================================================= -->

                <!-- FORM 1: SPMB (CALON WALI MURID) -->
                <div x-show="currentFormType === 'spmb'" style="{{ old('category', 'Umum') === 'SPMB' ? '' : 'display: none;' }}" class="space-y-4 pt-1">
                    <div class="border-b border-slate-100 pb-2">
                        <h3 class="text-xs sm:text-sm font-bold text-slate-800">Data Orang Tua & Calon Murid</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Nama Ortu (Orang Tua / Wali) <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" x-model="formData.name" value="{{ old('name') }}"
                                {{ old('category', 'Umum') !== 'SPMB' ? 'disabled' : '' }}
                                :disabled="currentFormType !== 'spmb'"
                                :required="currentFormType === 'spmb'"
                                placeholder="Contoh: Kiki Gustinasari"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border {{ ($errors ?? null)?->has('name') ? 'border-rose-500 ring-1 ring-rose-500 bg-rose-50/20' : 'border-slate-200' }} rounded-xl text-xs sm:text-sm font-semibold text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                            @error('name')
                                <p class="text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                                    <i data-lucide="alert-circle" class="w-3 h-3 shrink-0"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                No. Handphone / WhatsApp <span class="text-rose-500">*</span>
                            </label>
                            <input type="tel" name="phone" x-model="formData.phone" value="{{ old('phone') }}" inputmode="tel"
                                {{ old('category', 'Umum') !== 'SPMB' ? 'disabled' : '' }}
                                :disabled="currentFormType !== 'spmb'"
                                :required="currentFormType === 'spmb'"
                                placeholder="Contoh: 082232370123"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border {{ ($errors ?? null)?->has('phone') ? 'border-rose-500 ring-1 ring-rose-500 bg-rose-50/20' : 'border-slate-200' }} rounded-xl text-xs sm:text-sm font-semibold text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all font-mono">
                            @error('phone')
                                <p class="text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                                    <i data-lucide="alert-circle" class="w-3 h-3 shrink-0"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Nama Calon Murid <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="student_name" x-model="formData.student_name" value="{{ old('student_name') }}"
                                {{ old('category', 'Umum') !== 'SPMB' ? 'disabled' : '' }}
                                :disabled="currentFormType !== 'spmb'"
                                :required="currentFormType === 'spmb'"
                                placeholder="Contoh: Muhammad Ihsan Ammando Saqi"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border {{ ($errors ?? null)?->has('student_name') ? 'border-rose-500 ring-1 ring-rose-500 bg-rose-50/20' : 'border-slate-200' }} rounded-xl text-xs sm:text-sm font-semibold text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                            @error('student_name')
                                <p class="text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                                    <i data-lucide="alert-circle" class="w-3 h-3 shrink-0"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Tanggal Lahir Calon Murid
                            </label>
                            <input type="date" name="student_birth_date" x-model="formData.student_birth_date" value="{{ old('student_birth_date') }}"
                                {{ old('category', 'Umum') !== 'SPMB' ? 'disabled' : '' }}
                                :disabled="currentFormType !== 'spmb'"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border {{ ($errors ?? null)?->has('student_birth_date') ? 'border-rose-500 ring-1 ring-rose-500' : 'border-slate-200' }} rounded-xl text-xs sm:text-sm font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                            @error('student_birth_date')
                                <p class="text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                                    <i data-lucide="alert-circle" class="w-3 h-3 shrink-0"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Riwayat Terapi (Interactive Modern Toggle) -->
                    <div class="p-3 sm:p-4 bg-slate-50/70 border border-slate-200/80 rounded-xl sm:rounded-2xl space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Apakah Anak Pernah Menjalani Terapi?
                            </label>
                            <div class="grid grid-cols-2 gap-2 max-w-xs">
                                <button type="button" @click="therapy_history = 'Tidak'"
                                    :class="therapy_history === 'Tidak' ? 'bg-teal-600 text-white shadow-xs font-bold' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 font-semibold'"
                                    class="h-9 px-3 text-xs rounded-lg transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                                    <i data-lucide="x-circle" class="w-3.5 h-3.5" :class="therapy_history === 'Tidak' ? 'text-white' : 'text-slate-400'"></i>
                                    <span>Tidak</span>
                                </button>
                                <button type="button" @click="therapy_history = 'Ya'"
                                    :class="therapy_history === 'Ya' ? 'bg-teal-600 text-white shadow-xs font-bold' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 font-semibold'"
                                    class="h-9 px-3 text-xs rounded-lg transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                                    <i data-lucide="check-circle" class="w-3.5 h-3.5" :class="therapy_history === 'Ya' ? 'text-white' : 'text-slate-400'"></i>
                                    <span>Ya, Pernah</span>
                                </button>
                            </div>
                            <input type="hidden" name="therapy_history" :value="therapy_history" value="{{ old('therapy_history', 'Tidak') }}" {{ old('category', 'Umum') !== 'SPMB' ? 'disabled' : '' }} :disabled="currentFormType !== 'spmb'">
                        </div>

                        <!-- Smooth reveal when Ya -->
                        <div x-show="therapy_history === 'Ya'" style="{{ old('therapy_history', 'Tidak') === 'Ya' ? '' : 'display: none;' }}" class="pt-2 border-t border-slate-200/60">
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Diagnosa Terapi <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="therapy_diagnosis" x-model="formData.therapy_diagnosis" value="{{ old('therapy_diagnosis') }}"
                                {{ (old('category', 'Umum') !== 'SPMB' || old('therapy_history', 'Tidak') !== 'Ya') ? 'disabled' : '' }}
                                :disabled="currentFormType !== 'spmb' || therapy_history !== 'Ya'"
                                :required="currentFormType === 'spmb' && therapy_history === 'Ya'"
                                placeholder="Contoh: Terapi Wicara / Speech delay, Okupasi, Sensori Integrasi"
                                class="w-full h-11 px-3.5 bg-white border {{ ($errors ?? null)?->has('therapy_diagnosis') ? 'border-rose-500 ring-1 ring-rose-500 bg-rose-50/20' : 'border-slate-200' }} rounded-xl text-xs sm:text-sm font-semibold text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                            @error('therapy_diagnosis')
                                <p class="text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                                    <i data-lucide="alert-circle" class="w-3 h-3 shrink-0"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Alamat Rumah
                        </label>
                        <input type="text" name="address" x-model="formData.address" value="{{ old('address') }}"
                            {{ old('category', 'Umum') !== 'SPMB' ? 'disabled' : '' }}
                            :disabled="currentFormType !== 'spmb'"
                            placeholder="Contoh: Perum La Tansa Cluster, A11, Merjosari, Lowokwaru"
                            class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Penerima Tamu / Yang Ditemui
                        </label>
                        <input type="text" name="host_recipient" x-model="formData.host_recipient" value="{{ old('host_recipient') }}"
                            {{ old('category', 'Umum') !== 'SPMB' ? 'disabled' : '' }}
                            :disabled="currentFormType !== 'spmb'"
                            placeholder="Contoh: Kepala Sekolah / Tim Panitia SPMB"
                            class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                    </div>
                </div>

                <!-- FORM 2: PUSKESMAS / DINKES -->
                <div x-show="currentFormType === 'puskesmas'" style="{{ old('category', 'Umum') === 'Puskesmas/Dinkes' ? '' : 'display: none;' }}" class="space-y-4 pt-1">
                    <div class="border-b border-slate-100 pb-2">
                        <h3 class="text-xs sm:text-sm font-bold text-slate-800">Identitas Petugas Kesehatan & Faskes</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Nama Petugas / Tamu <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" x-model="formData.name" value="{{ old('name') }}"
                                :disabled="currentFormType !== 'puskesmas'"
                                :required="currentFormType === 'puskesmas'"
                                placeholder="Contoh: Mariana DN"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border {{ ($errors ?? null)?->has('name') ? 'border-rose-500 ring-1 ring-rose-500 bg-rose-50/20' : 'border-slate-200' }} rounded-xl text-xs sm:text-sm font-semibold text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                            @error('name')
                                <p class="text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                                    <i data-lucide="alert-circle" class="w-3 h-3 shrink-0"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                No. Handphone / WhatsApp <span class="text-rose-500">*</span>
                            </label>
                            <input type="tel" name="phone" x-model="formData.phone" value="{{ old('phone') }}" inputmode="tel"
                                :disabled="currentFormType !== 'puskesmas'"
                                :required="currentFormType === 'puskesmas'"
                                placeholder="Contoh: 089601662496"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border {{ ($errors ?? null)?->has('phone') ? 'border-rose-500 ring-1 ring-rose-500 bg-rose-50/20' : 'border-slate-200' }} rounded-xl text-xs sm:text-sm font-semibold text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all font-mono">
                            @error('phone')
                                <p class="text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                                    <i data-lucide="alert-circle" class="w-3 h-3 shrink-0"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Institusi / Faskes</label>
                            <input type="text" name="institution" x-model="formData.institution" value="{{ old('institution') }}"
                                :disabled="currentFormType !== 'puskesmas'"
                                placeholder="Contoh: Puskesmas Mojolangu"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jabatan</label>
                            <input type="text" name="position" x-model="formData.position" value="{{ old('position') }}"
                                :disabled="currentFormType !== 'puskesmas'"
                                placeholder="Contoh: Bidan / Dokter / Perawat"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Pekerjaan</label>
                            <input type="text" name="occupation" x-model="formData.occupation" value="{{ old('occupation') }}"
                                :disabled="currentFormType !== 'puskesmas'"
                                placeholder="Contoh: PNS / Tenaga Medis"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Kegiatan Pelayanan Kesehatan <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="purpose" x-model="formData.purpose" rows="2"
                            :disabled="currentFormType !== 'puskesmas'"
                            :required="currentFormType === 'puskesmas'"
                            placeholder="Contoh: Screening DDTK, kusta prambusia, pemeriksaan gigi, mata, dan GDS anak"
                            class="w-full p-3.5 bg-slate-50/70 border {{ ($errors ?? null)?->has('purpose') ? 'border-rose-500 ring-1 ring-rose-500 bg-rose-50/20' : 'border-slate-200' }} rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 leading-relaxed transition-all">{{ old('purpose') }}</textarea>
                        @error('purpose')
                            <p class="text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-3 h-3 shrink-0"></i>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Penerima Tamu</label>
                        <input type="text" name="host_recipient" x-model="formData.host_recipient" value="{{ old('host_recipient') }}"
                            :disabled="currentFormType !== 'puskesmas'"
                            placeholder="Contoh: Bu Guru / Petugas UKS / Tata Usaha"
                            class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                    </div>
                </div>

                <!-- FORM 3: DINAS (KEDINASAN) -->
                <div x-show="currentFormType === 'dinas'" style="{{ old('category', 'Umum') === 'Dinas' ? '' : 'display: none;' }}" class="space-y-4 pt-1">
                    <div class="border-b border-slate-100 pb-2">
                        <h3 class="text-xs sm:text-sm font-bold text-slate-800">Identitas Pejabat Kedinasan</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Nama Pejabat / Tamu <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" x-model="formData.name" value="{{ old('name') }}"
                                :disabled="currentFormType !== 'dinas'"
                                :required="currentFormType === 'dinas'"
                                placeholder="Contoh: Drs. H. Achmad Fauzi, M.Si"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border {{ ($errors ?? null)?->has('name') ? 'border-rose-500 ring-1 ring-rose-500 bg-rose-50/20' : 'border-slate-200' }} rounded-xl text-xs sm:text-sm font-semibold text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                            @error('name')
                                <p class="text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                                    <i data-lucide="alert-circle" class="w-3 h-3 shrink-0"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                No. Handphone / WhatsApp <span class="text-rose-500">*</span>
                            </label>
                            <input type="tel" name="phone" x-model="formData.phone" value="{{ old('phone') }}" inputmode="tel"
                                :disabled="currentFormType !== 'dinas'"
                                :required="currentFormType === 'dinas'"
                                placeholder="Contoh: 081234567890"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border {{ ($errors ?? null)?->has('phone') ? 'border-rose-500 ring-1 ring-rose-500 bg-rose-50/20' : 'border-slate-200' }} rounded-xl text-xs sm:text-sm font-semibold text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all font-mono">
                            @error('phone')
                                <p class="text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                                    <i data-lucide="alert-circle" class="w-3 h-3 shrink-0"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Institusi Kedinasan</label>
                            <input type="text" name="institution" x-model="formData.institution" value="{{ old('institution') }}"
                                :disabled="currentFormType !== 'dinas'"
                                placeholder="Contoh: Dinas Pendidikan Kota Malang"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jabatan</label>
                            <input type="text" name="position" x-model="formData.position" value="{{ old('position') }}"
                                :disabled="currentFormType !== 'dinas'"
                                placeholder="Contoh: Pengawas Sekolah / Dinas Pendidikan"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Pekerjaan</label>
                            <input type="text" name="occupation" x-model="formData.occupation" value="{{ old('occupation') }}"
                                :disabled="currentFormType !== 'dinas'"
                                placeholder="Contoh: ASN / PNS"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Keperluan / Agenda Kedinasan <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="purpose" x-model="formData.purpose" rows="2"
                            :disabled="currentFormType !== 'dinas'"
                            :required="currentFormType === 'dinas'"
                            placeholder="Contoh: Monitoring dan Evaluasi BOSP / Supervisi"
                            class="w-full p-3.5 bg-slate-50/70 border {{ ($errors ?? null)?->has('purpose') ? 'border-rose-500 ring-1 ring-rose-500 bg-rose-50/20' : 'border-slate-200' }} rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 leading-relaxed transition-all">{{ old('purpose') }}</textarea>
                        @error('purpose')
                            <p class="text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-3 h-3 shrink-0"></i>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Penerima Tamu</label>
                        <input type="text" name="host_recipient" x-model="formData.host_recipient" value="{{ old('host_recipient') }}"
                            :disabled="currentFormType !== 'dinas'"
                            placeholder="Contoh: Kepala Sekolah"
                            class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kesan & Pesan / Saran <span class="text-slate-400 font-normal">(Opsional)</span></label>
                        <textarea name="feedback" x-model="formData.feedback" rows="2"
                            :disabled="currentFormType !== 'dinas'"
                            placeholder="Tuliskan kesan atau catatan tindak lanjut..."
                            class="w-full p-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 leading-relaxed transition-all">{{ old('feedback') }}</textarea>
                    </div>
                </div>

                <!-- FORM 4: OFFERING / MARKETING (VENDOR & PENAWARAN) -->
                <div x-show="currentFormType === 'offering'" style="{{ old('category', 'Umum') === 'Offering/Marketing' ? '' : 'display: none;' }}" class="space-y-4 pt-1">
                    <div class="border-b border-slate-100 pb-2">
                        <h3 class="text-xs sm:text-sm font-bold text-slate-800">Identitas Perusahaan / Vendor & Penawaran</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Nama Sales / Marketing / Tamu <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" x-model="formData.name" value="{{ old('name') }}"
                                :disabled="currentFormType !== 'offering'"
                                :required="currentFormType === 'offering'"
                                placeholder="Contoh: Rian Pratama"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                No. Handphone / WhatsApp <span class="text-rose-500">*</span>
                            </label>
                            <input type="tel" name="phone" x-model="formData.phone" value="{{ old('phone') }}" inputmode="tel"
                                :disabled="currentFormType !== 'offering'"
                                :required="currentFormType === 'offering'"
                                placeholder="Contoh: 081234567890"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all font-mono">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Perusahaan / Vendor <span class="text-rose-500">*</span></label>
                            <input type="text" name="institution" x-model="formData.institution" value="{{ old('institution') }}"
                                :disabled="currentFormType !== 'offering'"
                                placeholder="Contoh: CV. Bintang Edukasi / Penerbit Erlangga"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jabatan / Posisi</label>
                            <input type="text" name="position" x-model="formData.position" value="{{ old('position') }}"
                                :disabled="currentFormType !== 'offering'"
                                placeholder="Contoh: Sales Executive / Marketing"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Bidang Usaha</label>
                            <input type="text" name="occupation" x-model="formData.occupation" value="{{ old('occupation') }}"
                                :disabled="currentFormType !== 'offering'"
                                placeholder="Contoh: Pengadaan Buku / IT / Seragam"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Produk / Layanan yang Ditawarkan <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="purpose" x-model="formData.purpose" rows="2"
                            :disabled="currentFormType !== 'offering'"
                            :required="currentFormType === 'offering'"
                            placeholder="Contoh: Penawaran buku pendamping kurikulum merdeka & sampel buku ajar"
                            class="w-full p-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 leading-relaxed transition-all">{{ old('purpose') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Penerima Tamu / Yang Ditemui</label>
                        <input type="text" name="host_recipient" x-model="formData.host_recipient" value="{{ old('host_recipient') }}"
                            :disabled="currentFormType !== 'offering'"
                            placeholder="Contoh: Waka Sarpras / Koordinator Kurikulum / Kepala Sekolah"
                            class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tindak Lanjut / Kesan <span class="text-slate-400 font-normal">(Opsional)</span></label>
                        <textarea name="feedback" x-model="formData.feedback" rows="2"
                            :disabled="currentFormType !== 'offering'"
                            placeholder="Contoh: Menunggu konfirmasi presentasi produk lanjutan"
                            class="w-full p-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 leading-relaxed transition-all">{{ old('feedback') }}</textarea>
                    </div>
                </div>

                <!-- FORM 5: PENELITIAN / OBSERVASI -->
                <div x-show="currentFormType === 'penelitian'" style="{{ old('category', 'Umum') === 'Penelitian/Observasi' ? '' : 'display: none;' }}" class="space-y-4 pt-1">
                    <div class="border-b border-slate-100 pb-2">
                        <h3 class="text-xs sm:text-sm font-bold text-slate-800">Identitas Peneliti & Fokus Riset / Observasi</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Nama Peneliti / Tamu <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" x-model="formData.name" value="{{ old('name') }}"
                                :disabled="currentFormType !== 'penelitian'"
                                :required="currentFormType === 'penelitian'"
                                placeholder="Contoh: Nurul Hidayati, S.Pd"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                No. Handphone / WhatsApp <span class="text-rose-500">*</span>
                            </label>
                            <input type="tel" name="phone" x-model="formData.phone" value="{{ old('phone') }}" inputmode="tel"
                                :disabled="currentFormType !== 'penelitian'"
                                :required="currentFormType === 'penelitian'"
                                placeholder="Contoh: 085712345678"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all font-mono">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Asal Kampus / Lembaga <span class="text-rose-500">*</span></label>
                            <input type="text" name="institution" x-model="formData.institution" value="{{ old('institution') }}"
                                :disabled="currentFormType !== 'penelitian'"
                                placeholder="Contoh: Universitas Negeri Malang (UM)"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jurusan / Program Studi</label>
                            <input type="text" name="position" x-model="formData.position" value="{{ old('position') }}"
                                :disabled="currentFormType !== 'penelitian'"
                                placeholder="Contoh: Pendidikan Matematika / PGSD"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Status Peneliti</label>
                            <input type="text" name="occupation" x-model="formData.occupation" value="{{ old('occupation') }}"
                                :disabled="currentFormType !== 'penelitian'"
                                placeholder="Contoh: Mahasiswa S1 / Mahasiswa S2 / Dosen"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Judul / Topik Penelitian & Observasi <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="purpose" x-model="formData.purpose" rows="2"
                            :disabled="currentFormType !== 'penelitian'"
                            :required="currentFormType === 'penelitian'"
                            placeholder="Contoh: Analisis implementasi Panca Karakter dalam pembelajaran di SD Anak Saleh"
                            class="w-full p-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 leading-relaxed transition-all">{{ old('purpose') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Guru Pamong / Dosen / Penerima Tamu</label>
                        <input type="text" name="host_recipient" x-model="formData.host_recipient" value="{{ old('host_recipient') }}"
                            :disabled="currentFormType !== 'penelitian'"
                            placeholder="Contoh: Guru Pamong / Waka Kurikulum"
                            class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kesan & Pesan / Saran <span class="text-slate-400 font-normal">(Opsional)</span></label>
                        <textarea name="feedback" x-model="formData.feedback" rows="2"
                            :disabled="currentFormType !== 'penelitian'"
                            placeholder="Kesan terhadap proses observasi dan pembelajaran..."
                            class="w-full p-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 leading-relaxed transition-all">{{ old('feedback') }}</textarea>
                    </div>
                </div>

                <!-- FORM 6: MAGANG (PKL / PPL / PLP) -->
                <div x-show="currentFormType === 'magang'" style="{{ old('category', 'Umum') === 'Magang' ? '' : 'display: none;' }}" class="space-y-4 pt-1">
                    <div class="border-b border-slate-100 pb-2">
                        <h3 class="text-xs sm:text-sm font-bold text-slate-800">Identitas Siswa / Mahasiswa Magang (PKL / PPL)</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Nama Mahasiswa / Siswa Magang <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" x-model="formData.name" value="{{ old('name') }}"
                                :disabled="currentFormType !== 'magang'"
                                :required="currentFormType === 'magang'"
                                placeholder="Contoh: Ahmad Rizki Maulana"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                No. Handphone / WhatsApp <span class="text-rose-500">*</span>
                            </label>
                            <input type="tel" name="phone" x-model="formData.phone" value="{{ old('phone') }}" inputmode="tel"
                                :disabled="currentFormType !== 'magang'"
                                :required="currentFormType === 'magang'"
                                placeholder="Contoh: 081987654321"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all font-mono">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Asal Kampus / Sekolah <span class="text-rose-500">*</span></label>
                            <input type="text" name="institution" x-model="formData.institution" value="{{ old('institution') }}"
                                :disabled="currentFormType !== 'magang'"
                                placeholder="Contoh: UIN Maulana Malik Ibrahim Malang"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jurusan / Program Studi</label>
                            <input type="text" name="position" x-model="formData.position" value="{{ old('position') }}"
                                :disabled="currentFormType !== 'magang'"
                                placeholder="Contoh: Pendidikan Agama Islam / Bimbingan Konseling"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jenjang / Kategori</label>
                            <input type="text" name="occupation" x-model="formData.occupation" value="{{ old('occupation') }}"
                                :disabled="currentFormType !== 'magang'"
                                placeholder="Contoh: PPL Mahasiswa / PKL SMK"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Keperluan / Periode & Agenda Magang <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="purpose" x-model="formData.purpose" rows="2"
                            :disabled="currentFormType !== 'magang'"
                            :required="currentFormType === 'magang'"
                            placeholder="Contoh: Penyerahan mahasiswa PPL semester gasal / Laporan kegiatan harian"
                            class="w-full p-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 leading-relaxed transition-all">{{ old('purpose') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Guru Pamong / Pembimbing Lapangan</label>
                        <input type="text" name="host_recipient" x-model="formData.host_recipient" value="{{ old('host_recipient') }}"
                            :disabled="currentFormType !== 'magang'"
                            placeholder="Contoh: Koordinator Guru Pamong / Waka Humas"
                            class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                    </div>
                </div>

                <!-- FORM 7: STUDI BANDING / STUDI TIRU -->
                <div x-show="currentFormType === 'studi_banding'" style="{{ old('category', 'Umum') === 'Studi Banding/Studi Tiru' ? '' : 'display: none;' }}" class="space-y-4 pt-1">
                    <div class="border-b border-slate-100 pb-2">
                        <h3 class="text-xs sm:text-sm font-bold text-slate-800">Identitas Rombongan Studi Tiru / Studi Banding</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Nama Ketua / Pimpinan Rombongan <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" x-model="formData.name" value="{{ old('name') }}"
                                :disabled="currentFormType !== 'studi_banding'"
                                :required="currentFormType === 'studi_banding'"
                                placeholder="Contoh: Dr. H. Subagyo, M.Pd"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                No. Handphone / WhatsApp <span class="text-rose-500">*</span>
                            </label>
                            <input type="tel" name="phone" x-model="formData.phone" value="{{ old('phone') }}" inputmode="tel"
                                :disabled="currentFormType !== 'studi_banding'"
                                :required="currentFormType === 'studi_banding'"
                                placeholder="Contoh: 081223344556"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all font-mono">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Asal Sekolah / Lembaga / Yayasan <span class="text-rose-500">*</span></label>
                            <input type="text" name="institution" x-model="formData.institution" value="{{ old('institution') }}"
                                :disabled="currentFormType !== 'studi_banding'"
                                placeholder="Contoh: SD Islam Al-Azhar Surabaya"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jabatan Pimpinan</label>
                            <input type="text" name="position" x-model="formData.position" value="{{ old('position') }}"
                                :disabled="currentFormType !== 'studi_banding'"
                                placeholder="Contoh: Kepala Sekolah / Ketua Yayasan"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah Rombongan</label>
                            <input type="text" name="occupation" x-model="formData.occupation" value="{{ old('occupation') }}"
                                :disabled="currentFormType !== 'studi_banding'"
                                placeholder="Contoh: 12 Orang Guru & Staff"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Fokus & Topik Studi Tiru / Keperluan <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="purpose" x-model="formData.purpose" rows="2"
                            :disabled="currentFormType !== 'studi_banding'"
                            :required="currentFormType === 'studi_banding'"
                            placeholder="Contoh: Studi tiru manajemen kurikulum digital, sistem penjaminan mutu, dan Panca Karakter"
                            class="w-full p-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 leading-relaxed transition-all">{{ old('purpose') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Penerima Tamu</label>
                        <input type="text" name="host_recipient" x-model="formData.host_recipient" value="{{ old('host_recipient') }}"
                            :disabled="currentFormType !== 'studi_banding'"
                            placeholder="Contoh: Kepala Sekolah & Tim Manajemen SD"
                            class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kesan & Pesan Rombongan <span class="text-slate-400 font-normal">(Opsional)</span></label>
                        <textarea name="feedback" x-model="formData.feedback" rows="2"
                            :disabled="currentFormType !== 'studi_banding'"
                            placeholder="Tuliskan kesan atau apresiasi rombongan..."
                            class="w-full p-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 leading-relaxed transition-all">{{ old('feedback') }}</textarea>
                    </div>
                </div>

                <!-- FORM 8: UMUM (TAMU UMUM / MITRA / LAINNYA) -->
                <div x-show="currentFormType === 'umum' || !['spmb', 'puskesmas', 'dinas', 'offering', 'penelitian', 'magang', 'studi_banding'].includes(currentFormType)" style="{{ (old('category', 'Umum') === 'Umum' || !in_array(old('category', 'Umum'), ['SPMB', 'Puskesmas/Dinkes', 'Dinas', 'Offering/Marketing', 'Penelitian/Observasi', 'Magang', 'Studi Banding/Studi Tiru'])) ? '' : 'display: none;' }}" class="space-y-4 pt-1">
                    <div class="border-b border-slate-100 pb-2">
                        <h3 class="text-xs sm:text-sm font-bold text-slate-800">Identitas Diri & Asal Instansi</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Nama Lengkap Tamu <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" x-model="formData.name" value="{{ old('name') }}"
                                :disabled="currentFormType !== 'umum' && ['spmb', 'puskesmas', 'dinas', 'offering', 'penelitian', 'magang', 'studi_banding'].includes(currentFormType)"
                                :required="currentFormType === 'umum' || !['spmb', 'puskesmas', 'dinas', 'offering', 'penelitian', 'magang', 'studi_banding'].includes(currentFormType)"
                                placeholder="Contoh: Mohammad Choirul Anam"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border {{ ($errors ?? null)?->has('name') ? 'border-rose-500 ring-1 ring-rose-500 bg-rose-50/20' : 'border-slate-200' }} rounded-xl text-xs sm:text-sm font-semibold text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                            @error('name')
                                <p class="text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                                    <i data-lucide="alert-circle" class="w-3 h-3 shrink-0"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                No. Handphone / WhatsApp <span class="text-rose-500">*</span>
                            </label>
                            <input type="tel" name="phone" x-model="formData.phone" value="{{ old('phone') }}" inputmode="tel"
                                :disabled="currentFormType !== 'umum' && ['spmb', 'puskesmas', 'dinas', 'offering', 'penelitian', 'magang', 'studi_banding'].includes(currentFormType)"
                                :required="currentFormType === 'umum' || !['spmb', 'puskesmas', 'dinas', 'offering', 'penelitian', 'magang', 'studi_banding'].includes(currentFormType)"
                                placeholder="Contoh: 0895613444125"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border {{ ($errors ?? null)?->has('phone') ? 'border-rose-500 ring-1 ring-rose-500 bg-rose-50/20' : 'border-slate-200' }} rounded-xl text-xs sm:text-sm font-semibold text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all font-mono">
                            @error('phone')
                                <p class="text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                                    <i data-lucide="alert-circle" class="w-3 h-3 shrink-0"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Institusi / Lembaga</label>
                            <input type="text" name="institution" x-model="formData.institution" value="{{ old('institution') }}"
                                :disabled="currentFormType !== 'umum' && ['spmb', 'puskesmas', 'dinas', 'offering', 'penelitian', 'magang', 'studi_banding'].includes(currentFormType)"
                                placeholder="Contoh: MI Khadijah Malang"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jabatan</label>
                            <input type="text" name="position" x-model="formData.position" value="{{ old('position') }}"
                                :disabled="currentFormType !== 'umum' && ['spmb', 'puskesmas', 'dinas', 'offering', 'penelitian', 'magang', 'studi_banding'].includes(currentFormType)"
                                placeholder="Contoh: Guru / Staff"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Pekerjaan</label>
                            <input type="text" name="occupation" x-model="formData.occupation" value="{{ old('occupation') }}"
                                :disabled="currentFormType !== 'umum' && ['spmb', 'puskesmas', 'dinas', 'offering', 'penelitian', 'magang', 'studi_banding'].includes(currentFormType)"
                                placeholder="Contoh: Pendidik / Wiraswasta"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kota / Kabupaten</label>
                            <input type="text" name="city" x-model="formData.city" value="{{ old('city', 'Kota Malang') }}"
                                :disabled="currentFormType !== 'umum' && ['spmb', 'puskesmas', 'dinas', 'offering', 'penelitian', 'magang', 'studi_banding'].includes(currentFormType)"
                                placeholder="Contoh: Kota Malang"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Provinsi</label>
                            <input type="text" name="province" x-model="formData.province" value="{{ old('province', 'Jawa Timur') }}"
                                :disabled="currentFormType !== 'umum' && ['spmb', 'puskesmas', 'dinas', 'offering', 'penelitian', 'magang', 'studi_banding'].includes(currentFormType)"
                                placeholder="Contoh: Jawa Timur"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Alamat</label>
                            <input type="text" name="address" x-model="formData.address" value="{{ old('address') }}"
                                :disabled="currentFormType !== 'umum' && ['spmb', 'puskesmas', 'dinas', 'offering', 'penelitian', 'magang', 'studi_banding'].includes(currentFormType)"
                                placeholder="Contoh: Jl. Arjuno No. 12"
                                class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Keperluan Kunjungan <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="purpose" x-model="formData.purpose" rows="2"
                            :disabled="currentFormType !== 'umum' && ['spmb', 'puskesmas', 'dinas', 'offering', 'penelitian', 'magang', 'studi_banding'].includes(currentFormType)"
                            :required="currentFormType === 'umum' || !['spmb', 'puskesmas', 'dinas', 'offering', 'penelitian', 'magang', 'studi_banding'].includes(currentFormType)"
                            placeholder="Contoh: Sosialisasi program, brosur lomba, kunjungan silaturahmi"
                            class="w-full p-3.5 bg-slate-50/70 border {{ ($errors ?? null)?->has('purpose') ? 'border-rose-500 ring-1 ring-rose-500 bg-rose-50/20' : 'border-slate-200' }} rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 leading-relaxed transition-all">{{ old('purpose') }}</textarea>
                        @error('purpose')
                            <p class="text-rose-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-3 h-3 shrink-0"></i>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Penerima Tamu</label>
                        <input type="text" name="host_recipient" x-model="formData.host_recipient" value="{{ old('host_recipient') }}"
                            :disabled="currentFormType !== 'umum' && ['spmb', 'puskesmas', 'dinas', 'offering', 'penelitian', 'magang', 'studi_banding'].includes(currentFormType)"
                            placeholder="Contoh: Admin / Staff Tata Usaha"
                            class="w-full h-11 px-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kesan & Pesan / Saran <span class="text-slate-400 font-normal">(Opsional)</span></label>
                        <textarea name="feedback" x-model="formData.feedback" rows="2"
                            :disabled="currentFormType !== 'umum' && ['spmb', 'puskesmas', 'dinas', 'offering', 'penelitian', 'magang', 'studi_banding'].includes(currentFormType)"
                            placeholder="Contoh: Pelayanan ramah, tempat bersih dan nyaman"
                            class="w-full p-3.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 placeholder:font-normal focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 leading-relaxed transition-all">{{ old('feedback') }}</textarea>
                    </div>
                </div>

                <!-- ========================================================================= -->
                <!-- 4. SUBMIT ACTION BUTTON (LARGE & PROMINENT FOR KIOSK)                     -->
                <!-- ========================================================================= -->
                <div class="pt-4 sm:pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-end gap-3">
                    <button type="submit" :disabled="submitting"
                        class="w-full sm:w-auto px-8 sm:px-10 py-3.5 sm:py-4 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 active:scale-[0.98] text-white text-sm sm:text-base font-bold rounded-xl sm:rounded-2xl shadow-lg shadow-teal-600/25 transition-all flex items-center justify-center gap-2.5 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed">
                        <span x-show="!submitting" class="inline-flex items-center gap-2">
                            <i data-lucide="send" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                            <span>Kirim & Catat Kunjungan</span>
                        </span>
                        <span x-show="submitting" x-cloak class="inline-flex items-center gap-2">
                            <i data-lucide="loader-2" class="w-4 h-4 sm:w-5 sm:h-5 animate-spin"></i>
                            <span>Menyimpan Catatan...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="max-w-3xl w-full mx-auto text-center pt-5 sm:pt-7 text-[11px] sm:text-xs text-slate-400">
        <p>&copy; {{ date('Y') }} {{ setting('unit_name', setting('app_name', 'SD Anak Saleh Malang')) }}. Hak Cipta Dilindungi.</p>
    </footer>

    <!-- Kiosk Clock & Lucide Script -->
    <script>
        // Update Kiosk Clock every second
        function updateKioskClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const el = document.getElementById('kiosk-live-time');
            if (el) {
                el.textContent = `${hours}:${minutes} WIB`;
            }
        }
        setInterval(updateKioskClock, 1000);
        updateKioskClock();

        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
            const errorBox = document.getElementById('validation-errors-box');
            if (errorBox) {
                errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    </script>
</body>
</html>
