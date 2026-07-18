<x-admin-layout>
    <div class="p-6 space-y-6">

        <!-- GREETING / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Persetujuan Izin & Cuti (Approval)</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Tinjau, setujui, atau tolak permohonan izin dan cuti pegawai secara efisien.</p>
            </div>
        </section>

        <!-- STATS CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full">
            <!-- Menunggu Keputusan -->
            <div class="bg-white dark:bg-slate-955 p-4 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-lg">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Menunggu Approval</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-slate-50" id="stat-pending">2</p>
                </div>
            </div>
            <!-- Total Diproses -->
            <div class="bg-white dark:bg-slate-955 p-4 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-lg">
                    <i data-lucide="check-square" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Diproses</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-slate-50" id="stat-processed">4</p>
                </div>
            </div>
            <!-- Persentase Persetujuan -->
            <div class="bg-white dark:bg-slate-955 p-4 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg">
                    <i data-lucide="percent" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Rasio Persetujuan</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-slate-50" id="stat-ratio">75%</p>
                </div>
            </div>
        </div>

        <!-- MAIN CONTAINER GRID -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- LEFT: PENDING REQUESTS LIST (2 COLS WIDTH ON LG) -->
            <div class="lg:col-span-2 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-50 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        Perlu Tindakan Segera
                    </h3>
                    <span class="px-2.5 py-0.5 text-[10px] font-bold bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-400 rounded-full" id="pending-count-badge">2 Pengajuan</span>
                </div>

                <div class="space-y-4" id="pending-list-container">
                    <!-- Cards will be populated here -->
                </div>
            </div>

            <!-- RIGHT: RECENTLY PROCESSED HISTORY LOG (1 COL WIDTH) -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-50 flex items-center gap-2">
                    <i data-lucide="history" class="w-4 h-4 text-slate-500"></i>
                    Riwayat Keputusan Terbaru
                </h3>

                <div class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-sm divide-y divide-slate-100 dark:divide-slate-800/80 space-y-3.5" id="processed-list-container">
                    <!-- History log will be populated here -->
                </div>
            </div>

        </div>

    </div>

    <!-- MODAL: DETAIL PENGAJUAN -->
    <div id="detail-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-950/40 dark:bg-slate-950/60 backdrop-blur-sm transition-opacity">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-850 rounded-xl shadow-xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <div class="px-6 py-4 border-b border-slate-150 dark:border-slate-800 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-50">Tinjau Pengajuan</h3>
                <button onclick="closeDetailModal()" class="p-1 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div class="space-y-3">
                    <div class="flex justify-between border-b border-slate-100 dark:border-slate-800/80 pb-2">
                        <span class="text-xs text-slate-550 dark:text-slate-405 font-medium">Pegawai</span>
                        <span class="text-xs text-slate-900 dark:text-slate-50 font-bold" id="det-pegawai">-</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-100 dark:border-slate-800/80 pb-2">
                        <span class="text-xs text-slate-550 dark:text-slate-405 font-medium">Tipe</span>
                        <span id="det-jenis-badge">-</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-100 dark:border-slate-800/80 pb-2">
                        <span class="text-xs text-slate-550 dark:text-slate-405 font-medium">Waktu</span>
                        <span class="text-xs text-slate-900 dark:text-slate-50 font-semibold" id="det-tanggal">-</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-100 dark:border-slate-800/80 pb-2">
                        <span class="text-xs text-slate-550 dark:text-slate-405 font-medium">Durasi</span>
                        <span class="text-xs text-slate-900 dark:text-slate-50 font-bold" id="det-durasi">-</span>
                    </div>
                    <div class="flex flex-col gap-1 border-b border-slate-100 dark:border-slate-800/80 pb-2">
                        <span class="text-xs text-slate-550 dark:text-slate-405 font-medium">Alasan Pengajuan</span>
                        <span class="text-xs text-slate-850 dark:text-slate-200 bg-slate-50 dark:bg-slate-950 p-2.5 rounded-lg border border-slate-100 dark:border-slate-850/80 mt-1 animate-pulse-none" id="det-keterangan">-</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-100 dark:border-slate-800/80 pb-2 items-center">
                        <span class="text-xs text-slate-550 dark:text-slate-405 font-medium">Lampiran Dokumen</span>
                        <span class="text-xs text-slate-900 dark:text-slate-50" id="det-lampiran">
                            <span class="inline-flex items-center gap-1 text-slate-400 italic">Tidak ada lampiran</span>
                        </span>
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-2 border-t border-slate-150 dark:border-slate-800">
                    <button onclick="processRequest(false)" class="flex-1 px-4 py-2 hover:bg-rose-50 dark:hover:bg-rose-950/20 border border-rose-200 dark:border-rose-900/60 text-rose-600 dark:text-rose-400 text-xs font-semibold rounded-lg transition-colors cursor-pointer flex items-center justify-center gap-1.5">
                        <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                        Tolak
                    </button>
                    <button onclick="processRequest(true)" class="flex-1 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-750 dark:hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer flex items-center justify-center gap-1.5">
                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                        Setujui
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- CLIENT SIDE DATA & INTERACTION -->
    <script>
        let approvalData = [
            {
                id: 1,
                name: "MUHAMMAD AKBAR AMIN",
                email: "14@example.com",
                jenis: "Cuti",
                startDate: "2026-07-20",
                endDate: "2026-07-22",
                durasi: 3,
                keterangan: "Cuti tahunan untuk menghadiri acara pernikahan adik kandung di Bandung.",
                status: "Pending",
                lampiran: "undangan_nikah.pdf",
                updatedAt: null
            },
            {
                id: 2,
                name: "MELINDA",
                email: "10@example.com",
                jenis: "Sakit",
                startDate: "2026-07-16",
                endDate: "2026-07-17",
                durasi: 2,
                keterangan: "Sakit radang tenggorokan dan demam, melampirkan surat dokter.",
                status: "Pending",
                lampiran: "surat_dokter.jpg",
                updatedAt: null
            },
            {
                id: 3,
                name: "AHMAD FAJAR ARIF",
                email: "15@example.com",
                jenis: "Izin",
                startDate: "2026-07-12",
                endDate: "2026-07-12",
                durasi: 1,
                keterangan: "Izin menghadiri wisuda anak pertama di Universitas Brawijaya.",
                status: "Disetujui",
                lampiran: "bukti_wisuda.jpg",
                updatedAt: "2026-07-14 09:30"
            },
            {
                id: 4,
                name: "Drs. Eko Wibowo, M.Pd",
                email: "eko.wibowo@example.com",
                jenis: "Cuti",
                startDate: "2026-07-01",
                endDate: "2026-07-05",
                durasi: 5,
                keterangan: "Cuti alasan penting mendesak urusan keluarga.",
                status: "Disetujui",
                lampiran: null,
                updatedAt: "2026-07-01 08:15"
            },
            {
                id: 5,
                name: "Dewi Lestari, S.Pd",
                email: "dewi.lestari@example.com",
                jenis: "Izin",
                startDate: "2026-06-28",
                endDate: "2026-06-28",
                durasi: 1,
                keterangan: "Mengikuti seminar diklat kompetensi tingkat provinsi.",
                status: "Disetujui",
                lampiran: "surat_tugas.pdf",
                updatedAt: "2026-06-27 14:00"
            },
            {
                id: 6,
                name: "Rian Hidayat",
                email: "rian.hidayat@example.com",
                jenis: "Dispensasi",
                startDate: "2026-06-20",
                endDate: "2026-06-20",
                durasi: 1,
                keterangan: "Urusan pribadi mendesak di bank selama jam kerja.",
                status: "Ditolak",
                lampiran: null,
                updatedAt: "2026-06-19 16:30"
            }
        ];

        let selectedId = null;

        document.addEventListener('DOMContentLoaded', () => {
            renderData();
        });

        function renderData() {
            const pendingContainer = document.getElementById('pending-list-container');
            const processedContainer = document.getElementById('processed-list-container');

            pendingContainer.innerHTML = '';
            processedContainer.innerHTML = '';

            const pendings = approvalData.filter(item => item.status === 'Pending');
            const processeds = approvalData.filter(item => item.status !== 'Pending');

            // Update stats counter
            document.getElementById('stat-pending').innerText = pendings.length;
            document.getElementById('stat-processed').innerText = processeds.length;
            document.getElementById('pending-count-badge').innerText = `${pendings.length} Pengajuan`;

            const approvedCount = processeds.filter(item => item.status === 'Disetujui').length;
            const ratio = processeds.length > 0 ? Math.round((approvedCount / processeds.length) * 100) : 100;
            document.getElementById('stat-ratio').innerText = `${ratio}%`;

            // Render Pending list
            if (pendings.length === 0) {
                pendingContainer.innerHTML = `
                    <div class="bg-white dark:bg-slate-950 border border-dashed border-slate-200 dark:border-slate-850 rounded-xl p-12 text-center text-slate-500 dark:text-slate-400">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <i data-lucide="check-circle-2" class="w-10 h-10 text-emerald-500 mb-2"></i>
                            <p class="font-bold text-sm text-slate-800 dark:text-slate-100">Semua Bersih!</p>
                            <p class="text-xs opacity-75">Tidak ada pengajuan izin/cuti yang menunggu persetujuan.</p>
                        </div>
                    </div>
                `;
            } else {
                pendings.forEach(item => {
                    let jBadge = getJenisBadge(item.jenis);
                    
                    const card = document.createElement('div');
                    card.className = "bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-sm space-y-4 hover:border-slate-300 dark:hover:border-slate-700 transition-all";
                    card.innerHTML = `
                        <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-900 flex items-center justify-center text-xs font-bold text-slate-700 dark:text-slate-350 shrink-0 mt-0.5 border border-slate-200/50 dark:border-slate-800/80">
                                    ${item.name.split(' ').map(n => n[0]).join('').substring(0,2)}
                                </div>
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="text-xs font-bold uppercase tracking-wide text-slate-900 dark:text-slate-50">${item.name}</h4>
                                        ${jBadge}
                                    </div>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-405">${item.email}</p>
                                </div>
                            </div>
                            <div class="text-left sm:text-right shrink-0">
                                <p class="text-xs font-bold text-slate-850 dark:text-slate-250">${formatDateIndo(item.startDate)} ${item.startDate !== item.endDate ? 's/d ' + formatDateIndo(item.endDate) : ''}</p>
                                <p class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 mt-0.5">Durasi: <span class="text-slate-700 dark:text-slate-300 font-bold">${item.durasi} Hari</span></p>
                            </div>
                        </div>

                        <div class="bg-slate-50 dark:bg-slate-900/40 p-3 rounded-lg border border-slate-100 dark:border-slate-800/60 text-xs text-slate-650 dark:text-slate-350 line-clamp-2">
                            ${item.keterangan}
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2 border-t border-slate-100 dark:border-slate-900/60">
                            <div>
                                ${item.lampiran ? `
                                    <span class="inline-flex items-center gap-1 text-[11px] text-blue-600 dark:text-blue-400 font-semibold">
                                        <i data-lucide="paperclip" class="w-3 h-3"></i>
                                        ${item.lampiran}
                                    </span>
                                ` : `
                                    <span class="text-[10px] text-slate-400 italic">Tanpa lampiran</span>
                                `}
                            </div>
                            <div class="flex items-center gap-2 self-end sm:self-auto">
                                <button onclick="openDetail(${item.id})" class="px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-[11px] font-semibold rounded-lg transition-colors cursor-pointer">
                                    Tinjau Detail
                                </button>
                                <button onclick="quickProcess(${item.id}, false)" class="p-2 hover:bg-rose-50 dark:hover:bg-rose-950/20 border border-rose-100 dark:border-rose-900/40 text-rose-600 dark:text-rose-400 rounded-lg transition-colors cursor-pointer" title="Tolak">
                                    <i data-lucide="x-circle" class="w-4 h-4"></i>
                                </button>
                                <button onclick="quickProcess(${item.id}, true)" class="p-2 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-750 dark:hover:bg-emerald-700 text-white rounded-lg transition-colors cursor-pointer" title="Setujui">
                                    <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    `;
                    pendingContainer.appendChild(card);
                });
            }

            // Render Processed History List
            if (processeds.length === 0) {
                processedContainer.innerHTML = `<p class="text-xs text-slate-550 dark:text-slate-500 text-center py-6">Belum ada keputusan yang diproses.</p>`;
            } else {
                // Show latest 5 items
                const latestProcessed = processeds.reverse().slice(0, 5);
                latestProcessed.forEach((item, index) => {
                    let sBadge = '';
                    if (item.status === 'Disetujui') {
                        sBadge = `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/30">Disetujui</span>`;
                    } else {
                        sBadge = `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-rose-50 dark:bg-rose-950/20 text-rose-700 dark:text-rose-400 border border-rose-100 dark:border-rose-900/30">Ditolak</span>`;
                    }

                    const row = document.createElement('div');
                    row.className = `py-3 ${index > 0 ? 'border-t border-slate-100 dark:border-slate-800/80' : ''} space-y-1.5`;
                    row.innerHTML = `
                        <div class="flex justify-between items-start gap-2">
                            <div>
                                <h5 class="text-xs font-bold text-slate-850 dark:text-slate-200 line-clamp-1">${item.name}</h5>
                                <p class="text-[10px] text-slate-400 dark:text-slate-500">${item.jenis} • ${item.durasi} Hari</p>
                            </div>
                            ${sBadge}
                        </div>
                        <div class="flex justify-between items-center text-[10px] text-slate-400 dark:text-slate-550">
                            <span>Selesai diproses</span>
                            <span>${item.updatedAt || '-'}</span>
                        </div>
                    `;
                    processedContainer.appendChild(row);
                });
            }

            // Sync icons
            lucide.createIcons();
        }

        function getJenisBadge(jenis) {
            if (jenis === 'Cuti') {
                return `<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 border border-blue-150 dark:border-blue-900/40">Cuti</span>`;
            } else if (jenis === 'Izin') {
                return `<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-455 border border-amber-150 dark:border-amber-900/40">Izin</span>`;
            } else if (jenis === 'Sakit') {
                return `<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-150 dark:border-rose-900/40">Sakit</span>`;
            } else {
                return `<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800">Dispensasi</span>`;
            }
        }

        // Action handles
        function openDetail(id) {
            const item = approvalData.find(i => i.id === id);
            if (!item) return;

            selectedId = id;
            document.getElementById('det-pegawai').innerText = item.name;
            
            const jBadge = document.getElementById('det-jenis-badge');
            jBadge.innerHTML = getJenisBadge(item.jenis);

            const startStr = formatDateIndo(item.startDate);
            const endStr = formatDateIndo(item.endDate);
            document.getElementById('det-tanggal').innerText = startStr === endStr ? startStr : `${startStr} s/d ${endStr}`;
            document.getElementById('det-durasi').innerText = `${item.durasi} Hari`;
            document.getElementById('det-keterangan').innerText = item.keterangan;

            const lampiranContainer = document.getElementById('det-lampiran');
            if (item.lampiran) {
                lampiranContainer.innerHTML = `<a href="#" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-semibold hover:underline"><i data-lucide="paperclip" class="w-3.5 h-3.5"></i> ${item.lampiran}</a>`;
            } else {
                lampiranContainer.innerHTML = `<span class="inline-flex items-center gap-1 text-slate-400 dark:text-slate-500 italic">Tidak ada lampiran</span>`;
            }

            const modal = document.getElementById('detail-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            lucide.createIcons();
        }

        function closeDetailModal() {
            const modal = document.getElementById('detail-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            selectedId = null;
        }

        function processRequest(isApproved) {
            if (!selectedId) return;
            quickProcess(selectedId, isApproved);
            closeDetailModal();
        }

        function quickProcess(id, isApproved) {
            const item = approvalData.find(i => i.id === id);
            if (item) {
                item.status = isApproved ? 'Disetujui' : 'Ditolak';
                
                // Set updated time
                const now = new Date();
                const timeStr = `${now.getFullYear()}-${String(now.getMonth()+1).padStart(2,'0')}-${String(now.getDate()).padStart(2,'0')} ${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
                item.updatedAt = timeStr;

                renderData();
            }
        }

        // Date helper
        function formatDateIndo(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
        }
    </script>
</x-admin-layout>
