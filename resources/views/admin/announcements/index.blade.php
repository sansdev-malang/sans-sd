<x-admin-layout>
    <div class="p-4 sm:p-6 space-y-6 w-full" x-data="{ showDetailModal: false, selectedAnnouncement: {} }">
        <!-- HEADER -->
        <section class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-100 dark:border-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0 shadow-xs">
                    <i data-lucide="megaphone" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">Manajemen Pengumuman</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola semua pengumuman internal sekolah.</p>
                </div>
            </div>
            @if(auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin_sd') || auth()->user()->hasRole('admin_paud') || auth()->user()->hasRole('admin_smp') || auth()->user()->hasRole('kepala_sekolah') || auth()->user()->hasRole('waka'))
            <div>
                <a href="{{ route('announcements.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-colors cursor-pointer">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Tambah Pengumuman
                </a>
            </div>
            @endif
        </section>

        <!-- TABLE LIST (Desktop) -->
        <div class="hidden sm:block bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs overflow-hidden w-full">
            <div class="overflow-x-auto" style="max-height: calc(100vh - 240px); overflow-y: auto;">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-950/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[11px] sticky top-0 z-10">
                            <th class="px-5 py-3.5 text-left min-w-[240px]">Judul &amp; Kategori</th>
                            <th class="px-5 py-3.5 text-left w-36 whitespace-nowrap">Target</th>
                            <th class="px-5 py-3.5 text-left w-64 min-w-[210px] whitespace-nowrap">Masa Berlaku</th>
                            <th class="px-5 py-3.5 text-left w-40 whitespace-nowrap">Pembuat</th>
                            <th class="px-5 py-3.5 text-center w-28 whitespace-nowrap">Status</th>
                            <th class="px-5 py-3.5 text-right w-28 whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300 font-medium">
                        @forelse($announcements as $announcement)
                            @php
                                if ($announcement->central_id) {
                                    $creatorName = 'Yayasan';
                                    $canEditDelete = auth()->user()->hasRole('super_admin');
                                } else {
                                    $creator = $announcement->creator;
                                    if ($creator && $creator->role !== 'employee') {
                                        $unit = strtoupper(config('app.school_unit', 'sd'));
                                        $creatorName = 'Admin ' . $unit;
                                    } else {
                                        $creatorName = $creator->name ?? 'Admin';
                                    }
                                    $canEditDelete = auth()->user()->hasRole('super_admin') || 
                                                     auth()->user()->hasRole('admin_sd') || 
                                                     auth()->user()->hasRole('admin_paud') || 
                                                     auth()->user()->hasRole('admin_smp') || 
                                                     auth()->user()->hasRole('kepala_sekolah') || 
                                                     auth()->user()->hasRole('waka');
                                }
                                $creatorInitials = strtoupper(substr($creatorName, 0, 2));

                                $categoryColors = [
                                    'umum' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 border-indigo-200 dark:border-indigo-800/40',
                                    'akademik' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 border-blue-200 dark:border-blue-800/40',
                                    'kepegawaian' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800/40',
                                    'penting' => 'bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400 border-rose-200 dark:border-rose-800/40',
                                ];
                                $catColor = $categoryColors[$announcement->category] ?? 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700';

                                $audienceMap = [
                                    'global' => 'Semua',
                                    'management' => 'Manajemen',
                                    'teacher' => 'Guru',
                                    'employee' => 'Pegawai',
                                    'student' => 'Siswa',
                                    'parent' => 'Orang Tua'
                                ];
                                $audiences = explode(',', $announcement->target_audience);
                                $translatedAudiences = array_map(function($aud) use ($audienceMap) {
                                    return $audienceMap[trim($aud)] ?? trim($aud);
                                }, $audiences);
                                $displayText = implode(', ', $translatedAudiences);
                            @endphp
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-5 py-3.5 text-left">
                                    <div class="flex flex-col min-w-0 max-w-lg">
                                        <span class="text-slate-900 dark:text-slate-100 font-bold tracking-tight text-xs" title="{{ $announcement->title }}">{{ $announcement->title }}</span>
                                        <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-md text-[10px] font-semibold border capitalize w-fit {{ $catColor }}">
                                            <i data-lucide="tag" class="w-3 h-3"></i>
                                            {{ $announcement->category }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-left whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 border border-blue-200/60 dark:border-blue-900/40">
                                        <i data-lucide="users" class="w-3 h-3"></i>
                                        {{ $displayText }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-left whitespace-nowrap text-slate-600 dark:text-slate-400">
                                    <div class="flex items-center gap-1.5 mb-1 whitespace-nowrap">
                                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i>
                                        <span class="font-medium text-slate-800 dark:text-slate-200">{{ $announcement->publish_date ? $announcement->publish_date->translatedFormat('d M Y, H:i') : '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-[10px] text-slate-400 dark:text-slate-500 whitespace-nowrap">
                                        <i data-lucide="clock" class="w-3.5 h-3.5 shrink-0"></i>
                                        <span>s/d {{ $announcement->expiry_date ? $announcement->expiry_date->translatedFormat('d M Y, H:i') : 'Selamanya' }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-left whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 border border-indigo-200/20 dark:border-indigo-900/30 flex items-center justify-center font-bold text-[10px] uppercase shrink-0">
                                            {{ $creatorInitials }}
                                        </div>
                                        <span class="text-slate-700 dark:text-slate-300 font-medium truncate max-w-[120px]">{{ $creatorName }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                    @if($announcement->is_active)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20 uppercase">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 uppercase">
                                            Draft
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1">
                                        <!-- Lihat Detail -->
                                        <button data-announcement="{{ json_encode($announcement) }}" @click.prevent="selectedAnnouncement = JSON.parse($el.dataset.announcement); selectedAnnouncement.attachment_url = '{{ $announcement->attachment ? (filter_var($announcement->attachment, FILTER_VALIDATE_URL) ? $announcement->attachment : Storage::url($announcement->attachment)) : '' }}'; selectedAnnouncement.creator_name = '{{ $creatorName }}'; selectedAnnouncement.formatted_publish_date = '{{ $announcement->publish_date ? $announcement->publish_date->translatedFormat('d M Y, H:i') : '-' }}'; showDetailModal = true; $nextTick(() => lucide.createIcons())" class="inline-flex items-center justify-center p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 transition-colors cursor-pointer" title="Lihat Detail">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </button>

                                        @if($canEditDelete)
                                            <!-- Edit -->
                                            <a href="{{ route('announcements.edit', $announcement) }}" class="inline-flex items-center justify-center p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 transition-colors cursor-pointer" title="Edit">
                                                <i data-lucide="edit" class="w-4 h-4"></i>
                                            </a>

                                            <!-- Hapus -->
                                            <form action="{{ route('announcements.destroy', $announcement) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center p-1.5 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg text-rose-500 dark:text-rose-400 hover:text-rose-700 transition-colors cursor-pointer" title="Hapus">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                                            <i data-lucide="megaphone" class="w-6 h-6"></i>
                                        </div>
                                        <p class="text-xs font-medium">Belum ada data pengumuman yang dapat ditampilkan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($announcements->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $announcements->links() }}
            </div>
            @endif
        </div>

        <!-- MOBILE LIST (Mobile View) -->
        <div class="block sm:hidden space-y-4">
            @forelse($announcements as $announcement)
                @php
                    if ($announcement->central_id) {
                        $creatorName = 'Yayasan';
                        $canEditDelete = auth()->user()->hasRole('super_admin');
                    } else {
                        $creator = $announcement->creator;
                        if ($creator && $creator->role !== 'employee') {
                            $unit = strtoupper(config('app.school_unit', 'sd'));
                            $creatorName = 'Admin ' . $unit;
                        } else {
                            $creatorName = $creator->name ?? 'Admin';
                        }
                        $canEditDelete = auth()->user()->hasRole('super_admin') || 
                                         auth()->user()->hasRole('admin_sd') || 
                                         auth()->user()->hasRole('admin_paud') || 
                                         auth()->user()->hasRole('admin_smp') || 
                                         auth()->user()->hasRole('kepala_sekolah') || 
                                         auth()->user()->hasRole('waka');
                    }
                    $creatorInitials = strtoupper(substr($creatorName, 0, 2));

                    $categoryColors = [
                        'umum' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 border-indigo-200/50 dark:border-indigo-900/30',
                        'akademik' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 border-blue-200/55 dark:border-blue-900/30',
                        'kepegawaian' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border-emerald-200/55 dark:border-emerald-900/30',
                        'penting' => 'bg-rose-50 text-rose-700 dark:bg-rose-900/20 dark:text-rose-400 border-rose-200/55 dark:border-rose-900/30 animate-pulse',
                    ];
                    $catColor = $categoryColors[$announcement->category] ?? 'bg-slate-50 text-slate-700 dark:bg-slate-900 dark:text-slate-300 border-slate-200 dark:border-slate-800';

                    $audienceMap = [
                        'global' => 'Semua',
                        'management' => 'Manajemen',
                        'teacher' => 'Guru',
                        'employee' => 'Pegawai',
                        'student' => 'Siswa',
                        'parent' => 'Orang Tua'
                    ];
                    $audiences = explode(',', $announcement->target_audience);
                    $translatedAudiences = array_map(function($aud) use ($audienceMap) {
                        return $audienceMap[trim($aud)] ?? trim($aud);
                    }, $audiences);
                    $displayText = implode(', ', $translatedAudiences);
                @endphp
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-xs space-y-3.5 text-left">
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium border {{ $catColor }} capitalize">
                            {{ $announcement->category }}
                        </span>
                        <div>
                            @if($announcement->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200/20 shadow-xs">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-50 dark:bg-slate-900 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-800 shadow-xs">
                                    Draft
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 leading-snug">{{ $announcement->title }}</h3>
                        <div class="flex items-center gap-1 text-[10px] text-slate-400 dark:text-slate-500">
                            <span>Oleh: {{ $creatorName }}</span>
                            <span>•</span>
                            <span>{{ $announcement->publish_date ? $announcement->publish_date->translatedFormat('d M Y, H:i') : '-' }}</span>
                        </div>
                    </div>
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800/60 flex items-center justify-between">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] text-slate-400 dark:text-slate-500">Target:</span>
                            <span class="text-[10px] font-semibold text-slate-600 dark:text-slate-400">{{ $displayText }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <!-- Detail View -->
                            <button data-announcement="{{ json_encode($announcement) }}" @click.prevent="selectedAnnouncement = JSON.parse($el.dataset.announcement); selectedAnnouncement.attachment_url = '{{ $announcement->attachment ? (filter_var($announcement->attachment, FILTER_VALIDATE_URL) ? $announcement->attachment : Storage::url($announcement->attachment)) : '' }}'; selectedAnnouncement.creator_name = '{{ $creatorName }}'; selectedAnnouncement.formatted_publish_date = '{{ $announcement->publish_date ? $announcement->publish_date->translatedFormat('d M Y, H:i') : '-' }}'; showDetailModal = true; $nextTick(() => lucide.createIcons())" class="inline-flex items-center justify-center p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 transition-colors cursor-pointer" title="Lihat Detail">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                            @if($canEditDelete)
                                <!-- Edit -->
                                <a href="{{ route('announcements.edit', $announcement) }}" class="inline-flex items-center justify-center p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 transition-colors cursor-pointer" title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </a>
                                <!-- Hapus -->
                                <form action="{{ route('announcements.destroy', $announcement) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center p-1.5 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg text-rose-600 dark:text-rose-400 hover:text-rose-700 transition-colors cursor-pointer" title="Hapus">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-8 text-center text-slate-500 dark:text-slate-400">
                    <div class="flex flex-col items-center justify-center gap-2">
                        <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                            <i data-lucide="megaphone" class="w-6 h-6"></i>
                        </div>
                        <p class="text-xs font-medium">Belum ada data pengumuman yang dapat ditampilkan.</p>
                    </div>
                </div>
            @endforelse
            
            @if($announcements->hasPages())
            <div class="mt-4">
                {{ $announcements->links() }}
            </div>
            @endif
        </div>

        <!-- Detail Announcement Modal -->
        <div x-show="showDetailModal" style="display: none;" class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-16 sm:pt-24" x-cloak>
            <div x-show="showDetailModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="showDetailModal = false"></div>
            <div x-show="showDetailModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="relative w-full max-w-2xl bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xl p-6 overflow-hidden z-10 flex flex-col max-h-[80vh]">
                <div class="flex justify-between items-start border-b border-slate-100 dark:border-slate-800 pb-4 mb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-slate-100" x-text="selectedAnnouncement.title"></h3>
                        <div class="flex flex-wrap gap-2 items-center text-[10px] text-slate-500 dark:text-slate-400 mt-2">
                            <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 font-medium capitalize" x-text="selectedAnnouncement.category"></span>
                            <span class="px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 font-medium" x-text="selectedAnnouncement.target_audience === 'global' ? 'Semua' : (selectedAnnouncement.target_audience === 'management' ? 'Manajemen' : (selectedAnnouncement.target_audience === 'teacher' ? 'Guru Saja' : (selectedAnnouncement.target_audience === 'employee' ? 'Pegawai/Staf' : (selectedAnnouncement.target_audience === 'student' ? 'Siswa (API)' : 'Orang Tua (API)'))))"></span>
                            <span class="flex items-center gap-1">
                                <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span x-text="selectedAnnouncement.formatted_publish_date"></span>
                            </span>
                            <span class="flex items-center gap-1">
                                <i data-lucide="user" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span x-text="selectedAnnouncement.creator_name"></span>
                            </span>
                        </div>
                    </div>
                    <button @click="showDetailModal = false" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto pr-1 text-slate-700 dark:text-slate-300 text-sm leading-relaxed whitespace-normal prose prose-slate dark:prose-invert max-w-none" x-html="selectedAnnouncement.content"></div>
                <!-- Attachment Area in Modal -->
                <div x-show="selectedAnnouncement.attachment" class="border-t border-slate-100 dark:border-slate-800 pt-4 mt-4 space-y-3">
                    <!-- Image Preview -->
                    <template x-if="selectedAnnouncement.attachment && ['png', 'jpg', 'jpeg', 'webp'].includes(selectedAnnouncement.attachment.split('.').pop().toLowerCase())">
                        <div class="relative max-w-lg rounded-xl overflow-hidden border border-slate-200/60 dark:border-slate-800/80 shadow-sm bg-slate-50 dark:bg-slate-950/40 p-2">
                            <img :src="selectedAnnouncement.attachment_url" alt="Lampiran Pengumuman" class="rounded-lg object-contain w-full max-h-48 mx-auto">
                        </div>
                    </template>

                    <!-- Download panel box -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-3 bg-slate-50 dark:bg-slate-950/40 border border-slate-200/50 dark:border-slate-800/60 rounded-xl gap-4">
                        <div class="flex items-center gap-3 overflow-hidden text-left">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 border border-slate-200/40 dark:border-slate-800/40"
                                 :class="selectedAnnouncement.attachment && ['png', 'jpg', 'jpeg', 'webp'].includes(selectedAnnouncement.attachment.split('.').pop().toLowerCase()) ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400' : 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400'">
                                
                                <template x-if="selectedAnnouncement.attachment && ['png', 'jpg', 'jpeg', 'webp'].includes(selectedAnnouncement.attachment.split('.').pop().toLowerCase())">
                                    <i data-lucide="image" class="w-4.5 h-4.5"></i>
                                </template>
                                <template x-if="selectedAnnouncement.attachment && !['png', 'jpg', 'jpeg', 'webp'].includes(selectedAnnouncement.attachment.split('.').pop().toLowerCase())">
                                    <i data-lucide="file-text" class="w-4.5 h-4.5"></i>
                                </template>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate" 
                                   x-text="selectedAnnouncement.attachment ? (selectedAnnouncement.attachment.split('/').pop().length > 24 ? selectedAnnouncement.attachment.split('/').pop().substr(0, 12) + '...' + selectedAnnouncement.attachment.split('/').pop().substr(-12) : selectedAnnouncement.attachment.split('/').pop()) : ''"></p>
                                <p class="text-[9px] text-slate-400 dark:text-slate-500 uppercase" x-text="selectedAnnouncement.attachment ? selectedAnnouncement.attachment.split('.').pop() + ' file' : ''"></p>
                            </div>
                        </div>
                        <a :href="'/announcements/' + selectedAnnouncement.id + '/download'" class="inline-flex items-center justify-center gap-1.5 h-8 px-3.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-xs hover:shadow-md transition-all duration-100 cursor-pointer w-full sm:w-auto shrink-0">
                            <i data-lucide="download" class="w-3.5 h-3.5"></i>
                            <span>Unduh</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
