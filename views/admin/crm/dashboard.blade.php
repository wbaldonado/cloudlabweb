@extends('admin.layouts.app')

@section('title', 'CRM - Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ \App\Support\Branding::assetUrl('css/crm-dashboard.css') }}?v=tabs-2">
@endpush

@section('content')
@php
    $openDeals = $deals->whereNotIn('stage', ['won', 'lost']);
    $closedDeals = $deals->whereIn('stage', ['won', 'lost']);
    $sortedDeals = $openDeals->sortByDesc('amount')->concat($closedDeals->sortByDesc('updated_at'));
    $stageOrder = [
        'prospecting' => ['Prospección', 'crm-badge--prospecting'],
        'qualified' => ['Calificación', 'crm-badge--qualified'],
        'proposal' => ['Propuesta', 'crm-badge--proposal'],
        'negotiation' => ['Negociación', 'crm-badge--negotiation'],
    ];
    $today = now()->startOfDay();
    $typeIcons = ['email' => 'bi-envelope', 'meeting' => 'bi-camera-video', 'call' => 'bi-telephone', 'task' => 'bi-check2-square'];
    $pendingActivities = $activities->where('status', '!=', 'completed');
    $completedActivities = $activities->where('status', 'completed');
    $sortedActivities = $pendingActivities
        ->sortBy(fn ($a) => $a->due_date ? $a->due_date->format('Y-m-d') : '9999-12-31')
        ->concat($completedActivities->sortByDesc('updated_at'));
    $overdueCount = $pendingActivities->filter(fn ($a) => $a->due_date && $a->due_date->lt($today))->count();
    $recentReplies = $recentReplies ?? collect();
    $contactBulkUrl = route('admin.crm.contacts.store');
    $companyBulkUrl = route('admin.crm.companies.store');
    $activeTab = request()->has('companies_page') ? 'empresas' : 'contactos';
    $hasContactFilters = trim((string) $search) !== '' || trim((string) $stageFilter) !== '';
    $fmtDate = fn ($value) => $value ? \Carbon\Carbon::parse($value)->format('d/m/Y') : '—';
    $campaignsUrl = \Illuminate\Support\Facades\Route::has('admin.crm.campaigns.index') ? route('admin.crm.campaigns.index') : url('/admin/crm/campaigns');
@endphp

<div class="crm-dashboard" id="crmDashboard"
     data-contact-store="{{ route('admin.crm.contacts.store') }}"
     data-company-store="{{ route('admin.crm.companies.store') }}"
     data-deal-store="{{ route('admin.crm.deals.store') }}"
     data-activity-store="{{ route('admin.crm.activities.store') }}"
     data-import-store="{{ route('admin.crm.import.store') }}"
     data-contact-bulk-destroy="{{ $contactBulkUrl }}"
     data-company-bulk-destroy="{{ $companyBulkUrl }}"
     data-contacts-total="{{ $contacts->total() }}"
     data-companies-total="{{ $companies->total() }}"
     data-contacts-q="{{ $search }}"
     data-contacts-stage="{{ $stageFilter }}">

    <div class="crm-topbar">
        <div>
            <span class="crm-pill">CloudLab Latam</span>
            <h1 class="crm-hero-title">CRM comercial</h1>
            <p class="crm-hero-sub">Hola, {{ auth()->user()->name }}. Contactos, empresas, oportunidades y actividades en un solo panel.</p>
        </div>
        <div class="crm-topbar-actions">
            <a href="{{ $campaignsUrl }}" class="btn btn-dark">
                <i class="bi bi-megaphone"></i> Campañas email
            </a>
        </div>
    </div>

    <div id="crmFlash" class="alert alert-success d-none" role="alert"></div>

    <div class="crm-stats">
        <div class="crm-stat-card">
            <div class="crm-stat-icon"><i class="bi bi-people"></i></div>
            <div>
                <div class="crm-stat-value" id="crmStatContacts">{{ $stats['contacts'] }}</div>
                <div class="crm-stat-label">Contactos</div>
                <div class="crm-stat-meta">Base comercial</div>
            </div>
        </div>
        <div class="crm-stat-card">
            <div class="crm-stat-icon"><i class="bi bi-building"></i></div>
            <div>
                <div class="crm-stat-value" id="crmStatCompanies">{{ $stats['companies'] }}</div>
                <div class="crm-stat-label">Empresas</div>
                <div class="crm-stat-meta">Cuentas y prospectos</div>
            </div>
        </div>
        <div class="crm-stat-card">
            <div class="crm-stat-icon"><i class="bi bi-funnel"></i></div>
            <div>
                <div class="crm-stat-value" id="crmPipelineTotal">USD {{ number_format($stats['pipeline_total'], 0) }}</div>
                <div class="crm-stat-label">Pipeline abierto</div>
                <div class="crm-stat-meta">{{ $openDeals->count() }} oportunidades · ponderado <span id="crmPipelineWeighted">USD {{ number_format($stats['pipeline_weighted'], 0) }}</span></div>
            </div>
        </div>
        <div class="crm-stat-card">
            <div class="crm-stat-icon"><i class="bi bi-currency-dollar"></i></div>
            <div>
                <div class="crm-stat-value" id="crmStatPayments">USD {{ number_format($stats['total_payments'], 0) }}</div>
                <div class="crm-stat-label">Ganado</div>
                <div class="crm-stat-meta">Oportunidades cerradas</div>
            </div>
        </div>
        <div class="crm-stat-card">
            <div class="crm-stat-icon"><i class="bi bi-calendar-check"></i></div>
            <div>
                <div class="crm-stat-value" id="crmStatPending">{{ $stats['pending_activities'] }}</div>
                <div class="crm-stat-label">Actividades pendientes</div>
                <div class="crm-stat-meta {{ $overdueCount ? 'text-danger' : '' }}">{{ $overdueCount ? $overdueCount . ' vencidas' : 'Sin vencidas' }}</div>
            </div>
        </div>
    </div>

    <ul class="nav crm-tabs" id="crmTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'contactos' ? 'active' : '' }}" id="crmTabBtnContactos" data-bs-toggle="tab" data-bs-target="#crmTabContactos" data-crm-tab="contactos" type="button" role="tab">
                <i class="bi bi-people"></i> Contactos <span class="crm-tab-count" id="crmTabCountContacts">{{ $contacts->total() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'empresas' ? 'active' : '' }}" id="crmTabBtnEmpresas" data-bs-toggle="tab" data-bs-target="#crmTabEmpresas" data-crm-tab="empresas" type="button" role="tab">
                <i class="bi bi-building"></i> Empresas <span class="crm-tab-count" id="crmTabCountCompanies">{{ $companies->total() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="crmTabBtnOportunidades" data-bs-toggle="tab" data-bs-target="#crmTabOportunidades" data-crm-tab="oportunidades" type="button" role="tab">
                <i class="bi bi-funnel"></i> Oportunidades <span class="crm-tab-count">{{ $openDeals->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="crmTabBtnActividades" data-bs-toggle="tab" data-bs-target="#crmTabActividades" data-crm-tab="actividades" type="button" role="tab">
                <i class="bi bi-calendar-check"></i> Actividades <span class="crm-tab-count" id="crmTabCountActivities">{{ $pendingActivities->count() }}</span>
            </button>
        </li>
        @if($recentReplies->isNotEmpty())
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="crmTabBtnBandeja" data-bs-toggle="tab" data-bs-target="#crmTabBandeja" data-crm-tab="bandeja" type="button" role="tab">
                <i class="bi bi-inbox"></i> Respuestas
                @if(($stats['email_replies'] ?? 0) > 0)
                <span class="crm-tab-count crm-tab-count--alert">{{ $stats['email_replies'] }}</span>
                @endif
            </button>
        </li>
        @endif
    </ul>

    <div class="tab-content crm-tab-content">

        {{-- Contactos --}}
        <div class="tab-pane fade {{ $activeTab === 'contactos' ? 'show active' : '' }}" id="crmTabContactos" role="tabpanel">
            <div class="crm-panel">
                <div class="crm-toolbar">
                    <form method="GET" action="{{ route('admin.crm.dashboard') }}" class="crm-filters">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="search" name="q" value="{{ $search }}" class="form-control" placeholder="Buscar por nombre, email, empresa o cargo">
                        </div>
                        <select name="stage" class="form-select" onchange="this.form.submit()" aria-label="Filtrar por estado">
                            <option value="">Todos los estados</option>
                            <option value="lead" @selected($stageFilter == 'lead')>Nuevo</option>
                            <option value="mql" @selected($stageFilter == 'mql')>Contactado</option>
                            <option value="sql" @selected($stageFilter == 'sql')>Calificado</option>
                            <option value="customer" @selected($stageFilter == 'customer')>Cliente</option>
                        </select>
                        @if($hasContactFilters)
                        <a href="{{ route('admin.crm.dashboard') }}" class="btn btn-outline-secondary" title="Quitar filtros" aria-label="Quitar filtros"><i class="bi bi-x-lg"></i></a>
                        @endif
                    </form>
                    <div class="crm-toolbar-actions">
                        <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#crmImportModal">
                            <i class="bi bi-upload"></i> Importar CSV
                        </button>
                        <button type="button" class="btn btn-dark btn-sm" data-crm-open="contact">
                            <i class="bi bi-plus-lg"></i> Nuevo contacto
                        </button>
                    </div>
                </div>

                <form method="POST" action="{{ $contactBulkUrl }}" id="crmContactsBulkForm">
                    @csrf
                    <input type="hidden" name="_bulk" value="1">
                    <input type="hidden" name="all" id="crmContactsAllField" value="0">
                    <input type="hidden" name="q" value="{{ $search }}">
                    <input type="hidden" name="stage" value="{{ $stageFilter }}">

                    <div class="crm-bulk-bar" id="crmContactsBulkBar">
                        <div class="crm-bulk-bar-info">
                            <span id="crmContactsSelectedCount">Ningún contacto seleccionado</span>
                            <button type="button" class="btn btn-link btn-sm p-0 {{ $contacts->total() ? '' : 'd-none' }}" id="crmSelectAllMatchingContacts">
                                Seleccionar todos ({{ $contacts->total() }})
                            </button>
                            <span class="small text-muted d-none" id="crmContactsMatchingNote">Todos los contactos de esta búsqueda están seleccionados.</span>
                        </div>
                        <button type="submit" class="btn btn-sm btn-outline-danger" id="crmDeleteSelectedContacts">
                            Eliminar seleccionados
                        </button>
                    </div>

                    <div class="table-responsive crm-table-wrap">
                        <table class="table crm-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="crm-select-col">
                                        <input type="checkbox" class="form-check-input" id="crmSelectAllContacts" @disabled($contacts->isEmpty()) title="Seleccionar todos en esta página" aria-label="Seleccionar todos los contactos de esta página">
                                    </th>
                                    <th>Nombre</th>
                                    <th>Empresa</th>
                                    <th>Estado</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Campañas</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="crmContactsTableBody">
                                @forelse($contacts as $contact)
                                @php
                                    $c = \App\Support\CrmPresenter::contact($contact);
                                    $cs = $campaignStats[$contact->id] ?? null;
                                @endphp
                                <tr data-crm-contact-row="{{ $contact->id }}">
                                    <td class="crm-select-col">
                                        <input type="checkbox" class="form-check-input" name="ids[]" data-crm-select="contact" value="{{ $contact->id }}" aria-label="Seleccionar {{ $c['full_name'] }}">
                                    </td>
                                    <td class="crm-cell-primary">
                                        <div class="crm-cell-title">{{ $c['full_name'] }}</div>
                                        @if($c['job_title'])<div class="crm-cell-sub">{{ $c['job_title'] }}</div>@endif
                                    </td>
                                    <td class="crm-cell-truncate" title="{{ $c['company'] }}">{{ $c['company'] ?: '—' }}</td>
                                    <td><span class="crm-badge {{ $c['stage_class'] }}">{{ $c['stage_label'] }}</span></td>
                                    <td class="crm-cell-truncate">
                                        @if($c['email'])<a href="mailto:{{ $c['email'] }}" class="crm-link" title="{{ $c['email'] }}">{{ $c['email'] }}</a>@else <span class="text-muted">—</span> @endif
                                    </td>
                                    <td class="crm-nowrap">
                                        @if($c['phone'])<a href="tel:{{ preg_replace('/\s+/', '', $c['phone']) }}" class="crm-link">{{ $c['phone'] }}</a>@else <span class="text-muted">—</span> @endif
                                    </td>
                                    <td class="crm-nowrap" data-crm-campaign-cell>
                                        @if($cs && $cs['total'] > 0)
                                        <button type="button" class="crm-campaign-pill" data-crm-campaigns data-crm-campaigns-url="{{ route('admin.crm.contacts.campaigns', $contact) }}" data-crm-campaigns-name="{{ $c['full_name'] }}" title="Ver historial de campañas">
                                            <i class="bi bi-megaphone"></i>
                                            <span>{{ $cs['sent'] }}</span>
                                            @if($cs['opened'] > 0)<span class="crm-campaign-pill-meta crm-campaign-pill-meta--open" title="{{ $cs['opened'] }} abiertas"><i class="bi bi-envelope-open"></i> {{ $cs['opened'] }}</span>@endif
                                            @if($cs['clicked'] > 0)<span class="crm-campaign-pill-meta crm-campaign-pill-meta--click" title="{{ $cs['clicked'] }} con clic"><i class="bi bi-cursor"></i> {{ $cs['clicked'] }}</span>@endif
                                            @if($cs['failed'] > 0)<span class="crm-campaign-pill-meta crm-campaign-pill-meta--fail" title="{{ $cs['failed'] }} fallidas"><i class="bi bi-exclamation-circle"></i> {{ $cs['failed'] }}</span>@endif
                                        </button>
                                        @else
                                        <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="crm-actions">
                                            @if($c['send_email_url'])
                                            <button type="button" class="crm-icon-btn" title="Enviar correo" aria-label="Enviar correo a {{ $c['full_name'] }}" data-crm-email data-crm-email-url="{{ $c['send_email_url'] }}" data-crm-email-to="{{ $c['email'] }}" data-crm-email-name="{{ $c['full_name'] }}"><i class="bi bi-envelope"></i></button>
                                            @endif
                                            <button type="button" class="crm-icon-btn" title="Editar" aria-label="Editar {{ $c['full_name'] }}" data-crm-edit="contact" data-crm-payload='@json($c)'><i class="bi bi-pencil"></i></button>
                                            <button type="button" class="crm-icon-btn crm-icon-btn--danger" title="Eliminar" aria-label="Eliminar {{ $c['full_name'] }}" data-crm-delete="contact" data-crm-url="{{ $c['destroy_url'] }}" data-crm-id="{{ $contact->id }}"><i class="bi bi-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr id="crmContactsEmpty">
                                    <td colspan="8" class="crm-empty">
                                        @if($hasContactFilters)
                                        Ningún contacto coincide con la búsqueda.
                                        @else
                                        No hay contactos. Crea el primero con el botón "Nuevo contacto".
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>
                <div class="crm-table-foot">
                    <span class="crm-table-count">
                        @if($contacts->total())
                        Mostrando {{ $contacts->firstItem() }}–{{ $contacts->lastItem() }} de {{ $contacts->total() }}
                        @endif
                    </span>
                    <div class="crm-pagination">{{ $contacts->fragment('contactos')->links('pagination::bootstrap-5') }}</div>
                </div>
            </div>
        </div>

        {{-- Empresas --}}
        <div class="tab-pane fade {{ $activeTab === 'empresas' ? 'show active' : '' }}" id="crmTabEmpresas" role="tabpanel">
            <div class="crm-panel">
                <div class="crm-toolbar">
                    <div>
                        <h2 class="crm-panel-title">Empresas</h2>
                        <p class="crm-panel-sub">Cuentas y prospectos ordenados por nombre.</p>
                    </div>
                    <div class="crm-toolbar-actions">
                        <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#crmImportModal">
                            <i class="bi bi-upload"></i> Importar CSV
                        </button>
                        <button type="button" class="btn btn-dark btn-sm" data-crm-open="company">
                            <i class="bi bi-plus-lg"></i> Nueva empresa
                        </button>
                    </div>
                </div>

                <form method="POST" action="{{ $companyBulkUrl }}" id="crmCompaniesBulkForm">
                    @csrf
                    <input type="hidden" name="_bulk" value="1">
                    <input type="hidden" name="all" id="crmCompaniesAllField" value="0">

                    <div class="crm-bulk-bar" id="crmCompaniesBulkBar">
                        <div class="crm-bulk-bar-info">
                            <span id="crmCompaniesSelectedCount">Ninguna empresa seleccionada</span>
                            <button type="button" class="btn btn-link btn-sm p-0 {{ $companies->total() ? '' : 'd-none' }}" id="crmSelectAllMatchingCompanies">
                                Seleccionar todas ({{ $companies->total() }})
                            </button>
                            <span class="small text-muted d-none" id="crmCompaniesMatchingNote">Todas las empresas están seleccionadas.</span>
                        </div>
                        <button type="submit" class="btn btn-sm btn-outline-danger" id="crmDeleteSelectedCompanies">
                            Eliminar seleccionadas
                        </button>
                    </div>

                    <div class="table-responsive crm-table-wrap">
                        <table class="table crm-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="crm-select-col">
                                        <input type="checkbox" class="form-check-input" id="crmSelectAllCompanies" @disabled($companies->isEmpty()) title="Seleccionar todas en esta página" aria-label="Seleccionar todas las empresas de esta página">
                                    </th>
                                    <th>Empresa</th>
                                    <th>Industria</th>
                                    <th>Ciudad</th>
                                    <th>País</th>
                                    <th>Teléfono</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="crmCompaniesTableBody">
                                @forelse($companies as $company)
                                @php $co = \App\Support\CrmPresenter::company($company); @endphp
                                <tr data-crm-company-row="{{ $company->id }}">
                                    <td class="crm-select-col">
                                        <input type="checkbox" class="form-check-input" name="ids[]" data-crm-select="company" value="{{ $company->id }}" aria-label="Seleccionar {{ $co['name'] }}">
                                    </td>
                                    <td class="crm-cell-primary">
                                        <div class="crm-cell-title">{{ $co['name'] }}</div>
                                        @if($co['website'])
                                        <div class="crm-cell-sub"><a href="{{ $co['website'] }}" target="_blank" rel="noopener" class="crm-link">{{ preg_replace('#^https?://#i', '', $co['website']) }}</a></div>
                                        @endif
                                    </td>
                                    <td class="crm-cell-truncate" title="{{ $co['industry'] }}">{{ $co['industry'] ?: '—' }}</td>
                                    <td class="crm-nowrap">{{ $co['city'] ?: '—' }}</td>
                                    <td class="crm-nowrap">{{ $co['country'] ?: '—' }}</td>
                                    <td class="crm-nowrap">{{ $co['phone'] ?: '—' }}</td>
                                    <td class="text-end">
                                        <div class="crm-actions">
                                            <button type="button" class="crm-icon-btn" title="Editar" aria-label="Editar {{ $co['name'] }}" data-crm-edit="company" data-crm-payload='@json($co)'><i class="bi bi-pencil"></i></button>
                                            <button type="button" class="crm-icon-btn crm-icon-btn--danger" title="Eliminar" aria-label="Eliminar {{ $co['name'] }}" data-crm-delete="company" data-crm-url="{{ $co['destroy_url'] }}" data-crm-id="{{ $company->id }}"><i class="bi bi-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr id="crmCompaniesEmpty">
                                    <td colspan="7" class="crm-empty">Sin empresas registradas.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>
                <div class="crm-table-foot">
                    <span class="crm-table-count">
                        @if($companies->total())
                        Mostrando {{ $companies->firstItem() }}–{{ $companies->lastItem() }} de {{ $companies->total() }}
                        @endif
                    </span>
                    <div class="crm-pagination">{{ $companies->fragment('empresas')->links('pagination::bootstrap-5') }}</div>
                </div>
            </div>
        </div>

        {{-- Oportunidades --}}
        <div class="tab-pane fade" id="crmTabOportunidades" role="tabpanel">
            <div class="crm-panel">
                <div class="crm-toolbar">
                    <div>
                        <h2 class="crm-panel-title">Oportunidades</h2>
                        <p class="crm-panel-sub">Abiertas primero, de mayor a menor monto. Ganadas y perdidas al final.</p>
                    </div>
                    <div class="crm-toolbar-actions">
                        <button type="button" class="btn btn-dark btn-sm" data-crm-open="deal">
                            <i class="bi bi-plus-lg"></i> Nueva oportunidad
                        </button>
                    </div>
                </div>

                <div class="crm-stage-chips">
                    @foreach($stageOrder as $stageKey => [$stageLabel, $stageClass])
                    @php $group = $openDeals->where('stage', $stageKey); @endphp
                    <div class="crm-stage-chip">
                        <span class="crm-badge {{ $stageClass }}">{{ $stageLabel }}</span>
                        <span class="crm-stage-chip-count">{{ $group->count() }}</span>
                        <span class="crm-stage-chip-amount">USD {{ number_format($group->sum('amount'), 0) }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="crm-segmented" role="group" aria-label="Filtrar oportunidades" data-crm-filter-group="deals">
                    <button type="button" class="active" data-crm-filter="open">Abiertas <span>{{ $openDeals->count() }}</span></button>
                    <button type="button" data-crm-filter="won">Ganadas <span>{{ $deals->where('stage', 'won')->count() }}</span></button>
                    <button type="button" data-crm-filter="lost">Perdidas <span>{{ $deals->where('stage', 'lost')->count() }}</span></button>
                    <button type="button" data-crm-filter="all">Todas <span>{{ $deals->count() }}</span></button>
                </div>

                <div class="table-responsive crm-table-wrap">
                    <table class="table crm-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Oportunidad</th>
                                <th>Empresa</th>
                                <th>Etapa</th>
                                <th class="crm-num">Monto</th>
                                <th>Probabilidad</th>
                                <th class="crm-num">Ponderado</th>
                                <th>Cierre estimado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="crmDealsTableBody" data-crm-filter-target="deals">
                            @forelse($sortedDeals as $deal)
                            @php
                                $d = \App\Support\CrmPresenter::deal($deal);
                                $state = $deal->stage === 'won' ? 'won' : ($deal->stage === 'lost' ? 'lost' : 'open');
                                $closeOverdue = $state === 'open' && $deal->expected_close_date && $deal->expected_close_date->lt($today);
                            @endphp
                            <tr data-crm-deal-row="{{ $deal->id }}" data-crm-state="{{ $state }}" class="{{ $state !== 'open' ? 'crm-row-muted' : '' }} {{ $state !== 'open' ? 'd-none' : '' }}">
                                <td class="crm-cell-primary">
                                    <div class="crm-cell-title">{{ $d['title'] }}</div>
                                    @if($d['contact'])<div class="crm-cell-sub">{{ $d['contact'] }}</div>@endif
                                </td>
                                <td class="crm-cell-truncate" title="{{ $d['company'] }}">{{ $d['company'] ?: '—' }}</td>
                                <td><span class="crm-badge {{ $d['stage_class'] }}">{{ $d['stage_label'] }}</span></td>
                                <td class="crm-num">USD {{ number_format($d['amount'], 0) }}</td>
                                <td>
                                    <div class="crm-prob">
                                        <div class="crm-progress"><div class="crm-progress-bar" style="width: {{ $d['probability'] }}%"></div></div>
                                        <span>{{ $d['probability'] }}%</span>
                                    </div>
                                </td>
                                <td class="crm-num">USD {{ number_format($d['weighted'], 0) }}</td>
                                <td class="crm-nowrap {{ $closeOverdue ? 'crm-date--overdue' : '' }}">
                                    {{ $fmtDate($d['expected_close_date']) }}
                                    @if($closeOverdue)<span class="crm-date-flag">Vencida</span>@endif
                                </td>
                                <td class="text-end">
                                    <div class="crm-actions">
                                        <button type="button" class="crm-icon-btn" title="Editar" aria-label="Editar {{ $d['title'] }}" data-crm-edit="deal" data-crm-payload='@json($d)'><i class="bi bi-pencil"></i></button>
                                        <button type="button" class="crm-icon-btn crm-icon-btn--danger" title="Eliminar" aria-label="Eliminar {{ $d['title'] }}" data-crm-delete="deal" data-crm-url="{{ $d['destroy_url'] }}" data-crm-id="{{ $deal->id }}"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr id="crmDealsEmpty">
                                <td colspan="8" class="crm-empty">Sin oportunidades. Crea la primera con "Nueva oportunidad".</td>
                            </tr>
                            @endforelse
                            @if($deals->isNotEmpty())
                            <tr class="crm-filter-empty d-none" data-crm-filter-empty="deals">
                                <td colspan="8" class="crm-empty">No hay oportunidades en este filtro.</td>
                            </tr>
                            @endif
                        </tbody>
                        @if($openDeals->isNotEmpty())
                        <tfoot data-crm-filter-foot="deals">
                            <tr>
                                <td colspan="3" class="crm-foot-label">Total pipeline abierto ({{ $openDeals->count() }})</td>
                                <td class="crm-num">USD {{ number_format($stats['pipeline_total'], 0) }}</td>
                                <td></td>
                                <td class="crm-num">USD {{ number_format($stats['pipeline_weighted'], 0) }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        {{-- Actividades --}}
        <div class="tab-pane fade" id="crmTabActividades" role="tabpanel">
            <div class="crm-panel">
                <div class="crm-toolbar">
                    <div>
                        <h2 class="crm-panel-title">Actividades</h2>
                        <p class="crm-panel-sub">Pendientes primero, ordenadas por fecha. Las vencidas se marcan en rojo.</p>
                    </div>
                    <div class="crm-toolbar-actions">
                        <button type="button" class="btn btn-dark btn-sm" data-crm-open="activity">
                            <i class="bi bi-plus-lg"></i> Nueva actividad
                        </button>
                    </div>
                </div>

                <div class="crm-segmented" role="group" aria-label="Filtrar actividades" data-crm-filter-group="activities">
                    <button type="button" class="active" data-crm-filter="pending">Pendientes <span id="crmActivitiesPendingCount">{{ $pendingActivities->count() }}</span></button>
                    <button type="button" data-crm-filter="completed">Completadas <span>{{ $completedActivities->count() }}</span></button>
                    <button type="button" data-crm-filter="all">Todas <span>{{ $activities->count() }}</span></button>
                </div>

                <div class="table-responsive crm-table-wrap">
                    <table class="table crm-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Actividad</th>
                                <th>Tipo</th>
                                <th>Relacionado con</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="crmActivitiesList" data-crm-filter-target="activities">
                            @forelse($sortedActivities as $activity)
                            @php
                                $a = \App\Support\CrmPresenter::activity($activity);
                                $state = $activity->status === 'completed' ? 'completed' : 'pending';
                                $overdue = $state === 'pending' && $activity->due_date && $activity->due_date->lt($today);
                            @endphp
                            <tr data-crm-activity-row="{{ $activity->id }}" data-crm-state="{{ $state }}" class="{{ $state === 'completed' ? 'crm-row-muted d-none' : '' }}">
                                <td class="crm-cell-primary">
                                    <div class="crm-cell-title">{{ $a['title'] }}</div>
                                    @if($a['notes'])<div class="crm-cell-sub crm-cell-truncate" title="{{ $a['notes'] }}">{{ \Illuminate\Support\Str::limit($a['notes'], 90) }}</div>@endif
                                </td>
                                <td><span class="crm-type-chip"><i class="bi {{ $typeIcons[$a['type']] ?? 'bi-check2-square' }}"></i> {{ $a['type_label'] }}</span></td>
                                <td class="crm-cell-truncate" title="{{ $a['meta'] }}">{{ $a['meta'] }}</td>
                                <td class="crm-nowrap {{ $overdue ? 'crm-date--overdue' : '' }}">
                                    {{ $fmtDate($a['due_date']) }}
                                    @if($overdue)<span class="crm-date-flag">Vencida</span>@endif
                                </td>
                                <td><span class="crm-badge crm-status--{{ $a['status'] }} crm-activity-status">{{ $a['status_label'] }}</span></td>
                                <td class="text-end">
                                    <div class="crm-actions">
                                        <button type="button" class="crm-icon-btn" title="Editar" aria-label="Editar {{ $a['title'] }}" data-crm-edit="activity" data-crm-payload='@json($a)'><i class="bi bi-pencil"></i></button>
                                        <button type="button" class="crm-icon-btn crm-icon-btn--danger" title="Eliminar" aria-label="Eliminar {{ $a['title'] }}" data-crm-delete="activity" data-crm-url="{{ $a['destroy_url'] }}" data-crm-id="{{ $activity->id }}"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr id="crmActivitiesEmpty">
                                <td colspan="6" class="crm-empty">Sin actividades registradas.</td>
                            </tr>
                            @endforelse
                            @if($activities->isNotEmpty())
                            <tr class="crm-filter-empty d-none" data-crm-filter-empty="activities">
                                <td colspan="6" class="crm-empty">No hay actividades en este filtro.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Respuestas recientes --}}
        @if($recentReplies->isNotEmpty())
        <div class="tab-pane fade" id="crmTabBandeja" role="tabpanel">
            <div class="crm-panel">
                <div class="crm-toolbar">
                    <div>
                        <h2 class="crm-panel-title">Respuestas recientes</h2>
                        <p class="crm-panel-sub">Correos entrantes de leads (IMAP).</p>
                    </div>
                </div>
                <div class="table-responsive crm-table-wrap">
                    <table class="table crm-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>De</th>
                                <th>Asunto</th>
                                <th>Recibido</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentReplies as $reply)
                            <tr class="{{ $reply->read_at ? '' : 'crm-row-unread' }}">
                                <td class="crm-cell-primary">
                                    <div class="crm-cell-title">{{ $reply->contact?->full_name ?? $reply->from_email }}</div>
                                    @if($reply->contact)<div class="crm-cell-sub">{{ $reply->from_email }}</div>@endif
                                </td>
                                <td class="crm-cell-truncate" title="{{ $reply->subject }}">{{ $reply->subject }}</td>
                                <td class="crm-nowrap">{{ $reply->sent_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@include('admin.crm._modals')
@endsection

@push('scripts')
<script src="{{ \App\Support\Branding::assetUrl('js/crm-dashboard.js') }}?v=tabs-2" nonce="{{ $cspNonce ?? '' }}"></script>
<script src="{{ \App\Support\Branding::alternateAssetUrl('js/crm-dashboard.js') }}?v=tabs-2" nonce="{{ $cspNonce ?? '' }}"></script>
<script nonce="{{ $cspNonce ?? '' }}">
(function () {
    var root = document.getElementById('crmDashboard');
    if (!root) return;

    var contactForm = document.getElementById('crmContactsBulkForm');
    var companyForm = document.getElementById('crmCompaniesBulkForm');
    var allField = document.getElementById('crmContactsAllField');
    var contactsSelectAllMatching = false;
    var companiesSelectAllMatching = false;

    function contactBoxes() {
        return Array.prototype.slice.call(document.querySelectorAll('[data-crm-select="contact"]'));
    }
    function companyBoxes() {
        return Array.prototype.slice.call(document.querySelectorAll('[data-crm-select="company"]'));
    }
    function setBoxes(boxes, checked) {
        boxes.forEach(function (box) { box.checked = !!checked; });
    }
    function selectedCount(boxes) {
        return boxes.filter(function (box) { return box.checked; }).length;
    }

    function updateContacts() {
        var boxes = contactBoxes();
        var checked = selectedCount(boxes);
        var total = parseInt(root.getAttribute('data-contacts-total') || '0', 10) || 0;
        var count = contactsSelectAllMatching ? total : checked;
        var selectAll = document.getElementById('crmSelectAllContacts');
        if (selectAll) {
            selectAll.disabled = boxes.length === 0;
            selectAll.checked = boxes.length > 0 && checked === boxes.length;
            selectAll.indeterminate = checked > 0 && checked < boxes.length;
        }
        var countEl = document.getElementById('crmContactsSelectedCount');
        if (countEl) {
            countEl.textContent = count === 0
                ? 'Ningún contacto seleccionado'
                : (count === 1 ? '1 contacto seleccionado' : count + ' contactos seleccionados');
        }
        var matchBtn = document.getElementById('crmSelectAllMatchingContacts');
        if (matchBtn) {
            matchBtn.classList.toggle('d-none', contactsSelectAllMatching || total === 0);
            matchBtn.textContent = 'Seleccionar todos (' + total + ')';
        }
        var note = document.getElementById('crmContactsMatchingNote');
        if (note) note.classList.toggle('d-none', !contactsSelectAllMatching);
        if (allField) allField.value = contactsSelectAllMatching ? '1' : '0';
        var del = document.getElementById('crmDeleteSelectedContacts');
        if (del) del.textContent = count <= 1 ? 'Eliminar seleccionados' : ('Eliminar ' + count + ' contactos');
    }

    function updateCompanies() {
        var boxes = companyBoxes();
        var checked = selectedCount(boxes);
        var total = parseInt(root.getAttribute('data-companies-total') || '0', 10) || 0;
        var count = companiesSelectAllMatching ? total : checked;
        var selectAll = document.getElementById('crmSelectAllCompanies');
        if (selectAll) {
            selectAll.disabled = boxes.length === 0;
            selectAll.checked = boxes.length > 0 && checked === boxes.length;
            selectAll.indeterminate = checked > 0 && checked < boxes.length;
        }
        var countEl = document.getElementById('crmCompaniesSelectedCount');
        if (countEl) {
            countEl.textContent = count === 0
                ? 'Ninguna empresa seleccionada'
                : (count === 1 ? '1 empresa seleccionada' : count + ' empresas seleccionadas');
        }
        var matchBtn = document.getElementById('crmSelectAllMatchingCompanies');
        if (matchBtn) {
            matchBtn.classList.toggle('d-none', companiesSelectAllMatching || total === 0);
            matchBtn.textContent = 'Seleccionar todas (' + total + ')';
        }
        var note = document.getElementById('crmCompaniesMatchingNote');
        if (note) note.classList.toggle('d-none', !companiesSelectAllMatching);
        var allCompanies = document.getElementById('crmCompaniesAllField');
        if (allCompanies) allCompanies.value = companiesSelectAllMatching ? '1' : '0';
        var del = document.getElementById('crmDeleteSelectedCompanies');
        if (del) del.textContent = count <= 1 ? 'Eliminar seleccionadas' : ('Eliminar ' + count + ' empresas');
    }

    document.addEventListener('change', function (e) {
        var t = e.target;
        if (!t || !t.id && !t.getAttribute) return;
        if (t.id === 'crmSelectAllContacts') {
            contactsSelectAllMatching = false;
            setBoxes(contactBoxes(), t.checked);
            updateContacts();
            return;
        }
        if (t.getAttribute('data-crm-select') === 'contact') {
            contactsSelectAllMatching = false;
            updateContacts();
            return;
        }
        if (t.id === 'crmSelectAllCompanies') {
            companiesSelectAllMatching = false;
            setBoxes(companyBoxes(), t.checked);
            updateCompanies();
            return;
        }
        if (t.getAttribute('data-crm-select') === 'company') {
            companiesSelectAllMatching = false;
            updateCompanies();
        }
    });

    document.addEventListener('click', function (e) {
        var matchContacts = e.target.closest ? e.target.closest('#crmSelectAllMatchingContacts') : null;
        if (matchContacts) {
            e.preventDefault();
            contactsSelectAllMatching = true;
            setBoxes(contactBoxes(), true);
            updateContacts();
            return;
        }
        var matchCompanies = e.target.closest ? e.target.closest('#crmSelectAllMatchingCompanies') : null;
        if (matchCompanies) {
            e.preventDefault();
            companiesSelectAllMatching = true;
            setBoxes(companyBoxes(), true);
            updateCompanies();
        }
    });

    if (contactForm) {
        contactForm.addEventListener('submit', function (e) {
            var total = parseInt(root.getAttribute('data-contacts-total') || '0', 10) || 0;
            var all = allField && String(allField.value) === '1';
            var count = all ? total : selectedCount(contactBoxes());
            if (count < 1) {
                e.preventDefault();
                alert('Selecciona al menos un contacto.');
                return;
            }
            if (!confirm('¿Eliminar ' + count + ' ' + (count === 1 ? 'contacto' : 'contactos') + '? Esta acción no se puede deshacer.')) {
                e.preventDefault();
            }
        });
    }
    if (companyForm) {
        companyForm.addEventListener('submit', function (e) {
            var total = parseInt(root.getAttribute('data-companies-total') || '0', 10) || 0;
            var allCompanies = document.getElementById('crmCompaniesAllField');
            var all = allCompanies && String(allCompanies.value) === '1';
            var count = all ? total : selectedCount(companyBoxes());
            if (count < 1) {
                e.preventDefault();
                alert('Selecciona al menos una empresa.');
                return;
            }
            if (!confirm('¿Eliminar ' + count + ' ' + (count === 1 ? 'empresa' : 'empresas') + '? Esta acción no se puede deshacer.')) {
                e.preventDefault();
            }
        });
    }

    updateContacts();
    updateCompanies();
})();
</script>
@endpush
