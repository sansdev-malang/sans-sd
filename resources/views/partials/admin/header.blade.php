<header id="header"
    class="sticky top-0 z-30 flex items-center justify-between px-6 py-3 bg-white/85 dark:bg-[#09090b]/85 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 transition-colors duration-200">
    <!-- Sidebar Toggle, Close internally, and Breadcrumb -->
    <div class="flex items-center gap-3">
        <button id="sidebar-toggle"
            class="p-1.5 text-slate-500 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-md cursor-pointer transition-colors"
            title="Toggle Sidebar">
            <i data-lucide="panel-left" class="w-4 h-4"></i>
        </button>
        <!-- Breadcrumbs display (sidebar-07 look) -->
        <nav
            class="hidden sm:flex items-center space-x-1.5 text-xs font-medium text-slate-400 dark:text-slate-500 select-none">
            <span class="hover:text-slate-700 dark:hover:text-slate-300 cursor-pointer">{{ setting('unit_name', 'SANS Malang') }}</span>
            <span class="text-slate-300 dark:text-slate-700">/</span>
            <span class="hover:text-slate-700 dark:hover:text-slate-300 cursor-pointer font-bold">{{ ucwords(str_replace(['_', 'hrd', 'smp', 'sd', 'paud', 'employee'], [' ', 'HRD', 'SMP', 'SD', 'PAUD', 'Pegawai'], auth()->user()->role ?? 'Admin')) }}</span>
        </nav>
    </div>

    <!-- Action items -->
    <div class="flex items-center gap-2">
        <!-- Light / Dark Switch Button -->
        <button id="theme-toggle"
            class="p-1.5 text-slate-500 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-md cursor-pointer transition-colors"
            title="Toggle Tema">
            <i data-lucide="sun" class="w-4 h-4 hidden dark:block"></i>
            <i data-lucide="moon" class="w-4 h-4 block dark:hidden"></i>
        </button>

        <!-- Notification container with Alpine dropdown -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" id="notify-btn"
                class="relative p-1.5 text-slate-500 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-md cursor-pointer transition-colors"
                title="Notifikasi">
                <i data-lucide="bell" class="w-4 h-4"></i>
                
                @php
                    $dbNotifsCount = auth()->user() ? auth()->user()->unreadNotifications->count() : 0;
                    $pendingLvsCount = isset($pendingLeavesCount) ? $pendingLeavesCount : 0;
                    $myNotifsCount = isset($myNotifications) ? count($myNotifications) : 0;
                    $totalNotifs = $dbNotifsCount + $pendingLvsCount + $myNotifsCount;
                @endphp
                
                <!-- Display badge if there are notifications -->
                @if($totalNotifs > 0)
                    <span class="absolute top-1 right-1 w-2 h-2 bg-rose-600 dark:bg-rose-400 rounded-full ring-2 ring-white dark:ring-[#09090b]"></span>
                @endif
            </button>
            
            <!-- Dropdown Menu -->
            <div x-show="open" @click.outside="open = false" 
                class="absolute right-0 mt-2 w-80 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl py-2 z-50 text-left text-xs"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                style="display: none;">
                
                <div class="px-4 py-1.5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-900/20">
                    <span class="font-bold text-slate-900 dark:text-slate-100">Notifikasi</span>
                    @if($totalNotifs > 0)
                        <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-400 border border-rose-100 dark:border-rose-900/40">
                            {{ $totalNotifs }} Baru
                        </span>
                    @endif
                </div>
                
                <div class="max-h-64 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/60">
                    @if($totalNotifs == 0)
                        <div class="p-6 text-center text-slate-400">
                            <i data-lucide="bell-off" class="w-6 h-6 mx-auto mb-1 text-slate-300 dark:text-slate-700"></i>
                            <p class="text-[10px]">Belum ada notifikasi baru.</p>
                        </div>
                    @else
                        <!-- General Database Notifications (Announcements, etc) -->
                        @if(auth()->user() && auth()->user()->unreadNotifications->count() > 0)
                            @foreach(auth()->user()->unreadNotifications as $notification)
                                <a href="{{ route('notifications.read', $notification->id) }}" class="flex items-start gap-3 p-3 hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-colors">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50/60 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-100/40 dark:border-indigo-900/20">
                                        <i data-lucide="megaphone" class="w-4 h-4"></i>
                                    </div>
                                    <div class="space-y-0.5 overflow-hidden">
                                        <p class="font-bold text-slate-800 dark:text-slate-200 truncate">{{ $notification->data['message'] ?? 'Notifikasi Baru' }}</p>
                                        <p class="text-slate-500 dark:text-slate-400 text-[10px]">{{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                </a>
                            @endforeach
                        @endif

                        <!-- Admin / Principal / Waka view: pending leave requests -->
                        @if(auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin_sd') || auth()->user()->hasRole('admin_paud') || auth()->user()->hasRole('admin_smp') || auth()->user()->hasRole('kepala_sekolah') || auth()->user()->hasRole('waka'))
                            @if(isset($pendingLeaves) && count($pendingLeaves) > 0)
                                @foreach($pendingLeaves as $item)
                                    <a href="{{ route('leave-approvals.index') }}" class="flex items-start gap-3 p-3 hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-colors">
                                        <div class="w-8 h-8 rounded-lg bg-amber-50/60 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 flex items-center justify-center shrink-0 border border-amber-100/40 dark:border-amber-900/20">
                                            <i data-lucide="file-signature" class="w-4 h-4"></i>
                                        </div>
                                        <div class="space-y-0.5 overflow-hidden">
                                            <p class="font-bold text-slate-800 dark:text-slate-200 truncate">{{ $item->employee->name ?? 'Pegawai' }}</p>
                                            <p class="text-slate-500 dark:text-slate-400 text-[10px]">Mengajukan {{ $item->type }} ({{ $item->start_date->format('d M Y') }})</p>
                                        </div>
                                    </a>
                                @endforeach
                            @endif
                        @else
                            <!-- Employee / Teacher view: approved/rejected leave requests status updates -->
                            @if(isset($myNotifications) && count($myNotifications) > 0)
                                @foreach($myNotifications as $item)
                                    <a href="{{ route('my-leaves.index') }}" class="flex items-start gap-3 p-3 hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-colors">
                                        <div class="w-8 h-8 rounded-lg {{ $item->status === 'Approved' ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-100/50 dark:border-emerald-900/30' : 'bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-400 border border-rose-100/50 dark:border-rose-900/30' }} flex items-center justify-center shrink-0">
                                            <i data-lucide="{{ $item->status === 'Approved' ? 'check-circle-2' : 'x-circle' }}" class="w-4 h-4"></i>
                                        </div>
                                        <div class="space-y-0.5 overflow-hidden">
                                            <p class="font-bold text-slate-800 dark:text-slate-200 truncate">Izin Anda {{ $item->status === 'Approved' ? 'Disetujui' : 'Ditolak' }}</p>
                                            <p class="text-slate-500 dark:text-slate-400 text-[10px] truncate">Jenis: {{ $item->type }} tanggal {{ $item->start_date->format('d M Y') }}.</p>
                                        </div>
                                    </a>
                                @endforeach
                            @endif
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</header>
