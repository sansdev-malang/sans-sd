<x-admin-layout>
    <div class="p-6 space-y-6" x-data="alumniApp()">

        <!-- GREETING / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full text-left">
            <div class="flex flex-col gap-0.5">
                <div class="flex items-center gap-2.5">
                    <div class="p-2 bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-xl border border-blue-500/20">
                        <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 flex items-center gap-2">
                            Buku Induk Alumni
                            <span class="text-xs px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-400 font-semibold border border-blue-200 dark:border-blue-800">
                                Lulusan SD
                            </span>
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Database resmi seluruh peserta didik yang telah lulus dari {{ setting('app_name', 'SD Anak Saleh') }}, arsip nomor ijazah, dan sekolah lanjutan.</p>
                    </div>
                </div>
            </div>
            <!-- ACTION CONTROLS -->
            <div class="flex items-center gap-2.5">
                <a href="{{ route('promotions.index') }}"
                    class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 shadow-xs transition-all">
                    <i data-lucide="arrow-up-circle" class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400"></i>
                    Kelulusan Kelas 6
                </a>
                <a href="{{ route('students.index') }}"
                    class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all">
                    <i data-lucide="users" class="w-3.5 h-3.5"></i>
                    Data Siswa Aktif
                </a>
            </div>
        </section>

        <!-- STATS CARDS KHUSUS ALUMNI -->
        <section class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-xs flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Alumni</p>
                        <h3 class="text-xl font-black tracking-tight text-slate-900 dark:text-slate-50 mt-1">
                            {{ number_format($stats['total_alumni']) }}
                        </h3>
                    </div>
                    <div class="p-2 bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 rounded-lg border border-blue-100 dark:border-blue-900/50">
                        <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-2 text-[10px] text-slate-400">Siswa yang telah dinyatakan lulus</div>
            </div>

            <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-xs flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Putra (L)</p>
                        <h3 class="text-xl font-black tracking-tight text-blue-700 dark:text-blue-300 mt-1">
                            {{ number_format($stats['male']) }}
                        </h3>
                    </div>
                    <div class="p-2 bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 rounded-lg border border-blue-100 dark:border-blue-900/50">
                        <i data-lucide="user" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-2 text-[10px] text-slate-400">Alumni putra</div>
            </div>

            <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-xs flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider">Putri (P)</p>
                        <h3 class="text-xl font-black tracking-tight text-rose-700 dark:text-rose-300 mt-1">
                            {{ number_format($stats['female']) }}
                        </h3>
                    </div>
                    <div class="p-2 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 rounded-lg border border-rose-100 dark:border-rose-900/50">
                        <i data-lucide="user-check" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-2 text-[10px] text-slate-400">Alumni putri</div>
            </div>

            <div class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-xs flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Tercatat No. Ijazah</p>
                        <h3 class="text-xl font-black tracking-tight text-emerald-700 dark:text-emerald-300 mt-1">
                            {{ number_format($stats['with_diploma']) }}
                        </h3>
                    </div>
                    <div class="p-2 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-lg border border-emerald-100 dark:border-emerald-900/50">
                        <i data-lucide="file-check" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-2 text-[10px] text-slate-400">Telah memiliki nomor seri ijazah</div>
            </div>
        </section>

        <!-- FILTERS -->
        <section class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3.5 shadow-xs w-full">
            <form method="GET" action="{{ route('alumni.index') }}" class="flex flex-col lg:flex-row gap-2.5 items-stretch lg:items-center justify-between">
                <!-- Search Box -->
                <div class="relative w-full lg:max-w-md">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NIS, NISN, NIK, Nama Alumni, No. Ijazah, SMP Lanjutan..."
                        style="padding-left: 2.25rem;"
                        class="w-full h-8.5 pr-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-slate-900 dark:text-slate-50 placeholder-slate-400 shadow-inner">
                </div>

                <!-- Filter Toolbar -->
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Filter Tahun Pelajaran Kelulusan -->
                    <select name="academic_year_id" onchange="this.form.submit()"
                        class="h-8.5 px-3 text-xs font-semibold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 cursor-pointer shadow-xs">
                        <option value="all">Semua Tahun Pelajaran Kelulusan</option>
                        @foreach($academicYears as $ay)
                            <option value="{{ $ay->id }}" {{ request('academic_year_id') == $ay->id ? 'selected' : '' }}>
                                TP {{ $ay->name }} {{ $ay->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Filter Gender -->
                    <select name="gender" onchange="this.form.submit()"
                        class="h-8.5 px-3 text-xs font-medium bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500/20 cursor-pointer shadow-xs">
                        <option value="all">Semua Gender</option>
                        <option value="L" {{ request('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ request('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>

                    @if(request()->hasAny(['search', 'academic_year_id', 'graduation_year', 'gender']))
                        <a href="{{ route('alumni.index') }}" 
                            class="h-8.5 px-2.5 inline-flex items-center justify-center text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 rounded-lg border border-slate-200 dark:border-slate-700 transition-colors"
                            title="Reset Filter">
                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                        </a>
                    @endif
                </div>
            </form>
        </section>

        <!-- TABLE BUKU INDUK ALUMNI -->
        <section class="animate-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs overflow-hidden transition-all w-full">
            <div class="overflow-x-auto">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-900/50">
                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase w-12">No</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase w-28">NIS / NISN</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase">Nama Alumni</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase w-24">L/P</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase w-48">Tahun Pelajaran & No. Ijazah</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase w-52">Sekolah Lanjutan (SMP/MTs)</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase w-40">Kontak Orang Tua / Alumni</th>
                            <th class="px-4 py-3 text-right text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        @forelse($students as $index => $s)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors group">
                                <td class="px-4 py-3 text-slate-400 font-mono text-[11px]">
                                    {{ $students->firstItem() + $index }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col font-mono">
                                        <span class="font-bold text-blue-600 dark:text-blue-400 text-xs">{{ $s->nis }}</span>
                                        <span class="text-[10px] text-slate-400">{{ $s->nisn ?: '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-950/50 border border-blue-100 dark:border-blue-900/50 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ $s->avatar_initials }}
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-bold text-slate-900 dark:text-slate-100 text-xs hover:text-blue-600 cursor-pointer" @click="openDetailModal({{ $s->id }})">
                                                {{ $s->full_name }}
                                            </span>
                                            <div class="flex items-center gap-1.5 text-[10px] text-slate-400">
                                                @if($s->nik)
                                                     <span>NIK: {{ $s->nik }}</span>
                                                @endif
                                                @if($s->student_type && str_contains(strtoupper($s->student_type), 'PDBK'))
                                                    <span class="px-1 py-0.2 rounded bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 font-bold">PDBK</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">
                                    {{ $s->formatted_gender }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col text-xs">
                                        <span class="font-bold text-blue-700 dark:text-blue-300">
                                            TP {{ $s->academicYear?->name ?: ($s->graduation_year ? "Tahun {$s->graduation_year}" : '-') }}
                                        </span>
                                        <span class="text-[11px] font-mono text-slate-500 dark:text-slate-400 truncate max-w-[170px]" title="{{ $s->diploma_number }}">
                                            {{ $s->diploma_number ?: 'Ijazah: (Belum diisi)' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-slate-800 dark:text-slate-200 font-medium truncate max-w-[180px] block" title="{{ $s->continued_school }}">
                                        {{ $s->continued_school ?: '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col text-xs">
                                        <span class="text-slate-700 dark:text-slate-300 truncate max-w-[140px]">{{ $s->father_name ?: ($s->mother_name ?: '-') }}</span>
                                        @if($s->clean_parent_phone)
                                            <span class="text-[10px] font-mono text-emerald-600 dark:text-emerald-400">{{ $s->parent_phone }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('alumni.print', $s->id) }}" target="_blank"
                                            class="p-1.5 hover:bg-blue-50 dark:hover:bg-blue-950/40 text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg transition-colors cursor-pointer"
                                            title="Cetak Lembar Buku Induk Alumni (A4 / PDF)">
                                            <i data-lucide="printer" class="w-4 h-4"></i>
                                        </a>
                                        <button type="button" @click="openEditAlumniModal({{ json_encode($s) }})"
                                            class="p-1.5 hover:bg-amber-50 dark:hover:bg-amber-950/40 text-slate-500 hover:text-amber-600 dark:hover:text-amber-400 rounded-lg transition-colors cursor-pointer"
                                            title="Edit Info Ijazah & SMP Lanjutan">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </button>
                                        <button type="button" @click="openDetailModal({{ $s->id }})"
                                            class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-lg transition-colors cursor-pointer"
                                            title="Lihat Rekam Jejak Lengkap">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <i data-lucide="graduation-cap" class="w-8 h-8 text-slate-300 dark:text-slate-600 mb-2"></i>
                                        <p class="font-semibold text-slate-600 dark:text-slate-400">Belum ada data alumni yang tercatat</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Siswa yang diluluskan melalui menu Kenaikan & Kelulusan akan otomatis tampil di sini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($students->hasPages())
                <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between">
                    {{ $students->links() }}
                </div>
            @endif
        </section>

        <!-- MODAL EDIT INFO ALUMNI & IJAZAH -->
        <div x-show="editAlumniModalOpen" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4" style="display: none; margin-top: 0px !important; z-index: 9999; background-color: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px);">
            <div @click.outside="editAlumniModalOpen = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl flex flex-col">
                <form @submit.prevent="submitAlumniForm">
                    <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50/80 dark:bg-slate-900/80">
                        <div class="flex items-center gap-2.5">
                            <div class="p-2 bg-blue-600 text-white rounded-xl font-bold">
                                <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-slate-50">Update Info Alumni & Ijazah</h3>
                                <p class="text-xs text-slate-400" x-text="alumniForm.full_name"></p>
                            </div>
                        </div>
                        <button type="button" @click="editAlumniModalOpen = false" class="p-2 text-slate-400 hover:text-slate-600 rounded-lg">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <div class="p-6 space-y-3.5 text-xs">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tahun Pelajaran Kelulusan <span class="text-rose-500">*</span></label>
                                <select x-model="alumniForm.academic_year_id" required
                                    class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg font-bold">
                                    @foreach($academicYears as $ay)
                                        <option value="{{ $ay->id }}">TP {{ $ay->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor Seri Ijazah</label>
                                <input type="text" x-model="alumniForm.diploma_number" placeholder="No. DN-xx/xx..."
                                    class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg font-mono">
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Sekolah Lanjutan (SMP / MTs Tujuan)</label>
                            <input type="text" x-model="alumniForm.continued_school" placeholder="Contoh: SMP Negeri 1 Malang, SMP Anak Saleh..."
                                class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Kesiswaan</label>
                            <select x-model="alumniForm.status" required
                                class="w-full h-8.5 px-3 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg font-bold">
                                <option value="lulus">Lulus (Alumni)</option>
                                <option value="aktif">Aktif Kembali</option>
                                <option value="mutasi">Mutasi Keluar</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Catatan Tambahan Alumni</label>
                            <textarea x-model="alumniForm.notes" rows="2" placeholder="Catatan kelulusan..."
                                class="w-full p-2.5 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg"></textarea>
                        </div>
                    </div>

                    <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/80 flex justify-end gap-2">
                        <button type="button" @click="editAlumniModalOpen = false" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 rounded-lg text-xs font-semibold">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold shadow-xs">
                            Simpan Info Alumni
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- Alpine Logic -->
    <script>
        function alumniApp() {
            return {
                editAlumniModalOpen: false,
                alumniForm: {
                    id: null,
                    full_name: '',
                    status: 'lulus',
                    academic_year_id: null,
                    graduation_year: '',
                    diploma_number: '',
                    continued_school: '',
                    notes: '',
                },

                openEditAlumniModal(student) {
                    this.alumniForm = {
                        id: student.id,
                        full_name: student.full_name,
                        status: student.status || 'lulus',
                        academic_year_id: student.academic_year_id || '{{ $academicYears->first()?->id }}',
                        graduation_year: student.graduation_year || '',
                        diploma_number: student.diploma_number || '',
                        continued_school: student.continued_school || '',
                        notes: student.notes || '',
                    };
                    this.editAlumniModalOpen = true;
                },

                submitAlumniForm() {
                    fetch(`/alumni/${this.alumniForm.id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.alumniForm)
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            alert(res.message);
                            this.editAlumniModalOpen = false;
                            window.location.reload();
                        } else {
                            alert('Gagal: ' + res.message);
                        }
                    })
                    .catch(err => alert('Error: ' + err.message));
                },

                openDetailModal(id) {
                    window.location.href = `/students?search=${id}`;
                }
            };
        }
    </script>
</x-admin-layout>
