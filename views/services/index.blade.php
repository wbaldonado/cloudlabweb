@extends('layouts.app')

@section('content')
<div class="cl-page cl-service-page">
    <section class="cl-about-intro">
        <div class="container">
            <h1 class="cl-about-title">{{ __('messages.services.index_title') }}</h1>
            <p class="cl-about-tagline">{{ __('messages.services.index_lead') }}</p>
        </div>
    </section>

    <section class="cl-section cl-section--white">
        <div class="container">
            <div class="row g-4">
                @foreach($services as $service)
                    @php $copy = $service[app()->getLocale()] ?? $service['es']; @endphp
                    <div class="col-md-6">
                        <article class="cl-svc-card cl-glow-card h-100">
                            <div class="cl-svc-icon cl-svc-icon--blue"><i class="bi {{ $service['icon'] }}"></i></div>
                            <h2 class="h4">{{ $copy['h1'] }}</h2>
                            <p>{{ $copy['excerpt'] }}</p>
                            <a href="{{ route('services.show', ['slug' => $service['slug']]) }}" class="cl-link-read">
                                {{ __('messages.home.service_link') }} <i class="bi bi-arrow-right"></i>
                            </a>
                        </article>
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-5">
                <a href="{{ route('contact.index') }}" class="cl-btn-solid">{{ __('messages.cta.free_consultation') }}</a>
            </div>
        </div>
    </section>
</div>
@endsection
