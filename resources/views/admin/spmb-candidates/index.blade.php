<x-admin-layout>
    <div class="p-6 space-y-6" x-data="spmbCandidateApp()">

        <!-- HEADER / ACTION BAR -->
        <section class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <div class="flex items-center gap-2">
                    <h2 class="text-base sm:text-lg font-bold tracking-tight text-slate-900 dark:text-slate-50 flex items-center gap-2">
                        SPMB
                        <span class="text-[11px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 font-bold border border-emerald-200 dark:border-emerald-800">
                            Unit SD
                        </span>
                    </h2>
                </div>
                <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400">Data pendaftar dan calon murid yang masuk dari sistem pendaftaran SPMB Pusat.</p>
            </div>

            <!-- ACTION CONTROLS: TEST CONNECTION & SYNC BUTTON -->
            <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                <!-- Tombol Tes Koneksi API -->
                <button type="button" @click="testConnection()" :disabled="testingConnection"
                    class="inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 disabled:opacity-50 text-xs font-semibold rounded-xl shadow-2xs transition-all cursor-pointer">
                    <i data-lucide="radio" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" :class="{ 'animate-pulse': testingConnection }"></i>
                    <span x-text="testingConnection ? 'Memeriksa...' : 'Tes Koneksi'">Tes Koneksi</span>
                </button>

                <!-- Tombol Tarik Data dari SPMB -->
                <button type="button" @click="syncData()" :disabled="syncing"
                    class="inline-flex items-center justify-center gap-2 px-3.5 sm:px-4 py-2 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white text-xs font-bold rounded-xl shadow-xs transition-all duration-150 cursor-pointer">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5" :class="{ 'animate-spin': syncing }"></i>
                    <span x-text="syncing ? 'Menyinkronkan...' : 'Tarik Data dari SPMB'">Tarik Data dari SPMB</span>
                </button>
            </div>
        </section>

        <!-- COMPACT STAT CARDS GRID -->
        <section class="grid grid-cols-1 sm:grid-cols-2 {{ !empty($stats['has_payment_data']) ? 'lg:grid-cols-4' : 'lg:grid-cols-3' }} gap-3 sm:gap-3.5">
            <!-- Stat 1: Total Pendaftar -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 shadow-xs flex items-center gap-3 transition-colors">
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 border border-blue-100 dark:border-blue-900/40">
                    <i data-lucide="users" class="w-5 h-5"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10.5px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Total Pendaftar</p>
                    <div class="flex items-baseline gap-1.5 mt-0.5">
                        <h3 class="text-xl font-bold tracking-tight text-slate-900 dark:text-slate-50 font-mono">
                            {{ number_format($stats['total']) }}
                        </h3>
                        <span class="text-[10px] text-slate-400 font-medium truncate">({{ $selectedYear === 'all' ? 'Semua TA' : 'TA ' . $selectedYear }})</span>
                    </div>
                </div>
            </div>

            <!-- Stat 2: Terverifikasi / Lolos Berkas -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 shadow-xs flex items-center gap-3 transition-colors">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-100 dark:border-emerald-900/40">
                    <i data-lucide="user-check" class="w-5 h-5"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10.5px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Terverifikasi</p>
                    <div class="flex items-baseline gap-1.5 mt-0.5">
                        <h3 class="text-xl font-bold tracking-tight text-emerald-600 dark:text-emerald-400 font-mono">
                            {{ number_format($stats['verified']) }}
                        </h3>
                        <span class="text-[10px] text-slate-400 font-medium truncate">Lolos Seleksi</span>
                    </div>
                </div>
            </div>

            <!-- Stat 3: Pembayaran Lunas -->
            @if(!empty($stats['has_payment_data']))
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 shadow-xs flex items-center gap-3 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-100 dark:border-indigo-900/40">
                        <i data-lucide="wallet" class="w-5 h-5"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10.5px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Lunas Biaya</p>
                        <div class="flex items-baseline gap-1.5 mt-0.5">
                            <h3 class="text-xl font-bold tracking-tight text-indigo-600 dark:text-indigo-400 font-mono">
                                {{ number_format($stats['paid']) }}
                            </h3>
                            <span class="text-[10px] text-slate-400 font-medium truncate">Administrasi</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Stat 4: Sudah Masuk Siswa Aktif -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 shadow-xs flex items-center gap-3 transition-colors">
                <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0 border border-purple-100 dark:border-purple-900/40">
                    <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10.5px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Siswa Aktif</p>
                    <div class="flex items-baseline gap-1.5 mt-0.5">
                        <h3 class="text-xl font-bold tracking-tight text-purple-600 dark:text-purple-400 font-mono">
                            {{ number_format($stats['enrolled']) }}
                        </h3>
                        <span class="text-[10px] text-slate-400 font-medium truncate">Masuk Rombel</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- SEARCH & FILTER TOOLBAR -->
        <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3.5 sm:p-4 shadow-xs">
            <form method="GET" action="{{ route('spmb.candidates.index') }}" class="flex flex-col gap-3">
                <div class="flex flex-wrap items-center gap-2.5 w-full">
                    
                    <!-- Search input -->
                    <div class="relative flex-1 min-w-[220px] max-w-sm">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari siswa, no. reg, NIK, orang tua..."
                            class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    </div>

                    <!-- Dropdowns Filter Sejajar -->
                    <div class="flex flex-wrap items-center gap-2 flex-1 justify-start xl:justify-end">
                        <!-- 1. Tahun Pelajaran -->
                        <select name="period" onchange="this.form.submit()" 
                            class="px-3 py-2 text-xs font-semibold bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 cursor-pointer">
                            <option value="all" {{ $selectedYear === 'all' ? 'selected' : '' }}>Semua Tahun</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year }}" {{ $selectedYear === $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>

                        <!-- 2. Jalur Masuk -->
                        <select name="registration_type" onchange="this.form.submit()" 
                            class="px-3 py-2 text-xs font-semibold bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 cursor-pointer">
                            <option value="all">Semua Jalur</option>
                            @foreach($registrationTypes as $rType)
                                <option value="{{ $rType }}" {{ request('registration_type') === $rType ? 'selected' : '' }}>
                                    {{ $rType }}
                                </option>
                            @endforeach
                        </select>

                        <!-- 3. Gelombang -->
                        @if(count($availableWaves) > 1)
                            <select name="wave" onchange="this.form.submit()" 
                                class="px-3 py-2 text-xs font-semibold bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 cursor-pointer">
                                <option value="all">Semua Gelombang</option>
                                @foreach($availableWaves as $w)
                                    <option value="{{ $w }}" {{ request('wave') === $w ? 'selected' : '' }}>{{ $w }}</option>
                                @endforeach
                            </select>
                        @endif

                        <!-- 4. Pilihan Kelas -->
                        <select name="admission_level" onchange="this.form.submit()" 
                            class="px-3 py-2 text-xs font-semibold bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 cursor-pointer">
                            <option value="all">Semua Kelas</option>
                            @foreach($availableAdmissionLevels as $lvl)
                                <option value="{{ $lvl }}" {{ request('admission_level') === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                            @endforeach
                        </select>

                        <!-- 5. Kategori Murid (Reguler / PDBK) -->
                        <select name="student_type" onchange="this.form.submit()" 
                            class="px-3 py-2 text-xs font-semibold bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 cursor-pointer">
                            <option value="all">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ (request('student_type') === $cat || request('category') === $cat) ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>

                        <!-- 6. Status Pendaftaran -->
                        <select name="status" onchange="this.form.submit()" 
                            class="px-3 py-2 text-xs font-semibold bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 cursor-pointer">
                            <option value="all">Semua Status Verifikasi</option>
                            <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                            <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Diterima</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        </select>

                        <!-- 7. Status Pembayaran -->
                        @if(!empty($stats['has_payment_data']))
                        <select name="payment_status" onchange="this.form.submit()" 
                            class="px-3 py-2 text-xs font-semibold bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 cursor-pointer">
                            <option value="all">Semua Status Bayar</option>
                            <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Lunas</option>
                            <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Belum Lunas</option>
                        </select>
                        @endif

                        <!-- Filter Jumlah Baris (Per Page) -->
                        <select name="per_page" onchange="this.form.submit()"
                            class="px-3 py-2 text-xs font-semibold bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 cursor-pointer"
                            title="Tampilkan jumlah baris per halaman">
                            <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15 baris</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 baris</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 baris</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 baris</option>
                            <option value="all" {{ request('per_page') === 'all' ? 'selected' : '' }}>Semua Baris</option>
                        </select>

                        @if(request()->hasAny(['search', 'registration_type', 'wave', 'admission_level', 'student_type', 'category', 'status', 'payment_status']) || (request('per_page') && request('per_page') != 15))
                            <a href="{{ route('spmb.candidates.index', ['period' => $selectedYear]) }}" 
                                class="p-2 inline-flex items-center justify-center text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 rounded-xl border border-slate-200 dark:border-slate-700 transition-colors"
                                title="Reset Filter Pencarian">
                                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                            </a>
                        @endif
                    </div>

                </div>
            </form>
        </section>

        <!-- TABLE CANDIDATES -->
        <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs overflow-hidden w-full">
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse min-w-[1050px]">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-950/50 text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            <th class="px-4 py-3.5 w-40">No. Registrasi</th>
                            <th class="px-4 py-3.5 min-w-[210px]">Calon Murid</th>
                            <th class="px-4 py-3.5 w-44">Jalur & Gelombang</th>
                            <th class="px-4 py-3.5 w-48">Jenjang & Kelas</th>
                            <th class="px-4 py-3.5 w-32 text-center">Kategori</th>
                            <th class="px-4 py-3.5 min-w-[170px]">Orang Tua & WA</th>
                            <th class="px-4 py-3.5 text-center w-36">Status Murid</th>
                            <th class="px-4 py-3.5 text-right w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-xs">
                        @forelse($candidates as $c)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors group">
                                <!-- 1. No Registrasi & Periode -->
                                <td class="px-4 py-3.5 align-top whitespace-nowrap">
                                    <div class="font-bold font-mono text-slate-900 dark:text-slate-100">
                                        {{ $c->registration_number }}
                                    </div>
                                    <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-800/60">
                                            TA {{ $c->academic_year }}
                                        </span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-1">
                                        {{ $c->created_at ? $c->created_at->translatedFormat('d M Y, H:i') : '-' }}
                                    </div>
                                </td>

                                <!-- 2. Calon Murid -->
                                <td class="px-4 py-3.5 align-top">
                                    <div class="flex items-start gap-2.5">
                                        @if($c->student_photo_url && !str_ends_with(strtolower($c->student_photo_url), '.pdf'))
                                             <img src="{{ $c->student_photo_url }}" alt="{{ $c->full_name }}" class="w-10 h-10 rounded-xl object-cover ring-1 ring-slate-200 dark:ring-slate-700 shrink-0 shadow-2xs">
                                        @else
                                            <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                                {{ strtoupper(substr($c->full_name, 0, 2)) }}
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <div class="font-bold text-slate-900 dark:text-slate-100 text-xs hover:text-emerald-600 dark:hover:text-emerald-400 cursor-pointer transition-colors" @click="openDetail({{ $c->id }})">
                                                {{ $c->full_name }}
                                            </div>
                                            <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-1 flex-wrap">
                                                <span>{{ ($c->gender === 'L' || $c->gender === 'male') ? '👦 Laki-laki' : (($c->gender === 'P' || $c->gender === 'female') ? '👧 Perempuan' : ($c->gender ?? '-')) }}</span>
                                                @if($c->birth_date)
                                                    <span>• {{ \Carbon\Carbon::parse($c->birth_date)->age }} th</span>
                                                @endif
                                            </div>
                                            @if($c->nik)
                                                <div class="text-[10px] font-mono text-slate-400 mt-0.5">NIK: {{ $c->nik }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- 3. Jalur Masuk & Gelombang -->
                                <td class="px-4 py-3.5 align-top whitespace-nowrap">
                                    <div class="font-semibold text-slate-800 dark:text-slate-200 text-xs flex items-center gap-1">
                                        <i data-lucide="signpost" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i>
                                        <span>{{ $c->registration_type ?: 'Murid Baru' }}</span>
                                    </div>
                                    <div class="mt-1.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-indigo-50/70 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-800/60">
                                            {{ $c->wave ?? 'Gelombang 1' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- 4. Jenjang & Kelas -->
                                <td class="px-4 py-3.5 align-top">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300 border-blue-200 dark:border-blue-800">
                                            SD
                                        </span>
                                        <span class="font-bold text-slate-800 dark:text-slate-100 text-xs">
                                            {{ $c->admission_level ?: ($c->target_class ?: 'Kelas 1') }}
                                        </span>
                                    </div>
                                </td>

                                <!-- 5. Kategori Murid -->
                                <td class="px-4 py-3.5 align-top whitespace-nowrap text-center">
                                    @php
                                        $isMbk = ($c->student_type && in_array(strtoupper($c->student_type), ['PDBK', 'MBK', 'ABK', 'INKLUSI'])) 
                                              || (str_contains(strtoupper($c->target_class ?? ''), 'MBK') || str_contains(strtoupper($c->target_class ?? ''), 'INKLUSI'))
                                              || !empty($c->special_needs_type);
                                    @endphp
                                    @if($isMbk)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 dark:bg-amber-950/70 dark:text-amber-300 border border-amber-300 dark:border-amber-800">
                                            🌟 {{ $c->special_needs_type ?: ($c->class_program ?: 'PDBK') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                            {{ $c->class_program ?: 'Reguler' }}
                                        </span>
                                    @endif
                                </td>

                                <!-- 6. Orang Tua & WhatsApp -->
                                <td class="px-4 py-3.5 align-top">
                                    <div class="font-semibold text-slate-800 dark:text-slate-200 text-xs">
                                        {{ $c->father_name ?? ($c->mother_name ?? ($c->guardian_name ?? '-')) }}
                                    </div>
                                    @if($c->parent_phone)
                                        <div class="mt-1 flex items-center gap-1.5">
                                            <a href="{{ $c->whatsapp_url }}" target="_blank" class="inline-flex items-center gap-1.5 px-2 py-0.8 rounded-lg bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/50 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 text-[10.5px] font-semibold border border-emerald-200 dark:border-emerald-800 transition-colors shadow-2xs" title="Hubungi via WhatsApp">
                                                <i data-lucide="message-circle" class="w-3 h-3 text-emerald-600 dark:text-emerald-400"></i>
                                                <span>{{ $c->parent_phone }}</span>
                                            </a>
                                        </div>
                                    @else
                                        <div class="text-[10px] text-slate-400 mt-0.5">Tidak ada no. WA</div>
                                    @endif
                                </td>

                                <!-- 7. Status Murid Aktif & Pembayaran -->
                                <td class="px-4 py-3.5 align-top whitespace-nowrap text-center">
                                    @if($c->is_enrolled)
                                        <div class="flex flex-col items-center gap-0.5">
                                            <span class="inline-flex items-center justify-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-400 border border-purple-200 dark:border-purple-800 shadow-2xs">
                                                <i data-lucide="sparkles" class="w-3 h-3"></i> Murid Aktif
                                            </span>
                                            @if($c->student)
                                                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-mono font-bold">NIS: {{ $c->student->nis }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="inline-flex items-center justify-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                            Belum Terdaftar
                                        </span>
                                    @endif

                                    @if(!empty($c->payment_status))
                                        <div class="mt-1">
                                            @if(in_array(strtolower($c->payment_status), ['paid', 'lunas', 'settlement', 'success']))
                                                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400">💳 Lunas</span>
                                            @else
                                                <span class="text-[10px] font-semibold text-amber-600 dark:text-amber-400">⏳ Belum Lunas</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                <!-- 8. Aksi -->
                                <td class="px-4 py-3.5 align-top whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Tombol Detail -->
                                        <button type="button" @click="openDetail({{ $c->id }})"
                                            class="px-2.5 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/60 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold transition-colors flex items-center gap-1 shadow-2xs cursor-pointer" title="Lihat Biodata Lengkap">
                                            <i data-lucide="eye" class="w-3.5 h-3.5 text-slate-500"></i>
                                            <span>Detail</span>
                                        </button>

                                        <!-- Tombol Enrollment Murid Aktif: Daftarkan / Kelola -->
                                        <button type="button" @click="openEnrollModal({{ $c->id }})"
                                            class="px-2.5 py-1.5 {{ $c->is_enrolled ? 'bg-purple-100 hover:bg-purple-200 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800' : 'bg-purple-600 hover:bg-purple-700 text-white' }} rounded-lg text-xs font-bold transition-colors flex items-center gap-1 cursor-pointer shadow-xs"
                                            title="{{ $c->is_enrolled ? 'Kelola / Batalkan Murid Aktif' : 'Daftarkan sebagai Murid Aktif' }}">
                                            <i data-lucide="graduation-cap" class="w-3.5 h-3.5"></i>
                                            <span>{{ $c->is_enrolled ? 'Kelola' : 'Daftarkan' }}</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            @php
                                $hasActiveFilters = request()->filled('search') 
                                    || (request()->filled('registration_type') && request('registration_type') !== 'all')
                                    || (request()->filled('wave') && request('wave') !== 'all')
                                    || (request()->filled('admission_level') && request('admission_level') !== 'all')
                                    || (request()->filled('student_type') && request('student_type') !== 'all')
                                    || (request()->filled('category') && request('category') !== 'all')
                                    || (request()->filled('status') && request('status') !== 'all')
                                    || (request()->filled('payment_status') && request('payment_status') !== 'all');
                            @endphp
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                                    <div class="flex flex-col items-center justify-center gap-2.5 max-w-md mx-auto">
                                        @if(($stats['total'] ?? 0) === 0 && !$hasActiveFilters)
                                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shadow-2xs">
                                                <i data-lucide="user-check" class="w-6 h-6"></i>
                                            </div>
                                            <p class="font-bold text-slate-800 dark:text-slate-200 text-sm">Belum Ada Calon Murid Masuk di TA {{ $selectedYear }}</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                                                Data calon murid baru akan otomatis masuk ke SANS SD saat pendaftar di SPMB telah mencapai <b>Tahap Administrasi / Daftar Ulang</b> (surat pernyataan disetujui / pembayaran daftar ulang).
                                            </p>
                                            <button type="button" @click="syncData()" :disabled="syncing"
                                                class="mt-1 inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-colors shadow-xs cursor-pointer">
                                                <i data-lucide="refresh-cw" class="w-3.5 h-3.5" :class="syncing ? 'animate-spin' : ''"></i>
                                                <span>Tarik Data Dari SPMB</span>
                                            </button>
                                        @else
                                            <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-400 shadow-2xs">
                                                <i data-lucide="search-x" class="w-6 h-6"></i>
                                            </div>
                                            <p class="font-bold text-slate-700 dark:text-slate-300 text-sm">Tidak Ada Data Sesuai Filter Pencarian</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                                                Tidak ditemukan calon murid yang cocok dengan kombinasi filter atau kata kunci yang dipilih.
                                            </p>
                                            <a href="{{ route('spmb.candidates.index', ['period' => $selectedYear]) }}"
                                                class="mt-1 inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold border border-slate-200 dark:border-slate-700 transition-colors shadow-2xs">
                                                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                                <span>Reset Filter Pencarian</span>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-5 py-3 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500 dark:text-slate-400">
                <div>
                    Menampilkan <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $candidates->firstItem() ?? 0 }}</span> - <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $candidates->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $candidates->total() }}</span> pendaftar
                    @if(request('per_page') === 'all')
                        <span class="ml-1 text-emerald-600 dark:text-emerald-400 font-medium">(Semua ditampilkan)</span>
                    @endif
                </div>
                @if($candidates->hasPages())
                    <div>
                        {{ $candidates->links() }}
                    </div>
                @endif
            </div>
        </section>

        <!-- ========================================================================= -->
        <!-- MODAL DETAIL PENDAFTAR LENGKAP (4 TABS) -->
        <!-- ========================================================================= -->
        <div x-show="modalOpen" x-cloak class="fixed inset-0 z-[99999] flex items-center justify-center p-4" 
            style="display: none; margin: 0px !important; margin-top: 0px !important; top: 0px !important; left: 0px !important; right: 0px !important; bottom: 0px !important; z-index: 99999 !important; background-color: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px);">
            
            <div @click.outside="modalOpen = false" 
                class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl w-full max-w-3xl overflow-hidden shadow-2xl flex flex-col text-left"
                style="height: 85vh; max-height: 680px; min-height: 500px;">
                
                <!-- Modal Header -->
                <div class="p-5 sm:p-6 border-b border-slate-200 dark:border-slate-800 flex items-start justify-between bg-white dark:bg-slate-900 z-10 shrink-0">
                    <div class="flex items-center gap-3.5 sm:gap-4 overflow-hidden">
                        <template x-if="selectedCandidate?.student_photo_url && !selectedCandidate.student_photo_url.toLowerCase().endsWith('.pdf')">
                            <img :src="selectedCandidate.student_photo_url" x-on:error="$event.target.style.display='none'" class="w-13 h-13 sm:w-14 sm:h-14 rounded-2xl object-cover ring-2 ring-emerald-500/20 shadow-xs shrink-0">
                        </template>
                        <template x-if="!selectedCandidate?.student_photo_url || selectedCandidate.student_photo_url.toLowerCase().endsWith('.pdf')">
                            <div class="w-13 h-13 sm:w-14 sm:h-14 rounded-2xl bg-emerald-100 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 font-bold text-base sm:text-lg flex items-center justify-center shrink-0" 
                                x-text="selectedCandidate?.full_name ? selectedCandidate.full_name.substring(0,2).toUpperCase() : 'PS'">
                            </div>
                        </template>
                        <div class="min-w-0">
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-slate-50 truncate" x-text="selectedCandidate?.full_name"></h3>
                            
                            <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 mt-1 font-mono flex items-center gap-1.5 flex-wrap">
                                <span>No. Reg: <strong class="text-slate-800 dark:text-slate-200" x-text="selectedCandidate?.registration_number"></strong></span>
                                <span>&bull;</span>
                                <span>Kelas: <strong x-text="selectedCandidate?.target_class || 'Kelas 1'"></strong></span>
                                <span>&bull;</span>
                                <span><strong x-text="selectedCandidate?.wave || 'Gelombang 1'"></strong></span>
                                <span>&bull;</span>
                                <span>TA <strong x-text="selectedCandidate?.academic_year"></strong></span>
                            </p>
                        </div>
                    </div>
                    <button @click="modalOpen = false" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer shrink-0">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Detail Navigation Tabs -->
                <div class="flex items-center border-b border-slate-200 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-950/50 text-xs px-5 py-2 gap-2 overflow-x-auto no-scrollbar shrink-0">
                    <button type="button" @click="detailTab = 'bio'"
                        :class="detailTab === 'bio' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-semibold'"
                        class="px-3 py-1.5 rounded-lg transition-colors cursor-pointer flex items-center gap-1.5 shrink-0">
                        <i data-lucide="user" class="w-3.5 h-3.5"></i>
                        <span>Biodata & Pendaftaran</span>
                    </button>
                    <button type="button" @click="detailTab = 'parents'"
                        :class="detailTab === 'parents' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-semibold'"
                        class="px-3 py-1.5 rounded-lg transition-colors cursor-pointer flex items-center gap-1.5 shrink-0">
                        <i data-lucide="users" class="w-3.5 h-3.5"></i>
                        <span>Orang Tua & Kontak</span>
                    </button>
                    <button type="button" @click="detailTab = 'payment'"
                        x-show="selectedCandidate?.has_payment_access || selectedCandidate?.payment_status || (selectedCandidate?.fee_categories && selectedCandidate.fee_categories.length > 0) || (selectedCandidate?.formatted_payments && selectedCandidate.formatted_payments.length > 0)"
                        :class="detailTab === 'payment' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-semibold'"
                        class="px-3 py-1.5 rounded-lg transition-colors cursor-pointer flex items-center gap-1.5 shrink-0">
                        <i data-lucide="wallet" class="w-3.5 h-3.5"></i>
                        <span>Pembayaran & Biaya</span>
                    </button>
                    <button type="button" @click="detailTab = 'docs'"
                        :class="detailTab === 'docs' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-semibold'"
                        class="px-3 py-1.5 rounded-lg transition-colors cursor-pointer flex items-center gap-1.5 shrink-0">
                        <i data-lucide="paperclip" class="w-3.5 h-3.5"></i>
                        <span>Berkas Dokumen (<span x-text="formattedDocuments.length"></span>)</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 overflow-y-auto flex-1 space-y-6 text-xs" style="flex: 1 1 0%; min-height: 0;" x-show="selectedCandidate">

                    <!-- TAB 1: BIODATA CALON SISWA & SPMB -->
                    <div x-show="detailTab === 'bio'" class="space-y-4">
                        <div class="p-4 bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800/60 rounded-xl space-y-3">
                            <h4 class="text-xs font-bold text-emerald-900 dark:text-emerald-300 uppercase tracking-wider flex items-center gap-2 border-b border-emerald-200 dark:border-emerald-800/50 pb-2">
                                <i data-lucide="clipboard-list" class="w-4 h-4 text-emerald-600"></i>
                                Informasi Pendaftaran SPMB
                            </h4>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Tahun Ajaran</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200" x-text="'TA ' + (selectedCandidate?.academic_year || '-')"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Pilihan Kelas</span>
                                    <span class="font-bold text-purple-700 dark:text-purple-300" x-text="selectedCandidate?.target_class || 'Kelas 1'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Kategori Murid</span>
                                    <span class="font-bold" :class="selectedCandidate?.category === 'PDBK' ? 'text-purple-700 dark:text-purple-300' : 'text-slate-800 dark:text-slate-200'" x-text="selectedCandidate?.category === 'PDBK' ? '🌟 PDBK (Inklusi)' : 'Reguler'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Jalur Masuk</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200" x-text="selectedCandidate?.registration_type || 'Murid Baru'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Gelombang</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200" x-text="selectedCandidate?.wave || 'Gelombang 1'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Status Seleksi</span>
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400" x-text="selectedCandidate?.registration_status || 'Verified'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- IDENTITAS LENGKAP -->
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 rounded-xl space-y-4">
                            <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2 border-b border-slate-200 dark:border-slate-700/60 pb-2.5 text-emerald-700 dark:text-emerald-400">
                                <i data-lucide="user-check" class="w-4 h-4"></i>
                                Identitas Lengkap Calon Siswa
                            </h4>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Nama Lengkap</span>
                                    <span class="font-bold text-slate-900 dark:text-slate-100 text-xs" x-text="selectedCandidate?.full_name"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Nama Panggilan</span>
                                    <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedCandidate?.nickname || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Jenis Kelamin</span>
                                    <span class="font-semibold text-slate-800 dark:text-slate-200 flex items-center gap-1 mt-0.5">
                                        <span x-text="selectedCandidate?.gender === 'L' || selectedCandidate?.gender === 'male' ? '👦 Laki-laki' : (selectedCandidate?.gender === 'P' || selectedCandidate?.gender === 'female' ? '👧 Perempuan' : selectedCandidate?.gender || '-')"></span>
                                    </span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Tempat, Tanggal Lahir</span>
                                    <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="(selectedCandidate?.birth_place ? selectedCandidate.birth_place + ', ' : '') + (selectedCandidate?.formatted_birth_date || '-')"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">NIK</span>
                                    <span class="font-mono font-semibold text-slate-800 dark:text-slate-200" x-text="selectedCandidate?.nik || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">No. Kartu Keluarga (KK)</span>
                                    <span class="font-mono font-semibold text-slate-800 dark:text-slate-200" x-text="selectedCandidate?.no_kk || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">NISN</span>
                                    <span class="font-mono font-semibold text-slate-800 dark:text-slate-200" x-text="selectedCandidate?.nisn || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Asal TK / Sekolah Asal</span>
                                    <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedCandidate?.previous_school || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Kebutuhan Khusus (PDBK)</span>
                                    <span class="font-semibold text-purple-700 dark:text-purple-300" x-text="selectedCandidate?.special_needs_type || (selectedCandidate?.student_type === 'PDBK' ? 'Inklusi (MBK)' : 'Tidak Ada')"></span>
                                </div>
                                <div class="sm:col-span-2 lg:col-span-3">
                                    <span class="text-slate-400 block text-[11px]">Alamat Lengkap</span>
                                    <span class="font-medium text-slate-800 dark:text-slate-200 leading-relaxed" x-text="selectedCandidate?.formatted_address || selectedCandidate?.address || '-'"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: ORANG TUA & KONTAK -->
                    <div x-show="detailTab === 'parents'" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Data Ayah -->
                            <div class="p-4 bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 rounded-xl space-y-3">
                                <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2 border-b border-slate-200 dark:border-slate-700/60 pb-2 text-blue-600 dark:text-blue-400">
                                    <i data-lucide="user" class="w-4 h-4"></i>
                                    Data Ayah Kandung
                                </h4>
                                <div class="space-y-2 text-[11px]">
                                    <div>
                                        <span class="text-slate-400 block">Nama Lengkap Ayah</span>
                                        <span class="font-bold text-slate-800 dark:text-slate-200 text-xs" x-text="selectedCandidate?.father_name || '-'"></span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 block">NIK Ayah</span>
                                        <span class="font-mono font-semibold text-slate-800 dark:text-slate-200" x-text="selectedCandidate?.father_nik || '-'"></span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 block">Pekerjaan</span>
                                        <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedCandidate?.father_job || '-'"></span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 block">Pendidikan Terakhir</span>
                                        <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedCandidate?.father_education || '-'"></span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 block">Nomor Telepon / WhatsApp</span>
                                        <span class="font-mono font-semibold text-emerald-600 dark:text-emerald-400" x-text="selectedCandidate?.father_phone || '-'"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Ibu -->
                            <div class="p-4 bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 rounded-xl space-y-3">
                                <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2 border-b border-slate-200 dark:border-slate-700/60 pb-2 text-rose-600 dark:text-rose-400">
                                    <i data-lucide="user" class="w-4 h-4"></i>
                                    Data Ibu Kandung
                                </h4>
                                <div class="space-y-2 text-[11px]">
                                    <div>
                                        <span class="text-slate-400 block">Nama Lengkap Ibu</span>
                                        <span class="font-bold text-slate-800 dark:text-slate-200 text-xs" x-text="selectedCandidate?.mother_name || '-'"></span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 block">NIK Ibu</span>
                                        <span class="font-mono font-semibold text-slate-800 dark:text-slate-200" x-text="selectedCandidate?.mother_nik || '-'"></span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 block">Pekerjaan</span>
                                        <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedCandidate?.mother_job || '-'"></span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 block">Pendidikan Terakhir</span>
                                        <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedCandidate?.mother_education || '-'"></span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 block">Nomor Telepon / WhatsApp</span>
                                        <span class="font-mono font-semibold text-emerald-600 dark:text-emerald-400" x-text="selectedCandidate?.mother_phone || '-'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kontak Utama & WhatsApp Button -->
                        <div class="p-4 bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800/60 rounded-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                            <div>
                                <span class="text-slate-400 block text-[11px]">Nomor WhatsApp Utama Wali Murid</span>
                                <span class="font-mono font-bold text-emerald-700 dark:text-emerald-400 text-sm" x-text="selectedCandidate?.parent_phone || '-'"></span>
                            </div>
                            <template x-if="modalWaUrl">
                                <a :href="modalWaUrl" target="_blank" 
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-xs cursor-pointer">
                                    <i data-lucide="message-circle" class="w-4 h-4"></i>
                                    <span>Chat WhatsApp Orang Tua</span>
                                </a>
                            </template>
                        </div>
                    </div>

                    <!-- TAB 3: PEMBAYARAN & BIAYA SPMB -->
                    <div x-show="detailTab === 'payment'" class="space-y-4">
                        <div class="p-4 bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-200 dark:border-indigo-800/60 rounded-xl space-y-3">
                            <h4 class="text-xs font-bold text-indigo-900 dark:text-indigo-300 uppercase tracking-wider flex items-center gap-2 border-b border-indigo-200 dark:border-indigo-800/50 pb-2">
                                <i data-lucide="credit-card" class="w-4 h-4 text-indigo-600"></i>
                                Status Pembayaran Administrasi SPMB
                            </h4>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Status Bayar</span>
                                    <span class="font-bold text-indigo-700 dark:text-indigo-400" x-text="selectedCandidate?.payment_status === 'paid' ? '✅ Lunas' : '⏳ Belum Lunas'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[11px]">Jumlah Riwayat Invoice</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200" x-text="(selectedCandidate?.formatted_payments?.length || 0) + ' Transaksi'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Tabel Riwayat Pembayaran -->
                        <template x-if="selectedCandidate?.formatted_payments && selectedCandidate.formatted_payments.length > 0">
                            <div class="border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden">
                                <table class="w-full text-xs">
                                    <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800 text-slate-500 font-semibold">
                                        <tr>
                                            <th class="px-3 py-2 text-left">No. Invoice</th>
                                            <th class="px-3 py-2 text-left">Item Biaya</th>
                                            <th class="px-3 py-2 text-right">Nominal</th>
                                            <th class="px-3 py-2 text-left">Metode</th>
                                            <th class="px-3 py-2 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                        <template x-for="(p, pIdx) in selectedCandidate.formatted_payments" :key="pIdx">
                                            <tr>
                                                <td class="px-3 py-2.5 font-mono text-slate-700 dark:text-slate-300 font-medium" x-text="p.invoice_number"></td>
                                                <td class="px-3 py-2.5 text-slate-800 dark:text-slate-200 font-semibold" x-text="p.payment_type"></td>
                                                <td class="px-3 py-2.5 text-right font-mono font-bold text-slate-900 dark:text-slate-100" x-text="p.formatted_amount"></td>
                                                <td class="px-3 py-2.5 text-slate-500" x-text="p.payment_method"></td>
                                                <td class="px-3 py-2.5 text-center">
                                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold"
                                                        :class="p.is_paid ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400'"
                                                        x-text="p.is_paid ? 'Lunas' : 'Pending'">
                                                    </span>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </template>
                    </div>

                    <!-- TAB 4: BERKAS DOKUMEN -->
                    <div x-show="detailTab === 'docs'" class="space-y-4">
                        <template x-if="formattedDocuments.length > 0">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <template x-for="(doc, idx) in formattedDocuments" :key="idx">
                                    <a :href="doc.url" target="_blank" 
                                        class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-950/40 hover:border-emerald-500 hover:bg-emerald-50/20 transition-all group cursor-pointer shadow-2xs">
                                        <div class="flex items-center gap-3 overflow-hidden">
                                            <div class="p-2.5 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 text-emerald-600 shrink-0">
                                                <i data-lucide="file-text" class="w-4 h-4"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <span class="font-bold text-slate-800 dark:text-slate-200 truncate block text-xs" x-text="doc.name"></span>
                                                <span class="text-[10.5px] text-slate-400">Klik untuk buka berkas</span>
                                            </div>
                                        </div>
                                        <i data-lucide="external-link" class="w-4 h-4 text-slate-400 group-hover:text-emerald-600 shrink-0 ml-2"></i>
                                    </a>
                                </template>
                            </div>
                        </template>
                        <template x-if="formattedDocuments.length === 0">
                            <div class="p-8 text-center text-slate-400 border border-dashed border-slate-200 dark:border-slate-800 rounded-xl">
                                <i data-lucide="file-x" class="w-8 h-8 text-slate-300 dark:text-slate-600 mx-auto mb-2"></i>
                                <p class="font-semibold text-slate-600 dark:text-slate-400">Belum ada lampiran berkas yang diunggah</p>
                            </div>
                        </template>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="p-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-950/50 shrink-0">
                    <div class="text-[11px] text-slate-400 font-mono">
                        ID: <span x-text="selectedCandidate?.spmb_registration_id || selectedCandidate?.id || '-'"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="modalOpen = false" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition-colors cursor-pointer">
                            Tutup
                        </button>
                        <button type="button" @click="modalOpen = false; openEditModal(selectedCandidate.id)" class="px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 dark:hover:bg-amber-900/60 rounded-xl text-xs font-bold transition-colors border border-amber-200 dark:border-amber-800 flex items-center gap-1.5 cursor-pointer">
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                            <span>Edit Data</span>
                        </button>
                        <button type="button" @click="modalOpen = false; openEnrollModal(selectedCandidate.id)" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                            <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                            <span x-text="selectedCandidate?.is_enrolled ? 'Kelola Murid Aktif' : 'Daftarkan Murid Aktif'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL ENROLLMENT WIZARD (DAFTARKAN / KELOLA MURID AKTIF) -->
        <!-- ========================================================================= -->
        <div x-show="enrollModalOpen" x-cloak class="fixed inset-0 z-[99999] flex items-center justify-center p-4" 
            style="display: none; margin: 0px !important; margin-top: 0px !important; top: 0px !important; left: 0px !important; right: 0px !important; bottom: 0px !important; z-index: 99999 !important; background-color: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px);">
            <div @click.outside="enrollModalOpen = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl flex flex-col text-left">
                
                <form @submit.prevent="submitEnroll">
                    <!-- Modal Header -->
                    <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-purple-50/50 dark:bg-purple-950/20">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center font-bold shadow-xs">
                                <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-slate-50" x-text="enrollData.candidate?.is_enrolled ? 'Kelola Murid Aktif SD' : 'Daftarkan Murid Aktif SD'">
                                </h3>
                                <p class="text-xs text-slate-400 mt-0.5">Penetapan NIS dan penempatan rombongan belajar.</p>
                            </div>
                        </div>
                        <button type="button" @click="enrollModalOpen = false" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 space-y-4 text-xs" x-show="enrollData.candidate">
                        
                        <!-- Info Card Calon Siswa -->
                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/60 flex items-center gap-3">
                            <template x-if="enrollData.candidate?.student_photo_url && !enrollData.candidate.student_photo_url.toLowerCase().endsWith('.pdf')">
                                <img :src="enrollData.candidate.student_photo_url" x-on:error="$event.target.style.display='none'; $event.target.nextElementSibling.style.display='flex'" class="w-11 h-11 rounded-xl object-cover ring-2 ring-purple-500/20 shadow-xs shrink-0">
                            </template>
                            <div class="w-11 h-11 rounded-xl bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300 font-bold flex items-center justify-center text-xs shrink-0 shadow-xs border border-purple-200 dark:border-purple-800" 
                                x-text="enrollData.candidate?.full_name ? enrollData.candidate.full_name.substring(0, 2).toUpperCase() : 'PS'"
                                :style="enrollData.candidate?.student_photo_url && !enrollData.candidate.student_photo_url.toLowerCase().endsWith('.pdf') ? 'display: none;' : ''">
                            </div>
                            <div class="overflow-hidden min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <h4 class="font-bold text-slate-900 dark:text-slate-50 truncate text-xs" x-text="enrollData.candidate?.full_name"></h4>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300 border border-purple-200 dark:border-purple-800" x-text="enrollData.candidate?.target_class || enrollData.candidate?.admission_level || 'Kelas 1'"></span>
                                </div>
                                <p class="text-[11px] text-slate-400 font-mono mt-0.5" x-text="enrollData.candidate?.registration_number + ' • ' + (enrollData.candidate?.wave || 'Gelombang 1') + ' • ' + (enrollData.candidate?.class_program || 'Reguler')"></p>
                            </div>
                        </div>

                        <!-- NIS Input (Auto-suggested) -->
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Nomor Induk Siswa (NIS) <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" x-model="enrollForm.nis" required placeholder="Contoh: 26.SD.001"
                                class="w-full h-9 px-3 text-xs font-mono font-bold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 text-purple-700 dark:text-purple-300">
                            <p class="text-[10px] text-slate-400 mt-1">Saran format otomatis berdasarkan tahun masuk dan nomor urut SD.</p>
                        </div>

                        <!-- Tahun Pelajaran, Tingkat Kelas & Rombel Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                            <!-- 1. Tahun Pelajaran (Tapel) -->
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Tahun Pelajaran (Tapel) <span class="text-rose-500">*</span>
                                </label>
                                <select x-model="enrollForm.academic_year_id" @change="onEnrollYearChange()" required
                                    class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 text-slate-900 dark:text-slate-50 cursor-pointer">
                                    <template x-for="ay in enrollData.academic_years" :key="ay.id">
                                        <option :value="ay.id" x-text="'Tapel ' + ay.name + (ay.is_active ? ' (Aktif)' : '')"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- 2. Tingkat Kelas (Auto-matched dari pilihan SPMB) -->
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Tingkat Kelas <span class="text-rose-500">*</span>
                                </label>
                                <select x-model="enrollForm.class_level_id" @change="onClassLevelChange()" required
                                    class="w-full h-9 px-3 text-xs font-bold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 text-purple-700 dark:text-purple-300 cursor-pointer">
                                    <template x-for="lvl in (enrollData.class_levels || [])" :key="lvl.id">
                                        <option :value="lvl.id" x-text="lvl.name"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- 3. Rombongan Belajar (Cascade sesuai Tingkat Kelas) -->
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Pilih Rombel <span class="text-rose-500">*</span>
                                </label>
                                <select x-model="enrollForm.classroom_id" required
                                    class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 text-slate-900 dark:text-slate-50 cursor-pointer">
                                    <option value="">Pilih Rombel...</option>
                                    <template x-for="r in filteredClassrooms" :key="r.id">
                                        <option :value="r.id" x-text="(r.code ? r.code + ' - ' : '') + r.name + ' • ' + (r.active_students_count ?? 0) + '/' + (r.capacity ?? 28) + ' siswa'"></option>
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
                                    Calon murid ini telah berstatus <b>Murid Aktif</b>. Anda dapat mengubah rombel atau membatalkan status murid aktif melalui tombol di bawah.
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Modal Footer -->
                    <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/50 flex items-center justify-between">
                        <div>
                            <template x-if="enrollData.candidate?.is_enrolled">
                                <button type="button" @click="promptUnenroll(enrollData.candidate.id, enrollData.candidate.full_name)" class="px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400 dark:hover:bg-rose-900/60 rounded-xl text-xs font-bold transition-colors border border-rose-200 dark:border-rose-800 cursor-pointer">
                                    Batalkan Status Murid Aktif
                                </button>
                            </template>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="enrollModalOpen = false" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition-colors cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" :disabled="enrolling" class="px-5 py-2 bg-purple-600 hover:bg-purple-700 disabled:opacity-50 text-white rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                                <i data-lucide="check" class="w-4 h-4"></i>
                                <span x-text="enrolling ? 'Memproses...' : (enrollData.candidate?.is_enrolled ? 'Simpan Perubahan' : 'Daftarkan Murid Aktif')"></span>
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL EDIT DATA PENDAFTAR -->
        <!-- ========================================================================= -->
        <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-[99999] flex items-center justify-center p-4" 
            style="display: none; margin: 0px !important; margin-top: 0px !important; top: 0px !important; left: 0px !important; right: 0px !important; bottom: 0px !important; z-index: 99999 !important; background-color: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px);">
            <div @click.outside="editModalOpen = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden shadow-2xl flex flex-col text-left">
                
                <form @submit.prevent="submitEdit" class="flex flex-col h-full overflow-hidden">
                    <!-- Modal Header -->
                    <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-amber-50/50 dark:bg-amber-950/20 shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold shadow-xs">
                                <i data-lucide="edit-3" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-slate-50 flex items-center gap-2">
                                    Edit Data Pendaftar SPMB
                                </h3>
                                <p class="text-xs text-slate-400 mt-0.5">Perbarui biodata calon murid, data orang tua, dan status pendaftaran.</p>
                            </div>
                        </div>
                        <button type="button" @click="editModalOpen = false" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 space-y-6 text-xs overflow-y-auto max-h-[calc(90vh-140px)]">
                        
                        <!-- Section 1: Data Calon Siswa -->
                        <div>
                            <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider mb-3 flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-2">
                                <i data-lucide="user" class="w-4 h-4 text-amber-600"></i>
                                1. Biodata Calon Siswa
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                                <div class="sm:col-span-2">
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                        Nama Lengkap <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" x-model="editForm.full_name" required placeholder="Nama lengkap calon siswa"
                                        class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-slate-900 dark:text-slate-50">
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Panggilan</label>
                                    <input type="text" x-model="editForm.nickname" placeholder="Nama panggilan"
                                        class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-slate-900 dark:text-slate-50">
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                        Jenis Kelamin <span class="text-rose-500">*</span>
                                    </label>
                                    <select x-model="editForm.gender" required
                                        class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-slate-900 dark:text-slate-50 cursor-pointer">
                                        <option value="male">Laki-laki</option>
                                        <option value="female">Perempuan</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tempat Lahir</label>
                                    <input type="text" x-model="editForm.birth_place" placeholder="Kota lahir"
                                        class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-slate-900 dark:text-slate-50">
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Lahir</label>
                                    <input type="date" x-model="editForm.birth_date"
                                        class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-slate-900 dark:text-slate-50">
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">NIK</label>
                                    <input type="text" x-model="editForm.nik" placeholder="16 digit NIK"
                                        class="w-full h-9 px-3 text-xs font-mono bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-slate-900 dark:text-slate-50">
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. Kartu Keluarga (KK)</label>
                                    <input type="text" x-model="editForm.no_kk" placeholder="16 digit No. KK"
                                        class="w-full h-9 px-3 text-xs font-mono bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-slate-900 dark:text-slate-50">
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">NISN</label>
                                    <input type="text" x-model="editForm.nisn" placeholder="10 digit NISN (jika ada)"
                                        class="w-full h-9 px-3 text-xs font-mono bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-slate-900 dark:text-slate-50">
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Asal TK / Sekolah Sebelumnya</label>
                                    <input type="text" x-model="editForm.previous_school" placeholder="Nama TK asal"
                                        class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-slate-900 dark:text-slate-50">
                                </div>

                                <div class="sm:col-span-2 lg:col-span-3">
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Alamat Lengkap</label>
                                    <textarea x-model="editForm.address" rows="2" placeholder="Alamat jalan, nomor rumah, RT/RW..."
                                        class="w-full p-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-slate-900 dark:text-slate-50"></textarea>
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kota / Kabupaten</label>
                                    <input type="text" x-model="editForm.city" placeholder="Contoh: Malang"
                                        class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-slate-900 dark:text-slate-50">
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Provinsi</label>
                                    <input type="text" x-model="editForm.province" placeholder="Contoh: Jawa Timur"
                                        class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-slate-900 dark:text-slate-50">
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Data Orang Tua / Wali -->
                        <div>
                            <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider mb-3 flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-2">
                                <i data-lucide="users" class="w-4 h-4 text-amber-600"></i>
                                2. Data Orang Tua / Wali
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Ayah</label>
                                    <input type="text" x-model="editForm.father_name" placeholder="Nama ayah kandung"
                                        class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-slate-900 dark:text-slate-50">
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">NIK Ayah</label>
                                    <input type="text" x-model="editForm.father_nik" placeholder="16 digit NIK"
                                        class="w-full h-9 px-3 text-xs font-mono bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-slate-900 dark:text-slate-50">
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. HP / WA Ayah</label>
                                    <input type="text" x-model="editForm.father_phone" placeholder="08..."
                                        class="w-full h-9 px-3 text-xs font-mono bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-slate-900 dark:text-slate-50">
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Ibu</label>
                                    <input type="text" x-model="editForm.mother_name" placeholder="Nama ibu kandung"
                                        class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-slate-900 dark:text-slate-50">
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">NIK Ibu</label>
                                    <input type="text" x-model="editForm.mother_nik" placeholder="16 digit NIK"
                                        class="w-full h-9 px-3 text-xs font-mono bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-slate-900 dark:text-slate-50">
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. HP / WA Ibu</label>
                                    <input type="text" x-model="editForm.mother_phone" placeholder="08..."
                                        class="w-full h-9 px-3 text-xs font-mono bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-slate-900 dark:text-slate-50">
                                </div>

                                <div class="sm:col-span-2 lg:col-span-3">
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                        Kontak Utama WhatsApp Orang Tua <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" x-model="editForm.parent_phone" placeholder="081234567890"
                                        class="w-full h-9 px-3 text-xs font-mono font-bold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-slate-900 dark:text-slate-50">
                                    <p class="text-[10px] text-slate-400 mt-1">Nomor ini digunakan untuk tombol kirim WhatsApp cepat dan komunikasi sekolah.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Status SPMB & Pembayaran -->
                        <div>
                            <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider mb-3 flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-2">
                                <i data-lucide="badge-check" class="w-4 h-4 text-amber-600"></i>
                                3. Informasi Pendaftaran & Status
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tahun Pelajaran (Tapel)</label>
                                    <select x-model="editForm.academic_year"
                                        class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-slate-900 dark:text-slate-50 cursor-pointer">
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year }}">Tapel {{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Gelombang</label>
                                    <input type="text" x-model="editForm.wave" placeholder="Contoh: Gelombang 1"
                                        class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-slate-900 dark:text-slate-50">
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori Murid</label>
                                    <select x-model="editForm.student_type"
                                        class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-slate-900 dark:text-slate-50 cursor-pointer">
                                        <option value="REGULER">Reguler</option>
                                        <option value="PDBK">PDBK (Inklusi)</option>
                                    </select>
                                </div>

                                <div x-show="editForm.student_type === 'PDBK'">
                                    <label class="block font-semibold text-purple-700 dark:text-purple-300 mb-1">Diagnosa Kekhususan (PDBK)</label>
                                    <input type="text" x-model="editForm.special_needs_type" placeholder="Contoh: Autism, Slow Learner, ADHD..."
                                        class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-purple-300 dark:border-purple-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 text-slate-900 dark:text-slate-50">
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Pendaftaran</label>
                                    <select x-model="editForm.registration_status"
                                        class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-slate-900 dark:text-slate-50 cursor-pointer">
                                        <option value="verified">Terverifikasi (Verified)</option>
                                        <option value="accepted">Diterima (Accepted)</option>
                                        <option value="completed">Selesai (Completed)</option>
                                        <option value="pending">Menunggu Verifikasi (Pending)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Pembayaran</label>
                                    <select x-model="editForm.payment_status"
                                        class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-slate-900 dark:text-slate-50 cursor-pointer">
                                        <option value="paid">Lunas (Paid)</option>
                                        <option value="unpaid">Belum Lunas (Unpaid)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Modal Footer -->
                    <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/50 flex items-center justify-between shrink-0">
                        <span class="text-[11px] text-slate-400">
                            * Perubahan otomatis menyinkronkan data siswa aktif jika calon siswa sudah resmi terdaftar.
                        </span>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="editModalOpen = false" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition-colors cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" :disabled="editing" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 disabled:opacity-50 text-white rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                                <i data-lucide="check" class="w-4 h-4"></i>
                                <span x-text="editing ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- DELETE CONFIRMATION MODAL -->
        <!-- ========================================================================= -->
        <div x-show="deleteModalOpen" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity"
            x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div @click.away="if(!deleting) deleteModalOpen = false"
                class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4 text-left"
                x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                <div class="flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0 border border-rose-100 dark:border-rose-900/50">
                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Hapus Data Calon Siswa SPMB</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            Apakah Anda yakin ingin menghapus calon pendaftar <strong class="text-slate-800 dark:text-slate-200 font-bold" x-text="candidateToDelete.name"></strong>?
                        </p>
                        <div class="mt-2.5 p-2.5 rounded-lg bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/50 text-[11px] text-amber-800 dark:text-amber-300">
                            <i data-lucide="alert-circle" class="w-3.5 h-3.5 inline mr-1 -mt-0.5"></i>
                            <span>Jika pendaftar ini sudah terdaftar sebagai Siswa Aktif, data terkait di modul kesiswaan juga akan dibersihkan. Tindakan ini tidak dapat dibatalkan.</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="deleteModalOpen = false" :disabled="deleting"
                        class="px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="confirmDelete()" :disabled="deleting"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all cursor-pointer">
                        <i data-lucide="loader-2" class="w-3.5 h-3.5 animate-spin" x-show="deleting"></i>
                        <span x-text="deleting ? 'Menghapus...' : 'Ya, Hapus Data'">Ya, Hapus Data</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- UNENROLL CONFIRMATION MODAL -->
        <!-- ========================================================================= -->
        <div x-show="unenrollModalOpen" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity"
            x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div @click.away="if(!unenrolling) unenrollModalOpen = false"
                class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4 text-left"
                x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                <div class="flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 border border-amber-100 dark:border-amber-900/50">
                        <i data-lucide="user-minus" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Batalkan Status Siswa Aktif</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            Apakah Anda yakin ingin membatalkan status Siswa Aktif untuk <strong class="text-slate-800 dark:text-slate-200 font-bold" x-text="unenrollCandidate.name"></strong>?
                        </p>
                        <p class="text-[11px] text-slate-400 mt-1.5">
                            Data di tabel Siswa Aktif akan dihapus, namun data calon pendaftar di SPMB tetap tersimpan.
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="unenrollModalOpen = false" :disabled="unenrolling"
                        class="px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="confirmUnenroll()" :disabled="unenrolling"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all cursor-pointer">
                        <i data-lucide="loader-2" class="w-3.5 h-3.5 animate-spin" x-show="unenrolling"></i>
                        <span x-text="unenrolling ? 'Membatalkan...' : 'Ya, Batalkan Status'">Ya, Batalkan Status</span>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- Alpine.js Application Logic -->
    <script>
        function spmbCandidateApp() {
            return {
                syncing: false,
                testingConnection: false,
                modalOpen: false,
                detailTab: 'bio',
                enrollModalOpen: false,
                editModalOpen: false,
                deleteModalOpen: false,
                unenrollModalOpen: false,
                enrolling: false,
                editing: false,
                deleting: false,
                unenrolling: false,
                selectedCandidate: null,
                modalWaUrl: null,
                formattedDocuments: [],
                candidateToDelete: { id: null, name: '' },
                unenrollCandidate: { id: null, name: '' },
                enrollData: {
                    candidate: null,
                    academic_years: [],
                    class_levels: [],
                    classrooms: [],
                },
                enrollForm: {
                    nis: '',
                    class_level_id: '',
                    classroom_id: '',
                    academic_year_id: '',
                    enrolled_date: '{{ date("Y-m-d") }}',
                    notes: '',
                },

                get filteredClassrooms() {
                    if (!this.enrollData || !this.enrollData.classrooms) return [];
                    const levelId = parseInt(this.enrollForm.class_level_id);
                    const ayId = parseInt(this.enrollForm.academic_year_id);
                    return this.enrollData.classrooms.filter(r => {
                        const matchLevel = !levelId || parseInt(r.class_level_id) === levelId;
                        const matchAy = !ayId || !r.academic_year_id || parseInt(r.academic_year_id) === ayId;
                        return matchLevel && matchAy;
                    });
                },

                onClassLevelChange() {
                    const available = this.filteredClassrooms;
                    if (available.length > 0) {
                        const currentStillValid = available.some(r => r.id === parseInt(this.enrollForm.classroom_id));
                        if (!currentStillValid) {
                            this.enrollForm.classroom_id = available[0].id;
                        }
                    } else {
                        this.enrollForm.classroom_id = '';
                    }
                },
                editForm: {
                    id: null,
                    full_name: '',
                    nickname: '',
                    gender: 'male',
                    birth_place: '',
                    birth_date: '',
                    nik: '',
                    no_kk: '',
                    nisn: '',
                    student_type: 'REGULER',
                    special_needs_type: '',
                    target_class: 'Kelas 1',
                    academic_year: '{{ $selectedYear !== "all" ? $selectedYear : date("Y") . "/" . (date("Y") + 1) }}',
                    wave: 'Gelombang 1',
                    father_name: '',
                    father_nik: '',
                    father_phone: '',
                    father_job: '',
                    mother_name: '',
                    mother_nik: '',
                    mother_phone: '',
                    mother_job: '',
                    guardian_name: '',
                    guardian_phone: '',
                    parent_phone: '',
                    address: '',
                    city: '',
                    province: '',
                    previous_school: '',
                    registration_status: 'verified',
                    payment_status: 'unpaid',
                },

                testConnection() {
                    if (this.testingConnection) return;
                    this.testingConnection = true;

                    fetch('{{ route("spmb.candidates.test-connection") }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.testingConnection = false;
                        if (data.success) {
                            if (typeof window.showToast === 'function') {
                                window.showToast('Koneksi Berhasil', data.message, 'success');
                            }
                        } else {
                            if (typeof window.showToast === 'function') {
                                window.showToast('Koneksi Gagal', data.message, 'error');
                            }
                        }
                    })
                    .catch(err => {
                        this.testingConnection = false;
                        if (typeof window.showToast === 'function') {
                            window.showToast('Kesalahan Jaringan', err.message, 'error');
                        }
                    });
                },

                syncData() {
                    if (this.syncing) return;
                    this.syncing = true;
                    if (typeof window.showToast === 'function') {
                        window.showToast('Menyinkronkan', 'Sedang mengambil data pendaftar terbaru dari SPMB Pusat...', 'info');
                    }

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
                            if (typeof window.showToast === 'function') {
                                window.showToast('Sinkronisasi Berhasil', data.message || 'Data pendaftar SPMB berhasil diperbarui.', 'success');
                            }
                            setTimeout(() => window.location.reload(), 600);
                        } else {
                            if (typeof window.showToast === 'function') {
                                window.showToast('Sinkronisasi Gagal', data.message || 'Terjadi kesalahan saat mengambil data SPMB', 'error');
                            }
                        }
                    })
                    .catch(err => {
                        this.syncing = false;
                        if (typeof window.showToast === 'function') {
                            window.showToast('Kesalahan Jaringan', err.message, 'error');
                        }
                    });
                },

                openDetail(id) {
                    this.openCandidateDetail(id);
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
                            this.detailTab = 'bio';
                            this.modalOpen = true;

                            this.$nextTick(() => {
                                if (window.lucide) lucide.createIcons();
                            });
                        } else {
                            if (typeof window.showToast === 'function') {
                                window.showToast('Gagal Memuat Detail', res.message || 'Data tidak ditemukan', 'error');
                            }
                        }
                    })
                    .catch(err => {
                        if (typeof window.showToast === 'function') {
                            window.showToast('Gagal Memuat Detail', err.message, 'error');
                        }
                    });
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
                            this.enrollForm.academic_year_id = res.student ? res.student.academic_year_id : res.selected_year_id;
                            
                            // Auto select tingkat kelas matching candidate target_class / student
                            let levelId = '';
                            if (res.student && res.student.classroom && res.student.classroom.class_level_id) {
                                levelId = res.student.classroom.class_level_id;
                            } else if (res.selected_class_level_id) {
                                levelId = res.selected_class_level_id;
                            } else if (res.class_levels && res.class_levels.length > 0) {
                                levelId = res.class_levels[0].id;
                            }
                            this.enrollForm.class_level_id = levelId;

                            // Auto select rombel belonging to the chosen class level
                            if (res.student && res.student.classroom_id) {
                                this.enrollForm.classroom_id = res.student.classroom_id;
                            } else {
                                const matchingRooms = (res.classrooms || []).filter(r => !levelId || parseInt(r.class_level_id) === parseInt(levelId));
                                this.enrollForm.classroom_id = matchingRooms.length > 0 ? matchingRooms[0].id : (res.classrooms[0] ? res.classrooms[0].id : '');
                            }

                            this.enrollForm.enrolled_date = res.student && res.student.enrolled_date ? res.student.enrolled_date.substring(0, 10) : '{{ date("Y-m-d") }}';
                            this.enrollModalOpen = true;

                            this.$nextTick(() => {
                                if (window.lucide) lucide.createIcons();
                            });
                        } else {
                            if (typeof window.showToast === 'function') {
                                window.showToast('Gagal Memuat Alokasi', res.message || 'Terjadi kesalahan', 'error');
                            }
                        }
                    })
                    .catch(err => {
                        if (typeof window.showToast === 'function') {
                            window.showToast('Gagal Memuat Alokasi', err.message, 'error');
                        }
                    });
                },

                onEnrollYearChange() {
                    const selectedAyId = parseInt(this.enrollForm.academic_year_id);
                    const selectedAy = (this.enrollData.academic_years || []).find(ay => ay.id === selectedAyId);
                    if (selectedAy) {
                        const rawName = selectedAy.name || '2026';
                        const yearDigits = rawName.split('/')[0].slice(-2);
                        const prefix = `${yearDigits}.SD.`;
                        if (this.enrollForm.nis && (!this.enrollData.student || !this.enrollData.student.id)) {
                            const seqMatch = this.enrollForm.nis.match(/(\d+)$/);
                            const seq = seqMatch ? seqMatch[1] : '001';
                            this.enrollForm.nis = prefix + seq;
                        }
                    }
                    this.onClassLevelChange();
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
                            if (typeof window.showToast === 'function') {
                                window.showToast('Siswa Diresmikan', res.message || 'Siswa berhasil resmi terdaftar sebagai Siswa Aktif!', 'success');
                            }
                            setTimeout(() => window.location.reload(), 600);
                        } else {
                            if (typeof window.showToast === 'function') {
                                window.showToast('Gagal Meresmikan', res.message || 'Gagal meresmikan siswa.', 'error');
                            }
                        }
                    })
                    .catch(err => {
                        this.enrolling = false;
                        if (typeof window.showToast === 'function') {
                            window.showToast('Kesalahan Jaringan', err.message, 'error');
                        }
                    });
                },

                promptUnenroll(candidateId, candidateName) {
                    this.unenrollCandidate = { id: candidateId, name: candidateName };
                    this.unenrollModalOpen = true;
                    this.$nextTick(() => {
                        if (window.lucide) lucide.createIcons();
                    });
                },

                confirmUnenroll() {
                    if (this.unenrolling || !this.unenrollCandidate.id) return;
                    this.unenrolling = true;

                    fetch(`/spmb/pendaftar/${this.unenrollCandidate.id}/unenroll`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => res.json())
                    .then(res => {
                        this.unenrolling = false;
                        this.unenrollModalOpen = false;
                        this.enrollModalOpen = false;
                        if (res.success) {
                            if (typeof window.showToast === 'function') {
                                window.showToast('Status Dibatalkan', res.message, 'success');
                            }
                            setTimeout(() => window.location.reload(), 600);
                        } else {
                            if (typeof window.showToast === 'function') {
                                window.showToast('Gagal Membatalkan', res.message || 'Gagal membatalkan status siswa aktif.', 'error');
                            }
                        }
                    })
                    .catch(err => {
                        this.unenrolling = false;
                        if (typeof window.showToast === 'function') {
                            window.showToast('Kesalahan Jaringan', err.message, 'error');
                        }
                    });
                },

                openEditModal(id) {
                    fetch(`/spmb/pendaftar/${id}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success && res.candidate) {
                            const c = res.candidate;
                            this.editForm = {
                                id: c.id,
                                full_name: c.full_name || '',
                                nickname: c.nickname || '',
                                gender: (c.gender === 'female' || c.gender === 'P') ? 'female' : 'male',
                                birth_place: c.birth_place || '',
                                birth_date: c.birth_date ? c.birth_date.substring(0, 10) : '',
                                nik: c.nik || '',
                                no_kk: c.no_kk || '',
                                nisn: c.nisn || '',
                                student_type: (c.student_type === 'PDBK' || c.student_type === 'MBK' || c.student_type === 'ABK' || c.target_class === 'MBK' || c.target_class === 'Inklusi' || c.special_needs_type) ? 'PDBK' : 'REGULER',
                                special_needs_type: c.special_needs_type || '',
                                target_class: c.target_class || 'Kelas 1',
                                academic_year: c.academic_year || '{{ $selectedYear !== "all" ? $selectedYear : date("Y") . "/" . (date("Y") + 1) }}',
                                wave: c.wave || 'Gelombang 1',
                                father_name: c.father_name || '',
                                father_nik: c.father_nik || '',
                                father_phone: c.father_phone || '',
                                father_job: c.father_job || '',
                                mother_name: c.mother_name || '',
                                mother_nik: c.mother_nik || '',
                                mother_phone: c.mother_phone || '',
                                mother_job: c.mother_job || '',
                                guardian_name: c.guardian_name || '',
                                guardian_phone: c.guardian_phone || '',
                                parent_phone: c.parent_phone || '',
                                address: c.address || '',
                                city: c.city || '',
                                province: c.province || '',
                                previous_school: c.previous_school || '',
                                registration_status: c.registration_status || c.spmb_status || 'verified',
                                payment_status: c.payment_status || c.spmb_payment_status || 'unpaid',
                            };
                            this.editModalOpen = true;

                            this.$nextTick(() => {
                                if (window.lucide) lucide.createIcons();
                            });
                        } else {
                            if (typeof window.showToast === 'function') {
                                window.showToast('Gagal Memuat Data', res.message || 'Data tidak ditemukan', 'error');
                            }
                        }
                    })
                    .catch(err => {
                        if (typeof window.showToast === 'function') {
                            window.showToast('Gagal Memuat Data', err.message, 'error');
                        }
                    });
                },

                submitEdit() {
                    if (this.editing) return;
                    this.editing = true;

                    fetch(`/spmb/pendaftar/${this.editForm.id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.editForm)
                    })
                    .then(res => res.json())
                    .then(res => {
                        this.editing = false;
                        if (res.success) {
                            this.editModalOpen = false;
                            if (typeof window.showToast === 'function') {
                                window.showToast('Data Diperbarui', res.message || 'Data pendaftar berhasil diperbarui!', 'success');
                            }
                            setTimeout(() => window.location.reload(), 600);
                        } else {
                            if (typeof window.showToast === 'function') {
                                window.showToast('Gagal Menyimpan', res.message || 'Terjadi kesalahan saat menyimpan', 'error');
                            }
                        }
                    })
                    .catch(err => {
                        this.editing = false;
                        if (typeof window.showToast === 'function') {
                            window.showToast('Kesalahan Jaringan', err.message, 'error');
                        }
                    });
                },

                promptDelete(id, name) {
                    this.candidateToDelete = { id: id, name: name };
                    this.deleteModalOpen = true;
                    this.$nextTick(() => {
                        if (window.lucide) lucide.createIcons();
                    });
                },

                confirmDelete() {
                    if (this.deleting || !this.candidateToDelete.id) return;
                    this.deleting = true;

                    fetch(`/spmb/pendaftar/${this.candidateToDelete.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => res.json())
                    .then(res => {
                        this.deleting = false;
                        this.deleteModalOpen = false;
                        if (res.success) {
                            if (typeof window.showToast === 'function') {
                                window.showToast('Data Dihapus', res.message || 'Data pendaftar berhasil dihapus.', 'success');
                            }
                            setTimeout(() => window.location.reload(), 600);
                        } else {
                            if (typeof window.showToast === 'function') {
                                window.showToast('Gagal Menghapus', res.message || 'Terjadi kesalahan saat menghapus data.', 'error');
                            }
                        }
                    })
                    .catch(err => {
                        this.deleting = false;
                        if (typeof window.showToast === 'function') {
                            window.showToast('Kesalahan Jaringan', err.message, 'error');
                        }
                    });
                }
            }
        }
    </script>
</x-admin-layout>
