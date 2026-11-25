@extends('layouts.admin')

@section('title', 'Data Rapor Mengaji')

@section('content')
<div class="p-4 sm:p-5 lg:p-6 space-y-4 lg:space-y-5" x-data="{
    previewPdfUrl: null,
    previewStudentName: '',
    previewGroupInfo: '',
    isFullscreen: false,

    openPreview(item) {
        this.previewPdfUrl = item.print_url;
        this.previewStudentName = item.full_name;
        this.previewGroupInfo = (item.group_name || 'Mengaji') + ' - ' + (item.shift || '-') + ' (' + (item.classroom_code || item.classroom_name || '-') + ')';
        this.isFullscreen = false;
        document.body.classList.add('overflow-hidden');
    },

    closePreview() {
        this.previewPdfUrl = null;
        this.previewStudentName = '';
        this.previewGroupInfo = '';
        this.isFullscreen = false;
        document.body.classList.remove('overflow-hidden');
    },

    toggleFullscreen() {
        this.isFullscreen = !this.isFullscreen;
    }
}">

    <!-- ========================================================= -->
    <!-- GREETING / PAGE TITLE & ACTION BUTTONS                    -->
    <!-- ========================================================= -->
    <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3.5 w-full text-left">
        <div class="flex flex-col gap-0.5">
            <h2 class="text-base sm:text-lg font-bold tracking-tight text-slate-900 dark:text-slate-50">
                Data Rapor Mengaji
            </h2>
            <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Rekapitulasi capaian, bimbingan GPQ & PAI, dan status kelengkapan rapor mengaji murid.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap items-center gap-2 shrink-0">
            @if(!empty($printClassroomUrl))
            <a href="{{ $printClassroomUrl }}" target="_blank"
                class="inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer shrink-0">
                <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                <span>Cetak Rapor 1 Rombel</span>
            </a>
            @endif

            <a href="{{ $raporUrl }}/mengaji/bilqolam" target="_blank"
                class="inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 shadow-xs transition-colors cursor-pointer shrink-0">
                <i data-lucide="external-link" class="w-3.5 h-3.5 text-slate-400"></i>
                <span>SANS Rapor</span>
            </a>
        </div>
    </section>

    <!-- ========================================================= -->
    <!-- STATS SUMMARY CARDS                                       -->
    <!-- ========================================================= -->
    <section class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-3.5">
        <!-- Total Murid -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 lg:p-4 shadow-xs flex flex-col justify-between">
            <div class="flex justify-between items-start gap-2">
                <div>
                    <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Murid</p>
                    <h3 class="text-lg sm:text-xl font-black tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                        {{ number_format($totalFiltered) }}
                    </h3>
                </div>
                <div class="w-8 h-8 flex items-center justify-center bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 rounded-lg border border-indigo-100 dark:border-indigo-900/50 shrink-0">
                    <i data-lucide="users" class="w-4 h-4 shrink-0"></i>
                </div>
            </div>
            <div class="mt-2 text-[10px] text-slate-400 truncate">
                TA {{ $selectedYear?->name ?? '-' }} ({{ $selectedSemester }})
            </div>
        </div>

        <!-- Siap Cetak -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 lg:p-4 shadow-xs flex flex-col justify-between">
            <div class="flex justify-between items-start gap-2">
                <div>
                    <p class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Siap Cetak</p>
                    <h3 class="text-lg sm:text-xl font-black tracking-tight text-emerald-600 dark:text-emerald-400 mt-1">
                        {{ number_format($totalSiapCetak) }}
                    </h3>
                </div>
                <div class="w-8 h-8 flex items-center justify-center bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-lg border border-emerald-100 dark:border-emerald-900/50 shrink-0">
                    <i data-lucide="check-circle-2" class="w-4 h-4 shrink-0"></i>
                </div>
            </div>
            <div class="mt-2 text-[10px] text-emerald-600/80 dark:text-emerald-400/80 font-medium truncate">
                Catatan GPQ & PAI lengkap
            </div>
        </div>

        <!-- Belum Lengkap -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 lg:p-4 shadow-xs flex flex-col justify-between">
            <div class="flex justify-between items-start gap-2">
                <div>
                    <p class="text-[10px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">Belum Lengkap</p>
                    <h3 class="text-lg sm:text-xl font-black tracking-tight text-amber-600 dark:text-amber-400 mt-1">
                        {{ number_format($totalBelumLengkap) }}
                    </h3>
                </div>
                <div class="w-8 h-8 flex items-center justify-center bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 rounded-lg border border-amber-100 dark:border-amber-900/50 shrink-0">
                    <i data-lucide="clock" class="w-4 h-4 shrink-0"></i>
                </div>
            </div>
            <div class="mt-2 text-[10px] text-amber-600/80 dark:text-amber-400/80 font-medium truncate">
                Sebagian catatan/nilai belum terisi
            </div>
        </div>

        <!-- Belum Diisi -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 lg:p-4 shadow-xs flex flex-col justify-between">
            <div class="flex justify-between items-start gap-2">
                <div>
                    <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Belum Diisi</p>
                    <h3 class="text-lg sm:text-xl font-black tracking-tight text-slate-600 dark:text-slate-300 mt-1">
                        {{ number_format($totalBelumDiisi) }}
                    </h3>
                </div>
                <div class="w-8 h-8 flex items-center justify-center bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 rounded-lg border border-slate-200 dark:border-slate-700 shrink-0">
                    <i data-lucide="file-question" class="w-4 h-4 shrink-0"></i>
                </div>
            </div>
            <div class="mt-2 text-[10px] text-slate-400 truncate">
                Belum ada data nilai atau catatan
            </div>
        </div>
    </section>

    <!-- ========================================================= -->
    <!-- SEARCH & FILTERS (WITHOUT LABELS)                         -->
    <!-- ========================================================= -->
    <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 shadow-xs w-full">
        <form method="GET" action="{{ route('rekap-rapor-mengaji.index') }}" class="space-y-3">
            <!-- Top Row: Semester Tabs & Tahun Ajaran Selector -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                <!-- Semester Pill Buttons -->
                <div class="flex items-center gap-1 p-1 bg-slate-100 dark:bg-slate-800/80 rounded-lg w-fit">
                    @foreach($semesters as $sem)
                    <button type="submit" name="semester" value="{{ $sem['code'] }}"
                        class="px-3 py-1.5 text-xs font-semibold rounded-md transition cursor-pointer {{ $selectedSemester === $sem['code'] ? 'bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200' }}">
                        <span>{{ $sem['name'] }}</span>
                    </button>
                    @endforeach
                </div>

                <!-- Tahun Ajaran Dropdown -->
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-500 dark:text-slate-400 whitespace-nowrap">Tahun Ajaran:</span>
                    <select name="academic_year_id" onchange="this.form.submit()"
                        class="h-8.5 px-2.5 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer shadow-xs">
                        @foreach($academicYears as $ay)
                        <option value="{{ $ay->id }}" {{ $selectedYearId == $ay->id ? 'selected' : '' }}>
                            TA {{ $ay->name }} {{ $ay->is_active ? '(Aktif)' : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Filter Controls Row (NO LABELS) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-2.5 items-center">
                <!-- Filter Rombel -->
                <div>
                    <select name="classroom_id" onchange="this.form.submit()"
                        class="w-full h-9 px-2.5 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer shadow-xs">
                        @if($isAdmin || $isKoordinatorGpq)
                        <option value="">Semua Rombel</option>
                        @endif
                        @foreach($classrooms as $c)
                        <option value="{{ $c->id }}" {{ $selectedClassroomId == $c->id ? 'selected' : '' }}>
                            {{ $c->full_name ?? ($c->code ? $c->code . ' - ' . $c->name : $c->name) }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Kelompok Mengaji -->
                <div>
                    <select name="group_name" onchange="this.form.submit()"
                        class="w-full h-9 px-2.5 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer shadow-xs">
                        <option value="">Semua Kelompok</option>
                        @foreach($allGroups as $grp)
                        <option value="{{ $grp }}" {{ $selectedGroup == $grp ? 'selected' : '' }}>
                            {{ $grp }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Shift -->
                <div>
                    <select name="shift" onchange="this.form.submit()"
                        class="w-full h-9 px-2.5 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer shadow-xs">
                        <option value="">Semua Shift</option>
                        @foreach($allShifts as $shf)
                        <option value="{{ $shf }}" {{ $selectedShift == $shf ? 'selected' : '' }}>
                            {{ $shf }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status Rapor -->
                <div>
                    <select name="status" onchange="this.form.submit()"
                        class="w-full h-9 px-2.5 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer shadow-xs">
                        <option value="">Semua Status Rapor</option>
                        <option value="siap_cetak" {{ $selectedStatus === 'siap_cetak' ? 'selected' : '' }}>Siap Cetak</option>
                        <option value="belum_lengkap" {{ $selectedStatus === 'belum_lengkap' ? 'selected' : '' }}>Belum Lengkap</option>
                        <option value="belum_diisi" {{ $selectedStatus === 'belum_diisi' ? 'selected' : '' }}>Belum Diisi</option>
                    </select>
                </div>

                <!-- Search Input with Action Buttons -->
                <div class="flex items-center gap-1.5 w-full">
                    <div class="relative flex-1">
                        <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama / NIS..."
                            class="w-full h-9 pl-8 pr-2 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 dark:text-slate-50 placeholder-slate-400 shadow-xs">
                    </div>
                    <button type="submit"
                        class="h-9 px-3 bg-slate-900 dark:bg-slate-100 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 rounded-lg text-xs font-semibold shadow-xs transition cursor-pointer shrink-0">
                        Cari
                    </button>
                    @if($search || $selectedClassroomId || $selectedGroup || $selectedShift || $selectedStatus)
                    <a href="{{ route('rekap-rapor-mengaji.index', ['academic_year_id' => $selectedYearId, 'semester' => $selectedSemester]) }}"
                        class="h-9 w-9 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 shadow-xs transition shrink-0"
                        title="Reset Filter">
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </section>

    <!-- ========================================================= -->
    <!-- TABEL DATA RAPOR MENGAJI                                  -->
    <!-- ========================================================= -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 font-bold uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-center w-12">No</th>
                        <th class="px-4 py-3 min-w-[200px]">Murid & Rombel</th>
                        <th class="px-4 py-3 min-w-[220px]">Kelompok & Pengampu</th>
                        <th class="px-4 py-3 min-w-[260px]">Catatan Perkembangan</th>
                        <th class="px-4 py-3 text-center w-32">Status</th>
                        <th class="px-4 py-3 text-right w-44">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($paginatedStudents as $index => $item)
                    <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                        <!-- 1. No -->
                        <td class="px-4 py-3 text-center font-medium text-slate-400">
                            {{ $paginatedStudents->firstItem() + $index }}
                        </td>

                        <!-- 2. Murid & Rombel -->
                        <td class="px-4 py-3">
                            <div class="flex items-start gap-2.5">
                                <div class="w-7 h-7 rounded-full bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200/50 dark:border-indigo-800/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                                    {{ substr($item->full_name, 0, 1) }}
                                </div>
                                <div class="space-y-0.5">
                                    <div class="font-bold text-slate-900 dark:text-slate-100 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                                        {{ $item->full_name }}
                                    </div>
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                            {{ $item->classroom_code ?: $item->classroom_name }}
                                        </span>
                                        @if($item->status_student === 'PDBK')
                                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-bold bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/60">
                                            PDBK
                                        </span>
                                        @endif
                                        <span class="text-[11px] text-slate-400">
                                            NIS: {{ $item->nis ?: '-' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- 3. Kelompok Mengaji & Pengampu -->
                        <td class="px-4 py-3">
                            <div class="space-y-1">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="font-semibold text-slate-800 dark:text-slate-200 text-xs">
                                        {{ $item->group_name }}
                                    </span>
                                    <span class="inline-flex items-center rounded-md px-1.5 py-0.5 text-[10px] font-bold bg-teal-50 dark:bg-teal-950/50 text-teal-700 dark:text-teal-300 border border-teal-200/60 dark:border-teal-800/60">
                                        {{ $item->shift }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 text-[11px] text-slate-500 dark:text-slate-400">
                                    <span>Jilid/Target: <strong class="text-slate-700 dark:text-slate-300">{{ $item->active_jilid }}</strong></span>
                                    @if($item->location && $item->location !== '-')
                                    <span>•</span>
                                    <span>Ruang: {{ $item->location }}</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate max-w-[220px]" title="Guru GPQ: {{ $item->gpq_teacher_name }}">
                                    Pengampu GPQ: <span class="font-medium text-slate-700 dark:text-slate-300">{{ $item->gpq_teacher_name }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- 4. Catatan Perkembangan -->
                        <td class="px-4 py-3 max-w-[280px]">
                            <div class="space-y-1.5">
                                @if(!empty($item->notes_gpq))
                                <div>
                                    <span class="text-[10px] font-bold text-teal-700 dark:text-teal-400 uppercase tracking-wider block">Catatan Guru GPQ:</span>
                                    <p class="text-xs text-slate-600 dark:text-slate-300 line-clamp-2 leading-relaxed" title="{{ $item->notes_gpq }}">
                                        "{{ $item->notes_gpq }}"
                                    </p>
                                </div>
                                @endif

                                @if(!empty($item->notes_pai))
                                <div>
                                    <span class="text-[10px] font-bold text-indigo-700 dark:text-indigo-400 uppercase tracking-wider block">Catatan Guru PAI:</span>
                                    <p class="text-xs text-slate-600 dark:text-slate-300 line-clamp-2 leading-relaxed" title="{{ $item->notes_pai }}">
                                        "{{ $item->notes_pai }}"
                                    </p>
                                </div>
                                @endif

                                @if(empty($item->notes_gpq) && empty($item->notes_pai))
                                <span class="text-xs text-slate-400 italic">Belum ada catatan pembimbing</span>
                                @endif
                            </div>
                        </td>

                        <!-- 5. Status Rapor -->
                        <td class="px-4 py-3 text-center">
                            @if($item->status_rapor === 'siap_cetak')
                            <div class="inline-flex items-center gap-1.5 font-semibold text-emerald-600 dark:text-emerald-400 text-xs whitespace-nowrap">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                <span>Siap Cetak</span>
                            </div>
                            @elseif($item->status_rapor === 'belum_lengkap')
                            <div class="inline-flex items-center gap-1.5 font-semibold text-amber-600 dark:text-amber-400 text-xs whitespace-nowrap">
                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse shrink-0"></span>
                                <span>Belum Lengkap</span>
                            </div>
                            @else
                            <div class="inline-flex items-center gap-1.5 font-medium text-slate-400 dark:text-slate-500 text-xs whitespace-nowrap">
                                <span class="w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-600 shrink-0"></span>
                                <span>Belum Diisi</span>
                            </div>
                            @endif
                        </td>

                        <!-- 6. Aksi (Pratinjau & Cetak PDF) -->
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex items-center justify-end gap-1.5">
                                <!-- Tombol Pratinjau Modal -->
                                <button @click="openPreview({{ json_encode($item) }})" type="button"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/50 px-2.5 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-200 shadow-2xs transition whitespace-nowrap cursor-pointer"
                                    title="Pratinjau Lembar Rapor Mengaji">
                                    <i data-lucide="eye" class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400 shrink-0"></i>
                                    <span>Pratinjau</span>
                                </button>

                                <!-- Tombol Cetak PDF Langsung -->
                                <a href="{{ $item->print_url }}" target="_blank"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white px-2.5 py-1.5 text-xs font-semibold shadow-xs transition whitespace-nowrap cursor-pointer"
                                    title="Cetak Lembar Rapor Mengaji PDF">
                                    <i data-lucide="printer" class="w-3.5 h-3.5 shrink-0"></i>
                                    <span>Cetak PDF</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400">
                            <i data-lucide="book-x" class="w-8 h-8 mx-auto mb-2 text-slate-300 dark:text-slate-600"></i>
                            <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">Tidak ada data murid mengaji yang sesuai filter.</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Silakan sesuaikan pilihan Rombel, Kelompok Mengaji, atau Shift pada filter di atas.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($paginatedStudents->hasPages())
        <div class="p-3 border-t border-slate-200 dark:border-slate-800">
            {{ $paginatedStudents->links() }}
        </div>
        @endif
    </div>

    <!-- ========================================================= -->
    <!-- LIVE PDF PREVIEW MODAL                                    -->
    <!-- ========================================================= -->
    <div x-show="previewPdfUrl" x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center p-3 sm:p-5 bg-slate-900/80 backdrop-blur-xs"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">

        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-800 flex flex-col transition-all overflow-hidden"
            :class="isFullscreen ? 'fixed inset-2 z-[101] w-auto h-auto' : 'w-full max-w-4xl h-[90vh]'"
            @click.outside="closePreview()">

            <!-- Modal Header -->
            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/50">
                <div class="flex items-center gap-2 overflow-hidden">
                    <div class="overflow-hidden">
                        <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 truncate" x-text="'Pratinjau Rapor Mengaji: ' + previewStudentName"></h4>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate" x-text="previewGroupInfo"></p>
                    </div>
                </div>

                <div class="flex items-center gap-1.5 shrink-0">
                    <!-- Tombol Fullscreen -->
                    <button type="button" @click="toggleFullscreen()"
                        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-800 transition cursor-pointer"
                        title="Beralih Layar Penuh">
                        <i :data-lucide="isFullscreen ? 'minimize-2' : 'maximize-2'" class="w-4 h-4"></i>
                    </button>

                    <!-- Tombol Cetak dari Modal -->
                    <a :href="previewPdfUrl" target="_blank"
                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold shadow-xs transition cursor-pointer">
                        <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                        <span>Cetak</span>
                    </a>

                    <!-- Tombol Tutup -->
                    <button type="button" @click="closePreview()"
                        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-800 transition cursor-pointer"
                        title="Tutup Pratinjau">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <!-- Modal PDF Iframe Body -->
            <div class="flex-1 bg-slate-100 dark:bg-slate-950 p-1">
                <template x-if="previewPdfUrl">
                    <iframe :src="previewPdfUrl" class="h-full w-full rounded-b-xl border-0" title="Pratinjau PDF Rapor Mengaji"></iframe>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endsection
