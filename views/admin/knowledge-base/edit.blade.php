@extends('admin.layouts.app')

@section('title', 'Edit Knowledge Base Entry - Admin')

@section('content')
<div class="mt-4">
    <h1 class="h3 mb-3">Edit Knowledge Base Entry</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.knowledge-base.update', $knowledgeBase) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.knowledge-base._form', [
                    'submitLabel' => 'Update Entry',
                    'entry' => $knowledgeBase,
                ])
            </form>
        </div>
    </div>
</div>
@endsection

