/**
 * SANS Malang School Information System
 * Dashboard Animations & Interactivity
 * Using Anime.js
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Initial Entry Animations (Staggered fade-in and scale-up)
    const animateEntry = () => {
        const isDesktop = window.innerWidth >= 768;

        // Sidebar entrance (Desktop only to prevent auto-opening on mobile)
        if (isDesktop) {
            anime({
                targets: '#sidebar',
                translateX: [-100, 0],
                opacity: [0, 1],
                easing: 'easeOutExpo',
                duration: 1200
            });
        }

        // Header entrance
        anime({
            targets: '#header',
            translateY: [-50, 0],
            opacity: [0, 1],
            easing: 'easeOutExpo',
            duration: 1200,
            delay: 150
        });

        // Grid cards entrance (staggered)
        anime({
            targets: '.animate-card',
            scale: [0.9, 1],
            translateY: [30, 0],
            opacity: [0, 1],
            delay: anime.stagger(100, { start: 300 }),
            duration: 1000,
            easing: 'easeOutElastic(1, .8)'
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
                duration: 2000,
                delay: 500,
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
                        scale: 1.2,
                        rotate: '5deg',
                        duration: 300,
                        easing: 'easeOutQuad'
                    });
                }
                if (text) {
                    anime({
                        targets: text,
                        translateX: 5,
                        duration: 300,
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
                        rotate: '0deg',
                        duration: 300,
                        easing: 'easeOutQuad'
                    });
                }
                if (text) {
                    anime({
                        targets: text,
                        translateX: 0,
                        duration: 300,
                        easing: 'easeOutQuad'
                    });
                }
            });
        });

        // Stat Card hovers
        const cards = document.querySelectorAll('.animate-card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                anime({
                    targets: card,
                    translateY: -6,
                    scale: 1.02,
                    boxShadow: '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
                    duration: 250,
                    easing: 'easeOutQuad'
                });
            });
            card.addEventListener('mouseleave', () => {
                anime({
                    targets: card,
                    translateY: 0,
                    scale: 1.0,
                    boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
                    duration: 250,
                    easing: 'easeOutQuad'
                });
            });
        });
    };

    // 4. Quick Notification Toast
    const setupNotifications = () => {
        const notifyBtn = document.getElementById('notify-btn');
        const toast = document.getElementById('toast-notification');
        
        if (notifyBtn && toast) {
            notifyBtn.addEventListener('click', () => {
                toast.classList.remove('hidden');
                
                // Slide in toast
                anime({
                    targets: toast,
                    translateX: [300, 0],
                    opacity: [0, 1],
                    duration: 500,
                    easing: 'easeOutExpo'
                });

                // Auto hide after 4 seconds
                setTimeout(() => {
                    anime({
                        targets: toast,
                        translateX: [0, 300],
                        opacity: [1, 0],
                        duration: 500,
                        easing: 'easeInExpo',
                        complete: () => {
                            toast.classList.add('hidden');
                        }
                    });
                }, 4000);
            });
        }
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
        }
    };

    // Execute animations
    animateEntry();
    animateCounters();
    setupHoverAnimations();
    setupNotifications();
    animateChart();
    setupThemeToggle();
    setupSidebarToggle();
});
