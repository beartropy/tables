<?php

use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\Classes\Columns\ToggleColumn;
use Beartropy\Tables\BeartropyTable;
use Livewire\Livewire;

class ArrayTableForRowOps extends BeartropyTable
{
    public function columns()
    {
        return [
            Column::make('Name', 'name'),
            Column::make('Email', 'email'),
            ToggleColumn::make('Active', 'active'),
        ];
    }

    public function data()
    {
        return [
            ['id' => 1, 'name' => 'Alice', 'email' => 'alice@example.com', 'active' => true],
            ['id' => 2, 'name' => 'Bob', 'email' => 'bob@example.com', 'active' => false],
            ['id' => 3, 'name' => 'Charlie', 'email' => 'charlie@example.com', 'active' => true],
        ];
    }

    public function settings()
    {
        $this->hasBulk(true);
    }
}

class ArrayTableWithTrigger extends BeartropyTable
{
    public bool $triggerCalled = false;

    public function columns()
    {
        return [
            Column::make('Name', 'name'),
            ToggleColumn::make('Active', 'active')->trigger('onToggle'),
        ];
    }

    public function data()
    {
        return [
            ['id' => 1, 'name' => 'Alice', 'active' => true],
        ];
    }

    public function onToggle($id, $column): void
    {
        $this->triggerCalled = true;
    }

    public function settings() {}
}

it('removeRowFromTable removes a row', function () {
    $component = Livewire::test(ArrayTableForRowOps::class)
        ->assertSee('Alice')
        ->call('removeRowFromTable', 1)
        ->assertDontSee('Alice')
        ->assertSee('Bob');
});

it('removeRowFromTable with resetSelected=true clears selection', function () {
    $component = Livewire::test(ArrayTableForRowOps::class)
        ->set('yat_selected_checkbox', [2, 3])
        ->call('removeRowFromTable', 1, true);

    // updateCacheData also calls emptySelection, so selection is always cleared
    expect($component->get('yat_selected_checkbox'))->toBe([]);
});

it('addRowToTable adds a row', function () {
    Livewire::test(ArrayTableForRowOps::class)
        ->call('addRowToTable', ['id' => 4, 'name' => 'Diana', 'email' => 'diana@example.com', 'active' => true])
        ->assertSee('Diana');
});

it('updateRowOnTable updates row data', function () {
    Livewire::test(ArrayTableForRowOps::class)
        ->call('updateRowOnTable', 1, ['name' => 'Alice Updated'])
        ->assertSee('Alice Updated');
});

it('toggleBoolean flips boolean value', function () {
    $component = Livewire::test(ArrayTableForRowOps::class)
        ->call('toggleBoolean', 2, 'active');

    // Bob's active was false, should now be true
    $data = $component->instance()->getAllData();
    $bob = $data->firstWhere('id', 2);
    expect($bob['active'])->toBeTrue();
});

it('toggleBoolean calls trigger method when defined', function () {
    $component = Livewire::test(ArrayTableWithTrigger::class)
        ->call('toggleBoolean', 1, 'active');

    expect($component->get('triggerCalled'))->toBeTrue();
});

it('toggleExpandedRow expands and collapses', function () {
    $component = Livewire::test(ArrayTableForRowOps::class)
        ->call('toggleExpandedRow', 1, 'Row 1 details');

    expect($component->get('yatable_expanded_rows'))->toContain(1);

    $component->call('toggleExpandedRow', 1, 'Row 1 details');

    expect($component->get('yatable_expanded_rows'))->not->toContain(1);
});

it('toggleExpandedRow with string content works', function () {
    $component = Livewire::test(ArrayTableForRowOps::class)
        ->call('toggleExpandedRow', 1, '<div>Details here</div>');

    expect($component->get('yatable_expanded_rows'))->toContain(1);
});
