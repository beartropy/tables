# Beartropy Tables - Universal AI Assistant Guide

> This guide helps AI assistants generate correct code using Beartropy Tables for Laravel/Livewire applications.

## Overview

**Beartropy Tables** is a feature-rich data table component for the TALL stack (Tailwind, Alpine, Laravel, Livewire).

- **Two Data Modes**: Model-based (Eloquent) or array-based (any data source)
- **5 Column Types**: Column, BoolColumn, DateColumn, LinkColumn, ToggleColumn
- **6 Filter Types**: FilterString, FilterSelect, FilterBool, FilterDateRange, FilterSelectMagic, Filter (base)
- **Built-in Features**: Search, sort, pagination, bulk actions, inline editing, export, card view, mobile responsive

## Architecture

All tables extend `BeartropyTable`, a Livewire component composed of 14 traits:

```
BeartropyTable extends Livewire\Component
├── Bulk          — Row selection & bulk operations
├── Cache         — Data caching
├── Columns       — Column management
├── Data          — Data fetching & transformation
├── Editable      — Inline editing
├── Filters       — Filter application
├── Options       — Component options
├── Pagination    — Pagination controls
├── RowManipulators — Add/remove/update rows
├── Search        — Global search
├── Sort          — Column sorting
├── Spinner       — Loading states
├── StateHandler  — State persistence
└── View          — Rendering & theming
```

## Creating a Table

### Step 1: Create a Livewire Component

```php
namespace App\Livewire;

use Beartropy\Tables\BeartropyTable;
use Beartropy\Tables\Classes\Columns\Column;

class UsersTable extends BeartropyTable
{
    public $model = \App\Models\User::class;

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')->sortable()->searchable(),
            Column::make('Email', 'email')->sortable()->searchable(),
        ];
    }
}
```

Or scaffold with artisan:

```bash
php artisan make:btable UsersTable
php artisan make:btable UsersTable --model=App\\Models\\User
```

### Step 2: Use in Blade

```blade
<livewire:users-table />
```

## Table Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `$model` | mixed | null | Eloquent model class (null for array tables) |
| `$with` | array | [] | Eager load relationships |
| `$column_id` | string | 'id' | Primary key column name |
| `$with_pagination` | bool | true | Enable pagination |
| `$has_bulk` | bool | false | Enable bulk selection |
| `$has_counter` | bool | true | Show row counter |
| `$useGlobalSearch` | bool | true | Enable global search |
| `$show_column_toggle` | bool | true | Show column visibility toggle |
| `$useCards` | bool | false | Use card layout |
| `$showCardsOnMobile` | bool | false | Cards on mobile |
| `$sticky_header` | bool | false | Sticky table header |
| `$theme` | string | 'gray' | Table color theme |
| `$stripRows` | bool | true | Alternating row striping |

## Settings Methods

Call these from `settings()` to configure behavior programmatically:

```php
public function settings(): void
{
    // Theming
    $this->setTheme('beartropy');
    $this->setComponentSize('sm');       // Resize all header UI components
    $this->setButtonThemeOverride('gray');
    $this->setInputThemeOverride('gray');
    $this->setBulkThemeOverride('gray');
    $this->setButtonVariant('outline');
    $this->stripRows(false);

    // Title & Header
    $this->setTitle('User Management');
    $this->overrideTitleClasses('text-2xl font-bold');
    $this->setCustomHeader('<div>Custom HTML</div>');

    // Layout & CSS
    $this->setComponentClasses('rounded-lg shadow');
    $this->addTableClasses('min-w-full');
    $this->setStickyHeader();
    $this->setLayout('layouts.admin');

    // Header slots (inject custom Blade views)
    $this->setMostLeftView('partials.table-logo');
    $this->setLessLeftView('partials.table-filters');
    $this->setLessRightView('partials.table-stats');
    $this->setMostRightView('partials.table-actions');
    $this->setModalsView('partials.table-modals');

    // Buttons
    $this->addButtons([
        ['label' => 'Export', 'action' => 'exportData'],
    ]);
    $this->showOptionsOnlyOnRowSelect(true);

    // Search
    $this->useGlobalSearch(true);
    $this->setSearchLabel('Search users...');

    // Pagination
    $this->usePagination(true);
    $this->setPerPageDefault(25);
    $this->setPerPageOptions(['10', '25', '50', '100', 'Total']);

    // Columns
    $this->setColumnID('uuid');
    $this->showColumnToggle(true);
    $this->showCounter(false);

    // Sort
    $this->setSortColumn('name');
    $this->setSortDirectionDesc(true);

    // Spinner
    $this->useTableSpinner(true);
    $this->setTableSpinnerView('partials.custom-spinner');
    $this->addTargetsToSpinner(['customAction']);

    // State persistence (requires migration)
    $this->useStateHandler(true);
    $this->setHandlerPrefix('admin');

    // Cache
    $this->setCachePrefix('admin-users');
}
```

