<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    */

    'default' => env('FILESYSTEM_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    */

    'disks' => [

        /*
        |--------------------------------------------------------------------------
        | Local (privado - não público)
        |--------------------------------------------------------------------------
        */
        'local' => [
            'driver' => 'local',
            'root'   => storage_path('app/private'),
            'throw'  => false,
        ],

        /*
        |--------------------------------------------------------------------------
        | PUBLIC (HOSTGATOR - SEM SYMLINK)
        |--------------------------------------------------------------------------
        |
        | Estrutura do servidor:
        | public_html/
        |   ├── laravel_app/
        |   └── storage/   ← queremos gravar aqui
        |
        | base_path() aponta para:
        | public_html/laravel_app
        |
        | Então public_html/storage = base_path('../storage')
        |--------------------------------------------------------------------------
        */
        'public' => [
            'driver'     => 'local',

            // ✅ grava direto em public_html/storage
            'root'       => base_path('../storage'),

            'url'        => rtrim(env('APP_URL', 'http://localhost'), '/') . '/storage',

            'visibility' => 'public',
            'throw'      => false,
        ],

        /*
        |--------------------------------------------------------------------------
        | S3 (opcional)
        |--------------------------------------------------------------------------
        */
        's3' => [
            'driver' => 's3',
            'key'    => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url'    => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

    ],

];