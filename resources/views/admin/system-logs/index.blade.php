<x-admin-layout>
    <div class="p-6 space-y-6">

        <!-- HEADER / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex flex-col gap-0.5">
                <div class="flex items-center gap-2">
                    <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 font-nasalization">Log Sistem</h2>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                        {{ $selectedFileName }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                    Pantau rekaman error, peringatan, dan status runtime sistem Laravel secara real-time.
                </p>
            </div>

            <div class="flex items-center flex-wrap gap-2.5">
                <!-- Refresh Button -->
                <button type="button" onclick="window.location.reload()"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-colors shadow-sm cursor-pointer"
                    title="Muat Ulang Halaman">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                    <span>Segarkan</span>
                </button>

                <!-- Download Log Button -->
                @if($selectedFile && $selectedFile['raw_size'] > 0)
                    <a href="{{ route('system-logs.download', ['file' => $selectedFileName]) }}"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-colors shadow-sm cursor-pointer"
                        title="Unduh Berkas Log Mentah">
                        <i data-lucide="download" class="w-3.5 h-3.5"></i>
                        <span>Unduh Log</span>
                    </a>
                @endif

                <!-- Clear Log Form -->
                @if($selectedFile && $selectedFile['raw_size'] > 0)
                    <form action="{{ route('system-logs.clear', ['file' => $selectedFileName]) }}" method="POST"
                          onsubmit="return confirm('Apakah Anda yakin ingin mengosongkan file log {{ $selectedFileName }}? Tindakan ini tidak dapat dibatalkan.');">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 dark:bg-rose-600 dark:hover:bg-rose-700 rounded-lg transition-colors shadow-sm cursor-pointer">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            <span>Bersihkan Log</span>
                        </button>
                    </form>
                @endif
            </div>
        </section>

        <!-- STAT CARDS GRID -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Logs -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Entri Log</p>
                        <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                            {{ number_format($levelStats['total'], 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="p-2.5 bg-slate-100 dark:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-400">
                        <i data-lucide="file-text" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="mt-3 text-xs text-slate-500 dark:text-slate-400">
                    Ukuran Berkas: <span class="font-bold text-slate-700 dark:text-slate-300">{{ $selectedFile['size'] ?? '0 B' }}</span>
                </div>
            </div>

            <!-- Error / Critical -->
            @php
                $errorCount = $levelStats['error'] + $levelStats['critical'] + $levelStats['emergency'] + $levelStats['alert'];
            @endphp
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-semibold text-rose-600 dark:text-rose-400 uppercase tracking-wider">Error &amp; Kritis</p>
                        <h3 class="text-2xl font-bold tracking-tight text-rose-600 dark:text-rose-400 mt-1">
                            {{ number_format($errorCount, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="p-2.5 bg-rose-50 dark:bg-rose-950/40 rounded-lg text-rose-600 dark:text-rose-400">
                        <i data-lucide="alert-octagon" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="mt-3 text-xs text-slate-500 dark:text-slate-400">
                    @if($errorCount > 0)
                        <span class="text-rose-600 dark:text-rose-400 font-bold">Perlu perhatian</span> segera
                    @else
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold">Tidak ada error</span> kritis
                    @endif
                </div>
            </div>

            <!-- Warnings -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider">Peringatan (Warning)</p>
                        <h3 class="text-2xl font-bold tracking-tight text-amber-600 dark:text-amber-400 mt-1">
                            {{ number_format($levelStats['warning'], 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="p-2.5 bg-amber-50 dark:bg-amber-950/40 rounded-lg text-amber-600 dark:text-amber-400">
                        <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="mt-3 text-xs text-slate-500 dark:text-slate-400">
                    Potensi kendala sistem
                </div>
            </div>

            <!-- Info & Debug -->
            @php
                $infoCount = $levelStats['info'] + $levelStats['debug'] + $levelStats['notice'];
            @endphp
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-semibold text-sky-600 dark:text-sky-400 uppercase tracking-wider">Informasi &amp; Debug</p>
                        <h3 class="text-2xl font-bold tracking-tight text-sky-600 dark:text-sky-400 mt-1">
                            {{ number_format($infoCount, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="p-2.5 bg-sky-50 dark:bg-sky-950/40 rounded-lg text-sky-600 dark:text-sky-400">
                        <i data-lucide="info" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="mt-3 text-xs text-slate-500 dark:text-slate-400">
                    Pembaruan: <span class="text-slate-700 dark:text-slate-300">{{ $selectedFile['modified_at'] ?? '-' }}</span>
                </div>
            </div>
        </section>

        <!-- FILTERS & SEARCH CONTROL -->
        <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-sm">
            <form method="GET" action="{{ route('system-logs.index') }}" class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
                
                <!-- Left: File Selector & Level Filter Pills -->
                <div class="flex flex-wrap items-center gap-3">
                    <!-- File Selector Dropdown -->
                    @if(count($files) > 1)
                        <div class="flex items-center gap-1.5">
                            <label for="log-file-select" class="text-xs font-semibold text-slate-500 dark:text-slate-400 shrink-0">
                                Berkas:
                            </label>
                            <select id="log-file-select" name="file" onchange="this.form.submit()"
                                class="text-xs bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-lg px-2.5 py-1.5 focus:ring-2 focus:ring-indigo-500 outline-none cursor-pointer">
                                @foreach($files as $f)
                                    <option value="{{ $f['name'] }}" {{ $selectedFileName === $f['name'] ? 'selected' : '' }}>
                                        {{ $f['name'] }} ({{ $f['size'] }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="file" value="{{ $selectedFileName }}">
                    @endif

                    <!-- Level Filter Pills -->
                    <div class="flex items-center flex-wrap gap-1 bg-slate-100 dark:bg-slate-800/60 p-1 rounded-lg border border-slate-200/60 dark:border-slate-700/60">
                        <a href="{{ route('system-logs.index', array_merge(request()->except('level', 'page'), ['level' => 'all'])) }}"
                            class="px-2.5 py-1 text-xs font-semibold rounded-md transition-colors {{ $filterLevel === 'all' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-50 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200' }}">
                            Semua
                        </a>
                        <a href="{{ route('system-logs.index', array_merge(request()->except('level', 'page'), ['level' => 'error_critical'])) }}"
                            class="px-2.5 py-1 text-xs font-semibold rounded-md transition-colors {{ $filterLevel === 'error_critical' ? 'bg-rose-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400' }}">
                            Error &amp; Kritis
                        </a>
                        <a href="{{ route('system-logs.index', array_merge(request()->except('level', 'page'), ['level' => 'warning'])) }}"
                            class="px-2.5 py-1 text-xs font-semibold rounded-md transition-colors {{ $filterLevel === 'warning' ? 'bg-amber-500 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-amber-600 dark:hover:text-amber-400' }}">
                            Warning
                        </a>
                        <a href="{{ route('system-logs.index', array_merge(request()->except('level', 'page'), ['level' => 'info'])) }}"
                            class="px-2.5 py-1 text-xs font-semibold rounded-md transition-colors {{ $filterLevel === 'info' ? 'bg-sky-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-sky-600 dark:hover:text-sky-400' }}">
                            Info
                        </a>
                        <a href="{{ route('system-logs.index', array_merge(request()->except('level', 'page'), ['level' => 'debug'])) }}"
                            class="px-2.5 py-1 text-xs font-semibold rounded-md transition-colors {{ $filterLevel === 'debug' ? 'bg-slate-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200' }}">
                            Debug
                        </a>
                    </div>
                </div>

                <!-- Right: Search Input -->
                <div class="relative w-full lg:w-72">
                    <input type="hidden" name="level" value="{{ $filterLevel }}">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </div>
                    <input type="text" name="search" value="{{ $searchKeyword }}" placeholder="Cari pesan error / stack trace..."
                        class="w-full text-xs pl-9 pr-8 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                    @if($searchKeyword !== '')
                        <a href="{{ route('system-logs.index', request()->except('search', 'page')) }}"
                            class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                        </a>
                    @endif
                </div>
            </form>
        </section>

        <!-- LOG ENTRIES LIST -->
        <section class="space-y-3">
            @if($paginatedEntries->count() > 0)
                @foreach($paginatedEntries as $index => $entry)
                    @php
                        $lvl = strtoupper($entry['level']);
                        $isError = in_array($lvl, ['ERROR', 'CRITICAL', 'EMERGENCY', 'ALERT']);
                        $isWarning = $lvl === 'WARNING';
                        $isInfo = $lvl === 'INFO' || $lvl === 'NOTICE';
                        $isDebug = $lvl === 'DEBUG';

                        if ($isError) {
                            $badgeClass = 'bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-900/60';
                            $borderAccent = 'border-l-4 border-l-rose-500';
                            $iconName = 'alert-octagon';
                        } elseif ($isWarning) {
                            $badgeClass = 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-900/60';
                            $borderAccent = 'border-l-4 border-l-amber-500';
                            $iconName = 'alert-triangle';
                        } elseif ($isInfo) {
                            $badgeClass = 'bg-sky-100 dark:bg-sky-950/60 text-sky-700 dark:text-sky-400 border-sky-200 dark:border-sky-900/60';
                            $borderAccent = 'border-l-4 border-l-sky-500';
                            $iconName = 'info';
                        } else {
                            $badgeClass = 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700';
                            $borderAccent = 'border-l-4 border-l-slate-400';
                            $iconName = 'terminal';
                        }
                    @endphp

                    <div x-data="{ open: false, copied: false }"
                         class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-sm {{ $borderAccent }} transition-all duration-200 hover:shadow-md">
                        
                        <!-- Entry Header -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                            <div class="flex items-center flex-wrap gap-2">
                                <!-- Level Badge -->
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded border {{ $badgeClass }}">
                                    <i data-lucide="{{ $iconName }}" class="w-3 h-3"></i>
                                    {{ $lvl }}
                                </span>

                                <!-- Environment Badge -->
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 uppercase tracking-wider">
                                    {{ $entry['environment'] }}
                                </span>

                                <!-- Timestamp -->
                                <span class="inline-flex items-center gap-1 text-xs text-slate-500 dark:text-slate-400 font-mono">
                                    <i data-lucide="clock" class="w-3 h-3"></i>
                                    {{ $entry['timestamp'] }}
                                </span>
                            </div>

                            <!-- Action Buttons: Copy & Toggle Stack Trace -->
                            <div class="flex items-center gap-1.5 self-end sm:self-auto">
                                <button type="button"
                                    @click="
                                        navigator.clipboard.writeText(@js($entry['raw']));
                                        copied = true;
                                        setTimeout(() => copied = false, 2000);
                                    "
                                    class="inline-flex items-center gap-1 px-2 py-1 text-[11px] font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800 rounded transition-colors cursor-pointer"
                                    title="Salin Pesan Error">
                                    <template x-if="!copied">
                                        <div class="flex items-center gap-1">
                                            <i data-lucide="copy" class="w-3 h-3"></i>
                                            <span>Salin</span>
                                        </div>
                                    </template>
                                    <template x-if="copied">
                                        <div class="flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-bold">
                                            <i data-lucide="check" class="w-3 h-3"></i>
                                            <span>Tersalin!</span>
                                        </div>
                                    </template>
                                </button>

                                @if(!empty($entry['stack_trace']))
                                    <button type="button" @click="open = !open"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 rounded transition-colors cursor-pointer">
                                        <span x-text="open ? 'Sembunyikan Trace' : 'Lihat Trace'"></span>
                                        <i data-lucide="chevron-down" class="w-3 h-3 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Main Error Message -->
                        <div class="text-xs font-mono text-slate-800 dark:text-slate-200 break-words leading-relaxed select-all">
                            {{ $entry['message'] }}
                        </div>

                        <!-- Collapsible Stack Trace -->
                        @if(!empty($entry['stack_trace']))
                            <div x-show="open" x-collapse x-cloak class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                                <div class="bg-slate-950 text-slate-200 rounded-lg p-3 text-[11px] font-mono overflow-x-auto max-h-96 border border-slate-800">
                                    <pre class="whitespace-pre-wrap leading-relaxed select-all">{{ $entry['stack_trace'] }}</pre>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach

                <!-- Pagination -->
                <div class="mt-6 flex items-center justify-between flex-wrap gap-4">
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Menampilkan <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $paginatedEntries->firstItem() ?? 0 }}</span> - 
                        <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $paginatedEntries->lastItem() ?? 0 }}</span> dari 
                        <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $paginatedEntries->total() }}</span> entri
                    </p>

                    <div class="p-1">
                        {{ $paginatedEntries->appends(request()->query())->links() }}
                    </div>
                </div>
            @else
                <!-- EMPTY STATE -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-12 text-center shadow-sm">
                    <div class="w-16 h-16 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto mb-4 border border-emerald-200 dark:border-emerald-800/60">
                        <i data-lucide="check-circle" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 mb-1">
                        @if($searchKeyword !== '' || $filterLevel !== 'all')
                            Tidak Ditemukan Log yang Sesuai
                        @else
                            Log Sistem Bersih
                        @endif
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">
                        @if($searchKeyword !== '' || $filterLevel !== 'all')
                            Tidak ada entri log dengan kata kunci atau filter level yang Anda pilih. Coba sesuaikan kata kunci pencarian.
                        @else
                            Berkas log saat ini kosong atau belum mencatat adanya error/kegagalan sistem.
                        @endif
                    </p>
                    @if($searchKeyword !== '' || $filterLevel !== 'all')
                        <div class="mt-4">
                            <a href="{{ route('system-logs.index', ['file' => $selectedFileName]) }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/30 rounded-lg hover:bg-indigo-100 transition-colors">
                                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                <span>Reset Filter</span>
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </section>

    </div>
</x-admin-layout>
