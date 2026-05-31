<!-- Main Navigation - CloudLab style -->
<nav class="cl-nav">
    <div class="container cl-nav-inner">
        <a href="{{ route('home') }}" class="cl-brand">
            @php
                $logoClPath = public_path('images/logo-cl.png');
                $logoPath = public_path('images/logo.png');
                if (@file_exists($logoClPath) && @is_readable($logoClPath)) {
                    $logoSrc = asset('images/logo-cl.png');
                } elseif (@file_exists($logoPath) && @is_readable($logoPath)) {
                    $logoSrc = asset('images/logo.png');
                } else {
                    $logoSrc = null;
                }
            @endphp
            @if($logoSrc)
                <img src="{{ $logoSrc }}" alt="CloudLab Latam" class="cl-brand-logo">
            @endif
            <span class="cl-brand-text">
                <span class="cl-brand-name">CloudLab Latam</span>
                <span class="cl-brand-tagline">{{ __('messages.nav.tagline') }}</span>
            </span>
        </a>

        <ul class="cl-nav-links d-none d-lg-flex">
            <li><a href="{{ route('home') }}" class="cl-nav-link {{ request()->routeIs('home') ? 'is-active' : '' }}">{{ __('messages.nav.home') }}</a></li>
            <li><a href="{{ route('about.index') }}" class="cl-nav-link {{ request()->routeIs('about.*') ? 'is-active' : '' }}">{{ __('messages.nav.about') }}</a></li>
            <li><a href="{{ route('technologies.index') }}" class="cl-nav-link {{ request()->routeIs('technologies.*') ? 'is-active' : '' }}">{{ __('messages.nav.technologies') }}</a></li>
            <li><a href="{{ route('projects.index') }}" class="cl-nav-link {{ request()->routeIs('projects.*') ? 'is-active' : '' }}">{{ __('messages.nav.projects') }}</a></li>
            <li><a href="{{ route('blogs.index') }}" class="cl-nav-link {{ request()->routeIs('blogs.*') ? 'is-active' : '' }}">{{ __('messages.nav.blog') }}</a></li>
            <li><a href="{{ route('contact.index') }}" class="cl-nav-link {{ request()->routeIs('contact.*') ? 'is-active' : '' }}">{{ __('messages.nav.contact') }}</a></li>
        </ul>

        <div class="cl-nav-actions">
            <div class="cl-lang-toggle d-none d-md-flex">
                <a href="{{ route('language.switch', 'es') }}" class="cl-lang-btn {{ app()->getLocale() === 'es' ? 'is-active' : '' }}">ES</a>
                <a href="{{ route('language.switch', 'en') }}" class="cl-lang-btn {{ app()->getLocale() === 'en' ? 'is-active' : '' }}">EN</a>
            </div>
            <a href="{{ route('contact.index') }}" class="cl-nav-cta d-none d-md-inline-flex">{{ __('messages.cta.free_consultation_short') }}</a>
            <button type="button" class="cl-nav-toggle d-lg-none" onclick="toggleMobileMenu()" aria-label="Menu">
                <i class="bi bi-list"></i>
            </button>
        </div>
    </div>

    <div id="mobile-menu" class="cl-mobile-menu d-lg-none hidden">
        <a href="{{ route('home') }}" class="cl-mobile-link">{{ __('messages.nav.home') }}</a>
        <a href="{{ route('about.index') }}" class="cl-mobile-link">{{ __('messages.nav.about') }}</a>
        <a href="{{ route('technologies.index') }}" class="cl-mobile-link">{{ __('messages.nav.technologies') }}</a>
        <a href="{{ route('projects.index') }}" class="cl-mobile-link">{{ __('messages.nav.projects') }}</a>
        <a href="{{ route('blogs.index') }}" class="cl-mobile-link">{{ __('messages.nav.blog') }}</a>
        <a href="{{ route('contact.index') }}" class="cl-mobile-link">{{ __('messages.nav.contact') }}</a>
        <div class="cl-lang-toggle mt-3">
            <a href="{{ route('language.switch', 'es') }}" class="cl-lang-btn {{ app()->getLocale() === 'es' ? 'is-active' : '' }}">ES</a>
            <a href="{{ route('language.switch', 'en') }}" class="cl-lang-btn {{ app()->getLocale() === 'en' ? 'is-active' : '' }}">EN</a>
        </div>
        <a href="{{ route('contact.index') }}" class="cl-nav-cta mt-3 d-inline-flex">{{ __('messages.cta.free_consultation_short') }}</a>
    </div>
</nav>

<script>
function toggleMobileMenu() {
    const menu = document.getElementById('mobile-menu');
    if (menu) menu.classList.toggle('hidden');
}
</script>
