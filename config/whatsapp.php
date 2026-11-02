<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Meta WhatsApp Cloud API
    |--------------------------------------------------------------------------
    */

    'enabled' => env('WHATSAPP_ENABLED', false),

    'token' => env('WHATSAPP_TOKEN'),

    'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),

    'verify_token' => env('WHATSAPP_VERIFY_TOKEN', 'cloudlab-whatsapp-verify'),

    /** E.164 digits only, e.g. 573208156396 — used by the floating button */
    'number' => env('WHATSAPP_NUMBER', '573208156396'),

    'api_version' => env('WHATSAPP_API_VERSION', 'v21.0'),

    'graph_url' => env('WHATSAPP_GRAPH_URL', 'https://graph.facebook.com'),

    'notify_email' => env('WHATSAPP_NOTIFY_EMAIL', env('CRM_NOTIFY_EMAIL', env('MAIL_FROM_ADDRESS'))),

    'prefill_text' => env('WHATSAPP_PREFILL_TEXT', 'Hola, quiero información'),

];
