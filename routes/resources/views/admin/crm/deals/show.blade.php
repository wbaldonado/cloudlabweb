@extends('admin.layouts.app')

@section('title', 'Deal Details - CRM')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Deal Details</h1>
    <div>
        <a href="{{ route('admin.crm.deals.edit', $deal) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('admin.crm.deals.index') }}" class="btn btn-secondary">Back to Deals</a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Deal Information</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Title:</dt>
                    <dd class="col-sm-9"><strong>{{ $deal->title }}</strong></dd>

                    <dt class="col-sm-3">Amount:</dt>
                    <dd class="col-sm-9"><strong>${{ number_format($deal->amount, 2) }}</strong></dd>

                    <dt class="col-sm-3">Stage:</dt>
                    <dd class="col-sm-9">
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
                    </dd>

                    @if($deal->probability)
                    <dt class="col-sm-3">Probability:</dt>
                    <dd class="col-sm-9">{{ $deal->probability }}%</dd>
                    @endif

                    @if($deal->expected_close_date)
                    <dt class="col-sm-3">Expected Close Date:</dt>
                    <dd class="col-sm-9">{{ $deal->expected_close_date->format('M d, Y') }}</dd>
                    @endif

                    @if($deal->contact)
                    <dt class="col-sm-3">Contact:</dt>
                    <dd class="col-sm-9"><a href="{{ route('admin.crm.contacts.show', $deal->contact) }}">{{ $deal->contact->first_name }} {{ $deal->contact->last_name }}</a></dd>
                    @endif

                    @if($deal->company)
                    <dt class="col-sm-3">Company:</dt>
                    <dd class="col-sm-9"><a href="{{ route('admin.crm.companies.show', $deal->company) }}">{{ $deal->company->name }}</a></dd>
                    @endif

                    @if($deal->owner)
                    <dt class="col-sm-3">Owner:</dt>
                    <dd class="col-sm-9">{{ $deal->owner->name }}</dd>
                    @endif

                    @if($deal->description)
                    <dt class="col-sm-3">Description:</dt>
                    <dd class="col-sm-9">{{ $deal->description }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Details</h5>
            </div>
            <div class="card-body">
                <p><strong>Created:</strong><br>{{ $deal->created_at->format('M d, Y H:i') }}</p>
                <p><strong>Last Updated:</strong><br>{{ $deal->updated_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

