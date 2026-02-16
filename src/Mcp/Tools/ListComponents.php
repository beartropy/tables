<?php

namespace Beartropy\Tables\Mcp\Tools;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class ListComponents extends Tool
{
    protected string $name = 'bt-tables-list-components';

    protected string $description = 'List all available Beartropy Tables components with their categories. Use this to discover component names before calling bt-tables-component-docs.';

    /** @var array<string, list<string>> */
    public const CATEGORIES = [
        'tables' => [
            'bear-table',
        ],
        'columns' => [
            'bool-column',
            'column',
            'date-column',
            'link-column',
            'toggle-column',
        ],
        'filters' => [
            'filter',
            'filter-bool',
            'filter-date-range',
            'filter-select',
            'filter-select-magic',
            'filter-string',
        ],
    ];

    public function schema(\Illuminate\Contracts\JsonSchema\JsonSchema $schema): array
    {
        return [
            'category' => $schema->string()
                ->description('Filter by category. Omit to list all components.'),
        ];
    }

    public function handle(Request $request): Response
    {
        $category = $request->get('category');
        $docsPath = dirname(__DIR__, 3).'/docs/llms';
        $available = [];

        foreach (glob($docsPath.'/*.md') as $file) {
            $available[] = basename($file, '.md');
        }

        $grouped = [];

        foreach (self::CATEGORIES as $cat => $components) {
            if ($category && $cat !== $category) {
                continue;
            }

            $grouped[$cat] = array_values(array_intersect($components, $available));
        }

        // Include any components not in the hardcoded map
        if (! $category) {
            $mapped = self::CATEGORIES ? array_merge(...array_values(self::CATEGORIES)) : [];
            $uncategorized = array_diff($available, $mapped);

            if ($uncategorized !== []) {
                $grouped['other'] = array_values($uncategorized);
            }
        }

        return Response::json($grouped);
    }
}
