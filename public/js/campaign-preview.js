(function (window, document) {
    'use strict';

    if (window.CampaignPreview) {
        return;
    }

    function wrap(html) {
        html = String(html || '');
        if (!html.trim()) {
            return '<!DOCTYPE html><html><body style="font-family:Arial,Helvetica,sans-serif;color:#888;padding:24px">Sin contenido</body></html>';
        }
        if (/<\s*html[\s>]/i.test(html)) {
            return html;
        }
        return '<!DOCTYPE html><html><head><meta charset="utf-8">'
            + '<meta name="viewport" content="width=device-width, initial-scale=1">'
            + '<base target="_blank" rel="noopener">'
            + '<style>body{margin:0;padding:16px;font-family:Arial,Helvetica,sans-serif;line-height:1.5;color:#222;word-wrap:break-word}img{max-width:100%;height:auto}table{max-width:100%}video,iframe{max-width:100%}</style>'
            + '</head><body>' + html + '</body></html>';
    }

    function render(iframe, html) {
        if (!iframe) return;
        var doc = wrap(html);
        try {
            iframe.srcdoc = doc;
        } catch (e) {
            try {
                var inner = iframe.contentDocument || (iframe.contentWindow && iframe.contentWindow.document);
                if (!inner) return;
                inner.open();
                inner.write(doc);
                inner.close();
            } catch (e2) {}
        }
    }

    function bindSizeToggles() {
        document.querySelectorAll('[data-preview-size]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var targetId = btn.getAttribute('data-preview-target');
                var iframe = targetId ? document.getElementById(targetId) : null;
                if (!iframe) return;
                var group = btn.parentElement;
                if (group) {
                    group.querySelectorAll('[data-preview-size]').forEach(function (other) {
                        other.classList.toggle('active', other === btn);
                    });
                }
                iframe.classList.toggle('is-mobile', btn.getAttribute('data-preview-size') === 'mobile');
            });
        });
    }

    function hydrateFromJson() {
        document.querySelectorAll('script[data-campaign-preview]').forEach(function (el) {
            var iframe = document.getElementById(el.getAttribute('data-campaign-preview'));
            if (!iframe) return;
            try {
                render(iframe, JSON.parse(el.textContent || '""'));
            } catch (e) {
                render(iframe, '');
            }
        });
    }

    function init() {
        bindSizeToggles();
        hydrateFromJson();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    window.CampaignPreview = { wrap: wrap, render: render, init: init };
})(window, document);
