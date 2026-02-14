<?php

use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\YATBaseTable;
use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;

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

class CustomPerPageTable extends YATBaseTable
{
    public function settings()
    {
        $this->model = UserForPagination::class;
        $this->setPerPageDefault(5);
    }

    public function columns()
    {
        return [
            Column::make('Name'),
            Column::make('Email'),
        ];
    }
}

class NoPaginationTable extends YATBaseTable
{
    public function settings()
    {
        $this->model = UserForPagination::class;
        $this->usePagination(false);
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
        $name = 'User '.sprintf('%02d', $i);
        UserForPagination::create(['name' => $name, 'email' => "user$i@example.com"]);
    }
});

it('paginates results by default', function () {
    Livewire::test(UserTableForPagination::class)
        ->assertSee('User 01')
        ->assertSee('User 10')
        ->assertDontSee('User 11');
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

it('Total per page shows all records', function () {
    Livewire::test(UserTableForPagination::class)
        ->set('perPage', PHP_INT_MAX)
        ->assertSee('User 01')
        ->assertSee('User 20');
});

it('setPerPageDefault changes default', function () {
    Livewire::test(CustomPerPageTable::class)
        ->assertSet('perPage', 5)
        ->assertSee('User 01')
        ->assertSee('User 05')
        ->assertDontSee('User 06');
});

it('pagination disabled shows all rows', function () {
    Livewire::test(NoPaginationTable::class)
        ->assertSee('User 01')
        ->assertSee('User 20');
});

it('setting perPageDisplay to Total shows all records', function () {
    Livewire::test(UserTableForPagination::class)
        ->set('perPageDisplay', 'Total')
        ->assertSet('perPage', PHP_INT_MAX)
        ->assertSet('perPageDisplay', 'Total')
        ->assertSee('User 01')
        ->assertSee('User 20');
});
