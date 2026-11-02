@extends('admin.layouts.app')

@section('title', 'Campañas email - CRM')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Campañas de email</h1>
        <p class="text-muted mb-0">Crea campañas, mide aperturas y clics, y mejora la entregabilidad.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.crm.dashboard') }}" class="btn btn-outline-secondary">Volver al CRM</a>
        <a href="{{ route('admin.crm.campaigns.create') }}" class="btn btn-dark">
            <i class="bi bi-plus-lg"></i> Nueva campaña
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Campaña</th>
                    <th>Estado</th>
                    <th>Destinatarios</th>
                    <th>Enviados</th>
                    <th>Aperturas</th>
                    <th>Clics</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($campaigns as $c)
                    <tr>
                        <td>
                            <strong>{{ $c->name }}</strong>
                            <div class="small text-muted">{{ $c->subject }}</div>
                        </td>
                        <td><span class="badge bg-secondary">{{ $c->statusLabel() }}</span></td>
                        <td>{{ $c->recipients_count }}</td>
                        <td>{{ $c->sent_count }}</td>
                        <td>{{ $c->unique_open_count }} <span class="text-muted small">({{ $c->openRate() }}%)</span></td>
                        <td>{{ $c->unique_click_count }} <span class="text-muted small">({{ $c->clickRate() }}%)</span></td>
                        <td class="text-end">
                            <a href="{{ route('admin.crm.campaigns.show', $c) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Aún no hay campañas. Crea la primera.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($campaigns->hasPages())
        <div class="card-footer">{{ $campaigns->links() }}</div>
    @endif
</div>

<div class="alert alert-info mt-4">
    <strong>Entregabilidad (anti-spam):</strong>
    En el DNS de <code>cloudlablatam.tech</code> configura SPF, DKIM y DMARC para el correo Hostinger.
    Sin eso, los proveedores (Gmail/Yahoo) filtrarán a spam aunque el CRM esté bien.
    Guía: <code>deploy/CRM_CAMPAIGN_DELIVERABILITY.txt</code>
</div>
@endsection
