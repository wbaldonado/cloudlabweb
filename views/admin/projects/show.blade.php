@extends('admin.layouts.app')

@section('title', $project->title . ' - Admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">{{ $project->title }}</h1>
    <div>
        <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-warning">Edit</a>
        <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($project->image)
        <img src="{{ asset('storage/' . $project->image) }}" class="img-fluid mb-3" alt="{{ $project->title }}">
        @endif
        
        <p><strong>Status:</strong> 
            @if($project->is_published)
            <span class="badge bg-success">Published</span>
            @else
            <span class="badge bg-secondary">Draft</span>
            @endif
        </p>
        
        <p><strong>Featured:</strong> 
            @if($project->is_featured)
            <span class="badge bg-warning">Yes</span>
            @else
            <span class="badge bg-secondary">No</span>
            @endif
        </p>
        
        @if($project->description)
        <p><strong>Description:</strong> {{ $project->description }}</p>
        @endif
        
        @if($project->content)
        <p><strong>Content:</strong></p>
        <div class="border p-3 mb-3">
            {!! nl2br(e($project->content)) !!}
        </div>
        @endif
        
        @if($project->url)
        <p><strong>Project URL:</strong> <a href="{{ $project->url }}" target="_blank">{{ $project->url }}</a></p>
        @endif
        
        @if($project->github_url)
        <p><strong>GitHub URL:</strong> <a href="{{ $project->github_url }}" target="_blank">{{ $project->github_url }}</a></p>
        @endif
        
        @if($project->technologies)
        <p><strong>Technologies:</strong> {{ $project->technologies }}</p>
        @endif
        
        <p><strong>Author:</strong> {{ $project->user->name ?? 'N/A' }}</p>
        <p><strong>Created:</strong> {{ $project->created_at->format('F d, Y H:i') }}</p>
        <p><strong>Updated:</strong> {{ $project->updated_at->format('F d, Y H:i') }}</p>
    </div>
</div>
@endsection

