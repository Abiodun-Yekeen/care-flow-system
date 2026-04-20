<?php

namespace App\Modules\OfficeFiles\Registry\Providers;


use App\Modules\OfficeFiles\Registry\Repository\Contracts\RegistryInterface;
use App\Modules\OfficeFiles\Registry\Repository\Eloquent\RegistryRepository;
use Illuminate\Support\ServiceProvider;

class RegistryProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {


        $this->app->bind(
            RegistryInterface::class,
            RegistryRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    }
}
