<x-admin-layout>
    <div class="p-4 sm:p-5 lg:p-6 space-y-4 lg:space-y-5" x-data="subjectApp()">

        <!-- HEADER / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3.5 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <div class="flex items-center gap-2.5">
                    <div class="p-2 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-xl border border-indigo-500/20">
                        <i data-lucide="book-open" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold tracking-tight text-slate-900 dark:text-slate-50">
                            Master Mata Pelajaran
                        </h2>
                        <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Pengelolaan daftar mata pelajaran resmi unit sekolah sebagai dasar penugasan guru mengajar dan rapor.
                        </p>
                    </div>
                </div>
            </div>

            <!-- ACTION CONTROLS -->
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <a href="{{ route('homeroom-assignments.index') }}"
                    class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 shadow-xs transition-all">
                    <i data-lucide="user-check" class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400"></i>
                    Lihat Penugasan Guru
                </a>

                <button type="button" @click="openCreateModal()"
                    class="inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all cursor-pointer">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    Tambah Mata Pelajaran
                </button>
            </div>
        </section>

        <!-- STATS CARDS GRID -->
        <section class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3 lg:gap-3.5">
            <!-- Stat 1: Total Mapel -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 shadow-xs flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-100 dark:border-indigo-900/40">
                    <i data-lucide="book-marked" class="w-5 h-5"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10.5px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Total Mapel</p>
                    <div class="flex items-baseline gap-1.5 mt-0.5">
                        <h3 class="text-xl font-bold tracking-tight text-slate-900 dark:text-slate-50 font-mono">
                            {{ $stats['total'] ?? 0 }}
                        </h3>
                        <span class="text-[10px] text-slate-400 font-medium">Mapel</span>
                    </div>
                </div>
            </div>

            <!-- Stat 2: Mapel Wajib -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 shadow-xs flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 border border-blue-100 dark:border-blue-900/40">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10.5px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Kelompok Wajib</p>
                    <div class="flex items-baseline gap-1.5 mt-0.5">
                        <h3 class="text-xl font-bold tracking-tight text-blue-600 dark:text-blue-400 font-mono">
                            {{ $stats['wajib'] ?? 0 }}
                        </h3>
                        <span class="text-[10px] text-slate-400 font-medium">Mapel</span>
                    </div>
                </div>
            </div>

            <!-- Stat 3: Keislaman -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 shadow-xs flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-100 dark:border-emerald-900/40">
                    <i data-lucide="heart" class="w-5 h-5"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10.5px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Keislaman</p>
                    <div class="flex items-baseline gap-1.5 mt-0.5">
                        <h3 class="text-xl font-bold tracking-tight text-emerald-600 dark:text-emerald-400 font-mono">
                            {{ $stats['keislaman'] ?? 0 }}
                        </h3>
                        <span class="text-[10px] text-slate-400 font-medium">Mapel</span>
                    </div>
                </div>
            </div>

            <!-- Stat 4: Muatan Lokal -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-3.5 shadow-xs flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 border border-amber-100 dark:border-amber-900/40">
                    <i data-lucide="sparkles" class="w-5 h-5"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10.5px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Muatan Lokal</p>
                    <div class="flex items-baseline gap-1.5 mt-0.5">
                        <h3 class="text-xl font-bold tracking-tight text-amber-600 dark:text-amber-400 font-mono">
                            {{ $stats['mulok'] ?? 0 }}
                        </h3>
                        <span class="text-[10px] text-slate-400 font-medium">Mapel</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- FILTER TOOLBAR -->
        <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 shadow-xs">
            <form method="GET" action="{{ route('subjects.index') }}" class="flex flex-col sm:flex-row gap-2.5 items-stretch sm:items-center justify-between">
                <div class="flex flex-wrap items-center gap-2 flex-1">
                    <!-- Filter Kelompok -->
                    <div class="min-w-[160px]">
                        <select name="group" onchange="this.form.submit()"
                            class="w-full text-xs font-medium rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950 text-slate-700 dark:text-slate-200 py-1.5 px-2.5 focus:ring-1 focus:ring-indigo-500">
                            <option value="all" {{ ($group ?? 'all') === 'all' ? 'selected' : '' }}>Semua Kelompok</option>
                            <option value="Wajib" {{ ($group ?? '') === 'Wajib' ? 'selected' : '' }}>Wajib</option>
                            <option value="Keislaman" {{ ($group ?? '') === 'Keislaman' ? 'selected' : '' }}>Keislaman</option>
                            <option value="Muatan Lokal" {{ ($group ?? '') === 'Muatan Lokal' ? 'selected' : '' }}>Muatan Lokal</option>
                            <option value="Pilihan" {{ ($group ?? '') === 'Pilihan' ? 'selected' : '' }}>Pilihan</option>
                        </select>
                    </div>

                    <!-- Search -->
                    <div class="relative flex-1 min-w-[200px]">
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama atau kode mapel..."
                            class="w-full text-xs rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950 text-slate-700 dark:text-slate-200 py-1.5 pl-8 pr-2.5 focus:ring-1 focus:ring-indigo-500">
                        <i data-lucide="search" class="w-3.5 h-3.5 absolute left-2.5 top-2.5 text-slate-400"></i>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors">
                        Filter
                    </button>
                    @if(!empty($search) || ($group ?? 'all') !== 'all')
                        <a href="{{ route('subjects.index') }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-semibold rounded-lg transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </section>

        <!-- TABLE -->
        <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-900/50 text-[10px] uppercase font-bold text-slate-500 dark:text-slate-400">
                            <th class="px-3 py-2.5 text-center w-12">Urutan</th>
                            <th class="px-3 py-2.5 w-24">Kode</th>
                            <th class="px-4 py-2.5 min-w-[200px]">Nama Mata Pelajaran</th>
                            <th class="px-3 py-2.5 w-32 text-center">Kelompok</th>
                            <th class="px-3 py-2.5 w-32 text-center">Guru Mengampu</th>
                            <th class="px-3 py-2.5 w-24 text-center">Status</th>
                            <th class="px-3 py-2.5 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium text-slate-700 dark:text-slate-300">
                        @forelse($subjects as $item)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-3 py-2.5 text-center font-bold font-mono text-slate-400">
                                    {{ $item->order }}
                                </td>
                                <td class="px-3 py-2.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md font-mono font-bold text-[11px] bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200">
                                        {{ $item->code }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5">
                                    <div class="font-bold text-slate-900 dark:text-slate-100">{{ $item->name }}</div>
                                    @if($item->description)
                                        <div class="text-[11px] text-slate-400 line-clamp-1">{{ $item->description }}</div>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold
                                        {{ $item->group === 'Keislaman' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' :
                                           ($item->group === 'Wajib' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300' :
                                           'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300') }}">
                                        {{ $item->group }}
                                    </span>
                                </td>
                                <td class="px-3 py-2.5 text-center">
                                    <span class="inline-flex items-center gap-1 font-mono font-bold text-slate-800 dark:text-slate-200">
                                        <i data-lucide="users" class="w-3.5 h-3.5 text-slate-400"></i>
                                        <span>{{ $item->assignments_count }} rombel</span>
                                    </span>
                                </td>
                                <td class="px-3 py-2.5 text-center">
                                    <button type="button" @click="toggleStatus({{ $item->id }})"
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10.5px] font-bold cursor-pointer transition-colors {{ $item->is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $item->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        <span>{{ $item->is_active ? 'Aktif' : 'Non-Aktif' }}</span>
                                    </button>
                                </td>
                                <td class="px-3 py-2.5 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" @click='openEditModal(@json($item))'
                                            class="p-1 rounded-md text-slate-500 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" title="Edit">
                                            <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                        </button>
                                        <form method="POST" action="{{ route('subjects.destroy', $item->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mata pelajaran ini?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 rounded-md text-slate-400 hover:text-rose-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" title="Hapus">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-10 text-center text-slate-400 font-medium">
                                    Belum ada data mata pelajaran yang sesuai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-500 flex justify-between items-center">
                <span>Total: <strong>{{ count($subjects) }}</strong> mata pelajaran</span>
            </div>
        </section>

        <!-- MODAL TAMBAH & EDIT MATA PELAJARAN -->
        <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4" style="display: none;">
            <div class="w-full max-w-md rounded-2xl bg-white p-5 sm:p-6 shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800"
                @click.away="modalOpen = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800">
                    <h3 class="font-extrabold text-sm sm:text-base text-slate-900 dark:text-slate-100" x-text="isEditing ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran'"></h3>
                    <button type="button" @click="modalOpen = false" class="text-slate-400 hover:text-slate-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form :action="isEditing ? '{{ url('subjects') }}/' + editId : '{{ route('subjects.store') }}'" method="POST" class="mt-4 space-y-3.5">
                    @csrf
                    <input type="hidden" name="_method" :value="isEditing ? 'PUT' : 'POST'">

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kode Mapel <span class="text-rose-500">*</span></label>
                            <input type="text" name="code" x-model="formData.code" required placeholder="Contoh: PAI, MTK"
                                class="w-full text-xs font-mono uppercase rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 px-2.5 py-2 text-slate-800 dark:text-slate-100 focus:ring-1 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Urutan Rapor <span class="text-rose-500">*</span></label>
                            <input type="number" name="order" x-model="formData.order" required min="0"
                                class="w-full text-xs font-mono rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 px-2.5 py-2 text-slate-800 dark:text-slate-100 focus:ring-1 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Mata Pelajaran <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" x-model="formData.name" required placeholder="Contoh: Pendidikan Agama Islam dan Budi Pekerti"
                            class="w-full text-xs rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 px-2.5 py-2 text-slate-800 dark:text-slate-100 focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kelompok Mapel <span class="text-rose-500">*</span></label>
                            <select name="group" x-model="formData.group" required
                                class="w-full text-xs rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 px-2.5 py-2 text-slate-800 dark:text-slate-100 focus:ring-1 focus:ring-indigo-500">
                                <option value="Wajib">Wajib (Umum)</option>
                                <option value="Keislaman">Keislaman</option>
                                <option value="Muatan Lokal">Muatan Lokal</option>
                                <option value="Pilihan">Pilihan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Status Keaktifan</label>
                            <select name="is_active" x-model="formData.is_active" required
                                class="w-full text-xs rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 px-2.5 py-2 text-slate-800 dark:text-slate-100 focus:ring-1 focus:ring-indigo-500">
                                <option value="1">Aktif</option>
                                <option value="0">Non-Aktif</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Keterangan / Deskripsi</label>
                        <textarea name="description" x-model="formData.description" rows="2" placeholder="Catatan opsional..."
                            class="w-full text-xs rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 px-2.5 py-2 text-slate-800 dark:text-slate-100 focus:ring-1 focus:ring-indigo-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="modalOpen = false"
                            class="px-3.5 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-1.5 rounded-lg bg-indigo-600 text-xs font-bold text-white hover:bg-indigo-700 shadow-xs">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        function subjectApp() {
            return {
                modalOpen: false,
                isEditing: false,
                editId: null,
                formData: {
                    code: '',
                    name: '',
                    group: 'Wajib',
                    order: 1,
                    is_active: '1',
                    description: ''
                },
                openCreateModal() {
                    this.isEditing = false;
                    this.editId = null;
                    this.formData = {
                        code: '',
                        name: '',
                        group: 'Wajib',
                        order: 1,
                        is_active: '1',
                        description: ''
                    };
                    this.modalOpen = true;
                    this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
                },
                openEditModal(item) {
                    this.isEditing = true;
                    this.editId = item.id;
                    this.formData = {
                        code: item.code || '',
                        name: item.name || '',
                        group: item.group || 'Wajib',
                        order: item.order || 0,
                        is_active: (item.is_active == 1 || item.is_active === true) ? '1' : '0',
                        description: item.description || ''
                    };
                    this.modalOpen = true;
                    this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
                },
                toggleStatus(id) {
                    fetch(`{{ url('subjects') }}/${id}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        }
                    })
                    .catch(err => console.error(err));
                }
            }
        }
    </script>
    @endpush
</x-admin-layout>

