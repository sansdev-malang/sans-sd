<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @if(setting('app_favicon'))
            <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . setting('app_favicon')) }}">
        @else
            <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Cdefs%3E%3ClinearGradient id='grad' x1='0%25' y1='0%25' x2='100%25' y2='100%25'%3E%3Cstop offset='0%25' stop-color='%23e91d1d'/%3E%3Cstop offset='25%25' stop-color='%23f3e40f'/%3E%3Cstop offset='50%25' stop-color='%2333ee0d'/%3E%3Cstop offset='75%25' stop-color='%23070be2'/%3E%3Cstop offset='100%25' stop-color='%238a18c5'/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width='32' height='32' rx='8' fill='url(%23grad)'/%3E%3Ctext x='50%25' y='55%25' dominant-baseline='middle' text-anchor='middle' fill='%23ffffff' font-family='sans-serif' font-weight='900' font-size='18'%3E{{ substr(setting('app_name', 'SANS'), 0, 1) }}%3C/text%3E%3C/svg%3E">
        @endif
        @php
            if (!isset($title) || empty($title) || $title === 'Admin Dashboard') {
                $routeName = request()->route() ? request()->route()->getName() : '';
                $routeTitles = [
                    'dashboard' => 'Dashboard',
                    'bonus-reports.index' => 'Rekap Bonus Kehadiran',
                    'attendances.index' => 'Data Riwayat Absensi',
                    'leaves.index' => 'Pengajuan Izin & Cuti',
                    'my-leaves.index' => 'Izin & Cuti Saya',
                    'my-attendance' => 'Absensi Saya',
                    'payslips.index' => 'Slip Gaji Saya',
                    'settings' => 'Pengaturan Aplikasi',
                    'users.index' => 'Manajemen Pengguna',
                    'teachers.index' => 'Data Guru',
                    'employees.index' => 'Data Pegawai',
                    'announcements.index' => 'Pengumuman',
                    'leave-approvals.index' => 'Persetujuan Izin & Cuti',
                    'absensi_hari_ini' => 'Absensi Hari Ini',
                    'absensi_laporan' => 'Laporan Absensi',
                    'absensi_izin_cuti' => 'Izin & Cuti',
                    'absensi_mesin' => 'Mesin Absensi',
                    'absensi_log_penarikan' => 'Log Penarikan Absensi',
                    'absensi_shift' => 'Kelola Shift',
                    'absensi_libur' => 'Kelola Hari Libur',
                    'absensi_bonus_denda' => 'Bonus & Denda',
                    'absensi_karyawan' => 'Data Karyawan',
                    'homebase_leaderboard' => 'Homebase Leaderboard',
                ];
                $title = $routeTitles[$routeName] ?? 'Dashboard';
            }
        @endphp
        <title>{{ setting('app_name', 'SANS Malang') }} - @yield('title', $title)</title>

        <!-- Google Fonts: Inter & Plus Jakarta Sans -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link href="https://fonts.cdnfonts.com/css/nasalization" rel="stylesheet">

        <!-- Load Tailwind v4 styling compiled by Vite / CDN Fallback -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
            <!-- Alpine.js + Collapse CDN Fallback -->
            <script src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js" defer></script>
            <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        @endif

        <script>
            // Support tailwind CDN dark mode class configuration
            if (typeof tailwind !== 'undefined') {
                tailwind.config = {
                    darkMode: 'class'
                }
            }
            // Apply saved theme or default to dark
            if (localStorage.getItem('color-theme') === 'light' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: light)').matches)) {
                document.documentElement.classList.remove('dark');
            } else {
                document.documentElement.classList.add('dark');
            }
        </script>

        <style>
            body {
                font-family: 'Inter', 'Plus Jakarta Sans', sans-serif;
            }
            /* Custom shadcn-like scrollbar */
            ::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }
            ::-webkit-scrollbar-track {
                background: transparent;
            }
            ::-webkit-scrollbar-thumb {
                background: rgba(100, 116, 139, 0.2);
                border-radius: 99px;
            }
            ::-webkit-scrollbar-thumb:hover {
                background: rgba(100, 116, 139, 0.45);
            }
            /* Smooth Desktop Sidebar Transitions (Mini-Sidebar / Collapsed view) */
            @media (min-width: 768px) {
                #sidebar {
                    transition: width 0.2s cubic-bezier(0.4, 0, 0.2, 1), padding 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
                }
                .sidebar-collapsed #sidebar {
                    width: 4rem !important; /* w-16 */
                    padding-left: 0.5rem !important;
                    padding-right: 0.5rem !important;
                }
                .sidebar-collapsed .menu-text,
                .sidebar-collapsed .school-info,
                .sidebar-collapsed .chevron-icon,
                .sidebar-collapsed .user-info {
                    display: none !important;
                }
                .sidebar-collapsed .menu-item {
                    justify-content: center !important;
                    padding-left: 0 !important;
                    padding-right: 0 !important;
                }
                .sidebar-collapsed .workspace-selector {
                    justify-content: center !important;
                    padding-left: 0 !important;
                    padding-right: 0 !important;
                }
                .sidebar-collapsed .user-selector {
                    justify-content: center !important;
                    padding-left: 0 !important;
                    padding-right: 0 !important;
                }
                .sidebar-collapsed #sidebar .flex-1 {
                    overflow: visible !important;
                }
                .sidebar-collapsed .sidebar-tooltip {
                    display: block;
                }
            }
            /* Hidden by default, shown on hover when collapsed */
            .sidebar-tooltip {
                display: none;
            }
            /* Hide scrollbar for Chrome, Safari and Opera */
            .no-scrollbar::-webkit-scrollbar {
                display: none;
            }
            /* Hide scrollbar for IE, Edge and Firefox */
            .no-scrollbar {
                -ms-overflow-style: none;  /* IE and Edge */
                scrollbar-width: none;  /* Firefox */
            }

            /* Animated gradient logo background */
            @keyframes gradient-bg {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            .logo-gradient-bg {
                background: linear-gradient(135deg, #e91d1d, #f3e40fff, #33ee0dee, #070be2ff, #8a18c5ff);
                background-size: 400% 400%;
                animation: gradient-bg 16s ease-in-out infinite;
            }
        </style>
    </head>
    <body class="h-full bg-slate-50 dark:bg-[#09090b] text-[#09090b] dark:text-slate-50 antialiased overflow-x-hidden transition-colors duration-200">
        <div class="flex h-screen overflow-hidden">

            <!-- SIDEBAR BACKDROP FOR MOBILE -->
            <div id="sidebar-backdrop" class="fixed inset-0 z-35 bg-slate-950/20 backdrop-blur-xs hidden md:hidden transition-opacity duration-200 opacity-0"></div>

            <!-- SIDEBAR -->
            @include('partials.admin.sidebar')

            <!-- MAIN LAYOUT -->
            <main class="flex-1 flex flex-col overflow-y-auto">
                <!-- HEADER -->
                @include('partials.admin.header')

                <!-- MAIN CONTENT CONTAINER -->
                {{ $slot }}
            </main>
        </div>

        <!-- TOAST NOTIFICATION CONTAINER -->
        <div id="toast-notification" class="fixed bottom-5 right-5 z-50 hidden bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-50 px-4 py-3 rounded-xl shadow-lg border border-slate-200 dark:border-slate-800 flex items-center gap-3 max-w-sm">
            <div id="toast-icon-bg" class="w-8 h-8 rounded-full flex items-center justify-center shrink-0">
                <i id="toast-icon" data-lucide="check" class="w-4 h-4"></i>
            </div>
            <div class="text-left">
                <h5 id="toast-title" class="text-xs font-bold">Notifikasi</h5>
                <p id="toast-message" class="text-xs text-slate-500 dark:text-slate-400"></p>
            </div>
        </div>

        <!-- Anime.js CDN -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.2/anime.min.js" referrerpolicy="no-referrer"></script>

        <!-- Lucide Icons CDN -->
        <script src="https://unpkg.com/lucide@latest"></script>
        <script>
            // Initialize Lucide Icons
            lucide.createIcons();

            // Global Toast helper function
            function showToast(title, message, type = 'success') {
                const toast = document.getElementById('toast-notification');
                const titleEl = document.getElementById('toast-title');
                const messageEl = document.getElementById('toast-message');
                const iconBg = document.getElementById('toast-icon-bg');
                const icon = document.getElementById('toast-icon');
                
                if (!toast) return;

                titleEl.textContent = title;
                messageEl.textContent = message;

                if (type === 'success') {
                    iconBg.className = 'w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0';
                    icon.setAttribute('data-lucide', 'check');
                } else {
                    iconBg.className = 'w-8 h-8 rounded-full bg-rose-100 dark:bg-rose-955/20 text-rose-600 dark:text-rose-455 flex items-center justify-center shrink-0';
                    icon.setAttribute('data-lucide', 'alert-circle');
                }

                if (window.lucide) {
                    window.lucide.createIcons();
                }

                toast.classList.remove('hidden');
                
                if (window.anime) {
                    window.anime({
                        targets: toast,
                        translateX: [300, 0],
                        opacity: [0, 1],
                        duration: 400,
                        easing: 'easeOutExpo'
                    });

                    setTimeout(() => {
                        window.anime({
                            targets: toast,
                            translateX: [0, 300],
                            opacity: [1, 0],
                            duration: 400,
                            easing: 'easeInExpo',
                            complete: () => {
                                toast.classList.add('hidden');
                            }
                        });
                    }, 4000);
                } else {
                    setTimeout(() => {
                        toast.classList.add('hidden');
                    }, 4000);
                }
            }
        </script>

        <!-- Session Message Trigger Script -->
        @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    showToast('Sukses!', "{{ session('success') }}", 'success');
                });
            </script>
        @endif
        @if(session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    showToast('Perhatian!', "{{ session('error') }}", 'error');
                });
            </script>
        @endif

        <!-- Custom Isolated Dashboard Animations Script -->
        <script src="{{ asset('js/dashboard.js') }}?v={{ time() }}"></script>

        <!-- Global Delete Confirmation Modal Script -->
        <script>
            function showGlobalConfirmModal(message, onConfirm, isDelete = true) {
                const existing = document.getElementById('global-delete-modal');
                if (existing) existing.remove();
                
                const isDark = document.documentElement.classList.contains('dark');
                const panelBg = isDark ? '#09090b' : '#ffffff';
                const panelText = isDark ? '#f8fafc' : '#0f172a';
                const panelBorder = isDark ? '#1e293b' : '#e2e8f0';
                const descText = isDark ? '#94a3b8' : '#64748b';
                
                const cancelBg = isDark ? '#09090b' : '#ffffff';
                const cancelBorder = isDark ? '#1e293b' : '#e2e8f0';
                const cancelText = isDark ? '#cbd5e1' : '#334155';
                
                const iconBg = isDelete 
                    ? (isDark ? 'rgba(225, 29, 72, 0.15)' : '#ffe4e6')
                    : (isDark ? 'rgba(79, 70, 229, 0.15)' : '#e0e7ff');
                const iconColor = isDelete ? '#e11d48' : '#4f46e5';
                const titleText = isDelete ? 'Konfirmasi Hapus' : 'Konfirmasi Tindakan';
                const confirmBg = isDelete ? '#e11d48' : '#4f46e5';
                const confirmHoverBg = isDelete ? '#be123c' : '#4338ca';
                const confirmText = isDelete ? 'Ya, Hapus' : 'Ya, Lanjutkan';
                
                const iconSvg = isDelete 
                    ? `<svg xmlns="http://www.w3.org/2000/svg" style="width: 2rem; height: 2rem; color: ${iconColor};" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>`
                    : `<svg xmlns="http://www.w3.org/2000/svg" style="width: 2rem; height: 2rem; color: ${iconColor};" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>`;
                
                const modal = document.createElement('div');
                modal.id = 'global-delete-modal';
                modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 99999; display: flex; align-items: center; justify-content: center; padding: 1rem; box-sizing: border-box;';
                
                modal.innerHTML = `
                    <div style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); transition: opacity 0.3s ease; opacity: 0;" id="global-delete-backdrop"></div>
                    <div style="position: relative; background: ${panelBg}; color: ${panelText}; border-radius: 1rem; width: 100%; max-width: 400px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.04); border: 1px solid ${panelBorder}; padding: 1.5rem; z-index: 10; transition: all 0.3s ease; transform: scale(0.95); opacity: 0; box-sizing: border-box;" id="global-delete-panel">
                        <div style="text-align: center; font-family: system-ui, -apple-system, sans-serif;">
                            <div style="width: 4rem; height: 4rem; border-radius: 9999px; background-color: ${iconBg}; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                                ${iconSvg}
                            </div>
                            <h3 style="font-size: 1.125rem; font-weight: 700; margin: 0 0 0.5rem 0; line-height: 1.25;">${titleText}</h3>
                            <p style="font-size: 0.875rem; color: ${descText}; margin: 0 0 1.5rem 0; line-height: 1.5;" id="global-delete-message"></p>
                            <div style="display: flex; gap: 0.75rem; justify-content: center;">
                                <button type="button" id="global-delete-cancel" style="flex: 1; height: 2.5rem; padding: 0 1rem; border: 1px solid ${cancelBorder}; color: ${cancelText}; background: ${cancelBg}; font-size: 0.875rem; font-weight: 600; border-radius: 0.75rem; cursor: pointer; transition: background 0.2s; outline: none;">
                                    Batal
                                </button>
                                <button type="button" id="global-delete-confirm" style="flex: 1; height: 2.5rem; padding: 0 1rem; border: none; color: #ffffff; background: ${confirmBg}; font-size: 0.875rem; font-weight: 600; border-radius: 0.75rem; cursor: pointer; transition: background 0.2s; outline: none;">
                                    ${confirmText}
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                
                document.body.appendChild(modal);
                document.getElementById('global-delete-message').textContent = message;
                
                const cancelBtn = document.getElementById('global-delete-cancel');
                const confirmBtn = document.getElementById('global-delete-confirm');
                cancelBtn.onmouseenter = () => cancelBtn.style.background = isDark ? '#1e293b' : '#f8fafc';
                cancelBtn.onmouseleave = () => cancelBtn.style.background = cancelBg;
                confirmBtn.onmouseenter = () => confirmBtn.style.background = confirmHoverBg;
                confirmBtn.onmouseleave = () => confirmBtn.style.background = confirmBg;
                
                setTimeout(() => {
                    const backdrop = document.getElementById('global-delete-backdrop');
                    const panel = document.getElementById('global-delete-panel');
                    if (backdrop) backdrop.style.opacity = '1';
                    if (panel) {
                        panel.style.opacity = '1';
                        panel.style.transform = 'scale(1)';
                    }
                }, 10);
                
                const closeModal = () => {
                    const backdrop = document.getElementById('global-delete-backdrop');
                    const panel = document.getElementById('global-delete-panel');
                    if (backdrop) backdrop.style.opacity = '0';
                    if (panel) {
                        panel.style.opacity = '0';
                        panel.style.transform = 'scale(0.95)';
                    }
                    setTimeout(() => modal.remove(), 300);
                };
                
                document.getElementById('global-delete-cancel').addEventListener('click', closeModal);
                document.getElementById('global-delete-backdrop').addEventListener('click', closeModal);
                document.getElementById('global-delete-confirm').addEventListener('click', function() {
                    onConfirm();
                    closeModal();
                });
            }

            document.addEventListener('submit', function (e) {
                const form = e.target;
                
                if (form.dataset.confirmed === 'true' || form.getAttribute('data-confirm') === 'false') {
                    return;
                }
                
                const methodInput = form.querySelector('input[name="_method"]');
                const isDelete = methodInput && methodInput.value.toUpperCase() === 'DELETE';
                
                let confirmMsg = '';
                const onsubmitAttr = form.getAttribute('onsubmit');
                if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
                    const match = onsubmitAttr.match(/confirm\(['"](.*?)['"]\)/);
                    if (match) confirmMsg = match[1];
                }
                
                const activeEl = document.activeElement;
                if (!confirmMsg && activeEl && activeEl.form === form) {
                    const onclickAttr = activeEl.getAttribute('onclick');
                    if (onclickAttr && onclickAttr.includes('confirm(')) {
                        const match = onclickAttr.match(/confirm\(['"](.*?)['"]\)/);
                        if (match) confirmMsg = match[1];
                    }
                }
                
                if (confirmMsg || isDelete) {
                    if (!confirmMsg) {
                        confirmMsg = 'Apakah Anda yakin ingin menghapus data ini?';
                    }
                    
                    e.preventDefault();
                    e.stopPropagation();
                    
                    showGlobalConfirmModal(confirmMsg, function () {
                        form.dataset.confirmed = 'true';
                        const originalOnsubmit = form.getAttribute('onsubmit');
                        form.removeAttribute('onsubmit');
                        form.submit();
                        if (originalOnsubmit) {
                            form.setAttribute('onsubmit', originalOnsubmit);
                        }
                    }, isDelete);
                }
            }, true);

            // Global Tooltip System
            (function() {
                let tooltipEl = null;
                
                function createTooltip() {
                    tooltipEl = document.createElement('div');
                    tooltipEl.id = 'global-tooltip';
                    tooltipEl.style.cssText = 'position: fixed; display: none; background-color: #0f172a; color: #f8fafc; font-size: 10px; font-weight: 500; padding: 4px 8px; border-radius: 6px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06); pointer-events: none; z-index: 999999; transition: opacity 0.15s ease; opacity: 0; white-space: nowrap; font-family: system-ui, -apple-system, sans-serif;';
                    
                    if (document.documentElement.classList.contains('dark')) {
                        tooltipEl.style.backgroundColor = '#09090b';
                        tooltipEl.style.color = '#f8fafc';
                        tooltipEl.style.border = '1px solid #27272a';
                    } else {
                        tooltipEl.style.backgroundColor = '#0f172a';
                        tooltipEl.style.color = '#f8fafc';
                        tooltipEl.style.border = '1px solid #1e293b';
                    }
                    document.body.appendChild(tooltipEl);
                }
                
                document.addEventListener('mouseover', function(e) {
                    const target = e.target.closest('[data-tooltip], [title]');
                    if (!target) return;
                    
                    // Convert title attribute to data-tooltip to prevent native tooltip
                    if (target.hasAttribute('title')) {
                        const titleText = target.getAttribute('title');
                        if (titleText && titleText.trim() !== '') {
                            target.setAttribute('data-tooltip', titleText);
                            target.removeAttribute('title');
                        } else {
                            return;
                        }
                    }
                    
                    if (!tooltipEl) createTooltip();
                    
                    tooltipEl.textContent = target.getAttribute('data-tooltip');
                    tooltipEl.style.display = 'block';
                    
                    const rect = target.getBoundingClientRect();
                    const tooltipWidth = tooltipEl.offsetWidth;
                    const tooltipHeight = tooltipEl.offsetHeight;
                    
                    let left = rect.left + rect.width / 2 - tooltipWidth / 2;
                    let top = rect.top - tooltipHeight - 6;
                    
                    // Boundary safety: show below if goes off top edge
                    if (top < 4) {
                        top = rect.bottom + 6;
                    }
                    // Boundary safety: horizontally constrained
                    if (left < 4) left = 4;
                    if (left + tooltipWidth > window.innerWidth - 4) {
                        left = window.innerWidth - tooltipWidth - 4;
                    }
                    
                    tooltipEl.style.left = left + 'px';
                    tooltipEl.style.top = top + 'px';
                    
                    requestAnimationFrame(() => {
                        tooltipEl.style.opacity = '1';
                    });
                });
                
                document.addEventListener('mouseout', function(e) {
                    const target = e.target.closest('[data-tooltip]');
                    if (!target) return;
                    
                    if (tooltipEl) {
                        tooltipEl.style.opacity = '0';
                        tooltipEl.style.display = 'none';
                    }
                });
                
                window.addEventListener('scroll', () => {
                    if (tooltipEl) {
                        tooltipEl.style.opacity = '0';
                        tooltipEl.style.display = 'none';
                    }
                }, true);
                
                document.addEventListener('click', () => {
                    if (tooltipEl) {
                        tooltipEl.style.opacity = '0';
                        tooltipEl.style.display = 'none';
                    }
                });
            })();
        </script>
    </body>
</html>
