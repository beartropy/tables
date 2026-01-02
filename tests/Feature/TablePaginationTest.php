<?php

use Beartropy\Tables\YATBaseTable;
use Beartropy\Tables\Classes\Columns\Column;
use Livewire\Livewire;
use Illuminate\Database\Eloquent\Model;

class UserForPagination extends Model
{
    protected $table = 'users';
    protected $guarded = [];
}

class UserTableForPagination extends YATBaseTable
{
    public function settings()
    {
        $this->model = UserForPagination::class;
    }

    public function columns()
    {
        return [
            Column::make('Name'),
            Column::make('Email'),
        ];
    }
}

beforeEach(function () {
    for ($i = 1; $i <= 20; $i++) {
        $name = 'User ' . sprintf('%02d', $i);
        UserForPagination::create(['name' => $name, 'email' => "user$i@example.com"]);
    }
});

it('paginates results by default', function () {
    Livewire::test(UserTableForPagination::class)
        ->assertSee('User 01')
        ->assertSee('User 10')
        ->assertDontSee('User 11');
    // YATBaseTable perPage default is 10.
});

it('can change per page', function () {
    Livewire::test(UserTableForPagination::class)
        ->set('perPage', 20)
        ->assertSee('User 01')
        ->assertSee('User 20');
});

it('can navigate to next page', function () {
    Livewire::test(UserTableForPagination::class)
        ->set('forcePageNumber', 2)
        ->assertSee('User 11')
        ->assertDontSee('User 01');
});
