<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - SANS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // Check for theme in localStorage or system preference
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }
        .dark .glass-card {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .blob-1 { animation: float 10s infinite; }
        .blob-2 { animation: float-reverse 12s infinite; }
        @keyframes float {
            0% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-30px) scale(1.05); }
            100% { transform: translateY(0px) scale(1); }
        }
        @keyframes float-reverse {
            0% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(30px) scale(1.05); }
            100% { transform: translateY(0px) scale(1); }
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 antialiased min-h-screen relative overflow-hidden flex items-center justify-center p-4">
    <!-- Animated Background Blobs -->
    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-indigo-400/30 dark:bg-indigo-600/20 rounded-full mix-blend-multiply dark:mix-blend-lighten filter blur-3xl opacity-70 blob-1"></div>
    <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-400/30 dark:bg-purple-600/20 rounded-full mix-blend-multiply dark:mix-blend-lighten filter blur-3xl opacity-70 blob-2"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-pink-400/20 dark:bg-pink-600/10 rounded-full mix-blend-multiply dark:mix-blend-lighten filter blur-3xl opacity-70 blob-1" style="animation-delay: -5s;"></div>

    <div class="relative z-10 glass-card max-w-2xl w-full text-center px-8 py-16 md:px-12 md:py-20 rounded-3xl shadow-2xl">
        <!-- Error Code with Floating Badge -->
        <div class="mb-6 relative inline-block">
            <h1 class="text-8xl md:text-9xl font-black bg-gradient-to-br from-slate-900 to-slate-500 dark:from-white dark:to-slate-500 text-transparent bg-clip-text drop-shadow-sm tracking-tighter" style="font-family: 'Nasalization Rg', sans-serif;">
                @yield('code')
            </h1>
            <div class="absolute -bottom-4 left-1/2 -translate-x-1/2 bg-white dark:bg-slate-800 p-3 rounded-full shadow-xl border border-slate-100 dark:border-slate-700">
                <i data-lucide="@yield('icon')" class="w-8 h-8 text-indigo-500 dark:text-indigo-400"></i>
            </div>
        </div>

        <!-- Title & Message -->
        <h2 class="text-2xl md:text-3xl font-bold mb-4 mt-6" style="font-family: 'Nasalization Rg', sans-serif;">@yield('title')</h2>
        <p class="text-slate-500 dark:text-slate-400 text-base md:text-lg max-w-lg mx-auto mb-10 leading-relaxed font-medium">
            @yield('message')
        </p>

        <!-- Action Button -->
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ url('/') }}" class="inline-flex justify-center items-center gap-2 px-8 py-3.5 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-semibold rounded-2xl transition-all shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 hover:-translate-y-0.5 active:translate-y-0 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-slate-950">
                <i data-lucide="home" class="w-5 h-5"></i>
                Ke Beranda
            </a>
            @if(View::hasSection('action'))
                @yield('action')
            @endif
        </div>
    </div>
    
    <script type="module">
        import { createIcons, icons } from 'lucide';
        createIcons({ icons });
    </script>
</body>
</html>
