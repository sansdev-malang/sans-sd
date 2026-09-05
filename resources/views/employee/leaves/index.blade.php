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
                            <th class="px-6 py-3 text-left w-64 min-w-[200px]">Jenis Izin</th>
                            <th class="px-6 py-3 text-center">Tanggal Mulai</th>
                            <th class="px-6 py-3 text-center">Tanggal Selesai</th>
                            <th class="px-6 py-3 text-left">Keterangan</th>
                            <th class="px-6 py-3 text-left">Catatan / Alasan</th>
                            <th class="px-6 py-3 text-center w-48 min-w-[185px]">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-900 text-slate-700 dark:text-slate-300 font-medium">
                        @forelse($leaves as $leave)
                            @php
                                $leaveName = $leave->leaveType ? $leave->leaveType->name : $leave->type;
                                $statusCode = $leave->leaveType ? $leave->leaveType->status_code : ($leave->type === 'Sakit' ? 'S' : ($leave->type === 'Cuti' ? 'C' : ($leave->type === 'Dinas' ? 'H' : 'I')));

                                $processedByName = '';
                                $processedByRole = '';
                                if ($leave->processedBy) {
                                    $roleLabels = [
                                        'super_admin' => 'Super Admin',
                                        'admin_paud' => 'Admin PAUD',
                                        'admin_sd' => 'Admin SD',
                                        'admin_smp' => 'Admin SMP',
                                        'kepala_sekolah' => 'Kepala Sekolah',
                                        'waka' => 'Wakil Kepala Sekolah',
                                    ];
                                    $processedByName = $leave->processedBy->name;
                                    $processedByRole = $roleLabels[$leave->processedBy->role] ?? $leave->processedBy->role;
                                } elseif ($leave->processed_by_name) {
                                    $processedByName = $leave->processed_by_name;
                                    if (preg_match('/^(.*?)\s*\((.*?)\)$/', $processedByName, $matches)) {
                                        $processedByName = trim($matches[1]);
                                        $processedByRole = trim($matches[2]);
                                    }
                                } else {
                                    $noteText = $leave->notes ?? '';
                                    $lowerNote = strtolower($noteText);
                                    $rawProcessed = '';
                                    if (
                                        str_starts_with($lowerNote, 'disetujui oleh ') ||
                                        str_starts_with($lowerNote, 'ditolak oleh ') ||
                                        str_starts_with($lowerNote, 'disetujui otomatis oleh ')
                                    ) {
                                        $parts = explode('oleh ', $noteText);
                                        $rawProcessed = preg_replace('/[\s.]+$/', '', end($parts));
                                    } elseif (preg_match('/\((Keputusan|Ditolak|Disetujui)\s+oleh\s+(.*?)\)/i', $noteText, $matches)) {
                                        $rawProcessed = $matches[2];
                                    }
                                    
                                    if ($rawProcessed) {
                                        if (preg_match('/^(.*?)\s*\((.*?)\)$/', $rawProcessed, $matches)) {
                                            $processedByName = trim($matches[1]);
                                            $processedByRole = trim($matches[2]);
                                        } else {
                                            $processedByName = $rawProcessed;
                                        }
                                    }
                                }

                                $displayNotes = $leave->notes ?? '';
                                $lowerNotes = strtolower($displayNotes);
                                if (
                                    str_starts_with($lowerNotes, 'disetujui oleh') || 
                                    str_starts_with($lowerNotes, 'ditolak oleh') || 
                                    str_starts_with($lowerNotes, 'disetujui otomatis oleh')
                                ) {
                                    $displayNotes = '';
                                } else {
                                    $displayNotes = preg_replace('/\s*\((Keputusan|Ditolak|Disetujui)\s+oleh.*?\)/i', '', $displayNotes);
                                }
                            @endphp
                            <tr>
                                <td class="px-6 py-4 text-left">
                                    <div class="flex flex-col items-start gap-1">
                                        <div class="flex items-center justify-start gap-1.5">
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200/40 dark:border-slate-700/50 uppercase">
                                                {{ $statusCode }}
                                            </span>
                                            <span class="font-bold text-slate-800 dark:text-slate-200 uppercase">
                                                {{ $leaveName }}
                                            </span>
                                        </div>
                                        <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">
                                            Diajukan: {{ $leave->created_at ? $leave->created_at->format('d M Y H:i') : '-' }}
                                        </span>
                                    </div>
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
                                <td class="px-6 py-4 text-left">
                                    <span class="text-slate-600 dark:text-slate-400 block">{{ $displayNotes ?: '-' }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        @if($leave->status === 'Pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-700/30 uppercase">
                                                Pending
                                            </span>
                                        @elseif($leave->status === 'Approved')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-700/30 uppercase">
                                                Disetujui
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-700/30 uppercase">
                                                Ditolak
                                            </span>
                                        @endif

                                        @if($leave->status !== 'Pending' && $processedByName)
                                            <div class="text-[9px] text-slate-400 dark:text-slate-500 font-semibold mt-0.5 leading-tight">
                                                oleh: {{ $processedByName }}
                                                @if($processedByRole)
                                                    <span class="block text-[8px] text-slate-400/80 font-normal">({{ $processedByRole }})</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-slate-500 dark:text-slate-400">
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
                @php
                    $leaveName = $leave->leaveType ? $leave->leaveType->name : $leave->type;
                    $statusCode = $leave->leaveType ? $leave->leaveType->status_code : ($leave->type === 'Sakit' ? 'S' : ($leave->type === 'Cuti' ? 'C' : ($leave->type === 'Dinas' ? 'H' : 'I')));

                    $processedByName = '';
                    $processedByRole = '';
                    if ($leave->processedBy) {
                        $roleLabels = [
                            'super_admin' => 'Super Admin',
                            'admin_paud' => 'Admin PAUD',
                            'admin_sd' => 'Admin SD',
                            'admin_smp' => 'Admin SMP',
                            'kepala_sekolah' => 'Kepala Sekolah',
                            'waka' => 'Wakil Kepala Sekolah',
                        ];
                        $processedByName = $leave->processedBy->name;
                        $processedByRole = $roleLabels[$leave->processedBy->role] ?? $leave->processedBy->role;
                    } elseif ($leave->processed_by_name) {
                        $processedByName = $leave->processed_by_name;
                        if (preg_match('/^(.*?)\s*\((.*?)\)$/', $processedByName, $matches)) {
                            $processedByName = trim($matches[1]);
                            $processedByRole = trim($matches[2]);
                        }
                    } else {
                        $noteText = $leave->notes ?? '';
                        $lowerNote = strtolower($noteText);
                        $rawProcessed = '';
                        if (
                            str_starts_with($lowerNote, 'disetujui oleh ') ||
                            str_starts_with($lowerNote, 'ditolak oleh ') ||
                            str_starts_with($lowerNote, 'disetujui otomatis oleh ')
                        ) {
                            $parts = explode('oleh ', $noteText);
                            $rawProcessed = preg_replace('/[\s.]+$/', '', end($parts));
                        } elseif (preg_match('/\((Keputusan|Ditolak|Disetujui)\s+oleh\s+(.*?)\)/i', $noteText, $matches)) {
                            $rawProcessed = $matches[2];
                        }
                        
                        if ($rawProcessed) {
                            if (preg_match('/^(.*?)\s*\((.*?)\)$/', $rawProcessed, $matches)) {
                                $processedByName = trim($matches[1]);
                                $processedByRole = trim($matches[2]);
                            } else {
                                $processedByName = $rawProcessed;
                            }
                        }
                    }

                    $displayNotes = $leave->notes ?? '';
                    $lowerNotes = strtolower($displayNotes);
                    if (
                        str_starts_with($lowerNotes, 'disetujui oleh') || 
                        str_starts_with($lowerNotes, 'ditolak oleh') || 
                        str_starts_with($lowerNotes, 'disetujui otomatis oleh')
                    ) {
                        $displayNotes = '';
                    } else {
                        $displayNotes = preg_replace('/\s*\((Keputusan|Ditolak|Disetujui)\s+oleh.*?\)/i', '', $displayNotes);
                    }
                @endphp
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-sm space-y-3 text-left">
                    <div class="flex items-center justify-between gap-4">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200/40 dark:border-slate-700/50 uppercase">
                            [{{ $statusCode }}] {{ $leaveName }}
                        </span>
                        <div>
                            @if($leave->status === 'Pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 dark:bg-amber-955/30 text-amber-700 dark:text-amber-400 border border-amber-200/30 dark:border-amber-900/30 uppercase">
                                    Pending
                                </span>
                            @elseif($leave->status === 'Approved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 dark:bg-emerald-955/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200/30 dark:border-emerald-900/30 uppercase">
                                    Disetujui
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-rose-50 dark:bg-rose-955/30 text-rose-700 dark:text-rose-455 border border-rose-200/30 dark:border-rose-900/30 uppercase">
                                    Ditolak
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="space-y-1.5 pt-1.5 border-t border-slate-100 dark:border-slate-800/60">
                        <div class="flex justify-between text-xs gap-4">
                            <span class="text-slate-400 dark:text-slate-500 shrink-0">Tgl Pengajuan:</span>
                            <span class="font-mono font-medium text-slate-800 dark:text-slate-200 text-right">{{ $leave->created_at ? $leave->created_at->format('d M Y H:i') : '-' }}</span>
                        </div>
                        <div class="flex justify-between text-xs gap-4">
                            <span class="text-slate-400 dark:text-slate-500 shrink-0">Mulai:</span>
                            <span class="font-mono font-medium text-slate-800 dark:text-slate-200 text-right">{{ $leave->start_date->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between text-xs gap-4">
                            <span class="text-slate-400 dark:text-slate-500 shrink-0">Selesai:</span>
                            <span class="font-mono font-medium text-slate-800 dark:text-slate-200 text-right">{{ $leave->end_date->format('d M Y') }}</span>
                        </div>
                        @if($leave->status !== 'Pending' && $processedByName)
                            <div class="flex justify-between text-xs gap-4 items-start">
                                <span class="text-slate-400 dark:text-slate-500 shrink-0">Diproses Oleh:</span>
                                <div class="text-right">
                                    <span class="font-medium text-slate-850 dark:text-slate-200 block leading-tight">{{ $processedByName }}</span>
                                    @if($processedByRole)
                                        <span class="text-[10px] text-slate-400 dark:text-slate-500 block mt-0.5 leading-tight">({{ $processedByRole }})</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                        <div class="flex flex-col gap-0.5 pt-1">
                            <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">Keterangan:</span>
                            <p class="text-xs text-slate-700 dark:text-slate-300 leading-normal">{{ $leave->reason ?? '-' }}</p>
                        </div>
                        @if($displayNotes)
                            <div class="flex flex-col gap-0.5 pt-1">
                                <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">Catatan / Alasan:</span>
                                <p class="text-xs text-slate-750 dark:text-slate-300 leading-normal">{{ $displayNotes }}</p>
                            </div>
                        @endif
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

                <form method="POST" action="{{ route('my-leaves.store') }}" enctype="multipart/form-data" id="leaveForm" onsubmit="let btn = this.querySelector('button[type=submit]'); if (btn) { btn.disabled = true; btn.classList.add('opacity-50', 'cursor-not-allowed'); }" class="p-5 pt-0 pb-5 space-y-4 text-xs">
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
                        <select name="leave_type_id" required class="w-full text-xs px-3 py-2 bg-slate-50 dark:bg-slate-900 border @error('leave_type_id') border-rose-500 focus:border-rose-500 @else border-slate-200 dark:border-slate-800 @enderror rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500 font-sans">
                            @foreach($leaveTypes as $lt)
                                <option value="{{ $lt->id }}" @selected(old('leave_type_id') == $lt->id)>{{ $lt->name }} - {{ $lt->status_code }}</option>
                            @endforeach
                        </select>
                        @error('leave_type_id')
                            <span class="text-xs text-rose-600 dark:text-rose-400 block mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Mulai Tanggal</label>
                            <input type="date" name="start_date" required min="{{ date('Y-m-d') }}" value="{{ old('start_date') }}" class="w-full text-xs px-3 py-2 bg-slate-50 dark:bg-slate-900 border @error('start_date') border-rose-500 focus:border-rose-500 @else border-slate-200 dark:border-slate-800 @enderror rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500 font-mono">
                            @error('start_date')
                                <span class="text-xs text-rose-600 dark:text-rose-400 block mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Selesai Tanggal</label>
                            <input type="date" name="end_date" required min="{{ date('Y-m-d') }}" value="{{ old('end_date') }}" class="w-full text-xs px-3 py-2 bg-slate-50 dark:bg-slate-900 border @error('end_date') border-rose-500 focus:border-rose-500 @else border-slate-200 dark:border-slate-800 @enderror rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500 font-mono font-nasalization">
                            @error('end_date')
                                <span class="text-xs text-rose-600 dark:text-rose-400 block mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Keterangan</label>
                        <textarea name="reason" rows="3" placeholder="Tuliskan keterangan detail..." class="w-full text-xs px-3 py-2 bg-slate-50 dark:bg-slate-900 border @error('reason') border-rose-500 focus:border-rose-500 @else border-slate-200 dark:border-slate-800 @enderror rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500">{{ old('reason') }}</textarea>
                        @error('reason')
                            <span class="text-xs text-rose-600 dark:text-rose-400 block mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">File Lampiran (Surat Dokter / Bukti Pendukung)</label>
                        <input type="file" name="attachment" accept=".pdf,.png,.jpg,.jpeg,.doc,.docx" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border @error('attachment') border-rose-500 focus:border-rose-500 @else border-slate-200 dark:border-slate-800 @enderror rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500">
                        <span class="text-xs text-slate-400 dark:text-slate-500 block mt-1">Format: PDF, PNG, JPG, JPEG, DOC, DOCX. Maksimal 2MB.</span>
                        @error('attachment')
                            <span class="text-xs text-rose-600 dark:text-rose-400 block mt-1">{{ $message }}</span>
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
