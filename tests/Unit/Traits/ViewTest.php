<?php

use Beartropy\Tables\Traits\View;

beforeEach(function () {
    $this->viewClass = new class
    {
        use View;

        // Required by getRowStripingClasses
        public function getAllData()
        {
            return collect();
        }
    };
});

it('has default property values', function () {
    expect($this->viewClass->title)->toBeNull();
    expect($this->viewClass->has_counter)->toBeTrue();
    expect($this->viewClass->showCardsOnMobile)->toBeFalse();
    expect($this->viewClass->useCards)->toBeFalse();
    expect($this->viewClass->yat_is_mobile)->toBeFalse();
    expect($this->viewClass->theme)->toBe('gray');
    expect($this->viewClass->stripRows)->toBeTrue();
    expect($this->viewClass->sticky_header)->toBeFalse();
    expect($this->viewClass->yat_custom_buttons)->toBe([]);
    expect($this->viewClass->yat_card_modal_buttons)->toBe([]);
    expect($this->viewClass->yat_button_variant)->toBe('soft');
});

it('setTheme loads config from presets', function () {
    $this->viewClass->setTheme('blue');

    expect($this->viewClass->theme)->toBe('blue');
    expect($this->viewClass->themeConfig)->not->toBeEmpty();
});

it('getThemeConfig falls back to gray for invalid theme', function () {
    $grayConfig = $this->viewClass->getThemeConfig('gray');
    $invalidConfig = $this->viewClass->getThemeConfig('nonexistent_theme');

    expect($invalidConfig)->toBe($grayConfig);
});

it('setTitle sets the title', function () {
    $this->viewClass->setTitle('My Table');

    expect($this->viewClass->title)->toBe('My Table');
});

it('overrideTitleClasses sets title classes', function () {
    $this->viewClass->overrideTitleClasses('text-xl font-bold');

    expect($this->viewClass->titleClasses)->toBe('text-xl font-bold');
});

it('setCustomHeader sets custom HTML', function () {
    $this->viewClass->setCustomHeader('<h1>Custom</h1>');

    expect($this->viewClass->customHeader)->toBe('<h1>Custom</h1>');
});

it('setComponentClasses sets wrapper classes', function () {
    $this->viewClass->setComponentClasses('p-4 bg-white');

    expect($this->viewClass->main_wrapper_classes)->toBe('p-4 bg-white');
});

it('addTableClasses sets table classes', function () {
    $this->viewClass->addTableClasses('w-full');

    expect($this->viewClass->table_classes)->toBe('w-full');
    expect($this->viewClass->override_table_classes)->toBeFalse();
});

it('setTableClasses sets and overrides table classes', function () {
    $this->viewClass->setTableClasses('custom-table');

    expect($this->viewClass->table_classes)->toBe('custom-table');
    expect($this->viewClass->override_table_classes)->toBeTrue();
});

it('setStickyHeader enables sticky header', function () {
    $this->viewClass->setStickyHeader();

    expect($this->viewClass->sticky_header)->toBeTrue();
});

it('showCounter toggles counter', function () {
    $this->viewClass->showCounter(false);
    expect($this->viewClass->has_counter)->toBeFalse();

    $this->viewClass->showCounter(true);
    expect($this->viewClass->has_counter)->toBeTrue();
});

it('useCards toggles card layout', function () {
    $this->viewClass->useCards(true);
    expect($this->viewClass->useCards)->toBeTrue();

    $this->viewClass->useCards(false);
    expect($this->viewClass->useCards)->toBeFalse();
});

it('showCardsOnMobile toggles mobile cards', function () {
    $this->viewClass->showCardsOnMobile(true);
    expect($this->viewClass->showCardsOnMobile)->toBeTrue();
});

it('addButtons stores button array', function () {
    $buttons = [['label' => 'Export', 'action' => 'export']];
    $this->viewClass->addButtons($buttons);

    expect($this->viewClass->yat_custom_buttons)->toBe($buttons);
});

