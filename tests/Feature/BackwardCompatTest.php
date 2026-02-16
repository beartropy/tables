<?php

use Beartropy\Tables\BeartropyTable;
use Beartropy\Tables\BeartropyTableServiceProvider;
use Beartropy\Tables\YATBaseTable;
use Beartropy\Tables\YATProvider;
use Beartropy\Tables\Classes\Columns\Column;
use Livewire\Livewire;

class LegacyTable extends YATBaseTable
{
    public function columns()
    {
        return [
            Column::make('Name', 'name'),
        ];
    }

    public function data()
    {
        return [
            ['id' => 1, 'name' => 'Legacy Row'],
        ];
    }

    public function settings() {}
}

it('extends YATBaseTable still works', function () {
    Livewire::test(LegacyTable::class)
        ->assertStatus(200)
        ->assertSee('Legacy Row');
});

it('YATBaseTable instanceof BeartropyTable', function () {
    $table = new LegacyTable;
    expect($table)->toBeInstanceOf(BeartropyTable::class);
    expect($table)->toBeInstanceOf(YATBaseTable::class);
});

it('YATProvider extends BeartropyTableServiceProvider', function () {
    $provider = new YATProvider(app());
    expect($provider)->toBeInstanceOf(BeartropyTableServiceProvider::class);
});
