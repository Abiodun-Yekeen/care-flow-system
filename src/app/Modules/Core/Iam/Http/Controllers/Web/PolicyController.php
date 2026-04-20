<?php

namespace App\Modules\Core\Iam\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Core\Iam\Http\Requests\RoleRequest;
use App\Modules\Core\Iam\Models\Role;
use App\Modules\Core\Iam\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PolicyController extends Controller
{

    public function __construct( Private RoleService $roleService){}

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

        return redirect()->route('role.index');
    }
    public function show(Role $role)
    {
        return Inertia::render('modules/admin/pages/roles/Show',[
            'role' => $role,

        ]);
    }
}
