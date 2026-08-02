<x-admin-layout>
    <div class="p-6 space-y-6 max-w-4xl mx-auto text-left">

        <!-- HEADER -->
        <header class="flex flex-col gap-1 w-full">
            <div class="flex items-center gap-2">
                <a href="{{ route('employees.index') }}" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-900 rounded-lg text-slate-500 hover:text-slate-900 transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                </a>
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Tambah Pegawai Baru</h2>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 pl-8">Masukkan rincian profil pendidik baru di bawah ini. Tipe pegawai akan diatur sebagai Pegawai secara otomatis.</p>
        </header>

        <!-- FORM CARD -->
        <section class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm p-6">
            <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 text-xs">
                @csrf

                <!-- Grid Form -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <!-- Gelar & Nama -->
                    <div class="col-span-full grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Gelar Depan -->
                        <div>
                            <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Gelar Depan</label>
                            <input type="text" name="front_title" value="{{ old('front_title') }}" placeholder="Dr., Ir."
                                class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800 focus:ring-slate-100">
                        </div>
                        <!-- Nama Lengkap -->
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Eko Wibowo" required
                                class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('name') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                            @error('name')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                        <!-- Gelar Belakang -->
                        <div>
                            <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Gelar Belakang</label>
                            <input type="text" name="back_title" value="{{ old('back_title') }}" placeholder="S.Pd., M.Kom."
                                class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800 focus:ring-slate-100">
                        </div>
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

                                        <!-- DATA DIRI -->
                    <div class="md:col-span-2 mt-4 mb-2 border-b pb-2"><h4 class="font-bold text-slate-700 dark:text-slate-300">Data Diri</h4></div>
                    
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Tempat Lahir</label>
                        <input type="text" name="birth_place" value="{{ old('birth_place', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Tanggal Lahir</label>
                        <input type="date" name="birth_date" value="{{ old('birth_date', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Jenis Kelamin</label>
                        <select name="gender" required class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800 cursor-pointer">
                            <option value="Male" {{ old('gender', '') == 'Male' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Female" {{ old('gender', '') == 'Female' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Alamat</label>
                        <input type="text" name="address" value="{{ old('address', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">No. HP / WA</label>
                        <input type="text" name="phone" value="{{ old('phone', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>

                    <!-- DATA KEPEGAWAIAN -->
                    <div class="md:col-span-2 mt-4 mb-2 border-b pb-2"><h4 class="font-bold text-slate-700 dark:text-slate-300">Data Kepegawaian</h4></div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Tipe Pegawai <span class="text-rose-500">*</span></label>
                        <select name="employee_type_id" required class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800 cursor-pointer">
                            <option value="">Pilih Tipe Pegawai</option>
                            @foreach($employeeTypes as $type)
                                <option value="{{ $type->id }}" {{ old('employee_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('employee_type_id')
                            <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">NIK</label>
                        <input type="text" name="nik" value="{{ old('nik', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">NIY</label>
                        <input type="text" name="niy" value="{{ old('niy', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">NUPTK</label>
                        <input type="text" name="nuptk" value="{{ old('nuptk', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">NO UKG</label>
                        <input type="text" name="no_ukg" value="{{ old('no_ukg', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">NRG</label>
                        <input type="text" name="nrg" value="{{ old('nrg', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Pangkat / Golongan</label>
                        <input type="text" name="pangkat_golongan" value="{{ old('pangkat_golongan', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Pendidikan Terakhir</label>
                        <input type="text" name="last_education" value="{{ old('last_education', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Jurusan</label>
                        <input type="text" name="major" value="{{ old('major', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Jabatan Utama</label>
                        <input type="text" name="position" value="{{ old('position', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Jabatan Tambahan</label>
                        <input type="text" name="additional_position" value="{{ old('additional_position', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Status Kepegawaian</label>
                        <input type="text" name="employment_status" value="{{ old('employment_status', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Tanggal Mulai Tugas</label>
                        <input type="date" name="task_start_date" value="{{ old('task_start_date', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Tanggal Diangkat</label>
                        <input type="date" name="appointment_date" value="{{ old('appointment_date', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Tanggal SK Terakhir</label>
                        <input type="date" name="last_sk_date" value="{{ old('last_sk_date', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Nomor SK Terakhir</label>
                        <input type="text" name="last_sk_number" value="{{ old('last_sk_number', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Masa Kerja Golongan</label>
                        <input type="text" name="work_period" value="{{ old('work_period', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>

                    <!-- SISTEM ABSENSI -->
                    <div class="md:col-span-2 mt-4 mb-2 border-b pb-2"><h4 class="font-bold text-slate-700 dark:text-slate-300">Sistem Absensi & Catatan</h4></div>
                    @if(auth()->user()->role === 'super_admin')
                    <!-- ID ZKTeco / PIN Fingerprint -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">ID ZKTeco / PIN Fingerprint</label>
                        <input type="text" name="zkteco_uid" value="{{ old('zkteco_uid') }}" readonly
                            class="w-full h-9 px-3 font-mono bg-slate-100 dark:bg-slate-800 border rounded-lg text-slate-500 dark:text-slate-400 focus:outline-none cursor-not-allowed border-slate-200 dark:border-slate-800">
                        <span class="text-[10px] text-amber-600 dark:text-amber-500 mt-1 block">
                            *Pendaftaran/Perubahan PIN Mesin ZKTeco hanya dapat dilakukan melalui Portal SANS HRD Pusat.
                        </span>
                        @error('zkteco_uid')
                            <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>
                    @endif

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
                    <a href="{{ route('employees.index') }}" class="h-9 px-4 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg hover:bg-slate-50 dark:hover:bg-slate-900 flex items-center justify-center transition-all">Batal</a>
                    <button type="submit" class="h-9 px-5 bg-slate-900 dark:bg-slate-50 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center justify-center cursor-pointer">Simpan Pegawai</button>
                </div>
            </form>
        </section>
    </div>
</x-admin-layout>

