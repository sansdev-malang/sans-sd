<x-admin-layout>
    <div class="p-3.5 sm:p-5 lg:p-6 space-y-3.5 sm:space-y-4" x-data="guestBookApp()" x-init="if (modalOpen) { $nextTick(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); }); }">

        <!-- ========================================================================= -->
        <!-- HEADER SECTION                                                            -->
        <!-- ========================================================================= -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-base sm:text-lg font-bold tracking-tight text-slate-900 dark:text-slate-50">Buku Tamu</h2>
                <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400">
                    Buku registrasi kunjungan tamu kedinasan, Puskesmas/Dinkes, calon wali murid SPMB, dan tamu umum SD.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <!-- Kiosk Public Link -->
                <a href="{{ route('buku-tamu.public') }}" target="_blank"
                    class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-[11px] sm:text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 shadow-xs transition-colors">
                    <i data-lucide="tablet" class="w-3.5 h-3.5 text-teal-600 dark:text-teal-400"></i>
                    <span>Mode Kiosk Mandiri</span>
                    <i data-lucide="external-link" class="w-3 h-3 text-slate-400"></i>
                </a>

                <!-- Ekspor Dropdown (Excel & Unduh PDF) -->
                <div x-data="{ exportOpen: false }" class="relative">
                    <button type="button" @click="exportOpen = !exportOpen" @click.outside="exportOpen = false"
                        id="btn_export_guest_book"
                        class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-[11px] sm:text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 shadow-xs transition-colors cursor-pointer">
                        <i data-lucide="download" class="w-3.5 h-3.5 text-slate-500"></i>
                        <span>Ekspor</span>
                        <i data-lucide="chevron-down" class="w-3 h-3 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': exportOpen }"></i>
                    </button>
                    
                    <div x-show="exportOpen" x-cloak
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 mt-1.5 w-44 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl z-50 py-1 overflow-hidden">
                        <a href="{{ route('guest-books.export.excel', ['month' => $selectedMonth, 'year' => $selectedYear, 'category' => $selectedCategory]) }}"
                            class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors">
                            <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0"></i>
                            <span>Excel (.xlsx)</span>
                        </a>
                        <a href="{{ route('guest-books.download.pdf', ['month' => $selectedMonth, 'year' => $selectedYear, 'category' => $selectedCategory]) }}"
                            class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-rose-50 dark:hover:bg-rose-950/40 hover:text-rose-700 dark:hover:text-rose-400 transition-colors border-t border-slate-100 dark:border-slate-800">
                            <i data-lucide="file-text" class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0"></i>
                            <span>PDF (.pdf)</span>
                        </a>
                    </div>
                </div>

                <!-- Print -->
                <a href="{{ route('guest-books.print', ['month' => $selectedMonth, 'year' => $selectedYear, 'category' => $selectedCategory]) }}" target="_blank"
                    class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-[11px] sm:text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 shadow-xs transition-colors">
                    <i data-lucide="printer" class="w-3.5 h-3.5 text-slate-500"></i>
                    <span>Cetak</span>
                </a>

                <!-- Kelola Kategori -->
                <button type="button" @click="openCategoryModal()"
                    class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-[11px] sm:text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 shadow-xs transition-colors cursor-pointer">
                    <i data-lucide="sliders" class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400"></i>
                    <span>Kelola Kategori</span>
                </button>

                <!-- Tambah Tamu -->
                <button type="button" @click="openCreateModal()"
                    class="inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 sm:px-4 sm:py-2 bg-teal-600 hover:bg-teal-700 text-white text-[11px] sm:text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>Catat Tamu Baru</span>
                </button>
            </div>
        </section>



        <!-- ========================================================================= -->
        <!-- TABLE CONTAINER WITH FILTER TOOLBAR                                       -->
        <!-- ========================================================================= -->
        <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs overflow-hidden transition-all w-full text-left">
            <!-- Table Header Bar: Filters Toolbar -->
            <div class="p-2 sm:px-3 sm:py-2.5 border-b border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-2.5">
                <form method="GET" action="{{ route('guest-books.index') }}" id="guestFilterForm" class="flex items-center gap-1.5 flex-wrap shrink-0">

                    <!-- Search Input -->
                    <div x-data="{ searchVal: '{{ $search }}' }" class="flex items-center w-48 sm:w-64 search-container bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 rounded-lg overflow-hidden focus-within:ring-1 focus-within:ring-teal-500 focus-within:border-teal-500 shrink-0">
                        <input type="text" name="search" x-model="searchVal" placeholder="Cari nama, kontak, instansi..."
                            style="border: none !important; outline: none !important; box-shadow: none !important;"
                            class="w-full h-8 pl-2.5 pr-1 text-xs bg-transparent text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:ring-0">
                        
                        <button type="button" x-show="searchVal.trim() !== ''" @click="searchVal = ''; $el.closest('.search-container').querySelector('input').focus();" class="h-8 px-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer bg-transparent border-0 flex items-center justify-center" title="Bersihkan pencarian">
                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                        </button>

                        <button type="submit" 
                            :class="searchVal.trim() !== '' ? 'bg-teal-600 text-white dark:bg-teal-500 dark:text-white' : 'bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300'"
                            class="h-8 px-2 font-bold text-xs transition-all duration-150 cursor-pointer flex items-center justify-center border-l border-slate-200 dark:border-slate-700">
                            <i data-lucide="search" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>

                    <!-- 1. Filter Periode: Tahun -->
                    <select name="year" onchange="this.form.submit()" title="Filter Tahun"
                        class="h-8 pl-2.5 pr-7 text-xs font-semibold bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 rounded-lg text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-teal-500 cursor-pointer shrink-0">
                        @foreach($yearsList as $yr)
                            <option value="{{ $yr }}" {{ $selectedYear == $yr ? 'selected' : '' }}>
                                {{ $yr }}
                            </option>
                        @endforeach
                    </select>

                    <!-- 2. Filter Periode: Bulan -->
                    <select name="month" onchange="this.form.submit()" title="Filter Bulan"
                        class="h-8 pl-2.5 pr-7 text-xs font-semibold bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 rounded-lg text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-teal-500 cursor-pointer shrink-0">
                        @foreach($monthNames as $mNum => $mLabel)
                            <option value="{{ $mNum }}" {{ $selectedMonth == $mNum ? 'selected' : '' }}>
                                {{ $mLabel }}
                            </option>
                        @endforeach
                    </select>

                    <!-- 3. Filter Kategori (Setelah Periode) -->
                    <select name="category" onchange="this.form.submit()" title="Filter Kategori Kunjungan"
                        class="h-8 pl-2.5 pr-7 text-xs font-semibold bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 rounded-lg text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-teal-500 cursor-pointer shrink-0">
                        <option value="all" {{ ($selectedCategory === 'all' || empty($selectedCategory)) ? 'selected' : '' }}>
                            Semua Kategori
                        </option>
                        @foreach($categories as $c)
                            @php
                                $cName = is_object($c) ? $c->name : $c;
                                $isSelected = in_array($cName, ['Puskesmas/Dinkes', 'Puskesmas', 'Dinkes'])
                                    ? in_array($selectedCategory, ['Puskesmas/Dinkes', 'Puskesmas', 'Dinkes'])
                                    : ($selectedCategory === $cName);
                            @endphp
                            <option value="{{ $cName }}" {{ $isSelected ? 'selected' : '' }}>
                                {{ $cName }}
                            </option>
                        @endforeach
                    </select>

                    <!-- 4. Jumlah Baris (per_page) -->
                    <select name="per_page" onchange="this.form.submit()" title="Jumlah data per halaman"
                        class="h-8 pl-2.5 pr-6 text-xs font-semibold bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 rounded-lg text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-teal-500 cursor-pointer shrink-0">
                        <option value="10" {{ ($perPage ?? 15) == 10 ? 'selected' : '' }}>10 baris</option>
                        <option value="15" {{ ($perPage ?? 15) == 15 ? 'selected' : '' }}>15 baris</option>
                        <option value="25" {{ ($perPage ?? 15) == 25 ? 'selected' : '' }}>25 baris</option>
                        <option value="50" {{ ($perPage ?? 15) == 50 ? 'selected' : '' }}>50 baris</option>
                        <option value="100" {{ ($perPage ?? 15) == 100 ? 'selected' : '' }}>100 baris</option>
                        <option value="99999" {{ ($perPage ?? 15) == 99999 || ($perPage ?? '') === 'all' ? 'selected' : '' }}>Semua</option>
                    </select>

                    <!-- Reset Button -->
                    @if($search || $selectedMonth != \Carbon\Carbon::now()->month || $selectedYear != 2026 || ($selectedCategory && $selectedCategory !== 'all') || (request()->filled('per_page') && request('per_page') != 15))
                        <a href="{{ route('guest-books.index') }}" class="h-8 w-8 flex items-center justify-center bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300 rounded-lg transition-colors shrink-0" title="Reset Semua Filter">
                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                        </a>
                    @endif
                </form>

                <!-- Data Count Badge -->
                <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400 ml-auto shrink-0 flex items-center gap-1.5">
                    <span>Ditemukan:</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 font-mono">
                        {{ $guests->total() }} data
                    </span>
                </div>
            </div>

            <!-- Table Container with Horizontal Scroll -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    @if($selectedCategory === 'SPMB')
                        {{-- ========================================================================= --}}
                        {{-- TABEL KHUSUS KATEGORI: SPMB (WALI MURID)                                  --}}
                        {{-- ========================================================================= --}}
                        <thead class="sticky top-0 z-10 shadow-xs">
                            <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900">
                                <th class="px-3 py-2.5 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-10 whitespace-nowrap">No</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap w-28">Waktu & Tanggal</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap min-w-[170px]">Nama Ortu / Wali</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap min-w-[180px]">Calon Murid</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap min-w-[160px]">Riwayat Terapi</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap min-w-[150px]">Alamat Rumah</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap min-w-[180px]">Keperluan</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap min-w-[130px]">Penerima Tamu</th>
                                <th class="px-4 py-2.5 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium text-slate-700 dark:text-slate-300">
                            @forelse($guests as $index => $g)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="px-3 py-2.5 text-center text-slate-400 font-bold whitespace-nowrap">
                                        {{ $guests->firstItem() ? ($guests->firstItem() + $index) : ($index + 1) }}
                                    </td>
                                    <td class="px-4 py-2.5 whitespace-nowrap">
                                        <div class="font-bold text-slate-900 dark:text-slate-100">
                                            {{ \Carbon\Carbon::parse($g->date)->format('d/m/Y') }}
                                        </div>
                                        <div class="text-[11px] text-teal-600 dark:text-teal-400 font-mono flex items-center gap-1 mt-0.5">
                                            <i data-lucide="clock" class="w-3 h-3"></i>
                                            <span>{{ $g->formatted_time }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <div class="flex items-start gap-2.5">
                                            <div class="w-7 h-7 rounded-full bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 font-bold flex items-center justify-center text-xs shrink-0 mt-0.5">
                                                {{ strtoupper(substr($g->name, 0, 1)) }}
                                            </div>
                                            <div class="space-y-0.5">
                                                <div class="font-bold text-slate-900 dark:text-slate-100 text-xs">
                                                    {{ $g->name }}
                                                </div>
                                                <div class="text-[11px] text-slate-500 font-mono">
                                                    {{ $g->phone }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <div class="font-bold text-slate-900 dark:text-slate-100 text-xs flex items-center gap-1.5">
                                            <i data-lucide="baby" class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400 shrink-0"></i>
                                            <span>{{ $g->student_name ?: '-' }}</span>
                                        </div>
                                        @if($g->student_birth_date)
                                            <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                                Lahir: {{ \Carbon\Carbon::parse($g->student_birth_date)->format('d/m/Y') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <div class="flex items-center gap-1.5">
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold {{ $g->therapy_history === 'Ya' ? 'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' }}">
                                                {{ $g->therapy_history === 'Ya' ? 'Pernah Terapi' : 'Tidak Terapi' }}
                                            </span>
                                        </div>
                                        @if($g->therapy_diagnosis)
                                            <div class="text-[11px] text-slate-600 dark:text-slate-400 mt-0.5 line-clamp-1" title="{{ $g->therapy_diagnosis }}">
                                                {{ $g->therapy_diagnosis }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <div class="text-xs text-slate-800 dark:text-slate-200 line-clamp-2 max-w-[160px]" title="{{ $g->address }}">
                                            {{ $g->address ?: '-' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <div class="text-xs text-slate-800 dark:text-slate-200 line-clamp-2 max-w-[200px]" title="{{ $g->purpose }}">
                                            {{ $g->purpose ?: 'Informasi Pendaftaran SPMB' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-2.5 whitespace-nowrap">
                                        <div class="flex items-center gap-1.5 text-slate-700 dark:text-slate-300">
                                            <i data-lucide="user-check" class="w-3.5 h-3.5 text-slate-400"></i>
                                            <span>{{ $g->host_recipient ?: '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2.5 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center justify-center gap-1">
                                            <button type="button" @click="openViewModal({{ json_encode($g) }})" title="Lihat Detail Tamu"
                                                class="inline-flex items-center justify-center w-7 h-7 text-slate-400 hover:text-teal-600 hover:bg-teal-50 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </button>
                                            <button type="button" @click="openEditModal({{ json_encode($g) }})" title="Edit Data Tamu"
                                                class="inline-flex items-center justify-center w-7 h-7 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer">
                                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                                            </button>
                                            <form method="POST" action="{{ route('guest-books.destroy', $g->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan tamu ini?')" class="inline-flex items-center m-0 p-0">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="current_category" value="{{ $selectedCategory }}">
                                                <input type="hidden" name="per_page" value="{{ $perPage ?? 15 }}">
                                                <button type="submit" title="Hapus Catatan"
                                                    class="inline-flex items-center justify-center w-7 h-7 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-12 text-center text-slate-400">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <i data-lucide="baby" class="w-8 h-8 text-amber-300 dark:text-amber-600"></i>
                                            <p class="font-medium text-xs">Belum ada kunjungan calon wali murid SPMB pada filter ini.</p>
                                            <button type="button" @click="openCreateModal()" class="mt-2 text-teal-600 dark:text-teal-400 text-xs font-semibold hover:underline">
                                                + Catat Tamu Baru
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    @elseif($selectedCategory === 'Dinas')
                        {{-- ========================================================================= --}}
                        {{-- TABEL KHUSUS KATEGORI: KEDINASAN                                          --}}
                        {{-- ========================================================================= --}}
                        <thead class="sticky top-0 z-10 shadow-xs">
                            <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900">
                                <th class="px-3 py-2.5 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-10 whitespace-nowrap">No</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap w-28">Waktu & Tanggal</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap min-w-[170px]">Nama Pejabat / Tamu</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap min-w-[180px]">Instansi Kedinasan</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap min-w-[150px]">Jabatan & Profesi</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap min-w-[130px]">Asal Wilayah</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap min-w-[200px]">Agenda Kedinasan</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap min-w-[130px]">Penerima Tamu</th>
                                <th class="px-4 py-2.5 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium text-slate-700 dark:text-slate-300">
                            @forelse($guests as $index => $g)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="px-3 py-2.5 text-center text-slate-400 font-bold whitespace-nowrap">
                                        {{ $guests->firstItem() ? ($guests->firstItem() + $index) : ($index + 1) }}
                                    </td>
                                    <td class="px-4 py-2.5 whitespace-nowrap">
                                        <div class="font-bold text-slate-900 dark:text-slate-100">
                                            {{ \Carbon\Carbon::parse($g->date)->format('d/m/Y') }}
                                        </div>
                                        <div class="text-[11px] text-teal-600 dark:text-teal-400 font-mono flex items-center gap-1 mt-0.5">
                                            <i data-lucide="clock" class="w-3 h-3"></i>
                                            <span>{{ $g->formatted_time }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <div class="flex items-start gap-2.5">
                                            <div class="w-7 h-7 rounded-full bg-indigo-100 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 font-bold flex items-center justify-center text-xs shrink-0 mt-0.5">
                                                {{ strtoupper(substr($g->name, 0, 1)) }}
                                            </div>
                                            <div class="space-y-0.5">
                                                <div class="font-bold text-slate-900 dark:text-slate-100 text-xs">
                                                    {{ $g->name }}
                                                </div>
                                                <div class="text-[11px] text-slate-500 font-mono">
                                                    {{ $g->phone }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <div class="font-bold text-slate-800 dark:text-slate-200 text-xs">
                                            {{ $g->institution ?: '-' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <div class="font-semibold text-slate-800 dark:text-slate-200 text-xs">
                                            {{ $g->position ?: '-' }}
                                        </div>
                                        @if($g->occupation && $g->occupation !== $g->position)
                                            <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                                {{ $g->occupation }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <div class="text-xs text-slate-800 dark:text-slate-200">
                                            {{ $g->city ?: '-' }}
                                        </div>
                                        @if($g->province)
                                            <div class="text-[11px] text-slate-400">
                                                {{ $g->province }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <p class="text-xs text-slate-800 dark:text-slate-200 line-clamp-2 max-w-[220px]" title="{{ $g->purpose }}">
                                            {{ $g->purpose }}
                                        </p>
                                        @if($g->feedback)
                                            <div class="text-[10px] text-indigo-600 dark:text-indigo-400 italic line-clamp-1 mt-0.5" title="Kesan: {{ $g->feedback }}">
                                                "{{ $g->feedback }}"
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5 whitespace-nowrap">
                                        <div class="flex items-center gap-1.5 text-slate-700 dark:text-slate-300">
                                            <i data-lucide="user-check" class="w-3.5 h-3.5 text-slate-400"></i>
                                            <span>{{ $g->host_recipient ?: '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2.5 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center justify-center gap-1">
                                            <button type="button" @click="openViewModal({{ json_encode($g) }})" title="Lihat Detail Tamu"
                                                class="inline-flex items-center justify-center w-7 h-7 text-slate-400 hover:text-teal-600 hover:bg-teal-50 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </button>
                                            <button type="button" @click="openEditModal({{ json_encode($g) }})" title="Edit Data Tamu"
                                                class="inline-flex items-center justify-center w-7 h-7 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer">
                                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                                            </button>
                                            <form method="POST" action="{{ route('guest-books.destroy', $g->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan tamu ini?')" class="inline-flex items-center m-0 p-0">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="current_category" value="{{ $selectedCategory }}">
                                                <input type="hidden" name="per_page" value="{{ $perPage ?? 15 }}">
                                                <button type="submit" title="Hapus Catatan"
                                                    class="inline-flex items-center justify-center w-7 h-7 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-12 text-center text-slate-400">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <i data-lucide="building-2" class="w-8 h-8 text-indigo-300 dark:text-indigo-600"></i>
                                            <p class="font-medium text-xs">Belum ada kunjungan tamu kedinasan pada filter ini.</p>
                                            <button type="button" @click="openCreateModal()" class="mt-2 text-teal-600 dark:text-teal-400 text-xs font-semibold hover:underline">
                                                + Catat Tamu Baru
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    @elseif(in_array($selectedCategory, ['Puskesmas/Dinkes', 'Puskesmas', 'Dinkes']))
                        {{-- ========================================================================= --}}
                        {{-- TABEL KHUSUS KATEGORI: PUSKESMAS / DINKES                                 --}}
                        {{-- ========================================================================= --}}
                        <thead class="sticky top-0 z-10 shadow-xs">
                            <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900">
                                <th class="px-3 py-2.5 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-10 whitespace-nowrap">No</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap w-28">Waktu & Tanggal</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap min-w-[170px]">Petugas Kesehatan</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap min-w-[180px]">Faskes / Puskesmas</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap min-w-[150px]">Jabatan / Profesi</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap min-w-[220px]">Pelayanan Kesehatan</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap min-w-[130px]">Penerima Tamu</th>
                                <th class="px-4 py-2.5 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium text-slate-700 dark:text-slate-300">
                            @forelse($guests as $index => $g)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="px-3 py-2.5 text-center text-slate-400 font-bold whitespace-nowrap">
                                        {{ $guests->firstItem() ? ($guests->firstItem() + $index) : ($index + 1) }}
                                    </td>
                                    <td class="px-4 py-2.5 whitespace-nowrap">
                                        <div class="font-bold text-slate-900 dark:text-slate-100">
                                            {{ \Carbon\Carbon::parse($g->date)->format('d/m/Y') }}
                                        </div>
                                        <div class="text-[11px] text-teal-600 dark:text-teal-400 font-mono flex items-center gap-1 mt-0.5">
                                            <i data-lucide="clock" class="w-3 h-3"></i>
                                            <span>{{ $g->formatted_time }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <div class="flex items-start gap-2.5">
                                            <div class="w-7 h-7 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold flex items-center justify-center text-xs shrink-0 mt-0.5">
                                                {{ strtoupper(substr($g->name, 0, 1)) }}
                                            </div>
                                            <div class="space-y-0.5">
                                                <div class="font-bold text-slate-900 dark:text-slate-100 text-xs">
                                                    {{ $g->name }}
                                                </div>
                                                <div class="text-[11px] text-slate-500 font-mono">
                                                    {{ $g->phone }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <div class="font-bold text-slate-800 dark:text-slate-200 text-xs">
                                            {{ $g->institution ?: '-' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <div class="font-semibold text-slate-800 dark:text-slate-200 text-xs">
                                            {{ $g->position ?: '-' }}
                                        </div>
                                        @if($g->occupation && $g->occupation !== $g->position)
                                            <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                                {{ $g->occupation }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <p class="text-xs text-slate-800 dark:text-slate-200 line-clamp-2 max-w-[240px]" title="{{ $g->purpose }}">
                                            {{ $g->purpose }}
                                        </p>
                                        @if($g->feedback)
                                            <div class="text-[10px] text-emerald-600 dark:text-emerald-400 italic line-clamp-1 mt-0.5" title="Kesan / Hasil: {{ $g->feedback }}">
                                                "{{ $g->feedback }}"
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5 whitespace-nowrap">
                                        <div class="flex items-center gap-1.5 text-slate-700 dark:text-slate-300">
                                            <i data-lucide="user-check" class="w-3.5 h-3.5 text-slate-400"></i>
                                            <span>{{ $g->host_recipient ?: '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2.5 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center justify-center gap-1">
                                            <button type="button" @click="openViewModal({{ json_encode($g) }})" title="Lihat Detail Tamu"
                                                class="inline-flex items-center justify-center w-7 h-7 text-slate-400 hover:text-teal-600 hover:bg-teal-50 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </button>
                                            <button type="button" @click="openEditModal({{ json_encode($g) }})" title="Edit Data Tamu"
                                                class="inline-flex items-center justify-center w-7 h-7 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer">
                                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                                            </button>
                                            <form method="POST" action="{{ route('guest-books.destroy', $g->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan tamu ini?')" class="inline-flex items-center m-0 p-0">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="current_category" value="{{ $selectedCategory }}">
                                                <input type="hidden" name="per_page" value="{{ $perPage ?? 15 }}">
                                                <button type="submit" title="Hapus Catatan"
                                                    class="inline-flex items-center justify-center w-7 h-7 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-12 text-center text-slate-400">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <i data-lucide="stethoscope" class="w-8 h-8 text-emerald-300 dark:text-emerald-600"></i>
                                            <p class="font-medium text-xs">Belum ada kunjungan petugas Puskesmas/Dinkes pada filter ini.</p>
                                            <button type="button" @click="openCreateModal()" class="mt-2 text-teal-600 dark:text-teal-400 text-xs font-semibold hover:underline">
                                                + Catat Tamu Baru
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    @elseif($selectedCategory === 'Umum')
                        {{-- ========================================================================= --}}
                        {{-- TABEL KHUSUS KATEGORI: UMUM                                               --}}
                        {{-- ========================================================================= --}}
                        <thead class="sticky top-0 z-10 shadow-xs">
                            <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900">
                                <th class="px-3 py-2.5 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-10 whitespace-nowrap">No</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap w-28">Waktu & Tanggal</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap min-w-[170px]">Nama Tamu</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap min-w-[180px]">Instansi / Lembaga</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap min-w-[150px]">Jabatan / Pekerjaan</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap min-w-[140px]">Kota / Alamat</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap min-w-[200px]">Keperluan Kunjungan</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap min-w-[130px]">Penerima Tamu</th>
                                <th class="px-4 py-2.5 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium text-slate-700 dark:text-slate-300">
                            @forelse($guests as $index => $g)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="px-3 py-2.5 text-center text-slate-400 font-bold whitespace-nowrap">
                                        {{ $guests->firstItem() ? ($guests->firstItem() + $index) : ($index + 1) }}
                                    </td>
                                    <td class="px-4 py-2.5 whitespace-nowrap">
                                        <div class="font-bold text-slate-900 dark:text-slate-100">
                                            {{ \Carbon\Carbon::parse($g->date)->format('d/m/Y') }}
                                        </div>
                                        <div class="text-[11px] text-teal-600 dark:text-teal-400 font-mono flex items-center gap-1 mt-0.5">
                                            <i data-lucide="clock" class="w-3 h-3"></i>
                                            <span>{{ $g->formatted_time }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <div class="flex items-start gap-2.5">
                                            <div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold flex items-center justify-center text-xs shrink-0 mt-0.5">
                                                {{ strtoupper(substr($g->name, 0, 1)) }}
                                            </div>
                                            <div class="space-y-0.5">
                                                <div class="font-bold text-slate-900 dark:text-slate-100 text-xs">
                                                    {{ $g->name }}
                                                </div>
                                                <div class="text-[11px] text-slate-500 font-mono">
                                                    {{ $g->phone }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <div class="font-bold text-slate-800 dark:text-slate-200 text-xs">
                                            {{ $g->institution ?: '-' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <div class="font-semibold text-slate-800 dark:text-slate-200 text-xs">
                                            {{ $g->position ?: ($g->occupation ?: 'Tamu') }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <div class="text-xs text-slate-800 dark:text-slate-200">
                                            {{ $g->city ?: ($g->address ?: '-') }}
                                        </div>
                                        @if($g->province)
                                            <div class="text-[11px] text-slate-400">
                                                {{ $g->province }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <p class="text-xs text-slate-800 dark:text-slate-200 line-clamp-2 max-w-[220px]" title="{{ $g->purpose }}">
                                            {{ $g->purpose }}
                                        </p>
                                        @if($g->feedback)
                                            <div class="text-[10px] text-slate-500 italic line-clamp-1 mt-0.5" title="Kesan: {{ $g->feedback }}">
                                                "{{ $g->feedback }}"
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5 whitespace-nowrap">
                                        <div class="flex items-center gap-1.5 text-slate-700 dark:text-slate-300">
                                            <i data-lucide="user-check" class="w-3.5 h-3.5 text-slate-400"></i>
                                            <span>{{ $g->host_recipient ?: '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2.5 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center justify-center gap-1">
                                            <button type="button" @click="openViewModal({{ json_encode($g) }})" title="Lihat Detail Tamu"
                                                class="inline-flex items-center justify-center w-7 h-7 text-slate-400 hover:text-teal-600 hover:bg-teal-50 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </button>
                                            <button type="button" @click="openEditModal({{ json_encode($g) }})" title="Edit Data Tamu"
                                                class="inline-flex items-center justify-center w-7 h-7 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer">
                                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                                            </button>
                                            <form method="POST" action="{{ route('guest-books.destroy', $g->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan tamu ini?')" class="inline-flex items-center m-0 p-0">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="current_category" value="{{ $selectedCategory }}">
                                                <input type="hidden" name="per_page" value="{{ $perPage ?? 15 }}">
                                                <button type="submit" title="Hapus Catatan"
                                                    class="inline-flex items-center justify-center w-7 h-7 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-12 text-center text-slate-400">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <i data-lucide="users" class="w-8 h-8 text-slate-300 dark:text-slate-600"></i>
                                            <p class="font-medium text-xs">Belum ada kunjungan tamu umum pada filter ini.</p>
                                            <button type="button" @click="openCreateModal()" class="mt-2 text-teal-600 dark:text-teal-400 text-xs font-semibold hover:underline">
                                                + Catat Tamu Baru
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    @else
                        {{-- ========================================================================= --}}
                        {{-- TABEL UMUM / SEMUA KATEGORI (RINGKASAN INTEGRATIF)                         --}}
                        {{-- ========================================================================= --}}
                        <thead class="sticky top-0 z-10 shadow-xs">
                            <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900">
                                <th class="px-3 py-2.5 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-10 whitespace-nowrap">No</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap w-28">Waktu & Tanggal</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap min-w-[170px]">Nama & Kontak</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap w-28">Kategori</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap min-w-[180px]">Instansi / Calon Murid</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap min-w-[150px]">Jabatan / Terapi</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap min-w-[200px]">Keperluan</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap min-w-[130px]">Penerima Tamu</th>
                                <th class="px-4 py-2.5 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium text-slate-700 dark:text-slate-300">
                            @forelse($guests as $index => $g)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="px-3 py-2.5 text-center text-slate-400 font-bold whitespace-nowrap">
                                        {{ $guests->firstItem() ? ($guests->firstItem() + $index) : ($index + 1) }}
                                    </td>
                                    <td class="px-4 py-2.5 whitespace-nowrap">
                                        <div class="font-bold text-slate-900 dark:text-slate-100">
                                            {{ \Carbon\Carbon::parse($g->date)->format('d/m/Y') }}
                                        </div>
                                        <div class="text-[11px] text-teal-600 dark:text-teal-400 font-mono flex items-center gap-1 mt-0.5">
                                            <i data-lucide="clock" class="w-3 h-3"></i>
                                            <span>{{ $g->formatted_time }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <div class="flex items-start gap-2.5">
                                            <div class="w-7 h-7 rounded-full bg-teal-100 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 font-bold flex items-center justify-center text-xs shrink-0 mt-0.5">
                                                {{ strtoupper(substr($g->name, 0, 1)) }}
                                            </div>
                                            <div class="space-y-0.5">
                                                <div class="font-bold text-slate-900 dark:text-slate-100 text-xs">
                                                    {{ $g->name }}
                                                    @if($g->category === 'SPMB')
                                                        <span class="text-[10px] font-normal text-slate-400">(Ortu)</span>
                                                    @endif
                                                </div>
                                                <div class="text-[11px] text-slate-500 font-mono">
                                                    {{ $g->phone }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2.5 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $g->category_badge_class }}">
                                            {{ $g->category }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2.5">
                                        @if($g->category === 'SPMB')
                                            <div class="font-bold text-slate-900 dark:text-slate-100 text-xs flex items-center gap-1">
                                                <i data-lucide="baby" class="w-3 h-3 text-amber-500 shrink-0"></i>
                                                <span>{{ $g->student_name ?: '-' }}</span>
                                            </div>
                                            @if($g->student_birth_date)
                                                <div class="text-[11px] text-slate-400">
                                                    Lahir: {{ \Carbon\Carbon::parse($g->student_birth_date)->format('d/m/Y') }}
                                                </div>
                                            @endif
                                        @else
                                            <div class="font-semibold text-slate-800 dark:text-slate-200 text-xs">
                                                {{ $g->institution ?: '-' }}
                                            </div>
                                            @if($g->city)
                                                <div class="text-[11px] text-slate-400">
                                                    {{ $g->city }}
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5">
                                        @if($g->category === 'SPMB')
                                            <div class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                                                Terapi: {{ $g->therapy_history ?: 'Tidak' }}
                                            </div>
                                            @if($g->therapy_diagnosis)
                                                <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate max-w-[150px]" title="{{ $g->therapy_diagnosis }}">
                                                    {{ $g->therapy_diagnosis }}
                                                </div>
                                            @endif
                                        @else
                                            <div class="font-semibold text-slate-800 dark:text-slate-200 text-xs">
                                                {{ $g->position ?: ($g->occupation ?: 'Tamu') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <p class="text-slate-700 dark:text-slate-300 line-clamp-2 max-w-[220px] text-xs" title="{{ $g->purpose }}">
                                            {{ $g->purpose }}
                                        </p>
                                        @if($g->feedback)
                                            <div class="text-[10px] text-teal-600 dark:text-teal-400 italic line-clamp-1 mt-0.5" title="Kesan: {{ $g->feedback }}">
                                                "{{ $g->feedback }}"
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5 whitespace-nowrap">
                                        <div class="flex items-center gap-1.5 text-slate-700 dark:text-slate-300 text-xs">
                                            <i data-lucide="user-check" class="w-3.5 h-3.5 text-slate-400"></i>
                                            <span>{{ $g->host_recipient ?: '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2.5 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center justify-center gap-1">
                                            <button type="button" @click="openViewModal({{ json_encode($g) }})" title="Lihat Detail Tamu"
                                                class="inline-flex items-center justify-center w-7 h-7 text-slate-400 hover:text-teal-600 hover:bg-teal-50 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </button>
                                            <button type="button" @click="openEditModal({{ json_encode($g) }})" title="Edit Data Tamu"
                                                class="inline-flex items-center justify-center w-7 h-7 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer">
                                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                                            </button>
                                            <form method="POST" action="{{ route('guest-books.destroy', $g->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan tamu ini?')" class="inline-flex items-center m-0 p-0">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="current_category" value="{{ $selectedCategory }}">
                                                <input type="hidden" name="per_page" value="{{ $perPage ?? 15 }}">
                                                <button type="submit" title="Hapus Catatan"
                                                    class="inline-flex items-center justify-center w-7 h-7 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-12 text-center text-slate-400">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <i data-lucide="inbox" class="w-8 h-8 text-slate-300 dark:text-slate-600"></i>
                                            <p class="font-medium text-xs">Belum ada kunjungan tamu tercatat pada filter ini.</p>
                                            <button type="button" @click="openCreateModal()" class="mt-2 text-teal-600 dark:text-teal-400 text-xs font-semibold hover:underline">
                                                + Catat Tamu Baru
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    @endif
                </table>
            </div>

            <!-- Table Footer: Pagination & Data Counter -->
            @if($guests->total() > 0)
                <div class="px-4 py-3 sm:px-6 sm:py-3.5 border-t border-slate-200 dark:border-slate-800 bg-slate-50/40 dark:bg-slate-900/40 flex flex-col sm:flex-row justify-between items-center gap-3 text-xs text-slate-500 dark:text-slate-400">
                    <div>
                        Menampilkan
                        <span class="font-bold text-slate-700 dark:text-slate-300">{{ $guests->firstItem() }}</span>
                        sampai
                        <span class="font-bold text-slate-700 dark:text-slate-300">{{ $guests->lastItem() }}</span>
                        dari
                        <span class="font-bold text-slate-700 dark:text-slate-300">{{ $guests->total() }}</span>
                        catatan tamu
                    </div>

                    @if($guests->hasPages())
                        <div class="flex items-center gap-1.5 font-semibold">
                            @if ($guests->onFirstPage())
                                <span class="h-8 px-3 rounded-lg border border-slate-200 dark:border-slate-800 text-slate-400 dark:text-slate-600 flex items-center justify-center cursor-not-allowed select-none bg-slate-50 dark:bg-slate-900/20 text-xs">Sebelumnya</span>
                            @else
                                <a href="{{ $guests->appends(request()->query())->previousPageUrl() }}" class="h-8 px-3 rounded-lg border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center transition-all bg-white dark:bg-slate-900 text-xs">Sebelumnya</a>
                            @endif

                            <span class="px-3 py-1 font-medium text-slate-700 dark:text-slate-300 text-xs">
                                Halaman {{ $guests->currentPage() }} dari {{ $guests->lastPage() }}
                            </span>

                            @if ($guests->hasMorePages())
                                <a href="{{ $guests->appends(request()->query())->nextPageUrl() }}" class="h-8 px-3 rounded-lg border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center transition-all bg-white dark:bg-slate-900 text-xs">Berikutnya</a>
                            @else
                                <span class="h-8 px-3 rounded-lg border border-slate-200 dark:border-slate-800 text-slate-400 dark:text-slate-600 flex items-center justify-center cursor-not-allowed select-none bg-slate-50 dark:bg-slate-900/20 text-xs">Berikutnya</span>
                            @endif
                        </div>
                    @endif
                </div>
            @endif
        </section>

        <!-- ========================================================================= -->
        <!-- MODAL FORM: TAMBAH / EDIT TAMU                                            -->
        <!-- ========================================================================= -->
        <div x-show="modalOpen" x-cloak
            style="margin: 0px !important; margin-top: 0px !important; top: 0px !important; left: 0px !important; right: 0px !important; bottom: 0px !important; z-index: 99999 !important; background-color: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px);"
            class="fixed inset-0 z-[99999] flex items-center justify-center p-3 sm:p-4">
            
            <div @click.outside="modalOpen = false"
                class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl max-w-2xl w-full flex flex-col max-h-[90vh] text-left overflow-hidden">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 shrink-0">
                    <div class="flex items-center gap-2">
                        <span class="p-1.5 rounded-lg bg-teal-50 dark:bg-teal-950/50 text-teal-600 dark:text-teal-400">
                            <i data-lucide="book-open-check" class="w-4 h-4"></i>
                        </span>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-slate-100"
                            x-text="isEdit ? 'Edit Catatan Buku Tamu' : 'Catat Tamu Baru'">
                        </h3>
                    </div>
                    <button type="button" @click="modalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <!-- Modal Body Form -->
                <form :action="formAction" method="POST" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>
                    <input type="hidden" name="id" :value="formData.id">
                    <input type="hidden" name="current_category" value="{{ $selectedCategory }}">
                    <input type="hidden" name="per_page" value="{{ $perPage ?? 15 }}">

                    <!-- Scrollable Form Body -->
                    <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4">

                        <!-- Validation Error Notification inside Modal -->
                        @if($errors->any())
                            <div class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60 text-rose-800 dark:text-rose-300 text-xs space-y-1.5">
                                <div class="flex items-center gap-2 font-bold text-rose-900 dark:text-rose-200">
                                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0"></i>
                                    <span>Terdapat kesalahan validasi. Mohon periksa kembali:</span>
                                </div>
                                <ul class="list-disc list-inside space-y-0.5 pl-1 text-[11px] text-rose-700 dark:text-rose-300">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                    <!-- Row 1: Tanggal, Waktu & Kategori Layanan -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <!-- Tanggal -->
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                Tanggal Kunjungan <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="date" x-model="formData.date" required
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        </div>

                        <!-- Waktu -->
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                Waktu / Jam Datang
                            </label>
                            <input type="time" name="time" x-model="formData.time"
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        </div>

                        <!-- Kategori Layanan -->
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                Kategori Layanan <span class="text-rose-500">*</span>
                            </label>
                            <select name="category" x-model="formData.category" required
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                @foreach($categories as $cat)
                                    @php $catName = is_object($cat) ? $cat->name : $cat; @endphp
                                    <option value="{{ $catName }}">{{ $catName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- CATEGORY-SPECIFIC FORM FIELDS -->
                    
                    <!-- 1. SPMB -->
                    <template x-if="currentFormType === 'spmb'">
                        <div class="space-y-3">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Nama Ortu (Orang Tua / Wali) <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="name" x-model="formData.name" :required="currentFormType === 'spmb'"
                                        placeholder="Contoh: Kiki Gustinasari"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        No. Handphone / WhatsApp <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="phone" x-model="formData.phone" :required="currentFormType === 'spmb'"
                                        placeholder="Contoh: 082232370123"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 font-mono">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Nama Calon Murid <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="student_name" x-model="formData.student_name" :required="currentFormType === 'spmb'"
                                        placeholder="Contoh: Muhammad Ihsan Ammando Saqi"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Tanggal Lahir Calon Murid
                                    </label>
                                    <input type="date" name="student_birth_date" x-model="formData.student_birth_date"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Pernah Terapi?
                                    </label>
                                    <select name="therapy_history" x-model="formData.therapy_history"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                        <option value="Tidak">Tidak</option>
                                        <option value="Ya">Ya</option>
                                    </select>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Diagnosa Terapi <span class="text-slate-400 text-[10px] font-normal">(jika pernah terapi)</span>
                                    </label>
                                    <input type="text" name="therapy_diagnosis" x-model="formData.therapy_diagnosis"
                                        placeholder="Contoh: Speech delay"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                    Alamat Rumah
                                </label>
                                <input type="text" name="address" x-model="formData.address"
                                    placeholder="Contoh: Perum La Tansa Cluster, A11, Merjosari"
                                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                    Penerima Tamu
                                </label>
                                <input type="text" name="host_recipient" x-model="formData.host_recipient"
                                    placeholder="Contoh: Kepala Sekolah / Panitia SPMB"
                                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            </div>
                        </div>
                    </template>

                    <!-- 2. PUSKESMAS / DINKES -->
                    <template x-if="currentFormType === 'puskesmas'">
                        <div class="space-y-3">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Nama Petugas / Tamu <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="name" x-model="formData.name" :required="currentFormType === 'puskesmas'"
                                        placeholder="Contoh: Mariana DN (PNS)"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        No. Handphone / WhatsApp <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="phone" x-model="formData.phone" :required="currentFormType === 'puskesmas'"
                                        placeholder="Contoh: 089601662496"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 font-mono">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Institusi / Faskes
                                    </label>
                                    <input type="text" name="institution" x-model="formData.institution"
                                        placeholder="Contoh: Puskesmas Mojolangu"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Jabatan
                                    </label>
                                    <input type="text" name="position" x-model="formData.position"
                                        placeholder="Contoh: Bidan / Dokter"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Pekerjaan
                                    </label>
                                    <input type="text" name="occupation" x-model="formData.occupation"
                                        placeholder="Contoh: Tenaga Medis / PNS"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                    Kegiatan Pelayanan Kesehatan <span class="text-rose-500">*</span>
                                </label>
                                <textarea name="purpose" x-model="formData.purpose" rows="2" :required="currentFormType === 'puskesmas'"
                                    placeholder="Contoh: screening DDTK, kusta prambusia, gigi, mata, GDS"
                                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-medium text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 leading-relaxed"></textarea>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                    Penerima Tamu
                                </label>
                                <input type="text" name="host_recipient" x-model="formData.host_recipient"
                                    placeholder="Contoh: Guru UKS / Bu Eka"
                                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                    Catatan / Hasil Pemeriksaan
                                </label>
                                <textarea name="feedback" x-model="formData.feedback" rows="2"
                                    placeholder="Catatan hasil pelayanan kesehatan..."
                                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-medium text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 leading-relaxed"></textarea>
                            </div>
                        </div>
                    </template>

                    <!-- 3. DINAS -->
                    <template x-if="currentFormType === 'dinas'">
                        <div class="space-y-3">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Nama Pejabat / Tamu <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="name" x-model="formData.name" :required="currentFormType === 'dinas'"
                                        placeholder="Contoh: Drs. H. Achmad Fauzi, M.Si"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        No. Handphone / WhatsApp <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="phone" x-model="formData.phone" :required="currentFormType === 'dinas'"
                                        placeholder="Contoh: 081234567890"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 font-mono">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Instansi Kedinasan
                                    </label>
                                    <input type="text" name="institution" x-model="formData.institution"
                                        placeholder="Contoh: Dinas Pendidikan Kota Malang"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Jabatan
                                    </label>
                                    <input type="text" name="position" x-model="formData.position"
                                        placeholder="Contoh: Pengawas Sekolah"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Pekerjaan
                                    </label>
                                    <input type="text" name="occupation" x-model="formData.occupation"
                                        placeholder="Contoh: ASN / PNS"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                    Keperluan / Agenda Kedinasan <span class="text-rose-500">*</span>
                                </label>
                                <textarea name="purpose" x-model="formData.purpose" rows="2" :required="currentFormType === 'dinas'"
                                    placeholder="Contoh: Monitoring dan Evaluasi BOSP"
                                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-medium text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 leading-relaxed"></textarea>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                    Penerima Tamu
                                </label>
                                <input type="text" name="host_recipient" x-model="formData.host_recipient"
                                    placeholder="Contoh: Kepala Sekolah"
                                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                    Kesan
                                </label>
                                <textarea name="feedback" x-model="formData.feedback" rows="2"
                                    placeholder="Kesan atau catatan tindak lanjut..."
                                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-medium text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 leading-relaxed"></textarea>
                            </div>
                        </div>
                    </template>

                    <!-- 4. OFFERING / MARKETING -->
                    <template x-if="currentFormType === 'offering'">
                        <div class="space-y-3">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Nama Sales / Tamu <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="name" x-model="formData.name" :required="currentFormType === 'offering'"
                                        placeholder="Contoh: Rian Pratama"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        No. Handphone / WhatsApp <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="phone" x-model="formData.phone" :required="currentFormType === 'offering'"
                                        placeholder="Contoh: 081234567890"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 font-mono">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Perusahaan / Vendor <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="institution" x-model="formData.institution" :required="currentFormType === 'offering'"
                                        placeholder="Contoh: CV. Bintang Edukasi"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Jabatan / Posisi
                                    </label>
                                    <input type="text" name="position" x-model="formData.position"
                                        placeholder="Contoh: Sales Executive"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Bidang Usaha
                                    </label>
                                    <input type="text" name="occupation" x-model="formData.occupation"
                                        placeholder="Contoh: Buku & Media Ajar"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                    Produk / Penawaran yang Ditawarkan <span class="text-rose-500">*</span>
                                </label>
                                <textarea name="purpose" x-model="formData.purpose" rows="2" :required="currentFormType === 'offering'"
                                    placeholder="Contoh: Penawaran buku pendamping kurikulum merdeka dan sistem absensi digital"
                                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-medium text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 leading-relaxed"></textarea>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                    Penerima Tamu / Yang Ditemui
                                </label>
                                <input type="text" name="host_recipient" x-model="formData.host_recipient"
                                    placeholder="Contoh: Waka Sarpras / Kepala Sekolah"
                                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                    Catatan Tindak Lanjut / Kesan
                                </label>
                                <textarea name="feedback" x-model="formData.feedback" rows="2"
                                    placeholder="Contoh: Menunggu follow-up presentasi produk lanjutan"
                                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-medium text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 leading-relaxed"></textarea>
                            </div>
                        </div>
                    </template>

                    <!-- 5. PENELITIAN / OBSERVASI -->
                    <template x-if="currentFormType === 'penelitian'">
                        <div class="space-y-3">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Nama Peneliti / Tamu <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="name" x-model="formData.name" :required="currentFormType === 'penelitian'"
                                        placeholder="Contoh: Nurul Hidayati, S.Pd"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        No. Handphone / WhatsApp <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="phone" x-model="formData.phone" :required="currentFormType === 'penelitian'"
                                        placeholder="Contoh: 085712345678"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 font-mono">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Asal Kampus / Lembaga <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="institution" x-model="formData.institution" :required="currentFormType === 'penelitian'"
                                        placeholder="Contoh: Universitas Negeri Malang (UM)"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Jurusan / Program Studi
                                    </label>
                                    <input type="text" name="position" x-model="formData.position"
                                        placeholder="Contoh: Pendidikan IPA / PGSD"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Status Peneliti
                                    </label>
                                    <input type="text" name="occupation" x-model="formData.occupation"
                                        placeholder="Contoh: Mahasiswa S1 / S2"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                    Judul / Topik Penelitian & Observasi <span class="text-rose-500">*</span>
                                </label>
                                <textarea name="purpose" x-model="formData.purpose" rows="2" :required="currentFormType === 'penelitian'"
                                    placeholder="Contoh: Analisis implementasi Panca Karakter dalam pembelajaran di SD Anak Saleh"
                                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-medium text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 leading-relaxed"></textarea>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                    Guru Pamong / Penerima Tamu
                                </label>
                                <input type="text" name="host_recipient" x-model="formData.host_recipient"
                                    placeholder="Contoh: Guru Pamong / Waka Kurikulum"
                                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                    Kesan & Pesan / Saran
                                </label>
                                <textarea name="feedback" x-model="formData.feedback" rows="2"
                                    placeholder="Kesan terhadap proses observasi..."
                                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-medium text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 leading-relaxed"></textarea>
                            </div>
                        </div>
                    </template>

                    <!-- 6. MAGANG / PKL / PPL -->
                    <template x-if="currentFormType === 'magang'">
                        <div class="space-y-3">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Nama Mahasiswa / Siswa Magang <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="name" x-model="formData.name" :required="currentFormType === 'magang'"
                                        placeholder="Contoh: Ahmad Rizki Maulana"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        No. Handphone / WhatsApp <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="phone" x-model="formData.phone" :required="currentFormType === 'magang'"
                                        placeholder="Contoh: 081987654321"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 font-mono">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Asal Kampus / Sekolah <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="institution" x-model="formData.institution" :required="currentFormType === 'magang'"
                                        placeholder="Contoh: UIN Maulana Malik Ibrahim Malang"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Jurusan / Program Studi
                                    </label>
                                    <input type="text" name="position" x-model="formData.position"
                                        placeholder="Contoh: PAI / Bimbingan Konseling"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Jenjang / Kategori
                                    </label>
                                    <input type="text" name="occupation" x-model="formData.occupation"
                                        placeholder="Contoh: Mahasiswa PPL / Siswa PKL"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                    Keperluan / Periode & Agenda Magang <span class="text-rose-500">*</span>
                                </label>
                                <textarea name="purpose" x-model="formData.purpose" rows="2" :required="currentFormType === 'magang'"
                                    placeholder="Contoh: Penyerahan mahasiswa PPL semester gasal / Laporan kegiatan"
                                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-medium text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 leading-relaxed"></textarea>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                    Guru Pamong / Pembimbing
                                </label>
                                <input type="text" name="host_recipient" x-model="formData.host_recipient"
                                    placeholder="Contoh: Koordinator Guru Pamong"
                                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                    Catatan / Kesan
                                </label>
                                <textarea name="feedback" x-model="formData.feedback" rows="2"
                                    placeholder="Catatan pelaksanaan kegiatan..."
                                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-medium text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 leading-relaxed"></textarea>
                            </div>
                        </div>
                    </template>

                    <!-- 7. STUDI BANDING / STUDI TIRU -->
                    <template x-if="currentFormType === 'studi_banding'">
                        <div class="space-y-3">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Nama Ketua / Pimpinan Rombongan <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="name" x-model="formData.name" :required="currentFormType === 'studi_banding'"
                                        placeholder="Contoh: Dr. H. Subagyo, M.Pd"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        No. Handphone / WhatsApp <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="phone" x-model="formData.phone" :required="currentFormType === 'studi_banding'"
                                        placeholder="Contoh: 081223344556"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 font-mono">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Asal Sekolah / Lembaga <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="institution" x-model="formData.institution" :required="currentFormType === 'studi_banding'"
                                        placeholder="Contoh: SD Islam Al-Azhar Surabaya"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Jabatan Pimpinan
                                    </label>
                                    <input type="text" name="position" x-model="formData.position"
                                        placeholder="Contoh: Kepala Sekolah / Ketua Yayasan"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Jumlah Rombongan
                                    </label>
                                    <input type="text" name="occupation" x-model="formData.occupation"
                                        placeholder="Contoh: 12 Orang Guru & Staff"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                    Fokus & Topik Studi Tiru / Keperluan <span class="text-rose-500">*</span>
                                </label>
                                <textarea name="purpose" x-model="formData.purpose" rows="2" :required="currentFormType === 'studi_banding'"
                                    placeholder="Contoh: Studi tiru manajemen kurikulum digital, sistem penjaminan mutu, dan Panca Karakter"
                                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-medium text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 leading-relaxed"></textarea>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                    Penerima Tamu
                                </label>
                                <input type="text" name="host_recipient" x-model="formData.host_recipient"
                                    placeholder="Contoh: Kepala Sekolah & Tim Manajemen"
                                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                    Kesan & Pesan Rombongan
                                </label>
                                <textarea name="feedback" x-model="formData.feedback" rows="2"
                                    placeholder="Kesan atau apresiasi rombongan..."
                                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-medium text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 leading-relaxed"></textarea>
                            </div>
                        </div>
                    </template>

                    <!-- 8. UMUM (DEFAULT / PRESET UMUM) -->
                    <template x-if="currentFormType === 'umum' || !['spmb', 'puskesmas', 'dinas', 'offering', 'penelitian', 'magang', 'studi_banding'].includes(currentFormType)">
                        <div class="space-y-3">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Nama Lengkap Tamu <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="name" x-model="formData.name" :required="currentFormType === 'umum' || !['spmb', 'puskesmas', 'dinas', 'offering', 'penelitian', 'magang', 'studi_banding'].includes(currentFormType)"
                                        placeholder="Contoh: Mohammad Choirul Anam"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        No. Handphone / WhatsApp <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="phone" x-model="formData.phone" :required="currentFormType === 'umum' || !['spmb', 'puskesmas', 'dinas', 'offering', 'penelitian', 'magang', 'studi_banding'].includes(currentFormType)"
                                        placeholder="Contoh: 0895613444125"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 font-mono">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Institusi
                                    </label>
                                    <input type="text" name="institution" x-model="formData.institution"
                                        placeholder="Contoh: MI Khadijah Malang"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Jabatan
                                    </label>
                                    <input type="text" name="position" x-model="formData.position"
                                        placeholder="Contoh: Guru"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Pekerjaan
                                    </label>
                                    <input type="text" name="occupation" x-model="formData.occupation"
                                        placeholder="Contoh: Guru"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Kota
                                    </label>
                                    <input type="text" name="city" x-model="formData.city"
                                        placeholder="Contoh: Kota Malang"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Provinsi
                                    </label>
                                    <input type="text" name="province" x-model="formData.province"
                                        placeholder="Contoh: Jawa Timur"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Alamat
                                    </label>
                                    <input type="text" name="address" x-model="formData.address"
                                        placeholder="Contoh: MI Khadijah Malang"
                                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                    Keperluan <span class="text-rose-500">*</span>
                                </label>
                                <textarea name="purpose" x-model="formData.purpose" rows="2" :required="currentFormType === 'umum' || !['spmb', 'puskesmas', 'dinas', 'offering', 'penelitian', 'magang', 'studi_banding'].includes(currentFormType)"
                                    placeholder="Contoh: Brosur Lomba"
                                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-medium text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 leading-relaxed"></textarea>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                    Penerima Tamu
                                </label>
                                <input type="text" name="host_recipient" x-model="formData.host_recipient"
                                    placeholder="Contoh: Admin"
                                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                    Kesan
                                </label>
                                <textarea name="feedback" x-model="formData.feedback" rows="2"
                                    placeholder="Contoh: Ramah dan baik"
                                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-medium text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 leading-relaxed"></textarea>
                            </div>
                        </div>
                    </template>

                    </div>

                    <!-- Footer Actions -->
                    <div class="flex items-center justify-end gap-2.5 p-3.5 sm:px-6 sm:py-3.5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 shrink-0">
                        <button type="button" @click="modalOpen = false"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg transition-colors cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">
                            <span x-text="isEdit ? 'Simpan Perubahan' : 'Simpan Catatan'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL DETAIL: LIHAT RINCIAN DETAIL INFORMASI TAMU                          -->
        <!-- ========================================================================= -->
        <div x-show="viewModalOpen" x-cloak
            style="margin: 0px !important; margin-top: 0px !important; top: 0px !important; left: 0px !important; right: 0px !important; bottom: 0px !important; z-index: 99999 !important; background-color: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px);"
            class="fixed inset-0 z-[99999] flex items-center justify-center p-3 sm:p-4">
            
            <div @click.outside="viewModalOpen = false"
                class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl max-w-lg w-full flex flex-col max-h-[90vh] text-left overflow-hidden">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 shrink-0">
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-slate-100">
                        Detail Informasi Tamu
                    </h3>
                    <button type="button" @click="viewModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Modal Body: Striped 2-Column Table (Scrollable) -->
                <div class="flex-1 overflow-y-auto p-4 sm:p-5">
                    <div class="overflow-hidden rounded-lg border border-slate-200 dark:border-slate-800 text-xs" x-show="viewData">
                    <table class="w-full text-left border-collapse">
                        <!-- 1. SPMB -->
                        <template x-if="currentViewFormType === 'spmb'">
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 w-36 sm:w-44 border-r border-slate-100 dark:border-slate-800">Kategori</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.category || 'SPMB'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Nama Ortu / Wali</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.name || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">No. Handphone</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100 font-mono" x-text="viewData?.phone || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Nama Calon Murid</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.student_name || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Tanggal Lahir Murid</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.student_birth_date ? (viewData.student_birth_date.includes('T') ? viewData.student_birth_date.split('T')[0] : viewData.student_birth_date) : '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Pernah Terapi</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.therapy_history || 'Tidak'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Diagnosa Terapi</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.therapy_diagnosis || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Alamat Rumah</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.address || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Penerima Tamu</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.host_recipient || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Waktu & Tanggal</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100 font-mono" x-text="(viewData?.date ? (viewData.date.includes('T') ? viewData.date.split('T')[0] : viewData.date) : '-') + ' ' + (viewData?.formatted_time || (viewData?.time ? viewData.time.substring(0, 5) : ''))"></td>
                                </tr>
                            </tbody>
                        </template>

                        <!-- 2. PUSKESMAS / DINKES -->
                        <template x-if="currentViewFormType === 'puskesmas'">
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 w-36 sm:w-44 border-r border-slate-100 dark:border-slate-800">Kategori</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.category || 'Puskesmas/Dinkes'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Petugas / Tamu</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.name || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">No. Handphone</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100 font-mono" x-text="viewData?.phone || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Faskes / Puskesmas</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.institution || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Jabatan</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.position || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Pekerjaan</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.occupation || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Pelayanan Kesehatan</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.purpose || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Penerima Tamu</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.host_recipient || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Catatan / Hasil</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100 italic" x-text="viewData?.feedback || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Waktu & Tanggal</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100 font-mono" x-text="(viewData?.date ? (viewData.date.includes('T') ? viewData.date.split('T')[0] : viewData.date) : '-') + ' ' + (viewData?.formatted_time || (viewData?.time ? viewData.time.substring(0, 5) : ''))"></td>
                                </tr>
                            </tbody>
                        </template>

                        <!-- 3. DINAS -->
                        <template x-if="currentViewFormType === 'dinas'">
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 w-36 sm:w-44 border-r border-slate-100 dark:border-slate-800">Kategori</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.category || 'Dinas'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Nama Pejabat</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.name || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">No. Handphone</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100 font-mono" x-text="viewData?.phone || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Instansi Kedinasan</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.institution || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Jabatan</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.position || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Pekerjaan</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.occupation || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Agenda Kedinasan</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.purpose || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Penerima Tamu</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.host_recipient || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Kesan</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100 italic" x-text="viewData?.feedback || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Waktu & Tanggal</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100 font-mono" x-text="(viewData?.date ? (viewData.date.includes('T') ? viewData.date.split('T')[0] : viewData.date) : '-') + ' ' + (viewData?.formatted_time || (viewData?.time ? viewData.time.substring(0, 5) : ''))"></td>
                                </tr>
                            </tbody>
                        </template>

                        <!-- 4. OFFERING / MARKETING -->
                        <template x-if="currentViewFormType === 'offering'">
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 w-36 sm:w-44 border-r border-slate-100 dark:border-slate-800">Kategori</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.category || 'Offering/Marketing'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Nama Sales / Tamu</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.name || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">No. Handphone</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100 font-mono" x-text="viewData?.phone || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Perusahaan / Vendor</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.institution || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Jabatan / Posisi</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.position || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Bidang Usaha</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.occupation || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Produk / Penawaran</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.purpose || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Penerima Tamu</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.host_recipient || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Catatan Tindak Lanjut</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100 italic" x-text="viewData?.feedback || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Waktu & Tanggal</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100 font-mono" x-text="(viewData?.date ? (viewData.date.includes('T') ? viewData.date.split('T')[0] : viewData.date) : '-') + ' ' + (viewData?.formatted_time || (viewData?.time ? viewData.time.substring(0, 5) : ''))"></td>
                                </tr>
                            </tbody>
                        </template>

                        <!-- 5. PENELITIAN / OBSERVASI -->
                        <template x-if="currentViewFormType === 'penelitian'">
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 w-36 sm:w-44 border-r border-slate-100 dark:border-slate-800">Kategori</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.category || 'Penelitian/Observasi'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Nama Peneliti</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.name || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">No. Handphone</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100 font-mono" x-text="viewData?.phone || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Asal Kampus / Lembaga</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.institution || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Jurusan / Program Studi</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.position || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Status Peneliti</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.occupation || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Judul / Topik Riset</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.purpose || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Guru Pamong / Dosen</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.host_recipient || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Kesan & Pesan</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100 italic" x-text="viewData?.feedback || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Waktu & Tanggal</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100 font-mono" x-text="(viewData?.date ? (viewData.date.includes('T') ? viewData.date.split('T')[0] : viewData.date) : '-') + ' ' + (viewData?.formatted_time || (viewData?.time ? viewData.time.substring(0, 5) : ''))"></td>
                                </tr>
                            </tbody>
                        </template>

                        <!-- 6. MAGANG / PKL / PPL -->
                        <template x-if="currentViewFormType === 'magang'">
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 w-36 sm:w-44 border-r border-slate-100 dark:border-slate-800">Kategori</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.category || 'Magang'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Nama Siswa / Mahasiswa</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.name || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">No. Handphone</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100 font-mono" x-text="viewData?.phone || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Asal Kampus / Sekolah</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.institution || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Jurusan / Program Studi</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.position || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Jenjang / Kategori</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.occupation || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Periode / Agenda Magang</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.purpose || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Guru Pamong / Pembimbing</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.host_recipient || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Catatan / Kesan</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100 italic" x-text="viewData?.feedback || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Waktu & Tanggal</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100 font-mono" x-text="(viewData?.date ? (viewData.date.includes('T') ? viewData.date.split('T')[0] : viewData.date) : '-') + ' ' + (viewData?.formatted_time || (viewData?.time ? viewData.time.substring(0, 5) : ''))"></td>
                                </tr>
                            </tbody>
                        </template>

                        <!-- 7. STUDI BANDING / STUDI TIRU -->
                        <template x-if="currentViewFormType === 'studi_banding'">
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 w-36 sm:w-44 border-r border-slate-100 dark:border-slate-800">Kategori</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.category || 'Studi Banding/Studi Tiru'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Pimpinan Rombongan</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.name || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">No. Handphone</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100 font-mono" x-text="viewData?.phone || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Asal Lembaga / Sekolah</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.institution || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Jabatan Pimpinan</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.position || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Jumlah Rombongan</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.occupation || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Fokus Studi Tiru</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.purpose || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Penerima Tamu</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.host_recipient || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Kesan & Pesan Rombongan</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100 italic" x-text="viewData?.feedback || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Waktu & Tanggal</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100 font-mono" x-text="(viewData?.date ? (viewData.date.includes('T') ? viewData.date.split('T')[0] : viewData.date) : '-') + ' ' + (viewData?.formatted_time || (viewData?.time ? viewData.time.substring(0, 5) : ''))"></td>
                                </tr>
                            </tbody>
                        </template>

                        <!-- 8. UMUM -->
                        <template x-if="currentViewFormType === 'umum' || !['spmb', 'puskesmas', 'dinas', 'offering', 'penelitian', 'magang', 'studi_banding'].includes(currentViewFormType)">
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 w-36 sm:w-44 border-r border-slate-100 dark:border-slate-800">Kategori</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.category || 'Umum'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Nama Tamu</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.name || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">No. Handphone</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100 font-mono" x-text="viewData?.phone || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Institusi</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.institution || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Jabatan</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.position || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Pekerjaan</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.occupation || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Kota</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.city || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Provinsi</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.province || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Alamat</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.address || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Keperluan</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.purpose || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Penerima Tamu</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100" x-text="viewData?.host_recipient || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Kesan</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100 italic" x-text="viewData?.feedback || '-'"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 border-r border-slate-100 dark:border-slate-800">Waktu & Tanggal</td>
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900 dark:text-slate-100 font-mono" x-text="(viewData?.date ? (viewData.date.includes('T') ? viewData.date.split('T')[0] : viewData.date) : '-') + ' ' + (viewData?.formatted_time || (viewData?.time ? viewData.time.substring(0, 5) : ''))"></td>
                                </tr>
                            </tbody>
                        </template>
                    </table>
                </div>
                </div>

                <!-- Footer Action -->
                <div class="flex items-center justify-between p-3.5 sm:px-5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 shrink-0">
                    <button type="button" @click="openEditModal(viewData); viewModalOpen = false"
                        class="px-3.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/40 dark:hover:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 text-xs font-semibold rounded-lg transition-colors cursor-pointer">
                        Edit Data Ini
                    </button>
                    <button type="button" @click="viewModalOpen = false"
                        class="px-4 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg transition-colors cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL KELOLA KATEGORI BUKU TAMU                                           -->
        <!-- ========================================================================= -->
        <div x-show="categoryModalOpen" x-cloak
            style="margin: 0px !important; margin-top: 0px !important; top: 0px !important; left: 0px !important; right: 0px !important; bottom: 0px !important; z-index: 99999 !important; background-color: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px);"
            class="fixed inset-0 z-[99999] flex items-center justify-center p-3 sm:p-4">
            
            <div @click.outside="categoryModalOpen = false"
                class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl max-w-4xl w-full flex flex-col max-h-[92vh] text-left overflow-hidden">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 shrink-0">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                            <i data-lucide="sliders" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-slate-100">
                                Kelola Kategori Buku Tamu
                            </h3>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                Atur kategori kunjungan, format formulir yang digunakan, warna tema, dan status aktif.
                            </p>
                        </div>
                    </div>
                    <button type="button" @click="categoryModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Modal Body (Grid: List on Left/Top, Form on Right/Bottom) -->
                <div class="flex-1 overflow-y-auto p-4 sm:p-6 grid grid-cols-1 lg:grid-cols-12 gap-5">
                    <!-- Left: Categories Table/List (7 cols) -->
                    <div class="lg:col-span-7 space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                                Daftar Kategori Terdaftar ({{ $allCategories->count() }})
                            </h4>
                            <span class="text-[10px] text-slate-400">Klik 'Edit' untuk mengubah kategori</span>
                        </div>

                        <div class="border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($allCategories as $cat)
                                <div class="p-3 bg-white dark:bg-slate-900/50 hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 border {{ $cat->badge_class }}">
                                            <i data-lucide="{{ $cat->icon ?: 'briefcase' }}" class="w-4 h-4"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="text-xs font-bold text-slate-900 dark:text-slate-100 truncate">{{ $cat->name }}</span>
                                                <span class="px-1.5 py-0.2 text-[9px] font-bold rounded uppercase {{ $cat->is_active ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300' : 'bg-slate-200 dark:bg-slate-800 text-slate-500' }}">
                                                    {{ $cat->is_active ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                            </div>
                                            <div class="text-[10px] text-slate-500 dark:text-slate-400 truncate mt-0.5">
                                                <span class="font-medium text-slate-600 dark:text-slate-300">{{ $cat->sub_label ?: $cat->name }}</span>
                                                <span class="text-slate-300 dark:text-slate-700 mx-1">•</span>
                                                <span>Preset: {{ $cat->form_type }}</span>
                                                <span class="text-slate-300 dark:text-slate-700 mx-1">•</span>
                                                <span>Urutan: {{ $cat->order }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-1 shrink-0">
                                        <!-- Toggle Active Status Form -->
                                        <form method="POST" action="{{ route('guest-books.categories.toggle', $cat->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" title="{{ $cat->is_active ? 'Nonaktifkan Kategori' : 'Aktifkan Kategori' }}"
                                                class="p-1.5 rounded-lg text-xs font-semibold transition-colors {{ $cat->is_active ? 'text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/50' : 'text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                                <i data-lucide="{{ $cat->is_active ? 'toggle-right' : 'toggle-left' }}" class="w-4 h-4"></i>
                                            </button>
                                        </form>

                                        <!-- Edit Category in Alpine -->
                                        <button type="button" @click="startEditCategory({{ json_encode($cat) }})" title="Edit Kategori"
                                            class="p-1.5 rounded-lg text-indigo-600 hover:bg-indigo-50 dark:text-indigo-400 dark:hover:bg-indigo-950/50 transition-colors cursor-pointer">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </button>

                                        <!-- Delete Category Form -->
                                        <form method="POST" action="{{ route('guest-books.categories.destroy', $cat->id) }}"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori {{ $cat->name }}?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus Kategori"
                                                class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/50 transition-colors cursor-pointer">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Right: Add / Edit Form (5 cols) -->
                    <div class="lg:col-span-5 bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 rounded-xl p-4 sm:p-5 flex flex-col justify-between">
                        <form method="POST" :action="categoryFormAction">
                            @csrf
                            <template x-if="editingCategory">
                                <input type="hidden" name="_method" value="PUT">
                            </template>

                            <div class="space-y-3.5">
                                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700/80 pb-2.5">
                                    <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider"
                                        x-text="editingCategory ? 'Edit Kategori' : 'Tambah Kategori Baru'"></h4>
                                    <button type="button" x-show="editingCategory" @click="resetCategoryForm()"
                                        class="text-[11px] text-teal-600 dark:text-teal-400 font-semibold hover:underline">
                                        + Buat Baru
                                    </button>
                                </div>

                                <!-- Nama Kategori -->
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Nama Kategori <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="name" x-model="categoryForm.name" required
                                        placeholder="Contoh: Offering/Marketing"
                                        class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>

                                <!-- Sub-label -->
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Sub Label / Keterangan Singkat
                                    </label>
                                    <input type="text" name="sub_label" x-model="categoryForm.sub_label"
                                        placeholder="Contoh: Vendor / Penawaran"
                                        class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>

                                <!-- Format Formulir (Preset) -->
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                        Format Formulir (Preset) <span class="text-rose-500">*</span>
                                    </label>
                                    <select name="form_type" x-model="categoryForm.form_type" required
                                        class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                        @foreach($formTypes as $fKey => $fDesc)
                                            <option value="{{ $fKey }}">{{ $fDesc }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Ikon & Warna Grid -->
                                <div class="grid grid-cols-2 gap-2.5">
                                    <div>
                                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                            Ikon Lucide <span class="text-rose-500">*</span>
                                        </label>
                                        <select name="icon" x-model="categoryForm.icon" required
                                            class="w-full px-2.5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                            @foreach($popularIcons as $iconCode => $iconTitle)
                                                <option value="{{ $iconCode }}">{{ $iconTitle }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                            Warna Tema <span class="text-rose-500">*</span>
                                        </label>
                                        <select name="color" x-model="categoryForm.color" required
                                            class="w-full px-2.5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                            @foreach($colorOptions as $colorKey => $colorName)
                                                <option value="{{ $colorKey }}">{{ $colorName }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Urutan & Status Aktif -->
                                <div class="grid grid-cols-2 gap-2.5 items-center pt-1">
                                    <div>
                                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                                            Urutan Tampil
                                        </label>
                                        <input type="number" name="order" x-model="categoryForm.order" min="1" max="99"
                                            class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 font-mono">
                                    </div>

                                    <div class="pt-4">
                                        <label class="flex items-center gap-2 cursor-pointer select-none">
                                            <input type="checkbox" name="is_active" value="1" x-model="categoryForm.is_active"
                                                class="w-4 h-4 rounded text-teal-600 focus:ring-teal-500 border-slate-300 dark:border-slate-700 dark:bg-slate-900">
                                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Status Aktif</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="pt-3 border-t border-slate-200 dark:border-slate-700 flex items-center justify-end gap-2">
                                    <button type="button" x-show="editingCategory" @click="resetCategoryForm()"
                                        class="px-3 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg transition-colors cursor-pointer">
                                        Batal
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">
                                        <span x-text="editingCategory ? 'Perbarui Kategori' : 'Simpan Kategori'"></span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end p-3.5 sm:px-5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 shrink-0">
                    <button type="button" @click="categoryModalOpen = false"
                        class="px-4 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg transition-colors cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- ALPINE.JS SCRIPT -->
    <script>
        function guestBookApp() {
            const hasErrors = {{ $errors->any() ? 'true' : 'false' }};
            const isCategoryError = {{ ($errors->has('name') && old('form_type')) || $errors->has('category_error') || $errors->has('icon') || $errors->has('color') || $errors->has('form_type') ? 'true' : 'false' }};
            const isEditFromOld = {{ (old('_method') === 'PUT' || old('id')) ? 'true' : 'false' }};
            const oldId = @json(old('id'));

            return {
                modalOpen: hasErrors && !isCategoryError,
                categoryModalOpen: isCategoryError,
                viewModalOpen: false,
                isEdit: isEditFromOld,
                formAction: (isEditFromOld && oldId) ? ("/guest-books/" + oldId) : "{{ route('guest-books.store') }}",
                viewData: null,
                categoriesMap: @json($allCategories->pluck('form_type', 'name')),

                get currentFormType() {
                    return this.categoriesMap[this.formData.category] || 'umum';
                },

                get currentViewFormType() {
                    if (!this.viewData || !this.viewData.category) return 'umum';
                    return this.categoriesMap[this.viewData.category] || 'umum';
                },

                // Guest Form Data
                formData: {
                    id: @json(old('id', null)),
                    date: @json(old('date', \Carbon\Carbon::now()->format('Y-m-d'))),
                    time: @json(old('time', \Carbon\Carbon::now()->format('H:i'))),
                    category: @json(old('category', $selectedCategory !== 'all' ? $selectedCategory : 'Umum')),
                    name: @json(old('name', '')),
                    phone: @json(old('phone', '')),
                    student_name: @json(old('student_name', '')),
                    student_birth_date: @json(old('student_birth_date', '')),
                    therapy_history: @json(old('therapy_history', 'Tidak')),
                    therapy_diagnosis: @json(old('therapy_diagnosis', '')),
                    institution: @json(old('institution', '')),
                    position: @json(old('position', '')),
                    occupation: @json(old('occupation', '')),
                    city: @json(old('city', 'Kota Malang')),
                    province: @json(old('province', 'Jawa Timur')),
                    address: @json(old('address', '')),
                    purpose: @json(old('purpose', '')),
                    host_recipient: @json(old('host_recipient', '')),
                    feedback: @json(old('feedback', ''))
                },

                // Category Management State & Form
                editingCategory: null,
                categoryForm: {
                    id: null,
                    name: '',
                    sub_label: '',
                    icon: 'briefcase',
                    color: 'teal',
                    form_type: 'umum',
                    order: {{ ($allCategories->max('order') ?? 0) + 1 }},
                    is_active: true
                },

                get categoryFormAction() {
                    return this.editingCategory
                        ? ("/guest-books/categories/" + this.editingCategory)
                        : "{{ route('guest-books.categories.store') }}";
                },

                openCategoryModal() {
                    this.resetCategoryForm();
                    this.categoryModalOpen = true;
                    this.$nextTick(() => {
                        if (typeof lucide !== 'undefined') lucide.createIcons();
                    });
                },

                resetCategoryForm() {
                    this.editingCategory = null;
                    this.categoryForm = {
                        id: null,
                        name: '',
                        sub_label: '',
                        icon: 'briefcase',
                        color: 'teal',
                        form_type: 'umum',
                        order: {{ ($allCategories->max('order') ?? 0) + 1 }},
                        is_active: true
                    };
                },

                startEditCategory(cat) {
                    this.editingCategory = cat.id;
                    this.categoryForm = {
                        id: cat.id,
                        name: cat.name,
                        sub_label: cat.sub_label || '',
                        icon: cat.icon || 'briefcase',
                        color: cat.color || 'teal',
                        form_type: cat.form_type || 'umum',
                        order: cat.order ?? 0,
                        is_active: !!cat.is_active
                    };
                    this.$nextTick(() => {
                        if (typeof lucide !== 'undefined') lucide.createIcons();
                    });
                },

                openCreateModal() {
                    this.isEdit = false;
                    this.formAction = "{{ route('guest-books.store') }}";
                    this.formData = {
                        id: null,
                        date: "{{ \Carbon\Carbon::now()->format('Y-m-d') }}",
                        time: "{{ \Carbon\Carbon::now()->format('H:i') }}",
                        category: "{{ $selectedCategory !== 'all' ? $selectedCategory : 'Umum' }}",
                        name: "",
                        phone: "",
                        student_name: "",
                        student_birth_date: "",
                        therapy_history: "Tidak",
                        therapy_diagnosis: "",
                        institution: "",
                        position: "",
                        occupation: "",
                        city: "Kota Malang",
                        province: "Jawa Timur",
                        address: "",
                        purpose: "",
                        host_recipient: "",
                        feedback: ""
                    };
                    this.modalOpen = true;
                    this.$nextTick(() => {
                        if (typeof lucide !== 'undefined') lucide.createIcons();
                    });
                },

                openEditModal(guest) {
                    this.isEdit = true;
                    this.formAction = "/guest-books/" + guest.id;
                    this.formData = {
                        id: guest.id,
                        date: guest.date ? (guest.date.includes('T') ? guest.date.split('T')[0] : guest.date) : "",
                        time: guest.time ? guest.time.substring(0, 5) : "",
                        category: guest.category || "Umum",
                        name: guest.name || "",
                        phone: guest.phone || "",
                        student_name: guest.student_name || "",
                        student_birth_date: guest.student_birth_date ? (guest.student_birth_date.includes('T') ? guest.student_birth_date.split('T')[0] : guest.student_birth_date) : "",
                        therapy_history: guest.therapy_history || "Tidak",
                        therapy_diagnosis: guest.therapy_diagnosis || "",
                        institution: guest.institution || "",
                        position: guest.position || "",
                        occupation: guest.occupation || "",
                        city: guest.city || "",
                        province: guest.province || "",
                        address: guest.address || "",
                        purpose: guest.purpose || "",
                        host_recipient: guest.host_recipient || "",
                        feedback: guest.feedback || ""
                    };
                    this.modalOpen = true;
                    this.$nextTick(() => {
                        if (typeof lucide !== 'undefined') lucide.createIcons();
                    });
                },

                openViewModal(guest) {
                    this.viewData = guest;
                    this.viewModalOpen = true;
                    this.$nextTick(() => {
                        if (typeof lucide !== 'undefined') lucide.createIcons();
                    });
                }
            };
        }
    </script>
</x-admin-layout>
