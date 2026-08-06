<x-admin-layout>
    <div class="p-6 space-y-6 w-full">

        <!-- GREETING / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50 font-nasalization">Profil Saya</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Kelola informasi profil, kata sandi, dan keamanan akun Anda.</p>
            </div>
        </section>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- PROFILE INFORMATION -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 w-full transition-all duration-300 hover:shadow-lg hover:-translate-y-1 hover:border-indigo-500/50 dark:hover:border-indigo-400/50">
                @include('profile.partials.update-profile-information-form')
            </div>

            <!-- UPDATE PASSWORD -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 w-full transition-all duration-300 hover:shadow-lg hover:-translate-y-1 hover:border-indigo-500/50 dark:hover:border-indigo-400/50">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- DELETE USER FORM -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 w-full border-rose-100 dark:border-rose-900/20 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 hover:border-rose-500/50 dark:hover:border-rose-400/50">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-admin-layout>
