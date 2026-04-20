<?php

namespace App\Modules\Core\Iam\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Core\Iam\Http\Requests\RoleRequest;
use App\Modules\Core\Iam\Models\Policy;
use App\Modules\Core\Iam\Models\Role;
use App\Modules\Core\Iam\Services\EffectiveAccessService;
use App\Modules\Core\Iam\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PolicyController extends Controller
{

    public function __construct(
        protected EffectiveAccessService $accessService
    ) {}

    public function index()
    {
        $policies = Policy::withCount('roles')
            ->latest()
            ->paginate(15);

        return Inertia::render('modules/admin/pages/policy/Index', [
            'policies' => $policies,
        ]);
    }

    public function show(Policy $policy)
    {
        $policy->load('roles:id,name');

        return Inertia::render('modules/admin/pages/policy/Show', [
            'policy' => $policy,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:policies,name'],
            'description' => ['nullable', 'string'],
            'document' => ['required', 'array'],
            'document.version' => ['nullable', 'string'],
            'document.statements' => ['required', 'array', 'min:1'],
        ]);

        Policy::create($data);

        return redirect()->route('admin.iam.policies.index')
            ->with('success', 'Policy created successfully.');
    }

    public function update(Request $request, Policy $policy)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', "unique:policies,name,{$policy->id}"],
            'description' => ['nullable', 'string'],
            'document' => ['required', 'array'],
            'document.version' => ['nullable', 'string'],
            'document.statements' => ['required', 'array', 'min:1'],
        ]);

        $policy->update($data);

        return back()->with('success', 'Policy updated successfully.');
    }

    public function simulate(Request $request, Policy $policy)
    {
        $data = $request->validate([
            'action' => ['required', 'string'],
            'resource' => ['required', 'string'],
        ]);

        $result = $this->accessService->simulateForPolicies(
            collect([$policy]),
            $data['action'],
            $data['resource']
        );

        return response()->json($result);
    }
}
