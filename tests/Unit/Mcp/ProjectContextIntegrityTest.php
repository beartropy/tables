<?php

/**
 * Tests for the data layer used by the bt-tables-project-context MCP tool.
 *
 * Verifies that the data sources the tool relies on are present and consistent.
 *
 * Since `laravel/mcp` is not a dev dependency, we test the raw data sources
 * rather than instantiating the tool class.
 */

$basePath = dirname(__DIR__, 3);
$composerPath = $basePath.'/composer.json';

it('has a version field in composer.json', function () use ($composerPath) {
    $data = json_decode(file_get_contents($composerPath), true);

    expect($data)->toHaveKey('version');
    expect($data['version'])->toBeString()->not->toBeEmpty();
});

it('has a valid semver version in composer.json', function () use ($composerPath) {
    $data = json_decode(file_get_contents($composerPath), true);

    expect($data['version'])->toMatch('/^\d+\.\d+\.\d+(-[\w.]+)?$/');
});
