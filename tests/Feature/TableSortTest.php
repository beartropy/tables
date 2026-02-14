<?php

use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\YATBaseTable;
use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;

class UserForSort extends Model
{
    protected $table = 'users';

    protected $guarded = [];
}

class UserTableForSort extends YATBaseTable
{
    public function settings()
    {
        $this->model = UserForSort::class;
    }

    public function columns()
    {
        return [
            Column::make('Name')->sortable(),
            Column::make('Email'),
        ];
    }
}

class ArrayTableForSort extends YATBaseTable
{
    public function columns()
    {
        return [
            Column::make('Name', 'name')->sortable(),
            Column::make('Email', 'email'),
        ];
    }

    public function data()
    {
        return [
            ['id' => 1, 'name' => 'Charlie', 'email' => 'charlie@example.com'],
            ['id' => 2, 'name' => 'Alice', 'email' => 'alice@example.com'],
            ['id' => 3, 'name' => 'Bob', 'email' => 'bob@example.com'],
        ];
    }

    public function settings() {}
}

beforeEach(function () {
    UserForSort::create(['name' => 'Alice', 'email' => 'alice@example.com']);
    UserForSort::create(['name' => 'Charlie', 'email' => 'charlie@example.com']);
    UserForSort::create(['name' => 'Bob', 'email' => 'bob@example.com']);
});

it('can sort by column', function () {
    expect(UserForSort::count())->toBe(3);

    $component = Livewire::test(UserTableForSort::class)
        ->assertSee('Alice')
        ->call('sortBy', 'name');

    $html = $component->html();

    expect(strpos($html, 'Alice'))->toBeLessThan(strpos($html, 'Bob'));
    expect(strpos($html, 'Bob'))->toBeLessThan(strpos($html, 'Charlie'));

    $component->call('sortBy', 'name'); // Toggle direction

    $html = $component->html();
    expect(strpos($html, 'Charlie'))->toBeLessThan(strpos($html, 'Bob'));
    expect(strpos($html, 'Bob'))->toBeLessThan(strpos($html, 'Alice'));
});

it('sort toggles direction on same column', function () {
    $component = Livewire::test(UserTableForSort::class)
        ->call('sortBy', 'name')
        ->assertSet('sortDirection', 'asc')
        ->call('sortBy', 'name')
        ->assertSet('sortDirection', 'desc')
        ->call('sortBy', 'name')
        ->assertSet('sortDirection', 'asc');
});

it('sort by non-sortable column is ignored', function () {
    // Create a table where email is explicitly non-sortable
    $tableClass = new class extends YATBaseTable
    {
        public function settings()
        {
            $this->model = UserForSort::class;
        }

        public function columns()
        {
            return [
                Column::make('Name')->sortable(),
                Column::make('Email')->sortable(false),
            ];
        }
    };

    Livewire::test($tableClass::class)
        ->call('sortBy', 'email')
        ->assertSet('sortColumn', null);
});

it('sort clears selection', function () {
    Livewire::test(UserTableForSort::class)
        ->set('yat_selected_checkbox', [1, 2])
        ->call('sortBy', 'name')
        ->assertSet('yat_selected_checkbox', []);
});

it('array-mode sort works', function () {
    $component = Livewire::test(ArrayTableForSort::class)
        ->call('sortBy', 'name');

    $html = $component->html();

    expect(strpos($html, 'Alice'))->toBeLessThan(strpos($html, 'Bob'));
    expect(strpos($html, 'Bob'))->toBeLessThan(strpos($html, 'Charlie'));
});
