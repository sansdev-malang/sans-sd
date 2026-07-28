<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @if(setting('app_favicon'))
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . setting('app_favicon')) }}">
    @else
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><defs><linearGradient id='g' x1='0%' y1='0%' x2='100%' y2='100%'><stop offset='0%' stop-color='%236366f1'/><stop offset='100%' stop-color='%23a855f7'/></linearGradient></defs><rect width='100' height='100' rx='25' fill='url(%23g)'/><text x='50' y='75' font-family='Arial, sans-serif' font-size='65' font-weight='bold' fill='white' text-anchor='middle'>{{ substr(setting('app_name', 'SANS'), 0, 1) }}</text></svg>">
    @endif
    <title>SANS School System SIS - Portal Pendidikan</title>

    <!-- Google Fonts: Inter & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/nasalization" rel="stylesheet">

    <!-- Tailwind CSS CDN (Unconditional for reliable mockup rendering) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        // Support tailwind CDN dark mode class configuration
        tailwind.config = {
            darkMode: 'class'
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
            background: radial-gradient(circle at top right, #e0f2fe, #f8fafc 60%);
        }
        .dark body {
            background: radial-gradient(circle at top right, #082f49, #09090b 60%);
        }
        .portal-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            flex: 1;
            min-width: 280px;
        }
        .portal-card:hover {
            transform: translateY(-5px);
        }
        .portal-grid {
            display: flex;
            flex-direction: row;
            gap: 24px;
            width: 100%;
            max-width: 1024px;
            justify-content: center;
        }
        @media (max-width: 768px) {
            .portal-grid {
                flex-direction: column;
                align-items: center;
            }
            .portal-card {
                width: 100%;
                max-width: 340px;
            }
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
<body class="min-h-screen flex flex-col justify-between text-[#0f172a] dark:text-slate-50 transition-colors duration-200 antialiased">

    <!-- HEADER -->
    <header class="w-full max-w-7xl mx-auto px-6 py-6 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            @if (setting('app_logo'))
                <img src="{{ asset('storage/' . setting('app_logo')) }}" alt="Logo" class="w-8 h-8 rounded-lg object-cover shrink-0 shadow-sm">
            @else
                <div class="w-8 h-8 rounded-lg logo-gradient-bg flex items-center justify-center shrink-0 shadow-sm">
                    <span class="text-white text-lg font-bold" style="font-family: 'Nasalization Rg', sans-serif; font-weight: 400;">
                        {{ substr(setting('app_name', 'SANS.dev'), 0, 1) }}
                    </span>
                </div>
            @endif
            <span class="text-lg font-bold tracking-tight text-slate-900 dark:text-slate-50" style="font-family: 'Nasalization Rg', sans-serif; font-weight: 400;">SANS School System</span>
        </div>

        <div class="flex items-center gap-4">
            <!-- Light / Dark Switch Button -->
            <button id="theme-toggle" class="p-1.5 text-slate-500 hover:text-slate-900 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-md cursor-pointer transition-colors" title="Toggle Tema">
                <i data-lucide="sun" class="w-4 h-4 hidden dark:block"></i>
                <i data-lucide="moon" class="w-4 h-4 block dark:hidden"></i>
            </button>

            <!-- Back to Home link -->
            <a href="/" class="flex items-center gap-1.5 text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-950 dark:hover:text-slate-50 transition-colors">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                Back to Home
            </a>
        </div>
    </header>

    <!-- MAIN PORTAL SECTION -->
    <main class="flex-1 w-full max-w-7xl mx-auto px-6 py-8 flex flex-col items-center justify-center gap-10">
        
        <!-- INTRO TEXT -->
        <div class="text-center space-y-4 max-w-2xl">
            <h3 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-[#0f172a] dark:text-slate-50">Pilih Unit Pendidikan Anda</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                Selamat datang di Portal SANS.dev. Silakan pilih jenjang pendidikan untuk melanjutkan ke halaman login yang sesuai dengan kurikulum Anda.
            </p>
        </div>

        <!-- THREE PORTAL CARDS -->
        <div class="portal-grid">
            
            <!-- Card 1: PAUD & TK -->
            <div class="portal-card bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 border-l-[4px] border-l-teal-650 dark:border-l-teal-400 rounded-xl p-6 shadow-sm flex flex-col justify-between">
                <div class="space-y-5">
                    <!-- Icon badge: Teal container with dark teal/cyan icon -->
                    <div class="w-12 h-12 rounded-xl bg-[#e6f7f5] dark:bg-teal-950/40 text-teal-650 dark:text-teal-400 flex items-center justify-center">
                        <i data-lucide="smile" class="w-6 h-6"></i>
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-50">PAUD & TK</h3>
                        <p class="text-[13px] text-slate-500 dark:text-slate-400 leading-relaxed">
                            Pendidikan anak usia dini yang berfokus pada eksplorasi, kreativitas, dan pengembangan karakter dasar yang menyenangkan.
                        </p>
                    </div>
                </div>
                
                <div class="flex items-center justify-between pt-6 mt-4 border-t border-slate-100 dark:border-slate-900/60">
                    <span class="text-[10px] font-bold text-teal-650 dark:text-teal-400 uppercase tracking-widest">Login ke Dashboard PAUD & TK</span>
                    <a href="http://sans-paud.test/login" class="w-8 h-8 rounded-full bg-[#eff6ff] hover:bg-[#dbeafe] text-blue-650 dark:bg-slate-900 dark:hover:bg-slate-800 dark:text-blue-400 flex items-center justify-center transition-colors">
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>

            <!-- Card 2: SD -->
            <div class="portal-card bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 border-l-[4px] border-l-[#0f172a] dark:border-l-indigo-500 rounded-xl p-6 shadow-sm flex flex-col justify-between">
                <div class="space-y-5">
                    <!-- Icon badge: dark slate/blue container with white icon -->
                    <div class="w-12 h-12 rounded-xl bg-[#0f172a] dark:bg-indigo-950/40 text-white dark:text-indigo-400 flex items-center justify-center">
                        <i data-lucide="book-open" class="w-5.5 h-5.5"></i>
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-2xl font-bold text-[#0f172a] dark:text-slate-50">Sekolah Dasar</h3>
                        <p class="text-[13px] text-slate-500 dark:text-slate-400 leading-relaxed">
                            Membangun fondasi akademik yang kokoh melalui pembelajaran interaktif dan literasi yang terintegrasi secara digital.
                        </p>
                    </div>
                </div>
                
                <div class="flex items-center justify-between pt-6 mt-4 border-t border-slate-100 dark:border-slate-900/60">
                    <span class="text-[10px] font-bold text-slate-800 dark:text-slate-400 uppercase tracking-widest">Login ke Dashboard SD</span>
                    <a href="http://sans-sd.test/login" class="w-8 h-8 rounded-full bg-[#eff6ff] hover:bg-[#dbeafe] text-blue-655 dark:bg-slate-900 dark:hover:bg-slate-800 dark:text-blue-400 flex items-center justify-center transition-colors">
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>

            <!-- Card 3: SMP -->
            <div class="portal-card bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 border-l-[4px] border-l-[#7c2d12] dark:border-l-amber-500 rounded-xl p-6 shadow-sm flex flex-col justify-between">
                <div class="space-y-5">
                    <!-- Icon badge: light orange/amber container with dark orange icon -->
                    <div class="w-12 h-12 rounded-xl bg-[#ffedd5] dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 flex items-center justify-center">
                        <i data-lucide="microscope" class="w-5.5 h-5.5"></i>
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-slate-50">Sekolah Menengah Pertama</h3>
                        <p class="text-[13px] text-slate-500 dark:text-slate-400 leading-relaxed">
                            Persiapan menuju tingkat lanjut dengan fokus pada sains, teknologi, dan kemandirian berpikir kritis siswa remaja.
                        </p>
                    </div>
                </div>
                
                <div class="flex items-center justify-between pt-6 mt-4 border-t border-slate-100 dark:border-slate-900/60">
                    <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-widest">Login ke Dashboard SMP</span>
                    <a href="http://sans-smp.test/login" class="w-8 h-8 rounded-full bg-[#eff6ff] hover:bg-[#dbeafe] text-blue-655 dark:bg-slate-900 dark:hover:bg-slate-800 dark:text-blue-400 flex items-center justify-center transition-colors">
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>

        </div>

        <!-- HELP SUBTEXT -->
        <p class="text-xs text-slate-500 dark:text-slate-400">
            Kesulitan menemukan unit Anda? <a href="#" class="font-bold text-slate-900 dark:text-slate-100 hover:underline">Hubungi Admin Sekolah</a>
        </p>

    </main>

    <!-- FOOTER -->
    <footer class="w-full border-t border-slate-200 dark:border-slate-800 bg-[#e6f0fa]/40 dark:bg-slate-950/40 transition-colors duration-200 py-8">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="space-y-1 text-center md:text-left">
                <p class="text-[10px] text-slate-500 dark:text-slate-400">&copy; 2026 SANS School Information System. All rights reserved.</p>
            </div>
            <div class="flex flex-wrap gap-x-6 gap-y-2 justify-center text-[10px] font-semibold text-slate-500 dark:text-slate-400">
                <a href="#" class="hover:text-slate-900 dark:hover:text-slate-50 transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-slate-900 dark:hover:text-slate-50 transition-colors">Terms of Service</a>
                <a href="#" class="hover:text-slate-900 dark:hover:text-slate-50 transition-colors">Contact Support</a>
            </div>
        </div>
    </footer>

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
