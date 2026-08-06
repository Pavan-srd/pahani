<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        'r2' => [
            'driver' => 's3',
            'key' => env('R2_ACCESS_KEY_ID'),
            'secret' => env('R2_SECRET_ACCESS_KEY'),
            'region' => 'auto',
            'bucket' => env('R2_BUCKET', 'cloud-pdfs'),
            'endpoint' => env('R2_ENDPOINT'),
            'url' => env('R2_URL'),
            'use_path_style_endpoint' => false,
            
            // ─── Security Settings ───
            'visibility' => 'private',  // IMPORTANT: Keep private!
            'metadata' => [
                'CacheControl' => 'max-age=0,no-cache,no-store,must-revalidate,private',
            ],
            
            // ─── Domain Restrictions ───
            'allowed_domains' => array_filter(
                array_map('trim', explode(',', env('R2_ALLOWED_DOMAINS', 'dlrsrd.in')))
            ),
            'allowed_domain' => env('R2_ALLOWED_DOMAIN', 'dlrsrd.in'),
            
            // ─── IP Restrictions (optional) ───
            'verify_ip' => env('R2_VERIFY_IP', false),
            'whitelisted_ips' => array_filter(
                array_map('trim', explode(',', env('R2_WHITELISTED_IPS', '')))
            ),
            
            // ─── Rate Limiting ───
            'rate_limit_per_hour' => env('R2_RATE_LIMIT_PER_HOUR', 100),
            
            // ─── URL Expiration ───
            'signed_url_expires' => env('R2_SIGNED_URL_EXPIRES', 60),
            
            'throw' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
