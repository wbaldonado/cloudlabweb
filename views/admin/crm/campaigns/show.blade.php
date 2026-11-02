@extends('admin.layouts.app')

@section('title', $campaign->name . ' - Campaña')

@push('styles')
<link rel="stylesheet" href="{{ \App\Support\Branding::assetUrl('css/campaign-studio.css') }}?v=preview-1">
<link rel="stylesheet" href="{{ \App\Support\Branding::alternateAssetUrl('css/campaign-studio.css') }}?v=preview-1">
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
        <a href="{{ route('admin.crm.campaigns.index') }}" class="text-decoration-none">&larr; Campañas</a>
        <h1 class="h3 mt-2 mb-1">{{ $campaign->name }}</h1>
        <div class="text-muted">{{ $campaign->subject }} · <span class="badge bg-secondary">{{ $campaign->statusLabel() }}</span></div>
    </div>
    <div class="d-flex gap-2">
        @if(in_array($campaign->status, ['draft', 'sending']))
            <form method="POST" action="{{ route('admin.crm.campaigns.send', $campaign) }}" onsubmit="return confirm('¿Enviar hasta 50 correos de esta campaña?');">
                @csrf
                <input type="hidden" name="batch" value="50">
                <button class="btn btn-dark" type="submit">
                    <i class="bi bi-send"></i>
                    {{ $campaign->status === 'sending' ? 'Continuar envío' : 'Enviar campaña' }}
                </button>
            </form>
        @endif
        <form method="POST" action="{{ route('admin.crm.campaigns.destroy', $campaign) }}" onsubmit="return confirm('¿Eliminar campaña?');">
            @csrf
            @method('DELETE')
            <button class="btn btn-outline-danger" type="submit">Eliminar</button>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Destinatarios</div>
            <div class="fs-3 fw-bold">{{ $campaign->recipients_count }}</div>
        </div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Enviados</div>
            <div class="fs-3 fw-bold">{{ $campaign->sent_count }}</div>
            <div class="small text-danger">Fallidos: {{ $campaign->failed_count }}</div>
        </div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Aperturas únicas</div>
            <div class="fs-3 fw-bold">{{ $campaign->unique_open_count }}</div>
            <div class="small text-muted">{{ $campaign->openRate() }}% · total {{ $campaign->open_count }}</div>
        </div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Clics únicos</div>
            <div class="fs-3 fw-bold">{{ $campaign->unique_click_count }}</div>
            <div class="small text-muted">{{ $campaign->clickRate() }}% · total {{ $campaign->click_count }}</div>
        </div></div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-lg-5">
        <div class="card shadow-sm mb-4">
            <div class="card-header">Detalles</div>
            <div class="card-body">
                <dl class="campaign-studio-meta mb-0">
                    <dt>Asunto</dt>
                    <dd>{{ $campaign->subject }}</dd>
                    <dt>CTA</dt>
                    <dd>
                        @if($campaign->cta_label || $campaign->cta_url)
                            {{ $campaign->cta_label ?: 'Enlace' }}
                            @if($campaign->cta_url)
                                → <a href="{{ $campaign->cta_url }}" target="_blank" rel="noopener">{{ $campaign->cta_url }}</a>
                            @endif
                        @else
                            —
                        @endif
                    </dd>
                    <dt>Remitente</dt>
                    <dd>{{ $campaign->from_name }} &lt;{{ $campaign->from_email }}&gt;</dd>
                    <dt>Bajas</dt>
                    <dd>{{ $campaign->unsubscribe_count }}</dd>
                    <dt>Pendientes de envío</dt>
                    <dd>{{ $campaign->pending_count ?? 0 }}</dd>
                </dl>
            </div>
        </div>

        <div class="card shadow-sm h-100">
            <div class="card-header">Últimas aperturas</div>
            <ul class="list-group list-group-flush">
                @forelse($recentOpens as $ev)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $ev->recipient?->email }}</span>
                        <span class="text-muted small">{{ $ev->created_at?->format('Y-m-d H:i') }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Sin aperturas aún.</li>
                @endforelse
            </ul>
        </div>
    </div>
    <div class="col-lg-7">
        @include('admin.crm.campaigns._preview', ['frameId' => 'campaignDetailPreview', 'previewHtml' => $previewHtml])
        <p class="small text-muted mt-2 mb-0">Así se verá el correo. Las imágenes y estilos de la plantilla se renderizan aquí; el texto CSS crudo ya no se muestra.</p>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header">Últimos clics</div>
            <ul class="list-group list-group-flush">
                @forelse($recentClicks as $ev)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <span>{{ $ev->recipient?->email }}</span>
                            <span class="text-muted small">{{ $ev->created_at?->format('Y-m-d H:i') }}</span>
                        </div>
                        <div class="small text-truncate text-muted">{{ $ev->url }}</div>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Sin clics aún.</li>
                @endforelse
            </ul>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header">Enlaces más clicados</div>
            <ul class="list-group list-group-flush">
                @forelse($topLinks as $link)
                    <li class="list-group-item d-flex justify-content-between gap-3">
                        <span class="text-truncate">{{ $link->url }}</span>
                        <strong>{{ $link->total }}</strong>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Sin datos.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

@php
    $recipientFilters = [
        '' => ['Todos', $recipientCounts['all']],
        'sent' => ['Enviados', $recipientCounts['sent']],
        'opened' => ['Abrieron', $recipientCounts['opened']],
        'clicked' => ['Hicieron clic', $recipientCounts['clicked']],
        'pending' => ['Pendientes', $recipientCounts['pending']],
        'failed' => ['Fallidos', $recipientCounts['failed']],
        'skipped' => ['Omitidos', $recipientCounts['skipped']],
        'unsubscribed' => ['Bajas', $recipientCounts['unsubscribed']],
    ];
@endphp

<div class="card shadow-sm mt-4" id="destinatarios">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <strong>Destinatarios</strong>
            <span class="text-muted small ms-2">Estado de envío y actividad por contacto</span>
        </div>
        <div class="d-flex flex-wrap gap-1">
            @foreach($recipientFilters as $key => [$label, $count])
                @if($key === '' || $count > 0)
                <a href="{{ route('admin.crm.campaigns.show', $campaign) }}{{ $key !== '' ? '?status=' . $key : '' }}#destinatarios"
                   class="btn btn-sm {{ $recipientFilter === $key ? 'btn-dark' : 'btn-outline-secondary' }}">
                    {{ $label }} <span class="badge {{ $recipientFilter === $key ? 'bg-light text-dark' : 'bg-secondary' }} ms-1">{{ $count }}</span>
                </a>
                @endif
            @endforeach
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 campaign-recipients-table">
            <thead class="table-light">
                <tr>
                    <th>Contacto</th>
                    <th>Estado</th>
                    <th>Enviado</th>
                    <th>Aperturas</th>
                    <th>Clics</th>
                    <th>Detalle</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recipients as $r)
                    @php
                        $contactName = $r->contact?->full_name ?? $r->name;
                        $contactUrl = $r->contact ? route('admin.crm.dashboard', ['q' => $r->email]) : null;
                    @endphp
                    <tr>
                        <td>
                            @if($contactName)
                                <div class="fw-semibold">
                                    @if($contactUrl)<a href="{{ $contactUrl }}" class="text-decoration-none text-reset">{{ $contactName }}</a>@else{{ $contactName }}@endif
                                </div>
                            @endif
                            <div class="small text-muted">{{ $r->email }}</div>
                        </td>
                        <td><span class="badge {{ $r->statusBadgeClass() }}">{{ $r->statusLabel() }}</span></td>
                        <td class="text-nowrap">{{ $r->sent_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td class="text-nowrap">
                            @if($r->opened_at)
                                <span class="text-success"><i class="bi bi-envelope-open"></i> {{ $r->open_count }}</span>
                                <div class="small text-muted">{{ $r->opened_at->format('d/m/Y H:i') }}</div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-nowrap">
                            @if($r->clicked_at)
                                <span class="text-primary"><i class="bi bi-cursor"></i> {{ $r->click_count }}</span>
                                <div class="small text-muted">{{ $r->clicked_at->format('d/m/Y H:i') }}</div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="small text-muted" style="max-width: 320px;">
                            @if($r->error)
                                <span class="text-danger text-truncate d-inline-block" style="max-width: 100%;" title="{{ $r->error }}">{{ $r->error }}</span>
                            @elseif(! $r->contact)
                                Contacto eliminado del CRM
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            {{ $recipientFilter !== '' ? 'Ningún destinatario con este filtro.' : 'Esta campaña no tiene destinatarios.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($recipients->total() > 0)
    <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span class="small text-muted">Mostrando {{ $recipients->firstItem() }}–{{ $recipients->lastItem() }} de {{ $recipients->total() }}</span>
        {{ $recipients->fragment('destinatarios')->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="{{ \App\Support\Branding::assetUrl('js/campaign-preview.js') }}?v=preview-1" nonce="{{ $cspNonce ?? '' }}"></script>
<script src="{{ \App\Support\Branding::alternateAssetUrl('js/campaign-preview.js') }}?v=preview-1" nonce="{{ $cspNonce ?? '' }}"></script>
@endpush
