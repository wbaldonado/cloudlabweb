{{-- Technology icon for public pages (never throws) --}}
@php
    $size = $size ?? 48;
    $webPath = null;
    $version = time();
    $initials = '·';

    try {
        if (isset($technology) && is_object($technology)) {
            if (method_exists($technology, 'initials')) {
                $initials = $technology->initials();
            } elseif (! empty($technology->name)) {
                $initials = strtoupper(\Illuminate\Support\Str::substr((string) $technology->name, 0, 2));
            }

            if (method_exists($technology, 'publicIconPath')) {
                $webPath = $technology->publicIconPath();
            } elseif (! empty($technology->icon_path)) {
                $candidate = ltrim((string) $technology->icon_path, '/');
                if (! str_starts_with($candidate, 'images/')) {
                    $candidate = 'images/technologies/' . basename($candidate);
                }
                if (is_file(public_path($candidate))) {
                    $webPath = $candidate;
                }
            }

            if ($technology->updated_at ?? null) {
                $version = $technology->updated_at->timestamp;
            }
        }
    } catch (\Throwable $e) {
        $webPath = null;
    }

    $src = null;
    $fallbackSrc = null;
    try {
        if ($webPath && class_exists(\App\Support\Branding::class)) {
            $src = \App\Support\Branding::assetUrl($webPath) . '?v=' . $version;
            $fallbackSrc = \App\Support\Branding::alternateAssetUrl($webPath) . '?v=' . $version;
        } elseif ($webPath) {
            $src = '/' . ltrim($webPath, '/') . '?v=' . $version;
            $fallbackSrc = $src;
        }
    } catch (\Throwable $e) {
        $src = null;
    }
@endphp
<span class="tech-icon-media" style="--tech-icon-size: {{ (int) $size }}px;">
    @if($src)
        <img
            src="{{ $src }}"
            @if($fallbackSrc) data-fallback="{{ $fallbackSrc }}" @endif
            alt=""
            class="tech-icon-image {{ $class ?? '' }}"
            width="{{ (int) $size }}"
            height="{{ (int) $size }}"
            loading="eager"
            decoding="async"
            onerror="typeof clTechIconFallback==='function'&&clTechIconFallback(this)"
        >
        <span class="tech-icon-initials is-hidden" aria-hidden="true">{{ $initials }}</span>
    @else
        <span class="tech-icon-initials" aria-hidden="true">{{ $initials }}</span>
    @endif
</span>
