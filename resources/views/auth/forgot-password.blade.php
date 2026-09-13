<x-auth-layout>
    <!-- Centered form content -->
    <div class="my-auto mx-auto w-full max-w-sm space-y-6 py-10">
        <div class="space-y-2 text-center lg:text-left">
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-50">Lupa Password</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Tidak masalah. Masukkan email Anda dan kami akan mengirimkan tautan untuk mengatur ulang password.</p>
        </div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400 p-3 rounded-lg text-xs font-semibold flex items-center gap-2">
                <i data-lucide="check-circle-2" class="w-4 h-4 shrink-0"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4" onsubmit="handleForgotSubmit(event)">
            @csrf

            <!-- Email Address -->
            <div class="space-y-1.5">
                <label for="email" class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full bg-transparent border border-slate-200 dark:border-slate-800 focus:border-slate-400 dark:focus:border-slate-600 rounded-lg px-3.5 py-2 text-sm outline-none transition-colors dark:text-slate-50 text-slate-900 placeholder:text-slate-400 @error('email') border-red-500 dark:border-red-500 @enderror"
                    placeholder="admin@sansmalang.sch.id">

                @if ($errors->has('email'))
                    <p class="text-xs font-bold text-red-500 mt-1.5 flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0"></i>
                        <span>{{ $errors->first('email') }}</span>
                    </p>
                @endif
            </div>

            <!-- Submit Button with loading state -->
            <button type="submit" id="btn-forgot-submit"
                class="w-full bg-[#0f172a] hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 font-semibold text-sm py-2.5 rounded-lg transition-all duration-150 cursor-pointer shadow-sm flex items-center justify-center gap-2">
                <span id="btn-spinner" class="hidden w-4 h-4 border-2 border-current border-t-transparent rounded-full animate-spin"></span>
                <span id="btn-text">Kirim Tautan Reset Password</span>
            </button>
            
            <div class="text-center pt-2">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-50 transition-colors">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    Kembali ke Halaman Login
                </a>
            </div>
        </form>
    </div>

    <script>
        function handleForgotSubmit(event) {
            const btn = document.getElementById('btn-forgot-submit');
            const spinner = document.getElementById('btn-spinner');
            const text = document.getElementById('btn-text');
            if (btn) {
                btn.disabled = true;
                btn.classList.add('opacity-80', 'cursor-not-allowed');
                if (spinner) spinner.classList.remove('hidden');
                if (text) text.textContent = 'Mengirim Tautan...';
            }
        }
    </script>
</x-auth-layout>
