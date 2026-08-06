<section class="space-y-6">
    <header class="flex items-center gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
        <div class="p-2 bg-rose-50 dark:bg-rose-900/30 rounded-lg text-rose-600 dark:text-rose-400">
            <i data-lucide="trash-2" class="w-5 h-5"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Hapus Akun</h3>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Setelah akun Anda dihapus, semua data dan sumber daya di dalamnya akan dihapus secara permanen.</p>
        </div>
    </header>

    <div class="max-w-xl text-xs space-y-4">
        <p class="text-slate-500 dark:text-slate-400">
            Sebelum menghapus akun, mohon unduh data atau informasi penting yang ingin Anda simpan terlebih dahulu. Tindakan ini tidak dapat dibatalkan.
        </p>

        <button
            type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="h-9 px-4 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer"
        >
            Hapus Akun Saya
        </button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 text-xs text-left bg-white dark:bg-slate-900">
            @csrf
            @method('delete')

            <h3 class="text-sm font-bold text-slate-900 dark:text-slate-50 mb-2">
                Apakah Anda yakin ingin menghapus akun Anda?
            </h3>

            <p class="text-slate-500 dark:text-slate-400 mb-4 leading-relaxed">
                Semua data akan dihapus secara permanen. Silakan masukkan kata sandi Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun secara permanen.
            </p>

            <div class="space-y-1.5 max-w-md">
                <label for="password" class="block font-semibold text-slate-700 dark:text-slate-300">Kata Sandi Anda</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="Masukkan Kata Sandi"
                    class="w-full h-10 px-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                />
                @error('password', 'userDeletion')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 flex justify-end gap-2.5">
                <button type="button" x-on:click="$dispatch('close')" class="h-9 px-4 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 bg-transparent text-xs font-semibold rounded-lg transition-all cursor-pointer">
                    Batal
                </button>

                <button type="submit" class="h-9 px-4 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all cursor-pointer">
                    Hapus Akun
                </button>
            </div>
        </form>
    </x-modal>
</section>
