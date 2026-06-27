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

        // Rows are cached as a plain array of plain-array rows (see cacheableRows),
        // so a healthy hit is always an array. Anything else — a legacy cached
        // TableCollection object, or a __PHP_Incomplete_Class produced by Laravel
        // 13's hardened cache unserialization for a pre-upgrade entry — is unusable
        // and would blow up rendering (htmlspecialchars on an incomplete class).
        // Drop it and rebuild from source so the table self-heals.
        if (! is_array($cached)) {
            $this->clearData();
            $this->mount();
            $cached = CacheFacade::get($this->getCacheKey());
        }

        return new TableCollection(is_array($cached) ? $cached : []);
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
     * Normalize table data to a plain array of plain-array rows for caching.
     *
     * The cache must contain ONLY scalars and arrays — never objects. Laravel
     * 13 hardens cache unserialization (config cache.serializable_classes), so
     * any object stored in the cache (the TableCollection itself, or an object
     * cell value such as a Carbon date, enum, or value object) comes back as a
     * __PHP_Incomplete_Class and breaks rendering. Deep-normalizing here keeps
     * the cache compatible without consumers having to allow-list any class.
     *
     * @param  mixed  $data
     * @return array<int, mixed>
     */
    protected function cacheableRows($data): array
    {
        $rows = $data instanceof Collection ? $data->all() : (array) ($data ?? []);

        return array_map(fn ($row) => $this->normalizeRowForCache($row), $rows);
    }

    /**
     * Reduce a single row to a plain array, then normalize each of its values.
     *
     * @param  mixed  $row
     * @return array<string, mixed>
     */
    protected function normalizeRowForCache($row): array
    {
        if ($row instanceof Collection) {
            $row = $row->all();
        } elseif (is_object($row)) {
            $row = method_exists($row, 'toArray') ? $row->toArray() : (array) $row;
        }

        return array_map(fn ($value) => $this->normalizeValueForCache($value), (array) $row);
    }

    /**
     * Recursively convert a cell value to a cache-safe (object-free) form.
     *
     * Stringable objects (e.g. Carbon) become their string representation —
     * exactly what the Blade view would render — so output is preserved.
     *
     * @param  mixed  $value
     * @return mixed
     */
    protected function normalizeValueForCache($value)
    {
        if (is_array($value)) {
            return array_map(fn ($v) => $this->normalizeValueForCache($v), $value);
        }

        if ($value instanceof \BackedEnum) {
            return $value->value;
        }

        if ($value instanceof \UnitEnum) {
            return $value->name;
        }

        if (is_object($value)) {
            if (method_exists($value, '__toString')) {
                return (string) $value;
            }

            if ($value instanceof \JsonSerializable) {
                return $this->normalizeValueForCache($value->jsonSerialize());
            }

            if (method_exists($value, 'toArray')) {
                return $this->normalizeValueForCache($value->toArray());
            }

            return $this->normalizeValueForCache((array) $value);
        }

        return $value;
    }
}
