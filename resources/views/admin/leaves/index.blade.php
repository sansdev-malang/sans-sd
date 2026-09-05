<x-admin-layout>
    <div class="p-6 space-y-6" x-data="{ 
        showRejectModal: false,
        selectedLeaveId: '',
        selectedLeaveEmployee: '',
        showEmpDetailModal: false,
        selectedEmp: null,
        showEditModal: false,
        editLeave: { id: '', status: '', notes: '', name: '', leave_type_id: '' },
        showAddModal: {{ $errors->any() ? 'true' : 'false' }}
    }">

        @php
            if (!function_exists('getInitials')) {
                function getInitials($name) {
                    if (empty($name)) return '?';
                    $words = explode(' ', $name);
                    if (count($words) >= 2) {
                        return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                    }
                    return strtoupper(substr($name, 0, 2));
                }
            }
            $colors = ['#6366f1', '#ec4899', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6', '#14b8a6', '#f43f5e', '#0ea5e9', '#d946ef'];
        @endphp

        <!-- HEADER -->
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left ">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-200 font-nasalization">Riwayat Izin Pegawai</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Daftar riwayat izin, sakit, dan cuti pegawai di unit sekolah.</p>
            </div>
            <div>
                <button @click="showAddModal = true" class="h-9 px-4 bg-slate-900 dark:bg-slate-100 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-all cursor-pointer flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Input Izin / Cuti
                </button>
            </div>
        </header>

        <!-- STATS SECTION -->
        <section class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Menunggu Approval -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-sm text-left flex items-center justify-between">
                <div>
                    <span class="text-xs text-slate-500 dark:text-slate-400 block font-medium">Menunggu Approval</span>
                    <span class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1 block">
                        <span>{{ $pendingCount }}</span> <span class="text-xs font-medium text-slate-400">Pengajuan</span>
                    </span>
                </div>
                <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
            </div>

            <!-- Total Diproses -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-sm text-left flex items-center justify-between">
                <div>
                    <span class="text-xs text-slate-500 dark:text-slate-400 block font-medium">Total Diproses</span>
                    <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 mt-1 block">
                        <span>{{ $processedCount }}</span> <span class="text-xs font-medium text-slate-400">Izin</span>
                    </span>
                </div>
                <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                    <i data-lucide="file-signature" class="w-5 h-5"></i>
                </div>
            </div>

            <!-- Rasio Persetujuan -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-sm text-left flex items-center justify-between">
                <div>
                    <span class="text-xs text-slate-500 dark:text-slate-400 block font-medium">Rasio Persetujuan</span>
                    <span class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1 block">
                        <span>{{ $approvalRate }}%</span>
                    </span>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <i data-lucide="percent" class="w-5 h-5"></i>
                </div>
            </div>
        </section>

        <!-- FILTERS & SEARCH -->
        <section class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-sm w-full text-left">
            <form method="GET" action="{{ route('leaves.index') }}" class="flex flex-col md:flex-row gap-4 items-stretch md:items-center justify-between">
                <!-- Left Side: Search & Filters -->
                <div class="flex flex-wrap items-center gap-2 flex-1">
                    <!-- Search Box -->
                    <div x-data="{ searchVal: '{{ request('search') }}' }" class="flex items-center w-full md:w-60 h-9 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg overflow-hidden transition-all focus-within:ring-2 focus-within:ring-slate-100 dark:focus-within:ring-slate-800/60 focus-within:border-slate-400 dark:focus-within:border-slate-600 shadow-inner search-container">
                        <input type="text" name="search" x-model="searchVal" placeholder="Cari pegawai..."
                            class="w-full h-full px-3 text-xs bg-transparent border-none outline-none focus:ring-0 text-slate-900 dark:text-slate-55 placeholder-slate-400 dark:placeholder-slate-500">
                        
                        <!-- Clear Button (x) -->
                        <button type="button" x-show="searchVal.trim() !== ''" @click="searchVal = ''; $el.closest('.search-container').querySelector('input').focus();" class="h-full px-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors cursor-pointer bg-transparent border-0 flex items-center justify-center" title="Bersihkan pencarian">
                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                        </button>

                        <button type="submit" 
                            :class="searchVal.trim() !== '' ? 'bg-indigo-600 text-white dark:bg-indigo-500 dark:text-white border-indigo-700' : 'bg-slate-50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300'"
                            class="h-full px-4 font-bold text-xs transition-all duration-150 cursor-pointer whitespace-nowrap flex items-center justify-center border-l border-slate-200 dark:border-slate-800">
                            Cari
                        </button>
                    </div>

                    <!-- Jenis Izin -->
                    @if(isset($leaveTypes) && count($leaveTypes) > 0)
                        <select name="type" onchange="this.form.submit()"
                            class="h-9 pl-2.5 pr-8 flex-1 sm:flex-initial sm:w-44 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none cursor-pointer text-ellipsis overflow-hidden whitespace-nowrap">
                            <option value="">Semua Jenis Izin</option>
                            @foreach($leaveTypes as $lt)
                                <option value="{{ $lt->id }}" {{ request('type') == $lt->id ? 'selected' : '' }}>{{ $lt->name }}</option>
                            @endforeach
                        </select>
                    @endif

                    <!-- Status -->
                    <select name="status" onchange="this.form.submit()"
                        class="h-9 pl-2.5 pr-8 flex-1 sm:flex-initial sm:w-44 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none cursor-pointer text-ellipsis overflow-hidden whitespace-nowrap">
                        <option value="">Semua Status</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                        <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>

                    @if(request()->anyFilled(['search', 'type', 'status']) || request()->filled('per_page') && request('per_page') != 50)
                        <a href="{{ route('leaves.index') }}" class="h-9 px-3 flex items-center justify-center bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-300 rounded-lg transition-colors reset-filter-btn" title="Reset Filter">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>

                <!-- Right Side: Per Page Options -->
                <div class="flex items-center gap-2 w-full md:w-auto shrink-0 self-end md:self-auto justify-end">
                    <select name="per_page" onchange="this.form.submit()"
                        class="h-9 pl-2.5 pr-8 flex-1 sm:flex-initial sm:w-24 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none cursor-pointer text-ellipsis overflow-hidden whitespace-nowrap">
                        <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10 baris</option>
                        <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25 baris</option>
                        <option value="50" {{ request('per_page', '50') == '50' ? 'selected' : '' }}>50 baris</option>
                        <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 baris</option>
                        <option value="500" {{ request('per_page') == '500' ? 'selected' : '' }}>500 baris</option>
                        <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>Semua</option>
                    </select>
                </div>
            </form>
        </section>

        <!-- TABLE LIST -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden text-left w-full flex flex-col justify-between">
            <div class="p-5 border-b border-slate-100 dark:border-slate-900 flex justify-between items-center flex-wrap gap-2 bg-white dark:bg-slate-900">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                    Daftar Riwayat Izin
                </h3>
            </div>
            <div class="overflow-x-auto overflow-y-auto custom-scrollbar" style="max-height: calc(100vh - 240px);">
                <table class="w-full text-xs border-collapse">
                    <thead class="z-10">
                        <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-400 dark:text-slate-505 font-bold uppercase tracking-wider text-[10px]">
                            <th class="sticky top-0 z-10 bg-slate-50 dark:bg-slate-900 px-6 py-3 text-left w-64 min-w-[200px]">Profil Pegawai</th>
                            <th class="sticky top-0 z-10 bg-slate-50 dark:bg-slate-900 px-6 py-3 text-left w-48 min-w-[150px]">Jenis Izin</th>
                            <th class="sticky top-0 z-10 bg-slate-50 dark:bg-slate-900 px-6 py-3 text-center w-32">Tanggal Mulai</th>
                            <th class="sticky top-0 z-10 bg-slate-50 dark:bg-slate-900 px-6 py-3 text-center w-32">Tanggal Selesai</th>
                            <th class="sticky top-0 z-10 bg-slate-50 dark:bg-slate-900 px-6 py-3 text-left w-auto min-w-[120px]">Keterangan</th>
                            <th class="sticky top-0 z-10 bg-slate-50 dark:bg-slate-900 px-6 py-3 text-left w-auto min-w-[120px]">Catatan / Alasan</th>
                            <th class="sticky top-0 z-10 bg-slate-50 dark:bg-slate-900 px-6 py-3 text-center w-28">Status</th>
                            <th class="sticky top-0 z-10 bg-slate-50 dark:bg-slate-900 px-6 py-3 text-center w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-900 text-slate-700 dark:text-slate-300 font-medium">
                        @forelse($paginatedLeaves as $index => $leave)
                            @php
                                $emp = $leave->employee;
                                $empName = $emp ? $emp->name : 'Tidak Diketahui';
                                $color = $colors[$index % count($colors)];
                                $initial = getInitials($empName);
                                $unitName = $emp ? $emp->unit : '-';
                                
                                $processedBy = '-';
                                if ($leave->processedBy) {
                                    $roleLabels = [
                                        'super_admin' => 'Super Admin',
                                        'admin_paud' => 'Admin PAUD',
                                        'admin_sd' => 'Admin SD',
                                        'admin_smp' => 'Admin SMP',
                                        'kepala_sekolah' => 'Kepala Sekolah',
                                        'waka' => 'Wakil Kepala Sekolah',
                                    ];
                                    $roleLabel = $roleLabels[$leave->processedBy->role] ?? $leave->processedBy->role;
                                    $processedBy = "{$leave->processedBy->name} ({$roleLabel})";
                                } elseif ($leave->processed_by_name) {
                                    $processedBy = $leave->processed_by_name;
                                } else {
                                    $noteText = $leave->notes ?? '';
                                    $lowerNote = strtolower($noteText);
                                    if (
                                        str_starts_with($lowerNote, 'disetujui oleh ') ||
                                        str_starts_with($lowerNote, 'ditolak oleh ') ||
                                        str_starts_with($lowerNote, 'disetujui otomatis oleh ')
                                    ) {
                                        $parts = explode('oleh ', $noteText);
                                        $processedBy = preg_replace('/[\s.]+$/', '', end($parts));
                                    } elseif (preg_match('/\((Keputusan|Ditolak|Disetujui)\s+oleh\s+(.*?)\)/i', $noteText, $matches)) {
                                        $processedBy = $matches[2];
                                    }
                                }

                                $displayNotes = $leave->notes ?? '';
                                $lowerNotes = strtolower($displayNotes);
                                if (
                                    str_starts_with($lowerNotes, 'disetujui oleh') || 
                                    str_starts_with($lowerNotes, 'ditolak oleh') || 
                                    str_starts_with($lowerNotes, 'disetujui otomatis oleh')
                                ) {
                                    $displayNotes = '';
                                } else {
                                    $displayNotes = preg_replace('/\s*\((Keputusan|Ditolak|Disetujui)\s+oleh.*?\)/i', '', $displayNotes);
                                }

                                $leaveData = [
                                    'name' => $empName,
                                    'nuptk_nip_nik' => $emp ? $emp->nuptk_nip_nik : '-',
                                    'subject_position' => $emp ? $emp->position : '-',
                                    'unit' => strtoupper($unitName),
                                    'email' => $emp ? $emp->email : '-',
                                    'gender' => $emp ? $emp->gender : '-',
                                    'employment_status' => $emp ? $emp->employment_status : '-',
                                    'photo_url' => $emp && $emp->photo ? (str_contains($emp->photo, 'photos/') ? asset('storage/' . $emp->photo) : asset('storage/photos/' . $emp->photo)) : '',
                                    'leave_type' => $leave->leaveType ? $leave->leaveType->name : $leave->type,
                                    'leave_start' => $leave->start_date->format('d M Y'),
                                    'leave_end' => $leave->end_date->format('d M Y'),
                                    'leave_reason' => $leave->reason ?? '-',
                                    'leave_attachment' => $leave->attachment ? asset('storage/' . $leave->attachment) : '',
                                    'status_code' => $leave->leaveType ? $leave->leaveType->status_code : 'I',
                                    'leave_status' => $leave->status,
                                    'notes' => $leave->notes ?? '',
                                    'created_at' => $leave->created_at ? $leave->created_at->format('d M Y H:i') : '-',
                                    'updated_at' => $leave->updated_at ? $leave->updated_at->format('d M Y H:i') : '-',
                                    'processed_by' => $processedBy,
                                ];
                            @endphp
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/20 transition-colors">
                                <td class="px-6 py-3 text-left">
                                    <div class="flex items-center gap-3">
                                        <!-- Photo/Avatar -->
                                        <div class="shrink-0">
                                            @if($emp && $emp->photo)
                                                <img src="{{ str_contains($emp->photo, 'photos/') ? asset('storage/' . $emp->photo) : asset('storage/photos/' . $emp->photo) }}" class="w-8 h-8 rounded-full object-cover shrink-0 border border-slate-200/50 dark:border-slate-800/40">
                                            @else
                                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white shadow-sm shrink-0" style="background:{{ $color }}">{{ $initial }}</div>
                                            @endif
                                        </div>
                                        <div class="flex flex-col min-w-0">
                                            <span @click="selectedEmp = {{ json_encode($leaveData) }}; showEmpDetailModal = true" class="text-slate-900 dark:text-slate-200 font-bold tracking-tight inline-block cursor-pointer hover:text-indigo-600 dark:hover:text-indigo-400 hover:scale-[1.01] transform transition-all duration-200 origin-left truncate text-left" title="Klik untuk melihat detail lengkap">{{ $empName }}</span>
                                            <div class="flex flex-col gap-0.5 mt-0.5 min-w-0 text-left">
                                                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold truncate max-w-[180px] block" title="{{ $emp ? $emp->position : '-' }}">{{ $emp ? $emp->position : '-' }}</span>
                                                <span class="text-[9px] text-slate-400 dark:text-slate-500 mt-0.5 font-medium block">Diajukan: {{ $leave->created_at ? $leave->created_at->format('d M Y H:i') : '-' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-left">
                                    <div class="flex flex-col items-start justify-start gap-1">
                                        <div class="flex items-center gap-1.5">
                                            @php
                                                $statusCode = $leave->leaveType ? $leave->leaveType->status_code : 'I';
                                                $badgeClasses = 'bg-slate-50 text-slate-700 border-slate-100 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800';
                                                if ($statusCode === 'S') {
                                                    $badgeClasses = 'bg-rose-50 text-rose-700 border-rose-100 dark:bg-rose-900/30 dark:text-rose-455 dark:border-rose-900/50';
                                                } elseif ($statusCode === 'I') {
                                                    $badgeClasses = 'bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-900/30 dark:text-amber-450 dark:border-amber-900/50';
                                                } elseif ($statusCode === 'C') {
                                                    $badgeClasses = 'bg-sky-50 text-sky-700 border-sky-100 dark:bg-sky-900/30 dark:text-sky-450 dark:border-sky-900/50';
                                                } elseif ($statusCode === 'H') {
                                                    $badgeClasses = 'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-900/50';
                                                }
                                            @endphp
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full text-[10px] font-black border uppercase {{ $badgeClasses }}" title="Kode Status: {{ $statusCode }}">
                                                {{ $statusCode }}
                                            </span>
                                            <span class="text-xs text-slate-700 dark:text-slate-355 font-bold capitalize tracking-tight">{{ $leave->leaveType ? $leave->leaveType->name : $leave->type }}</span>
                                        </div>
                                        @if($leave->attachment)
                                            <div class="mt-1">
                                                <a href="{{ asset('storage/' . $leave->attachment) }}" target="_blank" class="inline-flex items-center gap-1 text-[9px] text-indigo-700 hover:text-indigo-850 dark:text-indigo-400 dark:hover:text-indigo-300 font-bold bg-indigo-50/50 dark:bg-indigo-905/20 border border-indigo-100 dark:border-indigo-900/30 px-1.5 py-0.5 rounded transition-all shadow-2xs hover:shadow-xs">
                                                    <i data-lucide="paperclip" class="w-3.5 h-3.5"></i>
                                                    Lampiran
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-center font-mono">
                                    {{ $leave->start_date->format('d M Y') }}
                                </td>
                                <td class="px-6 py-3 text-center font-mono">
                                    {{ $leave->end_date->format('d M Y') }}
                                </td>
                                <td class="px-6 py-3 text-left">
                                    <span @click="selectedEmp = {{ json_encode($leaveData) }}; showEmpDetailModal = true"
                                          class="text-slate-600 dark:text-slate-400 block truncate max-w-[150px] lg:max-w-[250px] cursor-pointer hover:text-indigo-600 dark:hover:text-indigo-400 hover:scale-[1.01] transform transition-all duration-200 origin-left"
                                          title="Klik untuk melihat detail lengkap">
                                        {{ \Illuminate\Support\Str::limit($leave->reason ?? '-', 40) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-left">
                                    <span class="text-slate-600 dark:text-slate-400 block truncate max-w-[150px] lg:max-w-[200px]"
                                          title="{{ $leave->notes ?? '-' }}">
                                        {{ $leave->notes ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        @if($leave->status === 'Pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-955/30 text-amber-700 dark:text-amber-400 border border-amber-200/30 dark:border-amber-900/30 uppercase">
                                                Pending
                                            </span>
                                        @elseif($leave->status === 'Approved')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-955/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200/30 dark:border-emerald-900/30 uppercase">
                                                Disetujui
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 dark:bg-rose-955/30 text-rose-700 dark:text-rose-455 border border-rose-200/30 dark:border-rose-900/30 uppercase">
                                                Ditolak
                                            </span>
                                        @endif

                                        @if($leave->status !== 'Pending' && $processedBy !== '-')
                                            <div class="text-[9px] text-slate-405 dark:text-slate-500 font-semibold mt-0.5 leading-tight">oleh: {{ $processedBy }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button @click="selectedEmp = {{ json_encode($leaveData) }}; showEmpDetailModal = true" class="inline-flex items-center justify-center w-6 h-6 bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-655 dark:text-slate-300 rounded-lg border border-slate-200 dark:border-slate-800 transition-all cursor-pointer shadow-2xs hover:shadow-xs" title="Lihat Detail">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </button>
                                        @if($leave->status === 'Pending')
                                            <form action="{{ route('leaves.approve', $leave->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-955/30 dark:hover:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold rounded-lg border border-emerald-200/30 dark:border-emerald-900/30 transition-all cursor-pointer shadow-2xs hover:shadow-xs">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                                    Setujui
                                                </button>
                                            </form>
                                            <button @click="selectedLeaveId = '{{ $leave->id }}'; selectedLeaveEmployee = '{{ $empName }}'; showRejectModal = true" class="inline-flex items-center gap-1 px-2.5 py-1 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/30 dark:hover:bg-rose-900/40 text-rose-700 dark:text-rose-400 text-[10px] font-bold rounded-lg border border-rose-200/30 dark:border-rose-900/30 transition-all cursor-pointer shadow-2xs hover:shadow-xs">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                                Tolak
                                            </button>
                                        @else
                                            <button @click="editLeave = { id: '{{ $leave->id }}', status: '{{ $leave->status }}', notes: {{ json_encode($displayNotes) }}, name: {{ json_encode($empName) }}, leave_type_id: '{{ $leave->leave_type_id }}' }; showEditModal = true" class="inline-flex items-center justify-center w-6 h-6 bg-amber-50/60 hover:bg-amber-100/60 dark:bg-amber-955/20 dark:hover:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-lg border border-amber-200/30 dark:border-amber-900/30 transition-all cursor-pointer shadow-2xs hover:shadow-xs" title="Edit Keputusan">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                            </button>
                                        @endif
                                        <form action="{{ route('leaves.destroy', $leave->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data izin ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center w-6 h-6 bg-rose-50/50 hover:bg-rose-100/60 dark:bg-rose-955/20 dark:hover:bg-rose-900/30 text-rose-600 dark:text-rose-455 rounded-lg border border-rose-200/30 dark:border-rose-900/30 transition-all cursor-pointer shadow-2xs hover:shadow-xs" title="Hapus Izin">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center text-slate-500 dark:text-slate-400">
                                    Belum ada data riwayat izin pegawai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            @if($paginatedLeaves instanceof \Illuminate\Pagination\LengthAwarePaginator && $paginatedLeaves->total() > 0)
                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-xs text-slate-550 dark:text-slate-400">
                        Menampilkan
                        <span class="font-bold text-slate-700 dark:text-slate-300">{{ $paginatedLeaves->firstItem() }}</span>
                        sampai
                        <span class="font-bold text-slate-700 dark:text-slate-300">{{ $paginatedLeaves->lastItem() }}</span>
                        dari
                        <span class="font-bold text-slate-700 dark:text-slate-300">{{ $paginatedLeaves->total() }}</span>
                        data riwayat izin
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        @if ($paginatedLeaves->onFirstPage())
                            <span class="h-8 px-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-400 dark:text-slate-655 flex items-center justify-center cursor-not-allowed select-none">Sebelumnya</span>
                        @else
                            <a href="{{ $paginatedLeaves->appends(request()->query())->previousPageUrl() }}" class="h-8 px-3 rounded-lg border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-900 text-slate-700 dark:text-slate-300 flex items-center justify-center transition-all bg-white dark:bg-slate-900">Sebelumnya</a>
                        @endif

                        <span class="px-2 font-semibold text-slate-600 dark:text-slate-400">
                            Halaman {{ $paginatedLeaves->currentPage() }} dari {{ $paginatedLeaves->lastPage() }}
                        </span>

                        @if ($paginatedLeaves->hasMorePages())
                            <a href="{{ $paginatedLeaves->appends(request()->query())->nextPageUrl() }}" class="h-8 px-3 rounded-lg border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-900 text-slate-700 dark:text-slate-300 flex items-center justify-center transition-all bg-white dark:bg-slate-900">Berikutnya</a>
                        @else
                            <span class="h-8 px-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-400 dark:text-slate-655 flex items-center justify-center cursor-not-allowed select-none">Berikutnya</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- MODAL DETAIL PEGAWAI -->
        <template x-teleport="body">
            <div x-show="showEmpDetailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs text-left" style="display: none; margin-top: 0px !important; z-index: 9999;">
                <div @click.outside="showEmpDetailModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl max-w-lg w-full overflow-hidden text-xs flex flex-col max-h-[90vh]">
                    <!-- Header -->
                    <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-950/50 shrink-0">
                        <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 font-nasalization flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>
                            <span>Detail Riwayat Izin</span>
                        </h3>
                        <button @click="showEmpDetailModal = false" class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 bg-transparent border-0 cursor-pointer flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        </button>
                    </div>
                    
                    <div class="p-5 space-y-5 overflow-y-auto max-h-[60vh] md:max-h-[65vh] bg-white dark:bg-slate-900">
                        <!-- Employee Compact Profile Card -->
                        <div class="bg-slate-50 dark:bg-slate-950/40 rounded-xl p-4 border border-slate-100 dark:border-slate-800/80 flex items-center gap-4">
                            <!-- Photo / Initials -->
                            <div class="shrink-0">
                                <template x-if="selectedEmp && selectedEmp.photo_url">
                                    <img :src="selectedEmp.photo_url" class="w-14 h-14 rounded-full object-cover border border-slate-200 dark:border-slate-800 shadow-xs">
                                </template>
                                <template x-if="!selectedEmp || !selectedEmp.photo_url">
                                    <div class="w-14 h-14 rounded-full bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 font-bold flex items-center justify-center text-xl uppercase shadow-xs">
                                        <span x-text="selectedEmp ? selectedEmp.name.substring(0,2) : ''"></span>
                                    </div>
                                </template>
                            </div>
                            <div class="space-y-1 text-left flex-1 min-w-0">
                                <h4 class="text-sm font-bold text-slate-900 dark:text-slate-200 font-nasalization truncate" x-text="selectedEmp ? selectedEmp.name : ''"></h4>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate" x-text="selectedEmp ? selectedEmp.email : ''"></p>
                                <p class="text-[10px] text-slate-600 dark:text-slate-400 font-semibold truncate mt-0.5" x-text="selectedEmp ? selectedEmp.subject_position : ''"></p>
                            </div>
                        </div>

                        <!-- Status Banner -->
                        <template x-if="selectedEmp">
                            <div class="rounded-lg p-3 border text-[11px] font-medium flex items-center gap-2 animate-fade-in"
                                :class="{
                                    'bg-emerald-50/50 dark:bg-emerald-950/20 text-emerald-800 dark:text-emerald-400 border-emerald-100 dark:border-emerald-900/40': selectedEmp.leave_status === 'Approved',
                                    'bg-rose-50/50 dark:bg-rose-950/20 text-rose-800 dark:text-rose-450 border-rose-100 dark:border-rose-900/40': selectedEmp.leave_status === 'Rejected',
                                    'bg-amber-50/50 dark:bg-amber-950/20 text-amber-800 dark:text-amber-400 border-amber-100 dark:border-amber-900/40': selectedEmp.leave_status === 'Pending'
                                }">
                                <span class="w-2 h-2 rounded-full"
                                    :class="{
                                        'bg-emerald-500': selectedEmp.leave_status === 'Approved',
                                        'bg-rose-500': selectedEmp.leave_status === 'Rejected',
                                        'bg-amber-500': selectedEmp.leave_status === 'Pending'
                                    }"></span>
                                <span x-text="
                                    selectedEmp.leave_status === 'Approved' ? 'Pengajuan izin ini telah disetujui' : 
                                   (selectedEmp.leave_status === 'Rejected' ? 'Pengajuan izin ini telah ditolak: ' + (selectedEmp.notes || 'tanpa catatan') : 'Pengajuan izin ini sedang menunggu persetujuan')
                                "></span>
                            </div>
                        </template>

                        <!-- Leave Info Grid -->
                        <div class="grid grid-cols-2 gap-4 text-[11px] text-left bg-white dark:bg-slate-900">
                            <div>
                                <span class="block text-slate-400 dark:text-slate-500 text-[9px] uppercase font-bold tracking-tight mb-1">Jenis Izin</span>
                                <div class="flex flex-col items-start gap-1.5">
                                    <template x-if="selectedEmp">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <!-- SICH Code Badge -->
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full text-[10px] font-black border uppercase"
                                                :class="{
                                                    'bg-rose-50 text-rose-700 border-rose-100 dark:bg-rose-900/30 dark:text-rose-455 dark:border-rose-900/50': selectedEmp.status_code === 'S',
                                                    'bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-900/50': selectedEmp.status_code === 'I',
                                                    'bg-sky-50 text-sky-700 border-sky-100 dark:bg-sky-900/30 dark:text-sky-400 dark:border-sky-900/50': selectedEmp.status_code === 'C',
                                                    'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-900/50': selectedEmp.status_code === 'H'
                                                }"
                                                x-text="selectedEmp.status_code"></span>
                                            
                                            <!-- Type Name -->
                                            <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-200 border border-slate-200/50 dark:border-slate-800 capitalize" x-text="selectedEmp.leave_type"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div>
                                <span class="block text-slate-400 dark:text-slate-500 text-[9px] uppercase font-bold tracking-tight mb-1">Rentang Tanggal</span>
                                <div class="flex items-center gap-1.5 h-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
                                    <span class="font-bold text-slate-700 dark:text-slate-200" x-text="selectedEmp ? selectedEmp.leave_start + ' - ' + selectedEmp.leave_end : ''"></span>
                                </div>
                            </div>

                            <div class="col-span-2 pt-1">
                                <span class="block text-slate-400 dark:text-slate-500 text-[9px] uppercase font-bold tracking-tight mb-1">Keterangan / Alasan</span>
                                <div class="bg-slate-50 dark:bg-slate-950/30 rounded-xl p-3 border border-slate-100 dark:border-slate-800/80">
                                    <p class="text-slate-600 dark:text-slate-300 italic font-medium leading-relaxed" x-text="selectedEmp ? selectedEmp.leave_reason : ''"></p>
                                </div>
                            </div>

                            <template x-if="selectedEmp && selectedEmp.leave_attachment">
                                <div class="col-span-2 pt-1">
                                    <span class="block text-slate-400 dark:text-slate-500 text-[9px] uppercase font-bold tracking-tight mb-1">Berkas Lampiran</span>
                                    <a :href="selectedEmp.leave_attachment" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] text-indigo-700 hover:text-indigo-805 dark:text-indigo-400 dark:hover:text-indigo-300 font-bold bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/30 transition-all shadow-2xs hover:shadow-xs">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l8.57-8.57A4 4 0 1 1 18 8.84l-8.59 8.57a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                                        Lihat Lampiran Dokumen
                                    </a>
                                </div>
                            </template>
                        </div>

                        <!-- Additional Details -->
                        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 grid grid-cols-2 gap-4 text-[10px] text-left bg-white dark:bg-slate-900">
                            <div>
                                <span class="block text-slate-400 dark:text-slate-500 font-semibold mb-0.5">Status Kepegawaian</span>
                                <span class="font-bold text-slate-700 dark:text-slate-300" x-text="selectedEmp ? selectedEmp.employment_status : ''"></span>
                            </div>
                            <div>
                                <span class="block text-slate-400 dark:text-slate-500 font-semibold mb-0.5">Jenis Kelamin</span>
                                <span class="font-bold text-slate-700 dark:text-slate-300" x-text="selectedEmp ? (selectedEmp.gender === 'Male' ? 'Laki-laki' : 'Perempuan') : ''"></span>
                            </div>
                            <div>
                                <span class="block text-slate-400 dark:text-slate-500 font-semibold mb-0.5">Tanggal Pengajuan</span>
                                <span class="font-bold text-slate-700 dark:text-slate-300" x-text="selectedEmp ? selectedEmp.created_at : ''"></span>
                            </div>
                            <template x-if="selectedEmp && selectedEmp.leave_status !== 'Pending'">
                                <div>
                                    <span class="block text-slate-400 dark:text-slate-500 font-semibold mb-0.5" x-text="selectedEmp.leave_status === 'Approved' ? 'Tanggal Disetujui' : 'Tanggal Ditolak'"></span>
                                    <span class="font-bold text-slate-700 dark:text-slate-300" x-text="selectedEmp.updated_at"></span>
                                </div>
                            </template>
                            <template x-if="selectedEmp && selectedEmp.leave_status !== 'Pending' && selectedEmp.processed_by && selectedEmp.processed_by !== '-'">
                                <div class="col-span-2">
                                    <span class="block text-slate-400 dark:text-slate-500 font-semibold mb-0.5">Diproses Oleh</span>
                                    <span class="font-bold text-slate-700 dark:text-slate-300" x-text="selectedEmp.processed_by"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="p-5 border-t border-slate-100 dark:border-slate-800 flex justify-end bg-slate-50 dark:bg-slate-950/40 shrink-0">
                        <button @click="showEmpDetailModal = false" class="h-9 px-4 bg-slate-900 dark:bg-slate-100 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 font-semibold rounded-lg shadow-2xs hover:shadow-xs transition-all cursor-pointer">Tutup</button>
                    </div>
                </div>
            </div>
        </template>

        <!-- REJECT MODAL -->
        <template x-teleport="body">
            <div x-show="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" style="display: none; margin-top: 0px !important; z-index: 9999;">
            <div @click.outside="showRejectModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl w-full max-w-sm p-6 text-left animate-in fade-in duration-200">
                <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-850 pb-3 mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-slate-50 font-nasalization">Tolak Pengajuan Izin</h3>
                        <p class="text-[10px] text-slate-400 dark:text-slate-50 mt-0.5" x-text="selectedLeaveEmployee"></p>
                    </div>
                    <button @click="showRejectModal = false" class="text-slate-400 hover:text-slate-650 dark:hover:text-slate-300 bg-transparent border-0 cursor-pointer flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>

                <form method="POST" :action="`{{ url('leaves') }}/${selectedLeaveId}/reject`" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Alasan Penolakan</label>
                        <textarea name="notes" required rows="3" placeholder="Masukkan alasan penolakan izin..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950/30 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-1 focus:ring-indigo-500"></textarea>
                    </div>

                    <div class="flex gap-2.5 pt-4 border-t border-slate-100 dark:border-slate-800 justify-end">
                        <button type="button" @click="showRejectModal = false" class="h-9 px-4 bg-slate-50 hover:bg-slate-100 dark:bg-slate-900 dark:hover:bg-slate-850 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 transition-all cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="h-9 px-4 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white text-xs font-semibold rounded-lg shadow-xs hover:shadow-sm transition-all cursor-pointer">
                            Tolak Izin
                        </button>
                    </div>
                </form>
            </div>
            </div>
        </template>

        <!-- EDIT DECISION MODAL -->
        <template x-teleport="body">
            <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs text-left animate-in fade-in duration-200" style="display: none; margin-top: 0px !important; z-index: 9999;">
                <div @click.outside="showEditModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl w-full max-w-sm overflow-hidden text-xs flex flex-col">
                    <!-- Header -->
                    <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-900/50 shrink-0">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-slate-200 font-nasalization flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                            <span>Ubah Keputusan Izin</span>
                        </h3>
                        <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 bg-transparent border-0 cursor-pointer flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <form method="POST" :action="`{{ url('leaves') }}/${editLeave.id}`" class="space-y-4 text-xs p-5">
                        @csrf
                        @method('PUT')

                        <div>
                            <span class="block text-[10px] text-slate-400 dark:text-slate-505 font-semibold mb-1">Nama Pegawai</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200" x-text="editLeave.name"></span>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Jenis Izin</label>
                            <select name="leave_type_id" x-model="editLeave.leave_type_id" 
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-1 focus:ring-indigo-500 font-sans text-xs">
                                @foreach($leaveTypes as $lt)
                                    <option value="{{ $lt->id }}">{{ $lt->name }} - {{ $lt->status_code }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Keputusan Status</label>
                            <select name="status" x-model="editLeave.status" 
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-1 focus:ring-indigo-500 font-sans text-xs">
                                <option value="Pending">Menunggu Persetujuan (Pending)</option>
                                <option value="Approved">Disetujui (Approved)</option>
                                <option value="Rejected">Ditolak (Rejected)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Catatan / Alasan</label>
                            <textarea name="notes" x-model="editLeave.notes" rows="3" placeholder="Masukkan catatan atau alasan keputusan..."
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900/30 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-1 focus:ring-indigo-500 resize-none font-sans text-xs"></textarea>
                        </div>

                        <div class="flex gap-2.5 pt-4 border-t border-slate-100 dark:border-slate-800 justify-end">
                            <button type="button" @click="showEditModal = false" class="h-9 px-4 bg-slate-50 hover:bg-slate-100 dark:bg-slate-900 dark:hover:bg-slate-850 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 transition-all cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" class="h-9 px-4 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-850 text-white text-xs font-bold rounded-lg shadow-xs hover:shadow-sm transition-all cursor-pointer">
                                Simpan Keputusan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        <!-- ADD MODAL -->
        <template x-teleport="body">
            <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs text-left" style="display: none; margin-top: 0px !important; z-index: 9999;" x-transition>
                <div @click.away="showAddModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl w-full max-w-md overflow-hidden text-xs flex flex-col">
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-slate-55 flex items-center gap-2">
                            <i data-lucide="plus-circle" class="w-4 h-4 text-slate-500"></i>
                            Input Izin / Cuti Baru
                        </h3>
                        <button @click="showAddModal = false" class="p-1 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-slate-400 hover:text-slate-600 transition-colors cursor-pointer bg-transparent border-0">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                    <form action="{{ route('leaves.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="p-6 space-y-4">
                            @if($errors->any())
                                <div class="bg-rose-50 dark:bg-rose-900/40 border border-rose-200 dark:border-rose-900/60 rounded-xl p-4 mb-2">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="w-7 h-7 rounded-full bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                                            <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                        </div>
                                        <h5 class="text-xs font-bold text-slate-800 dark:text-slate-200">Terjadi Kesalahan Validasi</h5>
                                    </div>
                                    <ul class="list-disc list-inside text-xs text-slate-600 dark:text-slate-400 space-y-1 ml-10">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div x-data="{
                                open: false,
                                search: '',
                                selectedId: '{{ old('employee_id') }}',
                                selectedName: '',
                                employees: [
                                    @foreach($employees as $emp)
                                        { id: '{{ $emp->id }}', name: {{ json_encode($emp->name) }} },
                                    @endforeach
                                ],
                                get filteredEmployees() {
                                    if (this.search.trim() === '') return this.employees;
                                    return this.employees.filter(emp => emp.name.toLowerCase().includes(this.search.toLowerCase()));
                                },
                                init() {
                                    let initial = this.employees.find(emp => emp.id == this.selectedId);
                                    if (initial) this.selectedName = initial.name;
                                }
                            }" class="relative">
                                <label class="block text-xs font-bold text-slate-455 dark:text-slate-500 uppercase tracking-wider mb-2">Pegawai</label>
                                
                                <input type="hidden" name="employee_id" :value="selectedId" required>
                                
                                <button type="button" @click="open = !open" 
                                    class="w-full text-xs h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 cursor-pointer flex items-center justify-between text-left transition-all">
                                    <span x-text="selectedName || 'Pilih Pegawai'" :class="selectedName ? 'text-slate-900 dark:text-slate-100' : 'text-slate-400 dark:text-slate-500'"></span>
                                    <svg class="w-4 h-4 text-slate-400 shrink-0 ml-2 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"></path></svg>
                                </button>
                                
                                <div x-show="open" @click.outside="open = false" 
                                    class="absolute z-50 left-0 right-0 mt-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg shadow-lg overflow-hidden flex flex-col max-h-60"
                                    x-transition style="display: none;">
                                    
                                    <div class="p-2 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/20">
                                        <input type="text" x-model="search" placeholder="Cari nama pegawai..."
                                            class="w-full h-8 px-2.5 text-xs bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-850 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-slate-900 dark:text-slate-100 placeholder-slate-400">
                                    </div>
                                    
                                    <div class="overflow-y-auto flex-1 py-1">
                                        <template x-for="emp in filteredEmployees" :key="emp.id">
                                            <button type="button" @click="selectedId = emp.id; selectedName = emp.name; open = false; search = ''"
                                                class="w-full text-left px-3 py-2 text-xs hover:bg-slate-50 dark:hover:bg-slate-800/60 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-slate-100 flex items-center justify-between cursor-pointer bg-transparent border-0">
                                                <span x-text="emp.name"></span>
                                                <template x-if="selectedId == emp.id">
                                                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path></svg>
                                                </template>
                                            </button>
                                        </template>
                                        <template x-if="filteredEmployees.length === 0">
                                            <div class="px-3 py-3 text-center text-slate-400 dark:text-slate-500">
                                                Pegawai tidak ditemukan
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                @error('employee_id') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-2">Jenis Izin</label>
                                <select name="leave_type_id" required class="w-full text-xs h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 cursor-pointer">
                                    <option value="">Pilih Jenis Izin</option>
                                    @foreach($leaveTypes as $lt)
                                        <option value="{{ $lt->id }}" {{ old('leave_type_id') == $lt->id ? 'selected' : '' }}>{{ $lt->name }}</option>
                                    @endforeach
                                </select>
                                @error('leave_type_id') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-455 dark:text-slate-500 uppercase tracking-wider mb-2">Tanggal Mulai</label>
                                    <input type="date" name="start_date" value="{{ old('start_date') }}" required class="w-full text-xs h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                                    @error('start_date') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-455 dark:text-slate-500 uppercase tracking-wider mb-2">Tanggal Selesai</label>
                                    <input type="date" name="end_date" value="{{ old('end_date') }}" required class="w-full text-xs h-9 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800">
                                    @error('end_date') <span class="text-rose-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-455 dark:text-slate-500 uppercase tracking-wider mb-2">Alasan</label>
                                <textarea name="reason" rows="3" class="w-full text-xs p-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-100 dark:focus:ring-slate-800 resize-none" placeholder="Tulis alasan izin..."></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-455 dark:text-slate-500 uppercase tracking-wider mb-2">Berkas Lampiran</label>
                                <input type="file" name="attachment" class="w-full text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none">
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                            <button type="button" @click="showAddModal = false" class="px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors cursor-pointer bg-transparent border-0">Batal</button>
                            <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-slate-900 dark:bg-slate-100 dark:text-slate-900 hover:bg-slate-850 rounded-lg transition-colors cursor-pointer">Simpan Izin</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>

    <style>
        @media (min-width: 768px) {
            .search-container {
                max-width: 280px !important;
            }
        }
    </style>
</x-admin-layout>
