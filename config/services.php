<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'infobip' => [
        'base_url' => env('INFOBIP_BASE_URL', 'https://d9p9ll.api.infobip.com'),
        'api_key' => env('INFOBIP_API_KEY', 'ca9eaf59e10cb64149d173ce207e2556-9a4332a2-868f-4da8-a287-b6a7b44db93c'),
        'whatsapp_number' => env('INFOBIP_WHATSAPP_NUMBER', '+447860088970'),
        'default_country_code' => env('INFOBIP_DEFAULT_COUNTRY_CODE', '+263'),
        'status_template' => env('INFOBIP_STATUS_TEMPLATE', 'fault_status_update'),
    ],

];
