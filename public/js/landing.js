function initAutomationFlow() {
    var root = document.querySelector('[data-flow]');
    if (!root) {
        return;
    }

    var sources = Array.prototype.slice.call(root.querySelectorAll('[data-source]'));
    var lines = Array.prototype.slice.call(root.querySelectorAll('[data-line]'));
    var steps = Array.prototype.slice.call(root.querySelectorAll('[data-step]'));
    var eventG = root.querySelector('[data-event-banner]');
    var eventText = eventG.querySelector('text');
    var badge = root.querySelector('[data-badge]');
    var bump = root.querySelector('[data-bump]');
    var countEl = root.querySelector('[data-count]');
    var tokenLayer = root.querySelector('[data-tokens]');
    var routes = {};
    lines.forEach(function (line) {
        routes[line.getAttribute('data-line')] = line.getAttribute('d');
    });

    var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var paso = reduce ? 5 : 0;
    var ciclo = 0;
    var first = true;
    var dur = [1000, 1300, 800, 800, 1100, 2800];
    var svgNs = 'http://www.w3.org/2000/svg';

    function fuente() {
        return sources[ciclo % 3];
    }

    function addToken(kind, pathData) {
        var g = document.createElementNS(svgNs, 'g');
        g.setAttribute('class', 'fa-token ' + kind);
        g.style.offsetPath = 'path("' + pathData + '")';
        var rect = document.createElementNS(svgNs, 'rect');
        rect.setAttribute('x', '-8');
        rect.setAttribute('y', '-10');
        rect.setAttribute('width', '16');
        rect.setAttribute('height', '20');
        rect.setAttribute('rx', '3');
        var path = document.createElementNS(svgNs, 'path');
        path.setAttribute('d', kind === 'fa-crudo' ? 'M-4 -4h8M-4 0h5M-4 4h7' : 'M-4 0l3 3 5-6');
        g.appendChild(rect);
        g.appendChild(path);
        tokenLayer.appendChild(g);
    }

    function paint() {
        var current = fuente();
        var sourceId = Number(current.getAttribute('data-source'));
        root.classList.toggle('fa-trabajando', paso >= 2 && paso <= 5);
        sources.forEach(function (source) {
            var id = Number(source.getAttribute('data-source'));
            var activeWindow = paso >= 1 && paso <= 4;
            source.classList.toggle('fa-dim', activeWindow && id !== sourceId);
            source.classList.toggle('fa-sel', activeWindow && id === sourceId);
        });
        lines.forEach(function (line) {
            var id = Number(line.getAttribute('data-line'));
            line.classList.toggle('fa-viva', (paso === 1 && id === sourceId) || (paso >= 4 && id === 4));
        });
        if (paso === 1) {
            eventG.classList.add('fa-ver');
            eventG.setAttribute('transform', 'translate(0 ' + (Number(current.getAttribute('data-y')) - 27) + ')');
            eventText.textContent = current.getAttribute('data-event');
        } else {
            eventG.classList.remove('fa-ver');
        }
        steps.forEach(function (step, index) {
            step.classList.toggle('fa-hecho', paso >= index + 2);
            step.classList.toggle('fa-actual', paso === index + 2);
        });
        if (bump) {
            bump.classList.toggle('fa-bump', paso === 5);
        }
        badge.classList.toggle('fa-ver', paso === 5);
        var total = String(128 + ciclo + (paso >= 5 ? 1 : 0));
        if (countEl.textContent !== total) {
            countEl.textContent = total;
            countEl.classList.remove('fa-pop');
            void countEl.getBoundingClientRect();
            countEl.classList.add('fa-pop');
        }
        while (tokenLayer.firstChild) {
            tokenLayer.removeChild(tokenLayer.firstChild);
        }
        if (reduce) {
            return;
        }
        if (paso === 1) {
            addToken('fa-crudo', routes[String(sourceId)]);
        }
        if (paso === 4) {
            addToken('fa-limpio', routes['4']);
        }
    }

    function tick() {
        first = false;
        if (paso === 5) {
            ciclo += 1;
            paso = 0;
        } else {
            paso += 1;
        }
        paint();
        schedule();
    }

    function schedule() {
        var wait = paso === 0 && first ? 2600 : dur[paso];
        window.setTimeout(tick, wait);
    }

    function start() {
        root.classList.add('fa-on');
        paint();
        if (reduce) {
            root.classList.add('fa-settled');
            return;
        }
        window.setTimeout(function () {
            root.classList.add('fa-settled');
        }, 3200);
        schedule();
    }

    if (reduce || !('IntersectionObserver' in window)) {
        start();
        return;
    }

    var observer = new IntersectionObserver(function (entries) {
        if (!entries[0].isIntersecting) {
            return;
        }
        observer.disconnect();
        start();
    }, { threshold: 0.35 });
    observer.observe(root);
}

