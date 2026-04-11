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
        'staff' => ['view', 'create', 'update', 'deactivate', 'list'],
        'departments' => ['view', 'create', 'update', 'list'],
        'locations' => ['view', 'create', 'update', 'list'],
        'schedules' => ['view', 'create', 'update', 'list'],

        'users' => ['view', 'create', 'update', 'delete', 'list'],
        'roles' => ['view', 'create', 'update', 'delete', 'list', 'attach'],
        'modules' => ['view', 'enable', 'disable', 'list'],
        'audit' => ['view', 'export', 'list'],

        'reports' => ['view', 'create', 'export', 'schedule'],
    ],

    'audit' => [
        'enabled' => env('IAM_AUDIT_ENABLED', true),
        'log_denials' => env('IAM_LOG_DENIALS', true),
        'log_allows' => env('IAM_LOG_ALLOWS', false),
        'channel' => env('IAM_AUDIT_CHANNEL', 'stack'),
    ],

];
