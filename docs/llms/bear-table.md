# BeartropyTable — AI Reference

> **Note**: Previously named `YATBaseTable`. The old name still works via `class_alias`.

## Class
- `Beartropy\Tables\BeartropyTable` extends `Livewire\Component`
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
| `$theme` | string | 'gray' | Table color theme |
| `$stripRows` | bool | true | Enable alternating row striping |

## Settings Methods

Call these from your table's `settings()` method (or `mount()`) to configure behavior.

### View & Theming

| Method | Signature | Description |
|--------|-----------|-------------|
| `setTheme` | `setTheme(string $theme)` | Set the table color theme (e.g. `'gray'`, `'beartropy'`). Loads the theme's preset config. |
| `setComponentSize` | `setComponentSize(?string $size)` | Uniformly resize all beartropy-ui components in the table header (e.g. `'sm'`, `'md'`, `'lg'`, `'xl'`). Pass `null` to reset. |
| `setBulkThemeOverride` | `setBulkThemeOverride(?string $theme)` | Override the theme for bulk action UI elements. |
| `setButtonThemeOverride` | `setButtonThemeOverride(?string $theme)` | Override the theme for buttons. Default: `'beartropy'`. |
| `setInputThemeOverride` | `setInputThemeOverride(?string $theme)` | Override the theme for input elements. Default: `'beartropy'`. |
| `setTitle` | `setTitle(string $title)` | Display a title above the table. |
| `overrideTitleClasses` | `overrideTitleClasses(string $classes)` | Override the CSS classes for the title element. |
| `setCustomHeader` | `setCustomHeader(string $html)` | Inject custom HTML content into the table header area. |
| `setButtonVariant` | `setButtonVariant(string $variant)` | Set the button style variant (default: `'outline'`). |
| `stripRows` | `stripRows(bool $strip = true)` | Enable or disable alternating row striping. |
| `setStickyHeader` | `setStickyHeader()` | Enable sticky table header on scroll. |

### Layout & CSS

| Method | Signature | Description |
|--------|-----------|-------------|
| `setComponentClasses` | `setComponentClasses(string $classes)` | Set CSS classes on the outermost component wrapper div. |
| `addTableClasses` | `addTableClasses(string $classes)` | Append CSS classes to the table element (adds to defaults). |
| `setTableClasses` | `setTableClasses(string $classes)` | Replace all CSS classes on the table element (overrides defaults). |
| `setLayout` | `setLayout(mixed $layout)` | Set the main layout view for the component render. |

### View Slots (Custom Blade Partials)

| Method | Signature | Description |
|--------|-----------|-------------|
| `setModalsView` | `setModalsView(string $view)` | Set a Blade view for custom modals rendered inside the table component. |
| `setMostLeftView` | `setMostLeftView(string $view)` | Inject a Blade view in the leftmost header slot. |
| `setLessLeftView` | `setLessLeftView(string $view)` | Inject a Blade view in the inner-left header slot. |
| `setMostRightView` | `setMostRightView(string $view)` | Inject a Blade view in the rightmost header slot. |
| `setLessRightView` | `setLessRightView(string $view)` | Inject a Blade view in the inner-right header slot. |

### Header Buttons

| Method | Signature | Description |
|--------|-----------|-------------|
| `addButtons` | `addButtons(array $buttons)` | Add custom buttons to the table header area. Each button is an array with `label`, `action`, etc. |
| `addCardModalButtons` | `addCardModalButtons(array $buttons)` | Add buttons to the mobile card detail modal. |
| `showOptionsOnlyOnRowSelect` | `showOptionsOnlyOnRowSelect(bool $value = true)` | Only show the options menu when at least one row is selected. |

### Search

| Method | Signature | Description |
|--------|-----------|-------------|
| `useGlobalSearch` | `useGlobalSearch(bool $status = true)` | Enable or disable the global search input. |
| `setSearchLabel` | `setSearchLabel(string $label)` | Set a custom placeholder label for the search input. |

### Pagination

| Method | Signature | Description |
|--------|-----------|-------------|
| `usePagination` | `usePagination(bool $bool)` | Enable or disable pagination. |
| `setPerPageDefault` | `setPerPageDefault(int $number)` | Set the default items per page. Pass `0` to show all items ('Total'). |
| `setPerPageOptions` | `setPerPageOptions(array $array)` | Set the per-page dropdown options (e.g. `['10', '25', '50', 'Total']`). |

### Columns

