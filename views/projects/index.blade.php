@extends('layouts.app')

@section('title', 'Projects - CloudLab Latam')

@section('content')
<div class="container my-5">
    <h1 class="mb-4">{{ __('messages.projects.title') }}</h1>
    
    @if($projects->count() > 0)
    <div class="row">
        @foreach($projects as $index => $project)
        <div class="col-md-4 mb-4">
            <div class="card h-100 scroll-animate slide-up stagger-{{ min(($index % 6) + 1, 6) }}">
                @if($project->image_url)
                <img src="{{ $project->image_url }}" class="card-img-top" alt="{{ $project->translated('title') }}" style="height: 200px; object-fit: cover;" loading="lazy">
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $project->translated('title') }}</h5>
                    <p class="card-text">{{ Str::limit($project->translated('description'), 100) }}</p>
                    @if($project->translated('technologies') !== '')
                    <p class="card-text"><small class="text-muted">{{ __('messages.projects.technologies') }}: {{ $project->translated('technologies') }}</small></p>
                    @endif
                    <a href="{{ route('projects.show', $project->slug) }}" class="btn btn-primary">{{ __('messages.projects.view_details') }}</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="d-flex justify-content-center">
        {{ $projects->links() }}
    </div>
    @else
    <div class="alert alert-info">
        {{ __('messages.projects.no_projects') }}
    </div>
    @endif
</div>
@endsection

