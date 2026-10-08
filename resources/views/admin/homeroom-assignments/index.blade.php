<x-admin-layout>
    <div class="p-4 sm:p-5 lg:p-6 space-y-4 lg:space-y-5" x-data="homeroomApp()">

        <!-- HEADER / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3.5 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <div class="flex items-center gap-2.5">
                    <div class="p-2 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-xl border border-indigo-500/20">
                        <i data-lucide="user-check" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">
                            Wali Kelas & Formasi Guru
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Pengelolaan dan riwayat penugasan Wali Kelas, Guru Kelas, GPK, GPQ, dan Koordinator Jenjang per Tahun Pelajaran.</p>
                    </div>
                </div>
            </div>

            <!-- ACTION CONTROLS -->
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <a href="{{ route('student-reports.index', ['academic_year_id' => $selectedYearId]) }}"
                    class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 shadow-xs transition-all">
                    <i data-lucide="file-bar-chart-2" class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400"></i>
                    Lihat Rekap Matriks Rombel
                </a>

                <button type="button" @click="openCreateModal()"
                    class="inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all cursor-pointer">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    Tambah Penugasan
                </button>
            </div>
        </section>

        <!-- STATS CARDS GRID (Compact) -->
        <section class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3 lg:gap-3.5">
            <!-- Stat Card 1: Total Penugasan -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 shadow-xs flex items-center gap-3 transition-colors">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-100 dark:border-indigo-900/40">
                    <i data-lucide="layers" class="w-5 h-5"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10.5px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Total Penugasan</p>
                    <div class="flex items-baseline gap-1.5 mt-0.5">
                        <h3 class="text-xl font-bold tracking-tight text-slate-900 dark:text-slate-50 font-mono">
                            {{ number_format($stats['total_assignments'] ?? 0) }}
                        </h3>
                        <span class="text-[10px] text-slate-400 font-medium truncate">Formasi</span>
                    </div>
                </div>
            </div>

            <!-- Stat Card 2: Wali Kelas -->
            <a href="{{ route('homeroom-assignments.index', array_merge(request()->except(['page', 'role']), ['role' => request('role') === 'wali_kelas' ? 'all' : 'wali_kelas'])) }}"
                class="bg-white dark:bg-slate-900 border {{ request('role') === 'wali_kelas' ? 'border-indigo-500 ring-2 ring-indigo-500/20' : 'border-slate-200 dark:border-slate-800 hover:border-indigo-300 dark:hover:border-indigo-700' }} rounded-xl p-3 sm:p-3.5 shadow-xs flex items-center gap-3 transition-all cursor-pointer group">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-100 dark:border-indigo-900/40 group-hover:scale-105 transition-transform">
                    <i data-lucide="user-check" class="w-5 h-5"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10.5px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Wali Kelas</p>
                    <div class="flex items-baseline gap-1.5 mt-0.5">
                        <h3 class="text-xl font-bold tracking-tight text-indigo-600 dark:text-indigo-400 font-mono">
                            {{ number_format($stats['total_homeroom'] ?? 0) }}
                        </h3>
                        <span class="text-[10px] text-slate-400 font-medium truncate">Rombel</span>
                    </div>
                </div>
            </a>

            <!-- Stat Card 3: GPK Inklusi -->
            <a href="{{ route('homeroom-assignments.index', array_merge(request()->except(['page', 'role']), ['role' => request('role') === 'gpk' ? 'all' : 'gpk'])) }}"
                class="bg-white dark:bg-slate-900 border {{ request('role') === 'gpk' ? 'border-purple-500 ring-2 ring-purple-500/20' : 'border-slate-200 dark:border-slate-800 hover:border-purple-300 dark:hover:border-purple-700' }} rounded-xl p-3 sm:p-3.5 shadow-xs flex items-center gap-3 transition-all cursor-pointer group">
                <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0 border border-purple-100 dark:border-purple-900/40 group-hover:scale-105 transition-transform">
                    <i data-lucide="heart-handshake" class="w-5 h-5"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10.5px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">GPK (Inklusi)</p>
                    <div class="flex items-baseline gap-1.5 mt-0.5">
                        <h3 class="text-xl font-bold tracking-tight text-purple-700 dark:text-purple-300 font-mono">
                            {{ number_format($stats['total_gpk'] ?? 0) }}
                        </h3>
                        <span class="text-[10px] text-slate-400 font-medium truncate">Guru</span>
                    </div>
                </div>
            </a>

            <!-- Stat Card 4: Total Pendidik Terlibat -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 shadow-xs flex items-center gap-3 transition-colors">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-100 dark:border-emerald-900/40">
                    <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10.5px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Guru Terlibat</p>
                    <div class="flex items-baseline gap-1.5 mt-0.5">
                        <h3 class="text-xl font-bold tracking-tight text-emerald-600 dark:text-emerald-400 font-mono">
                            {{ number_format($stats['total_teachers'] ?? 0) }}
                        </h3>
                        <span class="text-[10px] text-slate-400 font-medium truncate">Pendidik</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- FILTERS & SEARCH -->
        <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 shadow-xs w-full">
            <form method="GET" action="{{ route('homeroom-assignments.index') }}" class="flex flex-col lg:flex-row gap-2.5 items-stretch lg:items-center justify-between">
                <!-- Search Box -->
                <div class="relative w-full lg:max-w-xs">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama guru, rombel, NIP..."
                        style="padding-left: 2.25rem;"
                        class="w-full h-8.5 pr-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 dark:text-slate-50 placeholder-slate-400 shadow-inner">
                </div>

                <!-- Filter Toolbar -->
                <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                    <!-- Tapel -->
                    <select name="academic_year_id" onchange="this.form.submit()"
                        class="h-8.5 px-2.5 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none cursor-pointer shadow-xs">
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                                Tapel {{ $year->name }} {{ $year->is_active ? '★' : '' }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Tingkat Kelas -->
                    <select name="class_level_id" onchange="if(this.form.classroom_id) this.form.classroom_id.value = 'all'; this.form.submit()"
                        class="h-8.5 px-2.5 text-xs font-medium bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none cursor-pointer shadow-xs">
                        <option value="all">Semua Tingkat</option>
                        @foreach($classLevels as $lvl)
                            <option value="{{ $lvl->id }}" {{ request('class_level_id') == $lvl->id ? 'selected' : '' }}>
                                {{ $lvl->name }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Rombel -->
                    <select name="classroom_id" onchange="this.form.submit()"
                        class="h-8.5 px-2.5 text-xs font-medium bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none cursor-pointer shadow-xs max-w-[150px] truncate">
                        <option value="all">Semua Rombel</option>
                        @php
                            $filteredClassrooms = request('class_level_id') && request('class_level_id') !== 'all'
                                ? $classrooms->where('class_level_id', request('class_level_id'))
                                : $classrooms;
                        @endphp
                        @foreach($filteredClassrooms as $rombel)
                            <option value="{{ $rombel->id }}" {{ request('classroom_id') == $rombel->id ? 'selected' : '' }}>
                                {{ $rombel->full_name }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Peran Guru -->
                    <select name="role" onchange="this.form.submit()"
                        class="h-8.5 px-2.5 text-xs font-medium bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none cursor-pointer shadow-xs">
                        <option value="all">Semua Peran</option>
                        <option value="wali_kelas" {{ request('role') == 'wali_kelas' ? 'selected' : '' }}>Wali Kelas</option>
                        <option value="guru_kelas" {{ request('role') == 'guru_kelas' ? 'selected' : '' }}>Guru Kelas</option>
                        <option value="gpk" {{ request('role') == 'gpk' ? 'selected' : '' }}>GPK (Inklusi)</option>
                        <option value="gpq" {{ request('role') == 'gpq' ? 'selected' : '' }}>GPQ (Al-Qur'an)</option>
                        <option value="koordinator_tingkat" {{ request('role') == 'koordinator_tingkat' ? 'selected' : '' }}>Koordinator Tingkat</option>
                        <option value="pendamping_tingkat" {{ request('role') == 'pendamping_tingkat' ? 'selected' : '' }}>Pendamping Tingkat</option>
                    </select>

                    <!-- Per Page -->
                    <select name="per_page" onchange="this.form.submit()"
                        class="h-8.5 px-2.5 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none cursor-pointer shadow-xs">
                        <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15 baris</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 baris</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 baris</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 baris</option>
                        <option value="all" {{ request('per_page') === 'all' ? 'selected' : '' }}>Semua Baris</option>
                    </select>

                    @if(request()->hasAny(['search', 'class_level_id', 'classroom_id', 'role']) || (request('academic_year_id') && request('academic_year_id') != ($activeAcademicYear?->id ?? '')) || (request('per_page') && request('per_page') != 15))
                        <a href="{{ route('homeroom-assignments.index', ['academic_year_id' => $activeAcademicYear?->id]) }}" 
                            class="h-8.5 px-2.5 inline-flex items-center justify-center text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 rounded-lg border border-slate-200 dark:border-slate-700 transition-colors"
                            title="Reset Filter">
                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                        </a>
                    @endif
                </div>
            </form>
        </section>

        <!-- TABLE LIST -->
        <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs overflow-hidden w-full">
            <div class="overflow-x-auto">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-900/50">
                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-12">No</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pendidik / Guru</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-40">Peran Guru</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider min-w-[180px]">Tingkat & Rombel</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-28">Tahun Pelajaran</th>
                            <th class="px-4 py-3 text-center text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-24">Status</th>
                            <th class="px-4 py-3 text-right text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        @forelse($assignments as $index => $item)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors group">
                                <td class="px-4 py-3 text-slate-400 font-mono text-[11px]">
                                    {{ $assignments->firstItem() + $index }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        @if($item->teacher?->photo)
                                            <img src="{{ asset('storage/' . $item->teacher->photo) }}" alt="{{ $item->teacher->name }}" class="w-8 h-8 rounded-full object-cover ring-1 ring-slate-200 dark:ring-slate-700 shrink-0">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-100 dark:border-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-xs shrink-0">
                                                {{ strtoupper(substr($item->teacher?->raw_name ?? $item->teacher?->name ?? 'G', 0, 2)) }}
                                            </div>
                                        @endif
                                        <div class="flex flex-col">
                                            <span class="font-bold text-slate-900 dark:text-slate-100 text-xs tracking-tight">
                                                {{ $item->teacher?->name ?? 'Tidak Diketahui' }}
                                            </span>
                                            <div class="flex items-center gap-2 text-[10.5px] text-slate-400 mt-0.5">
                                                <span>{{ $item->teacher?->nuptk ? 'NUPTK: '.$item->teacher->nuptk : ($item->teacher?->nik ? 'NIK: '.$item->teacher->nik : 'Pendidik SD') }}</span>
                                                @if($item->notes)
                                                    <span class="text-slate-300 dark:text-slate-600">•</span>
                                                    <span class="italic truncate max-w-[140px]">{{ $item->notes }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $badgeClass = match($item->role) {
                                            'wali_kelas' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800',
                                            'guru_kelas' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                                            'gpk' => 'bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border-purple-200 dark:border-purple-800',
                                            'gpq' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                                            'koordinator_tingkat', 'pendamping_tingkat' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                                            default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10.5px] font-bold border {{ $badgeClass }}">
                                        {{ $item->role_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($item->classroom)
                                        <div class="flex flex-col">
                                            <span class="font-bold text-slate-800 dark:text-slate-200 text-xs">
                                                {{ $item->classroom->full_name }}
                                            </span>
                                            <span class="text-[10px] text-slate-400">
                                                {{ $item->classroom->classLevel?->name ?? 'Tingkat' }}
                                            </span>
                                        </div>
                                    @elseif($item->classLevel)
                                        <span class="font-semibold text-slate-700 dark:text-slate-300 text-xs">
                                            Jenjang {{ $item->classLevel->name }} (Seluruh Rombel)
                                        </span>
                                    @else
                                        <span class="text-slate-400 italic text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-mono text-xs font-semibold text-slate-700 dark:text-slate-300">
                                        {{ $item->academicYear?->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" @click="toggleStatus({{ $item->id }}, $event)"
                                        class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold transition-all cursor-pointer {{ $item->is_active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/80' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $item->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                                        <span>{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                    </button>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <button type="button" @click="openEditModal({{ json_encode($item) }})"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 transition-colors"
                                            title="Ubah Penugasan">
                                            <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                        </button>

                                        <form method="POST" action="{{ route('homeroom-assignments.destroy', $item->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus penugasan guru ini?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/50 transition-colors"
                                                title="Hapus Penugasan">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <i data-lucide="user-x" class="w-8 h-8 text-slate-300 dark:text-slate-600"></i>
                                        <p class="text-xs font-medium">Belum ada data penugasan guru pada tahun pelajaran yang dipilih.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            @if($assignments->hasPages())
                <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30 flex items-center justify-between">
                    <div class="text-[11px] text-slate-500">
                        Menampilkan {{ $assignments->firstItem() }} sampai {{ $assignments->lastItem() }} dari {{ $assignments->total() }} penugasan
                    </div>
                    <div>
                        {{ $assignments->links() }}
                    </div>
                </div>
            @endif
        </section>

        <!-- CREATE / EDIT MODAL -->
        <div x-show="modalOpen" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div @click.outside="modalOpen = false"
                class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl max-w-lg w-full shadow-2xl overflow-hidden text-left transform transition-all">
                
                <form :action="isEditing ? '{{ url('homeroom-assignments') }}/' + editId : '{{ route('homeroom-assignments.store') }}'" method="POST">
                    @csrf
                    <template x-if="isEditing">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="p-2 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900/40">
                                <i data-lucide="user-check" class="w-4 h-4"></i>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-slate-50" x-text="isEditing ? 'Ubah Penugasan Guru' : 'Tambah Penugasan Guru Baru'"></h3>
                        </div>
                        <button type="button" @click="modalOpen = false" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <div class="p-5 space-y-4 text-xs">
                        <!-- Tahun Pelajaran -->
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tahun Pelajaran <span class="text-rose-500">*</span></label>
                            <select name="academic_year_id" x-model="formData.academic_year_id" required
                                class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}">Tapel {{ $year->name }} {{ $year->is_active ? '(Aktif)' : '' }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Pilih Pendidik / Guru -->
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Pendidik / Guru <span class="text-rose-500">*</span></label>
                            <select name="employee_id" x-model="formData.employee_id" required
                                class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                <option value="">-- Pilih Guru --</option>
                                @foreach($teachers as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->position ?: 'Guru' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Peran Guru -->
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Peran / Posisi Guru <span class="text-rose-500">*</span></label>
                            <select name="role" x-model="formData.role" required
                                class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                <option value="wali_kelas">Wali Kelas</option>
                                <option value="guru_kelas">Guru Kelas (Pendamping)</option>
                                <option value="gpk">GPK (Guru Pendamping Khusus Inklusi)</option>
                                <option value="gpq">GPQ (Guru Pembina Al-Qur'an)</option>
                                <option value="koordinator_tingkat">Koordinator Tingkat / Jenjang</option>
                                <option value="pendamping_tingkat">Pendamping Tingkat</option>
                            </select>
                        </div>

                        <!-- Tingkat & Rombel Selection -->
                        <div class="grid grid-cols-2 gap-3" x-show="formData.role !== 'koordinator_tingkat' && formData.role !== 'pendamping_tingkat'">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tingkat Kelas</label>
                                <select name="class_level_id" x-model="formData.class_level_id"
                                    class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                    <option value="">-- Pilih Tingkat --</option>
                                    @foreach($classLevels as $lvl)
                                        <option value="{{ $lvl->id }}">{{ $lvl->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Rombel Target</label>
                                <select name="classroom_id" x-model="formData.classroom_id"
                                    class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                    <option value="">-- Pilih Rombel --</option>
                                    @foreach($classrooms as $cr)
                                        <option value="{{ $cr->id }}" :hidden="formData.class_level_id && formData.class_level_id != '{{ $cr->class_level_id }}'">
                                            {{ $cr->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Jenjang Coordinator Selection (if Coordinator role) -->
                        <div x-show="formData.role === 'koordinator_tingkat' || formData.role === 'pendamping_tingkat'">
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tingkat yang Dikoordinasikan <span class="text-rose-500">*</span></label>
                            <select name="class_level_id" x-model="formData.class_level_id"
                                class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                @foreach($classLevels as $lvl)
                                    <option value="{{ $lvl->id }}">Jenjang {{ $lvl->name }} (Kelas {{ $lvl->order }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status & Notes -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Penugasan</label>
                                <select name="is_active" x-model="formData.is_active"
                                    class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Catatan / Keterangan</label>
                                <input type="text" name="notes" x-model="formData.notes" placeholder="Misal: SK No. 12/2026..."
                                    class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                            </div>
                        </div>
                    </div>

                    <div class="px-5 py-3.5 bg-slate-50 dark:bg-slate-800/40 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2">
                        <button type="button" @click="modalOpen = false"
                            class="px-3.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 text-xs font-semibold cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-xs cursor-pointer">
                            Simpan Penugasan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function homeroomApp() {
            return {
                modalOpen: false,
                isEditing: false,
                editId: null,
                formData: {
                    academic_year_id: '{{ $selectedYearId }}',
                    employee_id: '',
                    role: 'wali_kelas',
                    class_level_id: '',
                    classroom_id: '',
                    is_active: '1',
                    notes: '',
                },
                openCreateModal() {
                    this.isEditing = false;
                    this.editId = null;
                    this.formData = {
                        academic_year_id: '{{ $selectedYearId }}',
                        employee_id: '',
                        role: 'wali_kelas',
                        class_level_id: '',
                        classroom_id: '',
                        is_active: '1',
                        notes: '',
                    };
                    this.modalOpen = true;
                },
                openEditModal(item) {
                    this.isEditing = true;
                    this.editId = item.id;
                    this.formData = {
                        academic_year_id: item.academic_year_id,
                        employee_id: item.employee_id,
                        role: item.role,
                        class_level_id: item.class_level_id || '',
                        classroom_id: item.classroom_id || '',
                        is_active: item.is_active ? '1' : '0',
                        notes: item.notes || '',
                    };
                    this.modalOpen = true;
                },
                toggleStatus(id, event) {
                    fetch(`{{ url('homeroom-assignments') }}/${id}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            if (window.showToast) window.showToast('Sukses', data.message, 'success');
                            window.location.reload();
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Gagal mengubah status.');
                    });
                }
            }
        }
    </script>
</x-admin-layout>
