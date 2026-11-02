@extends('admin.layouts.app')

@section('title', 'Companies - CRM')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/crm-dashboard.css') }}">
@endpush

@section('content')
<div class="crm-dashboard">
    <div class="d-flex justify-content-between flex-wrap align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1">Empresas</h1>
            <p class="text-muted mb-0">Cuentas y prospectos por industria.</p>
        </div>
        <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#createCompanyModal">
            <i class="bi bi-plus-lg"></i> Nueva empresa
        </button>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="crm-panel">
        <div class="table-responsive">
            <table class="table crm-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Industria</th>
                        <th>Teléfono</th>
                        <th>Ciudad</th>
                        <th>País</th>
                        <th>Creada</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="crmCompaniesTableBody">
                    @forelse($companies as $company)
                    <tr>
                        <td>{{ $company->id }}</td>
                        <td><strong>{{ $company->name }}</strong></td>
                        <td>{{ $company->industry ?? '—' }}</td>
                        <td>{{ $company->phone ?? '—' }}</td>
                        <td>{{ $company->city ?? '—' }}</td>
                        <td>{{ $company->country ?? '—' }}</td>
                        <td>{{ $company->created_at->format('M d, Y') }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.crm.companies.show', $company) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                            <a href="{{ route('admin.crm.companies.edit', $company) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No hay empresas. Crea la primera con el botón "Nueva empresa".</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-3">{{ $companies->links() }}</div>
    </div>
</div>

@include('admin.crm._company-modal')
@endsection
