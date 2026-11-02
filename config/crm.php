<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CRM email (free: Hostinger SMTP + IMAP polling)
    |--------------------------------------------------------------------------
    */

    'message_id_domain' => env('CRM_MESSAGE_ID_DOMAIN', parse_url(env('APP_URL', 'https://cloudlablatam.tech'), PHP_URL_HOST) ?: 'cloudlablatam.tech'),

    'notify_email' => env('CRM_NOTIFY_EMAIL', env('MAIL_FROM_ADDRESS')),

    'company_name' => env('CRM_COMPANY_NAME', env('APP_NAME', 'CloudLab Latam')),

    'company_address' => env('CRM_COMPANY_ADDRESS', 'Colombia'),

    'imap' => [
        'enabled' => env('CRM_IMAP_ENABLED', false),
        'host' => env('CRM_IMAP_HOST', 'imap.hostinger.com'),
        'port' => (int) env('CRM_IMAP_PORT', 993),
        'encryption' => env('CRM_IMAP_ENCRYPTION', 'ssl'),
        'username' => env('CRM_IMAP_USERNAME'),
        'password' => env('CRM_IMAP_PASSWORD'),
        'folder' => env('CRM_IMAP_FOLDER', 'INBOX'),
    ],

];
