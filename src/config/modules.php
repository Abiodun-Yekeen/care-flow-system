<?php

return [

    /*
    |--------------------------------------------------------------------------
    | eFile Nigeria System Defaults
    |--------------------------------------------------------------------------
    */

    'system' => [
        'name' => 'CareFlow',
        'code' => 'care-flow',
        'version' => '1.0.0',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Modules (BOOTSTRAP ONLY)
    |--------------------------------------------------------------------------
    | Used when database is empty or on first install
    */

    'default' => [

        'dashboard' => [
            'key' => 'dashboard',
            'label' => 'Dashboard',
            'route' => '/dashboard',
            'icon' => 'layout-dashboard',
            'order' => 1,
            'actions' => ['view'],
        ],

        'registry' => [
            'key' => 'registry',
            'label' => 'Registry',
            'route' => '/registry',
            'icon' => 'folder-plus',
            'order' => 2,
            'actions' => ['view'],
            'children' => [
                [
                    'key' => 'receive_register',
                    'label' => 'Receive & Register File',
                    'route' => '/registry/register',
                    'icon' => 'inbox',
                    'order' => 1,
                    'actions' => ['view', 'create', 'submit'],
                ],
                [
                    'key' => 'temporary_files',
                    'label' => 'Temporary Files',
                    'route' => '/registry/temp-files',
                    'icon' => 'file-clock',
                    'order' => 2,
                    'actions' => ['view', 'convert'],
                ],
            ],
        ],

        'my_desk' => [
            'key' => 'my_desk',
            'label' => 'My Desk',
            'route' => '/my-desk',
            'icon' => 'briefcase',
            'order' => 3,
            'actions' => ['view'],
        ],

        'tracking' => [
            'key' => 'tracking',
            'label' => 'Tracking & Audit',
            'route' => '/tracking',
            'icon' => 'map-pin',
            'order' => 4,
            'actions' => ['view'],
            'children' => [
                [
                    'key' => 'movement_history',
                    'label' => 'Movement History',
                    'route' => '/tracking/movement',
                    'icon' => 'history',
                    'order' => 1,
                    'actions' => ['view', 'export'],
                ],
                [
                    'key' => 'current_location',
                    'label' => 'Current Location',
                    'route' => '/tracking/location',
                    'icon' => 'map-pinned',
                    'order' => 2,
                    'actions' => ['view'],
                ],
                [
                    'key' => 'processing_delays',
                    'label' => 'Delayed Processing',
                    'route' => '/tracking/delays',
                    'icon' => 'triangle-alert',
                    'order' => 3,
                    'actions' => ['view'],
                ],
                [
                    'key' => 'audit_trail',
                    'label' => 'Audit Trail',
                    'route' => '/tracking/audit',
                    'icon' => 'shield-check',
                    'order' => 4,
                    'actions' => ['view', 'export'],
                ],
            ],
        ],

        'organization' => [
            'key' => 'organization',
            'label' => 'Organization',
            'icon' => 'building',
            'route' => '/organization',
            'order' => 5,
            'actions' => ['view'],
            'children' => [
                [
                    'key' => 'departments',
                    'label' => 'Departments / MDAs',
                    'route' => '/organization/departments',
                    'icon' => 'building-2',
                    'order' => 1,
                    'actions' => ['view', 'create', 'update'],
                ],
                [
                    'key' => 'units',
                    'label' => 'Units / Divisions',
                    'route' => '/organization/units',
                    'icon' => 'blocks',
                    'order' => 2,
                    'actions' => ['view', 'create', 'update'],
                ],
                [
                    'key' => 'staff_directory',
                    'label' => 'Staff Directory',
                    'route' => '/organization/staff',
                    'icon' => 'users',
                    'order' => 3,
                    'actions' => ['view', 'create', 'update'],
                ],
            ],
        ],

        'admin' => [
            'key' => 'admin',
            'label' => 'Administration',
            'icon' => 'settings',
            'route' => '/admin',
            'order' => 6,
            'actions' => ['view'],
            'children' => [
                [
                    'key' => 'users',
                    'label' => 'User Accounts',
                    'icon' => 'user-cog',
                    'route' => '/admin/users',
                    'order' => 1,
                    'actions' => ['view', 'create', 'update', 'delete'],
                ],
                [
                    'key' => 'roles',
                    'label' => 'Roles',
                    'icon' => 'key',
                    'route' => '/admin/roles',
                    'order' => 2,
                    'actions' => ['view', 'create', 'update', 'delete', 'attach'],
                ],
                [
                    'key' => 'policies',
                    'label' => 'Policies',
                    'icon' => 'shield',
                    'route' => '/admin/policies',
                    'order' => 3,
                    'actions' => ['view', 'create', 'update', 'delete', 'attach'],
                ],
                [
                    'key' => 'modules',
                    'label' => 'Module Manager',
                    'icon' => 'puzzle',
                    'route' => '/admin/modules',
                    'order' => 4,
                    'actions' => ['view', 'enable', 'disable'],
                ],
                [
                    'key' => 'audit',
                    'label' => 'Audit Logs',
                    'icon' => 'history',
                    'route' => '/admin/audit',
                    'order' => 5,
                    'actions' => ['view', 'export'],
                ],
                [
                    'key' => 'system_settings',
                    'label' => 'System Settings',
                    'route' => '/admin/system-settings',
                    'icon' => 'sliders-horizontal',
                    'order' => 6,
                    'actions' => ['view', 'update'],
                ],
            ],
        ],

        'reports' => [
            'key' => 'reports',
            'label' => 'Reports',
            'icon' => 'bar-chart-3',
            'route' => '/reports',
            'order' => 7,
            'actions' => ['view'],
            'children' => [
                [
                    'key' => 'registry_reports',
                    'label' => 'Registry Reports',
                    'route' => '/reports/registry',
                    'icon' => 'clipboard-list',
                    'order' => 1,
                    'actions' => ['view', 'export'],
                ],
                [
                    'key' => 'routing_reports',
                    'label' => 'Routing Reports',
                    'route' => '/reports/routing',
                    'icon' => 'git-compare',
                    'order' => 2,
                    'actions' => ['view', 'export'],
                ],
                [
                    'key' => 'department_reports',
                    'label' => 'Department Reports',
                    'route' => '/reports/departments',
                    'icon' => 'building',
                    'order' => 3,
                    'actions' => ['view', 'export'],
                ],
                [
                    'key' => 'performance_reports',
                    'label' => 'Performance Reports',
                    'route' => '/reports/performance',
                    'icon' => 'activity',
                    'order' => 4,
                    'actions' => ['view', 'export'],
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Routing Rules
    |--------------------------------------------------------------------------
    */

    'routing_rules' => [
        'staff_can_only_route_to_hod' => true,
        'registry_routes_to_hod_only' => true,
        'hod_can_route_to_staff' => true,
        'hod_can_route_to_other_departments' => true,
        'only_hod_can_close_file' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | File Numbering Rules
    |--------------------------------------------------------------------------
    */

    'file_numbering' => [
        'temp_prefix' => 'TMP',
        'temp_format' => '{prefix}-{mda}-{year}-{sequence}',
        'official_format' => '{mda}/{department}/{year}/{sequence}',
        'allow_temp_to_official_conversion' => true,
        'preserve_temp_number_after_conversion' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Document Rules
    |--------------------------------------------------------------------------
    */

    'documents' => [
        'allowed_extensions' => ['pdf', 'jpg', 'jpeg', 'png'],
        'max_upload_size_mb' => 10,
        'scan_required_on_registration' => true,
    ],

];
