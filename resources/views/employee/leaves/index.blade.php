<x-admin-layout>
    <style>
        .dark input[type="date"],
        .dark input[type="month"] {
            color-scheme: dark;
        }
    </style>
    <div class="p-4 sm:p-6 space-y-6 w-full text-left" x-data="{ 
        showAddModal: {{ $errors->any() ? 'true' : 'false' }},
        filterStatus: 'all',
        startDate: '{{ old('start_date', date('Y-m-d')) }}',
        endDate: '{{ old('end_date', date('Y-m-d')) }}',
        isSubmitting: false,
        get durationDays() {
            if (!this.startDate || !this.endDate) return 0;
            let s = new Date(this.startDate);
            let e = new Date(this.endDate);
            if (e < s) return 0;
            let diffTime = Math.abs(e - s);
            return Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        },
        onStartDateChange() {
            if (this.startDate && (!this.endDate || this.endDate < this.startDate)) {
                this.endDate = this.startDate;
            }
        },
        closeModal() {
            if ({{ $errors->any() ? 'true' : 'false' }}) {
                window.location.href = '{{ route('my-leaves.index') }}';
            } else {
                document.getElementById('leaveForm').reset();
                this.showAddModal = false;
            }
        },
        confirmCancel(leaveId, leaveName) {
            if (confirm('Apakah Anda yakin ingin membatalkan pengajuan ' + leaveName + ' ini?')) {
                document.getElementById('cancel-form-' + leaveId).submit();
            }
        }
    }">

        <!-- HEADER SECTION -->
        <section class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-xs">
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                        <i data-lucide="calendar-days" class="w-4 h-4"></i>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Izin & Cuti Saya</h2>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400">Pantau riwayat izin, sakit, dinas, dan cuti mandiri Anda.</p>
            </div>
            <button @click="showAddModal = true"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 dark:bg-slate-100 px-4 py-2.5 text-xs font-semibold text-white dark:text-slate-900 shadow-sm hover:bg-slate-800 dark:hover:bg-slate-200 transition-all cursor-pointer shrink-0">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Ajukan Izin / Cuti</span>
            </button>
        </section>

        <!-- BENTO STAT CARDS (4 CARDS) -->
        <section class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- TAHUN INI -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-xs flex items-center gap-4 transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                <div class="w-11 h-11 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                    <i data-lucide="calendar-check" class="w-5 h-5"></i>
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400 truncate">Disetujui Tahun {{ date('Y') }}</span>
                    <span class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-slate-50 tracking-tight">{{ $stats['total_this_year'] }}</span>
                </div>
            </div>

            <!-- MENUNGGU PERSETUJUAN -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-xs flex items-center gap-4 transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                <div class="w-11 h-11 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 relative">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                    @if($stats['pending'] > 0)
                        <span class="absolute -top-1 -right-1 w-3 h-3 bg-amber-500 rounded-full animate-ping"></span>
                        <span class="absolute -top-1 -right-1 w-3 h-3 bg-amber-500 rounded-full"></span>
                    @endif
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400 truncate">Menunggu Persetujuan</span>
                    <span class="text-xl sm:text-2xl font-bold text-amber-600 dark:text-amber-400 tracking-tight">{{ $stats['pending'] }}</span>
                </div>
            </div>

            <!-- TOTAL DISETUJUI -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-xs flex items-center gap-4 transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400 truncate">Total Disetujui (Semua)</span>
                    <span class="text-xl sm:text-2xl font-bold text-emerald-600 dark:text-emerald-400 tracking-tight">{{ $stats['approved'] }}</span>
                </div>
            </div>

            <!-- DITOLAK -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-xs flex items-center gap-4 transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                <div class="w-11 h-11 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                    <i data-lucide="x-circle" class="w-5 h-5"></i>
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400 truncate">Ditolak</span>
                    <span class="text-xl sm:text-2xl font-bold text-rose-600 dark:text-rose-400 tracking-tight">{{ $stats['rejected'] }}</span>
                </div>
            </div>
        </section>

        <!-- STATUS FILTER TABS -->
        <section class="flex flex-wrap items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-3">
            <button @click="filterStatus = 'all'"
                :class="filterStatus === 'all' 
                    ? 'bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 font-bold' 
                    : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800'"
                class="px-3.5 py-1.5 rounded-xl text-xs transition-all cursor-pointer flex items-center gap-1.5">
                <span>Semua Riwayat</span>
                <span class="px-1.5 py-0.2 rounded-md text-[10px] bg-slate-200/60 dark:bg-slate-800 font-semibold">{{ count($leaves) }}</span>
            </button>

            <button @click="filterStatus = 'Pending'"
                :class="filterStatus === 'Pending' 
                    ? 'bg-amber-500 text-white font-bold shadow-xs' 
                    : 'bg-white dark:bg-slate-900 text-amber-700 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/30 border border-amber-200/60 dark:border-amber-800/40'"
                class="px-3.5 py-1.5 rounded-xl text-xs transition-all cursor-pointer flex items-center gap-1.5">
                <span>Menunggu Persetujuan</span>
                <span class="px-1.5 py-0.2 rounded-md text-[10px] bg-amber-100 dark:bg-amber-900/60 font-semibold">{{ $stats['pending'] }}</span>
            </button>

            <button @click="filterStatus = 'Approved'"
                :class="filterStatus === 'Approved' 
                    ? 'bg-emerald-600 text-white font-bold shadow-xs' 
                    : 'bg-white dark:bg-slate-900 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 border border-emerald-200/60 dark:border-emerald-800/40'"
                class="px-3.5 py-1.5 rounded-xl text-xs transition-all cursor-pointer flex items-center gap-1.5">
                <span>Disetujui</span>
                <span class="px-1.5 py-0.2 rounded-md text-[10px] bg-emerald-100 dark:bg-emerald-900/60 font-semibold">{{ $stats['approved'] }}</span>
            </button>

            <button @click="filterStatus = 'Rejected'"
                :class="filterStatus === 'Rejected' 
                    ? 'bg-rose-600 text-white font-bold shadow-xs' 
                    : 'bg-white dark:bg-slate-900 text-rose-700 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 border border-rose-200/60 dark:border-rose-800/40'"
                class="px-3.5 py-1.5 rounded-xl text-xs transition-all cursor-pointer flex items-center gap-1.5">
                <span>Ditolak</span>
                <span class="px-1.5 py-0.2 rounded-md text-[10px] bg-rose-100 dark:bg-rose-900/60 font-semibold">{{ $stats['rejected'] }}</span>
            </button>
        </section>

        <!-- TABLE LIST (Desktop View) -->
        <div class="hidden sm:block bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs overflow-hidden text-left">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-xs">
                    <thead class="bg-slate-50/75 dark:bg-slate-850/50 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="px-5 py-3.5 text-left w-64 min-w-[200px]">Jenis Izin</th>
                            <th class="px-5 py-3.5 text-left min-w-[160px]">Periode & Durasi</th>
                            <th class="px-5 py-3.5 text-left min-w-[200px]">Keterangan</th>
                            <th class="px-5 py-3.5 text-left min-w-[180px]">Catatan / Respon HRD</th>
                            <th class="px-5 py-3.5 text-center w-48 min-w-[170px]">Status</th>
                            <th class="px-5 py-3.5 text-center w-28 min-w-[100px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300 font-medium">
                        @forelse($leaves as $leave)
                            @php
                                $leaveName = $leave->leaveType ? $leave->leaveType->name : $leave->type;
                                $statusCode = $leave->leaveType ? $leave->leaveType->status_code : ($leave->type === 'Sakit' ? 'S' : ($leave->type === 'Cuti' ? 'C' : ($leave->type === 'Dinas' ? 'H' : 'I')));

                                $days = $leave->start_date && $leave->end_date ? $leave->start_date->diffInDays($leave->end_date) + 1 : 1;

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
                            <tr x-show="filterStatus === 'all' || filterStatus === '{{ $leave->status }}'" class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-5 py-4 text-left">
                                    <div class="flex flex-col items-start gap-1">
                                        <div class="flex items-center gap-1.5">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-800/50 uppercase">
                                                {{ $statusCode }}
                                            </span>
                                            <span class="font-bold text-slate-900 dark:text-slate-100">
                                                {{ $leaveName }}
                                            </span>
                                        </div>
                                        <span class="text-[11px] text-slate-400 dark:text-slate-500">
                                            Diajukan: {{ $leave->created_at ? $leave->created_at->format('d M Y H:i') : '-' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-left">
                                    <div class="flex flex-col gap-1">
                                        <span class="font-semibold text-slate-800 dark:text-slate-200 font-mono text-xs">
                                            @if($leave->start_date && $leave->end_date && $leave->start_date->format('Y-m-d') === $leave->end_date->format('Y-m-d'))
                                                {{ $leave->start_date->translatedFormat('d M Y') }}
                                            @else
                                                {{ $leave->start_date ? $leave->start_date->translatedFormat('d M Y') : '-' }} s.d. {{ $leave->end_date ? $leave->end_date->translatedFormat('d M Y') : '-' }}
                                            @endif
                                        </span>
                                        <span class="inline-flex items-center gap-1 text-[11px] text-slate-500 dark:text-slate-400">
                                            <i data-lucide="clock" class="w-3 h-3 text-indigo-500"></i>
                                            <span>{{ $days }} Hari Kerja</span>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-left">
                                    <span class="text-slate-700 dark:text-slate-300 block leading-relaxed">{{ $leave->reason ?: '-' }}</span>
                                    @if($leave->attachment)
                                        <div class="mt-1.5">
                                            <a href="{{ asset('storage/' . $leave->attachment) }}" target="_blank" class="inline-flex items-center gap-1.5 text-[11px] text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold underline">
                                                <i data-lucide="paperclip" class="w-3.5 h-3.5"></i>
                                                <span>Lihat Lampiran</span>
                                            </a>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-left">
                                    <span class="text-slate-600 dark:text-slate-400 block leading-relaxed">{{ $displayNotes ?: '-' }}</span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        @if($leave->status === 'Pending')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/60 uppercase tracking-wide">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                Pending
                                            </span>
                                        @elseif($leave->status === 'Approved')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/60 uppercase tracking-wide">
                                                <i data-lucide="check" class="w-3 h-3"></i>
                                                Disetujui
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800/60 uppercase tracking-wide">
                                                <i data-lucide="x" class="w-3 h-3"></i>
                                                Ditolak
                                            </span>
                                        @endif

                                        @if($leave->status !== 'Pending' && $processedByName)
                                            <div class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold mt-0.5 leading-tight text-center">
                                                oleh: {{ $processedByName }}
                                                @if($processedByRole)
                                                    <span class="block text-[9px] text-slate-400/80 font-normal">({{ $processedByRole }})</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @if($leave->status === 'Pending')
                                        <form id="cancel-form-{{ $leave->id }}" action="{{ route('my-leaves.destroy', $leave->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" @click="confirmCancel('{{ $leave->id }}', '{{ $leaveName }}')"
                                                class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/50 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900/50 text-[11px] font-bold transition-colors cursor-pointer" title="Batalkan Pengajuan">
                                                Batalkan
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[11px] text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <i data-lucide="calendar-x" class="w-8 h-8 text-slate-300 dark:text-slate-600"></i>
                                        <p class="text-xs font-semibold">Anda belum memiliki riwayat pengajuan izin/cuti.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MOBILE CARD LIST (Mobile View) -->
        <div class="block sm:hidden space-y-4">
            @forelse($leaves as $leave)
                @php
                    $leaveName = $leave->leaveType ? $leave->leaveType->name : $leave->type;
                    $statusCode = $leave->leaveType ? $leave->leaveType->status_code : ($leave->type === 'Sakit' ? 'S' : ($leave->type === 'Cuti' ? 'C' : ($leave->type === 'Dinas' ? 'H' : 'I')));
                    $days = $leave->start_date && $leave->end_date ? $leave->start_date->diffInDays($leave->end_date) + 1 : 1;

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
                <div x-show="filterStatus === 'all' || filterStatus === '{{ $leave->status }}'" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-xs space-y-3.5 text-left">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-1.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-800/50 uppercase">
                                {{ $statusCode }}
                            </span>
                            <span class="font-bold text-slate-900 dark:text-slate-100 text-xs">
                                {{ $leaveName }}
                            </span>
                        </div>
                        <div>
                            @if($leave->status === 'Pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/60 uppercase">
                                    Pending
                                </span>
                            @elseif($leave->status === 'Approved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/60 uppercase">
                                    Disetujui
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800/60 uppercase">
                                    Ditolak
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800 text-xs">
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-400 dark:text-slate-500 shrink-0">Tgl Pengajuan:</span>
                            <span class="font-mono text-slate-800 dark:text-slate-200 text-right">{{ $leave->created_at ? $leave->created_at->format('d M Y H:i') : '-' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-400 dark:text-slate-500 shrink-0">Periode:</span>
                            <span class="font-mono font-medium text-slate-800 dark:text-slate-200 text-right">
                                @if($leave->start_date && $leave->end_date && $leave->start_date->format('Y-m-d') === $leave->end_date->format('Y-m-d'))
                                    {{ $leave->start_date->translatedFormat('d M Y') }}
                                @else
                                    {{ $leave->start_date ? $leave->start_date->translatedFormat('d M Y') : '-' }} s.d. {{ $leave->end_date ? $leave->end_date->translatedFormat('d M Y') : '-' }}
                                @endif
                                <span class="text-indigo-600 dark:text-indigo-400 font-semibold block text-[11px]">({{ $days }} Hari Kerja)</span>
                            </span>
                        </div>
                        @if($leave->status !== 'Pending' && $processedByName)
                            <div class="flex justify-between gap-4 items-start">
                                <span class="text-slate-400 dark:text-slate-500 shrink-0">Diproses Oleh:</span>
                                <div class="text-right">
                                    <span class="font-medium text-slate-800 dark:text-slate-200 block leading-tight">{{ $processedByName }}</span>
                                    @if($processedByRole)
                                        <span class="text-[10px] text-slate-400 dark:text-slate-500 block mt-0.5 leading-tight">({{ $processedByRole }})</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                        <div class="flex flex-col gap-0.5 pt-1">
                            <span class="text-slate-400 dark:text-slate-500 font-medium">Keterangan:</span>
                            <p class="text-slate-700 dark:text-slate-300 leading-normal">{{ $leave->reason ?: '-' }}</p>
                        </div>
                        @if($displayNotes)
                            <div class="flex flex-col gap-0.5 pt-1">
                                <span class="text-slate-400 dark:text-slate-500 font-medium">Catatan HRD:</span>
                                <p class="text-slate-700 dark:text-slate-300 leading-normal">{{ $displayNotes }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                        <div>
                            @if($leave->attachment)
                                <a href="{{ asset('storage/' . $leave->attachment) }}" target="_blank" class="inline-flex items-center gap-1.5 text-[11px] text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 font-semibold underline">
                                    <i data-lucide="paperclip" class="w-3.5 h-3.5"></i>
                                    <span>Lihat Lampiran</span>
                                </a>
                            @endif
                        </div>
                        @if($leave->status === 'Pending')
                            <form id="cancel-form-{{ $leave->id }}" action="{{ route('my-leaves.destroy', $leave->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" @click="confirmCancel('{{ $leave->id }}', '{{ $leaveName }}')"
                                    class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/50 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900/50 text-xs font-bold transition-colors cursor-pointer">
                                    Batalkan Pengajuan
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-8 text-center text-slate-400 dark:text-slate-500">
                    <div class="flex flex-col items-center justify-center gap-2">
                        <i data-lucide="calendar-x" class="w-8 h-8 text-slate-300 dark:text-slate-600"></i>
                        <p class="text-xs font-semibold">Anda belum memiliki riwayat pengajuan izin/cuti.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- ADD MODAL FORM -->
        <template x-teleport="body">
            <div x-show="showAddModal" 
                 x-init="if (window.lucide) window.lucide.createIcons()"
                 @click.self="closeModal()" 
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs overflow-y-auto" 
                 style="display: none; margin-top: 0px !important; z-index: 9999;">
                <div @click.outside="closeModal()" 
                     class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden text-left transform transition-all sm:my-8">
                    
                    <!-- Modal Header -->
                    <div class="flex justify-between items-center border-b border-slate-200 dark:border-slate-800 px-6 py-4 bg-slate-50/50 dark:bg-slate-900/50">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-slate-50">Ajukan Izin / Cuti Baru</h3>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Isi formulir pengajuan izin atau cuti mandiri Anda.</p>
                            </div>
                        </div>
                        <button type="button" @click="closeModal()" class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-500 transition-colors cursor-pointer">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <!-- Modal Body Form -->
                    <form method="POST" 
                          action="{{ route('my-leaves.store') }}" 
                          enctype="multipart/form-data" 
                          id="leaveForm" 
                          @submit="isSubmitting = true" 
                          class="p-6 space-y-4 text-xs">
                        @csrf
                        
                        @if($errors->any())
                            <div class="bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60 rounded-xl p-3.5 mb-2">
                                <div class="flex items-center gap-2 mb-1.5 text-rose-700 dark:text-rose-400 font-bold text-xs">
                                    <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                                    <span>Gagal Mengajukan:</span>
                                </div>
                                <ul class="list-disc list-inside text-[11px] text-rose-600 dark:text-rose-400 space-y-1 ml-6">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <!-- JENIS IZIN -->
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                                Jenis Izin / Cuti <span class="text-rose-500">*</span>
                            </label>
                            <select name="leave_type_id" required 
                                class="w-full text-xs h-10 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-sans cursor-pointer">
                                <option value="" disabled selected>-- Pilih Jenis Izin / Cuti --</option>
                                @foreach($leaveTypes as $lt)
                                    <option value="{{ $lt->id }}" @selected(old('leave_type_id') == $lt->id)>
                                        {{ $lt->name }} ({{ $lt->status_code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('leave_type_id')
                                <span class="text-[11px] text-rose-500 block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- PERIODE TANGGAL -->
                        <div class="space-y-2">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                                        Tanggal Mulai <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="date" 
                                           name="start_date" 
                                           required 
                                           x-model="startDate"
                                           @change="onStartDateChange()"
                                           class="w-full text-xs h-10 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-mono">
                                    @error('start_date')
                                        <span class="text-[11px] text-rose-500 block mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                                        Tanggal Selesai <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="date" 
                                           name="end_date" 
                                           required 
                                           x-model="endDate"
                                           :min="startDate"
                                           class="w-full text-xs h-10 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-mono">
                                    @error('end_date')
                                        <span class="text-[11px] text-rose-500 block mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- LIVE DURATION INDICATOR PILL -->
                            <div x-show="durationDays > 0" 
                                 class="p-2.5 rounded-xl bg-indigo-50/80 dark:bg-indigo-950/40 border border-indigo-200/80 dark:border-indigo-800/60 text-indigo-700 dark:text-indigo-300 flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2 font-medium">
                                    <i data-lucide="clock" class="w-4 h-4 text-indigo-600 dark:text-indigo-400"></i>
                                    <span>Estimasi Durasi Pengajuan:</span>
                                </div>
                                <span class="font-bold px-2 py-0.5 rounded-md bg-indigo-100 dark:bg-indigo-900/60 text-indigo-800 dark:text-indigo-200 text-xs" x-text="durationDays + ' Hari'"></span>
                            </div>
                        </div>

                        <!-- KETERANGAN / ALASAN -->
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                                Keterangan / Alasan
                            </label>
                            <textarea name="reason" rows="3" placeholder="Tuliskan alasan atau keterangan izin/cuti secara jelas..." class="w-full text-xs p-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 resize-none">{{ old('reason') }}</textarea>
                            @error('reason')
                                <span class="text-[11px] text-rose-500 block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- FILE LAMPIRAN -->
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                                Berkas Lampiran <span class="text-slate-400 font-normal">(Surat Dokter / Bukti Pendukung)</span>
                            </label>
                            <input type="file" 
                                   name="attachment" 
                                   accept=".pdf,.png,.jpg,.jpeg,.doc,.docx" 
                                   class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-950/40 dark:file:text-indigo-300 cursor-pointer">
                            <span class="text-[11px] text-slate-400 dark:text-slate-500 block mt-1">Format: PDF, PNG, JPG, JPEG, DOC, DOCX. Maksimal 2MB.</span>
                            @error('attachment')
                                <span class="text-[11px] text-rose-500 block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- MODAL ACTION BUTTONS -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                            <button type="button" 
                                    @click="closeModal()" 
                                    class="h-10 px-4 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-750 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl transition-colors cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" 
                                    :disabled="isSubmitting"
                                    class="h-10 px-5 bg-slate-900 dark:bg-slate-100 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-bold rounded-xl shadow-sm transition-all cursor-pointer inline-flex items-center gap-2">
                                <span x-show="!isSubmitting">Kirim Pengajuan</span>
                                <span x-show="isSubmitting" class="inline-flex items-center gap-2" style="display: none;">
                                    <svg class="animate-spin h-3.5 w-3.5 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Memproses...</span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

    </div>
</x-admin-layout>
