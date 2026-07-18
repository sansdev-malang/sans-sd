<x-admin-layout>
    <div class="p-6 space-y-6">

        <!-- GREETING / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Manajemen Karyawan</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Halaman ini digunakan untuk mengelola data akun
                    karyawan, guru, dan staff.</p>
            </div>
            <div class="flex items-center gap-2.5 shrink-0">
                <button
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-855 text-slate-700 dark:text-slate-355 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm transition-all duration-150 cursor-pointer">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-slate-500"></i>
                    Tarik Karyawan
                </button>
                <button
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-900 dark:bg-slate-50 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-all duration-150 cursor-pointer">
                    <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
                    Tambah Manual
                </button>
            </div>
        </section>

        <!-- MAIN TABLE CARD -->
        <section
            class="animate-card bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden transition-all w-full p-6 space-y-6">

            <!-- Search & Filters Container -->
            <div class="flex flex-col md:flex-row items-end justify-between w-full">
                <!-- Search Box -->
                <div class="space-y-1.5 w-full md:flex-1 md:max-w-md">
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Cari Karyawan</label>
                    <div class="relative w-full">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i data-lucide="search" class="w-4 h-4 text-slate-400 dark:text-slate-550"></i>
                        </span>
                        <input type="text" id="table-search" placeholder="Nama, Email, atau NIK..."
                            style="padding-left: 2.25rem;"
                            class="w-full h-9 pr-4 text-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 focus:border-slate-400 dark:focus:border-slate-600 text-slate-900 dark:text-slate-50 placeholder-slate-400 dark:placeholder-slate-500 transition-all shadow-inner">
                    </div>
                </div>

                <!-- Filter Row -->
                <div class="flex flex-row items-end gap-2.5 w-full md:w-auto">
                    <!-- Filter Jabatan -->
                    <div class="space-y-1.5 flex-1 sm:flex-initial">
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Filter Jabatan</label>
                        <select id="filter-jabatan"
                            class="h-9 px-3 w-full sm:w-40 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 cursor-pointer transition-all shadow-sm">
                            <option value="">Semua Jabatan</option>
                            <option value="-">-</option>
                            <option value="Guru">Guru</option>
                            <option value="Staff">Staff</option>
                        </select>
                    </div>

                    <!-- Filter Role -->
                    <div class="space-y-1.5 flex-1 sm:flex-initial">
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">Filter Role</label>
                        <select id="filter-role"
                            class="h-9 px-3 w-full sm:w-40 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 cursor-pointer transition-all shadow-sm">
                            <option value="">Semua Role</option>
                            <option value="User">User</option>
                            <option value="Admin">Admin</option>
                            <option value="HR">HR</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Table Container -->
            <div class="overflow-x-auto border border-slate-100 dark:border-slate-900 rounded-xl">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30">
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-16">
                                No</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider">
                                Nama / Email</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-40">
                                Jabatan</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-44">
                                UID ZKTeco</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-36">
                                Role</th>
                            <th
                                class="px-6 py-4 text-right text-xs font-semibold text-slate-550 dark:text-slate-400 uppercase tracking-wider w-24">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-900">
                        <!-- Row 1 -->
                        <tr class="employee-row hover:bg-slate-50/40 dark:hover:bg-slate-900/20 transition-colors">
                            <td class="px-6 py-4 text-slate-900 dark:text-slate-50 font-medium">1</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-xs font-bold text-slate-700 dark:text-slate-350 shrink-0">
                                        N
                                    </div>
                                    <div>
                                        <span
                                            class="employee-name text-xs font-bold tracking-wide uppercase text-slate-900 dark:text-slate-50">NURAGA
                                            ALAM</span>
                                        <p class="employee-email text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                            13@example.com</p>
                                    </div>
                                </div>
                            </td>
                            <td class="employee-jabatan px-6 py-4 text-slate-600 dark:text-slate-400">-</td>
                            <td class="px-6 py-4">
                                <span
                                    class="employee-uid px-2.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-mono">UID:
                                    13</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="employee-role text-xs text-slate-600 dark:text-slate-400">User</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button
                                        class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-855 rounded-lg text-slate-600 dark:text-slate-455 hover:text-slate-955 dark:hover:text-slate-100 transition-colors cursor-pointer"
                                        title="Edit Data">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </button>
                                    <button
                                        class="p-1.5 hover:bg-red-55 dark:hover:bg-red-955/20 rounded-lg text-red-655 dark:text-red-400 hover:text-red-700 transition-colors cursor-pointer"
                                        title="Hapus Data">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Row 2 -->
                        <tr class="employee-row hover:bg-slate-50/40 dark:hover:bg-slate-900/20 transition-colors">
                            <td class="px-6 py-4 text-slate-900 dark:text-slate-50 font-medium">2</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-xs font-bold text-slate-700 dark:text-slate-350 shrink-0">
                                        M
                                    </div>
                                    <div>
                                        <span
                                            class="employee-name text-xs font-bold tracking-wide uppercase text-slate-900 dark:text-slate-50">MUHAMMAD
                                            AKBAR AMIN</span>
                                        <p class="employee-email text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                            14@example.com</p>
                                    </div>
                                </div>
                            </td>
                            <td class="employee-jabatan px-6 py-4 text-slate-600 dark:text-slate-400">-</td>
                            <td class="px-6 py-4">
                                <span
                                    class="employee-uid px-2.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-mono">UID:
                                    14</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="employee-role text-xs text-slate-600 dark:text-slate-400">User</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button
                                        class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-855 rounded-lg text-slate-600 dark:text-slate-455 hover:text-slate-955 dark:hover:text-slate-100 transition-colors cursor-pointer"
                                        title="Edit Data">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </button>
                                    <button
                                        class="p-1.5 hover:bg-red-55 dark:hover:bg-red-955/20 rounded-lg text-red-655 dark:text-red-400 hover:text-red-700 transition-colors cursor-pointer"
                                        title="Hapus Data">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Row 3 -->
                        <tr class="employee-row hover:bg-slate-50/40 dark:hover:bg-slate-900/20 transition-colors">
                            <td class="px-6 py-4 text-slate-900 dark:text-slate-50 font-medium">3</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-xs font-bold text-slate-700 dark:text-slate-350 shrink-0">
                                        A
                                    </div>
                                    <div>
                                        <span
                                            class="employee-name text-xs font-bold tracking-wide uppercase text-slate-900 dark:text-slate-50">AHMAD
                                            FAJAR ARIF</span>
                                        <p class="employee-email text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                            15@example.com</p>
                                    </div>
                                </div>
                            </td>
                            <td class="employee-jabatan px-6 py-4 text-slate-600 dark:text-slate-400">-</td>
                            <td class="px-6 py-4">
                                <span
                                    class="employee-uid px-2.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-mono">UID:
                                    15</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="employee-role text-xs text-slate-600 dark:text-slate-400">User</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button
                                        class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-855 rounded-lg text-slate-600 dark:text-slate-455 hover:text-slate-955 dark:hover:text-slate-100 transition-colors cursor-pointer"
                                        title="Edit Data">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </button>
                                    <button
                                        class="p-1.5 hover:bg-red-55 dark:hover:bg-red-955/20 rounded-lg text-red-655 dark:text-red-400 hover:text-red-700 transition-colors cursor-pointer"
                                        title="Hapus Data">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Row 4 -->
                        <tr class="employee-row hover:bg-slate-50/40 dark:hover:bg-slate-900/20 transition-colors">
                            <td class="px-6 py-4 text-slate-900 dark:text-slate-50 font-medium">4</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-xs font-bold text-slate-700 dark:text-slate-350 shrink-0">
                                        M
                                    </div>
                                    <div>
                                        <span
                                            class="employee-name text-xs font-bold tracking-wide uppercase text-slate-900 dark:text-slate-50">MELINDA</span>
                                        <p class="employee-email text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                            10@example.com</p>
                                    </div>
                                </div>
                            </td>
                            <td class="employee-jabatan px-6 py-4 text-slate-600 dark:text-slate-400">-</td>
                            <td class="px-6 py-4">
                                <span
                                    class="employee-uid px-2.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-mono">UID:
                                    10</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="employee-role text-xs text-slate-600 dark:text-slate-400">User</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button
                                        class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-855 rounded-lg text-slate-600 dark:text-slate-455 hover:text-slate-955 dark:hover:text-slate-100 transition-colors cursor-pointer"
                                        title="Edit Data">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </button>
                                    <button
                                        class="p-1.5 hover:bg-red-55 dark:hover:bg-red-955/20 rounded-lg text-red-655 dark:text-red-400 hover:text-red-700 transition-colors cursor-pointer"
                                        title="Hapus Data">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Row 5 -->
                        <tr class="employee-row hover:bg-slate-50/40 dark:hover:bg-slate-900/20 transition-colors">
                            <td class="px-6 py-4 text-slate-900 dark:text-slate-50 font-medium">5</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-xs font-bold text-slate-700 dark:text-slate-350 shrink-0">
                                        F
                                    </div>
                                    <div>
                                        <span
                                            class="employee-name text-xs font-bold tracking-wide uppercase text-slate-900 dark:text-slate-50">FAIZAL</span>
                                        <p class="employee-email text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                            11@example.com</p>
                                    </div>
                                </div>
                            </td>
                            <td class="employee-jabatan px-6 py-4 text-slate-600 dark:text-slate-400">-</td>
                            <td class="px-6 py-4">
                                <span
                                    class="employee-uid px-2.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-mono">UID:
                                    11</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="employee-role text-xs text-slate-600 dark:text-slate-400">User</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button
                                        class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-855 rounded-lg text-slate-600 dark:text-slate-455 hover:text-slate-955 dark:hover:text-slate-100 transition-colors cursor-pointer"
                                        title="Edit Data">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </button>
                                    <button
                                        class="p-1.5 hover:bg-red-55 dark:hover:bg-red-955/20 rounded-lg text-red-655 dark:text-red-400 hover:text-red-700 transition-colors cursor-pointer"
                                        title="Hapus Data">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div
                class="px-6 py-4 border-t border-slate-200 dark:border-slate-800/80 bg-slate-50/20 dark:bg-slate-900/10 flex items-center justify-between gap-4">
                <span class="text-xs text-slate-500 dark:text-slate-400">
                    Menampilkan <span class="font-semibold text-slate-700 dark:text-slate-300">1-5</span> dari <span
                        class="font-semibold text-slate-700 dark:text-slate-300">5</span> karyawan
                </span>

                <div class="flex items-center gap-1.5">
                    <button style="width: 36px; height: 36px; border-radius: 8px;"
                        class="inline-flex items-center justify-center text-xs font-semibold text-slate-550 dark:text-slate-400 border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 hover:bg-slate-50 dark:hover:bg-slate-900 transition-all disabled:opacity-50 cursor-pointer shadow-sm"
                        disabled>
                        <i data-lucide="chevron-left" class="w-4 h-4"></i>
                    </button>
                    <button style="width: 36px; height: 36px; border-radius: 8px;"
                        class="inline-flex items-center justify-center text-xs font-bold text-white dark:text-slate-900 bg-slate-900 dark:bg-slate-50 shadow-sm">1</button>
                    <button style="width: 36px; height: 36px; border-radius: 8px;"
                        class="inline-flex items-center justify-center text-xs font-semibold text-slate-700 dark:text-slate-355 border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 hover:bg-slate-50 dark:hover:bg-slate-900 transition-all cursor-pointer shadow-sm">2</button>
                    <button style="width: 36px; height: 36px; border-radius: 8px;"
                        class="inline-flex items-center justify-center text-xs font-semibold text-slate-700 dark:text-slate-355 border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 hover:bg-slate-50 dark:hover:bg-slate-900 transition-all cursor-pointer shadow-sm">3</button>
                    <button style="width: 36px; height: 36px; border-radius: 8px;"
                        class="inline-flex items-center justify-center text-xs font-semibold text-slate-550 dark:text-slate-400 border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 hover:bg-slate-50 dark:hover:bg-slate-900 transition-all cursor-pointer shadow-sm">
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </section>

    </div>

    <!-- Client-side real-time Search & Filter Javascript -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('table-search');
            const filterJabatan = document.getElementById('filter-jabatan');
            const filterRole = document.getElementById('filter-role');
            const rows = document.querySelectorAll('.employee-row');

            function filterTable() {
                const searchQuery = searchInput.value.toLowerCase().trim();
                const jabatanQuery = filterJabatan.value.toLowerCase();
                const roleQuery = filterRole.value.toLowerCase();

                rows.forEach(row => {
                    const name = row.querySelector('.employee-name').textContent.toLowerCase();
                    const email = row.querySelector('.employee-email').textContent.toLowerCase();
                    const jabatan = row.querySelector('.employee-jabatan').textContent.toLowerCase();
                    const role = row.querySelector('.employee-role').textContent.toLowerCase();

                    const matchesSearch = name.includes(searchQuery) || email.includes(searchQuery);
                    const matchesJabatan = !jabatanQuery || jabatan.includes(jabatanQuery);
                    const matchesRole = !roleQuery || role.includes(roleQuery);

                    if (matchesSearch && matchesJabatan && matchesRole) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            if (searchInput) searchInput.addEventListener('input', filterTable);
            if (filterJabatan) filterJabatan.addEventListener('change', filterTable);
            if (filterRole) filterRole.addEventListener('change', filterTable);
        });
    </script>
</x-admin-layout>