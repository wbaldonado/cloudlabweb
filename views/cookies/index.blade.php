@extends('layouts.app')

@section('title', __('messages.cookies.page_title') . ' - CloudLab Latam')

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
            <h1 class="documentation-title">{{ __('messages.cookies.page_title') }}</h1>
            <p class="documentation-subtitle">{{ __('messages.cookies.page_subtitle') }}</p>
            <p class="documentation-updated">{{ __('messages.cookies.last_updated') }}: {{ $formattedDate }}</p>
        </div>
    </div>
</section>

<section class="documentation-content">
    <div class="container">
        <div class="documentation-card">
            <h2 class="section-heading">{{ __('messages.cookies.sections.what') }}</h2>
            <p class="section-text">{{ __('messages.cookies.content.what') }}</p>

            <h3 class="section-heading mt-4">{{ __('messages.cookies.sections.types') }}</h3>
            <p class="section-text"><strong>{{ __('messages.cookies.cat_necessary') }}:</strong> {{ __('messages.cookies.cat_necessary_desc') }}</p>
            <p class="section-text"><strong>{{ __('messages.cookies.cat_analytics') }}:</strong> {{ __('messages.cookies.cat_analytics_desc') }}</p>
            <p class="section-text"><strong>{{ __('messages.cookies.cat_marketing') }}:</strong> {{ __('messages.cookies.cat_marketing_desc') }}</p>

            <h3 class="section-heading mt-4">{{ __('messages.cookies.sections.manage') }}</h3>
            <p class="section-text">{{ __('messages.cookies.content.manage') }}</p>

            <h3 class="section-heading mt-4">{{ __('messages.cookies.sections.contact') }}</h3>
            <p class="section-text">{!! __('messages.cookies.content.contact') !!}</p>
        </div>
    </div>
</section>
@endsection
