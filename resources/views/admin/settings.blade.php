<x-admin-layout>
    <div class="p-6 space-y-6">

        <!-- PAGE HEADER -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Pengaturan Sistem</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Konfigurasi identitas sistem, informasi unit kerja, dan aset visual (logo/favicon).</p>
            </div>
        </section>

        <!-- FORM SETTINGS -->
        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            @if ($errors->any())
                <div class="p-4 bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-800 rounded-xl text-red-650 dark:text-red-400 text-xs space-y-1">
                    <p class="font-bold">Terjadi kesalahan validasi:</p>
                    <ul class="list-disc pl-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- LEFT SIDE: GENERAL SETTINGS -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- General Settings Card -->
                    <div class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-slate-200 dark:border-slate-800">
                            <h3 class="text-sm font-bold tracking-tight text-slate-900 dark:text-slate-50">Informasi Umum</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Konfigurasi nama aplikasi, nama instansi unit, dan detail kontak sistem.</p>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- App Name -->
                                <div>
                                    <label for="app_name" class="block text-xs font-semibold text-slate-650 dark:text-slate-400 mb-1.5">Nama Aplikasi <span class="text-red-550">*</span></label>
                                    <input type="text" name="app_name" id="app_name" value="{{ old('app_name', setting('app_name', 'SANS Malang')) }}" required
                                        class="w-full px-3.5 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:border-slate-400 dark:focus:border-slate-700 transition-colors">
                                </div>

                                <!-- Unit Name -->
                                <div>
                                    <label for="unit_name" class="block text-xs font-semibold text-slate-650 dark:text-slate-400 mb-1.5">Nama Unit / Sekolah <span class="text-red-550">*</span></label>
                                    <input type="text" name="unit_name" id="unit_name" value="{{ old('unit_name', setting('unit_name', 'SANS Malang')) }}" required
                                        class="w-full px-3.5 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:border-slate-400 dark:focus:border-slate-700 transition-colors">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Email -->
                                <div>
                                    <label for="app_email" class="block text-xs font-semibold text-slate-650 dark:text-slate-400 mb-1.5">Email Sistem</label>
                                    <input type="email" name="app_email" id="app_email" value="{{ old('app_email', setting('app_email', 'admin@sans.dev')) }}"
                                        class="w-full px-3.5 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:border-slate-400 dark:focus:border-slate-700 transition-colors">
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label for="app_phone" class="block text-xs font-semibold text-slate-650 dark:text-slate-400 mb-1.5">Telepon / WhatsApp</label>
                                    <input type="text" name="app_phone" id="app_phone" value="{{ old('app_phone', setting('app_phone', '+62 812-3456-7890')) }}"
                                        class="w-full px-3.5 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:border-slate-400 dark:focus:border-slate-700 transition-colors">
                                </div>
                            </div>

                            <!-- Address -->
                            <div>
                                <label for="app_address" class="block text-xs font-semibold text-slate-650 dark:text-slate-400 mb-1.5">Alamat Instansi</label>
                                <textarea name="app_address" id="app_address" rows="3"
                                    class="w-full px-3.5 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:border-slate-400 dark:focus:border-slate-700 transition-colors resize-none">{{ old('app_address', setting('app_address', 'Jl. Danau Ranau, Sawojajar, Kota Malang, Jawa Timur 65139')) }}</textarea>
                            </div>

                            <!-- Copyright Text -->
                            <div>
                                <label for="app_copyright" class="block text-xs font-semibold text-slate-650 dark:text-slate-400 mb-1.5">Teks Copyright / Hak Cipta</label>
                                <input type="text" name="app_copyright" id="app_copyright" value="{{ old('app_copyright', setting('app_copyright', '© ' . date('Y') . ' SANS Malang. All rights reserved.')) }}"
                                    class="w-full px-3.5 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:border-slate-400 dark:focus:border-slate-700 transition-colors">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Integrasi API HRD Card -->
                <div class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden mt-6">
                    <div class="p-6 border-b border-slate-200 dark:border-slate-800">
                        <h3 class="text-sm font-bold tracking-tight text-slate-900 dark:text-slate-50">Integrasi API HRD</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Pengaturan koneksi API untuk tersambung ke aplikasi pusat HRD.</p>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- API URL -->
                        <div>
                            <label for="hrd_api_url" class="block text-xs font-semibold text-slate-650 dark:text-slate-400 mb-1.5">URL Aplikasi HRD</label>
                            <input type="url" name="hrd_api_url" id="hrd_api_url" value="{{ old('hrd_api_url', setting('hrd_api_url', env('HRD_URL', 'http://sans-hrd.test'))) }}" placeholder="contoh: https://hrd.sekolahanaksaleh.sch.id"
                                class="w-full px-3.5 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:border-slate-400 dark:focus:border-slate-700 transition-colors">
                        </div>

                        <!-- API Token -->
                        <div>
                            <label for="hrd_api_token" class="block text-xs font-semibold text-slate-650 dark:text-slate-400 mb-1.5">Token API HRD</label>
                            <input type="text" name="hrd_api_token" id="hrd_api_token" value="{{ old('hrd_api_token', setting('hrd_api_token', config('app.hrd_api_token'))) }}"
                                class="w-full px-3.5 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:border-slate-400 dark:focus:border-slate-700 transition-colors">
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT SIDE: BRANDING (LOGO & FAVICON) -->
                <div class="space-y-6">
                    <div class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-slate-200 dark:border-slate-800">
                            <h3 class="text-sm font-bold tracking-tight text-slate-900 dark:text-slate-50">Visual & Branding</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Sesuaikan logo dan ikon situs web Anda.</p>
                        </div>
                        <div class="p-6 space-y-6">

                            <!-- Logo Settings -->
                            <div class="space-y-3">
                                <label class="block text-xs font-semibold text-slate-650 dark:text-slate-400">Logo Aplikasi</label>
                                <div class="flex items-center gap-4">
                                    <!-- Logo Preview Box -->
                                    <div class="relative w-16 h-16 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 flex items-center justify-center overflow-hidden shrink-0">
                                        @if (setting('app_logo'))
                                            <img id="logo-preview" src="{{ asset('storage/' . setting('app_logo')) }}" alt="Logo Preview" class="w-full h-full object-cover">
                                        @else
                                            <div id="logo-placeholder" class="w-10 h-10 rounded-lg logo-gradient-bg flex items-center justify-center">
                                                <span class="text-white text-base font-bold" style="font-family: 'Nasalization Rg', sans-serif; font-weight: 400;">{{ substr(setting('app_name', 'SANS'), 0, 1) }}</span>
                                            </div>
                                            <img id="logo-preview" src="" alt="Logo Preview" class="w-full h-full object-cover hidden">
                                        @endif
                                    </div>
                                    <div class="flex-1 space-y-1">
                                        <input type="file" name="app_logo" id="app_logo_input" accept="image/*" class="hidden" onchange="previewImage(this, 'logo-preview', 'logo-placeholder')">
                                        <button type="button" onclick="document.getElementById('app_logo_input').click()"
                                            class="px-3 py-1.5 bg-slate-100 dark:bg-slate-900 hover:bg-slate-200 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-850 text-slate-700 dark:text-slate-350 text-[10px] font-bold rounded-lg cursor-pointer transition-colors flex items-center gap-1.5">
                                            <i data-lucide="upload" class="w-3.5 h-3.5"></i> Unggah Logo
                                        </button>
                                        <p class="text-[10px] text-slate-500 dark:text-slate-400">Format: PNG, JPG, atau SVG. Maks 1MB.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Favicon Settings -->
                            <div class="space-y-3">
                                <label class="block text-xs font-semibold text-slate-650 dark:text-slate-400">Ikon Favicon</label>
                                <div class="flex items-center gap-4">
                                    <!-- Favicon Preview Box -->
                                    <div class="relative w-12 h-12 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 flex items-center justify-center overflow-hidden shrink-0">
                                        @if (setting('app_favicon'))
                                            <img id="favicon-preview" src="{{ asset('storage/' . setting('app_favicon')) }}" alt="Favicon Preview" class="w-6 h-6 object-contain">
                                        @else
                                            <div id="favicon-placeholder" class="w-8 h-8 rounded bg-slate-100 dark:bg-slate-800 logo-gradient-bg flex items-center justify-center shrink-0 shadow-sm">
                                                <span class="text-white text-xs font-bold" style="font-family: 'Nasalization Rg', sans-serif; font-weight: 400;">{{ substr(setting('app_name', 'SANS'), 0, 1) }}</span>
                                            </div>
                                            <img id="favicon-preview" src="" alt="Favicon Preview" class="w-6 h-6 object-contain hidden">
                                        @endif
                                    </div>
                                    <div class="flex-1 space-y-1">
                                        <input type="file" name="app_favicon" id="app_favicon_input" accept="image/x-icon,image/png,image/jpg,image/jpeg,image/svg+xml" class="hidden" onchange="previewImage(this, 'favicon-preview', 'favicon-placeholder', true)">
                                        <button type="button" onclick="document.getElementById('app_favicon_input').click()"
                                            class="px-3 py-1.5 bg-slate-100 dark:bg-slate-900 hover:bg-slate-200 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-850 text-slate-700 dark:text-slate-350 text-[10px] font-bold rounded-lg cursor-pointer transition-colors flex items-center gap-1.5">
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

            <!-- ACTIONS BUTTONS -->
            <div class="flex justify-end items-center gap-3">
                <a href="{{ route('dashboard') }}" class="px-4 py-2 border border-slate-200 dark:border-slate-850 text-slate-700 dark:text-slate-350 bg-transparent hover:bg-slate-100 dark:hover:bg-slate-900 text-xs font-bold rounded-lg cursor-pointer transition-colors">
                    Batalkan
                </a>
                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 dark:bg-slate-50 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-bold rounded-lg cursor-pointer transition-colors flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan Perubahan
                </button>
            </div>

        </form>
    </div>

    <!-- JAVASCRIPT FOR LIVE IMAGE PREVIEWS -->
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
    </script>

    <!-- TOAST NOTIFICATION SCRIPT -->
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const toast = document.getElementById('toast-notification');
                if (toast) {
                    const toastText = toast.querySelector('p');
                    if (toastText) {
                        toastText.textContent = "{{ session('success') }}";
                    }
                    
                    // Show notification with anim
                    toast.classList.remove('hidden');
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(20px)';
                    
                    setTimeout(() => {
                        toast.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                        toast.style.opacity = '1';
                        toast.style.transform = 'translateY(0)';
                    }, 50);

                    // Auto hide
                    setTimeout(() => {
                        toast.style.opacity = '0';
                        toast.style.transform = 'translateY(20px)';
                        setTimeout(() => {
                            toast.classList.add('hidden');
                        }, 300);
                    }, 5000);
                }
            });
        </script>
    @endif
</x-admin-layout>
