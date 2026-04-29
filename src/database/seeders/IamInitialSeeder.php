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
        // BASIC ACCESS (Everyone gets Dashboard and their own Profile)
        $basicUserPolicy = $builder->new()
            ->statement('dashboard-view')
            ->allow()
            ->action(['dashboard:ViewOverview'])
            ->resource(['arn:cf:dashboard:dashboard:*'])
            ->end()

            ->statement('self-profile-access')
            ->allow()
            ->action(['iam:GetUserDetails', 'iam:UpdateUser'])
            // This ARN is dynamic! Users can only see/edit their own ID.
            ->resource(["arn:cf:iam:users:user-\${user:id}"])
            ->end()

            ->statement('org-directory-view')
            ->allow()
            ->action(['org:ListStaff', 'org:GetStaffDetails'])
            ->resource(['arn:cf:org:staff:*'])
            ->end()

            ->create('BasicUserPolicy', 'Core system access: Dashboard, Directory, and Self-Management');
        /*
 |--------------------------------------------------------------------------
 | STAFF POLICY (Department-Bound)
 |--------------------------------------------------------------------------
 */
        $staffPolicy = $builder->new()
            ->statement('work-desk-access')
            ->allow()
            ->action(['my_desk:ListInTray', 'my_desk:ViewAssignedFiles'])
            ->resource(['arn:cf:office_files:my_desk:*'])
            // This condition ensures the evaluator checks the ID
            ->condition('NumericEquals', ['resource:department' => '${user:department_id}'])
            ->end()

            ->statement('internal-routing')
            ->allow()
            ->action(['files:RouteToHod', 'files:TreatFile'])
            ->resource(['arn:cf:office_files:files:*'])
            ->condition('NumericEquals', ['resource:department' => '${user:department_id}'])
            ->end()

            ->statement('file-processing')
            ->allow()
            ->action(['office_files:TreatFile', 'office_files:AttachDocument'])
            ->resource(['arn:cf:office_files:files:*'])
            ->condition('NumericEquals', ['resource:department' => '${user:department_id}'])
            ->end()

            ->create('StaffPolicy', 'Operational processing for files within the user\'s department');

        /*
        |--------------------------------------------------------------------------
        | HOD POLICY (Full Departmental Oversight)
        |--------------------------------------------------------------------------
        */
        $hodPolicy = $builder->new()
            ->statement('departmental-control')
            ->allow()
            ->action([
                'office_files:ListRegistry',
                'office_files:ViewDeptHistory',
                'office_files:TreatFile',
                'office_files:CloseFile'
            ])
            ->resource(['arn:cf:office_files:*:*'])
            // HODs can do anything, but ONLY within their department
            ->condition('NumericEquals', ['resource:department' => '${user:department_id}'])
            ->end()
            ->statement('external-routing')
            ->allow()
            ->action(['files:RouteToDept', 'files:RequestFile'])
            ->resource(['arn:cf:office_files:files:*','arn:cf:office_files:my_desk:*'])
            ->end()
            ->create('HodPolicy', 'Management oversight restricted to the HOD\'s department');

        /*
        |--------------------------------------------------------------------------
        | REGISTRY POLICY (Departmental Intake)
        |--------------------------------------------------------------------------
        */
        $registryPolicy = $builder->new()
            ->statement('registry-intake')
            ->allow()
            ->action(['registry:ListIncoming', 'registry:RegisterFile', 'registry:SubmitToWorkflow','registry:UpdateTempFile'])
            ->resource(['arn:cf:office_files:registry:*'])
            // If your registry is shared, remove the condition.
            // If each dept has its own registry, keep it:
            ->condition('NumericEquals', ['resource:department' => '${user:department_id}'])
            ->end()
            ->create('RegistryPolicy', 'Registry operations for the department');

        // DMS / DIRECTOR (Global Power + Work Desk)
        $dmsPolicy = $builder->new()
            ->statement('desk-access')
            ->allow()
            ->action(['my_desk:ListInTray', 'my_desk:ViewAssignedFiles'])
            ->resource(['arn:cf:office_files:my_desk:*'])
            ->end()

            ->statement('executive-oversight')
            ->allow()
            ->action([
                'office_files:ListRegistry',   // See all registries
                'office_files:GetFileDetails', // Open any file
                'office_files:ViewAllHistory', // See full movement logs
                'office_files:ReopenFile',     // Executive power
                'office_files:MergeFiles'
            ])
            // Note: No 'NumericEquals' condition here if they need to see files
            // from ALL departments. If restricted to their branch, add the condition.
            ->resource(['arn:cf:office_files:*'])
            ->end()

            ->create('DmsPolicy', 'High-level executive oversight and cross-departmental tracking');
        // AUDITOR (Read-Only Global Access)
        $auditorPolicy = $builder->new()
            ->statement('global-read-only')
            ->allow()
            ->action([
                'registry:ListIncoming',
                'my_desk:ListInTray',
                'office_files:ListRegistry',
                'office_files:GetFileDetails',
                'iam:ListUsers',
                'iam:ViewAuditLogs',
                'reports:ViewPerformanceStats'
            ])
            ->resource(['*']) // Auditors can see everything
            ->end()

            ->statement('prevent-modification')
            ->deny()
            ->action(['*:Create*', '*:Update*', '*:Delete*', '*:Treat*'])
            ->resource(['*'])
            ->end()
            ->create('AuditorPolicy', 'Total read-only access for compliance and audit');

        // SYSTEM ADMIN (Infrastructure & User Management)
        $systemAdminPolicy = $builder->new()
            ->statement('manage-iam')
            ->allow()
            ->action([
                'iam:ListUsers',
                'iam:GetUserDetails',
                'iam:CreateUser',
                'iam:UpdateUser',
                'iam:DeleteUser',
                'iam:ListRoles',
                'iam:ManageRoles',
                'iam:ListPolicies',
                'iam:ManagePolicies',
                'iam:AttachPolicy'
            ])
            ->resource([
                'arn:cf:iam:users:*',
                'arn:cf:iam:roles:*',
                'arn:cf:iam:policies:*'
            ])
            ->end()

            ->statement('system-configuration')
            ->allow()
            ->action([
                'iam:ManageModules',
                'iam:UpdateSystemSettings',
                'iam:ViewAuditLogs'
            ])
            ->resource(['arn:cf:iam:settings:*', 'arn:cf:iam:modules:*'])
            ->end()

            ->statement('org-management')
            ->allow()
            ->action([
                'org:ListDepartments',
                'org:CreateDepartment',
                'org:UpdateDepartment',
                'org:ManageStaff'
            ])
            ->resource(['arn:cf:org:*'])
            ->end()

            ->create('SystemAdminPolicy', 'Technical management of users, roles, and system settings');
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

