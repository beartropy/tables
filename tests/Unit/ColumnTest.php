<?php

use Beartropy\Tables\Classes\Columns\Column;

beforeEach(function () {
    Column::resetStaticKeys();
});

it('can be instantiated', function () {
    $column = Column::make('Name');

    expect($column)->toBeInstanceOf(Column::class);
    expect($column->label)->toBe('Name');
    expect($column->key)->toBe('name');
});

it('generates unique keys', function () {
    $column1 = Column::make('Name');
    $column2 = Column::make('Name');

    expect($column1->key)->toBe('name');
    expect($column2->key)->toBe('name_1');
});

it('can set index manually', function () {
    $column = Column::make('Name', 'user_name');

    expect($column->key)->toBe('user_name');
    expect($column->index)->toBe('user_name');
});

it('can be made sortable', function () {
    $column = Column::make('Name')->sortable();
    expect($column->isSortable)->toBeTrue();

    $column = Column::make('Name')->sortable(false);
    expect($column->isSortable)->toBeFalse();
});

it('can be made searchable', function () {
    $column = Column::make('Name')->searchable();
    expect($column->isSearchable)->toBeTrue();

    $column = Column::make('Name')->searchable(false);
    expect($column->isSearchable)->toBeFalse();
});

it('can be configured for mobile', function () {
    $column = Column::make('Name')->collapseOnMobile();
    expect($column->collapseOnMobile)->toBeTrue();
});
