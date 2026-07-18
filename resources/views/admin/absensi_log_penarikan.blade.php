<x-admin-layout>
    <div class="p-6 space-y-6">

        <!-- GREETING / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Log Penarikan Absensi</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Riwayat sinkronisasi dan penarikan data transaksi absensi dari mesin ZKTeco ke basis data server.</p>
            </div>
            <div class="flex items-center gap-2.5 shrink-0">
                <button onclick="startSync()"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-900 dark:bg-slate-50 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-all duration-150 cursor-pointer">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                    Tarik Data Log Sekarang
                </button>
            </div>
        </section>

        <!-- STATS CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full">
            <!-- Total Sinkronisasi -->
            <div class="bg-white dark:bg-slate-955 p-4 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-400 rounded-lg">
                    <i data-lucide="history" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Sinkronisasi</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-slate-50" id="stat-total">15</p>
                </div>
            </div>
            <!-- Berhasil -->
            <div class="bg-white dark:bg-slate-955 p-4 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-lg">
                    <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Berhasil</p>
                    <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400" id="stat-success">14</p>
                </div>
            </div>
            <!-- Gagal -->
            <div class="bg-white dark:bg-slate-955 p-4 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-lg">
                    <i data-lucide="x-circle" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Gagal / Masalah</p>
                    <p class="text-xl font-bold text-rose-600 dark:text-rose-400" id="stat-failed">1</p>
                </div>
            </div>
        </div>

        <!-- MAIN TABLE CARD -->
        <section class="animate-card bg-white dark:bg-slate-955 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden transition-all w-full p-6 space-y-6">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
                <div class="space-y-1">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-slate-50">Log Riwayat Sinkronisasi</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Catatan riwayat koneksi penarikan transaksi mesin absensi lokal.</p>
                </div>
            </div>

            <!-- Table Container -->
            <div class="overflow-x-auto border border-slate-100 dark:border-slate-900 rounded-xl">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30">
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-16">No</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-48">Waktu Penarikan</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider">Perangkat Sumber</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-32">Metode</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-36">Data Masuk</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-32">Status</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-40">Operator</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60" id="log-table-body">
                        <!-- Data rows rendered dynamically -->
                    </tbody>
                </table>
            </div>

        </section>
    </div>

    <!-- MODAL: PROSES SINKRONISASI TERMINAL SIMULATOR -->
    <div id="sync-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-950/40 dark:bg-slate-950/60 backdrop-blur-sm transition-opacity">
        <div class="bg-slate-900 border border-slate-850 rounded-xl shadow-xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-100 flex items-center gap-2">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    ZKTeco Sinkronisasi Terminal
                </h3>
                <button onclick="closeSyncModal()" class="p-1 hover:bg-slate-800 rounded-lg text-slate-400 hover:text-slate-200 transition-colors cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <!-- Terminal Console -->
                <div class="bg-black rounded-lg p-4 font-mono text-xs text-emerald-400 space-y-1.5 h-64 overflow-y-auto" id="terminal-console">
                    <p class="text-slate-500">> Memulai proses penarikan log absensi...</p>
                </div>
                
                <div class="pt-2 flex items-center justify-between border-t border-slate-850">
                    <span class="text-[11px] text-slate-455" id="sync-status-text">Menghubungkan ke mesin...</span>
                    <button id="btn-close-sync" disabled onclick="closeSyncModal()" class="px-4 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-lg transition-colors cursor-pointer disabled:opacity-50">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- CLIENT SIDE LOGIC -->
    <script>
        let logsData = [
            {
                id: 1,
                time: "2026-07-15 07:00:02",
                device: "Mesin Sidik Jari Gerbang Utama",
                method: "Otomatis (Cron)",
                records: 12,
                status: "Berhasil",
                operator: "System Scheduler"
            },
            {
                id: 2,
                time: "2026-07-15 07:00:01",
                device: "Mesin Wajah & Finger Kantor Guru",
                method: "Otomatis (Cron)",
                records: 8,
                status: "Berhasil",
                operator: "System Scheduler"
            },
            {
                id: 3,
                time: "2026-07-15 06:15:22",
                device: "Mesin Sidik Jari Gerbang Utama",
                method: "Manual",
                records: 142,
                status: "Berhasil",
                operator: "Administrator"
            },
            {
                id: 4,
                time: "2026-07-14 17:00:03",
                device: "Mesin Sidik Jari Gerbang Utama",
                method: "Otomatis (Cron)",
                records: 45,
                status: "Berhasil",
                operator: "System Scheduler"
            },
            {
                id: 5,
                time: "2026-07-14 17:00:02",
                device: "Mesin Wajah & Finger Kantor Guru",
                method: "Otomatis (Cron)",
                records: 31,
                status: "Berhasil",
                operator: "System Scheduler"
            },
            {
                id: 6,
                time: "2026-07-14 12:30:10",
                device: "Mesin Wajah & Finger Kantor Guru",
                ip: "192.168.1.202",
                method: "Manual",
                records: 0,
                status: "Gagal",
                operator: "Administrator"
            }
        ];

        document.addEventListener('DOMContentLoaded', () => {
            renderTable();
        });

        function renderTable() {
            const tableBody = document.getElementById('log-table-body');
            tableBody.innerHTML = '';

            updateStats();

            logsData.forEach((item, index) => {
                let statusBadge = '';
                if (item.status === 'Berhasil') {
                    statusBadge = `
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-450 border border-emerald-150 dark:border-emerald-900/30">
                            Sukses
                        </span>
                    `;
                } else {
                    statusBadge = `
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-450 border border-rose-150 dark:border-rose-900/30">
                            Gagal
                        </span>
                    `;
                }

                const row = document.createElement('tr');
                row.className = 'hover:bg-slate-50/40 dark:hover:bg-slate-900/20 transition-colors';
                row.innerHTML = `
                    <td class="px-6 py-4 font-semibold text-slate-900 dark:text-slate-50">${index + 1}</td>
                    <td class="px-6 py-4 text-slate-700 dark:text-slate-300 font-semibold">${item.time}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <i data-lucide="cpu" class="w-3.5 h-3.5 text-slate-400"></i>
                            <span class="font-bold text-slate-800 dark:text-slate-100">${item.device}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-slate-650 dark:text-slate-400 font-medium">${item.method}</td>
                    <td class="px-6 py-4 font-bold text-slate-700 dark:text-slate-300">${item.records} Baris Log</td>
                    <td class="px-6 py-4">${statusBadge}</td>
                    <td class="px-6 py-4 text-slate-650 dark:text-slate-400">${item.operator}</td>
                `;
                tableBody.appendChild(row);
            });

            lucide.createIcons();
        }

        function updateStats() {
            const total = logsData.length;
            const success = logsData.filter(l => l.status === 'Berhasil').length;
            const failed = logsData.filter(l => l.status === 'Gagal').length;

            document.getElementById('stat-total').innerText = total;
            document.getElementById('stat-success').innerText = success;
            document.getElementById('stat-failed').innerText = failed;
        }

        // Terminal sync simulation
        function startSync() {
            const modal = document.getElementById('sync-modal');
            const consoleBox = document.getElementById('terminal-console');
            const statusText = document.getElementById('sync-status-text');
            const btnClose = document.getElementById('btn-close-sync');

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            btnClose.disabled = true;
            statusText.innerText = "Menghubungkan ke mesin...";
            consoleBox.innerHTML = `<p class="text-slate-500">> Memulai proses penarikan log absensi...</p>`;

            const lines = [
                { text: "> Ping host 192.168.1.201 - Terkoneksi (Reply 1ms)", delay: 800 },
                { text: "> Mengirim sinyal handshake ke ZKTeco SDK...", delay: 1400 },
                { text: "> Handshake berhasil. Membaca model data mesin...", delay: 2000 },
                { text: "> Model: ZKTeco K40, Firmware: Ver 10.0.1", delay: 2400 },
                { text: "> Mengunduh catatan transaksi absensi baru...", delay: 3000 },
                { text: "> Mengunduh total 18 data absen baru hari ini...", delay: 3600 },
                { text: "> Menyimpan data absensi ke table (hr_logs)...", delay: 4200 },
                { text: "> Menyesuaikan data absensi ke kehadiran hari ini...", delay: 4800 },
                { text: "> [SUKSES] Sinkronisasi data log absensi selesai.", delay: 5400 }
            ];

            lines.forEach(line => {
                setTimeout(() => {
                    const p = document.createElement('p');
                    p.innerText = line.text;
                    if (line.text.includes('[SUKSES]')) {
                        p.className = 'text-emerald-300 font-bold';
                        statusText.innerText = "Sinkronisasi selesai!";
                        btnClose.disabled = false;
                        
                        // Push log to list
                        const now = new Date();
                        const timeStr = `${now.getFullYear()}-${String(now.getMonth()+1).padStart(2,'0')}-${String(now.getDate()).padStart(2,'0')} ${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}:${String(now.getSeconds()).padStart(2,'0')}`;
                        logsData.unshift({
                            id: logsData.length + 1,
                            time: timeStr,
                            device: "Mesin Sidik Jari Gerbang Utama",
                            method: "Manual",
                            records: 18,
                            status: "Berhasil",
                            operator: "Administrator"
                        });
                        renderTable();
                    }
                    consoleBox.appendChild(p);
                    consoleBox.scrollTop = consoleBox.scrollHeight;
                }, line.delay);
            });
        }

        function closeSyncModal() {
            const modal = document.getElementById('sync-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
</x-admin-layout>
