<section class="space-y-6">
    <header class="flex items-center gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
        <div class="p-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg text-indigo-600 dark:text-indigo-400">
            <i data-lucide="key-round" class="w-5 h-5"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Perbarui Kata Sandi</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Pastikan akun Anda menggunakan kata sandi yang panjang dan acak untuk menjaga keamanan.</p>
        </div>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-4 max-w-xl text-xs">
        @csrf
        @method('put')

        <div class="space-y-1.5">
            <label for="update_password_current_password" class="block font-semibold text-slate-700 dark:text-slate-300">Kata Sandi Saat Ini</label>
            <div class="relative">
                <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" class="w-full text-xs h-10 px-3 pr-10 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
                <button type="button" data-password-toggle data-password-target="update_password_current_password" aria-label="Tampilkan kata sandi" aria-pressed="false" class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors">
                    <i data-password-eye data-lucide="eye" class="w-4 h-4" aria-hidden="true"></i>
                    <i data-password-eye-closed data-lucide="eye-closed" class="hidden w-4 h-4" aria-hidden="true"></i>
                </button>
            </div>
            @error('current_password', 'updatePassword')
                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-1.5">
            <label for="update_password_password" class="block font-semibold text-slate-700 dark:text-slate-300">Kata Sandi Baru</label>
            <div class="relative">
                <input id="update_password_password" name="password" type="password" autocomplete="new-password" class="w-full text-xs h-10 px-3 pr-10 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
                <button type="button" data-password-toggle data-password-target="update_password_password" aria-label="Tampilkan kata sandi" aria-pressed="false" class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors">
                    <i data-password-eye data-lucide="eye" class="w-4 h-4" aria-hidden="true"></i>
                    <i data-password-eye-closed data-lucide="eye-closed" class="hidden w-4 h-4" aria-hidden="true"></i>
                </button>
            </div>
            @error('password', 'updatePassword')
                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-1.5">
            <label for="update_password_password_confirmation" class="block font-semibold text-slate-700 dark:text-slate-300">Konfirmasi Kata Sandi Baru</label>
            <div class="relative">
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="w-full text-xs h-10 px-3 pr-10 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
                <button type="button" data-password-toggle data-password-target="update_password_password_confirmation" aria-label="Tampilkan kata sandi" aria-pressed="false" class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors">
                    <i data-password-eye data-lucide="eye" class="w-4 h-4" aria-hidden="true"></i>
                    <i data-password-eye-closed data-lucide="eye-closed" class="hidden w-4 h-4" aria-hidden="true"></i>
                </button>
            </div>
            @error('password_confirmation', 'updatePassword')
                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center gap-4">
            <button type="submit" class="h-9 px-5 bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 font-semibold text-xs rounded-lg shadow-sm transition-colors cursor-pointer">
                Simpan Kata Sandi
            </button>
        </div>
    </form>
</section>

<script>
    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-password-toggle]');
        if (!button) return;

        const input = document.getElementById(button.dataset.passwordTarget);
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        button.setAttribute('aria-label', isHidden ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        button.setAttribute('aria-pressed', String(isHidden));
        button.querySelector('[data-password-eye]').classList.toggle('hidden', isHidden);
        button.querySelector('[data-password-eye-closed]').classList.toggle('hidden', !isHidden);
    });
</script>
@if (session('status') === 'password-updated')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            showToast('Sukses!', 'Kata sandi berhasil diperbarui.', 'success');
        });
    </script>
@endif
