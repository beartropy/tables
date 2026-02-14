<?php

use Beartropy\Tables\Classes\Filters\Filter;
use Beartropy\Tables\Classes\Filters\FilterDateRange;

it('can be instantiated', function () {
    $filter = new FilterDateRange('Created At');

    expect($filter->label)->toBe('Created At');
    expect($filter->type)->toBe('daterange');
});

it('has empty daterange by default', function () {
    $filter = new FilterDateRange('Created At');

    expect($filter->daterange)->toBe([]);
});

it('has make factory', function () {
    $filter = FilterDateRange::make('Created At');

    expect($filter)->toBeInstanceOf(FilterDateRange::class);
    expect($filter)->toBeInstanceOf(Filter::class);
    expect($filter->label)->toBe('Created At');
});

it('make factory accepts index', function () {
    $filter = FilterDateRange::make('Created At', 'created_at');

    expect($filter->column)->toBe('created_at');
});

it('extends Filter', function () {
    $filter = new FilterDateRange('Date');

    expect($filter)->toBeInstanceOf(Filter::class);
});
