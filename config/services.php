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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Checkout Pro de Mercado Pago (misma integración que BBS)
    'mercadopago' => [
        'public_key' => env('MP_PUBLIC_KEY'),
        'access_token' => env('MP_ACCESS_TOKEN'),
        // clave secreta del webhook (panel de la aplicación > Webhooks); vacía no valida la firma
        'webhook_secret' => env('MP_WEBHOOK_SECRET'),
        // Pruebas con cuentas de prueba de Mercado Pago: no se manda el mail del cliente como pagador,
        // así el checkout deja ingresar con el comprador de prueba (o se usa MP_PAGADOR_PRUEBA si está cargado)
        'modo_prueba' => (bool) env('MP_MODO_PRUEBA', false),
        'pagador_prueba' => env('MP_PAGADOR_PRUEBA'),
    ],

];
