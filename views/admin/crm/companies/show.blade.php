@extends('admin.layouts.app')

@section('title', 'Company Details - CRM')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Company Details</h1>
    <div>
        <a href="{{ route('admin.crm.companies.edit', $company) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('admin.crm.companies.index') }}" class="btn btn-secondary">Back to Companies</a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Company Information</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Name:</dt>
                    <dd class="col-sm-9"><strong>{{ $company->name }}</strong></dd>

                    @if($company->industry)
                    <dt class="col-sm-3">Industry:</dt>
                    <dd class="col-sm-9">{{ $company->industry }}</dd>
                    @endif

                    @if($company->website)
                    <dt class="col-sm-3">Website:</dt>
                    <dd class="col-sm-9"><a href="{{ $company->website }}" target="_blank">{{ $company->website }}</a></dd>
                    @endif

                    @if($company->phone)
                    <dt class="col-sm-3">Phone:</dt>
                    <dd class="col-sm-9">{{ $company->phone }}</dd>
                    @endif

                    @if($company->description)
                    <dt class="col-sm-3">Description:</dt>
                    <dd class="col-sm-9">{{ $company->description }}</dd>
                    @endif
                </dl>
            </div>
        </div>

        @if($company->address || $company->city || $company->state || $company->country)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Address</h5>
            </div>
            <div class="card-body">
                <address class="mb-0">
                    @if($company->address){{ $company->address }}<br>@endif
                    @if($company->city){{ $company->city }}, @endif
                    @if($company->state){{ $company->state }} @endif
                    @if($company->zip_code){{ $company->zip_code }}@endif<br>
                    @if($company->country){{ $company->country }}@endif
                </address>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Details</h5>
            </div>
            <div class="card-body">
                <p><strong>Created:</strong><br>{{ $company->created_at->format('M d, Y H:i') }}</p>
                <p><strong>Last Updated:</strong><br>{{ $company->updated_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

