<x-admin-layout>
    <div class="p-6 space-y-6 max-w-4xl mx-auto text-left">

        <!-- HEADER -->
        <header class="flex flex-col gap-1 w-full">
            <div class="flex items-center gap-2">
                <a href="{{ route('teachers.index') }}" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-900 rounded-lg text-slate-500 hover:text-slate-900 transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                </a>
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Tambah Guru Baru</h2>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 pl-8">Masukkan rincian profil pendidik baru di bawah ini. Tipe pegawai akan diatur sebagai Guru secara otomatis.</p>
        </header>

        <!-- FORM CARD -->
        <section class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm p-6">
            <form action="{{ route('teachers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 text-xs">
                @csrf

                <!-- Grid Form -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <!-- Nama Lengkap -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Drs. Eko Wibowo, M.Pd" required
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('name') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                        @error('name')
                            <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Contoh: nama@domain.com" required
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('email') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                        @error('email')
                            <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- NIP / NUPTK / NIK -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">NIP / NUPTK / NIK</label>
                        <input type="text" name="nuptk_nip_nik" value="{{ old('nuptk_nip_nik') }}" placeholder="Masukkan nomor identitas pendidik"
                            class="w-full h-9 px-3 font-mono bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('nuptk_nip_nik') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                        @error('nuptk_nip_nik')
                            <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Mata Pelajaran -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Mata Pelajaran</label>
                        <input type="text" name="subject_position" value="{{ old('subject_position') }}" placeholder="Contoh: Matematika, Fisika, Bahasa Indonesia"
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('subject_position') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                        @error('subject_position')
                            <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Jenis Kelamin -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Jenis Kelamin</label>
                        <select name="gender" required class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('gender') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror cursor-pointer">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('gender')
                            <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Status Kepegawaian -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Status Kepegawaian</label>
                        <input type="text" name="employment_status" value="{{ old('employment_status') }}" placeholder="Contoh: PNS, GTY, Honorer" required
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('employment_status') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                        @error('employment_status')
                            <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- ID ZKTeco / PIN Fingerprint -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">ID ZKTeco / PIN Fingerprint</label>
                        <input type="text" name="zkteco_uid" value="{{ old('zkteco_uid') }}" placeholder="Dapat dikosongkan terlebih dahulu"
                            class="w-full h-9 px-3 font-mono bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('zkteco_uid') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 block">
                            *Dianjurkan dikosongkan dahulu. Biarkan Admin Unit mengisi setelah mengecek fisik mesin.
                        </span>
                        @error('zkteco_uid')
                            <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Status Keaktifan -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Status Keaktifan</label>
                        <select name="status" required class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('status') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror cursor-pointer">
                            <option value="Active" {{ old('status', 'Active') == 'Active' ? 'selected' : '' }}>Aktif</option>
                            <option value="Leave" {{ old('status') == 'Leave' ? 'selected' : '' }}>Cuti</option>
                            <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('status')
                            <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Unggah Foto -->
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Foto Profil</label>
                        <input type="file" name="photo" accept="image/*"
                            class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none file:mr-4 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-[10px] file:font-semibold file:bg-slate-200 dark:file:bg-slate-800 file:text-slate-700 dark:file:text-slate-300 hover:file:bg-slate-300 dark:hover:file:bg-slate-700 cursor-pointer">
                        @error('photo')
                            <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-900 flex justify-end gap-3">
                    <a href="{{ route('teachers.index') }}" class="h-9 px-4 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg hover:bg-slate-50 dark:hover:bg-slate-900 flex items-center justify-center transition-all">Batal</a>
                    <button type="submit" class="h-9 px-5 bg-slate-900 dark:bg-slate-50 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center justify-center cursor-pointer">Simpan Guru</button>
                </div>
            </form>
        </section>
    </div>
</x-admin-layout>
