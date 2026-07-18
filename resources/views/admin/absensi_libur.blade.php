<x-admin-layout>
    <div class="p-6 space-y-6">

        <!-- GREETING / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Kalender Hari Libur</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Kelola daftar libur nasional, cuti bersama, dan hari libur khusus sekolah.</p>
            </div>
            <div class="flex items-center gap-2.5 shrink-0">
                <button onclick="openAddModal()"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-900 dark:bg-slate-50 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-all duration-150 cursor-pointer">
                    <i data-lucide="calendar-plus" class="w-3.5 h-3.5"></i>
                    Tambah Hari Libur
                </button>
            </div>
        </section>

        <!-- STATS CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full">
            <!-- Total Hari Libur -->
            <div class="bg-white dark:bg-slate-955 p-4 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-400 rounded-lg">
                    <i data-lucide="calendar-days" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Libur (Tahun Ini)</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-slate-50" id="stat-total">3</p>
                </div>
            </div>
            <!-- Libur Nasional -->
            <div class="bg-white dark:bg-slate-955 p-4 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-red-100 dark:bg-red-900/30 text-red-650 dark:text-red-400 rounded-lg">
                    <i data-lucide="flag" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Libur Nasional</p>
                    <p class="text-xl font-bold text-red-600 dark:text-red-400" id="stat-nasional">2</p>
                </div>
            </div>
            <!-- Libur Intern / Khusus -->
            <div class="bg-white dark:bg-slate-955 p-4 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 text-blue-650 dark:text-blue-400 rounded-lg">
                    <i data-lucide="building" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Libur Khusus / Cuti Bersama</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-slate-50" id="stat-khusus">1</p>
                </div>
            </div>
        </div>

        <!-- MAIN TABLE CARD -->
        <section class="animate-card bg-white dark:bg-slate-955 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden transition-all w-full p-6 space-y-6">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
                <div class="space-y-1">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-slate-50">Daftar Hari Libur Terdaftar</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Hari libur terdaftar akan secara otomatis menonaktifkan kalkulasi denda alpha pada sistem absensi.</p>
                </div>
            </div>

            <!-- Table Container -->
            <div class="overflow-x-auto border border-slate-100 dark:border-slate-900 rounded-xl">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30">
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-16">No</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-48">Tanggal</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider">Nama Libur</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-36">Tipe Libur</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider">Keterangan</th>
                            <th class="px-6 py-4 text-center font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60" id="libur-table-body">
                        <!-- Data rows rendered dynamically -->
                    </tbody>
                </table>
            </div>

        </section>
    </div>

    <!-- MODAL: ADD / EDIT LIBUR -->
    <div id="add-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-955/40 dark:bg-slate-955/60 backdrop-blur-sm transition-opacity">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl shadow-xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <div class="px-6 py-4 border-b border-slate-150 dark:border-slate-800 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-50" id="modal-title">Tambah Hari Libur</h3>
                <button onclick="closeAddModal()" class="p-1 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <form id="add-form" onsubmit="submitForm(event)" class="p-6 space-y-4">
                <input type="hidden" id="form-id">
                
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Nama Hari Libur</label>
                    <input type="text" required id="form-nama" placeholder="Contoh: Hari Kemerdekaan RI" class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-955 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Tanggal Mulai</label>
                        <input type="date" required id="form-start-date" class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-955 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Tanggal Selesai</label>
                        <input type="date" required id="form-end-date" class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-955 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Tipe Libur</label>
                    <select required id="form-tipe" class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-955 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                        <option value="Nasional">Nasional</option>
                        <option value="Khusus">Khusus / Intern Sekolah</option>
                        <option value="Cuti Bersama">Cuti Bersama</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Deskripsi / Keterangan</label>
                    <textarea id="form-keterangan" rows="2" placeholder="Masukkan detail opsional..." class="w-full p-2.5 text-xs bg-white dark:bg-slate-955 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 placeholder-slate-400 dark:placeholder-slate-650 resize-none"></textarea>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-150 dark:border-slate-800">
                    <button type="button" onclick="closeAddModal()" class="px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-slate-900 dark:bg-slate-50 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">
                        Simpan Libur
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- CLIENT SIDE LOGIC -->
    <script>
        let currentId = 3;
        let dataLibur = [
            {
                id: 1,
                nama: "Tahun Baru Islam 1448 H",
                startDate: "2026-06-16",
                endDate: "2026-06-16",
                tipe: "Nasional",
                keterangan: "Hari Libur Keagamaan Nasional."
            },
            {
                id: 2,
                nama: "Hari Kemerdekaan Republik Indonesia",
                startDate: "2026-08-17",
                endDate: "2026-08-17",
                tipe: "Nasional",
                keterangan: "HUT Kemerdekaan RI ke-81."
            },
            {
                id: 3,
                nama: "Libur Cuti Bersama Khusus Sekolah SANS",
                startDate: "2026-07-23",
                endDate: "2026-07-24",
                tipe: "Khusus",
                keterangan: "Libur persiapan tengah semester / evaluasi intern."
            }
        ];

        document.addEventListener('DOMContentLoaded', () => {
            renderTable();
        });

        function renderTable() {
            const tableBody = document.getElementById('libur-table-body');
            tableBody.innerHTML = '';

            updateStats();

            dataLibur.forEach((item, index) => {
                let tipeBadge = '';
                if (item.tipe === 'Nasional') {
                    tipeBadge = `
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400 border border-rose-150 dark:border-rose-900/30">
                            Nasional
                        </span>
                    `;
                } else if (item.tipe === 'Cuti Bersama') {
                    tipeBadge = `
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 border border-amber-150 dark:border-amber-900/30">
                            Cuti Bersama
                        </span>
                    `;
                } else {
                    tipeBadge = `
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-405 border border-blue-150 dark:border-blue-900/30">
                            Khusus
                        </span>
                    `;
                }

                const startStr = formatDateIndo(item.startDate);
                const endStr = formatDateIndo(item.endDate);
                const dateText = startStr === endStr ? startStr : `${startStr} s/d ${endStr}`;

                const row = document.createElement('tr');
                row.className = 'hover:bg-slate-50/40 dark:hover:bg-slate-900/20 transition-colors';
                row.innerHTML = `
                    <td class="px-6 py-4 font-semibold text-slate-900 dark:text-slate-50">${index + 1}</td>
                    <td class="px-6 py-4 text-slate-700 dark:text-slate-350 font-bold font-mono">${dateText}</td>
                    <td class="px-6 py-4 text-slate-800 dark:text-slate-100 font-bold">${item.nama}</td>
                    <td class="px-6 py-4">${tipeBadge}</td>
                    <td class="px-6 py-4 text-slate-600 dark:text-slate-400">${item.keterangan || '-'}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <button onclick="editLibur(${item.id})" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 transition-colors cursor-pointer" title="Edit Hari Libur">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </button>
                            <button onclick="deleteLibur(${item.id})" class="p-1.5 hover:bg-rose-50 dark:hover:bg-rose-955/20 rounded-lg text-rose-600 dark:text-rose-400 hover:text-rose-700 transition-colors cursor-pointer" title="Hapus Hari Libur">
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
            const total = dataLibur.length;
            const nasional = dataLibur.filter(l => l.tipe === 'Nasional').length;
            const khusus = dataLibur.filter(l => l.tipe !== 'Nasional').length;

            document.getElementById('stat-total').innerText = total;
            document.getElementById('stat-nasional').innerText = nasional;
            document.getElementById('stat-khusus').innerText = khusus;
        }

        // Action handles
        function openAddModal() {
            document.getElementById('modal-title').innerText = "Tambah Hari Libur Baru";
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

        function editLibur(id) {
            const item = dataLibur.find(l => l.id === id);
            if (!item) return;

            document.getElementById('modal-title').innerText = "Edit Hari Libur";
            document.getElementById('form-id').value = item.id;
            document.getElementById('form-nama').value = item.nama;
            document.getElementById('form-start-date').value = item.startDate;
            document.getElementById('form-end-date').value = item.endDate;
            document.getElementById('form-tipe').value = item.tipe;
            document.getElementById('form-keterangan').value = item.keterangan;

            const modal = document.getElementById('add-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function submitForm(event) {
            event.preventDefault();
            const id = document.getElementById('form-id').value;
            const nama = document.getElementById('form-nama').value;
            const startDate = document.getElementById('form-start-date').value;
            const endDate = document.getElementById('form-end-date').value;
            const tipe = document.getElementById('form-tipe').value;
            const keterangan = document.getElementById('form-keterangan').value;

            if (id) {
                // Edit mode
                const item = dataLibur.find(l => l.id == id);
                if (item) {
                    item.nama = nama;
                    item.startDate = startDate;
                    item.endDate = endDate;
                    item.tipe = tipe;
                    item.keterangan = keterangan;
                }
            } else {
                // Add mode
                currentId++;
                dataLibur.push({
                    id: currentId,
                    nama: nama,
                    startDate: startDate,
                    endDate: endDate,
                    tipe: tipe,
                    keterangan: keterangan
                });
            }

            renderTable();
            closeAddModal();
        }

        function deleteLibur(id) {
            if (confirm("Apakah Anda yakin ingin menghapus hari libur ini?")) {
                dataLibur = dataLibur.filter(l => l.id !== id);
                renderTable();
            }
        }

        // Helper date format
        function formatDateIndo(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
        }
    </script>
</x-admin-layout>
