<?php

use Beartropy\Tables\BeartropyTable;
use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\Classes\Filters\FilterSelectMagic;
use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;

class UserForBaseQuery extends Model
{
    protected $table = 'users';

    protected $guarded = [];
}

// Default table — no query() override, should work exactly as before
class DefaultQueryTable extends BeartropyTable
{
    public function settings()
    {
        $this->model = UserForBaseQuery::class;
    }

    public function columns()
    {
        return [
            Column::make('Name')->searchable(),
            Column::make('Email'),
        ];
    }
}

// Custom query() override — only users with @acme.com email
class ScopedQueryTable extends BeartropyTable
{
    public function settings()
    {
        $this->model = UserForBaseQuery::class;
    }

    public function query(): \Illuminate\Database\Eloquent\Builder
    {
        return UserForBaseQuery::query()
            ->where('email', 'like', '%@acme.com');
    }

    public function columns()
    {
        return [
            Column::make('Name')->searchable(),
            Column::make('Email'),
        ];
    }
}

// Custom query() with $with — verifies eager loading still works
class ScopedQueryWithEagerLoadTable extends BeartropyTable
{
    public function settings()
    {
        $this->model = UserForBaseQuery::class;
        $this->with = [];
    }

    public function query(): \Illuminate\Database\Eloquent\Builder
    {
        return UserForBaseQuery::query()
            ->where('email', 'like', '%@acme.com');
    }

    public function columns()
    {
        return [
            Column::make('Name'),
            Column::make('Email'),
        ];
    }
}

// Custom query() with magic-select filter — filter options should be scoped
class ScopedQueryWithFilterTable extends BeartropyTable
{
    public function settings()
    {
        $this->model = UserForBaseQuery::class;
    }

    public function query(): \Illuminate\Database\Eloquent\Builder
    {
        return UserForBaseQuery::query()
            ->where('email', 'like', '%@acme.com');
    }

    public function columns()
    {
        return [
            Column::make('Name'),
            Column::make('Email'),
        ];
    }

    public function filters()
    {
        return [
            FilterSelectMagic::make('Name'),
        ];
    }
}

// Paginated scoped table
class ScopedPaginatedTable extends BeartropyTable
{
    public function settings()
    {
        $this->model = UserForBaseQuery::class;
        $this->setPerPageDefault(10);
    }

    public function query(): \Illuminate\Database\Eloquent\Builder
    {
        return UserForBaseQuery::query()
            ->where('email', 'like', '%@acme.com');
    }

    public function columns()
    {
        return [
            Column::make('Name')->searchable(),
            Column::make('Email'),
        ];
    }
}

beforeEach(function () {
    UserForBaseQuery::create(['name' => 'Alice', 'email' => 'alice@acme.com']);
    UserForBaseQuery::create(['name' => 'Bob', 'email' => 'bob@other.com']);
    UserForBaseQuery::create(['name' => 'Charlie', 'email' => 'charlie@acme.com']);
    UserForBaseQuery::create(['name' => 'Diana', 'email' => 'diana@other.com']);
});

it('default query returns all records (backward compat)', function () {
    $component = Livewire::test(DefaultQueryTable::class);

    $component
        ->assertSee('Alice')
        ->assertSee('Bob')
        ->assertSee('Charlie')
        ->assertSee('Diana');
});

it('custom query() scopes getAllData', function () {
    $component = Livewire::test(ScopedQueryTable::class);

    $instance = $component->instance();
    $instance->with_pagination = false;

    $allData = $instance->getAllData();

    expect($allData)->toHaveCount(2);
    $names = $allData->pluck('name')->toArray();
    expect($names)->toContain('Alice');
    expect($names)->toContain('Charlie');
    expect($names)->not->toContain('Bob');
    expect($names)->not->toContain('Diana');
});

it('custom query() scopes rendered output (unpaginated)', function () {
    $component = Livewire::test(ScopedQueryTable::class);

    $component
        ->set('with_pagination', false)
        ->assertSee('Alice')
        ->assertSee('Charlie')
        ->assertDontSee('Bob')
        ->assertDontSee('Diana');
});

it('custom query() scopes paginated data', function () {
    $component = Livewire::test(ScopedPaginatedTable::class);

    $component
        ->assertSee('Alice')
        ->assertSee('Charlie')
        ->assertDontSee('Bob')
        ->assertDontSee('Diana');
});

it('custom query() scopes search results', function () {
    $component = Livewire::test(ScopedQueryTable::class);

    $component
        ->set('yat_global_search', 'Alice')
        ->assertSee('Alice')
        ->assertDontSee('Charlie')
        ->assertDontSee('Bob');
});

it('custom query() scopes getSelectedData', function () {
    $component = Livewire::test(ScopedQueryTable::class);
    $instance = $component->instance();

    // Select IDs that include both scoped and out-of-scope users
    $alice = UserForBaseQuery::where('name', 'Alice')->first();
    $bob = UserForBaseQuery::where('name', 'Bob')->first();

    $instance->yat_selected_checkbox = [$alice->id, $bob->id];

    $selected = $instance->getSelectedData();

    // Only Alice should come back (Bob is outside the scope)
    expect($selected)->toHaveCount(1);
    expect($selected->first()['name'])->toBe('Alice');
});

it('custom query() scopes getRowByID', function () {
    $component = Livewire::test(ScopedQueryTable::class);
    $instance = $component->instance();

    $alice = UserForBaseQuery::where('name', 'Alice')->first();
    $bob = UserForBaseQuery::where('name', 'Bob')->first();

    // Alice is in scope
    $row = $instance->getRowByID($alice->id);
    expect($row)->not->toBeNull();
    expect($row['name'])->toBe('Alice');

    // Bob is out of scope
    $row = $instance->getRowByID($bob->id);
    expect($row)->toBeNull();
});

it('magic-select filter options are scoped by custom query', function () {
    $component = Livewire::test(ScopedQueryWithFilterTable::class);

    $filters = $component->get('filters');
    $filter = $filters->first();

    // Options should only contain names from @acme.com users
    // Options may be plain values or {value,label} objects depending on serialization
    $options = collect($filter['options'])->map(function ($opt) {
        return is_array($opt) ? ($opt['label'] ?? $opt['value'] ?? $opt) : $opt;
    })->toArray();
    expect($options)->toContain('Alice');
    expect($options)->toContain('Charlie');
    expect($options)->not->toContain('Bob');
    expect($options)->not->toContain('Diana');
});

it('$with eager loading works alongside custom query', function () {
    // This test verifies that newQuery() applies $with on top of query()
    // We can't easily assert eager loading without a real relationship,
    // but we verify the scoping still works when $with is set
    $component = Livewire::test(ScopedQueryWithEagerLoadTable::class);
    $instance = $component->instance();

    $allData = $instance->getAllData();

    expect($allData)->toHaveCount(2);
    $names = $allData->pluck('name')->toArray();
    expect($names)->toContain('Alice');
    expect($names)->toContain('Charlie');
});
