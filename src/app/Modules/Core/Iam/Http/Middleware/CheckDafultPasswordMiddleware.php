<?php

namespace App\Modules\Core\Iam\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckDafultPasswordMiddleware
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
            return redirect()->route('login');
        }

        if ($user->is_default_password && !$request->routeIs('password.reset')) {
            return redirect()->route('password.reset');
        }


        return $next($request);
    }

}
