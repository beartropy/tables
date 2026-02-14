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

it('can accept a callback for card title', function () {
    $callback = function ($row) {
        return 'Title: '.$row['name'];
    };
    $column = Column::make('Name')->cardTitle($callback);

    expect($column->cardTitle)->toBeTrue();
    expect($column->cardTitleCallback)->toBe($callback);
});

it('can configure modal trigger', function () {
    $column = Column::make('Name')->triggerCardInfoModal(false);
    expect($column->triggerCardInfoModal)->toBeFalse();

    $column->triggerCardInfoModal(true);
    expect($column->triggerCardInfoModal)->toBeTrue();
});

// --- New tests ---

it('can be made editable', function () {
    $column = Column::make('Name')->editable('select', ['opt1', 'opt2']);

    expect($column->isEditable)->toBeTrue();
    expect($column->editableType)->toBe('select');
    expect($column->editableOptions)->toBe(['opt1', 'opt2']);
});

it('editable stores callback', function () {
    $callback = function ($id, $field, $value) {};
    $column = Column::make('Name')->editable('input', [], $callback);

    expect($column->editableCallback)->toBe($callback);
});

it('editable defaults to input type', function () {
    $column = Column::make('Name')->editable();

    expect($column->editableType)->toBe('input');
    expect($column->editableOptions)->toBe([]);
    expect($column->editableCallback)->toBeNull();
});

it('can set updateField', function () {
    $column = Column::make('Display Name')->setUpdateField('name');

    expect($column->updateField)->toBe('name');
});

it('pushLeft adds alignment classes', function () {
    $column = Column::make('Name')->pushLeft();

    expect($column->classes)->toContain('text-left');
    expect($column->th_wrapper_classes)->toContain('justify-start');
});

it('pushRight adds alignment classes', function () {
    $column = Column::make('Amount')->pushRight();

    expect($column->classes)->toContain('text-right');
    expect($column->th_wrapper_classes)->toContain('justify-end');
});

it('centered adds alignment classes', function () {
    $column = Column::make('Status')->centered();

    expect($column->classes)->toContain('text-center');
    expect($column->th_wrapper_classes)->toContain('justify-center');
});

it('can set custom view', function () {
    $column = Column::make('Name')->view('components.custom-cell');

    expect($column->hasView)->toBeTrue();
    expect($column->view)->toBe('components.custom-cell');
});

it('can set styling classes', function () {
    $column = Column::make('Name')->styling('bg-red-100 p-2');

    expect($column->classes)->toBe('bg-red-100 p-2');
});

it('can set th styling classes', function () {
    $column = Column::make('Name')->thStyling('bg-gray-200');

    expect($column->th_classes)->toBe('bg-gray-200');
});

it('can set th wrapper styling classes', function () {
    $column = Column::make('Name')->thWrapperStyling('flex items-center');

    expect($column->th_wrapper_classes)->toBe('flex items-center');
});

it('stores customData closure', function () {
    $closure = function ($row) {
        return strtoupper($row['name']);
    };
    $column = Column::make('Name')->customData($closure);

    expect($column->customData)->toBe($closure);
});

it('hideWhen sets isHidden and hideFromSelector', function () {
    $column = Column::make('Secret')->hideWhen(true);

    expect($column->isHidden)->toBeTrue();
    expect($column->hideFromSelector)->toBeTrue();
});

it('hideWhen false does not set hideFromSelector', function () {
    $column = Column::make('Secret')->hideWhen(false);

    expect($column->isHidden)->toBeFalse();
    expect($column->hideFromSelector)->toBeFalse();
});

it('hideFromSelector is independent of hideWhen', function () {
    $column = Column::make('Internal')->hideFromSelector(true);

    expect($column->hideFromSelector)->toBeTrue();
    expect($column->isHidden)->toBeFalse();
});

it('can set visibility', function () {
    $column = Column::make('Name')->isVisible(false);

    expect($column->isVisible)->toBeFalse();
});

it('can set sortColumnBy', function () {
    $column = Column::make('Full Name');
    $column->sortColumnBy = 'last_name';

    expect($column->sortColumnBy)->toBe('last_name');
});

it('sortColumnBy method sets alternative sort column', function () {
    $column = Column::make('Display Name')->sortColumnBy('sort_name');

    expect($column->sortColumnBy)->toBe('sort_name');
});

it('can set toHtml', function () {
    $column = Column::make('Bio')->toHtml();

    expect($column->isHtml)->toBeTrue();
});

it('isBool marks column as boolean', function () {
    $column = Column::make('Active')->isBool();

    expect($column->isBool)->toBeTrue();
});

it('showOnCard configures card display', function () {
    $column = Column::make('Name')->showOnCard();

    expect($column->showOnCard)->toBeTrue();
});

it('sortable with callback stores the callback', function () {
    $callback = function ($query, $direction) {
        return $query->orderBy('custom_sort', $direction);
    };
    $column = Column::make('Name')->sortable($callback);

    expect($column->isSortable)->toBeTrue();
    expect($column->sortableCallback)->toBe($callback);
});

it('searchable with callback stores the callback', function () {
    $callback = function ($query, $term) {
        return $query->where('name', 'like', "%$term%");
    };
    $column = Column::make('Name')->searchable($callback);

    expect($column->isSearchable)->toBeTrue();
    expect($column->searchableCallback)->toBe($callback);
});

it('generates hash key for # label', function () {
    $column = Column::make('#');

    expect($column->key)->toBe('hash');
});

it('generates unique keys for multiple # columns', function () {
    $column1 = Column::make('#');
    $column2 = Column::make('#');

    expect($column1->key)->toBe('hash');
    expect($column2->key)->toBe('hash_1');
});

it('handles special characters in label for key generation', function () {
    $column = Column::make('First & Last Name');

    expect($column->key)->not->toBeEmpty();
    expect($column->key)->not->toContain('&');
});

it('trims label whitespace', function () {
    $column = Column::make('  Name  ');

    expect($column->label)->toBe('Name');
});
