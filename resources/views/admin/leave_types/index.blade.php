<x-admin-layout>
    <div class="p-6 space-y-6">

        <!-- HEADER -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Tipe Izin Pegawai</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Katalog referensi tipe izin/cuti pegawai beserta pemetaan status kehadiran, absensi fisik, dan bonus.</p>
            </div>
            <div class="flex items-center gap-2 px-3 py-1.5 bg-blue-50/70 dark:bg-blue-950/30 text-blue-700 dark:text-blue-400 rounded-lg border border-blue-200/50 dark:border-blue-900/40 text-xs font-semibold shrink-0">
                <i data-lucide="shield-check" class="w-4 h-4"></i>
                <span>Terkelola Terpusat HRD</span>
            </div>
        </section>

        <!-- INFO BANNER -->
        <div class="bg-blue-50/60 dark:bg-blue-950/20 border border-blue-200/50 dark:border-blue-900/40 rounded-xl p-4 flex items-start gap-3.5 shadow-2xs">
            <div class="p-2 bg-blue-100/70 dark:bg-blue-900/40 rounded-lg text-blue-600 dark:text-blue-400 shrink-0 mt-0.5">
                <i data-lucide="info" class="w-4 h-4"></i>
            </div>
            <div class="space-y-0.5 text-xs text-left">
                <h4 class="font-bold text-slate-900 dark:text-slate-100">Kebijakan Terpusat HRD Yayasan</h4>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Daftar dan konfigurasi tipe izin disinkronkan secara otomatis dari HRD Yayasan untuk memastikan standarisasi aturan presensi dan perhitungan payroll. Untuk penambahan atau perubahan kebijakan tipe izin, silakan hubungi HRD atau ubah melalui <strong class="text-slate-700 dark:text-slate-200">Portal HRD</strong>.
                </p>
            </div>
        </div>

        <!-- TABLE SECTION -->
        <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden w-full">
            <div class="overflow-x-auto">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-16">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Tipe Izin</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kode Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Absensi Fisik</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Persetujuan</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Bonus Kehadiran</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-32">Status Sumber</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-900">
                        @forelse($leaveTypes as $index => $type)
                            <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-900/20 transition-colors">
                                <td class="px-6 py-4 text-slate-900 dark:text-slate-50 font-medium">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <span class="text-slate-900 dark:text-slate-50 font-bold tracking-tight block">{{ $type->name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $badgeClass = 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300';
                                        $label = 'Izin (I)';
                                        if ($type->status_code === 'S') {
                                            $badgeClass = 'bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-400 border border-red-100 dark:border-red-900/30';
                                            $label = 'Sakit (S)';
                                        } elseif ($type->status_code === 'C') {
                                            $badgeClass = 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 border border-amber-100 dark:border-amber-900/30';
                                            $label = 'Cuti (C)';
                                        } elseif ($type->status_code === 'H') {
                                            $badgeClass = 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/30';
                                            $label = 'Hadir (H)';
                                        }
                                    @endphp
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold tracking-wide {{ $badgeClass }}">{{ $label }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($type->requires_attendance)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">Wajib Absen</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/30">Bebas Absen</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($type->requires_approval)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 border border-amber-100 dark:border-amber-900/30">Perlu Persetujuan</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/30">Otomatis Setuju</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($type->gets_presence_bonus)
                                        <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-semibold">
                                            <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                            Dapat Bonus
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-slate-400 dark:text-slate-500">
                                            <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                            Tanpa Bonus
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200/60 dark:border-slate-700/60">
                                        <i data-lucide="check" class="w-3 h-3 text-emerald-500"></i>
                                        HRD Sync
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-slate-500 dark:text-slate-400">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <i data-lucide="info" class="w-8 h-8 text-slate-300 dark:text-slate-700"></i>
                                        <p class="font-medium text-xs">Belum ada tipe izin terdaftar dari HRD Pusat.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </div>
</x-admin-layout>
