<?php

/**
 * Tests for the data layer used by the bt-tables-list-components MCP tool.
 *
 * Verifies that the hardcoded CATEGORIES constant in ListComponents stays in
 * sync with the actual documentation files.
 *
 * Since `laravel/mcp` is not a dev dependency, we parse the CATEGORIES constant
 * directly from the source file rather than loading the class.
 */
$docsPath = dirname(__DIR__, 3).'/docs/llms';
$sourcePath = dirname(__DIR__, 3).'/src/Mcp/Tools/ListComponents.php';

/**
 * Parse the CATEGORIES constant from the ListComponents source file.
 *
 * @return array<string, list<string>>
 */
function parseCategoriesFromSource(string $sourcePath): array
{
    $source = file_get_contents($sourcePath);

    preg_match('/(?:public|protected)\s+const\s+CATEGORIES\s*=\s*\[(.+?)\n\s*\];/s', $source, $match);

    if (! $match) {
        throw new RuntimeException('Could not parse CATEGORIES from ListComponents.php');
    }

    $categories = [];
    $block = $match[1];

    preg_match_all("/'(\w+)'\s*=>\s*\[([^\]]+)\]/s", $block, $catMatches, PREG_SET_ORDER);

    foreach ($catMatches as $catMatch) {
        $catName = $catMatch[1];
        preg_match_all("/'([a-z][-a-z]*)'/", $catMatch[2], $nameMatches);
        $categories[$catName] = $nameMatches[1];
    }

    return $categories;
}

$categories = parseCategoriesFromSource($sourcePath);
$allCategorized = $categories ? array_merge(...array_values($categories)) : [];

it('has doc files for every component in CATEGORIES', function () use ($docsPath, $allCategorized) {
    $missing = [];

    foreach ($allCategorized as $name) {
        if (! file_exists($docsPath.'/'.$name.'.md')) {
            $missing[] = $name;
        }
    }

    expect($missing)->toBeEmpty(
        'Components in CATEGORIES without LLM docs: '.implode(', ', $missing)
    );
});

it('has CATEGORIES entries for every doc file', function () use ($docsPath, $allCategorized) {
    $docFiles = glob($docsPath.'/*.md');
    $uncategorized = [];

    foreach ($docFiles as $file) {
        $name = basename($file, '.md');

        if ($name === '_template') {
            continue;
        }

        if (! in_array($name, $allCategorized, true)) {
            $uncategorized[] = $name;
        }
    }

    expect($uncategorized)->toBeEmpty(
        'Doc files not listed in CATEGORIES: '.implode(', ', $uncategorized)
    );
});

it('has no duplicate entries across categories', function () use ($categories) {
    $seen = [];
    $duplicates = [];

    foreach ($categories as $cat => $components) {
        foreach ($components as $name) {
            if (isset($seen[$name])) {
                $duplicates[] = "{$name} (in '{$seen[$name]}' and '{$cat}')";
            }
            $seen[$name] = $cat;
        }
    }

    expect($duplicates)->toBeEmpty(
        'Duplicate components across categories: '.implode(', ', $duplicates)
    );
});

it('keeps components alphabetically sorted within each category', function () use ($categories) {
    foreach ($categories as $cat => $components) {
        $sorted = $components;
        sort($sorted);

        expect($components)->toBe($sorted,
            "Components in '{$cat}' category are not alphabetically sorted"
        );
    }
});

it('categorized count matches doc file count', function () use ($docsPath, $allCategorized) {
    $docFiles = array_filter(
        glob($docsPath.'/*.md'),
        fn ($f) => basename($f) !== '_template.md'
    );
    $docCount = count($docFiles);

    expect(count($allCategorized))->toBe($docCount,
        count($allCategorized)." categorized components vs {$docCount} doc files"
    );
});
