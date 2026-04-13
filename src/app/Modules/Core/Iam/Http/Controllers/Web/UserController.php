<?php

namespace App\Modules\Core\Iam\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Modules\Core\Iam\DTO\UserDTO;
use App\Modules\Core\Iam\Http\Requests\UserRequest;
use App\Modules\Core\Iam\Http\Requests\UserRequestUpload;
use App\Modules\Core\Iam\Models\User;
use App\Modules\Core\Iam\Services\UserImportService;
use App\Modules\Core\Iam\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        return Inertia::render('modules/admin/pages/users/Index', [
            'users' => $this->userService->listUsers($request->only('search')),
            'filters' => $request->only('search'),
        ]);
    }
    public function create()
    {
        return Inertia::render('modules/admin/pages/users/Create',[]);
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

        // Sync roles (mapping 'role' from form to the roles table)
        if ($dto->role_id) {
            $user->roles()->sync($dto->role_id);
        }

       return redirect()->route('users.index');
    }
    public function show(User $user)
    {
        return Inertia::render('modules/admin/pages/users/Show',[
            'user' => $user->load(['roles:id,name', 'department:id,name']),

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
