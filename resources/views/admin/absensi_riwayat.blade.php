<x-admin-layout>
    <div class="p-6 space-y-6">

        <!-- GREETING / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Daftar Riwayat Absensi</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Kelola dan pantau data riwayat absensi guru SANS Malang.</p>
            </div>
        </section>

        <!-- SECTION 2: SEARCH & FILTERS -->
        <section
            class="animate-card bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-sm w-full">
            <div class="flex flex-col md:flex-row gap-4 items-stretch md:items-center justify-between">
                <!-- Search Box -->
                <div class="relative w-full md:max-w-md">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 dark:text-slate-500"></i>
                    </span>
                    <input type="text" id="table-search" placeholder="Cari berdasarkan Nama, NIS, atau NISN..."
                        style="padding-left: 2.25rem;"
                        class="w-full h-9 pr-4 text-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 focus:border-slate-400 dark:focus:border-slate-600 text-slate-900 dark:text-slate-50 placeholder-slate-400 dark:placeholder-slate-500 transition-all shadow-inner">
                </div>

                <!-- Filter Select Toolbar -->
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <!-- Filter Kelas -->
                    <select id="filter-kelas"
                        class="h-9 px-2 flex-1 sm:flex-initial sm:w-32 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 cursor-pointer transition-all shadow-sm">
                        <option value="">Semua Kelas</option>
                        <option value="10">Kelas 10</option>
                        <option value="11">Kelas 11</option>
                        <option value="12">Kelas 12</option>
                    </select>

                    <!-- Filter Rombel -->
                    <select id="filter-rombel"
                        class="h-9 px-2 flex-1 sm:flex-initial sm:w-32 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 cursor-pointer transition-all shadow-sm">
                        <option value="">Semua Rombel</option>
                        <option value="X-A">X-A</option>
                        <option value="XI-IPA">XI-IPA</option>
                        <option value="XII-IPS">XII-IPS</option>
                    </select>

                    <!-- Filter Status -->
                    <select id="filter-status"
                        class="h-9 px-2 flex-1 sm:flex-initial sm:w-32 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 cursor-pointer transition-all shadow-sm">
                        <option value="">Semua Status</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Cuti">Cuti</option>
                        <option value="Alumni">Alumni</option>
                    </select>
                </div>
            </div>
        </section>

        <!-- SECTION 4: TABLE (PREMIUM DESIGN) -->
        <!-- Date Range Navigator -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-3">
            <!-- Date Range Picker -->
            <div class="flex items-center gap-2 flex-wrap">
                <div class="flex items-center gap-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-1.5 shadow-sm">
                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i>
                    <input type="date" id="date-start" value="2026-07-27"
                        class="text-xs font-semibold text-slate-700 dark:text-slate-300 bg-transparent border-none outline-none cursor-pointer w-[120px]">
                </div>
                <span class="text-slate-400 dark:text-slate-500 text-xs font-medium">—</span>
                <div class="flex items-center gap-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-1.5 shadow-sm">
                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i>
                    <input type="date" id="date-end" value="2026-08-26"
                        class="text-xs font-semibold text-slate-700 dark:text-slate-300 bg-transparent border-none outline-none cursor-pointer w-[120px]">
                </div>
                <button id="btn-apply" class="inline-flex items-center gap-1.5 h-8 px-3 rounded-lg bg-slate-900 dark:bg-slate-50 text-white dark:text-slate-900 text-xs font-semibold shadow-sm hover:bg-slate-700 dark:hover:bg-slate-200 transition-all cursor-pointer">
                    <i data-lucide="refresh-cw" class="w-3 h-3"></i>
                    Terapkan
                </button>
                <!-- Error message -->
                <span id="date-error" class="text-xs text-red-500 hidden"></span>
            </div>

            <div class="flex items-center gap-2">
                <!-- Quick presets -->
                <div class="flex items-center gap-1">
                    <button data-preset="7"  class="preset-btn h-7 px-2.5 rounded-md border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all cursor-pointer">7 Hari</button>
                    <button data-preset="14" class="preset-btn h-7 px-2.5 rounded-md border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all cursor-pointer">14 Hari</button>
                    <button data-preset="30" class="preset-btn h-7 px-2.5 rounded-md border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all cursor-pointer">30 Hari</button>
                    <button data-preset="month" class="preset-btn h-7 px-2.5 rounded-md border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all cursor-pointer">Bulan Ini</button>
                </div>
                <!-- Legend -->
                <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 pl-2 border-l border-slate-200 dark:border-slate-700">
                    <span class="flex items-center gap-1"><span class="inline-block w-2 h-2 rounded-sm bg-emerald-100 dark:bg-emerald-900/40 border border-emerald-300 dark:border-emerald-700"></span>Tepat Waktu</span>
                    <span class="flex items-center gap-1"><span class="inline-block w-2 h-2 rounded-sm bg-amber-100 dark:bg-amber-900/40 border border-amber-300 dark:border-amber-700"></span>Terlambat</span>
                    <span class="flex items-center gap-1"><span class="inline-block w-2 h-2 rounded-sm bg-red-100 dark:bg-red-900/40 border border-red-300 dark:border-red-700"></span>Tidak Hadir</span>
                    <span class="flex items-center gap-1"><span class="inline-block w-2 h-2 rounded-sm bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-600"></span>Libur</span>
                </div>
            </div>
        </div>

        <section class="animate-card bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden transition-all w-full">
            <div class="overflow-x-auto">
                <table class="w-full text-xs border-collapse" id="absensi-calendar-table">
                    <thead id="absensi-thead">
                        <!-- Header rendered dynamically by JS -->
                    </thead>
                    <tbody id="absensi-tbody" class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        <!-- Rows rendered by JS -->
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800/80 bg-slate-50/20 dark:bg-slate-900/10 flex items-center justify-between gap-4">
                <span class="text-xs text-slate-500 dark:text-slate-400">
                    Menampilkan <span class="font-semibold text-slate-700 dark:text-slate-300">1-10</span> dari <span class="font-semibold text-slate-700 dark:text-slate-300">86</span> guru
                </span>
                <div class="flex items-center gap-1.5">
                    <button style="width:36px;height:36px;border-radius:8px;" class="inline-flex items-center justify-center text-xs font-semibold text-slate-550 dark:text-slate-400 border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-955 hover:bg-slate-50 dark:hover:bg-slate-900 transition-all disabled:opacity-50 cursor-pointer shadow-sm" disabled>
                        <i data-lucide="chevron-left" class="w-4 h-4"></i>
                    </button>
                    <button style="width:36px;height:36px;border-radius:8px;" class="inline-flex items-center justify-center text-xs font-bold text-white dark:text-slate-900 bg-slate-900 dark:bg-slate-50 shadow-sm">1</button>
                    <button style="width:36px;height:36px;border-radius:8px;" class="inline-flex items-center justify-center text-xs font-semibold text-slate-700 dark:text-slate-355 border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-955 hover:bg-slate-50 dark:hover:bg-slate-900 transition-all cursor-pointer shadow-sm">2</button>
                    <button style="width:36px;height:36px;border-radius:8px;" class="inline-flex items-center justify-center text-xs font-semibold text-slate-700 dark:text-slate-355 border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-955 hover:bg-slate-50 dark:hover:bg-slate-900 transition-all cursor-pointer shadow-sm">3</button>
                    <button style="width:36px;height:36px;border-radius:8px;" class="inline-flex items-center justify-center text-xs font-semibold text-slate-550 dark:text-slate-400 border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-955 hover:bg-slate-50 dark:hover:bg-slate-900 transition-all cursor-pointer shadow-sm">
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </section>

        <style>
            .attendance-cell { transition: background 0.15s; }
            .attendance-cell:hover { filter: brightness(0.96); }
            .status-ontime   { background: #f0fdf4; border-left: 3px solid transparent; }
            .status-late     { background: #fefce8; border-left: 3px solid #f59e0b; }
            .status-absent   { background: #fff1f2; border-left: 3px solid #f43f5e; }
            .status-holiday  { background: #f8fafc; border-left: 3px solid transparent; }
            .dark .status-ontime  { background: rgba(16,185,129,0.07); }
            .dark .status-late    { background: rgba(245,158,11,0.10); border-left-color:#d97706; }
            .dark .status-absent  { background: rgba(244,63,94,0.08); border-left-color:#e11d48; }
            .dark .status-holiday { background: rgba(100,116,139,0.08); }
            .day-header-today { background: #0f172a !important; color: #fff !important; border-radius: 8px; }
            .dark .day-header-today { background: #f8fafc !important; color: #0f172a !important; }
        </style>

        <script>
        (function() {
            const DAY_NAMES  = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
            const DAY_FULL   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
            const MONTHS     = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            const MAX_DAYS   = 62; // batas maksimum kolom yang ditampilkan

            // --- Sample data ---
            const staff = [
                { id:1, name:'Drs. Eko Wibowo, M.Pd',   role:'Guru',      initials:'EW', color:'#6366f1' },
                { id:2, name:'Siti Rahayu, S.Pd',        role:'Guru',      initials:'SR', color:'#ec4899' },
                { id:3, name:'Ahmad Fauzi, M.Kom',       role:'Guru',      initials:'AF', color:'#f59e0b' },
                { id:4, name:'Dewi Lestari, S.E',        role:'Karyawan',  initials:'DL', color:'#10b981' },
                { id:5, name:'Budi Santoso, S.Pd',       role:'Guru',      initials:'BS', color:'#3b82f6' },
                { id:6, name:'Rina Kusuma, S.T',         role:'Karyawan',  initials:'RK', color:'#8b5cf6' },
                { id:7, name:'Hendra Wijaya, M.Pd',      role:'Guru',      initials:'HW', color:'#ef4444' },
                { id:8, name:'Nita Permata, S.Pd',       role:'Guru',      initials:'NP', color:'#14b8a6' },
                { id:9, name:'Agus Prabowo, S.H',        role:'Karyawan',  initials:'AP', color:'#f97316' },
                { id:10,name:'Yunita Sari, M.Pd',        role:'Guru',      initials:'YS', color:'#a855f7' },
            ];

            // Fake attendance
            const STATUSES = ['ontime','ontime','ontime','ontime','late','absent','holiday'];
            const STATUS_REASONS = { late:['Macet','Telat Bangun','Kecelakaan'], absent:['Sakit','Izin Keluarga','Tanpa Keterangan'] };
            function fakeStatus(staffId, dayIdx) {
                return STATUSES[(staffId * 13 + dayIdx * 7) % STATUSES.length];
            }
            function fakeReason(status, staffId, dayIdx) {
                if (!STATUS_REASONS[status]) return '';
                return STATUS_REASONS[status][(staffId + dayIdx) % STATUS_REASONS[status].length];
            }

            // --- Helpers ---
            function addDays(date, n) {
                const d = new Date(date);
                d.setDate(d.getDate() + n);
                return d;
            }
            function toYMD(d) {
                // Returns YYYY-MM-DD string
                return d.toISOString().slice(0, 10);
            }
            function parseDate(str) {
                // Parse YYYY-MM-DD as local date
                const [y, m, day] = str.split('-').map(Number);
                const d = new Date(y, m - 1, day);
                d.setHours(0,0,0,0);
                return d;
            }
            function formatDate(d) {
                return `${d.getDate()} ${MONTHS[d.getMonth()]} ${d.getFullYear()}`;
            }
            function diffDays(a, b) {
                return Math.round((b - a) / 86400000) + 1;
            }

            // --- DOM refs ---
            const inputStart = document.getElementById('date-start');
            const inputEnd   = document.getElementById('date-end');
            const btnApply   = document.getElementById('btn-apply');
            const errEl      = document.getElementById('date-error');

            // --- Render ---
            function render() {
                const startDate = parseDate(inputStart.value);
                const endDate   = parseDate(inputEnd.value);
                const today     = new Date(); today.setHours(0,0,0,0);

                // Validation
                errEl.classList.add('hidden');
                errEl.textContent = '';
                if (isNaN(startDate) || isNaN(endDate)) {
                    errEl.textContent = 'Tanggal tidak valid.';
                    errEl.classList.remove('hidden');
                    return;
                }
                if (endDate < startDate) {
                    errEl.textContent = 'Tanggal akhir harus setelah tanggal mulai.';
                    errEl.classList.remove('hidden');
                    return;
                }
                let totalDays = diffDays(startDate, endDate);
                if (totalDays > MAX_DAYS) {
                    errEl.textContent = `Rentang maksimum ${MAX_DAYS} hari. Menampilkan ${MAX_DAYS} hari pertama.`;
                    errEl.classList.remove('hidden');
                    totalDays = MAX_DAYS;
                }

                const periodDays = Array.from({length: totalDays}, (_, i) => addDays(startDate, i));

                // --- Render thead ---
                const thead = document.getElementById('absensi-thead');
                const headerRow = document.createElement('tr');
                headerRow.className = 'border-b border-slate-200 dark:border-slate-800';

                // Profile th
                const profileTh = document.createElement('th');
                profileTh.className = 'px-4 py-3 bg-slate-50 dark:bg-slate-900/60 text-left sticky left-0 z-10 border-r border-slate-200 dark:border-slate-800 min-w-[200px]';
                profileTh.innerHTML = `
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="select-all" class="w-3.5 h-3.5 rounded border-slate-300 dark:border-slate-600 cursor-pointer">
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Profil Guru / Karyawan</span>
                    </div>`;
                headerRow.appendChild(profileTh);

                // Day columns
                periodDays.forEach((day, i) => {
                    const isToday    = day.getTime() === today.getTime();
                    const isWeekend  = day.getDay() === 0 || day.getDay() === 6;
                    const isLast     = i === totalDays - 1;
                    const th = document.createElement('th');
                    th.className = `py-2 px-1 text-center bg-slate-50 dark:bg-slate-900/60 min-w-[58px] max-w-[58px] ${isLast ? '' : 'border-r border-slate-100 dark:border-slate-800/60'}`;
                    const dayColor = isWeekend && !isToday ? 'text-red-400' : isToday ? '' : 'text-slate-400 dark:text-slate-500';
                    const numColor = isWeekend && !isToday ? 'text-red-500 dark:text-red-400' : isToday ? '' : 'text-slate-700 dark:text-slate-200';
                    th.innerHTML = `
                        <div class="flex flex-col items-center gap-0.5 py-0.5">
                            <span class="text-[9px] font-semibold ${dayColor} uppercase tracking-wider">${DAY_NAMES[day.getDay()]}</span>
                            <span class="${isToday ? 'day-header-today w-6 h-6 flex items-center justify-center rounded-full' : ''} text-xs font-bold ${numColor}">${day.getDate()}</span>
                        </div>`;
                    headerRow.appendChild(th);
                });

                thead.innerHTML = '';
                thead.appendChild(headerRow);

                // Re-bind select-all
                document.getElementById('select-all').addEventListener('change', function() {
                    document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
                });

                // --- Render rows ---
                const labelMap = { ontime:'Tepat Waktu', late:'Terlambat', absent:'Tidak Hadir', holiday:'Libur' };
                const textColorMap = {
                    ontime:  'text-emerald-700 dark:text-emerald-400',
                    late:    'text-amber-700 dark:text-amber-400',
                    absent:  'text-rose-700 dark:text-rose-400',
                    holiday: 'text-slate-400 dark:text-slate-500',
                };

                const tbody = document.getElementById('absensi-tbody');
                tbody.innerHTML = '';
                staff.forEach(s => {
                    const tr = document.createElement('tr');
                    tr.className = 'teacher-row group hover:bg-slate-50/60 dark:hover:bg-slate-900/30 transition-colors';

                    const profileTd = document.createElement('td');
                    profileTd.className = 'px-4 py-3 sticky left-0 z-10 bg-white dark:bg-slate-950 group-hover:bg-slate-50/60 dark:group-hover:bg-slate-900/30 border-r border-slate-100 dark:border-slate-800/60 transition-colors';
                    profileTd.innerHTML = `
                        <div class="flex items-center gap-3">
                            <input type="checkbox" class="row-check w-3.5 h-3.5 rounded border-slate-300 dark:border-slate-600 cursor-pointer shrink-0">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0 shadow-sm" style="background:${s.color}">${s.initials}</div>
                            <div class="flex flex-col min-w-0">
                                <span class="teacher-name font-semibold text-slate-800 dark:text-slate-100 truncate text-xs tracking-tight">${s.name}</span>
                                <span class="text-[10px] text-slate-400 dark:text-slate-500">${s.role}</span>
                            </div>
                        </div>`;
                    tr.appendChild(profileTd);

                    periodDays.forEach((day, di) => {
                        const isWeekend = day.getDay() === 0 || day.getDay() === 6;
                        const statusKey = isWeekend ? 'holiday' : fakeStatus(s.id, di);
                        const reason    = fakeReason(statusKey, s.id, di);
                        const isLast    = di === totalDays - 1;

                        const td = document.createElement('td');
                        td.className = `attendance-cell status-${statusKey} px-1 py-2.5 text-center ${isLast ? '' : 'border-r border-slate-100 dark:border-slate-800/40'}`;
                        td.title = `${DAY_FULL[day.getDay()]}, ${formatDate(day)} — ${labelMap[statusKey]}${reason ? ' (' + reason + ')' : ''}`;
                        td.innerHTML = `
                            <div class="flex flex-col items-center gap-0">
                                <span class="font-semibold text-[9px] leading-tight ${textColorMap[statusKey]}">${labelMap[statusKey]}</span>
                                ${reason ? `<span class="text-[8px] text-slate-400 dark:text-slate-500 leading-tight">(${reason})</span>` : ''}
                            </div>`;
                        tr.appendChild(td);
                    });

                    tbody.appendChild(tr);
                });
            }

            // --- Event: Terapkan button ---
            btnApply.addEventListener('click', render);

            // --- Event: Preset buttons ---
            document.querySelectorAll('.preset-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const preset = btn.dataset.preset;
                    const now = new Date(); now.setHours(0,0,0,0);
                    let start, end;
                    if (preset === 'month') {
                        start = new Date(now.getFullYear(), now.getMonth(), 1);
                        end   = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                    } else {
                        const days = parseInt(preset);
                        start = now;
                        end   = addDays(now, days - 1);
                    }
                    inputStart.value = toYMD(start);
                    inputEnd.value   = toYMD(end);
                    render();
                    // Highlight active preset
                    document.querySelectorAll('.preset-btn').forEach(b => b.classList.remove('bg-slate-900','dark:bg-slate-50','text-white','dark:text-slate-900'));
                    btn.classList.add('bg-slate-900','dark:bg-slate-50','text-white','dark:text-slate-900');
                });
            });

            // Initial render
            render();
        })();
        </script>

    </div>

    <!-- Client-side real-time Search & Filter Javascript -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('table-search');
            const filterMapel = document.getElementById('filter-mapel');
            const filterKepegawaian = document.getElementById('filter-kepegawaian');
            const filterStatus = document.getElementById('filter-status');
            const rows = document.querySelectorAll('.teacher-row');

            function filterTable() {
                const searchQuery = searchInput.value.toLowerCase().trim();
                const mapelQuery = filterMapel.value.toLowerCase();
                const kepegawaianQuery = filterKepegawaian.value.toLowerCase();
                const statusQuery = filterStatus.value.toLowerCase();

                rows.forEach(row => {
                    const name = row.querySelector('.teacher-name').textContent.toLowerCase();
                    const nip = row.querySelector('.teacher-nip').textContent.toLowerCase();
                    const mapel = row.querySelector('.teacher-mapel').textContent.toLowerCase();
                    const kepegawaian = row.querySelector('.teacher-kepegawaian').textContent.toLowerCase();
                    const status = row.querySelector('.teacher-status').textContent.trim().toLowerCase();

                    const matchesSearch = name.includes(searchQuery) || nip.includes(searchQuery) || mapel.includes(searchQuery);
                    const matchesMapel = !mapelQuery || mapel.includes(mapelQuery);
                    const matchesKepegawaian = !kepegawaianQuery || kepegawaian.includes(kepegawaianQuery);
                    const matchesStatus = !statusQuery || status.includes(statusQuery);

                    if (matchesSearch && matchesMapel && matchesKepegawaian && matchesStatus) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            if (searchInput) searchInput.addEventListener('input', filterTable);
            if (filterMapel) filterMapel.addEventListener('change', filterTable);
            if (filterKepegawaian) filterKepegawaian.addEventListener('change', filterTable);
            if (filterStatus) filterStatus.addEventListener('change', filterTable);
        });
    </script>
</x-admin-layout>
