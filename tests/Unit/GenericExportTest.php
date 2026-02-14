<?php

use Beartropy\Tables\Exports\GenericExport;

$skipExport = ! interface_exists(\Maatwebsite\Excel\Concerns\FromCollection::class);

it('converts 1-based index to Excel column name', function () {
    $export = new GenericExport(collect(), true);

    expect($export->getExcelColumnName(1))->toBe('A');
    expect($export->getExcelColumnName(26))->toBe('Z');
    expect($export->getExcelColumnName(27))->toBe('AA');
    expect($export->getExcelColumnName(52))->toBe('AZ');
    expect($export->getExcelColumnName(53))->toBe('BA');
    expect($export->getExcelColumnName(702))->toBe('ZZ');
})->skip($skipExport, 'maatwebsite/excel not installed');

it('generates headings from data keys as title case', function () {
    $data = collect([
        ['first_name' => 'Alice', 'last_name' => 'Smith', 'email' => 'alice@test.com'],
    ]);

    $export = new GenericExport($data, true);
    $headings = $export->headings();

    expect($headings)->toBe(['First name', 'Last name', 'Email']);
})->skip($skipExport, 'maatwebsite/excel not installed');

it('strips HTML tags from collection when strip_tags is true', function () {
    $data = collect([
        ['name' => '<b>Alice</b>', 'bio' => '<p>Developer</p>'],
    ]);

    $export = new GenericExport($data, true);
    $export->headings();
    $collection = $export->collection();

    expect($collection->first()['name'])->toBe('Alice');
    expect($collection->first()['bio'])->toBe('Developer');
})->skip($skipExport, 'maatwebsite/excel not installed');

it('preserves HTML when strip_tags is false', function () {
    $data = collect([
        ['name' => '<b>Alice</b>'],
    ]);

    $export = new GenericExport($data, false);
    $collection = $export->collection();

    expect($collection->first()['name'])->toBe('<b>Alice</b>');
})->skip($skipExport, 'maatwebsite/excel not installed');

it('strips _original keys from data', function () {
    $data = collect([
        ['name' => 'ALICE', 'name_original' => 'Alice', 'email' => 'alice@test.com'],
    ]);

    $export = new GenericExport($data, true);
    $export->headings();
    $collection = $export->collection();

    expect(array_keys($collection->first()->toArray()))->not->toContain('name_original');
})->skip($skipExport, 'maatwebsite/excel not installed');

it('handles empty data', function () {
    $export = new GenericExport(collect(), true);
    $headings = $export->headings();
    $collection = $export->collection();

    expect($headings)->toBe([]);
    expect($collection)->toBeEmpty();
})->skip($skipExport, 'maatwebsite/excel not installed');

it('implodes array values with comma and space', function () {
    $data = collect([
        ['name' => 'Alice', 'tags' => ['php', 'laravel']],
    ]);

    $export = new GenericExport($data, true);
    $export->headings();
    $collection = $export->collection();

    expect($collection->first()['tags'])->toBe('php, laravel');
})->skip($skipExport, 'maatwebsite/excel not installed');
