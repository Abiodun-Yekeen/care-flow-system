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
            'label' => 'Incoming Registry', // More descriptive
            'route' => '/registry',
            'icon' => 'inbox',
            'order' => 2,
            'module_key' => 'office_files',
            'children' => [
                [
                    'key' => 'receive_register',
                    'label' => 'Receive & Register File',
                    'route' => '/registry/register',
                    'icon' => 'plus-circle',
                    'actions' => ['view', 'create', 'submit'],
                ],
                [
                    'key' => 'temporary_files',
                    'label' => 'Temporary Files',
                    'route' => '/registry/temp-files',
                    'icon' => 'file-clock',
                    'actions' => ['view', 'update', 'submit'],
                ],
            ],
        ],

        'my_desk' => [
            'key' => 'my_desk',
            'label' => 'Work Desk',
            'route' => '/my-desk',
            'icon' => 'briefcase',
            'order' => 3,
            'module_key' => 'office_files',
            'children' => [
                [
                    'key' => 'my_files',
                    'label' => 'My In-Tray', // Files currently with the user
                    'route' => '/my-desk/files',
                    'icon' => 'file-text',
                ],
                [
                    'key' => 'movement_history', // Moved from Tracking for better context
                    'label' => 'My File History',
                    'route' => '/my-desk/history',
                    'icon' => 'history',
                ],
                [
                    'key' => 'file_documents',
                    'label' => 'My Documents',
                    'route' => '/my-desk/documents',
                    'icon' => 'files',
                    'actions' => ['view', 'search', 'download'],
                ],
            ],
        ],

        // Global Admin/Audit section
        'system_tracking' => [
            'key' => 'tracking',
            'label' => 'Global Tracking',
            'route' => '/tracking',
            'icon' => 'map-pin',
            'order' => 4,
            'module_key' => 'office_files',
            'children' => [
                ['key' => 'current_location', 'label' => 'Find Any File', 'route' => '/tracking/location', 'icon' => 'search'],
//                ['key' => 'processing_delays', 'label' => 'System Bottlenecks', 'route' => '/tracking/delays', 'icon' => 'clock-alert'],
            ]
        ],

        'organization' => [
            'key' => 'organization',
            'label' => 'Organization',
            'icon' => 'building',
            'route' => '/org',
            'order' => 5,
            'actions' => ['view'],
            'children' => [
                [
                    'key' => 'departments',
                    'label' => 'Departments / MDAs',
                    'route' => '/org/departments',
                    'icon' => 'building-2',
                    'order' => 1,
                    'actions' => ['view', 'create', 'update'],
                ],

                [
                    'key' => 'staff_directory',
                    'label' => 'Staff Directory',
                    'route' => '/org/staff',
                    'icon' => 'users',
                    'order' => 2,
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
            'module_key' => 'office_files',
            'actions' => ['view'],

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



