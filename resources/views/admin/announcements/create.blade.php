<x-admin-layout>
    <div class="p-3.5 sm:p-5 lg:p-6 space-y-4 sm:space-y-5 lg:space-y-6 text-left">
        <!-- Page Header -->
        <section class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
            <div>
                <h1 class="text-base sm:text-lg font-bold tracking-tight text-slate-900 dark:text-slate-50">Tambah Pengumuman</h1>
                <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 mt-0.5">Buat pengumuman baru untuk ditampilkan di portal informasi sekolah.</p>
            </div>
            <div>
                <a href="{{ route('announcements.index') }}" class="h-8 sm:h-9 px-3 sm:px-3.5 inline-flex items-center gap-1.5 text-xs font-semibold rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 shadow-xs transition-colors cursor-pointer">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </section>

        <!-- Form Card Container -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs overflow-hidden p-4 sm:p-6">
            <form action="{{ route('announcements.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5 sm:space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                    <!-- Judul (Full Width) -->
                    <div class="md:col-span-2">
                        <label for="title" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Judul Pengumuman <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="title" 
                            id="title" 
                            value="{{ old('title') }}" 
                            required 
                            placeholder="Contoh: Jadwal Libur Semester Ganjil TA 2026/2027"
                            class="w-full h-9 sm:h-9.5 px-3 text-xs bg-slate-50/50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-xs"
                        >
                        @error('title') 
                            <p class="text-rose-500 text-[11px] font-medium mt-1">{{ $message }}</p> 
                        @enderror
                    </div>

                    <!-- Kategori -->
                    <div>
                        <label for="category" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Kategori <span class="text-rose-500">*</span>
                        </label>
                        <select 
                            name="category" 
                            id="category" 
                            required 
                            class="w-full h-9 sm:h-9.5 px-3 text-xs bg-slate-50/50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-xs cursor-pointer"
                        >
                            <option value="umum" {{ old('category') == 'umum' ? 'selected' : '' }}>Umum / Info Sekolah</option>
                            <option value="akademik" {{ old('category') == 'akademik' ? 'selected' : '' }}>Kurikulum / Akademik</option>
                            <option value="kepegawaian" {{ old('category') == 'kepegawaian' ? 'selected' : '' }}>Kepegawaian / HRD</option>
                            <option value="penting" {{ old('category') == 'penting' ? 'selected' : '' }}>Penting / Urgent</option>
                        </select>
                        @error('category') 
                            <p class="text-rose-500 text-[11px] font-medium mt-1">{{ $message }}</p> 
                        @enderror
                    </div>

                    <!-- Target Audiens -->
                    <div>
                        <label for="target_audience" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Target Audiens <span class="text-rose-500">*</span>
                        </label>
                        <select 
                            name="target_audience" 
                            id="target_audience" 
                            required 
                            class="w-full h-9 sm:h-9.5 px-3 text-xs bg-slate-50/50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-xs cursor-pointer"
                        >
                            <option value="global" {{ old('target_audience') == 'global' ? 'selected' : '' }}>Semua (Global)</option>
                            <option value="management" {{ old('target_audience') == 'management' ? 'selected' : '' }}>Manajemen Saja (Kepala Sekolah, Waka, Admin)</option>
                            <option value="teacher" {{ old('target_audience') == 'teacher' ? 'selected' : '' }}>Guru Saja</option>
                            <option value="employee" {{ old('target_audience') == 'employee' ? 'selected' : '' }}>Pegawai / Staf Saja (Non-Guru)</option>
                            <option value="student" {{ old('target_audience') == 'student' ? 'selected' : '' }}>Siswa Saja (API)</option>
                            <option value="parent" {{ old('target_audience') == 'parent' ? 'selected' : '' }}>Orang Tua Saja (API)</option>
                        </select>
                        @error('target_audience') 
                            <p class="text-rose-500 text-[11px] font-medium mt-1">{{ $message }}</p> 
                        @enderror
                    </div>

                    <!-- Isi Pengumuman (CKEditor) -->
                    <div class="md:col-span-2">
                        <label for="content" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Isi Pengumuman <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="content" id="content" rows="6" required class="w-full text-xs rounded-lg">{{ old('content') }}</textarea>
                        @error('content') 
                            <p class="text-rose-500 text-[11px] font-medium mt-1">{{ $message }}</p> 
                        @enderror
                    </div>

                    <!-- Lampiran -->
                    <div class="space-y-1.5">
                        <label for="attachment" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Lampiran Dokumen / Gambar <span class="text-[11px] font-normal text-slate-400">(Opsional)</span>
                        </label>
                        <input 
                            type="file" 
                            name="attachment" 
                            id="attachment" 
                            accept=".pdf,.jpg,.jpeg,.png" 
                            class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-950/60 file:text-indigo-600 dark:file:text-indigo-400 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-900/50 file:transition-colors file:cursor-pointer border border-slate-200 dark:border-slate-800 rounded-lg p-1 bg-slate-50/50 dark:bg-slate-950 shadow-xs cursor-pointer"
                        >
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Format: PDF, JPG, PNG. Maksimal 5MB.</p>
                        @error('attachment') 
                            <p class="text-rose-500 text-[11px] font-medium mt-1">{{ $message }}</p> 
                        @enderror
                    </div>
                    
                    <!-- Pengaturan Waktu & Status -->
                    <div class="space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="publish_date" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Tanggal Publikasi
                                </label>
                                <input 
                                    type="datetime-local" 
                                    name="publish_date" 
                                    id="publish_date" 
                                    value="{{ old('publish_date', now()->format('Y-m-d\TH:i')) }}" 
                                    class="w-full h-9 sm:h-9.5 px-3 text-xs bg-slate-50/50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-xs"
                                >
                            </div>

                            <div>
                                <label for="expiry_date" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Masa Berlaku Berakhir <span class="text-[11px] font-normal text-slate-400">(Opsional)</span>
                                </label>
                                <input 
                                    type="datetime-local" 
                                    name="expiry_date" 
                                    id="expiry_date" 
                                    value="{{ old('expiry_date') }}" 
                                    class="w-full h-9 sm:h-9.5 px-3 text-xs bg-slate-50/50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-xs"
                                >
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Kosongkan waktu berakhir jika pengumuman berlaku selamanya.</p>
                        
                        <div class="pt-1">
                            <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                <input 
                                    type="checkbox" 
                                    name="is_active" 
                                    id="is_active" 
                                    value="1" 
                                    {{ old('is_active', true) ? 'checked' : '' }} 
                                    class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-indigo-500 dark:bg-slate-900 cursor-pointer"
                                >
                                <span class="text-xs font-medium text-slate-700 dark:text-slate-300">Langsung aktifkan pengumuman ini</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('announcements.index') }}" class="h-8 sm:h-9 px-3.5 inline-flex items-center justify-center rounded-lg font-semibold text-xs text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 shadow-xs transition-colors cursor-pointer">
                        Batal
                    </a>
                    <button type="submit" class="h-8 sm:h-9 px-4 inline-flex items-center justify-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold text-xs shadow-xs transition-colors cursor-pointer">
                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        <span>Simpan Pengumuman</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- CKEditor CDN -->
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <style>
        .cke_chrome {
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.5rem !important;
            overflow: hidden !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        }
        /* Custom styling for CKEditor Toolbar in Dark Mode */
        html.dark .cke_chrome {
            border-color: #334155 !important;
        }
        html.dark .cke_top, html.dark .cke_bottom {
            background: #0f172a !important;
            border-color: #334155 !important;
        }
        html.dark .cke_toolgroup {
            background: #1e293b !important;
            border-color: #475569 !important;
        }
        html.dark .cke_button_icon {
            filter: invert(0.8) !important;
        }
        html.dark .cke_button:hover {
            background: #334155 !important;
        }
        html.dark .cke_resizer {
            border-color: transparent transparent #cbd5e1 transparent !important;
        }
    </style>
    <script>
        CKEDITOR.replace('content', {
            height: 300,
            versionCheck: false,
            removeButtons: 'PasteFromWord,Image,Table',
            on: {
                instanceReady: function(evt) {
                    const isDark = document.documentElement.classList.contains('dark');
                    if (isDark) {
                        const body = evt.editor.document.getBody();
                        body.setStyle('background-color', '#0f172a');
                        body.setStyle('color', '#cbd5e1');
                    }
                }
            }
        });

        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'class') {
                    const isDark = document.documentElement.classList.contains('dark');
                    for (const instance in CKEDITOR.instances) {
                        const body = CKEDITOR.instances[instance].document.getBody();
                        if (isDark) {
                            body.setStyle('background-color', '#0f172a');
                            body.setStyle('color', '#cbd5e1');
                        } else {
                            body.setStyle('background-color', '#ffffff');
                            body.setStyle('color', '#333333');
                        }
                    }
                }
            });
        });
        observer.observe(document.documentElement, { attributes: true });
    </script>
</x-admin-layout>
