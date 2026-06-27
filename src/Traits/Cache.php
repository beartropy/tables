<?php

namespace Beartropy\Tables\Traits;

use Beartropy\Tables\Collections\TableCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache as CacheFacade;

trait Cache
{
    /**
     * Prefix for cache keys.
     */
    public string $cachePrefix = '';

    /**
     * Timestamp of the cached data.
     */
    public ?int $cacheTimeStamp = null;

    /**
     * Set the cache prefix.
     *
     * @param  string  $string  The prefix string.
     * @return void
     */
    public function setCachePrefix(string $string)
    {
        $this->cachePrefix = $string.'_';
    }

    /**
     * Generate a unique cache key for the current user and table.
     *
     * @return string
     */
    protected function getCacheKey()
    {
        $identifier = Auth::check() ? Auth::user()->username : 'guest_'.session()->getId();

        return $this->cachePrefix.static::class.'\\'.$identifier;
    }

    /**
     * Cache the current user data.
     *
     * Stores the data in the cache for 60 minutes and updates the timestamp.
     *
     * @return void
     */
    public function cacheData()
    {
        $this->all_data_count = count($this->userData);
        if (! CacheFacade::has($this->getCacheKey())) {
            CacheFacade::put($this->getCacheKey(), $this->cacheableRows($this->userData), now()->addMinutes(60));
            $this->cacheTimeStamp = now()->getTimestampMs();
        }
    }

    /**
     * Clear the cached data for this table.
     *
     * @return void
     */
    public function clearData()
    {
        CacheFacade::forget($this->getCacheKey());
    }

    /**
     * Retrieve cached data.
     *
     * If cache is missing, it attempts to re-initialize the component (mount).
     *
     * @return mixed The cached data.
     */
    public function getCachedData()
    {
        if (! CacheFacade::has($this->getCacheKey())) {
            $this->mount();
        }

        $cached = CacheFacade::get($this->getCacheKey());

        // Rows are cached as plain arrays (see cacheableRows). Re-wrap them in a
        // TableCollection so callers keep the Collection API. A legacy cached
        // Collection is tolerated for the TTL window right after upgrading.
        $rows = $cached instanceof Collection ? $cached->all() : (array) ($cached ?? []);

        return new TableCollection($rows);
    }

    /**
     * Update the cached data with new data.
     *
     * Clears current selection and updates the cache with the new data set.
     *
     * @param  mixed  $data  The new data to cache.
     * @return void
     */
    public function updateCacheData($data)
    {
        $this->all_data_count = count($data);
        $this->emptySelection();
        CacheFacade::put($this->getCacheKey(), $this->cacheableRows($data), now()->addMinutes(60));
        $this->cacheTimeStamp = now()->getTimestampMs();
    }

    /**
     * Normalize table data to a plain array of rows for caching.
     *
     * The table data is only ever cached as an array of row arrays, never as
     * the TableCollection object itself. This keeps the cache compatible with
     * Laravel 13's hardened unserialization (config cache.serializable_classes)
     * without consumers having to allow-list TableCollection.
     *
     * @param  mixed  $data
     * @return array<int, mixed>
     */
    protected function cacheableRows($data): array
    {
        return $data instanceof Collection ? $data->all() : (array) ($data ?? []);
    }
}
