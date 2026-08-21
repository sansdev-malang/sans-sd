<x-admin-layout>
    <div class="p-6 space-y-6">

        <!-- GREETING / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Daftar Guru</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Kelola dan pantau data akademis guru SANS Malang.</p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <button class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm transition-all duration-100 cursor-pointer">
                    <i data-lucide="download" class="w-3.5 h-3.5 text-slate-500"></i>
                    Unduh Laporan
                </button>
                <button class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-900 dark:bg-slate-50 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-all duration-100 cursor-pointer">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    Tambah Guru
                </button>
            </div>
        </section>

        <!-- SECTION 2: STATS CARDS GRID -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Stat Card 1 -->
            <div
                class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total
                            Guru Aktif</p>
                        <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                            <span class="stat-counter" data-target="{{ $totalGuru ?? 0 }}">{{ $totalGuru ?? 0 }}</span>
                        </h3>
                    </div>
                    <div class="p-2 bg-slate-50 dark:bg-slate-900 rounded-lg">
                        <i data-lucide="users" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                    </div>
                </div>
                <div class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">+2.4%</span> dari bulan lalu
                </div>
            </div>

            <!-- Stat Card 2 -->
            <div
                class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Guru Laki-laki</p>
                        <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                            <span class="stat-counter" data-target="{{ $guruMale ?? 0 }}">{{ $guruMale ?? 0 }}</span>
                        </h3>
                    </div>
                    <div class="p-2 bg-slate-50 dark:bg-slate-900 rounded-lg">
                        <i data-lucide="graduation-cap" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                    </div>
                </div>
                <div class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">98.2%</span> tingkat kehadiran
                </div>
            </div>

            <!-- Stat Card 3 -->
            <div
                class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Guru Perempuan</p>
                        <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                            <span class="stat-counter" data-target="{{ $guruFemale ?? 0 }}">{{ $guruFemale ?? 0 }}</span>
                        </h3>
                    </div>
                    <div class="p-2 bg-slate-50 dark:bg-slate-900 rounded-lg">
                        <i data-lucide="layout-grid" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                    </div>
                </div>
                <div class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                    Semua kelas terisi hari ini
                </div>
            </div>

            <!-- Stat Card 4 -->
            <div
                class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Telah Sertifikasi</p>
                        <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                            <span class="stat-counter" data-target="{{ $certifiedPercent ?? 0 }}">{{ $certifiedPercent ?? 0 }}</span>%
                        </h3>
                    </div>
                    <div class="p-2 bg-slate-50 dark:bg-slate-900 rounded-lg">
                        <i data-lucide="clock" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                    </div>
                </div>
                <div class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                    <span class="text-emerald-600 dark:text-emerald-400 font-bold">+1.2%</span> dari kemarin
                </div>
            </div>
        </section>

        <!-- SECTION 3: SEARCH & FILTERS -->
        <section class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-sm w-full">
            <form method="GET" action="{{ route('guru') }}" class="flex flex-col md:flex-row gap-4 items-stretch md:items-center justify-between">
                <!-- Search Box -->
                <div class="relative w-full md:max-w-md">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 dark:text-slate-500"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan Nama, NIP, atau NUPTK..."
                        style="padding-left: 2.25rem;"
                        class="w-full h-9 pr-4 text-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 focus:border-slate-400 dark:focus:border-slate-600 text-slate-900 dark:text-slate-50 placeholder-slate-400 dark:placeholder-slate-500 transition-all shadow-inner">
                </div>

                <!-- Filter Select Toolbar -->
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <!-- Filter Posisi -->
                    <select name="position" onchange="this.form.submit()"
                        class="h-9 px-2 flex-1 sm:flex-initial sm:w-36 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 cursor-pointer transition-all shadow-sm">
                        <option value="">Semua Jabatan</option>
                        @foreach($positions ?? [] as $pos)
                            <option value="{{ $pos }}" {{ request('position') == $pos ? 'selected' : '' }}>{{ $pos }}</option>
                        @endforeach
                    </select>

                    <!-- Filter Status -->
                    <select name="status" onchange="this.form.submit()"
                        class="h-9 px-2 flex-1 sm:flex-initial sm:w-32 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 cursor-pointer transition-all shadow-sm">
                        <option value="">Semua Status</option>
                        <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Aktif</option>
                        <option value="Leave" {{ request('status') == 'Leave' ? 'selected' : '' }}>Cuti</option>
                        <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </form>
        </section>

        <!-- SECTION 4: TABLE (PREMIUM DESIGN) -->
        <section class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden transition-all w-full">
            <div class="overflow-x-auto">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-16">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-48">NUPTK</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Guru</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-40">Mata Pelajaran</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-36">Jenis Kelamin</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-40">Kepegawaian</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-28">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-900">
                        @forelse($teachers as $index => $teacher)
                            <tr class="teacher-row hover:bg-slate-50/40 dark:hover:bg-slate-900/20 transition-colors">
                                <td class="px-6 py-4 text-slate-900 dark:text-slate-50 font-medium">{{ $teachers->firstItem() + $index }}</td>
                                <td class="px-6 py-4 text-slate-500 dark:text-slate-400 font-mono text-xs">{{ $teacher->nuptk ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($teacher->photo)
                                            <img src="{{ str_contains($teacher->photo, 'photos/') ? asset('storage/' . $teacher->photo) : asset('storage/photos/' . $teacher->photo) }}" class="w-8 h-8 rounded-full object-cover border border-slate-200/50 dark:border-slate-800/40 shrink-0">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-xs font-bold text-slate-700 dark:text-slate-300 shrink-0">
                                                {{ strtoupper(substr($teacher->name, 0, 2)) }}
                                            </div>
                                        @endif
                                        <span class="text-slate-900 dark:text-slate-50 font-semibold tracking-tight">{{ $teacher->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-400 font-medium">{{ $teacher->subject_position ?? '-' }}</td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-400">{{ $teacher->gender === 'Male' ? 'Laki-laki' : ($teacher->gender === 'Female' ? 'Perempuan' : '-') }}</td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-400">{{ $teacher->employment_status ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    @if($teacher->status == 'Active')
                                        <span class="inline-flex items-center px-4 py-1 rounded-full text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-800/40 shadow-sm">Aktif</span>
                                    @elseif($teacher->status == 'Leave')
                                        <span class="inline-flex items-center px-4 py-1 rounded-full text-[10px] font-semibold bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border border-amber-200/50 dark:border-amber-800/40 shadow-sm">Cuti</span>
                                    @else
                                        <span class="inline-flex items-center px-4 py-1 rounded-full text-[10px] font-semibold bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border border-red-200/50 dark:border-red-800/40 shadow-sm">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('employees.index') }}" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 transition-colors cursor-pointer" title="Kelola di Data Pegawai">
                                            <i data-lucide="external-link" class="w-4 h-4"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-10 text-center text-slate-500 dark:text-slate-400">
                                    Belum ada data guru terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $teachers->links() }}
            </div>
        </section>
    </div>
</x-admin-layout>
