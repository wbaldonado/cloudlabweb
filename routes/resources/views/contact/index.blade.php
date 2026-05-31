@extends('layouts.app')

@section('title', __('messages.contact.title') . ' - CloudLab Latam')

@section('content')
<section class="contact-section">
    <div class="container">
        <!-- Header Section -->
        <div class="contact-header scroll-animate slide-up">
            <h1 class="contact-title">{{ __('messages.contact.title') }}</h1>
            <p class="contact-subtitle">{{ __('messages.contact.subtitle') }}</p>
        </div>

        <div class="row mt-5">
            <!-- Left Column - Contact Information -->
            <div class="col-lg-5 mb-4 mb-lg-0">
                <div class="contact-info-card group hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 scroll-animate slide-left stagger-1">
                    <h3 class="contact-card-title mb-5">{{ __('messages.contact.get_in_touch') }}</h3>
                    
                    <div class="contact-item group/item mb-6 opacity-0 animate-fade-in" style="animation-delay: 0.3s;">
                        <div class="contact-icon-wrapper group-hover/item:scale-110 group-hover/item:rotate-3 transition-all duration-300">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <div class="contact-details">
                            <p class="contact-label">{{ __('messages.contact.email') }}</p>
                            <a href="mailto:{{ __('messages.contact.email_address') }}" class="contact-value hover:text-cyan-400 transition-colors duration-200">{{ __('messages.contact.email_address') }}</a>
                        </div>
                    </div>

                    <div class="contact-item group/item mb-6 scroll-animate fade-in stagger-3">
                        <div class="contact-icon-wrapper group-hover/item:scale-110 group-hover/item:rotate-3 transition-all duration-300">
                            <i class="bi bi-telephone"></i>
                        </div>
                        <div class="contact-details">
                            <p class="contact-label">{{ __('messages.footer.phone') }}</p>
                            <p class="contact-value">+57 3208156396</p>
                        </div>
                    </div>

                    <div class="contact-item group/item scroll-animate fade-in stagger-4">
                        <div class="contact-icon-wrapper group-hover/item:scale-110 group-hover/item:rotate-3 transition-all duration-300">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <div class="contact-details">
                            <p class="contact-label">{{ __('messages.contact.location') }}</p>
                            <p class="contact-value">Latin America</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Contact Form -->
            <div class="col-lg-7">
                <div class="contact-form-card group scroll-animate slide-right stagger-2">
                    <h3 class="contact-card-title mb-4">{{ __('messages.contact.send_message') }}</h3>
                    
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show animate-slide-down mb-4" role="alert">
                        <div class="flex items-center">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ session('success') }}
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif
                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show animate-slide-down mb-4" role="alert">
                        <div class="flex items-center">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            {{ session('error') }}
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <form id="contactForm" action="{{ route('contact.store') }}" method="POST" class="contact-form">
                        @csrf
                        <div class="row mb-4">
                            <div class="col-md-6 mb-4 mb-md-0">
                                <label for="name" class="form-label block mb-2 font-semibold text-gray-700">{{ __('messages.contact.name') }} *</label>
                                <div class="relative input-wrapper">
                                    <input type="text" 
                                           class="form-control input-field @error('name') is-invalid border-red-500 @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           placeholder="{{ __('messages.contact.name_placeholder') }}" 
                                           required>
                                    <div class="input-focus-ring"></div>
                                </div>
                                @error('name')
                                <div class="invalid-feedback mt-1 text-red-600 text-sm animate-shake">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label block mb-2 font-semibold text-gray-700">{{ __('messages.contact.email') }} *</label>
                                <div class="relative input-wrapper">
                                    <input type="email" 
                                           class="form-control input-field @error('email') is-invalid border-red-500 @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           placeholder="{{ __('messages.contact.email_placeholder') }}" 
                                           required>
                                    <div class="input-focus-ring"></div>
                                </div>
                                @error('email')
                                <div class="invalid-feedback mt-1 text-red-600 text-sm animate-shake">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-4 mb-md-0">
                                <label for="company" class="form-label block mb-2 font-semibold text-gray-700">{{ __('messages.contact.company') }}</label>
                                <div class="relative input-wrapper">
                                    <input type="text" 
                                           class="form-control input-field @error('company') is-invalid border-red-500 @enderror" 
                                           id="company" 
                                           name="company" 
                                           value="{{ old('company') }}" 
                                           placeholder="{{ __('messages.contact.company_placeholder') }}">
                                    <div class="input-focus-ring"></div>
                                </div>
                                @error('company')
                                <div class="invalid-feedback mt-1 text-red-600 text-sm animate-shake">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="subject" class="form-label block mb-2 font-semibold text-gray-700">{{ __('messages.contact.subject') }}</label>
                                <div class="relative input-wrapper">
                                    <input type="text" 
                                           class="form-control input-field @error('subject') is-invalid border-red-500 @enderror" 
                                           id="subject" 
                                           name="subject" 
                                           value="{{ old('subject') }}" 
                                           placeholder="{{ __('messages.contact.subject_placeholder') }}">
                                    <div class="input-focus-ring"></div>
                                </div>
                                @error('subject')
                                <div class="invalid-feedback mt-1 text-red-600 text-sm animate-shake">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-5">
                            <label for="message" class="form-label block mb-2 font-semibold text-gray-700">{{ __('messages.contact.message') }} *</label>
                            <div class="relative input-wrapper">
                                <textarea class="form-control input-field @error('message') is-invalid border-red-500 @enderror" 
                                          id="message" 
                                          name="message" 
                                          rows="6" 
                                          placeholder="{{ __('messages.contact.message_placeholder') }}" 
                                          required>{{ old('message') }}</textarea>
                                <div class="input-focus-ring"></div>
                            </div>
                            @error('message')
                            <div class="invalid-feedback mt-1 text-red-600 text-sm animate-shake">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" 
                                id="submitBtn"
                                class="btn btn-primary w-100 submit-button group/btn relative overflow-hidden">
                            <span class="relative z-10 flex items-center justify-center">
                                <span class="submit-text">{{ __('messages.contact.send') }}</span>
                                <i class="bi bi-send ms-2 submit-icon opacity-0 -translate-x-2 group-hover/btn:opacity-100 group-hover/btn:translate-x-0 transition-all duration-300"></i>
                            </span>
                            <span class="absolute inset-0 bg-gradient-to-r from-cyan-500 to-indigo-600 transform scale-x-0 group-hover/btn:scale-x-100 transition-transform duration-300 origin-left"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Trigger fade-in animations on load
    const animatedElements = document.querySelectorAll('.opacity-0');
    animatedElements.forEach((el, index) => {
        setTimeout(() => {
            el.classList.add('opacity-100');
            el.classList.remove('opacity-0');
        }, index * 100);
    });

    // Input focus effects
    const inputs = document.querySelectorAll('.input-field');
    inputs.forEach(input => {
        const wrapper = input.closest('.input-wrapper');
        const focusRing = wrapper?.querySelector('.input-focus-ring');

        input.addEventListener('focus', function() {
            wrapper?.classList.add('input-focused');
            if (focusRing) {
                focusRing.style.transform = 'scaleX(1)';
                focusRing.style.opacity = '1';
            }
        });

        input.addEventListener('blur', function() {
            wrapper?.classList.remove('input-focused');
            if (focusRing) {
                focusRing.style.transform = 'scaleX(0)';
                focusRing.style.opacity = '0';
            }
        });

        // Real-time validation
        input.addEventListener('input', function() {
            if (input.validity.valid) {
                input.classList.remove('border-red-500');
                input.classList.add('border-green-500');
                setTimeout(() => {
                    input.classList.remove('border-green-500');
                }, 2000);
            }
        });
    });

    // Form submission animation
    const form = document.getElementById('contactForm');
    const submitBtn = document.getElementById('submitBtn');
    
    form.addEventListener('submit', function(e) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
        submitBtn.classList.add('opacity-75');
    });

    // Character counter for textarea
    const textarea = document.getElementById('message');
    if (textarea) {
        const maxLength = 1000;
        const counter = document.createElement('div');
        counter.className = 'text-sm text-gray-500 mt-1 text-right';
        counter.textContent = `0 / ${maxLength}`;
        textarea.parentElement.appendChild(counter);

        textarea.addEventListener('input', function() {
            const length = this.value.length;
            counter.textContent = `${length} / ${maxLength}`;
            if (length > maxLength * 0.9) {
                counter.classList.add('text-orange-500');
                counter.classList.remove('text-gray-500');
            } else {
                counter.classList.remove('text-orange-500');
                counter.classList.add('text-gray-500');
            }
        });
    }
});
</script>
@endsection

