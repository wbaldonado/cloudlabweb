@extends('admin.layouts.app')

@section('title', 'Create Contact - CRM')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Create New Contact</h1>
    <a href="{{ route('admin.crm.contacts.index') }}" class="btn btn-secondary">Back to Contacts</a>
</div>

<form action="{{ route('admin.crm.contacts.store') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Basic Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name *</label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                                @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name *</label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                                @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}">
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                        @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="job_title" class="form-label">Job Title</label>
                        <input type="text" class="form-control @error('job_title') is-invalid @enderror" id="job_title" name="job_title" value="{{ old('job_title') }}">
                        @error('job_title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Lead Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="lifecycle_stage" class="form-label">Lifecycle Stage *</label>
                        <select class="form-select @error('lifecycle_stage') is-invalid @enderror" id="lifecycle_stage" name="lifecycle_stage" required>
                            <option value="lead" {{ old('lifecycle_stage') == 'lead' ? 'selected' : '' }}>Lead</option>
                            <option value="mql" {{ old('lifecycle_stage') == 'mql' ? 'selected' : '' }}>MQL (Marketing Qualified Lead)</option>
                            <option value="sql" {{ old('lifecycle_stage') == 'sql' ? 'selected' : '' }}>SQL (Sales Qualified Lead)</option>
                            <option value="customer" {{ old('lifecycle_stage') == 'customer' ? 'selected' : '' }}>Customer</option>
                        </select>
                        @error('lifecycle_stage')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="lead_source" class="form-label">Lead Source</label>
                        <input type="text" class="form-control @error('lead_source') is-invalid @enderror" id="lead_source" name="lead_source" value="{{ old('lead_source') }}" placeholder="Website, Referral, etc.">
                        @error('lead_source')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="lead_score" class="form-label">Lead Score (0-100)</label>
                        <input type="number" class="form-control @error('lead_score') is-invalid @enderror" id="lead_score" name="lead_score" value="{{ old('lead_score', 0) }}" min="0" max="100">
                        @error('lead_score')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="capture_method" class="form-label">Capture Method</label>
                        <select class="form-select @error('capture_method') is-invalid @enderror" id="capture_method" name="capture_method">
                            <option value="">Select...</option>
                            <option value="form" {{ old('capture_method') == 'form' ? 'selected' : '' }}>Form</option>
                            <option value="import" {{ old('capture_method') == 'import' ? 'selected' : '' }}>Import</option>
                            <option value="api" {{ old('capture_method') == 'api' ? 'selected' : '' }}>API</option>
                            <option value="manual" {{ old('capture_method') == 'manual' ? 'selected' : '' }}>Manual</option>
                        </select>
                        @error('capture_method')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Assignment</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="company_id" class="form-label">Company</label>
                        <select class="form-select @error('company_id') is-invalid @enderror" id="company_id" name="company_id">
                            <option value="">Select Company...</option>
                            @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
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
                            <option value="{{ $user->id }}" {{ old('owner_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('owner_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Assigned To</label>
                        <select class="form-select @error('assigned_to') is-invalid @enderror" id="assigned_to" name="assigned_to">
                            <option value="">Unassigned</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('assigned_to')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label for="notes" class="form-label">Notes</label>
        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4">{{ old('notes') }}</textarea>
        @error('notes')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">Create Contact</button>
    <a href="{{ route('admin.crm.contacts.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection

