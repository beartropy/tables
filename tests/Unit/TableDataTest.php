<?php

use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\Classes\Columns\DateColumn;
use Beartropy\Tables\Classes\Columns\LinkColumn;
use Beartropy\Tables\Classes\Columns\ToggleColumn;
use Beartropy\Tables\Traits\Columns;
use Beartropy\Tables\Traits\Data;

class TestTableWithData
{
    use Columns, Data;

    public mixed $model = null;

    public string $custom_column_id = 'id';

    public function columns()
    {
        return [
            Column::make('Name', 'name')
                ->cardTitle(function ($row) {
                    return 'Title: '.$row['name'];
                }),
            Column::make('Email', 'email'),
        ];
    }
}

beforeEach(function () {
    Column::resetStaticKeys();
});

it('calculates card title from callback in transformRow', function () {
    $table = new TestTableWithData;
    $table->columns = collect($table->columns());

    $row = ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'];

    $metadata = $table->getColumnMetadata();

    expect($metadata['cardTitleCallbacks'])->toHaveKey('name');

    $transformed = $table->transformRow($row, [], [], [], $metadata['cardTitleCallbacks']);

    expect($transformed)->toHaveKey('name_card_title');
    expect($transformed['name_card_title'])->toBe('Title: John Doe');
});

it('transformRow with customData closure stores _original and sets has_modified_data', function () {
    $table = new class
    {
        use Columns, Data;

        public mixed $model = null;

        public string $custom_column_id = 'id';

        public function columns()
        {
            return [
                Column::make('Name', 'name')->customData(function ($row) {
                    return strtoupper($row['name']);
                }),
                Column::make('Email', 'email'),
            ];
        }
    };

    $table->columns = collect($table->columns());
    $metadata = $table->getColumnMetadata();

    expect($metadata['customData'])->toHaveKey('name');

    $row = ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'];
    $transformed = $table->transformRow($row, $metadata['customData'], [], [], []);

    expect($transformed['name'])->toBe('JOHN DOE');
    expect($transformed['name_original'])->toBe('John Doe');
});

it('transformRow with DateColumn formats valid dates', function () {
    $table = new class
    {
        use Columns, Data;

        public mixed $model = null;

        public string $custom_column_id = 'id';

        public function columns()
        {
            return [
                Column::make('Name', 'name'),
                DateColumn::make('Created', 'created_at')->outputFormat('M d, Y'),
            ];
        }
    };

    $table->columns = collect($table->columns());
    $row = ['id' => 1, 'name' => 'Test', 'created_at' => '2024-01-15 10:30:00'];
    $transformed = $table->transformRow($row, [], [], [], []);

    expect($transformed['created_at'])->toBe('Jan 15, 2024');
});

it('transformRow with DateColumn returns emptyValue for null dates', function () {
    $table = new class
    {
        use Columns, Data;

        public mixed $model = null;

        public string $custom_column_id = 'id';

        public function columns()
        {
            return [
                Column::make('Name', 'name'),
                DateColumn::make('Created', 'created_at')->emptyValue('N/A'),
            ];
        }
    };

    $table->columns = collect($table->columns());
    $row = ['id' => 1, 'name' => 'Test', 'created_at' => null];
    $transformed = $table->transformRow($row, [], [], [], []);

    expect($transformed['created_at'])->toBe('N/A');
});

it('transformRow with LinkColumn creates JSON-encoded href/text', function () {
    $table = new class
    {
        use Columns, Data;

        public mixed $model = null;

        public string $custom_column_id = 'id';

        public function columns()
        {
            return [
                Column::make('Name', 'name'),
                LinkColumn::make('Website', 'url')->href(function ($row) {
                    return '/users/'.$row['id'];
                })->text('Visit'),
            ];
        }
    };

    $table->columns = collect($table->columns());
    $metadata = $table->getColumnMetadata();

    expect($metadata['linkColumns'])->toHaveKey('url');

    $row = ['id' => 42, 'name' => 'Test', 'url' => 'http://example.com'];
    $transformed = $table->transformRow($row, [], $metadata['linkColumns'], [], []);

    $decoded = json_decode($transformed['url'], true);
    expect($decoded[0])->toBe('/users/42');
    expect($decoded[1])->toBe('Visit');
    expect($transformed['url_original'])->toBe('Visit');
});

