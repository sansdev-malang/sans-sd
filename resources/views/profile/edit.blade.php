<x-admin-layout>
    <div class="p-6 space-y-6">

        <!-- GREETING / PAGE TITLE -->
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Profile</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Informasi Akun</p>
            </div>
        </section>

        <!-- STAT CARDS GRID -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4">
            
            <!-- Stat Card 1 -->
            <div class="animate-card bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>

            <!-- Stat Card 2 -->
            <div class="animate-card bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>
        </section>

        <!-- DELETE USER FORM -->
        <section>
            <div class="animate-card bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-6 shadow-sm flex flex-col justify-between">
                <div class="flex justify-between items-start">
                    <div>
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-admin-layout>
