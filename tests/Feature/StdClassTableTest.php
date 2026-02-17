<?php

use Beartropy\Tables\BeartropyTable;
use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\Classes\Columns\LinkColumn;
use Livewire\Livewire;

class StdClassArrayTable extends BeartropyTable
{
    public function columns()
    {
        return [
            Column::make('Name', 'name')->searchable()->sortable(),
            Column::make('Email', 'email')->searchable(),
        ];
    }

    public function data()
    {
        return json_decode('[
            {"id": 1, "name": "Alice", "email": "alice@example.com"},
            {"id": 2, "name": "Bob", "email": "bob@example.com"},
            {"id": 3, "name": "Charlie", "email": "charlie@example.com"}
        ]');
    }

    public function settings() {}
}

class StdClassCustomDataTable extends BeartropyTable
{
    public function columns()
    {
        return [
            Column::make('Name', 'name')->customData(function ($row) {
                return strtoupper($row->name);
            }),
            Column::make('Email', 'email'),
        ];
    }

    public function data()
    {
        return json_decode('[
            {"id": 1, "name": "Alice", "email": "alice@example.com"},
            {"id": 2, "name": "Bob", "email": "bob@example.com"}
        ]');
    }

    public function settings() {}
}

class StdClassLinkTable extends BeartropyTable
{
    public function columns()
    {
        return [
            Column::make('Name', 'name'),
            LinkColumn::make('Profile', 'profile_url')->href(function ($row) {
                return '/users/'.$row->id;
            }),
        ];
    }

    public function data()
    {
        return json_decode('[
            {"id": 1, "name": "Alice", "profile_url": "/old"},
            {"id": 2, "name": "Bob", "profile_url": "/old"}
        ]');
    }

    public function settings() {}
}

class StdClassCardTitleTable extends BeartropyTable
{
    public function columns()
    {
        return [
            Column::make('Name', 'name')->cardTitle(function ($row) {
                return 'User: '.$row->name;
            }),
            Column::make('Email', 'email'),
        ];
    }

    public function data()
    {
        return json_decode('[
            {"id": 1, "name": "Alice", "email": "alice@example.com"}
        ]');
    }

    public function settings() {}
}

it('renders stdClass data without errors', function () {
    Livewire::test(StdClassArrayTable::class)
        ->assertSee('Alice')
        ->assertSee('Bob')
        ->assertSee('Charlie');
});

it('search works on stdClass data', function () {
    Livewire::test(StdClassArrayTable::class)
        ->set('yat_global_search', 'Bob')
        ->assertSee('Bob')
        ->assertDontSee('Alice')
        ->assertDontSee('Charlie');
});

it('sort works on stdClass data', function () {
    $component = Livewire::test(StdClassArrayTable::class)
        ->call('sortBy', 'name');

    $data = $component->instance()->getCurrentPageData();
    $names = $data->pluck('name')->values()->all();

    expect($names)->toBe(['Alice', 'Bob', 'Charlie']);
});

it('customData callback receives row with object access on stdClass data', function () {
    Livewire::test(StdClassCustomDataTable::class)
        ->assertSee('ALICE')
        ->assertSee('BOB');
});

it('href callback receives row with object access on stdClass data', function () {
    Livewire::test(StdClassLinkTable::class)
        ->assertSee('Alice')
        ->assertSee('Bob');
});

it('cardTitle callback receives row with object access on stdClass data', function () {
    Livewire::test(StdClassCardTitleTable::class)
        ->assertSee('Alice');
});

it('addRowToTable works with stdClass row', function () {
    Livewire::test(StdClassArrayTable::class)
        ->call('addRowToTable', json_decode('{"id": 4, "name": "Diana", "email": "diana@example.com"}'))
        ->assertSee('Diana');
});

it('removeRowFromTable works on stdClass-origin data', function () {
    Livewire::test(StdClassArrayTable::class)
        ->assertSee('Alice')
        ->call('removeRowFromTable', 1)
        ->assertDontSee('Alice')
        ->assertSee('Bob');
});

it('updateRowOnTable works on stdClass-origin data', function () {
    Livewire::test(StdClassArrayTable::class)
        ->call('updateRowOnTable', 1, ['name' => 'Alice Updated'])
        ->assertSee('Alice Updated');
});
