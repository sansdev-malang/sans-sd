<x-auth-layout>
    <!-- Centered form content -->
    <div class="my-auto mx-auto w-full max-w-sm space-y-6 py-10">
        <div class="space-y-2 text-center lg:text-left">
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-50">Selamat Datang Kembali
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Masukkan email dan password Anda untuk masuk ke sistem
                dashboard.</p>
        </div>

        <!-- Laravel Session Status -->
        @if (session('status'))
            <div
                class="bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400 p-3 rounded-lg text-xs font-semibold flex items-center gap-2">
                <i data-lucide="check-circle-2" class="w-4 h-4 shrink-0"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <form id="login-form" method="POST" action="{{ route('login') }}" class="space-y-4" onsubmit="handleLoginSubmit(event)">
            @csrf

            <!-- Email Address -->
            <div class="space-y-1.5">
                <label for="email"
                    class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Email
                    Address</label>
                <div class="relative">
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        autocomplete="username"
                        class="w-full bg-transparent border border-slate-200 dark:border-slate-800 focus:border-slate-400 dark:focus:border-slate-600 rounded-lg px-3.5 py-2 text-sm outline-none transition-colors dark:text-slate-50 text-slate-900 placeholder:text-slate-400 @error('email') border-red-500 dark:border-red-500 @enderror"
                        placeholder="user@sekolahanaksaleh.sch.id">
                </div>

                @if ($errors->has('email'))
                    <p class="text-xs font-bold text-red-500 mt-1.5 flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0"></i>
                        <span>{{ $errors->first('email') }}</span>
                    </p>
                @endif
            </div>

            <!-- Password -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="password"
                        class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">Lupa
                            Password?</a>
                    @endif
                </div>
                <div class="relative">
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="w-full bg-transparent border border-slate-200 dark:border-slate-800 focus:border-slate-400 dark:focus:border-slate-600 rounded-lg px-3.5 py-2 pr-11 text-sm outline-none transition-colors dark:text-slate-50 text-slate-900 placeholder:text-slate-400 @error('password') border-red-500 dark:border-red-500 @enderror"
                        placeholder="Password">
                    <button type="button" id="password-toggle" aria-label="Tampilkan password" aria-pressed="false"
                        class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors cursor-pointer"
                        onclick="togglePasswordVisibility()">
                        <i id="password-eye-icon" data-lucide="eye" class="w-4 h-4" aria-hidden="true"></i>
                        <i id="password-eye-closed-icon" data-lucide="eye-closed" class="hidden w-4 h-4" aria-hidden="true"></i>
                    </button>
                </div>

                @if ($errors->has('password'))
                    <p class="text-xs font-bold text-red-500 mt-1.5 flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0"></i>
                        <span>{{ $errors->first('password') }}</span>
                    </p>
                @endif
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <input id="remember_me" type="checkbox" name="remember"
                    class="w-4 h-4 rounded border-slate-300 dark:border-slate-800 text-blue-600 focus:ring-blue-500 cursor-pointer">
                <label for="remember_me"
                    class="ml-2 text-xs font-medium text-slate-500 dark:text-slate-400 cursor-pointer select-none">Ingat
                    saya di perangkat ini</label>
            </div>

            <!-- Submit Button with non-destructive Loading State -->
            <button type="submit" id="btn-login-submit"
                class="w-full bg-[#0f172a] hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 font-semibold text-sm py-2.5 rounded-lg transition-all duration-150 cursor-pointer shadow-sm flex items-center justify-center gap-2">
                <span id="btn-spinner" class="hidden w-4 h-4 border-2 border-current border-t-transparent rounded-full animate-spin"></span>
                <span id="btn-text">Log In</span>
            </button>
        </form>
    </div>

    <script>
        function togglePasswordVisibility() {
            const input = document.getElementById('password');
            const button = document.getElementById('password-toggle');
            if (!input || !button) return;
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            button.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
            button.setAttribute('aria-pressed', String(isHidden));
            const eye = document.getElementById('password-eye-icon');
            const eyeClosed = document.getElementById('password-eye-closed-icon');
            if (eye) eye.classList.toggle('hidden', isHidden);
            if (eyeClosed) eyeClosed.classList.toggle('hidden', !isHidden);
        }

        function handleLoginSubmit(event) {
            const btn = document.getElementById('btn-login-submit');
            const spinner = document.getElementById('btn-spinner');
            const text = document.getElementById('btn-text');
            if (btn) {
                btn.disabled = true;
                btn.classList.add('opacity-80', 'cursor-not-allowed');
                if (spinner) spinner.classList.remove('hidden');
                if (text) text.textContent = 'Memproses...';
            }
        }
    </script>
</x-auth-layout>
