<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Official public contact
    |--------------------------------------------------------------------------
    |
    | The website domain is cloudlablatam.tech. The mailbox configured for
    | the contact form, CRM and campaigns is info@cloudlablatam.com.
    | That mailbox is the official contact address shown on the site.
    |
    */

    'contact_email' => env('CONTACT_EMAIL', env('CONTACT_TO_EMAIL', 'info@cloudlablatam.com')),

    'phone_display' => '+57 320 815 6396',

    'phone_e164' => '+573208156396',

    'address_locality' => env('SEO_ADDRESS_LOCALITY', 'Bogotá'),

    'address_country' => env('SEO_ADDRESS_COUNTRY', 'Colombia'),

    'address_country_code' => env('SEO_ADDRESS_COUNTRY_CODE', 'CO'),

    'locales' => ['es', 'en'],

    'default_locale' => 'es',

];
