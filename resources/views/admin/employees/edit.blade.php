<x-admin-layout>
    <div class="p-6 space-y-6 max-w-4xl mx-auto text-left">

        <!-- HEADER -->
        <header class="flex flex-col gap-1 w-full">
            <div class="flex items-center gap-2">
                <a href="{{ route('employees.index') }}" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-900 rounded-lg text-slate-500 hover:text-slate-900 transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                </a>
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Edit Data Pegawai</h2>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 pl-8">Perbarui data profil pendidik di bawah ini.</p>
        </header>

        <!-- FORM CARD -->
        <section class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm p-6">
            <form action="{{ route('employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6 text-xs">
                @csrf
                @method('PUT')

                <!-- Photo Preview Section -->
                <div class="flex items-center gap-4 bg-slate-50 dark:bg-slate-900 p-4 rounded-xl border border-slate-150 dark:border-slate-850">
                    @if($employee->photo)
                        <img id="photo-preview" src="{{ str_contains($employee->photo, 'photos/') ? asset('storage/' . $employee->photo) : asset('storage/photos/' . $employee->photo) }}" class="w-16 h-16 rounded-full object-cover border-2 border-white dark:border-slate-800 shadow-sm" alt="{{ $employee->name }}">
                    @else
                        <div id="photo-fallback" class="w-16 h-16 rounded-full bg-slate-200 dark:bg-slate-800 flex items-center justify-center text-lg font-bold text-slate-700 dark:text-slate-300 border-2 border-white dark:border-slate-800 uppercase">
                            {{ substr($employee->name, 0, 2) }}
                        </div>
                    @endif
                    <div class="flex flex-col gap-1 text-left">
                        <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Foto Profil Saat Ini</h4>
                        <p class="text-[10px] text-slate-450 dark:text-slate-500">Ganti foto dengan mengunggah file baru pada field di bawah.</p>
                    </div>
                </div>

                <!-- Grid Form -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <!-- Gelar & Nama -->
                    <div class="col-span-full grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Gelar Depan -->
                        <div>
                            <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Gelar Depan</label>
                            <input type="text" name="front_title" value="{{ old('front_title', $employee->front_title) }}" placeholder="Dr., Ir."
                                class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800 focus:ring-slate-100">
                        </div>
                        <!-- Nama Lengkap -->
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $employee->raw_name) }}" placeholder="Eko Wibowo" required
                                class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('name') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                            @error('name')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                        <!-- Gelar Belakang -->
                        <div>
                            <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Gelar Belakang</label>
                            <input type="text" name="back_title" value="{{ old('back_title', $employee->back_title) }}" placeholder="S.Pd., M.Kom."
                                class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800 focus:ring-slate-100">
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email', $employee->email) }}" placeholder="Contoh: nama@domain.com" required
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('email') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                        @error('email')
                            <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                                        <!-- DATA DIRI -->
                    <div class="md:col-span-2 mt-4 mb-2 border-b pb-2"><h4 class="font-bold text-slate-700 dark:text-slate-300">Data Diri</h4></div>
                    
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Tempat Lahir</label>
                        <input type="text" name="birth_place" value="{{ old('birth_place', $employee->birth_place ?? '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Tanggal Lahir</label>
                        <input type="date" name="birth_date" value="{{ old('birth_date', $employee->birth_date ?? '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Jenis Kelamin</label>
                        <select name="gender" required class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800 cursor-pointer">
                            <option value="Male" {{ old('gender', $employee->gender ?? '') == 'Male' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Female" {{ old('gender', $employee->gender ?? '') == 'Female' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Alamat</label>
                        <input type="text" name="address" value="{{ old('address', $employee->address ?? '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">No. HP / WA</label>
                        <input type="text" name="phone" value="{{ old('phone', $employee->phone ?? '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>

                    <!-- DATA KEPEGAWAIAN -->
                    <div class="md:col-span-2 mt-4 mb-2 border-b pb-2"><h4 class="font-bold text-slate-700 dark:text-slate-300">Data Kepegawaian</h4></div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Tipe Pegawai <span class="text-rose-500">*</span></label>
                        <select name="employee_type_id" required class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800 cursor-pointer">
                            <option value="">Pilih Tipe Pegawai</option>
                            @foreach($employeeTypes as $type)
                                <option value="{{ $type->id }}" {{ (old('employee_type_id', $employee->employee_type_id ?? '') == $type->id) ? 'selected' : '' }}>
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
                        <input type="text" name="nik" value="{{ old('nik', $employee->nik ?? '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">NIY</label>
                        <input type="text" name="niy" value="{{ old('niy', $employee->niy ?? '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">NUPTK</label>
                        <input type="text" name="nuptk" value="{{ old('nuptk', $employee->nuptk ?? '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">NO UKG</label>
                        <input type="text" name="no_ukg" value="{{ old('no_ukg', $employee->no_ukg ?? '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">NRG</label>
                        <input type="text" name="nrg" value="{{ old('nrg', $employee->nrg ?? '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Pangkat / Golongan</label>
                        <input type="text" name="pangkat_golongan" value="{{ old('pangkat_golongan', $employee->pangkat_golongan ?? '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Pendidikan Terakhir</label>
                        <input type="text" name="last_education" value="{{ old('last_education', $employee->last_education ?? '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Jurusan</label>
                        <input type="text" name="major" value="{{ old('major', $employee->major ?? '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Jabatan Utama</label>
                        <input type="text" name="position" value="{{ old('position', $employee->position ?? '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Jabatan Tambahan</label>
                        <input type="text" name="additional_position" value="{{ old('additional_position', $employee->additional_position ?? '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Status Kepegawaian</label>
                        <input type="text" name="employment_status" value="{{ old('employment_status', $employee->employment_status ?? '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Tanggal Mulai Tugas</label>
                        <input type="date" name="task_start_date" value="{{ old('task_start_date', $employee->task_start_date ?? '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Tanggal Diangkat</label>
                        <input type="date" name="appointment_date" value="{{ old('appointment_date', $employee->appointment_date ?? '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Tanggal SK Terakhir</label>
                        <input type="date" name="last_sk_date" value="{{ old('last_sk_date', $employee->last_sk_date ?? '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Nomor SK Terakhir</label>
                        <input type="text" name="last_sk_number" value="{{ old('last_sk_number', $employee->last_sk_number ?? '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Masa Kerja Golongan</label>
                        <input type="text" name="work_period" value="{{ old('work_period', $employee->work_period ?? '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800">
                    </div>

                    <!-- SISTEM ABSENSI -->
                    <div class="md:col-span-2 mt-4 mb-2 border-b pb-2"><h4 class="font-bold text-slate-700 dark:text-slate-300">Sistem Absensi & Catatan</h4></div>
                    @if(auth()->user()->role === 'super_admin')
                    <!-- ID ZKTeco / PIN Fingerprint -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">ID ZKTeco / PIN Fingerprint</label>
                        <input type="text" name="zkteco_uid" value="{{ old('zkteco_uid', $employee->zkteco_uid) }}" readonly
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
                            <option value="Active" {{ old('status', $employee->status) == 'Active' ? 'selected' : '' }}>Aktif</option>
                            <option value="Leave" {{ old('status', $employee->status) == 'Leave' ? 'selected' : '' }}>Cuti</option>
                            <option value="Inactive" {{ old('status', $employee->status) == 'Inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('status')
                            <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Ganti Foto Profil -->
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Ganti Foto Profil</label>
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
                    <button type="submit" class="h-9 px-5 bg-slate-900 dark:bg-slate-50 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center justify-center cursor-pointer">Simpan Perubahan</button>
                </div>
            </form>
        </section>
    </div>
</x-admin-layout>



