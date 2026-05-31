@extends('admin.layouts.app')

@section('title', 'Contacts - CRM')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Contacts</h1>
    <a href="{{ route('admin.crm.contacts.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Create New Contact
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
                <th>Email</th>
                <th>Phone</th>
                <th>Company</th>
                <th>Stage</th>
                <th>Owner</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($contacts as $contact)
            <tr>
                <td>{{ $contact->id }}</td>
                <td><strong>{{ $contact->first_name }} {{ $contact->last_name }}</strong></td>
                <td>{{ $contact->email ?? 'N/A' }}</td>
                <td>{{ $contact->phone ?? 'N/A' }}</td>
                <td>{{ $contact->company->name ?? 'N/A' }}</td>
                <td>
                    @php
                    $badgeColors = [
                        'lead' => 'secondary',
                        'mql' => 'info',
                        'sql' => 'primary',
                        'customer' => 'success'
                    ];
                    $color = $badgeColors[$contact->lifecycle_stage] ?? 'secondary';
                    @endphp
                    <span class="badge bg-{{ $color }}">{{ strtoupper($contact->lifecycle_stage) }}</span>
                </td>
                <td>{{ $contact->owner->name ?? 'Unassigned' }}</td>
                <td>{{ $contact->created_at->format('M d, Y') }}</td>
                <td>
                    <a href="{{ route('admin.crm.contacts.show', $contact) }}" class="btn btn-sm btn-info">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('admin.crm.contacts.edit', $contact) }}" class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('admin.crm.contacts.destroy', $contact) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this contact?')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">No contacts found. <a href="{{ route('admin.crm.contacts.create') }}">Create your first contact</a></td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-center">
    {{ $contacts->links() }}
</div>
@endsection

