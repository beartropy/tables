<?php

use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\Classes\Columns\LinkColumn;
use Beartropy\Tables\Classes\Columns\ToggleColumn;
use Beartropy\Tables\Traits\Columns;
use Beartropy\Tables\Traits\Data;

beforeEach(function () {
    Column::resetStaticKeys();
});

it('processCollection normalizes stdClass rows to ArrayObject', function () {
    $table = new class
    {
        use Columns, Data;

        public mixed $model = null;

        public string $custom_column_id = 'id';

        public function columns()
        {
            return [
                Column::make('Name', 'name'),
                Column::make('Email', 'email'),
            ];
        }
    };

    $table->columns = collect($table->columns());

    $collection = collect(json_decode('[
        {"id": 1, "name": "Alice", "email": "alice@example.com"},
        {"id": 2, "name": "Bob", "email": "bob@example.com"}
    ]'));

    $processed = $table->processCollection($collection);

    expect($processed)->toHaveCount(2);
    expect($processed->first()['name'])->toBe('Alice');
    expect($processed->last()['name'])->toBe('Bob');
});

it('transformRow passes stdClass-origin ArrayObject to customData callbacks supporting both access styles', function () {
    $objectAccess = null;
    $arrayAccess = null;

    $table = new class
    {
        use Columns, Data;

        public mixed $model = null;

        public string $custom_column_id = 'id';

        public function columns()
        {
            return [
                Column::make('Name', 'name'),
                Column::make('Email', 'email'),
            ];
        }
    };

    $table->columns = collect($table->columns());

    $customData = [
        'name' => [
            'function' => function ($row) use (&$objectAccess, &$arrayAccess) {
                $objectAccess = $row->name;
                $arrayAccess = $row['name'];

                return strtoupper($row->name);
            },
        ],
    ];

    $row = new \ArrayObject(
        ['id' => 1, 'name' => 'Alice', 'email' => 'alice@example.com'],
        \ArrayObject::ARRAY_AS_PROPS
    );

    $transformed = $table->transformRow($row, $customData, [], [], []);

    expect($objectAccess)->toBe('Alice');
    expect($arrayAccess)->toBe('Alice');
    expect($transformed['name'])->toBe('ALICE');
});

it('transformRow passes stdClass-origin ArrayObject to href callbacks', function () {
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
                    return '/users/'.$row->id;
                })->text('Visit'),
            ];
        }
    };

    $table->columns = collect($table->columns());
    $metadata = $table->getColumnMetadata();

    $row = new \ArrayObject(
        ['id' => 42, 'name' => 'Test', 'url' => 'http://example.com'],
        \ArrayObject::ARRAY_AS_PROPS
    );

    $transformed = $table->transformRow($row, [], $metadata['linkColumns'], [], []);

    $decoded = json_decode($transformed['url'], true);
    expect($decoded[0])->toBe('/users/42');
});

it('transformRow passes stdClass-origin ArrayObject to toggle callbacks', function () {
    $table = new class
    {
        use Columns, Data;

        public mixed $model = null;

        public string $custom_column_id = 'id';

        public function columns()
        {
            return [
                Column::make('Name', 'name'),
                ToggleColumn::make('Active', 'active'),
            ];
        }
    };

    $table->columns = collect($table->columns());

    $toggleColumns = [
        'active' => [
            'disableToggleWhen' => function ($row) {
                return $row->name === 'Locked';
            },
        ],
    ];

    $row = new \ArrayObject(
        ['id' => 1, 'name' => 'Locked', 'active' => 1],
        \ArrayObject::ARRAY_AS_PROPS
    );

    $transformed = $table->transformRow($row, [], [], $toggleColumns, []);

    expect($transformed['active_disabled'])->toBeTrue();
});

it('transformRow passes stdClass-origin ArrayObject to cardTitle callbacks', function () {
    $table = new class
    {
        use Columns, Data;

        public mixed $model = null;

        public string $custom_column_id = 'id';

        public function columns()
        {
            return [
                Column::make('Name', 'name')->cardTitle(function ($row) {
                    return 'Title: '.$row->name;
                }),
            ];
        }
    };

    $table->columns = collect($table->columns());
    $metadata = $table->getColumnMetadata();

    $row = new \ArrayObject(
        ['id' => 1, 'name' => 'Alice'],
        \ArrayObject::ARRAY_AS_PROPS
    );

    $transformed = $table->transformRow($row, [], [], [], $metadata['cardTitleCallbacks']);

    expect($transformed['name_card_title'])->toBe('Title: Alice');
});

it('processCollection handles nested stdClass objects recursively', function () {
    $table = new class
    {
        use Columns, Data;

        public mixed $model = null;

        public string $custom_column_id = 'id';

        public function columns()
        {
            return [
                Column::make('Name', 'name'),
                Column::make('City', 'address.city'),
            ];
        }
    };

    $table->columns = collect($table->columns());

    $row = json_decode('{"id": 1, "name": "Alice", "address": {"city": "Springfield", "zip": "12345"}}');

    $processed = $table->processCollection(collect([$row]));

    expect($processed->first()['name'])->toBe('Alice');
    expect($processed->first()['address.city'])->toBe('Springfield');
});

it('processCollection leaves plain arrays unchanged', function () {
    $table = new class
    {
        use Columns, Data;

        public mixed $model = null;

        public string $custom_column_id = 'id';

        public function columns()
        {
            return [
                Column::make('Name', 'name'),
            ];
        }
    };

    $table->columns = collect($table->columns());

    $collection = collect([
        ['id' => 1, 'name' => 'Alice'],
    ]);

    $processed = $table->processCollection($collection);

    expect($processed->first()['name'])->toBe('Alice');
});
