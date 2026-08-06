<section class="space-y-6">
    <header class="flex items-center gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
        <div class="p-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg text-indigo-600 dark:text-indigo-400">
            <i data-lucide="user" class="w-5 h-5"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Informasi Profil</h3>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Perbarui informasi profil akun dan alamat email Anda.</p>
        </div>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-4 max-w-xl text-xs">
        @csrf
        @method('patch')

        <div class="space-y-1.5">
            <label for="name" class="block font-semibold text-slate-700 dark:text-slate-300">Nama Lengkap <span class="text-red-500">*</span></label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" class="w-full h-10 px-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
            @error('name')
                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-1.5">
            <label for="email" class="block font-semibold text-slate-700 dark:text-slate-300">Alamat Email <span class="text-red-500">*</span></label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username" class="w-full h-10 px-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
            @error('email')
                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-900 rounded-lg">
                    <p class="text-[11px] text-amber-800 dark:text-amber-300 font-medium">
                        Alamat email Anda belum diverifikasi.
                        <button form="send-verification" class="underline text-xs text-amber-700 dark:text-amber-400 hover:text-amber-800 dark:hover:text-amber-300 font-bold ml-1 cursor-pointer">
                            Klik di sini untuk mengirim ulang email verifikasi.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-1.5 text-[11px] font-bold text-emerald-600 dark:text-emerald-400">
                            Tautan verifikasi baru telah dikirimkan ke alamat email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center gap-4">
            <button type="submit" class="h-9 px-5 bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 font-semibold text-xs rounded-lg shadow-sm transition-colors cursor-pointer">
                Simpan Perubahan
            </button>
        </div>
    </form>
</section>
