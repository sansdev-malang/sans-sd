<x-admin-layout>
    <div class="p-6 space-y-6">

        <!-- GREETING / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Mesin & Perangkat Absensi</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Kelola konfigurasi, status koneksi, dan sinkronisasi mesin sidik jari/wajah ZKTeco.</p>
            </div>
            <div class="flex items-center gap-2.5 shrink-0">
                <button onclick="openAddModal()"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-900 dark:bg-slate-50 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-all duration-150 cursor-pointer">
                    <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                    Daftarkan Perangkat
                </button>
            </div>
        </section>

        <!-- STATS CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full">
            <!-- Total Perangkat -->
            <div class="bg-white dark:bg-slate-955 p-4 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-400 rounded-lg">
                    <i data-lucide="cpu" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Mesin</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-slate-50" id="stat-total">2</p>
                </div>
            </div>
            <!-- Online -->
            <div class="bg-white dark:bg-slate-955 p-4 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-lg">
                    <i data-lucide="wifi" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Status Terkoneksi</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-slate-50 text-emerald-600 dark:text-emerald-400" id="stat-online">2</p>
                </div>
            </div>
            <!-- Offline -->
            <div class="bg-white dark:bg-slate-955 p-4 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-lg">
                    <i data-lucide="wifi-off" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Terputus</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-slate-50" id="stat-offline">0</p>
                </div>
            </div>
        </div>

        <!-- MAIN MACHINE TABLE CARD -->
        <section class="animate-card bg-white dark:bg-slate-955 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden transition-all w-full p-6 space-y-6">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
                <div class="space-y-1">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-slate-50">Daftar Mesin Terdaftar</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Kelola dan pastikan IP address dan port mesin sesuai dengan jaringan lokal sekolah.</p>
                </div>
            </div>

            <!-- Table Container -->
            <div class="overflow-x-auto border border-slate-100 dark:border-slate-900 rounded-xl">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30">
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-16">No</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider">Nama Mesin</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-40">IP Address</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-24">Port</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-36">Tipe Mesin</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider">Lokasi / Area</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-28">Status</th>
                            <th class="px-6 py-4 text-center font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-48">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60" id="mesin-table-body">
                        <!-- Data rows rendered dynamically -->
                    </tbody>
                </table>
            </div>

        </section>
    </div>

    <!-- MODAL: DAFTAR / EDIT MESIN -->
    <div id="add-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-950/40 dark:bg-slate-950/60 backdrop-blur-sm transition-opacity">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl shadow-xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <div class="px-6 py-4 border-b border-slate-150 dark:border-slate-800 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-50" id="modal-title">Daftarkan Perangkat Baru</h3>
                <button onclick="closeAddModal()" class="p-1 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <form id="add-form" onsubmit="submitForm(event)" class="p-6 space-y-4">
                <input type="hidden" id="form-id">
                
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Nama Perangkat</label>
                    <input type="text" required id="form-nama" placeholder="Contoh: Mesin Utama Lt. 1" class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">IP Address</label>
                        <input type="text" required id="form-ip" placeholder="192.168.1.201" class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Port Comm</label>
                        <input type="number" required id="form-port" value="4370" class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Tipe / Model</label>
                        <select required id="form-tipe" class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                            <option value="ZKTeco K40">ZKTeco K40</option>
                            <option value="ZKTeco iFace">ZKTeco iFace</option>
                            <option value="ZKTeco LX50">ZKTeco LX50</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Lokasi Penempatan</label>
                        <input type="text" required id="form-lokasi" placeholder="Pintu Gerbang Utama" class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                    </div>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-150 dark:border-slate-800">
                    <button type="button" onclick="closeAddModal()" class="px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-slate-900 dark:bg-slate-50 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">
                        Simpan Perangkat
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- CLIENT SIDE LOGIC -->
    <script>
        let currentId = 2;
        let dataMesin = [
            {
                id: 1,
                nama: "Mesin Sidik Jari Gerbang Utama",
                ip: "192.168.1.201",
                port: 4370,
                tipe: "ZKTeco K40",
                lokasi: "Pos Satpam Depan",
                status: "Online"
            },
            {
                id: 2,
                nama: "Mesin Wajah & Finger Kantor Guru",
                ip: "192.168.1.202",
                port: 4370,
                tipe: "ZKTeco iFace",
                lokasi: "Lobby Kantor Guru",
                status: "Online"
            }
        ];

        document.addEventListener('DOMContentLoaded', () => {
            renderTable();
        });

        function renderTable() {
            const tableBody = document.getElementById('mesin-table-body');
            tableBody.innerHTML = '';

            updateStats();

            dataMesin.forEach((item, index) => {
                let statusBadge = '';
                if (item.status === 'Online') {
                    statusBadge = `
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-250 dark:border-emerald-900/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Online
                        </span>
                    `;
                } else if (item.status === 'Testing') {
                    statusBadge = `
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 border border-amber-250 dark:border-amber-900/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                            Pinging...
                        </span>
                    `;
                } else {
                    statusBadge = `
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400 border border-rose-250 dark:border-rose-900/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                            Offline
                        </span>
                    `;
                }

                const row = document.createElement('tr');
                row.className = 'hover:bg-slate-50/40 dark:hover:bg-slate-900/20 transition-colors';
                row.innerHTML = `
                    <td class="px-6 py-4 font-semibold text-slate-900 dark:text-slate-50">${index + 1}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="fingerprint" class="w-4 h-4 text-slate-400"></i>
                            <span class="font-bold text-slate-850 dark:text-slate-100">${item.nama}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-semibold text-slate-700 dark:text-slate-300">${item.ip}</td>
                    <td class="px-6 py-4 text-slate-650 dark:text-slate-405 font-mono">${item.port}</td>
                    <td class="px-6 py-4 font-medium text-slate-600 dark:text-slate-400">${item.tipe}</td>
                    <td class="px-6 py-4 text-slate-600 dark:text-slate-400">${item.lokasi}</td>
                    <td class="px-6 py-4">${statusBadge}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="pingConnection(${item.id})" class="inline-flex items-center justify-center gap-1 px-2.5 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-[10px] font-bold rounded-md transition-colors cursor-pointer" title="Ping Mesin">
                                <i data-lucide="radio" class="w-3.5 h-3.5"></i>
                                Tes Koneksi
                            </button>
                            <button onclick="editMachine(${item.id})" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 transition-colors cursor-pointer" title="Edit Mesin">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </button>
                            <button onclick="deleteMachine(${item.id})" class="p-1.5 hover:bg-rose-50 dark:hover:bg-rose-950/20 rounded-lg text-rose-600 dark:text-rose-400 hover:text-rose-700 transition-colors cursor-pointer" title="Hapus Mesin">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </td>
                `;
                tableBody.appendChild(row);
            });

            lucide.createIcons();
        }

        function updateStats() {
            const total = dataMesin.length;
            const online = dataMesin.filter(m => m.status === 'Online').length;
            const offline = dataMesin.filter(m => m.status === 'Offline').length;

            document.getElementById('stat-total').innerText = total;
            document.getElementById('stat-online').innerText = online;
            document.getElementById('stat-offline').innerText = offline;
        }

        // Action methods
        function pingConnection(id) {
            const item = dataMesin.find(m => m.id === id);
            if (!item) return;

            item.status = 'Testing';
            renderTable();

            // Simulate ping network reply after 1.5 seconds
            setTimeout(() => {
                // 90% chance to be Online, 10% to be Offline (simulation)
                item.status = Math.random() > 0.1 ? 'Online' : 'Offline';
                renderTable();
            }, 1200);
        }

        function openAddModal() {
            document.getElementById('modal-title').innerText = "Daftarkan Perangkat Baru";
            document.getElementById('form-id').value = '';
            document.getElementById('add-form').reset();
            
            const modal = document.getElementById('add-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeAddModal() {
            const modal = document.getElementById('add-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function editMachine(id) {
            const item = dataMesin.find(m => m.id === id);
            if (!item) return;

            document.getElementById('modal-title').innerText = "Edit Perangkat Absensi";
            document.getElementById('form-id').value = item.id;
            document.getElementById('form-nama').value = item.nama;
            document.getElementById('form-ip').value = item.ip;
            document.getElementById('form-port').value = item.port;
            document.getElementById('form-tipe').value = item.tipe;
            document.getElementById('form-lokasi').value = item.lokasi;

            const modal = document.getElementById('add-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function submitForm(event) {
            event.preventDefault();
            const id = document.getElementById('form-id').value;
            const nama = document.getElementById('form-nama').value;
            const ip = document.getElementById('form-ip').value;
            const port = document.getElementById('form-port').value;
            const tipe = document.getElementById('form-tipe').value;
            const lokasi = document.getElementById('form-lokasi').value;

            if (id) {
                // Edit mode
                const item = dataMesin.find(m => m.id == id);
                if (item) {
                    item.nama = nama;
                    item.ip = ip;
                    item.port = parseInt(port);
                    item.tipe = tipe;
                    item.lokasi = lokasi;
                }
            } else {
                // Add mode
                currentId++;
                dataMesin.push({
                    id: currentId,
                    nama: nama,
                    ip: ip,
                    port: parseInt(port),
                    tipe: tipe,
                    lokasi: lokasi,
                    status: "Online"
                });
            }

            renderTable();
            closeAddModal();
        }

        function deleteMachine(id) {
            if (confirm("Apakah Anda yakin ingin menghapus perangkat ini?")) {
                dataMesin = dataMesin.filter(m => m.id !== id);
                renderTable();
            }
        }
    </script>
</x-admin-layout>
