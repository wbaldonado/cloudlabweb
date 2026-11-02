<?php

namespace App\Support;

class LocalizedUrl
{
    public static function locales(): array
    {
        return config('seo.locales', ['es', 'en']);
    }

    public static function isLocale(?string $locale): bool
    {
        return in_array($locale, self::locales(), true);
    }

    /**
     * Same path in another locale. Keeps pagination when present.
     */
    public static function swap(string $locale, ?string $fromUrl = null): string
    {
        if (! self::isLocale($locale)) {
            $locale = config('seo.default_locale', 'es');
        }

        $source = $fromUrl ?: url()->current();
        $path = parse_url($source, PHP_URL_PATH) ?: '/';
        $basePath = request()->getBasePath();

        if ($basePath !== '' && str_starts_with($path, $basePath)) {
            $path = substr($path, strlen($basePath)) ?: '/';
        }

        $path = '/' . ltrim($path, '/');

        if (preg_match('#^/(admin|language|webhooks|t|robots\.txt|sitemap\.xml)(/|$)#', $path)) {
            return url('/' . $locale);
        }

        $stripped = preg_replace('#^/(es|en)(?=/|$)#', '', $path) ?? $path;
        if ($stripped === '' || $stripped === '/') {
            $localized = '/' . $locale;
        } else {
            $localized = '/' . $locale . $stripped;
        }

        $url = url($localized);
        $page = request()->query('page');
        if (is_numeric($page) && (int) $page > 1) {
            $url .= '?page=' . (int) $page;
        }

        return $url;
    }

    public static function origin(): string
    {
        $configured = rtrim((string) config('app.url'), '/');

        if (! app()->runningInConsole() && request()) {
            $host = parse_url($configured, PHP_URL_HOST);
            if (in_array($host, ['localhost', '127.0.0.1'], true)) {
                return rtrim(request()->getSchemeAndHttpHost() . request()->getBaseUrl(), '/');
            }
        }

        return $configured;
    }

    public static function currentCanonical(): string
    {
        $base = self::origin();
        $path = '/' . ltrim(request()->getPathInfo(), '/');

        if ($path === '/' || $path === '') {
            $path = '/' . config('seo.default_locale', 'es');
        }

        $url = $base . $path;
        $page = request()->query('page');
        if (is_numeric($page) && (int) $page > 1) {
            $url .= '?page=' . (int) $page;
        }

        return $url;
    }

    public static function languageAlternates(): array
    {
        $base = self::origin();
        $path = '/' . ltrim(request()->getPathInfo(), '/');
        $stripped = preg_replace('#^/(es|en)(?=/|$)#', '', $path) ?? '';

        if ($stripped === '/') {
            $stripped = '';
        }

        $query = '';
        $page = request()->query('page');
        if (is_numeric($page) && (int) $page > 1) {
            $query = '?page=' . (int) $page;
        }

        $es = $base . '/es' . $stripped . $query;
        $en = $base . '/en' . $stripped . $query;

        return [
            'es' => $es,
            'en' => $en,
            'x-default' => $es,
        ];
    }
}
