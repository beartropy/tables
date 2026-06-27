<?php

use Beartropy\Tables\Collections\TableCollection;
use Beartropy\Tables\Traits\Cache as CacheTrait;
use Illuminate\Support\Facades\Cache;

enum CacheRole: string
{
    case Admin = 'admin';
    case Member = 'member';
}

/**
 * Minimal host for the Cache trait so we can exercise cacheData()/getCachedData()
 * without mounting/rendering a full Livewire table. mount() rebuilds the cache
 * from $userData, mirroring how BeartropyTable::mount() repopulates it.
 */
class CacheTraitHost
{
    use CacheTrait;

    public mixed $userData = null;

    public int $all_data_count = 0;

    public function mount(): void
    {
        if ($this->userData !== null) {
            $this->cacheData();
        }
    }

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

it('normalizes object cell values to strings so the cache holds no objects', function () {
    // A Carbon date (or any Stringable) as a cell value would otherwise be cached
    // as an object and come back as __PHP_Incomplete_Class under Laravel 13.
    $date = now();
    $host = new CacheTraitHost;
    $host->userData = new TableCollection([
        ['id' => 1, 'created_at' => $date],
    ]);

    $host->cacheData();

    $cached = Cache::get($host->cacheKey());

    expect($cached[0]['created_at'])->toBe((string) $date)
        ->and($cached[0]['created_at'])->toBeString();
});

it('normalizes backed enum cell values to their scalar value', function () {
    $host = new CacheTraitHost;
    $host->userData = new TableCollection([
        ['id' => 1, 'role' => CacheRole::Admin],
    ]);

    $host->cacheData();

    expect(Cache::get($host->cacheKey())[0]['role'])->toBe('admin');
});

it('normalizes nested arrays of objects within a row', function () {
    $date = now();
    $host = new CacheTraitHost;
    $host->userData = new TableCollection([
        ['id' => 1, 'meta' => ['stamp' => $date, 'role' => CacheRole::Member]],
    ]);

    $host->cacheData();

    $meta = Cache::get($host->cacheKey())[0]['meta'];

    expect($meta['stamp'])->toBe((string) $date)
        ->and($meta['role'])->toBe('member');
});

it('rebuilds from source when the cache holds a non-array (legacy/incomplete) value', function () {
    $host = new CacheTraitHost;
    $host->userData = new TableCollection([['id' => 1, 'name' => 'Alice']]);

    // Simulate a pre-upgrade entry that hardened unserialization would surface
    // as a bare object / __PHP_Incomplete_Class instead of a plain array.
    Cache::put($host->cacheKey(), (object) ['items' => 'garbage'], now()->addMinutes(60));

    $data = $host->getCachedData();

    expect($data)->toBeInstanceOf(TableCollection::class)
        ->and($data->first()['name'])->toBe('Alice');
});
