<?php

use Beartropy\Tables\BeartropyTable;

it('shows options by default', function () {
    $component = new class extends BeartropyTable
    {
        public function options()
        {
            return ['export' => 'Export'];
        }
    };

    expect($component->showOptionsOnlyOnRowSelect)->toBeFalse();
});

it('can enable show options only on row select', function () {
    $component = new class extends BeartropyTable
    {
        public function options()
        {
            return ['export' => 'Export'];
        }
    };

    $component->showOptionsOnlyOnRowSelect(true);
    expect($component->showOptionsOnlyOnRowSelect)->toBeTrue();
});

it('normalizes string options to array with label and icon', function () {
    $component = new class extends BeartropyTable
    {
        public function options()
        {
            return ['export' => 'Export', 'delete' => 'Delete'];
        }
    };

    $component->setOptions();

    expect($component->options['export'])->toBe(['label' => 'Export', 'icon' => null]);
    expect($component->options['delete'])->toBe(['label' => 'Delete', 'icon' => null]);
});

it('merges array options with defaults', function () {
    $component = new class extends BeartropyTable
    {
        public function options()
        {
            return [
                'export' => ['label' => 'Export', 'icon' => 'download'],
                'delete' => ['label' => 'Delete'],
            ];
        }
    };

    $component->setOptions();

    expect($component->options['export'])->toBe(['label' => 'Export', 'icon' => 'download']);
    expect($component->options['delete'])->toBe(['label' => 'Delete', 'icon' => null]);
});

it('silently handles exception in options method', function () {
    $component = new class extends BeartropyTable
    {
        public function options()
        {
            throw new \RuntimeException('Boom');
        }
    };

    $component->setOptions();

    expect($component->options)->toBeNull();
});
