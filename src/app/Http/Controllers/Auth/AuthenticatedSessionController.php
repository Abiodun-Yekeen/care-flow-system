<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Modules\Core\Iam\Services\IamAuthorizationService;
use App\Modules\Core\Shared\Services\PermissionCompilerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    public function __construct(Private IamAuthorizationService $iamService)
    {

    }
    /**
     * Display the login view.
     */
    public function create(Request $request): Response
    {

        if ($request->has('expired')) {
           //Invalidate the old session
            $request->session()->invalidate();
            //Wipe the CSRF token and make a new one
            $request->session()->regenerateToken();
        }

        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();
        $user = auth()->user();
        app(IamAuthorizationService::class)->buildSnapshot($user);
//


audit()->log([
            'category' => 'SYSTEM',
            'action' => 'login',
            'resource_type' => 'users',
            'resource_id' => $user->id,
            'metadata' => [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => $request->session()->getId(),
            ],
        ]);


        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
