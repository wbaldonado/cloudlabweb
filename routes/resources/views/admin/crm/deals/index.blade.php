@extends('admin.layouts.app')

@section('title', 'Deals - CRM')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Deals</h1>
    <a href="{{ route('admin.crm.deals.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Create New Deal
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
                <th>Title</th>
                <th>Amount</th>
                <th>Stage</th>
                <th>Contact</th>
                <th>Company</th>
                <th>Owner</th>
                <th>Close Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($deals as $deal)
            <tr>
                <td>{{ $deal->id }}</td>
                <td><strong>{{ $deal->title }}</strong></td>
                <td>${{ number_format($deal->amount, 2) }}</td>
                <td>
                    @php
                    $badgeColors = [
                        'prospecting' => 'secondary',
                        'qualified' => 'info',
                        'proposal' => 'primary',
                        'negotiation' => 'warning',
                        'won' => 'success',
                        'lost' => 'danger'
                    ];
                    $color = $badgeColors[$deal->stage] ?? 'secondary';
                    @endphp
                    <span class="badge bg-{{ $color }}">{{ ucfirst($deal->stage) }}</span>
                </td>
                <td>{{ $deal->contact ? $deal->contact->first_name . ' ' . $deal->contact->last_name : 'N/A' }}</td>
                <td>{{ $deal->company->name ?? 'N/A' }}</td>
                <td>{{ $deal->owner->name ?? 'Unassigned' }}</td>
                <td>{{ $deal->expected_close_date ? $deal->expected_close_date->format('M d, Y') : 'N/A' }}</td>
                <td>
                    <a href="{{ route('admin.crm.deals.show', $deal) }}" class="btn btn-sm btn-info">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('admin.crm.deals.edit', $deal) }}" class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('admin.crm.deals.destroy', $deal) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this deal?')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">No deals found. <a href="{{ route('admin.crm.deals.create') }}">Create your first deal</a></td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-center">
    {{ $deals->links() }}
</div>
@endsection

