<x-admin-layout>
    @php
        $pendingLeaves = $leaves->where('status', 'Pending');
        $processedLeaves = $leaves->whereIn('status', ['Approved', 'Rejected']);
        $pendingCount = $pendingLeaves->count();
        $processedCount = $processedLeaves->count();
        $approvedCount = $leaves->where('status', 'Approved')->count();
        $ratio = $processedCount > 0 ? round(($approvedCount / $processedCount) * 100) : 100;
    @endphp

    <div class="p-6 space-y-6" x-data="{
        showDetailModal: false,
        selectedLeave: null,
        openDetail(leave) {
            this.selectedLeave = leave;
            this.showDetailModal = true;
        }
    }">



        <!-- GREETING / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-55 font-nasalization">Persetujuan Izin & Cuti (Approval)</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Tinjau, setujui, atau tolak permohonan izin dan cuti pegawai secara efisien.</p>
            </div>
        </section>

        <!-- STATS CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full">
            <!-- Menunggu Keputusan -->
            <div class="bg-white dark:bg-slate-955 p-4 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-lg">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Menunggu Approval</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-slate-55">{{ $pendingCount }}</p>
                </div>
            </div>
            <!-- Total Diproses -->
            <div class="bg-white dark:bg-slate-955 p-4 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-lg">
                    <i data-lucide="check-square" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Diproses</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-slate-55">{{ $processedCount }}</p>
                </div>
            </div>
            <!-- Persentase Persetujuan -->
            <div class="bg-white dark:bg-slate-955 p-4 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg">
                    <i data-lucide="percent" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Rasio Persetujuan</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-slate-55">{{ $ratio }}%</p>
                </div>
            </div>
        </div>

        <!-- MAIN CONTAINER GRID -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- LEFT: PENDING REQUESTS LIST -->
            <div class="lg:col-span-2 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-55 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        Perlu Tindakan Segera
                    </h3>
                    <span class="px-2.5 py-0.5 text-[10px] font-bold bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-400 rounded-full">{{ $pendingCount }} Pengajuan</span>
                </div>

                <div class="space-y-4">
                    @forelse($pendingLeaves as $item)
                        @php
                            $duration = $item->start_date->diffInDays($item->end_date) + 1;
                        @endphp
                        <div class="bg-white dark:bg-slate-955 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-sm space-y-4 hover:border-slate-300 dark:hover:border-slate-700 transition-all text-left">
                            <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                                <div class="flex items-start gap-3">
                                    @if($item->employee && $item->employee->photo)
                                        <img src="{{ str_contains($item->employee->photo, 'photos/') ? asset('storage/' . $item->employee->photo) : asset('storage/photos/' . $item->employee->photo) }}" class="w-10 h-10 rounded-full object-cover shrink-0 mt-0.5 border border-slate-200/50 dark:border-slate-800/80">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-900 flex items-center justify-center text-xs font-bold text-slate-700 dark:text-slate-355 shrink-0 mt-0.5 border border-slate-200/50 dark:border-slate-800/80">
                                            {{ strtoupper(substr($item->employee ? $item->employee->name : 'P', 0, 2)) }}
                                        </div>
                                    @endif
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="text-xs font-bold uppercase tracking-wide text-slate-900 dark:text-slate-55">{{ $item->employee ? $item->employee->name : '-' }}</h4>
                                            @if($item->type === 'Cuti')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 border border-blue-150 dark:border-blue-900/40">Cuti</span>
                                            @elseif($item->type === 'Izin')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-455 border border-amber-150 dark:border-amber-900/40">Izin</span>
                                            @elseif($item->type === 'Sakit')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-150 dark:border-rose-900/40">Sakit</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800">{{ $item->type }}</span>
                                            @endif
                                        </div>
                                        <p class="text-slate-700 dark:text-slate-300 text-xs font-semibold leading-relaxed">{{ $item->reason }}</p>
                                        <div class="flex items-center gap-4 text-[10px] text-slate-400 dark:text-slate-500 pt-1">
                                            <span class="flex items-center gap-1">
                                                <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                                {{ $item->start_date->format('d M Y') }} s/d {{ $item->end_date->format('d M Y') }} ({{ $duration }} Hari)
                                            </span>
                                            @if($item->attachment)
                                                <a href="{{ asset('storage/' . $item->attachment) }}" target="_blank" class="flex items-center gap-1 text-indigo-650 dark:text-indigo-400 hover:underline font-semibold">
                                                    <i data-lucide="paperclip" class="w-3.5 h-3.5"></i>
                                                    Lihat Dokumen
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 w-full sm:w-auto justify-end shrink-0 pt-2 sm:pt-0">
                                    <button @click="openDetail({
                                        id: '{{ $item->id }}',
                                        employee: { name: '{{ $item->employee ? addslashes($item->employee->name) : '-' }}' },
                                        type: '{{ $item->type }}',
                                        duration: '{{ $duration }}',
                                        start_date_formatted: '{{ $item->start_date->format('d M Y') }}',
                                        end_date_formatted: '{{ $item->end_date->format('d M Y') }}',
                                        reason: '{{ addslashes($item->reason) }}',
                                        attachment: '{{ $item->attachment }}'
                                    })" class="px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-[11px] font-semibold rounded-lg transition-colors cursor-pointer">
                                        Tinjau Detail
                                    </button>
                                    <form action="{{ route('leave-approvals.reject', $item->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 hover:bg-rose-50 dark:hover:bg-rose-955/20 border border-rose-100 dark:border-rose-900/40 text-rose-600 dark:text-rose-400 rounded-lg transition-colors cursor-pointer" title="Tolak">
                                            <i data-lucide="x-circle" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('leave-approvals.approve', $item->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-750 dark:hover:bg-emerald-700 text-white rounded-lg transition-colors cursor-pointer" title="Setujui">
                                            <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-slate-950 border border-dashed border-slate-200 dark:border-slate-850 rounded-xl p-12 text-center text-slate-500 dark:text-slate-400">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <i data-lucide="check-circle-2" class="w-10 h-10 text-emerald-500 mb-2"></i>
                                <p class="font-bold text-sm text-slate-800 dark:text-slate-100">Semua Bersih!</p>
                                <p class="text-xs opacity-75">Tidak ada pengajuan izin/cuti yang menunggu persetujuan.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- RIGHT: RECENTLY PROCESSED HISTORY LOG -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-55 flex items-center gap-2">
                    <i data-lucide="history" class="w-4 h-4 text-slate-500"></i>
                    Riwayat Keputusan Terbaru
                </h3>

                <div class="bg-white dark:bg-slate-955 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-sm divide-y divide-slate-100 dark:divide-slate-800/80 space-y-3.5">
                    @forelse($processedLeaves->take(5) as $historyItem)
                        <div class="py-3 {{ !$loop->first ? 'border-t border-slate-100 dark:border-slate-800/80' : '' }} space-y-1.5 text-left">
                            <div class="flex justify-between items-start gap-2">
                                <div>
                                    <h5 class="text-xs font-bold text-slate-850 dark:text-slate-200 line-clamp-1 uppercase">{{ $historyItem->employee ? $historyItem->employee->name : '-' }}</h5>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500">{{ $historyItem->type }} • {{ $historyItem->start_date->diffInDays($historyItem->end_date) + 1 }} Hari</p>
                                </div>
                                @if($historyItem->status === 'Approved')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/30">Disetujui</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-rose-50 dark:bg-rose-955/20 text-rose-700 dark:text-rose-400 border border-rose-100 dark:border-rose-900/30">Ditolak</span>
                                @endif
                            </div>
                            <div class="flex justify-between items-center text-[10px] text-slate-400 dark:text-slate-550">
                                <span>{{ $historyItem->notes ?? ($historyItem->status === 'Approved' ? 'Disetujui' : 'Ditolak') }}</span>
                                <span>{{ $historyItem->updated_at->format('Y-m-d H:i') }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500 dark:text-slate-500 text-center py-6">Belum ada keputusan yang diproses.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>    <!-- MODAL: DETAIL PENGAJUAN -->
    <template x-teleport="body">
        <div x-show="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-955/40 dark:bg-slate-955/60 backdrop-blur-sm transition-opacity" style="display: none; margin-top: 0px !important; z-index: 9999;" x-transition>
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-855 rounded-xl shadow-xl w-full max-w-md overflow-hidden" @click.away="showDetailModal = false">
                <div class="px-6 py-4 border-b border-slate-150 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-55 flex items-center gap-2">
                        <i data-lucide="info" class="w-4 h-4 text-indigo-650 dark:text-indigo-400"></i>
                        Tinjau Pengajuan
                    </h3>
                    <button @click="showDetailModal = false" class="p-1 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-slate-400 hover:text-slate-650 transition-colors cursor-pointer">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4" x-show="selectedLeave">
                    <template x-if="selectedLeave">
                        <div class="space-y-3 text-left">
                            <div class="flex justify-between border-b border-slate-100 dark:border-slate-800/80 pb-2">
                                <span class="text-xs text-slate-550 dark:text-slate-405 font-medium">Pegawai</span>
                                <span class="text-xs text-slate-900 dark:text-slate-50 font-bold" x-text="selectedLeave.employee.name"></span>
                            </div>
                            <div class="flex justify-between border-b border-slate-100 dark:border-slate-800/80 pb-2">
                                <span class="text-xs text-slate-550 dark:text-slate-405 font-medium">Tipe</span>
                                <span class="text-xs font-bold text-slate-900 dark:text-slate-50" x-text="selectedLeave.type"></span>
                            </div>
                            <div class="flex justify-between border-b border-slate-100 dark:border-slate-800/80 pb-2">
                                <span class="text-xs text-slate-550 dark:text-slate-405 font-medium">Waktu</span>
                                <span class="text-xs text-slate-900 dark:text-slate-50 font-semibold" x-text="`${selectedLeave.start_date_formatted} s/d ${selectedLeave.end_date_formatted}`"></span>
                            </div>
                            <div class="flex justify-between border-b border-slate-100 dark:border-slate-800/80 pb-2">
                                <span class="text-xs text-slate-550 dark:text-slate-405 font-medium">Durasi</span>
                                <span class="text-xs text-slate-900 dark:text-slate-50 font-bold" x-text="`${selectedLeave.duration} Hari`"></span>
                            </div>
                            <div class="flex flex-col gap-1 border-b border-slate-100 dark:border-slate-800/80 pb-2">
                                <span class="text-xs text-slate-550 dark:text-slate-405 font-medium">Alasan Pengajuan</span>
                                <span class="text-xs text-slate-850 dark:text-slate-200 bg-slate-50 dark:bg-slate-950 p-2.5 rounded-lg border border-slate-100 dark:border-slate-850/80 mt-1" x-text="selectedLeave.reason || '-'"></span>
                            </div>
                            <div class="flex justify-between border-b border-slate-100 dark:border-slate-800/80 pb-2 items-center">
                                <span class="text-xs text-slate-550 dark:text-slate-405 font-medium">Lampiran Dokumen</span>
                                <span class="text-xs text-slate-900 dark:text-slate-50">
                                    <template x-if="selectedLeave.attachment">
                                        <a :href="`{{ asset('storage') }}/${selectedLeave.attachment}`" target="_blank" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700 dark:text-blue-400 font-semibold hover:underline">
                                            <i data-lucide="paperclip" class="w-3.5 h-3.5"></i> Lihat Lampiran
                                        </a>
                                    </template>
                                    <template x-if="!selectedLeave.attachment">
                                        <span class="inline-flex items-center gap-1 text-slate-400 dark:text-slate-500 italic">Tidak ada lampiran</span>
                                    </template>
                                </span>
                            </div>
                        </div>
                    </template>

                    <div class="pt-4 flex items-center justify-end gap-2 border-t border-slate-150 dark:border-slate-855" x-show="selectedLeave">
                        <form :action="`{{ url('leave-approvals') }}/${selectedLeave ? selectedLeave.id : ''}/reject`" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 hover:bg-rose-50 dark:hover:bg-rose-955/20 border border-rose-200 dark:border-rose-900/60 text-rose-600 dark:text-rose-400 text-xs font-semibold rounded-lg transition-colors cursor-pointer flex items-center justify-center gap-1.5">
                                <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                                Tolak
                            </button>
                        </form>
                        <form :action="`{{ url('leave-approvals') }}/${selectedLeave ? selectedLeave.id : ''}/approve`" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-750 dark:hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer flex items-center justify-center gap-1.5">
                                <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                Setujui
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>
</x-admin-layout>
