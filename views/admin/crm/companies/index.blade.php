@extends('admin.layouts.app')

@section('title', 'Companies - CRM')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Companies</h1>
    <a href="{{ route('admin.crm.companies.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Create New Company
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Industry</th>
                <th>Phone</th>
                <th>City</th>
                <th>Country</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($companies as $company)
            <tr>
                <td>{{ $company->id }}</td>
                <td><strong>{{ $company->name }}</strong></td>
                <td>{{ $company->industry ?? 'N/A' }}</td>
                <td>{{ $company->phone ?? 'N/A' }}</td>
                <td>{{ $company->city ?? 'N/A' }}</td>
                <td>{{ $company->country ?? 'N/A' }}</td>
                <td>{{ $company->created_at->format('M d, Y') }}</td>
                <td>
                    <a href="{{ route('admin.crm.companies.show', $company) }}" class="btn btn-sm btn-info">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('admin.crm.companies.edit', $company) }}" class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('admin.crm.companies.destroy', $company) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this company?')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">No companies found. <a href="{{ route('admin.crm.companies.create') }}">Create your first company</a></td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-center">
    {{ $companies->links() }}
</div>
@endsection

