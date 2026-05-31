<!-- Main Navigation Component -->
<nav class="sticky top-0 z-50 bg-gradient-to-br from-slate-900 via-blue-900 to-blue-800 shadow-2xl" 
     style="background-size: 200% 200%; animation: headerGradient 12s ease infinite;">
    <!-- Animated top border -->
    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-cyan-400 via-indigo-500 to-cyan-400" 
         style="background-size: 200% 200%; animation: shimmer 8s linear infinite;"></div>
    
    <!-- Radial gradient overlay -->
    <div class="absolute inset-0 pointer-events-none" style="background: radial-gradient(circle at top right, rgba(6, 182, 212, 0.25), transparent 55%);"></div>
    <div class="absolute inset-0 pointer-events-none" style="background: radial-gradient(circle at bottom left, rgba(99, 102, 241, 0.25), transparent 50%);"></div>
    
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex items-center justify-between h-16 md:h-18 lg:h-20">
            <!-- Logo and Brand -->
            <a href="{{ route('home') }}" class="flex items-center space-x-2 sm:space-x-3 md:space-x-3 group relative z-10 flex-shrink-0">
                @php
                    try {
                        $logoPath = public_path('images/logo.png');
                        $logoExists = @file_exists($logoPath) && @is_readable($logoPath);
                    } catch (\Exception $e) {
                        $logoExists = false;
                    }
                @endphp
                @if($logoExists)
                    <img src="{{ asset('images/logo.png') }}" alt="CloudLab Latam" 
                         class="h-8 sm:h-10 md:h-12 lg:h-14 w-auto max-w-[120px] sm:max-w-[140px] md:max-w-[160px] lg:max-w-[180px] object-contain"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 lg:w-14 lg:h-14 bg-gradient-to-br from-cyan-400 to-indigo-500 rounded-xl items-center justify-center text-white font-bold text-sm sm:text-base md:text-lg lg:text-xl shadow-lg" style="display: none;">
                        CL
                    </div>
                @else
                    <div class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 lg:w-14 lg:h-14 bg-gradient-to-br from-cyan-400 to-indigo-500 rounded-xl flex items-center justify-center text-white font-bold text-sm sm:text-base md:text-lg lg:text-xl shadow-lg">
                        CL
                    </div>
                @endif
                <span class="text-sm sm:text-base md:text-lg lg:text-xl xl:text-xl font-bold text-slate-50 group-hover:text-cyan-200 transition-colors whitespace-nowrap">
                    CloudLab Latam
                </span>
            </a>
            
            <!-- Mobile menu button -->
            <button type="button" 
                    class="md:hidden p-2 rounded-lg text-slate-200 hover:bg-slate-800/50 hover:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-cyan-400"
                    onclick="toggleMobileMenu(event)"
                    aria-label="Toggle navigation"
                    id="mobile-menu-toggle"
                    style="position: relative; z-index: 100; pointer-events: auto; cursor: pointer;">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="menu-icon" style="pointer-events: none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                <svg class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="close-icon" style="pointer-events: none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            
            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center flex-1 justify-center mx-1 sm:mx-2 md:mx-2 lg:mx-3 xl:mx-4 min-w-0">
                <ul class="flex items-center space-x-1 sm:space-x-1.5 md:space-x-2 lg:space-x-3 xl:space-x-4 2xl:space-x-6 justify-center flex-wrap" style="list-style: none; padding: 0; margin: 0;">
                    <li class="flex-shrink-0">
                        <a href="{{ route('home') }}" 
                           class="nav-link-item px-1.5 sm:px-2 md:px-2.5 lg:px-3 xl:px-4 2xl:px-4 py-1.5 sm:py-1.5 md:py-2 text-xs sm:text-xs md:text-xs lg:text-sm xl:text-base 2xl:text-base font-medium text-slate-200 rounded-lg transition-all duration-300 hover:text-white hover:bg-slate-800/30 {{ request()->routeIs('home') ? 'nav-link-active' : '' }}">
                            {{ __('messages.nav.home') }}
                        </a>
                    </li>
                    <li class="flex-shrink-0">
                        <a href="{{ route('about.index') }}" 
                           class="nav-link-item px-1.5 sm:px-2 md:px-2.5 lg:px-3 xl:px-4 2xl:px-4 py-1.5 sm:py-1.5 md:py-2 text-xs sm:text-xs md:text-xs lg:text-sm xl:text-base 2xl:text-base font-medium text-slate-200 rounded-lg transition-all duration-300 hover:text-white hover:bg-slate-800/30 {{ request()->routeIs('about.*') ? 'nav-link-active' : '' }}">
                            {{ __('messages.nav.about') }}
                        </a>
                    </li>
                    <li class="flex-shrink-0">
                        <a href="{{ route('technologies.index') }}" 
                           class="nav-link-item px-1.5 sm:px-2 md:px-2.5 lg:px-3 xl:px-4 2xl:px-4 py-1.5 sm:py-1.5 md:py-2 text-xs sm:text-xs md:text-xs lg:text-sm xl:text-base 2xl:text-base font-medium text-slate-200 rounded-lg transition-all duration-300 hover:text-white hover:bg-slate-800/30 {{ request()->routeIs('technologies.*') ? 'nav-link-active' : '' }}">
                            {{ __('messages.nav.technologies') }}
                        </a>
                    </li>
                    <li class="flex-shrink-0">
                        <a href="{{ route('projects.index') }}" 
                           class="nav-link-item px-1.5 sm:px-2 md:px-2.5 lg:px-3 xl:px-4 2xl:px-4 py-1.5 sm:py-1.5 md:py-2 text-xs sm:text-xs md:text-xs lg:text-sm xl:text-base 2xl:text-base font-medium text-slate-200 rounded-lg transition-all duration-300 hover:text-white hover:bg-slate-800/30 {{ request()->routeIs('projects.*') ? 'nav-link-active' : '' }}">
                            {{ __('messages.nav.projects') }}
                        </a>
                    </li>
                    <li class="flex-shrink-0">
                        <a href="{{ route('blogs.index') }}" 
                           class="nav-link-item px-1.5 sm:px-2 md:px-2.5 lg:px-3 xl:px-4 2xl:px-4 py-1.5 sm:py-1.5 md:py-2 text-xs sm:text-xs md:text-xs lg:text-sm xl:text-base 2xl:text-base font-medium text-slate-200 rounded-lg transition-all duration-300 hover:text-white hover:bg-slate-800/30 {{ request()->routeIs('blogs.*') ? 'nav-link-active' : '' }}">
                            {{ __('messages.nav.blog') }}
                        </a>
                    </li>
                    <li class="flex-shrink-0">
                        <a href="{{ route('contact.index') }}" 
                           class="nav-link-item px-1.5 sm:px-2 md:px-2.5 lg:px-3 xl:px-4 2xl:px-4 py-1.5 sm:py-1.5 md:py-2 text-xs sm:text-xs md:text-xs lg:text-sm xl:text-base 2xl:text-base font-medium text-slate-200 rounded-lg transition-all duration-300 hover:text-white hover:bg-slate-800/30 {{ request()->routeIs('contact.*') ? 'nav-link-active' : '' }}">
                            {{ __('messages.nav.contact') }}
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Right Section: Language -->
            <div class="hidden md:flex items-center space-x-1.5 sm:space-x-2 lg:space-x-3 relative z-10 flex-shrink-0">
                <div class="flex items-center space-x-1">
                    <a href="{{ route('language.switch', 'en') }}" 
                       class="px-2 sm:px-2.5 md:px-3 py-1 sm:py-1.5 text-xs sm:text-xs md:text-sm lg:text-sm font-medium rounded-md transition-all duration-300 {{ app()->getLocale() == 'en' ? 'text-white' : 'bg-slate-900/45 text-slate-200 hover:bg-slate-800/50 hover:text-white' }}" @if(app()->getLocale() == 'en') style="background-color: var(--primary);" @endif>
                        EN
                    </a>
                    <a href="{{ route('language.switch', 'es') }}" 
                       class="px-2 sm:px-2.5 md:px-3 py-1 sm:py-1.5 text-xs sm:text-xs md:text-sm lg:text-sm font-medium rounded-md transition-all duration-300 {{ app()->getLocale() == 'es' ? 'text-white' : 'text-slate-200 hover:text-white' }}" @if(app()->getLocale() == 'es') style="background-color: var(--primary);" @endif>
                        ES
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Mobile Navigation Menu -->
        <div id="mobile-menu" class="mobile-menu-container hidden md:hidden">
            <div class="mobile-menu-overlay" onclick="closeMobileMenu()"></div>
            <div class="mobile-menu-content">
                <div class="mobile-menu-header">
                    <span class="mobile-menu-title">Menu</span>
                    <button type="button" 
                            class="mobile-menu-close"
                            onclick="closeMobileMenu()"
                            aria-label="Close menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <ul class="mobile-menu-list">
                    <li class="mobile-menu-item">
                        <a href="{{ route('home') }}" 
                           class="mobile-menu-link {{ request()->routeIs('home') ? 'mobile-menu-link-active' : '' }}"
                           onclick="closeMobileMenu()">
                            <i class="bi bi-house-door me-3"></i>
                            <span>{{ __('messages.nav.home') }}</span>
                        </a>
                    </li>
                    <li class="mobile-menu-item">
                        <a href="{{ route('about.index') }}" 
                           class="mobile-menu-link {{ request()->routeIs('about.*') ? 'mobile-menu-link-active' : '' }}"
                           onclick="closeMobileMenu()">
                            <i class="bi bi-info-circle me-3"></i>
                            <span>{{ __('messages.nav.about') }}</span>
                        </a>
                    </li>
                    <li class="mobile-menu-item">
                        <a href="{{ route('technologies.index') }}" 
                           class="mobile-menu-link {{ request()->routeIs('technologies.*') ? 'mobile-menu-link-active' : '' }}"
                           onclick="closeMobileMenu()">
                            <i class="bi bi-cpu me-3"></i>
                            <span>{{ __('messages.nav.technologies') }}</span>
                        </a>
                    </li>
                    <li class="mobile-menu-item">
                        <a href="{{ route('projects.index') }}" 
                           class="mobile-menu-link {{ request()->routeIs('projects.*') ? 'mobile-menu-link-active' : '' }}"
                           onclick="closeMobileMenu()">
                            <i class="bi bi-folder me-3"></i>
                            <span>{{ __('messages.nav.projects') }}</span>
                        </a>
                    </li>
                    <li class="mobile-menu-item">
                        <a href="{{ route('blogs.index') }}" 
                           class="mobile-menu-link {{ request()->routeIs('blogs.*') ? 'mobile-menu-link-active' : '' }}"
                           onclick="closeMobileMenu()">
                            <i class="bi bi-journal-text me-3"></i>
                            <span>{{ __('messages.nav.blog') }}</span>
                        </a>
                    </li>
                    <li class="mobile-menu-item">
                        <a href="{{ route('contact.index') }}" 
                           class="mobile-menu-link {{ request()->routeIs('contact.*') ? 'mobile-menu-link-active' : '' }}"
                           onclick="closeMobileMenu()">
                            <i class="bi bi-envelope me-3"></i>
                            <span>{{ __('messages.nav.contact') }}</span>
                        </a>
                    </li>
                </ul>
                
                <!-- Language Switcher -->
                <div class="mobile-menu-language">
                    <div class="mobile-language-label">{{ __('messages.nav.language') }}</div>
                    <div class="mobile-language-buttons">
                        <a href="{{ route('language.switch', 'en') }}" 
                           class="mobile-language-btn {{ app()->getLocale() == 'en' ? 'mobile-language-btn-active' : '' }}"
                           @if(app()->getLocale() == 'en') style="background-color: var(--primary);" @endif>
                            EN
                        </a>
                        <a href="{{ route('language.switch', 'es') }}" 
                           class="mobile-language-btn {{ app()->getLocale() == 'es' ? 'mobile-language-btn-active' : '' }}"
                           @if(app()->getLocale() == 'es') style="background-color: var(--primary);" @endif>
                            ES
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
    function toggleMobileMenu(event) {
        // Only work on mobile screens
        if (window.innerWidth >= 768) {
            return; // Don't allow menu on desktop
        }
        
        // Prevent event from bubbling up
        if (event) {
            event.stopPropagation();
            event.preventDefault();
        }
        
        const menu = document.getElementById('mobile-menu');
        const menuIcon = document.getElementById('menu-icon');
        const closeIcon = document.getElementById('close-icon');
        const body = document.body;
        
        if (!menu) {
            console.error('Mobile menu element not found');
            return;
        }
        
        if (menu.classList.contains('hidden')) {
            // Open menu
            menu.classList.remove('hidden');
            // Force display and visibility
            menu.style.display = 'flex';
            menu.style.visibility = 'visible';
            menu.style.opacity = '1';
            menu.style.zIndex = '9999';
            menu.style.pointerEvents = 'auto';
            
            // Ensure menu content is visible
            const menuContent = menu.querySelector('.mobile-menu-content');
            if (menuContent) {
                menuContent.style.display = 'flex';
                menuContent.style.visibility = 'visible';
                menuContent.style.opacity = '1';
            }
            
            menuIcon.classList.add('hidden');
            closeIcon.classList.remove('hidden');
            body.style.overflow = 'hidden'; // Prevent body scroll
            
            // Trigger animation
            requestAnimationFrame(() => {
                menu.classList.add('mobile-menu-open');
            });
        } else {
            // Close menu
            menu.classList.remove('mobile-menu-open');
            body.style.overflow = ''; // Restore body scroll
            setTimeout(() => {
                menu.classList.add('hidden');
                menu.style.display = 'none';
                menu.style.visibility = 'hidden';
                menu.style.opacity = '0';
                menuIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
            }, 300); // Wait for animation to complete
        }
    }
    
    function closeMobileMenu() {
        const menu = document.getElementById('mobile-menu');
        const menuIcon = document.getElementById('menu-icon');
        const closeIcon = document.getElementById('close-icon');
        const body = document.body;
        
        if (!menu) return;
        
        if (!menu.classList.contains('hidden')) {
            menu.classList.remove('mobile-menu-open');
            body.style.overflow = '';
            setTimeout(() => {
                menu.classList.add('hidden');
                menu.style.display = 'none';
                menu.style.visibility = 'hidden';
                menuIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
            }, 300);
        }
    }
    
    // Close menu when clicking outside (only if menu is open and on mobile)
    let clickOutsideHandler = function(event) {
        // Only work on mobile screens
        if (window.innerWidth >= 768) {
            return; // Don't process on desktop
        }
        
        const menu = document.getElementById('mobile-menu');
        if (!menu) return;
        
        // Only process if menu is actually open and visible
        if (menu.classList.contains('hidden') || !menu.classList.contains('mobile-menu-open')) {
            return; // Menu is closed, allow all clicks
        }
        
        // Check if click is on menu toggle button
        const menuToggle = document.getElementById('mobile-menu-toggle');
        if (menuToggle && (event.target === menuToggle || menuToggle.contains(event.target))) {
            return; // Let toggleMobileMenu handle it
        }
        
        // Check if click is inside menu content
        const menuContent = event.target.closest('.mobile-menu-content');
        if (menuContent) {
            return; // Click is inside menu, don't close
        }
        
        // Check if click is on overlay
        const menuOverlay = event.target.closest('.mobile-menu-overlay');
        if (menuOverlay || event.target === menu) {
            closeMobileMenu();
            return;
        }
        
        // Click is outside menu, close it
        closeMobileMenu();
    };
    
    // Add event listener only once, but make sure it doesn't block clicks when menu is closed
    document.addEventListener('click', clickOutsideHandler, true);
    
    // Close menu on escape key (only on mobile)
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && window.innerWidth < 768) {
            closeMobileMenu();
        }
    });
    
    // Dynamic screen size detection and adaptation
    (function() {
        function adaptNavigation() {
            const width = window.innerWidth;
            const nav = document.querySelector('nav');
            const desktopNav = document.querySelector('.hidden.md\\:flex');
            const mobileMenu = document.getElementById('mobile-menu');
            
            // Add dynamic classes based on screen size
            if (nav) {
                // Remove all screen size classes
                nav.classList.remove('nav-mobile', 'nav-tablet', 'nav-laptop', 'nav-desktop');
                
                // Apply appropriate class based on screen width
                if (width < 768) {
                    nav.classList.add('nav-mobile'); // Mobile: < 768px
                } else if (width >= 768 && width < 1024) {
                    nav.classList.add('nav-tablet'); // Tablet: 768px - 1023px
                } else if (width >= 1024 && width < 1280) {
                    nav.classList.add('nav-laptop'); // Laptop: 1024px - 1279px
                } else {
                    nav.classList.add('nav-desktop'); // Desktop: >= 1280px
                }
            }
            
            // Adjust navigation items spacing dynamically for tablets
            if (width >= 768 && width < 1024) {
                const navLinks = document.querySelectorAll('.nav-link-item');
                navLinks.forEach(link => {
                    if (!link.classList.contains('tablet-optimized')) {
                        link.classList.add('tablet-optimized');
                    }
                });
            } else {
                const navLinks = document.querySelectorAll('.nav-link-item');
                navLinks.forEach(link => {
                    link.classList.remove('tablet-optimized');
                });
            }
        }
        
        // Run on load
        adaptNavigation();
        
        // Run on resize with debounce
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(adaptNavigation, 150);
            
            // Close mobile menu if switching to desktop
            if (window.innerWidth >= 768) {
                closeMobileMenu();
            }
        });
        
        // Run on orientation change (for mobile devices)
        window.addEventListener('orientationchange', function() {
            setTimeout(adaptNavigation, 200);
        });
    })();
</script>