it('addCardModalButtons stores button array', function () {
    $buttons = [['label' => 'Edit', 'action' => 'edit']];
    $this->viewClass->addCardModalButtons($buttons);

    expect($this->viewClass->yat_card_modal_buttons)->toBe($buttons);
});

it('setLayout stores layout', function () {
    $this->viewClass->setLayout('layouts.custom');

    expect($this->viewClass->layout)->toBe('layouts.custom');
});

it('stripRows can be disabled', function () {
    $this->viewClass->stripRows(false);

    expect($this->viewClass->stripRows)->toBeFalse();
});

it('getRowStripingClasses returns striped classes when enabled', function () {
    $this->viewClass->setTheme('gray');
    $classes = $this->viewClass->getRowStripingClasses();

    expect($classes)->toContain('odd:');
    expect($classes)->toContain('even:');
});

it('getRowStripingClasses returns non-striped classes when disabled', function () {
    $this->viewClass->setTheme('gray');
    $this->viewClass->stripRows(false);
    $classes = $this->viewClass->getRowStripingClasses();

    expect($classes)->not->toContain('odd:');
});

it('setBulkThemeOverride sets override', function () {
    $this->viewClass->setBulkThemeOverride('blue');

    expect($this->viewClass->bulkThemeOverride)->toBe('blue');
});

it('setButtonThemeOverride sets override', function () {
    $this->viewClass->setButtonThemeOverride('red');

    expect($this->viewClass->buttonThemeOverride)->toBe('red');
});

it('setInputThemeOverride sets override', function () {
    $this->viewClass->setInputThemeOverride('green');

    expect($this->viewClass->inputThemeOverride)->toBe('green');
});

it('setModalsView sets view', function () {
    $this->viewClass->setModalsView('modals.custom');

    expect($this->viewClass->modals_view)->toBe('modals.custom');
});

it('setMostLeftView sets view', function () {
    $this->viewClass->setMostLeftView('parts.left');

    expect($this->viewClass->yat_most_left_view)->toBe('parts.left');
});

it('setLessLeftView sets view', function () {
    $this->viewClass->setLessLeftView('parts.less-left');

    expect($this->viewClass->yat_less_left_view)->toBe('parts.less-left');
});

it('setMostRightView sets view', function () {
    $this->viewClass->setMostRightView('parts.right');

    expect($this->viewClass->yat_most_right_view)->toBe('parts.right');
});

it('setLessRightView sets view', function () {
    $this->viewClass->setLessRightView('parts.less-right');

    expect($this->viewClass->yat_less_right_view)->toBe('parts.less-right');
});

it('setButtonVariant sets variant', function () {
    $this->viewClass->setButtonVariant('solid');

    expect($this->viewClass->yat_button_variant)->toBe('solid');
});

it('showOptionsOnlyOnRowSelect defaults to false', function () {
    expect($this->viewClass->showOptionsOnlyOnRowSelect)->toBeFalse();
});

it('showOptionsOnlyOnRowSelect can be toggled', function () {
    $this->viewClass->showOptionsOnlyOnRowSelect(true);
    expect($this->viewClass->showOptionsOnlyOnRowSelect)->toBeTrue();

    $this->viewClass->showOptionsOnlyOnRowSelect(false);
    expect($this->viewClass->showOptionsOnlyOnRowSelect)->toBeFalse();
});

it('mountView sets theme if themeConfig is empty', function () {
    expect($this->viewClass->themeConfig)->toBe([]);

    $this->viewClass->mountView();

    expect($this->viewClass->themeConfig)->not->toBeEmpty();
});

it('mountView does not reset theme if themeConfig is already set', function () {
    $this->viewClass->setTheme('blue');
    $blueConfig = $this->viewClass->themeConfig;

    $this->viewClass->mountView();

    expect($this->viewClass->themeConfig)->toBe($blueConfig);
});
