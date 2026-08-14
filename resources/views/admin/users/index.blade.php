<x-admin-layout>
    <div class="p-6 space-y-6" x-data="{ 
        showAddModal: false, 
        showEditModal: false, 
        showDetailModal: false,
        selectedUser: null,
        editUser: { id: '', name: '', email: '', role: '', employee_id: '' },
        openEdit(user) {
            this.editUser = { 
                id: user.id, 
                name: user.name, 
                email: user.email, 
                role: user.role, 
                employee_id: user.employee_id || '' 
            };
            this.showEditModal = true;
        },
        openDetail(user) {
            this.selectedUser = user;
            this.showDetailModal = true;
        }
    }">

        <!-- HEADER -->
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 font-nasalization">Manajemen Pengguna</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Kelola akun akses sistem, hak role, dan hubungan ke profil data pegawai.</p>
            </div>
            <div>
                <button @click="showAddModal = true" class="h-9 px-4 bg-slate-900 dark:bg-slate-100 hover:bg-slate-800 dark:hover:bg-slate-205 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-all cursor-pointer flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Tambah User Baru
                </button>
            </div>
        </header>

        <!-- FILTERS -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-sm text-left">
                                                                                    <form method="GET" action="{{ route('users.index') }}" class="flex flex-col md:flex-row flex-wrap items-end gap-4 text-xs w-full">
                <!-- Search Name/Email -->
                    <div x-data="{ searchVal: '{{ request('search') }}' }" class="flex items-center w-full search-container bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg overflow-hidden shadow-inner focus-within:ring-0 focus-within:border-slate-300 dark:focus-within:border-slate-700">
                        <input type="text" name="search" x-model="searchVal" placeholder="Nama atau email..."
                            style="border: none !important; outline: none !important; box-shadow: none !important;"
                            class="w-full h-9 px-3 text-xs bg-transparent text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-0">
                        
                        <!-- Clear Button (x) -->
                        <button type="button" x-show="searchVal.trim() !== ''" @click="searchVal = ''; $el.closest('.search-container').querySelector('input').focus();" class="h-9 px-2.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer bg-transparent border-0 flex items-center justify-center" title="Bersihkan pencarian">
                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                        </button>

                        <button type="submit" 
                            :class="searchVal.trim() !== '' ? 'bg-indigo-600 text-white dark:bg-indigo-500 dark:text-white' : 'bg-slate-50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300'"
                            class="h-9 px-4 font-bold text-xs transition-all duration-150 cursor-pointer whitespace-nowrap flex items-center justify-center border-l border-slate-200 dark:border-slate-800">
                            Cari
                        </button>
                    </div>
                </div>

                <!-- Filter Role -->
                <div style="flex: 0 0 180px;">
                    <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Hak Akses / Role</label>
                    <select name="role" class="w-full text-xs h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 cursor-pointer">
                        <option value="">Semua Role</option>
                        <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                        <option value="admin_sd" {{ request('role') === 'admin_sd' ? 'selected' : '' }}>Admin SD</option>
                        <option value="kepala_sekolah" {{ request('role') === 'kepala_sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                        <option value="waka" {{ request('role') === 'waka' ? 'selected' : '' }}>Waka</option>
                        <option value="employee" {{ request('role') === 'employee' ? 'selected' : '' }}>Pegawai (Employee)</option>
                    </select>
                </div>

                <!-- Actions -->
                <div style="flex: 0 0 auto; display: flex; align-items: flex-end;">
                    <div class="flex gap-2 w-full h-9">
                        <button type="submit" class="px-5 h-full bg-slate-900 dark:bg-slate-100 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-all cursor-pointer flex items-center justify-center gap-1.5">
                            <i data-lucide="search" class="w-3.5 h-3.5"></i>
                            Filter
                        </button>
                        @if(request()->anyFilled(['search', 'role']))
                            <a href="{{ route('users.index') }}" class="h-full px-3 bg-slate-50 hover:bg-slate-100 dark:bg-slate-900 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 flex items-center justify-center transition-all cursor-pointer" title="Reset Filter">
                                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                            </a>
                        @endif
                    </div>
                </div>
            
                <!-- Per Page -->
                <div style="margin-left: auto; flex: 0 0 110px;">
                    <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Tampilkan</label>
                    <select name="per_page" onchange="this.form.submit()" class="w-full text-xs h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 cursor-pointer">
                        <option value="10" {{ request('per_page', '10') == '10' ? 'selected' : '' }}>10 baris</option>
                        <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25 baris</option>
                        <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 baris</option>
                        <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 baris</option>
                        <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>Semua</option>
                    </select>
                </div>

                
            </form>
        </div>

        <!-- USERS TABLE -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden text-left">
            <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200 dark:scrollbar-thumb-slate-700" style="max-height: calc(100vh - 280px); overflow-y: auto;">
                <table class="w-full text-xs">
                    <thead class="sticky top-0 z-40 bg-slate-50/70 dark:bg-slate-900/50 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="px-6 py-3.5 text-left">Pengguna</th>
                            <th class="px-6 py-3.5 text-left">Email</th>
                            <th class="px-6 py-3.5 text-center">Role / Akses</th>
                            <th class="px-6 py-3.5 text-left">Pegawai Terhubung</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 text-slate-700 dark:text-slate-300 font-medium">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-900/10 transition-colors">
                                <td class="px-6 py-4 text-left">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 font-bold flex items-center justify-center uppercase">
                                            {{ substr($user->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <button @click="openDetail({{ json_encode($user) }})" class="font-bold text-slate-900 dark:text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 text-left hover:underline">
                                                {{ $user->name }}
                                            </button>
                                            @if(auth()->id() === $user->id)
                                                <span class="ml-1.5 px-1.5 py-0.5 rounded text-[8px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-500 uppercase">Anda</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-left font-mono text-slate-600 dark:text-slate-400">
                                    {{ $user->email }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($user->role === 'super_admin')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-400 border border-rose-100 dark:border-rose-900/30 uppercase">Super Admin</span>
                                    @elseif(str_starts_with($user->role, 'admin_'))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900/30 uppercase">Admin Unit</span>
                                    @elseif($user->role === 'kepala_sekolah')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/30 uppercase">Kepala Sekolah</span>
                                    @elseif($user->role === 'waka')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border border-amber-100 dark:border-amber-900/30 uppercase">Waka</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800 uppercase">Pegawai</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-left">
                                    @if($user->employee)
                                        <div class="font-bold text-slate-800 dark:text-slate-200">{{ $user->employee->name }}</div>
                                        <div class="text-[10px] text-slate-400 dark:text-slate-500 font-mono">NIP: {{ $user->employee->nuptk_nip_nik ?? '-' }}</div>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right flex justify-end gap-2">
                                    <button @click="openEdit({{ json_encode($user) }})" class="w-8 h-8 rounded-lg bg-slate-50 hover:bg-slate-100 dark:bg-slate-900 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 flex items-center justify-center transition-colors cursor-pointer" title="Edit Akun">
                                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                    </button>
                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50/40 hover:bg-rose-50 dark:bg-rose-900/10 dark:hover:bg-rose-900/20 border border-rose-100/40 dark:border-rose-900/30 text-rose-600 dark:text-rose-400 flex items-center justify-center transition-colors cursor-pointer" title="Hapus Akun">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
            </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <i data-lucide="user-x" class="w-8 h-8 text-slate-300 dark:text-slate-700"></i>
                                        <p class="text-[10px]">Tidak ditemukan pengguna terdaftar.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                                </table>
            </div>
            
            @if(method_exists($users, 'hasPages') && $users->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                    {{ $users->links('pagination::tailwind') }}
                </div>
            @endif
        </div>

        <!-- MODAL 1: ADD USER -->
        <template x-teleport="body">
            <div x-show="showAddModal" @click.self="showAddModal = false" class="fixed inset-0 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs text-left" style="display: none; margin-top: 0px !important; z-index: 9999;">
            <div @click.outside="showAddModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl max-w-md w-full overflow-hidden text-xs">
                <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-slate-905 dark:text-slate-50 font-nasalization">Tambah User Baru</h3>
                    <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-300">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                <form action="{{ route('users.store') }}" method="POST" x-data="{ selectedRole: 'employee' }">
                    @csrf
                    <div class="p-5 space-y-4">
                        <!-- Name -->
                        <div class="space-y-1.5">
                            <label class="block font-bold text-slate-700 dark:text-slate-300">Nama Lengkap</label>
                            <input type="text" name="name" required placeholder="Nama lengkap..." 
                                class="w-full h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                        </div>

                        <!-- Email -->
                        <div class="space-y-1.5">
                            <label class="block font-bold text-slate-700 dark:text-slate-300">Alamat Email</label>
                            <input type="email" name="email" required placeholder="name@school.com" 
                                class="w-full h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                        </div>

                        <!-- Password -->
                        <div class="space-y-1.5">
                            <label class="block font-bold text-slate-700 dark:text-slate-300">Password</label>
                            <input type="password" name="password" required placeholder="Minimal 8 karakter..." 
                                class="w-full h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                        </div>

                        <!-- Role -->
                        <div class="space-y-1.5">
                            <label class="block font-bold text-slate-700 dark:text-slate-300">Hak Akses (Role)</label>
                            <select name="role" x-model="selectedRole" class="w-full h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 cursor-pointer">
                                <option value="employee">Pegawai (Employee)</option>
                                <option value="kepala_sekolah">Kepala Sekolah</option>
                                <option value="waka">Waka</option>
                                <option value="admin_sd">Admin SD</option>
                                <option value="super_admin">Super Admin</option>
                            </select>
                        </div>

                        <!-- Employee Link (Conditionally shown for unit roles) -->
                        <div class="space-y-1.5" >
                            <label class="block font-bold text-slate-700 dark:text-slate-300">Hubungkan ke Data Pegawai</label>
                            <select name="employee_id" class="w-full h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 cursor-pointer">
                                <option value="">-- Pilih Pegawai --</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->nuptk_nip_nik ?? 'Tanpa NIP' }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="p-5 border-t border-slate-200 dark:border-slate-800 flex gap-2 justify-end">
                        <button type="button" @click="showAddModal = false" class="h-9 px-4 bg-slate-50 hover:bg-slate-100 dark:bg-slate-900 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold rounded-lg border border-slate-200 dark:border-slate-800 transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="h-9 px-4 bg-slate-900 dark:bg-slate-100 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Simpan</button>
                    </div>
            </form>
            </div>
        </div>
        </template>

        <!-- MODAL 2: EDIT USER -->
        <template x-teleport="body">
            <div x-show="showEditModal" @click.self="showEditModal = false" class="fixed inset-0 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs text-left" style="display: none; margin-top: 0px !important; z-index: 9999;">
            <div @click.outside="showEditModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl max-w-md w-full overflow-hidden text-xs">
                <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-slate-905 dark:text-slate-50 font-nasalization">Edit Akun Pengguna</h3>
                    <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-300">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                <form :action="`{{ url('users') }}/${editUser.id}`" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-5 space-y-4">
                        <!-- Name -->
                        <div class="space-y-1.5">
                            <label class="block font-bold text-slate-700 dark:text-slate-300">Nama Lengkap</label>
                            <input type="text" name="name" x-model="editUser.name" required 
                                :readonly="editUser.employee_id !== null"
                                :class="editUser.employee_id !== null ? 'bg-slate-100 dark:bg-slate-800/50 cursor-not-allowed border-slate-200 text-slate-500 dark:text-slate-400' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-900 dark:text-slate-100'"
                                class="w-full h-9 px-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                            <p x-show="editUser.employee_id !== null" class="text-[10px] text-slate-400 mt-1">Nama lengkap disinkronkan dari data pegawai/guru yang terhubung.</p>
                        </div>

                        <!-- Email -->
                        <div class="space-y-1.5">
                            <label class="block font-bold text-slate-700 dark:text-slate-300">Alamat Email</label>
                            <input type="email" name="email" x-model="editUser.email" required 
                                class="w-full h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                        </div>

                        <!-- Password -->
                        <div class="space-y-1.5">
                            <label class="block font-bold text-slate-700 dark:text-slate-300">Password Baru <span class="text-[10px] text-slate-400 font-normal">(Kosongkan jika tidak diubah)</span></label>
                            <input type="password" name="password" placeholder="Sandi baru..." 
                                class="w-full h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                        </div>

                        <!-- Role -->
                        <div class="space-y-1.5">
                            <label class="block font-bold text-slate-700 dark:text-slate-300">Hak Akses (Role)</label>
                            <select name="role" x-model="editUser.role" class="w-full h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-805 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 cursor-pointer">
                                <option value="employee">Pegawai (Employee)</option>
                                <option value="kepala_sekolah">Kepala Sekolah</option>
                                <option value="waka">Waka</option>
                                <option value="admin_sd">Admin SD</option>
                                <option value="super_admin">Super Admin</option>
                            </select>
                        </div>

                        <!-- Employee Link (Conditionally shown for unit roles) -->
                        <div class="space-y-1.5" >
                            <label class="block font-bold text-slate-700 dark:text-slate-300">Hubungkan ke Data Pegawai</label>
                            <select name="employee_id" x-model="editUser.employee_id" class="w-full h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 cursor-pointer">
                                <option value="">-- Pilih Pegawai --</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->nuptk_nip_nik ?? 'Tanpa NIP' }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="p-5 border-t border-slate-200 dark:border-slate-800 flex gap-2 justify-end">
                        <button type="button" @click="showEditModal = false" class="h-9 px-4 bg-slate-50 hover:bg-slate-100 dark:bg-slate-900 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold rounded-lg border border-slate-200 dark:border-slate-800 transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="h-9 px-4 bg-slate-900 dark:bg-slate-100 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Simpan Perubahan</button>
                    </div>
            </form>
            </div>
        </div>
        </template>

        <!-- MODAL 3: PROFILE DETAIL CARD -->
        <template x-teleport="body">
            <div x-show="showDetailModal" @click.self="showDetailModal = false" class="fixed inset-0 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs text-left" style="display: none; margin-top: 0px !important; z-index: 9999;">
            <div @click.outside="showDetailModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl max-w-md w-full overflow-hidden text-xs">
                <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-900/50">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-50 font-nasalization flex items-center gap-2">
                        <i data-lucide="user-check" class="w-4 h-4 text-indigo-600 dark:text-indigo-400"></i>
                        Profil Pengguna
                    </h3>
                    <button @click="showDetailModal = false" class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-300">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                
                <div class="p-5 space-y-6">
                    <!-- User Account Card -->
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-xl bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 font-bold flex items-center justify-center text-2xl uppercase shadow-sm">
                            <span x-text="selectedUser ? selectedUser.name.substring(0,2) : ''"></span>
                        </div>
                        <div class="space-y-1">
                            <h4 class="text-sm font-bold text-slate-900 dark:text-slate-50 font-nasalization" x-text="selectedUser ? selectedUser.name : ''"></h4>
                            <p class="text-slate-400 dark:text-slate-500 font-mono" x-text="selectedUser ? selectedUser.email : ''"></p>
                            <span class="inline-flex px-2 py-0.5 rounded text-[9px] font-bold bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800 uppercase" x-text="selectedUser ? selectedUser.role : ''"></span>
                        </div>
                    </div>

                    <!-- Linked Employee Info -->
                    <div class="space-y-3">
                        <h5 class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Status Relasi Pegawai</h5>
                        
                        <div x-show="selectedUser && selectedUser.employee" class="bg-slate-50/50 dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800 rounded-xl p-4 space-y-3">
                            <div class="flex items-center gap-2">
                                <i data-lucide="briefcase" class="w-4 h-4 text-indigo-600 dark:text-indigo-400 shrink-0"></i>
                                <span class="font-bold text-slate-800 dark:text-slate-200" x-text="selectedUser && selectedUser.employee ? selectedUser.employee.name : ''"></span>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-3 text-[11px] pt-2 border-t border-slate-200/40 dark:border-slate-800/40">
                                <div>
                                    <span class="block text-slate-400 text-[9px] uppercase font-semibold">NUPTK / NIP / NIK</span>
                                    <span class="font-mono font-medium text-slate-700 dark:text-slate-300" x-text="selectedUser && selectedUser.employee ? (selectedUser.employee.nuptk_nip_nik || '-') : '-'"></span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 text-[9px] uppercase font-semibold">Unit Sekolah</span>
                                    <span class="font-bold text-slate-700 dark:text-slate-300 uppercase" x-text="selectedUser && selectedUser.employee ? (selectedUser.employee.unit || '-') : '-'"></span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 text-[9px] uppercase font-semibold">Status Pegawai</span>
                                    <span class="font-bold text-slate-700 dark:text-slate-300" x-text="selectedUser && selectedUser.employee ? (selectedUser.employee.status || '-') : '-'"></span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 text-[9px] uppercase font-semibold">Terdaftar Pada</span>
                                    <span class="font-mono text-slate-600 dark:text-slate-400" x-text="selectedUser && selectedUser.employee ? new Date(selectedUser.employee.created_at).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'}) : '-'"></span>
                                </div>
                            </div>
                        </div>

                        <div x-show="selectedUser && !selectedUser.employee" class="border border-dashed border-slate-200 dark:border-slate-800 rounded-xl p-4 text-center text-slate-400">
                            <i data-lucide="link-2-off" class="w-5 h-5 mx-auto text-slate-300 dark:text-slate-700 mb-1"></i>
                            <p class="text-[11px]">Akun ini tidak dihubungkan ke data pegawai.</p>
                        </div>
                    </div>
                </div>

                <div class="p-5 border-t border-slate-200 dark:border-slate-800 flex justify-end">
                    <button @click="showDetailModal = false" class="h-9 px-4 bg-slate-900 dark:bg-slate-100 hover:bg-slate-800 dark:hover:bg-slate-205 text-white dark:text-slate-900 font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Tutup</button>
                </div>
            </div>
        </div>
        </template>
</x-admin-layout>