## Column Types

### Column (Base)

```php
use Beartropy\Tables\Classes\Columns\Column;

// Basic
Column::make('Name', 'name')

// With sorting and searching
Column::make('Email', 'email')
    ->sortable()
    ->searchable()

// Custom data transformation
Column::make('Full Name')
    ->customData(fn($row) => $row->first_name . ' ' . $row->last_name)

// Relationship via dot notation
Column::make('Bio', 'profile.bio')
    ->sortable()
    ->searchable()

// Inline editing
Column::make('Status', 'status')
    ->editable('select', [
        ['value' => 'draft', 'label' => 'Draft'],
        ['value' => 'published', 'label' => 'Published'],
    ])

// Styling
Column::make('Price', 'price')
    ->pushRight()
    ->styling('font-bold text-green-600')

// Secondary header (aggregation row below the main header)
Column::make('Price', 'price')
    ->sortable()
    ->secondaryHeader(fn($rows) => 'Subtotal: $' . number_format($rows->sum('price'), 2))

// Visibility
Column::make('Admin Notes', 'admin_notes')
    ->hideWhen(!auth()->user()->isAdmin())

// Mobile
Column::make('Description', 'description')
    ->hideOnMobile()
    ->collapseOnMobile()

// Card view
Column::make('Title', 'title')
    ->cardTitle()
    ->showOnCard()
```

**Key Fluent Methods:**

| Method | Description |
|--------|-------------|
| `sortable(bool\|callable)` | Enable sorting or provide custom sort logic |
| `searchable(bool\|callable)` | Enable searching or provide custom search logic |
| `editable(string $type, array $options, ?callable $onUpdate)` | Inline editing |
| `setUpdateField(string $field)` | DB field for updates (if different from index) |
| `pushLeft()` / `pushRight()` / `centered()` | Alignment |
| `customData(Closure $cb)` | Transform displayed data |
| `view(string $view)` | Custom Blade view |
| `styling(string $classes)` | TD CSS classes |
| `thStyling(string $classes)` | TH CSS classes |
| `thWrapperStyling(string $classes)` | TH wrapper CSS classes |
| `hideWhen(bool $cond)` | Conditional visibility |
| `hideFromSelector(bool)` | Hide from column toggle |
| `isVisible(bool)` | Set initial visibility |
| `sortColumnBy(string $column)` | Sort by a different column key |
| `toHtml()` | Render as raw HTML |
| `secondaryHeader(callable $callback)` | Add a secondary header row with computed content (receives `$rows` Collection) |
| `hideOnMobile()` / `showOnMobile()` / `collapseOnMobile()` | Mobile behavior |
| `cardTitle()` / `showOnCard()` | Card view config |
| `triggerCardInfoModal(bool)` | Disable card title tap opening info modal |

### BoolColumn

```php
use Beartropy\Tables\Classes\Columns\BoolColumn;

BoolColumn::make('Verified', 'email_verified_at')
    ->trueIs(fn($v) => !is_null($v))
    ->trueLabel('Verified')
    ->falseLabel('Unverified')
    ->centered()
```

### DateColumn

```php
use Beartropy\Tables\Classes\Columns\DateColumn;

DateColumn::make('Created', 'created_at')
    ->outputFormat('M d, Y')
    ->inputFormat('Y-m-d H:i:s')
    ->emptyValue('N/A')
    ->sortable()
```

### LinkColumn

```php
use Beartropy\Tables\Classes\Columns\LinkColumn;

LinkColumn::make('Email', 'email')
    ->href(fn($row) => "mailto:{$row->email}")
    ->text(fn($row) => $row->email)
    ->target('_blank')
    ->classes('text-blue-600 hover:underline')
    ->popup(['width' => 750, 'height' => 800])
```

### ToggleColumn

```php
use Beartropy\Tables\Classes\Columns\ToggleColumn;

ToggleColumn::make('Active', 'is_active')
    ->disableToggleWhen(fn($row) => !auth()->user()->can('update', $row))
    ->hideToggleWhen(fn($row) => $row->is_system_user)
    ->trigger('handleToggle')
    ->centered()
```

