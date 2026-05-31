@extends('admin.layouts.app')

@section('title', 'Edit Technology - Admin')

@section('content')
<div class="mt-4">
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('admin.technologies.index') }}" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Back to list
        </a>
        <h1 class="h3 mb-0">Edit Technology</h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.technologies.update', $technology) }}" method="POST" enctype="multipart/form-data">
                @method('PUT')
                @include('admin.technologies._form', ['submitLabel' => 'Update Technology'])
            </form>
        </div>
    </div>
</div>
@endsection
