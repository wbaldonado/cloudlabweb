@extends('layouts.app')

@section('title', 'Blog - CloudLab Latam')

@section('content')
@php $placeholderImg = asset('images/placeholder-blog.jpg'); @endphp
<div class="container my-5">
    <h1 class="mb-4 scroll-animate slide-up">{{ __('messages.blog.title') }}</h1>
    
    @if($blogs->count() > 0)
    <div class="row">
        @foreach($blogs as $index => $blog)
        <div class="col-md-4 mb-4">
            <div class="card card-modern h-100 scroll-animate slide-up stagger-{{ min(($index % 6) + 1, 6) }}">
                <div class="card-image-wrapper">
                    @if($blog->featured_image_url)
                        <img src="{{ $blog->featured_image_url }}" 
                             class="card-img-top" 
                             alt="{{ $blog->title }}" 
                             style="height: 200px; object-fit: cover;" 
                             loading="lazy"
                             onerror="this.onerror=null; this.src='{{ $placeholderImg }}';">
                    @else
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                        </div>
                    @endif
                    <div class="card-overlay"></div>
                </div>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">{{ $blog->title }}</h5>
                    <p class="card-text text-muted flex-grow-1">{{ Str::limit($blog->excerpt ?? '', 100) }}</p>
                    <p class="card-text"><small class="text-muted">{{ $blog->created_at->format('M d, Y') }}</small></p>
                    <a href="{{ route('blogs.show', $blog->slug) }}" class="btn btn-primary btn-modern mt-auto" style="width: fit-content;">
                        <span>{{ __('messages.blog.read_more') }}</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    @if($blogs->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $blogs->links() }}
    </div>
    @endif
    @else
    <div class="alert alert-info scroll-animate fade-in">
        <i class="bi bi-info-circle me-2"></i>
        {{ __('messages.blog.no_posts') }}
    </div>
    @endif
</div>
@endsection

