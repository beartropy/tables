<?php

use Beartropy\Tables\Classes\Filters\Filter;
use Beartropy\Tables\Classes\Filters\FilterSelectMagic;

it('can be instantiated', function () {
    $filter = new FilterSelectMagic('Status');

    expect($filter->label)->toBe('Status');
    expect($filter->type)->toBe('magic-select');
});

it('has no options at construction', function () {
    $filter = new FilterSelectMagic('Status');

    expect($filter->options)->toBeNull();
});

it('has make factory', function () {
    $filter = FilterSelectMagic::make('Status');

    expect($filter)->toBeInstanceOf(FilterSelectMagic::class);
    expect($filter)->toBeInstanceOf(Filter::class);
    expect($filter->label)->toBe('Status');
});

it('make factory accepts index', function () {
    $filter = FilterSelectMagic::make('Status', 'user_status');

    expect($filter->column)->toBe('user_status');
});

it('extends Filter', function () {
    $filter = new FilterSelectMagic('Status');

    expect($filter)->toBeInstanceOf(Filter::class);
});
