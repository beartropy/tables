<?php

use Beartropy\Tables\YATBaseTable;
use Beartropy\Tables\Classes\Columns\Column;
use Livewire\Livewire;
use Illuminate\Database\Eloquent\Model;

class UserForSearch extends Model
{
    protected $table = 'users';
    protected $guarded = [];
}

class UserTableForSearch extends YATBaseTable
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
