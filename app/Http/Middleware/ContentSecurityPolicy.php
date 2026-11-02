<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('csp.enabled', true) || ! is_array(config('csp.directives'))) {
            return $next($request);
        }

        $nonce = base64_encode(Str::random(32));
        $request->attributes->set('csp_nonce', $nonce);
        View::share('cspNonce', $nonce);

        /** @var Response $response */
        $response = $next($request);

        $directives = $this->buildDirectives($nonce);

        $header = config('csp.report_only', false)
            ? 'Content-Security-Policy-Report-Only'
            : 'Content-Security-Policy';

        $response->headers->set($header, $directives);

        return $response;
    }

    protected function buildDirectives(string $nonce): string
    {
        $config = config('csp.directives', []);
        $parts = [];

        foreach ($config as $name => $sources) {
            if (! is_array($sources)) {
                continue;
            }
            $values = $sources;

            if (in_array($name, ['script-src', 'style-src'], true)) {
                $values[] = "'nonce-{$nonce}'";
            }

            if ($name === 'script-src' && $this->viteDevAllowed()) {
                $values = array_merge($values, [
                    'http://127.0.0.1:5173',
                    'http://localhost:5173',
                    'ws://127.0.0.1:5173',
                    'ws://localhost:5173',
                ]);
            }

            if ($name === 'connect-src' && $this->viteDevAllowed()) {
                $values = array_merge($values, [
                    'http://127.0.0.1:5173',
                    'http://localhost:5173',
                    'ws://127.0.0.1:5173',
                    'ws://localhost:5173',
                ]);
            }

            if ($name === 'style-src' && $this->viteDevAllowed()) {
                $values = array_merge($values, [
                    'http://127.0.0.1:5173',
                    'http://localhost:5173',
                ]);
            }

            $parts[] = $name . ' ' . implode(' ', array_unique($values));
        }

        if (app()->environment('production')) {
            $parts[] = 'upgrade-insecure-requests';
        }

        $reportUri = config('csp.report_uri');
        if ($reportUri) {
            $parts[] = 'report-uri ' . $reportUri;
        }

        return implode('; ', $parts);
    }

    protected function viteDevAllowed(): bool
    {
        return app()->environment('local') && ! file_exists(public_path('build/manifest.json'));
    }
}
