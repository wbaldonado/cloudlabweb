@extends('admin.layouts.app')

@section('title', $blog->title . ' - Admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">{{ $blog->title }}</h1>
    <div>
        <a href="{{ route('admin.blogs.edit', $blog) }}" class="btn btn-warning">Edit</a>
        <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($blog->featured_image)
        <img src="{{ asset('storage/' . $blog->featured_image) }}" class="img-fluid mb-3" alt="{{ $blog->title }}">
        @endif
        
        <p><strong>Status:</strong> 
            @if($blog->is_published)
            <span class="badge bg-success">Published</span>
            @else
            <span class="badge bg-secondary">Draft</span>
            @endif
        </p>
        
        @if($blog->excerpt)
        <p><strong>Excerpt:</strong> {{ $blog->excerpt }}</p>
        @endif
        
        <p><strong>Content:</strong></p>
        <div class="border p-3 mb-3">
            {!! nl2br(e($blog->content)) !!}
        </div>
        
        <p><strong>Author:</strong> {{ $blog->user->name ?? 'N/A' }}</p>
        <p><strong>Created:</strong> {{ $blog->created_at->format('F d, Y H:i') }}</p>
        <p><strong>Updated:</strong> {{ $blog->updated_at->format('F d, Y H:i') }}</p>
    </div>
</div>
@endsection

