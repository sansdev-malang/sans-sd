<x-auth-layout>

    <!-- Centered form content -->
    <div class="my-auto mx-auto w-full max-w-sm space-y-6 py-8">
        <div class="space-y-2 text-center lg:text-left">
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-50">Daftar Akun Baru</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Buat akun SANS.dev Anda untuk memulai pengelolaan
                akademik.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- Name -->
            <div class="space-y-1.5">
                <label for="name"
                    class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Nama
                    Lengkap</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                    autocomplete="name"
                    class="w-full bg-transparent border border-slate-200 dark:border-slate-800 focus:border-slate-400 dark:focus:border-slate-600 rounded-lg px-3.5 py-2 text-sm outline-none transition-colors dark:text-slate-50 text-slate-900 placeholder:text-slate-400"
                    placeholder="Nama Lengkap Anda">

                @if ($errors->has('name'))
                    <p class="text-xs font-bold text-red-500 mt-1.5">{{ $errors->first('name') }}</p>
                @endif
            </div>

            <!-- Email Address -->
            <div class="space-y-1.5">
                <label for="email"
                    class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Email
                    Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                    class="w-full bg-transparent border border-slate-200 dark:border-slate-800 focus:border-slate-400 dark:focus:border-slate-600 rounded-lg px-3.5 py-2 text-sm outline-none transition-colors dark:text-slate-50 text-slate-900 placeholder:text-slate-400"
                    placeholder="contoh@sansmalang.sch.id">

                @if ($errors->has('email'))
                    <p class="text-xs font-bold text-red-500 mt-1.5">{{ $errors->first('email') }}</p>
                @endif
            </div>

            <!-- Password -->
            <div class="space-y-1.5">
                <label for="password"
                    class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    class="w-full bg-transparent border border-slate-200 dark:border-slate-800 focus:border-slate-400 dark:focus:border-slate-600 rounded-lg px-3.5 py-2 text-sm outline-none transition-colors dark:text-slate-50 text-slate-900 placeholder:text-slate-400"
                    placeholder="Minimal 8 karakter">

                @if ($errors->has('password'))
                    <p class="text-xs font-bold text-red-500 mt-1.5">{{ $errors->first('password') }}</p>
                @endif
            </div>

            <!-- Confirm Password -->
            <div class="space-y-1.5">
                <label for="password_confirmation"
                    class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Konfirmasi
                    Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    autocomplete="new-password"
                    class="w-full bg-transparent border border-slate-200 dark:border-slate-800 focus:border-slate-400 dark:focus:border-slate-600 rounded-lg px-3.5 py-2 text-sm outline-none transition-colors dark:text-slate-50 text-slate-900 placeholder:text-slate-400"
                    placeholder="Ulangi password">

                @if ($errors->has('password_confirmation'))
                    <p class="text-xs font-bold text-red-500 mt-1.5">{{ $errors->first('password_confirmation') }}</p>
                @endif
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full bg-[#0f172a] hover:bg-slate-850 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-950 font-semibold text-sm py-2.5 rounded-lg transition-colors cursor-pointer shadow-sm">
                Daftar Akun
            </button>
        </form>

        <!-- Already registered prompt -->
        <p class="text-xs text-center text-slate-500 dark:text-slate-400">
            Sudah memiliki akun?
            <a href="{{ route('login') }}" class="font-bold text-blue-650 dark:text-blue-400 hover:underline">Masuk di
                sini</a>
        </p>
    </div>
</x-auth-layout>