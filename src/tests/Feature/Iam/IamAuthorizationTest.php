<?php

namespace Tests\Feature\Iam;

use App\Modules\Clinical\Patient\Models\Patient;
use App\Modules\Core\Iam\Models\Policy;
use App\Modules\Core\Iam\Models\Resource;
use App\Modules\Core\Iam\Models\Role;
use App\Modules\Core\Iam\Models\User;
use App\Modules\Core\Iam\Services\PolicyBuilderService;
use Database\Seeders\IamInitialSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class IamAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        PolicyBuilderService::clearResourceMap();
       $this->seed(IamInitialSeeder::class);
        Cache::flush();
    }

    /*
  |--------------------------------------------------------------------------
  | Prevevent Permission Escalation
  |--------------------------------------------------------------------------
  */
    public function test_inherited_deny_overrides_child_allow()
    {
        $builder = app(PolicyBuilderService::class);

        $parentRole = Role::create(['name' => 'restricted-area']);
        $childRole = Role::create(['name' => 'medical-staff']);

        $denyPolicy = $builder->new()
            ->statement('deny-archives')->deny()->action(['*'])->resource(['archives:*'])->end()
            ->create('DenyArchives');

        $allowPolicy = $builder->new()
            ->statement('allow-all')->allow()->action(['*'])->resource(['*'])->end()
            ->create('AllowAll');

        $parentRole->policies()->attach($denyPolicy);
        $childRole->policies()->attach($allowPolicy);

        $childRole->attachParent($parentRole);

        $user = User::factory()->create();
        $user->assignRole($childRole);

        // Should DENY because parent says so, even though child says allow
        $this->assertFalse($user->can('view', 'archives:folder_1'));
    }
    /*
  |--------------------------------------------------------------------------
  |
  |--------------------------------------------------------------------------
  */
    public function test_arn_wildcard_deep_matching()
    {
        $builder = app(PolicyBuilderService::class);
        $user = User::factory()->create();
        $policy = $builder->new()
            ->statement('deep-match')
            ->allow()
            ->action(['view'])
            ->resource(['arn:emr:clinical:patients:*'])
            ->end()
            ->create('DeepMatchPolicy');
        $role = Role::create(['name' => 'staff']);
        $role->policies()->attach($policy);

        $user->assignRole($role);


        //$user->assignRole(Role::create(['name' => 'staff'])->policies()->attach($policy));

        // Testing different depths
        $this->assertTrue($user->can('view', 'arn:emr:clinical:patients:123'));
        $this->assertTrue($user->can('view', 'arn:emr:clinical:patients:123:records:456'));
    }
    /*
  |--------------------------------------------------------------------------
  | Performance.
  |--------------------------------------------------------------------------
  */
    public function test_performance_with_many_policies()
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'heavy-role']);

        // Create 50 dummy policies
        for ($i = 0; $i < 50; $i++) {
            $p = Policy::create([
                'name' => "Dummy$i",
                'statement' => 'allow',
                'action' => ["action_$i"],
                'resource' => ["res_$i"]
            ]);
            $role->policies()->attach($p);
        }

        $user->assignRole($role);

        $start = microtime(true);
        $user->can('action_49', 'res_49');
        $end = microtime(true);

        // Authorization should ideally take less than 100ms (0.1s)
        $this->assertLessThan(0.1, $end - $start);
    }
    /*
   |--------------------------------------------------------------------------
   | dynamic replacement to prevent user from edit every other user's profile.
   |--------------------------------------------------------------------------
   */

    public function test_variable_replacement_works()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $builder = app(PolicyBuilderService::class);

        $policy = $builder->new()
            ->statement('own-profile')
            ->allow()
            ->action(['update'])
            ->resource(["arn:emr:iam:users:\${user_id}"]) // Note the escape for PHP
            ->end()
            ->create('OwnProfilePolicy');

        $role = Role::create(['name' => 'profile-manager']);
        $role->policies()->attach($policy);
        $user->assignRole($role);

        // Should ALLOW: accessing own ID
        $this->assertTrue($user->can('update', "arn:emr:iam:users:{$user->id}"));

        // Should DENY: accessing someone else's ID
        $this->assertFalse($user->can('update', "arn:emr:iam:users:{$otherUser->id}"));
    }
    /*
    |--------------------------------------------------------------------------
    | BASIC ALLOW / DENY
    |--------------------------------------------------------------------------
    */

    public function test_basic_allow()
    {
        $builder = app(PolicyBuilderService::class);

        $policy = $builder->new()
            ->statement('allow-view')
            ->allow()
            ->action(['view_patients'])
            ->resource(['*'])
            ->end()
            ->create('AllowView');

        $role = Role::firstOrCreate(['name' => 'doctor']);
       /// $role->policies()->attach($policy);
         $role->policies()->attach($policy->id);

        $user = User::factory()->create();
        $user->assignRole($role);



        $this->assertTrue($user->can('view_patients', 'patients'));
        $this->assertFalse($user->can('delete_patients', 'patients'));
    }

    /*
    |--------------------------------------------------------------------------
    | DENY PRECEDENCE ACROSS POLICIES
    |--------------------------------------------------------------------------
    */

    public function test_deny_across_multiple_policies()
    {
        $builder = app(PolicyBuilderService::class);

        $allow = $builder->new()
            ->statement('allow')
            ->allow()
            ->action(['view_patients'])
            ->resource(['*'])
            ->end()
            ->create('AllowPolicy');

        $deny = $builder->new()
            ->statement('deny')
            ->deny()
            ->action(['view_patients'])
            ->resource(['*'])
            ->end()
            ->create('DenyPolicy');

        $role = Role::firstOrCreate(['name' => 'conflict-role']);
        $role->policies()->attach([$allow->id, $deny->id]);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->assertFalse(
            $user->can('view_patients', 'patients'),
            'Deny must override allow across policies'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | MULTI ROLE CONFLICT
    |--------------------------------------------------------------------------
    */

    public function test_deny_from_one_role_overrides_allow_from_another()
    {
        $builder = app(PolicyBuilderService::class);

        $allowPolicy = $builder->new()
            ->statement('allow')
            ->allow()
            ->action(['view_patients'])
            ->resource(['*'])
            ->end()
            ->create('AllowRolePolicy');

        $denyPolicy = $builder->new()
            ->statement('deny')
            ->deny()
            ->action(['view_patients'])
            ->resource(['*'])
            ->end()
            ->create('DenyRolePolicy');

        $roleA = Role::firstOrCreate(['name' => 'roleA']);
        $roleA->policies()->attach($allowPolicy);

        $roleB = Role::firstOrCreate(['name' => 'roleB']);
        $roleB->policies()->attach($denyPolicy);

        $user = User::factory()->create();
        $user->assignRole($roleA);
        $user->assignRole($roleB);

        $this->assertFalse($user->can('view_patients', 'patients'));
    }

    /*
    |--------------------------------------------------------------------------
    | RESOURCE WILDCARD MATCHING
    |--------------------------------------------------------------------------
    */

    public function test_resource_wildcard_matching()
    {
        $builder = app(PolicyBuilderService::class);

        $policy = $builder->new()
            ->statement('resource-wildcard')
            ->allow()
            ->action(['view_*'])
            ->resource(['arn:emr:clinical:*'])
            ->end()
            ->create('ResourceWildcard');

        $role = Role::firstOrCreate(['name' => 'observer']);
        $role->policies()->attach($policy);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->assertTrue($user->can('view_patients', 'patients'));
        $this->assertTrue($user->can('view_encounters', 'encounters'));
    }

    /*
    |--------------------------------------------------------------------------
    | CONDITION LOGIC
    |--------------------------------------------------------------------------
    */

    public function test_condition_string_equals()
    {
        $builder = app(PolicyBuilderService::class);
        $policy = $builder->new()
            ->statement('allow')
            ->action(['view_patients'])
            ->resource(['patients'])
            ->condition('StringEquals', ['department' => 'emergency'])
            ->end()
            ->create('ConditionalPolicy');

        $role = Role::firstOrCreate(['name' => 'conditional-role']);
        $role->policies()->sync([$policy->id]);

        $user = User::factory()->create();
        $user->assignRole($role);

        // Clear EVERYTHING to be safe
        \Illuminate\Support\Facades\Cache::flush();
        app()->forgetInstance(\Illuminate\Contracts\Auth\Access\Gate::class);

        // Test positive case
        $this->assertTrue(
            $user->can('view_patients', ['patients', ['department' => 'emergency']])
        );

        // Test negative case
        $this->assertFalse(
            $user->can('view_patients', ['patients', ['department' => 'cardiology']])
        );
    }
    /*
    |--------------------------------------------------------------------------
    | CACHE INVALIDATION
    |--------------------------------------------------------------------------
    */

    public function test_cache_invalidates_when_role_changes()
    {
        $builder = app(PolicyBuilderService::class);
        $policy = $builder->new()
            ->statement('allow')
            ->action(['view_patients'])
            ->resource(['*'])
            ->end()
            ->create('CachePolicy');

        $role = Role::firstOrCreate(['name' => 'cached-role']);
        $role->policies()->sync([$policy->id]);

        $user = User::factory()->create();
        $user->roles()->detach(); // Start clean

        // 1. Initial check (should be false)
        $this->assertFalse($user->can('view_patients', 'patients'));

        $user->assignRole($role);

        // FORCE REFRESH EVERYTHING
        \Illuminate\Support\Facades\Cache::flush();
        app()->forgetInstance(\Illuminate\Contracts\Auth\Access\Gate::class);
        \Illuminate\Support\Facades\Facade::clearResolvedInstance(\Illuminate\Contracts\Auth\Access\Gate::class);

        $user = $user->fresh();
        $this->assertTrue($user->can('view_patients', 'patients'));
    }
    /*
    |--------------------------------------------------------------------------
    | POLICY VERSION ROLLBACK
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | CIRCULAR INHERITANCE PROTECTION
    |--------------------------------------------------------------------------
    */

    public function test_circular_inheritance_is_blocked()
    {
        $roleA = Role::firstOrCreate(['name' => 'A']);
        $roleB = Role::firstOrCreate(['name' => 'B']);

        $roleA->attachParent($roleB);

        $this->expectException(\RuntimeException::class);

        $roleB->attachParent($roleA);
    }

    public function test_doctor_can_view_patients()
    {
        // Create doctor role with policy
        $builder = app(PolicyBuilderService::class);
        $policy = $builder->new()
            ->statement('view-patients')
            ->allow()
            ->action(['view_patients'])
            ->resource(['arn:emr:clinical:test:patients'])
            ->end()
            ->create('ViewPatientsPolicy');

        $doctorRole = Role::firstOrCreate(['name' => 'doctor']);
        $doctorRole->policies()->attach($policy);

        // Create doctor user
        $doctor = User::factory()->create();
        $doctor->assignRole($doctorRole);

        // Test authorization
        $this->assertTrue($doctor->can('view_patients', 'patients'));
        $this->assertFalse($doctor->can('delete_patients', 'patients'));
    }

    public function test_deny_overrides_allow()
    {
        $resource = Resource::where('key', 'patients')->first();
        $patientsArn = $resource->arn->toString(); // e.g., "arn:emr:clinical:patients"

        $builder = app(PolicyBuilderService::class);
        $policy = $builder->new()
            ->statement('allow-view')
            ->allow()
            ->action(['view_patients'])
            ->resource([$patientsArn]) // Dynamic!
            ->end()
            ->statement('deny-specific')
            ->deny()
            ->action(['view_patients'])
            ->resource([$patientsArn . ':123'])
            ->end()
            ->create('ComplexPolicy');

        $role = Role::firstOrCreate(['name' => 'test']);
        $role->policies()->attach($policy);

        $user = User::factory()->create();
        $user->assignRole($role);

        // Can view general patients
        $this->assertTrue($user->can('view_patients', 'patients'));

        // Cannot view specific patient 123
        $patient = Patient::create(['id' => 123, 'name' => 'John Doe']);
        $this->assertFalse($user->can('view', $patient));
    }


    public function test_role_inheritance()
    {
        // 1. Clear everything at the start
        \Illuminate\Support\Facades\Cache::flush();

        // 2. Create base role with a TOTAL wildcard policy
        $basePolicy = app(PolicyBuilderService::class)->new()
            ->statement('base')
            ->allow()
            ->action(['*'])      // Allow any action
            ->resource(['*'])    // on any resource
            ->end()
            ->create('BasePolicy');

        $baseRole = Role::firstOrCreate(['name' => 'base-role']);
        $baseRole->policies()->sync([$basePolicy->id]);

        // 3. Create child role (no policy needed, just to test inheritance)
        $childRole = Role::firstOrCreate(['name' => 'child-role']);

        // 4. Attach Parent
        $childRole->attachParent($baseRole);

        // 5. Create User and Assign Child Role
        $user = User::factory()->create();
        $user->assignRole($childRole);

        // 6. CRITICAL: Force the service to forget the user's policy cache
        Cache::forget("user:{$user->id}:policies");
        Cache::forget("role:{$childRole->id}:inherited");
       Cache::flush();

        // 7. Verify - if this fails, the issue is in RoleHierarchyResolver
        $this->assertTrue($user->can('view_patients', 'patients'), "Inheritance failed to resolve parent policies");
    }

    public function test_deep_role_inheritance()
    {
        $builder = app(PolicyBuilderService::class);

        // Level 1: Grandparent (has the permission)
        $grandParentPolicy = $builder->new()
            ->statement('gp')->allow()->action(['delete_records'])->resource(['*'])->end()
            ->create('GPPolicy');
        $grandParentRole = Role::firstOrCreate(['name' => 'grandparent']);
        $grandParentRole->policies()->attach($grandParentPolicy);

        // Level 2: Parent
        $parentRole = Role::firstOrCreate(['name' => 'parent']);
        $parentRole->attachParent($grandParentRole);

        // Level 3: Child
        $childRole = Role::firstOrCreate(['name' => 'child']);
        $childRole->attachParent($parentRole);

        $user = User::factory()->create();
        $user->assignRole($childRole);

        $this->assertTrue($user->can('delete_records', 'patients'), "Failed to inherit from Grandparent level");
    }
    public function test_action_wildcards()
    {
        $builder = app(PolicyBuilderService::class);
        $policy = $builder->new()
            ->statement('wildcard-action')
            ->allow()
            ->action(['view_*']) // Matches view_patients, view_encounters
            ->resource(['*'])
            ->end()
            ->create('WildcardActionPolicy');

        $role = Role::firstOrCreate(['name' => 'observer']);
        $role->policies()->attach($policy);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->assertTrue($user->can('view_patients', 'patients'));
        $this->assertTrue($user->can('view_encounters', 'encounters'));
        $this->assertFalse($user->can('edit_patients', 'patients'), "Wildcard allowed an action it shouldn't have");
    }

    public function test_default_deny_with_no_roles()
    {
        $user = User::factory()->create(); // No roles assigned

        // Should always be false for any sensitive action
        $this->assertFalse($user->can('view_patients', 'patients'));
    }

    public function test_authorization_performance_under_load()
    {
        $builder = app(PolicyBuilderService::class);

        // 1. Create 50 distinct roles with 5 policies each (250 total policies)
        $roles = [];
        for ($i = 0; $i < 50; $i++) {
            $policy = $builder->new()
                ->statement("stmt-{$i}")
                ->allow()
                ->action(["action_{$i}"])
                ->resource(["resource_{$i}"])
                ->end()
                ->create("BulkPolicy-{$i}");

            $role = Role::firstOrCreate(['name' => "bulk-role-{$i}"]);
            $role->policies()->attach($policy);
            $roles[] = $role;
        }

        // 2. Create a User and assign ALL 50 roles
        $user = User::factory()->create();
        foreach ($roles as $role) {
            $user->assignRole($role);
        }

        // 3. Measure First Run (Cold Cache - Database + Logic)
        $startCold = microtime(true);
        $user->can('action_49', 'resource_49');
        $endCold = microtime(true);
        $coldTime = ($endCold - $startCold) * 1000; // Convert to ms

        // 4. Measure Second Run (Warm Cache - Redis/Internal Cache)
        $startWarm = microtime(true);
        $user->can('action_49', 'resource_49');
        $endWarm = microtime(true);
        $warmTime = ($endWarm - $startWarm) * 1000;

        // Output results to console
        dump("Cold Auth Time: " . number_format($coldTime, 2) . "ms");
        dump("Warm Auth Time: " . number_format($warmTime, 2) . "ms");

        // 5. Assertions: In a medical EMR, auth should NEVER take more than 200ms cold
        // and should be under 5ms warm (Redis).
        $this->assertLessThan(200, $coldTime, "Cold authorization is too slow!");
        $this->assertLessThan(10, $warmTime, "Warm (cached) authorization is too slow!");
    }

    public function test_it_handles_malformed_json_policies_gracefully()
    {
        $user = User::factory()->create();
        // Simulate a corrupted JSON string in the database
        $policy = Policy::create([
            'name' => 'Broken Policy',
            'statement' => '{"Statement": [{"Effect": "Allow", "Action": "view", "Resource": "arn:emr:iam:*}' // Missing closing bracket
        ]);

        $user->assignRole(Role::create(['name' => 'tester'])->policies()->attach($policy));

        // Should return false, but NOT throw an exception
        $this->assertFalse($user->can('view', 'arn:emr:iam:users:1'));
    }

    //New
    public function test_it_handles_missing_keys_in_policy_document()
    {
        $user = User::factory()->create();
        // Policy missing the 'Effect' key
        $policy = Policy::create([
            'name' => 'Invalid Format',
            'statement' => json_encode([
                'Statement' => [
                    ['Action' => 'view', 'Resource' => '*']
                ]
            ])
        ]);

        $user->assignRole(Role::create(['name' => 'tester2'])->policies()->attach($policy));

        $this->assertFalse($user->can('view', 'arn:emr:anything'));
    }

    public function test_it_handles_massive_policy_inheritance_efficiently()
    {
        $user = User::factory()->create();
        $rootRole = Role::create(['name' => 'root']);

        // Create a chain of 50 inherited roles
        $currentRole = $rootRole;
        for ($i = 0; $i < 50; $i++) {
            $parentRole = Role::create(['name' => "parent_{$i}"]);
            $currentRole->update(['parent_id' => $parentRole->id]);

            // Attach a policy to every 5th role
            if ($i % 5 === 0) {
                $policy = Policy::create([
                    'name' => "Policy_{$i}",
                    'statement' => json_encode([
                        'Statement' => [['Effect' => 'Allow', 'Action' => 'view', 'Resource' => "arn:emr:res:{$i}"]]
                    ])
                ]);
                $parentRole->policies()->attach($policy);
            }
            $currentRole = $parentRole;
        }

        $user->assignRole($rootRole);

        $start = microtime(true);
        $result = $user->can('view', 'arn:emr:res:10');
        $end = microtime(true);

        $this->assertTrue($result);
        $this->assertLessThan(0.5, $end - $start, "Policy evaluation took too long!");
    }

    public function test_cache_consistency_under_rapid_role_changes()
    {
        $user = User::factory()->create();
        $resource = 'arn:emr:iam:users:1';
        $role = Role::create(['name' => 'editor']);
        $policy = Policy::create([
            'name' => 'EditPolicy',
            'statement' => json_encode(['Statement' => [['Effect' => 'Allow', 'Action' => 'edit', 'Resource' => $resource]]])
        ]);
        $role->policies()->attach($policy);

        // 1. Initially No Access
        $this->assertFalse($user->can('edit', $resource));

        // 2. Grant Access
        $user->assignRole($role);
        // Note: If you use caching, ensure the service clears it on role assignment
        $this->assertTrue($user->can('edit', $resource));

        // 3. Revoke Access immediately
        $user->roles()->detach($role);
        $user->unsetRelation('roles'); // Clear Eloquent's internal cache

        $this->assertFalse($user->can('edit', $resource));
    }

    public function test_edge_cases_and_empty_inputs()
    {
        $user = User::factory()->create();

        // 1. Empty Resource
        $this->assertFalse($user->can('view', ''));

        // 2. Null Resource
        $this->assertFalse($user->can('view', null));

        // 3. Action Case Sensitivity (Should be case-insensitive if handled correctly)
        $policy = Policy::create([
            'name' => 'CaseTest',
            'statement' => json_encode(['Statement' => [['Effect' => 'Allow', 'Action' => 'VIEW_REPORT', 'Resource' => '*']]])
        ]);
        $user->assignRole(Role::create(['name' => 'case_tester'])->policies()->attach($policy));

        $this->assertTrue($user->can('view_report', 'arn:emr:any'));

        // 4. Overlapping Wildcards
        // One policy allows clinical:*, another denies clinical:patients:private
        $policy2 = Policy::create([
            'name' => 'DenyPrivate',
            'statement' => json_encode(['Statement' => [['Effect' => 'Deny', 'Action' => '*', 'Resource' => 'arn:emr:clinical:patients:private']]])
        ]);
        $user->assignRole(Role::create(['name' => 'denier'])->policies()->attach($policy2));

        $this->assertFalse($user->can('view', 'arn:emr:clinical:patients:private'), 'Explicit Deny must win over Wildcard Allow');
    }

}
