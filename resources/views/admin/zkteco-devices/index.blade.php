<x-admin-layout>
    <div class="p-6 space-y-6" x-data="{
        showAddModal: false,
        showEditModal: false,
        editId: '',
        editName: '',
        editIp: '',
        editPort: '4370',
        editModelName: 'ZKTeco K40',
        editLocation: '',
        openEditModal(device) {
            this.editId = device.id;
            this.editName = device.name;
            this.editIp = device.ip_address;
            this.editPort = device.port;
            this.editModelName = device.model_name;
            this.editLocation = device.location || '';
            this.showEditModal = true;
        },
        pingDevice(id, event) {
            const btn = event.currentTarget;
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<i data-lucide='loader-2' class='w-3.5 h-3.5 animate-spin'></i> Pinging...`;
            lucide.createIcons();

            const statusTextSpan = document.getElementById(`status-text-${id}`);
            const statusIndicatorSpan = document.getElementById(`status-indicator-${id}`);

            statusTextSpan.innerText = 'Pinging...';
            statusTextSpan.className = 'text-amber-700 dark:text-amber-400';
            statusIndicatorSpan.className = 'w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping';

            fetch(`{{ url('zkteco-devices') }}/${id}/ping`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                lucide.createIcons();

                if (data.is_online) {
                    statusTextSpan.innerText = 'Online';
                    statusTextSpan.className = 'text-emerald-700 dark:text-emerald-400';
                    statusIndicatorSpan.className = 'w-1.5 h-1.5 rounded-full bg-emerald-500';
                    statusIndicatorSpan.parentNode.className = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900/30';
                } else {
                    statusTextSpan.innerText = 'Offline';
                    statusTextSpan.className = 'text-rose-700 dark:text-rose-400';
                    statusIndicatorSpan.className = 'w-1.5 h-1.5 rounded-full bg-rose-500';
                    statusIndicatorSpan.parentNode.className = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-900/30';
                }
                
                // Reload stats count asynchronously
                location.reload();
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                lucide.createIcons();
                statusTextSpan.innerText = 'Offline';
                statusTextSpan.className = 'text-rose-700 dark:text-rose-400';
                statusIndicatorSpan.className = 'w-1.5 h-1.5 rounded-full bg-rose-500';
            });
        }
    }">

        <!-- SUCCESS ALERT -->
        @if(session('success'))
            <div class="bg-emerald-50 dark:bg-emerald-900/40 border border-emerald-200 dark:border-emerald-900/60 rounded-xl p-4 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <i data-lucide="check" class="w-4 h-4"></i>
                </div>
                <div>
                    <h5 class="text-xs font-bold text-slate-800 dark:text-slate-200">Sukses!</h5>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- GREETING / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Mesin & Perangkat Absensi</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Kelola konfigurasi, status koneksi, dan sinkronisasi mesin sidik jari/wajah ZKTeco secara real-time.</p>
            </div>
            <div class="flex items-center gap-2.5 shrink-0">
                <button @click="showAddModal = true"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-900 dark:bg-slate-50 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-all duration-100 cursor-pointer">
                    <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                    Daftarkan Perangkat
                </button>
            </div>
        </section>

        <!-- STATS CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full text-left">
            <!-- Total Perangkat -->
            <div class="bg-white dark:bg-slate-900 p-4 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-400 rounded-lg">
                    <i data-lucide="cpu" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Mesin</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-slate-50">{{ $devices->count() }}</p>
                </div>
            </div>
            <!-- Online -->
            <div class="bg-white dark:bg-slate-900 p-4 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-lg">
                    <i data-lucide="wifi" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Status Terkoneksi</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-slate-50 text-emerald-600 dark:text-emerald-400">{{ $devices->where('is_online', true)->count() }}</p>
                </div>
            </div>
            <!-- Offline -->
            <div class="bg-white dark:bg-slate-900 p-4 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm flex items-center gap-4">
                <div class="p-3 bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-lg">
                    <i data-lucide="wifi-off" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Terputus</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-slate-50 text-rose-600 dark:text-rose-400">{{ $devices->where('is_online', false)->count() }}</p>
                </div>
            </div>
        </div>

        <!-- MAIN MACHINE TABLE CARD -->
        <section class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden transition-all w-full p-6 space-y-6">
            
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
                            <th class="px-6 py-4 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-16">No</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Mesin</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-40">IP Address</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-24">Port</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-36">Tipe Mesin</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Lokasi / Area</th>
                            <th class="px-6 py-4 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-28">Status</th>
                            <th class="px-6 py-4 text-center font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-48 font-bold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        @forelse($devices as $device)
                            <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-900/20 transition-colors text-left">
                                <td class="px-6 py-4 font-semibold text-slate-900 dark:text-slate-50">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <i data-lucide="fingerprint" class="w-4 h-4 text-slate-400"></i>
                                        <span class="font-bold text-slate-800 dark:text-slate-100">{{ $device->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-700 dark:text-slate-300 font-mono">{{ $device->ip_address }}</td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-400 font-mono">{{ $device->port }}</td>
                                <td class="px-6 py-4 font-medium text-slate-600 dark:text-slate-400">{{ $device->model_name }}</td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-400">{{ $device->location ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    @if($device->is_online)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900/30">
                                            <span id="status-indicator-{{ $device->id }}" class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span id="status-text-{{ $device->id }}">Online</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-900/30">
                                            <span id="status-indicator-{{ $device->id }}" class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            <span id="status-text-{{ $device->id }}">Offline</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="pingDevice({{ $device->id }}, $event)" class="inline-flex items-center justify-center gap-1 px-2.5 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-[10px] font-bold rounded-md transition-colors cursor-pointer" title="Ping Mesin">
                                            <i data-lucide="radio" class="w-3.5 h-3.5"></i>
                                            Tes Koneksi
                                        </button>
                                        <button @click="openEditModal({{ $device }})" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 transition-colors cursor-pointer" title="Edit Mesin">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </button>
                                        <form action="{{ route('zkteco-devices.destroy', $device->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus perangkat absensi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg text-rose-600 dark:text-rose-400 hover:text-rose-750 transition-colors cursor-pointer" title="Hapus Mesin">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <i data-lucide="fingerprint" class="w-8 h-8 text-slate-300 dark:text-slate-700"></i>
                                        <p class="text-xs">Belum ada perangkat ZKTeco terdaftar.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </section>

        <!-- ADD MODAL -->
        <div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" style="display: none;" x-transition>
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl w-full max-w-md shadow-2xl p-6 relative text-left">
                <div class="flex justify-between items-center pb-3 border-b border-slate-100 dark:border-slate-900">
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-50">Daftarkan Perangkat Baru</h3>
                    <button @click="showAddModal = false" class="p-1 hover:bg-slate-100 dark:hover:bg-slate-900 rounded-lg text-slate-400 hover:text-slate-600 cursor-pointer">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('zkteco-devices.store') }}" class="mt-4 space-y-4 text-xs">
                    @csrf
                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Nama Perangkat</label>
                        <input type="text" name="name" required placeholder="Contoh: Mesin Utama Lt. 1" 
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">IP Address</label>
                            <input type="text" name="ip_address" required placeholder="192.168.1.201" 
                                class="w-full h-9 px-3 font-mono bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Port Comm</label>
                            <input type="number" name="port" required value="4370" 
                                class="w-full h-9 px-3 font-mono bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Tipe / Model</label>
                            <select name="model_name" required class="w-full h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 cursor-pointer">
                                <option value="ZKTeco K40">ZKTeco K40</option>
                                <option value="ZKTeco iFace">ZKTeco iFace</option>
                                <option value="ZKTeco LX50">ZKTeco LX50</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Lokasi Penempatan</label>
                            <input type="text" name="location" placeholder="Pintu Gerbang Utama" 
                                class="w-full h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                        </div>
                    </div>

                    <div class="flex gap-2.5 pt-4 border-t border-slate-100 dark:border-slate-900 justify-end">
                        <button type="button" @click="showAddModal = false" class="h-9 px-4 bg-slate-50 hover:bg-slate-100 dark:bg-slate-900 dark:hover:bg-slate-800 text-slate-750 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 cursor-pointer">Batal</button>
                        <button type="submit" class="h-9 px-4 bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 text-xs font-semibold rounded-lg cursor-pointer">Simpan Perangkat</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- EDIT MODAL -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" style="display: none;" x-transition>
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl w-full max-w-md shadow-2xl p-6 relative text-left">
                <div class="flex justify-between items-center pb-3 border-b border-slate-100 dark:border-slate-900">
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-50">Edit Perangkat Absensi</h3>
                    <button @click="showEditModal = false" class="p-1 hover:bg-slate-100 dark:hover:bg-slate-900 rounded-lg text-slate-400 hover:text-slate-600 cursor-pointer">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <form method="POST" :action="`{{ url('zkteco-devices') }}/${editId}`" class="mt-4 space-y-4 text-xs">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Nama Perangkat</label>
                        <input type="text" name="name" required x-model="editName"
                            class="w-full h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">IP Address</label>
                            <input type="text" name="ip_address" required x-model="editIp"
                                class="w-full h-9 px-3 font-mono bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Port Comm</label>
                            <input type="number" name="port" required x-model="editPort"
                                class="w-full h-9 px-3 font-mono bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Tipe / Model</label>
                            <select name="model_name" required x-model="editModelName" class="w-full h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 cursor-pointer">
                                <option value="ZKTeco K40">ZKTeco K40</option>
                                <option value="ZKTeco iFace">ZKTeco iFace</option>
                                <option value="ZKTeco LX50">ZKTeco LX50</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Lokasi Penempatan</label>
                            <input type="text" name="location" x-model="editLocation"
                                class="w-full h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                        </div>
                    </div>

                    <div class="flex gap-2.5 pt-4 border-t border-slate-100 dark:border-slate-900 justify-end">
                        <button type="button" @click="showEditModal = false" class="h-9 px-4 bg-slate-50 hover:bg-slate-100 dark:bg-slate-900 dark:hover:bg-slate-800 text-slate-750 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 cursor-pointer">Batal</button>
                        <button type="submit" class="h-9 px-4 bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 text-xs font-semibold rounded-lg cursor-pointer">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-admin-layout>
