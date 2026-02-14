<?php

use Beartropy\Tables\Classes\Columns\BoolColumn;
use Beartropy\Tables\Classes\Columns\Column;

beforeEach(function () {
    Column::resetStaticKeys();
});

it('can be instantiated', function () {
    $column = BoolColumn::make('Active');

    expect($column)->toBeInstanceOf(BoolColumn::class);
    expect($column->label)->toBe('Active');
    expect($column->key)->toBe('active');
});

it('has isBool set to true by default', function () {
    $column = BoolColumn::make('Active');

    expect($column->isBool)->toBeTrue();
});

it('has default what_is_true of 1', function () {
    $column = BoolColumn::make('Active');

    expect($column->what_is_true)->toBe(1);
});

it('has default true and false icons', function () {
    $column = BoolColumn::make('Active');

    expect($column->true_icon)->toContain('&#10004;');
    expect($column->false_icon)->toContain('&#10005;');
});

it('inherits trueIs method from Columns trait', function () {
    $column = BoolColumn::make('Active')->trueIs('yes');

    expect($column->what_is_true)->toBe('yes');
});

it('inherits trueLabel method from Columns trait', function () {
    $column = BoolColumn::make('Active')->trueLabel('Yes!');

    expect($column->true_icon)->toBe('Yes!');
});

it('inherits falseLabel method from Columns trait', function () {
    $column = BoolColumn::make('Active')->falseLabel('No!');

    expect($column->false_icon)->toBe('No!');
});

it('can set index manually', function () {
    $column = BoolColumn::make('Active', 'is_active');

    expect($column->key)->toBe('is_active');
    expect($column->index)->toBe('is_active');
});

it('extends Column class', function () {
    $column = BoolColumn::make('Active');

    expect($column)->toBeInstanceOf(Column::class);
});
