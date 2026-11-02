@php
    use App\Support\Branding;

    $class = $class ?? 'cl-brand-logo';
    $width = $width ?? 64;
    $height = $height ?? 56;
    $logoUrl = $logoUrl ?? Branding::logoUrl();
    $logoFallback = $logoFallback ?? Branding::logoFallbackUrl();
    $altPrimary = Branding::alternateAssetUrl('images/logo-brand.png');
    $altFallback = Branding::alternateAssetUrl('images/logo.png');
@endphp
<img src="{{ $logoUrl }}"
     alt="CloudLab Latam"
     class="{{ $class }}"
     width="{{ $width }}"
     height="{{ $height }}"
     @if(!empty($loading)) loading="{{ $loading }}" @endif
     decoding="async"
     data-logo-fallback="{{ $logoFallback }}"
     data-logo-alt="{{ $altPrimary }}"
     data-logo-alt-fallback="{{ $altFallback }}"
     onerror="typeof clLoadLogo === 'function' && clLoadLogo(this)">