| Method | Signature | Description |
|--------|-----------|-------------|
| `setColumnID` | `setColumnID(string $column_id)` | Set a custom primary key column (e.g. `'uuid'`). Used for row identification. |
| `showColumnToggle` | `showColumnToggle(bool $bool)` | Show or hide the column visibility toggle dropdown. |
| `showCounter` | `showCounter(bool $bool)` | Show or hide the row counter column. |

### Bulk Actions

| Method | Signature | Description |
|--------|-----------|-------------|
| `hasBulk` | `hasBulk(bool $bool)` | Enable or disable bulk selection checkboxes. |
| `getSelectedRows` | `getSelectedRows(): array` | Get the array of currently selected row IDs. |
| `emptySelection` | `emptySelection()` | Clear all selected rows. |
| `selectCurrentPage` | `selectCurrentPage(bool $value)` | Select or deselect all rows on the current page. |
| `select_all_data` | `select_all_data(bool $value)` | Select or deselect all rows across all pages (after filters). |

### Card & Mobile

| Method | Signature | Description |
|--------|-----------|-------------|
| `showCardsOnMobile` | `showCardsOnMobile(bool $bool = true)` | Switch to card layout on mobile devices. |
| `useCards` | `useCards(bool $bool = true)` | Use card layout on all devices. |

### Spinner / Loading

| Method | Signature | Description |
|--------|-----------|-------------|
| `useTableSpinner` | `useTableSpinner(bool $bool)` | Enable or disable the loading spinner overlay. |
| `setTableSpinnerView` | `setTableSpinnerView(string $view)` | Set a custom Blade view for the loading spinner. |
| `addTargetsToSpinner` | `addTargetsToSpinner(array $targets)` | Add Livewire method/property names that trigger the spinner (appended to defaults). |

### State Persistence

| Method | Signature | Description |
|--------|-----------|-------------|
| `useStateHandler` | `useStateHandler(bool $bool)` | Enable persisting column visibility to the `yat_user_table_config` database table. Requires the published migration. |
| `setHandlerPrefix` | `setHandlerPrefix(string $string)` | Set a prefix to differentiate state keys when the same table class is used in multiple contexts. |
| `saveTableState` | `saveTableState()` | Manually save the current column visibility state to the database. |

### Cache

| Method | Signature | Description |
|--------|-----------|-------------|
| `setCachePrefix` | `setCachePrefix(string $string)` | Set a prefix for cache keys (useful when the same table class is used in multiple places). |
| `clearData` | `clearData()` | Clear the cached data for this table instance. |

### Sort

| Method | Signature | Description |
|--------|-----------|-------------|
| `setSortColumn` | `setSortColumn(string $column)` | Set the initial sort column key. |
| `setSortDirectionAsc` | `setSortDirectionAsc(bool $bool)` | Set sort direction to ascending. |
| `setSortDirectionDesc` | `setSortDirectionDesc(bool $bool)` | Set sort direction to descending. |

### Row Manipulation

| Method | Signature | Description |
|--------|-----------|-------------|
| `removeRowFromTable` | `removeRowFromTable(mixed $id, bool $resetSelected = true)` | Remove a row by ID from the table data and cache. |
| `addRowToTable` | `addRowToTable(array $row)` | Add a new row to the table data and cache. |
| `updateRowOnTable` | `updateRowOnTable(mixed $id, array $newData)` | Update specific fields on a row by ID. |
| `toggleExpandedRow` | `toggleExpandedRow(mixed $rowId, mixed $content, bool $is_component = false)` | Toggle an expandable detail row. When `$is_component` is true, `$content` must be `['component' => '...', 'parameters' => [...]]`. |

### Data Access

| Method | Signature | Description |
|--------|-----------|-------------|
| `getAllData` | `getAllData(): Collection` | Get all table data (before filters/search). |
| `getAfterFiltersData` | `getAfterFiltersData(): Collection` | Get data after search, filters, and sorting are applied. |
| `getSelectedData` | `getSelectedData(): Collection` | Get data for selected rows only. |
| `getCurrentPageData` | `getCurrentPageData(): Collection` | Get data for the current page only. |
| `getRowByID` | `getRowByID(mixed $id): mixed` | Get a single transformed row by its ID. |
| `getAllOriginalData` | `getAllOriginalData(): Collection` | Get all data with `customData` transformations stripped (original values). |
| `getAfterFiltersOriginalData` | `getAfterFiltersOriginalData(): Collection` | Get filtered data with transformations stripped. |
| `getSelectedOriginalData` | `getSelectedOriginalData(): Collection` | Get selected data with transformations stripped. |
| `exportToClipboard` | `exportToClipboard(Collection $collection, bool $tabs = true)` | Export data to clipboard. Dispatches `copy-yatable-to-clipboard` browser event. TSV by default, pass `false` for CSV. |

