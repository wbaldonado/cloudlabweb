{{-- Cloud Bot chat agent (follows site locale ES/EN) --}}
@php
    $cbAvatarRel = 'images/cloud-bot-avatar.png';
    $cbAvatar256Rel = 'images/cloud-bot-avatar-256.png';
    $cbAvatar128Rel = 'images/cloud-bot-avatar-128.png';
    $cbAvatarSvgRel = 'images/cloud-bot-avatar.svg';

    $cbAvatar = \App\Support\Branding::assetUrl($cbAvatarRel);
    $cbAvatar256 = \App\Support\Branding::assetUrl($cbAvatar256Rel);
    $cbAvatar128 = \App\Support\Branding::assetUrl($cbAvatar128Rel);
    $cbAvatarSvg = \App\Support\Branding::assetUrl($cbAvatarSvgRel);

    $cbAvatarAlt = \App\Support\Branding::alternateAssetUrl($cbAvatarRel);
    $cbAvatar256Alt = \App\Support\Branding::alternateAssetUrl($cbAvatar256Rel);
    $cbAvatar128Alt = \App\Support\Branding::alternateAssetUrl($cbAvatar128Rel);
    $cbAvatarSvgAlt = \App\Support\Branding::alternateAssetUrl($cbAvatarSvgRel);

    $cbAvatarVer = is_file(public_path($cbAvatar256Rel))
        ? filemtime(public_path($cbAvatar256Rel))
        : (is_file(public_path($cbAvatarRel)) ? filemtime(public_path($cbAvatarRel)) : time());

    $cbWaNumber = preg_replace('/\D+/', '', (string) config('whatsapp.number', '573208156396'));
    $cbLocale = in_array(app()->getLocale(), ['en', 'es'], true) ? app()->getLocale() : 'es';

    $cbBuildI18n = static function (string $locale): array {
        $t = static function (string $key) use ($locale) {
            return trans('messages.' . $key, [], $locale);
        };

        return [
            'name' => $t('cloudbot.name'),
            'online' => $t('cloudbot.online'),
            'close' => $t('cloudbot.close'),
            'send' => $t('cloudbot.send'),
            'inputLabel' => $t('cloudbot.input_label'),
            'placeholder' => $t('cloudbot.placeholder'),
            'typing' => $t('cloudbot.typing'),
            'welcome' => $t('cloudbot.welcome'),
            'ask' => $t('cloudbot.ask'),
            'optAutomate' => $t('cloudbot.opt_automate'),
            'optCloud' => $t('cloudbot.opt_cloud'),
            'optAnalytics' => $t('cloudbot.opt_analytics'),
            'optHuman' => $t('cloudbot.opt_human'),
            'replyAutomate' => $t('cloudbot.reply_automate'),
            'replyCloud' => $t('cloudbot.reply_cloud'),
            'replyAnalytics' => $t('cloudbot.reply_analytics'),
            'replyHuman' => $t('cloudbot.reply_human'),
            'replyDefault' => $t('cloudbot.reply_default'),
            'ctaWhatsapp' => $t('cloudbot.cta_whatsapp'),
            'ctaContact' => $t('cloudbot.cta_contact'),
            'moreHelp' => $t('cloudbot.more_help'),
            'whatsappPrefill' => $t('cloudbot.whatsapp_prefill'),
            'formIntro' => $t('cloudbot.form_intro'),
            'formSubject' => $t('cloudbot.form_subject_default'),
            'formSending' => $t('cloudbot.form_sending'),
            'formRequired' => $t('cloudbot.form_required'),
            'formSent' => $t('cloudbot.form_sent'),
            'contactName' => $t('contact.name'),
            'contactEmail' => $t('contact.email'),
            'contactCompany' => $t('contact.company'),
            'contactSubject' => $t('contact.subject'),
            'contactMessage' => $t('contact.message'),
            'contactSend' => $t('contact.send'),
            'contactNamePh' => $t('contact.name_placeholder'),
            'contactEmailPh' => $t('contact.email_placeholder'),
            'contactCompanyPh' => $t('contact.company_placeholder'),
            'contactMessagePh' => $t('contact.message_placeholder'),
            'contactError' => $t('contact.error'),
        ];
    };

    $cbI18nPacks = [
        'es' => $cbBuildI18n('es'),
        'en' => $cbBuildI18n('en'),
    ];
    $cbWaUrl = 'https://wa.me/' . $cbWaNumber . '?text=' . rawurlencode($cbI18nPacks[$cbLocale]['whatsappPrefill']);

    $cbCssPath = 'css/cloud-bot.css';
    $cbCssVer = is_file(public_path($cbCssPath)) ? filemtime(public_path($cbCssPath)) : time();
    $cbCssUrl = \App\Support\Branding::assetUrl($cbCssPath) . '?v=' . $cbCssVer;
    $cbCssAlt = \App\Support\Branding::alternateAssetUrl($cbCssPath) . '?v=' . $cbCssVer;