## Filter Types

### FilterString

```php
use Beartropy\Tables\Classes\Filters\FilterString;

// Basic text filter
FilterString::make('Name', 'name')

// Multi-column search
FilterString::make('Search')
    ->query(function ($query, $value) {
        $query->where('name', 'like', "%$value%")
              ->orWhere('email', 'like', "%$value%");
    })

// Relationship filter with dot notation
FilterString::make('Bio', 'profile.bio')
```

### FilterSelect

```php
use Beartropy\Tables\Classes\Filters\FilterSelect;

// Basic select
FilterSelect::make('Status', ['pending', 'approved', 'rejected'], 'status')

// Associative options
FilterSelect::make('Role', [
    'admin' => 'Administrator',
    'editor' => 'Editor',
    'viewer' => 'Viewer'
], 'role')

// From model
FilterSelect::make('Category', Category::pluck('name', 'id')->toArray(), 'category_id')

// With custom query
FilterSelect::make('Department', Department::pluck('name', 'id')->toArray(), 'department_id')
    ->query(function ($query, $value) {
        $query->whereHas('department', fn($q) => $q->where('id', $value));
    })
```

### FilterBool

```php
use Beartropy\Tables\Classes\Filters\FilterBool;

FilterBool::make('Active', null, 'is_active')

// Custom query
FilterBool::make('Has Orders')
    ->query(function ($query, $value) {
        if ($value === 'true') {
            return $query->has('orders');
        }
        return $query->doesntHave('orders');
    })
```

### FilterDateRange

```php
use Beartropy\Tables\Classes\Filters\FilterDateRange;

FilterDateRange::make('Created', 'created_at')
```

### FilterSelectMagic

Auto-populates options from distinct column values:

```php
use Beartropy\Tables\Classes\Filters\FilterSelectMagic;

FilterSelectMagic::make('Role', 'role')
```

## Data Access Methods

Use these in your table component for custom actions:

```php
// Get data sets
$all = $this->getAllData();                  // All data (before filters)
$filtered = $this->getAfterFiltersData();   // After search/filters/sort
$selected = $this->getSelectedData();       // Selected rows only
$page = $this->getCurrentPageData();        // Current page only
$row = $this->getRowByID($id);             // Single row by ID

// Original data (strips customData transformations)
$original = $this->getAllOriginalData();
$filteredOriginal = $this->getAfterFiltersOriginalData();
$selectedOriginal = $this->getSelectedOriginalData();

// Export to clipboard
$this->exportToClipboard($filtered);              // TSV
$this->exportToClipboard($selected, tabs: false); // CSV

// Row manipulation
$this->addRowToTable(['id' => 99, 'name' => 'New']);
$this->updateRowOnTable($id, ['status' => 'active']);
$this->removeRowFromTable($id);

// Bulk selection
$ids = $this->getSelectedRows();
$this->emptySelection();
$this->selectCurrentPage(true);

// Expandable rows
$this->toggleExpandedRow($rowId, '<div>Details</div>');
$this->toggleExpandedRow($rowId, [
    'component' => 'detail-card',
    'parameters' => ['id' => $rowId],
], is_component: true);

// Filters
$this->clearAllFilters();

// Cache
$this->clearData();
```

## Complete Examples

### Model-Based Table with All Features

```php
namespace App\Livewire;

use App\Models\User;
use Beartropy\Tables\BeartropyTable;
use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\Classes\Columns\BoolColumn;
use Beartropy\Tables\Classes\Columns\DateColumn;
use Beartropy\Tables\Classes\Columns\LinkColumn;
use Beartropy\Tables\Classes\Columns\ToggleColumn;
use Beartropy\Tables\Classes\Filters\FilterString;
use Beartropy\Tables\Classes\Filters\FilterSelect;
use Beartropy\Tables\Classes\Filters\FilterBool;
use Beartropy\Tables\Classes\Filters\FilterDateRange;
use Beartropy\Tables\Classes\Filters\FilterSelectMagic;

class UsersTable extends BeartropyTable
{
    public $model = User::class;

    public array $with = ['profile', 'roles'];

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

            LinkColumn::make('Email', 'email')
                ->href(fn($row) => "mailto:{$row->email}")
                ->showOnCard(),

            Column::make('Bio', 'profile.bio')
                ->searchable()
                ->collapseOnMobile(),

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
            FilterSelectMagic::make('Role', 'role'),
            FilterBool::make('Active', null, 'is_active'),
            FilterDateRange::make('Joined', 'created_at'),
        ];
    }

    public function authorizeFieldUpdate(\Illuminate\Database\Eloquent\Model $record, string $field, mixed $value): bool
    {
        return auth()->user()->can('update', $record);
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
            Column::make('Product', 'name')->sortable()->searchable(),
            Column::make('Price', 'price')->sortable()->pushRight(),
            Column::make('Stock', 'stock')->centered(),
        ];
    }

    public function data(): array
    {
        return [
            ['id' => 1, 'name' => 'Widget', 'price' => 9.99, 'stock' => 150],
            ['id' => 2, 'name' => 'Gadget', 'price' => 19.99, 'stock' => 75],
            ['id' => 3, 'name' => 'Doohickey', 'price' => 4.99, 'stock' => 300],
        ];
    }
}
```

