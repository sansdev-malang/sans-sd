<x-admin-layout>
    <div class="p-3.5 sm:p-5 lg:p-6 space-y-4 sm:space-y-5 lg:space-y-6 text-left" 
         x-data="{ 
             showDetailModal: false, 
             selectedAnnouncement: {}, 
             searchQuery: '{{ request('search', '') }}', 
             selectedCategory: '{{ request('category', 'all') }}' 
         }">
         
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
            <div>
                <h1 class="text-base sm:text-lg font-bold tracking-tight text-slate-900 dark:text-slate-50">Pengumuman</h1>
                <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pusat informasi dan pengumuman resmi internal sekolah.</p>
            </div>
            @if(auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin_sd') || auth()->user()->hasRole('admin_paud') || auth()->user()->hasRole('admin_smp') || auth()->user()->hasRole('kepala_sekolah') || auth()->user()->hasRole('waka'))
            <div class="flex items-center gap-2">
                <a href="{{ route('announcements.create') }}" class="h-8 sm:h-9 px-3 sm:px-3.5 inline-flex items-center justify-center gap-1.5 text-xs font-semibold rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs transition-colors cursor-pointer">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>Tambah Pengumuman</span>
                </a>
            </div>
            @endif
        </div>

        <!-- Filter & Search Bar Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 sm:p-4 shadow-sm">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <!-- Search Box -->
                <div class="relative flex-1 max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </div>
                    <input 
                        type="text" 
                        x-model="searchQuery"
                        placeholder="Cari judul atau isi pengumuman..." 
                        class="w-full h-8 sm:h-9 pl-9 pr-8 text-xs bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                    >
                    <button 
                        type="button"
                        x-show="searchQuery.length > 0" 
                        @click="searchQuery = ''" 
                        class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer"
                        style="display: none;"
                    >
                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                    </button>
                </div>

                <!-- Category Filter Pills -->
                <div class="flex items-center gap-1 sm:gap-1.5 overflow-x-auto pb-1 sm:pb-0 scrollbar-none">
                    <button 
                        type="button"
                        @click="selectedCategory = 'all'"
                        :class="selectedCategory === 'all' ? 'bg-indigo-600 text-white font-semibold shadow-xs' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-400 font-medium'"
                        class="h-7 sm:h-8 px-2.5 sm:px-3 rounded-lg text-xs transition-colors shrink-0 cursor-pointer">
                        Semua
                    </button>
                    <button 
                        type="button"
                        @click="selectedCategory = 'umum'"
                        :class="selectedCategory === 'umum' ? 'bg-indigo-600 text-white font-semibold shadow-xs' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-400 font-medium'"
                        class="h-7 sm:h-8 px-2.5 sm:px-3 rounded-lg text-xs transition-colors shrink-0 cursor-pointer">
                        Umum
                    </button>
                    <button 
                        type="button"
                        @click="selectedCategory = 'akademik'"
                        :class="selectedCategory === 'akademik' ? 'bg-indigo-600 text-white font-semibold shadow-xs' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-400 font-medium'"
                        class="h-7 sm:h-8 px-2.5 sm:px-3 rounded-lg text-xs transition-colors shrink-0 cursor-pointer">
                        Akademik
                    </button>
                    <button 
                        type="button"
                        @click="selectedCategory = 'kepegawaian'"
                        :class="selectedCategory === 'kepegawaian' ? 'bg-indigo-600 text-white font-semibold shadow-xs' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-400 font-medium'"
                        class="h-7 sm:h-8 px-2.5 sm:px-3 rounded-lg text-xs transition-colors shrink-0 cursor-pointer">
                        Kepegawaian
                    </button>
                    <button 
                        type="button"
                        @click="selectedCategory = 'penting'"
                        :class="selectedCategory === 'penting' ? 'bg-indigo-600 text-white font-semibold shadow-xs' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-400 font-medium'"
                        class="h-7 sm:h-8 px-2.5 sm:px-3 rounded-lg text-xs transition-colors shrink-0 cursor-pointer">
                        Penting
                    </button>
                </div>
            </div>
        </div>

        <!-- TABLE LIST (Desktop) -->
        <div class="hidden sm:block bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden w-full">
            <div class="px-4 sm:px-5 py-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">Daftar Pengumuman</h3>
                <span class="text-[11px] text-slate-400 dark:text-slate-500 font-medium">Total: {{ $announcements->total() }} Pengumuman</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs border-collapse">
                    <thead class="bg-slate-50 dark:bg-[#0d0d10] border-b border-slate-200 dark:border-slate-800 text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider text-[10px] sticky top-0 z-10">
                        <tr>
                            <th class="px-4 sm:px-5 py-3 text-left min-w-[240px]">Judul &amp; Kategori</th>
                            <th class="px-4 sm:px-5 py-3 text-left w-36 whitespace-nowrap">Target</th>
                            <th class="px-4 sm:px-5 py-3 text-left w-56 min-w-[200px] whitespace-nowrap">Masa Berlaku</th>
                            <th class="px-4 sm:px-5 py-3 text-left w-36 whitespace-nowrap">Pembuat</th>
                            <th class="px-4 sm:px-5 py-3 text-center w-24 whitespace-nowrap">Status</th>
                            <th class="px-4 sm:px-5 py-3 text-right w-24 whitespace-nowrap">Aksi</th>
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
                                    'umum' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 border-indigo-200/60 dark:border-indigo-800/40',
                                    'akademik' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 border-blue-200/60 dark:border-blue-800/40',
                                    'kepegawaian' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border-emerald-200/60 dark:border-emerald-800/40',
                                    'penting' => 'bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400 border-rose-200/60 dark:border-rose-800/40',
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
                                $rawSnippet = html_entity_decode(strip_tags($announcement->content ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                $cleanSnippet = preg_replace('/[\s\x{00a0}]+/u', ' ', trim($rawSnippet));
                                $titleJson = json_encode(mb_strtolower($announcement->title, 'UTF-8'));
                                $snippetJson = json_encode(mb_strtolower($cleanSnippet, 'UTF-8'));
                            @endphp
                            <tr x-show="(selectedCategory === 'all' || selectedCategory === '{{ strtolower($announcement->category) }}') && (!searchQuery || {{ $titleJson }}.includes(searchQuery.toLowerCase()) || {{ $snippetJson }}.includes(searchQuery.toLowerCase()))"
                                class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-4 sm:px-5 py-3.5 text-left">
                                    <div class="flex flex-col min-w-0 max-w-lg">
                                        <span class="text-slate-900 dark:text-slate-100 font-bold tracking-tight text-xs" title="{{ $announcement->title }}">{{ $announcement->title }}</span>
                                        @if(!empty($cleanSnippet))
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-normal line-clamp-1 mt-0.5" title="{{ $cleanSnippet }}">
                                                {{ \Illuminate\Support\Str::limit($cleanSnippet, 85) }}
                                            </p>
                                        @endif
                                        <div class="mt-1.5">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-semibold border capitalize w-fit {{ $catColor }}">
                                                <i data-lucide="tag" class="w-3 h-3"></i>
                                                {{ $announcement->category }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 sm:px-5 py-3.5 text-left whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-semibold bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 border border-blue-200/60 dark:border-blue-900/40">
                                        <i data-lucide="users" class="w-3 h-3"></i>
                                        {{ $displayText }}
                                    </span>
                                </td>
                                <td class="px-4 sm:px-5 py-3.5 text-left whitespace-nowrap text-slate-600 dark:text-slate-400">
                                    <div class="flex items-center gap-1.5 whitespace-nowrap">
                                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i>
                                        <span class="font-medium text-slate-800 dark:text-slate-200 font-mono text-xs">{{ $announcement->publish_date ? $announcement->publish_date->translatedFormat('d M Y, H:i') : '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-[10px] text-slate-400 dark:text-slate-500 whitespace-nowrap mt-0.5">
                                        <i data-lucide="clock" class="w-3.5 h-3.5 shrink-0"></i>
                                        <span>s/d {{ $announcement->expiry_date ? $announcement->expiry_date->translatedFormat('d M Y, H:i') : 'Selamanya' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 sm:px-5 py-3.5 text-left whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 border border-indigo-200/20 dark:border-indigo-900/30 flex items-center justify-center font-bold text-[9px] uppercase shrink-0">
                                            {{ $creatorInitials }}
                                        </div>
                                        <span class="text-slate-700 dark:text-slate-300 font-medium text-xs truncate max-w-[120px]">{{ $creatorName }}</span>
                                    </div>
                                </td>
                                <td class="px-4 sm:px-5 py-3.5 text-center whitespace-nowrap">
                                    @if($announcement->is_active)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-500/20 uppercase">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 uppercase">
                                            Draft
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 sm:px-5 py-3.5 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1">
                                        <!-- Lihat Detail -->
                                        <button data-announcement="{{ json_encode($announcement) }}" @click.prevent="selectedAnnouncement = JSON.parse($el.dataset.announcement); selectedAnnouncement.attachment_url = '{{ $announcement->attachment ? (filter_var($announcement->attachment, FILTER_VALIDATE_URL) ? $announcement->attachment : Storage::url($announcement->attachment)) : '' }}'; selectedAnnouncement.creator_name = '{{ $creatorName }}'; selectedAnnouncement.formatted_publish_date = '{{ $announcement->publish_date ? $announcement->publish_date->translatedFormat('d M Y, H:i') : '-' }}'; showDetailModal = true; $nextTick(() => { if (window.lucide) lucide.createIcons(); })" class="h-7 w-7 rounded-lg inline-flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer" title="Lihat Detail">
                                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                        </button>

                                        @if($canEditDelete)
                                            <!-- Edit -->
                                            <a href="{{ route('announcements.edit', $announcement) }}" class="h-7 w-7 rounded-lg inline-flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer" title="Edit">
                                                <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                                            </a>

                                            <!-- Hapus -->
                                            <form action="{{ route('announcements.destroy', $announcement) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="h-7 w-7 rounded-lg inline-flex items-center justify-center text-rose-500 hover:text-rose-700 hover:bg-rose-50 dark:text-rose-400 dark:hover:text-rose-300 dark:hover:bg-rose-950/30 transition-colors cursor-pointer" title="Hapus">
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
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
                                        <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                                            <i data-lucide="megaphone" class="w-5 h-5"></i>
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
            <div class="p-3.5 sm:p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $announcements->links() }}
            </div>
            @endif
        </div>

        <!-- MOBILE LIST (Mobile View) -->
        <div class="block sm:hidden space-y-3">
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
                        'umum' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 border-indigo-200/60 dark:border-indigo-800/40',
                        'akademik' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 border-blue-200/60 dark:border-blue-800/40',
                        'kepegawaian' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border-emerald-200/60 dark:border-emerald-800/40',
                        'penting' => 'bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400 border-rose-200/60 dark:border-rose-800/40',
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
                    $rawSnippet = html_entity_decode(strip_tags($announcement->content ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $cleanSnippet = preg_replace('/[\s\x{00a0}]+/u', ' ', trim($rawSnippet));
                    $titleJson = json_encode(mb_strtolower($announcement->title, 'UTF-8'));
                    $snippetJson = json_encode(mb_strtolower($cleanSnippet, 'UTF-8'));
                @endphp
                <div x-show="(selectedCategory === 'all' || selectedCategory === '{{ strtolower($announcement->category) }}') && (!searchQuery || {{ $titleJson }}.includes(searchQuery.toLowerCase()) || {{ $snippetJson }}.includes(searchQuery.toLowerCase()))"
                     class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3.5 shadow-sm space-y-3 text-left">
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-semibold border capitalize {{ $catColor }}">
                            <i data-lucide="tag" class="w-3 h-3"></i>
                            {{ $announcement->category }}
                        </span>
                        <div>
                            @if($announcement->is_active)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200/40">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-800">
                                    Draft
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-1">
                        <h3 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-slate-100 leading-snug">{{ $announcement->title }}</h3>
                        @if(!empty($cleanSnippet))
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-normal line-clamp-2 leading-relaxed">
                                {{ \Illuminate\Support\Str::limit($cleanSnippet, 120) }}
                            </p>
                        @endif
                        <div class="flex items-center gap-1.5 text-[10px] text-slate-400 dark:text-slate-500 pt-0.5">
                            <span class="font-medium text-slate-600 dark:text-slate-400">Oleh: {{ $creatorName }}</span>
                            <span>•</span>
                            <span>{{ $announcement->publish_date ? $announcement->publish_date->translatedFormat('d M Y, H:i') : '-' }}</span>
                        </div>
                    </div>

                    <div class="pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div class="flex items-center gap-1">
                            <span class="text-[10px] text-slate-400 dark:text-slate-500">Target:</span>
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 text-[10px] font-semibold border border-blue-200/50 dark:border-blue-900/30">
                                {{ $displayText }}
                            </span>
                        </div>
                        <div class="flex items-center gap-1">
                            <!-- Detail View -->
                            <button data-announcement="{{ json_encode($announcement) }}" @click.prevent="selectedAnnouncement = JSON.parse($el.dataset.announcement); selectedAnnouncement.attachment_url = '{{ $announcement->attachment ? (filter_var($announcement->attachment, FILTER_VALIDATE_URL) ? $announcement->attachment : Storage::url($announcement->attachment)) : '' }}'; selectedAnnouncement.creator_name = '{{ $creatorName }}'; selectedAnnouncement.formatted_publish_date = '{{ $announcement->publish_date ? $announcement->publish_date->translatedFormat('d M Y, H:i') : '-' }}'; showDetailModal = true; $nextTick(() => { if (window.lucide) lucide.createIcons(); })" class="h-7 w-7 rounded-lg inline-flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer" title="Lihat Detail">
                                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                            </button>
                            @if($canEditDelete)
                                <!-- Edit -->
                                <a href="{{ route('announcements.edit', $announcement) }}" class="h-7 w-7 rounded-lg inline-flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer" title="Edit">
                                    <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                                </a>
                                <!-- Hapus -->
                                <form action="{{ route('announcements.destroy', $announcement) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="h-7 w-7 rounded-lg inline-flex items-center justify-center text-rose-500 hover:text-rose-700 hover:bg-rose-50 dark:text-rose-400 dark:hover:text-rose-300 dark:hover:bg-rose-950/30 transition-colors cursor-pointer" title="Hapus">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 text-center text-slate-500 dark:text-slate-400">
                    <div class="flex flex-col items-center justify-center gap-2">
                        <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                            <i data-lucide="megaphone" class="w-5 h-5"></i>
                        </div>
                        <p class="text-xs font-medium">Belum ada data pengumuman yang dapat ditampilkan.</p>
                    </div>
                </div>
            @endforelse
            
            @if($announcements->hasPages())
            <div class="mt-3">
                {{ $announcements->links() }}
            </div>
            @endif
        </div>

        <!-- Detail Announcement Modal -->
        <div x-show="showDetailModal" style="display: none;" class="fixed inset-0 z-50 flex items-start justify-center p-3.5 sm:p-4 pt-12 sm:pt-20" x-cloak>
            <div x-show="showDetailModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="showDetailModal = false"></div>
            <div x-show="showDetailModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="relative w-full max-w-2xl bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xl p-4 sm:p-6 overflow-hidden z-10 flex flex-col max-h-[85vh] text-left">
                <div class="flex justify-between items-start border-b border-slate-100 dark:border-slate-800 pb-3 mb-3 sm:pb-4 sm:mb-4">
                    <div class="space-y-1.5 pr-4">
                        <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-slate-100 leading-snug" x-text="selectedAnnouncement.title"></h3>
                        <div class="flex flex-wrap gap-2 items-center text-[10px] text-slate-500 dark:text-slate-400">
                            <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 font-semibold capitalize" x-text="selectedAnnouncement.category"></span>
                            <span class="px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 font-semibold" x-text="selectedAnnouncement.target_audience === 'global' ? 'Semua' : (selectedAnnouncement.target_audience === 'management' ? 'Manajemen' : (selectedAnnouncement.target_audience === 'teacher' ? 'Guru Saja' : (selectedAnnouncement.target_audience === 'employee' ? 'Pegawai/Staf' : (selectedAnnouncement.target_audience === 'student' ? 'Siswa (API)' : 'Orang Tua (API)'))))"></span>
                            <span class="flex items-center gap-1 font-mono">
                                <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span x-text="selectedAnnouncement.formatted_publish_date"></span>
                            </span>
                            <span class="flex items-center gap-1">
                                <i data-lucide="user" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span x-text="selectedAnnouncement.creator_name"></span>
                            </span>
                        </div>
                    </div>
                    <button @click="showDetailModal = false" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer shrink-0">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                
                <div class="flex-1 overflow-y-auto pr-1 text-slate-700 dark:text-slate-300 text-xs sm:text-sm leading-relaxed whitespace-normal prose prose-slate dark:prose-invert max-w-none" x-html="selectedAnnouncement.content"></div>
                
                <!-- Attachment Area in Modal -->
                <div x-show="selectedAnnouncement.attachment" class="border-t border-slate-100 dark:border-slate-800 pt-3 mt-3 sm:pt-4 sm:mt-4 space-y-3">
                    <!-- Image Preview -->
                    <template x-if="selectedAnnouncement.attachment && ['png', 'jpg', 'jpeg', 'webp'].includes(selectedAnnouncement.attachment.split('.').pop().toLowerCase())">
                        <div class="relative max-w-lg rounded-xl overflow-hidden border border-slate-200/60 dark:border-slate-800/80 shadow-sm bg-slate-50 dark:bg-slate-950/40 p-2 mx-auto">
                            <img :src="selectedAnnouncement.attachment_url" alt="Lampiran Pengumuman" class="rounded-lg object-contain w-full max-h-48 mx-auto">
                        </div>
                    </template>

                    <!-- Download panel box -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-3 bg-slate-50 dark:bg-slate-950/40 border border-slate-200/50 dark:border-slate-800/60 rounded-xl gap-3">
                        <div class="flex items-center gap-2.5 overflow-hidden text-left">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 border border-slate-200/40 dark:border-slate-800/40"
                                 :class="selectedAnnouncement.attachment && ['png', 'jpg', 'jpeg', 'webp'].includes(selectedAnnouncement.attachment.split('.').pop().toLowerCase()) ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400' : 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400'">
                                <template x-if="selectedAnnouncement.attachment && ['png', 'jpg', 'jpeg', 'webp'].includes(selectedAnnouncement.attachment.split('.').pop().toLowerCase())">
                                    <i data-lucide="image" class="w-4 h-4"></i>
                                </template>
                                <template x-if="selectedAnnouncement.attachment && !['png', 'jpg', 'jpeg', 'webp'].includes(selectedAnnouncement.attachment.split('.').pop().toLowerCase())">
                                    <i data-lucide="file-text" class="w-4 h-4"></i>
                                </template>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate" 
                                   x-text="selectedAnnouncement.attachment ? (selectedAnnouncement.attachment.split('/').pop().length > 24 ? selectedAnnouncement.attachment.split('/').pop().substr(0, 12) + '...' + selectedAnnouncement.attachment.split('/').pop().substr(-12) : selectedAnnouncement.attachment.split('/').pop()) : ''"></p>
                                <p class="text-[9px] text-slate-400 dark:text-slate-500 uppercase font-mono" x-text="selectedAnnouncement.attachment ? selectedAnnouncement.attachment.split('.').pop() + ' file' : ''"></p>
                            </div>
                        </div>
                        <a :href="'/announcements/' + selectedAnnouncement.id + '/download'" class="inline-flex items-center justify-center gap-1.5 h-8 px-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-xs hover:shadow-md transition-all duration-100 cursor-pointer w-full sm:w-auto shrink-0">
                            <i data-lucide="download" class="w-3.5 h-3.5"></i>
                            <span>Unduh</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
