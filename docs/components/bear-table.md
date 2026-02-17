# BeartropyTable

> **Note**: Previously named `YATBaseTable`. The old name still works via `class_alias`.

The base Livewire component for creating data tables with support for sorting, filtering, searching, pagination, and bulk operations.

## Basic Usage

```php
namespace App\Livewire;

use App\Models\User;
use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\BeartropyTable;

class UsersTable extends BeartropyTable
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
| `$theme` | string | 'gray' | Table color theme |
| `$stripRows` | bool | true | Enable alternating row striping |

## Overridable Methods

| Method | Return | Description |
|--------|--------|-------------|
| `columns()` | `array` | **Required.** Define the table's column structure. |
| `filters()` | `array` | Define table filters. Optional. |
| `data()` | `array` | Provide array data (when `$model` is null). Supports arrays of associative arrays or stdClass objects. |
| `options()` | `array` | Define bulk action options. |
| `settings()` | `void` | Configure the table via settings methods. Called during `mount()`. |
| `authorizeFieldUpdate(Model, string, mixed)` | `bool` | Authorization check for inline edits. Returns `true` by default. |

## Settings Methods

Call these in your `settings()` method to configure the table programmatically.

### View & Theming

| Method | Description |
|--------|-------------|
| `setTheme(string $theme)` | Set the table color theme (e.g. `'gray'`, `'beartropy'`). |
| `setComponentSize(?string $size)` | Resize all header UI components (`'sm'`, `'md'`, `'lg'`, `'xl'`, or `null`). |
| `setBulkThemeOverride(?string $theme)` | Override theme for bulk action UI. |
| `setButtonThemeOverride(?string $theme)` | Override theme for buttons. |
| `setInputThemeOverride(?string $theme)` | Override theme for inputs. |
| `setTitle(string $title)` | Display a title above the table. |
| `overrideTitleClasses(string $classes)` | Override title CSS classes. |
| `setCustomHeader(string $html)` | Inject custom HTML into the header. |
| `setButtonVariant(string $variant)` | Set button style variant (default: `'outline'`). |
| `stripRows(bool $strip)` | Toggle alternating row striping. |
| `setStickyHeader()` | Enable sticky header on scroll. |

### Layout & CSS

| Method | Description |
|--------|-------------|
| `setComponentClasses(string $classes)` | Set CSS classes on the outermost wrapper. |
| `addTableClasses(string $classes)` | Append CSS classes to the table element. |
| `setTableClasses(string $classes)` | Replace all CSS classes on the table element. |
| `setLayout(mixed $layout)` | Set the Livewire layout view. |

### Header Slots

| Method | Description |
|--------|-------------|
| `setModalsView(string $view)` | Set a Blade view for modals inside the component. |
| `setMostLeftView(string $view)` | Inject a Blade view in the leftmost header slot. |
| `setLessLeftView(string $view)` | Inject a Blade view in the inner-left header slot. |
| `setMostRightView(string $view)` | Inject a Blade view in the rightmost header slot. |
| `setLessRightView(string $view)` | Inject a Blade view in the inner-right header slot. |

### Header Buttons

| Method | Description |
|--------|-------------|
| `addButtons(array $buttons)` | Add custom buttons to the table header. |
| `addCardModalButtons(array $buttons)` | Add buttons to the mobile card detail modal. |
| `showOptionsOnlyOnRowSelect(bool $value)` | Only show options when rows are selected. |

### Search

| Method | Description |
|--------|-------------|
| `useGlobalSearch(bool $status)` | Enable or disable global search. |
| `setSearchLabel(string $label)` | Set search input placeholder text. |

### Pagination

| Method | Description |
|--------|-------------|
| `usePagination(bool $bool)` | Enable or disable pagination. |
| `setPerPageDefault(int $number)` | Set default items per page (0 = show all). |
| `setPerPageOptions(array $options)` | Set per-page dropdown options. |

### Columns & Counter

| Method | Description |
|--------|-------------|
| `setColumnID(string $column_id)` | Set a custom primary key column. |
| `showColumnToggle(bool $bool)` | Show/hide the column visibility toggle. |
| `showCounter(bool $bool)` | Show/hide the row counter. |

### Bulk Actions

| Method | Description |
|--------|-------------|
| `hasBulk(bool $bool)` | Enable/disable bulk selection. |
| `getSelectedRows(): array` | Get selected row IDs. |
| `emptySelection()` | Clear all selected rows. |
| `selectCurrentPage(bool $value)` | Select/deselect all rows on the current page. |
| `select_all_data(bool $value)` | Select/deselect all matching rows across pages. |

### Card & Mobile

| Method | Description |
|--------|-------------|
| `showCardsOnMobile(bool $bool)` | Use cards on mobile devices. |
| `useCards(bool $bool)` | Use cards on all devices. |

### Loading Spinner

| Method | Description |
|--------|-------------|
| `useTableSpinner(bool $bool)` | Enable/disable the loading spinner. |
| `setTableSpinnerView(string $view)` | Set a custom spinner Blade view. |
| `addTargetsToSpinner(array $targets)` | Add Livewire targets that trigger the spinner. |

### State Persistence

| Method | Description |
|--------|-------------|
| `useStateHandler(bool $bool)` | Enable persisting column visibility to the database. |
| `setHandlerPrefix(string $string)` | Set prefix for state keys (for multiple table instances). |
| `saveTableState()` | Manually save column visibility state. |

### Cache

| Method | Description |
|--------|-------------|
| `setCachePrefix(string $string)` | Set cache key prefix. |
| `clearData()` | Clear cached data for this table. |

### Sort

| Method | Description |
|--------|-------------|
| `setSortColumn(string $column)` | Set the initial sort column. |
| `setSortDirectionAsc(bool $bool)` | Set sort direction to ascending. |
| `setSortDirectionDesc(bool $bool)` | Set sort direction to descending. |

### Row Manipulation

| Method | Description |
|--------|-------------|
| `removeRowFromTable(mixed $id, bool $resetSelected = true)` | Remove a row from the table data. |
| `addRowToTable(array $row)` | Add a new row to the table data. |
| `updateRowOnTable(mixed $id, array $newData)` | Update fields on a row. |
| `toggleExpandedRow(mixed $rowId, mixed $content, bool $is_component = false)` | Toggle expandable row details. |

### Data Access

| Method | Description |
|--------|-------------|
| `getAllData(): Collection` | Get all data (before filters). |
| `getAfterFiltersData(): Collection` | Get data after search/filters/sort. |
| `getSelectedData(): Collection` | Get data for selected rows. |
| `getCurrentPageData(): Collection` | Get data for the current page. |
| `getRowByID(mixed $id): mixed` | Get a single row by ID. |
| `exportToClipboard(Collection $data, bool $tabs = true)` | Export data to clipboard (TSV/CSV). |

### Filters

| Method | Description |
|--------|-------------|
| `clearAllFilters(bool $selectAll = false)` | Clear all active filters and search. |

## Examples

### Model-Based Table with Relationships

```php
class OrdersTable extends BeartropyTable
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
class ProductsTable extends BeartropyTable
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