it('transformRow with ToggleColumn compares with what_is_true when passed correctly', function () {
    $table = new class
    {
        use Columns, Data;

        public mixed $model = null;

        public string $custom_column_id = 'id';

        public function columns()
        {
            return [
                Column::make('Name', 'name'),
                ToggleColumn::make('Active', 'active')
                    ->disableToggleWhen(function ($row) {
                        return $row['name'] === 'Locked';
                    })
                    ->hideToggleWhen(function ($row) {
                        return $row['name'] === 'Hidden';
                    }),
            ];
        }
    };

    $table->columns = collect($table->columns());

    // Manually build the toggleColumns array in the format transformRow expects
    $toggleColumns = [
        'active' => [
            'disableToggleWhen' => function ($row) {
                return $row['name'] === 'Locked';
            },
            'hideToggleWhen' => function ($row) {
                return $row['name'] === 'Hidden';
            },
        ],
    ];

    $row = ['id' => 1, 'name' => 'Locked', 'active' => 1];
    $transformed = $table->transformRow($row, [], [], $toggleColumns, []);

    expect($transformed['active'])->toBeTrue();
    expect($transformed['active_disabled'])->toBeTrue();
    expect($transformed['active_hidden'])->toBeFalse();
});

it('getColumnMetadata returns all 4 arrays', function () {
    $table = new TestTableWithData;
    $table->columns = collect($table->columns());
    $metadata = $table->getColumnMetadata();

    expect($metadata)->toHaveKey('customData');
    expect($metadata)->toHaveKey('linkColumns');
    expect($metadata)->toHaveKey('toggleColumns');
    expect($metadata)->toHaveKey('cardTitleCallbacks');
});

it('stripModifiedRows removes _original keys and restores values', function () {
    $table = new TestTableWithData;

    $collection = collect([
        ['name' => 'MODIFIED', 'name_original' => 'Original', 'email' => 'test@test.com'],
    ]);

    $stripped = $table->stripModifiedRows($collection);

    expect($stripped->first()['name'])->toBe('Original');
    expect($stripped->first())->not->toHaveKey('name_original');
});

it('exportToClipboard generates TSV', function () {
    $table = new class
    {
        use Columns, Data;

        public mixed $model = null;

        public string $custom_column_id = 'id';

        public string $csvString = '';

        public function columns()
        {
            return [Column::make('Name', 'name'), Column::make('Email', 'email')];
        }

        public function dispatch($event, ...$args)
        {
            // no-op for testing
        }

        public function getFreshColumns()
        {
            return collect($this->columns());
        }
    };

    $collection = collect([
        ['name' => 'Alice', 'email' => 'alice@test.com'],
        ['name' => 'Bob', 'email' => 'bob@test.com'],
    ]);

    $table->exportToClipboard($collection, true);

    expect($table->csvString)->toContain("Name\tEmail");
    expect($table->csvString)->toContain("Alice\talice@test.com");
});

it('exportToClipboard generates CSV with quoting', function () {
    $table = new class
    {
        use Columns, Data;

        public mixed $model = null;

        public string $custom_column_id = 'id';

        public string $csvString = '';

        public function columns()
        {
            return [Column::make('Name', 'name'), Column::make('Email', 'email')];
        }

        public function dispatch($event, ...$args) {}

        public function getFreshColumns()
        {
            return collect($this->columns());
        }
    };

    $collection = collect([
        ['name' => 'Alice', 'email' => 'alice@test.com'],
    ]);

    $table->exportToClipboard($collection, false);

    expect($table->csvString)->toContain('"Name","Email"');
    expect($table->csvString)->toContain('"Alice","alice@test.com"');
});

it('exportToClipboard handles empty collection', function () {
    $table = new class
    {
        use Columns, Data;

        public mixed $model = null;

        public string $custom_column_id = 'id';

        public string $csvString = '';

        public function columns()
        {
            return [Column::make('Name', 'name')];
        }

        public function dispatch($event, ...$args) {}

        public function getFreshColumns()
        {
            return collect($this->columns());
        }
    };

    $table->exportToClipboard(collect(), true);

    expect($table->csvString)->toBe('');
});

it('exportToClipboard strips _original keys', function () {
    $table = new class
    {
        use Columns, Data;

        public mixed $model = null;

        public string $custom_column_id = 'id';

        public string $csvString = '';

        public function columns()
        {
            return [Column::make('Name', 'name'), Column::make('Email', 'email')];
        }

        public function dispatch($event, ...$args) {}

        public function getFreshColumns()
        {
            return collect($this->columns());
        }
    };

    $collection = collect([
        ['name' => 'ALICE', 'name_original' => 'Alice', 'email' => 'alice@test.com'],
    ]);

    $table->exportToClipboard($collection, true);

    expect($table->csvString)->not->toContain('name_original');
    expect($table->csvString)->toContain('ALICE');
});

it('exportToClipboard handles array values', function () {
    $table = new class
    {
        use Columns, Data;

        public mixed $model = null;

        public string $custom_column_id = 'id';

        public string $csvString = '';

        public function columns()
        {
            return [Column::make('Name', 'name'), Column::make('Tags', 'tags')];
        }

        public function dispatch($event, ...$args) {}

        public function getFreshColumns()
        {
            return collect($this->columns());
        }
    };

    $collection = collect([
        ['name' => 'Alice', 'tags' => ['php', 'laravel']],
    ]);

    $table->exportToClipboard($collection, true);

    expect($table->csvString)->toContain('php;laravel');
});
