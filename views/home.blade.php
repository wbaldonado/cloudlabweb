@extends('layouts.app')

@section('title', 'Home - CloudLab Latam')

@section('content')
            @if(isset($homeRenderError) && $homeRenderError)
                <!-- HOME_RENDER_ERROR: {{ $homeRenderError }} -->
            @endif
<div class="cl-page">
    <section class="cl-hero-wrap">
        <div class="cl-hero-bg" aria-hidden="true"></div>
        <div class="container">
            <div class="cl-hero-grid">
                <div class="cl-hero-copy">
                    <h1>{{ __('messages.home.welcome') }}</h1>
                    <p class="cl-hero-lead">{{ __('messages.home.subtitle') }}</p>
                    <div class="cl-hero-actions">
                        <a href="{{ route('services.index') }}" class="cl-btn-solid" style="background:#05070a!important;background-image:none!important;color:#fff!important;">{{ __('messages.home.know_services') }}</a>
                        <a href="{{ route('contact.index') }}" class="cl-btn-outline cl-btn-outline--on-dark">{{ __('messages.home.contact_cta') }}</a>
                    </div>
                </div>

                <aside class="cl-engage-card" aria-label="{{ __('messages.home.engage_title') }}">
                    <p class="cl-engage-card__title">{{ __('messages.home.engage_title') }}</p>
                    <ul class="cl-engage-card__list">
                        <li>
                            <span class="cl-engage-card__check" aria-hidden="true"><i class="bi bi-check-lg"></i></span>
                            <span>{{ __('messages.home.engage_item_1') }}</span>
                        </li>
                        <li>
                            <span class="cl-engage-card__check" aria-hidden="true"><i class="bi bi-check-lg"></i></span>
                            <span>{{ __('messages.home.engage_item_2') }}</span>
                        </li>
                        <li>
                            <span class="cl-engage-card__check" aria-hidden="true"><i class="bi bi-check-lg"></i></span>
                            <span>{{ __('messages.home.engage_item_3') }}</span>
                        </li>
                        <li>
                            <span class="cl-engage-card__check" aria-hidden="true"><i class="bi bi-check-lg"></i></span>
                            <span>{{ __('messages.home.engage_item_4') }}</span>
                        </li>
                    </ul>
                    <p class="cl-engage-card__note">{{ __('messages.home.engage_note') }}</p>
                </aside>
            </div>
        </div>
    </section>

    <div class="cl-home-intro">
        <div class="cl-spiral-bg" aria-hidden="true">
            <div class="cl-spiral-layer cl-spiral-layer--a"></div>
            <div class="cl-spiral-layer cl-spiral-layer--b"></div>
            <div class="cl-spiral-glow cl-spiral-glow--cyan"></div>
            <div class="cl-spiral-glow cl-spiral-glow--magenta"></div>
        </div>

        <section class="cl-services">
            <div class="container">
                <h2 class="cl-section-title cl-services-heading">{{ __('messages.home.services_title') }}</h2>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <article class="cl-svc-card cl-glow-card h-100">
                            <div class="cl-svc-icon cl-svc-icon--blue"><i class="bi bi-bar-chart-fill"></i></div>
                            <h3>{{ __('messages.home.analytics_ai_title') }}</h3>
                            <p>{{ __('messages.home.analytics_ai_desc') }}</p>
                            <p class="cl-svc-tagline">{{ __('messages.home.analytics_ai_tagline') }}</p>
                            <a href="{{ route('services.show', ['slug' => 'power-bi-analitica']) }}" class="cl-link-read">{{ __('messages.home.service_link') }} <i class="bi bi-arrow-right"></i></a>
                        </article>
                    </div>
                    <div class="col-lg-6">
                        <article class="cl-svc-card cl-glow-card h-100">
                            <div class="cl-svc-icon cl-svc-icon--purple"><i class="bi bi-grid-1x2-fill"></i></div>
                            <h3>{{ __('messages.home.data_integration_title') }}</h3>
                            <p>{{ __('messages.home.data_integration_desc') }}</p>
                            <a href="{{ route('services.show', ['slug' => 'microsoft-fabric-ingenieria-de-datos']) }}" class="cl-link-read">{{ __('messages.home.service_link') }} <i class="bi bi-arrow-right"></i></a>
                        </article>
                    </div>
                    <div class="col-lg-6">
                        <article class="cl-svc-card cl-glow-card h-100">
                            <div class="cl-svc-icon cl-svc-icon--orange"><i class="bi bi-lightning-charge-fill"></i></div>
                            <h3>{{ __('messages.home.power_automate_title') }}</h3>
                            <p>{{ __('messages.home.power_automate_desc') }}</p>
                            <a href="{{ route('services.show', ['slug' => 'automatizacion-power-automate']) }}" class="cl-link-read">{{ __('messages.home.service_link') }} <i class="bi bi-arrow-right"></i></a>
                        </article>
                    </div>
                    <div class="col-lg-6">
                        <article class="cl-svc-card cl-glow-card h-100">
                            <div class="cl-svc-icon cl-svc-icon--orange"><i class="bi bi-gear-fill"></i></div>
                            <h3>{{ __('messages.home.rpa_title') }}</h3>
                            <p>{{ __('messages.home.rpa_desc') }}</p>
                            <a href="{{ route('services.show', ['slug' => 'rpa']) }}" class="cl-link-read">{{ __('messages.home.service_link') }} <i class="bi bi-arrow-right"></i></a>
                        </article>
                    </div>
                </div>
            </div>
        </section>
    </div>

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

    <section class="cl-section">
        <div class="container">
            <h2 class="cl-section-title">{{ __('messages.home.technologies_title') }}</h2>
            <p class="cl-section-subtitle">{{ __('messages.home.technologies_subtitle') }}</p>
            @if(isset($technologies) && $technologies->isNotEmpty())
                <div class="cl-tech-grid mb-4">
                    @foreach($technologies as $technology)
                        <div class="cl-tech-item">
                            @include('components.technology-icon', ['technology' => $technology, 'size' => 44])
                            <h4>{{ $technology->name }}</h4>
                        </div>
                    @endforeach
                </div>
                <div class="text-center">
                    <a href="{{ route('public.technologies.index') }}" class="cl-section-link">{{ __('messages.home.view_technologies') }} →</a>
                </div>
            @else
                <div class="cl-empty">{{ __('messages.home.no_technologies') }}</div>
            @endif
        </div>
    </section>

    <section class="cl-section cl-section--white">
        <div class="container">
            <div class="cl-section-header">
                <div>
                    <h2 class="cl-section-title mb-1">{{ __('messages.home.latest_blog') }}</h2>
                    <p class="cl-section-subtitle mb-0">{{ __('messages.home.latest_blog_subtitle') }}</p>
                </div>
                <a href="{{ route('blogs.index') }}" class="cl-section-link">{{ __('messages.home.view_all_posts') }} →</a>
            </div>
            @if(isset($recentBlogs) && $recentBlogs->count() > 0)
                <div class="row g-4">
                    @foreach($recentBlogs as $blog)
                        <div class="col-md-6 col-lg-4">
                            <article class="cl-card cl-glow-card">
                                <span class="cl-badge">{{ __('messages.home.badge_blog') }}</span>
                                <h5>{{ $blog->title }}</h5>
                                <p>{{ \Illuminate\Support\Str::limit($blog->excerpt ?: __('messages.home.card_placeholder'), 120) }}</p>
                                <a href="{{ route('blogs.show', ['slug' => $blog->slug]) }}" class="cl-link-read">
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

    <section class="cl-section pb-5">
        <div class="container">
            <div class="cl-section-header">
                <div>
                    <h2 class="cl-section-title mb-1">{{ __('messages.home.featured_projects') }}</h2>
                    <p class="cl-section-subtitle mb-0">{{ __('messages.home.featured_projects_subtitle') }}</p>
                </div>
                <a href="{{ route('projects.index') }}" class="cl-section-link">{{ __('messages.home.view_all') }} →</a>
            </div>
            @if(isset($featuredProjects) && $featuredProjects->count() > 0)
                <div class="row g-4">
                    @foreach($featuredProjects as $project)
                        <div class="col-md-6 col-lg-4">
                            <article class="cl-card cl-glow-card">
                                <span class="cl-badge">{{ __('messages.home.badge_project') }}</span>
                                <h5>{{ $project->translated('title') }}</h5>
                                <p>{{ \Illuminate\Support\Str::limit($project->translated('description') ?: __('messages.home.card_placeholder'), 120) }}</p>
                                <a href="{{ route('projects.show', ['slug' => $project->slug]) }}" class="cl-link-read">
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
