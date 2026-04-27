<?php

namespace App\Modules\Core\Iam\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IamAuthorizeMiddleware
{
    public function __construct(
        protected IamAuthorizationService $iam
    ) {}

    public function handle(
        Request $request,
        Closure $next,
        ?string $action = null,
        ?string $resource = null
    ) {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        // Fallback action (map controller → IAM action)
        if (!$action) {
            $action = $this->mapAction(
                $request->route()->getActionMethod()
            );
        }

        // Resolve resource
        if (!$resource) {
            $resource = $this->resolveResource($request);
        }

        // ✅ USE IAM (snapshot-powered)
        if (!$this->iam->can($user, $action, $resource)) {
            abort(403, "Unauthorized: {$action} on {$resource}");
        }

        return $next($request);
    }

    protected function resolveResource(Request $request): string
    {
        // Priority 1: explicit route param
        if ($request->route('resource')) {
            return $request->route('resource');
        }

        // Priority 2: route name (recommended)
        if ($routeName = $request->route()->getName()) {
            return explode('.', $routeName)[0];
        }

        return 'unknown';
    }

    /**
     * Map Laravel controller methods → IAM actions
     */
    protected function mapAction(string $method): string
    {
        return match ($method) {
            'index'   => 'list',
            'show'    => 'view',
            'store'   => 'create',
            'update'  => 'update',
            'destroy' => 'delete',
            default   => $method, // fallback (e.g., 'export', 'submit')
        };
    }
}
