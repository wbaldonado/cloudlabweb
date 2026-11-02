<div class="modal fade" id="createCompanyModal" tabindex="-1" aria-labelledby="createCompanyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCompanyModalLabel">Nueva empresa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="crmCreateCompanyForm" action="{{ route('admin.crm.companies.store') }}" method="POST">
                <div class="modal-body">
                    @include('admin.crm._company-form')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dark" id="crmCreateCompanySubmit">Guardar empresa</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('crmCreateCompanyForm');
    const errorsBox = document.getElementById('crmCompanyFormErrors');
    const submitBtn = document.getElementById('crmCreateCompanySubmit');

    if (!form) return;

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        errorsBox.classList.add('d-none');
        errorsBox.innerHTML = '';
        submitBtn.disabled = true;

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new FormData(form),
            });

            const data = await response.json();

            if (!response.ok) {
                const messages = data.errors
                    ? Object.values(data.errors).flat().join('<br>')
                    : (data.message || 'No se pudo crear la empresa.');
                errorsBox.innerHTML = messages;
                errorsBox.classList.remove('d-none');
                return;
            }

            const c = data.company;

            // Update companies table if present
            const tableBody = document.getElementById('crmCompaniesTableBody');
            if (tableBody) {
                const emptyRow = tableBody.querySelector('td[colspan]');
                if (emptyRow) emptyRow.closest('tr').remove();

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${c.id}</td>
                    <td><strong>${c.name}</strong></td>
                    <td>${c.industry || '—'}</td>
                    <td>${c.phone || '—'}</td>
                    <td>${c.city || '—'}</td>
                    <td>${c.country || '—'}</td>
                    <td>${new Date().toLocaleDateString('es-ES', { month: 'short', day: 'numeric', year: 'numeric' })}</td>
                    <td><a href="${c.show_url}" class="btn btn-sm btn-outline-primary">Ver</a></td>
                `;
                tableBody.prepend(row);
            }

            // Update dashboard companies list if present
            const companiesList = document.getElementById('crmCompaniesList');
            if (companiesList) {
                const emptyMsg = companiesList.querySelector('.text-muted');
                if (emptyMsg) emptyMsg.remove();

                const item = document.createElement('a');
                item.href = c.show_url;
                item.className = 'crm-list-item';
                item.innerHTML = `
                    <div>
                        <div class="crm-list-title">${c.name}</div>
                        <div class="crm-list-meta">${c.meta}</div>
                    </div>
                    <span class="crm-badge crm-badge--qualified">Activo</span>
                `;
                companiesList.prepend(item);
            }

            // Add to contact form company dropdown if present
            const companySelect = document.querySelector('#crmCreateContactForm select[name="company_id"]');
            if (companySelect) {
                const option = document.createElement('option');
                option.value = c.id;
                option.textContent = c.name;
                option.selected = true;
                companySelect.appendChild(option);
            }

            // Update stat card if present
            const statCompanies = document.getElementById('crmStatCompanies');
            if (statCompanies) {
                statCompanies.textContent = parseInt(statCompanies.textContent || '0', 10) + 1;
            }

            form.reset();
            bootstrap.Modal.getInstance(document.getElementById('createCompanyModal')).hide();
        } catch (err) {
            errorsBox.textContent = 'Error de conexión. Intenta de nuevo.';
            errorsBox.classList.remove('d-none');
        } finally {
            submitBtn.disabled = false;
        }
    });
});
</script>
@endpush
