<x-admin-layout>
    <div class="p-6 space-y-6">

        <!-- HEADER -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Tipe Pegawai / Peran</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Atur tipe pegawai dan peran dinamis (seperti Guru, Karyawan, Satpam, Staf Administrasi) untuk klasifikasi data pegawai.</p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <button onclick="toggleModal('add-type-modal')" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-900 dark:bg-slate-50 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-all duration-150 cursor-pointer">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    Tambah Tipe Baru
                </button>
            </div>
        </section>

        <!-- TABLE SECTION -->
        <section class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden w-full">
            <div class="overflow-x-auto">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-16">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Tipe</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kode/Slug</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-900">
                        @forelse($employeeTypes as $index => $type)
                            <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-900/20 transition-colors">
                                <td class="px-6 py-4 text-slate-900 dark:text-slate-50 font-medium">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <span class="text-slate-900 dark:text-slate-50 font-bold tracking-tight block">{{ $type->name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300 font-mono text-[10px]">{{ $type->code }}</span>
                                </td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-400 max-w-xs truncate">{{ $type->description ?? '-' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if(in_array($type->code, ['teacher', 'employee', 'management']))
                                            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold px-2 py-0.5 bg-slate-50 dark:bg-slate-900/60 rounded border border-slate-200/50 dark:border-slate-800" title="Tipe bawaan sistem tidak dapat diedit/dihapus">Sistem</span>
                                        @else
                                            <button onclick="editType({{ json_encode($type) }})" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-900 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 transition-colors cursor-pointer" title="Edit Tipe">
                                                <i data-lucide="edit" class="w-4 h-4"></i>
                                            </button>
                                            <button onclick="deleteType('{{ $type->id }}', '{{ $type->name }}')" class="p-1.5 hover:bg-red-50 dark:hover:bg-red-955/20 rounded-lg text-red-600 dark:text-red-400 hover:text-red-700 transition-colors cursor-pointer" title="Hapus Tipe">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-slate-500 dark:text-slate-400">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <i data-lucide="users" class="w-8 h-8 text-slate-300 dark:text-slate-700"></i>
                                        <p class="font-medium text-xs">Belum ada tipe pegawai terdaftar. Tambahkan data baru di atas.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- ADD MODAL -->
        <div x-data><template x-teleport="body">
        <div id="add-type-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs hidden transition-opacity overflow-y-auto" style="margin-top: 0px !important; z-index: 9999;" onclick="if(event.target === this) toggleModal('add-type-modal')">
            <div class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl w-full max-w-md overflow-hidden transform transition-all scale-95 opacity-0 duration-200">
                <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-50">Tambah Tipe Pegawai</h3>
                    <button onclick="toggleModal('add-type-modal')" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg cursor-pointer">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                <form action="{{ route('employee-types.store') }}" method="POST" class="p-5 space-y-4 text-left">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-650 dark:text-slate-400 mb-1">Nama Tipe <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required placeholder="Contoh: Guru Tetap, Satpam" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-650 dark:text-slate-400 mb-1">Kode / Slug (Opsional)</label>
                        <input type="text" name="code" placeholder="Contoh: guru-tetap (auto-generated jika kosong)" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-650 dark:text-slate-400 mb-1">Deskripsi</label>
                        <textarea name="description" placeholder="Penjelasan singkat..." rows="3" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none"></textarea>
                    </div>
                    <div class="p-5 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-2.5">
                        <button type="button" onclick="toggleModal('add-type-modal')" class="px-4 py-2 border border-slate-200 dark:border-slate-850 text-slate-700 dark:text-slate-300 bg-transparent text-xs font-bold rounded-lg cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-slate-900 dark:bg-slate-50 text-white dark:text-slate-900 text-xs font-bold rounded-lg cursor-pointer">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
        </template></div>

        <!-- EDIT MODAL -->
        <div x-data><template x-teleport="body">
        <div id="edit-type-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs hidden transition-opacity overflow-y-auto" style="margin-top: 0px !important; z-index: 9999;" onclick="if(event.target === this) toggleModal('edit-type-modal')">
            <div class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl w-full max-w-md overflow-hidden transform transition-all scale-95 opacity-0 duration-200">
                <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-50">Edit Tipe Pegawai</h3>
                    <button onclick="toggleModal('edit-type-modal')" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg cursor-pointer">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                <form id="edit-type-form" action="" method="POST" class="p-5 space-y-4 text-left">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold text-slate-650 dark:text-slate-400 mb-1">Nama Tipe <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-650 dark:text-slate-400 mb-1">Kode / Slug</label>
                        <input type="text" name="code" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-650 dark:text-slate-400 mb-1">Deskripsi</label>
                        <textarea name="description" rows="3" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none"></textarea>
                    </div>
                    <div class="p-5 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-2.5">
                        <button type="button" onclick="toggleModal('edit-type-modal')" class="px-4 py-2 border border-slate-200 dark:border-slate-850 text-slate-700 dark:text-slate-300 bg-transparent text-xs font-bold rounded-lg cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-slate-900 dark:bg-slate-50 text-white dark:text-slate-900 text-xs font-bold rounded-lg cursor-pointer">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
        </template></div>

        <!-- DELETE FORM -->
        <form id="delete-type-form" action="" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

    </div>

    <!-- SCRIPTS -->
    <script>
        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
            const content = modal.firstElementChild;
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.style.opacity = '1';
                    content.style.opacity = '1';
                    content.style.transform = 'scale(1)';
                }, 50);
            } else {
                content.style.opacity = '0';
                content.style.transform = 'scale(0.95)';
                modal.style.opacity = '0';
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 200);
            }
        }

        function editType(type) {
            const form = document.getElementById('edit-type-form');
            form.action = '/employee-types/' + type.id;
            form.querySelector('[name="name"]').value = type.name;
            form.querySelector('[name="code"]').value = type.code;
            form.querySelector('[name="description"]').value = type.description || '';
            
            toggleModal('edit-type-modal');
        }

        function deleteType(id, name) {
            if (confirm('Apakah Anda yakin ingin menghapus tipe pegawai "' + name + '"? Semua pegawai dengan tipe ini akan diset ke tipe kosong.')) {
                const form = document.getElementById('delete-type-form');
                form.action = '/employee-types/' + id;
                form.submit();
            }
        }
    </script>

    <!-- TOAST NOTIFICATION SCRIPT -->
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const toast = document.getElementById('toast-notification');
                if (toast) {
                    const toastText = toast.querySelector('p');
                    if (toastText) {
                        toastText.textContent = "{{ session('success') }}";
                    }
                    
                    toast.classList.remove('hidden');
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(20px)';
                    
                    setTimeout(() => {
                        toast.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                        toast.style.opacity = '1';
                        toast.style.transform = 'translateY(0)';
                    }, 50);

                    setTimeout(() => {
                        toast.style.opacity = '0';
                        toast.style.transform = 'translateY(20px)';
                        setTimeout(() => {
                            toast.classList.add('hidden');
                        }, 300);
                    }, 5000);
                }
            });
        </script>
    @endif
</x-admin-layout>
