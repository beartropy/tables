<?php

use Beartropy\Tables\Traits\Spinner;

beforeEach(function () {
    $this->spinnerClass = new class
    {
        use Spinner;
    };
});

it('has default trigger_spinner string', function () {
    expect($this->spinnerClass->trigger_spinner)->toContain('gotoPage');
    expect($this->spinnerClass->trigger_spinner)->toContain('sortBy');
    expect($this->spinnerClass->trigger_spinner)->toContain('yat_global_search');
});

it('has loading spinner enabled by default', function () {
    expect($this->spinnerClass->loading_table_spinner)->toBeTrue();
});

it('has null custom spinner view by default', function () {
    expect($this->spinnerClass->loading_table_spinner_custom_view)->toBeNull();
});

it('can disable spinner', function () {
    $this->spinnerClass->useTableSpinner(false);

    expect($this->spinnerClass->loading_table_spinner)->toBeFalse();
});

it('can re-enable spinner', function () {
    $this->spinnerClass->useTableSpinner(false);
    $this->spinnerClass->useTableSpinner(true);

    expect($this->spinnerClass->loading_table_spinner)->toBeTrue();
});

it('can set custom spinner view', function () {
    $this->spinnerClass->setTableSpinnerView('components.my-spinner');

    expect($this->spinnerClass->loading_table_spinner_custom_view)->toBe('components.my-spinner');
});

it('appends targets to spinner', function () {
    $original = $this->spinnerClass->trigger_spinner;
    $this->spinnerClass->addTargetsToSpinner(['customAction', 'anotherAction']);

    expect($this->spinnerClass->trigger_spinner)->toContain('customAction');
    expect($this->spinnerClass->trigger_spinner)->toContain('anotherAction');
    expect(strlen($this->spinnerClass->trigger_spinner))->toBeGreaterThan(strlen($original));
});

it('empty array is a no-op for addTargetsToSpinner', function () {
    $original = $this->spinnerClass->trigger_spinner;
    $this->spinnerClass->addTargetsToSpinner([]);

    expect($this->spinnerClass->trigger_spinner)->toBe($original);
});
