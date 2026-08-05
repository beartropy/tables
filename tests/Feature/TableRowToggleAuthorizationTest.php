<?php

use Beartropy\Tables\BeartropyTable;
use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\Classes\Columns\ToggleColumn;
use Livewire\Livewire;

class ToggleAuthorizationTable extends BeartropyTable
{
    public $denyToggle = false;

    public function columns()
    {
        return [
            Column::make('Name', 'name'),
            ToggleColumn::make('Active', 'active'),
        ];
    }

    public function authorizeToggle($id, string $column): bool
    {
        return ! $this->denyToggle;
    }

    public function data()
    {
        return [
            ['id' => 1, 'name' => 'Ada', 'active' => true],
        ];
    }

    public function settings() {}
}

it('a denied toggle leaves the value unchanged', function () {
    $component = Livewire::test(ToggleAuthorizationTable::class)
        ->set('denyToggle', true)
        ->call('toggleBoolean', 1, 'active');

    expect($component->instance()->getRowByID(1)['active'])->toBeTrue();
});

it('an allowed toggle flips the value', function () {
    $component = Livewire::test(ToggleAuthorizationTable::class)
        ->call('toggleBoolean', 1, 'active');

    expect($component->instance()->getRowByID(1)['active'])->toBeFalse();
});

it('a column that is not part of the table cannot be toggled', function () {
    $component = Livewire::test(ToggleAuthorizationTable::class)
        ->call('toggleBoolean', 1, 'is_admin');

    expect($component->instance()->getRowByID(1))->not->toHaveKey('is_admin');
});
