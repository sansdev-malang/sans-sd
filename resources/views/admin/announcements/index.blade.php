<x-admin-layout>
    <div class="p-6 space-y-6" x-data="{ showDetailModal: false, selectedAnnouncement: {} }">
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Manajemen Pengumuman</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Kelola semua pengumuman internal sekolah.</p>
            </div>
            @if(auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin_sd') || auth()->user()->hasRole('admin_paud') || auth()->user()->hasRole('admin_smp') || auth()->user()->hasRole('kepala_sekolah') || auth()->user()->hasRole('waka'))
            <div>
                <a href="{{ route('announcements.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-900 dark:bg-slate-50 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition-all duration-100 cursor-pointer">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    Tambah Pengumuman
                </a>
            </div>
            @endif
        </section>



        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-slate-500 dark:text-slate-400">
                    <thead class="text-xs text-slate-700 dark:text-slate-300 uppercase bg-slate-50 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 font-semibold">Judul & Kategori</th>
                            <th scope="col" class="px-6 py-3.5 font-semibold">Target</th>
                            <th scope="col" class="px-6 py-3.5 font-semibold">Masa Berlaku</th>
                            <th scope="col" class="px-6 py-3.5 font-semibold">Pembuat</th>
                            <th scope="col" class="px-6 py-3.5 font-semibold">Status</th>
                            <th scope="col" class="px-6 py-3.5 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($announcements as $announcement)
                            @php
                                if ($announcement->central_id) {
                                    $creatorName = 'Yayasan';
                                    $canEditDelete = auth()->user()->hasRole('super_admin');
                                } else {
                                    $creator = $announcement->creator;
                                    if ($creator && $creator->role !== 'employee') {
                                        $creatorName = 'Admin SD';
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
                            @endphp
                            <tr class="bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-900 dark:text-white mb-1.5" title="{{ $announcement->title }}">{{ \Illuminate\Support\Str::limit($announcement->title, 50) }}</div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 capitalize">
                                        <i data-lucide="tag" class="w-3 h-3 mr-1"></i>
                                        {{ $announcement->category }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-900/30">
                                        <i data-lucide="users" class="w-3 h-3 mr-1"></i>
                                        @php
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
                                        {{ $displayText }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-600 dark:text-slate-400">
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i>
                                        <span>{{ $announcement->publish_date ? $announcement->publish_date->format('d M Y, H:i') : '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-[11px] text-slate-400 dark:text-slate-500">
                                        <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                        <span>s/d {{ $announcement->expiry_date ? $announcement->expiry_date->format('d M Y, H:i') : 'Selamanya' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-indigo-50 dark:bg-indigo-900/40 text-indigo-755 dark:text-indigo-400 border border-indigo-200/20 dark:border-indigo-900/30 flex items-center justify-center font-bold text-xs uppercase">
                                            {{ $creatorInitials }}
                                        </div>
                                        <span class="text-xs font-medium text-slate-700 dark:text-slate-300">{{ $creatorName }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($announcement->is_active)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200/30 dark:border-emerald-900/30 shadow-sm">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-50 dark:bg-slate-900 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800 shadow-sm">
                                            Draft
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right relative overflow-visible">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Lihat Detail -->
                                        <div class="relative" x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false">
                                             <button data-announcement="{{ json_encode($announcement) }}" @click.prevent="selectedAnnouncement = JSON.parse($el.dataset.announcement); selectedAnnouncement.attachment_url = '{{ $announcement->attachment ? (filter_var($announcement->attachment, FILTER_VALIDATE_URL) ? $announcement->attachment : Storage::url($announcement->attachment)) : '' }}'; selectedAnnouncement.creator_name = '{{ $creatorName }}'; selectedAnnouncement.formatted_publish_date = '{{ $announcement->publish_date ? $announcement->publish_date->format('d M Y, H:i') : '-' }}'; showDetailModal = true; $nextTick(() => lucide.createIcons())" class="inline-flex items-center justify-center p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 transition-colors cursor-pointer">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                             </button>
                                             <div x-show="hover" style="display: none;" x-cloak class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 bg-slate-900 dark:bg-slate-900 text-slate-50 dark:text-slate-100 text-[10px] font-medium px-2 py-1 rounded-md shadow-md whitespace-nowrap z-50 pointer-events-none transition-all duration-100">
                                                 Lihat Detail
                                             </div>
                                        </div>

                                        @if($canEditDelete)
                                            <!-- Edit -->
                                            <div class="relative" x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false">
                                                <a href="{{ route('announcements.edit', $announcement) }}" class="inline-flex items-center justify-center p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 transition-colors cursor-pointer">
                                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                                </a>
                                                <div x-show="hover" style="display: none;" x-cloak class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 bg-slate-900 dark:bg-slate-900 text-slate-50 dark:text-slate-100 text-[10px] font-medium px-2 py-1 rounded-md shadow-md whitespace-nowrap z-50 pointer-events-none transition-all duration-100">
                                                    Edit
                                                </div>
                                            </div>

                                            <!-- Hapus -->
                                            <div class="relative" x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false">
                                                <form action="{{ route('announcements.destroy', $announcement) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center justify-center p-1.5 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg text-rose-600 dark:text-rose-400 hover:text-rose-700 transition-colors cursor-pointer">
                                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                    </button>
                                                </form>
                                                <div x-show="hover" style="display: none;" x-cloak class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 bg-slate-900 dark:bg-slate-900 text-slate-50 dark:text-slate-100 text-[10px] font-medium px-2 py-1 rounded-md shadow-md whitespace-nowrap z-50 pointer-events-none transition-all duration-100">
                                                    Hapus
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <i data-lucide="megaphone" class="w-8 h-8 text-slate-300 dark:text-slate-700"></i>
                                        <p class="text-xs">Belum ada data pengumuman yang dapat ditampilkan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $announcements->links() }}
            </div>
        </div>

        <!-- Detail Announcement Modal -->
        <div x-show="showDetailModal" style="display: none;" class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-16 sm:pt-24" x-cloak>
            <div x-show="showDetailModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs" @click="showDetailModal = false"></div>
            <div x-show="showDetailModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="relative w-full max-w-2xl bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xl p-6 overflow-hidden z-10 flex flex-col max-h-[80vh]">
                <div class="flex justify-between items-start border-b border-slate-100 dark:border-slate-900 pb-4 mb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white" x-text="selectedAnnouncement.title"></h3>
                        <div class="flex flex-wrap gap-2 items-center text-[10px] text-slate-500 mt-2">
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
                    <button @click="showDetailModal = false" class="p-1 rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-900 transition-colors cursor-pointer">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto pr-1 text-slate-700 dark:text-slate-300 text-sm leading-relaxed whitespace-normal prose prose-slate dark:prose-invert max-w-none" x-html="selectedAnnouncement.content"></div>
                <!-- Attachment Area in Modal -->
                <div x-show="selectedAnnouncement.attachment" class="border-t border-slate-100 dark:border-slate-900 pt-4 mt-4 space-y-3">
                    <!-- Image Preview -->
                    <template x-if="selectedAnnouncement.attachment && ['png', 'jpg', 'jpeg', 'webp'].includes(selectedAnnouncement.attachment.split('.').pop().toLowerCase())">
                        <div class="relative max-w-lg rounded-xl overflow-hidden border border-slate-200/60 dark:border-slate-800/80 shadow-sm bg-slate-50 dark:bg-slate-900/30 p-2">
                            <img :src="selectedAnnouncement.attachment_url" alt="Lampiran Pengumuman" class="rounded-lg object-contain w-full max-h-48 mx-auto">
                        </div>
                    </template>

                    <!-- Download panel box -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-3 bg-slate-50 dark:bg-slate-900/30 border border-slate-200/50 dark:border-slate-800/60 rounded-xl gap-4">
                        <div class="flex items-center gap-3 overflow-hidden text-left">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 border border-slate-200/40 dark:border-slate-800/40"
                                 :class="selectedAnnouncement.attachment && ['png', 'jpg', 'jpeg', 'webp'].includes(selectedAnnouncement.attachment.split('.').pop().toLowerCase()) ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400' : 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400'">
                                
                                <!-- SVG for Image -->
                                <template x-if="selectedAnnouncement.attachment && ['png', 'jpg', 'jpeg', 'webp'].includes(selectedAnnouncement.attachment.split('.').pop().toLowerCase())">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                        <circle cx="8.5" cy="8.5" r="1.5"/>
                                        <polyline points="21 15 16 10 5 21"/>
                                    </svg>
                                </template>
                                <!-- SVG for Document -->
                                <template x-if="selectedAnnouncement.attachment && !['png', 'jpg', 'jpeg', 'webp'].includes(selectedAnnouncement.attachment.split('.').pop().toLowerCase())">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                        <line x1="16" y1="13" x2="8" y2="13"/>
                                        <line x1="16" y1="17" x2="8" y2="17"/>
                                        <line x1="10" y1="9" x2="8" y2="9"/>
                                    </svg>
                                </template>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate" 
                                   x-text="selectedAnnouncement.attachment ? (selectedAnnouncement.attachment.split('/').pop().length > 24 ? selectedAnnouncement.attachment.split('/').pop().substr(0, 12) + '...' + selectedAnnouncement.attachment.split('/').pop().substr(-12) : selectedAnnouncement.attachment.split('/').pop()) : ''"></p>
                                <p class="text-[9px] text-slate-400 dark:text-slate-500 uppercase" x-text="selectedAnnouncement.attachment ? selectedAnnouncement.attachment.split('.').pop() + ' file' : ''"></p>
                            </div>
                        </div>
                        <a :href="'/announcements/' + selectedAnnouncement.id + '/download'" class="inline-flex items-center justify-center gap-1.5 h-8 px-3.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-xs hover:shadow-md transition-all duration-100 cursor-pointer w-full sm:w-auto shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="7 10 12 15 17 10"/>
                                <line x1="12" y1="15" x2="12" y2="3"/>
                            </svg>
                            <span>Unduh</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
