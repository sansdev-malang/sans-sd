<x-admin-layout>
    <div class="p-6 md:p-12 flex flex-col items-center justify-center min-h-[70vh] text-center">
        <div class="relative w-32 h-32 mb-8">
            <div class="absolute inset-0 bg-indigo-100 dark:bg-indigo-900/30 rounded-full animate-ping opacity-75"></div>
            <div class="relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-full w-full h-full flex items-center justify-center shadow-sm">
                <i data-lucide="wrench" class="w-12 h-12 text-indigo-500 dark:text-indigo-400"></i>
            </div>
        </div>
        
        <h1 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-slate-50 mb-3" style="font-family: 'Nasalization Rg', sans-serif;">Tahap Pengembangan</h1>
        <p class="text-slate-500 dark:text-slate-400 max-w-lg mx-auto text-sm md:text-base mb-8">
            Fitur ini sedang dalam tahap perancangan dan pembuatan oleh tim pengembang SANS. Kami sedang meracik kode-kode ajaib agar fitur ini segera bisa Anda nikmati!
        </p>

        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-slate-900 dark:bg-slate-50 text-white dark:text-slate-900 font-semibold rounded-lg hover:bg-slate-800 dark:hover:bg-slate-200 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900 dark:focus:ring-slate-50">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali Sebelumnya
        </a>
    </div>
</x-admin-layout>
