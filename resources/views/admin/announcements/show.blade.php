<x-admin-layout>
    <div class="p-6 space-y-6">
        <section class="flex justify-between items-center">
            <a href="{{ route('announcements.index') }}" class="inline-flex items-center text-sm font-medium text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Kembali
            </a>
        </section>

        <div class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-sm p-6 lg:p-10 max-w-4xl mx-auto">
            <div class="flex justify-between items-start mb-6">
                <div class="flex flex-wrap gap-2 items-center text-xs text-slate-500">
                    <span class="px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 font-medium capitalize">{{ $announcement->category }}</span>
                    <span class="px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 font-medium capitalize">{{ $announcement->target_audience }}</span>
                    <span class="flex items-center">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 mr-1"></i>
                        {{ $announcement->publish_date ? $announcement->publish_date->format('d M Y, H:i') : $announcement->created_at->format('d M Y') }}
                    </span>
                    <span class="flex items-center">
                        <i data-lucide="user" class="w-3.5 h-3.5 mr-1"></i>
                        {{ $announcement->creator->name ?? 'Admin' }}
                    </span>
                </div>
                
                @if(auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin_sd') || auth()->user()->hasRole('admin_paud') || auth()->user()->hasRole('admin_smp') || auth()->user()->hasRole('kepala_sekolah') || auth()->user()->hasRole('waka'))
                <a href="{{ route('announcements.edit', $announcement) }}" class="inline-flex items-center px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-md text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition shadow-sm">
                    <i data-lucide="edit" class="w-3.5 h-3.5 mr-1.5"></i> Edit
                </a>
                @endif
            </div>

            <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-6">{{ $announcement->title }}</h1>
            
            <div class="prose prose-slate dark:prose-invert max-w-none mb-8 whitespace-normal">{!! $announcement->content !!}</div>

            @if($announcement->attachment)
                <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-800">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white mb-3">Lampiran:</h3>
                    <a href="{{ Storage::url($announcement->attachment) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition">
                        <i data-lucide="paperclip" class="w-4 h-4 mr-2"></i>
                        Unduh File Lampiran
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
