<x-admin-layout>
    <div class="p-6 space-y-6" x-data="{ showEmpDetailModal: false, showCreateModal: {{ $errors->any() && !old('edit_id') ? 'true' : 'false' }}, showEditModal: {{ $errors->any() && old('edit_id') ? 'true' : 'false' }}, selectedEmp: null }">

        <!-- HEADER -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Data Pegawai</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Kelola dan pantau seluruh data pendidik (guru) dan kependidikan (karyawan/staff) di semua unit.</p>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full md:w-auto shrink-0">
                <form action="{{ route('employees.sync-cache') }}" method="POST" class="m-0 p-0 flex w-full sm:w-auto">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm transition-all duration-100 cursor-pointer w-full sm:w-auto" onclick="this.disabled=true; this.innerHTML='Syncing...'; this.form.submit();">
                        <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-slate-500"></i>
                        Sync ke Server
                    </button>
                </form>
                <button onclick="toggleModal('import-employee-modal')" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm transition-all duration-100 cursor-pointer w-full sm:w-auto">
                    <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5 text-slate-500"></i>
                    Impor Pegawai
                </button>
                <button @click="showCreateModal = true" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-900 dark:bg-slate-50 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-all duration-100 cursor-pointer w-full sm:w-auto">
                      <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                      Tambah Pegawai
                  </button>
                @if(auth()->user()->role === 'super_admin')
                <form action="{{ route('employees.generate-accounts') }}" method="POST" class="m-0 p-0 flex w-full sm:w-auto">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-600 dark:bg-emerald-500 hover:bg-emerald-700 dark:hover:bg-emerald-600 text-white text-xs font-semibold rounded-lg shadow-sm transition-all duration-100 cursor-pointer w-full" onclick="return confirm('Generate akun untuk semua pegawai yang memiliki email tetapi belum punya akun?')">
                        <i data-lucide="key" class="w-3.5 h-3.5"></i>
                        Generate Akun Massal
                    </button>
                </form>
                @endif
            </div>
        </section>

        <!-- IMPORT ERRORS ALERT -->
        @if(session('import_errors'))
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-900 text-red-800 dark:text-red-400 p-4 rounded-xl flex items-start gap-3 text-left">
                <i data-lucide="alert-triangle" class="w-5 h-5 mt-0.5 shrink-0 text-red-500 dark:text-red-400"></i>
                <div class="space-y-1">
                    <h5 class="text-xs font-bold">Beberapa baris data gagal diimpor:</h5>
                    <ul class="list-disc list-inside text-[11px] leading-relaxed opacity-90 max-h-40 overflow-y-auto">
                        @foreach(session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- FILTERS & SEARCH -->
        <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-sm w-full">
            <form method="GET" action="{{ route('employees.index') }}" class="flex flex-col md:flex-row gap-4 items-stretch md:items-center justify-between">
                <!-- Search Box Welded with Cari Button (Premium Input Group) -->
                <div x-data="{ searchVal: '{{ request('search') }}' }" class="flex items-center w-full search-container bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg overflow-hidden shadow-inner focus-within:ring-0 focus-within:border-slate-300 dark:focus-within:border-slate-700">
                    <input type="text" name="search" x-model="searchVal" placeholder="Cari Pegawai..."
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

                <!-- Filters -->
                <div class="flex items-center gap-2 w-full md:w-auto">

                      <select name="per_page" onchange="this.form.submit()"
                          class="h-9 px-2 flex-1 sm:flex-initial sm:w-28 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none cursor-pointer appearance-none" style="padding-right: 1.5rem; background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 0.5rem top 50%; background-size: 0.65rem auto;">
                          <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 Data</option>
                          <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 Data</option>
                          <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Data</option>
                          <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 Data</option>
                          <option value="99999" {{ request('per_page') == 99999 ? 'selected' : '' }}>Semua</option>
                      </select>
                    <!-- Type -->
                    <select name="type" onchange="this.form.submit()"
                        class="h-9 px-2 flex-1 sm:flex-initial sm:w-36 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none cursor-pointer">
                        <option value="">Semua Peran</option>
                        @foreach($employeeTypes as $type)
                            <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>

                    <!-- Position -->
                    <select name="position" onchange="this.form.submit()"
                        class="h-9 px-2 flex-1 sm:flex-initial sm:w-36 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none cursor-pointer">
                        <option value="">Semua Jabatan</option>
                        @foreach($positions ?? [] as $pos)
                            <option value="{{ $pos }}" {{ request('position') == $pos ? 'selected' : '' }}>{{ $pos }}</option>
                        @endforeach
                    </select>

                    <!-- Unit -->
                    @if(!config('app.school_unit'))
                    <select name="unit" onchange="this.form.submit()"
                        class="h-9 px-2 flex-1 sm:flex-initial sm:w-36 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none cursor-pointer">
                        <option value="">Semua Unit</option>
                        <option value="paud" {{ request('unit') == 'paud' ? 'selected' : '' }}>PAUD & TK</option>
                        <option value="sd" {{ request('unit') == 'sd' ? 'selected' : '' }}>Sekolah Dasar (SD)</option>
                        <option value="smp" {{ request('unit') == 'smp' ? 'selected' : '' }}>SMP</option>
                    </select>
                    @endif

                    <!-- Status -->
                    <select name="status" onchange="this.form.submit()"
                        class="h-9 px-2 flex-1 sm:flex-initial sm:w-36 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Aktif</option>
                        <option value="Leave" {{ request('status') == 'Leave' ? 'selected' : '' }}>Cuti</option>
                        <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>

                    @if(request()->anyFilled(['search', 'type', 'unit', 'status']))
                        <a href="{{ route('employees.index') }}" class="h-9 px-3 flex items-center justify-center bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-300 rounded-lg transition-colors" title="Reset Filter">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        </a>
                    @endif
                </div>
            </form>
        </section>

        <!-- TABLE SECTION -->
        <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden w-full">
            <div class="overflow-x-auto overflow-y-auto" style="max-height: calc(100vh - 240px);">
                <table class="w-full text-xs border-collapse">
                    <thead class="sticky top-0 z-10 shadow-sm">
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-14">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider min-w-[200px]">Nama & Email</th>
                            @if(!config('app.school_unit'))
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-28">Unit</th>
                            @endif
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-40">Tipe</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-56">Jabatan</th>
                            
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-32">ZK ID</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-24">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-900">
                        @forelse($employees as $index => $employee)
                            <tr class="hover:bg-slate-50/40 dark:hover:bg-slate-900/20 transition-colors">
                                <td class="px-6 py-4 text-slate-900 dark:text-slate-50 font-medium">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($employee->photo)
                                            <img src="{{ str_contains($employee->photo, 'photos/') ? asset('storage/' . $employee->photo) : asset('storage/photos/' . $employee->photo) }}" class="w-8 h-8 rounded-full object-cover shrink-0 border border-slate-200/50 dark:border-slate-800/40">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-900 flex items-center justify-center text-xs font-bold text-slate-700 dark:text-slate-300 shrink-0">
                                                {{ strtoupper(substr($employee->raw_name, 0, 2)) }}
                                            </div>
                                        @endif
                                        <div>
                                            @php
                                                $empData = $employee->toArray();
                                                $empData['photo_url'] = $employee->photo ? (str_contains($employee->photo, 'photos/') ? asset('storage/' . $employee->photo) : asset('storage/photos/' . $employee->photo)) : '';
                                                $empData['unit_name'] = 'SD';
                                            @endphp
                                            <span @click='selectedEmp = @json($empData); showEmpDetailModal = true' class="text-slate-900 dark:text-slate-50 font-bold tracking-tight block cursor-pointer hover:underline hover:text-indigo-600 dark:hover:text-indigo-400">{{ $employee->name }}</span>
                                            <span class="text-[10px] text-slate-500 dark:text-slate-400 block">{{ $employee->email ?? 'Tidak ada email' }}</span>
                                        </div>
                                    </div>
                                </td>
                                @if(!config('app.school_unit'))
                                <td class="px-6 py-4">
                                    @if($employee->unit == 'paud')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-teal-50 dark:bg-teal-900/30 text-teal-700 dark:text-teal-400 border border-teal-200/50 dark:border-teal-800/40 uppercase">PAUD & TK</span>
                                    @elseif($employee->unit == 'sd')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 border border-blue-200/50 dark:border-blue-800/40 uppercase">SD</span>
                                    @elseif($employee->unit == 'smp')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 border border-purple-200/50 dark:border-purple-800/40 uppercase">SMP</span>
                                    @endif
                                </td>
                                @endif
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-300 font-medium">
                                    {{ $employee->employeeType->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="block text-slate-700 dark:text-slate-300 font-medium">{{ $employee->position ?? '-' }}</span>
                                    @if(!empty($employee->additional_position))
                                        <span class="block text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">{{ $employee->additional_position }}</span>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4">
                                    @if($employee->zkteco_uid)
                                        <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300 font-mono text-[10px]">ID: {{ $employee->zkteco_uid }}</span>
                                    @else
                                        <span class="text-slate-400 dark:text-slate-600">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($employee->status == 'Active')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-800/40">Aktif</span>
                                    @elseif($employee->status == 'Leave')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border border-amber-200/50 dark:border-amber-800/40">Cuti</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border border-red-200/50 dark:border-red-800/40">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if(!$employee->user && auth()->user()->role === 'super_admin')
                                        <form action="{{ route('employees.generate-account', $employee->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-1.5 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 rounded-lg text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors cursor-pointer" title="Buatkan Akun" onclick="return confirm('Buat akun untuk {{ $employee->name }} dengan password default: sans1234?')">
                                                <i data-lucide="user-plus" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                        @endif
                                        <button @click='selectedEmp = @json($employee); showEditModal = true' class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-900 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 transition-colors cursor-pointer" title="Edit Data"><i data-lucide="edit" class="w-4 h-4"></i></button>
                                        <button onclick="deleteEmployee('{{ $employee->id }}', '{{ $employee->name }}')" class="p-1.5 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg text-red-600 dark:text-red-400 hover:text-red-700 transition-colors cursor-pointer" title="Hapus Data">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-10 text-center text-slate-500 dark:text-slate-400">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <i data-lucide="users" class="w-8 h-8 text-slate-300 dark:text-slate-700"></i>
                                        <p class="font-medium text-xs">Belum ada data pegawai terdaftar.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

              <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                  {{ $employees->links() }}
              </div>
        </section>

        <!-- IMPORT MODAL -->
        <div x-data><template x-teleport="body">
        <div id="import-employee-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden transition-opacity" style="margin-top: 0px !important; z-index: 9999;" onclick="if(event.target === this) toggleModal('import-employee-modal')">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl w-full max-w-md overflow-hidden transform transition-all scale-95 opacity-0 duration-200">
                <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-50">Impor Pegawai dari Excel</h3>
                    <button onclick="toggleModal('import-employee-modal')" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>
                <form action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data" class="p-5 space-y-4 text-left">
                    @csrf
                    <div class="space-y-2 bg-slate-50 dark:bg-slate-900 p-4 rounded-lg border border-slate-200 dark:border-slate-800">
                        <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300">Format Template Pengisian</h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                            Unduh template Excel resmi terlebih dahulu untuk memahami susunan kolom data pegawai yang benar. Pastikan format isian sesuai petunjuk contoh.
                        </p>
                        <a href="{{ route('employees.download-template') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                            <i data-lucide="download" class="w-3.5 h-3.5"></i>
                            Unduh Template Excel (.xlsx)
                        </a>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Pilih File Excel (.xlsx)</label>
                        <input type="file" name="file" accept=".xlsx, .xls" required class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-50 focus:outline-none file:mr-4 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-[10px] file:font-semibold file:bg-slate-200 dark:file:bg-slate-800 file:text-slate-700 dark:file:text-slate-300 hover:file:bg-slate-300 dark:hover:file:bg-slate-700 cursor-pointer">
                    </div>
                    <div class="p-5 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-2.5">
                        <button type="button" onclick="toggleModal('import-employee-modal')" class="px-4 py-2 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 bg-transparent text-xs font-bold rounded-lg cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-slate-900 dark:bg-slate-50 text-white dark:text-slate-900 text-xs font-bold rounded-lg cursor-pointer">Mulai Impor</button>
                    </div>
                </form>
            </div>
        </div>
        </template></div>

        <!-- DELETE FORM -->
        <form id="delete-form" action="" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

    <!-- SCRIPT MODALS -->
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

        function deleteEmployee(id, name) {
            showGlobalConfirmModal('Apakah Anda yakin ingin menghapus data pegawai ' + name + '?', function() {
                const form = document.getElementById('delete-form');
                form.action = '/employees/' + id;
                form.submit();
            });
        }

        function editEmployee(employee) {
            const form = document.getElementById('edit-employee-form');
            form.action = '/employees/' + employee.id;
            
            form.querySelector('[name="name"]').value = employee.name;
            form.querySelector('[name="email"]').value = employee.email || '';
            form.querySelector('[name="nuptk_nip_nik"]').value = employee.nuptk_nip_nik || '';
            form.querySelector('[name="employee_type_id"]').value = employee.employee_type_id || '';
            form.querySelector('[name="unit"]').value = employee.unit;
            form.querySelector('[name="gender"]').value = employee.gender;
            form.querySelector('[name="zkteco_uid"]').value = employee.zkteco_uid || '';
            form.querySelector('[name="subject_position"]').value = employee.subject_position || '';
            form.querySelector('[name="employment_status"]').value = employee.employment_status || '';
            form.querySelector('[name="status"]').value = employee.status || 'Active';
            
            // Reset photo file input
            form.querySelector('[name="photo"]').value = '';
            
            // Handle photo preview
            const previewContainer = document.getElementById('edit-photo-preview-container');
            const previewImg = document.getElementById('edit-photo-preview');
            if (employee.photo) {
                                previewImg.src = employee.photo.includes('photos/') ? '/storage/' + employee.photo : '/storage/photos/' + employee.photo;
                                previewContainer.classList.remove('hidden');
                            } else {
                previewImg.src = '';
                previewContainer.classList.add('hidden');
            }

            toggleModal('edit-employee-modal');
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
                <!-- MODAL DETAIL PEGAWAI (ADAPTED FROM HRD) -->
        <div x-data><template x-teleport="body">
        <!-- MODAL DETAIL PEGAWAI -->
        <div x-show="showEmpDetailModal" 
             style="display: none;"
             class="fixed inset-0 z-50 overflow-y-auto"
             aria-labelledby="detail-modal-title" 
             role="dialog" 
             aria-modal="true">
             
            <!-- Backdrop -->
            <div x-show="showEmpDetailModal" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" 
                 @click="showEmpDetailModal = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <!-- Modal Panel -->
                <div x-show="showEmpDetailModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-xl bg-white dark:bg-slate-900 text-left shadow-2xl transition-all sm:my-8 w-full sm:max-w-2xl border border-slate-200 dark:border-slate-800">
                    
                    <div class="p-6">
                        <!-- Header -->
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800" style="margin-bottom: 1.25rem; padding-bottom: 1rem;">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-slate-50" id="detail-modal-title">Profil Pegawai</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Detail informasi dan status pegawai terdaftar.</p>
                            </div>
                            <button type="button" @click="showEmpDetailModal = false" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-800 text-slate-500 cursor-pointer transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="space-y-6" style="display: flex; flex-direction: column; gap: 1.5rem;">
                            <div class="flex items-center gap-4">
                                <!-- Photo / Initials -->
                                <div class="shrink-0">
                                    <template x-if="selectedEmp && selectedEmp.photo_url">
                                        <img :src="selectedEmp.photo_url" class="w-16 h-16 rounded-xl object-cover border border-slate-200 dark:border-slate-800 shadow-sm">
                                    </template>
                                    <template x-if="!selectedEmp || !selectedEmp.photo_url">
                                        <div class="w-16 h-16 rounded-xl bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300 font-bold flex items-center justify-center text-2xl uppercase border border-slate-200 dark:border-slate-800 shadow-sm">
                                            <span x-text="selectedEmp ? selectedEmp.name.substring(0,2) : ''"></span>
                                        </div>
                                    </template>
                                </div>
                                <div class="space-y-1">
                                    <h4 class="text-sm font-bold text-slate-900 dark:text-slate-50" x-text="selectedEmp ? selectedEmp.name : ''"></h4>
                                    <p class="text-slate-500 dark:text-slate-400 font-mono text-[11px]" x-text="selectedEmp ? 'NIP/NUPTK: ' + ((selectedEmp.nik_nuptk || selectedEmp.nuptk || selectedEmp.nik || '-')) : ''"></p>
                                    <span class="inline-flex px-2 py-0.5 rounded text-[9px] font-bold bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800 uppercase" x-text="selectedEmp ? (selectedEmp.position || selectedEmp.subject_position || '') : ''"></span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-y-4 gap-x-3 text-[11px] pt-4 border-t border-slate-100 dark:border-slate-800 max-h-[50vh] overflow-y-auto pr-2 custom-scrollbar">
                                <div class="col-span-full bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-3">
                                    <h5 class="text-xs font-bold text-slate-700 dark:text-slate-300 border-b border-slate-200 dark:border-slate-800 pb-1.5">Informasi Umum</h5>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-y-3 gap-x-3 text-[11px]">
                                        <div class="min-w-0">
                                            <span class="block text-slate-400 text-[9px] uppercase font-semibold">Unit Kerja</span>
                                            <span class="font-bold text-slate-900 dark:text-slate-100 uppercase break-words block" x-text="selectedEmp ? (selectedEmp.unit_name || selectedEmp.unit) : '-'"></span>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="block text-slate-400 text-[9px] uppercase font-semibold">Email</span>
                                            <span class="font-bold text-slate-900 dark:text-slate-100 break-all block" x-text="selectedEmp ? (selectedEmp.email || '-') : '-'"></span>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="block text-slate-400 text-[9px] uppercase font-semibold">Jenis Kelamin</span>
                                            <span class="font-bold text-slate-900 dark:text-slate-100 block" x-text="selectedEmp ? (selectedEmp.gender === 'Male' ? 'Laki-laki' : (selectedEmp.gender === 'Female' ? 'Perempuan' : '-')) : '-'"></span>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="block text-slate-400 text-[9px] uppercase font-semibold">Tempat, Tgl Lahir</span>
                                            <span class="font-bold text-slate-900 dark:text-slate-100 break-words block" x-text="selectedEmp ? ((selectedEmp.birth_place || '-') + ', ' + (selectedEmp.birth_date || '-')) : '-'"></span>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="block text-slate-400 text-[9px] uppercase font-semibold">Alamat</span>
                                            <span class="font-bold text-slate-900 dark:text-slate-100 break-words block" x-text="selectedEmp ? (selectedEmp.address || '-') : '-'"></span>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="block text-slate-400 text-[9px] uppercase font-semibold">No. HP</span>
                                            <span class="font-bold text-slate-900 dark:text-slate-100 break-all block" x-text="selectedEmp ? (selectedEmp.phone || '-') : '-'"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-span-full bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-3">
                                    <h5 class="text-xs font-bold text-slate-700 dark:text-slate-300 border-b border-slate-200 dark:border-slate-800 pb-1.5">Informasi Status Pegawai</h5>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-y-3 gap-x-3 text-[11px]">
                                        <div class="min-w-0">
                                            <span class="block text-slate-400 text-[9px] uppercase font-semibold">Status Pegawai</span>
                                            <span class="font-bold text-slate-900 dark:text-slate-100 block" x-text="selectedEmp ? (selectedEmp.employment_status || '-') : '-'"></span>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="block text-slate-400 text-[9px] uppercase font-semibold">Jabatan</span>
                                            <span class="font-bold text-slate-900 dark:text-slate-100 break-words block" x-text="selectedEmp ? (selectedEmp.position || '-') : '-'"></span>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="block text-slate-400 text-[9px] uppercase font-semibold">Tugas Tambahan</span>
                                            <span class="font-bold text-slate-900 dark:text-slate-100 break-words block" x-text="selectedEmp ? (selectedEmp.additional_position || '-') : '-'"></span>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="block text-slate-400 text-[9px] uppercase font-semibold">Masa Kerja</span>
                                            <span class="font-bold text-slate-900 dark:text-slate-100 block" x-text="selectedEmp ? (selectedEmp.work_period || '-') : '-'"></span>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="block text-slate-400 text-[9px] uppercase font-semibold">Pangkat/Golongan</span>
                                            <span class="font-bold text-slate-900 dark:text-slate-100 break-words block" x-text="selectedEmp ? (selectedEmp.pangkat_golongan || '-') : '-'"></span>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="block text-slate-400 text-[9px] uppercase font-semibold">ID ZKTeco</span>
                                            <span class="font-bold text-slate-900 dark:text-slate-100 font-mono break-all block" x-text="selectedEmp ? (selectedEmp.zkteco_uid || '-') : '-'"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-span-full bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-3">
                                    <h5 class="text-xs font-bold text-slate-700 dark:text-slate-300 border-b border-slate-200 dark:border-slate-800 pb-1.5">Informasi Pegawai</h5>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-y-3 gap-x-3 text-[11px]">
                                        <div class="min-w-0">
                                            <span class="block text-slate-400 text-[9px] uppercase font-semibold">NIK</span>
                                            <span class="font-bold text-slate-900 dark:text-slate-100 font-mono break-all block" x-text="selectedEmp ? (selectedEmp.nik || '-') : '-'"></span>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="block text-slate-400 text-[9px] uppercase font-semibold">NUPTK</span>
                                            <span class="font-bold text-slate-900 dark:text-slate-100 font-mono break-all block" x-text="selectedEmp ? (selectedEmp.nuptk || '-') : '-'"></span>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="block text-slate-400 text-[9px] uppercase font-semibold">NIY</span>
                                            <span class="font-bold text-slate-900 dark:text-slate-100 font-mono break-all block" x-text="selectedEmp ? (selectedEmp.niy || '-') : '-'"></span>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="block text-slate-400 text-[9px] uppercase font-semibold">No. UKG</span>
                                            <span class="font-bold text-slate-900 dark:text-slate-100 font-mono break-all block" x-text="selectedEmp ? (selectedEmp.no_ukg || '-') : '-'"></span>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="block text-slate-400 text-[9px] uppercase font-semibold">NRG</span>
                                            <span class="font-bold text-slate-900 dark:text-slate-100 font-mono break-all block" x-text="selectedEmp ? (selectedEmp.nrg || '-') : '-'"></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-span-full bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-200 dark:border-slate-800 space-y-3">
                                    <h5 class="text-xs font-bold text-slate-700 dark:text-slate-300 border-b border-slate-200 dark:border-slate-800 pb-1.5">Pendidikan & SK</h5>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-y-3 gap-x-3 text-[11px]">
                                        <div class="min-w-0">
                                            <span class="block text-slate-400 text-[9px] uppercase font-semibold">Pendidikan Terakhir</span>
                                            <span class="font-bold text-slate-900 dark:text-slate-100 break-words block" x-text="selectedEmp ? (selectedEmp.last_education || '-') : '-'"></span>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="block text-slate-400 text-[9px] uppercase font-semibold">Jurusan</span>
                                            <span class="font-bold text-slate-900 dark:text-slate-100 break-words block" x-text="selectedEmp ? (selectedEmp.major || '-') : '-'"></span>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="block text-slate-400 text-[9px] uppercase font-semibold">Tgl Mulai Tugas</span>
                                            <span class="font-bold text-slate-900 dark:text-slate-100 block" x-text="selectedEmp ? (selectedEmp.task_start_date || '-') : '-'"></span>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="block text-slate-400 text-[9px] uppercase font-semibold">Info SK</span>
                                            <span class="font-bold text-slate-900 dark:text-slate-100 break-words block" x-text="selectedEmp ? ((selectedEmp.last_sk_number || 'Tidak Ada SK') + (selectedEmp.last_sk_date ? ' (' + selectedEmp.last_sk_date + ')' : '')) : '-'"></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-span-full mt-2">
                                    <span class="block text-slate-400 text-[9px] uppercase font-semibold">Catatan</span>
                                    <div class="p-2.5 bg-slate-50 dark:bg-slate-900/50 rounded-lg border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 mt-1" x-text="selectedEmp && selectedEmp.notes ? selectedEmp.notes : 'Tidak ada catatan tambahan.'"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="bg-slate-50 dark:bg-slate-900/50 px-6 py-4 flex items-center justify-end border-t border-slate-200 dark:border-slate-800">
                        <button type="button" @click="showEmpDetailModal = false" class="h-9 px-4 inline-flex items-center justify-center rounded-lg bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 text-xs font-semibold shadow-sm hover:bg-slate-800 dark:hover:bg-slate-200 transition-colors cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        </template></div>
        @include('admin.employees.modals')
<style>
    @media (min-width: 768px) {
        .search-container {
            max-width: 280px !important;
        }
    }
    .search-container button[type="submit"]:hover {
        background-color: #0f172a !important; /* bg-slate-900 */
        color: #ffffff !important; /* text-white */
    }
    .dark .search-container button[type="submit"]:hover {
        background-color: #f8fafc !important; /* bg-slate-105 */
        color: #0f172a !important; /* text-slate-900 */
    }
</style>
</x-admin-layout>




