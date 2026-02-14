<?php

use Beartropy\Tables\Classes\Filters\Filter;
use Beartropy\Tables\Classes\Filters\FilterString;

it('can be instantiated', function () {
    $filter = new FilterString('Name');

    expect($filter->label)->toBe('Name');
    expect($filter->type)->toBe('string');
});

it('has make factory', function () {
    $filter = FilterString::make('Name');

    expect($filter)->toBeInstanceOf(FilterString::class);
    expect($filter)->toBeInstanceOf(Filter::class);
    expect($filter->label)->toBe('Name');
});

it('make factory accepts index', function () {
    $filter = FilterString::make('Name', 'user_name');

    expect($filter->column)->toBe('user_name');
});

it('extends Filter', function () {
    $filter = new FilterString('Name');

    expect($filter)->toBeInstanceOf(Filter::class);
});

it('inherits query method', function () {
    $callback = function ($query, $value) {
        return $query->where('name', 'like', "%$value%");
    };

    $filter = FilterString::make('Name')->query($callback);

    expect($filter->queryCallback)->toBe($callback);
});