### Array-Based Table with stdClass Data

stdClass objects (e.g. from `json_decode()` or API responses) are automatically normalized to associative arrays. Nested objects are converted recursively.

```php
class ApiProductsTable extends BeartropyTable
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
        // stdClass objects from json_decode() work directly
        return json_decode('[{"id":1,"name":"Widget","price":9.99},{"id":2,"name":"Gadget","price":19.99}]');
    }
}
```

### Table with Settings

```php
class UsersTable extends BeartropyTable
{
    public $model = User::class;

    public function settings(): void
    {
        $this->setTheme('beartropy');
        $this->setComponentSize('sm');
        $this->setTitle('User Management');
        $this->setPerPageDefault(25);
        $this->setPerPageOptions(['10', '25', '50', 'Total']);
        $this->setSearchLabel('Search users...');
        $this->useStateHandler(true);
        $this->stripRows(false);
        $this->setButtonThemeOverride('gray');
    }

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')->sortable()->searchable(),
            Column::make('Email', 'email')->sortable(),
        ];
    }
}
```

### Table with Filters

```php
use Beartropy\Tables\Classes\Filters\FilterSelect;
use Beartropy\Tables\Classes\Filters\FilterString;

class UsersTable extends BeartropyTable
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

### Custom Header Slots

```php
class DashboardTable extends BeartropyTable
{
    public $model = Metric::class;

    public function settings(): void
    {
        $this->setMostLeftView('partials.table-logo');
        $this->setMostRightView('partials.export-buttons');
        $this->setModalsView('partials.table-modals');
        $this->addButtons([
            ['label' => 'Export', 'action' => 'exportData'],
            ['label' => 'Refresh', 'action' => 'refresh'],
        ]);
    }

    public function columns(): array
    {
        return [
            Column::make('Metric', 'name'),
            Column::make('Value', 'value')->pushRight(),
        ];
    }
}
```

### Expandable Rows

```php
class OrdersTable extends BeartropyTable
{
    public $model = Order::class;

    public function columns(): array
    {
        return [
            Column::make('Order #', 'id'),
            Column::make('Total', 'total'),
        ];
    }

    // Simple HTML expansion
    public function showDetails($rowId)
    {
        $row = $this->getRowByID($rowId);
        $html = view('partials.order-details', ['order' => $row])->render();
        $this->toggleExpandedRow($rowId, $html);
    }

    // Livewire component expansion
    public function showComponent($rowId)
    {
        $this->toggleExpandedRow($rowId, [
            'component' => 'order-detail-card',
            'parameters' => ['orderId' => $rowId],
        ], is_component: true);
    }
}
```

### Card View for Mobile

```php
class ProductsTable extends BeartropyTable
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

### Data Export

```php
class ReportsTable extends BeartropyTable
{
    public $model = Report::class;

    public function exportFiltered()
    {
        $data = $this->getAfterFiltersData();
        $this->exportToClipboard($data); // TSV format
    }

    public function exportSelectedAsCsv()
    {
        $data = $this->getSelectedData();
        $this->exportToClipboard($data, tabs: false); // CSV format
    }

    public function columns(): array
    {
        return [
            Column::make('Title', 'title'),
            Column::make('Date', 'created_at'),
        ];
    }
}
```

### Custom Primary Key

```php
class UuidTable extends BeartropyTable
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
use Beartropy\Tables\BeartropyTable;

class UsersTable extends BeartropyTable
{
    public $model = User::class;
    public array $with = ['profile', 'settings'];
    public bool $has_bulk = true;

    public function settings(): void
    {
        $this->setTheme('beartropy');
        $this->setComponentSize('sm');
        $this->setPerPageDefault(25);
        $this->useStateHandler(true);
    }

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
