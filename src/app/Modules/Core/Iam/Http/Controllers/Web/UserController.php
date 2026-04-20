<?php

namespace App\Modules\Core\Iam\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Modules\Core\Iam\DTO\UserDTO;
use App\Modules\Core\Iam\Http\Requests\UserRequest;
use App\Modules\Core\Iam\Http\Requests\UserRequestUpload;
use App\Modules\Core\Iam\Models\Role;
use App\Modules\Core\Iam\Models\User;
use App\Modules\Core\Iam\Services\EffectiveAccessService;
use App\Modules\Core\Iam\Services\IamAuthorizationService;
use App\Modules\Core\Iam\Services\UserImportService;
use App\Modules\Core\Iam\Services\UserService;
use App\Modules\Organization\Department\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{


    public function __construct(protected UserService $userService,
                                protected EffectiveAccessService $accessService,
                                protected IamAuthorizationService $iamService,
    )
    {}

    public function index(Request $request)
    {
        return Inertia::render('modules/admin/pages/users/Index', [
            'users' => $this->userService->listUsers($request->only('search')),
            'filters' => $request->only('search'),
        ]);
    }
    public function create()
    {
        return Inertia::render('modules/admin/pages/users/Create',[
        ]);
    }
    public function store(UserRequest $request)
    {
        try {
            // The validated data is passed directly to the service
            $dto = UserDTO::fromArray($request->validated());
           $this->userService->createUser($dto);
            return redirect()->route('users.index');
        } catch (\Exception $e) {
            // Log the actual error
            Log::error('Registration Failed: ' . $e->getMessage());
            // Send the user back with a "System Error" flash message
            return redirect()->back()
                ->with('error', 'Database Error: Could not save user. Please contact support.')
                ->withInput();
        }

    }
    public function edit(User $user)
    {
        return Inertia::render('modules/admin/pages/users/Edit',[
            'user' => $user->load(['roles:id,name', 'department:id,name']),
        ]);
    }

    public function update(UserRequest $request, User $user)
    {

        $dto = UserDTO::fromArray($request->validated());
        $this->userService->updateUser($user, $dto);

       return redirect()->route('users.index');
    }
    public function show(User $user)
    {
        return Inertia::render('modules/admin/pages/users/Show',[
            'user' => $user->load([
                'roles:id,name,display_name',
                'roles.policies:id,name',
                'roles.parents:id,name',
                'department:id,name'
            ]),
            'effectiveAccess' => $this->iamService->getPermissionMatrix($user),
            'roles' => Role::all(['id', 'display_name', 'name']),

        ]);

    }

    public function assignRoles(Request $request, User $user)
    {
        $data = $request->validate([
            'role_ids' => ['required', 'array'],
            'role_ids.*' => ['integer', 'exists:roles,id'],
        ]);

        $user->roles()->syncWithoutDetaching($data['role_ids']);

        return back()->with('success', 'Roles assigned successfully.');
    }

    public function removeRole(User $user, Role $role)
    {
        $user->roles()->detach($role->id);

        return back()->with('success', 'Role removed successfully.');
    }

    public function effectiveAccess(User $user)
    {
        return response()->json([
            'data' => $this->accessService->resolveUserMatrix($user),
        ]);
    }

    public function showImport(User $user)
    {
        return Inertia::render('modules/admin/pages/users/ImportData',[]);
    }

    public function uploadUserData(UserRequestUpload $request)
    {
       // Validate that it is actually an excel file
        $request->validated();

        try {
            // Call the service
            $this->userService->importUsers($request->file('excel_file'));

            return back();

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            // the first failure to the 'excel_file' key for the Inertia form
            $error = "Row " . $failures[0]->row() . ": " . $failures[0]->errors()[0];

            return back()->withErrors(['excel_file' => $error]);
        }
        }

}
