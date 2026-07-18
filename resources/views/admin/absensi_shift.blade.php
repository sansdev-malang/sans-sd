<x-admin-layout>
    <div class="p-6 space-y-6">

        <!-- GREETING / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Jam Kerja & Shift</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Atur jam kerja masuk, jam pulang, dan toleransi keterlambatan masing-masing shift pegawai.</p>
            </div>
            <div class="flex items-center gap-2.5 shrink-0">
                <button onclick="openAddModal()"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-900 dark:bg-slate-50 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-all duration-150 cursor-pointer">
                    <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                    Tambah Shift Baru
                </button>
            </div>
        </section>

        <!-- STATS CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full">
            <!-- Total Shift -->
            <div class="bg-white dark:bg-slate-955 p-4 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-400 rounded-lg">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Shift</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-slate-50" id="stat-total">3</p>
                </div>
            </div>
            <!-- Shift Aktif -->
            <div class="bg-white dark:bg-slate-955 p-4 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-lg">
                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Shift Aktif</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-slate-50 text-emerald-600 dark:text-emerald-400" id="stat-active">3</p>
                </div>
            </div>
            <!-- Toleransi Rata-rata -->
            <div class="bg-white dark:bg-slate-955 p-4 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-lg">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Toleransi Terlambat</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-slate-50" id="stat-tolerance">15 Menit</p>
                </div>
            </div>
        </div>

        <!-- MAIN TABLE CARD -->
        <section class="animate-card bg-white dark:bg-slate-955 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden transition-all w-full p-6 space-y-6">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
                <div class="space-y-1">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-slate-50">Konfigurasi Jam Kerja</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Daftar shift jam kerja aktif yang diintegrasikan ke pencocokan transaksi log absensi.</p>
                </div>
            </div>

            <!-- Table Container -->
            <div class="overflow-x-auto border border-slate-100 dark:border-slate-900 rounded-xl">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30">
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-16">No</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider">Nama Shift</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-32">Batas Awal Masuk</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-32">Jam Masuk</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-32">Jam Pulang</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-36">Toleransi</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-28">Status</th>
                            <th class="px-6 py-4 text-center font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60" id="shift-table-body">
                        <!-- Data rows rendered dynamically -->
                    </tbody>
                </table>
            </div>

        </section>
    </div>

    <!-- MODAL: ADD / EDIT SHIFT -->
    <div id="add-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-955/40 dark:bg-slate-955/60 backdrop-blur-sm transition-opacity">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl shadow-xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <div class="px-6 py-4 border-b border-slate-150 dark:border-slate-800 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-50" id="modal-title">Tambah Shift Baru</h3>
                <button onclick="closeAddModal()" class="p-1 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <form id="add-form" onsubmit="submitForm(event)" class="p-6 space-y-4">
                <input type="hidden" id="form-id">
                
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Nama Shift Kerja</label>
                    <input type="text" required id="form-nama" placeholder="Contoh: Shift Pagi Guru" class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-955 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Batas Awal Masuk</label>
                        <input type="time" required id="form-awal-masuk" class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-955 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Toleransi Keterlambatan</label>
                        <div class="relative w-full">
                            <input type="number" required id="form-toleransi" value="15" class="w-full h-9 pr-14 px-3 text-xs bg-white dark:bg-slate-955 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-[10px] text-slate-400 font-bold">Menit</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Jam Masuk (Batas Akhir)</label>
                        <input type="time" required id="form-jam-masuk" class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-955 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Jam Pulang</label>
                        <input type="time" required id="form-jam-pulang" class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-955 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Status Shift</label>
                    <select required id="form-status" class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-955 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                        <option value="Aktif">Aktif</option>
                        <option value="Non-aktif">Non-aktif</option>
                    </select>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-150 dark:border-slate-800">
                    <button type="button" onclick="closeAddModal()" class="px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-slate-900 dark:bg-slate-50 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">
                        Simpan Shift
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- CLIENT SIDE LOGIC -->
    <script>
        let currentId = 3;
        let dataShift = [
            {
                id: 1,
                nama: "Shift Full Day (Guru / Staff)",
                awalMasuk: "06:00",
                jamMasuk: "07:00",
                jamPulang: "15:30",
                toleransi: 15,
                status: "Aktif"
            },
            {
                id: 2,
                nama: "Shift Piket / Setengah Hari",
                awalMasuk: "06:00",
                jamMasuk: "07:00",
                jamPulang: "12:30",
                toleransi: 15,
                status: "Aktif"
            },
            {
                id: 3,
                nama: "Shift Security / Penjaga Malam",
                awalMasuk: "18:00",
                jamMasuk: "19:00",
                jamPulang: "06:00",
                toleransi: 15,
                status: "Aktif"
            }
        ];

        document.addEventListener('DOMContentLoaded', () => {
            renderTable();
        });

        function renderTable() {
            const tableBody = document.getElementById('shift-table-body');
            tableBody.innerHTML = '';

            updateStats();

            dataShift.forEach((item, index) => {
                let statusBadge = '';
                if (item.status === 'Aktif') {
                    statusBadge = `
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-150 dark:border-emerald-900/30">
                            Aktif
                        </span>
                    `;
                } else {
                    statusBadge = `
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-50 dark:bg-slate-900 text-slate-550 dark:text-slate-400 border border-slate-200 dark:border-slate-800">
                            Non-aktif
                        </span>
                    `;
                }

                const row = document.createElement('tr');
                row.className = 'hover:bg-slate-50/40 dark:hover:bg-slate-900/20 transition-colors';
                row.innerHTML = `
                    <td class="px-6 py-4 font-semibold text-slate-900 dark:text-slate-50">${index + 1}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <i data-lucide="clock" class="w-4 h-4 text-slate-400"></i>
                            <span class="font-bold text-slate-800 dark:text-slate-100">${item.nama}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-slate-600 dark:text-slate-400 font-semibold font-mono">${item.awalMasuk}</td>
                    <td class="px-6 py-4 text-slate-700 dark:text-slate-350 font-bold font-mono">${item.jamMasuk}</td>
                    <td class="px-6 py-4 text-slate-700 dark:text-slate-350 font-bold font-mono">${item.jamPulang}</td>
                    <td class="px-6 py-4 text-slate-600 dark:text-slate-400 font-medium">${item.toleransi} Menit</td>
                    <td class="px-6 py-4">${statusBadge}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <button onclick="editShift(${item.id})" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-255 transition-colors cursor-pointer" title="Edit Shift">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </button>
                            <button onclick="deleteShift(${item.id})" class="p-1.5 hover:bg-rose-50 dark:hover:bg-rose-955/20 rounded-lg text-rose-600 dark:text-rose-400 hover:text-rose-700 transition-colors cursor-pointer" title="Hapus Shift">
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
            const total = dataShift.length;
            const active = dataShift.filter(s => s.status === 'Aktif').length;
            const avgTolerance = dataShift.length > 0 
                ? Math.round(dataShift.reduce((acc, curr) => acc + curr.toleransi, 0) / dataShift.length)
                : 0;

            document.getElementById('stat-total').innerText = total;
            document.getElementById('stat-active').innerText = active;
            document.getElementById('stat-tolerance').innerText = `${avgTolerance} Menit`;
        }

        // Action handles
        function openAddModal() {
            document.getElementById('modal-title').innerText = "Tambah Shift Baru";
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

        function editShift(id) {
            const item = dataShift.find(s => s.id === id);
            if (!item) return;

            document.getElementById('modal-title').innerText = "Edit Shift Kerja";
            document.getElementById('form-id').value = item.id;
            document.getElementById('form-nama').value = item.nama;
            document.getElementById('form-awal-masuk').value = item.awalMasuk;
            document.getElementById('form-jam-masuk').value = item.jamMasuk;
            document.getElementById('form-jam-pulang').value = item.jamPulang;
            document.getElementById('form-toleransi').value = item.toleransi;
            document.getElementById('form-status').value = item.status;

            const modal = document.getElementById('add-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function submitForm(event) {
            event.preventDefault();
            const id = document.getElementById('form-id').value;
            const nama = document.getElementById('form-nama').value;
            const awalMasuk = document.getElementById('form-awal-masuk').value;
            const jamMasuk = document.getElementById('form-jam-masuk').value;
            const jamPulang = document.getElementById('form-jam-pulang').value;
            const toleransi = document.getElementById('form-toleransi').value;
            const status = document.getElementById('form-status').value;

            if (id) {
                // Edit mode
                const item = dataShift.find(s => s.id == id);
                if (item) {
                    item.nama = nama;
                    item.awalMasuk = awalMasuk;
                    item.jamMasuk = jamMasuk;
                    item.jamPulang = jamPulang;
                    item.toleransi = parseInt(toleransi);
                    item.status = status;
                }
            } else {
                // Add mode
                currentId++;
                dataShift.push({
                    id: currentId,
                    nama: nama,
                    awalMasuk: awalMasuk,
                    jamMasuk: jamMasuk,
                    jamPulang: jamPulang,
                    toleransi: parseInt(toleransi),
                    status: status
                });
            }

            renderTable();
            closeAddModal();
        }

        function deleteShift(id) {
            if (confirm("Apakah Anda yakin ingin menghapus shift kerja ini?")) {
                dataShift = dataShift.filter(s => s.id !== id);
                renderTable();
            }
        }
    </script>
</x-admin-layout>
