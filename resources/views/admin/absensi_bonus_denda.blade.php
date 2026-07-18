<x-admin-layout>
    <div class="p-6 space-y-6">

        <!-- GREETING / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Skema Bonus & Denda</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Atur nominal bonus untuk kedisiplinan pegawai serta denda pemotongan bagi keterlambatan/alpha.</p>
            </div>
            <div class="flex items-center gap-2.5 shrink-0">
                <button onclick="openAddModal()"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-900 dark:bg-slate-50 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-all duration-150 cursor-pointer">
                    <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                    Tambah Skema Baru
                </button>
            </div>
        </section>

        <!-- STATS CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full">
            <!-- Total Skema -->
            <div class="bg-white dark:bg-slate-955 p-4 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-400 rounded-lg">
                    <i data-lucide="settings" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Skema Aktif</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-slate-50" id="stat-total">3</p>
                </div>
            </div>
            <!-- Total Bonus -->
            <div class="bg-white dark:bg-slate-955 p-4 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-lg">
                    <i data-lucide="trending-up" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Skema Insentif / Bonus</p>
                    <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400" id="stat-bonus">1</p>
                </div>
            </div>
            <!-- Total Denda -->
            <div class="bg-white dark:bg-slate-955 p-4 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-lg">
                    <i data-lucide="trending-down" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Skema Denda / Potongan</p>
                    <p class="text-xl font-bold text-rose-600 dark:text-rose-400" id="stat-denda">2</p>
                </div>
            </div>
        </div>

        <!-- MAIN TABLE CARD -->
        <section class="animate-card bg-white dark:bg-slate-955 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden transition-all w-full p-6 space-y-6">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
                <div class="space-y-1">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-slate-50">Daftar Skema Aktif</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Gunakan skema ini untuk memicu pemotongan atau penambahan tunjangan kehadiran pada rekap bulanan.</p>
                </div>
            </div>

            <!-- Table Container -->
            <div class="overflow-x-auto border border-slate-100 dark:border-slate-900 rounded-xl">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30">
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-16">No</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider">Nama Aturan</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-36">Jenis</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-40">Nominal Tarif</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider">Kondisi Pemicu</th>
                            <th class="px-6 py-4 text-center font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60" id="skema-table-body">
                        <!-- Data rows rendered dynamically -->
                    </tbody>
                </table>
            </div>

        </section>
    </div>

    <!-- MODAL: ADD / EDIT SCHEME -->
    <div id="add-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-955/40 dark:bg-slate-955/60 backdrop-blur-sm transition-opacity">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl shadow-xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <div class="px-6 py-4 border-b border-slate-150 dark:border-slate-800 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-50" id="modal-title">Tambah Skema Baru</h3>
                <button onclick="closeAddModal()" class="p-1 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <form id="add-form" onsubmit="submitForm(event)" class="p-6 space-y-4">
                <input type="hidden" id="form-id">
                
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Nama Aturan / Skema</label>
                    <input type="text" required id="form-nama" placeholder="Contoh: Denda Terlambat >15 Menit" class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-955 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Jenis Skema</label>
                        <select required id="form-jenis" class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-955 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                            <option value="Denda">Denda / Potongan</option>
                            <option value="Bonus">Bonus / Insentif</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Nominal Tarif (Rp)</label>
                        <input type="number" required id="form-nominal" placeholder="50000" class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-955 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Kondisi Pemicu</label>
                    <input type="text" required id="form-kondisi" placeholder="Contoh: Keterlambatan lebih dari 15 menit" class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-955 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                </div>

                <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-150 dark:border-slate-800">
                    <button type="button" onclick="closeAddModal()" class="px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-slate-900 dark:bg-slate-50 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">
                        Simpan Skema
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- CLIENT SIDE LOGIC -->
    <script>
        let currentId = 3;
        let dataSkema = [
            {
                id: 1,
                nama: "Denda Terlambat Masuk (> 15 Menit)",
                jenis: "Denda",
                nominal: 25000,
                kondisi: "Pegawai terlambat melakukan presensi masuk di atas 15 menit dari jam shift."
            },
            {
                id: 2,
                nama: "Denda Alpha (Tanpa Keterangan)",
                jenis: "Denda",
                nominal: 75000,
                kondisi: "Pegawai tidak hadir presensi dan tidak mengajukan izin/cuti."
            },
            {
                id: 3,
                nama: "Bonus Kedisiplinan Bulanan",
                jenis: "Bonus",
                nominal: 150000,
                kondisi: "Hadir 100% penuh sesuai jam kerja shift (0 kali terlambat/izin/sakit)."
            }
        ];

        document.addEventListener('DOMContentLoaded', () => {
            renderTable();
        });

        function renderTable() {
            const tableBody = document.getElementById('skema-table-body');
            tableBody.innerHTML = '';

            updateStats();

            dataSkema.forEach((item, index) => {
                let jenisBadge = '';
                if (item.jenis === 'Bonus') {
                    jenisBadge = `
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-450 border border-emerald-150 dark:border-emerald-900/30">
                            Bonus / Insentif
                        </span>
                    `;
                } else {
                    jenisBadge = `
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-450 border border-rose-150 dark:border-rose-900/30">
                            Denda / Potongan
                        </span>
                    `;
                }

                const row = document.createElement('tr');
                row.className = 'hover:bg-slate-50/40 dark:hover:bg-slate-900/20 transition-colors';
                row.innerHTML = `
                    <td class="px-6 py-4 font-semibold text-slate-900 dark:text-slate-50">${index + 1}</td>
                    <td class="px-6 py-4 font-bold text-slate-800 dark:text-slate-100">${item.nama}</td>
                    <td class="px-6 py-4">${jenisBadge}</td>
                    <td class="px-6 py-4 font-semibold text-slate-700 dark:text-slate-350 font-mono">${formatRupiah(item.nominal)}</td>
                    <td class="px-6 py-4 text-slate-600 dark:text-slate-405">${item.kondisi}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <button onclick="editSkema(${item.id})" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 transition-colors cursor-pointer" title="Edit Skema">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </button>
                            <button onclick="deleteSkema(${item.id})" class="p-1.5 hover:bg-rose-50 dark:hover:bg-rose-955/20 rounded-lg text-rose-600 dark:text-rose-400 hover:text-rose-700 transition-colors cursor-pointer" title="Hapus Skema">
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
            const total = dataSkema.length;
            const bonus = dataSkema.filter(s => s.jenis === 'Bonus').length;
            const denda = dataSkema.filter(s => s.jenis === 'Denda').length;

            document.getElementById('stat-total').innerText = total;
            document.getElementById('stat-bonus').innerText = bonus;
            document.getElementById('stat-denda').innerText = denda;
        }

        // Action handles
        function openAddModal() {
            document.getElementById('modal-title').innerText = "Tambah Skema Baru";
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

        function editSkema(id) {
            const item = dataSkema.find(s => s.id === id);
            if (!item) return;

            document.getElementById('modal-title').innerText = "Edit Skema Tarif";
            document.getElementById('form-id').value = item.id;
            document.getElementById('form-nama').value = item.nama;
            document.getElementById('form-jenis').value = item.jenis;
            document.getElementById('form-nominal').value = item.nominal;
            document.getElementById('form-kondisi').value = item.kondisi;

            const modal = document.getElementById('add-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function submitForm(event) {
            event.preventDefault();
            const id = document.getElementById('form-id').value;
            const nama = document.getElementById('form-nama').value;
            const jenis = document.getElementById('form-jenis').value;
            const nominal = document.getElementById('form-nominal').value;
            const kondisi = document.getElementById('form-kondisi').value;

            if (id) {
                // Edit mode
                const item = dataSkema.find(s => s.id == id);
                if (item) {
                    item.nama = nama;
                    item.jenis = jenis;
                    item.nominal = parseInt(nominal);
                    item.kondisi = kondisi;
                }
            } else {
                // Add mode
                currentId++;
                dataSkema.push({
                    id: currentId,
                    nama: nama,
                    jenis: jenis,
                    nominal: parseInt(nominal),
                    kondisi: kondisi
                });
            }

            renderTable();
            closeAddModal();
        }

        function deleteSkema(id) {
            if (confirm("Apakah Anda yakin ingin menghapus skema denda/bonus ini?")) {
                dataSkema = dataSkema.filter(s => s.id !== id);
                renderTable();
            }
        }

        // Helper currency formatter
        function formatRupiah(num) {
            return 'Rp ' + num.toLocaleString('id-ID');
        }
    </script>
</x-admin-layout>
