<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Content Security Policy
    |--------------------------------------------------------------------------
    |
    | Use CSP_REPORT_ONLY=true in .env to log violations without blocking.
    |
    */

    'enabled' => env('CSP_ENABLED', true),

    'report_only' => env('CSP_REPORT_ONLY', false),

    'report_uri' => env('CSP_REPORT_URI'),

    'directives' => [
        'default-src' => ["'self'"],
        'base-uri' => ["'self'"],
        'form-action' => ["'self'"],
        'frame-ancestors' => ["'self'"],
        'object-src' => ["'none'"],
        'script-src' => [
            "'self'",
            'https://cdn.jsdelivr.net',
        ],
        'style-src' => [
            "'self'",
            'https://cdn.jsdelivr.net',
            'https://fonts.googleapis.com',
        ],
        'font-src' => [
            "'self'",
            'https://fonts.gstatic.com',
            'https://cdn.jsdelivr.net',
            'data:',
        ],
        'img-src' => [
            "'self'",
            'data:',
            'https:',
            'blob:',
        ],
        'media-src' => [
            "'self'",
            'blob:',
            'https:',
        ],
        'connect-src' => [
            "'self'",
        ],
        'frame-src' => [
            "'self'",
            'blob:',
            'data:',
            'https://wa.me',
            'https://www.youtube.com',
            'https://www.youtube-nocookie.com',
            'https://player.vimeo.com',
        ],
        'worker-src' => ["'self'", 'blob:'],
        'manifest-src' => ["'self'"],
    ],

];
