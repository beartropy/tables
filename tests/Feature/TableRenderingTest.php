<?php

use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\BeartropyTable;
use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;

class User extends Model
{
    protected $guarded = [];
}

class UserTable extends BeartropyTable
{
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

class ArrayTable extends BeartropyTable
{
    public function columns()
    {
        return [
            Column::make('Name', 'name'),
            Column::make('Email', 'email'),
        ];
    }

    public function data()
    {
        return [
            ['id' => 1, 'name' => 'Alice Array', 'email' => 'alice@array.com'],
            ['id' => 2, 'name' => 'Bob Array', 'email' => 'bob@array.com'],
        ];
    }

    public function settings() {}
}

class CustomColumnIdTable extends BeartropyTable
{
    public function columns()
    {
        return [
            Column::make('Code', 'code'),
            Column::make('Label', 'label'),
        ];
    }

    public function data()
    {
        return [
            ['code' => 'A1', 'label' => 'Item A'],
            ['code' => 'B2', 'label' => 'Item B'],
        ];
    }

    public function settings()
    {
        $this->setColumnID('code');
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

it('renders array-based table', function () {
    Livewire::test(ArrayTable::class)
        ->assertStatus(200)
        ->assertSee('Alice Array')
        ->assertSee('bob@array.com');
});

it('renders with custom column_id', function () {
    Livewire::test(CustomColumnIdTable::class)
        ->assertStatus(200)
        ->assertSee('Item A')
        ->assertSee('Item B');
});

it('showOnlyTable hides search and pagination', function () {
    $tableClass = new class extends BeartropyTable
    {
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
            $this->showOnlyTable(true);
        }
    };

    Livewire::test($tableClass::class)
        ->assertSet('useGlobalSearch', false)
        ->assertSet('with_pagination', false)
        ->assertSet('show_column_toggle', false);
});
