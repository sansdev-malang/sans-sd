<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - SANS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // Check for theme in localStorage or system preference
        if (localStorage.getItem('color-theme') === 'light' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: light)').matches)) {
            document.documentElement.classList.remove('dark')
        } else {
            document.documentElement.classList.add('dark')
        }
    </script>
    <style>
        .btn-action {
            padding-left: 2rem;
            padding-right: 2rem;
            padding-top: 0.875rem;
            padding-bottom: 0.875rem;
        }
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
        .error-title-huge {
            font-size: 6rem;
            line-height: 1;
            font-weight: 900;
            background-image: linear-gradient(to bottom right, #0f172a, #64748b);
            -webkit-background-clip: text;
            color: transparent;
            margin-bottom: 0;
            padding-bottom: 1.5rem;
        }
        .dark .error-title-huge {
            background-image: linear-gradient(to bottom right, #ffffff, #64748b);
        }
        .icon-badge {
            position: absolute;
            bottom: -0.5rem;
            left: 50%;
            transform: translateX(-50%);
            padding: 0.75rem;
            border-radius: 9999px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        @media (min-width: 768px) {
            .error-title-huge { font-size: 8rem; }
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
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 antialiased min-h-screen relative overflow-hidden flex items-center justify-center p-4">
    <!-- Animated Background Blobs -->
    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-indigo-400/30 dark:bg-indigo-600/20 rounded-full mix-blend-multiply dark:mix-blend-lighten filter blur-3xl opacity-70 blob-1"></div>
    <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-400/30 dark:bg-purple-600/20 rounded-full mix-blend-multiply dark:mix-blend-lighten filter blur-3xl opacity-70 blob-2"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-pink-400/20 dark:bg-pink-600/10 rounded-full mix-blend-multiply dark:mix-blend-lighten filter blur-3xl opacity-70 blob-1" style="animation-delay: -5s;"></div>

    <div class="relative z-10 glass-card max-w-2xl w-full text-center px-8 py-16 md:px-12 md:py-20 rounded-3xl shadow-2xl">
        <!-- Error Code with Floating Badge -->
        <div style="position: relative; display: inline-block; margin-bottom: 1.5rem;">
            <h1 class="error-title-huge drop-shadow-sm tracking-tighter" style="font-family: 'Nasalization Rg', sans-serif;">
                @yield('code')
            </h1>
            <div class="icon-badge bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700">
                <i data-lucide="@yield('icon')" style="width: 2rem; height: 2rem;" class="text-indigo-500 dark:text-indigo-400"></i>
            </div>
        </div>

        <!-- Title & Message -->
        <h2 style="font-size: 1.875rem; line-height: 2.25rem; font-weight: 700; margin-bottom: 1rem; margin-top: 1.5rem;" style="font-family: 'Nasalization Rg', sans-serif;">@yield('title')</h2>
        <p style="margin-bottom: 2.5rem;" class="text-slate-500 dark:text-slate-400 text-base md:text-lg max-w-lg mx-auto leading-relaxed font-medium">
            @yield('message')
        </p>

        <!-- Action Button -->
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ url('/') }}" class="btn-action inline-flex justify-center items-center gap-2 bg-slate-900 hover:bg-slate-800 dark:bg-indigo-600 dark:hover:bg-indigo-700 text-white font-semibold rounded-2xl transition-all shadow-md hover:-translate-y-0.5 active:translate-y-0 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900 dark:focus:ring-indigo-500">
                <i data-lucide="home" class="w-5 h-5"></i>
                Ke Beranda
            </a>
            @if(View::hasSection('action'))
                @yield('action')
            @endif
        </div>
    </div>
    
    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
