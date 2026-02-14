# YATBaseTable — AI Reference

## Class
- `Beartropy\Tables\YATBaseTable` extends `Livewire\Component`
- Traits: Bulk, Cache, Columns, Data, Editable, Filters, Options, Pagination, RowManipulators, Search, Sort, Spinner, StateHandler, View (14 total)

## Props (constructor)
This is a Livewire component — properties are public class properties, not constructor parameters.

| Property | PHP Type | Default | Description |
|----------|----------|---------|-------------|
| `$model` | mixed | null | Eloquent model class name or null for array-based tables |
| `$with` | array | [] | Eager load relationships for model-based tables |
| `$column_id` | string | 'id' | Primary key column name |
| `$with_pagination` | bool | true | Enable pagination |
| `$has_bulk` | bool | false | Enable bulk selection |
| `$has_counter` | bool | true | Show row counter |
| `$useGlobalSearch` | bool | true | Enable global search |
| `$show_column_toggle` | bool | true | Show column visibility toggle |
| `$useCards` | bool | false | Use card layout instead of table |
| `$showCardsOnMobile` | bool | false | Switch to cards on mobile |
| `$sticky_header` | bool | false | Make header sticky on scroll |

## Fluent Methods
N/A — This is a base class to extend, not a fluent builder.

## Usage Examples

### Model-Based Table
```php
namespace App\Livewire;

use App\Models\User;
use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\Classes\Filters\FilterString;
use Beartropy\Tables\YATBaseTable;

class UsersTable extends YATBaseTable
{
    public $model = User::class;

    public array $with = ['profile', 'roles'];

    public bool $with_pagination = true;

    public bool $has_bulk = true;

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')->sortable()->searchable(),
            Column::make('Email', 'email')->sortable()->searchable(),
            Column::make('Created', 'created_at'),
        ];
    }

    public function filters(): array
    {
        return [
            FilterString::make('Search Name', 'name'),
        ];
    }
}
```

### Array-Based Table
```php
class ProductsTable extends YATBaseTable
{
    public bool $with_pagination = false;

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

### Blade Usage
```blade
<livewire:users-table />

{{-- Show only table without wrapper --}}
<livewire:products-table :show-only-table="true" />
```

### Complete Real-World Table (Model + Relationships + Filters + Columns)
```php
namespace App\Livewire;

use App\Models\User;
use App\Models\Profile;
use App\Models\UserSetting;
use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\Classes\Columns\BoolColumn;
use Beartropy\Tables\Classes\Columns\DateColumn;
use Beartropy\Tables\Classes\Columns\LinkColumn;
use Beartropy\Tables\Classes\Columns\ToggleColumn;
use Beartropy\Tables\Classes\Filters\FilterBool;
use Beartropy\Tables\Classes\Filters\FilterDateRange;
use Beartropy\Tables\Classes\Filters\FilterSelect;
use Beartropy\Tables\Classes\Filters\FilterSelectMagic;
use Beartropy\Tables\Classes\Filters\FilterString;
use Beartropy\Tables\YATBaseTable;

class UsersTable extends YATBaseTable
{
    public $model = User::class;

    public array $with = ['profile', 'roles', 'settings'];

    public bool $with_pagination = true;
    public bool $has_bulk = true;
    public bool $showCardsOnMobile = true;

    public function columns(): array
    {
        return [
            // Basic sortable/searchable column
            Column::make('Name', 'name')
                ->sortable()
                ->searchable()
                ->cardTitle(),

            // Dot notation: auto-resolves belongsTo/hasOne relationship
            Column::make('Bio', 'profile.bio')
                ->searchable()
                ->collapseOnMobile(),

            // Virtual column from hasMany — needs custom sort & search
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

            // Link column with route
            LinkColumn::make('Email', 'email')
                ->href(fn($row) => "mailto:{$row->email}")
                ->showOnCard(),

            // Toggle column with authorization
            ToggleColumn::make('Active', 'is_active')
                ->disableToggleWhen(fn($row) => !auth()->user()->can('update', $row))
                ->centered(),

            // Bool column
            BoolColumn::make('Verified', 'email_verified_at')
                ->trueIs(fn($v) => !is_null($v))
                ->centered()
                ->hideOnMobile(),

            // Date column
            DateColumn::make('Joined', 'created_at')
                ->outputFormat('M d, Y')
                ->sortable()
                ->showOnCard(),

            // Inline editable column
            Column::make('Role', 'role')
                ->editable('select', [
                    ['value' => 'admin', 'label' => 'Admin'],
                    ['value' => 'editor', 'label' => 'Editor'],
                    ['value' => 'viewer', 'label' => 'Viewer'],
                ]),
        ];
    }

