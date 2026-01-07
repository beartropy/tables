<?php

use Beartropy\Tables\Classes\Columns\DateColumn;
use Beartropy\Tables\Classes\Columns\Column;

beforeEach(function () {
    Column::resetStaticKeys();
});

it('can be instantiated', function () {
    $column = DateColumn::make('Created At');

    expect($column)->toBeInstanceOf(DateColumn::class);
    expect($column->label)->toBe('Created At');
    expect($column->key)->toBe('created_at');
    expect($column->isDate)->toBeTrue();
});

it('has default output format', function () {
    $column = DateColumn::make('Date');

    expect($column->outputFormat)->toBe('Y-m-d');
});

it('has default empty value', function () {
    $column = DateColumn::make('Date');

    expect($column->emptyValue)->toBe('');
});

it('can set input format', function () {
    $column = DateColumn::make('Date')->inputFormat('Y-m-d H:i:s');

    expect($column->inputFormat)->toBe('Y-m-d H:i:s');
});

it('can set output format', function () {
    $column = DateColumn::make('Date')->outputFormat('M d, Y');

    expect($column->outputFormat)->toBe('M d, Y');
});

it('can set empty value', function () {
    $column = DateColumn::make('Date')->emptyValue('Not Found');

    expect($column->emptyValue)->toBe('Not Found');
});

it('supports method chaining', function () {
    $column = DateColumn::make('Date')
        ->inputFormat('Y-m-d H:i:s')
        ->outputFormat('M d, Y')
        ->emptyValue('N/A');

    expect($column->inputFormat)->toBe('Y-m-d H:i:s');
    expect($column->outputFormat)->toBe('M d, Y');
    expect($column->emptyValue)->toBe('N/A');
});

it('can set index manually', function () {
    $column = DateColumn::make('Created', 'created_at');

    expect($column->key)->toBe('created_at');
    expect($column->index)->toBe('created_at');
});
