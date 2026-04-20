<?php

namespace App\Modules\Core\Shared\Http\Middleware;

use App\Modules\Core\Iam\Services\IamAuthorizationService;
use App\Modules\Core\Shared\Services\ModuleService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
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
        $moduleService = app(ModuleService::class);

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
            ],

            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'unread_notifications_count' => $request->user()
                ? $request->user()->unreadNotifications()->count()
                : 0,

            // sidebar, filtered by IAM
            'navigation' => fn () =>
            $request->user()
                ? $moduleService->getSidebarModules($request->user())
                : [],
                'subNavigation' => fn () =>
                $request->user()
                    ? $moduleService->getChildrenModules(
                    $request->user(),
                    $request->segment(1)
                )
                    : [],
            'permissions' => fn () => $request->user()
                ? app(IamAuthorizationService::class)
                    ->getPermissionMatrix($request->user())
                : [],
            'breadcrumb' =>
                $moduleService->buildBreadcrumb($request->path()),
        ]);

    }
}
