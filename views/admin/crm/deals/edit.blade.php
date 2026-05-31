@extends('admin.layouts.app')

@section('title', 'Edit Deal - CRM')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Deal</h1>
    <a href="{{ route('admin.crm.deals.index') }}" class="btn btn-secondary">Back to Deals</a>
</div>

<form action="{{ route('admin.crm.deals.update', $deal) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Deal Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Deal Title *</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $deal->title) }}" required>
                        @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', $deal->amount) }}" min="0">
                            @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="stage" class="form-label">Stage *</label>
                        <select class="form-select @error('stage') is-invalid @enderror" id="stage" name="stage" required>
                            <option value="prospecting" {{ old('stage', $deal->stage) == 'prospecting' ? 'selected' : '' }}>Prospecting</option>
                            <option value="qualified" {{ old('stage', $deal->stage) == 'qualified' ? 'selected' : '' }}>Qualified</option>
                            <option value="proposal" {{ old('stage', $deal->stage) == 'proposal' ? 'selected' : '' }}>Proposal</option>
                            <option value="negotiation" {{ old('stage', $deal->stage) == 'negotiation' ? 'selected' : '' }}>Negotiation</option>
                            <option value="won" {{ old('stage', $deal->stage) == 'won' ? 'selected' : '' }}>Won</option>
                            <option value="lost" {{ old('stage', $deal->stage) == 'lost' ? 'selected' : '' }}>Lost</option>
                        </select>
                        @error('stage')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="probability" class="form-label">Probability (0-100%)</label>
                        <input type="number" class="form-control @error('probability') is-invalid @enderror" id="probability" name="probability" value="{{ old('probability', $deal->probability) }}" min="0" max="100">
                        @error('probability')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="expected_close_date" class="form-label">Expected Close Date</label>
                        <input type="date" class="form-control @error('expected_close_date') is-invalid @enderror" id="expected_close_date" name="expected_close_date" value="{{ old('expected_close_date', $deal->expected_close_date ? $deal->expected_close_date->format('Y-m-d') : '') }}">
                        @error('expected_close_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Relationships</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="contact_id" class="form-label">Contact</label>
                        <select class="form-select @error('contact_id') is-invalid @enderror" id="contact_id" name="contact_id">
                            <option value="">Select Contact...</option>
                            @foreach($contacts as $contact)
                            <option value="{{ $contact->id }}" {{ old('contact_id', $deal->contact_id) == $contact->id ? 'selected' : '' }}>{{ $contact->first_name }} {{ $contact->last_name }}@if($contact->company) - {{ $contact->company->name }}@endif</option>
                            @endforeach
                        </select>
                        @error('contact_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="company_id" class="form-label">Company</label>
                        <select class="form-select @error('company_id') is-invalid @enderror" id="company_id" name="company_id">
                            <option value="">Select Company...</option>
                            @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ old('company_id', $deal->company_id) == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                            @endforeach
                        </select>
                        @error('company_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="owner_id" class="form-label">Owner</label>
                        <select class="form-select @error('owner_id') is-invalid @enderror" id="owner_id" name="owner_id">
                            <option value="">Unassigned</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('owner_id', $deal->owner_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('owner_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $deal->description) }}</textarea>
        @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">Update Deal</button>
    <a href="{{ route('admin.crm.deals.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection

