<x-admin-layout>
    <div class="p-6 space-y-6">

        <!-- GREETING / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Pengajuan Izin & Cuti</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Kelola dan tinjau pengajuan izin, sakit, dan cuti guru serta staff.</p>
            </div>
            <div class="flex items-center gap-2.5 shrink-0">
                <button onclick="openAddModal()"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-900 dark:bg-slate-50 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-all duration-150 cursor-pointer">
                    <i data-lucide="calendar-plus" class="w-3.5 h-3.5"></i>
                    Buat Pengajuan
                </button>
            </div>
        </section>

        <!-- STATS CARDS -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 w-full">
            <!-- Total Pengajuan -->
            <div class="bg-white dark:bg-slate-950 p-4 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-400 rounded-lg">
                    <i data-lucide="file-text" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Pengajuan</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-slate-50" id="stat-total">3</p>
                </div>
            </div>
            <!-- Menunggu Persetujuan -->
            <div class="bg-white dark:bg-slate-950 p-4 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-lg">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Menunggu Approval</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-slate-50" id="stat-pending">1</p>
                </div>
            </div>
            <!-- Disetujui -->
            <div class="bg-white dark:bg-slate-950 p-4 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-lg">
                    <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Disetujui</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-slate-50" id="stat-approved">1</p>
                </div>
            </div>
            <!-- Ditolak -->
            <div class="bg-white dark:bg-slate-950 p-4 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-lg">
                    <i data-lucide="x-circle" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Ditolak</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-slate-50" id="stat-rejected">1</p>
                </div>
            </div>
        </div>

        <!-- MAIN TABLE CARD -->
        <section class="animate-card bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden transition-all w-full p-6 space-y-6">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
                <div class="space-y-1">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-slate-50">Daftar Pengajuan Izin & Cuti</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Gunakan filter untuk mempercepat pencarian data pengajuan.</p>
                </div>
            </div>

            <!-- Search & Filters Container -->
            <div class="flex flex-col md:flex-row gap-4 items-end justify-between w-full">
                <!-- Search Box -->
                <div class="space-y-1.5 w-full md:flex-1 md:max-w-md">
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Cari Pegawai / Keterangan</label>
                    <div class="relative w-full">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i data-lucide="search" class="w-4 h-4 text-slate-400 dark:text-slate-500"></i>
                        </span>
                        <input type="text" id="table-search" placeholder="Nama pegawai, alasan..."
                            style="padding-left: 2.25rem;"
                            class="w-full h-9 pr-4 text-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 focus:border-slate-400 dark:focus:border-slate-600 text-slate-900 dark:text-slate-50 placeholder-slate-400 dark:placeholder-slate-500 transition-all shadow-inner">
                    </div>
                </div>

                <!-- Filter Type & Status -->
                <div class="flex flex-row items-end gap-2.5 w-full md:w-auto">
                    <!-- Filter Jenis -->
                    <div class="space-y-1.5 flex-1 sm:flex-initial">
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Jenis</label>
                        <select id="filter-jenis"
                            class="h-9 px-3 w-full sm:w-40 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 cursor-pointer transition-all shadow-sm">
                            <option value="">Semua Jenis</option>
                            <option value="Cuti">Cuti</option>
                            <option value="Izin">Izin</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Dispensasi">Dispensasi</option>
                        </select>
                    </div>

                    <!-- Filter Status -->
                    <div class="space-y-1.5 flex-1 sm:flex-initial">
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Status</label>
                        <select id="filter-status"
                            class="h-9 px-3 w-full sm:w-40 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 cursor-pointer transition-all shadow-sm">
                            <option value="">Semua Status</option>
                            <option value="Pending">Menunggu Approval</option>
                            <option value="Disetujui">Disetujui</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Table Container -->
            <div class="overflow-x-auto border border-slate-100 dark:border-slate-900 rounded-xl">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30">
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-16">No</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider">Nama Pegawai</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-28">Jenis</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-44">Tanggal</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-20">Durasi</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider">Keterangan</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-36">Status</th>
                            <th class="px-6 py-4 text-center font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60" id="izin-table-body">
                        <!-- Data rows will be rendered here dynamically -->
                    </tbody>
                </table>
            </div>

        </section>
    </div>

    <!-- MODAL: TAMBAH PENGAJUAN -->
    <div id="add-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-950/40 dark:bg-slate-950/60 backdrop-blur-sm transition-opacity">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl shadow-xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <div class="px-6 py-4 border-b border-slate-150 dark:border-slate-800 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-50">Buat Pengajuan Baru</h3>
                <button onclick="closeAddModal()" class="p-1 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <form id="add-form" onsubmit="submitForm(event)" class="p-6 space-y-4">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Pegawai</label>
                    <select required id="form-pegawai" class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                        <option value="">Pilih Pegawai...</option>
                        <option value="MUHAMMAD AKBAR AMIN">MUHAMMAD AKBAR AMIN</option>
                        <option value="AHMAD FAJAR ARIF">AHMAD FAJAR ARIF</option>
                        <option value="MELINDA">MELINDA</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Jenis Pengajuan</label>
                        <select required id="form-jenis" class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                            <option value="Cuti">Cuti</option>
                            <option value="Izin">Izin</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Dispensasi">Dispensasi</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Lampiran Dokumen</label>
                        <input type="file" id="form-file" class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-100 dark:file:bg-slate-800 file:text-slate-700 dark:file:text-slate-300 hover:file:bg-slate-200 dark:hover:file:bg-slate-700 cursor-pointer">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Tanggal Mulai</label>
                        <input type="date" required id="form-start-date" class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Tanggal Selesai</label>
                        <input type="date" required id="form-end-date" class="w-full h-9 px-3 text-xs bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Keterangan / Alasan</label>
                    <textarea required id="form-keterangan" rows="3" placeholder="Masukkan alasan pengajuan..." class="w-full p-3 text-xs bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 placeholder-slate-400 dark:placeholder-slate-650 resize-none"></textarea>
                </div>
                <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-150 dark:border-slate-800">
                    <button type="button" onclick="closeAddModal()" class="px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-slate-900 dark:bg-slate-50 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">
                        Simpan Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: DETAIL & PERSETUJUAN -->
    <div id="detail-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-950/40 dark:bg-slate-950/60 backdrop-blur-sm transition-opacity">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl shadow-xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <div class="px-6 py-4 border-b border-slate-150 dark:border-slate-800 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-50">Detail Pengajuan Izin/Cuti</h3>
                <button onclick="closeDetailModal()" class="p-1 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <!-- Info list -->
                <div class="space-y-3">
                    <div class="flex justify-between border-b border-slate-100 dark:border-slate-800/80 pb-2">
                        <span class="text-xs text-slate-550 dark:text-slate-405 font-medium">Nama Pegawai</span>
                        <span class="text-xs text-slate-900 dark:text-slate-50 font-bold" id="det-pegawai">-</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-100 dark:border-slate-800/80 pb-2">
                        <span class="text-xs text-slate-550 dark:text-slate-405 font-medium">Jenis Pengajuan</span>
                        <span id="det-jenis-badge">-</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-100 dark:border-slate-800/80 pb-2">
                        <span class="text-xs text-slate-550 dark:text-slate-405 font-medium">Tanggal Pelaksanaan</span>
                        <span class="text-xs text-slate-900 dark:text-slate-50 font-semibold" id="det-tanggal">-</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-100 dark:border-slate-800/80 pb-2">
                        <span class="text-xs text-slate-550 dark:text-slate-405 font-medium">Durasi</span>
                        <span class="text-xs text-slate-900 dark:text-slate-50 font-bold" id="det-durasi">-</span>
                    </div>
                    <div class="flex flex-col gap-1 border-b border-slate-100 dark:border-slate-800/80 pb-2">
                        <span class="text-xs text-slate-550 dark:text-slate-405 font-medium">Keterangan / Alasan</span>
                        <span class="text-xs text-slate-850 dark:text-slate-200 bg-slate-50 dark:bg-slate-950 p-2.5 rounded-lg border border-slate-100 dark:border-slate-850/80 mt-1" id="det-keterangan">-</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-100 dark:border-slate-800/80 pb-2 items-center">
                        <span class="text-xs text-slate-550 dark:text-slate-405 font-medium">Lampiran Dokumen</span>
                        <span class="text-xs text-slate-900 dark:text-slate-50" id="det-lampiran">
                            <span class="inline-flex items-center gap-1 text-slate-400 italic">Tidak ada lampiran</span>
                        </span>
                    </div>
                    <div class="flex justify-between items-center pt-1">
                        <span class="text-xs text-slate-550 dark:text-slate-405 font-medium">Status Saat Ini</span>
                        <span id="det-status-badge">-</span>
                    </div>
                </div>

                <!-- Approval Actions -->
                <div id="approval-actions" class="pt-4 flex items-center justify-end gap-2 border-t border-slate-150 dark:border-slate-800">
                    <button onclick="approveRequest(false)" class="flex-1 px-4 py-2 hover:bg-rose-50 dark:hover:bg-rose-950/20 border border-rose-200 dark:border-rose-900/60 text-rose-600 dark:text-rose-400 text-xs font-semibold rounded-lg transition-colors cursor-pointer flex items-center justify-center gap-1.5">
                        <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                        Tolak
                    </button>
                    <button onclick="approveRequest(true)" class="flex-1 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-750 dark:hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer flex items-center justify-center gap-1.5">
                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                        Setujui
                    </button>
                </div>
                <div id="close-only-action" class="pt-4 hidden border-t border-slate-150 dark:border-slate-800">
                    <button onclick="closeDetailModal()" class="w-full px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-lg transition-colors cursor-pointer text-center">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- CLIENT SIDE LOGIC -->
    <script>
        // Seed initial mock data
        let currentId = 3;
        let dataIzin = [
            {
                id: 1,
                name: "MUHAMMAD AKBAR AMIN",
                email: "14@example.com",
                jenis: "Cuti",
                startDate: "2026-07-16",
                endDate: "2026-07-18",
                durasi: 3,
                keterangan: "Keperluan keluarga di luar kota (pernikahan adik kandung).",
                status: "Pending",
                lampiran: "surat_undangan.pdf"
            },
            {
                id: 2,
                name: "AHMAD FAJAR ARIF",
                email: "15@example.com",
                jenis: "Sakit",
                startDate: "2026-07-10",
                endDate: "2026-07-11",
                durasi: 2,
                keterangan: "Sakit demam tinggi dan disarankan dokter untuk istirahat 2 hari.",
                status: "Disetujui",
                lampiran: "surat_keterangan_sakit.jpg"
            },
            {
                id: 3,
                name: "MELINDA",
                email: "10@example.com",
                jenis: "Izin",
                startDate: "2026-07-05",
                endDate: "2026-07-05",
                durasi: 1,
                keterangan: "Mengurus perpanjangan SIM di Satpas Polres Malang.",
                status: "Ditolak",
                lampiran: null
            }
        ];

        let selectedRequestId = null;

        document.addEventListener('DOMContentLoaded', () => {
            renderTable();
            setupSearchFilters();
        });

        function renderTable() {
            const tableBody = document.getElementById('izin-table-body');
            tableBody.innerHTML = '';

            // Filter values
            const searchQuery = document.getElementById('table-search')?.value.toLowerCase() || '';
            const filterJenis = document.getElementById('filter-jenis')?.value || '';
            const filterStatus = document.getElementById('filter-status')?.value || '';

            const filteredData = dataIzin.filter(item => {
                const matchesSearch = item.name.toLowerCase().includes(searchQuery) || item.keterangan.toLowerCase().includes(searchQuery);
                const matchesJenis = !filterJenis || item.jenis === filterJenis;
                const matchesStatus = !filterStatus || item.status === filterStatus;
                return matchesSearch && matchesJenis && matchesStatus;
            });

            // Update stats
            updateStats();

            if (filteredData.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <i data-lucide="calendar-x-2" class="w-8 h-8 text-slate-350 dark:text-slate-600 mb-1"></i>
                                <p class="font-semibold text-xs">Tidak ada data pengajuan</p>
                                <p class="text-[11px] opacity-70">Gunakan kata kunci lain atau ubah filter Anda.</p>
                            </div>
                        </td>
                    </tr>
                `;
                lucide.createIcons();
                return;
            }

            filteredData.forEach((item, index) => {
                // Badges builder
                let jenisBadge = '';
                if (item.jenis === 'Cuti') {
                    jenisBadge = `<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 border border-blue-150 dark:border-blue-900/40">Cuti</span>`;
                } else if (item.jenis === 'Izin') {
                    jenisBadge = `<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-455 border border-amber-150 dark:border-amber-900/40">Izin</span>`;
                } else if (item.jenis === 'Sakit') {
                    jenisBadge = `<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-150 dark:border-rose-900/40">Sakit</span>`;
                } else {
                    jenisBadge = `<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800">Dispensasi</span>`;
                }

                let statusBadge = '';
                if (item.status === 'Pending') {
                    statusBadge = `
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 border border-amber-200/50 dark:border-amber-800/40 shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            Pending
                        </span>
                    `;
                } else if (item.status === 'Disetujui') {
                    statusBadge = `
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-450 border border-emerald-200/50 dark:border-emerald-800/40 shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Disetujui
                        </span>
                    `;
                } else {
                    statusBadge = `
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-450 border border-rose-200/50 dark:border-rose-800/40 shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                            Ditolak
                        </span>
                    `;
                }

                // Date formatter
                const startStr = formatDateIndo(item.startDate);
                const endStr = formatDateIndo(item.endDate);
                const dateText = startStr === endStr ? startStr : `${startStr} s/d ${endStr}`;

                const row = document.createElement('tr');
                row.className = 'hover:bg-slate-50/40 dark:hover:bg-slate-900/20 transition-colors';
                row.innerHTML = `
                    <td class="px-6 py-4 font-semibold text-slate-900 dark:text-slate-50">${index + 1}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-xs font-bold text-slate-700 dark:text-slate-350 shrink-0">
                                ${item.name.split(' ').map(n => n[0]).join('').substring(0,2)}
                            </div>
                            <div>
                                <span class="text-xs font-bold tracking-wide uppercase text-slate-900 dark:text-slate-50">${item.name}</span>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">${item.email}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">${jenisBadge}</td>
                    <td class="px-6 py-4 text-slate-600 dark:text-slate-400 font-medium">${dateText}</td>
                    <td class="px-6 py-4 font-bold text-slate-700 dark:text-slate-300">${item.durasi} Hari</td>
                    <td class="px-6 py-4 text-slate-500 dark:text-slate-400 max-w-xs truncate" title="${item.keterangan}">${item.keterangan}</td>
                    <td class="px-6 py-4">${statusBadge}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <button onclick="viewDetails(${item.id})" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-855 rounded-lg text-slate-600 dark:text-slate-455 hover:text-slate-955 dark:hover:text-slate-100 transition-colors cursor-pointer" title="Detail & Approval">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                            <button onclick="deleteRequest(${item.id})" class="p-1.5 hover:bg-red-50 dark:hover:bg-red-955/20 rounded-lg text-red-655 dark:text-red-400 hover:text-red-700 transition-colors cursor-pointer" title="Hapus Data">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </td>
                `;
                tableBody.appendChild(row);
            });

            // Trigger lucide icon compilation
            lucide.createIcons();
        }

        function updateStats() {
            const total = dataIzin.length;
            const pending = dataIzin.filter(i => i.status === 'Pending').length;
            const approved = dataIzin.filter(i => i.status === 'Disetujui').length;
            const rejected = dataIzin.filter(i => i.status === 'Ditolak').length;

            document.getElementById('stat-total').innerText = total;
            document.getElementById('stat-pending').innerText = pending;
            document.getElementById('stat-approved').innerText = approved;
            document.getElementById('stat-rejected').innerText = rejected;
        }

        function setupSearchFilters() {
            const searchInput = document.getElementById('table-search');
            const filterJenis = document.getElementById('filter-jenis');
            const filterStatus = document.getElementById('filter-status');

            if (searchInput) searchInput.addEventListener('input', renderTable);
            if (filterJenis) filterJenis.addEventListener('change', renderTable);
            if (filterStatus) filterStatus.addEventListener('change', renderTable);
        }

        // Modal triggers
        function openAddModal() {
            const modal = document.getElementById('add-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            // Set default date range to tomorrow
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            const tomorrowStr = tomorrow.toISOString().split('T')[0];
            document.getElementById('form-start-date').value = tomorrowStr;
            document.getElementById('form-end-date').value = tomorrowStr;
        }

        function closeAddModal() {
            const modal = document.getElementById('add-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('add-form').reset();
        }

        function submitForm(event) {
            event.preventDefault();
            const name = document.getElementById('form-pegawai').value;
            const jenis = document.getElementById('form-jenis').value;
            const startDate = document.getElementById('form-start-date').value;
            const endDate = document.getElementById('form-end-date').value;
            const keterangan = document.getElementById('form-keterangan').value;
            const fileInput = document.getElementById('form-file');
            
            let fileAttachment = null;
            if (fileInput.files.length > 0) {
                fileAttachment = fileInput.files[0].name;
            }

            // Calc duration
            const sDate = new Date(startDate);
            const eDate = new Date(endDate);
            const diffTime = Math.abs(eDate - sDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

            if (eDate < sDate) {
                alert("Tanggal selesai tidak boleh sebelum tanggal mulai!");
                return;
            }

            let email = "pegawai@example.com";
            if (name === "MUHAMMAD AKBAR AMIN") email = "14@example.com";
            if (name === "AHMAD FAJAR ARIF") email = "15@example.com";
            if (name === "MELINDA") email = "10@example.com";

            currentId++;
            dataIzin.unshift({
                id: currentId,
                name: name,
                email: email,
                jenis: jenis,
                startDate: startDate,
                endDate: endDate,
                durasi: diffDays,
                keterangan: keterangan,
                status: "Pending",
                lampiran: fileAttachment
            });

            renderTable();
            closeAddModal();
        }

        function viewDetails(id) {
            const item = dataIzin.find(i => i.id === id);
            if (!item) return;

            selectedRequestId = id;
            document.getElementById('det-pegawai').innerText = item.name;
            
            // badge jenis
            const jBadge = document.getElementById('det-jenis-badge');
            jBadge.className = 'text-xs font-semibold';
            jBadge.innerText = item.jenis;
            if (item.jenis === 'Cuti') jBadge.className = 'inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 dark:bg-blue-950/45 text-blue-700 dark:text-blue-400 border border-blue-150 dark:border-blue-900/40';
            else if (item.jenis === 'Izin') jBadge.className = 'inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 dark:bg-amber-950/45 text-amber-700 dark:text-amber-455 border border-amber-150 dark:border-amber-900/40';
            else if (item.jenis === 'Sakit') jBadge.className = 'inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 dark:bg-rose-950/45 text-rose-700 dark:text-rose-400 border border-rose-150 dark:border-rose-900/40';
            else jBadge.className = 'inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800';

            // date range
            const startStr = formatDateIndo(item.startDate);
            const endStr = formatDateIndo(item.endDate);
            document.getElementById('det-tanggal').innerText = startStr === endStr ? startStr : `${startStr} s/d ${endStr}`;
            document.getElementById('det-durasi').innerText = `${item.durasi} Hari`;
            document.getElementById('det-keterangan').innerText = item.keterangan;

            // attachment
            const lampiranContainer = document.getElementById('det-lampiran');
            if (item.lampiran) {
                lampiranContainer.innerHTML = `<a href="#" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-semibold hover:underline"><i data-lucide="paperclip" class="w-3.5 h-3.5"></i> ${item.lampiran}</a>`;
            } else {
                lampiranContainer.innerHTML = `<span class="inline-flex items-center gap-1 text-slate-400 dark:text-slate-500 italic">Tidak ada lampiran</span>`;
            }

            // status badge
            const sBadge = document.getElementById('det-status-badge');
            sBadge.innerText = item.status;
            if (item.status === 'Pending') {
                sBadge.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 border border-amber-200/50 dark:border-amber-800/40';
                document.getElementById('approval-actions').classList.remove('hidden');
                document.getElementById('approval-actions').classList.add('flex');
                document.getElementById('close-only-action').classList.add('hidden');
            } else {
                if (item.status === 'Disetujui') {
                    sBadge.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-450 border border-emerald-200/50 dark:border-emerald-800/40';
                } else {
                    sBadge.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-450 border border-rose-200/50 dark:border-rose-800/40';
                }
                document.getElementById('approval-actions').classList.add('hidden');
                document.getElementById('approval-actions').classList.remove('flex');
                document.getElementById('close-only-action').classList.remove('hidden');
            }

            // Show modal
            const modal = document.getElementById('detail-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            lucide.createIcons();
        }

        function closeDetailModal() {
            const modal = document.getElementById('detail-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            selectedRequestId = null;
        }

        function approveRequest(isApproved) {
            if (!selectedRequestId) return;
            const item = dataIzin.find(i => i.id === selectedRequestId);
            if (item) {
                item.status = isApproved ? 'Disetujui' : 'Ditolak';
                renderTable();
                closeDetailModal();
            }
        }

        function deleteRequest(id) {
            if (confirm("Apakah Anda yakin ingin menghapus pengajuan ini?")) {
                dataIzin = dataIzin.filter(i => i.id !== id);
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
