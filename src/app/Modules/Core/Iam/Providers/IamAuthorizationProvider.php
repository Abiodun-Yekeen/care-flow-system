<?php

namespace App\Modules\Core\Iam\Providers;

use App\Modules\Core\Iam\Security\PolicyEvaluator;
use App\Modules\Core\Iam\Services\IamAuthorizationService;
use App\Modules\Core\Iam\Services\PolicyBuilderService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class IamAuthorizationProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(IamAuthorizationService::class, function ($app) {
            return new IamAuthorizationService($app->make(PolicyEvaluator::class));
        });

        $this->app->singleton(PolicyBuilderService::class, function ($app) {
            return new PolicyBuilderService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // This connects $user->can() calls to your custom IAM logic.
        Gate::before(function ($user, $ability, array $arguments) {
            $iam = app(IamAuthorizationService::class);

            $resource = null;
            $context  = [];

            if (is_array($arguments)) {
                $resource = $arguments[0] ?? null;
                $context  = $arguments[1] ?? [];
            }
            return $iam->authorize($user, $ability, $resource, $context);

        });
    }
}
