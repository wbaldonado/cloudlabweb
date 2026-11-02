{{-- Reusable SEO head: title, description, canonical, Open Graph and hreflang. --}}
@php
    use App\Services\SEOService;
    use App\Support\LocalizedUrl;

    $baseUrl = rtrim((string) config('app.url'), '/');
    $pageType = $pageType ?? 'home';
    $pageData = $pageData ?? null;
    $meta = SEOService::getPageMeta($pageType, $pageData);
    $meta['canonical'] = LocalizedUrl::currentCanonical();
    $alternates = LocalizedUrl::languageAlternates();
    $meta['alternate_lang_es'] = $alternates['es'];
    $meta['alternate_lang_en'] = $alternates['en'];
    $isSpanish = app()->getLocale() === 'es';
    $ogLocale = $isSpanish ? 'es_CO' : 'en_US';
    $ogLocaleAlt = $isSpanish ? 'en_US' : 'es_CO';
@endphp

<title>{{ $meta['title'] }}</title>
<meta name="description" content="{{ $meta['description'] }}">
<meta name="author" content="{{ $meta['author'] }}">
<meta http-equiv="content-language" content="{{ $isSpanish ? 'es-CO' : 'en' }}">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<meta name="googlebot" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<meta name="bingbot" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

@if(!empty($meta['copyright']))
<meta name="copyright" content="{{ $meta['copyright'] }}">
@endif
@if(!empty($meta['reply_to']))
<meta name="reply-to" content="{{ $meta['reply_to'] }}">
@endif

<link rel="canonical" href="{{ $meta['canonical'] }}">

<meta property="og:type" content="{{ $meta['og_type'] }}">
<meta property="og:url" content="{{ $meta['canonical'] }}">
<meta property="og:title" content="{{ $meta['title'] }}">
<meta property="og:description" content="{{ $meta['description'] }}">
<meta property="og:site_name" content="{{ $meta['og_site_name'] }}">
<meta property="og:locale" content="{{ $ogLocale }}">
<meta property="og:locale:alternate" content="{{ $ogLocaleAlt }}">
<meta property="og:image" content="{{ $meta['og_image'] ?? $baseUrl . '/images/logo.png' }}">
<meta property="og:image:secure_url" content="{{ str_replace('http://', 'https://', $meta['og_image'] ?? $baseUrl . '/images/logo.png') }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="{{ $meta['title'] }}">

@if(($meta['og_type'] ?? '') === 'article' && isset($meta['article_published_time']))
<meta property="article:published_time" content="{{ $meta['article_published_time'] }}">
<meta property="article:modified_time" content="{{ $meta['article_modified_time'] ?? $meta['article_published_time'] }}">
<meta property="article:author" content="{{ $meta['article_author'] ?? 'CloudLab Latam' }}">
<meta property="article:section" content="{{ $meta['article_section'] ?? 'Technology' }}">
@endif

<meta name="twitter:card" content="{{ $meta['twitter_card'] }}">
<meta name="twitter:site" content="{{ $meta['twitter_site'] }}">
@if(!empty($meta['twitter_creator']))
<meta name="twitter:creator" content="{{ $meta['twitter_creator'] }}">
@endif
<meta name="twitter:title" content="{{ $meta['title'] }}">
<meta name="twitter:description" content="{{ $meta['description'] }}">
<meta name="twitter:image" content="{{ $meta['og_image'] ?? $baseUrl . '/images/logo.png' }}">
<meta name="twitter:image:alt" content="{{ $meta['title'] }}">

<link rel="alternate" hreflang="es" href="{{ $meta['alternate_lang_es'] }}">
<link rel="alternate" hreflang="en" href="{{ $meta['alternate_lang_en'] }}">
<link rel="alternate" hreflang="x-default" href="{{ $alternates['x-default'] }}">

<script type="application/ld+json" nonce="{{ $cspNonce ?? '' }}">
{!! json_encode(SEOService::generateOrganizationSchema(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
<script type="application/ld+json" nonce="{{ $cspNonce ?? '' }}">
{!! json_encode(SEOService::generateProfessionalServiceSchema(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
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
@if($pageType === 'service' && $pageData)
<script type="application/ld+json" nonce="{{ $cspNonce ?? '' }}">
{!! json_encode(SEOService::generateServiceSchema($pageData), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@if(!empty($pageData['faqs']))
<script type="application/ld+json" nonce="{{ $cspNonce ?? '' }}">
{!! json_encode(SEOService::generateFaqSchema($pageData['faqs']), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endif
@endif
@if(!empty($breadcrumbs))
<script type="application/ld+json" nonce="{{ $cspNonce ?? '' }}">
{!! json_encode(SEOService::generateBreadcrumbSchema($breadcrumbs), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endif
