<x-admin-layout>
    <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.9.4/dist/dotlottie-wc.js" type="module"></script>

    <div class="p-6 md:p-12 flex flex-col items-center justify-center min-h-[70vh] text-center">
        <div class="relative w-64 h-64 md:w-80 md:h-80 mb-8">
            <div class="absolute inset-0"></div>
            <div class="relative w-full h-full flex items-center justify-center overflow-visible">
                <dotlottie-wc
                    src="https://lottie.host/f8398d94-93f9-4eca-9b43-e1e8005e55eb/w2bZIsxPrk.json"
                    style="width: 320px; height: 320px; transform: translateX(35px);"
                    autoplay
                    loop
                ></dotlottie-wc>
            </div>
        </div>
        
        <h1 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-slate-50 mb-3" style="font-family: 'Nasalization Rg', sans-serif;">Tahap Pengembangan</h1>
        <p class="text-slate-500 dark:text-slate-400 max-w-lg mx-auto text-xs md:text-base mb-8">
            Fitur ini sedang dalam tahap perancangan dan pembuatan oleh tim pengembang SANS. Kami sedang meracik kode-kode ajaib agar fitur ini segera bisa Anda nikmati!
        </p>

        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-slate-900 dark:bg-slate-50 text-white dark:text-slate-900 text-xs font-semibold rounded-lg hover:bg-slate-800 dark:hover:bg-slate-200 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900 dark:focus:ring-slate-50">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali Sebelumnya
        </a>
    </div>
</x-admin-layout>
