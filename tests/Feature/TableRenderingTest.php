<?php

use Beartropy\Tables\YATBaseTable;
use Beartropy\Tables\Classes\Columns\Column;
use Livewire\Livewire;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $guarded = [];
}

class UserTable extends YATBaseTable
{
    public function query()
    {
        return User::query();
    }

    public function columns()
    {
        return [
            Column::make('Name'),
            Column::make('Email'),
        ];
    }

    public function settings()
    {
        $this->model = User::class;
    }
}

beforeEach(function () {
    User::create(['name' => 'John Doe', 'email' => 'john@example.com']);
    User::create(['name' => 'Jane Doe', 'email' => 'jane@example.com']);
});

it('can render the table component', function () {
    Livewire::test(UserTable::class)
        ->assertStatus(200)
        ->assertSee('John Doe')
        ->assertSee('jane@example.com');
});

it('renders columns correctly', function () {
    Livewire::test(UserTable::class)
        ->assertSee('Name')
        ->assertSee('Email');
});
