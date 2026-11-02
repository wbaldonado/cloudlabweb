@extends('layouts.app')

@section('title', $project->translated('title') . ' - CloudLab Latam')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-8 scroll-animate fade-in">
            @if($project->image_url)
            <img src="{{ $project->image_url }}" class="img-fluid mb-4 scroll-animate slide-up stagger-1" alt="{{ $project->translated('title') }}" loading="lazy">
            @endif
            
            <h1 class="scroll-animate slide-up stagger-2">{{ $project->translated('title') }}</h1>
            
            @if($project->translated('description') !== '')
            <p class="lead">{{ $project->translated('description') }}</p>
            @endif
            
            @if($project->translated('content') !== '')
            <div class="project-content content mb-4">
                {!! $project->translated('content') !!}
            </div>
            @endif
            
            @if($project->translated('technologies') !== '')
            <div class="mb-4">
                <strong>{{ __('messages.projects.technologies') }}:</strong> {{ $project->translated('technologies') }}
            </div>
            @endif
            
            <div class="mb-4">
                @if($project->url)
                <a href="{{ $project->url }}" target="_blank" class="btn btn-primary me-2">
                    <i class="bi bi-box-arrow-up-right"></i> {{ __('messages.projects.view_live') }}
                </a>
                @endif
                @if($project->github_url)
                <a href="{{ $project->github_url }}" target="_blank" class="btn btn-dark">
                    <i class="bi bi-github"></i> {{ __('messages.projects.view_github') }}
                </a>
                @endif
            </div>
        </div>
        
        <div class="col-md-4 scroll-animate slide-right stagger-3">
            <h3>{{ __('messages.projects.related') }}</h3>
            @if($relatedProjects->count() > 0)
            <div class="list-group">
                @foreach($relatedProjects as $index => $relatedProject)
                <a href="{{ route('projects.show', $relatedProject->slug) }}" class="list-group-item list-group-item-action scroll-animate fade-in stagger-{{ min($index + 1, 6) }}">
                    <h6>{{ $relatedProject->translated('title') }}</h6>
                    @if($relatedProject->translated('description') !== '')
                    <small class="text-muted">{{ Str::limit($relatedProject->translated('description'), 60) }}</small>
                    @endif
                </a>
                @endforeach
            </div>
            @else
            <p class="text-muted">No related projects available.</p>
            @endif
        </div>
    </div>
</div>
@endsection

