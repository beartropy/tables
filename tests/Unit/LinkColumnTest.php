<?php

use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\Classes\Columns\LinkColumn;

beforeEach(function () {
    Column::resetStaticKeys();
});

it('can be instantiated', function () {
    $column = LinkColumn::make('Website');

    expect($column)->toBeInstanceOf(LinkColumn::class);
    expect($column->label)->toBe('Website');
    expect($column->key)->toBe('website');
});

it('has isLink set to true by default', function () {
    $column = LinkColumn::make('Website');

    expect($column->isLink)->toBeTrue();
});

it('stores href closure', function () {
    $closure = function ($row) {
        return '/users/'.$row['id'];
    };
    $column = LinkColumn::make('Website')->href($closure);

    expect($column->href)->toBe($closure);
});

it('stores text value', function () {
    $column = LinkColumn::make('Website')->text('Visit');

    expect($column->text)->toBe('Visit');
});

it('sets target attribute', function () {
    $column = LinkColumn::make('Website')->target('_blank');

    expect($column->target)->toBe('_blank');
});

it('sets popup dimensions', function () {
    $column = LinkColumn::make('Website')->popup(['width' => 500, 'height' => 400]);

    expect($column->popup)->toBe(['width' => 500, 'height' => 400]);
});

it('sets tag classes via classes method', function () {
    $column = LinkColumn::make('Website')->classes('text-blue-500 underline');

    expect($column->tag_classes)->toBe('text-blue-500 underline');
});

it('text throws on non-link column', function () {
    Column::make('Name')->text('Something');
})->throws(ErrorException::class);

it('href throws on non-link column', function () {
    Column::make('Name')->href(function () {
        return '/test';
    });
})->throws(ErrorException::class);

it('target throws on non-link column', function () {
    Column::make('Name')->target('_blank');
})->throws(ErrorException::class);

it('extends Column class', function () {
    $column = LinkColumn::make('Website');

    expect($column)->toBeInstanceOf(Column::class);
});

it('has has_modified_data set to false by default', function () {
    $column = LinkColumn::make('Website');

    expect($column->has_modified_data)->toBeFalse();
});
