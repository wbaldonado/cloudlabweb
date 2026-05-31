@extends('admin.layouts.app')

@section('title', 'Add Technology - Admin')

@section('content')
<div class="mt-4">
    <h1 class="h3 mb-3">Add Technology</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.technologies.store') }}" method="POST" enctype="multipart/form-data">
                @include('admin.technologies._form', [
                    'submitLabel' => 'Create Technology',
                    'technology' => new \App\Models\Technology(),
                ])
            </form>
        </div>
    </div>
</div>
@endsection
