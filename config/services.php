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

    'google' => [
    'client_id'     => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect'      => env('GOOGLE_REDIRECT_URI'),
    ],

    'google_analytics' => [
        'id' => env('GA_ID'),
    ],

    'bsre' => [
        'url'      => env('BSRE_API_URL', 'https://esign-api.bssn.go.id/api'),
        'username' => env('BSRE_USERNAME'),
        'password' => env('BSRE_PASSWORD'),
    ],

    'simpegnas' => [
        'url'         => 'https://api-absensi.simpegnas.go.id/absensi/api/get/rekap-bulanan-by-kantor',
        'token'       => env('API_ABSENSI_TOKEN'),
        'kantor_id'   => env('API_ID_KANTOR'),
        'nama_kantor' => env('API_NAMA_KANTOR'),
    ],

    'kompass' => [
        'sso_url' => env('KOMPASS_SSO_URL', 'http://localhost:8585'),
        'client_id' => env('KOMPASS_CLIENT_ID'),
        'client_secret' => env('KOMPASS_CLIENT_SECRET'),
        'redirect_uri' => env('KOMPASS_REDIRECT_URI'),
    ],

];
