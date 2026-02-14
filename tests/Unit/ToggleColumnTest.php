<?php

use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\Classes\Columns\ToggleColumn;

beforeEach(function () {
    Column::resetStaticKeys();
});

it('can be instantiated', function () {
    $column = ToggleColumn::make('Active');

    expect($column)->toBeInstanceOf(ToggleColumn::class);
    expect($column->label)->toBe('Active');
    expect($column->key)->toBe('active');
});

it('has isToggle set to true by default', function () {
    $column = ToggleColumn::make('Active');

    expect($column->isToggle)->toBeTrue();
});

it('has default what_is_true of 1', function () {
    $column = ToggleColumn::make('Active');

    expect($column->what_is_true)->toBe(1);
});

it('has default trigger of false', function () {
    $column = ToggleColumn::make('Active');

    expect($column->trigger)->toBeFalse();
});

it('sets trigger string', function () {
    $column = ToggleColumn::make('Active')->trigger('onToggleActive');

    expect($column->trigger)->toBe('onToggleActive');
});

it('stores disableToggleWhen callable', function () {
    $callback = function ($row) {
        return $row['locked'];
    };
    $column = ToggleColumn::make('Active')->disableToggleWhen($callback);

    expect($column->disableToggleWhen)->toBe($callback);
});

it('stores hideToggleWhen callable', function () {
    $callback = function ($row) {
        return $row['hidden'];
    };
    $column = ToggleColumn::make('Active')->hideToggleWhen($callback);

    expect($column->hideToggleWhen)->toBe($callback);
});

it('has null disableToggleWhen by default', function () {
    $column = ToggleColumn::make('Active');

    expect($column->disableToggleWhen)->toBeNull();
});

it('has null hideToggleWhen by default', function () {
    $column = ToggleColumn::make('Active');

    expect($column->hideToggleWhen)->toBeNull();
});

it('extends Column class', function () {
    $column = ToggleColumn::make('Active');

    expect($column)->toBeInstanceOf(Column::class);
});
