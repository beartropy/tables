<?php

namespace Beartropy\Tables\Traits;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache as CacheFacade;

trait Cache
{
    /**
     * Prefix for cache keys.
     *
     * @var string
     */
    public $cachePrefix = '';

    /**
     * Timestamp of the cached data.
     *
     * @var int|null
     */
    public $cacheTimeStamp;

    /**
     * Set the cache prefix.
     *
     * @param string $string The prefix string.
     * @return void
     */
    public function setCachePrefix(string $string)
    {
        $this->cachePrefix = $string . "_";
    }

    /**
     * Generate a unique cache key for the current user and table.
     *
     * @return string
     */
    protected function getCacheKey()
    {
        $identifier = Auth::check() ? Auth::user()->username : 'guest_' . session()->getId();
        return $this->cachePrefix . static::class . '\\' . $identifier;
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
        if (!CacheFacade::has($this->getCacheKey())) {
            CacheFacade::put($this->getCacheKey(), $this->userData, now()->addMinutes(60));
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
        if (!CacheFacade::has($this->getCacheKey())) {
            $this->mount();
        }
        return CacheFacade::get($this->getCacheKey());
    }

    /**
     * Update the cached data with new data.
     *
     * Clears current selection and updates the cache with the new data set.
     *
     * @param mixed $data The new data to cache.
     * @return void
     */
    public function updateCacheData($data)
    {
        $this->all_data_count = count($data);
        $this->emptySelection();
        CacheFacade::put($this->getCacheKey(), $data, now()->addMinutes(60));
        $this->cacheTimeStamp = now()->getTimestampMs();
    }
}
