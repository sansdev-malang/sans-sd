<x-admin-layout>
<div x-data="{ showEditModal: {{ $errors->any() ? 'true' : 'false' }} }" class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Profil Pegawai</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Informasi detail data diri dan kepegawaian Anda.</p>
        </div>
        <button @click="showEditModal = true" class="inline-flex items-center gap-2 justify-center rounded-lg bg-slate-900 dark:bg-slate-100 px-4 py-2.5 text-sm font-semibold text-white dark:text-slate-900 shadow-sm hover:bg-slate-800 dark:hover:bg-slate-200 transition-colors cursor-pointer">
            <i data-lucide="edit-3" class="w-4 h-4"></i> Edit Profil
        </button>
    </div>

    
    

    <!-- Tampilan Data Profil -->
    <div class="bg-white dark:bg-slate-900 shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl overflow-hidden">
        <div class="px-6 py-6 sm:px-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 mb-8 pb-8 border-b border-slate-100 dark:border-slate-800">
                <div class="w-24 h-24 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 shrink-0 overflow-hidden shadow-sm">
                    @if($employee->photo)
                        <img src="{{ Storage::url($employee->photo) }}" alt="Foto {{ $employee->name }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-2xl font-bold uppercase">{{ substr($employee->name, 0, 2) }}</span>
                    @endif
                </div>
                <div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-slate-50">
                        {{ $employee->front_title ? $employee->front_title . ' ' : '' }}{{ $employee->name }}{{ $employee->back_title ? ', ' . $employee->back_title : '' }}
                    </h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $employee->email ?? 'Email belum diisi' }}</p>
                    <div class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 border border-indigo-200/50 dark:border-indigo-800/40">
                        {{ $employee->employeeType->name ?? 'Tipe Pegawai Tidak Diketahui' }}
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-10">
                <!-- Data Diri -->
                <div>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100 mb-4 flex items-center gap-2">
                        <i data-lucide="contact" class="w-4 h-4 text-slate-400"></i> Data Diri
                    </h4>
                    <dl class="space-y-4 text-sm">
                        <div class="grid grid-cols-3 gap-4">
                            <dt class="font-medium text-slate-500 dark:text-slate-400">Tempat Lahir</dt>
                            <dd class="col-span-2 text-slate-900 dark:text-slate-200">{{ $employee->birth_place ?: '-' }}</dd>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <dt class="font-medium text-slate-500 dark:text-slate-400">Tanggal Lahir</dt>
                            <dd class="col-span-2 text-slate-900 dark:text-slate-200">{{ $employee->birth_date ? \Carbon\Carbon::parse($employee->birth_date)->translatedFormat('d F Y') : '-' }}</dd>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <dt class="font-medium text-slate-500 dark:text-slate-400">Jenis Kelamin</dt>
                            <dd class="col-span-2 text-slate-900 dark:text-slate-200">{{ $employee->gender === 'Male' || $employee->gender === 'L' ? 'Laki-laki' : ($employee->gender === 'Female' || $employee->gender === 'P' ? 'Perempuan' : '-') }}</dd>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <dt class="font-medium text-slate-500 dark:text-slate-400">No. HP / WA</dt>
                            <dd class="col-span-2 text-slate-900 dark:text-slate-200">{{ $employee->phone ?: '-' }}</dd>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <dt class="font-medium text-slate-500 dark:text-slate-400">Alamat</dt>
                            <dd class="col-span-2 text-slate-900 dark:text-slate-200">{{ $employee->address ?: '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Data Kepegawaian & Identitas -->
                <div>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100 mb-4 flex items-center gap-2">
                        <i data-lucide="briefcase" class="w-4 h-4 text-slate-400"></i> Data Kepegawaian
                    </h4>
                    <dl class="space-y-4 text-sm">
                        <div class="grid grid-cols-3 gap-4">
                            <dt class="font-medium text-slate-500 dark:text-slate-400">NIK</dt>
                            <dd class="col-span-2 text-slate-900 dark:text-slate-200">{{ $employee->nik ?: '-' }}</dd>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <dt class="font-medium text-slate-500 dark:text-slate-400">NIY</dt>
                            <dd class="col-span-2 text-slate-900 dark:text-slate-200">{{ $employee->niy ?: '-' }}</dd>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <dt class="font-medium text-slate-500 dark:text-slate-400">NUPTK</dt>
                            <dd class="col-span-2 text-slate-900 dark:text-slate-200">{{ $employee->nuptk ?: '-' }}</dd>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <dt class="font-medium text-slate-500 dark:text-slate-400">NRG</dt>
                            <dd class="col-span-2 text-slate-900 dark:text-slate-200">{{ $employee->nrg ?: '-' }}</dd>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <dt class="font-medium text-slate-500 dark:text-slate-400">Pendidikan</dt>
                            <dd class="col-span-2 text-slate-900 dark:text-slate-200">{{ $employee->last_education ?: '-' }} {{ $employee->major ? ' - ' . $employee->major : '' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form Edit -->
    <template x-teleport="body">
        <div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="z-index: 9999;">
            <div x-show="showEditModal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="if('{{ $errors->any() }}') { window.location.href = window.location.pathname; } else { showEditModal = false; $refs.profileForm.reset(); }"></div>
            
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="showEditModal" x-transition.opacity.scale.95 class="relative transform overflow-hidden rounded-xl bg-white dark:bg-slate-900 text-left shadow-2xl transition-all sm:my-8 w-full sm:max-w-3xl border border-slate-200 dark:border-slate-800">
                    <div class="flex flex-col max-h-[85vh] text-left">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-800 shrink-0">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-50" id="modal-title">Edit Profil Pegawai</h3>
                            <button type="button" @click="if('{{ $errors->any() }}') { window.location.href = window.location.pathname; } else { showEditModal = false; $refs.profileForm.reset(); }" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-800 text-slate-500 cursor-pointer transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                            </button>
                        </div>
                        
                        <div class="px-6 py-4 overflow-y-auto custom-scrollbar flex-1">
                            <form x-ref="profileForm" action="{{ route('my-employee-profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6 text-xs">
                                @csrf
                                @method('PUT')

                                <!-- Form input container -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                
                                    <!-- Foto Profil -->
                                    <div class="col-span-full border border-slate-200 dark:border-slate-800 rounded-lg p-4 flex flex-col sm:flex-row items-center gap-6">
                                        <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 shrink-0 overflow-hidden">
                                            @if($employee->photo)
                                                <img src="{{ Storage::url($employee->photo) }}" alt="Foto" class="w-full h-full object-cover">
                                            @else
                                                <span class="text-xl font-bold uppercase">{{ substr($employee->name, 0, 2) }}</span>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Ganti Foto Profil</label>
                                            <input type="file" name="photo" accept="image/*" class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/30 dark:file:text-indigo-400 cursor-pointer">
                                            @error('photo') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                            <p class="text-[10px] text-slate-400 mt-1">Format: JPG, PNG. Maksimal: 2MB.</p>
                                        </div>
                                    </div>

                                    <!-- Gelar & Nama -->
                                    <div class="col-span-full grid grid-cols-1 md:grid-cols-4 gap-4">
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Gelar Depan</label>
                                            <input type="text" name="front_title" value="{{ old('front_title', $employee->front_title) }}" class="w-full h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                                            @error('front_title') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Nama Lengkap</label>
                                            <input type="text" name="name" value="{{ old('name', $employee->name) }}" required class="w-full h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                                            @error('name') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Gelar Belakang</label>
                                            <input type="text" name="back_title" value="{{ old('back_title', $employee->back_title) }}" class="w-full h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                                            @error('back_title') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Alamat Email</label>
                                        <input type="email" name="email" value="{{ old('email', $employee->email) }}" class="w-full h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                                        @error('email') <span class="text-[10px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="md:col-span-2 mt-4 mb-2 border-b border-slate-200 dark:border-slate-800 pb-2"><h4 class="font-bold text-slate-700 dark:text-slate-300">Data Diri</h4></div>
                                    
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Tempat Lahir</label>
                                        <input type="text" name="birth_place" value="{{ old('birth_place', $employee->birth_place) }}" class="w-full h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Tanggal Lahir</label>
                                        <input type="date" name="birth_date" value="{{ old('birth_date', $employee->birth_date) }}" class="w-full h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Jenis Kelamin</label>
                                        <select name="gender" required class="w-full h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                                            <option value="Male" {{ old('gender', $employee->gender) == 'Male' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="Female" {{ old('gender', $employee->gender) == 'Female' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">No. HP / WA</label>
                                        <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}" class="w-full h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                                    </div>
                                    <div class="col-span-full">
                                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Alamat</label>
                                        <input type="text" name="address" value="{{ old('address', $employee->address) }}" class="w-full h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                                    </div>

                                    <div class="md:col-span-2 mt-4 mb-2 border-b border-slate-200 dark:border-slate-800 pb-2"><h4 class="font-bold text-slate-700 dark:text-slate-300">Data Kepegawaian</h4></div>
                                    
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">NIK</label>
                                        <input type="text" name="nik" value="{{ old('nik', $employee->nik) }}" class="w-full h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">NIY</label>
                                        <input type="text" name="niy" value="{{ old('niy', $employee->niy) }}" class="w-full h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">NUPTK</label>
                                        <input type="text" name="nuptk" value="{{ old('nuptk', $employee->nuptk) }}" class="w-full h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">NRG</label>
                                        <input type="text" name="nrg" value="{{ old('nrg', $employee->nrg) }}" class="w-full h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                                    </div>
                                </div>
                                <div class="mt-8 flex items-center justify-end gap-x-3 pt-5 border-t border-slate-200 dark:border-slate-800">
                                    <button type="button" @click="if('{{ $errors->any() }}') { window.location.href = window.location.pathname; } else { showEditModal = false; $refs.profileForm.reset(); }" class="inline-flex justify-center rounded-lg px-4 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">Batal</button>
                                    <button type="submit" class="inline-flex justify-center rounded-lg bg-slate-900 dark:bg-slate-100 px-6 py-2.5 text-sm font-semibold text-white dark:text-slate-900 shadow-sm hover:bg-slate-800 dark:hover:bg-slate-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 cursor-pointer">Simpan Perubahan</button>
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