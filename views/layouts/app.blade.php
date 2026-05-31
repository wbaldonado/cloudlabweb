<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @php
        use App\Services\SEOService;
        $baseUrl = config('app.url');
        $pageType = $pageType ?? 'home';
        $pageData = $pageData ?? null;
        $meta = SEOService::getPageMeta($pageType, $pageData);
    @endphp
    
    {{-- Basic Meta Tags --}}
    <title>{{ $meta['title'] }}</title>
    <meta name="description" content="{{ $meta['description'] }}">
    <meta name="keywords" content="{{ $meta['keywords'] }}">
    <meta name="author" content="{{ $meta['author'] }}">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="googlebot" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="bingbot" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    
    {{-- Additional SEO Meta Tags --}}
    @if(isset($meta['revisit_after']))
    <meta name="revisit-after" content="{{ $meta['revisit_after'] }}">
    @endif
    @if(isset($meta['rating']))
    <meta name="rating" content="{{ $meta['rating'] }}">
    @endif
    @if(isset($meta['distribution']))
    <meta name="distribution" content="{{ $meta['distribution'] }}">
    @endif
    @if(isset($meta['language']))
    <meta name="language" content="{{ $meta['language'] }}">
    @endif
    @if(isset($meta['geo_region']))
    <meta name="geo.region" content="{{ $meta['geo_region'] }}">
    @endif
    @if(isset($meta['geo_placename']))
    <meta name="geo.placename" content="{{ $meta['geo_placename'] }}">
    @endif
    @if(isset($meta['copyright']))
    <meta name="copyright" content="{{ $meta['copyright'] }}">
    @endif
    @if(isset($meta['reply_to']))
    <meta name="reply-to" content="{{ $meta['reply_to'] }}">
    @endif
    @if(isset($meta['category']))
    <meta name="category" content="{{ $meta['category'] }}">
    @endif
    
    {{-- Canonical URL --}}
    @if(isset($meta['canonical']))
    <link rel="canonical" href="{{ $meta['canonical'] }}">
    @else
    <link rel="canonical" href="{{ $baseUrl . request()->getPathInfo() }}">
    @endif
    
    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="{{ $meta['og_type'] }}">
    <meta property="og:url" content="{{ $meta['canonical'] ?? $baseUrl . request()->getPathInfo() }}">
    <meta property="og:title" content="{{ $meta['title'] }}">
    <meta property="og:description" content="{{ $meta['description'] }}">
    <meta property="og:site_name" content="{{ $meta['og_site_name'] }}">
    <meta property="og:locale" content="{{ app()->getLocale() === 'es' ? 'es_ES' : 'en_US' }}">
    <meta property="og:locale:alternate" content="{{ app()->getLocale() === 'es' ? 'en_US' : 'es_ES' }}">
    <meta property="og:image" content="{{ $meta['og_image'] ?? $baseUrl . '/images/logo.png' }}">
    <meta property="og:image:secure_url" content="{{ str_replace('http://', 'https://', $meta['og_image'] ?? $baseUrl . '/images/logo.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:alt" content="{{ $meta['title'] }}">
    <meta property="og:updated_time" content="{{ now()->toIso8601String() }}">
    
    {{-- Article Meta (for blog posts) --}}
    @if($meta['og_type'] === 'article' && isset($meta['article_published_time']))
    <meta property="article:published_time" content="{{ $meta['article_published_time'] }}">
    <meta property="article:modified_time" content="{{ $meta['article_modified_time'] ?? $meta['article_published_time'] }}">
    <meta property="article:author" content="{{ $meta['article_author'] ?? 'CloudLab Latam' }}">
    <meta property="article:section" content="{{ $meta['article_section'] ?? 'Technology' }}">
    @if(isset($meta['article_tag']))
    <meta property="article:tag" content="{{ $meta['article_tag'] }}">
    @endif
    @endif
    
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="{{ $meta['twitter_card'] }}">
    <meta name="twitter:site" content="{{ $meta['twitter_site'] }}">
    @if(isset($meta['twitter_creator']))
    <meta name="twitter:creator" content="{{ $meta['twitter_creator'] }}">
    @endif
    <meta name="twitter:title" content="{{ $meta['title'] }}">
    <meta name="twitter:description" content="{{ $meta['description'] }}">
    <meta name="twitter:image" content="{{ $meta['og_image'] ?? $baseUrl . '/images/logo.png' }}">
    <meta name="twitter:image:alt" content="{{ $meta['title'] }}">
    
    {{-- Language Alternates (hreflang) --}}
    @if(isset($meta['alternate_lang_es']) || isset($meta['alternate_lang_en']))
        @if(isset($meta['alternate_lang_es']))
        <link rel="alternate" hreflang="es" href="{{ $meta['alternate_lang_es'] }}">
        @endif
        @if(isset($meta['alternate_lang_en']))
        <link rel="alternate" hreflang="en" href="{{ $meta['alternate_lang_en'] }}">
        @endif
        <link rel="alternate" hreflang="x-default" href="{{ $meta['canonical'] ?? $baseUrl . request()->getPathInfo() }}">
    @else
        <link rel="alternate" hreflang="{{ app()->getLocale() === 'es' ? 'es' : 'en' }}" href="{{ $meta['canonical'] ?? $baseUrl . request()->getPathInfo() }}">
        <link rel="alternate" hreflang="{{ app()->getLocale() === 'es' ? 'en' : 'es' }}" href="{{ $baseUrl . request()->getPathInfo() }}?lang={{ app()->getLocale() === 'es' ? 'en' : 'es' }}">
        <link rel="alternate" hreflang="x-default" href="{{ $baseUrl . request()->getPathInfo() }}">
    @endif
    
    {{-- Additional Meta Tags for AI Search Tools --}}
    <meta name="theme-color" content="#0066FF">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="format-detection" content="telephone=no">
    
    {{-- Security and Privacy Meta Tags --}}
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <meta name="format-detection" content="telephone=no, address=no, email=no">
    
    {{-- Content Type and Charset --}}
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="Content-Language" content="{{ app()->getLocale() === 'es' ? 'es' : 'en' }}">
    
    {{-- Favicons --}}
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    
    {{-- Structured Data (JSON-LD) --}}
    <script type="application/ld+json" nonce="{{ $cspNonce ?? '' }}">
    {!! json_encode(SEOService::generateOrganizationSchema(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
    
    <script type="application/ld+json" nonce="{{ $cspNonce ?? '' }}">
    {!! json_encode(SEOService::generateWebSiteSchema(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
    
    @if($pageType === 'blog_post' && $pageData)
    <script type="application/ld+json" nonce="{{ $cspNonce ?? '' }}">
    {!! json_encode(SEOService::generateBlogPostSchema($pageData), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
    @endif
    
    @if($pageType === 'project_detail' && $pageData)
    <script type="application/ld+json" nonce="{{ $cspNonce ?? '' }}">
    {!! json_encode(SEOService::generateProjectSchema($pageData), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
    @endif
    @php
        $viteManifest = public_path('build/manifest.json');
        $viteExists = file_exists($viteManifest);
    @endphp
    @if($viteExists)
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        {{-- Fallback if Vite manifest doesn't exist --}}
        <link rel="stylesheet" href="{{ asset('build/assets/app.css') }}" onerror="this.remove()">
        <script type="module" src="{{ asset('build/assets/app.js') }}" onerror="this.remove()"></script>
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Google Fonts: Plus Jakarta Sans for headings -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Critical Navigation CSS (fallback when Vite assets not loaded) -->
    <style nonce="{{ $cspNonce ?? '' }}">
        /* Color Palette Variables */
        :root {
            --primary: #0066FF; /* Azul más vibrante */
            --secondary: #00D4AA; /* Verde azulado para CTAs */
            --dark: #0F172A; /* Para textos */
            --light: #F8FAFC; /* Fondo claro */
            --accent: #F59E0B; /* Para highlights */
            
            /* Responsive Typography Base Sizes */
            --base-font-size: clamp(14px, 2.5vw, 16px);
            --h1-size: clamp(1.75rem, 5vw, 3.5rem);
            --h2-size: clamp(1.5rem, 4vw, 2.5rem);
            --h3-size: clamp(1.25rem, 3.5vw, 2rem);
            --h4-size: clamp(1.125rem, 3vw, 1.5rem);
            --h5-size: clamp(1rem, 2.5vw, 1.25rem);
            --h6-size: clamp(0.875rem, 2vw, 1.125rem);
            --p-size: clamp(0.875rem, 2vw, 1rem);
            --small-size: clamp(0.75rem, 1.5vw, 0.875rem);
        }
        
        /* Tailwind-like utilities for navigation */
        .sticky { position: sticky; }
        .top-0 { top: 0; }
        .z-50 { z-index: 50; }
        .z-10 { z-index: 10; }
        .bg-gradient-to-br { background-image: linear-gradient(to bottom right, var(--tw-gradient-stops)); }
        .from-slate-900 { --tw-gradient-from: var(--dark); --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(15, 23, 42, 0)); }
        .via-blue-900 { --tw-gradient-stops: var(--tw-gradient-from), var(--primary), var(--tw-gradient-to, rgba(0, 102, 255, 0)); }
        .to-blue-800 { --tw-gradient-to: var(--primary); }
        .shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
        .absolute { position: absolute; }
        .inset-0 { top: 0; right: 0; bottom: 0; left: 0; }
        .w-full { width: 100%; }
        .h-1 { height: 0.25rem; }
        .h-16 { height: 4rem; }
        .pointer-events-none { pointer-events: none; }
        .container { width: 100%; margin-left: auto; margin-right: auto; padding-left: 1rem; padding-right: 1rem; }
        @media (min-width: 640px) { .container { max-width: 640px; } }
        @media (min-width: 768px) { .container { max-width: 768px; } }
        @media (min-width: 1024px) { .container { max-width: 1024px; } }
        @media (min-width: 1280px) { .container { max-width: 1280px; } }
        .mx-auto { margin-left: auto; margin-right: auto; }
        .px-4 { padding-left: 1rem; padding-right: 1rem; }
        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .space-x-2 > * + * { margin-left: 0.5rem; }
        .space-x-3 > * + * { margin-left: 0.75rem; }
        .flex-shrink-0 { flex-shrink: 0; }
        .text-sm { font-size: clamp(0.8rem, 2vw, 0.875rem); }
        .text-base { font-size: var(--base-font-size); }
        .text-lg { font-size: clamp(1rem, 2.5vw, 1.125rem); }
        .text-xl { font-size: clamp(1.125rem, 3vw, 1.25rem); }
        .font-bold { font-weight: 700; }
        .font-medium { font-weight: 500; }
        .font-semibold { font-weight: 600; }
        .text-slate-50 { color: var(--light); }
        .text-slate-200 { color: #e2e8f0; }
        .text-cyan-400 { color: var(--primary); }
        .text-slate-900 { color: var(--dark); }
        .whitespace-nowrap { white-space: nowrap; }
        .rounded-lg { border-radius: 0.5rem; }
        .rounded-xl { border-radius: 0.75rem; }
        .rounded-md { border-radius: 0.375rem; }
        .bg-cyan-400 { background-color: var(--primary); }
        .bg-slate-900\/45 { background-color: rgba(15, 23, 42, 0.45); }
        .bg-slate-800\/50 { background-color: rgba(30, 41, 59, 0.5); }
        .bg-slate-800\/30 { background-color: rgba(30, 41, 59, 0.3); }
        .bg-gradient-to-br.from-cyan-400.to-indigo-500 { background-image: linear-gradient(to bottom right, var(--primary), var(--secondary)); }
        .transition-colors { transition-property: color, background-color, border-color, text-decoration-color, fill, stroke; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); transition-duration: 150ms; }
        .transition-all { transition-property: all; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); transition-duration: 150ms; }
        .duration-300 { transition-duration: 300ms; }
        .hover\:text-white:hover { color: #fff; }
        .hover\:text-cyan-200:hover { color: var(--secondary); }
        .hover\:bg-slate-800\/50:hover { background-color: rgba(30, 41, 59, 0.5); }
        .hover\:bg-slate-800\/30:hover { background-color: rgba(30, 41, 59, 0.3); }
        .hover\:bg-slate-700\/50:hover { background-color: rgba(51, 65, 85, 0.5); }
        .w-6 { width: 1.5rem; }
        .h-6 { height: 1.5rem; }
        .w-8 { width: 2rem; }
        .h-8 { height: 2rem; }
        .w-10 { width: 2.5rem; }
        .h-10 { height: 2.5rem; }
        .w-12 { width: 3rem; }
        .h-12 { height: 3rem; }
        .w-14 { width: 3.5rem; }
        .h-14 { height: 3.5rem; }
        .w-auto { width: auto; }
        .h-auto { height: auto; }
        .max-w-\[120px\] { max-width: 120px; }
        .max-w-\[140px\] { max-width: 140px; }
        .max-w-\[160px\] { max-width: 160px; }
        .max-w-\[180px\] { max-width: 180px; }
        .object-contain { object-fit: contain; }
        .p-2 { padding: 0.5rem; }
        .px-1\.5 { padding-left: 0.375rem; padding-right: 0.375rem; }
        .px-2 { padding-left: 0.5rem; padding-right: 0.5rem; }
        .px-2\.5 { padding-left: 0.625rem; padding-right: 0.625rem; }
        .px-3 { padding-left: 0.75rem; padding-right: 0.75rem; }
        .px-4 { padding-left: 1rem; padding-right: 1rem; }
        .py-1 { padding-top: 0.25rem; padding-bottom: 0.25rem; }
        .py-1\.5 { padding-top: 0.375rem; padding-bottom: 0.375rem; }
        .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
        .text-xs { font-size: clamp(0.7rem, 1.5vw, 0.75rem); }
        .hidden { display: none; }
        .block { display: block; }
        .flex-1 { flex: 1 1 0%; }
        .justify-center { justify-content: center; }
        .mx-1 { margin-left: 0.25rem; margin-right: 0.25rem; }
        .mx-2 { margin-left: 0.5rem; margin-right: 0.5rem; }
        .mx-3 { margin-left: 0.75rem; margin-right: 0.75rem; }
        .mx-4 { margin-left: 1rem; margin-right: 1rem; }
        .min-w-0 { min-width: 0; }
        .space-x-1 > * + * { margin-left: 0.25rem; }
        .space-x-1\.5 > * + * { margin-left: 0.375rem; }
        .space-x-2 > * + * { margin-left: 0.5rem; }
        .space-x-3 > * + * { margin-left: 0.75rem; }
        .space-x-4 > * + * { margin-left: 1rem; }
        .space-x-6 > * + * { margin-left: 1.5rem; }
        .space-y-2 > * + * { margin-top: 0.5rem; }
        .pt-2 { padding-top: 0.5rem; }
        .border-t { border-top-width: 1px; }
        .border-slate-700\/50 { border-color: rgba(51, 65, 85, 0.5); }
        .focus\:outline-none:focus { outline: 2px solid transparent; outline-offset: 2px; }
        .focus\:ring-2:focus { --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color); --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color); box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000); }
        .focus\:ring-cyan-400:focus { --tw-ring-opacity: 1; --tw-ring-color: rgb(22 211 238 / var(--tw-ring-opacity)); }
        .underline { text-decoration-line: underline; }
        .decoration-2 { text-decoration-thickness: 2px; }
        .decoration-cyan-400 { text-decoration-color: var(--primary); }
        
        /* Navigation Active Link Styles */
        .nav-link-active {
            color: var(--secondary) !important;
            font-weight: 700 !important;
            background-color: rgba(0, 212, 170, 0.15) !important;
            text-decoration: underline !important;
            text-decoration-color: var(--secondary) !important;
            text-decoration-thickness: 2px !important;
            text-underline-offset: 6px !important;
        }
        .nav-link-active:hover {
            color: var(--secondary) !important;
            background-color: rgba(0, 212, 170, 0.25) !important;
        }
        
        /* Global Button Styles */
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            color: white;
            position: relative;
            z-index: 10;
            pointer-events: auto;
            cursor: pointer;
        }
        .btn-primary:hover {
            background-color: #0052CC;
            border-color: #0052CC;
            color: white;
        }
        .btn-secondary {
            background-color: var(--secondary);
            border-color: var(--secondary);
            color: var(--dark);
        }
        .btn-secondary:hover {
            background-color: #00B894;
            border-color: #00B894;
            color: var(--dark);
        }
        .underline-offset-4 { text-underline-offset: 4px; }
        @media (min-width: 640px) {
            .sm\:px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
            .sm\:space-x-3 > * + * { margin-left: 0.75rem; }
            .sm\:h-10 { height: 2.5rem; }
            .sm\:w-10 { width: 2.5rem; }
            .sm\:text-base { font-size: 1rem; }
            .sm\:text-xs { font-size: 0.75rem; }
            .sm\:px-2 { padding-left: 0.5rem; padding-right: 0.5rem; }
            .sm\:px-2\.5 { padding-left: 0.625rem; padding-right: 0.625rem; }
            .sm\:py-1 { padding-top: 0.25rem; padding-bottom: 0.25rem; }
            .sm\:py-1\.5 { padding-top: 0.375rem; padding-bottom: 0.375rem; }
        }
        @media (min-width: 768px) {
            .md\:hidden { display: none; }
            .md\:flex { display: flex; }
            .md\:h-18 { height: 4.5rem; }
            .md\:h-20 { height: 5rem; }
        }
        @media (min-width: 1024px) {
            .lg\:px-8 { padding-left: 2rem; padding-right: 2rem; }
            .lg\:h-14 { height: 3.5rem; }
            .lg\:w-14 { width: 3.5rem; }
            .lg\:text-lg { font-size: 1.125rem; }
            .lg\:text-sm { font-size: 0.875rem; }
            .lg\:px-3 { padding-left: 0.75rem; padding-right: 0.75rem; }
            .lg\:space-x-3 > * + * { margin-left: 0.75rem; }
        }
        @media (min-width: 1280px) {
            .xl\:text-xl { font-size: 1.25rem; }
            .xl\:px-4 { padding-left: 1rem; padding-right: 1rem; }
            .xl\:text-base { font-size: 1rem; }
            .xl\:space-x-4 > * + * { margin-left: 1rem; }
        }
        @media (min-width: 1536px) {
            .\32xl\:text-base { font-size: 1rem; }
            .\32xl\:px-4 { padding-left: 1rem; padding-right: 1rem; }
            .\32xl\:space-x-6 > * + * { margin-left: 1.5rem; }
        }
        /* Navigation specific fixes */
        .nav-link-item {
            text-decoration: none !important;
        }
        .nav-link-item.underline {
            text-decoration: underline !important;
        }
        a.nav-link-item {
            text-decoration: none !important;
        }
        nav a:not(.underline) {
            text-decoration: none !important;
        }
        /* Remove default link styling */
        nav a {
            text-decoration: none;
        }
        nav a.underline {
            text-decoration: underline;
        }
    </style>
    <style nonce="{{ $cspNonce ?? '' }}">
        /* Typography System */
        /* Body: System fonts for optimal legibility */
        /* Responsive Typography */
        html {
            font-size: var(--base-font-size);
            -webkit-text-size-adjust: 100%;
            -moz-text-size-adjust: 100%;
            text-size-adjust: 100%;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: var(--base-font-size);
            line-height: 1.6;
            line-height: 1.6;
            color: var(--dark);
            background-color: var(--light);
        }
        
        /* Headings: Plus Jakarta Sans for modern look */
        h1, h2, h3, h4, h5, h6,
        .h1, .h2, .h3, .h4, .h5, .h6 {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-weight: 700;
            line-height: 1.2;
            color: var(--dark);
            margin-top: 0;
        }
        
        /* Enhanced hierarchy with larger size differences - Responsive */
        h1, .h1 {
            font-size: var(--h1-size);
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 1.5rem;
        }
        
        h2, .h2 {
            font-size: var(--h2-size);
            font-weight: 700;
            letter-spacing: -0.01em;
            margin-bottom: 1.25rem;
        }
        
        h3, .h3 {
            font-size: var(--h3-size);
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        h4, .h4 {
            font-size: var(--h4-size);
            font-weight: 600;
            margin-bottom: 0.875rem;
        }
        
        h5, .h5 {
            font-size: var(--h5-size);
            font-weight: 600;
            margin-bottom: 0.75rem;
        }
        
        h6, .h6 {
            font-size: var(--h6-size);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        p {
            font-size: var(--p-size);
            line-height: 1.6;
        }
        
        small, .small {
            font-size: var(--small-size);
        }
        .top-teal-strip {
            display: none;
        }
        .main-header {
            background: linear-gradient(135deg, var(--dark) 0%, var(--primary) 45%, #0052CC 100%);
            background-size: 200% 200%;
            animation: headerGradient 12s ease infinite;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.4);
            padding: 22px 0 18px;
            color: var(--light);
            position: sticky;
            top: 0;
            z-index: 1100;
        }
        .main-header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 50%, var(--primary) 100%);
            background-size: 200% 200%;
            animation: shimmer 8s linear infinite;
            z-index: 0;
        }
        .main-header::after {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top right, rgba(14, 165, 233, 0.25), transparent 55%),
                        radial-gradient(circle at bottom left, rgba(99, 102, 241, 0.25), transparent 50%);
            pointer-events: none;
            z-index: 0;
        }
        .header-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #38bdf8 0%, #6366f1 100%);
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 2rem;
            margin-right: 20px;
            position: relative;
            z-index: 1;
            flex-shrink: 0;
        }
        .navbar-logo-img {
            height: 80px;
            width: auto;
            max-width: 280px;
            margin-right: 20px;
            object-fit: contain;
            display: block;
        }
        .navbar-brand {
            position: relative;
            font-weight: bold;
            font-size: 1.5rem;
            color: #f9fafb !important;
            display: flex;
            align-items: center;
            text-decoration: none;
            z-index: 1;
        }
        .navbar-brand-text {
            font-size: 1.5rem;
            white-space: nowrap;
        }
        .navbar-brand:hover {
            color: #bae6fd !important;
        }
        .main-navbar {
            position: relative;
            background-color: transparent;
            z-index: 1;
        }
        .main-navbar .container {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }
        .main-navbar .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.4);
        }
        .main-navbar .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(226, 232, 240, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        /* Old navigation styles removed - now using Tailwind CSS */
        /* Ensure navigation links have no underline unless active */
        .nav-link-item {
            text-decoration: none !important;
        }
        .nav-link-item.underline {
            text-decoration: underline !important;
        }
        
        /* Responsive navigation - prevent overlap */
        .nav-link-item {
            white-space: nowrap;
            display: inline-block;
        }
        
        /* Ensure navigation container doesn't overflow */
        nav ul {
            max-width: 100%;
        }
        
        nav li {
            flex-shrink: 0;
        }
        
        /* Remove separators/dots from navigation menu */
        nav ul li::before,
        nav ul li::after {
            content: none !important;
            display: none !important;
        }
        
        nav ul li + li::before {
            content: none !important;
            display: none !important;
        }
        
        /* Dynamic screen size adaptation */
        
        /* Mobile: < 768px */
        nav.nav-mobile .hidden.md\:flex {
            display: none !important;
        }
        
        /* Tablet: 768px - 1023px */
        nav.nav-tablet .nav-link-item {
            font-size: 0.75rem;
            padding: 0.375rem 0.5rem;
        }
        
        nav.nav-tablet .nav-link-item.tablet-optimized {
            font-size: 0.7rem;
            padding: 0.35rem 0.45rem;
        }
        
        nav.nav-tablet .flex.items-center.space-x-2 > div {
            gap: 0.25rem;
        }
        
        nav.nav-tablet .flex.items-center.space-x-2 > div a {
            font-size: 0.7rem;
            padding: 0.35rem 0.5rem;
        }
        
        /* Laptop: 1024px - 1279px */
        nav.nav-laptop .nav-link-item {
            font-size: 0.875rem;
            padding: 0.5rem 0.75rem;
        }
        
        /* Mobile Menu Styles */
        .mobile-menu-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 9999;
            display: none;
            pointer-events: none;
        }
        
        /* Show menu when not hidden and when open */
        .mobile-menu-container:not(.hidden) {
            display: flex !important;
        }
        
        .mobile-menu-container.mobile-menu-open {
            pointer-events: auto !important;
            display: flex !important;
            visibility: visible !important;
        }
        
        .mobile-menu-container.hidden {
            display: none !important;
            pointer-events: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
        }
        
        /* When menu is not open, ensure it doesn't block anything */
        .mobile-menu-container:not(.mobile-menu-open):not(.hidden) {
            pointer-events: none !important;
        }
        
        .mobile-menu-container:not(.mobile-menu-open) .mobile-menu-overlay {
            pointer-events: none !important;
        }
        
        /* Override md:hidden on mobile when menu is open */
        @media (max-width: 767px) {
            .mobile-menu-container.mobile-menu-open {
                display: flex !important;
            }
        }
        
        .mobile-menu-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(4px);
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }
        
        .mobile-menu-container.mobile-menu-open .mobile-menu-overlay {
            opacity: 1;
            pointer-events: auto;
        }
        
        .mobile-menu-content {
            position: relative;
            width: 85%;
            max-width: 320px;
            height: 100%;
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.5);
            transform: translateX(-100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            z-index: 10000;
            pointer-events: auto;
        }
        
        .mobile-menu-container.mobile-menu-open .mobile-menu-content {
            transform: translateX(0);
            visibility: visible;
            opacity: 1;
        }
        
        .mobile-menu-container:not(.mobile-menu-open) .mobile-menu-content {
            pointer-events: none;
        }
        
        /* Ensure menu content is visible when menu is open */
        .mobile-menu-container.mobile-menu-open .mobile-menu-list {
            visibility: visible;
            opacity: 1;
            display: flex;
            flex-direction: column;
        }
        
        .mobile-menu-container.mobile-menu-open .mobile-menu-item {
            visibility: visible;
            opacity: 1;
            display: block;
            width: 100%;
        }
        
        .mobile-menu-container.mobile-menu-open .mobile-menu-link {
            visibility: visible;
            opacity: 1;
            display: flex;
            flex-direction: row;
            align-items: center;
            width: 100%;
        }
        
        .mobile-menu-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 10001;
            pointer-events: auto;
        }
        
        .mobile-menu-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #ffffff;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        
        .mobile-menu-close {
            padding: 8px;
            color: #e2e8f0;
            background: transparent;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .mobile-menu-close:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }
        
        .mobile-menu-list {
            list-style: none;
            padding: 0;
            margin: 0;
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            visibility: visible;
        }
        
        .mobile-menu-item {
            margin: 0;
            padding: 0;
            display: block;
            visibility: visible;
            width: 100%;
        }
        
        .mobile-menu-link {
            display: flex;
            flex-direction: row;
            align-items: center;
            padding: 16px 20px;
            color: #e2e8f0;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
            position: relative;
            z-index: 10001;
            pointer-events: auto;
            cursor: pointer;
            width: 100%;
            box-sizing: border-box;
        }
        
        .mobile-menu-link i {
            font-size: 1.25rem;
            width: 24px;
            min-width: 24px;
            text-align: center;
            margin-right: 12px;
            flex-shrink: 0;
        }
        
        .mobile-menu-link span {
            flex: 1;
            white-space: nowrap;
        }
        
        .mobile-menu-link:hover {
            background: rgba(255, 255, 255, 0.05);
            color: #ffffff;
            border-left-color: var(--primary);
            padding-left: 24px;
        }
        
        .mobile-menu-link-active {
            background: rgba(0, 102, 255, 0.15);
            color: #ffffff;
            border-left-color: var(--primary);
            font-weight: 600;
        }
        
        .mobile-menu-link-active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--primary);
        }
        
        .mobile-menu-cta {
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .mobile-cta-button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 14px 20px;
            background: linear-gradient(135deg, var(--secondary) 0%, var(--primary) 100%);
            color: var(--dark);
            text-decoration: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 212, 170, 0.3);
        }
        
        .mobile-cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 212, 170, 0.5);
            color: var(--dark);
        }
        
        .mobile-menu-language {
            padding: 20px;
            background: rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 10001;
            pointer-events: auto;
        }
        
        .mobile-language-btn {
            position: relative;
            z-index: 10002;
            pointer-events: auto;
            cursor: pointer;
        }
        
        .mobile-language-label {
            font-size: 0.875rem;
            color: #94a3b8;
            margin-bottom: 12px;
            font-weight: 500;
        }
        
        .mobile-language-buttons {
            display: flex;
            gap: 10px;
        }
        
        .mobile-language-btn {
            flex: 1;
            padding: 10px 16px;
            text-align: center;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.2s ease;
            background: rgba(255, 255, 255, 0.05);
            color: #e2e8f0;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .mobile-language-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }
        
        .mobile-language-btn-active {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }
        
        /* Custom Scrollbar Styles - Blue Theme */
        /* Webkit browsers (Chrome, Safari, Edge) */
        ::-webkit-scrollbar {
            width: 12px;
            height: 12px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.1);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, var(--primary) 0%, #0052CC 100%);
            border-radius: 10px;
            border: 2px solid rgba(15, 23, 42, 0.1);
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #0052CC 0%, var(--primary) 100%);
        }
        
        ::-webkit-scrollbar-corner {
            background: rgba(15, 23, 42, 0.1);
        }
        
        /* Firefox */
        * {
            scrollbar-width: thin;
            scrollbar-color: var(--primary) rgba(15, 23, 42, 0.1);
        }
        
        /* Scrollbar for specific elements */
        .blog-content::-webkit-scrollbar,
        .mobile-menu-content::-webkit-scrollbar,
        .card-body::-webkit-scrollbar,
        .card::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        .blog-content::-webkit-scrollbar-thumb,
        .card-body::-webkit-scrollbar-thumb,
        .card::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 4px;
        }
        
        .blog-content::-webkit-scrollbar-thumb:hover,
        .card-body::-webkit-scrollbar-thumb:hover,
        .card::-webkit-scrollbar-thumb:hover {
            background: #0052CC;
        }
        
        /* Firefox for specific elements */
        .blog-content,
        .card-body,
        .card {
            scrollbar-width: thin;
            scrollbar-color: var(--primary) rgba(15, 23, 42, 0.1);
        }
        
        /* Smooth scrollbar for mobile menu - already covered above but keeping for specificity */
        .mobile-menu-content::-webkit-scrollbar {
            width: 6px;
        }
        
        .mobile-menu-content::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.2);
        }
        
        .mobile-menu-content::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 3px;
        }
        
        .mobile-menu-content::-webkit-scrollbar-thumb:hover {
            background: #0052CC;
        }
        
        /* Additional scrollbar styles for overflow elements */
        *[style*="overflow"]::-webkit-scrollbar,
        *[class*="overflow"]::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        *[style*="overflow"]::-webkit-scrollbar-thumb,
        *[class*="overflow"]::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 4px;
        }
        
        *[style*="overflow"]::-webkit-scrollbar-thumb:hover,
        *[class*="overflow"]::-webkit-scrollbar-thumb:hover {
            background: #0052CC;
        }
        
        /* Desktop: >= 1280px */
        nav.nav-desktop .nav-link-item {
            font-size: 1rem;
            padding: 0.5rem 1rem;
        }
        
        /* Responsive logo and brand text */
        @media (max-width: 640px) {
            nav .text-sm {
                font-size: 0.75rem;
            }
        }
        
        @media (min-width: 641px) and (max-width: 768px) {
            nav .text-sm {
                font-size: 0.875rem;
            }
        }
        
        @media (min-width: 769px) and (max-width: 1024px) {
            /* Tablet specific adjustments */
            nav.nav-tablet .hidden.md\:flex ul {
                gap: 0.25rem !important;
            }
        }
        
        @media (min-width: 1025px) and (max-width: 1280px) {
            /* Laptop adjustments */
            nav.nav-laptop .hidden.md\:flex ul {
                gap: 0.5rem !important;
            }
        }
        
        @media (min-width: 1281px) {
            /* Desktop - full spacing */
            nav.nav-desktop .hidden.md\:flex ul {
                gap: 1rem !important;
            }
        }
        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
            position: relative;
            z-index: 1;
        }
        .language-switcher {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .lang-button {
            background-color: rgba(15, 23, 42, 0.45);
            color: #e2e8f0;
            border: 1px solid transparent;
            border-radius: 6px;
            padding: 6px 12px;
            font-weight: 500;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .lang-button:hover {
            background-color: rgba(148, 163, 184, 0.25);
            color: #ffffff;
        }
        .lang-button.active {
            background-color: #38bdf8;
            color: #0f172a;
            border-color: rgba(255, 255, 255, 0.35);
        }
        .lang-text {
            color: #e2e8f0;
            font-weight: 500;
            font-size: 0.9rem;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 6px;
            transition: all 0.3s;
        }
        .lang-text:hover {
            color: #ffffff;
        }
        .lang-text.active {
            background-color: #38bdf8;
            color: #0f172a;
        }
        .admin-button {
            background: linear-gradient(135deg, #38bdf8 0%, #6366f1 100%);
            color: #0f172a;
            border: none;
            border-radius: 8px;
            padding: 8px 18px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
        }
        .admin-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(56, 189, 248, 0.35);
            color: #0f172a;
        }
        .hero-section {
            position: relative;
            background: radial-gradient(circle at top left, rgba(59, 130, 246, 0.25), transparent 55%),
                        radial-gradient(circle at bottom right, rgba(56, 189, 248, 0.2), transparent 50%),
                        linear-gradient(135deg, #0f172a 0%, #1e3a8a 45%, #1f4ed8 100%);
            color: #e2e8f0;
            padding: 110px 0 90px;
            overflow: hidden;
        }
        .hero-section-with-pattern {
            background-size: cover, auto, auto, auto;
            background-position: center, top left, bottom right, center;
            background-repeat: no-repeat, no-repeat, no-repeat, no-repeat;
            background-blend-mode: overlay, normal, normal, normal;
        }
        .hero-section::before,
        .hero-section::after {
            content: "";
            position: absolute;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            mix-blend-mode: screen;
            opacity: 0.18;
            filter: blur(0);
            animation: heroOrbs 18s ease-in-out infinite;
        }
        .hero-section::before {
            background: linear-gradient(135deg, rgba(56, 189, 248, 0.65), rgba(59, 130, 246, 0.2));
            top: -120px;
            right: -80px;
        }
        .hero-section::after {
            background: linear-gradient(135deg, rgba(129, 140, 248, 0.55), rgba(59, 130, 246, 0.25));
            bottom: -140px;
            left: -100px;
            animation-delay: -7s;
        }
        .hero-section .container {
            position: relative;
            z-index: 1;
        }
        .hero-section h1 {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: var(--h1-size);
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #f8fafc;
            text-shadow: 0 10px 25px rgba(15, 23, 42, 0.35);
        }
        
        .hero-section p.lead {
            color: rgba(226, 232, 240, 0.9);
            max-width: 680px;
            margin: 0 auto;
            font-size: clamp(1rem, 2.5vw, 1.25rem);
        }
        
        /* Futuristic Hero Enhancements */
        .hero-section-futuristic {
            position: relative;
            overflow: hidden;
        }
        
        /* Ensure all interactive elements are clickable */
        .hero-section a,
        .hero-section button,
        .hero-section .btn {
            position: relative;
            z-index: 20;
            pointer-events: auto !important;
            cursor: pointer !important;
        }
        
        /* Ensure cards and links throughout the page are clickable */
        section a,
        section button,
        section .btn,
        .card-modern a,
        .feature-card-modern a,
        nav a,
        nav button {
            position: relative;
            z-index: 10;
            pointer-events: auto !important;
            cursor: pointer !important;
        }
        
        /* Global fix for all links and buttons */
        a:not([disabled]),
        button:not([disabled]),
        .btn:not([disabled]),
        [role="button"]:not([disabled]) {
            pointer-events: auto !important;
            cursor: pointer !important;
        }
        
        /* Ensure navigation links work */
        .nav-link-item,
        .mobile-menu-link {
            pointer-events: auto !important;
            cursor: pointer !important;
            z-index: 100;
        }
        
        .particles-canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none !important;
            touch-action: none;
        }
        
        .gradient-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.4;
            animation: floatOrb 20s ease-in-out infinite;
            pointer-events: none !important;
            z-index: 0;
        }
        
        .orb-1 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(0, 102, 255, 0.6), rgba(0, 212, 170, 0.3));
            top: -200px;
            right: -100px;
            animation-delay: 0s;
        }
        
        .orb-2 {
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(0, 212, 170, 0.5), rgba(245, 158, 11, 0.2));
            bottom: -150px;
            left: -80px;
            animation-delay: -7s;
        }
        
        .orb-3 {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.4), rgba(0, 102, 255, 0.2));
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: -14s;
        }
        
        @keyframes floatOrb {
            0%, 100% {
                transform: translate(0, 0) scale(1);
            }
            33% {
                transform: translate(30px, -30px) scale(1.1);
            }
            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
        }
        
        .hero-content {
            position: relative;
            z-index: 10;
            pointer-events: auto;
        }
        
        .hero-badge {
            display: inline-block;
            padding: 8px 20px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #ffffff 0%, #00d4aa 50%, #0066ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradientShift 5s ease infinite;
            background-size: 200% 200%;
        }
        
        @keyframes gradientShift {
            0%, 100% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
        }
        
        .hero-subtitle {
            font-size: 1.25rem;
            line-height: 1.8;
            color: rgba(226, 232, 240, 0.95);
        }
        
        .hero-cta-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 40px;
            flex-wrap: wrap;
        }
        
        .btn-hero {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 28px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            z-index: 11;
            pointer-events: auto;
            cursor: pointer;
        }
        
        .btn-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-hero:hover::before {
            left: 100%;
        }
        
        .btn-hero-primary {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--primary) 100%);
            color: var(--dark);
            box-shadow: 0 4px 15px rgba(0, 212, 170, 0.4);
        }
        
        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 212, 170, 0.6);
            color: var(--dark);
        }
        
        .btn-hero-secondary {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #ffffff;
        }
        
        .btn-hero-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
            color: #ffffff;
        }
        
        .scroll-indicator {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
            pointer-events: none;
        }
        
        .mouse {
            width: 24px;
            height: 40px;
            border: 2px solid rgba(255, 255, 255, 0.5);
            border-radius: 15px;
            position: relative;
            margin: 0 auto 10px;
        }
        
        .wheel {
            width: 4px;
            height: 8px;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 2px;
            position: absolute;
            top: 8px;
            left: 50%;
            transform: translateX(-50%);
            animation: scrollWheel 2s infinite;
        }
        
        @keyframes scrollWheel {
            0% {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
            100% {
                opacity: 0;
                transform: translateX(-50%) translateY(15px);
            }
        }
        
        .arrow span {
            display: block;
            width: 8px;
            height: 8px;
            border-right: 2px solid rgba(255, 255, 255, 0.7);
            border-bottom: 2px solid rgba(255, 255, 255, 0.7);
            transform: rotate(45deg);
            margin: -4px auto;
            animation: arrowDown 2s infinite;
        }
        
        .arrow span:nth-child(2) {
            animation-delay: -0.2s;
        }
        
        .arrow span:nth-child(3) {
            animation-delay: -0.4s;
        }
        
        @keyframes arrowDown {
            0% {
                opacity: 0;
                transform: rotate(45deg) translate(-5px, -5px);
            }
            50% {
                opacity: 1;
            }
            100% {
                opacity: 0;
                transform: rotate(45deg) translate(5px, 5px);
            }
        }
        
        @media (max-width: 768px) {
            .hero-cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn-hero {
                width: 100%;
                max-width: 280px;
                justify-content: center;
            }
            
            .gradient-orb {
                display: none;
            }
        }
        .card {
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        
        /* Modern Card Styles */
        .card-modern {
            position: relative;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            z-index: 1;
            pointer-events: auto;
        }
        
        .card-modern a,
        .card-modern .btn,
        .card-modern button {
            position: relative;
            z-index: 15 !important;
            pointer-events: auto !important;
            cursor: pointer !important;
        }
        
        .card-modern .btn-modern,
        .card-modern .btn-primary,
        .card-modern a.btn,
        .card-modern a.btn-modern,
        .card-modern a.btn-primary {
            z-index: 15 !important;
            pointer-events: auto !important;
            cursor: pointer !important;
            position: relative !important;
        }
        
        /* Ensure card doesn't block clicks to buttons */
        .card-modern {
            pointer-events: auto;
        }
        
        /* Override for overlay only - already set but ensuring it */
        .card-modern .card-overlay,
        .card-image-wrapper .card-overlay {
            pointer-events: none !important;
        }
        
        .card-modern:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 102, 255, 0.15);
            border-color: rgba(0, 102, 255, 0.2);
        }
        
        .card-image-wrapper {
            position: relative;
            overflow: hidden;
            height: 200px;
            z-index: 0;
        }
        
        .card-modern .card-img-top {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .card-modern:hover .card-img-top {
            transform: scale(1.1);
        }
        
        .card-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, transparent 0%, rgba(0, 0, 0, 0.3) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none !important;
            z-index: 0;
        }
        
        .card-modern .card-body {
            position: relative;
            z-index: 2;
            pointer-events: auto !important;
        }
        
        .card-modern .card-body * {
            pointer-events: auto;
        }
        
        .card-modern .card-body a,
        .card-modern .card-body .btn,
        .card-modern .card-body button {
            pointer-events: auto !important;
            cursor: pointer !important;
        }
        
        .card-modern:hover .card-overlay {
            opacity: 1;
        }
        
        .card-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 5px;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            pointer-events: none;
            z-index: 1;
        }
        
        .card-modern:hover .card-badge {
            opacity: 1;
            transform: translateY(0);
        }
        
        .card-meta {
            margin-bottom: 10px;
        }
        
        .card-date {
            font-size: 0.85rem;
            color: rgba(0, 0, 0, 0.6);
            font-weight: 500;
        }
        
        .btn-modern {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            z-index: 10;
            pointer-events: auto;
            cursor: pointer;
        }
        
        .btn-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
            pointer-events: none;
            z-index: -1;
        }
        
        .btn-modern:hover::before {
            left: 100%;
        }
        
        .btn-modern i {
            transition: transform 0.3s ease;
        }
        
        .btn-modern:hover i {
            transform: translateX(5px);
        }
        footer {
            background-color: #1a202c;
            color: white;
            padding: 60px 0 20px;
        }
        .footer-logo {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
            margin-right: 10px;
        }
        .footer-brand {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .footer-brand h5 {
            margin: 0;
            font-weight: bold;
        }
        .footer-column h5 {
            color: white;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .footer-column ul {
            list-style: none;
            padding: 0;
        }
        .footer-column ul li {
            margin-bottom: 10px;
        }
        .footer-column ul li a {
            color: #cbd5e0;
            text-decoration: none;
            transition: color 0.3s;
        }
        .footer-column ul li a:hover {
            color: white;
        }
        .footer-contact p {
            color: #cbd5e0;
            margin-bottom: 10px;
        }
        .footer-contact a {
            color: var(--primary);
            text-decoration: none;
        }
        .footer-contact a:hover {
            text-decoration: underline;
        }
        .footer-copyright {
            border-top: 1px solid #374151;
            padding-top: 20px;
            margin-top: 40px;
            text-align: center;
            color: #cbd5e0;
        }
        /* Floating CTA Button */
        .floating-cta-button {
            position: fixed;
            bottom: 90px;
            left: 20px;
            z-index: 999;
            animation: floatPulse 3s ease-in-out infinite;
        }
        .floating-cta-link {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 14px 20px;
            background: linear-gradient(135deg, var(--secondary) 0%, var(--primary) 100%);
            color: var(--dark);
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            font-size: 14px;
            box-shadow: 0 4px 15px rgba(0, 212, 170, 0.4), 0 2px 8px rgba(0, 102, 255, 0.3);
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        .floating-cta-link:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 6px 20px rgba(0, 212, 170, 0.6), 0 4px 12px rgba(0, 102, 255, 0.4);
            color: #0f172a;
            text-decoration: none;
        }
        .floating-cta-link .cta-text {
            display: inline-block;
        }
        @keyframes floatPulse {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        @media (max-width: 768px) {
            .floating-cta-button {
                bottom: 160px;
                left: 10px;
            }
            .floating-cta-link {
                padding: 12px 16px;
                font-size: 12px;
            }
            .floating-cta-link .cta-text {
                display: none;
            }
            .floating-cta-link i {
                margin: 0 !important;
            }
        }

        .whatsapp-float {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 998;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .whatsapp-float a {
            display: flex;
            align-items: center;
            gap: 10px;
            background-color: #25d366;
            color: white;
            text-decoration: none;
            padding: 14px 20px;
            border-radius: 50px;
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            font-weight: bold;
            font-size: 14px;
            line-height: 1;
        }
        .whatsapp-float a:hover {
            transform: scale(1.05);
            box-shadow: 2px 4px 12px rgba(0, 0, 0, 0.4);
            color: white;
            text-decoration: none;
        }
        .whatsapp-float a i {
            font-size: 24px;
        }
        .whatsapp-float a .whatsapp-text {
            white-space: nowrap;
        }
        @media (max-width: 768px) {
            .whatsapp-float {
                bottom: 80px;
                right: 10px;
            }
            .whatsapp-float a {
                padding: 12px 16px;
                font-size: 12px;
            }
            .whatsapp-float a .whatsapp-text {
                display: none;
            }
            .whatsapp-float a i {
                font-size: 20px;
                margin: 0;
            }
        }

        .why-choose-section {
            background-color: white;
            padding: 80px 0 20px;
        }
        .why-choose-title {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: clamp(1.75rem, 5vw, 3rem);
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #1a202c;
            text-align: center;
            margin-bottom: 30px;
        }
        .why-choose-content-wrapper {
            display: flex;
            align-items: center;
            gap: 40px;
            width: 100%;
            margin: 0 0 60px 0;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 30px 40px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .why-choose-content-wrapper:has(.why-choose-image) .why-choose-description {
            text-align: left;
        }
        .why-choose-content-wrapper:not(:has(.why-choose-image)) .why-choose-description {
            text-align: center;
        }
        .why-choose-description {
            font-size: 1.15rem;
            color: #4a5568;
            text-align: left;
            flex: 1;
            line-height: 1.8;
            margin: 0;
        }
        .why-choose-image {
            flex-shrink: 0;
            width: 400px;
            display: block;
        }
        .why-choose-img {
            width: 100%;
            height: auto;
            object-fit: contain;
            border-radius: 8px;
            display: block;
            max-width: 100%;
        }
        @media (max-width: 992px) {
            .why-choose-content-wrapper {
                flex-direction: column;
                gap: 30px;
                padding: 25px 30px;
            }
            .why-choose-description {
                text-align: center;
            }
            .why-choose-image {
                width: 100%;
                max-width: 500px;
            }
        }
        @media (max-width: 768px) {
            .why-choose-content-wrapper {
                padding: 25px 25px;
                margin-bottom: 40px;
            }
            .why-choose-description {
                font-size: 1rem;
            }
            .why-choose-title {
                margin-bottom: 20px;
            }
            .why-choose-image {
                max-width: 400px;
            }
        }
        @media (max-width: 576px) {
            .why-choose-content-wrapper {
                padding: 20px 20px;
                margin-bottom: 30px;
                gap: 20px;
            }
            .why-choose-description {
                font-size: 0.95rem;
            }
            .why-choose-image {
                max-width: 100%;
            }
        }
        
        /* Card Animation Styles */
        .animate-card {
            opacity: 0;
            transform: translateY(50px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }
        .animate-card.animated {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Fallback for no-js: show cards immediately */
        .no-js .animate-card {
            opacity: 1;
            transform: translateY(0);
        }
        
        .feature-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 0;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .feature-card:hover {
            transform: translateY(-5px) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        /* Modern Feature Cards with Glassmorphism */
        .feature-card-modern {
            position: relative;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            z-index: 1;
            pointer-events: auto;
            overflow: visible;
        }
        
        .feature-card-glow {
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            pointer-events: none;
            z-index: 0;
            background: radial-gradient(circle, rgba(0, 102, 255, 0.1), transparent 70%);
            opacity: 0;
            transition: opacity 0.5s ease;
            pointer-events: none;
        }
        
        .feature-card-modern:hover .feature-card-glow {
            opacity: 1;
        }
        
        .feature-card-modern:hover {
            transform: translateY(-10px) scale(1.02) !important;
            box-shadow: 0 20px 40px rgba(0, 102, 255, 0.2), 0 0 0 1px rgba(255, 255, 255, 0.5);
        }
        
        .feature-icon-animated {
            position: relative;
        }
        
        .icon-pulse {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: var(--primary);
            opacity: 0;
            animation: none; /* Desactivar animación por defecto */
        }
        
        /* Activar animación solo en hover */
        .feature-card-modern:hover .icon-pulse {
            animation: iconPulse 2s ease-in-out infinite;
        }
        
        @keyframes iconPulse {
            0% {
                transform: translate(-50%, -50%) scale(0.8);
                opacity: 0.7;
            }
            50% {
                transform: translate(-50%, -50%) scale(1.2);
                opacity: 0.3;
            }
            100% {
                transform: translate(-50%, -50%) scale(1.5);
                opacity: 0;
            }
        }
        
        .feature-card-footer {
            margin-top: auto;
            padding-top: 15px;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .feature-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: var(--primary);
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            z-index: 10;
            pointer-events: auto;
            cursor: pointer;
        }
        
        .feature-card-modern:hover .feature-link {
            gap: 10px;
            color: var(--secondary);
        }
        
        .feature-link i {
            transition: transform 0.3s ease;
        }
        
        .feature-card-modern:hover .feature-link i {
            transform: translateX(5px);
        }
        .feature-card-header {
            background: linear-gradient(to bottom, #bfdbfe 0%, #e0f2fe 100%);
            padding: 50px 20px 40px;
            text-align: center;
            flex: 0 0 auto;
        }
        .feature-icon {
            width: 90px;
            height: 90px;
            background: var(--primary);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2.8rem;
            margin-bottom: 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        .feature-icon i {
            stroke-width: 2;
            stroke: white;
            fill: none;
        }
        /* SVG container - fixed size for all cards */
        .feature-icon-svg-container {
            width: 200px;
            height: 150px;
            background: transparent;
            border-radius: 0;
            box-shadow: none;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }
        .feature-icon-svg {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }
        .feature-icon img {
            display: block;
        }
        .feature-card-body {
            background: white;
            padding: 35px 25px;
            text-align: center;
            flex: 1 1 auto;
        }
        .feature-card-title {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 15px;
        }
        .feature-card-description {
            color: #4a5568;
            line-height: 1.7;
            font-size: 0.95rem;
        }
        .content-section {
            background-color: white;
            padding: 60px 0;
        }
        
        /* Blog Content Styles */
        .blog-content {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.8;
            font-size: 1.1rem;
            color: #374151;
        }
        .blog-content h1,
        .blog-content h2,
        .blog-content h3,
        .blog-content h4,
        .blog-content h5,
        .blog-content h6 {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin-top: 1.5em;
            margin-bottom: 0.75em;
            font-weight: 700;
            color: #1f2937;
            line-height: 1.3;
        }
        .blog-content h1 { 
            font-size: clamp(1.75rem, 5vw, 3rem);
            font-weight: 800;
            letter-spacing: -0.02em;
        }
        .blog-content h2 { 
            font-size: clamp(1.5rem, 4vw, 2.5rem);
            font-weight: 700;
            letter-spacing: -0.01em;
        }
        .blog-content h3 { 
            font-size: clamp(1.25rem, 3.5vw, 2rem);
            font-weight: 700;
        }
        .blog-content h4 { 
            font-size: clamp(1.125rem, 3vw, 1.5rem);
            font-weight: 600;
        }
        .blog-content h5 { 
            font-size: clamp(1rem, 2.5vw, 1.25rem);
            font-weight: 600;
        }
        .blog-content h6 { 
            font-size: clamp(0.875rem, 2vw, 1.125rem);
            font-weight: 600;
        }
        .blog-content p {
            margin-bottom: 1em;
        }
        .blog-content ul,
        .blog-content ol {
            margin: 1em 0 !important;
            padding-left: 2em !important;
            list-style-position: outside !important;
            display: block !important;
        }
        .blog-content ul {
            list-style-type: disc !important;
        }
        .blog-content ol {
            list-style-type: decimal !important;
        }
        .blog-content li {
            margin: 0.5em 0 !important;
            line-height: 1.8 !important;
            display: list-item !important;
            list-style-position: outside !important;
        }
        /* Support for nested lists */
        .blog-content ul ul,
        .blog-content ol ol,
        .blog-content ul ol,
        .blog-content ol ul {
            margin: 0.5em 0 !important;
            padding-left: 1.5em !important;
        }
        .blog-content ul ul {
            list-style-type: circle !important;
        }
        .blog-content ul ul ul {
            list-style-type: square !important;
        }
        /* Ensure list items are visible */
        .blog-content li::marker {
            color: var(--primary) !important;
            font-weight: bold !important;
        }
        /* Support for Quill-generated lists */
        .blog-content [class*="ql-list"],
        .blog-content .ql-list {
            list-style-type: disc !important;
            padding-left: 2em !important;
        }
        .blog-content [class*="ql-list"] li,
        .blog-content .ql-list li {
            display: list-item !important;
            list-style-type: disc !important;
        }
        /* Force list display for any ul/ol tags - override any global styles */
        .blog-content > ul,
        .blog-content > ol,
        .blog-content ul,
        .blog-content ol {
            list-style: disc outside !important;
            list-style-type: disc !important;
        }
        .blog-content ol,
        .blog-content > ol {
            list-style: decimal outside !important;
            list-style-type: decimal !important;
        }
        /* Ensure list items show markers - override any global list-style: none */
        .blog-content li,
        .blog-content ul li,
        .blog-content ol li {
            list-style: inherit !important;
            list-style-type: inherit !important;
            display: list-item !important;
        }
        /* Specific override for any parent styles that might hide lists */
        .blog-content * {
            /* Don't apply list-style: none to blog content */
        }
        .blog-content ul[style*="list-style"],
        .blog-content ol[style*="list-style"] {
            list-style-type: disc !important;
        }
        .blog-content ol[style*="list-style"] {
            list-style-type: decimal !important;
        }
        .blog-content a {
            color: var(--primary);
            text-decoration: underline;
        }
        .blog-content a:hover {
            color: var(--primary);
        }
        .blog-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 1.5em 0;
            display: block;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        /* Ensure images within blog content are clickable and display correctly */
        .blog-content img[src*="storage"],
        .blog-content img[src*="blogs/content"] {
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .blog-content img[src*="storage"]:hover,
        .blog-content img[src*="blogs/content"]:hover {
            transform: scale(1.02);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        }
        
        /* Handle broken images */
        .blog-content img[src=""],
        .blog-content img:not([src]) {
            display: none;
        }
        
        /* Lazy Loading Styles */
        img[loading="lazy"] {
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        img[loading="lazy"].loaded {
            opacity: 1;
        }
        
        /* Placeholder for lazy images */
        img[loading="lazy"]:not(.loaded) {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading-shimmer 1.5s infinite;
        }
        
        @keyframes loading-shimmer {
            0% {
                background-position: 200% 0;
            }
            100% {
                background-position: -200% 0;
            }
        }
        .blog-content blockquote {
            border-left: 4px solid var(--accent);
            padding-left: 1em;
            margin: 1.5em 0;
            font-style: italic;
            color: #6b7280;
        }
        .blog-content code {
            background-color: #f3f4f6;
            padding: 0.2em 0.4em;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
        }
        .blog-content pre {
            background-color: #1f2937;
            color: #f9fafb;
            padding: 1em;
            border-radius: 8px;
            overflow-x: auto;
            margin: 1.5em 0;
        }
        .blog-content pre code {
            background-color: transparent;
            padding: 0;
            color: inherit;
        }
        .blog-content iframe {
            max-width: 100%;
            border-radius: 8px;
        }
        .blog-content strong {
            font-weight: 600;
        }
        .blog-content em {
            font-style: italic;
        }
        .blog-content u {
            text-decoration: underline;
        }
        .blog-content s {
            text-decoration: line-through;
        }
        .blog-content [style*="text-align"] {
            display: block;
        }
        .blog-content [style*="text-align: center"] {
            text-align: center;
        }
        .blog-content [style*="text-align: right"] {
            text-align: right;
        }
        .blog-content [style*="text-align: justify"] {
            text-align: justify;
        }
        .blog-content video {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 1.5em 0;
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            flex-wrap: wrap;
            gap: 15px;
        }
        /* Responsive Navigation Font Sizes */
        /* Large laptops and desktops (1400px+) */
        @media (min-width: 1400px) {
            .main-navbar .nav-link {
                font-size: 1.05rem;
                padding: 10px 18px !important;
            }
            .navbar-brand-text {
                font-size: 1.6rem;
            }
            .navbar-logo-img {
                height: 85px;
                max-width: 300px;
            }
        }
        
        /* Medium laptops (1200px - 1399px) - default sizes */
        @media (min-width: 1200px) and (max-width: 1399px) {
            .main-navbar .nav-link {
                font-size: 1rem;
                padding: 10px 16px !important;
            }
            .navbar-brand-text {
                font-size: 1.5rem;
            }
        }
        
        /* Small laptops (992px - 1199px) */
        @media (min-width: 992px) and (max-width: 1199px) {
            .main-navbar .nav-link {
                font-size: 0.95rem;
                padding: 9px 14px !important;
                margin: 0 3px;
            }
            .navbar-brand-text {
                font-size: 1.4rem;
            }
            .navbar-logo-img {
                height: 75px;
                max-width: 260px;
                margin-right: 15px;
            }
            .header-logo {
                width: 75px;
                height: 75px;
                font-size: 1.8rem;
                margin-right: 15px;
            }
        }
        
        /* Tablets (768px - 991px) */
        @media (min-width: 768px) and (max-width: 991px) {
            .main-navbar .nav-link {
                font-size: 0.875rem;
                padding: 8px 12px !important;
                margin: 0 2px;
            }
            .navbar-brand-text {
                font-size: 1.3rem;
            }
            .navbar-logo-img {
                height: 70px;
                max-width: 240px;
                margin-right: 12px;
            }
            .header-logo {
                width: 70px;
                height: 70px;
                font-size: 1.6rem;
                margin-right: 12px;
            }
            .lang-button,
            .lang-text {
                font-size: 0.85rem;
                padding: 5px 10px;
            }
            .admin-button {
                font-size: 0.9rem;
                padding: 7px 15px;
            }
        }
        
        /* Small tablets and large phones (576px - 767px) */
        @media (min-width: 576px) and (max-width: 767px) {
            .main-navbar .nav-link {
                font-size: 0.8rem;
                padding: 7px 10px !important;
                margin: 0 2px;
            }
            .navbar-brand-text {
                font-size: 1.2rem;
            }
            .navbar-logo-img {
                height: 65px;
                max-width: 220px;
                margin-right: 10px;
            }
            .header-logo {
                width: 65px;
                height: 65px;
                font-size: 1.5rem;
                margin-right: 10px;
            }
            .lang-button,
            .lang-text {
                font-size: 0.8rem;
                padding: 5px 9px;
            }
            .admin-button {
                font-size: 0.85rem;
                padding: 6px 12px;
            }
        }
        
        /* Mobile phones (below 576px) */
        @media (max-width: 575px) {
            .main-navbar .nav-link {
                font-size: 0.9rem;
                padding: 8px 12px !important;
            }
            .navbar-brand-text {
                font-size: 1.1rem;
            }
            .navbar-logo-img {
                height: 60px;
                max-width: 200px;
                margin-right: 10px;
            }
            .header-logo {
                width: 60px;
                height: 60px;
                font-size: 1.4rem;
                margin-right: 10px;
            }
            .lang-button,
            .lang-text {
                font-size: 0.75rem;
                padding: 4px 8px;
            }
            .admin-button {
                font-size: 0.8rem;
                padding: 6px 10px;
            }
        }
        
        @media (max-width: 768px) {
            .section-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .section-title {
                font-size: 2rem;
            }
            .why-choose-title {
                font-size: clamp(1.5rem, 6vw, 2.25rem);
            }
            .header-right {
                margin-top: 15px;
                width: 100%;
                justify-content: space-between;
            }
            .main-navbar .navbar-nav {
                margin: 15px 0;
            }
        }
        .section-title {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: clamp(1.5rem, 4vw, 2.75rem);
            font-weight: 700;
            letter-spacing: -0.01em;
            color: var(--dark);
            margin: 0;
        }
        .section-link {
            color: var(--primary);
            text-decoration: none;
            font-size: 1.1rem;
            font-weight: 500;
            transition: color 0.3s;
            position: relative;
            z-index: 10;
            pointer-events: auto;
            cursor: pointer;
        }
        .section-link:hover {
            color: var(--primary);
            text-decoration: none;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #9ca3af;
            font-size: 1.1rem;
        }
        .home-projects-section {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            padding: 60px 0;
            color: white;
        }
        .home-blog-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 60px 0;
            color: white;
        }
        .home-projects-section .section-title,
        .home-blog-section .section-title {
            color: white;
        }
        .home-projects-section .section-link,
        .home-blog-section .section-link {
            color: #93c5fd;
        }
        .home-projects-section .section-link:hover,
        .home-blog-section .section-link:hover {
            color: #dbeafe;
        }
        .home-projects-section .empty-state,
        .home-blog-section .empty-state {
            color: #cbd5e0;
        }
        .technologies-section {
            background: linear-gradient(180deg, #eff6ff 0%, #dbeafe 100%);
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }
        .technologies-section::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 0;
        }
        .technologies-section::after {
            content: '';
            position: absolute;
            bottom: -50px;
            left: -50px;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 0;
        }
        .technologies-header {
            text-align: center;
            margin-bottom: 60px;
            position: relative;
            z-index: 1;
        }
        .technologies-title {
            font-size: clamp(1.75rem, 5vw, 3rem);
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 15px;
            background: linear-gradient(135deg, var(--dark) 0%, var(--primary) 100%);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -1px;
        }
        .technologies-subtitle {
            font-size: clamp(1rem, 2.5vw, 1.25rem);
            color: #64748b;
            max-width: 600px;
            margin: 0 auto;
        }
        .technologies-grid {
            background: transparent;
            padding: 0;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 30px;
            position: relative;
            z-index: 1;
            margin-bottom: 40px;
        }
        .tech-card {
            background: white;
            border-radius: 20px;
            padding: 30px 20px;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            border: 1px solid #f1f5f9;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 1;
            pointer-events: auto;
        }
        .tech-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: #e2e8f0;
        }
        .tech-icon {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            font-size: 2.5rem;
            transition: transform 0.3s ease;
            background: #f8fafc; 
            color: #64748b;
        }
        .tech-card:hover .tech-icon {
            transform: scale(1.1) rotate(5deg);
        }
        .tech-icon.blue { background: rgba(0, 102, 255, 0.1); color: var(--primary); }
        .tech-icon.purple { background: rgba(168, 85, 247, 0.1); color: #a855f7; }
        .tech-icon.purple-light { background: rgba(192, 132, 252, 0.1); color: #c084fc; }
        .tech-icon.yellow { background: rgba(251, 191, 36, 0.1); color: #fbbf24; }
        .tech-icon.pink { background: rgba(244, 114, 182, 0.1); color: #f472b6; }
        .tech-icon.python { background: rgba(0, 102, 255, 0.1); color: var(--primary); }
        .tech-icon.tableau { background: rgba(99, 102, 241, 0.1); color: #6366f1; }
        
        .tech-icon img,
        .tech-icon-image {
            width: 48px;
            height: 48px;
            object-fit: contain;
        }
        .tech-name {
            font-weight: 700;
            color: #334155;
            font-size: 1.1rem;
            margin-bottom: 8px;
        }
        .tech-description {
            color: #94a3b8;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-top: 0;
        }
        .technologies-link {
            color: var(--primary);
            text-decoration: none;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            border-radius: 50px;
            background: rgba(59, 130, 246, 0.1);
            position: relative;
            z-index: 10;
            pointer-events: auto;
            cursor: pointer;
        }
        .technologies-link:hover {
            color: white;
            background: var(--primary);
            transform: translateX(5px);
        }
        @media (max-width: 768px) {
            .technologies-title {
                font-size: 2.2rem;
            }
            .technologies-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 480px) {
            .technologies-grid {
                grid-template-columns: 1fr;
            }
        }
        .contact-section {
            background-color: #f8f9fa;
            padding: 80px 0;
            min-height: 100vh;
        }
        .contact-header {
            text-align: center;
            margin-bottom: 50px;
        }
        .contact-title {
            font-size: 3rem;
            font-weight: bold;
            color: #1a202c;
            margin-bottom: 20px;
            position: relative;
            display: inline-block;
            letter-spacing: -0.5px;
        }
        .contact-title::after {
            content: '';
            position: absolute;
            right: -12px;
            top: 50%;
            transform: translateY(-50%);
            width: 1px;
            height: 50%;
            background-color: #cbd5e0;
        }
        .contact-subtitle {
            font-size: 1.1rem;
            color: #4a5568;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.6;
        }
        .contact-info-card,
        .contact-form-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 40px;
            height: 100%;
        }
        .contact-card-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #1a202c;
            margin-bottom: 30px;
        }
        .contact-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 30px;
        }
        .contact-icon-wrapper {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #93c5fd 0%, #60a5fa 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.3rem;
            margin-right: 20px;
            flex-shrink: 0;
        }
        .contact-details {
            flex: 1;
        }
        .contact-label {
            font-size: 0.9rem;
            color: #4a5568;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .contact-value {
            font-size: 1rem;
            color: #1a202c;
            margin: 0;
            text-decoration: none;
        }
        .contact-value[href] {
            color: var(--primary);
        }
        .contact-value[href]:hover {
            color: var(--primary);
            text-decoration: underline;
        }
        .contact-form-card .form-label {
            font-weight: 500;
            color: #1a202c;
            margin-bottom: 8px;
        }
        .contact-form-card .form-control {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 1rem;
        }
        .contact-form-card .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .contact-form-card .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border: none;
            border-radius: 8px;
            padding: 14px;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .contact-form-card .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 102, 255, 0.3);
        }
        .contact-form-card .btn-primary:active {
            transform: translateY(0);
        }
        
        /* Contact Form Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }
        
        .animate-fade-in-left {
            animation: fadeInLeft 0.6s ease-out forwards;
        }
        
        .animate-fade-in-right {
            animation: fadeInRight 0.6s ease-out forwards;
        }
        
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }
        
        /* Scroll Animations - Subtle and smooth */
        .scroll-animate {
            opacity: 0;
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }
        
        .scroll-animate.fade-in {
            transform: translateY(20px);
        }
        
        .scroll-animate.slide-up {
            transform: translateY(30px);
        }
        
        .scroll-animate.slide-left {
            transform: translateX(-30px);
        }
        
        .scroll-animate.slide-right {
            transform: translateX(30px);
        }
        
        .scroll-animate.scale-in {
            transform: scale(0.95);
        }
        
        .scroll-animate.animated {
            opacity: 1;
            transform: translateY(0) translateX(0) scale(1);
        }
        
        /* Staggered animation delays for lists */
        .scroll-animate.stagger-1 { transition-delay: 0.1s; }
        .scroll-animate.stagger-2 { transition-delay: 0.2s; }
        .scroll-animate.stagger-3 { transition-delay: 0.3s; }
        .scroll-animate.stagger-4 { transition-delay: 0.4s; }
        .scroll-animate.stagger-5 { transition-delay: 0.5s; }
        .scroll-animate.stagger-6 { transition-delay: 0.6s; }
        
        .animate-slide-down {
            animation: slideDown 0.4s ease-out;
        }
        
        .animate-shake {
            animation: shake 0.5s ease-in-out;
        }
        
        /* Input field styles with focus effects */
        .input-wrapper {
            position: relative;
        }
        
        .input-field {
            transition: all 0.3s ease;
            border: 2px solid #e5e7eb;
        }
        
        .input-field:hover {
            border-color: #9ca3af;
        }
        
        .input-wrapper.input-focused .input-field {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .input-focus-ring {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
            transform: scaleX(0);
            transform-origin: left;
            opacity: 0;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }
        
        .input-wrapper.input-focused .input-focus-ring {
            transform: scaleX(1);
            opacity: 1;
        }
        
        /* Contact info card hover effects */
        .contact-item {
            transition: all 0.3s ease;
        }
        
        .contact-item:hover {
            transform: translateX(5px);
        }
        
        .contact-icon-wrapper {
            transition: all 0.3s ease;
        }
        
        /* Submit button enhancements */
        .submit-button {
            position: relative;
            z-index: 1;
        }
        
        .submit-button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        .about-hero-section {
            position: relative;
            background: radial-gradient(circle at top left, rgba(59, 130, 246, 0.25), transparent 55%),
                        radial-gradient(circle at bottom right, rgba(56, 189, 248, 0.2), transparent 50%),
                        linear-gradient(135deg, #0f172a 0%, #1e3a8a 45%, #1f4ed8 100%);
            color: #e2e8f0;
            padding: 110px 0 90px;
            overflow: hidden;
        }
        .about-hero-section::before,
        .about-hero-section::after {
            content: "";
            position: absolute;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            mix-blend-mode: screen;
            opacity: 0.18;
            animation: heroOrbs 20s ease-in-out infinite;
        }
        .about-hero-section::before {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.55), rgba(14, 165, 233, 0.2));
            top: -100px;
            left: -80px;
        }
        .about-hero-section::after {
            background: linear-gradient(135deg, rgba(56, 189, 248, 0.65), rgba(59, 130, 246, 0.25));
            bottom: -120px;
            right: -90px;
            animation-delay: -6s;
        }
        .about-hero-section .container {
            position: relative;
            z-index: 1;
        }
        .about-hero-title {
            font-size: clamp(1.75rem, 5vw, 3.5rem);
            font-weight: bold;
            margin-bottom: 20px;
            color: #f8fafc;
            text-shadow: 0 10px 25px rgba(15, 23, 42, 0.35);
        }
        .about-hero-description {
            font-size: clamp(1rem, 2.5vw, 1.2rem);
            opacity: 0.9;
            margin-bottom: 15px;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.7;
            color: rgba(226, 232, 240, 0.9);
        }
        .about-hero-subtitle {
            font-size: 1.3rem;
            opacity: 0.9;
            color: rgba(226, 232, 240, 0.9);
        }
        .about-content-section {
            background-color: #f8f9fa;
            padding: 80px 0 40px 0;
        }
        .about-story-card {
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 40px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .about-story-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }
        .about-section-title {
            font-size: clamp(1.5rem, 4vw, 2.5rem);
            font-weight: bold;
            color: #1a202c;
            margin-bottom: 25px;
        }
        .about-section-text {
            font-size: 1.1rem;
            color: #4a5568;
            line-height: 1.8;
        }
        .about-image-placeholder {
            background: #f7fafc;
            border-radius: 12px;
            padding: 60px;
            text-align: center;
            min-height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .about-image-container {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: stretch;
            justify-content: center;
            padding: 0;
        }
        .about-history-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
        }
        /* Make the image column match the height of the text column */
        .about-story-card .row .col-lg-6:last-child {
            display: flex;
            align-items: stretch;
        }
        .about-story-card .row .col-lg-6:last-child .about-image-container {
            width: 100%;
        }
        .about-mission-section {
            background-color: #f8f9fa;
            padding: 20px 0 80px 0;
        }
        .mission-card {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            height: 100%;
            text-align: center;
        }
        .mission-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2.5rem;
            margin-bottom: 25px;
        }
        .mission-title {
            font-size: 1.8rem;
            font-weight: bold;
            color: #1a202c;
            margin-bottom: 20px;
        }
        .mission-text {
            font-size: 1rem;
            color: #4a5568;
            line-height: 1.7;
            text-align: justify;
        }
        .about-values-section {
            background: linear-gradient(165deg, #001428 0%, #002b5c 45%, #001f3d 100%);
            padding: 80px 0;
        }
        .about-values-section .about-section-title {
            color: white;
        }
        .value-card {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            transition: all 0.3s;
            height: 100%;
        }
        .value-card:hover {
            border-color: #667eea;
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
        }
        .value-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 2rem;
            margin-bottom: 20px;
        }
        .value-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #1a202c;
            margin-bottom: 15px;
        }
        .value-text {
            font-size: 1rem;
            color: #4a5568;
            line-height: 1.7;
        }
        .home-view-all {
             color: #60a5fa;
             display: inline-flex;
             align-items: center;
             gap: 8px;
             transition: color 0.3s;
         }
 
        /* Documentation Page */
        .documentation-hero {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            color: white;
            padding: 120px 0 90px;
        }
        .documentation-title {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .documentation-subtitle {
            font-size: 1.25rem;
            max-width: 700px;
            margin: 0 auto 20px;
            line-height: 1.6;
        }
        .documentation-updated {
            font-size: 0.95rem;
            opacity: 0.8;
        }
        .documentation-content {
            background-color: #f8fafc;
            padding: 80px 0;
        }
        .documentation-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
            padding: 40px;
            margin-bottom: 30px;
            border-left: 5px solid #1e3a8a;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .documentation-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 30px rgba(15, 23, 42, 0.12);
        }
        .documentation-card .section-heading {
            font-size: 1.5rem;
            color: #1e3a8a;
            margin-bottom: 15px;
            font-weight: 700;
        }
        .documentation-card .section-text {
            font-size: 1.05rem;
            color: #334155;
            line-height: 1.8;
            margin-bottom: 0;
        }
        @media (max-width: 768px) {
            .documentation-hero {
                padding: 90px 0 60px;
            }
            .documentation-title {
                font-size: 2.3rem;
            }
            .documentation-card {
                padding: 30px;
            }
        }
 
        @keyframes headerGradient {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        @keyframes shimmer {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        @keyframes heroOrbs {
            0%, 100% {
                transform: translate3d(0, 0, 0) scale(1);
                opacity: 0.2;
            }
            50% {
                transform: translate3d(40px, -20px, 0) scale(1.15);
                opacity: 0.3;
            }
        }
 
        /* Footer */
        .site-footer {
            position: relative;
            background: linear-gradient(160deg, #01010c 0%, #04132b 55%, #031c44 100%);
            color: #e2e8f0;
            overflow: hidden;
            padding: 80px 0 40px;
            margin-top: 120px;
            border-top: 1px solid rgba(148, 163, 184, 0.2);
            box-shadow: 0 -40px 80px rgba(5, 7, 12, 0.6);
        }

        .site-footer::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 20% 30%, rgba(56, 189, 248, 0.15), transparent 35%),
                radial-gradient(circle at 70% 20%, rgba(14, 165, 233, 0.18), transparent 40%),
                radial-gradient(circle at 60% 70%, rgba(3, 169, 244, 0.12), transparent 45%);
            z-index: 0;
            opacity: 0.95;
        }

        .site-footer::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='1400' height='600' viewBox='0 0 1400 600' xmlns='http://www.w3.org/2000/svg'%3E%3Cdefs%3E%3ClinearGradient id='waveGrad' x1='0%25' y1='0%25' x2='100%25' y2='0%25'%3E%3Cstop offset='0%25' stop-color='rgba(89,213,255,0.35)'/%3E%3Cstop offset='100%25' stop-color='rgba(0,247,255,0.55)'/%3E%3C/linearGradient%3E%3Cfilter id='waveGlow' x='-50%25' y='-50%25' width='200%25' height='200%25'%3E%3CfeGaussianBlur stdDeviation='6' result='coloredBlur'/%3E%3CfeMerge%3E%3CfeMergeNode in='coloredBlur'/%3E%3CfeMergeNode in='SourceGraphic'/%3E%3C/feMerge%3E%3C/filter%3E%3C/defs%3E%3Cpath d='M0 420 C150 360 250 300 350 320 C450 340 550 410 650 360 C750 310 850 220 950 250 C1050 280 1150 370 1250 330 C1320 300 1400 220 1400 220 L1400 600 L0 600 Z' fill='url(%23waveGrad)' opacity='0.12' filter='url(%23waveGlow)'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            opacity: 0.7;
            z-index: 0;
        }

        .site-footer .container,
        .site-footer .footer-column,
        .site-footer .footer-brand,
        .site-footer .footer-contact,
        .site-footer .footer-copyright {
            position: relative;
            z-index: 1;
        }

        .site-footer .footer-column h5,
        .site-footer .footer-brand h5 {
            color: #f8fafc;
            letter-spacing: 0.02em;
        }

        .site-footer a {
            color: #9ecbff;
            transition: color 0.3s ease, padding-left 0.3s ease;
        }

        .site-footer a:hover {
            color: #38bdf8;
            padding-left: 4px;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/cloudlab.css') }}?v={{ file_exists(public_path('css/cloudlab.css')) ? filemtime(public_path('css/cloudlab.css')) : '1' }}">
    @stack('styles')
</head>
<body class="{{ request()->routeIs('home') ? 'cl-home-body' : '' }}">
    <!-- Top Teal Strip -->
    <div class="top-teal-strip"></div>
    
    <!-- Main Navigation Component -->
    @include('components.navigation')

    <main>
        @yield('content')
    </main>

    <footer class="site-footer mt-5">
        <div class="container">
            <div class="row">
                <!-- Column 1: CloudLab Latam -->
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="footer-brand">
                        @php
                            $footerLogo = @file_exists(public_path('images/logo-cl.png'))
                                ? asset('images/logo-cl.png')
                                : null;
                        @endphp
                        @if($footerLogo)
                            <img src="{{ $footerLogo }}" alt="CloudLab Latam" class="footer-logo-img" width="48" height="48">
                        @else
                            <div class="footer-logo">CL</div>
                        @endif
                        <h5>CloudLab Latam</h5>
                    </div>
                    <p style="color: #cbd5e0; line-height: 1.6;">
                        {{ __('messages.footer.description') }}
                    </p>
                </div>

                <!-- Column 2: Enlaces Rápidos -->
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="footer-column">
                        <h5>{{ __('messages.footer.quick_links') }}</h5>
                        <ul>
                            <li><a href="{{ route('home') }}">{{ __('messages.nav.home') }}</a></li>
                            <li><a href="{{ route('blogs.index') }}">{{ __('messages.nav.blog') }}</a></li>
                            <li><a href="{{ route('projects.index') }}">{{ __('messages.nav.projects') }}</a></li>
                            <li><a href="#about">{{ __('messages.nav.about') }}</a></li>
                            <li><a href="#technologies">{{ __('messages.nav.technologies') }}</a></li>
                            <li><a href="#contact">{{ __('messages.nav.contact') }}</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Column 3: Recursos -->
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="footer-column">
                        <h5>{{ __('messages.footer.resources') }}</h5>
                        <ul>
                            <li><a href="{{ route('documentation.index') }}">{{ __('messages.footer.documentation') }}</a></li>
                            <li><a href="#support">{{ __('messages.footer.support') }}</a></li>
                            <li><a href="#contact-us">{{ __('messages.footer.contact_us') }}</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Column 4: Contacto -->
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="footer-column">
                        <h5>{{ __('messages.footer.contact') }}</h5>
                        <div class="footer-contact">
                            <p>
                                <strong>{{ __('messages.footer.email') }}:</strong><br>
                                <a href="mailto:{{ __('messages.footer.email_address') }}">{{ __('messages.footer.email_address') }}</a>
                            </p>
                            <p>
                                <strong>{{ __('messages.footer.phone') }}:</strong><br>
                                +57 3208156396
                            </p>
                            <p>
                                <strong>{{ __('messages.footer.address') }}:</strong><br>
                                Latin America
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Copyright Section -->
            <div class="footer-copyright">
                <p class="mb-0">&copy; {{ date('Y') }} CloudLab Latam. {{ __('messages.footer.copyright') }}</p>
            </div>
        </div>
    </footer>

    <!-- Floating CTA Button -->
    <div class="floating-cta-button">
        <a href="{{ route('contact.index') }}" 
           class="floating-cta-link"
           aria-label="{{ __('messages.cta.free_consultation') }}">
            <i class="bi bi-calendar-check me-2"></i>
            <span class="cta-text">{{ __('messages.cta.free_consultation_short') }}</span>
        </a>
    </div>

    <!-- Floating WhatsApp Button -->
    <div class="whatsapp-float">
        <a href="https://wa.me/573208156396" target="_blank" rel="noopener noreferrer" aria-label="{{ __('messages.whatsapp.contact_us') }}">
            <i class="bi bi-whatsapp"></i>
            <span class="whatsapp-text">{{ __('messages.whatsapp.contact_us') }}</span>
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    
    <script nonce="{{ $cspNonce ?? '' }}">
        // Ensure mobile menu is closed and page is clickable on load
        (function() {
            function ensurePageIsClickable() {
                // Close mobile menu if open
                const mobileMenu = document.getElementById('mobile-menu');
                if (mobileMenu) {
                    // Force close menu
                    mobileMenu.classList.add('hidden');
                    mobileMenu.classList.remove('mobile-menu-open');
                    mobileMenu.style.pointerEvents = 'none';
                    mobileMenu.style.display = 'none';
                }
                
                // Ensure body scroll is enabled
                document.body.style.overflow = '';
                
                // Ensure all links and buttons are clickable
                const allLinks = document.querySelectorAll('a, button, .btn, [role="button"]');
                allLinks.forEach(element => {
                    if (!element.hasAttribute('disabled') && element.tagName !== 'SCRIPT') {
                        element.style.pointerEvents = 'auto';
                        element.style.cursor = 'pointer';
                        // Remove any event listeners that might be blocking
                        if (element.onclick === null) {
                            element.onclick = null;
                        }
                    }
                });
                
                // Ensure navigation links work
                const navLinks = document.querySelectorAll('.nav-link-item, .mobile-menu-link');
                navLinks.forEach(link => {
                    link.style.pointerEvents = 'auto';
                    link.style.cursor = 'pointer';
                    link.style.zIndex = '100';
                });
            }
            
            // Run immediately
            ensurePageIsClickable();
            
            // Run on DOMContentLoaded
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', ensurePageIsClickable);
            } else {
                ensurePageIsClickable();
            }
            
            // Run after a short delay to catch any late-loading elements
            setTimeout(ensurePageIsClickable, 100);
        })();
        
        // Card animation on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Remove no-js class if present
            document.documentElement.classList.remove('no-js');
            
            // Select all cards that should be animated
            const cards = document.querySelectorAll(
                '.feature-card, .tech-card, .card, .mission-card, .value-card, .contact-info-card, .contact-form-card'
            );
            
            // Add initial animation class to all cards
            cards.forEach(card => {
                if (!card.classList.contains('animate-card')) {
                    card.classList.add('animate-card');
                }
            });
            
            // Animate cards with staggered delay on page load
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('animated');
                }, index * 100); // 100ms delay between each card
            });
            
            // Lazy loading image handler
            const lazyImages = document.querySelectorAll('img[loading="lazy"]');
            
            // Also add lazy loading to images in blog content (from Quill editor)
            const blogContentImages = document.querySelectorAll('.blog-content img:not([loading])');
            blogContentImages.forEach(img => {
                img.setAttribute('loading', 'lazy');
            });
            
            // Combine all lazy images
            const allLazyImages = document.querySelectorAll('img[loading="lazy"]');
            
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.addEventListener('load', function() {
                                img.classList.add('loaded');
                            });
                            // If image is already loaded (cached), add loaded class immediately
                            if (img.complete) {
                                img.classList.add('loaded');
                            }
                            observer.unobserve(img);
                        }
                    });
                }, {
                    rootMargin: '50px' // Start loading 50px before image enters viewport
                });
                
                allLazyImages.forEach(img => imageObserver.observe(img));
            } else {
                // Fallback for browsers without IntersectionObserver
                allLazyImages.forEach(img => {
                    img.addEventListener('load', function() {
                        img.classList.add('loaded');
                    });
                    if (img.complete) {
                        img.classList.add('loaded');
                    }
                });
            }
            
            // Scroll animations with IntersectionObserver
            const scrollAnimateElements = document.querySelectorAll('.scroll-animate');
            
            if ('IntersectionObserver' in window && scrollAnimateElements.length > 0) {
                const scrollObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('animated');
                            scrollObserver.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.1, // Trigger when 10% of element is visible
                    rootMargin: '0px 0px -50px 0px' // Start animation slightly before element enters viewport
                });
                
                scrollAnimateElements.forEach(el => {
                    scrollObserver.observe(el);
                });
            } else {
                // Fallback: show all elements immediately
                scrollAnimateElements.forEach(el => {
                    el.classList.add('animated');
                });
            }
            
            // Particle Animation for Hero Section
            const canvas = document.getElementById('particles-canvas');
            if (canvas) {
                const ctx = canvas.getContext('2d');
                let particles = [];
                let animationId;
                
                function resizeCanvas() {
                    canvas.width = canvas.offsetWidth;
                    canvas.height = canvas.offsetHeight;
                }
                
                function createParticle() {
                    return {
                        x: Math.random() * canvas.width,
                        y: Math.random() * canvas.height,
                        radius: Math.random() * 2 + 1,
                        speedX: (Math.random() - 0.5) * 0.5,
                        speedY: (Math.random() - 0.5) * 0.5,
                        opacity: Math.random() * 0.5 + 0.2
                    };
                }
                
                function initParticles() {
                    particles = [];
                    const particleCount = Math.floor((canvas.width * canvas.height) / 15000);
                    for (let i = 0; i < particleCount; i++) {
                        particles.push(createParticle());
                    }
                }
                
                function animateParticles() {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    
                    particles.forEach((particle, i) => {
                        particle.x += particle.speedX;
                        particle.y += particle.speedY;
                        
                        if (particle.x < 0 || particle.x > canvas.width) particle.speedX *= -1;
                        if (particle.y < 0 || particle.y > canvas.height) particle.speedY *= -1;
                        
                        ctx.beginPath();
                        ctx.arc(particle.x, particle.y, particle.radius, 0, Math.PI * 2);
                        ctx.fillStyle = `rgba(255, 255, 255, ${particle.opacity})`;
                        ctx.fill();
                        
                        // Draw connections
                        particles.slice(i + 1).forEach(p2 => {
                            const dx = particle.x - p2.x;
                            const dy = particle.y - p2.y;
                            const distance = Math.sqrt(dx * dx + dy * dy);
                            
                            if (distance < 100) {
                                ctx.beginPath();
                                ctx.moveTo(particle.x, particle.y);
                                ctx.lineTo(p2.x, p2.y);
                                ctx.strokeStyle = `rgba(255, 255, 255, ${0.1 * (1 - distance / 100)})`;
                                ctx.lineWidth = 0.5;
                                ctx.stroke();
                            }
                        });
                    });
                    
                    animationId = requestAnimationFrame(animateParticles);
                }
                
                resizeCanvas();
                initParticles();
                animateParticles();
                
                window.addEventListener('resize', () => {
                    resizeCanvas();
                    initParticles();
                });
            }
        });
    </script>
    
    <!-- Suprimir errores de extensiones del navegador -->
    <script nonce="{{ $cspNonce ?? '' }}">
        // Suprimir errores de extensiones del navegador que no afectan el sitio
        const originalError = console.error;
        console.error = function(...args) {
            const errorMessage = args.join(' ');
            // Ignorar errores de extensiones del navegador
            if (errorMessage.includes('runtime.lastError') || 
                errorMessage.includes('message port closed')) {
                return; // No mostrar estos errores
            }
            // Mostrar otros errores normalmente
            originalError.apply(console, args);
        };
    </script>
    
    <script src="{{ asset('js/card-glow.js') }}" defer></script>
    @stack('scripts')
</body>
</html>