### Inline Editing

| Method | Signature | Description |
|--------|-----------|-------------|
| `authorizeFieldUpdate` | `authorizeFieldUpdate(Model $record, string $field, mixed $value): bool` | Override to add authorization checks for inline edits. Returns `true` by default. |
| `updateField` | `updateField(mixed $id, string $field, mixed $value): bool` | Handle an inline edit update. Supports callback, Eloquent, and array modes. |

### Filters

| Method | Signature | Description |
|--------|-----------|-------------|
| `clearAllFilters` | `clearAllFilters(bool $selectAll = false)` | Clear all active filters and the global search. Optionally select all data after clearing. |

## Overridable Methods

| Method | Return | Description |
|--------|--------|-------------|
| `columns()` | `array` | **Required.** Return an array of Column objects defining the table structure. |
| `filters()` | `array` | Return an array of Filter objects. Optional. |
| `data()` | `array` | Return array data for array-based tables (when `$model` is null). Supports arrays of associative arrays or stdClass objects. |
| `options()` | `array` | Return bulk action options (string labels or `['label' => '...', 'icon' => '...']`). |
| `settings()` | `void` | Called during `mount()`. Use to call settings methods like `setTheme()`, `setPerPageDefault()`, etc. |

## Livewire Events

| Event | Payload | Description |
|-------|---------|-------------|
| `yatDataGathered` | — | Dispatched after data is parsed (array mode). |
| `yatDataGatheredWithData` | — | Dispatched when parsed data is non-empty. |
| `yatDataGatheredEmpty` | — | Dispatched when parsed data is empty. |
| `tableStateSaved` | `bool $success` | Dispatched after saving column visibility state. |
| `table-field-updated` | `id, field, value` | Dispatched when a field is updated via inline editing. |
| `copy-yatable-to-clipboard` | `csv: string` | Browser event dispatched with clipboard content. |
| `refreshTable` | — | Listen for this to trigger a table refresh from other components. |

## Usage Examples

### Model-Based Table
```php
namespace App\Livewire;

use App\Models\User;
use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\Classes\Filters\FilterString;
use Beartropy\Tables\BeartropyTable;

class UsersTable extends BeartropyTable
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
class ProductsTable extends BeartropyTable
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

### Array-Based Table with stdClass Data

stdClass objects (e.g. from `json_decode()` or API responses) are automatically normalized to associative arrays. Nested objects are converted recursively.

```php
class ApiProductsTable extends BeartropyTable
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
        // stdClass objects from json_decode() work directly
        return json_decode('[{"id":1,"name":"Widget","price":9.99},{"id":2,"name":"Gadget","price":19.99}]');
    }
}
```

### Blade Usage
```blade
<livewire:users-table />

{{-- Show only table without wrapper --}}
<livewire:products-table :show-only-table="true" />
```

### Using Settings Methods
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
        $this->setPerPageOptions(['10', '25', '50', '100']);
        $this->setSearchLabel('Search users...');
        $this->stripRows(false);
        $this->useStateHandler(true);
        $this->setHandlerPrefix('admin');
        $this->setButtonThemeOverride('gray');
        $this->setInputThemeOverride('gray');
    }

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')->sortable()->searchable(),
            Column::make('Email', 'email'),
        ];
    }
}
```

### Custom Header Slots
```php
public function settings(): void
{
    $this->setMostLeftView('partials.table-logo');
    $this->setMostRightView('partials.table-actions');
    $this->setModalsView('partials.table-modals');
    $this->addButtons([
        ['label' => 'Export', 'action' => 'exportData', 'icon' => 'heroicon-o-arrow-down-tray'],
        ['label' => 'Create', 'action' => 'create', 'icon' => 'heroicon-o-plus'],
    ]);
}
```

### Expandable Rows
```php
// Simple HTML content
public function showDetails($rowId)
{
    $row = $this->getRowByID($rowId);
    $html = "<div class='p-4'>Details for {$row['name']}</div>";
    $this->toggleExpandedRow($rowId, $html);
}

// Livewire component
public function showDetails($rowId)
{
    $this->toggleExpandedRow($rowId, [
        'component' => 'user-detail-card',
        'parameters' => ['userId' => $rowId],
    ], is_component: true);
}
```

### Data Export
```php
public function exportAll()
{
    $data = $this->getAfterFiltersData();
    $this->exportToClipboard($data); // TSV
}

public function exportSelected()
{
    $data = $this->getSelectedData();
    $this->exportToClipboard($data, tabs: false); // CSV
}
```

