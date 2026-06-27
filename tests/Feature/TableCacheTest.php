<?php

use Beartropy\Tables\Collections\TableCollection;
use Beartropy\Tables\Traits\Cache as CacheTrait;
use Illuminate\Support\Facades\Cache;

/**
 * Minimal host for the Cache trait so we can exercise cacheData()/getCachedData()
 * without mounting/rendering a full Livewire table.
 */
class CacheTraitHost
{
    use CacheTrait;

    public mixed $userData = null;

    public int $all_data_count = 0;

    public function mount(): void {}

    public function emptySelection(): void {}

    public function cacheKey(): string
    {
        return $this->getCacheKey();
    }
}

it('caches table rows as a plain array, never the TableCollection object', function () {
    // Laravel 13 hardens cache unserialization; caching the object would force
    // every consumer to allow-list TableCollection in config/cache.serializable_classes.
    $host = new CacheTraitHost;
    $host->userData = new TableCollection([
        ['id' => 1, 'name' => 'Alice'],
        ['id' => 2, 'name' => 'Bob'],
    ]);

    $host->cacheData();

    $cached = Cache::get($host->cacheKey());

    expect($cached)->toBeArray()
        ->and($cached)->not->toBeInstanceOf(TableCollection::class)
        ->and($cached[0]['name'])->toBe('Alice');
});

it('re-wraps cached rows into a TableCollection on read', function () {
    $host = new CacheTraitHost;
    $host->userData = new TableCollection([['id' => 1, 'name' => 'Alice']]);
    $host->cacheData();

    $data = $host->getCachedData();

    expect($data)->toBeInstanceOf(TableCollection::class)
        ->and($data->first()['name'])->toBe('Alice');
});

it('updateCacheData also stores a plain array', function () {
    $host = new CacheTraitHost;
    $host->updateCacheData(new TableCollection([['id' => 9, 'name' => 'Zoe']]));

    expect(Cache::get($host->cacheKey()))->toBeArray()
        ->and(Cache::get($host->cacheKey())[0]['name'])->toBe('Zoe');
});
