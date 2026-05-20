<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    'remote_post_api_key' => env('REMOTE_POST_API_KEY', ''),

    /**
     * ✅ OpenAI (Responses + Images)
     */
    'openai' => [
        'key' => env('OPENAI_API_KEY', ''),
        'base_uri' => env('OPENAI_BASE_URI', 'https://api.openai.com'),
    ],
    'indexnow' => [
    'key' => env('INDEXNOW_KEY'),
    'key_location' => env('INDEXNOW_KEY_LOCATION'),
    'host' => env('INDEXNOW_HOST', 'www.iatioben.com.br'),
    'endpoint' => env('INDEXNOW_ENDPOINT', 'https://api.indexnow.org/indexnow'),
],

];