### Complete Real-World Table (Model + Relationships + Filters + Settings)
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
use Beartropy\Tables\BeartropyTable;

class UsersTable extends BeartropyTable
{
    public $model = User::class;

    public array $with = ['profile', 'roles', 'settings'];

    public bool $with_pagination = true;
    public bool $has_bulk = true;
    public bool $showCardsOnMobile = true;

    public function settings(): void
    {
        $this->setTheme('beartropy');
        $this->setComponentSize('sm');
        $this->setTitle('Users');
        $this->setPerPageDefault(25);
        $this->useStateHandler(true);
    }

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')
                ->sortable()
                ->searchable()
                ->cardTitle(),

            Column::make('Bio', 'profile.bio')
                ->searchable()
                ->collapseOnMobile(),

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

            LinkColumn::make('Email', 'email')
                ->href(fn($row) => "mailto:{$row->email}")
                ->showOnCard(),

            ToggleColumn::make('Active', 'is_active')
                ->disableToggleWhen(fn($row) => !auth()->user()->can('update', $row))
                ->centered(),

            BoolColumn::make('Verified', 'email_verified_at')
                ->trueIs(fn($v) => !is_null($v))
                ->centered()
                ->hideOnMobile(),

            DateColumn::make('Joined', 'created_at')
                ->outputFormat('M d, Y')
                ->sortable()
                ->showOnCard(),

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
            FilterString::make('Name', 'name'),

            FilterSelect::make('Bio',
                Profile::distinct()->pluck('bio')->toArray(),
                'profile.bio'
            ),

            FilterSelectMagic::make('Role', 'role'),

            FilterSelect::make('Theme',
                UserSetting::where('key', 'theme')->distinct()->pluck('value')->toArray(),
                'theme_setting'
            )->query(function($query, $value, $filter) {
                $query->whereHas('settings', function($q) use ($value) {
                    $q->where('key', 'theme')->where('value', 'like', "%$value%");
                });
            }),

            FilterString::make('Search')
                ->query(function($query, $value) {
                    $query->where('name', 'like', "%$value%")
                          ->orWhere('email', 'like', "%$value%");
                }),

            FilterBool::make('Active', null, 'is_active'),

            FilterDateRange::make('Joined', 'created_at'),
        ];
    }

    public function options(): array
    {
        return [
            'Deactivate',
            ['label' => 'Delete', 'icon' => 'heroicon-o-trash'],
        ];
    }

    public function authorizeFieldUpdate(\Illuminate\Database\Eloquent\Model $record, string $field, mixed $value): bool
    {
        return auth()->user()->can('update', $record);
    }
}
```

## Architecture Notes

- **14 Traits Composition**: The component is composed of 14 traits that handle distinct concerns (bulk operations, caching, column management, data fetching, inline editing, filtering, options, pagination, row manipulation, search, sort, loading states, state persistence, and view rendering).
- **Two Data Modes**: Set `$model` for Eloquent-based tables, or leave null and implement `data()` for array-based tables. Array-mode accepts both associative arrays and stdClass objects (nested stdClass is recursively converted).
- **Abstract-Like Pattern**: `columns()` must be implemented; `filters()`, `data()`, `options()`, and `settings()` are optional.
- **Livewire Lifecycle**: Uses `mount()` for initialization, `render()` for view rendering, and various trait-provided lifecycle hooks.
- **State Management**: The StateHandler trait persists filter/search/sort state across page loads via the `yat_user_table_config` database table.
- **Complex Property Types**: `$columns` and `$filters` are intentionally left untyped because they contain Livewire-serialized Collections with complex nested structures.
- **Query Callback Signature**: All filter `->query()` callbacks receive `($query, $value, $filter)`. The third `$filter` parameter is the filter object itself, giving access to `$filter->key`, `$filter->label`, etc. Most callbacks only need `$query` and `$value`.
- **Dot Notation in Filters**: Filters with dot-notation keys (e.g., `'profile.bio'`) are automatically resolved using `whereHas()` for model-based tables. The system splits on `.`, uses the left part as the relationship name and the right part as the column.
- **Authorize Field Update**: The `authorizeFieldUpdate()` method in the Editable trait provides a security hook for inline editing. Override it to add policy checks.
- **Cache Strategy**: Array-based tables cache data per-user for 60 minutes. Use `clearData()` to invalidate manually.
- **Header Layout**: The table header has 4 named slots (mostLeft, lessLeft, lessRight, mostRight) plus the search input and buttons area.
