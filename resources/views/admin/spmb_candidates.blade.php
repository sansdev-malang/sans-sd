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
                            <option value="all" {{ ($selectedYear ?? 'all') === 'all' ? 'selected' : '' }}>Semua Tahun Ajaran</option>
                            @foreach($availableAcademicYears ?? [] as $year)
                                <option value="{{ $year }}" {{ ($selectedYear ?? '') === $year ? 'selected' : '' }}>
                                    Tahun Ajaran {{ $year }}
                                </option>
                            @endforeach
                            @if(empty($availableAcademicYears))
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
                        <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">{{ number_format($stats['total'] ?? 0) }}</h3>
                    </div>
                    <div class="p-2.5 bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 rounded-xl border border-blue-100 dark:border-blue-900/50">
                        <i data-lucide="users" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="mt-3 text-[11px] text-slate-500 dark:text-slate-400">
                    Periode: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ ($selectedYear ?? 'all') === 'all' ? 'Semua Periode' : $selectedYear }}</span>
                </div>
            </div>

            <!-- Stat 2: Terverifikasi / Diterima -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Terverifikasi / Diterima</p>
                        <h3 class="text-2xl font-bold tracking-tight text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($stats['verified'] ?? 0) }}</h3>
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
                        <h3 class="text-2xl font-bold tracking-tight text-indigo-600 dark:text-indigo-400 mt-1">{{ number_format($stats['paid'] ?? 0) }}</h3>
                    </div>
                    <div class="p-2.5 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 rounded-xl border border-indigo-100 dark:border-indigo-900/50">
                        <i data-lucide="wallet" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="mt-3 text-[11px] text-slate-500 dark:text-slate-400">
                    Formulir / Biaya Pendidikan Lunas
                </div>
            </div>

            <!-- Stat 4: Siswa Aktif SD -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-xs flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Sudah Siswa Aktif</p>
                        <h3 class="text-2xl font-bold tracking-tight text-purple-600 dark:text-purple-400 mt-1">{{ number_format($stats['active_students'] ?? ($stats['enrolled'] ?? 0)) }}</h3>
                    </div>
                    <div class="p-2.5 bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 rounded-xl border border-purple-100 dark:border-purple-900/50">
                        <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="mt-3 text-[11px] text-slate-500 dark:text-slate-400">
                    Telah dikonfirmasi masuk SD
                </div>
            </div>
        </section>

        <!-- FILTER & SEARCH BAR -->
        <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-xs">
            <form method="GET" action="{{ route('spmb.candidates.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3">
                <input type="hidden" name="period" value="{{ $selectedYear ?? 'all' }}">

                <!-- Search Input -->
                <div class="md:col-span-5 relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa, no. registrasi, NIK, nama orang tua..."
                        class="w-full pl-9 pr-3.5 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:border-slate-400 dark:focus:border-slate-700 transition-colors">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                </div>

                <!-- Status Filter -->
                <div class="md:col-span-2">
                    <select name="status" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:border-slate-400 dark:focus:border-slate-700 transition-colors cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai / Lulus</option>
                        <option value="agreement_signed" {{ request('status') === 'agreement_signed' ? 'selected' : '' }}>Akad Selesai</option>
                    </select>
                </div>

                <!-- Payment Status Filter -->
                <div class="md:col-span-2">
                    <select name="payment_status" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:border-slate-400 dark:focus:border-slate-700 transition-colors cursor-pointer">
                        <option value="">Semua Pembayaran</option>
                        <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Lunas</option>
                        <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Belum Lunas</option>
                    </select>
                </div>

                <!-- Gelombang Filter -->
                <div class="md:col-span-2">
                    <select name="wave" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none focus:border-slate-400 dark:focus:border-slate-700 transition-colors cursor-pointer">
                        <option value="">Semua Gelombang</option>
                        <option value="Gelombang 1" {{ request('wave') === 'Gelombang 1' ? 'selected' : '' }}>Gelombang 1</option>
                        <option value="Gelombang 2" {{ request('wave') === 'Gelombang 2' ? 'selected' : '' }}>Gelombang 2</option>
                        <option value="Gelombang 3" {{ request('wave') === 'Gelombang 3' ? 'selected' : '' }}>Gelombang 3</option>
                    </select>
                </div>

                <!-- Submit Filter -->
                <div class="md:col-span-1 flex gap-1.5">
                    <button type="submit" class="w-full py-2 bg-slate-900 dark:bg-slate-100 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 rounded-lg text-xs font-bold flex items-center justify-center cursor-pointer transition-colors shadow-xs" title="Terapkan Filter">
                        <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    </button>
                    @if(request()->hasAny(['search', 'status', 'payment_status', 'wave']))
                        <a href="{{ route('spmb.candidates.index', ['period' => $selectedYear ?? 'all']) }}" class="px-2.5 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-lg text-xs font-semibold flex items-center justify-center transition-colors" title="Reset Filter">
                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                        </a>
                    @endif
                </div>
            </form>
        </section>

        <!-- CANDIDATES TABLE CARD -->
        <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-950/70 border-b border-slate-200 dark:border-slate-800 text-[11px] uppercase tracking-wider text-slate-500 dark:text-slate-400 font-bold">
                        <tr>
                            <th class="px-4 py-3.5">No. Registrasi</th>
                            <th class="px-4 py-3.5">Calon Siswa</th>
                            <th class="px-4 py-3.5">Orang Tua & WhatsApp</th>
                            <th class="px-4 py-3.5">Gelombang & Program</th>
                            <th class="px-4 py-3.5">Pembayaran</th>
                            <th class="px-4 py-3.5">Status Siswa</th>
                            <th class="px-4 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($candidates as $c)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                <!-- No. Registrasi -->
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="font-mono font-bold text-slate-900 dark:text-slate-50 text-xs">
                                        {{ $c->registration_number ?? ('REG-' . $c->spmb_registration_id) }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">
                                        {{ $c->created_at ? $c->created_at->format('d M Y, H:i') : '-' }}
                                    </div>
                                    <span class="inline-block mt-1 text-[9px] font-bold px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                        TA {{ $c->academic_year ?? '-' }}
                                    </span>
                                </td>

                                <!-- Calon Siswa -->
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-3">
                                        @php
                                            $photoUrl = $c->student_photo_url;
                                            $isImg = false;
                                            if (!empty($photoUrl)) {
                                                $path = parse_url($photoUrl, PHP_URL_PATH);
                                                $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                                                $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg']);
                                            }
                                        @endphp
                                        @if($isImg)
                                            <img src="{{ $photoUrl }}" alt="Foto" class="w-9 h-9 rounded-xl object-cover ring-1 ring-slate-200 dark:ring-slate-800 shrink-0" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 font-bold text-xs shrink-0 border border-emerald-200 dark:border-emerald-800" style="display: none; align-items: center; justify-content: center;">
                                                {{ $c->initials }}
                                            </div>
                                        @else
                                            <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 flex items-center justify-center font-bold text-xs shrink-0 border border-emerald-200 dark:border-emerald-800" title="{{ !empty($photoUrl) ? 'Berkas Dokumen' : 'Inisial' }}">
                                                {{ $c->initials }}
                                            </div>
                                        @endif
                                        <div class="overflow-hidden">
                                            <div class="font-bold text-slate-900 dark:text-slate-100 truncate text-xs">
                                                {{ $c->full_name }}
                                            </div>
                                            <div class="text-[11px] text-slate-400 flex items-center gap-2 mt-0.5">
                                                <span>{{ $c->gender === 'male' || $c->gender === 'L' ? '👦 Laki-laki' : '👧 Perempuan' }}</span>
                                                @if($c->birth_date)
                                                    <span>• {{ $c->birth_date->age }} th ({{ $c->birth_date->format('d/m/Y') }})</span>
                                                @endif
                                            </div>
                                            @if($c->nik)
                                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                                    NIK: {{ $c->nik }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Orang Tua & WhatsApp -->
                                <td class="px-4 py-3.5">
                                    <div class="font-medium text-slate-800 dark:text-slate-200 text-xs">
                                        {{ $c->father_name ?? ($c->mother_name ?? ($c->guardian_name ?? '-')) }}
                                    </div>
                                    @if($c->parent_phone)
                                        <div class="mt-1 flex items-center gap-1.5">
                                            <a href="{{ $c->whatsapp_url }}" target="_blank" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/50 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 text-[11px] font-semibold border border-emerald-200 dark:border-emerald-800 transition-colors" title="Hubungi via WhatsApp">
                                                <i data-lucide="message-circle" class="w-3 h-3 text-emerald-600"></i>
                                                <span>{{ $c->parent_phone }}</span>
                                            </a>
                                        </div>
                                    @else
                                        <div class="text-[11px] text-slate-400 mt-0.5">Tidak ada no. WA</div>
                                    @endif
                                </td>

                                <!-- Gelombang & Program -->
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="font-semibold text-slate-800 dark:text-slate-200 text-xs">
                                        {{ $c->wave ?? 'Gelombang 1' }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                        {{ $c->target_class ?? 'Kelas 1' }}
                                    </div>
                                    @if($c->previous_school)
                                        <div class="text-[10px] text-slate-400 mt-0.5 truncate max-w-[140px]" title="{{ $c->previous_school }}">
                                            Asal: {{ $c->previous_school }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Status Pembayaran -->
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    @if(in_array(strtolower($c->spmb_payment_status), ['paid', 'lunas', 'settlement', 'success']))
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                            <i data-lucide="check" class="w-3 h-3"></i> Lunas
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                            <i data-lucide="clock" class="w-3 h-3"></i> Belum Lunas
                                        </span>
                                    @endif
                                </td>

                                <!-- Status Siswa Aktif -->
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    @if($c->is_active_student)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-400 border border-purple-200 dark:border-purple-800">
                                            <i data-lucide="sparkles" class="w-3 h-3"></i> Siswa Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                            Belum Terdaftar
                                        </span>
                                    @endif
                                </td>

                                <!-- Aksi -->
                                <td class="px-4 py-3.5 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Tombol Detail -->
                                        <button type="button" @click="openDetail({{ $c->id }})"
                                            class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold transition-colors flex items-center gap-1 cursor-pointer" title="Lihat Biodata Lengkap">
                                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                            <span>Detail</span>
                                        </button>

                                        <!-- Tombol Konfirmasi Siswa Aktif -->
                                        <form method="POST" action="{{ route('spmb.candidates.toggle-active', $c->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" onclick="return confirm('{{ $c->is_active_student ? 'Batalkan status siswa aktif untuk ananda ' . addslashes($c->full_name) . '?' : 'Jadikan ananda ' . addslashes($c->full_name) . ' sebagai Siswa Aktif SD?' }}')"
                                                class="px-2.5 py-1.5 {{ $c->is_active_student ? 'bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300 border border-rose-200 dark:border-rose-800' : 'bg-purple-600 hover:bg-purple-700 text-white' }} rounded-lg text-xs font-bold transition-colors flex items-center gap-1 cursor-pointer shadow-xs"
                                                title="{{ $c->is_active_student ? 'Batalkan Status Siswa Aktif' : 'Tandai sebagai Siswa Aktif' }}">
                                                <i data-lucide="{{ $c->is_active_student ? 'user-x' : 'user-check' }}" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                                            <i data-lucide="inbox" class="w-6 h-6"></i>
                                        </div>
                                        <p class="font-bold text-slate-600 dark:text-slate-300 text-sm">Belum ada data pendaftar SPMB</p>
                                        <p class="text-xs max-w-sm">Klik tombol <b>"Tarik Data dari SPMB"</b> di atas untuk menyinkronkan data pendaftar dari server SPMB.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            @if($candidates->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                    {{ $candidates->links() }}
                </div>
            @endif
        </section>

        <!-- DETAIL MODAL -->
        <div x-show="modalOpen" x-cloak style="display: none; z-index: 9999; margin-top: 0px !important;"
            class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.outside="modalOpen = false"
                class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                
                <!-- Modal Header -->
                <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-950/50">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-xl">
                            <i data-lucide="file-text" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-slate-50" x-text="selectedCandidate?.full_name || 'Detail Pendaftar'"></h3>
                            <p class="text-xs font-mono text-slate-500 dark:text-slate-400">
                                No. Reg: <span class="font-bold text-slate-700 dark:text-slate-300" x-text="selectedCandidate?.registration_number || ('REG-' + selectedCandidate?.spmb_registration_id)"></span>
                                • TA: <span x-text="selectedCandidate?.academic_year || '-'"></span>
                            </p>
                        </div>
                    </div>
                    <button @click="modalOpen = false" class="p-1 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 rounded-lg cursor-pointer">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 overflow-y-auto space-y-6 flex-1 text-xs">
                    <!-- Photo & Quick Info -->
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 p-4 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800">
                        <template x-if="selectedCandidate?.student_photo_url && isImageUrl(selectedCandidate.student_photo_url)">
                            <img :src="selectedCandidate.student_photo_url" class="w-20 h-20 rounded-xl object-cover ring-2 ring-emerald-500/30 shrink-0" x-on:error="$el.style.display='none'">
                        </template>
                        <template x-if="!selectedCandidate?.student_photo_url || !isImageUrl(selectedCandidate?.student_photo_url)">
                            <div class="w-20 h-20 rounded-xl bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 font-bold text-2xl flex items-center justify-center shrink-0 border border-emerald-200 dark:border-emerald-800">
                                <span x-text="selectedCandidate?.initials || selectedCandidate?.full_name?.substring(0, 2)?.toUpperCase()"></span>
                            </div>
                        </template>
                        <div class="space-y-1.5 text-center sm:text-left flex-1">
                            <h4 class="text-sm font-bold text-slate-900 dark:text-slate-50" x-text="selectedCandidate?.full_name"></h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Panggilan: <span class="font-medium text-slate-700 dark:text-slate-300" x-text="selectedCandidate?.nickname || '-'"></span>
                            </p>
                            <div class="flex flex-wrap gap-2 pt-1">
                                <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 font-bold text-[10px] rounded" x-text="selectedCandidate?.wave || 'Gelombang 1'"></span>
                                <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 font-bold text-[10px] rounded" x-text="selectedCandidate?.target_class || 'Kelas 1'"></span>
                                <span class="px-2 py-0.5 bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 font-bold text-[10px] rounded" x-text="selectedCandidate?.spmb_payment_status === 'paid' ? 'Lunas' : 'Belum Lunas'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Biodata Siswa Section -->
                    <div class="space-y-3">
                        <h5 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 flex items-center gap-1.5">
                            <i data-lucide="user" class="w-3.5 h-3.5"></i>
                            Biodata Calon Siswa
                        </h5>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
                            <div><span class="text-slate-400">NIK:</span> <span class="font-semibold text-slate-800 dark:text-slate-200 font-mono" x-text="selectedCandidate?.nik || '-'"></span></div>
                            <div><span class="text-slate-400">NISN:</span> <span class="font-semibold text-slate-800 dark:text-slate-200 font-mono" x-text="selectedCandidate?.nisn || '-'"></span></div>
                            <div><span class="text-slate-400">Jenis Kelamin:</span> <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedCandidate?.gender === 'male' || selectedCandidate?.gender === 'L' ? 'Laki-laki' : 'Perempuan'"></span></div>
                            <div><span class="text-slate-400">Tempat, Tgl Lahir:</span> <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="(selectedCandidate?.birth_place ? selectedCandidate.birth_place + ', ' : '') + (selectedCandidate?.formatted_birth_date || selectedCandidate?.birth_date || '-')"></span></div>
                            <div><span class="text-slate-400">Sekolah Asal:</span> <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedCandidate?.previous_school || '-'"></span></div>
                            <div><span class="text-slate-400">Target Jenjang:</span> <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedCandidate?.target_unit || 'SD'"></span></div>
                            <div class="sm:col-span-2"><span class="text-slate-400">Alamat Lengkap:</span> <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedCandidate?.address || '-'"></span></div>
                        </div>
                    </div>

                    <!-- Orang Tua / Wali Section -->
                    <div class="space-y-3">
                        <h5 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 flex items-center gap-1.5">
                            <i data-lucide="users" class="w-3.5 h-3.5"></i>
                            Data Orang Tua / Wali
                        </h5>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
                            <div><span class="text-slate-400">Nama Ayah:</span> <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedCandidate?.father_name || '-'"></span></div>
                            <div><span class="text-slate-400">Pekerjaan Ayah:</span> <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedCandidate?.father_job || '-'"></span></div>
                            <div><span class="text-slate-400">Nama Ibu:</span> <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedCandidate?.mother_name || '-'"></span></div>
                            <div><span class="text-slate-400">Pekerjaan Ibu:</span> <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedCandidate?.mother_job || '-'"></span></div>
                            <div class="sm:col-span-2 flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800">
                                <div><span class="text-slate-400">Kontak WhatsApp:</span> <span class="font-bold text-slate-800 dark:text-slate-200" x-text="selectedCandidate?.parent_phone || '-'"></span></div>
                                <template x-if="modalWaUrl">
                                    <a :href="modalWaUrl" target="_blank" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold flex items-center gap-1.5 transition-colors">
                                        <i data-lucide="message-circle" class="w-3 h-3"></i> Chat WA
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Dokumen Lampiran Section -->
                    <template x-if="formattedDocuments.length > 0">
                        <div class="space-y-3">
                            <h5 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 flex items-center gap-1.5">
                                <i data-lucide="paperclip" class="w-3.5 h-3.5"></i>
                                Berkas & Lampiran Dokumen
                            </h5>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <template x-for="(doc, idx) in formattedDocuments" :key="idx">
                                    <a :href="doc.url" target="_blank" class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-850 flex items-center justify-between gap-2 transition-colors group">
                                        <div class="flex items-center gap-2 truncate">
                                            <i data-lucide="file-text" class="w-4 h-4 text-slate-400 group-hover:text-emerald-600 shrink-0"></i>
                                            <span class="font-semibold text-slate-700 dark:text-slate-300 truncate text-xs" x-text="doc.name"></span>
                                        </div>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 shrink-0 flex items-center gap-1">
                                            <i data-lucide="external-link" class="w-3 h-3"></i> Buka
                                        </span>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Modal Footer -->
                <div class="p-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-950/50">
                    <div class="text-[11px] text-slate-400 font-mono">
                        Sinkron Terakhir: <span x-text="selectedCandidate?.updated_at ? new Date(selectedCandidate.updated_at).toLocaleString('id-ID') : '-'"></span>
                    </div>
                    <button @click="modalOpen = false" class="px-4 py-2 bg-slate-900 dark:bg-slate-100 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 rounded-lg font-bold transition-colors cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- ALPINE JS APP LOGIC -->
    <script>
        function spmbCandidateApp() {
            return {
                modalOpen: false,
                syncing: false,
                selectedCandidate: null,
                modalWaUrl: null,

                get formattedDocuments() {
                    if (!this.selectedCandidate) return [];
                    if (this.selectedCandidate.formatted_documents && Array.isArray(this.selectedCandidate.formatted_documents) && this.selectedCandidate.formatted_documents.length > 0) {
                        return this.selectedCandidate.formatted_documents;
                    }
                    const docs = this.selectedCandidate.documents;
                    if (!docs) return [];
                    if (Array.isArray(docs)) {
                        return docs.map(d => ({
                            name: d.name || d.label || d.key || 'Berkas Dokumen',
                            url: d.url || '#'
                        }));
                    }
                    if (typeof docs === 'object') {
                        const labelMap = {
                            'student_photo': 'Pas Foto Calon Murid (Foto Formal)',
                            'student_photo_path': 'Pas Foto Calon Murid (Foto Formal)',
                            'birth_certificate': 'Akta Kelahiran',
                            'birth_certificate_path': 'Akta Kelahiran',
                            'family_card': 'Kartu Keluarga (KK)',
                            'family_card_path': 'Kartu Keluarga (KK)',
                            'diploma_certificate': 'Ijazah / Surat Keterangan Aktif Sekolah',
                            'diploma_certificate_path': 'Ijazah / Surat Keterangan Aktif Sekolah',
                            'student_card': 'NISN / KIA / Kartu Pelajar (Opsional)',
                            'student_card_path': 'NISN / KIA / Kartu Pelajar (Opsional)',
                            'special_needs_assessment_path': 'Asesmen Kebutuhan Khusus (Jika Ada)',
                            'payment_receipt_path': 'Bukti Pembayaran Pendaftaran',
                        };
                        return Object.entries(docs).filter(([k, v]) => v && typeof v === 'string' && v.trim() !== '').map(([k, v]) => ({
                            name: labelMap[k] || (k.includes('_') ? k.replace(/_/g, ' ') : k),
                            url: v
                        }));
                    }
                    return [];
                },

                isImageUrl(url) {
                    if (!url) return false;
                    const clean = url.split('?')[0].toLowerCase();
                    return clean.endsWith('.jpg') || clean.endsWith('.jpeg') || clean.endsWith('.png') || clean.endsWith('.webp') || clean.endsWith('.gif') || clean.endsWith('.svg');
                },

                openDetail(id) {
                    fetch(`{{ url('/spmb/pendaftar') }}/${id}`, {
                        headers: {
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        }
                    })
                    .then(res => {
                        if (!res.ok) throw new Error("HTTP Status " + res.status);
                        return res.json();
                    })
                    .then(res => {
                        if (res.status === 'success') {
                            this.selectedCandidate = res.data;
                            this.modalWaUrl = res.whatsapp_url;
                            this.modalOpen = true;
                            this.$nextTick(() => {
                                if (window.lucide) lucide.createIcons();
                            });
                        } else {
                            alert("Gagal memuat: " + (res.message || "Data tidak ditemukan"));
                        }
                    })
                    .catch(err => alert("Gagal memuat detail pendaftar: " + err.message));
                },

                syncData() {
                    if (this.syncing) return;
                    this.syncing = true;

                    fetch("{{ route('spmb.candidates.sync') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        }
                    })
                    .then(res => {
                        this.syncing = false;
                        window.location.reload();
                    })
                    .catch(err => {
                        this.syncing = false;
                        alert("Terjadi kesalahan koneksi: " + err.message);
                    });
                }
            }
        }
    </script>
</x-admin-layout>
