<x-admin-layout>
    <div class="p-3.5 sm:p-5 lg:p-6 space-y-3 sm:space-y-3.5" x-data="teacherDocsApp()">

        <!-- ========================================================================= -->
        <!-- HEADER SECTION                                                            -->
        <!-- ========================================================================= -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-base sm:text-lg font-bold tracking-tight text-slate-900 dark:text-slate-50">Berkas Guru</h2>
                <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400">
                    Pengarsipan perangkat pembelajaran (PROTA, PROSEM, RPP), anggaran (RKAS, LPJ), dan supervisi administrasi guru.
                </p>
            </div>

            <!-- Header Actions -->
            <div class="flex flex-wrap items-center gap-2 shrink-0">

                @if($isAdmin)
                <!-- Export Matrix Excel -->
                <a href="{{ route('teacher-documents.export.excel', ['academic_year_id' => $selectedYearId]) }}"
                    class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] sm:text-xs font-semibold rounded-lg shadow-sm transition-colors">
                    <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5"></i>
                    <span>Ekspor Matriks</span>
                </a>

                <!-- Print Matrix -->
                <a href="{{ route('teacher-documents.print', ['academic_year_id' => $selectedYearId]) }}" target="_blank"
                    class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 bg-slate-700 hover:bg-slate-800 text-white text-[11px] sm:text-xs font-semibold rounded-lg shadow-sm transition-colors">
                    <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                    <span>Cetak Matriks</span>
                </a>
                @endif

                <!-- Upload New Document -->
                <button type="button" @click="openCreateModal()"
                    class="inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 sm:px-4 sm:py-2 bg-purple-600 hover:bg-purple-700 text-white text-[11px] sm:text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">
                    <i data-lucide="upload-cloud" class="w-3.5 h-3.5"></i>
                    <span>Unggah Berkas Baru</span>
                </button>
            </div>
        </section>

        <!-- ========================================================================= -->
        <!-- FLASH NOTIFICATIONS                                                       -->
        <!-- ========================================================================= -->


        @if(isset($errors) && $errors->any())
            <div class="bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 p-3 sm:p-3.5 rounded-xl flex items-start gap-2.5 text-xs shadow-xs">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0 mt-0.5"></i>
                <div>
                    <span class="font-bold">Terjadi kesalahan input:</span>
                    <ul class="list-disc list-inside mt-1 space-y-0.5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- ========================================================================= -->
        <!-- TAB SWITCHER & KPI STATUS: 1. ARSIP BERKAS  |  2. MATRIKS KELENGKAPAN     -->
        <!-- ========================================================================= -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-200 dark:border-slate-800 gap-2 sm:gap-4">
            <div class="flex items-center space-x-1 sm:space-x-2 shrink-0">
                <a href="{{ route('teacher-documents.index', array_merge(request()->query(), ['tab' => 'files'])) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold border-b-2 transition-colors {{ $activeTab === 'files' ? 'border-purple-600 text-purple-600 dark:text-purple-400 dark:border-purple-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400 hover:border-slate-300' }}">
                    <i data-lucide="folder-open" class="w-4 h-4"></i>
                    <span>Daftar Berkas</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 font-bold">
                        {{ $documents->total() }}
                    </span>
                </a>

                <a href="{{ route('teacher-documents.index', array_merge(request()->query(), ['tab' => 'matrix'])) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold border-b-2 transition-colors {{ $activeTab === 'matrix' ? 'border-purple-600 text-purple-600 dark:text-purple-400 dark:border-purple-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400 hover:border-slate-300' }}">
                    <i data-lucide="table-2" class="w-4 h-4"></i>
                    <span>Matriks Kelengkapan Administrasi</span>
                </a>
            </div>

            <!-- Status KPI Dots (Pojok Kanan Sejajar dengan Tab) -->
            <div class="flex items-center gap-2.5 shrink-0 sm:ml-auto pb-2 sm:pb-0">
                <span class="inline-flex flex-wrap items-center gap-3 text-[11px] font-semibold text-slate-500 dark:text-slate-400">
                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                        <span class="w-2 h-2 rounded-full bg-purple-500 inline-block"></span> Total: <strong class="text-slate-800 dark:text-slate-200 font-mono">{{ $stats['total'] }}</strong>
                    </span>
                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span> Disetujui: <strong class="text-emerald-600 dark:text-emerald-400 font-mono">{{ $stats['approved'] }}</strong>
                    </span>
                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                        <span class="w-2 h-2 rounded-full bg-amber-500 inline-block"></span> Menunggu: <strong class="text-amber-600 dark:text-amber-400 font-mono">{{ $stats['pending'] }}</strong>
                    </span>
                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                        <span class="w-2 h-2 rounded-full bg-rose-500 inline-block"></span> Revisi: <strong class="text-rose-600 dark:text-rose-400 font-mono">{{ $stats['needs_revision'] }}</strong>
                    </span>
                    @if($isAdmin)
                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                        <span class="w-2 h-2 rounded-full bg-indigo-500 inline-block"></span> Guru: <strong class="text-indigo-600 dark:text-indigo-400 font-mono">{{ $stats['teachers_count'] }}</strong><span class="text-[10px] text-slate-400">/{{ $teachers->count() }}</span>
                    </span>
                    @endif
                </span>
            </div>
        </div>

        @if($activeTab === 'files')
            <!-- ========================================================================= -->
            <!-- TAB 1: ARSIP & DAFTAR BERKAS (FILTER + TABEL)                            -->
            <!-- ========================================================================= -->

            <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs overflow-hidden transition-all w-full text-left">
                <!-- Table Header Bar: Filters Toolbar -->
                <div class="p-2 sm:px-3 sm:py-2.5 border-b border-slate-100 dark:border-slate-800 overflow-x-auto">
                    <form method="GET" action="{{ route('teacher-documents.index') }}" id="docsFilterForm" class="flex items-center gap-1.5 flex-nowrap min-w-max">
                        <input type="hidden" name="tab" value="files">

                        <!-- Search Input -->
                        <div x-data="{ searchVal: '{{ $search }}' }" class="flex items-center w-36 sm:w-44 search-container bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 rounded-lg overflow-hidden focus-within:ring-1 focus-within:ring-purple-500 focus-within:border-purple-500 shrink-0">
                            <input type="text" name="search" x-model="searchVal" placeholder="Cari berkas..."
                                style="border: none !important; outline: none !important; box-shadow: none !important;"
                                class="w-full h-8 pl-2.5 pr-1 text-xs bg-transparent text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:ring-0">
                            
                            <button type="button" x-show="searchVal.trim() !== ''" @click="searchVal = ''; $el.closest('.search-container').querySelector('input').focus();" class="h-8 px-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer bg-transparent border-0 flex items-center justify-center" title="Bersihkan pencarian">
                                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                            </button>

                            <button type="submit" 
                                :class="searchVal.trim() !== '' ? 'bg-purple-600 text-white dark:bg-purple-500 dark:text-white' : 'bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300'"
                                class="h-8 px-2 font-bold text-xs transition-all duration-150 cursor-pointer flex items-center justify-center border-l border-slate-200 dark:border-slate-700">
                                <i data-lucide="search" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>

                        <!-- 1. Tahun Ajaran -->
                        <select name="academic_year_id" onchange="this.form.submit()"
                            class="h-8 pl-2.5 pr-7 text-xs font-semibold bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 rounded-lg text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-purple-500 cursor-pointer shrink-0">
                            <option value="all" {{ (string)$selectedYearId === 'all' ? 'selected' : '' }}>Semua Tahun</option>
                            @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}" {{ (string)$selectedYearId === (string)$ay->id ? 'selected' : '' }}>
                                    {{ $ay->name }}{{ $ay->is_active ? ' (Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>

                        @if($isAdmin)
                        <!-- 2. Guru / Pendidik -->
                        <select name="teacher_id" onchange="this.form.submit()"
                            class="h-8 pl-2.5 pr-7 text-xs font-semibold bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 rounded-lg text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-purple-500 cursor-pointer max-w-[150px] truncate shrink-0">
                            <option value="all">Semua Guru</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}" {{ $selectedTeacherId == $t->id ? 'selected' : '' }}>
                                    {{ $t->name }}
                                </option>
                            @endforeach
                        </select>
                        @endif

                        <!-- 3. Kategori Berkas -->
                        <select name="category" onchange="this.form.submit()"
                            class="h-8 pl-2.5 pr-7 text-xs font-semibold bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 rounded-lg text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-purple-500 cursor-pointer max-w-[145px] truncate shrink-0">
                            <option value="all">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ $selectedCategory === $cat ? 'selected' : '' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>

                        <!-- 4. Semester -->
                        <select name="semester" onchange="this.form.submit()"
                            class="h-8 pl-2.5 pr-7 text-xs font-semibold bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 rounded-lg text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-purple-500 cursor-pointer shrink-0">
                            <option value="all">Semua Semester</option>
                            @foreach($semesters as $sem)
                                <option value="{{ $sem }}" {{ $selectedSemester === $sem ? 'selected' : '' }}>
                                    {{ $sem }}
                                </option>
                            @endforeach
                        </select>

                        <!-- 5. Status Supervisi -->
                        <select name="status" onchange="this.form.submit()"
                            class="h-8 pl-2.5 pr-7 text-xs font-semibold bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 rounded-lg text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-purple-500 cursor-pointer shrink-0">
                            <option value="all">Semua Status</option>
                            <option value="Menunggu Review" {{ $selectedStatus === 'Menunggu Review' ? 'selected' : '' }}>Menunggu Review</option>
                            <option value="Disetujui" {{ $selectedStatus === 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                            <option value="Perlu Revisi" {{ $selectedStatus === 'Perlu Revisi' ? 'selected' : '' }}>Perlu Revisi</option>
                        </select>

                        <!-- 6. Jumlah Baris -->
                        <select name="per_page" onchange="this.form.submit()" title="Jumlah data per halaman"
                            class="h-8 pl-2 pr-6 text-xs font-semibold bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 rounded-lg text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-purple-500 cursor-pointer shrink-0 w-[70px]">
                            <option value="10" {{ ($perPage ?? 15) == 10 ? 'selected' : '' }}>10</option>
                            <option value="15" {{ ($perPage ?? 15) == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ ($perPage ?? 15) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ ($perPage ?? 15) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ ($perPage ?? 15) == 100 ? 'selected' : '' }}>100</option>
                            <option value="99999" {{ ($perPage ?? 15) == 99999 || ($perPage ?? '') === 'all' ? 'selected' : '' }}>Semua</option>
                        </select>

                        @if($search || $selectedCategory !== 'all' || ($isAdmin && $selectedTeacherId !== 'all') || $selectedStatus !== 'all' || $selectedSemester !== 'all' || (string)$selectedYearId !== (string)($activeAcademicYear?->id ?? '') || (request()->filled('per_page') && request('per_page') != 15))
                            <a href="{{ route('teacher-documents.index', ['tab' => 'files']) }}" class="h-8 w-8 flex items-center justify-center bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300 rounded-lg transition-colors shrink-0" title="Reset Filter">
                                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                            </a>
                        @endif
                    </form>
                </div>

                <!-- Table Container with Vertical Scroll -->
                <div class="overflow-x-auto overflow-y-auto" style="max-height: calc(100vh - 220px);">
                    <table class="w-full text-xs border-collapse">
                        <thead class="sticky top-0 z-10 shadow-sm">
                            <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900">
                                <th class="px-3 py-2.5 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-10 whitespace-nowrap">No</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap min-w-[180px]">Guru / Pendidik</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap w-24">Jenis</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap min-w-[200px]">Dokumen</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap w-24">Semester</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap min-w-[140px]">Keterangan</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap">Status Supervisi</th>
                                <th class="px-4 py-2.5 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium text-slate-700 dark:text-slate-300">
                            @forelse($documents as $idx => $doc)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                                    <!-- No -->
                                    <td class="px-3 py-2.5 text-center text-slate-400 font-bold whitespace-nowrap">
                                        {{ $documents->firstItem() ? ($documents->firstItem() + $idx) : ($idx + 1) }}
                                    </td>

                                    <!-- Guru -->
                                    <td class="px-4 py-2.5">
                                        <div class="font-bold text-slate-900 dark:text-slate-100">
                                            {{ $doc->teacher ? $doc->teacher->name : 'Guru Dihapus' }}
                                        </div>
                                        <div class="text-[11px] text-slate-400 font-mono">
                                            NIY: {{ $doc->teacher?->niy ?? '-' }}
                                        </div>
                                    </td>

                                    <!-- Kategori -->
                                    <td class="px-4 py-2.5 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $doc->category_badge_class }}">
                                            {{ $doc->category }}
                                        </span>
                                    </td>

                                    <!-- Dokumen -->
                                    <td class="px-4 py-2.5">
                                        @php
                                            $files = $doc->files_list;
                                            $totalFiles = count($files);
                                        @endphp

                                        @if($totalFiles === 1)
                                            @php $f = $files[0]; @endphp
                                            @if(!empty($doc->title) && $doc->title !== $f['title'])
                                                <div class="font-bold text-slate-900 dark:text-slate-100 text-xs mb-1">
                                                    {{ $doc->title }}
                                                </div>
                                                <div>
                                                    <a href="{{ route('teacher-documents.download', ['id' => $doc->id, 'file' => 0]) }}"
                                                        class="inline-flex items-center gap-1.5 text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300 font-medium text-xs hover:underline transition-colors"
                                                        title="Unduh {{ $f['file_name'] }}">
                                                        <i data-lucide="{{ $f['icon'] }}" class="w-3.5 h-3.5 shrink-0"></i>
                                                        <span class="truncate max-w-[260px]">{{ $f['title'] }}</span>
                                                    </a>
                                                </div>
                                            @else
                                                <div>
                                                    <a href="{{ route('teacher-documents.download', ['id' => $doc->id, 'file' => 0]) }}"
                                                        class="inline-flex items-center gap-1.5 text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300 font-bold text-xs hover:underline transition-colors"
                                                        title="Unduh {{ $f['file_name'] }}">
                                                        <i data-lucide="{{ $f['icon'] }}" class="w-3.5 h-3.5 shrink-0"></i>
                                                        <span class="truncate max-w-[260px]">{{ $doc->title ?: $f['title'] }}</span>
                                                    </a>
                                                </div>
                                            @endif
                                        @elseif($totalFiles > 1)
                                            <div class="font-bold text-slate-900 dark:text-slate-100 text-xs mb-1">
                                                {{ $doc->title }}
                                            </div>
                                            <div class="space-y-1">
                                                @foreach($files as $fIdx => $f)
                                                    <div>
                                                        <a href="{{ route('teacher-documents.download', ['id' => $doc->id, 'file' => $fIdx]) }}"
                                                            class="inline-flex items-center gap-1.5 text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300 font-medium text-xs hover:underline transition-colors"
                                                            title="Unduh {{ $f['file_name'] }}">
                                                            <i data-lucide="{{ $f['icon'] }}" class="w-3.5 h-3.5 shrink-0"></i>
                                                            <span class="truncate max-w-[260px]">{{ $f['title'] }}</span>
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="font-bold text-slate-900 dark:text-slate-100 text-xs">{{ $doc->title ?: '-' }}</span>
                                        @endif
                                    </td>

                                    <!-- Semester -->
                                    <td class="px-4 py-2.5 whitespace-nowrap">
                                        <span class="text-slate-700 dark:text-slate-300 font-semibold">
                                            {{ $doc->semester }}
                                        </span>
                                        <div class="text-[10px] text-slate-400">
                                            {{ \Carbon\Carbon::parse($doc->created_at)->format('d/m/Y') }}
                                        </div>
                                    </td>

                                    <!-- Keterangan Tambahan -->
                                    <td class="px-4 py-2.5">
                                        <p class="text-slate-600 dark:text-slate-400 line-clamp-2 text-[11px]" title="{{ $doc->notes }}">
                                            {{ $doc->notes ?: '-' }}
                                        </p>
                                    </td>

                                    <!-- Status Supervisi -->
                                    <td class="px-4 py-2.5 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $doc->status_badge_class }}">
                                            {{ $doc->status }}
                                        </span>
                                        @if($doc->feedback)
                                            <div class="text-[10px] text-rose-600 dark:text-rose-400 line-clamp-1 italic mt-0.5" title="Catatan Review: {{ $doc->feedback }}">
                                                "{{ $doc->feedback }}"
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Aksi -->
                                    <td class="px-4 py-2.5 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <!-- Detail Modal -->
                                            <button type="button" @click="openViewModal({{ json_encode($doc) }})" title="Lihat Detail Berkas"
                                                class="w-8 h-8 flex items-center justify-center hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-400 hover:text-purple-600 dark:hover:text-purple-400 transition-colors cursor-pointer">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </button>

                                            <!-- Review / Status Modal (Admin / Kepala Sekolah) -->
                                            @if($isAdmin)
                                                <button type="button" @click="openReviewModal({{ json_encode($doc) }})" title="Supervisi / Verifikasi Berkas"
                                                    class="w-8 h-8 flex items-center justify-center hover:bg-emerald-50 dark:hover:bg-emerald-900/30 rounded-lg text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 transition-colors cursor-pointer">
                                                    <i data-lucide="check-square" class="w-4 h-4"></i>
                                                </button>
                                            @endif

                                            <!-- Edit Modal -->
                                            @if($isAdmin || $doc->uploaded_by == auth()->id())
                                                <button type="button" @click="openEditModal({{ json_encode($doc) }})" title="Edit Berkas"
                                                    class="w-8 h-8 flex items-center justify-center hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors cursor-pointer">
                                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                                </button>

                                                <!-- Delete -->
                                                <form method="POST" action="{{ route('teacher-documents.destroy', $doc->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus berkas ini?')" class="inline-flex items-center m-0 p-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" title="Hapus Berkas"
                                                        class="w-8 h-8 flex items-center justify-center hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg text-red-600 dark:text-red-400 hover:text-red-700 transition-colors cursor-pointer">
                                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <i data-lucide="folder-x" class="w-8 h-8 text-slate-300 dark:text-slate-600"></i>
                                            <p class="font-medium text-xs">Belum ada berkas guru yang ditemukan sesuai filter.</p>
                                            <button type="button" @click="openCreateModal()" class="mt-2 text-purple-600 dark:text-purple-400 text-xs font-semibold hover:underline">
                                                + Unggah Berkas Baru
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION (SAME STYLE AS GURU & PEGAWAI) -->
                @if($documents->total() > 0)
                    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800/80 bg-slate-50/20 dark:bg-slate-900/10 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-slate-500 dark:text-slate-400">
                        <div>
                            Menampilkan
                            <span class="font-bold text-slate-700 dark:text-slate-300">{{ $documents->firstItem() }}</span>
                            sampai
                            <span class="font-bold text-slate-700 dark:text-slate-300">{{ $documents->lastItem() }}</span>
                            dari
                            <span class="font-bold text-slate-700 dark:text-slate-300">{{ $documents->total() }}</span>
                            berkas
                        </div>
                        <div class="flex items-center gap-1.5 font-semibold">
                            @if ($documents->onFirstPage())
                                <span class="h-8 px-3 rounded-lg border border-slate-200 dark:border-slate-800 text-slate-400 dark:text-slate-600 flex items-center justify-center cursor-not-allowed select-none bg-slate-50 dark:bg-slate-900/20">Sebelumnya</span>
                            @else
                                <a href="{{ $documents->appends(request()->query())->previousPageUrl() }}" class="h-8 px-3 rounded-lg border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-900 text-slate-700 dark:text-slate-300 flex items-center justify-center transition-all bg-white dark:bg-slate-900">Sebelumnya</a>
                            @endif

                            <span class="px-3 py-1 font-medium text-slate-700 dark:text-slate-300">
                                Halaman {{ $documents->currentPage() }} dari {{ $documents->lastPage() }}
                            </span>

                            @if ($documents->hasMorePages())
                                <a href="{{ $documents->appends(request()->query())->nextPageUrl() }}" class="h-8 px-3 rounded-lg border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-900 text-slate-700 dark:text-slate-300 flex items-center justify-center transition-all bg-white dark:bg-slate-900">Berikutnya</a>
                            @else
                                <span class="h-8 px-3 rounded-lg border border-slate-200 dark:border-slate-800 text-slate-400 dark:text-slate-600 flex items-center justify-center cursor-not-allowed select-none bg-slate-50 dark:bg-slate-900/20">Berikutnya</span>
                            @endif
                        </div>
                    </div>
                @endif
            </section>

        @else
            <!-- ========================================================================= -->
            <!-- TAB 2: MATRIKS KELENGKAPAN ADMINISTRASI GURU                             -->
            <!-- ========================================================================= -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-xs space-y-4 p-4 sm:p-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="font-bold text-sm text-slate-900 dark:text-slate-100">Matriks Ceklis Supervisi Administrasi Guru</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Pantau kelengkapan perangkat tahunan (PROTA, PROSEM), pembelajaran (RPP), dan laporan (RKAS, LPJ) setiap guru.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Filter Tahun Ajaran Khusus Matriks -->
                        <form method="GET" action="{{ route('teacher-documents.index') }}" class="inline-flex items-center m-0 p-0">
                            <input type="hidden" name="tab" value="matrix">
                            <select name="academic_year_id" onchange="this.form.submit()"
                                title="Pilih Tahun Ajaran"
                                class="h-8 pl-3 pr-8 text-xs font-semibold bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-purple-500 cursor-pointer min-w-[130px]">
                                <option value="all" {{ (string)$selectedYearId === 'all' ? 'selected' : '' }}>Semua Tahun</option>
                                @foreach($academicYears as $ay)
                                    <option value="{{ $ay->id }}" {{ (string)$selectedYearId === (string)$ay->id ? 'selected' : '' }}>
                                        {{ $ay->name }}{{ $ay->is_active ? ' (Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </form>

                        <div class="h-4 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>

                        <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-slate-500">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span> Disetujui
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 inline-block ml-1"></span> Menunggu
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-300 dark:bg-slate-700 inline-block ml-1"></span> Belum Ada
                        </span>
                    </div>
                </div>

                <!-- Matrix Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 uppercase tracking-wider font-bold text-[10px]">
                                <th class="py-3 px-3.5 text-center w-10">No</th>
                                <th class="py-3 px-3.5 min-w-[200px]">Nama Guru / Pendidik</th>
                                <th class="py-3 px-3 text-center w-24">PROTA</th>
                                <th class="py-3 px-3 text-center w-24">PROSEM</th>
                                <th class="py-3 px-3 text-center w-24">RPP</th>
                                <th class="py-3 px-3 text-center w-24">RKAS</th>
                                <th class="py-3 px-3 text-center w-24">LPJ</th>
                                <th class="py-3 px-3 text-center w-24">Lainnya</th>
                                <th class="py-3 px-3 text-center w-28">Total Berkas</th>
                                <th class="py-3 px-3.5 text-center w-36">Persentase</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium text-slate-700 dark:text-slate-300">
                            @foreach($matrixData as $mIdx => $m)
                                @php
                                    $completedCats = 0;
                                    // Categories considered core: PROTA, PROSEM, RPP (3 main core pedagogy categories)
                                    foreach(['PROTA', 'PROSEM', 'RPP'] as $cCore) {
                                        if (($m['categories'][$cCore]['count'] ?? 0) > 0) $completedCats++;
                                    }
                                    $corePercent = round(($completedCats / 3) * 100);
                                @endphp
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="py-3 px-3.5 text-center text-slate-400 font-bold">
                                        {{ $mIdx + 1 }}
                                    </td>
                                    <td class="py-3 px-3.5">
                                        <div class="font-bold text-slate-900 dark:text-slate-100">
                                            {{ $m['teacher']->name }}
                                        </div>
                                        <div class="text-[11px] text-slate-400">
                                            {{ $m['teacher']->position ?: 'Tenaga Pendidik' }}
                                        </div>
                                    </td>

                                    <!-- Categories Columns -->
                                    @foreach($categories as $cat)
                                        @php
                                            $catStat = $m['categories'][$cat];
                                            $count = $catStat['count'];
                                            $approved = $catStat['approved'];
                                        @endphp
                                        <td class="py-3 px-3 text-center">
                                            @if($count > 0)
                                                @if($approved > 0)
                                                    <span class="inline-flex items-center justify-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                                        <i data-lucide="check" class="w-3 h-3"></i>
                                                        <span>{{ $count }}</span>
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center justify-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                                        <i data-lucide="clock" class="w-3 h-3"></i>
                                                        <span>{{ $count }}</span>
                                                    </span>
                                                @endif
                                            @else
                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-300 dark:text-slate-600 font-bold text-xs">
                                                    -
                                                </span>
                                            @endif
                                        </td>
                                    @endforeach

                                    <!-- Total Berkas -->
                                    <td class="py-3 px-3 text-center font-bold text-purple-700 dark:text-purple-300">
                                        {{ $m['total_uploaded'] }} File
                                    </td>

                                    <!-- Progress Bar -->
                                    <td class="py-3 px-3.5">
                                        <div class="space-y-1">
                                            <div class="flex items-center justify-between text-[10px] font-bold">
                                                <span class="text-slate-500">Perangkat Inti</span>
                                                <span class="{{ $corePercent === 100 ? 'text-emerald-600' : 'text-slate-700 dark:text-slate-300' }}">{{ $corePercent }}%</span>
                                            </div>
                                            <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden">
                                                <div class="h-full rounded-full transition-all duration-300 {{ $corePercent === 100 ? 'bg-emerald-500' : ($corePercent >= 50 ? 'bg-amber-500' : 'bg-rose-500') }}"
                                                    style="width: {{ $corePercent }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- ========================================================================= -->
        <!-- MODAL 1: UNGGAH / EDIT BERKAS GURU (SUPPORTS DYNAMIC MULTI-DOCUMENTS)     -->
        <!-- ========================================================================= -->
        <div x-show="modalOpen" x-cloak
            class="fixed inset-0 z-[99999] overflow-y-auto flex items-center justify-center p-3 sm:p-4"
            style="top: 0px !important; left: 0px !important; right: 0px !important; bottom: 0px !important; margin: 0px !important; z-index: 99999 !important; background-color: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px);">
            
            <div @click.outside="modalOpen = false"
                class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl shadow-2xl max-w-2xl w-full p-4 sm:p-6 text-left flex flex-col max-h-[92vh]">
                
                <!-- Fixed Header -->
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 dark:border-slate-800 shrink-0">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center ring-1 ring-purple-100 dark:ring-purple-900/40">
                            <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                        </span>
                        <div>
                            <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-slate-100"
                                x-text="isEdit ? 'Edit Data Berkas Guru' : 'Unggah Berkas Baru'">
                            </h3>
                            <p class="text-[11px] text-slate-400">
                                Lengkapi kategori berkas dan lampirkan dokumen administrasi.
                            </p>
                        </div>
                    </div>
                    <button type="button" @click="modalOpen = false" class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <!-- Form -->
                <form :action="formAction" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0 pt-3">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <!-- Scrollable Body -->
                    <div class="flex-1 overflow-y-auto py-1 space-y-4 pr-1 text-xs">
                        <!-- Banner Catatan Revisi jika sedang edit berkas yang direvisi -->
                        <template x-if="isEdit && currentFeedback">
                            <div class="p-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60 text-xs">
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="8" x2="12" y2="12"></line>
                                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                    </svg>
                                    <div>
                                        <span class="font-bold text-rose-800 dark:text-rose-300 block">Catatan Revisi dari Kepala Sekolah / Supervisor:</span>
                                        <p class="text-rose-700 dark:text-rose-200 mt-0.5 italic" x-text="currentFeedback"></p>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Row 1: Guru & Tahun Ajaran -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- Guru -->
                            @if($isAdmin)
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
                                    Guru Pemilik Berkas <span class="text-rose-500">*</span>
                                </label>
                                <select name="teacher_id" x-model="formData.teacher_id" required
                                    class="w-full h-10 px-3 bg-slate-50/80 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 outline-none cursor-pointer transition-all">
                                    <option value="">-- Pilih Guru --</option>
                                    @foreach($teachers as $t)
                                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @else
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
                                    Guru Pemilik Berkas
                                </label>
                                <input type="hidden" name="teacher_id" :value="formData.teacher_id || '{{ $myEmployeeId }}'">
                                <input type="text" readonly value="{{ auth()->user()->name }}"
                                    class="w-full h-10 px-3 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-500 dark:text-slate-400 cursor-not-allowed">
                            </div>
                            @endif

                            <!-- Tahun Ajaran -->
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
                                    Tahun Ajaran <span class="text-rose-500">*</span>
                                </label>
                                <select name="academic_year_id" x-model="formData.academic_year_id" required
                                    class="w-full h-10 px-3 bg-slate-50/80 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 outline-none cursor-pointer transition-all">
                                    @foreach($academicYears as $ay)
                                        <option value="{{ $ay->id }}">{{ $ay->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Row 2: Jenis Berkas & Semester -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- Jenis Berkas -->
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
                                    Kategori / Jenis Berkas <span class="text-rose-500">*</span>
                                </label>
                                <select name="category" x-model="formData.category" required
                                    class="w-full h-10 px-3 bg-slate-50/80 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 outline-none cursor-pointer transition-all">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}">{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Semester -->
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
                                    Semester <span class="text-rose-500">*</span>
                                </label>
                                <select name="semester" x-model="formData.semester" required
                                    class="w-full h-10 px-3 bg-slate-50/80 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 outline-none cursor-pointer transition-all">
                                    @foreach($semesters as $sem)
                                        <option value="{{ $sem }}">{{ $sem }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Judul / Nama Berkas Utama -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                                    Judul / Nama Berkas <span class="text-rose-500" x-show="isEdit">*</span>
                                </label>
                                <span class="text-[10px] text-slate-400" x-show="!isEdit">Opsional (Otomatis dari dokumen pertama jika kosong)</span>
                            </div>
                            <input type="text" name="title" x-model="formData.title" :required="isEdit"
                                placeholder="Contoh: Modul Ajar Sentra Main Peran Minggu 1-4 / RPP Semester Ganjil"
                                class="w-full h-10 px-3.5 bg-slate-50/80 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 outline-none transition-all">
                        </div>

                        <!-- DOKUMEN LAMPIRAN (UNIFIED FOR CREATE & EDIT) -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between pt-1">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-200">
                                        Daftar Dokumen Lampiran <span class="text-rose-500">*</span>
                                    </label>
                                    <p class="text-[10px] text-slate-400">
                                        <span x-show="!isEdit">Bisa unggah lebih dari 1 file sekaligus dalam 1 data berkas ini.</span>
                                        <span x-show="isEdit">Kelola file dalam berkas ini: ubah judul, ganti file, hapus, atau tambah dokumen baru.</span>
                                    </p>
                                </div>
                                <button type="button" @click="addFileItem()"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-50 hover:bg-purple-100 dark:bg-purple-950/60 dark:hover:bg-purple-900/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800 rounded-lg text-xs font-semibold shadow-2xs transition-all cursor-pointer shrink-0">
                                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                    <span>Tambah Dokumen</span>
                                </button>
                            </div>

                            <div class="space-y-3">
                                <template x-for="(item, idx) in fileItems" :key="item.id">
                                    <div class="p-3.5 bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200/90 dark:border-slate-700/80 rounded-xl space-y-3 transition-all hover:border-purple-300 dark:hover:border-purple-700/70">
                                        <div class="flex items-center justify-between pb-2 border-b border-slate-200/60 dark:border-slate-700/60">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-purple-600 text-white font-bold text-[10px]" x-text="idx + 1"></span>
                                                <span class="text-xs font-bold text-slate-800 dark:text-slate-100" x-text="'Dokumen #' + (idx + 1)"></span>
                                                
                                                <!-- Existing file badge -->
                                                <template x-if="item.isExisting && !item.newFileSelected">
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 truncate max-w-[220px]">
                                                        <i data-lucide="file-check" class="w-3 h-3 text-purple-600"></i>
                                                        <span class="truncate" x-text="item.fileName"></span>
                                                        <span class="text-slate-400 font-normal" x-show="item.fileSize" x-text="item.fileSize"></span>
                                                    </span>
                                                </template>

                                                <!-- New or replaced file badge -->
                                                <template x-if="item.newFileSelected || (!item.isExisting && item.fileName)">
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 truncate max-w-[220px]">
                                                        <i data-lucide="check" class="w-3 h-3 text-emerald-600"></i>
                                                        <span class="truncate" x-text="item.fileName"></span>
                                                        <span class="text-emerald-500 font-normal" x-show="item.fileSize" x-text="item.fileSize"></span>
                                                    </span>
                                                </template>
                                            </div>

                                            <template x-if="fileItems.length > 1">
                                                <button type="button" @click="removeFileItem(idx)" title="Hapus dokumen ini dari berkas"
                                                    class="inline-flex items-center gap-1 text-slate-400 hover:text-rose-600 px-2 py-0.5 rounded-md hover:bg-rose-50 dark:hover:bg-rose-950/40 text-[11px] font-medium transition-colors cursor-pointer">
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                    <span>Hapus</span>
                                                </button>
                                            </template>
                                        </div>

                                        <!-- Hidden Inputs for Keeping Existing File if Not Replaced -->
                                        <template x-if="item.isExisting && !item.newFileSelected">
                                            <div>
                                                <input type="hidden" :name="'documents[' + idx + '][existing_path]'" :value="item.existingPath">
                                                <input type="hidden" :name="'documents[' + idx + '][existing_name]'" :value="item.existingName">
                                                <input type="hidden" :name="'documents[' + idx + '][existing_size]'" :value="item.existingSize">
                                                <input type="hidden" :name="'documents[' + idx + '][existing_ext]'" :value="item.existingExt">
                                            </div>
                                        </template>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-start">
                                            <!-- Judul Dokumen -->
                                            <div>
                                                <label class="block text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">
                                                    Judul / Keterangan Dokumen <span class="text-rose-500">*</span>
                                                </label>
                                                <input type="text" :name="'documents[' + idx + '][title]'" x-model="item.title" required
                                                    placeholder="Contoh: Modul Ajar Sentra Main Peran..."
                                                    class="w-full h-10 px-3.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-900 dark:text-slate-100 placeholder:text-slate-400 focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 outline-none transition-all">
                                            </div>

                                            <!-- File Dokumen Picker -->
                                            <div>
                                                <div class="flex items-center justify-between mb-1">
                                                    <label class="block text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                                        <span x-text="item.isExisting ? 'Ganti File (Opsional)' : 'File Dokumen'"></span>
                                                        <span class="text-rose-500" x-show="!item.isExisting">*</span>
                                                    </label>
                                                    <span class="text-[9px] text-slate-400 font-medium">Maks 25MB</span>
                                                </div>
                                                <label class="relative flex items-center justify-between h-10 px-3 bg-white dark:bg-slate-900 border border-dashed border-slate-300 dark:border-slate-600 hover:border-purple-500 rounded-xl text-xs cursor-pointer group transition-all">
                                                    <input type="file" :name="'documents[' + idx + '][file]'" :required="!item.isExisting && !item.fileName"
                                                        @change="onFileSelected($event, item)"
                                                        accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.jpg,.jpeg,.png"
                                                        class="sr-only">
                                                    <div class="flex items-center gap-2 truncate pr-2">
                                                        <i data-lucide="paperclip" class="w-4 h-4 text-slate-400 group-hover:text-purple-600 shrink-0 transition-colors"></i>
                                                        <span class="truncate text-xs font-medium"
                                                            :class="item.newFileSelected ? 'text-emerald-700 dark:text-emerald-300 font-semibold' : (item.fileName ? 'text-slate-700 dark:text-slate-200' : 'text-slate-400')"
                                                            x-text="item.newFileSelected ? ('File Baru: ' + item.fileName) : (item.fileName ? item.fileName : 'Pilih file dokumen...')">
                                                        </span>
                                                    </div>
                                                    <span class="shrink-0 px-2 py-0.5 bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 rounded-md text-[10px] font-bold border border-purple-200/80 dark:border-purple-800/60 group-hover:bg-purple-600 group-hover:text-white transition-all"
                                                        x-text="item.isExisting ? (item.newFileSelected ? 'File Diganti' : 'Ganti File') : 'Pilih File'">
                                                    </span>
                                                </label>
                                                <p class="text-[10px] text-slate-400 mt-1">PDF, DOCX, XLSX, PPT, ZIP</p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Keterangan Tambahan -->
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
                                Keterangan Tambahan (Deskripsi / Catatan)
                            </label>
                            <textarea name="notes" x-model="formData.notes" rows="2"
                                placeholder="Tuliskan catatan tambahan mengenai berkas ini jika diperlukan..."
                                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-900 dark:text-slate-100 placeholder:text-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 outline-none leading-relaxed transition-all"></textarea>
                        </div>
                    </div>

                    <!-- Pinned Actions Footer -->
                    <div class="flex items-center justify-between pt-3.5 border-t border-slate-100 dark:border-slate-800 shrink-0">
                        <div class="text-[11px] text-slate-400 font-medium">
                            <span x-text="fileItems.length + ' dokumen (' + (isEdit ? 'dalam berkas' : '1 berkas baru') + ')'"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="modalOpen = false"
                                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl transition-colors cursor-pointer">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-xl shadow-sm hover:shadow-md transition-all cursor-pointer flex items-center gap-1.5">
                                <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                                <span x-text="isEdit ? 'Simpan Perubahan' : (fileItems.length > 1 ? 'Unggah 1 Berkas (' + fileItems.length + ' Dokumen)' : 'Unggah Berkas')"></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($isAdmin)
        <!-- ========================================================================= -->
        <!-- MODAL 2: VERIFIKASI / SUPERVISI STATUS (ADMIN / KEPALA SEKOLAH)           -->
        <!-- ========================================================================= -->
        <div x-show="reviewModalOpen" x-cloak
            class="fixed inset-0 z-[99999] overflow-y-auto flex items-center justify-center p-3 sm:p-4"
            style="top: 0px !important; left: 0px !important; right: 0px !important; bottom: 0px !important; margin: 0px !important; z-index: 99999 !important; background-color: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px);">
            
            <div @click.outside="reviewModalOpen = false"
                class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl shadow-2xl max-w-md w-full p-5 sm:p-6 text-left space-y-4">
                
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center ring-1 ring-emerald-100 dark:ring-emerald-900/40">
                            <i data-lucide="check-square" class="w-4 h-4"></i>
                        </span>
                        <div>
                            <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-slate-100">
                                Supervisi & Verifikasi Berkas
                            </h3>
                            <p class="text-[11px] text-slate-400">Tentukan status telaah dokumen administrasi guru.</p>
                        </div>
                    </div>
                    <button type="button" @click="reviewModalOpen = false" class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <form :action="reviewAction" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="p-3 bg-slate-50/80 dark:bg-slate-800/50 rounded-xl border border-slate-200/70 dark:border-slate-700/60 space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Dokumen yang Ditelaah:</span>
                        <span class="font-bold text-xs text-slate-900 dark:text-slate-100 block" x-text="reviewDoc?.title"></span>
                        <span class="text-[11px] text-purple-600 dark:text-purple-400 font-semibold" x-text="'Guru: ' + (reviewDoc?.teacher?.name || '-')"></span>
                    </div>

                    <!-- Status Selection -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
                            Status Hasil Telaah / Supervisi <span class="text-rose-500">*</span>
                        </label>
                        <select name="status" x-model="reviewForm.status" required
                            class="w-full h-10 px-3 bg-slate-50/80 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 outline-none cursor-pointer transition-all">
                            <option value="Menunggu Review">Menunggu Review</option>
                            <option value="Disetujui">Disetujui (Lolos Supervisi)</option>
                            <option value="Perlu Revisi">Perlu Revisi (Perbaiki)</option>
                        </select>
                    </div>

                    <!-- Catatan Feedback Supervisi -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
                            Catatan Telaah / Rekomendasi Perbaikan
                        </label>
                        <textarea name="feedback" x-model="reviewForm.feedback" rows="3"
                            placeholder="Tuliskan masukan dari Kepala Sekolah atau bagian kurikulum..."
                            class="w-full px-3.5 py-2.5 bg-slate-50/80 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-900 dark:text-slate-100 placeholder:text-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 outline-none leading-relaxed transition-all"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="reviewModalOpen = false"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl transition-colors cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm hover:shadow-md transition-all cursor-pointer flex items-center gap-1.5">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                            <span>Simpan Hasil Supervisi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- ========================================================================= -->
        <!-- MODAL 3: LIHAT DETAIL BERKAS                                              -->
        <!-- ========================================================================= -->
        <div x-show="viewModalOpen" x-cloak
            class="fixed inset-0 z-[99999] overflow-y-auto flex items-center justify-center p-3 sm:p-4"
            style="top: 0px !important; left: 0px !important; right: 0px !important; bottom: 0px !important; margin: 0px !important; z-index: 99999 !important; background-color: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px);">
            
            <div @click.outside="viewModalOpen = false"
                class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl shadow-2xl max-w-lg w-full p-5 sm:p-6 text-left space-y-4">
                
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center ring-1 ring-purple-100 dark:ring-purple-900/40">
                            <i data-lucide="file-check" class="w-4 h-4"></i>
                        </span>
                        <div>
                            <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-slate-100">
                                Rincian Berkas Administrasi
                            </h3>
                            <p class="text-[11px] text-slate-400">Informasi lengkap status dan berkas dokumen.</p>
                        </div>
                    </div>
                    <button type="button" @click="viewModalOpen = false" class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <div class="space-y-3.5 text-xs" x-show="viewData">
                    <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border uppercase"
                                    :class="viewData?.category_badge_class"
                                    x-text="viewData?.category"></span>
                                <h4 class="font-bold text-sm text-slate-900 dark:text-slate-100 mt-1.5" x-text="viewData?.title"></h4>
                                <div class="text-[11px] text-slate-500 mt-0.5" x-text="'Guru: ' + (viewData?.teacher?.name || '-')"></div>
                            </div>
                            <span class="px-2.5 py-1 rounded-md text-[10px] font-bold border shrink-0"
                                :class="viewData?.status_badge_class"
                                x-text="viewData?.status"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-2.5 rounded-xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                            <span class="text-[10px] font-bold text-slate-400 block uppercase">Semester & T.A</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 mt-0.5 block" x-text="(viewData?.semester || '-') + ' / ' + (viewData?.academic_year?.name || '-')"></span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                            <span class="text-[10px] font-bold text-slate-400 block uppercase">Jumlah Dokumen</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 mt-0.5 block" x-text="(viewData?.files_list?.length || 1) + ' File Dokumen'"></span>
                        </div>
                    </div>

                    <!-- File Dokumen Terlampir -->
                    <div class="p-3 rounded-xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 space-y-2">
                        <span class="text-[10px] font-bold text-slate-400 block uppercase">
                            Dokumen Lampiran (<span x-text="viewData?.files_list?.length || 0"></span>)
                        </span>
                        <div class="space-y-1.5">
                            <template x-for="(fileItem, fIdx) in (viewData?.files_list || [])" :key="fIdx">
                                <div class="flex items-center justify-between p-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700/80">
                                    <div class="flex items-center gap-2.5 truncate pr-2">
                                        <span class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                                            <i data-lucide="file-text" class="w-4 h-4"></i>
                                        </span>
                                        <div class="truncate">
                                            <span class="font-bold text-xs text-slate-900 dark:text-slate-100 block truncate" x-text="fileItem.title"></span>
                                            <span class="text-[10px] text-slate-400 font-mono block truncate" x-text="fileItem.file_name + ' • ' + (fileItem.formatted_size || '-')"></span>
                                        </div>
                                    </div>
                                    <button type="button"
                                        @click="downloadModalFile(viewData.id, fIdx)"
                                        :disabled="downloadingIndices[fIdx]"
                                        class="shrink-0 px-3 py-1.5 rounded-lg text-xs font-bold border transition-all inline-flex items-center gap-1.5 shadow-2xs cursor-pointer select-none"
                                        :class="downloadingIndices[fIdx] 
                                            ? 'bg-purple-600 text-white border-purple-600 pointer-events-none opacity-90' 
                                            : (downloadSuccessIndices[fIdx]
                                                ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-300 dark:border-emerald-800'
                                                : 'bg-purple-50 hover:bg-purple-600 hover:text-white dark:bg-purple-950/60 dark:hover:bg-purple-600 text-purple-700 dark:text-purple-300 border-purple-200 dark:border-purple-800')">
                                        <template x-if="downloadingIndices[fIdx]">
                                            <span class="inline-flex items-center gap-1.5">
                                                <svg class="animate-spin w-3.5 h-3.5 text-white shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                                </svg>
                                                <span>Mengunduh...</span>
                                            </span>
                                        </template>
                                        <template x-if="!downloadingIndices[fIdx] && downloadSuccessIndices[fIdx]">
                                            <span class="inline-flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400">
                                                <svg class="w-3.5 h-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                <span>Terunduh</span>
                                            </span>
                                        </template>
                                        <template x-if="!downloadingIndices[fIdx] && !downloadSuccessIndices[fIdx]">
                                            <span class="inline-flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                    <polyline points="7 10 12 15 17 10"></polyline>
                                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                                </svg>
                                                <span>Unduh</span>
                                            </span>
                                        </template>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div class="p-3 rounded-xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] font-bold text-slate-400 block uppercase">Keterangan Tambahan</span>
                        <p class="text-slate-700 dark:text-slate-300 mt-0.5 whitespace-pre-line leading-relaxed" x-text="viewData?.notes || 'Tidak ada keterangan tambahan.'"></p>
                    </div>

                    <!-- Feedback Catatan Supervisi -->
                    <template x-if="viewData?.feedback">
                        <div class="p-3 rounded-xl bg-rose-50/70 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50">
                            <span class="text-[10px] font-bold text-rose-700 dark:text-rose-400 block uppercase">Catatan Review Kepala Sekolah:</span>
                            <p class="text-rose-900 dark:text-rose-200 mt-0.5 whitespace-pre-line font-medium italic leading-relaxed" x-text="viewData?.feedback"></p>
                        </div>
                    </template>
                </div>

                <div class="flex items-center justify-end pt-3.5 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="viewModalOpen = false"
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl transition-colors cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- ALPINE.JS SCRIPT -->
    <script>
        function teacherDocsApp() {
            return {
                modalOpen: false,
                reviewModalOpen: false,
                viewModalOpen: false,
                isEdit: false,
                formAction: "{{ route('teacher-documents.store') }}",
                reviewAction: "",
                viewData: null,
                reviewDoc: null,
                currentFeedback: "",
                currentFileName: "",
                newEditFileName: "",
                editFilesList: [],
                formData: {
                    id: null,
                    teacher_id: "{{ $myEmployeeId ?: ($teachers->first()?->id ?? '') }}",
                    academic_year_id: "{{ $selectedYearId }}",
                    semester: "Tahunan",
                    category: "RPP",
                    title: "",
                    notes: ""
                },
                fileItems: [
                    { id: 1, title: '', fileName: '', fileSize: '', isExisting: false, existingPath: '', existingName: '', existingSize: '', existingExt: '', newFileSelected: false }
                ],
                reviewForm: {
                    status: "Disetujui",
                    feedback: ""
                },
                downloadingIndices: {},
                downloadSuccessIndices: {},

                addFileItem() {
                    this.fileItems.push({
                        id: Date.now() + Math.random(),
                        title: '',
                        fileName: '',
                        fileSize: '',
                        isExisting: false,
                        existingPath: '',
                        existingName: '',
                        existingSize: '',
                        existingExt: '',
                        newFileSelected: false
                    });
                    this.$nextTick(() => {
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    });
                },

                removeFileItem(index) {
                    if (this.fileItems.length > 1) {
                        this.fileItems.splice(index, 1);
                        this.$nextTick(() => {
                            if (window.lucide) {
                                window.lucide.createIcons();
                            }
                        });
                    } else {
                        alert('Minimal harus ada 1 dokumen dalam berkas.');
                    }
                },

                onFileSelected(event, item) {
                    const file = event.target.files[0];
                    if (file) {
                        let sizeStr = '';
                        if (file.size > 1024 * 1024) {
                            sizeStr = '(' + (file.size / (1024 * 1024)).toFixed(1) + ' MB)';
                        } else {
                            sizeStr = '(' + Math.round(file.size / 1024) + ' KB)';
                        }
                        item.fileName = file.name;
                        item.fileSize = sizeStr;
                        item.newFileSelected = true;

                        // Auto-fill title from clean filename if empty
                        if (!item.title || item.title.trim() === '') {
                            const nameWithoutExt = file.name.substring(0, file.name.lastIndexOf('.')) || file.name;
                            item.title = nameWithoutExt.replace(/[_\-]+/g, ' ').trim();
                        }

                        // Also auto-fill main berkas title if empty
                        if (!this.formData.title || this.formData.title.trim() === '') {
                            this.formData.title = item.title;
                        }

                        this.$nextTick(() => {
                            if (window.lucide) {
                                window.lucide.createIcons();
                            }
                        });
                    }
                },

                openCreateModal() {
                    this.isEdit = false;
                    this.formAction = "{{ route('teacher-documents.store') }}";
                    this.fileItems = [
                        {
                            id: Date.now() + Math.random(),
                            title: '',
                            fileName: '',
                            fileSize: '',
                            isExisting: false,
                            existingPath: '',
                            existingName: '',
                            existingSize: '',
                            existingExt: '',
                            newFileSelected: false
                        }
                    ];
                    this.formData = {
                        id: null,
                        teacher_id: "{{ $myEmployeeId ?: ($teachers->first()?->id ?? '') }}",
                        academic_year_id: "{{ $selectedYearId }}",
                        semester: "Tahunan",
                        category: "RPP",
                        title: "",
                        notes: ""
                    };
                    this.currentFeedback = "";
                    this.modalOpen = true;
                    this.$nextTick(() => {
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    });
                },

                openEditModal(doc) {
                    this.isEdit = true;
                    this.formAction = "/teacher-documents/" + doc.id;
                    this.formData = {
                        id: doc.id,
                        teacher_id: doc.teacher_id,
                        academic_year_id: doc.academic_year_id,
                        semester: doc.semester || "Tahunan",
                        category: doc.category || "RPP",
                        title: doc.title || "",
                        notes: doc.notes || ""
                    };
                    this.currentFeedback = doc.feedback || "";

                    if (doc.files_list && doc.files_list.length > 0) {
                        this.fileItems = doc.files_list.map((f, i) => ({
                            id: 'existing_' + i + '_' + Date.now(),
                            title: f.title || f.file_name || '',
                            fileName: f.file_name || '',
                            fileSize: f.formatted_size ? '(' + f.formatted_size + ')' : '',
                            isExisting: true,
                            existingPath: f.file_path || '',
                            existingName: f.file_name || '',
                            existingSize: f.file_size || 0,
                            existingExt: f.file_extension || '',
                            newFileSelected: false
                        }));
                    } else if (doc.file_path) {
                        this.fileItems = [{
                            id: 'existing_0_' + Date.now(),
                            title: doc.title || '',
                            fileName: doc.file_name || 'File Dokumen',
                            fileSize: doc.formatted_file_size ? '(' + doc.formatted_file_size + ')' : '',
                            isExisting: true,
                            existingPath: doc.file_path,
                            existingName: doc.file_name,
                            existingSize: doc.file_size || 0,
                            existingExt: doc.file_extension || '',
                            newFileSelected: false
                        }];
                    } else {
                        this.fileItems = [{
                            id: Date.now() + Math.random(),
                            title: '',
                            fileName: '',
                            fileSize: '',
                            isExisting: false,
                            existingPath: '',
                            existingName: '',
                            existingSize: '',
                            existingExt: '',
                            newFileSelected: false
                        }];
                    }

                    this.modalOpen = true;
                    this.$nextTick(() => {
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    });
                },

                openReviewModal(doc) {
                    this.reviewDoc = doc;
                    this.reviewAction = "/teacher-documents/" + doc.id + "/status";
                    this.reviewForm = {
                        status: doc.status || "Disetujui",
                        feedback: doc.feedback || ""
                    };
                    this.reviewModalOpen = true;
                    this.$nextTick(() => {
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    });
                },

                openViewModal(doc) {
                    this.viewData = doc;
                    this.downloadingIndices = {};
                    this.downloadSuccessIndices = {};
                    this.viewModalOpen = true;
                    this.$nextTick(() => {
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    });
                },

                downloadModalFile(docId, fIdx) {
                    if (this.downloadingIndices[fIdx]) return;
                    this.downloadingIndices[fIdx] = true;
                    this.downloadSuccessIndices[fIdx] = false;

                    const token = 'dt_' + Date.now();
                    const url = new URL('/teacher-documents/' + docId + '/download?file=' + fIdx, window.location.origin);
                    url.searchParams.set('download_token', token);

                    // Top-level navigation: Content-Disposition attachment ensures file extension is preserved in Chrome
                    window.location.href = url.toString();

                    let finished = false;
                    const finish = (isSuccess = true) => {
                        if (finished) return;
                        finished = true;
                        clearInterval(checkTimer);
                        this.downloadingIndices[fIdx] = false;
                        if (isSuccess) {
                            this.downloadSuccessIndices[fIdx] = true;
                            setTimeout(() => {
                                this.downloadSuccessIndices[fIdx] = false;
                            }, 2500);
                        }
                        document.cookie = 'download_token=; Max-Age=-99999999; path=/;';
                    };

                    const checkTimer = setInterval(() => {
                        const val = (`; ${document.cookie}`).split('; download_token=').pop().split(';').shift();
                        if (val === token) {
                            finish(true);
                        }
                    }, 150);

                    setTimeout(() => {
                        finish(false);
                    }, 10000);
                }
            };
        }
    </script>
</x-admin-layout>
