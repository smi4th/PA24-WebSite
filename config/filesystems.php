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
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        'receipts' => [
            'driver' => 'local',
            'root' => storage_path('app/public/receipts'),
            'url' => env('APP_URL').'/storage/receipts',
            'visibility' => 'public',
            'throw' => false,
        ],

        'locations' => [
            'driver' => 'local',
            'root' => storage_path('app/public/locations'),
            'url' => env('APP_URL').'/storage/locations',
            'visibility' => 'public',
            'throw' => false,
        ],

        'bedrooms' => [
            'driver' => 'local',
            'root' => storage_path('app/public/bedrooms'),
            'url' => env('APP_URL').'/storage/bedrooms',
            'visibility' => 'public',
            'throw' => false,
        ],

        'equipments' => [
            'driver' => 'local',
            'root' => storage_path('app/public/equipments'),
            'url' => env('APP_URL').'/storage/equipments',
            'visibility' => 'public',
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
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
        public_path('receipts') => storage_path('app/public/receipts'),
        public_path('locations') => storage_path('app/public/locations'),
        public_path('bedrooms') => storage_path('app/public/bedrooms'),
        public_path('equipments') => storage_path('app/public/equipments'),
    ],

];
