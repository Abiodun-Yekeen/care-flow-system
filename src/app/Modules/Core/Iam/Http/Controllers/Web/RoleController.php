<?php

namespace App\Modules\Core\Iam\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Core\Iam\Http\Requests\RoleRequest;
use App\Modules\Core\Iam\Models\Policy;
use App\Modules\Core\Iam\Models\Role;
use App\Modules\Core\Iam\Services\EffectiveAccessService;
use App\Modules\Core\Iam\Services\IamAuthorizationService;
use App\Modules\Core\Iam\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class RoleController extends Controller
{

    public function __construct( Private RoleService $roleService, private EffectiveAccessService $accessService){}

    public function index(Request $request)
    {
        return Inertia::render('modules/admin/pages/roles/Index', [
           'roles' => $this->roleService->listRoles($request->only('search')),
            'filters' => $request->only('search'),
        ]);
    }
    public function create()
    {
        return Inertia::render('modules/admin/pages/roles/Create',[]);
    }
    public function store(RoleRequest $request)
    {
        try {
            $data = $request->validated();
            $result = $this->roleService->createRole($data);
            return redirect()->route('roles.index');
        }catch (\Exception $exception){
            Log::error('Registration Failed: ' . $e->getMessage());
            // Send the user back with a "System Error" flash message
            return redirect()->back()
                ->with('error', 'Database Error: Could not save Role. Please contact support.')
                ->withInput();
        }

    }

    public function edit(Role $role)
    {
        return Inertia::render('modules/admin/pages/roles/Edit',[
            'role' => $role,
        ]);
    }

    public function update(RoleRequest $request, Role $role)
    {

        $data =$request->validated();
        $this->roleService->updateRole($role, $data);

        return redirect()->route('roles.index');
    }
    public function show(Role $role)
    {
        $role->load([
            'policies:id,name,description',
            'parents:id,name',
            'children:id,name',
            'users:id,name,email',
        ]);

        return Inertia::render('modules/admin/pages/roles/Show', [
            'role' => $role,
            'allRoles' => Role::where('id', '!=', $role->id)
                ->select('id', 'name')
                ->orderBy('name')
                ->get(),
            'allPolicies' => Policy::select('id', 'name')->get(),
            // Add the matrix calculation for the table
            'effectiveAccess' => $this->accessService->resolveRoleMatrix($role),
        ]);
    }

    public function attachPolicies(Request $request, Role $role)
    {
        $data = $request->validate([
            'policy_ids' => ['required', 'array'],
            'policy_ids.*' => ['integer', 'exists:policies,id'],
        ]);

        $role->policies()->syncWithoutDetaching($data['policy_ids']);
        $this->invalidateRoleUsers($role);

        return back()->with('success', 'Policies attached successfully.');
    }

    public function detachPolicy(Role $role, Policy $policy)
    {
        $role->policies()->detach($policy->id);
        $this->invalidateRoleUsers($role);
        return back()->with('success', 'Policy detached successfully.');
    }

    public function attachParents(Request $request, Role $role)
    {
        $data = $request->validate([
            'parent_ids' => ['required', 'array'],
            'parent_ids.*' => ['integer', 'exists:roles,id'],
        ]);

        $role->parents()->syncWithoutDetaching($data['parent_ids']);

        return back()->with('success', 'Parent roles attached successfully.');
    }

    public function detachParent(Role $role, Role $parent)
    {
        $role->parents()->detach($parent->id);

        return back()->with('success', 'Parent role detached successfully.');
    }

    public function effectiveAccess(Role $role)
    {
        return response()->json([
            'data' => $this->accessService->resolveRoleMatrix($role),
        ]);
    }

    protected function invalidateRoleUsers(Role $role): void
    {
        $userIds = $role->users()->pluck('id');

        foreach ($userIds as $userId) {
            app(IamAuthorizationService::class)
                ->forgetUserSnapshot($userId);
        }
    }
}
