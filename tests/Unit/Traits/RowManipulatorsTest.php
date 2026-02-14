<?php

use Beartropy\Tables\Traits\RowManipulators;

beforeEach(function () {
    $this->rowClass = new class
    {
        use RowManipulators;
    };
});

it('has empty expanded rows by default', function () {
    expect($this->rowClass->yatable_expanded_rows)->toBe([]);
    expect($this->rowClass->yatable_expanded_rows_content)->toBe([]);
    expect($this->rowClass->yatable_expanded_rows_is_component)->toBeFalse();
});

it('toggleExpandedRow expands a row', function () {
    $this->rowClass->toggleExpandedRow(1, 'Some content');

    expect($this->rowClass->yatable_expanded_rows)->toContain(1);
    expect($this->rowClass->yatable_expanded_rows_content[1])->toBe('Some content');
});

it('toggleExpandedRow collapses an expanded row', function () {
    $this->rowClass->toggleExpandedRow(1, 'Some content');
    $this->rowClass->toggleExpandedRow(1, 'Some content');

    expect($this->rowClass->yatable_expanded_rows)->not->toContain(1);
    expect($this->rowClass->yatable_expanded_rows_content)->not->toHaveKey(1);
});

it('toggleExpandedRow with is_component true stores valid component array', function () {
    $content = ['component' => 'my-component', 'parameters' => ['id' => 1]];
    $this->rowClass->toggleExpandedRow(1, $content, true);

    expect($this->rowClass->yatable_expanded_rows_is_component)->toBeTrue();
    expect($this->rowClass->yatable_expanded_rows_content[1])->toBe($content);
});

it('toggleExpandedRow with is_component true throws for invalid array', function () {
    expect(function () {
        $this->rowClass->toggleExpandedRow(1, ['invalid' => 'data'], true);
    })->toThrow(Exception::class);
});

it('toggleExpandedRow with is_component true throws for string content', function () {
    expect(function () {
        $this->rowClass->toggleExpandedRow(1, 'not an array', true);
    })->toThrow(Exception::class);
});

it('can expand multiple rows', function () {
    $this->rowClass->toggleExpandedRow(1, 'Content 1');
    $this->rowClass->toggleExpandedRow(2, 'Content 2');

    expect($this->rowClass->yatable_expanded_rows)->toBe([1, 2]);
    expect($this->rowClass->yatable_expanded_rows_content)->toHaveCount(2);
});
