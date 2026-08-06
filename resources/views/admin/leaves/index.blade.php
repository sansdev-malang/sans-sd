<x-admin-layout>
    <div class="p-6 space-y-6" x-data="{ showAddModal: {{ $errors->any() ? 'true' : 'false' }}, showEmpDetailModal: false, selectedEmp: null }">

        <!-- SUCCESS/ERROR ALERTS -->
        @if(session('success'))
            <div class="bg-emerald-50 dark:bg-emerald-900/40 border border-emerald-200 dark:border-emerald-900/60 rounded-xl p-4 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <i data-lucide="check" class="w-4 h-4"></i>
                </div>
                <div class="text-left">
                    <h5 class="text-xs font-bold text-slate-800 dark:text-slate-200">Sukses!</h5>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-50 dark:bg-rose-900/40 border border-rose-200 dark:border-rose-900/60 rounded-xl p-4 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                </div>
                <div class="text-left">
                    <h5 class="text-xs font-bold text-slate-800 dark:text-slate-200">Perhatian!</h5>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- HEADER -->
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 font-nasalization">Pengajuan Izin Pegawai</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Daftar pengajuan izin, sakit, dan cuti pegawai. Persetujuan dilakukan oleh HRD Pusat.</p>
            </div>
            <div>
                <button @click="showAddModal = true" class="h-9 px-4 bg-slate-900 dark:bg-slate-100 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-all cursor-pointer flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Input Pengajuan Izin
                </button>
            </div>
        </header>

        <!-- TABLE LIST -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden text-left">
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800 text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="px-6 py-3 text-left">Pegawai</th>
                            <th class="px-6 py-3 text-center">Jenis Izin</th>
                            <th class="px-6 py-3 text-center">Tanggal Mulai</th>
                            <th class="px-6 py-3 text-center">Tanggal Selesai</th>
                            <th class="px-6 py-3 text-left">Alasan</th>
                            <th class="px-6 py-3 text-center">Status HRD</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-900 text-slate-700 dark:text-slate-300 font-medium">
                        @forelse($leaves as $leave)
                            <tr>
                                <td class="px-6 py-4 text-left">
                                    <div class="flex items-center gap-3">
                                        @if($leave->employee && $leave->employee->photo)
                                            <img src="{{ str_contains($leave->employee->photo, 'photos/') ? asset('storage/' . $leave->employee->photo) : asset('storage/photos/' . $leave->employee->photo) }}" class="w-8 h-8 rounded-full object-cover shrink-0 border border-slate-200/50 dark:border-slate-800/40">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-900 flex items-center justify-center text-xs font-bold text-slate-700 dark:text-slate-300 shrink-0">
                                                {{ strtoupper(substr($leave->employee ? $leave->employee->name : 'P', 0, 2)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <span @click="selectedEmp = {
                                                name: '{{ addslashes($leave->employee ? $leave->employee->name : "-") }}',
                                                nuptk_nip_nik: '{{ addslashes($leave->employee ? $leave->employee->nuptk_nip_nik : "-") }}',
                                                subject_position: '{{ addslashes($leave->employee ? $leave->employee->subject_position : "-") }}',
                                                unit: '{{ addslashes($leave->employee ? strtoupper($leave->employee->unit) : "-") }}',
                                                email: '{{ addslashes($leave->employee ? $leave->employee->email : "-") }}',
                                                gender: '{{ addslashes($leave->employee ? $leave->employee->gender : "-") }}',
                                                employment_status: '{{ addslashes($leave->employee ? $leave->employee->employment_status : "-") }}',
                                                photo_url: '{{ $leave->employee && $leave->employee->photo ? (str_contains($leave->employee->photo, "photos/") ? asset("storage/" . $leave->employee->photo) : asset("storage/photos/" . $leave->employee->photo)) : "" }}',
                                                leave_type: '{{ $leave->type }}',
                                                leave_start: '{{ $leave->start_date->format("d M Y") }}',
                                                leave_end: '{{ $leave->end_date->format("d M Y") }}',
                                                leave_reason: '{{ addslashes($leave->reason ?? "-") }}',
                                                leave_attachment: '{{ $leave->attachment ? asset("storage/" . $leave->attachment) : "" }}'
                                            }; showEmpDetailModal = true" class="text-slate-900 dark:text-slate-50 font-bold tracking-tight block cursor-pointer hover:underline hover:text-indigo-600 dark:hover:text-indigo-400">{{ $leave->employee ? $leave->employee->name : '-' }}</span>
                                            <span class="text-[10px] text-slate-405 dark:text-slate-500 font-mono block">NIP: {{ $leave->employee ? $leave->employee->nuptk_nip_nik : '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 dark:bg-slate-900 text-slate-800 dark:text-slate-200 border border-slate-200/45 dark:border-slate-800 uppercase">
                                        {{ $leave->type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center font-mono">
                                    {{ $leave->start_date->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-center font-mono">
                                    {{ $leave->end_date->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-left">
                                    <span class="text-slate-600 dark:text-slate-400 block">{{ $leave->reason ?? '-' }}</span>
                                    @if($leave->attachment)
                                        <div class="mt-1">
                                            <a href="{{ asset('storage/' . $leave->attachment) }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold underline">
                                                <i data-lucide="paperclip" class="w-3 h-3"></i>
                                                Lihat Lampiran
                                            </a>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($leave->status === 'Pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border border-amber-200/30 dark:border-amber-900/30 uppercase animate-pulse">Menunggu</span>
                                    @elseif($leave->status === 'Approved')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200/30 dark:border-emerald-900/30 uppercase">Disetujui</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-400 border border-rose-200/30 dark:border-rose-900/30 uppercase" title="{{ $leave->notes }}">Ditolak</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($leave->status === 'Pending')
                                        <form action="{{ route('leaves.destroy', $leave->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan izin ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="h-7 px-2.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-900/20 dark:hover:bg-rose-900/40 text-rose-700 dark:text-rose-400 text-[10px] font-semibold rounded border border-rose-200/30 dark:border-rose-900/30 transition-all cursor-pointer flex items-center gap-1">
                                                <i data-lucide="x" class="w-3 h-3"></i>
                                                Batal
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-mono italic">Sudah Diproses</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-slate-500 dark:text-slate-400">
                                    Belum ada data pengajuan izin.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ADD MODAL -->
        <template x-teleport="body">
            <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" style="display: none; margin-top: 0px !important; z-index: 9999;">
            <div @click.outside="showAddModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl w-full max-w-md p-6 text-left">
                <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-900 pb-3 mb-4">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-50">Input Pengajuan Izin Pegawai</h3>
                    <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('leaves.store') }}" enctype="multipart/form-data" class="space-y-4 text-xs">
                    @csrf
                    
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Pilih Pegawai</label>
                        <div x-data="{
                            open: false,
                            search: '',
                            selectedId: '{{ old('employee_id') ?? '' }}',
                            selectedName: '{{ old('employee_id') ? addslashes(App\Models\Employee::find(old('employee_id'))?->name . ' (' . (App\Models\Employee::find(old('employee_id'))?->nuptk_nip_nik ?? '-') . ')') : '' }}',
                            employees: [
                                @foreach($employees as $emp)
                                { id: '{{ $emp->id }}', name: '{{ addslashes($emp->name) }}', nip: '{{ $emp->nuptk_nip_nik ?? '-' }}' },
                                @endforeach
                            ],
                            get filteredEmployees() {
                                if (this.search === '') return this.employees;
                                return this.employees.filter(emp => emp.name.toLowerCase().includes(this.search.toLowerCase()) || emp.nip.includes(this.search));
                            },
                            select(emp) {
                                this.selectedId = emp.id;
                                this.selectedName = emp.name + ' (' + emp.nip + ')';
                                this.open = false;
                                this.search = '';
                            }
                        }" class="relative">
                            <!-- Hidden input for form submission -->
                            <input type="hidden" name="employee_id" :value="selectedId" required>

                            <!-- Toggle button -->
                            <div @click="open = !open" 
                                 class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 cursor-pointer flex justify-between items-center focus:outline-none">
                                <span x-text="selectedName || '-- Pilih Pegawai --'"></span>
                                <i data-lucide="chevrons-up-down" class="w-4 h-4 text-slate-400"></i>
                            </div>

                            <!-- Dropdown panel -->
                            <div x-show="open" @click.outside="open = false" 
                                 class="absolute z-50 w-full mt-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg shadow-lg overflow-hidden text-left"
                                  x-transition style="display: none;">
                                <!-- Search input inside dropdown -->
                                <div class="p-2 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50">
                                    <input type="text" x-model="search" placeholder="Ketik nama/NIP untuk mencari..." 
                                           class="w-full px-3 py-1.5 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-md focus:outline-none focus:border-indigo-500 text-slate-900 dark:text-slate-100">
                                </div>
                                
                                <!-- List items -->
                                <ul class="max-h-48 overflow-y-auto py-1 divide-y divide-slate-100 dark:divide-slate-800/40">
                                    <template x-for="emp in filteredEmployees" :key="emp.id">
                                        <li @click="select(emp)" 
                                            class="px-3 py-2 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer text-slate-800 dark:text-slate-200 text-xs flex justify-between items-center transition-colors">
                                            <span x-text="emp.name" class="font-semibold uppercase"></span>
                                            <span x-text="emp.nip" class="text-[10px] text-slate-400 dark:text-slate-500 font-mono"></span>
                                        </li>
                                    </template>
                                    <li x-show="filteredEmployees.length === 0" class="px-3 py-3 text-slate-400 dark:text-slate-500 italic text-center text-xs">
                                        Pegawai tidak ditemukan
                                    </li>
                                </ul>
                            </div>
                        </div>
                        @error('employee_id')
                            <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Jenis Izin</label>
                        <select name="type" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-880 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500">
                            <option value="Sakit" {{ old('type') === 'Sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="Izin" {{ old('type') === 'Izin' ? 'selected' : '' }}>Izin (Pribadi/Penting)</option>
                            <option value="Cuti" {{ old('type') === 'Cuti' ? 'selected' : '' }}>Cuti Tahunan</option>
                            <option value="Dinas" {{ old('type') === 'Dinas' ? 'selected' : '' }}>Tugas Dinas (Tetap dapat Bonus)</option>
                        </select>
                        @error('type')
                            <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Mulai Tanggal</label>
                            <input type="date" name="start_date" value="{{ old('start_date') }}" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-880 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500 font-mono">
                            @error('start_date')
                                <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Selesai Tanggal</label>
                            <input type="date" name="end_date" value="{{ old('end_date') }}" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-880 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500 font-mono">
                            @error('end_date')
                                <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Alasan / Keterangan</label>
                        <textarea name="reason" rows="2" placeholder="Tuliskan keterangan detail alasan pengajuan..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-880 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500">{{ old('reason') }}</textarea>
                        @error('reason')
                            <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">File Lampiran (Surat Dokter / Bukti Pendukung)</label>
                        <input type="file" name="attachment" accept=".pdf,.png,.jpg,.jpeg,.doc,.docx" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-880 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500">
                        <span class="text-[10px] text-slate-400 block mt-1">Format: PDF, PNG, JPG, JPEG, DOC, DOCX. Maksimal 2MB.</span>
                        @error('attachment')
                            <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-2.5 pt-4 border-t border-slate-100 dark:border-slate-900 justify-end">
                        <button type="button" @click="showAddModal = false" class="h-9 px-4 bg-slate-50 hover:bg-slate-100 dark:bg-slate-900 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 transition-all cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="h-9 px-4 bg-slate-900 dark:bg-slate-100 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-all cursor-pointer">
                            Kirim Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </template>

        <!-- MODAL DETAIL PEGAWAI -->
        <template x-teleport="body">
            <div x-show="showEmpDetailModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs text-left" style="display: none; margin-top: 0px !important; z-index: 9999;">
            <div @click.outside="showEmpDetailModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl max-w-md w-full overflow-hidden text-xs">
                <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-900/50">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-55 font-nasalization flex items-center gap-2">
                        <i data-lucide="user" class="w-4 h-4 text-indigo-600 dark:text-indigo-400"></i>
                        Profil Pegawai
                    </h3>
                    <button @click="showEmpDetailModal = false" class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-300">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                <div class="p-5 space-y-6">
                    <div class="flex items-center gap-4">
                        <!-- Photo / Initials -->
                        <div class="shrink-0">
                            <template x-if="selectedEmp && selectedEmp.photo_url">
                                <img :src="selectedEmp.photo_url" class="w-16 h-16 rounded-xl object-cover border border-slate-200 dark:border-slate-800 shadow-sm">
                            </template>
                            <template x-if="!selectedEmp || !selectedEmp.photo_url">
                                <div class="w-16 h-16 rounded-xl bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 font-bold flex items-center justify-center text-2xl uppercase shadow-sm">
                                    <span x-text="selectedEmp ? selectedEmp.name.substring(0,2) : ''"></span>
                                </div>
                            </template>
                        </div>
                        <div class="space-y-1">
                            <h4 class="text-sm font-bold text-slate-900 dark:text-slate-50 font-nasalization" x-text="selectedEmp ? selectedEmp.name : ''"></h4>
                            <p class="text-slate-400 dark:text-slate-500 font-mono" x-text="selectedEmp ? 'NIP/NUPTK: ' + (selectedEmp.nuptk_nip_nik || '-') : ''"></p>
                            <span class="inline-flex px-2 py-0.5 rounded text-[9px] font-bold bg-indigo-50 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 uppercase" x-text="selectedEmp ? selectedEmp.subject_position : ''"></span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-[11px] pt-4 border-t border-slate-100 dark:border-slate-800">
                        <div>
                            <span class="block text-slate-400 text-[9px] uppercase font-semibold">Unit Kerja</span>
                            <span class="font-bold text-slate-700 dark:text-slate-200 uppercase" x-text="selectedEmp ? selectedEmp.unit : ''"></span>
                        </div>
                        <div>
                            <span class="block text-slate-400 text-[9px] uppercase font-semibold">Email</span>
                            <span class="font-medium text-slate-700 dark:text-slate-200" x-text="selectedEmp ? selectedEmp.email : ''"></span>
                        </div>
                        <div>
                            <span class="block text-slate-400 text-[9px] uppercase font-semibold">Jenis Kelamin</span>
                            <span class="font-bold text-slate-700 dark:text-slate-200" x-text="selectedEmp ? (selectedEmp.gender === 'Male' ? 'Laki-laki' : 'Perempuan') : ''"></span>
                        </div>
                        <div>
                            <span class="block text-slate-400 text-[9px] uppercase font-semibold">Status Pegawai</span>
                            <span class="font-bold text-slate-700 dark:text-slate-200" x-text="selectedEmp ? selectedEmp.employment_status : ''"></span>
                        </div>
                    </div>

                    <!-- Detail Pengajuan Izin -->
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-3">
                        <h4 class="font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider text-[10px]">Detail Pengajuan Izin</h4>
                        <div class="grid grid-cols-2 gap-4 text-[11px]">
                            <div>
                                <span class="block text-slate-400 text-[9px] uppercase font-semibold">Jenis Izin</span>
                                <span class="inline-flex px-2 py-0.5 rounded text-[9px] font-bold bg-amber-50 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400 border border-amber-200/30 dark:border-amber-900/30 uppercase" x-text="selectedEmp ? selectedEmp.leave_type : ''"></span>
                            </div>
                            <div>
                                <span class="block text-slate-400 text-[9px] uppercase font-semibold">Rentang Tanggal</span>
                                <span class="font-medium text-slate-700 dark:text-slate-200" x-text="selectedEmp ? selectedEmp.leave_start + ' - ' + selectedEmp.leave_end : ''"></span>
                            </div>
                            <div class="col-span-2">
                                <span class="block text-slate-400 text-[9px] uppercase font-semibold">Alasan</span>
                                <span class="font-medium text-slate-700 dark:text-slate-200" x-text="selectedEmp ? selectedEmp.leave_reason : ''"></span>
                            </div>
                            <template x-if="selectedEmp && selectedEmp.leave_attachment">
                                <div class="col-span-2">
                                    <span class="block text-slate-400 text-[9px] uppercase font-semibold">Lampiran</span>
                                    <a :href="selectedEmp.leave_attachment" target="_blank" class="inline-flex items-center gap-1 text-[10px] text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-bold underline mt-0.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l8.57-8.57A4 4 0 1 1 18 8.84l-8.59 8.57a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                                        Lihat Lampiran Dokumen
                                    </a>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="p-5 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                    <button @click="showEmpDetailModal = false" class="h-9 px-4 bg-slate-900 dark:bg-slate-100 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Tutup</button>
                </div>
            </div>
        </template>
    </div>
</x-admin-layout>
