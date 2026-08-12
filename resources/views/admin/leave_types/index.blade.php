<x-admin-layout>
    <div class="p-6 space-y-6">

        <!-- HEADER -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Tipe Izin / Cuti</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Atur kategori izin/cuti pegawai secara dinamis beserta pemetaan kode status kehadiran dan bonus.</p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <button onclick="toggleModal('add-type-modal')" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-900 dark:bg-slate-50 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-all duration-100 cursor-pointer">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    Tambah Tipe Baru
                </button>
            </div>
        </section>

        <!-- TABLE SECTION -->
        <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden w-full">
            <div class="overflow-x-auto">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-16">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Tipe</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kode Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Absensi Fisik</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Persetujuan</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Bonus Kehadiran</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-900">
                        @forelse($leaveTypes as $index => $type)
                            <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-900/20 transition-colors">
                                <td class="px-6 py-4 text-slate-900 dark:text-slate-50 font-medium">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <span class="text-slate-900 dark:text-slate-50 font-bold tracking-tight block">{{ $type->name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $badgeClass = 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300';
                                        $label = 'Izin (I)';
                                        if ($type->status_code === 'S') {
                                            $badgeClass = 'bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-400 border border-red-100 dark:border-red-900/30';
                                            $label = 'Sakit (S)';
                                        } elseif ($type->status_code === 'C') {
                                            $badgeClass = 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 border border-amber-100 dark:border-amber-900/30';
                                            $label = 'Cuti (C)';
                                        } elseif ($type->status_code === 'H') {
                                            $badgeClass = 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/30';
                                            $label = 'Hadir (H)';
                                        }
                                    @endphp
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold tracking-wide {{ $badgeClass }}">{{ $label }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($type->requires_attendance)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">Wajib Absen</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/30">Bebas Absen</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($type->requires_approval)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 border border-amber-100 dark:border-amber-900/30">Perlu Persetujuan</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/30">Otomatis Setuju</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($type->gets_presence_bonus)
                                        <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-semibold">
                                            <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                            Dapat Bonus
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-slate-400 dark:text-slate-500">
                                            <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                            Tanpa Bonus
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if(in_array($type->code, ['sakit-pribadi', 'tidak-bekerja', 'cuti-tahunan', 'dinas-luar']))
                                            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold px-2 py-0.5 bg-slate-50 dark:bg-slate-900/60 rounded border border-slate-200/50 dark:border-slate-800" title="Tipe bawaan sistem tidak dapat dihapus, tapi bisa diedit pengaturan persetujuan/absensinya">Sistem</span>
                                            <button onclick="editType({{ json_encode($type) }})" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 transition-colors cursor-pointer" title="Edit Tipe">
                                                <i data-lucide="edit" class="w-4 h-4"></i>
                                            </button>
                                        @else
                                            <button onclick="editType({{ json_encode($type) }})" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 transition-colors cursor-pointer" title="Edit Tipe">
                                                <i data-lucide="edit" class="w-4 h-4"></i>
                                            </button>
                                            <button onclick="deleteType('{{ $type->id }}', '{{ $type->name }}')" class="p-1.5 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg text-red-600 dark:text-red-400 hover:text-red-700 transition-colors cursor-pointer" title="Hapus Tipe">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-slate-500 dark:text-slate-400">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <i data-lucide="info" class="w-8 h-8 text-slate-300 dark:text-slate-700"></i>
                                        <p class="font-medium text-xs">Belum ada tipe izin terdaftar. Tambahkan data baru di atas.</p>
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
        <div id="add-type-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden transition-opacity overflow-y-auto" style="margin-top: 0px !important; z-index: 9999;" onclick="if(event.target === this) toggleModal('add-type-modal')">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl w-full max-w-md overflow-hidden transform transition-all scale-95 opacity-0 duration-200">
                <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-50">Tambah Tipe Izin baru</h3>
                    <button onclick="toggleModal('add-type-modal')" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg cursor-pointer">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                <form action="{{ route('leave-types.store') }}" method="POST" class="p-5 space-y-4 text-left">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Nama Tipe Izin <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required placeholder="Contoh: Izin Kedinasan Khusus" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Kode Status Kehadiran <span class="text-red-500">*</span></label>
                        <select name="status_code" required class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none cursor-pointer">
                            <option value="I">Izin (I)</option>
                            <option value="S">Sakit (S)</option>
                            <option value="C">Cuti (C)</option>
                            <option value="H">Hadir (H)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Absensi Fisik <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-4">
                            <label class="inline-flex items-center gap-2 cursor-pointer text-xs text-slate-700 dark:text-slate-350">
                                <input type="radio" name="requires_attendance" value="1" checked class="w-4 h-4 text-slate-900 dark:text-slate-100 border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 focus:ring-slate-500">
                                <span>Wajib Absen</span>
                            </label>
                            <label class="inline-flex items-center gap-2 cursor-pointer text-xs text-slate-700 dark:text-slate-350">
                                <input type="radio" name="requires_attendance" value="0" class="w-4 h-4 text-slate-900 dark:text-slate-100 border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 focus:ring-slate-500">
                                <span>Bebas Absen</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Persetujuan Pengajuan <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-4">
                            <label class="inline-flex items-center gap-2 cursor-pointer text-xs text-slate-700 dark:text-slate-350">
                                <input type="radio" name="requires_approval" value="1" checked class="w-4 h-4 text-slate-900 dark:text-slate-100 border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 focus:ring-slate-500">
                                <span>Perlu Persetujuan</span>
                            </label>
                            <label class="inline-flex items-center gap-2 cursor-pointer text-xs text-slate-700 dark:text-slate-350">
                                <input type="radio" name="requires_approval" value="0" class="w-4 h-4 text-slate-900 dark:text-slate-100 border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 focus:ring-slate-500">
                                <span>Otomatis Setuju</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Bonus Kehadiran <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-4">
                            <label class="inline-flex items-center gap-2 cursor-pointer text-xs text-slate-700 dark:text-slate-350">
                                <input type="radio" name="gets_presence_bonus" value="1" checked class="w-4 h-4 text-slate-900 dark:text-slate-100 border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 focus:ring-slate-500">
                                <span>Dapat Bonus</span>
                            </label>
                            <label class="inline-flex items-center gap-2 cursor-pointer text-xs text-slate-700 dark:text-slate-350">
                                <input type="radio" name="gets_presence_bonus" value="0" class="w-4 h-4 text-slate-900 dark:text-slate-100 border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 focus:ring-slate-500">
                                <span>Tanpa Bonus</span>
                            </label>
                        </div>
                    </div>
                    <div class="p-5 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-2.5">
                        <button type="button" onclick="toggleModal('add-type-modal')" class="px-4 py-2 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 bg-transparent text-xs font-bold rounded-lg cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-slate-900 dark:bg-slate-50 text-white dark:text-slate-900 text-xs font-bold rounded-lg cursor-pointer">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
        </template></div>

        <!-- EDIT MODAL -->
        <div x-data><template x-teleport="body">
        <div id="edit-type-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden transition-opacity overflow-y-auto" style="margin-top: 0px !important; z-index: 9999;" onclick="if(event.target === this) toggleModal('edit-type-modal')">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl w-full max-w-md overflow-hidden transform transition-all scale-95 opacity-0 duration-200">
                <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-50">Edit Tipe Izin</h3>
                    <button onclick="toggleModal('edit-type-modal')" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg cursor-pointer">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                <form id="edit-type-form" action="" method="POST" class="p-5 space-y-4 text-left">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Nama Tipe Izin <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Kode Status Kehadiran <span class="text-red-500">*</span></label>
                        <select name="status_code" required class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-95 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none cursor-pointer">
                            <option value="I">Izin (I)</option>
                            <option value="S">Sakit (S)</option>
                            <option value="C">Cuti (C)</option>
                            <option value="H">Hadir (H)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Absensi Fisik <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-4">
                            <label class="inline-flex items-center gap-2 cursor-pointer text-xs text-slate-700 dark:text-slate-350">
                                <input type="radio" name="requires_attendance" value="1" class="w-4 h-4 text-slate-900 dark:text-slate-100 border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 focus:ring-slate-500">
                                <span>Wajib Absen</span>
                            </label>
                            <label class="inline-flex items-center gap-2 cursor-pointer text-xs text-slate-700 dark:text-slate-350">
                                <input type="radio" name="requires_attendance" value="0" class="w-4 h-4 text-slate-900 dark:text-slate-100 border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 focus:ring-slate-500">
                                <span>Bebas Absen</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Persetujuan Pengajuan <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-4">
                            <label class="inline-flex items-center gap-2 cursor-pointer text-xs text-slate-700 dark:text-slate-350">
                                <input type="radio" name="requires_approval" value="1" class="w-4 h-4 text-slate-900 dark:text-slate-100 border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 focus:ring-slate-500">
                                <span>Perlu Persetujuan</span>
                            </label>
                            <label class="inline-flex items-center gap-2 cursor-pointer text-xs text-slate-700 dark:text-slate-350">
                                <input type="radio" name="requires_approval" value="0" class="w-4 h-4 text-slate-900 dark:text-slate-100 border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 focus:ring-slate-500">
                                <span>Otomatis Setuju</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Bonus Kehadiran <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-4">
                            <label class="inline-flex items-center gap-2 cursor-pointer text-xs text-slate-700 dark:text-slate-350">
                                <input type="radio" name="gets_presence_bonus" value="1" class="w-4 h-4 text-slate-900 dark:text-slate-100 border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 focus:ring-slate-500">
                                <span>Dapat Bonus</span>
                            </label>
                            <label class="inline-flex items-center gap-2 cursor-pointer text-xs text-slate-700 dark:text-slate-350">
                                <input type="radio" name="gets_presence_bonus" value="0" class="w-4 h-4 text-slate-900 dark:text-slate-100 border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 focus:ring-slate-500">
                                <span>Tanpa Bonus</span>
                            </label>
                        </div>
                    </div>
                    <div class="p-5 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-2.5">
                        <button type="button" onclick="toggleModal('edit-type-modal')" class="px-4 py-2 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 bg-transparent text-xs font-bold rounded-lg cursor-pointer">Batal</button>
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
            form.action = '/leave-types/' + type.id;
            form.querySelector('[name="name"]').value = type.name;
            form.querySelector('[name="status_code"]').value = type.status_code;
            
            // Set radio values
            form.querySelector(`[name="requires_attendance"][value="${type.requires_attendance ? "1" : "0"}"]`).checked = true;
            form.querySelector(`[name="requires_approval"][value="${type.requires_approval ? "1" : "0"}"]`).checked = true;
            form.querySelector(`[name="gets_presence_bonus"][value="${type.gets_presence_bonus ? "1" : "0"}"]`).checked = true;
            
            toggleModal('edit-type-modal');
        }

        function deleteType(id, name) {
            showGlobalConfirmModal('Apakah Anda yakin ingin menghapus tipe izin "' + name + '"? Pastikan tidak ada data pengajuan izin aktif yang menggunakan tipe ini.', function() {
                const form = document.getElementById('delete-type-form');
                form.action = '/leave-types/' + id;
                form.submit();
            });
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
