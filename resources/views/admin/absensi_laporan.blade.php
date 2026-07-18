<x-admin-layout>
    <div class="p-6 space-y-6">
        <!-- Header -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Laporan Rekap Absensi</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Analisis dan rekapitulasi data kehadiran guru dan karyawan.</p>
            </div>
            <div class="flex items-center gap-2">
                <button class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg shadow-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-all cursor-pointer">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                    Export Excel
                </button>
                <button class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-900 dark:bg-slate-50 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm hover:bg-slate-800 dark:hover:bg-slate-200 transition-all cursor-pointer">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    Cetak PDF
                </button>
            </div>
        </section>

        <!-- Filters -->
        <section class="animate-card bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Periode Tanggal</label>
                    <div class="relative w-full">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
                        </span>
                        <input type="text" placeholder="01 Jul 2026 - 31 Jul 2026" class="w-full h-9 pl-9 pr-4 text-xs font-medium bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-200 dark:focus:ring-slate-700 text-slate-800 dark:text-slate-200 transition-all shadow-inner">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Tipe Pegawai</label>
                    <select class="w-full h-9 px-3 text-xs font-medium bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-200 dark:focus:ring-slate-700 text-slate-800 dark:text-slate-200 transition-all shadow-sm">
                        <option>Semua Tipe</option>
                        <option>Guru</option>
                        <option>Staf & Karyawan</option>
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Status Kehadiran</label>
                    <select class="w-full h-9 px-3 text-xs font-medium bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-200 dark:focus:ring-slate-700 text-slate-800 dark:text-slate-200 transition-all shadow-sm">
                        <option>Semua Status</option>
                        <option>Hadir</option>
                        <option>Terlambat</option>
                        <option>Izin / Cuti</option>
                        <option>Alpha</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button class="w-full h-9 inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all cursor-pointer">
                        <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </section>

        <!-- Summary Cards -->
        <section class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-slate-950 p-5 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Rata-rata Hadir</p>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-slate-50 tracking-tight">96.8%</h3>
                    <p class="text-[11px] font-semibold text-emerald-500 mt-1 flex items-center gap-1"><i data-lucide="trending-up" class="w-3 h-3"></i> +2.4% dr bulan lalu</p>
                </div>
                <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl">
                    <i data-lucide="activity" class="w-6 h-6"></i>
                </div>
            </div>
            
            <div class="bg-white dark:bg-slate-950 p-5 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Total Terlambat</p>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-slate-50 tracking-tight">42</h3>
                    <p class="text-[11px] font-semibold text-amber-500 mt-1 flex items-center gap-1"><i data-lucide="trending-down" class="w-3 h-3"></i> -5 dr bulan lalu</p>
                </div>
                <div class="p-3 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl">
                    <i data-lucide="clock-alert" class="w-6 h-6"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-950 p-5 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Total Izin / Cuti</p>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-slate-50 tracking-tight">15</h3>
                    <p class="text-[11px] font-semibold text-blue-500 mt-1 flex items-center gap-1"><i data-lucide="minus" class="w-3 h-3"></i> stabil dr bulan lalu</p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl">
                    <i data-lucide="file-signature" class="w-6 h-6"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-950 p-5 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Total Alpha</p>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-slate-50 tracking-tight">3</h3>
                    <p class="text-[11px] font-semibold text-rose-500 mt-1 flex items-center gap-1"><i data-lucide="trending-up" class="w-3 h-3"></i> +1 dr bulan lalu</p>
                </div>
                <div class="p-3 bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-xl">
                    <i data-lucide="user-x" class="w-6 h-6"></i>
                </div>
            </div>
        </section>

        <!-- DETAILED SECTIONS: CHARTS & CALENDAR -->
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Graph Card (SVG) -->
            <div class="animate-card lg:col-span-2 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-6 relative flex flex-col justify-between overflow-hidden shadow-sm">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-50">Tren Kehadiran Pegawai</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Persentase kehadiran dalam 7 bulan terakhir</p>
                        </div>
                    </div>
                    <!-- Mini Graphic SVG Container -->
                    <div class="relative w-full h-44 mt-4 flex items-end">
                        <svg viewBox="0 0 500 150" class="w-full h-full chart-line overflow-visible">
                            <!-- Grids -->
                            <line x1="0" y1="30" x2="500" y2="30" stroke="currentColor" class="text-slate-100 dark:text-slate-900" stroke-width="1" />
                            <line x1="0" y1="75" x2="500" y2="75" stroke="currentColor" class="text-slate-100 dark:text-slate-900" stroke-width="1" />
                            <line x1="0" y1="120" x2="500" y2="120" stroke="currentColor" class="text-slate-100 dark:text-slate-900" stroke-width="1" />
                            
                            <!-- Area path -->
                            <path d="M 0,150 L 0,110 L 80,120 L 160,85 L 240,95 L 320,60 L 400,45 L 500,30 L 500,150 Z" 
                                  fill="url(#grad-area)" opacity="0.15"></path>
                            
                            <!-- Animated line path -->
                            <path d="M 0,110 L 80,120 L 160,85 L 240,95 L 320,60 L 400,45 L 500,30" 
                                  fill="none" stroke="currentColor" class="text-slate-800 dark:text-slate-100" stroke-width="2" stroke-linecap="round"></path>
                            
                            <!-- Gradients defs -->
                            <defs>
                                <linearGradient id="grad-area" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="currentColor" class="text-slate-800 dark:text-slate-100" />
                                    <stop offset="100%" stop-color="currentColor" class="text-slate-800 dark:text-slate-100" stop-opacity="0" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </div>
                
                <!-- Month labels -->
                <div class="flex justify-between text-xs text-slate-400 dark:text-slate-500 font-semibold uppercase px-1 mt-4">
                    <span>Jan</span>
                    <span>Feb</span>
                    <span>Mar</span>
                    <span>Apr</span>
                    <span>Mei</span>
                    <span>Jun</span>
                    <span>Jul</span>
                </div>
            </div>

            <!-- Calendar Card -->
            <div class="animate-card bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-6 flex flex-col justify-between shadow-sm">
                <div>
                    <!-- Calendar Header -->
                    <div class="flex items-center justify-between mb-6">
                        <button id="calendar-prev" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-900 rounded-lg text-slate-500 dark:text-slate-400 transition-colors cursor-pointer">
                            <i data-lucide="chevron-left" class="w-4 h-4"></i>
                        </button>
                        <h3 id="calendar-title" class="text-sm font-bold text-slate-800 dark:text-slate-100 tracking-wide">Juli 2026</h3>
                        <button id="calendar-next" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-900 rounded-lg text-slate-500 dark:text-slate-400 transition-colors cursor-pointer">
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <!-- Weekdays -->
                    <div class="grid grid-cols-7 gap-y-2 text-center text-xs font-semibold mb-3" style="display: grid; grid-template-columns: repeat(7, minmax(0, 1fr));">
                        <span class="text-rose-400 dark:text-rose-500">Sun</span>
                        <span class="text-slate-500 dark:text-slate-400">Mon</span>
                        <span class="text-slate-500 dark:text-slate-400">Tue</span>
                        <span class="text-slate-500 dark:text-slate-400">Wed</span>
                        <span class="text-slate-500 dark:text-slate-400">Thu</span>
                        <span class="text-slate-500 dark:text-slate-400">Fri</span>
                        <span class="text-rose-400 dark:text-rose-500">Sat</span>
                    </div>

                    <!-- Days Grid -->
                    <div id="calendar-days-grid" class="grid grid-cols-7 gap-y-2 text-center text-xs font-semibold" style="display: grid; grid-template-columns: repeat(7, minmax(0, 1fr));">
                        <!-- Populated by JS -->
                    </div>
                </div>

                <!-- Footer with current date info -->
                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-400 dark:text-slate-500">
                    <span id="calendar-today-btn" class="hover:text-slate-850 dark:hover:text-slate-200 cursor-pointer font-semibold transition-colors">Hari Ini</span>
                    <span id="calendar-selected-info" class="font-bold text-slate-800 dark:text-slate-200"></span>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const monthNames = [
                        "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                        "Juli", "Agustus", "September", "Oktober", "November", "Desember"
                    ];
                    const dayNames = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];

                    // Current real local date: 14 July 2026
                    const today = new Date(2026, 6, 14); // July is 6 (0-indexed)
                    let currentYear = 2026;
                    let currentMonth = 6; // 0-indexed (July)

                    let selectedDate = new Date(today);

                    const titleEl = document.getElementById('calendar-title');
                    const gridEl = document.getElementById('calendar-days-grid');
                    const infoEl = document.getElementById('calendar-selected-info');
                    const todayBtn = document.getElementById('calendar-today-btn');

                    // Mock status patterns matching user's reference image
                    const mockStatus = {
                        1: 'green',
                        15: 'purple',
                        20: 'red',
                        29: 'green',
                        30: 'outline'
                    };

                    function formatDateString(date) {
                        const dayName = dayNames[date.getDay()];
                        const day = date.getDate();
                        const month = monthNames[date.getMonth()];
                        return `${dayName}, ${day} ${month}`;
                    }

                    function renderCalendar() {
                        gridEl.innerHTML = '';
                        titleEl.textContent = `${monthNames[currentMonth]} ${currentYear}`;
                        
                        // First day of current displayed month
                        const firstDayIndex = new Date(currentYear, currentMonth, 1).getDay();
                        // Last date of current displayed month
                        const lastDate = new Date(currentYear, currentMonth + 1, 0).getDate();

                        // Fill in previous month empty spaces
                        for (let i = 0; i < firstDayIndex; i++) {
                            const emptyCell = document.createElement('div');
                            emptyCell.className = 'py-2';
                            gridEl.appendChild(emptyCell);
                        }

                        // Fill in current month days
                        for (let d = 1; d <= lastDate; d++) {
                            const dayButton = document.createElement('button');
                            dayButton.textContent = d;
                            
                            const thisDate = new Date(currentYear, currentMonth, d);
                            const dayOfWeek = thisDate.getDay();
                            
                            // Base day style
                            let btnClasses = 'w-9 h-9 mx-auto rounded-xl flex items-center justify-center transition-all duration-150 cursor-pointer focus:outline-none hover:bg-slate-100 dark:hover:bg-slate-800 ';
                            
                            // Check Sunday for red text color
                            if (dayOfWeek === 0) {
                                btnClasses += 'text-rose-500 dark:text-rose-450 font-bold ';
                            } else {
                                btnClasses += 'text-slate-850 dark:text-slate-200 ';
                            }

                            // Match the statuses/highlights from screenshot
                            const status = mockStatus[d];
                            if (status === 'green') {
                                btnClasses += 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 ';
                            } else if (status === 'purple') {
                                btnClasses += 'bg-indigo-100 dark:bg-indigo-950/60 text-indigo-850 dark:text-indigo-300 ';
                            } else if (status === 'red') {
                                btnClasses += 'bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-350 ';
                            } else if (status === 'outline') {
                                btnClasses += 'border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-850 dark:text-slate-200 ';
                            }

                            // If selected, apply special focus/active border or overlay if not already outlined
                            const isSelected = thisDate.toDateString() === selectedDate.toDateString();
                            if (isSelected && status !== 'outline') {
                                btnClasses += 'ring-2 ring-slate-800 dark:ring-slate-200 ';
                            }

                            dayButton.className = btnClasses.trim();
                            dayButton.style.width = '36px';
                            dayButton.style.height = '36px';
                            dayButton.style.aspectRatio = '1/1';

                            dayButton.addEventListener('click', () => {
                                selectedDate = thisDate;
                                updateFooterInfo();
                                renderCalendar();
                            });

                            gridEl.appendChild(dayButton);
                        }

                        updateFooterInfo();
                    }

                    function updateFooterInfo() {
                        infoEl.innerHTML = `<span class="font-bold text-slate-800 dark:text-slate-200">${formatDateString(selectedDate)}</span>`;
                    }

                    document.getElementById('calendar-prev').addEventListener('click', () => {
                        currentMonth--;
                        if (currentMonth < 0) {
                            currentMonth = 11;
                            currentYear--;
                        }
                        renderCalendar();
                    });

                    document.getElementById('calendar-next').addEventListener('click', () => {
                        currentMonth++;
                        if (currentMonth > 11) {
                            currentMonth = 0;
                            currentYear++;
                        }
                        renderCalendar();
                    });

                    todayBtn.addEventListener('click', () => {
                        selectedDate = new Date(today);
                        currentYear = today.getFullYear();
                        currentMonth = today.getMonth();
                        renderCalendar();
                    });

                    renderCalendar();
                });
            </script>
        </section>

        <!-- MAIN TABLE -->
        <section class="animate-card bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden transition-all w-full p-6 space-y-6">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full">
                <div class="space-y-1 text-left">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-slate-50">Rincian Kehadiran Pegawai</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Detail rekap absensi per pegawai berdasarkan periode yang difilter.</p>
                </div>
                <!-- Search -->
                <div class="relative w-full md:w-64">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 dark:text-slate-550"></i>
                    </span>
                    <input type="text" placeholder="Cari Nama Pegawai..." class="w-full h-9 pl-9 pr-4 text-xs font-medium bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-200 dark:focus:ring-slate-700 text-slate-800 dark:text-slate-200 transition-all shadow-inner">
                </div>
            </div>

            <div class="overflow-x-auto border border-slate-100 dark:border-slate-900 rounded-xl">
                <table class="w-full text-xs border-collapse text-left">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30">
                            <th class="px-6 py-4 font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-16">No</th>
                            <th class="px-6 py-4 font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider">Nama Pegawai</th>
                            <th class="px-6 py-4 font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider">Jabatan</th>
                            <th class="px-6 py-4 font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider text-center">Hadir</th>
                            <th class="px-6 py-4 font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider text-center">Terlambat</th>
                            <th class="px-6 py-4 font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider text-center">Izin / Cuti</th>
                            <th class="px-6 py-4 font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider text-center">Alpha</th>
                            <th class="px-6 py-4 font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider text-right">Persentase Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        <!-- Example Row 1 -->
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/50 transition-colors">
                            <td class="px-6 py-4 text-slate-700 dark:text-slate-300">1</td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 dark:text-slate-100">Ahmad Rizali, S.Pd</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">NIP: 1982390123901</div>
                            </td>
                            <td class="px-6 py-4 text-slate-700 dark:text-slate-300">Guru Matematika</td>
                            <td class="px-6 py-4 text-center text-emerald-600 dark:text-emerald-400 font-bold text-sm">24</td>
                            <td class="px-6 py-4 text-center text-amber-600 dark:text-amber-400 font-bold text-sm">1</td>
                            <td class="px-6 py-4 text-center text-slate-500 dark:text-slate-400 font-bold text-sm">0</td>
                            <td class="px-6 py-4 text-center text-rose-500 dark:text-rose-400 font-bold text-sm">0</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <span class="font-bold text-slate-900 dark:text-slate-50">98%</span>
                                    <div class="w-16 h-1.5 bg-slate-200 dark:bg-slate-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-emerald-500 rounded-full" style="width: 98%"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <!-- Example Row 2 -->
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/50 transition-colors">
                            <td class="px-6 py-4 text-slate-700 dark:text-slate-300">2</td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 dark:text-slate-100">Siti Aminah, M.Pd</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">NIP: 1984281048102</div>
                            </td>
                            <td class="px-6 py-4 text-slate-700 dark:text-slate-300">Guru B. Inggris</td>
                            <td class="px-6 py-4 text-center text-emerald-600 dark:text-emerald-400 font-bold text-sm">20</td>
                            <td class="px-6 py-4 text-center text-amber-600 dark:text-amber-400 font-bold text-sm">0</td>
                            <td class="px-6 py-4 text-center text-slate-500 dark:text-slate-400 font-bold text-sm">5</td>
                            <td class="px-6 py-4 text-center text-rose-500 dark:text-rose-400 font-bold text-sm">0</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <span class="font-bold text-slate-900 dark:text-slate-50">80%</span>
                                    <div class="w-16 h-1.5 bg-slate-200 dark:bg-slate-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-amber-500 rounded-full" style="width: 80%"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <!-- Example Row 3 -->
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/50 transition-colors">
                            <td class="px-6 py-4 text-slate-700 dark:text-slate-300">3</td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 dark:text-slate-100">Budi Santoso</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">NIK: 20230193021</div>
                            </td>
                            <td class="px-6 py-4 text-slate-700 dark:text-slate-300">Staf TU</td>
                            <td class="px-6 py-4 text-center text-emerald-600 dark:text-emerald-400 font-bold text-sm">23</td>
                            <td class="px-6 py-4 text-center text-amber-600 dark:text-amber-400 font-bold text-sm">2</td>
                            <td class="px-6 py-4 text-center text-slate-500 dark:text-slate-400 font-bold text-sm">0</td>
                            <td class="px-6 py-4 text-center text-rose-500 dark:text-rose-400 font-bold text-sm">0</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <span class="font-bold text-slate-900 dark:text-slate-50">92%</span>
                                    <div class="w-16 h-1.5 bg-slate-200 dark:bg-slate-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-emerald-500 rounded-full" style="width: 92%"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-slate-800">
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Menampilkan 1-3 dari 86 data</span>
                <div class="flex items-center gap-1">
                    <button class="px-3 py-1.5 border border-slate-200 dark:border-slate-700 rounded text-xs font-medium text-slate-400 cursor-not-allowed">Sebelumnya</button>
                    <button class="px-3 py-1.5 bg-slate-900 dark:bg-slate-50 text-white dark:text-slate-900 rounded text-xs font-bold">1</button>
                    <button class="px-3 py-1.5 hover:bg-slate-50 dark:hover:bg-slate-800 rounded text-xs font-medium text-slate-700 dark:text-slate-300 transition-colors">2</button>
                    <button class="px-3 py-1.5 hover:bg-slate-50 dark:hover:bg-slate-800 rounded text-xs font-medium text-slate-700 dark:text-slate-300 transition-colors">3</button>
                    <button class="px-3 py-1.5 border border-slate-200 dark:border-slate-700 rounded text-xs font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">Selanjutnya</button>
                </div>
            </div>

        </section>
    </div>
</x-admin-layout>
