<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @if(setting('app_favicon'))
            <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . setting('app_favicon')) }}">
        @else
            <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Cdefs%3E%3ClinearGradient id='grad' x1='0%25' y1='0%25' x2='100%25' y2='100%25'%3E%3Cstop offset='0%25' stop-color='%23e91d1d'/%3E%3Cstop offset='25%25' stop-color='%23f3e40f'/%3E%3Cstop offset='50%25' stop-color='%2333ee0d'/%3E%3Cstop offset='75%25' stop-color='%23070be2'/%3E%3Cstop offset='100%25' stop-color='%238a18c5'/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width='32' height='32' rx='8' fill='url(%23grad)'/%3E%3Ctext x='50%25' y='55%25' dominant-baseline='middle' text-anchor='middle' fill='%23ffffff' font-family='sans-serif' font-weight='900' font-size='18'%3E{{ substr(setting('app_name', 'SANS'), 0, 1) }}%3C/text%3E%3C/svg%3E">
        @endif
        <title>{{ setting('app_name', 'SANS Malang') }} - Admin Dashboard</title>

        <!-- Google Fonts: Inter & Plus Jakarta Sans -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link href="https://fonts.cdnfonts.com/css/nasalization" rel="stylesheet">

        <!-- Load Tailwind v4 styling compiled by Vite / CDN Fallback -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
            <!-- Alpine.js + Collapse CDN Fallback -->
            <script src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js" defer></script>
            <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        @endif

        <script>
            // Support tailwind CDN dark mode class configuration
            if (typeof tailwind !== 'undefined') {
                tailwind.config = {
                    darkMode: 'class'
                }
            }
            // Apply saved theme or default to dark
            if (localStorage.getItem('color-theme') === 'light' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: light)').matches)) {
                document.documentElement.classList.remove('dark');
            } else {
                document.documentElement.classList.add('dark');
            }
        </script>

        <style>
            body {
                font-family: 'Inter', 'Plus Jakarta Sans', sans-serif;
            }
            /* Custom shadcn-like scrollbar */
            ::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }
            ::-webkit-scrollbar-track {
                background: transparent;
            }
            ::-webkit-scrollbar-thumb {
                background: rgba(100, 116, 139, 0.2);
                border-radius: 99px;
            }
            ::-webkit-scrollbar-thumb:hover {
                background: rgba(100, 116, 139, 0.45);
            }
            /* Smooth Desktop Sidebar Transitions (Mini-Sidebar / Collapsed view) */
            @media (min-width: 768px) {
                #sidebar {
                    transition: width 0.2s cubic-bezier(0.4, 0, 0.2, 1), padding 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
                }
                .sidebar-collapsed #sidebar {
                    width: 4rem !important; /* w-16 */
                    padding-left: 0.5rem !important;
                    padding-right: 0.5rem !important;
                }
                .sidebar-collapsed .menu-text,
                .sidebar-collapsed .school-info,
                .sidebar-collapsed .chevron-icon,
                .sidebar-collapsed .user-info {
                    display: none !important;
                }
                .sidebar-collapsed .menu-item {
                    justify-content: center !important;
                    padding-left: 0 !important;
                    padding-right: 0 !important;
                }
                .sidebar-collapsed .workspace-selector {
                    justify-content: center !important;
                    padding-left: 0 !important;
                    padding-right: 0 !important;
                }
                .sidebar-collapsed .user-selector {
                    justify-content: center !important;
                    padding-left: 0 !important;
                    padding-right: 0 !important;
                }
                .sidebar-collapsed #sidebar .flex-1 {
                    overflow: visible !important;
                }
                .sidebar-collapsed .sidebar-tooltip {
                    display: block;
                }
            }
            /* Hidden by default, shown on hover when collapsed */
            .sidebar-tooltip {
                display: none;
            }
            /* Hide scrollbar for Chrome, Safari and Opera */
            .no-scrollbar::-webkit-scrollbar {
                display: none;
            }
            /* Hide scrollbar for IE, Edge and Firefox */
            .no-scrollbar {
                -ms-overflow-style: none;  /* IE and Edge */
                scrollbar-width: none;  /* Firefox */
            }

            /* Animated gradient logo background */
            @keyframes gradient-bg {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            .logo-gradient-bg {
                background: linear-gradient(135deg, #e91d1d, #f3e40fff, #33ee0dee, #070be2ff, #8a18c5ff);
                background-size: 400% 400%;
                animation: gradient-bg 16s ease-in-out infinite;
            }
        </style>
    </head>
    <body class="h-full bg-slate-50 dark:bg-[#09090b] text-[#09090b] dark:text-slate-50 antialiased overflow-x-hidden transition-colors duration-200">
        <div class="flex h-screen overflow-hidden">

            <!-- SIDEBAR BACKDROP FOR MOBILE -->
            <div id="sidebar-backdrop" class="fixed inset-0 z-35 bg-slate-950/20 backdrop-blur-xs hidden md:hidden transition-opacity duration-200 opacity-0"></div>

            <!-- SIDEBAR -->
            @include('partials.admin.sidebar')

            <!-- MAIN LAYOUT -->
            <main class="flex-1 flex flex-col overflow-y-auto">
                <!-- HEADER -->
                @include('partials.admin.header')

                <!-- MAIN CONTENT CONTAINER -->
                {{ $slot }}
            </main>
        </div>

        <!-- TOAST NOTIFICATION CONTAINER -->
        <div id="toast-notification" class="fixed bottom-5 right-5 z-50 hidden bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-50 px-4 py-3 rounded-xl shadow-lg border border-slate-200 dark:border-slate-800 flex items-center gap-3 max-w-sm">
            <div id="toast-icon-bg" class="w-8 h-8 rounded-full flex items-center justify-center shrink-0">
                <i id="toast-icon" data-lucide="check" class="w-4 h-4"></i>
            </div>
            <div class="text-left">
                <h5 id="toast-title" class="text-xs font-bold">Notifikasi</h5>
                <p id="toast-message" class="text-xs text-slate-500 dark:text-slate-400"></p>
            </div>
        </div>

        <!-- Anime.js CDN -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.2/anime.min.js" referrerpolicy="no-referrer"></script>

        <!-- Lucide Icons CDN -->
        <script src="https://unpkg.com/lucide@latest"></script>
        <script>
            // Initialize Lucide Icons
            lucide.createIcons();

            // Global Toast helper function
            function showToast(title, message, type = 'success') {
                const toast = document.getElementById('toast-notification');
                const titleEl = document.getElementById('toast-title');
                const messageEl = document.getElementById('toast-message');
                const iconBg = document.getElementById('toast-icon-bg');
                const icon = document.getElementById('toast-icon');
                
                if (!toast) return;

                titleEl.textContent = title;
                messageEl.textContent = message;

                if (type === 'success') {
                    iconBg.className = 'w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0';
                    icon.setAttribute('data-lucide', 'check');
                } else {
                    iconBg.className = 'w-8 h-8 rounded-full bg-rose-100 dark:bg-rose-955/20 text-rose-600 dark:text-rose-455 flex items-center justify-center shrink-0';
                    icon.setAttribute('data-lucide', 'alert-circle');
                }

                if (window.lucide) {
                    window.lucide.createIcons();
                }

                toast.classList.remove('hidden');
                
                if (window.anime) {
                    window.anime({
                        targets: toast,
                        translateX: [300, 0],
                        opacity: [0, 1],
                        duration: 400,
                        easing: 'easeOutExpo'
                    });

                    setTimeout(() => {
                        window.anime({
                            targets: toast,
                            translateX: [0, 300],
                            opacity: [1, 0],
                            duration: 400,
                            easing: 'easeInExpo',
                            complete: () => {
                                toast.classList.add('hidden');
                            }
                        });
                    }, 4000);
                } else {
                    setTimeout(() => {
                        toast.classList.add('hidden');
                    }, 4000);
                }
            }
        </script>

        <!-- Session Message Trigger Script -->
        @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    showToast('Sukses!', "{{ session('success') }}", 'success');
                });
            </script>
        @endif
        @if(session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    showToast('Perhatian!', "{{ session('error') }}", 'error');
                });
            </script>
        @endif

        <!-- Custom Isolated Dashboard Animations Script -->
        <script src="{{ asset('js/dashboard.js') }}?v={{ time() }}"></script>
    </body>
</html>
