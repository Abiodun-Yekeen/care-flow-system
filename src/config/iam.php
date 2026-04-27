<?php


return [
    /*
    |--------------------------------------------------------------------------
    | IAM Configuration
    |--------------------------------------------------------------------------
    */

    'partition' => env('IAM_PARTITION', 'cf'),

    'region' => env('IAM_REGION', 'global'),

    'account_id' => env('IAM_ACCOUNT_ID', 'default'),

    'cache' => [
        'enabled' => env('IAM_CACHE_ENABLED', true),
        'ttl' => env('IAM_CACHE_TTL', 3600),
        'prefix' => 'iam:',
        'driver' => env('IAM_CACHE_DRIVER', 'redis'), // redis, array, file
    ],

    'actions' => [

        'dashboard' => ['view'],

        'registry' => [
            'view',
            'view_dept',
            'create',
            'submit',
            'upload'
        ],

        'temporary_files' => [
            'view',
            'view_dept',
            'create',
            'submit',
            'upload'
        ],

        'tracking' => [
            'view',
            'view_dept_history',
            'search_dept'
        ],

        'my_desk' => [
            'view'
        ],

        'files' => [
            'view',
            'view_assigned',
            'treat',
            'attach_document'
        ],

        'documents' => [
            'view',
            'attach'
        ],

        'routing' => [
            'route_to_hod'
        ],

     'admin' => [
        'view',
        'create',
        'update',
        'delete',
        'attach'
    ],


        'users' => ['view', 'create', 'update', 'delete'],
        'roles' => ['view', 'create', 'update', 'delete', 'attach'],
        'policies' => ['view', 'create', 'update', 'delete'],
    ],

    'audit' => [
        'enabled' => env('IAM_AUDIT_ENABLED', true),
        'log_denials' => env('IAM_LOG_DENIALS', true),
        'log_allows' => env('IAM_LOG_ALLOWS', false),
        'channel' => env('IAM_AUDIT_CHANNEL', 'stack'),
    ],

];
