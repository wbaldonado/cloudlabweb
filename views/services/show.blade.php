@extends('layouts.app')

@section('content')
<div class="cl-page cl-service-page">
    <section class="cl-about-intro">
        <div class="container">
            <p class="mb-2">
                <a href="{{ route('services.index') }}">{{ __('messages.services.index_title') }}</a>
            </p>
            <h1 class="cl-about-title">{{ $copy['h1'] }}</h1>
            <p class="cl-about-tagline">{{ $copy['excerpt'] }}</p>
            <a href="{{ route('contact.index') }}" class="cl-btn-solid">{{ __('messages.cta.free_consultation') }}</a>
        </div>
    </section>

    <section class="cl-section cl-section--white">
        <div class="container">
            <div class="cl-service-prose">
                @foreach($copy['paragraphs'] as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach
            </div>

            @if(!empty($copy['faqs']))
                <h2 class="cl-section-title mt-5">{{ __('messages.services.faq_title') }}</h2>
                <div class="accordion" id="serviceFaq">
                    @foreach($copy['faqs'] as $index => $faq)
                        <div class="accordion-item">
                            <h3 class="accordion-header" id="faq-h-{{ $index }}">
                                <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq-c-{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="faq-c-{{ $index }}">
                                    {{ $faq['question'] }}
                                </button>
                            </h3>
                            <div id="faq-c-{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="faq-h-{{ $index }}" data-bs-parent="#serviceFaq">
                                <div class="accordion-body">{{ $faq['answer'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <h2 class="cl-section-title mt-5">{{ __('messages.services.related_title') }}</h2>
            @if($relatedProject)
                <p>
                    <a href="{{ route('projects.show', $relatedProject->slug) }}" class="cl-link-read">
                        {{ $relatedProject->translated('title') }} <i class="bi bi-arrow-right"></i>
                    </a>
                </p>
                @if($relatedProject->translated('description') !== '')
                    <p>{{ \Illuminate\Support\Str::limit(strip_tags($relatedProject->translated('description')), 220) }}</p>
                @endif
            @else
                <p>{{ __('messages.services.related_empty') }}</p>
                <p>
                    <a href="{{ route('projects.index') }}" class="cl-link-read">
                        {{ __('messages.home.view_all') }} <i class="bi bi-arrow-right"></i>
                    </a>
                </p>
            @endif

            <div class="mt-4">
                <a href="{{ route('contact.index') }}" class="cl-btn-solid">{{ __('messages.cta.free_consultation') }}</a>
            </div>
        </div>
    </section>
</div>
@endsection
