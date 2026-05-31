@extends('layouts.app')

@php use Illuminate\Support\Str; @endphp

@section('title', 'Home - CloudLab Latam')

@section('content')
@php
    $hexagonPattern = asset('images/hexagon-pattern.svg');
@endphp
<div class="hero-section hero-section-with-pattern hero-section-futuristic" 
     style="background-image: url('{{ $hexagonPattern }}'), radial-gradient(circle at top left, rgba(59, 130, 246, 0.25), transparent 55%), radial-gradient(circle at bottom right, rgba(56, 189, 248, 0.2), transparent 50%), linear-gradient(135deg, #0f172a 0%, #1e3a8a 45%, #1f4ed8 100%);">
    <!-- Animated Particles Background -->
    <canvas id="particles-canvas" class="particles-canvas"></canvas>
    
    <!-- Animated Gradient Orbs -->
    <div class="gradient-orb orb-1"></div>
    <div class="gradient-orb orb-2"></div>
    <div class="gradient-orb orb-3"></div>
    
    <div class="container text-center hero-content">
        <div class="hero-badge scroll-animate fade-in">
            <span class="badge-text">✨ {{ __('messages.home.innovative_tech') }}</span>
        </div>
        <h1 class="display-4 mb-4 hero-title scroll-animate slide-up stagger-1">
            <span class="gradient-text">{{ __('messages.home.welcome') }}</span>
        </h1>
        <p class="lead hero-subtitle scroll-animate slide-up stagger-2">{{ __('messages.home.subtitle') }}</p>
        <div class="hero-cta-buttons scroll-animate fade-in stagger-3">
            <a href="{{ route('contact.index') }}" class="btn-hero btn-hero-primary">
                <span>{{ __('messages.cta.free_consultation') }}</span>
                <i class="bi bi-arrow-right"></i>
            </a>
            <a href="{{ route('projects.index') }}" class="btn-hero btn-hero-secondary">
                <span>{{ __('messages.home.view_projects') }}</span>
            </a>
        </div>
    </div>
    
    <!-- Scroll Indicator -->
    <div class="scroll-indicator scroll-animate fade-in stagger-4">
        <div class="mouse">
            <div class="wheel"></div>
        </div>
        <div class="arrow">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</div>

<!-- Why Choose CloudLab Latam Section -->
<section class="why-choose-section scroll-animate fade-in">
    <div class="container">
        <h2 class="why-choose-title scroll-animate slide-up">{{ __('messages.home.why_choose') }}</h2>
        <div class="why-choose-content-wrapper scroll-animate fade-in stagger-1">
            <div class="why-choose-description">{{ __('messages.home.why_choose_desc') }}</div>
            @php
                $locale = app()->getLocale();
                $imagePath = $locale == 'es' 
                    ? 'images/digital-transformation-es.png' 
                    : 'images/digital-transformation-en.png';
                $imageUrl = asset($imagePath);
            @endphp
            <div class="why-choose-image">
                <img src="{{ $imageUrl }}" 
                     alt="{{ $locale == 'es' ? 'Transformación Digital para Empresas' : 'Digital Transformation for Business' }}" 
                     class="why-choose-img"
                     loading="lazy"
                     onerror="console.error('Error loading image:', this.src); this.style.border='2px solid red';">
            </div>
        </div>
        <div class="row">
            <!-- Card 1: Rápido y Confiable -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card feature-card-modern scroll-animate slide-up stagger-1">
                    <div class="feature-card-glow"></div>
                    <div class="feature-card-header">
                        <div class="feature-icon feature-icon-svg-container feature-icon-animated">
                            <img src="{{ asset('images/schedule-icon.svg') }}" alt="Schedule" class="feature-icon-svg">
                            <div class="icon-pulse"></div>
                        </div>
                    </div>
                    <div class="feature-card-body">
                        <h3 class="feature-card-title">{{ __('messages.home.fast_reliable') }}</h3>
                        <p class="feature-card-description">
                            {{ __('messages.home.fast_reliable_desc') }}
                        </p>
                        <div class="feature-card-footer">
                            <span class="feature-link">Learn more <i class="bi bi-arrow-right"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Seguro -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card feature-card-modern scroll-animate slide-up stagger-2">
                    <div class="feature-card-glow"></div>
                    <div class="feature-card-header">
                        <div class="feature-icon feature-icon-svg-container feature-icon-animated">
                            <img src="{{ asset('images/secure-server-icon.svg') }}" alt="Secure Server" class="feature-icon-svg" loading="lazy">
                            <div class="icon-pulse"></div>
                        </div>
                    </div>
                    <div class="feature-card-body">
                        <h3 class="feature-card-title">{{ __('messages.home.secure') }}</h3>
                        <p class="feature-card-description">
                            {{ __('messages.home.secure_desc') }}
                        </p>
                        <div class="feature-card-footer">
                            <span class="feature-link">Learn more <i class="bi bi-arrow-right"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Soporte Experto -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card feature-card-modern scroll-animate slide-up stagger-3">
                    <div class="feature-card-glow"></div>
                    <div class="feature-card-header">
                        <div class="feature-icon feature-icon-svg-container feature-icon-animated">
                            <img src="{{ asset('images/instant-support-icon.svg') }}" alt="Instant Support" class="feature-icon-svg" loading="lazy">
                            <div class="icon-pulse"></div>
                        </div>
                    </div>
                    <div class="feature-card-body">
                        <h3 class="feature-card-title">{{ __('messages.home.expert_support') }}</h3>
                        <p class="feature-card-description">
                            {{ __('messages.home.expert_support_desc') }}
                        </p>
                        <div class="feature-card-footer">
                            <span class="feature-link">Learn more <i class="bi bi-arrow-right"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Technologies Section -->
