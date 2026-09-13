<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Offline - {{ setting('app_name', 'SANS SD') }}</title>
    
    <!-- Google Fonts & CDN -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        }
                    }
                }
            }
        </script>
    @endif
</head>
<body class="h-full bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans flex flex-col justify-between items-center p-6">
    <div></div>

    <div class="max-w-md w-full text-center space-y-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-8 rounded-2xl shadow-xl">
        <div class="w-16 h-16 mx-auto bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-800 rounded-2xl flex items-center justify-center text-amber-600 dark:text-amber-400">
            <i data-lucide="wifi-off" class="w-8 h-8"></i>
        </div>

        <div class="space-y-2">
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-slate-50">Koneksi Terputus</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                Anda sedang tidak terhubung ke internet. Periksa koneksi data atau Wi-Fi Anda untuk melanjutkan.
            </p>
        </div>

        <button onclick="window.location.reload()" 
            class="w-full bg-[#0f172a] hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 font-semibold text-sm py-2.5 rounded-lg transition-colors cursor-pointer shadow-sm flex items-center justify-center gap-2">
            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
            <span>Coba Muat Ulang</span>
        </button>
    </div>

    <div class="text-xs font-semibold text-slate-400">
        {{ setting('app_name', 'SANS School Information System') }} &bull; Mode Offline
    </div>

    <script>
        lucide.createIcons();
        window.addEventListener('online', () => {
            window.location.reload();
        });
    </script>
</body>
</html>
