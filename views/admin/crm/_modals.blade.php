{{-- Contact modal --}}
<div class="modal fade" id="contactModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contactModalTitle">Nuevo contacto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="crmContactForm" method="POST" action="{{ route('admin.crm.contacts.store') }}">
                @csrf
                <div class="modal-body">@include('admin.crm._contact-form')</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dark">Guardar contacto</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Company modal --}}
<div class="modal fade" id="companyModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="companyModalTitle">Nueva empresa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="crmCompanyForm" method="POST" action="{{ route('admin.crm.companies.store') }}">
                @csrf
                <div class="modal-body">@include('admin.crm._company-form')</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dark">Guardar empresa</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Deal modal --}}
<div class="modal fade" id="dealModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dealModalTitle">Nueva oportunidad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="crmDealForm" method="POST" action="{{ route('admin.crm.deals.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Título *</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Monto (USD)</label>
                            <input type="number" name="amount" class="form-control" min="0" step="0.01" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Probabilidad (%)</label>
                            <input type="number" name="probability" class="form-control" min="0" max="100" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Etapa *</label>
                            <select name="stage" class="form-select" required>
                                <option value="prospecting">Prospección</option>
                                <option value="qualified">Calificación</option>
                                <option value="proposal">Propuesta</option>
                                <option value="negotiation">Negociación</option>
                                <option value="won">Ganada</option>
                                <option value="lost">Perdida</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha cierre estimada</label>
                            <input type="date" name="expected_close_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Empresa</label>
                            <select name="company_id" class="form-select">
                                <option value="">—</option>
                                @foreach($companiesList as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contacto</label>
                            <select name="contact_id" class="form-select">
                                <option value="">—</option>
                                @foreach($contactsList as $contact)
                                    <option value="{{ $contact->id }}">{{ $contact->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Descripción</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div id="crmDealFormErrors" class="alert alert-danger mt-3 d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dark">Guardar oportunidad</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Activity modal --}}
<div class="modal fade" id="activityModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="activityModalTitle">Nueva actividad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="crmActivityForm" method="POST" action="{{ route('admin.crm.activities.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Título *</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tipo *</label>
                            <select name="type" class="form-select" required>
                                <option value="task">Tarea</option>
                                <option value="email">Correo</option>
                                <option value="meeting">Reunión</option>
                                <option value="call">Llamada</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estado *</label>
                            <select name="status" class="form-select" required>
                                <option value="not_started">No iniciada</option>
                                <option value="in_progress">En progreso</option>
                                <option value="blocked">Bloqueada</option>
                                <option value="completed">Completada</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha</label>
                            <input type="date" name="due_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Empresa</label>
                            <select name="company_id" class="form-select">
                                <option value="">—</option>
                                @foreach($companiesList as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contacto</label>
                            <select name="contact_id" class="form-select">
                                <option value="">—</option>
                                @foreach($contactsList as $contact)
                                    <option value="{{ $contact->id }}">{{ $contact->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notas</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div id="crmActivityFormErrors" class="alert alert-danger mt-3 d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dark">Guardar actividad</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- CSV import --}}
<div class="modal fade" id="crmImportModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Importar CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="crmImportForm" method="POST" action="{{ route('admin.crm.import.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Sube un CSV (máx. ~15&nbsp;MB / 25.000 filas, UTF-8). Compatible con la plantilla CRM y con
                        exportaciones de <strong>Cámara de Comercio</strong> (RAZON SOCIAL, NIT, DIR-COMERCIAL, etc.).
                        Empresas: se actualizan por nombre. Contactos: por email.
                    </p>
                    <div class="mb-3">
                        <label class="form-label">Tipo de importación *</label>
                        <select name="type" id="crmImportType" class="form-select" required>
                            <option value="companies">Empresas (incl. Cámara de Comercio)</option>
                            <option value="contacts">Contactos</option>
                        </select>
                    </div>
                    <div class="form-check mb-3" id="crmImportWithContactsWrap">
                        <input class="form-check-input" type="checkbox" value="1" id="crmImportWithContacts" name="with_contacts">
                        <label class="form-check-label" for="crmImportWithContacts">
                            También crear contactos (email comercial / representante legal)
                        </label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Archivo CSV *</label>
                        <input type="file" name="csv_file" id="crmImportFile" class="form-control" accept=".csv,text/csv,text/plain" required>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.crm.import.template', 'contacts') }}">
                            <i class="bi bi-download"></i> Plantilla contactos
                        </a>
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.crm.import.template', 'companies') }}">
                            <i class="bi bi-download"></i> Plantilla empresas
                        </a>
                    </div>
                    <div id="crmImportFormErrors" class="alert alert-danger d-none"></div>
                    <div id="crmImportFormResult" class="alert alert-success d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dark" id="crmImportSubmit">
                        <i class="bi bi-upload"></i> Importar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Send email to contact --}}
<div class="modal fade" id="emailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emailModalTitle">Enviar correo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="crmEmailForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Para</label>
                        <input type="text" id="crmEmailTo" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Asunto *</label>
                        <input type="text" name="subject" class="form-control" required maxlength="255">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mensaje *</label>
                        <textarea name="body" class="form-control" rows="8" required maxlength="20000"></textarea>
                    </div>
                    <div id="crmEmailFormErrors" class="alert alert-danger d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dark" id="crmEmailSubmit">Enviar correo</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Campaign history for a contact --}}
<div class="modal fade" id="campaignHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0" id="campaignHistoryTitle">Historial de campañas</h5>
                    <div class="small text-muted" id="campaignHistorySubtitle"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="crm-history-summary" id="campaignHistorySummary"></div>
                <div id="campaignHistoryStatus" class="text-center text-muted py-4">Cargando…</div>
                <div class="table-responsive d-none" id="campaignHistoryTableWrap">
                    <table class="table table-hover align-middle mb-0 crm-history-table">
                        <thead class="table-light">
                            <tr>
                                <th>Campaña</th>
                                <th>Estado</th>
                                <th>Enviado</th>
                                <th>Aperturas</th>
                                <th>Clics</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="campaignHistoryBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

