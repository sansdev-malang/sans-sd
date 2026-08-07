<x-admin-layout>
    <div class="p-6 space-y-6">

        <!-- GREETING / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 font-nasalization">Dashboard</h2>
                @if($isAdmin)
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Selamat datang kembali! Aktivitas sekolah terpantau kondusif.</p>
                @else
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Selamat datang kembali, {{ auth()->user()->name }}! Pantau performa kehadiran harian Anda disini.</p>
                @endif
            </div>
        </section>

        <!-- STAT CARDS GRID -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @if($isAdmin)
                <!-- Admin Card 1: Total Siswa -->
                <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Siswa Aktif</p>
                            <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                                <span class="stat-counter" data-target="1248">1248</span>
                            </h3>
                        </div>
                        <div class="p-2 bg-slate-50 dark:bg-slate-900 rounded-lg">
                            <i data-lucide="users" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold">+4.5%</span> dari bulan lalu
                    </div>
                </div>

                <!-- Admin Card 2: Guru & Staf -->
                <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Guru & Staf</p>
                            <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                                @if(isset($employeeCount))
                                    <span class="stat-counter" data-target="{{ $employeeCount }}">{{ $employeeCount }}</span>
                                @else
                                    <span>-</span>
                                @endif
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

                <!-- Admin Card 3: Total Rombel / Kelas -->
                <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Rombel / Kelas</p>
                            <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                                <span class="stat-counter" data-target="36">36</span>
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

                <!-- Admin Card 4: Presensi Hari Ini -->
                <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Presensi Hari Ini</p>
                            <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                                <span class="stat-counter" data-target="96">96</span>%
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
            @else
                <!-- Pegawai Card 1: Kehadiran Bulan Ini -->
                <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kehadiran Bulan Ini</p>
                            <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                                <span>{{ $myReport['total_present'] ?? 0 }}</span> Hari
                            </h3>
                        </div>
                        <div class="p-2 bg-slate-50 dark:bg-slate-900 rounded-lg">
                            <i data-lucide="calendar" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                        Siklus aktif terhitung hadir
                    </div>
                </div>

                <!-- Pegawai Card 2: Menit Terlambat -->
                <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Keterlambatan</p>
                            <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                                <span>{{ $myReport['total_late_minutes'] ?? 0 }}</span> Menit
                            </h3>
                        </div>
                        <div class="p-2 bg-slate-50 dark:bg-slate-900 rounded-lg">
                            <i data-lucide="hourglass" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                        @if(($myReport['total_late_minutes'] ?? 0) > 0)
                            <span class="text-amber-500 font-bold">Terlambat</span> pada siklus aktif
                        @else
                            <span class="text-emerald-600 dark:text-emerald-400 font-bold">Tepat Waktu</span>
                        @endif
                    </div>
                </div>

                <!-- Pegawai Card 3: Total Izin & Cuti -->
                <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Izin & Cuti</p>
                            <h3 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                                <span>{{ $totalLeavesThisYear }}</span> Hari
                            </h3>
                        </div>
                        <div class="p-2 bg-slate-50 dark:bg-slate-900 rounded-lg">
                            <i data-lucide="calendar-days" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                        Akumulasi persetujuan tahun ini
                    </div>
                </div>

                <!-- Pegawai Card 4: Estimasi Bonus -->
                <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Estimasi Bonus</p>
                            <h3 class="text-2xl font-bold tracking-tight text-emerald-600 dark:text-emerald-400 mt-1">
                                Rp {{ number_format($myReport['bonus_nominal'] ?? 0, 0, ',', '.') }}
                            </h3>
                        </div>
                        <div class="p-2 bg-slate-50 dark:bg-slate-900 rounded-lg">
                            <i data-lucide="award" class="w-4 h-4 text-slate-500 dark:text-slate-400"></i>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                        Akumulasi bonus bulan ini
                    </div>
                </div>
            @endif
        </section>

        <!-- DETAILED SECTIONS: CHARTS & ACTIVITIES -->
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Graph Card (SVG) -->
            <div class="animate-card lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 relative flex flex-col justify-between overflow-hidden shadow-sm transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            @if($isAdmin)
                                <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50">Ikhtisar Kehadiran Bulanan</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Tingkat kehadiran siswa pada 7 bulan terakhir</p>
                            @else
                                <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50 font-nasalization">Riwayat Absensi Harian</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Tren waktu kedatangan (jam masuk) Anda pada bulan ini</p>
                            @endif
                        </div>
                    </div>
                    <!-- Scrollable wrapper for chart and labels -->
                    <div class="overflow-x-auto hide-scrollbar pb-2 mt-4 w-full">
                        @php
                            $minCardWidth = 850; // typical width to fill desktop card
                            $minSpacing = 60;
                            $svgWidth = 500;
                            
                            if (!$isAdmin && count($chartPoints) > 0) {
                                $count = count($chartPoints);
                                $requiredWidth = ($count - 1) * $minSpacing + 60; // 30px padding each side
                                $svgWidth = max($minCardWidth, $requiredWidth);
                                $spacing = ($svgWidth - 60) / max(1, $count - 1);
                                
                                $areaD = "";
                                $lineD = "";
                                
                                foreach($chartPoints as $i => &$pt) {
                                    $pt['x'] = $i * $spacing + 30;
                                    $prefix = $i == 0 ? "M" : "L";
                                    $lineD .= "{$prefix} {$pt['x']},{$pt['y']} ";
                                    $areaD .= "L {$pt['x']},{$pt['y']} ";
                                }
                                
                                $firstX = $chartPoints[0]['x'];
                                $lastX = end($chartPoints)['x'];
                                $areaD = "M {$firstX},150 " . $areaD . " L {$lastX},150 Z";
                            }
                        @endphp
                        
                        <div style="min-width: {{ $svgWidth }}px; width: {{ $svgWidth }}px;">
                            <!-- Mini Graphic SVG Container -->
                            <div class="relative w-full h-44 flex items-end">
                                <svg viewBox="0 0 {{ $svgWidth }} 150" class="w-full h-full chart-line overflow-visible">
                                    <!-- Grids -->
                                    <line x1="0" y1="30" x2="{{ $svgWidth }}" y2="30" stroke="currentColor" class="text-slate-100 dark:text-slate-900" stroke-width="1" />
                                    <line x1="0" y1="75" x2="{{ $svgWidth }}" y2="75" stroke="currentColor" class="text-slate-100 dark:text-slate-900" stroke-width="1" />
                                    <line x1="0" y1="120" x2="{{ $svgWidth }}" y2="120" stroke="currentColor" class="text-slate-100 dark:text-slate-900" stroke-width="1" />
                                    
                                    @if($isAdmin)
                                        <!-- Area path -->
                                        <path d="M 0,150 L 0,110 L 80,120 L 160,85 L 240,95 L 320,60 L 400,45 L 500,30 L 500,150 Z" 
                                              fill="url(#grad-area)" opacity="0.15"></path>
                                        
                                        <!-- Animated line path -->
                                        <path d="M 0,110 L 80,120 L 160,85 L 240,95 L 320,60 L 400,45 L 500,30" 
                                              fill="none" stroke="currentColor" class="text-slate-800 dark:text-slate-100" stroke-width="2" stroke-linecap="round"></path>
                                    @else
                                        <!-- Dynamic Area path -->
                                        @if(count($chartPoints) > 0)
                                        <path d="{{ $areaD }}" fill="url(#grad-area)" opacity="0.15"></path>
                                        
                                        <!-- Dynamic line path -->
                                        <path d="{{ $lineD }}" fill="none" stroke="currentColor" class="text-indigo-600 dark:text-indigo-400" stroke-width="2.5" stroke-linecap="round"></path>
                                        
                                        <!-- Circles & Text Labels on Points -->
                                        @foreach($chartPoints as $pt)
                                            <!-- Point Circle -->
                                            <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="3.5" class="fill-indigo-600 dark:fill-indigo-400 stroke-white dark:stroke-slate-900" stroke-width="1" />
                                            
                                            <!-- Time Text -->
                                            <text x="{{ $pt['x'] }}" y="{{ $pt['y'] - 8 }}" text-anchor="middle" class="text-[8px] sm:text-[9px] font-bold fill-slate-600 dark:fill-slate-300">
                                                {{ $pt['time'] }}
                                            </text>
                                        @endforeach
                                        @endif
                                    @endif
                                    
                                    <!-- Gradients defs -->
                                    <defs>
                                        <linearGradient id="grad-area" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0%" stop-color="currentColor" class="text-indigo-600 dark:text-indigo-400" />
                                            <stop offset="100%" stop-color="currentColor" class="text-indigo-600 dark:text-indigo-400" stop-opacity="0" />
                                        </linearGradient>
                                    </defs>
                                </svg>
                            </div>
                            
                            <!-- Chart Labels -->
                            <div class="flex justify-between text-xs text-slate-400 dark:text-slate-500 font-semibold uppercase mt-4 w-full px-[15px]">
                                @if($isAdmin)
                                    <span class="w-[30px] text-center">Jan</span>
                                    <span class="w-[30px] text-center">Feb</span>
                                    <span class="w-[30px] text-center">Mar</span>
                                    <span class="w-[30px] text-center">Apr</span>
                                    <span class="w-[30px] text-center">Mei</span>
                                    <span class="w-[30px] text-center">Jun</span>
                                    <span class="w-[30px] text-center">Jul</span>
                                @else
                                    @foreach($chartPoints as $pt)
                                        <span class="w-[30px] text-center">{{ $pt['short_date'] }}</span>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                <!-- Announcements / Information System -->
            <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 flex flex-col justify-between shadow-sm transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50">Pengumuman Sekolah</h3>
                        <a href="{{ route('announcements.index') }}" class="text-xs text-slate-500 dark:text-slate-400 font-semibold hover:underline">Lihat Semua</a>
                    </div>

                    <!-- List of updates -->
                    <div class="space-y-3.5">
                        @forelse($latestAnnouncements ?? collect() as $announcement)
                            <div class="flex gap-2.5 items-start">
                                <div class="w-1.5 h-1.5 rounded-full {{ $announcement->category == 'penting' ? 'bg-red-500' : 'bg-slate-400 dark:bg-slate-500' }} mt-1.5 shrink-0"></div>
                                <div class="flex-1">
                                    <h4 class="text-xs font-semibold {{ $announcement->category == 'penting' ? 'text-red-600 dark:text-red-400' : 'text-slate-900 dark:text-slate-50' }}">
                                        <a href="{{ route('announcements.show', $announcement) }}" class="hover:underline">{{ $announcement->title }}</a>
                                    </h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-2">{{ Str::limit(strip_tags($announcement->content), 100) }}</p>
                                    @if($announcement->attachment)
                                        <a href="{{ Storage::url($announcement->attachment) }}" target="_blank" class="text-[10px] text-blue-500 hover:underline mt-1 inline-block"><i data-lucide="paperclip" class="w-3 h-3 inline"></i> Lampiran</a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-xs text-slate-500 text-center py-4">Belum ada pengumuman terbaru.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Info tag footer -->
                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-400">
                    <span>Diperbarui secara real-time</span>
                </div>
            </div>
        </section>

        <!-- QUICK ACTIONS & ACTIVITY FEED -->
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Quick Actions Grid -->
            <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50 mb-4">Aksi Cepat</h3>
                <div class="grid grid-cols-2 gap-2">
                    @if($isAdmin)
                        <button onclick="window.location='{{ route('coming-soon') }}'" class="flex flex-col items-center justify-center p-3 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-900 rounded-lg group transition-all duration-100 cursor-pointer">
                            <i data-lucide="user-plus" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover:scale-105 transition-transform"></i>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300 mt-1.5">Tambah Siswa</span>
                        </button>
                        <button onclick="window.location='{{ route('coming-soon') }}'" class="flex flex-col items-center justify-center p-3 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-900 rounded-lg group transition-all duration-100 cursor-pointer">
                            <i data-lucide="clipboard-list" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover:scale-105 transition-transform"></i>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300 mt-1.5">Input Nilai</span>
                        </button>
                        <button onclick="window.location='{{ route('coming-soon') }}'" class="flex flex-col items-center justify-center p-3 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-900 rounded-lg group transition-all duration-100 cursor-pointer">
                            <i data-lucide="send" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover:scale-105 transition-transform"></i>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300 mt-1.5">Kirim Pesan</span>
                        </button>
                        <button onclick="window.location='{{ route('coming-soon') }}'" class="flex flex-col items-center justify-center p-3 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-900 rounded-lg group transition-all duration-100 cursor-pointer">
                            <i data-lucide="receipt" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover:scale-105 transition-transform"></i>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300 mt-1.5">Cek SPP</span>
                        </button>
                    @else
                        <button onclick="window.location='{{ route('my-leaves.index') }}'" class="flex flex-col items-center justify-center p-3 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-900 rounded-lg group transition-all duration-100 cursor-pointer">
                            <i data-lucide="file-text" class="w-4 h-4 text-indigo-600 dark:text-indigo-400 group-hover:scale-105 transition-transform"></i>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300 mt-1.5">Ajukan Cuti/Izin</span>
                        </button>
                        <button onclick="window.location='{{ route('coming-soon') }}'" class="flex flex-col items-center justify-center p-3 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-900 rounded-lg group transition-all duration-100 cursor-pointer relative">
                            <i data-lucide="clipboard-list" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover:scale-105 transition-transform"></i>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300 mt-1.5 flex items-center gap-1">Input Nilai Siswa <span class="text-[8px] font-bold bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 px-1 py-0.2 rounded uppercase">Dev</span></span>
                        </button>
                        <button onclick="window.location='{{ route('coming-soon') }}'" class="flex flex-col items-center justify-center p-3 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-900 rounded-lg group transition-all duration-100 cursor-pointer relative">
                            <i data-lucide="calendar" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover:scale-105 transition-transform"></i>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300 mt-1.5 flex items-center gap-1">Lihat Jadwal <span class="text-[8px] font-bold bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 px-1 py-0.2 rounded uppercase">Dev</span></span>
                        </button>
                        <button onclick="window.location='{{ route('payslips.index') }}'" class="flex flex-col items-center justify-center p-3 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-900 rounded-lg group transition-all duration-100 cursor-pointer">
                            <i data-lucide="receipt" class="w-4 h-4 text-slate-600 dark:text-slate-400 group-hover:scale-105 transition-transform"></i>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300 mt-1.5">Lihat Slip Gaji</span>
                        </button>
                    @endif
                </div>
                

            </div>

            <!-- Recent Activity Logs -->
            <div class="animate-card lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm transition-all duration-300 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700">
                @if($isAdmin)
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50 mb-4">Log Aktivitas Terbaru</h3>
                    <div class="space-y-3.5">
                        <div class="flex items-center justify-between py-1 border-b border-slate-50 dark:border-slate-900/60 pb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-lg bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-600 dark:text-slate-400">
                                    <i data-lucide="key" class="w-3.5 h-3.5"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-slate-800 dark:text-slate-200">Login Wali Kelas XI-IPA</p>
                                    <p class="text-xs text-slate-500">Guru: Drs. Eko Prasetyo</p>
                                </div>
                            </div>
                            <span class="text-xs text-slate-400">10 mnt yang lalu</span>
                        </div>
                        <div class="flex items-center justify-between py-1 border-b border-slate-50 dark:border-slate-900/60 pb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-lg bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-600 dark:text-slate-400">
                                    <i data-lucide="file-plus" class="w-3.5 h-3.5"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-slate-800 dark:text-slate-200">Unggah Materi Fisika Kuantum</p>
                                    <p class="text-xs text-slate-500">Kelas: XII-IPA</p>
                                </div>
                            </div>
                            <span class="text-xs text-slate-400">24 mnt yang lalu</span>
                        </div>
                        <div class="flex items-center justify-between py-1">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-lg bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-600 dark:text-slate-400">
                                    <i data-lucide="user-check" class="w-3.5 h-3.5"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-slate-800 dark:text-slate-200">Verifikasi Berkas Pendaftaran</p>
                                    <p class="text-xs text-slate-500">Gelombang 2 SANS Malang</p>
                                </div>
                            </div>
                            <span class="text-xs text-slate-400">45 mnt yang lalu</span>
                        </div>
                    </div>
                @else
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50 mb-4 font-nasalization">Aktivitas Saya (Pengajuan Terakhir)</h3>
                    <div class="space-y-3.5">
                        @forelse($myRecentLeaves as $leave)
                            <div class="flex items-center justify-between py-1 border-b border-slate-50 dark:border-slate-900/60 pb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-7 h-7 rounded-lg bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-600 dark:text-slate-400">
                                        <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-slate-800 dark:text-slate-200">Pengajuan Izin / Cuti ({{ $leave->type }})</p>
                                        <p class="text-xs text-slate-500">Status: 
                                            <span class="font-bold {{ $leave->status == 'Approved' ? 'text-emerald-600 dark:text-emerald-400' : ($leave->status == 'Rejected' ? 'text-rose-600 dark:text-rose-400' : 'text-amber-500') }}">
                                                {{ $leave->status }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <span class="text-xs text-slate-400">{{ $leave->created_at->diffForHumans() }}</span>
                            </div>
                        @empty
                            <div class="text-xs text-slate-500 text-center py-4">Belum ada riwayat aktivitas pengajuan cuti/izin.</div>
                        @endforelse
                    </div>
                @endif
            </div>
        </section>

    </div>
</x-admin-layout>
