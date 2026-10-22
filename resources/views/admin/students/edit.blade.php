<x-admin-layout>
    <div class="p-3 sm:p-5 lg:p-6 space-y-3">

        <!-- COMPACT HEADER BAR -->
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('students.show', $student->id) }}"
                    class="p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 transition-colors shadow-xs"
                    title="Kembali ke Detail Siswa">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                </a>
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5 text-[11px] text-slate-400 dark:text-slate-500">
                        <a href="{{ route('students.index') }}" class="hover:text-indigo-600 transition-colors">Data Siswa</a>
                        <i data-lucide="chevron-right" class="w-3 h-3 text-slate-300 dark:text-slate-600"></i>
                        <span class="truncate max-w-[150px] sm:max-w-xs">{{ $student->full_name }}</span>
                    </div>
                    <h1 class="text-base sm:text-lg font-bold text-slate-900 dark:text-slate-50 truncate leading-tight">
                        Edit Data: {{ $student->full_name }}
                    </h1>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('students.show', $student->id) }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs transition-colors">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                    Batal
                </a>
            </div>
        </div>

        <!-- FORM CONTAINER -->
        <form action="{{ route('students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.students._form')
        </form>

    </div>
</x-admin-layout>
