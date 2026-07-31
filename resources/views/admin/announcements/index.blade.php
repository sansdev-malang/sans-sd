<x-admin-layout>
    <div class="p-6 space-y-6">
        <section class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex flex-col gap-0.5">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Manajemen Pengumuman</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Kelola semua pengumuman internal sekolah.</p>
            </div>
            @if(auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin_sd') || auth()->user()->hasRole('admin_paud') || auth()->user()->hasRole('admin_smp') || auth()->user()->hasRole('kepala_sekolah') || auth()->user()->hasRole('waka'))
            <div>
                <a href="{{ route('announcements.create') }}" class="inline-flex items-center px-4 py-2 bg-slate-900 dark:bg-slate-50 border border-transparent rounded-md font-semibold text-xs text-white dark:text-slate-900 uppercase tracking-widest hover:bg-slate-800 dark:hover:bg-slate-200 focus:bg-slate-800 dark:focus:bg-slate-200 active:bg-slate-900 dark:active:bg-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Tambah Pengumuman
                </a>
            </div>
            @endif
        </section>



        <div class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-slate-500 dark:text-slate-400">
                    <thead class="text-xs text-slate-700 uppercase bg-slate-50 dark:bg-slate-900 dark:text-slate-300">
                        <tr>
                            <th scope="col" class="px-6 py-3">Judul</th>
                            <th scope="col" class="px-6 py-3">Kategori</th>
                            <th scope="col" class="px-6 py-3">Target</th>
                            <th scope="col" class="px-6 py-3">Waktu</th>
                            <th scope="col" class="px-6 py-3">Dibuat Oleh</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($announcements as $announcement)
                            <tr class="bg-white border-b dark:bg-slate-950 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-900/50">
                                <td class="px-6 py-4 font-medium text-slate-900 dark:text-white">
                                    {{ $announcement->title }}
                                </td>
                                <td class="px-6 py-4 capitalize">
                                    {{ $announcement->category }}
                                </td>
                                <td class="px-6 py-4 capitalize">
                                    {{ $announcement->target_audience }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs">
                                        <div class="mb-1"><span class="font-semibold">Mulai:</span> {{ $announcement->publish_date ? $announcement->publish_date->format('d M Y, H:i') : '-' }}</div>
                                        <div><span class="font-semibold">Akhir:</span> {{ $announcement->expiry_date ? $announcement->expiry_date->format('d M Y, H:i') : 'Selamanya' }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    {{ $announcement->creator->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($announcement->is_active)
                                        <span class="bg-emerald-100 text-emerald-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded border border-transparent dark:bg-emerald-500/20 dark:text-emerald-400 dark:border-emerald-500/30">Aktif</span>
                                    @else
                                        <span class="bg-slate-100 text-slate-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded border border-transparent dark:bg-slate-500/20 dark:text-slate-400 dark:border-slate-500/30">Draft/Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right flex justify-end gap-2">
                                    <a href="{{ route('announcements.show', $announcement) }}" class="text-blue-600 dark:text-blue-500 hover:underline">Lihat</a>
                                    @if(auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('admin_sd') || auth()->user()->hasRole('admin_paud') || auth()->user()->hasRole('admin_smp') || auth()->user()->hasRole('kepala_sekolah') || auth()->user()->hasRole('waka'))
                                    <a href="{{ route('announcements.edit', $announcement) }}" class="text-emerald-600 dark:text-emerald-500 hover:underline">Edit</a>
                                    <form action="{{ route('announcements.destroy', $announcement) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 dark:text-red-500 hover:underline">Hapus</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-slate-500">Belum ada pengumuman.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $announcements->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