    public function filters(): array
    {
        return [
            // Basic text search
            FilterString::make('Name', 'name'),

            // Dot notation: filter on related model column
            FilterSelect::make('Bio',
                Profile::distinct()->pluck('bio')->toArray(),
                'profile.bio'
            ),

            // Magic select: auto-populates from distinct values
            FilterSelectMagic::make('Role', 'role'),

            // Custom query: filter a hasMany relationship
            FilterSelect::make('Theme',
                UserSetting::where('key', 'theme')->distinct()->pluck('value')->toArray(),
                'theme_setting'
            )->query(function($query, $value, $filter) {
                $query->whereHas('settings', function($q) use ($value) {
                    $q->where('key', 'theme')->where('value', 'like', "%$value%");
                });
            }),

            // Multi-column string search
            FilterString::make('Search')
                ->query(function($query, $value) {
                    $query->where('name', 'like', "%$value%")
                          ->orWhere('email', 'like', "%$value%");
                }),

            // Boolean filter
            FilterBool::make('Active', null, 'is_active'),

            // Date range filter
            FilterDateRange::make('Joined', 'created_at'),
        ];
    }

    // Optional: override authorizeFieldUpdate for inline editing security
    public function authorizeFieldUpdate(\Illuminate\Database\Eloquent\Model $record, string $field, mixed $value): bool
    {
        return auth()->user()->can('update', $record);
    }
}
```

## Architecture Notes

- **14 Traits Composition**: The component is composed of 14 traits that handle distinct concerns (bulk operations, caching, column management, data fetching, inline editing, filtering, options, pagination, row manipulation, search, sort, loading states, state persistence, and view rendering).
- **Two Data Modes**: Set `$model` for Eloquent-based tables, or leave null and implement `data()` for array-based tables.
- **Abstract-Like Pattern**: `columns()` must be implemented; `filters()`, `data()`, `options()`, and `settings()` are optional.
- **Livewire Lifecycle**: Uses `mount()` for initialization, `render()` for view rendering, and various trait-provided lifecycle hooks.
- **State Management**: The StateHandler trait persists filter/search/sort state across page loads.
- **Complex Property Types**: `$columns` and `$filters` are intentionally left untyped because they contain Livewire-serialized Collections with complex nested structures.
- **Query Callback Signature**: All filter `->query()` callbacks receive `($query, $value, $filter)`. The third `$filter` parameter is the filter object itself, giving access to `$filter->key`, `$filter->label`, etc. Most callbacks only need `$query` and `$value`.
- **Dot Notation in Filters**: Filters with dot-notation keys (e.g., `'profile.bio'`) are automatically resolved using `whereHas()` for model-based tables. The system splits on `.`, uses the left part as the relationship name and the right part as the column.
- **Authorize Field Update**: The `authorizeFieldUpdate()` method in the Editable trait provides a security hook for inline editing. Override it to add policy checks.

## Common Patterns

### Extending with Custom Logic
```php
class CustomTable extends YATBaseTable
{
    public function mount(): void
    {
        parent::mount();
        // Custom initialization
    }

    public function settings(): array
    {
        return [
            'some_custom_setting' => true,
        ];
    }

    public function refresh(): void
    {
        parent::refresh();
        // Custom refresh logic
    }
}
```

### Programmatic Control
```php
// In another Livewire component
$this->dispatch('refreshTable'); // Trigger table refresh

// Show only table content
<livewire:my-table :show-only-table="true" />
```

### Custom Column ID
```php
class UuidTable extends YATBaseTable
{
    public string $column_id = 'uuid';
    public $model = UuidModel::class;
}
```
