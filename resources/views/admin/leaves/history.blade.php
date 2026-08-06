<x-admin-layout>
    <div class="p-6 space-y-6" x-data="{ showEmpDetailModal: false, selectedEmp: null }">

        <!-- HEADER -->
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 font-nasalization">Riwayat Izin & Cuti</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Daftar seluruh riwayat pengajuan izin, sakit, dinas, dan cuti pegawai di unit ini.</p>
            </div>
        </header>

        <!-- FILTERS CARD -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-sm text-left">
            <form method="GET" action="{{ route('leave-history.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end text-xs">
                <!-- Search Name -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Cari Pegawai</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama pegawai..." 
                        class="w-full h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                </div>

                <!-- Filter Type -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Jenis Izin</label>
                    <select name="type" class="w-full h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 cursor-pointer">
                        <option value="">Semua Jenis</option>
                        <option value="Sakit" {{ request('type') === 'Sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="Izin" {{ request('type') === 'Izin' ? 'selected' : '' }}>Izin</option>
                        <option value="Cuti" {{ request('type') === 'Cuti' ? 'selected' : '' }}>Cuti</option>
                        <option value="Dinas" {{ request('type') === 'Dinas' ? 'selected' : '' }}>Dinas</option>
                    </select>
                </div>

                <!-- Filter Status -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Status</label>
                    <select name="status" class="w-full h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="Approved" {{ request('status') === 'Approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="Rejected" {{ request('status') === 'Rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 h-9 bg-slate-900 dark:bg-slate-100 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-all cursor-pointer flex items-center justify-center gap-1.5">
                        <i data-lucide="search" class="w-3.5 h-3.5"></i>
                        Filter
                    </button>
                    @if(request()->anyFilled(['search', 'type', 'status']))
                        <a href="{{ route('leave-history.index') }}" class="h-9 px-3 bg-slate-50 hover:bg-slate-100 dark:bg-slate-900 dark:hover:bg-slate-800 text-slate-750 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 flex items-center justify-center transition-all cursor-pointer" title="Reset Filter">
                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- TABLE LIST -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden text-left">
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-slate-50/70 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-800 text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="px-6 py-3.5 text-left">Pegawai</th>
                            <th class="px-6 py-3.5 text-center">Jenis Izin</th>
                            <th class="px-6 py-3.5 text-center">Waktu Pengajuan</th>
                            <th class="px-6 py-3.5 text-center">Durasi</th>
                            <th class="px-6 py-3.5 text-left">Alasan & Lampiran</th>
                            <th class="px-6 py-3.5 text-center">Status Decision</th>
                            <th class="px-6 py-3.5 text-left">Catatan Admin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 text-slate-700 dark:text-slate-300 font-medium">
                        @forelse($leaves as $leave)
                            @php
                                $duration = $leave->start_date->diffInDays($leave->end_date) + 1;
                            @endphp
                            <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-900/10 transition-colors">
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
                                                name: '{{ $leave->employee ? $leave->employee->name : '-' }}',
                                                nuptk_nip_nik: '{{ $leave->employee ? $leave->employee->nuptk_nip_nik : '-' }}',
                                                subject_position: '{{ $leave->employee ? $leave->employee->subject_position : '-' }}',
                                                unit: '{{ strtoupper($leave->employee ? $leave->employee->unit : '-') }}',
                                                email: '{{ $leave->employee ? $leave->employee->email : '-' }}',
                                                gender: '{{ $leave->employee ? $leave->employee->gender : '-' }}',
                                                employment_status: '{{ $leave->employee ? $leave->employee->employment_status : '-' }}',
                                                photo_url: '{{ $leave->employee && $leave->employee->photo ? (str_contains($leave->employee->photo, 'photos/') ? asset('storage/' . $leave->employee->photo) : asset('storage/photos/' . $leave->employee->photo)) : '' }}'
                                            }; showEmpDetailModal = true" class="text-slate-900 dark:text-slate-50 font-bold tracking-tight block cursor-pointer hover:underline hover:text-indigo-600 dark:hover:text-indigo-400">{{ $leave->employee ? $leave->employee->name : '-' }}</span>
                                            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-mono block">NIP: {{ $leave->employee ? $leave->employee->nuptk_nip_nik : '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($leave->type === 'Cuti')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-900/40">Cuti</span>
                                    @elseif($leave->type === 'Izin')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400 border border-amber-100 dark:border-amber-900/40">Izin</span>
                                    @elseif($leave->type === 'Sakit')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 dark:bg-rose-900/40 text-rose-700 dark:text-rose-400 border border-rose-100 dark:border-rose-900/40">Sakit</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800">{{ $leave->type }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center font-mono">
                                    {{ $leave->start_date->format('d M Y') }}{{ $leave->start_date != $leave->end_date ? ' s/d ' . $leave->end_date->format('d M Y') : '' }}
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-slate-800 dark:text-slate-200">
                                    {{ $duration }} Hari
                                </td>
                                <td class="px-6 py-4 text-left">
                                    <span class="text-slate-600 dark:text-slate-400 block line-clamp-2 max-w-xs" title="{{ $leave->reason }}">{{ $leave->reason ?? '-' }}</span>
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
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-405 border border-amber-200/30 dark:border-amber-900/30 uppercase animate-pulse">Menunggu</span>
                                    @elseif($leave->status === 'Approved')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200/30 dark:border-emerald-900/30 uppercase">Disetujui</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-400 border border-rose-200/30 dark:border-rose-900/30 uppercase">Ditolak</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-left text-slate-500 dark:text-slate-405">
                                    {{ $leave->notes ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <i data-lucide="calendar-x" class="w-8 h-8 text-slate-300 dark:text-slate-700"></i>
                                        <p class="text-xs">Tidak ditemukan riwayat pengajuan izin.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL DETAIL PEGAWAI -->
        <template x-teleport="body">
        <div x-show="showEmpDetailModal" @click.self="showEmpDetailModal = false" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs text-left" style="display: none; margin-top: 0px !important; z-index: 9999;">
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
                </div>
                <div class="p-5 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                    <button @click="showEmpDetailModal = false" class="h-9 px-4 bg-slate-900 dark:bg-slate-100 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Tutup</button>
                </div>
            </div>
        </div>
        </template>
</x-admin-layout>
