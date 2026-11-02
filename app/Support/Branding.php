<?php

namespace App\Support;

class Branding
{
    /**
     * Browser URL for a file under public/ (logo, icons, etc.).
     * Uses a root-relative path (/images/...) so it works on Hostinger
     * even when APP_URL is still http://localhost.
     */
    public static function assetUrl(string $path): string
    {
        $path = '/' . ltrim($path, '/');

        if ($prefix = trim((string) env('ASSET_PREFIX', ''), '/')) {
            return '/' . $prefix . $path;
        }

        if (! app()->runningInConsole() && request()) {
            $base = request()->getBaseUrl();
            if ($base && $base !== '/') {
                return rtrim($base, '/') . $path;
            }
        }

        return $path;
    }

    /**
     * URL for a file on the "public" storage disk (storage/app/public).
     * Requires public/storage symlink — or copy storage/app/public → public/storage on Hostinger.
     */
    public static function storageUrl(string $path): string
    {
        return self::assetUrl('storage/' . ltrim($path, '/'));
    }

    /**
     * Alternate URL when document root is laravel_app instead of laravel_app/public.
     * Primary: /images/...  Fallback: /public/images/... (or the reverse if ASSET_PREFIX=public).
     */
    public static function alternateAssetUrl(string $path): string
    {
        $path = '/' . ltrim($path, '/');
        $prefix = trim((string) env('ASSET_PREFIX', ''), '/');

        if ($prefix === 'public') {
            return $path;
        }

        return '/public' . $path;
    }

    public static function logoUrl(): string
    {
        if ($override = env('LOGO_URL')) {
            return $override;
        }

        $path = env('LOGO_PATH', 'images/logo-brand.png');
        $url = self::assetUrl($path);

        return self::withVersion($url, $path);
    }

    public static function logoFallbackUrl(): string
    {
        if ($fallback = env('LOGO_FALLBACK_URL')) {
            return $fallback;
        }

        $path = env('LOGO_FALLBACK_PATH', 'images/logo.png');
        $url = self::assetUrl($path);

        return self::withVersion($url, $path);
    }

    protected static function withVersion(string $url, string $relativePath): string
    {
        $full = public_path(ltrim($relativePath, '/'));
        $version = is_file($full) ? filemtime($full) : time();

        return $url . (str_contains($url, '?') ? '&' : '?') . 'v=' . $version;
    }
}
