<x-admin-layout>
    <div class="p-6 space-y-6 max-w-4xl mx-auto">
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

        <!-- Back Navigation & Edit Button Row -->
        <div class="flex justify-between items-center w-full">
            <a href="{{ route('announcements.index') }}" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-900 hover:text-slate-900 dark:hover:text-slate-50 shadow-xs transition duration-100 cursor-pointer">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Kembali</span>
            </a>
            
            @if($canEditDelete)
            <a href="{{ route('announcements.edit', $announcement) }}" class="inline-flex items-center justify-center gap-2 px-3.5 py-1.5 bg-slate-900 dark:bg-slate-50 hover:bg-slate-800 dark:hover:bg-slate-200 text-white dark:text-slate-900 text-xs font-semibold rounded-lg shadow-sm transition duration-100 cursor-pointer">
                <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                <span>Edit Pengumuman</span>
            </a>
            @endif
        </div>

        @php
            $categoryColors = [
                'umum' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 border-indigo-200/55 dark:border-indigo-900/30',
                'akademik' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 border-blue-200/55 dark:border-blue-900/30',
                'kepegawaian' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border-emerald-200/55 dark:border-emerald-900/30',
                'penting' => 'bg-rose-50 text-rose-700 dark:bg-rose-900/20 dark:text-rose-400 border-rose-200/55 dark:border-rose-900/30 animate-pulse',
            ];
            $categoryLabels = [
                'umum' => 'Umum',
                'akademik' => 'Akademik',
                'kepegawaian' => 'Kepegawaian',
                'penting' => 'Penting / Urgent',
            ];
            $categoryIcons = [
                'umum' => 'info',
                'akademik' => 'book-open',
                'kepegawaian' => 'briefcase',
                'penting' => 'alert-triangle',
            ];
            $catColor = $categoryColors[$announcement->category] ?? 'bg-slate-50 text-slate-700 dark:bg-slate-900 dark:text-slate-300 border-slate-200 dark:border-slate-800';
            $catLabel = $categoryLabels[$announcement->category] ?? ucfirst($announcement->category);
            $catIcon = $categoryIcons[$announcement->category] ?? 'tag';

            $targetMapping = [
                'global' => 'Semua (Global)',
                'management' => 'Manajemen Saja',
                'teacher' => 'Guru Saja',
                'employee' => 'Pegawai/Staf Saja',
                'student' => 'Siswa (API)',
                'parent' => 'Orang Tua (API)',
            ];
            $targetLabel = $targetMapping[$announcement->target_audience] ?? $announcement->target_audience;
        @endphp

        <!-- ARTICLE CARD -->
        <article class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-md overflow-hidden w-full animate-fade-in">
            
            <!-- HEADER INFO BANNER -->
            <div class="h-1.5 w-full @if($announcement->category === 'penting') bg-rose-500 @elseif($announcement->category === 'kepegawaian') bg-emerald-500 @elseif($announcement->category === 'akademik') bg-blue-500 @else bg-indigo-500 @endif"></div>

            <div class="p-6 md:p-10 space-y-6">
                <!-- Meta information row -->
                <div class="flex flex-wrap items-center gap-3 text-xs">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold tracking-wide uppercase border {{ $catColor }}">
                        <i data-lucide="{{ $catIcon }}" class="w-3.5 h-3.5"></i>
                        {{ $catLabel }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold tracking-wide uppercase bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 border border-blue-200/50 dark:border-blue-900/30">
                        <i data-lucide="users" class="w-3.5 h-3.5"></i>
                        {{ $targetLabel }}
                    </span>
                    <div class="h-4 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
                    <span class="flex items-center gap-1.5 text-slate-500 dark:text-slate-400 font-medium">
                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                        {{ $announcement->publish_date ? $announcement->publish_date->format('d M Y, H:i') : $announcement->created_at->format('d M Y') }}
                    </span>
                </div>

                <!-- Title -->
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight">
                    {{ $announcement->title }}
                </h1>

                <!-- Creator Badge & Status -->
                <div class="flex items-center justify-between border-y border-slate-100 dark:border-slate-900/80 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-400 border border-indigo-200/20 dark:border-indigo-900/20 flex items-center justify-center font-bold text-xs uppercase shadow-xs">
                            {{ $creatorInitials }}
                        </div>
                        <div class="text-left">
                            <p class="text-[10px] text-slate-400 dark:text-slate-500">Dibuat Oleh</p>
                            <p class="text-xs font-semibold text-slate-800 dark:text-slate-200">{{ $creatorName }}</p>
                        </div>
                    </div>
                    
                    <div class="text-right">
                        @if($announcement->is_active)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200/30 dark:border-emerald-900/30 shadow-xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-slate-50 dark:bg-slate-900 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-800 shadow-xs">
                                Draft
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Rich Text Content Area -->
                <div class="text-slate-700 dark:text-slate-300 text-sm leading-relaxed whitespace-normal prose prose-slate dark:prose-invert max-w-none focus:outline-none">
                    {!! $announcement->content !!}
                </div>

                <!-- Attachment Area -->
                @if($announcement->attachment)
                    @php
                        $ext = strtolower(pathinfo($announcement->attachment, PATHINFO_EXTENSION));
                        $isImage = in_array($ext, ['png', 'jpg', 'jpeg', 'webp']);
                        $attachmentUrl = filter_var($announcement->attachment, FILTER_VALIDATE_URL) 
                            ? $announcement->attachment 
                            : Storage::url($announcement->attachment);
                        $rawFilename = basename($announcement->attachment);
                        // Truncate hashed filename visually
                        $cleanFilename = strlen($rawFilename) > 24 
                            ? substr($rawFilename, 0, 12) . '...' . substr($rawFilename, -12) 
                            : $rawFilename;
                    @endphp
                    
                    <div class="mt-8 pt-8 border-t border-slate-100 dark:border-slate-900 space-y-4">
                        <div class="flex items-center gap-2">
                            <i data-lucide="paperclip" class="w-4 h-4 text-indigo-500 dark:text-indigo-400"></i>
                            <h3 class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">File Lampiran</h3>
                        </div>

                        <!-- Image Preview -->
                        @if($isImage)
                            <div class="relative max-w-lg rounded-xl overflow-hidden border border-slate-200/60 dark:border-slate-800/80 shadow-sm bg-slate-50 dark:bg-slate-900/30 p-2">
                                <img src="{{ $attachmentUrl }}" alt="Lampiran Pengumuman" class="rounded-lg object-contain w-full max-h-64 mx-auto">
                            </div>
                        @endif

                        <!-- Download panel box -->
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 bg-slate-50 dark:bg-slate-900/30 border border-slate-200/50 dark:border-slate-800/60 rounded-xl gap-4">
                            <div class="flex items-center gap-3 overflow-hidden text-left">
                                <div class="w-10 h-10 rounded-lg @if($isImage) bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 @else bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 @endif border border-slate-200/40 dark:border-slate-800/40 flex items-center justify-center shrink-0">
                                    @if($isImage)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                            <circle cx="8.5" cy="8.5" r="1.5"/>
                                            <polyline points="21 15 16 10 5 21"/>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                                            <polyline points="14 2 14 8 20 8"/>
                                            <line x1="16" y1="13" x2="8" y2="13"/>
                                            <line x1="16" y1="17" x2="8" y2="17"/>
                                            <line x1="10" y1="9" x2="8" y2="9"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">{{ $cleanFilename }}</p>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase">{{ $ext }} file</p>
                                </div>
                            </div>
                            <a href="{{ route('announcements.download', $announcement) }}" class="inline-flex items-center justify-center gap-2 h-9 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-xs hover:shadow-md transition-all duration-100 cursor-pointer w-full sm:w-auto shrink-0">
                                <i data-lucide="download" class="w-4 h-4"></i>
                                <span>Unduh Lampiran</span>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </article>
    </div>
</x-admin-layout>
