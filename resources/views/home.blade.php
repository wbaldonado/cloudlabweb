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
                        <a href="{{ route('home') }}#contacto" class="cl-btn-solid" style="background:#05070a!important;background-image:none!important;color:#fff!important;">{{ __('messages.landing.schedule') }}</a>
                        <a href="#servicios" class="cl-btn-outline cl-btn-outline--on-dark">{{ __('messages.landing.see_services') }}</a>
                    </div>
                    <p class="cl-hero-facts">{{ __('messages.landing.facts') }}</p>
                </div>
                <div class="cl-flow-wrap">
                    @include('components.landing-flow')
                </div>
            </div>
        </div>
    </section>

    <section class="cl-section cl-section--white" id="servicios">
        <div class="container">
            <h2 class="cl-section-title">{{ __('messages.landing.services_title') }}</h2>
            <p class="cl-section-subtitle">{{ __('messages.landing.services_intro') }}</p>
            <div class="cl-offer-grid">
                <article class="cl-offer-card cl-offer-card--lg">
                    <h3>{{ __('messages.landing.auto_title') }}</h3>
                    <p>{{ __('messages.landing.auto_text') }}</p>
                    <ul>
                        <li>{{ __('messages.landing.auto_item_1') }}</li>
                        <li>{{ __('messages.landing.auto_item_2') }}</li>
                        <li>{{ __('messages.landing.auto_item_3') }}</li>
                    </ul>
                    <a href="{{ route('services.show', ['slug' => 'automatizacion-power-automate']) }}" class="cl-link-read">{{ __('messages.landing.see_service') }} <i class="bi bi-arrow-right"></i></a>
                </article>
                <article class="cl-offer-card">
                    <h3>{{ __('messages.landing.bi_title') }}</h3>
                    <p>{{ __('messages.landing.bi_text') }}</p>
                    <a href="{{ route('services.show', ['slug' => 'power-bi-analitica']) }}" class="cl-link-read">{{ __('messages.landing.see_service') }} <i class="bi bi-arrow-right"></i></a>
                </article>
                <article class="cl-offer-card">
                    <h3>{{ __('messages.landing.data_title') }}</h3>
                    <p>{{ __('messages.landing.data_text') }}</p>
                    <a href="{{ route('services.show', ['slug' => 'microsoft-fabric-ingenieria-de-datos']) }}" class="cl-link-read">{{ __('messages.landing.see_service') }} <i class="bi bi-arrow-right"></i></a>
                </article>
                <article class="cl-offer-card">
                    <h3>{{ __('messages.landing.apps_title') }}</h3>
                    <p>{{ __('messages.landing.apps_text') }}</p>
                    <a href="{{ route('projects.show', ['slug' => 'caso-de-exito-solucion-para-la-gestion-integral-de-un-taller-de-motocicletas']) }}" class="cl-link-read">{{ __('messages.landing.see_case') }} <i class="bi bi-arrow-right"></i></a>
                </article>
                <article class="cl-offer-card cl-offer-card--lg">
                    <h3>{{ __('messages.landing.ai_title') }}</h3>
                    <p>{{ __('messages.landing.ai_text') }}</p>
                    <a href="#contacto" class="cl-link-read">{{ __('messages.landing.talk') }} <i class="bi bi-arrow-right"></i></a>
                </article>
            </div>
        </div>
    </section>

    <section class="cl-section cl-section--white" id="calculadora" data-landing data-locale="{{ app()->getLocale() }}">
        <div class="container">
            <h2 class="cl-section-title">{{ __('messages.landing.calc_title') }}</h2>
            <div class="cl-calc">
                <div class="cl-calc-controls">
                    <label for="cl-people">{{ __('messages.landing.calc_people') }} <output id="cl-people-out">5</output></label>
                    <input id="cl-people" type="range" min="1" max="50" value="5">
                    <label for="cl-hours">{{ __('messages.landing.calc_hours') }} <output id="cl-hours-out">6</output></label>
                    <input id="cl-hours" type="range" min="1" max="30" value="6">
                    <label for="cl-rate">{{ __('messages.landing.calc_rate') }} <output id="cl-rate-out">12</output></label>
                    <input id="cl-rate" type="range" min="3" max="60" value="12">
                </div>
                <div class="cl-calc-results" aria-live="polite">
                    <p><span>{{ __('messages.landing.calc_month') }}</span> <strong id="cl-month">0</strong></p>
                    <p><span>{{ __('messages.landing.calc_year') }}</span> <strong id="cl-year">0</strong></p>
                </div>
            </div>
            <p class="cl-calc-note">{{ __('messages.landing.calc_note') }}</p>
        </div>
    </section>

    <section class="cl-section" id="proceso">
        <div class="container">
            <h2 class="cl-section-title">{{ __('messages.landing.process_title') }}</h2>
            <div class="cl-steps" data-steps>
                <div class="cl-step-tabs" role="tablist">
                    <button type="button" role="tab" id="cl-tab-1" aria-selected="true" aria-controls="cl-panel-1">1. {{ __('messages.landing.step_1') }}</button>
                    <button type="button" role="tab" id="cl-tab-2" aria-selected="false" aria-controls="cl-panel-2" tabindex="-1">2. {{ __('messages.landing.step_2') }}</button>
                    <button type="button" role="tab" id="cl-tab-3" aria-selected="false" aria-controls="cl-panel-3" tabindex="-1">3. {{ __('messages.landing.step_3') }}</button>
                    <button type="button" role="tab" id="cl-tab-4" aria-selected="false" aria-controls="cl-panel-4" tabindex="-1">4. {{ __('messages.landing.step_4') }}</button>
                </div>
                <div class="cl-step-panel" role="tabpanel" id="cl-panel-1" aria-labelledby="cl-tab-1">
                    <h3>{{ __('messages.landing.step_1') }}</h3>
                    <p>{{ __('messages.landing.step_1_text') }}</p>
                </div>
                <div class="cl-step-panel" role="tabpanel" id="cl-panel-2" aria-labelledby="cl-tab-2" hidden>
                    <h3>{{ __('messages.landing.step_2') }}</h3>
                    <p>{{ __('messages.landing.step_2_text') }}</p>
                </div>
                <div class="cl-step-panel" role="tabpanel" id="cl-panel-3" aria-labelledby="cl-tab-3" hidden>
                    <h3>{{ __('messages.landing.step_3') }}</h3>
                    <p>{{ __('messages.landing.step_3_text') }}</p>
                </div>
                <div class="cl-step-panel" role="tabpanel" id="cl-panel-4" aria-labelledby="cl-tab-4" hidden>
                    <h3>{{ __('messages.landing.step_4') }}</h3>
                    <p>{{ __('messages.landing.step_4_text') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="cl-section cl-section--white" id="contacto">
        <div class="container cl-contact-narrow">
            <h2 class="cl-section-title">{{ __('messages.landing.contact_title') }}</h2>
            <p class="cl-section-subtitle">{{ __('messages.landing.contact_lead') }}</p>
            @if(session('success'))
                <div class="alert alert-success" role="status">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
            @endif
            <form class="cl-landing-form" action="{{ route('contact.store') }}" method="POST">
                @csrf
                <input type="hidden" name="return_to" value="home">
                <div aria-hidden="true" style="position:absolute;left:-9999px;height:0;overflow:hidden;">
                    <label for="website">Website</label>
                    <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                </div>
                <div class="mb-3">
                    <label for="landing-name">{{ __('messages.contact.name') }}</label>
                    <input id="landing-name" name="name" type="text" value="{{ old('name') }}" required maxlength="255" autocomplete="name">
                    @error('name')<p class="cl-field-error">{{ $message }}</p>@enderror
                </div>
                <div class="mb-3">
                    <label for="landing-email">{{ __('messages.landing.contact_email') }}</label>
                    <input id="landing-email" name="email" type="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email">
                    @error('email')<p class="cl-field-error">{{ $message }}</p>@enderror
                </div>
                <div class="mb-3">
                    <label for="landing-message">{{ __('messages.landing.contact_process') }}</label>
                    <textarea id="landing-message" name="message" rows="4" required maxlength="5000">{{ old('message') }}</textarea>
                    @error('message')<p class="cl-field-error">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="cl-btn-solid">{{ __('messages.landing.schedule') }}</button>
            </form>
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

@push('scripts')
<script src="{{ asset('js/landing.js') }}?v={{ file_exists(public_path('js/landing.js')) ? filemtime(public_path('js/landing.js')) : '1' }}" defer></script>
@endpush
