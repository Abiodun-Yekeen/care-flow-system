<?php

namespace Database\Seeders;

use App\Modules\Core\Iam\Models\Resource;
use App\Modules\Core\Iam\Models\Role;
use App\Modules\Core\Iam\Models\Policy;
use App\Modules\Core\Iam\Models\User;
use App\Modules\Core\Iam\Services\PolicyBuilderService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class IamInitialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(PolicyBuilderService $builder): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. REGISTER RESOURCES
        |--------------------------------------------------------------------------
        | These are the dictionary entries used by IAM, sidebar seeding, and ARN
        | resolution. Keep them aligned with your business modules, not old EMR.
        */

        $resources = [
            // Dashboard / work areas
            ['key' => 'dashboard', 'module_key' => 'dashboard', 'name' => 'Dashboard'],
            ['key' => 'my_desk', 'module_key' => 'office_files', 'name' => 'My Desk'],

            // OfficeFiles domain
            ['key' => 'registry', 'module_key' => 'office_files', 'name' => 'Registry'],
            ['key' => 'receive_register', 'module_key' => 'office_files', 'name' => 'Receive & Register File'],
            ['key' => 'temporary_files', 'module_key' => 'office_files', 'name' => 'Temporary Files'],
            ['key' => 'files', 'module_key' => 'office_files', 'name' => 'Files'],
            ['key' => 'routing', 'module_key' => 'office_files', 'name' => 'Routing'],
            ['key' => 'movement_history', 'module_key' => 'office_files', 'name' => 'Movement History'],
            ['key' => 'tracking', 'module_key' => 'office_files', 'name' => 'Tracking & Audit'],
            ['key' => 'documents', 'module_key' => 'office_files', 'name' => 'File Documents'],
            ['key' => 'reports', 'module_key' => 'office_files', 'name' => 'Reports'],

            // Organization domain
            ['key' => 'departments', 'module_key' => 'organization', 'name' => 'Departments '],


            // Administration / IAM / Audit
            ['key' => 'users', 'module_key' => 'admin', 'name' => 'User Accounts'],
            ['key' => 'roles', 'module_key' => 'admin', 'name' => 'Roles'],
            ['key' => 'policies', 'module_key' => 'admin', 'name' => 'Policies'],
            ['key' => 'modules', 'module_key' => 'admin', 'name' => 'Module Manager'],
            ['key' => 'audit_logs', 'module_key' => 'admin', 'name' => 'Audit Logs'],
            ['key' => 'system_settings', 'module_key' => 'admin', 'name' => 'System Settings'],
        ];

        foreach ($resources as $res) {
            Resource::firstOrCreate(
                ['key' => $res['key']],
                $res
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 2. DEFINE POLICIES
        |--------------------------------------------------------------------------
        */

        // A. BASIC USER POLICY
        $basicUserPolicy = $builder->new()
            ->statement('self-service')
            ->allow()
            ->action(['view_own', 'update_own'])
            ->resource(['arn:cf:admin:users/${user_id}'])
            ->end()
            ->statement('dashboard-access')
            ->allow()
            ->action(['view'])
            ->resource([
                'arn:cf:dashboard:*',
                'arn:cf:office_files:my_desk:*',
            ])
            ->end()
            ->create('BasicUserPolicy', 'Minimum access for all authenticated staff');

        // B. REGISTRY POLICY
        $registryPolicy = $builder->new()
            ->statement('registry-intake')
            ->allow()
            ->action([
                'view',
                'create',
                'submit',
                'search',
                'upload',
                'attach_document',
            ])
            ->resource([
                'arn:cf:office_files:registry:*',
                'arn:cf:office_files:receive_register:*',
                'arn:cf:office_files:documents:*',
            ])
            ->end()
            ->statement('temporary-number-handling')
            ->allow()
            ->action(['view', 'create_temp', 'convert_temp'])
            ->resource([
                'arn:cf:office_files:temporary_files:*',
                'arn:cf:office_files:files:*',
            ])
            ->end()
            ->statement('route-to-hod-only')
            ->allow()
            ->action(['route_to_hod'])
            ->resource(['arn:cf:office_files:routing:*'])
            ->end()
            ->create('RegistryPolicy', 'Registry intake, registration, scan upload, temp numbering, and submission to HOD');

        // C. STAFF POLICY
        $staffPolicy = $builder->new()
            ->statement('desk-processing')
            ->allow()
            ->action([
                'view',
                'receive',
                'treat',
                'update',
                'attach_document',
            ])
            ->resource([
                'arn:cf:office_files:my_desk:*',
                'arn:cf:office_files:files:*',
                'arn:cf:office_files:documents:*',
            ])
            ->end()
            ->statement('staff-routing')
            ->allow()
            ->action(['route_to_hod'])
            ->resource(['arn:cf:office_files:routing:*'])
            ->end()
            ->statement('deny-close-and-cross-department')
            ->deny()
            ->action(['close', 'route_to_department', 'route_to_staff'])
            ->resource(['arn:cf:office_files:routing:*'])
            ->end()
            ->create('StaffPolicy', 'Operational staff processing with upward routing to HOD only');

        // D. HOD POLICY
        $hodPolicy = $builder->new()
            ->statement('hod-file-processing')
            ->allow()
            ->action([
                'view',
                'receive',
                'treat',
                'update',
                'close',
                'attach_document',
            ])
            ->resource([
                'arn:cf:office_files:my_desk:*',
                'arn:cf:office_files:files:*',
                'arn:cf:office_files:documents:*',
            ])
            ->end()
            ->statement('hod-routing')
            ->allow()
            ->action([
                'route_to_staff',
                'route_to_department',
                'route_to_hod',
                'return_item',
            ])
            ->resource(['arn:cf:office_files:routing:*'])
            ->end()
            ->statement('hod-tracking')
            ->allow()
            ->action(['view', 'export'])
            ->resource([
                'arn:cf:office_files:tracking:*',
                'arn:cf:office_files:movement_history:*',
            ])
            ->end()
            ->statement('hod-org-read')
            ->allow()
            ->action(['view'])
            ->resource([
                'arn:cf:organization:departments:*',
                'arn:cf:organization:units:*',
                'arn:cf:organization:staff_directory:*',
                'arn:cf:organization:designations:*',
            ])
            ->end()
            ->create('HodPolicy', 'Departmental head processing, routing, closure, and oversight');

        // E. RECORDS OFFICER POLICY
        $recordsOfficerPolicy = $builder->new()
            ->statement('records-tracking')
            ->allow()
            ->action(['view', 'export'])
            ->resource([
                'arn:cf:office_files:tracking:*',
                'arn:cf:office_files:movement_history:*',
                'arn:cf:office_files:reports:*',
            ])
            ->end()
            ->statement('records-registry-support')
            ->allow()
            ->action(['view', 'search', 'convert_temp'])
            ->resource([
                'arn:cf:office_files:registry:*',
                'arn:cf:office_files:temporary_files:*',
                'arn:cf:office_files:files:*',
            ])
            ->end()
            ->create('RecordsOfficerPolicy', 'Records supervision, tracking, temp conversion, and registry support');

        // F. AUDITOR POLICY
        $auditorPolicy = $builder->new()
            ->statement('oversight')
            ->allow()
            ->action(['view_*', 'list_*', 'export_*', 'generate_report'])
            ->resource([
                'arn:cf:office_files:tracking:*',
                'arn:cf:office_files:movement_history:*',
                'arn:cf:office_files:reports:*',
                'arn:cf:admin:audit_logs:*',
            ])
            ->end()
            ->statement('deny-mutation')
            ->deny()
            ->action(['create_*', 'update_*', 'delete_*', 'route_*', 'close'])
            ->resource(['*'])
            ->end()
            ->create('AuditorPolicy', 'Read-only institutional oversight and audit access');

        // G. SYSTEM ADMIN POLICY
        $adminPolicy = $builder->new()
            ->statement('manage-administration')
            ->allow()
            ->action(['*'])
            ->resource([
                'arn:cf:admin:users:*',
                'arn:cf:admin:roles:*',
                'arn:cf:admin:policies:*',
                'arn:cf:admin:modules:*',
                'arn:cf:admin:audit_logs:*',
                'arn:cf:admin:system_settings:*',
                'arn:cf:organization:*',
            ])
            ->end()
            ->statement('operational-read')
            ->allow()
            ->action(['view', 'list', 'export'])
            ->resource([
                'arn:cf:office_files:registry:*',
                'arn:cf:office_files:files:*',
                'arn:cf:office_files:routing:*',
                'arn:cf:office_files:tracking:*',
                'arn:cf:office_files:movement_history:*',
                'arn:cf:office_files:reports:*',
            ])
            ->end()
            ->statement('deny-regular-file-processing')
            ->deny()
            ->action(['treat', 'route_to_hod', 'route_to_staff', 'route_to_department', 'close'])
            ->resource(['arn:cf:office_files:routing:*'])
            ->end()
            ->create('SystemAdminPolicy', 'Technical and administrative control without day-to-day operational routing power');

        // H. SUPER ADMIN POLICY
        $superAdminPolicy = $builder->new()
            ->statement('root-access')
            ->allow()
            ->action(['*'])
            ->resource(['*'])
            ->end()
            ->create('SuperAdminPolicy', 'Absolute system access');

        /*
        |--------------------------------------------------------------------------
        | 3. DEFINE ROLES & ATTACH POLICIES
        |--------------------------------------------------------------------------
        */

        $rolesConfig = [
            'user' => [
                'desc' => 'Base authenticated staff role',
                'policies' => [$basicUserPolicy->id],
            ],
            'staff' => [
                'desc' => 'Operational department staff',
                'policies' => [$basicUserPolicy->id, $staffPolicy->id],
            ],
            'hod' => [
                'desc' => 'Head of Department',
                'policies' => [$basicUserPolicy->id, $hodPolicy->id],
            ],
            'records-officer' => [
                'desc' => 'Records management officer',
                'policies' => [$basicUserPolicy->id, $recordsOfficerPolicy->id],
            ],
            'auditor' => [
                'desc' => 'Audit and oversight officer',
                'policies' => [$basicUserPolicy->id, $auditorPolicy->id],
            ],
            'system-admin' => [
                'desc' => 'Technical system administrator',
                'policies' => [$basicUserPolicy->id, $adminPolicy->id],
            ],
            'super-admin' => [
                'desc' => 'Developer / root access',
                'policies' => [$superAdminPolicy->id],
            ],
        ];

        foreach ($rolesConfig as $name => $data) {
            $role = Role::firstOrCreate(
                ['name' => $name],
                [
                    'description' => $data['desc'],
                    'is_system' => true,
                ]
            );

            $role->policies()->sync($data['policies']);
        }

        /*
        |--------------------------------------------------------------------------
        | 4. ROLE INHERITANCE
        |--------------------------------------------------------------------------
        */

        $baseUserRole = Role::where('name', 'user')->first();
        $specialtyRoles = Role::whereIn('name', [
            'staff',
            'hod',
            'records-officer',
            'auditor',
            'system-admin',
        ])->get();

        foreach ($specialtyRoles as $role) {
            $role->attachParent($baseUserRole);
        }

        /*
        |--------------------------------------------------------------------------
        | 5. DEFAULT SUPER ADMIN USER
        |--------------------------------------------------------------------------
        */

        $superAdminRole = Role::where('name', 'super-admin')->first();

        $adminUser = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Super Admin',
                'staff_id' => '123',
                'mobile_no' => '08069318176',
                'password' => Hash::make('password'),
            ]
        );

        $adminUser->roles()->syncWithoutDetaching([$superAdminRole->id]);
    }
}
