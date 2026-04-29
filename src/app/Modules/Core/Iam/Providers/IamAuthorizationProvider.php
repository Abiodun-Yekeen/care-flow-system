<?php

namespace App\Modules\Core\Iam\Providers;

use App\Modules\Core\Iam\Models\User;
use App\Modules\Core\Iam\Observers\UserObserver;
use App\Modules\Core\Iam\Repository\Contracts\UserRepositoryInterface;
use App\Modules\Core\Iam\Repository\Eloquent\UserRepository;
use App\Modules\Core\Iam\Security\PolicyEvaluator;
use App\Modules\Core\Iam\Services\IamAuthorizationService;
use App\Modules\Core\Iam\Services\PolicyBuilderService;
use App\Modules\Core\Shared\Services\Cache\CacheManager;
use Illuminate\Support\Facades\Gate;
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
            return new IamAuthorizationService($app->make(PolicyEvaluator::class),$app->make(CacheManager::class));
        });

        $this->app->singleton(PolicyBuilderService::class, function ($app) {
            return new PolicyBuilderService();
        });

        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        User::observe(UserObserver::class);

        Gate::before(function ($user, $ability, ...$arguments) {

            $resource = $arguments[0] ?? null;
            $context  = $arguments[1] ?? [];

            return app(IamAuthorizationService::class)
                ->can($user, $ability, $resource, $context)
                ? true
                : null;
        });
    }
}
