<?php

use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\BeartropyTable;
use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;

class UserForSearch extends Model
{
    protected $table = 'users';

    protected $guarded = [];
}

class UserTableForSearch extends BeartropyTable
{
    public function settings()
    {
        $this->model = UserForSearch::class;
    }

    public function columns()
    {
        return [
            Column::make('Name')->searchable(),
            Column::make('Email')->searchable(),
        ];
    }
}

class ArrayTableForSearch extends BeartropyTable
{
    public function columns()
    {
        return [
            Column::make('Name', 'name')->searchable(),
            Column::make('Email', 'email')->searchable(),
        ];
    }

    public function data()
    {
        return [
            ['id' => 1, 'name' => 'Alice', 'email' => 'alice@example.com'],
            ['id' => 2, 'name' => 'Bob', 'email' => 'bob@example.com'],
            ['id' => 3, 'name' => 'Charlie', 'email' => 'charlie@example.com'],
        ];
    }

    public function settings() {}
}

class PartialSearchTable extends BeartropyTable
{
    public function settings()
    {
        $this->model = UserForSearch::class;
    }

    public function columns()
    {
        return [
            Column::make('Name')->searchable(),
            Column::make('Email')->searchable(false),
        ];
    }
}

beforeEach(function () {
    UserForSearch::create(['name' => 'Alice', 'email' => 'alice@example.com']);
    UserForSearch::create(['name' => 'Bob', 'email' => 'bob@example.com']);
});

it('can search users', function () {
    Livewire::test(UserTableForSearch::class)
        ->set('yat_global_search', 'Alice')
        ->assertSee('Alice')
        ->assertDontSee('Bob');
});

it('search is case insensitive', function () {
    Livewire::test(UserTableForSearch::class)
        ->set('yat_global_search', 'alice')
        ->assertSee('Alice')
        ->assertDontSee('Bob');
});

it('empty search shows all results', function () {
    Livewire::test(UserTableForSearch::class)
        ->set('yat_global_search', 'Alice')
        ->assertDontSee('Bob')
        ->set('yat_global_search', '')
        ->assertSee('Alice')
        ->assertSee('Bob');
});

it('non-searchable columns are ignored', function () {
    Livewire::test(PartialSearchTable::class)
        ->set('yat_global_search', 'alice@example.com')
        ->assertDontSee('Alice');
});

it('search works on array-mode tables', function () {
    Livewire::test(ArrayTableForSearch::class)
        ->set('yat_global_search', 'Charlie')
        ->assertSee('Charlie')
        ->assertDontSee('Alice')
        ->assertDontSee('Bob');
});

it('search by email column', function () {
    Livewire::test(UserTableForSearch::class)
        ->set('yat_global_search', 'bob@example')
        ->assertSee('Bob')
        ->assertDontSee('Alice');
});
