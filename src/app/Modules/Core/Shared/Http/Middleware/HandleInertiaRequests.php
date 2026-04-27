<?php

namespace App\Modules\Core\Shared\Http\Middleware;

use App\Modules\Core\Iam\Services\IamAuthorizationService;
use App\Modules\Core\Shared\Services\Cache\CacheManager;
use App\Modules\Core\Shared\Services\ModuleService;
use App\Modules\Core\Shared\Services\NavigationService;
use App\Modules\Core\Shared\Services\PermissionCompilerService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    public function __construct(
        private CacheManager $cache,
        private NavigationService $navigationService,
        private PermissionCompilerService $CompilerService
    )

    {}
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
 $user = $request->user();
        return array_merge(parent::share($request), [
            'auth' => [
                'user' =>$user,
            ],

            'flash' => [
                'success' => fn() => $request->session()->get('success'),
                'error' => fn() => $request->session()->get('error'),
            ],
            'unread_notifications_count' => $user
                ? $user->unreadNotifications()->count()
                : 0,

            // sidebar, filtered by IAM
            'navigation' => fn() => $user
                ? $this->navigationService->sidebar($user)
                : [],

            'permissions' => fn() => $user
                ? ($this->cache->get("iam:snapshot:user:{$user->id}") ?? $this->CompilerService->compile($user))
                : [],
            'breadcrumb' =>
                $this->navigationService->buildBreadcrumb($request->path()),

        ]);

    }
}

