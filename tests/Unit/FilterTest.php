<?php

use Beartropy\Tables\Classes\Filters\Filter;

it('can be instantiated', function () {
    $filter = new Filter('Name');

    expect($filter->label)->toBe('Name');
    expect($filter->column)->toBeNull();
});

it('trims the label', function () {
    $filter = new Filter('  Name  ');

    expect($filter->label)->toBe('Name');
});

it('can set column in constructor', function () {
    $filter = new Filter('Name', 'user_name');

    expect($filter->column)->toBe('user_name');
});

it('stores query callback and chains', function () {
    $callback = function ($query, $value) {
        return $query->where('name', $value);
    };

    $filter = new Filter('Name');
    $result = $filter->query($callback);

    expect($filter->queryCallback)->toBe($callback);
    expect($result)->toBe($filter);
});

it('has null queryCallback by default', function () {
    $filter = new Filter('Name');

    expect($filter->queryCallback)->toBeNull();
});

it('has null key by default', function () {
    $filter = new Filter('Name');

    expect($filter->key)->toBeNull();
});

it('has null input by default', function () {
    $filter = new Filter('Name');

    expect($filter->input)->toBeNull();
});
