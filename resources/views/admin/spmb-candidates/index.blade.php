<x-admin-layout>
    <div class="p-6 space-y-6" x-data="spmbCandidateApp()">

        <!-- HEADER / ACTION BAR -->
        <section class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <div class="flex items-center gap-2.5">
                    <div class="p-2 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-xl border border-emerald-500/20">
                        <i data-lucide="user-plus" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 flex items-center gap-2">
                            Siswa Baru SPMB
                            <span class="text-xs px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 font-semibold border border-emerald-200 dark:border-emerald-800">
                                Unit SD
                            </span>
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Data pendaftar dan calon murid yang masuk dari sistem pendaftaran SPMB Pusat.</p>
                    </div>
                </div>
            </div>

            <!-- ACTION CONTROLS: TAHUN AJARAN & SYNC BUTTON -->
            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <!-- Dropdown Tahun Ajaran -->
                <form id="filter-period-form" method="GET" action="{{ route('spmb.candidates.index') }}" class="flex items-center">
                    <div class="relative">
                        <select name="period" onchange="this.form.submit()" 
                            class="appearance-none pl-8 pr-8 py-2 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 shadow-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 cursor-pointer">
                            <option value="all" {{ $selectedYear === 'all' ? 'selected' : '' }}>Semua Tahun Ajaran</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year }}" {{ $selectedYear === $year ? 'selected' : '' }}>
                                    Tahun Ajaran {{ $year }}
                                </option>
                            @endforeach
                            @if(empty($academicYears))
                                <option value="{{ date('Y') . '/' . (date('Y') + 1) }}" selected>
                                    Tahun Ajaran {{ date('Y') . '/' . (date('Y') + 1) }}
                                </option>
                            @endif
                        </select>
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    </div>
                </form>

                <!-- Tombol Tarik Data dari SPMB -->
                <button type="button" @click="syncData()" :disabled="syncing"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white text-xs font-bold rounded-lg shadow-sm transition-all duration-150 cursor-pointer">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5" :class="{ 'animate-spin': syncing }"></i>
                    <span x-text="syncing ? 'Menyinkronkan...' : 'Tarik Data dari SPMB'">Tarik Data dari SPMB</span>
                </button>
            </div>
        </section>

        <!-- STATS CARDS GRID -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Stat 1: Total Pendaftar -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Pendaftar SPMB</p>
                        <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">{{ number_format($stats['total']) }}</h3>
                    </div>
                    <div class="p-2.5 bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 rounded-xl border border-blue-100 dark:border-blue-900/50">
                        <i data-lucide="users" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="mt-3 text-[11px] text-slate-500 dark:text-slate-400">
                    Periode: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $selectedYear === 'all' ? 'Semua Periode' : $selectedYear }}</span>
                </div>
            </div>

            <!-- Stat 2: Terverifikasi / Diterima -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Terverifikasi / Diterima</p>
                        <h3 class="text-2xl font-bold tracking-tight text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($stats['verified']) }}</h3>
                    </div>
                    <div class="p-2.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-xl border border-emerald-100 dark:border-emerald-900/50">
                        <i data-lucide="badge-check" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="mt-3 text-[11px] text-slate-500 dark:text-slate-400">
                    Status berkas & pendaftaran valid
                </div>
            </div>

            <!-- Stat 3: Pembayaran Lunas -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pembayaran Lunas</p>
                        <h3 class="text-2xl font-bold tracking-tight text-indigo-600 dark:text-indigo-400 mt-1">{{ number_format($stats['paid']) }}</h3>
                    </div>
                    <div class="p-2.5 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 rounded-xl border border-indigo-100 dark:border-indigo-900/50">
                        <i data-lucide="wallet" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="mt-3 text-[11px] text-slate-500 dark:text-slate-400">
                    Biaya pendaftaran / DU selesai
                </div>
            </div>

            <!-- Stat 4: Masuk Siswa Aktif -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Masuk Siswa Aktif</p>
                        <h3 class="text-2xl font-bold tracking-tight text-purple-600 dark:text-purple-400 mt-1">{{ number_format($stats['enrolled']) }}</h3>
                    </div>
                    <div class="p-2.5 bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 rounded-xl border border-purple-100 dark:border-purple-900/50">
                        <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="mt-3 text-[11px] text-slate-500 dark:text-slate-400">
                    Sudah terdaftar di database siswa
                </div>
            </div>
        </section>

        <!-- FILTERS & SEARCH BAR -->
        <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-xs w-full">
            <form method="GET" action="{{ route('spmb.candidates.index') }}" class="flex flex-col md:flex-row gap-3 items-stretch md:items-center justify-between">
                <!-- Keep Period in Query -->
                <input type="hidden" name="period" value="{{ $selectedYear }}">

                <!-- Search Input -->
                <div class="relative w-full md:max-w-md">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 dark:text-slate-500"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama calon siswa, no. pendaftaran, NIK, ortu..."
                        style="padding-left: 2.25rem;"
                        class="w-full h-9 pr-4 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-slate-900 dark:text-slate-50 placeholder-slate-400 dark:placeholder-slate-500 transition-all shadow-inner">
                </div>

                <!-- Filter Select Toolbar -->
                <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                    <!-- Status Pendaftaran -->
                    <select name="status" onchange="this.form.submit()"
                        class="h-9 px-3 text-xs font-medium bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 cursor-pointer shadow-xs">
                        <option value="all">Semua Status Pendaftaran</option>
                        <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                        <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Diterima</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    </select>

                    <!-- Status Pembayaran -->
                    <select name="payment_status" onchange="this.form.submit()"
                        class="h-9 px-3 text-xs font-medium bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 cursor-pointer shadow-xs">
                        <option value="all">Semua Status Bayar</option>
                        <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Lunas</option>
                        <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Belum Lunas</option>
                    </select>

                    <!-- Gelombang -->
                    @if(count($availableWaves) > 1)
                        <select name="wave" onchange="this.form.submit()"
                            class="h-9 px-3 text-xs font-medium bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 cursor-pointer shadow-xs">
                            <option value="all">Semua Gelombang</option>
                            @foreach($availableWaves as $w)
                                <option value="{{ $w }}" {{ request('wave') === $w ? 'selected' : '' }}>{{ $w }}</option>
                            @endforeach
                        </select>
                    @endif

                    @if(request()->hasAny(['search', 'status', 'payment_status', 'wave']))
                        <a href="{{ route('spmb.candidates.index', ['period' => $selectedYear]) }}" 
                            class="h-9 px-3 inline-flex items-center justify-center text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 rounded-lg border border-slate-200 dark:border-slate-700 transition-colors"
                            title="Reset Filter">
                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                        </a>
                    @endif
                </div>
            </form>
        </section>

        <!-- TABLE LIST PENDAFTAR -->
        <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs overflow-hidden transition-all w-full">
            <div class="overflow-x-auto">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-900/50">
                            <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-12">No</th>
                            <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">No. Reg & Gelombang</th>
                            <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Calon Siswa</th>
                            <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Target Kelas</th>
                            <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Orang Tua / Kontak</th>
                            <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status SPMB</th>
                            <th class="px-5 py-3.5 text-center text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status Siswa</th>
                            <th class="px-5 py-3.5 text-right text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        @forelse($candidates as $index => $c)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors group">
                                <td class="px-5 py-3.5 text-slate-400 font-mono text-[11px]">
                                    {{ $candidates->firstItem() + $index }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex flex-col">
                                        <span class="font-mono font-bold text-slate-900 dark:text-slate-100 text-xs">
                                            {{ $c->registration_number }}
                                        </span>
                                        <span class="text-[11px] text-slate-500 dark:text-slate-400">
                                            {{ $c->wave ?: 'Gelombang 1' }} &bull; TA {{ $c->academic_year }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        @if($c->student_photo_url)
                                            <img src="{{ $c->student_photo_url }}" alt="{{ $c->full_name }}" class="w-8 h-8 rounded-full object-cover ring-1 ring-slate-200 dark:ring-slate-700 shrink-0">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-100 dark:border-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-xs shrink-0">
                                                {{ $c->initials }}
                                            </div>
                                        @endif
                                        <div class="flex flex-col">
                                            <span class="font-bold text-slate-900 dark:text-slate-100 text-xs tracking-tight hover:text-emerald-600 dark:hover:text-emerald-400 cursor-pointer" @click="openCandidateDetail({{ $c->id }})">
                                                {{ $c->full_name }}
                                            </span>
                                            <span class="text-[11px] text-slate-400">
                                                {{ $c->gender === 'male' || $c->gender === 'L' ? 'Laki-laki' : ($c->gender === 'female' || $c->gender === 'P' ? 'Perempuan' : '-') }}
                                                @if($c->birth_date)
                                                    &bull; {{ $c->birth_date->age }} th
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                        {{ $c->target_class ?: 'Kelas 1' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-slate-800 dark:text-slate-200 truncate max-w-[150px]">
                                            {{ $c->father_name ?: ($c->mother_name ?: ($c->guardian_name ?: '-')) }}
                                        </span>
                                        @if($c->parent_phone)
                                            <a href="{{ $c->whatsapp_url }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-emerald-600 dark:text-emerald-400 hover:underline font-mono mt-0.5">
                                                <i data-lucide="message-circle" class="w-3 h-3"></i>
                                                {{ $c->parent_phone }}
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex flex-col gap-1">
                                        @if(in_array($c->registration_status, ['verified', 'accepted', 'diterima', 'terverifikasi']))
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 dark:text-emerald-400">
                                                <i data-lucide="check-circle" class="w-3 h-3"></i>
                                                {{ ucfirst($c->registration_status) }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-amber-600 dark:text-amber-400">
                                                <i data-lucide="clock" class="w-3 h-3"></i>
                                                {{ ucfirst($c->registration_status) }}
                                            </span>
                                        @endif

                                        @if(in_array($c->payment_status, ['paid', 'lunas', 'settlement', 'success']))
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-indigo-700 dark:text-indigo-400">
                                                <i data-lucide="wallet" class="w-3 h-3"></i>
                                                Lunas
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[10px] font-medium text-slate-400">
                                                <i data-lucide="circle-dashed" class="w-3 h-3"></i>
                                                Belum Lunas
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    @if($c->is_enrolled)
                                        <div class="inline-flex flex-col items-center">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-800">
                                                <i data-lucide="check" class="w-3 h-3"></i>
                                                Siswa Aktif
                                            </span>
                                            @if($c->student)
                                                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-mono mt-0.5">
                                                    NIS: {{ $c->student->nis }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <button type="button" @click="openEnrollModal({{ $c->id }})"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold bg-purple-50 hover:bg-purple-600 text-purple-700 hover:text-white border border-purple-200 dark:border-purple-800/60 dark:bg-purple-950/30 dark:text-purple-300 dark:hover:bg-purple-600 transition-all cursor-pointer shadow-xs">
                                            <i data-lucide="user-check" class="w-3.5 h-3.5"></i>
                                            Tandai Siswa Aktif
                                        </button>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="button" @click="openCandidateDetail({{ $c->id }})"
                                            class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 rounded-lg transition-colors cursor-pointer"
                                            title="Lihat Detail Pendaftaran">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </button>
                                        <button type="button" @click="openEnrollModal({{ $c->id }})"
                                            class="p-1.5 hover:bg-purple-50 dark:hover:bg-purple-950/40 text-purple-600 dark:text-purple-400 rounded-lg transition-colors cursor-pointer"
                                            title="Atur Kelas & NIS">
                                            <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <i data-lucide="user-x" class="w-8 h-8 text-slate-300 dark:text-slate-600 mb-2"></i>
                                        <p class="font-semibold text-slate-600 dark:text-slate-400">Belum ada data pendaftar SPMB pada periode ini</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Klik tombol <b>Tarik Data dari SPMB</b> di atas untuk menyinkronkan data.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($candidates->hasPages())
                <div class="px-5 py-3 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between">
                    {{ $candidates->links() }}
                </div>
            @endif
        </section>

        <!-- MODAL DETAIL PENDAFTAR -->
        <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);">
            <div @click.outside="modalOpen = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto shadow-2xl flex flex-col">
                
                <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between sticky top-0 bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm z-10">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400 font-bold text-sm flex items-center justify-center">
                            <i data-lucide="user" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-slate-50" x-text="selectedCandidate?.full_name || 'Detail Calon Siswa'"></h3>
                            <p class="text-xs text-slate-400 mt-0.5">No. Reg: <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400" x-text="selectedCandidate?.registration_number"></span></p>
                        </div>
                    </div>
                    <button type="button" @click="modalOpen = false" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <div class="p-6 space-y-6 text-xs">
                    <!-- Section 1: Data Pribadi -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider mb-3 flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-2">
                            <i data-lucide="user-check" class="w-4 h-4 text-emerald-600"></i>
                            1. Biodata Calon Siswa
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            <div class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-800/40">
                                <span class="text-slate-400 text-[10px] block">Nama Lengkap</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedCandidate?.full_name || '-'"></span>
                            </div>
                            <div class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-800/40">
                                <span class="text-slate-400 text-[10px] block">Nama Panggilan</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedCandidate?.nickname || '-'"></span>
                            </div>
                            <div class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-800/40">
                                <span class="text-slate-400 text-[10px] block">Jenis Kelamin</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedCandidate?.gender === 'male' || selectedCandidate?.gender === 'L' ? 'Laki-laki' : 'Perempuan'"></span>
                            </div>
                            <div class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-800/40">
                                <span class="text-slate-400 text-[10px] block">Tempat, Tanggal Lahir</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="(selectedCandidate?.birth_place ? selectedCandidate.birth_place + ', ' : '') + (selectedCandidate?.formatted_birth_date || '-')"></span>
                            </div>
                            <div class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-800/40">
                                <span class="text-slate-400 text-[10px] block">NIK / No. KK</span>
                                <span class="font-mono font-semibold text-slate-800 dark:text-slate-200" x-text="selectedCandidate?.nik || '-'"></span>
                            </div>
                            <div class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-800/40">
                                <span class="text-slate-400 text-[10px] block">Asal Sekolah Sebelumnya</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedCandidate?.previous_school || '-'"></span>
                            </div>
                            <div class="sm:col-span-2 lg:col-span-3 p-2.5 rounded-lg bg-slate-50 dark:bg-slate-800/40">
                                <span class="text-slate-400 text-[10px] block">Alamat Domisili</span>
                                <span class="font-medium text-slate-800 dark:text-slate-200" x-text="selectedCandidate?.address || '-'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Data Orang Tua -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider mb-3 flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-2">
                            <i data-lucide="users" class="w-4 h-4 text-emerald-600"></i>
                            2. Data Orang Tua / Wali
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-800/40">
                                <span class="text-slate-400 text-[10px] block">Nama Ayah</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedCandidate?.father_name || '-'"></span>
                                <span class="text-[10px] text-slate-400 block mt-0.5" x-text="selectedCandidate?.father_phone ? 'Telp: ' + selectedCandidate.father_phone : ''"></span>
                            </div>
                            <div class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-800/40">
                                <span class="text-slate-400 text-[10px] block">Nama Ibu</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedCandidate?.mother_name || '-'"></span>
                                <span class="text-[10px] text-slate-400 block mt-0.5" x-text="selectedCandidate?.mother_phone ? 'Telp: ' + selectedCandidate.mother_phone : ''"></span>
                            </div>
                            <div class="sm:col-span-2 p-2.5 rounded-lg bg-slate-50 dark:bg-slate-800/40 flex items-center justify-between">
                                <div>
                                    <span class="text-slate-400 text-[10px] block">Kontak WhatsApp Utama Ortu</span>
                                    <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400 text-xs" x-text="selectedCandidate?.parent_phone || '-'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Hubungi WA -->
                        <template x-if="modalWaUrl">
                            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-start">
                                <a :href="modalWaUrl" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition-colors shadow-xs">
                                    <i data-lucide="message-circle" class="w-4 h-4"></i>
                                    Hubungi Orang Tua via WhatsApp
                                </a>
                            </div>
                        </template>
                    </div>

                    <!-- Section 3: Berkas Dokumen -->
                    <template x-if="formattedDocuments.length > 0">
                        <div>
                            <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider mb-3 flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-2">
                                <i data-lucide="paperclip" class="w-4 h-4 text-emerald-600"></i>
                                Berkas & Lampiran Pendaftaran (<span x-text="formattedDocuments.length"></span>)
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <template x-for="(doc, idx) in formattedDocuments" :key="idx">
                                    <a :href="doc.url" target="_blank" 
                                        class="flex items-center justify-between p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-950/40 hover:border-emerald-500 hover:bg-emerald-50/20 transition-all group">
                                        <div class="flex items-center gap-2.5 overflow-hidden">
                                            <div class="p-2 bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-700 text-emerald-600 shrink-0">
                                                <i data-lucide="file-text" class="w-4 h-4"></i>
                                            </div>
                                            <span class="font-semibold text-slate-700 dark:text-slate-300 truncate" x-text="doc.name"></span>
                                        </div>
                                        <i data-lucide="external-link" class="w-3.5 h-3.5 text-slate-400 group-hover:text-emerald-600 shrink-0 ml-2"></i>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Modal Footer -->
                <div class="p-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-950/50">
                    <div class="text-[11px] text-slate-400 font-mono">
                        Sinkron: <span x-text="selectedCandidate?.synced_at || '-'"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="modalOpen = false" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 text-slate-700 dark:text-slate-300 rounded-lg font-bold transition-colors cursor-pointer">
                            Tutup
                        </button>
                        <button type="button" @click="modalOpen = false; openEnrollModal(selectedCandidate.id)" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-bold transition-colors shadow-xs flex items-center gap-1.5">
                            <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                            Kelola Siswa Aktif
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL ENROLLMENT WIZARD (TANDAI / ALOKASI SISWA AKTIF) -->
        <div x-show="enrollModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);">
            <div @click.outside="enrollModalOpen = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl flex flex-col">
                
                <form @submit.prevent="submitEnroll">
                    <!-- Modal Header -->
                    <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-purple-50/50 dark:bg-purple-950/20">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center font-bold shadow-xs">
                                <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-slate-50">
                                    Penerimaan Siswa Baru SD
                                </h3>
                                <p class="text-xs text-slate-400 mt-0.5">Penetapan NIS dan penempatan rombongan belajar.</p>
                            </div>
                        </div>
                        <button type="button" @click="enrollModalOpen = false" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 space-y-4 text-xs" x-show="enrollData.candidate">
                        
                        <!-- Info Card Calon Siswa -->
                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/60 flex items-center gap-3">
                            <template x-if="enrollData.candidate?.student_photo_url">
                                <img :src="enrollData.candidate.student_photo_url" class="w-10 h-10 rounded-full object-cover ring-1 ring-purple-500/30">
                            </template>
                            <template x-if="!enrollData.candidate?.student_photo_url">
                                <div class="w-10 h-10 rounded-full bg-purple-100 text-purple-700 font-bold flex items-center justify-center text-xs" x-text="enrollData.candidate?.full_name ? enrollData.candidate.full_name.substring(0, 2).toUpperCase() : 'PS'"></div>
                            </template>
                            <div class="overflow-hidden">
                                <h4 class="font-bold text-slate-900 dark:text-slate-50 truncate" x-text="enrollData.candidate?.full_name"></h4>
                                <p class="text-[11px] text-slate-400 font-mono" x-text="enrollData.candidate?.registration_number + ' • ' + (enrollData.candidate?.wave || 'Gelombang 1')"></p>
                            </div>
                        </div>

                        <!-- NIS Input (Auto-suggested) -->
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Nomor Induk Siswa (NIS) <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" x-model="enrollForm.nis" required placeholder="Contoh: 27.SD.001"
                                class="w-full h-9 px-3 text-xs font-mono font-bold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 text-purple-700 dark:text-purple-300">
                            <p class="text-[10px] text-slate-400 mt-1">Saran format otomatis berdasarkan tahun masuk dan nomor urut SD.</p>
                        </div>

                        <!-- Tahun Ajaran & Rombel Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Tahun Ajaran <span class="text-rose-500">*</span>
                                </label>
                                <select x-model="enrollForm.academic_year_id" required
                                    class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 text-slate-900 dark:text-slate-50 cursor-pointer">
                                    <template x-for="ay in enrollData.academic_years" :key="ay.id">
                                        <option :value="ay.id" x-text="'TA ' + ay.name + (ay.is_active ? ' (Aktif)' : '')"></option>
                                    </template>
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Pilih Rombongan Belajar <span class="text-rose-500">*</span>
                                </label>
                                <select x-model="enrollForm.classroom_id" required
                                    class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 text-slate-900 dark:text-slate-50 cursor-pointer">
                                    <option value="">Pilih Rombel...</option>
                                    <template x-for="r in enrollData.classrooms" :key="r.id">
                                        <option :value="r.id" x-text="r.name + ' (' + (r.class_level ? r.class_level.name : '') + ') • ' + r.active_students_count + '/' + r.capacity + ' siswa'"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <!-- Tanggal Terdaftar -->
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Masuk / Terdaftar</label>
                            <input type="date" x-model="enrollForm.enrolled_date"
                                class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 text-slate-900 dark:text-slate-50">
                        </div>

                        <!-- Status Terdaftar Info jika sudah enrolled -->
                        <template x-if="enrollData.candidate?.is_enrolled">
                            <div class="p-3 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 rounded-xl flex items-start gap-2.5">
                                <i data-lucide="info" class="w-4 h-4 text-amber-600 mt-0.5 shrink-0"></i>
                                <div class="text-[11px] text-amber-800 dark:text-amber-200 leading-relaxed">
                                    Calon murid ini telah berstatus <b>Siswa Aktif</b>. Anda dapat mengubah rombel atau membatalkan status siswa aktif melalui tombol di bawah.
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Modal Footer -->
                    <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/50 flex items-center justify-between">
                        <div>
                            <template x-if="enrollData.candidate?.is_enrolled">
                                <button type="button" @click="unenrollStudent(enrollData.candidate.id)" class="px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400 dark:hover:bg-rose-900/60 rounded-lg text-xs font-bold transition-colors border border-rose-200 dark:border-rose-800">
                                    Batalkan Status Siswa Aktif
                                </button>
                            </template>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="enrollModalOpen = false" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-bold transition-colors">
                                Batal
                            </button>
                            <button type="submit" :disabled="enrolling" class="px-5 py-2 bg-purple-600 hover:bg-purple-700 disabled:opacity-50 text-white rounded-lg text-xs font-bold transition-all shadow-xs flex items-center gap-1.5">
                                <i data-lucide="check" class="w-4 h-4"></i>
                                <span x-text="enrolling ? 'Memproses...' : (enrollData.candidate?.is_enrolled ? 'Simpan Perubahan' : 'Resmikan Siswa Aktif')"></span>
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

    </div>

    <!-- Alpine.js Application Logic -->
    <script>
        function spmbCandidateApp() {
            return {
                syncing: false,
                modalOpen: false,
                enrollModalOpen: false,
                enrolling: false,
                selectedCandidate: null,
                modalWaUrl: null,
                formattedDocuments: [],
                enrollData: {
                    candidate: null,
                    academic_years: [],
                    classrooms: [],
                },
                enrollForm: {
                    nis: '',
                    classroom_id: '',
                    academic_year_id: '',
                    enrolled_date: '{{ date("Y-m-d") }}',
                    notes: '',
                },

                syncData() {
                    if (this.syncing) return;
                    this.syncing = true;

                    fetch('{{ route("spmb.candidates.sync") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            period: '{{ $selectedYear }}'
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.syncing = false;
                        if (data.success) {
                            alert(data.message || 'Sinkronisasi berhasil!');
                            window.location.reload();
                        } else {
                            alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
                        }
                    })
                    .catch(err => {
                        this.syncing = false;
                        alert('Kesalahan jaringan: ' + err.message);
                    });
                },

                openCandidateDetail(id) {
                    fetch(`/spmb/pendaftar/${id}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            this.selectedCandidate = res.candidate;
                            this.modalWaUrl = res.wa_url;
                            this.formattedDocuments = res.candidate.formatted_documents || [];
                            this.modalOpen = true;

                            this.$nextTick(() => {
                                if (window.lucide) lucide.createIcons();
                            });
                        }
                    })
                    .catch(err => alert("Gagal memuat detail kandidat: " + err.message));
                },

                openEnrollModal(id) {
                    fetch(`/spmb/pendaftar/${id}/enroll-data`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            this.enrollData = res;
                            this.enrollForm.nis = res.student ? res.student.nis : res.suggested_nis;
                            this.enrollForm.classroom_id = res.student ? res.student.classroom_id : (res.classrooms[0] ? res.classrooms[0].id : '');
                            this.enrollForm.academic_year_id = res.student ? res.student.academic_year_id : res.selected_year_id;
                            this.enrollForm.enrolled_date = res.student && res.student.enrolled_date ? res.student.enrolled_date.substring(0, 10) : '{{ date("Y-m-d") }}';
                            this.enrollModalOpen = true;

                            this.$nextTick(() => {
                                if (window.lucide) lucide.createIcons();
                            });
                        }
                    })
                    .catch(err => alert("Gagal memuat data alokasi siswa: " + err.message));
                },

                submitEnroll() {
                    if (this.enrolling) return;
                    this.enrolling = true;

                    const candidateId = this.enrollData.candidate.id;

                    fetch(`/spmb/pendaftar/${candidateId}/enroll`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.enrollForm)
                    })
                    .then(res => res.json())
                    .then(res => {
                        this.enrolling = false;
                        if (res.success) {
                            this.enrollModalOpen = false;
                            alert(res.message || 'Siswa berhasil diresmikan!');
                            window.location.reload();
                        } else {
                            alert(res.message || 'Gagal meresmikan siswa.');
                        }
                    })
                    .catch(err => {
                        this.enrolling = false;
                        alert('Error: ' + err.message);
                    });
                },

                unenrollStudent(candidateId) {
                    if (!confirm("Apakah Anda yakin ingin membatalkan status Siswa Aktif untuk calon murid ini? Data di tabel Siswa akan dihapus.")) return;

                    fetch(`/spmb/pendaftar/${candidateId}/unenroll`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            this.enrollModalOpen = false;
                            alert(res.message);
                            window.location.reload();
                        } else {
                            alert(res.message || 'Gagal membatalkan enrollment.');
                        }
                    })
                    .catch(err => alert('Error: ' + err.message));
                }
            }
        }
    </script>
</x-admin-layout>
