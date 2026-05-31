@extends('admin.layouts.app')

@section('title', 'Contact Details - CRM')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Contact Details</h1>
    <div>
        <a href="{{ route('admin.crm.contacts.edit', $contact) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('admin.crm.contacts.index') }}" class="btn btn-secondary">Back to Contacts</a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Contact Information</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Name:</dt>
                    <dd class="col-sm-9"><strong>{{ $contact->first_name }} {{ $contact->last_name }}</strong></dd>

                    @if($contact->email)
                    <dt class="col-sm-3">Email:</dt>
                    <dd class="col-sm-9"><a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></dd>
                    @endif

                    @if($contact->phone)
                    <dt class="col-sm-3">Phone:</dt>
                    <dd class="col-sm-9">{{ $contact->phone }}</dd>
                    @endif

                    @if($contact->job_title)
                    <dt class="col-sm-3">Job Title:</dt>
                    <dd class="col-sm-9">{{ $contact->job_title }}</dd>
                    @endif

                    <dt class="col-sm-3">Lifecycle Stage:</dt>
                    <dd class="col-sm-9">
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
                    </dd>

                    @if($contact->lead_source)
                    <dt class="col-sm-3">Lead Source:</dt>
                    <dd class="col-sm-9">{{ $contact->lead_source }}</dd>
                    @endif

                    @if($contact->lead_score)
                    <dt class="col-sm-3">Lead Score:</dt>
                    <dd class="col-sm-9">{{ $contact->lead_score }}</dd>
                    @endif

                    @if($contact->company)
                    <dt class="col-sm-3">Company:</dt>
                    <dd class="col-sm-9"><a href="{{ route('admin.crm.companies.show', $contact->company) }}">{{ $contact->company->name }}</a></dd>
                    @endif

                    @if($contact->owner)
                    <dt class="col-sm-3">Owner:</dt>
                    <dd class="col-sm-9">{{ $contact->owner->name }}</dd>
                    @endif

                    @if($contact->assignedTo)
                    <dt class="col-sm-3">Assigned To:</dt>
                    <dd class="col-sm-9">{{ $contact->assignedTo->name }}</dd>
                    @endif

                    @if($contact->notes)
                    <dt class="col-sm-3">Notes:</dt>
                    <dd class="col-sm-9">{{ $contact->notes }}</dd>
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
                <p><strong>Created:</strong><br>{{ $contact->created_at->format('M d, Y H:i') }}</p>
                @if($contact->captured_at)
                <p><strong>Captured:</strong><br>{{ $contact->captured_at->format('M d, Y H:i') }}</p>
                @endif
                @if($contact->converted_at)
                <p><strong>Converted:</strong><br>{{ $contact->converted_at->format('M d, Y H:i') }}</p>
                @endif
                <p><strong>Last Updated:</strong><br>{{ $contact->updated_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

