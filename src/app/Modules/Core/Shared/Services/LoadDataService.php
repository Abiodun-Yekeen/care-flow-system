<?php

namespace App\Modules\Core\Shared\Services;

use App\Modules\Core\Shared\Repository\Contracts\LoadDataRepositoryInterface;
use App\Modules\Core\Shared\Services\Cache\CacheManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LoadDataService
{
    /**
     * Cache TTL in seconds
     */
    private const CACHE_TTL = 3600;

    /**
     * Cache key for all data
     */
    private const CACHE_KEY_ALL = 'system.metadata';

    public function __construct(
        protected LoadDataRepositoryInterface $repository,
        protected CacheManager $cache
    ) {}

    /**
     * Load all data with caching
     */
    public function loadAllData(): array
    {
        try {
            return $this->cache->remember(self::CACHE_KEY_ALL, self::CACHE_TTL,
                fn() => $this->repository->getAllFormatted()
          );

        } catch (\Throwable $e) {
            Log::error('Failed to load data from cache', [
                'error' => $e->getMessage()
            ]);

            // Fallback to direct repository call
            return $this->repository->getAllFormatted();
        }
    }

    /**
     * Clear the cache (useful after seeding)
     */
    public function invalidateLoadAllData(): bool
    {
        return Cache::forget(self::CACHE_KEY_ALL);
    }

    /**
     * Get specific data type
     */
    public function getDataByType(string $type): mixed
    {
        $method = 'get' . ucfirst($type);

        if (method_exists($this->repository, $method)) {
            return $this->repository->$method();
        }

        return collect();
    }


}
