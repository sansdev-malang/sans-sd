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
                class="bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400 p-3 rounded-lg text-xs font-semibold">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email Address -->
            <div class="space-y-1.5">
                <label for="email"
                    class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Email
                    Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    autocomplete="username"
                    class="w-full bg-transparent border border-slate-200 dark:border-slate-800 focus:border-slate-400 dark:focus:border-slate-600 rounded-lg px-3.5 py-2 text-sm outline-none transition-colors dark:text-slate-50 text-slate-900 placeholder:text-slate-400"
                    placeholder="admin@sansmalang.sch.id">

                @if ($errors->has('email'))
                    <p class="text-xs font-bold text-red-500 mt-1.5">{{ $errors->first('email') }}</p>
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
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    class="w-full bg-transparent border border-slate-200 dark:border-slate-800 focus:border-slate-400 dark:focus:border-slate-600 rounded-lg px-3.5 py-2 text-sm outline-none transition-colors dark:text-slate-50 text-slate-900 placeholder:text-slate-400"
                    placeholder="••••••••">

                @if ($errors->has('password'))
                    <p class="text-xs font-bold text-red-500 mt-1.5">{{ $errors->first('password') }}</p>
                @endif
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <input id="remember_me" type="checkbox" name="remember"
                    class="w-4 h-4 rounded border-slate-350 dark:border-slate-800 text-blue-600 focus:ring-blue-500 cursor-pointer">
                <label for="remember_me"
                    class="ml-2 text-xs font-medium text-slate-500 dark:text-slate-400 cursor-pointer select-none">Ingat
                    saya di perangkat ini</label>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full bg-[#0f172a] hover:bg-slate-850 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-950 font-semibold text-sm py-2.5 rounded-lg transition-colors cursor-pointer shadow-sm">
                Log In
            </button>
        </form>
    </div>
</x-auth-layout>