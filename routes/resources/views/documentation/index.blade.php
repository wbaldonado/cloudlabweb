@extends('layouts.app')

@section('title', __('messages.documentation.title') . ' - CloudLab Latam')

@section('content')
@php
    $currentDate = now()->locale(app()->getLocale());
    $formattedDate = app()->getLocale() === 'es'
        ? $currentDate->translatedFormat('d \de F \de Y')
        : $currentDate->translatedFormat('F d, Y');
@endphp
<section class="documentation-hero">
    <div class="container">
        <div class="text-center">
            <h1 class="documentation-title">{{ __('messages.documentation.title') }}</h1>
            <p class="documentation-subtitle">{{ __('messages.documentation.subtitle') }}</p>
            <p class="documentation-updated">{{ __('messages.documentation.last_updated') }}: {{ $formattedDate }}</p>
        </div>
    </div>
</section>

<section class="documentation-content">
    <div class="container">
        <div class="documentation-card">
            <h2 class="section-heading">{{ __('messages.documentation.sections.introduction') }}</h2>
            <p class="section-text">{{ __('messages.documentation.content.intro') }}</p>

            <h3 class="section-heading mt-4">{{ __('messages.documentation.sections.responsible') }}</h3>
            <p class="section-text">{{ __('messages.documentation.content.responsible') }}</p>

            <h3 class="section-heading mt-4">{{ __('messages.documentation.sections.purpose') }}</h3>
            <p class="section-text">{{ __('messages.documentation.content.purpose') }}</p>

            <h3 class="section-heading mt-4">{{ __('messages.documentation.sections.rights') }}</h3>
            <p class="section-text">{{ __('messages.documentation.content.rights') }}</p>

            <h3 class="section-heading mt-4">{{ __('messages.documentation.sections.transfer') }}</h3>
            <p class="section-text">{{ __('messages.documentation.content.transfer') }}</p>

            <h3 class="section-heading mt-4">{{ __('messages.documentation.sections.security') }}</h3>
            <p class="section-text">{{ __('messages.documentation.content.security') }}</p>

            <h3 class="section-heading mt-4">{{ __('messages.documentation.sections.validity') }}</h3>
            <p class="section-text">{{ __('messages.documentation.content.validity', ['date' => $formattedDate]) }}</p>
        </div>
    </div>
</section>
@endsection
