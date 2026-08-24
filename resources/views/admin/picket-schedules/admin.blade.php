<x-admin-layout>
@section('title', 'Manajemen Piket')
<div class="p-6 space-y-6" x-data="{
    tab: 'scheduler',
    showAreaModal: false,
    showAssignModal: false,
    editAreaMode: false,
    areaForm: { id: '', name: '', jobs: '', duty_hours: '06.30 - 07.00', is_active: 1 },
    dragOverCell: null,
    teacherSearch: '',
    schedules: @js($schedules->map(fn($s) => [
        'id' => $s->id,
        'picket_area_id' => $s->picket_area_id,
        'picket_area_name' => $s->picketArea->name,
        'day_of_week' => $s->day_of_week,
        'employee_id' => $s->employee_id,
        'employee_name' => $s->employee->name,
        'employee_position' => $s->employee->position ?? $s->employee->employeeType->name ?? '-'
    ])),
    openAreaCreate() {
        this.editAreaMode = false;
        this.areaForm = { id: '', name: '', jobs: '', duty_hours: '06.30 - 07.00', is_active: 1 };
        this.showAreaModal = true;
    },
    openAreaEdit(area) {
        this.editAreaMode = true;
        this.areaForm = { id: area.id, name: area.name, jobs: area.jobs, duty_hours: area.duty_hours, is_active: area.is_active ? 1 : 0 };
        this.showAreaModal = true;
    },
    async handleDrop(event, areaId, dayOfWeek) {
        event.preventDefault();
        this.dragOverCell = null;
        try {
            const data = JSON.parse(event.dataTransfer.getData('text/plain'));
            if (data && data.employeeId) {
                const response = await fetch('{{ route('picket-schedules.assignment.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        picket_area_id: areaId,
                        day_of_week: dayOfWeek,
                        employee_id: data.employeeId
                    })
                });
                const res = await response.json();
                if (res.success) {
                    const existingIndex = this.schedules.findIndex(s => s.picket_area_id == areaId && s.day_of_week == dayOfWeek && s.employee_id == data.employeeId);
                    if (existingIndex !== -1) {
                        this.schedules[existingIndex] = res.schedule;
                    } else {
                        this.schedules.push(res.schedule);
                    }
                } else {
                    alert(res.message || 'Gagal menyimpan penugasan.');
                }
            }
        } catch (e) {
            console.error(e);
        }
    },
    async removeAssignment(scheduleId) {
        try {
            const response = await fetch(`/admin/picket-schedules/assignment/${scheduleId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            const res = await response.json();
            if (res.success) {
                this.schedules = this.schedules.filter(s => s.id !== scheduleId);
            } else {
                alert(res.message || 'Gagal menghapus penugasan.');
            }
        } catch (e) {
            console.error(e);
        }
    }
}">
    <!-- HEADER SECTION -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
        <div>
            <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Manajemen Piket</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Konfigurasi jadwal mingguan, tupoksi area, dan verifikasi tukar piket</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('picket-schedules.index') }}" class="h-9 px-4 inline-flex items-center gap-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl shadow-2xs text-xs font-semibold cursor-pointer transition-colors">
                <i data-lucide="eye" class="w-4 h-4 text-indigo-500"></i>
                Lihat Matriks Jadwal
            </a>
        </div>
    </div>

    <!-- TABS NAVIGATION -->
    <div class="flex border-b border-slate-200 dark:border-slate-800 gap-6 text-xs font-bold text-slate-400">
        <button @click="tab = 'scheduler'" :class="tab === 'scheduler' ? 'text-indigo-650 dark:text-indigo-400 border-b-2 border-indigo-650 dark:border-indigo-400 pb-2.5' : 'hover:text-slate-700 dark:hover:text-slate-200 pb-2.5 cursor-pointer'" class="transition-colors">Penugasan Piket</button>
        <button @click="tab = 'areas'" :class="tab === 'areas' ? 'text-indigo-650 dark:text-indigo-400 border-b-2 border-indigo-650 dark:border-indigo-400 pb-2.5' : 'hover:text-slate-700 dark:hover:text-slate-200 pb-2.5 cursor-pointer'" class="transition-colors">Area & Tupoksi</button>
        <button @click="tab = 'swaps'" :class="tab === 'swaps' ? 'text-indigo-650 dark:text-indigo-400 border-b-2 border-indigo-650 dark:border-indigo-400 pb-2.5' : 'hover:text-slate-700 dark:hover:text-slate-200 pb-2.5 cursor-pointer'" class="transition-colors">
            Persetujuan Swap 
            @php
                $needsApprovalCount = $swaps->where('status', 'approved_by_target')->count();
            @endphp
            @if($needsApprovalCount > 0)
                <span class="ml-1 px-1.5 py-0.5 rounded-full text-[9px] bg-rose-500 text-white font-black">{{ $needsApprovalCount }}</span>
            @endif
        </button>
    </div>

    <!-- TAB 1: SCHEDULER (PENUGASAN PIKET) -->
    <div x-show="tab === 'scheduler'" class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="{ viewMode: 'board' }">
        <!-- LEFT SIDEBAR: DRAGGABLE TEACHERS -->
        <div class="space-y-6">
            <!-- Draggable Teachers List Card -->
            <div class="border border-slate-200 dark:border-slate-800 rounded-2xl p-5 bg-white dark:bg-slate-900/50 shadow-3xs">
                <h4 class="text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-350 mb-2">Daftar Guru (Seret / Drag)</h4>
                <p class="text-[10px] text-slate-450 leading-relaxed mb-4">Tarik nama guru di bawah ini dan jatuhkan ke kotak hari pada tabel di sebelah kanan.</p>
                
                <!-- Filter Search -->
                <div class="mb-3">
                    <input type="text" x-model="teacherSearch" placeholder="Cari nama guru..." class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500 transition-all">
                </div>

                <!-- Teacher cards scroll list -->
                <div class="max-h-[500px] overflow-y-auto space-y-2 pr-1 custom-scrollbar">
                    @foreach($employees as $emp)
                        <div x-show="teacherSearch === '' || '{{ addslashes(strtolower($emp->name)) }}'.includes(teacherSearch.toLowerCase())"
                            draggable="true"
                            @dragstart="event.dataTransfer.setData('text/plain', JSON.stringify({ employeeId: '{{ $emp->id }}' }))"
                            class="p-2.5 border border-slate-200 dark:border-slate-800/80 rounded-xl bg-slate-50/50 dark:bg-slate-950 hover:bg-indigo-50/50 dark:hover:bg-indigo-950/20 hover:border-indigo-300 dark:hover:border-indigo-850 flex items-center gap-2.5 cursor-grab active:cursor-grabbing transition-all select-none">
                            
                            <div class="w-6 h-6 rounded-full bg-slate-250 dark:bg-slate-800 text-[10px] font-extrabold flex items-center justify-center text-slate-600 dark:text-slate-400 shrink-0 uppercase border border-slate-300/10">
                                {{ substr($emp->raw_name, 0, 2) }}
                            </div>
                            <div class="min-w-0 leading-tight text-left">
                                <p class="font-bold text-[11px] text-slate-800 dark:text-slate-200 truncate">{{ $emp->name }}</p>
                                <p class="text-[9px] text-slate-400 font-medium truncate mt-0.5">{{ $emp->position ?? $emp->employeeType->name ?? '-' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- RIGHT MAIN AREA: BOARD DRAG-DROP OR LIST TABLE -->
        <div class="lg:col-span-2 border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-slate-900/10 overflow-hidden shadow-xs flex flex-col h-full min-h-[500px]">
            <!-- Table/Board Toggles -->
            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-slate-50 dark:bg-slate-950">
                <div>
                    <h4 class="text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-350">Papan Penjadwalan</h4>
                    <p class="text-[10px] text-slate-455 mt-0.5">Tarik-lepas kartu guru atau kelola penugasan aktif di sini</p>
                </div>
                <div class="flex items-center gap-2.5">
                    <button @click="showAssignModal = true" class="h-8 px-3 inline-flex items-center gap-1.5 bg-indigo-50 dark:bg-indigo-950/40 hover:bg-indigo-100/50 dark:hover:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 rounded-xl text-xs font-semibold cursor-pointer border-0 transition-colors">
                        <i data-lucide="plus-circle" class="w-3.5 h-3.5 text-indigo-500"></i>
                        Tugaskan Manual
                    </button>
                    <div class="flex bg-slate-200/60 dark:bg-slate-900/80 p-0.5 rounded-lg text-[10px] font-bold">
                        <button @click="viewMode = 'board'" :class="viewMode === 'board' ? 'bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 shadow-3xs' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'" class="px-3 py-1.5 rounded-md cursor-pointer border-0 transition-all">Papan Visual</button>
                        <button @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 shadow-3xs' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'" class="px-3 py-1.5 rounded-md cursor-pointer border-0 transition-all">Daftar Tabel</button>
                    </div>
                </div>
            </div>

            <!-- BOARD PANEL -->
            <div x-show="viewMode === 'board'" class="p-5 overflow-x-auto flex-1">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-350 text-left">
                            <th class="pb-3 font-black uppercase tracking-wider w-40">Area Piket</th>
                            @php
                                $days = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
                            @endphp
                            @foreach($days as $idx => $day)
                                <th class="pb-3 text-center font-black uppercase tracking-wider min-w-[125px]">{{ $day }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-850">
                        @forelse($areas->where('is_active', true) as $area)
                        <tr>
                            <!-- Area info -->
                            <td class="py-4 pr-3 align-middle font-bold text-slate-800 dark:text-slate-200 leading-tight">
                                <div class="text-[11px]">{{ $area->name }}</div>
                                <div class="text-[9px] text-slate-400 font-normal mt-1">⏰ {{ $area->duty_hours }}</div>
                            </td>
                            <!-- Days drop zones -->
                            @foreach($days as $idx => $day)
                                <td class="py-3 px-1.5 align-top">
                                    <div 
                                        class="min-h-[110px] p-2 border border-dashed rounded-xl transition-all flex flex-col gap-1.5"
                                        :class="dragOverCell === '{{ $area->id }}-{{ $idx }}' ? 'border-indigo-500 bg-indigo-500/10 scale-[1.02]' : 'border-slate-200 dark:border-slate-800 bg-slate-50/25 dark:bg-slate-900/15'"
                                        @dragover.prevent="dragOverCell = '{{ $area->id }}-{{ $idx }}'"
                                        @dragleave="dragOverCell = null"
                                        @drop="handleDrop($event, {{ $area->id }}, {{ $idx }})"
                                    >
                                        <template x-for="sched in schedules.filter(s => s.picket_area_id == {{ $area->id }} && s.day_of_week == {{ $idx }})" :key="sched.id">
                                            <!-- Assigned Teacher Card (Also Draggable to move) -->
                                            <div draggable="true"
                                                @dragstart="event.dataTransfer.setData('text/plain', JSON.stringify({ employeeId: sched.employee_id }))"
                                                class="p-2 bg-white dark:bg-slate-950 border border-slate-200/80 dark:border-slate-850 rounded-xl shadow-3xs flex items-center justify-between gap-1.5 hover:border-indigo-300 dark:hover:border-indigo-800 cursor-grab active:cursor-grabbing">
                                                <div class="min-w-0 leading-tight text-left">
                                                    <p class="font-bold text-[10px] text-slate-800 dark:text-slate-200 truncate pr-1" x-text="sched.employee_name"></p>
                                                </div>
                                                
                                                <!-- Delete button via AJAX -->
                                                <button type="button" @click="removeAssignment(sched.id)" class="h-5 w-5 inline-flex items-center justify-center text-slate-400 hover:text-rose-655 dark:hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/20 rounded-md cursor-pointer border-0 bg-transparent transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                                </button>
                                            </div>
                                        </template>

                                        <!-- Placeholder when empty -->
                                        <div x-show="schedules.filter(s => s.picket_area_id == {{ $area->id }} && s.day_of_week == {{ $idx }}).length === 0" 
                                            class="flex-1 flex items-center justify-center py-5 text-[9px] text-slate-400 select-none italic text-center leading-normal">
                                            Tarik kemari
                                        </div>
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-16 text-slate-455">Belum ada area piket aktif. Aktifkan area terlebih dahulu.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- LIST PANEL (Original table view) -->
            <div x-show="viewMode === 'list'" class="flex-1">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-350">
                            <th class="px-4 py-3.5 text-left font-black uppercase tracking-wider">Area</th>
                            <th class="px-4 py-3.5 text-left font-black uppercase tracking-wider">Hari</th>
                            <th class="px-4 py-3.5 text-left font-black uppercase tracking-wider">Guru/Karyawan</th>
                            <th class="px-4 py-3.5 text-right font-black uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-850">
                        <template x-for="sched in [...schedules].sort((a, b) => a.picket_area_id - b.picket_area_id || a.day_of_week - b.day_of_week)" :key="sched.id">
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/20 transition-colors">
                                <td class="px-4 py-3.5 font-bold text-slate-800 dark:text-slate-200" x-text="sched.picket_area_name"></td>
                                <td class="px-4 py-3.5 text-slate-600 dark:text-slate-400 font-semibold" x-text="{1: 'Senin', 2: 'Selasa', 3: 'Rabu', 4: 'Kamis', 5: 'Jumat', 6: 'Sabtu'}[sched.day_of_week] || '-'"></td>
                                <td class="px-4 py-3.5 text-slate-800 dark:text-slate-100" x-text="sched.employee_name"></td>
                                <td class="px-4 py-3.5 text-right">
                                    <button type="button" @click="removeAssignment(sched.id)" class="h-8 w-8 inline-flex items-center justify-center bg-rose-50 hover:bg-rose-600 dark:bg-rose-950/20 dark:hover:bg-rose-900/40 text-rose-700 dark:text-rose-450 hover:text-white rounded-lg border border-rose-100/10 cursor-pointer transition-all shadow-3xs">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="schedules.length === 0">
                            <td colspan="4" class="text-center py-16 text-slate-400">Belum ada penugasan piket terdaftar.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- HIDDEN FORM FOR DRAG & DROP ACTION -->
        <form id="drag-drop-form" action="{{ route('picket-schedules.assignment.store') }}" method="POST" style="display: none;">
            @csrf
            <input type="hidden" name="picket_area_id" id="dd-area-id">
            <input type="hidden" name="day_of_week" id="dd-day-of-week">
            <input type="hidden" name="employee_id" id="dd-employee-id">
        </form>
    </div>

    <!-- TAB 2: AREAS CRUD -->
    <div x-show="tab === 'areas'" class="space-y-4">
        <div class="flex justify-between items-center">
            <h4 class="text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-350">Daftar Area Piket Sekolah</h4>
            <button @click="openAreaCreate()" class="h-8 px-3 inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold cursor-pointer transition-colors shadow-2xs">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                Tambah Area
            </button>
        </div>

        <div class="border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-slate-900/10 overflow-hidden shadow-xs">
            <table class="w-full text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-350">
                        <th class="px-4 py-3.5 text-left font-black uppercase tracking-wider w-44">Nama Area</th>
                        <th class="px-4 py-3.5 text-left font-black uppercase tracking-wider w-32">Jam Tugas</th>
                        <th class="px-4 py-3.5 text-left font-black uppercase tracking-wider">Tupoksi (Jobs)</th>
                        <th class="px-4 py-3.5 text-center font-black uppercase tracking-wider w-24">Status</th>
                        <th class="px-4 py-3.5 text-right font-black uppercase tracking-wider w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-850">
                    @forelse($areas as $area)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/20 transition-colors">
                        <td class="px-4 py-3.5 font-bold text-slate-800 dark:text-slate-200">{{ $area->name }}</td>
                        <td class="px-4 py-3.5 text-slate-600 dark:text-slate-400 font-semibold">{{ $area->duty_hours }}</td>
                        <td class="px-4 py-3.5 text-slate-500 dark:text-slate-455 max-w-md truncate">
                            {{ str_replace("\n", ' | ', $area->jobs) ?: '-' }}
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            @if($area->is_active)
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200/20">Aktif</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-slate-50 dark:bg-slate-950 text-slate-500 dark:text-slate-500 border border-slate-200/20">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <button @click="openAreaEdit({{ json_encode($area) }})" class="h-8 w-8 inline-flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-900 text-slate-700 dark:text-slate-350 rounded-lg cursor-pointer transition-all shadow-3xs">
                                    <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                </button>
                                <form action="{{ route('picket-schedules.areas.destroy', $area->id) }}" method="POST" 
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus area piket ini? Seluruh jadwal terhubung akan ikut terhapus.')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="h-8 w-8 inline-flex items-center justify-center bg-rose-50 hover:bg-rose-600 dark:bg-rose-950/20 dark:hover:bg-rose-900/40 text-rose-700 dark:text-rose-450 hover:text-white rounded-lg border border-rose-100/10 cursor-pointer transition-all shadow-3xs">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-16 text-slate-400">Belum ada area piket dikonfigurasi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 3: SWAP APPROVALS -->
    <div x-show="tab === 'swaps'" class="space-y-4">
        <h4 class="text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-350">Daftar Pengajuan Tukar Jadwal Piket</h4>
        
        <div class="border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-slate-900/10 overflow-hidden shadow-xs">
            <table class="w-full text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-350">
                        <th class="px-4 py-3.5 text-left font-black uppercase tracking-wider">Guru Pengaju</th>
                        <th class="px-4 py-3.5 text-left font-black uppercase tracking-wider">Tanggal Asal</th>
                        <th class="px-4 py-3.5 text-left font-black uppercase tracking-wider">Guru Target</th>
                        <th class="px-4 py-3.5 text-left font-black uppercase tracking-wider">Tanggal Target</th>
                        <th class="px-4 py-3.5 text-left font-black uppercase tracking-wider">Catatan</th>
                        <th class="px-4 py-3.5 text-center font-black uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3.5 text-right font-black uppercase tracking-wider">Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-850">
                    @forelse($swaps as $swap)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/20 transition-colors">
                        <td class="px-4 py-3.5 font-bold text-slate-800 dark:text-slate-200">{{ $swap->requester->name }}</td>
                        <td class="px-4 py-3.5 text-slate-600 dark:text-slate-400 font-semibold">{{ $swap->requested_date->translatedFormat('d M Y') }}</td>
                        <td class="px-4 py-3.5 text-slate-800 dark:text-slate-100">{{ $swap->targetEmployee->name }}</td>
                        <td class="px-4 py-3.5 text-slate-600 dark:text-slate-400 font-semibold">{{ $swap->target_date->translatedFormat('d M Y') }}</td>
                        <td class="px-4 py-3.5 text-slate-550 dark:text-slate-455 max-w-xs truncate">{{ $swap->notes ?: '-' }}</td>
                        <td class="px-4 py-3.5 text-center">
                            @if($swap->status === 'pending')
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 border border-amber-200/20">Menunggu Target</span>
                            @elseif($swap->status === 'approved_by_target')
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-indigo-50 dark:bg-indigo-950/20 text-indigo-700 dark:text-indigo-400 border border-indigo-200/20 animate-pulse">Butuh Verifikasi Anda</span>
                            @elseif($swap->status === 'approved')
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200/20">Disetujui Resmi</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-rose-50 dark:bg-rose-950/20 text-rose-700 dark:text-rose-400 border border-rose-200/20">Ditolak/Batal</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-right">
                            @if($swap->status === 'approved_by_target')
                            <div class="flex items-center justify-end gap-1.5">
                                <form action="{{ route('picket-schedules.swap.approve-admin', $swap->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="h-7 px-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[10px] font-bold cursor-pointer transition-colors shadow-3xs">Setujui Resmi</button>
                                </form>
                                <form action="{{ route('picket-schedules.swap.reject', $swap->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="h-7 px-2.5 bg-rose-50 hover:bg-rose-600 dark:bg-rose-950/20 dark:hover:bg-rose-900/40 text-rose-700 dark:text-rose-455 hover:text-white rounded-lg text-[10px] font-bold cursor-pointer transition-colors border border-rose-100/10">Tolak</button>
                                </form>
                            </div>
                            @else
                            <span class="text-[10px] text-slate-400 italic">Selesai</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-16 text-slate-400">Belum ada pengajuan pertukaran jadwal.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- AREA CREATE/EDIT MODAL -->
    <template x-teleport="body">
        <div x-cloak x-show="showAreaModal" class="fixed inset-0 z-[9999] overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen p-4 text-center">
                <!-- Backdrop -->
                <div x-show="showAreaModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showAreaModal = false" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity"></div>

                <!-- Modal Panel -->
                <div x-show="showAreaModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative w-full max-w-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl text-left shadow-xl overflow-hidden z-10 flex flex-col">
                    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-950">
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-200" x-text="editAreaMode ? 'Edit Data Area Piket' : 'Tambah Area Piket Baru'"></h4>
                        <button @click="showAreaModal = false" class="p-1 rounded-lg text-slate-400 dark:text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-850 hover:text-slate-700 cursor-pointer border-0 bg-transparent flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        </button>
                    </div>

                    <!-- Dynamic Form Action -->
                    <form :action="editAreaMode ? '/admin/picket-schedules/areas/' + areaForm.id : '{{ route('picket-schedules.areas.store') }}'" method="POST" class="p-6 space-y-4 text-xs">
                        @csrf
                        <template x-if="editAreaMode">
                            <input type="hidden" name="_method" value="PUT">
                        </template>
                        
                        <!-- Area Name -->
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nama Area Piket</label>
                            <input type="text" name="name" x-model="areaForm.name" required placeholder="Contoh: Depan Masjid" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500 transition-all">
                        </div>

                        <!-- Duty Hours -->
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Jam Bertugas</label>
                            <input type="text" name="duty_hours" x-model="areaForm.duty_hours" required placeholder="Contoh: 06.30 - 07.00" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500 transition-all">
                        </div>

                        <!-- Area Tupoksi (Jobs) -->
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Tupoksi Kerja (Jobs) - Pisahkan dengan enter</label>
                            <textarea name="jobs" x-model="areaForm.jobs" rows="4" placeholder="Contoh:
- Menyambut siswa dengan Salam
- Mengatur ketertiban kendaraan" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500 transition-all resize-none"></textarea>
                        </div>

                        <!-- Status Active Toggle (Shown only in edit) -->
                        <template x-if="editAreaMode">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Status Area</label>
                                <select name="is_active" x-model="areaForm.is_active" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500 transition-all cursor-pointer">
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                            </div>
                        </template>

                        <!-- Actions -->
                        <div class="flex justify-end gap-2.5 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" @click="showAreaModal = false" class="h-9 px-4 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-semibold cursor-pointer transition-colors">Batal</button>
                            <button type="submit" class="h-9 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold cursor-pointer transition-colors shadow-2xs" x-text="editAreaMode ? 'Simpan Perubahan' : 'Tambah Area'"></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

    <!-- MANUAL ASSIGNMENT MODAL -->
    <template x-teleport="body">
        <div x-cloak x-show="showAssignModal" class="fixed inset-0 z-[9999] overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen p-4 text-center">
                <!-- Backdrop -->
                <div x-show="showAssignModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showAssignModal = false" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity"></div>

                <!-- Modal Panel -->
                <div x-show="showAssignModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative w-full max-w-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl text-left shadow-xl overflow-hidden z-10 flex flex-col">
                    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-950">
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-200">Tugaskan Guru Piket (Manual)</h4>
                        <button @click="showAssignModal = false" class="p-1 rounded-lg text-slate-400 dark:text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-850 hover:text-slate-700 cursor-pointer border-0 bg-transparent flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        </button>
                    </div>

                    <form action="{{ route('picket-schedules.assignment.store') }}" method="POST" class="p-6 space-y-4 text-xs">
                        @csrf
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Pilih Area Piket</label>
                            <select name="picket_area_id" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500 transition-all cursor-pointer">
                                <option value="">Pilih Area...</option>
                                @foreach($areas->where('is_active', true) as $area)
                                    <option value="{{ $area->id }}">{{ $area->name }} ({{ $area->duty_hours }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Hari Bertugas</label>
                            <select name="day_of_week" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500 transition-all cursor-pointer">
                                <option value="">Pilih Hari...</option>
                                <option value="1">Senin (Monday)</option>
                                <option value="2">Selasa (Tuesday)</option>
                                <option value="3">Rabu (Wednesday)</option>
                                <option value="4">Kamis (Thursday)</option>
                                <option value="5">Jumat (Friday)</option>
                                <option value="6">Sabtu (Saturday)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Pilih Guru / Karyawan</label>
                            <select name="employee_id" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-500 transition-all cursor-pointer">
                                <option value="">Pilih Guru...</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end gap-2.5 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" @click="showAssignModal = false" class="h-9 px-4 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-semibold cursor-pointer transition-colors">Batal</button>
                            <button type="submit" class="h-9 px-4 bg-indigo-650 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold cursor-pointer transition-colors shadow-2xs">Simpan Penugasan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>
</x-admin-layout>
