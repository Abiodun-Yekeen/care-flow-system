<?php

namespace Database\Seeders;

use App\Modules\Core\Iam\Models\Resource;
use App\Modules\Core\Iam\Models\Role;
use App\Modules\Core\Iam\Models\User;
use App\Modules\Core\Iam\Services\PolicyBuilderService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class IamInitialSeeder extends Seeder
{
    public function run(PolicyBuilderService $builder): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. REGISTER HIERARCHICAL RESOURCES
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () {


            foreach (config('resources.default') as $parentConfig) {
                // Determine the service name (e.g., 'office_files')
                // use resource key if module_key isn't set
                $serviceName = $parentConfig['module_key'] ?? $parentConfig['key'];

                $parent = Resource::updateOrCreate(
                    ['key' => $parentConfig['key']],
                    [
                        'name' => $parentConfig['label'],
                        'module_key' => $serviceName,
                        'route' => $parentConfig['route'] ?? null,
                        'icon' => $parentConfig['icon'] ?? null,
                        'order' => $parentConfig['order'] ?? 0,
                        'parent_id' => null,
                    ]
                );

                foreach ($parentConfig['children'] ?? [] as $childConfig) {
                    Resource::updateOrCreate(
                        ['key' => $childConfig['key']],
                        [
                            'name' => $childConfig['label'],
                            'module_key' => $serviceName,
                            'route' => $childConfig['route'] ?? null,
                            'icon' => $childConfig['icon'] ?? null,
                            'parent_id' => $parent->id,
                        ]
                    );
                }
            }
        });
        /*
        |--------------------------------------------------------------------------
        | 2. DEFINE POLICIES
        |--------------------------------------------------------------------------
        */

        // BASIC ACCESS (Everyone gets Dashboard)
        $basicUserPolicy = $builder->new()
            ->statement('dashboard-access')
            ->allow()
            ->action(['view'])
            ->resource(['arn:cf:dashboard:dashboard:*'])
            ->end()
            ->statement('profile-access')
            ->allow()
            ->action(['view', 'update'])
            ->resource(["arn:cf:organization:users:\${user_id}"])
            ->end()
            ->create('BasicUserPolicy', 'Core system access for all staff');
        // REGISTRY (Intake + Tracking visibility)
        $registryPolicy = $builder->new()
            ->statement('intake')
            ->allow()->action(['view','view_dept', 'create', 'submit', 'upload'])
            ->resource([
                'arn:cf:office_files:registry:*',
                'arn:cf:office_files:temporary_files:*',
                "arn:cf:office_files:receive_register:*",
            ])
            ->end()
            ->statement('registry-tracking')
            ->allow()->action(['view','view_dept_history', 'search_dept'])
            ->resource([
                'arn:cf:office_files:tracking:*',
                "arn:cf:office_files:current_location:*",
            ]) // Can check movement for people
            ->end()->create('RegistryPolicy', 'Registry operations and departmental tracking');

        // STAFF (Work Desk + File Processing)
        $staffPolicy = $builder->new()
            ->statement('work-desk')
            ->allow()->action(['view'])
            ->resource(['arn:cf:office_files:my_desk:*']) // Needed to see the in-tray
            ->end()
            ->statement('file-processing')
            ->allow()->action(['view_assigned', 'treat', 'attach_document'])
            ->resource(['arn:cf:office_files:files:*', 'arn:cf:office_files:documents:*'])
            ->end()
            ->statement('route')
            ->allow()->action(['route_to_hod'])
            ->resource(['arn:cf:office_files:routing:*'])
            ->end()->create('StaffPolicy', 'Operational processing for assigned files');

        // HOD
        $hodPolicy = $builder->new()
            ->statement('control')
            ->allow()->action(['view_dept', 'treat', 'close', 'merge_file', 'request_external'])
            ->resource(['arn:cf:office_files:*', 'arn:cf:office_files:my_desk:*'])
            ->end()->create('HodPolicy', 'Departmental head management');

        // DMS / DIRECTOR (Global Power + Work Desk)
        $dmsPolicy = $builder->new()
            ->statement('desk-access')
            ->allow()->action(['view'])
            ->resource(['arn:cf:office_files:my_desk:*'])
            ->end()
            ->statement('executive-power')
            ->allow()->action([
                'view_all',
                'merge_file',
                'reopen_file',
                'convert_temp',
                'open_permanent_file',
                'reactivate_file'
            ])
            ->resource(['arn:cf:office_files:files:*'])
            ->end()->create('DmsPolicy', 'High-level DMS oversight and file reactivation');

        // SYSTEM ADMIN
        $systemAdminPolicy = $builder->new()
            ->statement('manage-system')
            ->allow()->action(['*'])
            ->resource(['arn:cf:admin:*', 'arn:cf:org:*'])
            ->end()->create('SystemAdminPolicy', 'Technical management');

        // AUDITOR
        $auditorPolicy = $builder->new()
            ->statement('oversight')
            ->allow()->action(['view_all', 'export_all', 'view_all_history'])
            ->resource(['arn:cf:office_files:*', 'arn:cf:admin:audit_logs:*'])
            ->end()->create('AuditorPolicy', 'Read-only audit access');

        $superAdminPolicy = $builder->new()
            ->statement('root')
            ->allow()
            ->action(['*'])
            ->resource(['*'])
            ->end()
            ->create('SuperAdminPolicy', 'Full system access');
        /*
        |--------------------------------------------------------------------------
        | 3. DEFINE ROLES
        |--------------------------------------------------------------------------
        */
        $rolesConfig = [
            'user'=>['display' => 'Default User', 'policies' => [$basicUserPolicy->id]],
            'super-admin' => ['display' => 'Super Administrator', 'policies' => ['*']],
            'system-admin' => ['display' => 'System Administrator', 'policies' => [$systemAdminPolicy->id]],
            'director' => ['display' => 'Director / DMS', 'policies' => [$dmsPolicy->id, $auditorPolicy->id]],
            'hod' => ['display' => 'Head of Department', 'policies' => [$hodPolicy->id]],
            'registry-clerk' => ['display' => 'Registry Clerk', 'policies' => [$registryPolicy->id]],
            'staff' => ['display' => 'Staff Officer', 'policies' => [$staffPolicy->id]],
            'auditor' => ['display' => 'Internal Auditor', 'policies' => [$auditorPolicy->id]],
        ];

        foreach ($rolesConfig as $slug => $data) {
            $role = Role::firstOrCreate(['name' => $slug], [
                'display_name' => $data['display'],
                'is_system' => true
            ]);

            if ($data['policies'] === ['*']) {
                $role->policies()->sync([$superAdminPolicy->id]);
            } else {
                $role->policies()->sync(array_merge($data['policies'], [$basicUserPolicy->id]));
            }
        }

        $superAdminRole = Role::where('name', 'super-admin')->first();

        $adminUser = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'      => 'Super Admin',
                'staff_id'  => 'ROOT-001',
                'mobile_no' => '08000000000',
                'password'  => '$2y$12$Nb9Tjy9EnSe5roaVB5vFaOH91uoySy6kpZ5lihnk7P9hkoqkJK2Q2',
                'email_verified_at' => now(),
            ]
        );

        // This will now work perfectly
        $adminUser->roles()->sync([$superAdminRole->id]);
    }

}

