/**
 * SANS Malang School Information System
 * Dashboard Animations & Interactivity
 * Using Anime.js
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Initial Entry Animations (Staggered fade-in - ONLY on main Dashboard)
    const animateEntry = () => {
        const isDashboard = window.location.pathname === '/dashboard' || window.location.pathname === '/admin/dashboard' || window.location.pathname === '/';
        if (!isDashboard) return;

        const isDesktop = window.innerWidth >= 768;

        // Grid cards entrance (staggered, fast & crisp)
        anime({
            targets: '.dashboard-grid .animate-card, .dashboard-overview-container .animate-card',
            scale: [0.98, 1],
            translateY: [10, 0],
            opacity: [0, 1],
            delay: anime.stagger(40, { start: 100 }),
            duration: 300,
            easing: 'easeOutQuad'
        });
    };

    // 2. Stat Counter Animations
    const animateCounters = () => {
        const counters = document.querySelectorAll('.stat-counter');
        counters.forEach(counter => {
            const targetVal = parseInt(counter.getAttribute('data-target') || '0', 10);
            const obj = { value: 0 };
            
            anime({
                targets: obj,
                value: targetVal,
                round: 1,
                easing: 'easeOutExpo',
                duration: 1200,
                delay: 200,
                update: () => {
                    counter.innerHTML = obj.value.toLocaleString('id-ID');
                }
            });
        });
    };

    // 3. Hover Micro-animations
    const setupHoverAnimations = () => {
        // Menu item hovers
        const menuItems = document.querySelectorAll('.menu-item');
        menuItems.forEach(item => {
            item.addEventListener('mouseenter', () => {
                const icon = item.querySelector('.menu-icon');
                const text = item.querySelector('.menu-text');
                if (icon) {
                    anime({
                        targets: icon,
                        scale: 1.15,
                        duration: 150,
                        easing: 'easeOutQuad'
                    });
                }
                if (text) {
                    anime({
                        targets: text,
                        translateX: 3,
                        duration: 150,
                        easing: 'easeOutQuad'
                    });
                }
            });

            item.addEventListener('mouseleave', () => {
                const icon = item.querySelector('.menu-icon');
                const text = item.querySelector('.menu-text');
                if (icon) {
                    anime({
                        targets: icon,
                        scale: 1.0,
                        duration: 150,
                        easing: 'easeOutQuad'
                    });
                }
                if (text) {
                    anime({
                        targets: text,
                        translateX: 0,
                        duration: 150,
                        easing: 'easeOutQuad'
                    });
                }
            });
        });

        // Stat Card hovers (ONLY for small grid stat cards, never for tables or sections)
        const cards = document.querySelectorAll('.grid > div.animate-card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                anime({
                    targets: card,
                    translateY: -2,
                    duration: 150,
                    easing: 'easeOutQuad'
                });
            });
            card.addEventListener('mouseleave', () => {
                anime({
                    targets: card,
                    translateY: 0,
                    duration: 150,
                    easing: 'easeOutQuad'
                });
            });
        });
    };

    // 4. Quick Notification Toast
    const setupNotifications = () => {
        // Handled by Alpine in header component
    };

    // 5. Mini Interactive Performance Chart Animation (SVG path)
    const animateChart = () => {
        const path = document.querySelector('.chart-line path');
        if (path) {
            const length = path.getTotalLength();
            path.style.strokeDasharray = length;
            path.style.strokeDashoffset = length;

            anime({
                targets: path,
                strokeDashoffset: [length, 0],
                duration: 2500,
                easing: 'easeOutSine',
                delay: 800
            });
        }
    };

    // 6. Theme Toggle Switch (Dark / Light Mode)
    const setupThemeToggle = () => {
        const themeToggleBtn = document.getElementById('theme-toggle');
        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', () => {
                // Toggle dark class on HTML document
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
                
                // Micro-animation for theme button click
                anime({
                    targets: themeToggleBtn,
                    rotate: '360deg',
                    scale: [0.8, 1],
                    duration: 500,
                    easing: 'easeOutElastic(1, .8)',
                    complete: () => {
                        themeToggleBtn.style.transform = 'none'; // reset style for future clicks
                    }
                });
            });
        }
    };

    // 7. Sidebar Toggle for Mobile & Desktop (Burger & Close Arrow)
    const setupSidebarToggle = () => {
        const toggleBtn = document.getElementById('sidebar-toggle');
        const closeBtn = document.getElementById('sidebar-close');
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        
        if (sidebar) {
            const isDesktop = () => window.innerWidth >= 768;

            const openSidebarMobile = () => {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                document.body.classList.add('sidebar-open');
                if (backdrop) {
                    backdrop.classList.remove('hidden');
                    setTimeout(() => {
                        backdrop.classList.remove('opacity-0');
                        backdrop.classList.add('opacity-100');
                    }, 20);
                }
            };
            
            const closeSidebarMobile = () => {
                sidebar.classList.remove('translate-x-0');
                sidebar.classList.add('-translate-x-full');
                document.body.classList.remove('sidebar-open');
                if (backdrop) {
                    backdrop.classList.remove('opacity-100');
                    backdrop.classList.add('opacity-0');
                    setTimeout(() => {
                        backdrop.classList.add('hidden');
                    }, 300);
                }
            };

            const toggleSidebar = () => {
                if (isDesktop()) {
                    document.body.classList.toggle('sidebar-collapsed');
                } else {
                    if (sidebar.classList.contains('-translate-x-full')) {
                        openSidebarMobile();
                    } else {
                        closeSidebarMobile();
                    }
                }
            };

            if (toggleBtn) {
                toggleBtn.addEventListener('click', toggleSidebar);
            }

            // Tambahan: tombol "Menu" mobile (data-sidebar-toggle)
            document.querySelectorAll('[data-sidebar-toggle]').forEach(btn => {
                btn.addEventListener('click', toggleSidebar);
            });

            if (closeBtn) {
                closeBtn.addEventListener('click', () => {
                    if (isDesktop()) {
                        document.body.classList.add('sidebar-collapsed');
                    } else {
                        closeSidebarMobile();
                    }
                });
            }

            if (backdrop) {
                backdrop.addEventListener('click', closeSidebarMobile);
            }

            window.addEventListener('resize', () => {
                if (isDesktop()) {
                    document.body.classList.remove('sidebar-open');
                    if (backdrop) {
                        backdrop.classList.add('hidden');
                        backdrop.classList.remove('opacity-100');
                        backdrop.classList.add('opacity-0');
                    }
                    sidebar.classList.remove('translate-x-0');
                    sidebar.classList.add('-translate-x-full');
                }
            });
        }
    };

    // 8. Sidebar Scroll Preservation & Active Item Auto-Scroll
    const setupSidebarScroll = () => {
        const sidebarScroll = document.getElementById('sidebar-nav-container') || document.querySelector('#sidebar .overflow-y-auto');
        if (!sidebarScroll) return;

        // Save scroll position on scroll and link clicks
        let scrollTimeout;
        sidebarScroll.addEventListener('scroll', () => {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                sessionStorage.setItem('sidebar_scroll_top', sidebarScroll.scrollTop);
            }, 100);
        }, { passive: true });

        sidebarScroll.addEventListener('click', (e) => {
            if (e.target.closest('a')) {
                sessionStorage.setItem('sidebar_scroll_top', sidebarScroll.scrollTop);
            }
        });

        window.addEventListener('beforeunload', () => {
            sessionStorage.setItem('sidebar_scroll_top', sidebarScroll.scrollTop);
        });

        // Find active menu item
        const findActiveItem = () => {
            // 1. Direct class matching
            let active = sidebarScroll.querySelector(
                'a.bg-slate-100, a.text-indigo-600, a.bg-slate-50, .menu-item.bg-slate-100, a.font-semibold:not(h1):not(h2):not(h3):not(h4)'
            );
            if (active) return active;

            // 2. URL pathname matching fallback
            const currentPath = window.location.pathname.replace(/\/$/, '') || '/';
            const links = Array.from(sidebarScroll.querySelectorAll('a[href]'));
            for (const link of links) {
                try {
                    const linkPath = new URL(link.href, window.location.origin).pathname.replace(/\/$/, '') || '/';
                    if (linkPath === currentPath && linkPath !== '') {
                        return link;
                    }
                } catch (err) {}
            }
            return null;
        };

        const activeItem = findActiveItem();
        const savedScroll = sessionStorage.getItem('sidebar_scroll_top');
        let hasRestored = false;

        // Restore saved scroll position immediately if available
        if (savedScroll !== null) {
            const scrollVal = parseInt(savedScroll, 10);
            if (!isNaN(scrollVal)) {
                sidebarScroll.scrollTop = scrollVal;
                hasRestored = true;
            }
        }

        // Ensure active item is within comfortable visible range
        const scrollActiveIntoView = () => {
            if (!activeItem) return;
            const containerRect = sidebarScroll.getBoundingClientRect();
            const itemRect = activeItem.getBoundingClientRect();

            // Check if item is above or below the container's visible bounds
            const isAbove = itemRect.top < containerRect.top + 20;
            const isBelow = itemRect.bottom > containerRect.bottom - 20;

            if (isAbove || isBelow || !hasRestored) {
                activeItem.scrollIntoView({
                    block: 'nearest',
                    behavior: hasRestored ? 'instant' : 'auto'
                });
            }
        };

        // Run immediately
        scrollActiveIntoView();

        // Run after potential Alpine.js accordion collapse/expand animation completes
        setTimeout(scrollActiveIntoView, 150);
        setTimeout(scrollActiveIntoView, 350);
    };

    // Execute animations
    animateEntry();
    animateCounters();
    setupHoverAnimations();
    setupNotifications();
    animateChart();
    setupThemeToggle();
    setupSidebarToggle();
    setupSidebarScroll();
});
