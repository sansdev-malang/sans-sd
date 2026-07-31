<x-admin-layout>
    <div class="p-6 space-y-6">
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Tambah Pengumuman</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Buat pengumuman baru untuk ditampilkan di dashboard.</p>
            </div>
        </section>

        <div class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-sm p-6">
            <form action="{{ route('announcements.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Judul -->
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Judul Pengumuman <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Kategori -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Kategori <span class="text-red-500">*</span></label>
                        <select name="category" id="category" required class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="umum" {{ old('category') == 'umum' ? 'selected' : '' }}>Umum / Info Sekolah</option>
                            <option value="akademik" {{ old('category') == 'akademik' ? 'selected' : '' }}>Kurikulum / Akademik</option>
                            <option value="kepegawaian" {{ old('category') == 'kepegawaian' ? 'selected' : '' }}>Kepegawaian / HRD</option>
                            <option value="penting" {{ old('category') == 'penting' ? 'selected' : '' }}>Penting / Urgent</option>
                        </select>
                        @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Target Audience -->
                    <div>
                        <label for="target_audience" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Target Audiens <span class="text-red-500">*</span></label>
                        <select name="target_audience" id="target_audience" required class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="global" {{ old('target_audience') == 'global' ? 'selected' : '' }}>Global (Semua)</option>
                            <option value="employee" {{ old('target_audience') == 'employee' ? 'selected' : '' }}>Pegawai / Guru Saja</option>
                            <option value="student" {{ old('target_audience') == 'student' ? 'selected' : '' }}>Siswa Saja (API)</option>
                            <option value="parent" {{ old('target_audience') == 'parent' ? 'selected' : '' }}>Orang Tua Saja (API)</option>
                        </select>
                        @error('target_audience') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Isi -->
                    <div class="md:col-span-2">
                        <label for="content" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Isi Pengumuman <span class="text-red-500">*</span></label>
                        <textarea name="content" id="content" rows="6" required class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('content') }}</textarea>
                        @error('content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Lampiran -->
                    <div>
                        <label for="attachment" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Lampiran (Opsional)</label>
                        <input type="file" name="attachment" id="attachment" accept=".pdf,.jpg,.jpeg,.png" class="mt-1 block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-slate-50 file:text-slate-700 hover:file:bg-slate-100 dark:file:bg-slate-900 dark:hover:file:bg-slate-800">
                        <p class="text-xs text-slate-500 mt-1">Format: PDF, JPG, PNG. Maksimal 5MB.</p>
                        @error('attachment') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <!-- Pengaturan Tambahan -->
                    <div class="space-y-4">
                        <div>
                            <label for="publish_date" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Tanggal Publish</label>
                            <input type="datetime-local" name="publish_date" id="publish_date" value="{{ old('publish_date', now()->format('Y-m-d\TH:i')) }}" class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        </div>

                        <div>
                            <label for="expiry_date" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Waktu Berakhir (Opsional)</label>
                            <input type="datetime-local" name="expiry_date" id="expiry_date" value="{{ old('expiry_date') }}" class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <p class="text-xs text-slate-500 mt-1">Kosongkan jika pengumuman berlaku selamanya.</p>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <label for="is_active" class="ml-2 block text-sm text-slate-700 dark:text-slate-300">Langsung Aktifkan</label>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                    <a href="{{ route('announcements.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-md font-semibold text-xs text-slate-700 dark:text-slate-300 uppercase tracking-widest shadow-sm hover:bg-slate-50 dark:hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Simpan Pengumuman
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tambahkan CKEditor CDN -->
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <style>
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
            removeButtons: 'PasteFromWord,Image,Table', // Sesuaikan fitur sesuai kebutuhan
            on: {
                instanceReady: function(evt) {
                    // Cek apakah dark mode aktif
                    const isDark = document.documentElement.classList.contains('dark');
                    if (isDark) {
                        const body = evt.editor.document.getBody();
                        body.setStyle('background-color', '#0f172a');
                        body.setStyle('color', '#cbd5e1');
                    }
                }
            }
        });

        // Optional: Listen to theme changes if your app has a dynamic toggle
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
