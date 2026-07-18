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
            <span class="hover:text-slate-700 dark:hover:text-slate-350 cursor-pointer">{{ setting('unit_name', 'SANS Malang') }}</span>
            <span class="text-slate-300 dark:text-slate-700">/</span>
            <span class="hover:text-slate-700 dark:hover:text-slate-350 cursor-pointer">Admin</span>
            <span class="text-slate-300 dark:text-slate-700">/</span>
            <span class="text-slate-700 dark:text-slate-200 font-semibold">Dashboard</span>
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

        <button id="notify-btn"
            class="relative p-1.5 text-slate-500 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-md cursor-pointer transition-colors"
            title="Notifikasi">
            <i data-lucide="bell" class="w-4 h-4"></i>
            <span
                class="absolute top-1 right-1 w-2 h-2 bg-slate-900 dark:bg-slate-100 rounded-full ring-2 ring-white dark:ring-[#09090b]"></span>
        </button>
    </div>
</header>