<x-auth-layout>
    <!-- Centered form content -->
    <div class="my-auto mx-auto w-full max-w-sm space-y-6 py-10">
        <div class="space-y-2 text-center lg:text-left">
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-50">Reset Password</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Silakan masukkan password baru Anda.</p>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-4" onsubmit="handleResetSubmit(event)">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div class="space-y-1.5">
                <label for="email" class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                    class="w-full bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-lg px-3.5 py-2 text-sm outline-none transition-colors dark:text-slate-400 text-slate-500 cursor-not-allowed"
                    placeholder="admin@sansmalang.sch.id" readonly>

                @if ($errors->has('email'))
                    <p class="text-xs font-bold text-red-500 mt-1.5 flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0"></i>
                        <span>{{ $errors->first('email') }}</span>
                    </p>
                @endif
            </div>

            <!-- Password -->
            <div class="space-y-1.5">
                <label for="password" class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Password Baru</label>
                <div class="relative">
                    <input id="password" type="password" name="password" required autocomplete="new-password"
                        class="w-full bg-transparent border border-slate-200 dark:border-slate-800 focus:border-slate-400 dark:focus:border-slate-600 rounded-lg px-3.5 py-2 pr-11 text-sm outline-none transition-colors dark:text-slate-50 text-slate-900 placeholder:text-slate-400 @error('password') border-red-500 dark:border-red-500 @enderror"
                        placeholder="••••••••">
                    <button type="button" id="password-toggle" aria-label="Tampilkan password" aria-pressed="false"
                        class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors cursor-pointer"
                        onclick="toggleFieldVisibility('password', 'password-eye-icon', 'password-eye-closed-icon', 'password-toggle')">
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

            <!-- Confirm Password -->
            <div class="space-y-1.5">
                <label for="password_confirmation" class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Konfirmasi Password</label>
                <div class="relative">
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                        class="w-full bg-transparent border border-slate-200 dark:border-slate-800 focus:border-slate-400 dark:focus:border-slate-600 rounded-lg px-3.5 py-2 pr-11 text-sm outline-none transition-colors dark:text-slate-50 text-slate-900 placeholder:text-slate-400 @error('password_confirmation') border-red-500 dark:border-red-500 @enderror"
                        placeholder="••••••••">
                    <button type="button" id="password_confirmation-toggle" aria-label="Tampilkan konfirmasi password" aria-pressed="false"
                        class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors cursor-pointer"
                        onclick="toggleFieldVisibility('password_confirmation', 'password_confirmation-eye-icon', 'password_confirmation-eye-closed-icon', 'password_confirmation-toggle')">
                        <i id="password_confirmation-eye-icon" data-lucide="eye" class="w-4 h-4" aria-hidden="true"></i>
                        <i id="password_confirmation-eye-closed-icon" data-lucide="eye-closed" class="hidden w-4 h-4" aria-hidden="true"></i>
                    </button>
                </div>

                @if ($errors->has('password_confirmation'))
                    <p class="text-xs font-bold text-red-500 mt-1.5 flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0"></i>
                        <span>{{ $errors->first('password_confirmation') }}</span>
                    </p>
                @endif
            </div>

            <!-- Submit Button with loading state -->
            <button type="submit" id="btn-reset-submit"
                class="w-full bg-[#0f172a] hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 font-semibold text-sm py-2.5 rounded-lg transition-all duration-150 cursor-pointer shadow-sm flex items-center justify-center gap-2">
                <span id="btn-spinner" class="hidden w-4 h-4 border-2 border-current border-t-transparent rounded-full animate-spin"></span>
                <span id="btn-text">Reset Password</span>
            </button>
        </form>
    </div>

    <script>
        function toggleFieldVisibility(inputId, eyeId, eyeClosedId, btnId) {
            const input = document.getElementById(inputId);
            const button = document.getElementById(btnId);
            if (!input || !button) return;
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            button.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
            button.setAttribute('aria-pressed', String(isHidden));
            const eye = document.getElementById(eyeId);
            const eyeClosed = document.getElementById(eyeClosedId);
            if (eye) eye.classList.toggle('hidden', isHidden);
            if (eyeClosed) eyeClosed.classList.toggle('hidden', !isHidden);
        }

        function handleResetSubmit(event) {
            const btn = document.getElementById('btn-reset-submit');
            const spinner = document.getElementById('btn-spinner');
            const text = document.getElementById('btn-text');
            if (btn) {
                btn.disabled = true;
                btn.classList.add('opacity-80', 'cursor-not-allowed');
                if (spinner) spinner.classList.remove('hidden');
                if (text) text.textContent = 'Menyimpan Password...';
            }
        }
    </script>
</x-auth-layout>
