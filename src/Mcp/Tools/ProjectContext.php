<?php

namespace Beartropy\Tables\Mcp\Tools;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class ProjectContext extends Tool
{
    protected string $name = 'bt-tables-project-context';

    protected string $description = 'Returns this project\'s Beartropy Tables configuration: installed version, component counts, and package info.';

    public function schema(\Illuminate\Contracts\JsonSchema\JsonSchema $schema): array
    {
        return [];
    }

    public function handle(Request $request): Response
    {
        $lines = [];

        $lines[] = '# Beartropy Tables — Project Context';
        $lines[] = '';

        // Package version
        $lines[] = '## Version';
        $lines[] = '';
        $lines[] = $this->packageVersion();
        $lines[] = '';

        // Component tag prefix
        $lines[] = '## Livewire Component';
        $lines[] = '';
        $lines[] = 'Main component: `<livewire:y-a-t-base-table />` or `@livewire(\'YATBaseTable\')`.';
        $lines[] = 'View prefix: `yat::` — translations: `yat::yat.*`';
        $lines[] = '';

        // Component counts by category
        $lines[] = '## Available Components';
        $lines[] = '';

        $docsPath = dirname(__DIR__, 3).'/docs/llms';
        $docFiles = glob($docsPath.'/*.md') ?: [];
        $docNames = array_map(fn ($f) => basename($f, '.md'), $docFiles);

        $categories = ListComponents::CATEGORIES;
        $total = 0;

        foreach ($categories as $cat => $components) {
            $available = array_intersect($components, $docNames);
            $count = count($available);
            $total += $count;
            $lines[] = "- **{$cat}**: {$count} components";
        }

        $mapped = $categories ? array_merge(...array_values($categories)) : [];
        $uncategorized = array_diff($docNames, $mapped);

        if ($uncategorized !== []) {
            $count = count($uncategorized);
            $total += $count;
            $lines[] = "- **other**: {$count} components";
        }

        $lines[] = "- **total**: {$total} components";
        $lines[] = '';
        $lines[] = '> Use `bt-tables-list-components` for full names, `bt-tables-component-docs` for per-component details.';

        return Response::text(implode("\n", $lines));
    }

    protected function packageVersion(): string
    {
        $composerFile = dirname(__DIR__, 3).'/composer.json';

        if (! file_exists($composerFile)) {
            return 'unknown';
        }

        $data = json_decode(file_get_contents($composerFile), true);

        return $data['version'] ?? 'unknown';
    }
}
