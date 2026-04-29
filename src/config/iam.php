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

        'dashboard' => [
            'dashboard:ViewOverview',
        ],

        'registry' => [
            'registry:ListIncoming',      // View the registry list
            'registry:GetFileDetails',    // View specific file details
            'registry:RegisterFile',      // Create/Register new file
            'registry:SubmitToWorkflow',  // Push to next stage
            'registry:UploadEvidence',    // Attach scans
            'registry:UpdateTempFile',    // Manage temporary records
        ],

        'my_desk' => [
            'my_desk:ListInTray',         // Show 'My Desk' menu
            'my_desk:ViewAssignedFiles',  // Access files sent to user
            'my_desk:ViewPersonalHistory',// Access user's own movement logs
            'my_desk:ListDocuments',      // View personal document vault
            'my_desk:DownloadDocument',
        ],

        'tracking' => [
            'tracking:SearchGlobal',      // Find any file in the system
            'tracking:ViewBottlenecks',    // View system delay reports
            'tracking:ViewDeptHistory',    // View history for whole dept
        ],
        'files' => [
            'files:TreatFile',            // Minute/Approve/Comment
            'files:AttachDocument',       // Add documents to file
            'files:RouteToHod',           // Internal: Send to own HOD
            'files:RouteToDept',          // External: Send to another Department
            'files:RequestFile',          // Pull/Request a file from another Dept/Registry
            'files:MergeFiles',           // Administrative merging
            'files:CloseFile',            // Mark as completed
        ], 

        'org' => [
            'org:ListDepartments',
            'org:GetDepartmentDetails',
            'org:CreateDepartment',
            'org:UpdateDepartment',
            'org:ListStaff',
            'org:GetStaffDetails',
            'org:CreateStaff',
            'org:UpdateStaff',
        ],

        'admin' => [
            // User Management
            'iam:ListUsers',
            'iam:GetUserDetails',
            'iam:CreateUser',
            'iam:UpdateUser',
            'iam:DeleteUser',

            // Access Control
            'iam:ListRoles',
            'iam:ManageRoles',             // Create/Update/Delete roles
            'iam:ListPolicies',
            'iam:ManagePolicies',          // Create/Update/Delete policies
            'iam:AttachPolicy',            // Bind policy to role/user

            // System Config
            'iam:ManageModules',           // Enable/Disable modules
            'iam:ViewAuditLogs',
            'iam:ExportAuditLogs',
            'iam:UpdateSystemSettings',
        ],

        'reports' => [
            'reports:ViewRegistryStats',
            'reports:ViewRoutingStats',
            'reports:ViewPerformanceStats',
            'reports:ExportReportData',
        ],
    ],
];
