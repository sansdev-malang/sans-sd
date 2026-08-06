<x-admin-layout>
    <div class="p-6 space-y-6" x-data="{ showAddModal: false }">

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
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 font-nasalization">Izin & Cuti Saya</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Ajukan surat izin sakit, cuti, atau dinas secara mandiri ke HRD Pusat.</p>
            </div>
            <div>
                <button @click="showAddModal = true" class="h-9 px-4 bg-slate-900 dark:bg-slate-100 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-all cursor-pointer flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Buat Pengajuan Izin
                </button>
            </div>
        </header>

        <!-- TABLE LIST -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden text-left">
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800 text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="px-6 py-3 text-center">Jenis Izin</th>
                            <th class="px-6 py-3 text-center">Tanggal Mulai</th>
                            <th class="px-6 py-3 text-center">Tanggal Selesai</th>
                            <th class="px-6 py-3 text-left">Alasan / Uraian</th>
                            <th class="px-6 py-3 text-center">Status HRD</th>
                            <th class="px-6 py-3 text-left">Catatan HRD</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-900 text-slate-700 dark:text-slate-300 font-medium">
                        @forelse($leaves as $leave)
                            <tr>
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
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-400 border border-rose-200/30 dark:border-rose-900/30 uppercase">Ditolak</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-left font-sans italic text-slate-500 dark:text-slate-400">
                                    {{ $leave->notes ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($leave->status === 'Pending')
                                        <form action="{{ route('my-leaves.destroy', $leave->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="h-7 px-2.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-900/20 dark:hover:bg-rose-900/40 text-rose-700 dark:text-rose-400 text-[10px] font-semibold rounded border border-rose-200/30 dark:border-rose-900/30 transition-all cursor-pointer flex items-center gap-1">
                                                <i data-lucide="x" class="w-3 h-3"></i>
                                                Batal
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-mono italic">Diproses</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-slate-500 dark:text-slate-400">
                                    Anda belum memiliki riwayat pengajuan izin/cuti.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ADD MODAL -->
        <template x-teleport="body">
        <div x-show="showAddModal" @click.self="showAddModal = false" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto" style="display: none; margin-top: 0px !important; z-index: 9999;">
            <div @click.outside="showAddModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl w-full max-w-md overflow-hidden text-left">
                <div class="flex justify-between items-center border-b border-slate-200 dark:border-slate-800 p-5 bg-slate-50 dark:bg-slate-900/50">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-50">Ajukan Surat Izin / Cuti</h3>
                    <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('my-leaves.store') }}" enctype="multipart/form-data" class="p-5 space-y-4 text-xs">
                    @csrf
                    
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Jenis Izin / Cuti</label>
                        <select name="type" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-880 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500 font-sans">
                            <option value="Sakit">Sakit</option>
                            <option value="Izin">Izin (Pribadi/Penting)</option>
                            <option value="Cuti">Cuti Tahunan</option>
                            <option value="Dinas">Tugas Dinas (Tetap dapat Bonus)</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Mulai Tanggal</label>
                            <input type="date" name="start_date" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-880 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500 font-mono">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Selesai Tanggal</label>
                            <input type="date" name="end_date" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-880 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500 font-mono font-nasalization">
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Alasan / Keterangan Pengajuan</label>
                        <textarea name="reason" rows="2" placeholder="Tuliskan keterangan detail alasan..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-880 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500"></textarea>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">File Lampiran (Surat Dokter / Bukti Pendukung)</label>
                        <input type="file" name="attachment" accept=".pdf,.png,.jpg,.jpeg,.doc,.docx" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-880 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500">
                        <span class="text-[10px] text-slate-400 block mt-1">Format: PDF, PNG, JPG, JPEG, DOC, DOCX. Maksimal 2MB.</span>
                    </div>

                    <div class="flex gap-2.5 pt-4 border-t border-slate-100 dark:border-slate-900 justify-end">
                        <button type="button" @click="showAddModal = false" class="h-9 px-4 bg-slate-50 hover:bg-slate-100 dark:bg-slate-900 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 transition-all cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="h-9 px-4 bg-slate-900 dark:bg-slate-100 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-all cursor-pointer">
                            Kirim Pengajuan
                        </button>
                    </div>
                    </div>
                </form>
            </div>
        </div>
        </template>

    </div>
</x-admin-layout>
