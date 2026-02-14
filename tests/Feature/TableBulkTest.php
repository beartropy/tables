<?php

use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\YATBaseTable;
use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;

class UserForBulk extends Model
{
    protected $table = 'users';

    protected $guarded = [];
}

class BulkUserTable extends YATBaseTable
{
    public function settings()
    {
        $this->model = UserForBulk::class;
        $this->hasBulk(true);
    }

    public function columns()
    {
        return [
            Column::make('Name'),
            Column::make('Email'),
        ];
    }
}

class NoBulkUserTable extends YATBaseTable
{
    public function settings()
    {
        $this->model = UserForBulk::class;
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
    for ($i = 1; $i <= 15; $i++) {
        UserForBulk::create(['name' => "User $i", 'email' => "user$i@example.com"]);
    }
});

it('has bulk disabled by default', function () {
    Livewire::test(NoBulkUserTable::class)
        ->assertSet('has_bulk', false);
});

it('has bulk enabled when configured', function () {
    Livewire::test(BulkUserTable::class)
        ->assertSet('has_bulk', true);
});

it('selectCurrentPage selects current page IDs', function () {
    $component = Livewire::test(BulkUserTable::class)
        ->call('selectCurrentPage', true);

    $selected = $component->get('yat_selected_checkbox');
    expect($selected)->toHaveCount(10); // Default perPage is 10
});

it('select_all_data selects all IDs across pages', function () {
    $component = Livewire::test(BulkUserTable::class)
        ->call('select_all_data', true);

    $selected = $component->get('yat_selected_checkbox');
    expect($selected)->toHaveCount(15);
});

it('emptySelection clears selection', function () {
    Livewire::test(BulkUserTable::class)
        ->call('selectCurrentPage', true)
        ->call('emptySelection')
        ->assertSet('yat_selected_checkbox', [])
        ->assertSet('selectAll', false);
});

it('getSelectedRows returns correct IDs', function () {
    $component = Livewire::test(BulkUserTable::class);
    $component->set('yat_selected_checkbox', [1, 3, 5]);

    $selected = $component->get('yat_selected_checkbox');
    expect($selected)->toBe([1, 3, 5]);
});

it('setting selectAll to true triggers selectCurrentPage', function () {
    $component = Livewire::test(BulkUserTable::class)
        ->set('selectAll', true);

    $selected = $component->get('yat_selected_checkbox');
    expect($selected)->toHaveCount(10);
    expect($component->get('pageSelected'))->toBeTrue();
});

it('setting selectAll to false clears selection', function () {
    Livewire::test(BulkUserTable::class)
        ->set('selectAll', true)
        ->set('selectAll', false)
        ->assertSet('yat_selected_checkbox', []);
});
