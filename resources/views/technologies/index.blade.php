@extends('layouts.app')

@section('title', 'Technologies - CloudLab Latam')

@section('content')
<section class="technologies-section">
    <div class="container">
        <div class="technologies-header">
            <h2 class="technologies-title">{{ __('messages.home.technologies_title') }}</h2>
            <p class="technologies-subtitle">{{ __('messages.home.technologies_subtitle') }}</p>
        </div>
        @if($technologies->isNotEmpty())
            <div class="technologies-grid">
                @foreach($technologies as $index => $technology)
                    <div class="tech-card scroll-animate scale-in stagger-{{ min(($index % 6) + 1, 6) }}">
                        <div class="tech-icon {{ $technology->color_class }}">
                            @include('components.technology-icon', ['technology' => $technology, 'size' => 48])
                        </div>
                        <h4 class="tech-name">{{ $technology->name }}</h4>
                        @if($technology->description)
                            <p class="tech-description">{{ $technology->description }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info" role="alert">
                {{ __('messages.home.no_technologies') }}
            </div>
        @endif
    </div>
</section>
@endsection
