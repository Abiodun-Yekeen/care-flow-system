<?php

namespace App\Modules\Core\Iam\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IamAuthorizeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ?string $action = null, ?string $resource = null)
    {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        // Fallback action if not provided in middleware params
        if (!$action) {
            $action = $request->route()->getActionMethod(); // e.g., 'index', 'store'
        }

        // Resolve resource from request if not provided
        if (!$resource) {
            $resource = $this->resolveResource($request);
        }

        // Check authorization
        if (!$user->can($action, $resource)) {
            abort(403, "Unauthorized: {$action} on {$resource}");
        }

        return $next($request);
    }
    protected function resolveResource(Request $request): string
    {
        // Try to get from route parameter
        if ($request->route('resource')) {
            return $request->route('resource');
        }

        // Derive from route name
        $routeName = $request->route()->getName();
        if ($routeName) {
            $parts = explode('.', $routeName);
            return $parts[0] ?? 'unknown';
        }

        return 'unknown';
    }
}
