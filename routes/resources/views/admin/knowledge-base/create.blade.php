@extends('admin.layouts.app')

@section('title', 'Add Knowledge Base Entry - Admin')

@section('content')
<div class="mt-4">
    <h1 class="h3 mb-3">Add Knowledge Base Entry</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.knowledge-base.store') }}" method="POST">
                @include('admin.knowledge-base._form', [
                    'submitLabel' => 'Create Entry',
                    'entry' => new \App\Models\KnowledgeBase(),
                ])
            </form>
        </div>
    </div>
</div>
@endsection

