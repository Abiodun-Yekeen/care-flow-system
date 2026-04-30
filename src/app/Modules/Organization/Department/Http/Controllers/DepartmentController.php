<?php
namespace App\Modules\Organization\Department\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Organization\Department\Models\Department;
use App\Modules\Organization\Department\Services\DepartmentService;
use Illuminate\Http\JsonResponse;

class DepartmentController extends Controller
{
    public function __construct(
        protected DepartmentService $departmentService
    ) {}

    /**
     * Endpoint for the dynamic Staff dropdown.
     */
    public function getStaff(Department $department): JsonResponse
    {
        $staff = $this->departmentService->getStaff($department);

        return response()->json($staff);
    }

}
