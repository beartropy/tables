<?php

use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\Classes\Filters\FilterString;
use Beartropy\Tables\Traits\Data;
use Beartropy\Tables\Traits\Filters;
use Beartropy\Tables\BeartropyTable;
use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;

class UserForFilter extends Model
{
    protected $table = 'users';

    protected $guarded = [];
}

class FilteredModelTable extends BeartropyTable
{
    public function settings()
    {
        $this->model = UserForFilter::class;
    }

    public function columns()
    {
        return [
            Column::make('Name')->searchable(),
            Column::make('Email')->searchable(),
        ];
    }

    public function filters()
    {
        return [
            FilterString::make('Name'),
        ];
    }
}

class ArrayStringFilterTable extends BeartropyTable
{
    public function columns()
    {
        return [
            Column::make('Name', 'name'),
            Column::make('Role', 'role'),
        ];
    }

    public function data()
    {
        return [
            ['id' => 1, 'name' => 'Alice', 'role' => 'admin'],
            ['id' => 2, 'name' => 'Bob', 'role' => 'user'],
            ['id' => 3, 'name' => 'Charlie', 'role' => 'admin'],
            ['id' => 4, 'name' => 'Diana', 'role' => 'user'],
        ];
    }

    public function filters()
    {
        return [
            FilterString::make('Name'),
            FilterString::make('Role'),
        ];
    }

    public function settings() {}
}

beforeEach(function () {
    UserForFilter::create(['name' => 'Alice', 'email' => 'alice@example.com']);
    UserForFilter::create(['name' => 'Bob', 'email' => 'bob@example.com']);
    UserForFilter::create(['name' => 'Charlie', 'email' => 'charlie@example.com']);
});

it('has filters available after mount', function () {
    $component = Livewire::test(FilteredModelTable::class);

    expect($component->get('has_filters'))->toBeTrue();
    expect($component->get('filters'))->not->toBeEmpty();
});

it('string filter on model table reduces results', function () {
    $component = Livewire::test(FilteredModelTable::class);

    $filters = $component->get('filters');
    $filterKey = $filters->keys()->first();

    $component->set("filters.{$filterKey}.input", 'Alice')
        ->assertSee('Alice')
        ->assertDontSee('Bob')
        ->assertDontSee('Charlie');
});

it('clearAllFilters resets all inputs', function () {
    $component = Livewire::test(FilteredModelTable::class);

    $filters = $component->get('filters');
    $filterKey = $filters->keys()->first();

    $component
        ->set("filters.{$filterKey}.input", 'Alice')
        ->assertDontSee('Bob')
        ->call('clearAllFilters')
        ->assertSee('Alice')
        ->assertSee('Bob')
        ->assertSee('Charlie');
});

it('string filter on array table reduces results', function () {
    $component = Livewire::test(ArrayStringFilterTable::class);

    $nameFilterKey = $component->get('filters')->search(function ($f) {
        return $f['label'] === 'Name';
    });

    $component
        ->set("filters.{$nameFilterKey}.input", 'Alice')
        ->assertSee('Alice')
        ->assertDontSee('Bob');
});

it('role filter on array table filters matching rows', function () {
    $component = Livewire::test(ArrayStringFilterTable::class);

    $roleFilterKey = $component->get('filters')->search(function ($f) {
        return $f['label'] === 'Role';
    });

    $component
        ->set("filters.{$roleFilterKey}.input", 'admin')
        ->assertSee('Alice')
        ->assertSee('Charlie')
        ->assertDontSee('Bob')
        ->assertDontSee('Diana');
});

it('combined filters work on array table', function () {
    $component = Livewire::test(ArrayStringFilterTable::class);

    $filters = $component->get('filters');
    $nameFilterKey = $filters->search(function ($f) {
        return $f['label'] === 'Name';
    });
    $roleFilterKey = $filters->search(function ($f) {
        return $f['label'] === 'Role';
    });

    $component
        ->set("filters.{$roleFilterKey}.input", 'admin')
        ->set("filters.{$nameFilterKey}.input", 'Alice')
        ->assertSee('Alice')
        ->assertDontSee('Charlie')
        ->assertDontSee('Bob');
});

