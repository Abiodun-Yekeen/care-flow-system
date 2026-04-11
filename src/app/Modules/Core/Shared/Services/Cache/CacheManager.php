<?php

namespace App\Modules\Core\Shared\Services\Cache;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * CacheManager - A robust cache management class with automatic failover
 *
 * This class provides a unified interface for caching operations with
 * automatic fallback from Redis to database cache when Redis is unavailable.
 * It ensures application stability by gracefully handling Redis connection issues.
 */
class CacheManager
{
    /**
     * Cached Redis availability status
     *
     * @var bool|null
     *
     * This property caches the Redis availability check result to avoid
     * repeatedly attempting to connect to Redis on every operation.
     * null = not yet checked, true = available, false = unavailable
     */
    protected $redisAvailable = null;

    /**
     * Check if Redis is available and cache the result
     *
     * Attempts to ping Redis to verify connectivity. If successful, caches
     * and returns true. If failed, logs a warning and caches false.
     * This prevents repeated connection attempts on subsequent calls.
     *
     * @return bool True if Redis is available, false otherwise
     */
    protected function redisAvailable(): bool
    {
        // Return cached result if we've already checked Redis availability
        if ($this->redisAvailable !== null) {
            return $this->redisAvailable;
        }

        try {
            // Attempt to ping Redis to verify connection
            Redis::connection()->ping();
            return $this->redisAvailable = true;
        } catch (Throwable $e) {
            // Log the Redis failure for monitoring purposes
            Log::warning('Redis unavailable. Using database cache.', [
                'error' => $e->getMessage()
            ]);

            return $this->redisAvailable = false;
        }
    }

    /**
     * Get the appropriate cache store based on Redis availability
     *
     * Automatically selects the best available cache store:
     * - Redis store if Redis is available (for better performance)
     * - Database store as fallback when Redis is down
     *
     * @return \Illuminate\Contracts\Cache\Repository The selected cache store
     */
    protected function store()
    {
        return $this->redisAvailable()
            ? Cache::store('redis')
            : Cache::store('database');
    }

    /**
     * Retrieve an item from the cache
     *
     * @param string $key The cache key to retrieve
     * @param mixed $default Default value to return if key doesn't exist
     * @return mixed The cached value or the default value
     */
    public function get(string $key, mixed $default = null)
    {
        return $this->store()->get($key, $default);
    }

    /**
     * Store an item in the cache
     *
     * @param string $key The cache key
     * @param mixed $value The value to store
     * @param int $ttl Time to live in seconds (default: 3600 seconds / 1 hour)
     * @return bool True on success, false on failure
     */
    public function put(string $key, mixed $value, int $ttl = 3600)
    {
        return $this->store()->put($key, $value, $ttl);
    }

    /**
     * Get an item from cache or store the default value
     *
     * If the item exists in the cache, it returns the cached value.
     * If not, it executes the callback, stores the result in cache,
     * and returns the result.
     *
     * @param string $key The cache key
     * @param int $ttl Time to live in seconds
     * @param Closure $callback Callback that generates the value to cache
     * @return mixed The cached or generated value
     */
    public function remember(string $key, int $ttl, Closure $callback)
    {
        return $this->store()->remember($key, $ttl, $callback);
    }

    /**
     * Get an item from cache or store it permanently
     *
     * Similar to remember(), but stores the item permanently in the cache
     * (or until explicitly forgotten).
     *
     * @param string $key The cache key
     * @param Closure $callback Callback that generates the value to cache
     * @return mixed The cached or generated value
     */
    public function rememberForever(string $key, Closure $callback)
    {
        return $this->store()->rememberForever($key, $callback);
    }

    /**
     * Remove an item from all cache stores
     *
     * Attempts to remove the specified key from both Redis and database
     * cache stores. This ensures cache consistency when switching between
     * stores or during cache invalidation.
     *
     * The method silently ignores errors to prevent cache clearance issues
     * from affecting application flow.
     *
     * @param string $key The cache key to remove
     * @return bool Always returns true to indicate the operation was attempted
     */
    public function forget(string $key)
    {
        // Attempt to remove from Redis (ignore failures)
        try {
            Cache::store('redis')->forget($key);
        } catch (Throwable $e) {
            // Silently ignore Redis failures during cache clear
            // This prevents a single store failure from blocking the entire operation
        }

        // Attempt to remove from database (ignore failures)
        try {
            Cache::store('database')->forget($key);
        } catch (Throwable $e) {
            // Silently ignore database failures during cache clear
        }

        return true;
    }
}