document.addEventListener('DOMContentLoaded', function () {
    initAutomationFlow();

    var root = document.querySelector('[data-landing]');
    if (root) {
        var calc = root.querySelector('.cl-calc');
        var people = document.getElementById('cl-people');
        var hours = document.getElementById('cl-hours');
        var rate = document.getElementById('cl-rate');
        var locale = root.getAttribute('data-locale') === 'en' ? 'en-US' : 'es-CO';
        var hoursFormat = new Intl.NumberFormat(locale, { maximumFractionDigits: 0 });
        var usdFormat = new Intl.NumberFormat(locale, { style: 'currency', currency: 'USD', maximumFractionDigits: 0 });
        var copFormat = new Intl.NumberFormat(locale, { style: 'currency', currency: 'COP', maximumFractionDigits: 0 });
        var numberFormat = new Intl.NumberFormat(locale, { maximumFractionDigits: 0 });
        var unitPerson = calc.getAttribute('data-unit-person');
        var unitPeople = calc.getAttribute('data-unit-people');
        var unitHours = calc.getAttribute('data-unit-hours');
        var unitRate = calc.getAttribute('data-unit-rate');
        var unitMonth = calc.getAttribute('data-unit-month');
        var unitYear = calc.getAttribute('data-unit-year');
        var presets = Array.prototype.slice.call(calc.querySelectorAll('[data-preset]'));
        var regions = Array.prototype.slice.call(calc.querySelectorAll('[data-region]'));
        var copPerUsd = 4000;
        var region = root.getAttribute('data-locale') === 'en' ? 'usd' : 'cop';
        var latest = { month: '', year: '', detail: '' };

        function placePill(input, pill) {
            var min = Number(input.min);
            var max = Number(input.max);
            var ratio = (Number(input.value) - min) / (max - min);
            pill.style.left = 'calc(' + (ratio * 100) + '% + ' + ((0.5 - ratio) * 18) + 'px)';
        }

        function rateFor(button) {
            return button.getAttribute(region === 'usd' ? 'data-rate-usd' : 'data-rate-cop');
        }

        function applyMarket(next, preset) {
            region = next;
            rate.min = region === 'usd' ? '8' : '8000';
            rate.max = region === 'usd' ? '80' : '80000';
            rate.step = region === 'usd' ? '1' : '1000';
            people.value = preset ? preset.getAttribute('data-people') : '8';
            hours.value = preset ? preset.getAttribute('data-hours') : '8';
            rate.value = preset ? rateFor(preset) : (region === 'usd' ? '25' : '25000');
            render();
        }

        function render() {
            var peopleValue = Number(people.value);
            var hoursValue = Number(hours.value);
            var rateValue = Number(rate.value);
            var freed = peopleValue * hoursValue * 4.33 * 0.7;
            var yearly = freed * 12 * rateValue;
            var peopleLabel = String(peopleValue) + ' ' + (peopleValue === 1 ? unitPerson : unitPeople);
            var hoursLabel = String(hoursValue) + ' ' + unitHours;
            var rateLabel = region === 'usd'
                ? '$' + String(rateValue) + unitRate
                : 'COP ' + numberFormat.format(rateValue) + unitRate;
            var monthText = hoursFormat.format(Math.round(freed)) + ' ' + unitMonth;
            var yearText = (region === 'usd' ? usdFormat : copFormat).format(yearly) + ' ' + unitYear;
            var equiv = document.getElementById('cl-equiv');
            document.getElementById('cl-people-out').textContent = peopleLabel;
            document.getElementById('cl-hours-out').textContent = hoursLabel;
            document.getElementById('cl-rate-out').textContent = rateLabel;
            document.getElementById('cl-month').textContent = monthText;
            document.getElementById('cl-year').textContent = yearText;
            document.getElementById('cl-rate-label').textContent = calc.getAttribute(region === 'usd' ? 'data-rate-label-usd' : 'data-rate-label-cop');
            document.getElementById('cl-year-label').textContent = calc.getAttribute(region === 'usd' ? 'data-year-label-usd' : 'data-year-label-cop');
            calc.classList.toggle('is-cop', region === 'cop');
            if (region === 'cop') {
                equiv.hidden = false;
                equiv.textContent = calc.getAttribute('data-equiv').replace(':amount', usdFormat.format(yearly / copPerUsd));
            } else {
                equiv.hidden = true;
                equiv.textContent = '';
            }
            people.setAttribute('aria-valuetext', peopleLabel);
            hours.setAttribute('aria-valuetext', hoursLabel);
            rate.setAttribute('aria-valuetext', rateLabel);
            placePill(people, document.getElementById('cl-people-out'));
            placePill(hours, document.getElementById('cl-hours-out'));
            placePill(rate, document.getElementById('cl-rate-out'));
            latest.month = monthText;
            latest.year = yearText;
            latest.detail = peopleLabel + ', ' + hoursLabel + ', ' + rateLabel + ', ' + calc.getAttribute(region === 'usd' ? 'data-region-name-usd' : 'data-region-name-cop');
            presets.forEach(function (button) {
                var match = Number(button.getAttribute('data-people')) === peopleValue
                    && Number(button.getAttribute('data-hours')) === hoursValue
                    && Number(rateFor(button)) === rateValue;
                button.classList.toggle('is-active', match);
                button.setAttribute('aria-pressed', match ? 'true' : 'false');
            });
            regions.forEach(function (button) {
                var selected = button.getAttribute('data-region') === region;
                button.classList.toggle('is-active', selected);
                button.setAttribute('aria-checked', selected ? 'true' : 'false');
            });
        }

        [people, hours, rate].forEach(function (input) {
            input.addEventListener('input', render);
        });
        presets.forEach(function (button) {
            button.addEventListener('click', function () {
                people.value = button.getAttribute('data-people');
                hours.value = button.getAttribute('data-hours');
                rate.value = rateFor(button);
                render();
            });
        });
        regions.forEach(function (button) {
            button.addEventListener('click', function () {
                var selected = presets.filter(function (item) { return item.classList.contains('is-active'); })[0];
                applyMarket(button.getAttribute('data-region'), selected);
            });
        });
        applyMarket(region, null);
        var cta = document.getElementById('cl-calc-cta');
        if (cta) {
            cta.addEventListener('click', function (event) {
                var message = document.getElementById('landing-message');
                var target = document.getElementById('contacto');
                if (!message || !target) {
                    return;
                }
                event.preventDefault();
                var estimate = calc.getAttribute('data-estimate-prefix') + ' ' + latest.month + ' ' + calc.getAttribute('data-and') + ' ' + latest.year + ' (' + latest.detail + ').';
                if (!message.value.trim() || message.value === message.getAttribute('data-last-estimate')) {
                    message.value = estimate;
                    message.setAttribute('data-last-estimate', estimate);
                }
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                window.setTimeout(function () {
                    message.focus({ preventScroll: true });
                }, 450);
            });
        }
        render();
    }

    var steps = document.querySelector('[data-steps]');
    if (!steps) {
        return;
    }

    var tabs = Array.prototype.slice.call(steps.querySelectorAll('[role="tab"]'));

    function select(tab) {
        tabs.forEach(function (item) {
            var selected = item === tab;
            var panel = document.getElementById(item.getAttribute('aria-controls'));
            item.setAttribute('aria-selected', selected ? 'true' : 'false');
            item.tabIndex = selected ? 0 : -1;
            if (panel) {
                panel.hidden = !selected;
            }
        });
    }

    tabs.forEach(function (tab, index) {
        tab.addEventListener('click', function () {
            select(tab);
        });
        tab.addEventListener('keydown', function (event) {
            var next = index;
            if (event.key === 'ArrowRight' || event.key === 'ArrowDown') {
                next = (index + 1) % tabs.length;
            } else if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') {
                next = (index - 1 + tabs.length) % tabs.length;
            } else {
                return;
            }
            event.preventDefault();
            tabs[next].focus();
            select(tabs[next]);
        });
    });
});
