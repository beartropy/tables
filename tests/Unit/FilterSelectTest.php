<?php

use Beartropy\Tables\Classes\Filters\Filter;
use Beartropy\Tables\Classes\Filters\FilterSelect;

it('can be instantiated', function () {
    $filter = new FilterSelect('Status', ['active', 'inactive']);

    expect($filter->label)->toBe('Status');
    expect($filter->type)->toBe('select');
    expect($filter->options)->toBe(['active', 'inactive']);
});

it('stores options correctly', function () {
    $options = ['admin' => 'Admin', 'user' => 'User', 'guest' => 'Guest'];
    $filter = new FilterSelect('Role', $options);

    expect($filter->options)->toBe($options);
});

it('has make factory', function () {
    $filter = FilterSelect::make('Status', ['active', 'inactive']);

    expect($filter)->toBeInstanceOf(FilterSelect::class);
    expect($filter)->toBeInstanceOf(Filter::class);
    expect($filter->label)->toBe('Status');
    expect($filter->options)->toBe(['active', 'inactive']);
});

it('make factory accepts index', function () {
    $filter = FilterSelect::make('Status', ['active'], 'user_status');

    expect($filter->column)->toBe('user_status');
});

it('extends Filter', function () {
    $filter = new FilterSelect('Status', []);

    expect($filter)->toBeInstanceOf(Filter::class);
});
