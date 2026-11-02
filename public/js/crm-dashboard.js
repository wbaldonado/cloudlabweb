(function () {
    'use strict';

    if (window.__crmDashboardJs) return;
    window.__crmDashboardJs = true;

    const root = document.getElementById('crmDashboard');
    if (!root) return;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const headers = {
        'X-CSRF-TOKEN': csrf,
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    };

    const stores = {
        contact: root.dataset.contactStore,
        company: root.dataset.companyStore,
        deal: root.dataset.dealStore,
        activity: root.dataset.activityStore,
    };

    const modals = {
        contact: document.getElementById('contactModal'),
        company: document.getElementById('companyModal'),
        deal: document.getElementById('dealModal'),
        activity: document.getElementById('activityModal'),
    };

    const forms = {
        contact: document.getElementById('crmContactForm'),
        company: document.getElementById('crmCompanyForm'),
        deal: document.getElementById('crmDealForm'),
        activity: document.getElementById('crmActivityForm'),
    };

    const titles = {
        contact: document.getElementById('contactModalTitle'),
        company: document.getElementById('companyModalTitle'),
        deal: document.getElementById('dealModalTitle'),
        activity: document.getElementById('activityModalTitle'),
    };

    const errorBoxes = {
        contact: document.getElementById('crmContactFormErrors'),
        company: document.getElementById('crmCompanyFormErrors'),
        deal: document.getElementById('crmDealFormErrors'),
        activity: document.getElementById('crmActivityFormErrors'),
        email: document.getElementById('crmEmailFormErrors'),
    };

    const emailForm = document.getElementById('crmEmailForm');
    const emailModal = document.getElementById('emailModal');
    let emailSendUrl = '';

    const contactFields = ['first_name', 'last_name', 'email', 'phone', 'job_title', 'lifecycle_stage', 'company_id', 'owner_id', 'notes'];
    const companyFields = ['name', 'industry', 'website', 'phone', 'address', 'city', 'state', 'country', 'zip_code', 'description'];
    const dealFields = ['title', 'amount', 'probability', 'stage', 'expected_close_date', 'company_id', 'contact_id', 'description'];
    const activityFields = ['title', 'type', 'status', 'due_date', 'company_id', 'contact_id', 'notes'];

    const fieldMap = { contact: contactFields, company: companyFields, deal: dealFields, activity: activityFields };

    const titleCreate = {
        contact: 'Nuevo contacto',
        company: 'Nueva empresa',
        deal: 'Nueva oportunidad',
        activity: 'Nueva actividad',
    };

    const titleEdit = {
        contact: 'Editar contacto',
        company: 'Editar empresa',
        deal: 'Editar oportunidad',
        activity: 'Editar actividad',
    };

    function getModal(type) {
        return bootstrap.Modal.getOrCreateInstance(modals[type]);
    }

    function clearErrors(type) {
        const box = errorBoxes[type];
        if (!box) return;
        box.classList.add('d-none');
        box.innerHTML = '';
    }

    function showErrors(type, payload) {
        const box = errorBoxes[type];
        if (!box) return;
        const messages = payload.errors
            ? Object.values(payload.errors).flat().join('<br>')
            : (payload.message || 'No se pudo guardar.');
        box.innerHTML = messages;
        box.classList.remove('d-none');
    }

    function setMethod(form, method) {
        let input = form.querySelector('input[name="_method"]');
        if (method === 'POST') {
            if (input) input.remove();
            return;
        }
        if (!input) {
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = '_method';
            form.appendChild(input);
        }
        input.value = method;
    }

    function fillForm(type, data) {
        const form = forms[type];
        const fields = fieldMap[type];
        fields.forEach((name) => {
            const el = form.querySelector(`[name="${name}"]`);
            if (!el) return;
            const value = data[name] ?? '';
            el.value = value === null ? '' : value;
        });
    }

    function openCreate(type) {
        const form = forms[type];
        form.reset();
        clearErrors(type);
        setMethod(form, 'POST');
        form.action = stores[type];
        titles[type].textContent = titleCreate[type];
        getModal(type).show();
    }

    function openEdit(type, data) {
        const form = forms[type];
        form.reset();
        clearErrors(type);
        fillForm(type, data);
        setMethod(form, 'PUT');
        form.action = data.update_url;
        titles[type].textContent = titleEdit[type];
        getModal(type).show();
    }

    function flash(message) {
        const el = document.getElementById('crmFlash');
        if (!el) return;
        el.textContent = message;
        el.classList.remove('d-none');
        setTimeout(() => el.classList.add('d-none'), 4000);
    }

    function bumpStat(id, delta) {
        const el = document.getElementById(id);
        if (!el) return;
        const num = parseInt(el.textContent.replace(/\D/g, ''), 10) || 0;
        el.textContent = String(num + delta);
    }

    function escapeHtml(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function attrJson(obj) {
        return JSON.stringify(obj).replace(/&/g, '&amp;').replace(/'/g, '&#39;').replace(/</g, '\\u003c');
    }

    function fmtDate(iso) {
        if (!iso) return '—';
        const m = /^(\d{4})-(\d{2})-(\d{2})/.exec(String(iso));
        return m ? `${m[3]}/${m[2]}/${m[1]}` : escapeHtml(iso);
    }

    function isPastDate(iso) {
        if (!iso) return false;
        const today = new Date();
        const todayIso = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
        return String(iso).slice(0, 10) < todayIso;
    }

    function iconBtn(cls, title, attrs, icon) {
        return `<button type="button" class="crm-icon-btn ${cls}" title="${title}" aria-label="${title}" ${attrs}><i class="bi ${icon}"></i></button>`;
    }

    function contactRowHtml(c) {
        const payload = attrJson(c);
        const email = c.email
            ? `<a href="mailto:${escapeHtml(c.email)}" class="crm-link" title="${escapeHtml(c.email)}">${escapeHtml(c.email)}</a>`
            : '<span class="text-muted">—</span>';
        const phone = c.phone
            ? `<a href="tel:${escapeHtml(String(c.phone).replace(/\s+/g, ''))}" class="crm-link">${escapeHtml(c.phone)}</a>`
            : '<span class="text-muted">—</span>';
        const emailBtn = c.send_email_url
            ? iconBtn('', 'Enviar correo', `data-crm-email data-crm-email-url="${escapeHtml(c.send_email_url)}" data-crm-email-to="${escapeHtml(c.email)}" data-crm-email-name="${escapeHtml(c.full_name)}"`, 'bi-envelope')
            : '';
        const job = c.job_title ? `<div class="crm-cell-sub">${escapeHtml(c.job_title)}</div>` : '';
        // Campaign counters are computed server-side on page load; a contact that was just
        // created/edited via AJAX keeps whatever the existing row already showed.
        const existingCell = document.querySelector(`[data-crm-contact-row="${c.id}"] [data-crm-campaign-cell]`);
        const campaignCell = existingCell ? existingCell.innerHTML : '<span class="text-muted">—</span>';
        return `
            <tr data-crm-contact-row="${c.id}">
                <td class="crm-select-col">
                    <input type="checkbox" class="form-check-input" name="ids[]" data-crm-select="contact" value="${c.id}" aria-label="Seleccionar ${escapeHtml(c.full_name)}">
                </td>
                <td class="crm-cell-primary"><div class="crm-cell-title">${escapeHtml(c.full_name)}</div>${job}</td>
                <td class="crm-cell-truncate" title="${escapeHtml(c.company || '')}">${escapeHtml(c.company || '—')}</td>
                <td><span class="crm-badge ${escapeHtml(c.stage_class)}">${escapeHtml(c.stage_label)}</span></td>
                <td class="crm-cell-truncate">${email}</td>
                <td class="crm-nowrap">${phone}</td>
                <td class="crm-nowrap" data-crm-campaign-cell>${campaignCell}</td>
                <td class="text-end">
                    <div class="crm-actions">
                        ${emailBtn}
                        ${iconBtn('', 'Editar', `data-crm-edit="contact" data-crm-payload='${payload}'`, 'bi-pencil')}
                        ${iconBtn('crm-icon-btn--danger', 'Eliminar', `data-crm-delete="contact" data-crm-url="${escapeHtml(c.destroy_url)}" data-crm-id="${c.id}"`, 'bi-trash')}
                    </div>
                </td>
            </tr>`;
    }

    function companyItemHtml(co) {
        const payload = attrJson(co);
        const website = co.website
            ? `<div class="crm-cell-sub"><a href="${escapeHtml(co.website)}" target="_blank" rel="noopener" class="crm-link">${escapeHtml(String(co.website).replace(/^https?:\/\//i, ''))}</a></div>`
            : '';
        return `
            <tr data-crm-company-row="${co.id}">
                <td class="crm-select-col">
                    <input type="checkbox" class="form-check-input" name="ids[]" data-crm-select="company" value="${co.id}" aria-label="Seleccionar ${escapeHtml(co.name)}">
                </td>
                <td class="crm-cell-primary"><div class="crm-cell-title">${escapeHtml(co.name)}</div>${website}</td>
                <td class="crm-cell-truncate" title="${escapeHtml(co.industry || '')}">${escapeHtml(co.industry || '—')}</td>
                <td class="crm-nowrap">${escapeHtml(co.city || '—')}</td>
                <td class="crm-nowrap">${escapeHtml(co.country || '—')}</td>
                <td class="crm-nowrap">${escapeHtml(co.phone || '—')}</td>
                <td class="text-end">
                    <div class="crm-actions">
                        ${iconBtn('', 'Editar', `data-crm-edit="company" data-crm-payload='${payload}'`, 'bi-pencil')}
                        ${iconBtn('crm-icon-btn--danger', 'Eliminar', `data-crm-delete="company" data-crm-url="${escapeHtml(co.destroy_url)}" data-crm-id="${co.id}"`, 'bi-trash')}
                    </div>
                </td>
            </tr>`;
    }

    const activityTypeIcons = { email: 'bi-envelope', meeting: 'bi-camera-video', call: 'bi-telephone', task: 'bi-check2-square' };

    function activityCardHtml(a) {
        const payload = attrJson(a);
        const state = a.status === 'completed' ? 'completed' : 'pending';
        const overdue = state === 'pending' && isPastDate(a.due_date);
        const notes = a.notes
            ? `<div class="crm-cell-sub crm-cell-truncate" title="${escapeHtml(a.notes)}">${escapeHtml(String(a.notes).slice(0, 90))}</div>`
            : '';
        const flag = overdue ? '<span class="crm-date-flag">Vencida</span>' : '';
        return `
            <tr data-crm-activity-row="${a.id}" data-crm-state="${state}" class="${state === 'completed' ? 'crm-row-muted' : ''}">
                <td class="crm-cell-primary"><div class="crm-cell-title">${escapeHtml(a.title)}</div>${notes}</td>
                <td><span class="crm-type-chip"><i class="bi ${activityTypeIcons[a.type] || 'bi-check2-square'}"></i> ${escapeHtml(a.type_label)}</span></td>
                <td class="crm-cell-truncate" title="${escapeHtml(a.meta)}">${escapeHtml(a.meta)}</td>
                <td class="crm-nowrap ${overdue ? 'crm-date--overdue' : ''}">${fmtDate(a.due_date)} ${flag}</td>
                <td><span class="crm-badge crm-status--${escapeHtml(a.status)} crm-activity-status">${escapeHtml(a.status_label)}</span></td>
                <td class="text-end">
                    <div class="crm-actions">
                        ${iconBtn('', 'Editar', `data-crm-edit="activity" data-crm-payload='${payload}'`, 'bi-pencil')}
                        ${iconBtn('crm-icon-btn--danger', 'Eliminar', `data-crm-delete="activity" data-crm-url="${escapeHtml(a.destroy_url)}" data-crm-id="${a.id}"`, 'bi-trash')}
                    </div>
                </td>
            </tr>`;
    }

    /* ---------- Tabs + client-side filters ---------- */

    const filterState = { deals: 'open', activities: 'pending' };

    function applyFilter(group) {
        const target = root.querySelector(`[data-crm-filter-target="${group}"]`);
        if (!target) return;
        const mode = filterState[group];
        let visible = 0;
        target.querySelectorAll('tr[data-crm-state]').forEach((row) => {
            const show = mode === 'all' || row.dataset.crmState === mode;
            row.classList.toggle('d-none', !show);
            if (show) visible += 1;
        });
        const emptyRow = target.querySelector(`[data-crm-filter-empty="${group}"]`);
        if (emptyRow) emptyRow.classList.toggle('d-none', visible > 0);
        const foot = root.querySelector(`[data-crm-filter-foot="${group}"]`);
        if (foot) foot.classList.toggle('d-none', mode !== 'open');
        root.querySelectorAll(`[data-crm-filter-group="${group}"] [data-crm-filter]`).forEach((btn) => {
            btn.classList.toggle('active', btn.dataset.crmFilter === mode);
        });
    }

    root.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-crm-filter]');
        if (!btn) return;
        const group = btn.closest('[data-crm-filter-group]')?.dataset.crmFilterGroup;
        if (!group) return;
        filterState[group] = btn.dataset.crmFilter;
        applyFilter(group);
    });

    function activateTab(name) {
        const btn = root.querySelector(`[data-crm-tab="${name}"]`);
        if (!btn || typeof bootstrap === 'undefined') return;
        bootstrap.Tab.getOrCreateInstance(btn).show();
    }

    (function initTabs() {
        const hash = (window.location.hash || '').replace('#', '');
        if (hash && root.querySelector(`[data-crm-tab="${hash}"]`)) {
            activateTab(hash);
        }
        root.querySelectorAll('[data-crm-tab]').forEach((btn) => {
            btn.addEventListener('shown.bs.tab', () => {
                if (window.history.replaceState) {
                    window.history.replaceState(null, '', `#${btn.dataset.crmTab}`);
                }
            });
        });
    })();

    function upsertContact(c) {
        const tbody = document.getElementById('crmContactsTableBody');
        const existing = tbody.querySelector(`[data-crm-contact-row="${c.id}"]`);
        const html = contactRowHtml(c);
        if (existing) {
            existing.outerHTML = html;
        } else {
            document.getElementById('crmContactsEmpty')?.remove();
            tbody.insertAdjacentHTML('afterbegin', html);
            bumpStat('crmStatContacts', 1);
            bumpStat('crmTabCountContacts', 1);
            const total = (parseInt(root.dataset.contactsTotal || '0', 10) || 0) + 1;
            root.dataset.contactsTotal = String(total);
        }
        updateContactBulkBar();
    }

    function upsertCompany(co) {
        const list = document.getElementById('crmCompaniesTableBody');
        if (!list) return;
        const existing = list.querySelector(`[data-crm-company-row="${co.id}"]`);
        const html = companyItemHtml(co);
        if (existing) {
            existing.outerHTML = html;
        } else {
            document.getElementById('crmCompaniesEmpty')?.remove();
            list.insertAdjacentHTML('afterbegin', html);
            bumpStat('crmStatCompanies', 1);
            bumpStat('crmTabCountCompanies', 1);
            const total = (parseInt(root.dataset.companiesTotal || '0', 10) || 0) + 1;
            root.dataset.companiesTotal = String(total);
        }
        refreshCompanySelects(co);
        updateCompanyBulkBar();
    }

    function refreshCompanySelects(co) {
        document.querySelectorAll('select[name="company_id"]').forEach((select) => {
            let opt = select.querySelector(`option[value="${co.id}"]`);
            if (!opt) {
                opt = document.createElement('option');
                opt.value = co.id;
                select.appendChild(opt);
            }
            opt.textContent = co.name;
        });
    }

    function refreshContactSelects(c) {
        document.querySelectorAll('select[name="contact_id"]').forEach((select) => {
            let opt = select.querySelector(`option[value="${c.id}"]`);
            if (!opt) {
                opt = document.createElement('option');
                opt.value = c.id;
                select.appendChild(opt);
            }
            opt.textContent = c.full_name;
        });
    }

    function upsertActivity(a) {
        const list = document.getElementById('crmActivitiesList');
        const existing = list.querySelector(`[data-crm-activity-row="${a.id}"]`);
        const html = activityCardHtml(a);
        const isPending = a.status !== 'completed';
        if (existing) {
            const wasPending = existing.dataset.crmState === 'pending';
            existing.outerHTML = html;
            if (wasPending && !isPending) {
                bumpStat('crmStatPending', -1);
                bumpStat('crmTabCountActivities', -1);
                bumpStat('crmActivitiesPendingCount', -1);
            } else if (!wasPending && isPending) {
                bumpStat('crmStatPending', 1);
                bumpStat('crmTabCountActivities', 1);
                bumpStat('crmActivitiesPendingCount', 1);
            }
        } else {
            document.getElementById('crmActivitiesEmpty')?.remove();
            list.insertAdjacentHTML('afterbegin', html);
            if (isPending) {
                bumpStat('crmStatPending', 1);
                bumpStat('crmTabCountActivities', 1);
                bumpStat('crmActivitiesPendingCount', 1);
            }
        }
        applyFilter('activities');
    }

    async function submitForm(type) {
        const form = forms[type];
        clearErrors(type);
        const submitBtn = form.querySelector('[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers,
                body: new FormData(form),
            });
            const data = await response.json();

            if (!response.ok) {
                showErrors(type, data);
                return;
            }

            getModal(type).hide();
            flash(data.message || 'Guardado correctamente.');

            if (type === 'contact') {
                upsertContact(data.contact);
                refreshContactSelects(data.contact);
            } else if (type === 'company') {
                upsertCompany(data.company);
            } else if (type === 'deal') {
                window.location.reload();
                return;
            } else if (type === 'activity') {
                upsertActivity(data.activity);
            }
        } catch (err) {
            showErrors(type, { message: 'Error de conexión. Intenta de nuevo.' });
        } finally {
            if (submitBtn) submitBtn.disabled = false;
        }
    }

    let contactsSelectAllMatching = false;

    function contactCheckboxes() {
        return [...document.querySelectorAll('[data-crm-select="contact"]')];
    }

    function companyCheckboxes() {
        return [...document.querySelectorAll('[data-crm-select="company"]')];
    }

    function setChecked(boxes, checked) {
        boxes.forEach((box) => { box.checked = checked; });
    }

    function selectedIds(type) {
        return [...document.querySelectorAll(`[data-crm-select="${type}"]:checked`)].map((el) => el.value);
    }

    function updateSelectAllState(selectAll, boxes) {
        if (!selectAll) return;
        selectAll.disabled = boxes.length === 0;
        selectAll.checked = boxes.length > 0 && boxes.every((box) => box.checked);
        selectAll.indeterminate = boxes.some((box) => box.checked) && !selectAll.checked;
    }

    function updateContactBulkBar() {
        const boxes = contactCheckboxes();
        const checked = boxes.filter((box) => box.checked);
        const total = parseInt(root.dataset.contactsTotal || '0', 10) || 0;
        const count = contactsSelectAllMatching ? total : checked.length;

        updateSelectAllState(document.getElementById('crmSelectAllContacts'), boxes);

        const countEl = document.getElementById('crmContactsSelectedCount');
        if (countEl) {
            countEl.textContent = count === 0
                ? 'Ningún contacto seleccionado'
                : (count === 1 ? '1 contacto seleccionado' : `${count} contactos seleccionados`);
        }

        const matchingBtn = document.getElementById('crmSelectAllMatchingContacts');
        if (matchingBtn) {
            const showMatching = !contactsSelectAllMatching && total > 0;
            matchingBtn.textContent = `Seleccionar todos (${total})`;
            matchingBtn.classList.toggle('d-none', !showMatching);
            matchingBtn.disabled = total === 0;
        }

        const matchingNote = document.getElementById('crmContactsMatchingNote');
        if (matchingNote) {
            matchingNote.classList.toggle('d-none', !contactsSelectAllMatching);
        }

        const deleteBtn = document.getElementById('crmDeleteSelectedContacts');
        if (deleteBtn) {
            deleteBtn.disabled = count === 0;
            deleteBtn.textContent = count <= 1 ? 'Eliminar seleccionados' : `Eliminar ${count} contactos`;
        }
    }

    function updateCompanyBulkBar() {
        const boxes = companyCheckboxes();
        const count = boxes.filter((box) => box.checked).length;

        updateSelectAllState(document.getElementById('crmSelectAllCompanies'), boxes);

        const deleteBtn = document.getElementById('crmDeleteSelectedCompanies');
        if (deleteBtn) {
            deleteBtn.disabled = count === 0;
            deleteBtn.textContent = count <= 1 ? 'Eliminar seleccionadas' : `Eliminar ${count} empresas`;
        }
    }

    async function bulkDelete(type) {
        const isContact = type === 'contact';
        const url = isContact ? root.dataset.contactBulkDestroy : root.dataset.companyBulkDestroy;
        const allMatching = isContact && contactsSelectAllMatching;
        const ids = selectedIds(type);
        const total = isContact ? (parseInt(root.dataset.contactsTotal || '0', 10) || 0) : companyCheckboxes().length;
        const count = allMatching ? total : ids.length;
        const noun = isContact
            ? (count === 1 ? 'contacto' : 'contactos')
            : (count === 1 ? 'empresa' : 'empresas');

        if (!url || count === 0) return;
        if (!confirm(`¿Eliminar ${count} ${noun}? Esta acción no se puede deshacer.`)) return;

        const body = new FormData();
        body.append('_token', csrf);
        if (allMatching || (!isContact && count === companyCheckboxes().length && count > 0)) {
            body.append('all', '1');
            if (isContact) {
                const q = (root.dataset.contactsQ || '').trim();
                const stage = (root.dataset.contactsStage || '').trim();
                if (q) body.append('q', q);
                if (stage) body.append('stage', stage);
            }
        } else {
            ids.forEach((id) => body.append('ids[]', id));
        }

        const deleteBtn = document.getElementById(isContact ? 'crmDeleteSelectedContacts' : 'crmDeleteSelectedCompanies');
        if (deleteBtn) deleteBtn.disabled = true;

        try {
            const response = await fetch(url, { method: 'POST', headers, body, credentials: 'same-origin' });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                let msg = data.message || 'No se pudo eliminar la selección.';
                if (data.errors) {
                    const list = Object.values(data.errors).flat();
                    if (list.length) msg = list.join('\n');
                }
                alert(msg);
                return;
            }
            flash(data.message || 'Eliminados.');
            window.location.reload();
        } catch (err) {
            alert('Error de conexión.');
        } finally {
            if (deleteBtn) deleteBtn.disabled = false;
        }
    }

    async function deleteEntity(type, url, id) {
        if (!confirm('¿Eliminar este registro? Esta acción no se puede deshacer.')) return;

        try {
            const body = new FormData();
            body.append('_method', 'DELETE');
            body.append('_token', csrf);

            const response = await fetch(url, { method: 'POST', headers, body });
            const data = await response.json();

            if (!response.ok) {
                alert(data.message || 'No se pudo eliminar.');
                return;
            }

            flash(data.message || 'Eliminado.');

            if (type === 'contact') {
                document.querySelector(`[data-crm-contact-row="${id}"]`)?.remove();
                bumpStat('crmStatContacts', -1);
                bumpStat('crmTabCountContacts', -1);
                const total = Math.max(0, (parseInt(root.dataset.contactsTotal || '0', 10) || 0) - 1);
                root.dataset.contactsTotal = String(total);
                contactsSelectAllMatching = false;
                updateContactBulkBar();
            } else if (type === 'company') {
                document.querySelector(`[data-crm-company-row="${id}"]`)?.remove();
                bumpStat('crmStatCompanies', -1);
                bumpStat('crmTabCountCompanies', -1);
                const total = Math.max(0, (parseInt(root.dataset.companiesTotal || '0', 10) || 0) - 1);
                root.dataset.companiesTotal = String(total);
                updateCompanyBulkBar();
            } else if (type === 'deal') {
                window.location.reload();
            } else if (type === 'activity') {
                const row = document.querySelector(`[data-crm-activity-row="${id}"]`);
                const wasPending = row?.dataset.crmState === 'pending';
                row?.remove();
                if (wasPending) {
                    bumpStat('crmStatPending', -1);
                    bumpStat('crmTabCountActivities', -1);
                    bumpStat('crmActivitiesPendingCount', -1);
                }
                applyFilter('activities');
            }
        } catch (err) {
            alert('Error de conexión.');
        }
    }

    function openEmailModal(url, to, name) {
        if (!emailForm || !emailModal) return;
        emailSendUrl = url;
        emailForm.reset();
        clearErrors('email');
        document.getElementById('crmEmailTo').value = `${name} <${to}>`;
        document.getElementById('emailModalTitle').textContent = `Email a ${name}`;
        bootstrap.Modal.getOrCreateInstance(emailModal).show();
    }

    async function submitEmailForm(e) {
        e.preventDefault();
        if (!emailSendUrl) return;
        clearErrors('email');
        const submitBtn = document.getElementById('crmEmailSubmit');
        if (submitBtn) submitBtn.disabled = true;

        const body = new FormData(emailForm);

        try {
            const response = await fetch(emailSendUrl, {
                method: 'POST',
                headers,
                body,
                credentials: 'same-origin',
            });
            const raw = await response.text();
            let data = {};
            try {
                data = raw ? JSON.parse(raw) : {};
            } catch (parseErr) {
                const hint = response.status === 419
                    ? 'Sesión expirada. Recarga la página e intenta de nuevo.'
                    : response.status === 404
                        ? 'Ruta de email no encontrada. Sube routes/web.php y ejecuta php artisan route:clear.'
                        : `El servidor respondió HTTP ${response.status} (no JSON). Revisa MAIL_* en .env y storage/logs/laravel.log.`;
                showErrors('email', { message: hint });
                return;
            }
            if (!response.ok) {
                showErrors('email', data);
                return;
            }
            bootstrap.Modal.getInstance(emailModal)?.hide();
            flash(data.message || 'Correo enviado.');
        } catch (err) {
            showErrors('email', {
                message: 'No se pudo contactar al servidor (red/CSP). Recarga la página. Si persiste, revisa APP_URL y que uses https.',
            });
        } finally {
            if (submitBtn) submitBtn.disabled = false;
        }
    }

    // ---- Campaign history per contact ----
    const historyModalEl = document.getElementById('campaignHistoryModal');
    const historyModal = historyModalEl && window.bootstrap ? new bootstrap.Modal(historyModalEl) : null;

    function historyRowHtml(item) {
        const campaign = item.campaign_url
            ? `<a href="${escapeHtml(item.campaign_url)}" class="fw-semibold text-decoration-none text-reset">${escapeHtml(item.campaign)}</a>`
            : `<span class="fw-semibold">${escapeHtml(item.campaign)}</span>`;
        const subject = item.subject ? `<div class="small text-muted">${escapeHtml(item.subject)}</div>` : '';
        const opened = item.opened_at
            ? `<span class="text-success"><i class="bi bi-envelope-open"></i> ${item.open_count}</span><div class="small text-muted">${escapeHtml(item.opened_at)}</div>`
            : '<span class="text-muted">—</span>';
        const clicked = item.clicked_at
            ? `<span class="text-primary"><i class="bi bi-cursor"></i> ${item.click_count}</span><div class="small text-muted">${escapeHtml(item.clicked_at)}</div>`
            : '<span class="text-muted">—</span>';
        const detail = item.error
            ? `<span class="small text-danger" title="${escapeHtml(item.error)}">${escapeHtml(item.error)}</span>`
            : (item.campaign_url ? `<a href="${escapeHtml(item.campaign_url)}" class="btn btn-sm btn-outline-secondary">Ver campaña</a>` : '');
        return `
            <tr>
                <td>${campaign}${subject}</td>
                <td><span class="badge ${escapeHtml(item.status_class)}">${escapeHtml(item.status_label)}</span></td>
                <td class="text-nowrap">${escapeHtml(item.sent_at || '—')}</td>
                <td class="text-nowrap">${opened}</td>
                <td class="text-nowrap">${clicked}</td>
                <td class="text-end">${detail}</td>
            </tr>`;
    }

    async function openCampaignHistory(url, name) {
        if (!historyModal) return;
        const title = document.getElementById('campaignHistoryTitle');
        const subtitle = document.getElementById('campaignHistorySubtitle');
        const summary = document.getElementById('campaignHistorySummary');
        const status = document.getElementById('campaignHistoryStatus');
        const wrap = document.getElementById('campaignHistoryTableWrap');
        const body = document.getElementById('campaignHistoryBody');

        if (title) title.textContent = `Campañas de ${name || 'contacto'}`;
        if (subtitle) subtitle.textContent = '';
        if (summary) summary.innerHTML = '';
        if (body) body.innerHTML = '';
        if (wrap) wrap.classList.add('d-none');
        if (status) {
            status.textContent = 'Cargando…';
            status.classList.remove('d-none', 'text-danger');
        }
        historyModal.show();

        try {
            const res = await fetch(url, { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            if (!res.ok || !data.success) {
                throw new Error(data.message || 'No se pudo cargar el historial.');
            }

            if (subtitle) subtitle.textContent = data.contact?.email || '';
            if (summary) {
                const s = data.summary || {};
                summary.innerHTML = `
                    <div class="crm-history-stat"><span class="crm-history-stat-value">${s.total ?? 0}</span><span class="crm-history-stat-label">Campañas</span></div>
                    <div class="crm-history-stat"><span class="crm-history-stat-value">${s.sent ?? 0}</span><span class="crm-history-stat-label">Enviadas</span></div>
                    <div class="crm-history-stat"><span class="crm-history-stat-value text-success">${s.opened ?? 0}</span><span class="crm-history-stat-label">Abiertas</span></div>
                    <div class="crm-history-stat"><span class="crm-history-stat-value text-primary">${s.clicked ?? 0}</span><span class="crm-history-stat-label">Con clic</span></div>`;
            }

            const items = Array.isArray(data.items) ? data.items : [];
            if (items.length === 0) {
                if (status) status.textContent = 'Este contacto no ha recibido campañas.';
                return;
            }

            if (body) body.innerHTML = items.map(historyRowHtml).join('');
            if (status) status.classList.add('d-none');
            if (wrap) wrap.classList.remove('d-none');
        } catch (err) {
            if (status) {
                status.textContent = err.message || 'No se pudo cargar el historial.';
                status.classList.add('text-danger');
            }
        }
    }

    root.addEventListener('click', (e) => {
        const historyBtn = e.target.closest('[data-crm-campaigns]');
        if (historyBtn) {
            openCampaignHistory(historyBtn.dataset.crmCampaignsUrl, historyBtn.dataset.crmCampaignsName);
            return;
        }

        const emailBtn = e.target.closest('[data-crm-email]');
        if (emailBtn) {
            openEmailModal(emailBtn.dataset.crmEmailUrl, emailBtn.dataset.crmEmailTo, emailBtn.dataset.crmEmailName);
            return;
        }

        const openBtn = e.target.closest('[data-crm-open]');
        if (openBtn) {
            openCreate(openBtn.dataset.crmOpen);
            return;
        }

        const editBtn = e.target.closest('[data-crm-edit]');
        if (editBtn) {
            try {
                openEdit(editBtn.dataset.crmEdit, JSON.parse(editBtn.dataset.crmPayload));
            } catch (err) {
                console.error(err);
            }
            return;
        }

        const deleteBtn = e.target.closest('[data-crm-delete]');
        if (deleteBtn) {
            deleteEntity(deleteBtn.dataset.crmDelete, deleteBtn.dataset.crmUrl, deleteBtn.dataset.crmId);
        }
    });


    if (emailForm) {
        emailForm.addEventListener('submit', submitEmailForm);
    }

    const importForm = document.getElementById('crmImportForm');
    if (importForm) {
        importForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const errBox = document.getElementById('crmImportFormErrors');
            const okBox = document.getElementById('crmImportFormResult');
            const submitBtn = document.getElementById('crmImportSubmit');
            if (errBox) {
                errBox.classList.add('d-none');
                errBox.innerHTML = '';
            }
            if (okBox) {
                okBox.classList.add('d-none');
                okBox.innerHTML = '';
            }
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Importando…';
            }

            try {
                const res = await fetch(importForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: new FormData(importForm),
                    credentials: 'same-origin',
                });
                const data = await res.json().catch(() => ({}));
                if (!res.ok || data.success === false) {
                    let msg = data.message || 'No se pudo importar el archivo.';
                    if (data.errors) {
                        const list = Object.values(data.errors).flat();
                        if (list.length) msg = list.join('<br>');
                    }
                    if (Array.isArray(data.errors) && data.errors.length && typeof data.errors[0] === 'string') {
                        msg += '<br><br>' + data.errors.slice(0, 8).join('<br>');
                    }
                    if (errBox) {
                        errBox.innerHTML = msg;
                        errBox.classList.remove('d-none');
                    }
                    return;
                }

                let detail = data.message || 'Importación completada.';
                if (Array.isArray(data.errors) && data.errors.length) {
                    detail += '<br><small class="text-muted">Avisos:<br>' + data.errors.slice(0, 8).join('<br>') + '</small>';
                }
                if (okBox) {
                    okBox.innerHTML = detail;
                    okBox.classList.remove('d-none');
                }
                flash(data.message || 'Importación completada.');
                setTimeout(() => window.location.reload(), 1500);
            } catch (err) {
                if (errBox) {
                    errBox.textContent = 'Error de red o tiempo de espera. Para archivos grandes use: php artisan crm:import-csv ruta/archivo.csv --type=companies';
                    errBox.classList.remove('d-none');
                }
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-upload"></i> Importar';
                }
            }
        });
    }

    updateContactBulkBar();
    updateCompanyBulkBar();

    Object.keys(forms).forEach((type) => {
        const form = forms[type];
        if (!form) return;
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            submitForm(type);
        });
    });
})();
