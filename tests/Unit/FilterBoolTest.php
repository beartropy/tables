<?php

use Beartropy\Tables\Classes\Filters\Filter;
use Beartropy\Tables\Classes\Filters\FilterBool;

it('can be instantiated', function () {
    $filter = new FilterBool('Active');

    expect($filter->label)->toBe('Active');
    expect($filter->type)->toBe('bool');
});

it('has default compared_with', function () {
    $filter = new FilterBool('Active');

    expect($filter->compared_with)->toBe(['true' => true, 'false' => false]);
});

it('accepts custom compared_with', function () {
    $filter = new FilterBool('Active', ['yes' => 1, 'no' => 0]);

    expect($filter->compared_with)->toBe(['yes' => 1, 'no' => 0]);
});

it('has make factory', function () {
    $filter = FilterBool::make('Active');

    expect($filter)->toBeInstanceOf(FilterBool::class);
    expect($filter)->toBeInstanceOf(Filter::class);
    expect($filter->label)->toBe('Active');
});

it('make factory accepts custom compared_with and index', function () {
    $filter = FilterBool::make('Active', ['yes' => 1, 'no' => 0], 'is_active');

    expect($filter->compared_with)->toBe(['yes' => 1, 'no' => 0]);
    expect($filter->column)->toBe('is_active');
});

it('extends Filter', function () {
    $filter = new FilterBool('Active');

    expect($filter)->toBeInstanceOf(Filter::class);
});
