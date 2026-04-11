<?php

namespace App\Modules\Core\Shared\Providers;

use App\Modules\Core\Shared\Repository\Contracts\LoadDataRepositoryInterface;
use App\Modules\Core\Shared\Repository\Contracts\ModuleRepositoryInterface;
use App\Modules\Core\Shared\Repository\Eloquent\EloquentLoadDataRepository;
use App\Modules\Core\Shared\Repository\Eloquent\EloquentModuleRepository;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use App\Modules\Core\Shared\Services\Cache\CacheManager;

class AppProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            ModuleRepositoryInterface::class,
            EloquentModuleRepository::class,
        );
        $this->app->bind(LoadDataRepositoryInterface::class,
            EloquentLoadDataRepository::class
        );

        $this->app->singleton(CacheManager::class, function () {
            return new CacheManager();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadCustomMigrationsRoute();
    }

    private function loadCustomMigrationsRoute(): void
    {
        $modulePath = app_path('Modules');

        if (is_dir($modulePath)) {
            $directory = new \RecursiveDirectoryIterator($modulePath, \RecursiveDirectoryIterator::SKIP_DOTS);
            // SELF_FIRST is the magic flag that lets us see the directories
            $iterator = new \RecursiveIteratorIterator($directory, \RecursiveIteratorIterator::SELF_FIRST);

            foreach ($iterator as $info) {
                $path = $info->getRealPath();

                //  Register Migrations (Look for the directory)
                if ($info->isDir() && str_ends_with($path, 'Database/Migrations')) {
                    $this->loadMigrationsFrom($path);
                }

                $filename = $info->getFilename();

                // Load web routes with web middleware
                if ($filename === 'web.php') {
                    Route::middleware('web')
                        ->group(function() use ($path) {
                            $this->loadRoutesFrom($path);
                        });
                }

                // Load api routes with api middleware and optional prefix
                if ($filename === 'api.php') {
                    Route::prefix('api')
                        ->middleware('api')
                        ->group(function() use ($path) {
                            $this->loadRoutesFrom($path);
                        });
                }
            }
        }
    }
}
