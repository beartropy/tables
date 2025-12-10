<?php

namespace Beartropy\Tables\Traits;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache as CacheFacade;

trait Cache
{
    public $cachePrefix = '';
    public $cacheTimeStamp;

    public function setCachePrefix(string $string) {
        $this->cachePrefix = $string."_";
    }

    protected function getCacheKey()
    {
        $identifier = Auth::check() ? Auth::user()->username : 'guest_' . session()->getId();
        return $this->cachePrefix . static::class . '\\' . $identifier;
    }

    public function cacheData() {
        $this->all_data_count = count($this->userData);
        if (!CacheFacade::has($this->getCacheKey())) {
            CacheFacade::put($this->getCacheKey(), $this->userData, now()->addMinutes(60));
            $this->cacheTimeStamp = now()->getTimestampMs();
        }
    }

    public function clearData() {
        CacheFacade::forget($this->getCacheKey());
    }

    public function getCachedData() {
        if (!CacheFacade::has($this->getCacheKey())) {
            $this->mount();
        }
        return CacheFacade::get($this->getCacheKey());
    }

    public function updateCacheData($data) {
        $this->all_data_count = count($data);
        $this->emptySelection();
        CacheFacade::put($this->getCacheKey(), $data, now()->addMinutes(60));
        $this->cacheTimeStamp = now()->getTimestampMs();
    }
}
