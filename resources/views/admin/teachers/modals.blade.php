<div x-data><template x-teleport="body">
    <!-- ===== MODAL TAMBAH PEGAWAI ===== -->
    <div x-show="showCreateModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="create-modal-title" role="dialog" aria-modal="true" style="margin-top: 0px !important; z-index: 9999;">
        <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity" @click="if('{{ $errors->any() }}') { window.location.href = window.location.pathname; } else { showCreateModal = false; document.getElementById('create-teacher-form').reset(); }"></div>
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative transform overflow-hidden rounded-xl bg-white dark:bg-slate-950 text-left shadow-2xl transition-all sm:my-8 w-full sm:max-w-3xl border border-slate-200 dark:border-slate-800">
                <div class="flex flex-col max-h-[85vh] text-left">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-800 shrink-0">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-slate-50" id="create-modal-title">Tambah Guru Baru</h3>
                        <button type="button" @click="if('{{ $errors->any() }}') { window.location.href = window.location.pathname; } else { showCreateModal = false; document.getElementById('create-teacher-form').reset(); }" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-800 text-slate-500 cursor-pointer transition-colors"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
                    </div>
                    <div class="px-6 py-4 overflow-y-auto custom-scrollbar flex-1">
                        <form id="create-teacher-form" action="{{ route('teachers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 text-xs">
                @csrf

                <!-- Grid Form -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                                                            <!-- Gelar & Nama -->
                    <div class="col-span-full grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Gelar Depan -->
                        <div>
                            <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Gelar Depan</label>
                            <input type="text" name="front_title" value="{{ old('front_title') }}" placeholder="Dr., Ir."
                                class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('front_title') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                            @error('front_title')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
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
                                class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('back_title') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                            @error('back_title')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Contoh: nama@domain.com" 
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
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('birth_place') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('birth_place')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Tanggal Lahir</label>
                        <input type="date" name="birth_date" value="{{ old('birth_date', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('birth_date') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('birth_date')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Jenis Kelamin</label>
                        <select name="gender" required class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800 cursor-pointer">
                            <option value="Male" {{ old('gender', '') == 'Male' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Female" {{ old('gender', '') == 'Female' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    
                            @error('gender')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Alamat</label>
                        <input type="text" name="address" value="{{ old('address', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('address') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('address')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">No. HP / WA</label>
                        <input type="text" name="phone" value="{{ old('phone', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('phone') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('phone')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>

                    <!-- DATA KEPEGAWAIAN -->
                    <div class="md:col-span-2 mt-4 mb-2 border-b pb-2"><h4 class="font-bold text-slate-700 dark:text-slate-300">Data Kepegawaian</h4></div>

                    

                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">NIK</label>
                        <input type="text" name="nik" value="{{ old('nik', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('nik') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('nik')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">NIY</label>
                        <input type="text" name="niy" value="{{ old('niy', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('niy') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('niy')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">NUPTK</label>
                        <input type="text" name="nuptk" value="{{ old('nuptk', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('nuptk') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('nuptk')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">NO UKG</label>
                        <input type="text" name="no_ukg" value="{{ old('no_ukg', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('no_ukg') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('no_ukg')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">NRG</label>
                        <input type="text" name="nrg" value="{{ old('nrg', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('nrg') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('nrg')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Pangkat / Golongan</label>
                        <input type="text" name="pangkat_golongan" value="{{ old('pangkat_golongan', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('pangkat_golongan') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('pangkat_golongan')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Pendidikan Terakhir</label>
                        <input type="text" name="last_education" value="{{ old('last_education', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('last_education') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('last_education')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Jurusan</label>
                        <input type="text" name="major" value="{{ old('major', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('major') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('major')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Jabatan Utama</label>
                        <input type="text" name="position" value="{{ old('position', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('position') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('position')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Jabatan Tambahan</label>
                        <input type="text" name="additional_position" value="{{ old('additional_position', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('additional_position') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('additional_position')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Status Kepegawaian</label>
                        <input type="text" name="employment_status" value="{{ old('employment_status', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('employment_status') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('employment_status')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Tanggal Mulai Tugas</label>
                        <input type="date" name="task_start_date" value="{{ old('task_start_date', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('task_start_date') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('task_start_date')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Tanggal Diangkat</label>
                        <input type="date" name="appointment_date" value="{{ old('appointment_date', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('appointment_date') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('appointment_date')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Tanggal SK Terakhir</label>
                        <input type="date" name="last_sk_date" value="{{ old('last_sk_date', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('last_sk_date') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('last_sk_date')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Nomor SK Terakhir</label>
                        <input type="text" name="last_sk_number" value="{{ old('last_sk_number', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('last_sk_number') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('last_sk_number')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Masa Kerja Golongan</label>
                        <input type="text" name="work_period" value="{{ old('work_period', '') }}" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('work_period') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('work_period')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>

                    <!-- SISTEM ABSENSI -->
                    <div class="md:col-span-2 mt-4 mb-2 border-b pb-2"><h4 class="font-bold text-slate-700 dark:text-slate-300">Sistem Absensi & Catatan</h4></div>
                    @if(auth()->user()->role === 'super_admin')
                    <!-- ID ZKTeco / PIN Fingerprint -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">ID ZKTeco / PIN Fingerprint</label>
                        <input type="text" name="zkteco_uid" value="{{ old('zkteco_uid') }}" readonly
                            class="w-full h-9 px-3 font-mono bg-slate-100 dark:bg-slate-800 border rounded-lg text-slate-500 dark:text-slate-400 focus:outline-none cursor-not-allowed @error('zkteco_uid') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
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
                        <span class="text-[10px] text-slate-400 block mt-1">Format: JPG, JPEG, PNG, GIF, SVG. Maksimal 2MB.</span>
                        @error('photo')
                            <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                </form>
                    </div>
                    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 flex justify-end gap-3 shrink-0">
                        <button type="button" @click="if('{{ $errors->any() }}') { window.location.href = window.location.pathname; } else { showCreateModal = false; document.getElementById('create-teacher-form').reset(); }" class="h-9 px-4 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg hover:bg-slate-50 dark:hover:bg-slate-900 flex items-center justify-center transition-all cursor-pointer">Batal</button>
                        <button type="submit" form="create-teacher-form" class="h-9 px-5 bg-slate-900 dark:bg-slate-50 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center justify-center cursor-pointer">Simpan Pegawai</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template></div>

<div x-data><template x-teleport="body">
    <!-- ===== MODAL EDIT PEGAWAI ===== -->
    <div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="edit-modal-title" role="dialog" aria-modal="true" style="margin-top: 0px !important; z-index: 9999;">
        <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity" @click="if('{{ $errors->any() }}') { window.location.href = window.location.pathname; } else { showEditModal = false; document.getElementById('edit-teacher-form').reset(); }"></div>
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative transform overflow-hidden rounded-xl bg-white dark:bg-slate-950 text-left shadow-2xl transition-all sm:my-8 w-full sm:max-w-3xl border border-slate-200 dark:border-slate-800">
                <div class="flex flex-col max-h-[85vh] text-left">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-800 shrink-0">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-slate-50" id="edit-modal-title">Edit Guru</h3>
                        <button type="button" @click="if('{{ $errors->any() }}') { window.location.href = window.location.pathname; } else { showEditModal = false; document.getElementById('edit-teacher-form').reset(); }" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-800 text-slate-500 cursor-pointer transition-colors"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
                    </div>
                    <div class="px-6 py-4 overflow-y-auto custom-scrollbar flex-1">
                        <form id="edit-teacher-form" method="POST" :action="selectedTeacher ? '{{ url('teachers') }}/' + selectedTeacher.id : ( '{{ old('edit_id') }}' ? '{{ url('teachers') }}/' + '{{ old('edit_id') }}' : '#' )" enctype="multipart/form-data" class="space-y-6 text-xs">
                <input type="hidden" name="edit_id" :value="selectedTeacher ? selectedTeacher.id : '{{ old('edit_id') }}'">
                @csrf
                @method('PUT')
                <input type="hidden" name="_method" value="PUT">

                                <!-- Photo Preview Section -->
                <div class="flex items-center gap-4 bg-slate-50 dark:bg-slate-900 p-4 rounded-xl border border-slate-150 dark:border-slate-850">
                    <template x-if="selectedTeacher && selectedTeacher.photo">
                        <img id="photo-preview" :src="selectedTeacher.photo.includes('photos/') ? '/storage/' + selectedTeacher.photo : '/storage/photos/' + selectedTeacher.photo" class="w-16 h-16 rounded-full object-cover border-2 border-white dark:border-slate-800 shadow-sm" :alt="selectedTeacher.name">
                    </template>
                    <template x-if="!selectedTeacher || !selectedTeacher.photo">
                        <div id="photo-fallback" class="w-16 h-16 rounded-full bg-slate-200 dark:bg-slate-800 flex items-center justify-center text-lg font-bold text-slate-700 dark:text-slate-300 border-2 border-white dark:border-slate-800 uppercase" x-text="selectedTeacher && selectedTeacher.raw_name ? selectedTeacher.raw_name.substring(0, 2).toUpperCase() : 'XX'">
                        </div>
                    </template>
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
                            <input type="text" name="front_title" :value="selectedTeacher ? selectedTeacher.front_title : '{{ old('front_title') }}'" placeholder="Dr., Ir."
                                class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('front_title') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                            @error('front_title')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                        <!-- Nama Lengkap -->
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Nama Lengkap</label>
                            <input type="text" name="name" :value="selectedTeacher ? selectedTeacher.name : '{{ old('name') }}'" placeholder="Eko Wibowo" required
                                class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('name') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                            @error('name')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                        <!-- Gelar Belakang -->
                        <div>
                            <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Gelar Belakang</label>
                            <input type="text" name="back_title" :value="selectedTeacher ? selectedTeacher.back_title : '{{ old('back_title') }}'" placeholder="S.Pd., M.Kom."
                                class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('back_title') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                            @error('back_title')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Alamat Email</label>
                        <input type="email" name="email" :value="selectedTeacher ? selectedTeacher.email : '{{ old('email') }}'" placeholder="Contoh: nama@domain.com" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('email') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                        @error('email')
                            <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                                        <!-- DATA DIRI -->
                    <div class="md:col-span-2 mt-4 mb-2 border-b pb-2"><h4 class="font-bold text-slate-700 dark:text-slate-300">Data Diri</h4></div>
                    
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Tempat Lahir</label>
                        <input type="text" name="birth_place" :value="selectedTeacher ? selectedTeacher.birth_place : '{{ old('birth_place') }}'" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('birth_place') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('birth_place')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Tanggal Lahir</label>
                        <input type="date" name="birth_date" :value="selectedTeacher ? selectedTeacher.birth_date : '{{ old('birth_date') }}'" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('birth_date') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('birth_date')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Jenis Kelamin</label>
                        <select name="gender" required class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 border-slate-200 dark:border-slate-800 cursor-pointer">
                            <option value="Male" :selected="(selectedTeacher ? selectedTeacher.gender : '{{ old('gender') }}') == 'Male'">Laki-laki</option>
                            <option value="Female" :selected="(selectedTeacher ? selectedTeacher.gender : '{{ old('gender') }}') == 'Female'">Perempuan</option>
                        </select>
                    
                            @error('gender')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Alamat</label>
                        <input type="text" name="address" :value="selectedTeacher ? selectedTeacher.address : '{{ old('address') }}'" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('address') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('address')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">No. HP / WA</label>
                        <input type="text" name="phone" :value="selectedTeacher ? selectedTeacher.phone : '{{ old('phone') }}'" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('phone') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('phone')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>

                    <!-- DATA KEPEGAWAIAN -->
                    <div class="md:col-span-2 mt-4 mb-2 border-b pb-2"><h4 class="font-bold text-slate-700 dark:text-slate-300">Data Kepegawaian</h4></div>

                    

                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">NIK</label>
                        <input type="text" name="nik" :value="selectedTeacher ? selectedTeacher.nik : '{{ old('nik') }}'" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('nik') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('nik')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">NIY</label>
                        <input type="text" name="niy" :value="selectedTeacher ? selectedTeacher.niy : '{{ old('niy') }}'" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('niy') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('niy')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">NUPTK</label>
                        <input type="text" name="nuptk" :value="selectedTeacher ? selectedTeacher.nuptk : '{{ old('nuptk') }}'" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('nuptk') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('nuptk')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">NO UKG</label>
                        <input type="text" name="no_ukg" :value="selectedTeacher ? selectedTeacher.no_ukg : '{{ old('no_ukg') }}'" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('no_ukg') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('no_ukg')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">NRG</label>
                        <input type="text" name="nrg" :value="selectedTeacher ? selectedTeacher.nrg : '{{ old('nrg') }}'" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('nrg') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('nrg')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Pangkat / Golongan</label>
                        <input type="text" name="pangkat_golongan" :value="selectedTeacher ? selectedTeacher.pangkat_golongan : '{{ old('pangkat_golongan') }}'" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('pangkat_golongan') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('pangkat_golongan')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Pendidikan Terakhir</label>
                        <input type="text" name="last_education" :value="selectedTeacher ? selectedTeacher.last_education : '{{ old('last_education') }}'" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('last_education') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('last_education')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Jurusan</label>
                        <input type="text" name="major" :value="selectedTeacher ? selectedTeacher.major : '{{ old('major') }}'" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('major') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('major')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Jabatan Utama</label>
                        <input type="text" name="position" :value="selectedTeacher ? selectedTeacher.position : '{{ old('position') }}'" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('position') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('position')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Jabatan Tambahan</label>
                        <input type="text" name="additional_position" :value="selectedTeacher ? selectedTeacher.additional_position : '{{ old('additional_position') }}'" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('additional_position') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('additional_position')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Status Kepegawaian</label>
                        <input type="text" name="employment_status" :value="selectedTeacher ? selectedTeacher.employment_status : '{{ old('employment_status') }}'" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('employment_status') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('employment_status')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Tanggal Mulai Tugas</label>
                        <input type="date" name="task_start_date" :value="selectedTeacher ? selectedTeacher.task_start_date : '{{ old('task_start_date') }}'" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('task_start_date') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('task_start_date')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Tanggal Diangkat</label>
                        <input type="date" name="appointment_date" :value="selectedTeacher ? selectedTeacher.appointment_date : '{{ old('appointment_date') }}'" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('appointment_date') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('appointment_date')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Tanggal SK Terakhir</label>
                        <input type="date" name="last_sk_date" :value="selectedTeacher ? selectedTeacher.last_sk_date : '{{ old('last_sk_date') }}'" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('last_sk_date') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('last_sk_date')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Nomor SK Terakhir</label>
                        <input type="text" name="last_sk_number" :value="selectedTeacher ? selectedTeacher.last_sk_number : '{{ old('last_sk_number') }}'" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('last_sk_number') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('last_sk_number')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">Masa Kerja Golongan</label>
                        <input type="text" name="work_period" :value="selectedTeacher ? selectedTeacher.work_period : '{{ old('work_period') }}'" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 @error('work_period') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
                    
                            @error('work_period')
                                <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                            @enderror
                        </div>

                    <!-- SISTEM ABSENSI -->
                    <div class="md:col-span-2 mt-4 mb-2 border-b pb-2"><h4 class="font-bold text-slate-700 dark:text-slate-300">Sistem Absensi & Catatan</h4></div>
                    @if(auth()->user()->role === 'super_admin')
                    <!-- ID ZKTeco / PIN Fingerprint -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-1">ID ZKTeco / PIN Fingerprint</label>
                        <input type="text" name="zkteco_uid" :value="selectedTeacher ? selectedTeacher.zkteco_uid : '{{ old('zkteco_uid') }}'" readonly
                            class="w-full h-9 px-3 font-mono bg-slate-100 dark:bg-slate-800 border rounded-lg text-slate-500 dark:text-slate-400 focus:outline-none cursor-not-allowed @error('zkteco_uid') border-rose-500 focus:ring-rose-200 dark:focus:ring-rose-950/40 @else border-slate-200 dark:border-slate-800 focus:ring-slate-100 @enderror">
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
                            <option value="Active" :selected="(selectedTeacher ? selectedTeacher.status : '{{ old('status') }}') == 'Active'">Aktif</option>
                            <option value="Leave" :selected="(selectedTeacher ? selectedTeacher.status : '{{ old('status') }}') == 'Leave'">Cuti</option>
                            <option value="Inactive" :selected="(selectedTeacher ? selectedTeacher.status : '{{ old('status') }}') == 'Inactive'">Nonaktif</option>
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
                        <span class="text-[10px] text-slate-400 block mt-1">Format: JPG, JPEG, PNG, GIF, SVG. Maksimal 2MB.</span>
                        @error('photo')
                            <span class="text-[10px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                </form>
                    </div>
                    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 flex justify-end gap-3 shrink-0">
                        <button type="button" @click="if('{{ $errors->any() }}') { window.location.href = window.location.pathname; } else { showEditModal = false; document.getElementById('edit-teacher-form').reset(); }" class="h-9 px-4 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg hover:bg-slate-50 dark:hover:bg-slate-900 flex items-center justify-center transition-all cursor-pointer">Batal</button>
                        <button type="submit" form="edit-teacher-form" class="h-9 px-5 bg-slate-900 dark:bg-slate-50 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center justify-center cursor-pointer">Simpan Perubahan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template></div>
<style>
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #475569; }
</style>