@endphp

<link rel="stylesheet" href="{{ $cbCssUrl }}" data-cb-css-alt="{{ $cbCssAlt }}" onerror="this.onerror=null;this.href=this.getAttribute('data-cb-css-alt');">

<div id="cloud-bot" class="cloud-bot" data-open-delay="1800"
     data-locale="{{ $cbLocale }}"
     data-wa-number="{{ $cbWaNumber }}"
     data-wa-url="{{ $cbWaUrl }}"
     data-contact-store="{{ route('contact.store') }}"
     data-avatar="{{ $cbAvatar256 }}?v={{ $cbAvatarVer }}"
     data-avatar-fallback="{{ $cbAvatar256Alt }}?v={{ $cbAvatarVer }}"
     data-avatar-svg="{{ $cbAvatarSvg }}?v={{ $cbAvatarVer }}"
     data-avatar-svg-fallback="{{ $cbAvatarSvgAlt }}?v={{ $cbAvatarVer }}">
    <button type="button" id="cloud-bot-launcher" class="cloud-bot__launcher" aria-expanded="false" aria-controls="cloud-bot-panel" aria-label="{{ $cbI18nPacks[$cbLocale]['name'] }}">
        <img
            src="{{ $cbAvatar256 }}?v={{ $cbAvatarVer }}"
            srcset="{{ $cbAvatar128 }}?v={{ $cbAvatarVer }} 128w, {{ $cbAvatar256 }}?v={{ $cbAvatarVer }} 256w, {{ $cbAvatar }}?v={{ $cbAvatarVer }} 512w"
            sizes="72px"
            alt="{{ $cbI18nPacks[$cbLocale]['name'] }}"
            class="cloud-bot__launcher-avatar"
            width="72"
            height="72"
            decoding="async"
            data-fallback="{{ $cbAvatar256Alt }}?v={{ $cbAvatarVer }}"
            data-fallback-svg="{{ $cbAvatarSvg }}?v={{ $cbAvatarVer }}"
            onerror="window.clCloudBotImgFallback && window.clCloudBotImgFallback(this)"
        >
        <span class="cloud-bot__launcher-pulse" aria-hidden="true"></span>
        <span class="cloud-bot__badge" id="cloud-bot-badge" hidden>1</span>
    </button>

    <section id="cloud-bot-panel" class="cloud-bot__panel" role="dialog" aria-labelledby="cloud-bot-title" hidden>
        <header class="cloud-bot__header">
            <div class="cloud-bot__identity">
                <img
                    src="{{ $cbAvatar256 }}?v={{ $cbAvatarVer }}"
                    srcset="{{ $cbAvatar128 }}?v={{ $cbAvatarVer }} 128w, {{ $cbAvatar256 }}?v={{ $cbAvatarVer }} 256w"
                    sizes="44px"
                    alt=""
                    class="cloud-bot__avatar"
                    width="44"
                    height="44"
                    decoding="async"
                    data-fallback="{{ $cbAvatar256Alt }}?v={{ $cbAvatarVer }}"
                    data-fallback-svg="{{ $cbAvatarSvg }}?v={{ $cbAvatarVer }}"
                    onerror="window.clCloudBotImgFallback && window.clCloudBotImgFallback(this)"
                >
                <div>
                    <h2 id="cloud-bot-title" class="cloud-bot__name">{{ $cbI18nPacks[$cbLocale]['name'] }}</h2>
                    <p class="cloud-bot__status"><span class="cloud-bot__status-dot" aria-hidden="true"></span><span id="cloud-bot-status-text">{{ $cbI18nPacks[$cbLocale]['online'] }}</span></p>
                </div>
            </div>
            <button type="button" class="cloud-bot__close" id="cloud-bot-close" aria-label="{{ $cbI18nPacks[$cbLocale]['close'] }}">
                <i class="bi bi-x-lg" aria-hidden="true"></i>
            </button>
        </header>

        <div class="cloud-bot__messages" id="cloud-bot-messages" aria-live="polite"></div>

        <div class="cloud-bot__quick" id="cloud-bot-quick" hidden></div>

        <form class="cloud-bot__composer" id="cloud-bot-form" autocomplete="off">
            <label class="visually-hidden" id="cloud-bot-input-label" for="cloud-bot-input">{{ $cbI18nPacks[$cbLocale]['inputLabel'] }}</label>
            <input type="text" id="cloud-bot-input" class="cloud-bot__input" maxlength="400"
                   placeholder="{{ $cbI18nPacks[$cbLocale]['placeholder'] }}">
            <button type="submit" class="cloud-bot__send" id="cloud-bot-send" aria-label="{{ $cbI18nPacks[$cbLocale]['send'] }}">
                <i class="bi bi-send-fill" aria-hidden="true"></i>
            </button>
        </form>
    </section>
