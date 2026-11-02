<!-- Main Navigation - CloudLab style -->
<nav class="cl-nav">
    <div class="container cl-nav-inner">
        <a href="{{ route('home') }}" class="cl-brand">
            @include('components.brand-logo', ['width' => 64, 'height' => 56])
            <span class="cl-brand-text">
                <span class="cl-brand-name">CloudLab Latam</span>
                <span class="cl-brand-tagline">{{ __('messages.nav.tagline') }}</span>
            </span>
        </a>

        <ul class="cl-nav-links d-none d-lg-flex">
            <li><a href="{{ route('home') }}" class="cl-nav-link {{ request()->routeIs('home') ? 'is-active' : '' }}">{{ __('messages.nav.home') }}</a></li>
            <li><a href="{{ route('services.index') }}" class="cl-nav-link {{ request()->routeIs('services.*') ? 'is-active' : '' }}">{{ __('messages.nav.services') }}</a></li>
            <li><a href="{{ route('about.index') }}" class="cl-nav-link {{ request()->routeIs('about.*') ? 'is-active' : '' }}">{{ __('messages.nav.about') }}</a></li>
            <li><a href="{{ route('public.technologies.index') }}" class="cl-nav-link {{ request()->routeIs('public.technologies.index') ? 'is-active' : '' }}">{{ __('messages.nav.technologies') }}</a></li>
            <li><a href="{{ route('projects.index') }}" class="cl-nav-link {{ request()->routeIs('projects.*') ? 'is-active' : '' }}">{{ __('messages.nav.projects') }}</a></li>
            <li><a href="{{ route('blogs.index') }}" class="cl-nav-link {{ request()->routeIs('blogs.*') ? 'is-active' : '' }}">{{ __('messages.nav.blog') }}</a></li>
        </ul>

        <div class="cl-nav-actions">
            <div class="cl-lang-toggle d-none d-md-flex">
                <a href="{{ \App\Support\LocalizedUrl::swap('es') }}" class="cl-lang-btn {{ app()->getLocale() === 'es' ? 'is-active' : '' }}">ES</a>
                <a href="{{ \App\Support\LocalizedUrl::swap('en') }}" class="cl-lang-btn {{ app()->getLocale() === 'en' ? 'is-active' : '' }}">EN</a>
            </div>
            <a href="{{ route('home') }}#contacto" class="cl-nav-cta d-none d-md-inline-flex">{{ __('messages.cta.free_consultation_short') }}</a>
            <button type="button" class="cl-nav-toggle d-lg-none" id="clNavToggle" aria-label="Menu" aria-controls="mobile-menu" aria-expanded="false">
                <i class="bi bi-list" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    <div id="mobile-menu" class="cl-mobile-menu d-lg-none hidden">
        <a href="{{ route('home') }}" class="cl-mobile-link">{{ __('messages.nav.home') }}</a>
        <a href="{{ route('services.index') }}" class="cl-mobile-link">{{ __('messages.nav.services') }}</a>
        <a href="{{ route('about.index') }}" class="cl-mobile-link">{{ __('messages.nav.about') }}</a>
        <a href="{{ route('public.technologies.index') }}" class="cl-mobile-link">{{ __('messages.nav.technologies') }}</a>
        <a href="{{ route('projects.index') }}" class="cl-mobile-link">{{ __('messages.nav.projects') }}</a>
        <a href="{{ route('blogs.index') }}" class="cl-mobile-link">{{ __('messages.nav.blog') }}</a>
        <div class="cl-lang-toggle mt-3">
            <a href="{{ \App\Support\LocalizedUrl::swap('es') }}" class="cl-lang-btn {{ app()->getLocale() === 'es' ? 'is-active' : '' }}">ES</a>
            <a href="{{ \App\Support\LocalizedUrl::swap('en') }}" class="cl-lang-btn {{ app()->getLocale() === 'en' ? 'is-active' : '' }}">EN</a>
        </div>
        <a href="{{ route('home') }}#contacto" class="cl-nav-cta mt-3 d-inline-flex">{{ __('messages.cta.free_consultation_short') }}</a>
    </div>
</nav>

<script nonce="{{ $cspNonce ?? '' }}">
function clLoadLogo(img) {
    const step = parseInt(img.dataset.logoStep || '0', 10);
    const urls = [
        img.dataset.logoFallback,
        img.dataset.logoAlt,
        img.dataset.logoAltFallback,
    ].filter(Boolean);

    if (step < urls.length) {
        img.dataset.logoStep = String(step + 1);
        img.src = urls[step];
        return;
    }

    img.classList.add('is-broken');
    img.style.display = 'none';
}

function setMobileMenuOpen(open) {
    const menu = document.getElementById('mobile-menu');
    const toggle = document.getElementById('clNavToggle');
    if (!menu) return;

    menu.classList.toggle('hidden', !open);
    menu.style.removeProperty('display');
    menu.style.removeProperty('pointer-events');
    menu.setAttribute('aria-hidden', open ? 'false' : 'true');

    if (toggle) {
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        const icon = toggle.querySelector('i');
        if (icon) {
            icon.classList.toggle('bi-list', !open);
            icon.classList.toggle('bi-x-lg', open);
        }
    }
}

function toggleMobileMenu() {
    const menu = document.getElementById('mobile-menu');
    if (!menu) return;
    setMobileMenuOpen(menu.classList.contains('hidden'));
}

(function () {
    const toggle = document.getElementById('clNavToggle');
    if (toggle) {
        toggle.addEventListener('click', function (e) {
            e.preventDefault();
            toggleMobileMenu();
        });
    }

    document.querySelectorAll('#mobile-menu .cl-mobile-link').forEach(function (link) {
        link.addEventListener('click', function () {
            setMobileMenuOpen(false);
        });
    });
})();
</script>
