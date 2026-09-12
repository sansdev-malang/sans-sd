<x-admin-layout>
    <div class="p-6 space-y-6" x-data="{ activeTab: localStorage.getItem('sans_settings_tab') || 'general' }" x-init="$watch('activeTab', val => localStorage.setItem('sans_settings_tab', val))">

        <!-- PAGE HEADER -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Pengaturan Sistem</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Konfigurasi identitas sistem, informasi unit kerja, dan integrasi API eksternal.</p>
            </div>
        </section>

        <!-- MODERN TAB NAVIGATION -->
        <div class="border-b border-slate-200 dark:border-slate-800 flex items-center gap-2">
            <button type="button" @click="activeTab = 'general'" 
                :class="activeTab === 'general' ? 'border-slate-900 text-slate-900 dark:border-slate-100 dark:text-slate-100 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 hover:border-slate-300 font-semibold'"
                class="pb-3 px-4 text-xs border-b-2 flex items-center gap-2 transition-all cursor-pointer">
                <i data-lucide="building-2" class="w-4 h-4"></i>
                <span>Identitas & Branding</span>
            </button>

            <button type="button" @click="activeTab = 'integrations'" 
                :class="activeTab === 'integrations' ? 'border-slate-900 text-slate-900 dark:border-slate-100 dark:text-slate-100 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 hover:border-slate-300 font-semibold'"
                class="pb-3 px-4 text-xs border-b-2 flex items-center gap-2 transition-all cursor-pointer">
                <i data-lucide="cpu" class="w-4 h-4 text-emerald-600 dark:text-emerald-400"></i>
                <span>Integrasi API (HRD & SPMB)</span>
                <span class="px-1.5 py-0.5 text-[9px] font-bold rounded-md bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                    2 Aktif
                </span>
            </button>
        </div>

        <!-- FORM SETTINGS -->
        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            @if (isset($errors) && $errors->any())
                <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-red-600 dark:text-red-400 text-xs space-y-1">
                    <p class="font-bold">Terjadi kesalahan validasi:</p>
                    <ul class="list-disc pl-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- TAB 1: IDENTITAS & BRANDING -->
            <div x-show="activeTab === 'general'" x-cloak class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- LEFT SIDE: GENERAL SETTINGS -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- General Settings Card -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs overflow-hidden">
                        <div class="p-6 border-b border-slate-200 dark:border-slate-800">
                            <h3 class="text-sm font-bold tracking-tight text-slate-900 dark:text-slate-50">Informasi Umum Instansi</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Konfigurasi nama aplikasi, nama instansi unit, dan detail kontak sistem.</p>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- App Name -->
                                <div>
                                    <label for="app_name" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Nama Aplikasi <span class="text-red-500">*</span></label>
                                    <input type="text" name="app_name" id="app_name" value="{{ old('app_name', setting('app_name', 'SANS SD Malang')) }}" required
                                        class="w-full px-3.5 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:border-slate-400 dark:focus:border-slate-700 transition-colors">
                                </div>

                                <!-- Unit Name -->
                                <div>
                                    <label for="unit_name" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Nama Unit / Sekolah <span class="text-red-500">*</span></label>
                                    <input type="text" name="unit_name" id="unit_name" value="{{ old('unit_name', setting('unit_name', 'SD Anak Saleh Malang')) }}" required
                                        class="w-full px-3.5 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:border-slate-400 dark:focus:border-slate-700 transition-colors">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Email -->
                                <div>
                                    <label for="app_email" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Email Sistem</label>
                                    <input type="email" name="app_email" id="app_email" value="{{ old('app_email', setting('app_email', 'sd@anaksaleh.sch.id')) }}"
                                        class="w-full px-3.5 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:border-slate-400 dark:focus:border-slate-700 transition-colors">
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label for="app_phone" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Telepon / WhatsApp</label>
                                    <input type="text" name="app_phone" id="app_phone" value="{{ old('app_phone', setting('app_phone', '+62 812-3456-7890')) }}"
                                        class="w-full px-3.5 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:border-slate-400 dark:focus:border-slate-700 transition-colors">
                                </div>
                            </div>

                            <!-- Address -->
                            <div>
                                <label for="app_address" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Alamat Instansi</label>
                                <textarea name="app_address" id="app_address" rows="3"
                                    class="w-full px-3.5 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:border-slate-400 dark:focus:border-slate-700 transition-colors resize-none">{{ old('app_address', setting('app_address', 'Jl. Danau Ranau, Sawojajar, Kota Malang, Jawa Timur 65139')) }}</textarea>
                            </div>

                            <!-- Copyright Text -->
                            <div>
                                <label for="app_copyright" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Teks Copyright / Hak Cipta</label>
                                <input type="text" name="app_copyright" id="app_copyright" value="{{ old('app_copyright', setting('app_copyright', '© ' . date('Y') . ' SD Anak Saleh. All rights reserved.')) }}"
                                    class="w-full px-3.5 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:border-slate-400 dark:focus:border-slate-700 transition-colors">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT SIDE: BRANDING (LOGO & FAVICON) -->
                <div class="space-y-6">
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs overflow-hidden">
                        <div class="p-6 border-b border-slate-200 dark:border-slate-800">
                            <h3 class="text-sm font-bold tracking-tight text-slate-900 dark:text-slate-50">Visual & Branding</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Sesuaikan logo dan ikon favicon situs web Anda.</p>
                        </div>
                        <div class="p-6 space-y-6">

                            <!-- Logo Settings -->
                            <div class="space-y-3">
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400">Logo Aplikasi</label>
                                <div class="flex items-center gap-4">
                                    <!-- Logo Preview Box -->
                                    <div class="relative w-16 h-16 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 flex items-center justify-center overflow-hidden shrink-0">
                                        @if (setting('app_logo'))
                                            <img id="logo-preview" src="{{ asset('storage/' . setting('app_logo')) }}" alt="Logo Preview" class="w-full h-full object-cover">
                                        @else
                                            <div id="logo-placeholder" class="w-10 h-10 rounded-lg logo-gradient-bg flex items-center justify-center">
                                                <span class="text-white text-base font-bold" style="font-family: 'Nasalization Rg', sans-serif; font-weight: 400;">{{ substr(setting('app_name', 'SD'), 0, 1) }}</span>
                                            </div>
                                            <img id="logo-preview" src="" alt="Logo Preview" class="w-full h-full object-cover hidden">
                                        @endif
                                    </div>
                                    <div class="flex-1 space-y-1">
                                        <input type="file" name="app_logo" id="app_logo_input" accept="image/*" class="hidden" onchange="previewImage(this, 'logo-preview', 'logo-placeholder')">
                                        <button type="button" onclick="document.getElementById('app_logo_input').click()"
                                            class="px-3 py-1.5 bg-slate-100 dark:bg-slate-900 hover:bg-slate-200 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-[10px] font-bold rounded-lg cursor-pointer transition-colors flex items-center gap-1.5">
                                            <i data-lucide="upload" class="w-3.5 h-3.5"></i> Unggah Logo
                                        </button>
                                        <p class="text-[10px] text-slate-500 dark:text-slate-400">Format: PNG, JPG, SVG. Maks 1MB.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Favicon Settings -->
                            <div class="space-y-3">
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400">Ikon Favicon</label>
                                <div class="flex items-center gap-4">
                                    <!-- Favicon Preview Box -->
                                    <div class="relative w-12 h-12 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 flex items-center justify-center overflow-hidden shrink-0">
                                        @if (setting('app_favicon'))
                                            <img id="favicon-preview" src="{{ asset('storage/' . setting('app_favicon')) }}" alt="Favicon Preview" class="w-6 h-6 object-contain">
                                        @else
                                            <div id="favicon-placeholder" class="w-8 h-8 rounded bg-slate-100 dark:bg-slate-800 logo-gradient-bg flex items-center justify-center shrink-0 shadow-sm">
                                                <span class="text-white text-xs font-bold" style="font-family: 'Nasalization Rg', sans-serif; font-weight: 400;">{{ substr(setting('app_name', 'SD'), 0, 1) }}</span>
                                            </div>
                                            <img id="favicon-preview" src="" alt="Favicon Preview" class="w-6 h-6 object-contain hidden">
                                        @endif
                                    </div>
                                    <div class="flex-1 space-y-1">
                                        <input type="file" name="app_favicon" id="app_favicon_input" accept="image/x-icon,image/png,image/jpg,image/jpeg,image/svg+xml" class="hidden" onchange="previewImage(this, 'favicon-preview', 'favicon-placeholder', true)">
                                        <button type="button" onclick="document.getElementById('app_favicon_input').click()"
                                            class="px-3 py-1.5 bg-slate-100 dark:bg-slate-900 hover:bg-slate-200 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-[10px] font-bold rounded-lg cursor-pointer transition-colors flex items-center gap-1.5">
                                            <i data-lucide="upload" class="w-3.5 h-3.5"></i> Unggah Favicon
                                        </button>
                                        <p class="text-[10px] text-slate-500 dark:text-slate-400">Format: ICO, PNG, SVG. Maks 1MB.</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            <!-- TAB 2: INTEGRASI API (HRD & SPMB) -->
            <div x-show="activeTab === 'integrations'" x-cloak class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Card 1: Integrasi API HRD Pusat -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-bold tracking-tight text-slate-900 dark:text-slate-50 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                    Integrasi API HRD Pusat
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Koneksi data absensi, cuti, dan kepegawaian ke aplikasi HRD pusat.</p>
                            </div>
                            <span class="px-2 py-0.5 text-[10px] font-bold bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 rounded-md border border-blue-200 dark:border-blue-800">
                                Layanan HRD
                            </span>
                        </div>
                        <div class="p-6 space-y-4">
                            <!-- HRD URL -->
                            <div>
                                <label for="hrd_api_url" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">URL Aplikasi HRD</label>
                                <input type="url" name="hrd_api_url" id="hrd_api_url" value="{{ old('hrd_api_url', setting('hrd_api_url', config('app.hrd_url', 'http://sans-hrd.test'))) }}" placeholder="contoh: https://hrd.anakshaleh.sch.id"
                                    class="w-full px-3.5 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:border-slate-400 dark:focus:border-slate-700 transition-colors font-mono">
                            </div>

                            <!-- HRD Token -->
                            <div>
                                <label for="hrd_api_token" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Token API HRD</label>
                                <input type="text" name="hrd_api_token" id="hrd_api_token" value="{{ old('hrd_api_token', setting('hrd_api_token', config('app.hrd_api_token'))) }}"
                                    class="w-full px-3.5 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:border-slate-400 dark:focus:border-slate-700 transition-colors font-mono">
                            </div>

                            <!-- Info Box -->
                            <div class="p-3 bg-blue-50/60 dark:bg-blue-950/30 border border-blue-100 dark:border-blue-900/50 rounded-lg text-[11px] text-blue-700 dark:text-blue-300 leading-relaxed">
                                <i data-lucide="info" class="w-3.5 h-3.5 inline mr-1 text-blue-600"></i>
                                Pengaturan ini digunakan untuk sinkronisasi data presensi mesin, cuti pegawai, dan slip gaji secara otomatis ke pusat.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Integrasi API SPMB Pusat -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-bold tracking-tight text-slate-900 dark:text-slate-50 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    Integrasi API SPMB Pusat
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Koneksi data pendaftar & calon murid baru dari aplikasi SPMB.</p>
                            </div>
                            <span class="px-2 py-0.5 text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-md border border-emerald-200 dark:border-emerald-800">
                                Unit SD
                            </span>
                        </div>
                        <div class="p-6 space-y-4">
                            <!-- SPMB API URL -->
                            <div>
                                <label for="spmb_api_url" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">URL Aplikasi SPMB</label>
                                <input type="url" name="spmb_api_url" id="spmb_api_url" value="{{ old('spmb_api_url', setting('spmb_api_url', 'http://sans-spmb.test')) }}" placeholder="contoh: https://spmb.anakshaleh.sch.id"
                                    class="w-full px-3.5 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:border-slate-400 dark:focus:border-slate-700 transition-colors font-mono">
                            </div>

                            <!-- SPMB API Token -->
                            <div>
                                <label for="spmb_api_token" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Token Kunci API SPMB (Bearer Token)</label>
                                <input type="text" name="spmb_api_token" id="spmb_api_token" value="{{ old('spmb_api_token', setting('spmb_api_token')) }}" placeholder="spmb_tok_xxxxxxxxxxxx"
                                    class="w-full px-3.5 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:border-slate-400 dark:focus:border-slate-700 transition-colors font-mono">
                            </div>

                            <!-- SPMB Webhook Secret -->
                            <div>
                                <label for="spmb_webhook_secret" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Webhook Secret (HMAC SHA-256)</label>
                                <input type="text" name="spmb_webhook_secret" id="spmb_webhook_secret" value="{{ old('spmb_webhook_secret', setting('spmb_webhook_secret')) }}" placeholder="spmb_sec_xxxxxxxxxxxx"
                                    class="w-full px-3.5 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:border-slate-400 dark:focus:border-slate-700 transition-colors font-mono">
                            </div>

                            <!-- Webhook Receiver URL (Readonly Info) -->
                            <div class="p-3 bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 rounded-lg space-y-1">
                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400">URL Endpoint Webhook Receiver di SD (Salin ke SPMB):</label>
                                <div class="flex items-center gap-2">
                                    <code id="webhook-receiver-url" class="text-[11px] text-indigo-600 dark:text-indigo-400 font-mono select-all flex-1 truncate">{{ url('/api/spmb-webhook') }}</code>
                                    <button type="button" onclick="navigator.clipboard.writeText('{{ url('/api/spmb-webhook') }}'); alert('URL Webhook berhasil disalin!');" class="p-1 text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 text-xs font-semibold cursor-pointer" title="Salin URL Webhook">
                                        <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Test Connection Button -->
                            <div class="pt-2 flex items-center justify-between">
                                <button type="button" id="btn-test-spmb" onclick="testSpmbConnection()" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-colors flex items-center gap-2 cursor-pointer shadow-xs">
                                    <i data-lucide="activity" class="w-3.5 h-3.5"></i>
                                    <span>Tes Koneksi API SPMB</span>
                                </button>
                                <div id="spmb-test-result" class="text-xs font-semibold"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ACTIONS BUTTONS -->
            <div class="flex justify-end items-center gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                <a href="{{ route('dashboard') }}" class="px-4 py-2 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 bg-transparent hover:bg-slate-100 dark:hover:bg-slate-900 text-xs font-bold rounded-lg cursor-pointer transition-colors">
                    Batalkan
                </a>
                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 dark:bg-slate-50 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-bold rounded-lg cursor-pointer transition-colors flex items-center gap-2 shadow-xs">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan Perubahan
                </button>
            </div>

        </form>
    </div>

    <!-- JAVASCRIPT FOR LIVE IMAGE PREVIEWS & SPMB TEST -->
    <script>
        function previewImage(input, previewId, placeholderId, isFavicon = false) {
            const preview = document.getElementById(previewId);
            const placeholder = document.getElementById(placeholderId);
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    
                    if (placeholder) {
                        placeholder.classList.add('hidden');
                    }
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        function testSpmbConnection() {
            const btn = document.getElementById('btn-test-spmb');
            const resultBox = document.getElementById('spmb-test-result');
            const originalHtml = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-3.5 w-3.5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memeriksa...`;
            resultBox.innerHTML = `<span class="text-slate-400 font-normal">Menghubungi server SPMB...</span>`;

            fetch("{{ route('spmb.test-connection') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                }
            })
            .then(res => res.json().then(data => ({ status: res.status, ok: res.ok, data })))
            .then(res => {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                if (window.lucide) lucide.createIcons();

                if (res.ok && res.data.success) {
                    resultBox.innerHTML = `<span class="text-emerald-600 dark:text-emerald-400 flex items-center gap-1">✓ ${res.data.message}</span>`;
                } else {
                    resultBox.innerHTML = `<span class="text-red-500 dark:text-red-400 flex items-center gap-1">✕ ${res.data.message || 'Gagal terhubung ke SPMB'}</span>`;
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                if (window.lucide) lucide.createIcons();
                resultBox.innerHTML = `<span class="text-red-500 dark:text-red-400">✕ Terjadi kesalahan jaringan (${err.message})</span>`;
            });
        }
    </script>
</x-admin-layout>