<section class="technologies-section scroll-animate fade-in">
    <div class="container">
        <div class="technologies-header scroll-animate slide-up">
            <h2 class="technologies-title">{{ __('messages.home.technologies_title') }}</h2>
            <p class="technologies-subtitle">{{ __('messages.home.technologies_subtitle') }}</p>
        </div>
        @if($technologies->isNotEmpty())
            <div class="technologies-grid">
                @foreach($technologies as $index => $technology)
                    <div class="tech-card scroll-animate scale-in stagger-{{ min($index + 1, 6) }}">
                        <div class="tech-icon {{ $technology->color_class }}">
                            @if($technology->icon_url)
                                <img src="{{ $technology->icon_url }}" alt="{{ $technology->name }} icon" class="tech-icon-image" loading="lazy">
                            @else
                                <span class="text-white-50">{{ Str::substr($technology->name, 0, 2) }}</span>
                            @endif
                        </div>
                        <h4 class="tech-name">{{ $technology->name }}</h4>
                        @if($technology->description)
                            <p class="tech-description">{{ $technology->description }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('technologies.index') }}" class="technologies-link">{{ __('messages.home.view_technologies') }} →</a>
            </div>
        @else
            <div class="alert alert-info mt-4" role="alert">
                {{ __('messages.home.no_technologies') }}
            </div>
        @endif
    </div>
</section>

<!-- Proyectos Destacados Section -->
<section class="content-section home-projects-section scroll-animate fade-in">
    <div class="container">
        <div class="section-header scroll-animate slide-up">
            <h2 class="section-title">{{ __('messages.home.featured_projects') }}</h2>
            <a href="{{ route('projects.index') }}" class="section-link">{{ __('messages.home.view_all') }} →</a>
        </div>
        
        @if($featuredProjects->count() > 0)
        <div class="row">
            @foreach($featuredProjects as $index => $project)
            <div class="col-md-4 mb-4">
                <div class="card card-modern h-100 scroll-animate slide-up stagger-{{ min($index + 1, 6) }}">
                    <div class="card-image-wrapper">
                        @if($project->image)
                        <img src="{{ asset('storage/' . $project->image) }}" class="card-img-top" alt="{{ $project->title }}" loading="lazy">
                        @endif
                        <div class="card-overlay"></div>
                        <div class="card-badge">
                            <i class="bi bi-star-fill"></i> Featured
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $project->title }}</h5>
                        <p class="card-text">{{ Str::limit($project->description, 100) }}</p>
                        <a href="{{ route('projects.show', $project->slug) }}" class="btn btn-primary btn-modern">
                            <span>{{ __('messages.projects.view_details') }}</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            {{ __('messages.home.no_projects') }}
        </div>
        @endif
    </div>
</section>

<!-- Últimas Entradas del Blog Section -->
<section class="content-section home-blog-section scroll-animate fade-in">
    <div class="container">
        <div class="section-header scroll-animate slide-up">
            <h2 class="section-title">{{ __('messages.home.latest_blog') }}</h2>
            <a href="{{ route('blogs.index') }}" class="section-link">{{ __('messages.home.view_all') }} →</a>
        </div>
        
        @if($recentBlogs->count() > 0)
        <div class="row">
            @foreach($recentBlogs as $index => $blog)
            <div class="col-md-4 mb-4">
                <div class="card card-modern h-100 scroll-animate slide-up stagger-{{ min($index + 1, 6) }}">
                    <div class="card-image-wrapper">
                        @if($blog->featured_image_url)
                        <img src="{{ $blog->featured_image_url }}" class="card-img-top" alt="{{ $blog->title }}" loading="lazy">
                        @endif
                        <div class="card-overlay"></div>
                    </div>
                    <div class="card-body">
                        <div class="card-meta">
                            <span class="card-date">{{ $blog->created_at->format('M d, Y') }}</span>
                        </div>
                        <h5 class="card-title">{{ $blog->title }}</h5>
                        <p class="card-text">{{ Str::limit($blog->excerpt, 100) }}</p>
                        <a href="{{ url('/blogs/id/' . (int) $blog->id) }}" 
                           class="btn btn-primary btn-modern" 
                           style="position: relative; z-index: 15 !important; pointer-events: auto !important; cursor: pointer !important;">
                            <span>{{ __('messages.blog.read_more') }}</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            {{ __('messages.home.no_blog') }}
        </div>
        @endif
    </div>
</section>
@endsection

