<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ setting('app_name', 'SANS') }} - Log In</title>
    @if(setting('app_favicon'))
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . setting('app_favicon')) }}">
    @else
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><defs><linearGradient id='g' x1='0%' y1='0%' x2='100%' y2='100%'><stop offset='0%' stop-color='%236366f1'/><stop offset='100%' stop-color='%23a855f7'/></linearGradient></defs><rect width='100' height='100' rx='25' fill='url(%23g)'/><text x='50' y='75' font-family='Arial, sans-serif' font-size='65' font-weight='bold' fill='white' text-anchor='middle'>{{ substr(setting('app_name', 'SANS'), 0, 1) }}</text></svg>">
    @endif

    <!-- Google Fonts: Inter & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/nasalization" rel="stylesheet">

    <!-- Load Tailwind styling compiled by Vite / CDN Fallback -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
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

        /* Animated gradient logo background */
        @keyframes gradient-bg {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .logo-gradient-bg {
            background: linear-gradient(135deg, #e91d1d, #f3e40fff, #33ee0dee, #070be2ff, #8a18c5ff);
            background-size: 400% 400%;
            animation: gradient-bg 16s ease-in-out infinite;
        }
    </style>
</head>

<body
    class="min-h-screen bg-white dark:bg-[#09090b] text-[#0f172a] dark:text-slate-50 transition-colors duration-200 antialiased flex flex-col justify-between">

    <div class="grid min-h-screen lg:grid-cols-2">
        <!-- FORM COLUMN -->
        <div class="relative flex flex-col justify-between p-8 lg:p-12 overflow-hidden">
            <!-- Aesthetic "SANS DEV" watermark background -->
            <div aria-hidden="true" style="
                position: absolute;
                bottom: 60px;
                left: 90px;
                font-family: 'Nasalization Rg', sans-serif;
                font-size: clamp(64px, 22vw, 160px);
                font-weight: 600;
                line-height: 1;
                white-space: nowrap;
                pointer-events: none;
                user-select: none;
                transform: rotate(-8deg);
                transform-origin: bottom right;
                background: linear-gradient(135deg, #6366f1 0%, #a5b4fc 40%, transparent 90%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                opacity: 0.15;
                z-index: 0;
            ">SANS DEV</div>
            <!-- Header section (Logo and Theme Toggle) -->
            <div class="flex items-center justify-between">
                <a href="/" class="flex items-center gap-2.5">
                    @if (setting('app_logo'))
                        <img src="{{ asset('storage/' . setting('app_logo')) }}" alt="Logo" class="w-8 h-8 rounded-lg object-cover shrink-0 shadow-sm">
                    @else
                        <div class="w-8 h-8 rounded-lg logo-gradient-bg flex items-center justify-center shrink-0 shadow-sm">
                            <span class="text-white text-lg font-bold" style="font-family: 'Nasalization Rg', sans-serif; font-weight: 400;">{{ substr(setting('app_name', 'SANS'), 0, 1) }}</span>
                        </div>
                    @endif
                    <span class="text-lg font-bold tracking-tight text-slate-900 dark:text-slate-50" style="font-family: 'Nasalization Rg', sans-serif; font-weight: 400;">{{ setting('app_name', 'SANS') }}</span>
                </a>

                <div class="flex items-center gap-4">
                    <!-- Light / Dark Switch Button -->
                    <button id="theme-toggle"
                        class="p-1.5 text-slate-500 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-md cursor-pointer transition-colors"
                        title="Toggle Tema">
                        <i data-lucide="sun" class="w-4 h-4 hidden dark:block"></i>
                        <i data-lucide="moon" class="w-4 h-4 block dark:hidden"></i>
                    </button>
    
                    <!-- Back to Home link -->
                    <a href="https://sans.sch.id" class="flex items-center gap-1.5 text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-50 transition-colors">
                        <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                        Back to Portal
                    </a>
                </div>
            </div>

            {{ $slot }}

            <!-- Footer links -->
            <div class="text-center lg:text-left text-[10px] font-semibold text-slate-400">
                {{ setting('app_copyright', '© 2026 SANS School Information System. All rights reserved.') }}
            </div>
        </div>

        <!-- HERO COVER COLUMN (Visible on large screens) -->
        <div class="hidden lg:block relative overflow-hidden bg-slate-900 dark:bg-slate-900">
            <!-- Modern abstract gradient mesh background inside the cover -->
            <div class="absolute inset-0 bg-cover bg-center"
                style="background-image: url('https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&q=80&w=1200'); opacity: 0.65; mix-blend-mode: overlay;">
            </div>

            <!-- Sleek dark overlay gradient -->
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/50 to-slate-900"></div>

            <!-- Graphic layout content overlay -->
            <div class="absolute inset-0 flex flex-col justify-between p-12 text-white">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase tracking-widest text-slate-400">SANS Portal</span>
                </div>

                <div class="space-y-4 max-w-md">
                    <blockquote class="text-2xl font-medium leading-relaxed tracking-tight">
                        "Teknologi mempermudah sistem belajar, mempertemukan efisiensi administrasi, dan kenyamanan
                        kolaborasi di sekolah."
                    </blockquote>
                    <div>
                        <cite class="text-sm font-bold not-italic text-slate-200">SANS School System</cite>
                        <p class="text-xs text-slate-400 mt-1">Platform Informasi Sekolah Terintegrasi</p>
                    </div>
                </div>

                <div class="text-[10px] text-slate-400">
                    Sistem Manajemen Akademik &amp; Administrasi Digital
                </div>
            </div>
        </div>
    </div>

    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        // Interactive Theme Toggle Script
        const themeToggleBtn = document.getElementById('theme-toggle');
        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', () => {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            });
        }
    </script>
</body>

</html>