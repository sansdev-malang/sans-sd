<x-admin-layout>
    <style>
        .dark input[type="date"],
        .dark input[type="month"] {
            color-scheme: dark;
        }
    </style>
    <div class="p-6 space-y-6" x-data="{ 
        showAddModal: {{ $errors->any() ? 'true' : 'false' }},
        closeModal() {
            if ({{ $errors->any() ? 'true' : 'false' }}) {
                window.location.href = '{{ route('my-leaves.index') }}';
            } else {
                document.getElementById('leaveForm').reset();
                this.showAddModal = false;
            }
        }
    }">

        <!-- HEADER -->
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 font-nasalization">Izin & Cuti Saya</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Daftar riwayat izin, sakit, dan cuti mandiri pegawai.</p>
            </div>
            <div>
                <button @click="showAddModal = true" class="h-9 px-4 bg-slate-900 dark:bg-slate-100 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-all cursor-pointer flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Input Izin / Cuti
                </button>
            </div>
        </header>

        <!-- TABLE LIST (Desktop View) -->
        <div class="hidden sm:block bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden text-left">
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800 text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="px-6 py-3 text-center">Jenis Izin</th>
                            <th class="px-6 py-3 text-center">Tanggal Mulai</th>
                            <th class="px-6 py-3 text-center">Tanggal Selesai</th>
                            <th class="px-6 py-3 text-left">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-900 text-slate-700 dark:text-slate-300 font-medium">
                        @forelse($leaves as $leave)
                            <tr>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200/40 dark:border-slate-700/50 uppercase">
                                        {{ $leave->leaveType ? $leave->leaveType->name : $leave->type }}
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-slate-500 dark:text-slate-400">
                                    Anda belum memiliki riwayat pengajuan izin/cuti.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MOBILE CARD LIST -->
        <div class="block sm:hidden space-y-4">
            @forelse($leaves as $leave)
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-sm space-y-3 text-left">
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200/40 dark:border-slate-700/50 uppercase">
                            {{ $leave->leaveType ? $leave->leaveType->name : $leave->type }}
                        </span>
                        <div class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">
                            Status: <span class="text-emerald-600 dark:text-emerald-400 font-bold uppercase">Approved</span>
                        </div>
                    </div>
                    <div class="space-y-1.5 pt-1.5 border-t border-slate-100 dark:border-slate-800/60">
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-400 dark:text-slate-500">Mulai:</span>
                            <span class="font-mono font-medium text-slate-800 dark:text-slate-200">{{ $leave->start_date->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-400 dark:text-slate-500">Selesai:</span>
                            <span class="font-mono font-medium text-slate-800 dark:text-slate-200">{{ $leave->end_date->format('d M Y') }}</span>
                        </div>
                        <div class="flex flex-col gap-0.5 pt-1">
                            <span class="text-[10px] text-slate-400 dark:text-slate-500">Keterangan:</span>
                            <p class="text-xs text-slate-700 dark:text-slate-300 leading-normal">{{ $leave->reason ?? '-' }}</p>
                        </div>
                    </div>
                    @if($leave->attachment)
                        <div class="pt-2.5 border-t border-slate-100 dark:border-slate-800/60 flex justify-end">
                            <a href="{{ asset('storage/' . $leave->attachment) }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold underline">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"></path></svg>
                                Lihat Lampiran
                            </a>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-8 text-center text-slate-500 dark:text-slate-400">
                    Anda belum memiliki riwayat pengajuan izin/cuti.
                </div>
            @endforelse
        </div>

        <!-- ADD MODAL -->
        <template x-teleport="body">
        <div x-show="showAddModal" @click.self="closeModal()" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto" style="display: none; margin-top: 0px !important; z-index: 9999;">
            <div @click.outside="closeModal()" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl w-full max-w-md overflow-hidden text-left">
                <div class="flex justify-between items-center border-b border-slate-200 dark:border-slate-800 p-5 bg-slate-50 dark:bg-slate-900/50">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-50">Ajukan Izin / Cuti</h3>
                    <button @click="closeModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 cursor-pointer p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('my-leaves.store') }}" enctype="multipart/form-data" id="leaveForm" class="p-5 space-y-4 text-xs">
                    @csrf
                    
                    @if($errors->any())
                        <div class="bg-rose-50 dark:bg-rose-900/40 border border-rose-200 dark:border-rose-900/60 rounded-xl p-4 mb-2">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 rounded-full bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                </div>
                                <h5 class="text-xs font-bold text-slate-800 dark:text-slate-200">Terjadi Kesalahan!</h5>
                            </div>
                            <ul class="list-disc list-inside text-xs text-slate-600 dark:text-slate-400 space-y-1 ml-11">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Jenis Izin / Cuti</label>
                        <select name="leave_type_id" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border @error('leave_type_id') border-rose-500 focus:border-rose-500 @else border-slate-200 dark:border-slate-800 @enderror rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500 font-sans">
                            @foreach($leaveTypes as $lt)
                                <option value="{{ $lt->id }}" @selected(old('leave_type_id') == $lt->id)>{{ $lt->name }} - {{ $lt->status_code }}</option>
                            @endforeach
                        </select>
                        @error('leave_type_id')
                            <span class="text-[10px] text-rose-600 dark:text-rose-400 block mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Mulai Tanggal</label>
                            <input type="date" name="start_date" required min="{{ date('Y-m-d') }}" value="{{ old('start_date') }}" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border @error('start_date') border-rose-500 focus:border-rose-500 @else border-slate-200 dark:border-slate-800 @enderror rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500 font-mono">
                            @error('start_date')
                                <span class="text-[10px] text-rose-600 dark:text-rose-400 block mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Selesai Tanggal</label>
                            <input type="date" name="end_date" required min="{{ date('Y-m-d') }}" value="{{ old('end_date') }}" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border @error('end_date') border-rose-500 focus:border-rose-500 @else border-slate-200 dark:border-slate-800 @enderror rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500 font-mono font-nasalization">
                            @error('end_date')
                                <span class="text-[10px] text-rose-600 dark:text-rose-400 block mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Keterangan</label>
                        <textarea name="reason" rows="3" placeholder="Tuliskan keterangan detail..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border @error('reason') border-rose-500 focus:border-rose-500 @else border-slate-200 dark:border-slate-800 @enderror rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500">{{ old('reason') }}</textarea>
                        @error('reason')
                            <span class="text-[10px] text-rose-600 dark:text-rose-400 block mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">File Lampiran (Surat Dokter / Bukti Pendukung)</label>
                        <input type="file" name="attachment" accept=".pdf,.png,.jpg,.jpeg,.doc,.docx" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border @error('attachment') border-rose-500 focus:border-rose-500 @else border-slate-200 dark:border-slate-800 @enderror rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500">
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 block mt-1">Format: PDF, PNG, JPG, JPEG, DOC, DOCX. Maksimal 2MB.</span>
                        @error('attachment')
                            <span class="text-[10px] text-rose-600 dark:text-rose-400 block mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex gap-2.5 pt-4 border-t border-slate-100 dark:border-slate-900 justify-end">
                        <button type="button" @click="closeModal()" class="h-9 px-4 bg-slate-50 hover:bg-slate-100 dark:bg-slate-900 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 transition-all cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="h-9 px-4 bg-slate-900 dark:bg-slate-100 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-all cursor-pointer">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </template>

    </div>
</x-admin-layout>
