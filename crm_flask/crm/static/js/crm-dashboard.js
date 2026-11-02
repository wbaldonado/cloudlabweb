(function () {
    'use strict';

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

    function contactRowHtml(c) {
        const email = c.email ? `<div><i class="bi bi-envelope me-1"></i>${escapeHtml(c.email)}</div>` : '';
        const phone = c.phone ? `<div><i class="bi bi-telephone me-1"></i>${escapeHtml(c.phone)}</div>` : '';
        const payload = attrJson(c);
        const emailBtn = c.send_email_url
            ? `<button type="button" class="btn btn-sm btn-outline-primary" data-crm-email data-crm-email-url="${escapeHtml(c.send_email_url)}" data-crm-email-to="${escapeHtml(c.email)}" data-crm-email-name="${escapeHtml(c.full_name)}">Email</button>`
            : '';
        return `
            <tr data-crm-contact-row="${c.id}">
                <td><div class="crm-contact-name">${escapeHtml(c.full_name)}</div><div class="crm-contact-meta">${escapeHtml(c.job_title || '—')}</div></td>
                <td>${escapeHtml(c.company || '—')}</td>
                <td><span class="crm-badge ${escapeHtml(c.stage_class)}">${escapeHtml(c.stage_label)}</span></td>
                <td>${email}${phone}</td>
                <td class="text-end crm-row-actions">
                    ${emailBtn}
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-crm-edit="contact" data-crm-payload='${payload}'>Editar</button>
                    <button type="button" class="btn btn-sm btn-outline-danger" data-crm-delete="contact" data-crm-url="${escapeHtml(c.destroy_url)}" data-crm-id="${c.id}">Eliminar</button>
                </td>
            </tr>`;
    }

    function companyItemHtml(co) {
        const payload = attrJson(co);
        return `
            <div class="crm-list-item crm-list-item--actions" data-crm-company-row="${co.id}">
                <div>
                    <div class="crm-list-title">${escapeHtml(co.name)}</div>
                    <div class="crm-list-meta">${escapeHtml(co.meta)}</div>
                </div>
                <div class="crm-row-actions">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-crm-edit="company" data-crm-payload='${payload}'>Editar</button>
                    <button type="button" class="btn btn-sm btn-outline-danger" data-crm-delete="company" data-crm-url="${escapeHtml(co.destroy_url)}" data-crm-id="${co.id}">Eliminar</button>
                </div>
            </div>`;
    }

    function activityCardHtml(a) {
        const payload = attrJson(a);
        const due = a.due_date ? ` · ${escapeHtml(a.due_date)}` : '';
        return `
            <div class="crm-activity-card" data-crm-activity-row="${a.id}">
                <div class="d-flex justify-content-between">
                    <div class="crm-activity-title">${escapeHtml(a.title)}</div>
                    <span class="crm-activity-type">${escapeHtml(a.type_label)}</span>
                </div>
                <div class="crm-activity-meta">${escapeHtml(a.meta)}${due}</div>
                <div class="crm-activity-status">${escapeHtml(a.status_label)}</div>
                <div class="crm-row-actions mt-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-crm-edit="activity" data-crm-payload='${payload}'>Editar</button>
                    <button type="button" class="btn btn-sm btn-outline-danger" data-crm-delete="activity" data-crm-url="${escapeHtml(a.destroy_url)}" data-crm-id="${a.id}">Eliminar</button>
                </div>
            </div>`;
    }

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
        }
    }

    function upsertCompany(co) {
        const list = document.getElementById('crmCompaniesList');
        const existing = list.querySelector(`[data-crm-company-row="${co.id}"]`);
        const html = companyItemHtml(co);
        if (existing) {
            existing.outerHTML = html;
        } else {
            document.getElementById('crmCompaniesEmpty')?.remove();
            list.insertAdjacentHTML('afterbegin', html);
            bumpStat('crmStatCompanies', 1);
        }
        refreshCompanySelects(co);
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
        if (existing) {
            existing.outerHTML = html;
        } else {
            document.getElementById('crmActivitiesEmpty')?.remove();
            list.insertAdjacentHTML('afterbegin', html);
            if (a.status !== 'completed') bumpStat('crmStatPending', 1);
        }
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
            } else if (type === 'company') {
                document.querySelector(`[data-crm-company-row="${id}"]`)?.remove();
                bumpStat('crmStatCompanies', -1);
            } else if (type === 'deal') {
                window.location.reload();
            } else if (type === 'activity') {
                const row = document.querySelector(`[data-crm-activity-row="${id}"]`);
                const wasPending = row?.querySelector('.crm-activity-status')?.textContent !== 'Completada';
                row?.remove();
                if (wasPending) bumpStat('crmStatPending', -1);
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

    root.addEventListener('click', (e) => {
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

    Object.keys(forms).forEach((type) => {
        const form = forms[type];
        if (!form) return;
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            submitForm(type);
        });
    });
})();