it('filter state is tracked in filters collection', function () {
    $component = Livewire::test(FilteredModelTable::class);

    $filters = $component->get('filters');
    $filterKey = $filters->keys()->first();

    $component->set("filters.{$filterKey}.input", 'Test Value');

    $updatedFilters = $component->get('filters');
    expect($updatedFilters[$filterKey]['input'])->toBe('Test Value');
});

it('select filter applyFilters logic works correctly', function () {
    // Test the select filter logic at the trait level since the view
    // requires heroicon components not available in the test environment
    $instance = new class
    {
        use \Beartropy\Tables\Traits\Bulk;
        use \Beartropy\Tables\Traits\Cache;
        use \Beartropy\Tables\Traits\Columns;
        use \Beartropy\Tables\Traits\Search;
        use Data, Filters;

        public mixed $model = null;

        public string $custom_column_id = 'id';

        public function columns()
        {
            return [
                Column::make('Name', 'name'),
                Column::make('Role', 'role'),
            ];
        }
    };

    // Simulate filter state as serialized array (as Livewire would store it)
    $instance->has_filters = true;
    $instance->filters = collect([
        'abc123' => [
            'label' => 'Role',
            'type' => 'select',
            'key' => 'role',
            'input' => 'admin',
            'options' => ['admin', 'user'],
            'column' => null,
            'queryCallback' => null,
        ],
    ]);

    $data = collect([
        ['id' => 1, 'name' => 'Alice', 'role' => 'admin'],
        ['id' => 2, 'name' => 'Bob', 'role' => 'user'],
        ['id' => 3, 'name' => 'Charlie', 'role' => 'admin'],
    ]);

    $filtered = $instance->applyFilters($data);

    expect($filtered)->toHaveCount(2);
    $names = $filtered->pluck('name')->toArray();
    expect($names)->toContain('Alice');
    expect($names)->toContain('Charlie');
    expect($names)->not->toContain('Bob');
});

it('bool filter applyFilters logic works correctly', function () {
    $instance = new class
    {
        use \Beartropy\Tables\Traits\Bulk;
        use \Beartropy\Tables\Traits\Cache;
        use \Beartropy\Tables\Traits\Columns;
        use \Beartropy\Tables\Traits\Search;
        use Data, Filters;

        public mixed $model = null;

        public string $custom_column_id = 'id';

        public function columns()
        {
            return [
                Column::make('Name', 'name'),
                Column::make('Active', 'active'),
            ];
        }
    };

    $instance->has_filters = true;
    $instance->filters = collect([
        'xyz789' => [
            'label' => 'Active',
            'type' => 'bool',
            'key' => 'active',
            'input' => 'true',
            'column' => null,
            'queryCallback' => null,
            'compared_with' => ['true' => true, 'false' => false],
        ],
    ]);

    $data = collect([
        ['id' => 1, 'name' => 'Alice', 'active' => true],
        ['id' => 2, 'name' => 'Bob', 'active' => false],
        ['id' => 3, 'name' => 'Charlie', 'active' => true],
    ]);

    $filtered = $instance->applyFilters($data);

    expect($filtered)->toHaveCount(2);
    $names = $filtered->pluck('name')->toArray();
    expect($names)->toContain('Alice');
    expect($names)->not->toContain('Bob');
});

it('bool filter with all shows everything', function () {
    $instance = new class
    {
        use \Beartropy\Tables\Traits\Bulk;
        use \Beartropy\Tables\Traits\Cache;
        use \Beartropy\Tables\Traits\Columns;
        use \Beartropy\Tables\Traits\Search;
        use Data, Filters;

        public mixed $model = null;

        public string $custom_column_id = 'id';

        public function columns()
        {
            return [Column::make('Name', 'name'), Column::make('Active', 'active')];
        }
    };

    $instance->has_filters = true;
    $instance->filters = collect([
        'key1' => [
            'label' => 'Active',
            'type' => 'bool',
            'key' => 'active',
            'input' => 'all',
            'column' => null,
            'queryCallback' => null,
            'compared_with' => ['true' => true, 'false' => false],
        ],
    ]);

    $data = collect([
        ['id' => 1, 'name' => 'Alice', 'active' => true],
        ['id' => 2, 'name' => 'Bob', 'active' => false],
    ]);

    $filtered = $instance->applyFilters($data);

    expect($filtered)->toHaveCount(2);
});
