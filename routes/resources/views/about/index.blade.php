@extends('layouts.app')

@section('title', __('messages.about.title') . ' - CloudLab Latam')

@section('content')
<div class="cl-about-page cl-page">
    <section class="cl-about-intro">
        <div class="container">
            <h1 class="cl-about-title">{{ __('messages.about.title') }}</h1>
            <p class="cl-about-tagline">{{ __('messages.about.tagline') }}</p>
            <div class="cl-about-lead">
                <p>{{ __('messages.about.intro_1') }}</p>
                <p>{{ __('messages.about.intro_2') }}</p>
                <p>{{ __('messages.about.intro_3') }}</p>
            </div>
        </div>
    </section>

    <section class="cl-about-highlights">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card cl-about-box h-100 cl-glow-card">
                        <div class="card-body">
                            <h3 class="cl-about-box__title">{{ __('messages.about.what_we_do_title') }}</h3>
                            <p class="cl-about-box__text">{{ __('messages.about.what_we_do_desc') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card cl-about-box h-100 cl-glow-card">
                        <div class="card-body">
                            <h3 class="cl-about-box__title">{{ __('messages.about.approach_title') }}</h3>
                            <p class="cl-about-box__text">{{ __('messages.about.approach_desc') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="about-mission-section scroll-animate fade-in">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="mission-card scroll-animate slide-left stagger-1 cl-glow-card">
                        <div class="mission-icon">
                            <i class="bi bi-bullseye"></i>
                        </div>
                        <h3 class="mission-title">{{ __('messages.about.mission') }}</h3>
                        <p class="mission-text">{{ __('messages.about.mission_desc') }}</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mission-card scroll-animate slide-right stagger-2 cl-glow-card">
                        <div class="mission-icon">
                            <i class="bi bi-eye"></i>
                        </div>
                        <h3 class="mission-title">{{ __('messages.about.vision') }}</h3>
                        <p class="mission-text">{{ __('messages.about.vision_desc') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="about-values-section scroll-animate fade-in">
        <div class="container">
            <h2 class="about-section-title text-center mb-5 scroll-animate slide-up">{{ __('messages.about.values') }}</h2>
            <div class="row justify-content-center">
                <div class="col-xl-4 col-lg-6 mb-4">
                    <div class="value-card scroll-animate scale-in stagger-1 cl-glow-card">
                        <div class="value-icon">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h4 class="value-title">{{ __('messages.about.value_data_title') }}</h4>
                        <p class="value-text">{{ __('messages.about.value_data_desc') }}</p>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 mb-4">
                    <div class="value-card scroll-animate scale-in stagger-2 cl-glow-card">
                        <div class="value-icon">
                            <i class="bi bi-lightbulb"></i>
                        </div>
                        <h4 class="value-title">{{ __('messages.about.value_innovation_title') }}</h4>
                        <p class="value-text">{{ __('messages.about.value_innovation_desc') }}</p>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 mb-4">
                    <div class="value-card scroll-animate scale-in stagger-3 cl-glow-card">
                        <div class="value-icon">
                            <i class="bi bi-lightning-charge"></i>
                        </div>
                        <h4 class="value-title">{{ __('messages.about.value_agility_title') }}</h4>
                        <p class="value-text">{{ __('messages.about.value_agility_desc') }}</p>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 mb-4">
                    <div class="value-card scroll-animate scale-in stagger-4 cl-glow-card">
                        <div class="value-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <h4 class="value-title">{{ __('messages.about.value_partnership_title') }}</h4>
                        <p class="value-text">{{ __('messages.about.value_partnership_desc') }}</p>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 mb-4">
                    <div class="value-card scroll-animate scale-in stagger-5 cl-glow-card">
                        <div class="value-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        <h4 class="value-title">{{ __('messages.about.value_integrity_title') }}</h4>
                        <p class="value-text">{{ __('messages.about.value_integrity_desc') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
    .cl-about-highlights .cl-about-box {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 14px !important;
        box-shadow: 0 8px 30px rgba(15, 23, 42, 0.08) !important;
    }
    .cl-about-highlights .cl-about-box .card-body {
        padding: 1.75rem 1.5rem;
    }
    .cl-about-box__title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #002b5c;
        margin-bottom: 0.85rem;
    }
    .cl-about-box__text {
        font-size: 0.95rem;
        color: #64748b;
        line-height: 1.7;
        margin: 0;
    }
</style>
@endpush
