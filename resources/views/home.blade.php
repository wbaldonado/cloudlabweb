@extends('layouts.app')

@php use Illuminate\Support\Str; @endphp

@section('title', 'Home - CloudLab Latam')

@section('content')
<div class="cl-page">
    {{-- Hero --}}
    <section class="cl-hero-wrap">
        <div class="container">
            <div class="cl-hero-card">
                <h1>{{ __('messages.home.welcome') }}</h1>
                <p class="cl-hero-lead">{{ __('messages.home.subtitle') }}</p>
                <div class="cl-hero-actions">
                    <a href="{{ route('about.index') }}" class="cl-btn-solid">{{ __('messages.home.know_services') }}</a>
                    <a href="{{ route('contact.index') }}" class="cl-btn-outline">{{ __('messages.home.contact_cta') }}</a>
                </div>
            </div>
        </div>
    </section>

    {{-- Three service cards --}}
    <section class="cl-services">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <article class="cl-svc-card cl-glow-card">
                        <div class="cl-svc-icon cl-svc-icon--blue"><i class="bi bi-bar-chart-fill"></i></div>
                        <h3>{{ __('messages.home.analytics_ai_title') }}</h3>
                        <p>{{ __('messages.home.analytics_ai_desc') }}</p>
                        <p class="cl-svc-tagline">{{ __('messages.home.analytics_ai_tagline') }}</p>
                    </article>
                </div>
                <div class="col-lg-4 col-md-6">
                    <article class="cl-svc-card cl-glow-card">
                        <div class="cl-svc-icon cl-svc-icon--purple"><i class="bi bi-grid-1x2-fill"></i></div>
                        <h3>{{ __('messages.home.data_integration_title') }}</h3>
                        <p>{{ __('messages.home.data_integration_desc') }}</p>
                    </article>
                </div>
                <div class="col-lg-4 col-md-12">
                    <article class="cl-svc-card cl-glow-card">
                        <div class="cl-svc-icon cl-svc-icon--orange"><i class="bi bi-gear-fill"></i></div>
                        <h3>{{ __('messages.home.automation_title') }}</h3>
                        <p>{{ __('messages.home.automation_desc') }}</p>
                    </article>
                </div>
            </div>
        </div>
    </section>

    {{-- Why choose --}}
    <section class="cl-section cl-section--white">
        <div class="container">
            <h2 class="cl-section-title">{{ __('messages.home.why_choose') }}</h2>
            <p class="cl-section-subtitle">{{ __('messages.home.why_choose_desc') }}</p>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="cl-card cl-glow-card">
                        <h3 class="h5">{{ __('messages.home.fast_reliable') }}</h3>
                        <p>{{ __('messages.home.fast_reliable_desc') }}</p>
                        <a href="{{ route('about.index') }}" class="cl-learn-more">{{ __('messages.home.learn_more') }} <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="cl-card cl-glow-card">
                        <h3 class="h5">{{ __('messages.home.secure') }}</h3>
                        <p>{{ __('messages.home.secure_desc') }}</p>
                        <a href="{{ route('about.index') }}" class="cl-learn-more">{{ __('messages.home.learn_more') }} <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="cl-card cl-glow-card">
                        <h3 class="h5">{{ __('messages.home.expert_support') }}</h3>
                        <p>{{ __('messages.home.expert_support_desc') }}</p>
                        <a href="{{ route('contact.index') }}" class="cl-learn-more">{{ __('messages.home.learn_more') }} <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Technologies --}}
    <section class="cl-section">
        <div class="container">
            <h2 class="cl-section-title">{{ __('messages.home.technologies_title') }}</h2>
            <p class="cl-section-subtitle">{{ __('messages.home.technologies_subtitle') }}</p>
            @if($technologies->isNotEmpty())
                <div class="cl-tech-grid mb-4">
                    @foreach($technologies as $technology)
                        <div class="cl-tech-item">
                            @if($technology->icon_url)
                                <img src="{{ $technology->icon_url }}" alt="{{ $technology->name }}" loading="lazy">
                            @endif
                            <h4>{{ $technology->name }}</h4>
                        </div>
                    @endforeach
                </div>
                <div class="text-center">
                    <a href="{{ route('technologies.index') }}" class="cl-section-link">{{ __('messages.home.view_technologies') }} →</a>
                </div>
            @else
                <div class="cl-empty">{{ __('messages.home.no_technologies') }}</div>
            @endif
        </div>
    </section>

    {{-- Blog --}}
    <section class="cl-section cl-section--white">
        <div class="container">
            <div class="cl-section-header">
                <div>
                    <h2 class="cl-section-title mb-1">{{ __('messages.home.latest_blog') }}</h2>
                    <p class="cl-section-subtitle mb-0">{{ __('messages.home.latest_blog_subtitle') }}</p>
                </div>
                <a href="{{ route('blogs.index') }}" class="cl-section-link">{{ __('messages.home.view_all_posts') }} →</a>
            </div>
            @if($recentBlogs->count() > 0)
                <div class="row g-4">
                    @foreach($recentBlogs as $blog)
                        <div class="col-md-6 col-lg-4">
                            <article class="cl-card cl-glow-card">
                                <span class="cl-badge">{{ __('messages.home.badge_blog') }}</span>
                                <h5>{{ $blog->title }}</h5>
                                <p>{{ Str::limit($blog->excerpt ?: __('messages.home.card_placeholder'), 120) }}</p>
                                <a href="{{ route('blogs.show', $blog->slug) }}" class="cl-link-read">
                                    {{ __('messages.blog.read_more') }} <i class="bi bi-arrow-right"></i>
                                </a>
                            </article>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="cl-empty">{{ __('messages.home.no_blog') }}</div>
            @endif
        </div>
    </section>

    {{-- Projects --}}
    <section class="cl-section pb-5">
        <div class="container">
            <div class="cl-section-header">
                <div>
                    <h2 class="cl-section-title mb-1">{{ __('messages.home.featured_projects') }}</h2>
                    <p class="cl-section-subtitle mb-0">{{ __('messages.home.featured_projects_subtitle') }}</p>
                </div>
                <a href="{{ route('projects.index') }}" class="cl-section-link">{{ __('messages.home.view_all') }} →</a>
            </div>
            @if($featuredProjects->count() > 0)
                <div class="row g-4">
                    @foreach($featuredProjects as $project)
                        <div class="col-md-6 col-lg-4">
                            <article class="cl-card cl-glow-card">
                                <span class="cl-badge">{{ __('messages.home.badge_project') }}</span>
                                <h5>{{ $project->title }}</h5>
                                <p>{{ Str::limit($project->description ?: __('messages.home.card_placeholder'), 120) }}</p>
                                <a href="{{ route('projects.show', $project->slug) }}" class="cl-link-read">
                                    {{ __('messages.blog.read_more') }} <i class="bi bi-arrow-right"></i>
                                </a>
                            </article>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="cl-empty">{{ __('messages.home.no_projects') }}</div>
            @endif
        </div>
    </section>
</div>
@endsection