</div>

<script nonce="{{ $cspNonce ?? '' }}">
window.clCloudBotImgFallback = function (img) {
    if (!img || img.dataset.cbFail === '1') return;
    var step = parseInt(img.dataset.cbStep || '0', 10);
    if (step === 0 && img.dataset.fallback) {
        img.dataset.cbStep = '1';
        img.removeAttribute('srcset');
        img.src = img.dataset.fallback;
        return;
    }
    if (step <= 1 && img.dataset.fallbackSvg) {
        img.dataset.cbStep = '2';
        img.removeAttribute('srcset');
        img.src = img.dataset.fallbackSvg;
        return;
    }
    img.dataset.cbFail = '1';
};

(function () {
    var root = document.getElementById('cloud-bot');
    if (!root) return;

    var STORAGE_KEY = 'cl_cloud_bot_seen';
    var LOCALE_KEY = 'cl_cloud_bot_locale';
    var panel = document.getElementById('cloud-bot-panel');
    var launcher = document.getElementById('cloud-bot-launcher');
    var closeBtn = document.getElementById('cloud-bot-close');
    var messages = document.getElementById('cloud-bot-messages');
    var quick = document.getElementById('cloud-bot-quick');
    var form = document.getElementById('cloud-bot-form');
    var input = document.getElementById('cloud-bot-input');
    var badge = document.getElementById('cloud-bot-badge');
    var statusText = document.getElementById('cloud-bot-status-text');
    var inputLabel = document.getElementById('cloud-bot-input-label');
    var sendBtn = document.getElementById('cloud-bot-send');
    var titleEl = document.getElementById('cloud-bot-title');
    var packs = @json($cbI18nPacks);
    var locale = root.getAttribute('data-locale') || 'es';
    if (!packs[locale]) locale = 'es';
    var i18n = packs[locale];
    var waNumber = root.getAttribute('data-wa-number') || '';
    var waUrl = 'https://wa.me/' + waNumber + '?text=' + encodeURIComponent(i18n.whatsappPrefill || '');
    var contactStore = root.getAttribute('data-contact-store') || '/contact';
    var openDelay = parseInt(root.getAttribute('data-open-delay') || '1800', 10);
    var avatarUrl = root.getAttribute('data-avatar') || @json($cbAvatar256 . '?v=' . $cbAvatarVer);
    var avatarFallback = root.getAttribute('data-avatar-fallback') || '';
    var avatarSvg = root.getAttribute('data-avatar-svg') || '';
    var started = false;
    var csrfToken = document.querySelector('meta[name="csrf-token"]');
    csrfToken = csrfToken ? csrfToken.getAttribute('content') : '';

    var topicLabels = {};

    function makeAvatarImg() {
        var img = document.createElement('img');
        img.src = avatarUrl;
        img.alt = '';
        img.className = 'cloud-bot__msg-avatar';
        img.width = 32;
        img.height = 32;
        if (avatarFallback) img.setAttribute('data-fallback', avatarFallback);
        if (avatarSvg) img.setAttribute('data-fallback-svg', avatarSvg);
        img.onerror = function () { window.clCloudBotImgFallback(img); };
        return img;
    }

    function syncTopicLabels() {
        topicLabels = {
            automate: i18n.optAutomate,
            cloud: i18n.optCloud,
            analytics: i18n.optAnalytics,
            human: i18n.optHuman
        };
    }

    function applyChrome() {
        if (titleEl) titleEl.textContent = i18n.name;
        if (statusText) statusText.textContent = i18n.online;
        if (input) input.placeholder = i18n.placeholder;
        if (inputLabel) inputLabel.textContent = i18n.inputLabel;
        if (closeBtn) closeBtn.setAttribute('aria-label', i18n.close);
        if (sendBtn) sendBtn.setAttribute('aria-label', i18n.send);
        if (launcher) launcher.setAttribute('aria-label', i18n.name);
        var launcherImg = launcher ? launcher.querySelector('img') : null;
        if (launcherImg) launcherImg.alt = i18n.name;
        waUrl = 'https://wa.me/' + waNumber + '?text=' + encodeURIComponent(i18n.whatsappPrefill || '');
        root.setAttribute('data-wa-url', waUrl);
        root.setAttribute('lang', locale);
        syncTopicLabels();
    }

    // If site language changed since last visit, restart welcome in the new language
    var localeChanged = false;
    try {
        var prevLocale = localStorage.getItem(LOCALE_KEY);
        if (prevLocale && prevLocale !== locale) {
            localeChanged = true;
            localStorage.removeItem(STORAGE_KEY);
        }
        localStorage.setItem(LOCALE_KEY, locale);
    } catch (e) {}

    applyChrome();

    function openPanel(fromUser) {
        panel.hidden = false;
        launcher.setAttribute('aria-expanded', 'true');
        root.classList.add('is-open');
        if (badge) badge.hidden = true;
        if (!started) {
            started = true;
            runWelcome();
        }
        if (fromUser) {
            try { localStorage.setItem(STORAGE_KEY, '1'); } catch (e) {}
        }
        setTimeout(function () { input && input.focus(); }, 250);
    }

    function closePanel() {
        panel.hidden = true;
        launcher.setAttribute('aria-expanded', 'false');
        root.classList.remove('is-open');
        try { localStorage.setItem(STORAGE_KEY, '1'); } catch (e) {}
    }

    function scrollToBottom() {
        messages.scrollTop = messages.scrollHeight;
    }

    function addBubble(text, who) {
        var row = document.createElement('div');
        row.className = 'cloud-bot__row cloud-bot__row--' + who;
        if (who === 'bot') {
            row.appendChild(makeAvatarImg());
        }
        var bubble = document.createElement('div');
        bubble.className = 'cloud-bot__bubble cloud-bot__bubble--' + who;
        bubble.textContent = text;
        row.appendChild(bubble);
        messages.appendChild(row);
        scrollToBottom();
    }

    function addActions(actions) {
        var wrap = document.createElement('div');
        wrap.className = 'cloud-bot__actions';
        actions.forEach(function (action) {
            var a = document.createElement(action.href ? 'a' : 'button');
            a.className = 'cloud-bot__action' + (action.primary ? ' is-primary' : '');
            a.textContent = action.label;
            if (action.href) {
                a.href = action.href;
                a.target = action.external ? '_blank' : '_self';
                if (action.external) a.rel = 'noopener noreferrer';
            } else {
                a.type = 'button';
                a.addEventListener('click', action.onClick);
            }
            wrap.appendChild(a);
        });
        messages.appendChild(wrap);
        scrollToBottom();
    }

    function setQuick(options) {
        quick.innerHTML = '';
        if (!options || !options.length) {
            quick.hidden = true;
            return;
        }
        options.forEach(function (opt) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'cloud-bot__chip';
            btn.textContent = opt.label;
            btn.addEventListener('click', function () {
                handleUserChoice(opt.id, opt.label);
            });
            quick.appendChild(btn);
        });
        quick.hidden = false;
    }

    function defaultQuick() {
        setQuick([
            { id: 'automate', label: i18n.optAutomate },
            { id: 'cloud', label: i18n.optCloud },
            { id: 'analytics', label: i18n.optAnalytics },
            { id: 'human', label: i18n.optHuman }
        ]);
    }

    function showTyping(cb) {
        var row = document.createElement('div');
        row.className = 'cloud-bot__row cloud-bot__row--bot';
        row.id = 'cloud-bot-typing';
        var img = makeAvatarImg();
        var bubble = document.createElement('div');
        bubble.className = 'cloud-bot__bubble cloud-bot__bubble--bot cloud-bot__typing';
        bubble.setAttribute('aria-label', i18n.typing);
        bubble.innerHTML = '<span></span><span></span><span></span>';
        row.appendChild(img);
        row.appendChild(bubble);
        messages.appendChild(row);
        scrollToBottom();
        setTimeout(function () {
            var el = document.getElementById('cloud-bot-typing');
            if (el) el.remove();
            cb();
        }, 700);
    }

    function runWelcome() {
        showTyping(function () {
            addBubble(i18n.welcome, 'bot');
            showTyping(function () {
                addBubble(i18n.ask, 'bot');
                defaultQuick();
            });
        });
    }

    function field(label, name, type, placeholder, required, value) {
        var wrap = document.createElement('div');
        wrap.className = 'cloud-bot-contact__field';
        var lab = document.createElement('label');
        lab.setAttribute('for', 'cb-' + name);
        lab.textContent = label + (required ? ' *' : '');
        var inputEl;
        if (type === 'textarea') {
            inputEl = document.createElement('textarea');
            inputEl.rows = 3;
        } else {
            inputEl = document.createElement('input');
            inputEl.type = type || 'text';
        }
        inputEl.id = 'cb-' + name;
        inputEl.name = name;
        inputEl.className = 'cloud-bot-contact__control';
        inputEl.placeholder = placeholder || '';
        if (required) inputEl.required = true;
        if (value) inputEl.value = value;
        wrap.appendChild(lab);
        wrap.appendChild(inputEl);
        return wrap;
    }

    function showContactForm(topicId) {
        var existing = messages.querySelector('.cloud-bot-contact');
        if (existing) existing.remove();

        var card = document.createElement('div');
        card.className = 'cloud-bot-contact';

        var contactForm = document.createElement('form');
        contactForm.className = 'cloud-bot-contact__form';
        contactForm.setAttribute('novalidate', 'novalidate');
        contactForm.setAttribute('lang', locale);

        var honeypot = document.createElement('input');
        honeypot.type = 'text';
        honeypot.name = 'website';
        honeypot.tabIndex = -1;
        honeypot.autocomplete = 'off';
        honeypot.value = '';
        honeypot.className = 'cloud-bot-contact__hp';
        honeypot.setAttribute('aria-hidden', 'true');
        contactForm.appendChild(honeypot);

        contactForm.appendChild(field(i18n.contactName, 'name', 'text', i18n.contactNamePh, true));
        contactForm.appendChild(field(i18n.contactEmail, 'email', 'email', i18n.contactEmailPh, true));
        contactForm.appendChild(field(i18n.contactCompany, 'company', 'text', i18n.contactCompanyPh, false));
        contactForm.appendChild(field(i18n.contactSubject, 'subject', 'text', '', false, i18n.formSubject));
        contactForm.appendChild(field(i18n.contactMessage, 'message', 'textarea', i18n.contactMessagePh, true,
            topicLabels[topicId] ? topicLabels[topicId] : ''));

        var status = document.createElement('p');
        status.className = 'cloud-bot-contact__status';
        status.hidden = true;

        var actions = document.createElement('div');
        actions.className = 'cloud-bot-contact__actions';

        var submitBtn = document.createElement('button');
        submitBtn.type = 'submit';
        submitBtn.className = 'cloud-bot__action is-primary';
        submitBtn.textContent = i18n.contactSend;

        var waLink = document.createElement('a');
        waLink.href = waUrl;
        waLink.target = '_blank';
        waLink.rel = 'noopener noreferrer';
        waLink.className = 'cloud-bot__action';
        waLink.textContent = i18n.ctaWhatsapp;

        actions.appendChild(submitBtn);
        actions.appendChild(waLink);
        contactForm.appendChild(status);
        contactForm.appendChild(actions);
        card.appendChild(contactForm);
        messages.appendChild(card);
        scrollToBottom();

        var nameInput = contactForm.querySelector('[name="name"]');
        if (nameInput) setTimeout(function () { nameInput.focus(); }, 100);

        contactForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var name = (contactForm.querySelector('[name="name"]').value || '').trim();
            var email = (contactForm.querySelector('[name="email"]').value || '').trim();
            var message = (contactForm.querySelector('[name="message"]').value || '').trim();
            if (!name || !email || !message) {
                status.hidden = false;
                status.className = 'cloud-bot-contact__status is-error';
                status.textContent = i18n.formRequired;
                return;
            }

            submitBtn.disabled = true;
            submitBtn.textContent = i18n.formSending;
            status.hidden = true;

            var body = new FormData(contactForm);
            fetch(contactStore, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: body,
                credentials: 'same-origin'
            }).then(function (res) {
                return res.json().then(function (data) {
                    return { ok: res.ok, status: res.status, data: data };
                }).catch(function () {
                    return { ok: res.ok, status: res.status, data: {} };
                });
            }).then(function (result) {
                if (result.ok && result.data && result.data.ok !== false) {
                    card.remove();
                    addBubble(result.data.message || i18n.formSent, 'bot');
                    showTyping(function () {
                        addBubble(i18n.moreHelp, 'bot');
                        defaultQuick();
                    });
                    return;
                }

                var errMsg = i18n.contactError;
                if (result.data && result.data.message) errMsg = result.data.message;
                if (result.data && result.data.errors) {
                    var first = Object.keys(result.data.errors)[0];
                    if (first && result.data.errors[first][0]) errMsg = result.data.errors[first][0];
                }
                status.hidden = false;
                status.className = 'cloud-bot-contact__status is-error';
                status.textContent = errMsg;
                submitBtn.disabled = false;
                submitBtn.textContent = i18n.contactSend;
            }).catch(function () {
                status.hidden = false;
                status.className = 'cloud-bot-contact__status is-error';
                status.textContent = i18n.contactError;
                submitBtn.disabled = false;
                submitBtn.textContent = i18n.contactSend;
            });
        });
    }

    function botReplyTopic(id) {
        var map = {
            automate: i18n.replyAutomate,
            cloud: i18n.replyCloud,
            analytics: i18n.replyAnalytics,
            human: i18n.replyHuman,
            default: i18n.replyDefault
        };
        var text = map[id] || i18n.replyDefault;

        showTyping(function () {
            addBubble(text, 'bot');

            if (id === 'human') {
                showTyping(function () {
                    addBubble(i18n.formIntro, 'bot');
                    showContactForm('human');
                });
                return;
            }

            addActions([
                { label: i18n.ctaWhatsapp, href: waUrl, external: true, primary: true },
                {
                    label: i18n.ctaContact,
                    primary: false,
                    onClick: function () {
                        showTyping(function () {
                            addBubble(i18n.formIntro, 'bot');
                            showContactForm(id);
                        });
                    }
                }
            ]);
            showTyping(function () {
                addBubble(i18n.moreHelp, 'bot');
                defaultQuick();
            });
        });
    }

    function handleUserChoice(id, label) {
        addBubble(label, 'user');
        setQuick([]);
        botReplyTopic(id);
    }

    function handleFreeText(text) {
        addBubble(text, 'user');
        setQuick([]);
        var lower = text.toLowerCase();
        var id = 'default';
        if (/automat|rpa|proceso|process|workflow|flujo/.test(lower)) id = 'automate';
        else if (/nube|cloud|migraci|fabric|infra|moderniz/.test(lower)) id = 'cloud';
        else if (/analy|dato|data|bi|ai|ia|python|inteligencia/.test(lower)) id = 'analytics';
        else if (/humano|asesor|persona|whatsapp|llamar|contact|hablar|advisor|talk|human/.test(lower)) id = 'human';
        botReplyTopic(id);
    }

    launcher.addEventListener('click', function () {
        if (panel.hidden) openPanel(true);
        else closePanel();
    });
    closeBtn.addEventListener('click', closePanel);

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var text = (input.value || '').trim();
        if (!text) return;
        input.value = '';
        if (!started) started = true;
        handleFreeText(text);
    });

    var seen = false;
    try { seen = localStorage.getItem(STORAGE_KEY) === '1'; } catch (e) {}
    if (!seen || localeChanged) {
        if (badge) badge.hidden = false;
        setTimeout(function () {
            openPanel(false);
        }, openDelay);
    }
})();
</script>
