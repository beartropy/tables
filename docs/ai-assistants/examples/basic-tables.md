# Basic Table Examples - Beartropy Tables

Ready-to-use table examples using Beartropy Tables components.

## Simple Model-Based Table

### Livewire Component
```php
<?php

namespace App\Livewire;

use App\Models\User;
use Beartropy\Tables\BeartropyTable;
use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\Classes\Columns\DateColumn;

class UsersTable extends BeartropyTable
{
    public $model = User::class;

    public bool $with_pagination = true;

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')
                ->sortable()
                ->searchable(),

            Column::make('Email', 'email')
                ->sortable()
                ->searchable(),

            DateColumn::make('Created', 'created_at')
                ->outputFormat('M d, Y')
                ->sortable(),
        ];
    }
}
```

### Blade Template
```blade
<div class="max-w-6xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Users</h1>

    <livewire:users-table />
</div>
```

---

## Array-Based Table (No Model)

### Livewire Component
```php
<?php

namespace App\Livewire;

use Beartropy\Tables\BeartropyTable;
use Beartropy\Tables\Classes\Columns\Column;

class ProductsTable extends BeartropyTable
{
    public bool $with_pagination = false;

    public function columns(): array
    {
        return [
            Column::make('Product', 'name')
                ->sortable()
                ->searchable(),

            Column::make('Category', 'category')
                ->sortable(),

            Column::make('Price', 'price')
                ->sortable()
                ->pushRight()
                ->styling('font-bold text-green-600'),

            Column::make('Stock', 'stock')
                ->centered(),
        ];
    }

    public function data(): array
    {
        return [
            ['id' => 1, 'name' => 'Widget', 'category' => 'Tools', 'price' => '$9.99', 'stock' => 150],
            ['id' => 2, 'name' => 'Gadget', 'category' => 'Electronics', 'price' => '$19.99', 'stock' => 75],
            ['id' => 3, 'name' => 'Doohickey', 'category' => 'Tools', 'price' => '$4.99', 'stock' => 300],
            ['id' => 4, 'name' => 'Thingamajig', 'category' => 'Electronics', 'price' => '$29.99', 'stock' => 42],
        ];
    }
}
```

### Blade Template
```blade
<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Product Inventory</h1>

    <livewire:products-table />
</div>
```

---

## Array-Based Table with stdClass Data

stdClass objects (e.g. from `json_decode()` or API responses) are automatically normalized to associative arrays. Nested objects are converted recursively, so `customData` callbacks and all other row accessors receive plain arrays.

### Livewire Component
```php
<?php

namespace App\Livewire;

use Beartropy\Tables\BeartropyTable;
use Beartropy\Tables\Classes\Columns\Column;
use Illuminate\Support\Facades\Http;

class ApiProductsTable extends BeartropyTable
{
    public bool $with_pagination = false;

    public function columns(): array
    {
        return [
            Column::make('Product', 'name')
                ->sortable()
                ->searchable(),

            Column::make('Price', 'price')
                ->sortable()
                ->pushRight()
                ->customData(fn ($row) => '$' . number_format($row['price'], 2)),
        ];
    }

    public function data(): array
    {
        // json_decode() returns stdClass objects by default — works directly
        return json_decode('[
            {"id": 1, "name": "Widget", "price": 9.99},
            {"id": 2, "name": "Gadget", "price": 19.99},
            {"id": 3, "name": "Doohickey", "price": 4.99}
        ]');
    }
}
```

---

## Table with Relationships (Eager Loading)

### Livewire Component
```php
<?php

namespace App\Livewire;

use App\Models\Post;
use Beartropy\Tables\BeartropyTable;
use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\Classes\Columns\BoolColumn;
use Beartropy\Tables\Classes\Columns\DateColumn;

class PostsTable extends BeartropyTable
{
    public $model = Post::class;

    public array $with = ['author', 'category'];

    public bool $with_pagination = true;

    public function columns(): array
    {
        return [
            Column::make('Title', 'title')
                ->sortable()
                ->searchable(),

            // Dot notation resolves belongsTo relationship
            Column::make('Author', 'author.name')
                ->sortable()
                ->searchable(),

            // Another relationship column
            Column::make('Category', 'category.name')
                ->sortable(),

            BoolColumn::make('Published', 'is_published')
                ->centered(),

            DateColumn::make('Published At', 'published_at')
                ->outputFormat('M d, Y')
                ->sortable(),
        ];
    }
}
```

---

## Table with Mobile Card View

### Livewire Component
```php
<?php

namespace App\Livewire;

use App\Models\Contact;
use Beartropy\Tables\BeartropyTable;
use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\Classes\Columns\LinkColumn;

class ContactsTable extends BeartropyTable
{
    public $model = Contact::class;

    public bool $showCardsOnMobile = true;

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')
                ->sortable()
                ->searchable()
                ->cardTitle(),

            LinkColumn::make('Email', 'email')
                ->href(fn($row) => "mailto:{$row->email}")
                ->showOnCard(),

            Column::make('Phone', 'phone')
                ->showOnCard(),

            Column::make('Company', 'company')
                ->hideOnMobile()
                ->collapseOnMobile(),

            Column::make('Notes', 'notes')
                ->hideOnMobile(),
        ];
    }
}
```

---

## Table with Custom Column ID

### Livewire Component
```php
<?php

namespace App\Livewire;

use App\Models\Product;
use Beartropy\Tables\BeartropyTable;
use Beartropy\Tables\Classes\Columns\Column;

class UuidProductsTable extends BeartropyTable
{
    public $model = Product::class;

    public string $column_id = 'uuid';

    public function columns(): array
    {
        return [
            Column::make('SKU', 'sku')->sortable(),
            Column::make('Name', 'name')->sortable()->searchable(),
            Column::make('Price', 'price')->sortable()->pushRight(),
        ];
    }
}
```

---

## Show-Only Table (Embedded Without Wrapper)

### Blade Template
```blade
<div class="max-w-6xl mx-auto p-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Recent Activity</h2>

        {{-- Table renders without its own wrapper/chrome --}}
        <livewire:activity-table :show-only-table="true" />
    </div>
</div>
```

---

These examples cover the most common table patterns. Customize columns, filters, and properties based on your specific needs!
