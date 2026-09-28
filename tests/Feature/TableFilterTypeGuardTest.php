<?php

use Beartropy\Tables\BeartropyTable;
use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\Classes\Filters\Filter;
use Livewire\Livewire;

/**
 * A filter subclass that intentionally does NOT declare a $type property,
 * mirroring the base Filter class. Before the guard in setFilters(), such a
 * filter would be serialized without a 'type' key and blow up the filters
 * view with "Undefined array key 'type'".
 */
class TypelessFilter extends Filter
{
    public static function make(string $label, ?string $index = null): Filter
    {
        return new static($label, $index);
    }
}

class TypelessFilterTable extends BeartropyTable
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
        ];
    }

    public function filters()
    {
        return [
            TypelessFilter::make('Name'),
        ];
    }

    public function settings() {}
}

it('setFilters guarantees a type key even for filters without a declared type', function () {
    $component = Livewire::test(TypelessFilterTable::class);

    $filters = $component->get('filters');

    expect($filters)->not->toBeEmpty();

    foreach ($filters as $filter) {
        expect($filter)->toHaveKey('type');
        expect($filter['type'])->not->toBeNull();
    }
});

it('renders the filters view without error when a filter lacks a declared type', function () {
    Livewire::test(TypelessFilterTable::class)
        ->assertOk()
        ->assertSee('Alice')
        ->assertSee('Bob');
});