`data()` also accepts stdClass objects (e.g. from `json_decode()` or API responses) — they are automatically normalized to associative arrays, including nested objects:

```php
public function data(): array
{
    $response = Http::get('https://api.example.com/products');
    return json_decode($response->body()); // returns array of stdClass — works directly
}
```

### Blade Usage

```blade
{{-- Standard usage --}}
<livewire:users-table />

{{-- Show only table without wrapper --}}
<livewire:products-table :show-only-table="true" />
```

## Custom Sort and Search Callbacks

### Custom Sort (Relationship Column)

```php
Column::make('Theme', 'theme_setting')
    ->customData(fn($row) => $row->settings->firstWhere('key', 'theme')?->value ?? '-')
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
    })
```

### Filter Custom Query

```php
// Query callback receives ($query, $value, $filter) — 3 parameters
FilterSelect::make('Type', ['all', 'recent', 'archived'], 'type')
    ->query(function ($query, $value) {
        return match ($value) {
            'recent' => $query->recent(),
            'archived' => $query->archived(),
            default => $query,
        };
    })
```

## Dot Notation

Columns and filters with dot notation (e.g., `'profile.bio'`) automatically resolve Eloquent relationships:

```php
// Column: fetches $user->profile->bio via data_get()
Column::make('Bio', 'profile.bio')->sortable()->searchable()

// Filter: applies whereHas('profile', fn($q) => $q->where('bio', ...))
FilterString::make('Bio', 'profile.bio')
```

This works for `belongsTo` and `hasOne`. For `hasMany` or complex relationships, use `customData()` + custom callbacks.

## Inline Editing

```php
// Text input editing
Column::make('Name', 'name')->editable()

// Select dropdown editing
Column::make('Status', 'status')
    ->editable('select', [
        ['value' => 'draft', 'label' => 'Draft'],
        ['value' => 'published', 'label' => 'Published'],
    ])

// With callback
Column::make('Priority', 'priority')
    ->editable('select', [1 => 'Low', 2 => 'Medium', 3 => 'High'], 'handlePriorityUpdate')
```

Override authorization:

```php
public function authorizeFieldUpdate(\Illuminate\Database\Eloquent\Model $record, string $field, mixed $value): bool
{
    return auth()->user()->can('update', $record);
}
```

## Best Practices

1. **Always extend `BeartropyTable`** — never build table components from scratch
2. **Use `$with` for eager loading** — prevent N+1 queries
3. **Use dot notation** for simple relationships (belongsTo/hasOne)
4. **Use `customData()` + custom callbacks** for hasMany/complex relationships
5. **Add `sortable()` and `searchable()`** to key columns
6. **Use filter `->query()` callbacks** when default behavior isn't sufficient
7. **Override `authorizeFieldUpdate()`** for inline editing security
8. **Set `$has_bulk = true`** to enable bulk operations
9. **Use `$showCardsOnMobile = true`** for mobile-friendly tables
10. **Use `FilterSelectMagic`** when options come from distinct column values
11. **Use `settings()`** for programmatic configuration instead of setting props directly
12. **Use `setComponentSize('sm')`** for compact table headers

## Requirements

- PHP >= 8.2
- Laravel >= 11.x
- Livewire 3.x
- Tailwind CSS
- Alpine.js (included with Livewire 3)

## Installation

```bash
composer require beartropy/tables
php artisan vendor:publish --tag=migrations
php artisan migrate
```

---

**This guide provides all necessary information for AI assistants to generate correct Beartropy Tables code.** Use component names, column types, filter types, and patterns exactly as shown above.
