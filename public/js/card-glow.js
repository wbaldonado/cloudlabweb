/**
 * Interactive corner glow for CloudLab cards.
 * Tracks pointer proximity to each corner and updates CSS variables.
 */
(function () {
    var SELECTOR = '.cl-hero-card, .cl-svc-card, .cl-card, .cl-about-box, .mission-card, .value-card';

    function cornerIntensity(x, y, cx, cy, reach) {
        var d = Math.hypot(x - cx, y - cy);
        return Math.max(0, 1 - d / reach);
    }

    function setGlow(card, clientX, clientY) {
        var rect = card.getBoundingClientRect();
        var x = clientX - rect.left;
        var y = clientY - rect.top;
        var w = rect.width;
        var h = rect.height;
        var reach = Math.min(w, h) * 0.65;

        card.style.setProperty('--glow-tl', cornerIntensity(x, y, 0, 0, reach).toFixed(3));
        card.style.setProperty('--glow-tr', cornerIntensity(x, y, w, 0, reach).toFixed(3));
        card.style.setProperty('--glow-br', cornerIntensity(x, y, w, h, reach).toFixed(3));
        card.style.setProperty('--glow-bl', cornerIntensity(x, y, 0, h, reach).toFixed(3));
        card.classList.add('is-glow-active');
    }

    function clearGlow(card) {
        card.classList.remove('is-glow-active');
        card.style.setProperty('--glow-tl', '0');
        card.style.setProperty('--glow-tr', '0');
        card.style.setProperty('--glow-br', '0');
        card.style.setProperty('--glow-bl', '0');
    }

    function bindCard(card) {
        card.classList.add('cl-glow-card');

        card.addEventListener('mousemove', function (e) {
            setGlow(card, e.clientX, e.clientY);
        });

        card.addEventListener('mouseleave', function () {
            clearGlow(card);
        });

        card.addEventListener('touchstart', function (e) {
            if (!e.touches.length) return;
            setGlow(card, e.touches[0].clientX, e.touches[0].clientY);
        }, { passive: true });

        card.addEventListener('touchmove', function (e) {
            if (!e.touches.length) return;
            setGlow(card, e.touches[0].clientX, e.touches[0].clientY);
        }, { passive: true });

        card.addEventListener('touchend', function () {
            clearGlow(card);
        });
    }

    function init() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }
        document.querySelectorAll(SELECTOR).forEach(bindCard);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
