{{-- Cookie consent banner — necessary vs optional preferences --}}
<div id="cl-cookie-banner" class="cl-cookie-banner" role="dialog" aria-live="polite" aria-labelledby="cl-cookie-title" hidden>
    <div class="cl-cookie-banner__inner">
        <div class="cl-cookie-banner__text">
            <h2 id="cl-cookie-title" class="cl-cookie-banner__title">{{ __('messages.cookies.banner_title') }}</h2>
            <p class="cl-cookie-banner__desc">
                {{ __('messages.cookies.banner_text') }}
                <a href="{{ route('cookies.index') }}">{{ __('messages.cookies.learn_more') }}</a>
            </p>
        </div>
        <div class="cl-cookie-banner__actions">
            <button type="button" class="cl-cookie-btn cl-cookie-btn--ghost" id="cl-cookie-necessary" data-consent="necessary">
                {{ __('messages.cookies.necessary_only') }}
            </button>
            <button type="button" class="cl-cookie-btn cl-cookie-btn--outline" id="cl-cookie-settings-toggle" aria-expanded="false" aria-controls="cl-cookie-settings">
                {{ __('messages.cookies.customize') }}
            </button>
            <button type="button" class="cl-cookie-btn cl-cookie-btn--solid" id="cl-cookie-accept" data-consent="all">
                {{ __('messages.cookies.accept_all') }}
            </button>
        </div>
    </div>

    <div id="cl-cookie-settings" class="cl-cookie-settings" hidden>
        <label class="cl-cookie-option">
            <input type="checkbox" checked disabled>
            <span>
                <strong>{{ __('messages.cookies.cat_necessary') }}</strong>
                <small>{{ __('messages.cookies.cat_necessary_desc') }}</small>
            </span>
        </label>
        <label class="cl-cookie-option">
            <input type="checkbox" id="cl-cookie-analytics" name="analytics">
            <span>
                <strong>{{ __('messages.cookies.cat_analytics') }}</strong>
                <small>{{ __('messages.cookies.cat_analytics_desc') }}</small>
            </span>
        </label>
        <label class="cl-cookie-option">
            <input type="checkbox" id="cl-cookie-marketing" name="marketing">
            <span>
                <strong>{{ __('messages.cookies.cat_marketing') }}</strong>
                <small>{{ __('messages.cookies.cat_marketing_desc') }}</small>
            </span>
        </label>
        <div class="cl-cookie-settings__actions">
            <button type="button" class="cl-cookie-btn cl-cookie-btn--solid" id="cl-cookie-save">
                {{ __('messages.cookies.save_preferences') }}
            </button>
        </div>
    </div>
</div>

<button type="button" id="cl-cookie-reopen" class="cl-cookie-reopen" aria-label="{{ __('messages.cookies.manage') }}" title="{{ __('messages.cookies.manage') }}" hidden>
    <i class="bi bi-shield-lock" aria-hidden="true"></i>
</button>

<script nonce="{{ $cspNonce ?? '' }}">
(function () {
    var STORAGE_KEY = 'cl_cookie_consent';
    var banner = document.getElementById('cl-cookie-banner');
    var settings = document.getElementById('cl-cookie-settings');
    var reopen = document.getElementById('cl-cookie-reopen');
    var analyticsCb = document.getElementById('cl-cookie-analytics');
    var marketingCb = document.getElementById('cl-cookie-marketing');

    function readConsent() {
        try {
            var raw = localStorage.getItem(STORAGE_KEY);
            return raw ? JSON.parse(raw) : null;
        } catch (e) {
            return null;
        }
    }

    function writeConsent(data) {
        var payload = {
            necessary: true,
            analytics: !!data.analytics,
            marketing: !!data.marketing,
            updatedAt: new Date().toISOString()
        };
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(payload));
        } catch (e) {}
        // Mirror in a first-party cookie (1 year) for server-side checks if needed later
        var maxAge = 365 * 24 * 60 * 60;
        document.cookie = 'cl_cookie_consent=' + encodeURIComponent(JSON.stringify({
            analytics: payload.analytics,
            marketing: payload.marketing
        })) + '; path=/; max-age=' + maxAge + '; SameSite=Lax';
        window.CloudLabConsent = payload;
        document.dispatchEvent(new CustomEvent('cl:consent-updated', { detail: payload }));
        return payload;
    }

    function hideBanner() {
        if (banner) {
            banner.hidden = true;
            banner.setAttribute('aria-hidden', 'true');
        }
        if (reopen) reopen.hidden = false;
    }

    function showBanner() {
        if (banner) {
            banner.hidden = false;
            banner.removeAttribute('aria-hidden');
        }
        if (reopen) reopen.hidden = true;
    }

    function applyConsent(consent) {
        window.CloudLabConsent = consent || { necessary: true, analytics: false, marketing: false };
        // Hook for future scripts: only load analytics/marketing when allowed
        if (window.CloudLabConsent.analytics) {
            document.documentElement.setAttribute('data-consent-analytics', '1');
        } else {
            document.documentElement.removeAttribute('data-consent-analytics');
        }
        if (window.CloudLabConsent.marketing) {
            document.documentElement.setAttribute('data-consent-marketing', '1');
        } else {
            document.documentElement.removeAttribute('data-consent-marketing');
        }
    }

    function acceptAll() {
        writeConsent({ analytics: true, marketing: true });
        applyConsent(readConsent());
        hideBanner();
    }

    function necessaryOnly() {
        writeConsent({ analytics: false, marketing: false });
        applyConsent(readConsent());
        hideBanner();
    }

    function savePreferences() {
        writeConsent({
            analytics: analyticsCb ? analyticsCb.checked : false,
            marketing: marketingCb ? marketingCb.checked : false
        });
        applyConsent(readConsent());
        hideBanner();
        if (settings) settings.hidden = true;
    }

    var existing = readConsent();
    if (existing) {
        applyConsent(existing);
        if (analyticsCb) analyticsCb.checked = !!existing.analytics;
        if (marketingCb) marketingCb.checked = !!existing.marketing;
        hideBanner();
    } else {
        applyConsent({ necessary: true, analytics: false, marketing: false });
        showBanner();
    }

    var acceptBtn = document.getElementById('cl-cookie-accept');
    var necessaryBtn = document.getElementById('cl-cookie-necessary');
    var saveBtn = document.getElementById('cl-cookie-save');
    var settingsToggle = document.getElementById('cl-cookie-settings-toggle');

    if (acceptBtn) acceptBtn.addEventListener('click', acceptAll);
    if (necessaryBtn) necessaryBtn.addEventListener('click', necessaryOnly);
    if (saveBtn) saveBtn.addEventListener('click', savePreferences);
    if (settingsToggle && settings) {
        settingsToggle.addEventListener('click', function () {
            var open = settings.hidden;
            settings.hidden = !open;
            settingsToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }
    if (reopen) {
        reopen.addEventListener('click', function () {
            var c = readConsent();
            if (analyticsCb) analyticsCb.checked = !!(c && c.analytics);
            if (marketingCb) marketingCb.checked = !!(c && c.marketing);
            if (settings) settings.hidden = false;
            showBanner();
        });
    }
})();
</script>
