# YATBaseTable

The base Livewire component for creating data tables with support for sorting, filtering, searching, pagination, and bulk operations.

## Basic Usage

```php
namespace App\Livewire;

use App\Models\User;
use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\YATBaseTable;

class UsersTable extends YATBaseTable
{
    public $model = User::class;

    public function columns(): array
    {
        return [
            Column::make('Name', 'name'),
            Column::make('Email', 'email'),
        ];
    }
}
```

```blade
<livewire:users-table />
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `$model` | mixed | null | Eloquent model class name (set for model-based tables) |
| `$with` | array | [] | Relationships to eager load |
| `$column_id` | string | 'id' | Primary key column name |
| `$with_pagination` | bool | true | Enable pagination |
| `$has_bulk` | bool | false | Enable bulk selection checkbox |
| `$has_counter` | bool | true | Show row counter column |
| `$useGlobalSearch` | bool | true | Enable global search input |
| `$show_column_toggle` | bool | true | Show column visibility toggle button |
| `$useCards` | bool | false | Use card layout instead of table |
| `$showCardsOnMobile` | bool | false | Switch to cards on mobile devices |
| `$sticky_header` | bool | false | Make table header sticky when scrolling |

## Fluent Methods

N/A — This is a base class you extend, not a builder.

## Examples

### Model-Based Table with Relationships

```php
class OrdersTable extends YATBaseTable
{
    public $model = Order::class;

    public array $with = ['customer', 'items'];

    public bool $has_bulk = true;

    public function columns(): array
    {
        return [
            Column::make('Order #', 'id'),
            Column::make('Customer', 'customer.name'),
            Column::make('Total', 'total'),
            Column::make('Status', 'status'),
        ];
    }
}
```

### Array-Based Table

```php
class ProductsTable extends YATBaseTable
{
    public function columns(): array
    {
        return [
            Column::make('Product', 'name'),
            Column::make('Price', 'price'),
        ];
    }

    public function data(): array
    {
        return [
            ['id' => 1, 'name' => 'Widget', 'price' => 9.99],
            ['id' => 2, 'name' => 'Gadget', 'price' => 19.99],
        ];
    }
}
```

### Table with Filters

```php
use Beartropy\Tables\Classes\Filters\FilterSelect;
use Beartropy\Tables\Classes\Filters\FilterString;

class UsersTable extends YATBaseTable
{
    public $model = User::class;

    public function columns(): array
    {
        return [
            Column::make('Name', 'name'),
            Column::make('Email', 'email'),
            Column::make('Role', 'role'),
        ];
    }

    public function filters(): array
    {
        return [
            FilterString::make('Search Name', 'name'),
            FilterSelect::make('Role', ['admin', 'editor', 'viewer'], 'role'),
        ];
    }
}
```

### Card View for Mobile

```php
class ProductsTable extends YATBaseTable
{
    public $model = Product::class;

    public bool $showCardsOnMobile = true;

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')->cardTitle(),
            Column::make('Price', 'price')->showOnCard(),
            Column::make('Stock', 'stock')->showOnCard(),
        ];
    }
}
```

### Custom Primary Key

```php
class UuidTable extends YATBaseTable
{
    public $model = Item::class;

    public string $column_id = 'uuid';

    public function columns(): array
    {
        return [
            Column::make('Name', 'name'),
        ];
    }
}
```

### Show Only Table Content

```blade
{{-- Hide the table wrapper/container --}}
<livewire:users-table :show-only-table="true" />
```

### Complete Table with Relationships and Virtual Columns

```php
use App\Models\User;
use App\Models\Profile;
use App\Models\UserSetting;
use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\Classes\Columns\ToggleColumn;
use Beartropy\Tables\Classes\Columns\DateColumn;
use Beartropy\Tables\Classes\Filters\FilterSelect;
use Beartropy\Tables\Classes\Filters\FilterSelectMagic;
use Beartropy\Tables\Classes\Filters\FilterString;
use Beartropy\Tables\Classes\Filters\FilterDateRange;
use Beartropy\Tables\YATBaseTable;

class UsersTable extends YATBaseTable
{
    public $model = User::class;
    public array $with = ['profile', 'settings'];
    public bool $has_bulk = true;

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')->sortable()->searchable()->cardTitle(),

            // Dot notation for belongsTo
            Column::make('Bio', 'profile.bio')->searchable(),

            // Virtual column from hasMany
            Column::make('Theme', 'theme_setting')
                ->customData(fn($row) => $row->settings->firstWhere('key', 'theme')?->value ?? '—')
                ->sortable(function($query, $direction) {
                    $query->orderBy(
                        UserSetting::select('value')
                            ->whereColumn('user_settings.user_id', 'users.id')
                            ->where('key', 'theme'),
                        $direction
                    );
                })
                ->searchable(function($query, $term) {
                    $query->orWhereHas('settings', function($q) use ($term) {
                        $q->where('key', 'theme')->where('value', 'like', "%$term%");
                    });
                }),

            ToggleColumn::make('Active', 'is_active')->centered(),
            DateColumn::make('Joined', 'created_at')->outputFormat('M d, Y'),
        ];
    }

    public function filters(): array
    {
        return [
            FilterString::make('Name', 'name'),

            // Dot notation filter
            FilterSelect::make('Bio',
                Profile::distinct()->pluck('bio')->toArray(),
                'profile.bio'
            ),

            // Virtual filter for hasMany
            FilterSelect::make('Theme',
                UserSetting::where('key', 'theme')->distinct()->pluck('value')->toArray(),
                'theme_setting'
            )->query(function($query, $value, $filter) {
                $query->whereHas('settings', function($q) use ($value) {
                    $q->where('key', 'theme')->where('value', 'like', "%$value%");
                });
            }),

            FilterSelectMagic::make('Role', 'role'),
            FilterDateRange::make('Joined', 'created_at'),
        ];
    }

    public function authorizeFieldUpdate(\Illuminate\Database\Eloquent\Model $record, string $field, mixed $value): bool
    {
        return auth()->user()->can('update', $record);
    }
}
```
