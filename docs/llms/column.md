# Column — AI Reference

## Class
- `Beartropy\Tables\Classes\Columns\Column`
- Base class for all column types

## Props (constructor)
| Prop | PHP Type | Default | Description |
|------|----------|---------|-------------|
| `$label` | string | required | Display label for column header |
| `$index` | ?string | null | Database column or array key to fetch data from |

## Fluent Methods
| Method | Return | Description |
|--------|--------|-------------|
| `make(string $label, ?string $key = null)` | static | Static factory constructor |
| `sortable(bool\|callable $callback = true)` | self | Enable/disable sorting, or custom sort logic |
| `searchable(bool\|callable $callback = true)` | self | Enable/disable searching, or custom search logic |
| `editable(string $type = 'input', array $options = [], ?callable $onUpdate = null)` | self | Enable inline editing with specified input type |
| `setUpdateField(string $field)` | self | Set database field name for updates (if different from index) |
| `pushLeft()` | self | Align column content left |
| `pushRight()` | self | Align column content right |
| `centered()` | self | Center column content |
| `collapseOnMobile(bool $collapse = true)` | self | Collapse to show label + value on mobile |
| `cardTitle(bool\|callable $callback = true)` | self | Use this column as card title in card view |
| `triggerCardInfoModal(bool $trigger = true)` | self | Open info modal when card title clicked |
| `showOnCard(bool $show = true)` | self | Show column in card view |
| `hideOnMobile(bool $hide = true)` | self | Hide column on mobile devices |
| `showOnMobile(bool $show = true)` | self | Force show column on mobile |
| `view(string $view)` | self | Use custom Blade view for rendering |
| `styling(string $classes)` | self | Add CSS classes to td element |
| `thStyling(string $classes)` | self | Add CSS classes to th element |
| `thWrapperStyling(string $classes)` | self | Add CSS classes to th wrapper |
| `customData(Closure $callback)` | self | Transform data with custom callback |
| `hideWhen(bool $condition)` | self | Hide column when condition is true |
| `hideFromSelector(bool $hide = true)` | self | Hide from column visibility selector |
| `isVisible(bool $visible = true)` | self | Set column visibility |
| `secondaryHeader(callable $callback)` | self | Add a secondary header row with computed content (receives `$rows` Collection) |
| `sortColumnBy(string $column)` | self | Specify different column name for sorting |
| `toHtml()` | self | Mark column output as raw HTML (no escaping) |
| `isBool()` | self | Mark column as boolean type |
| `trueIs(mixed $value)` | self | Define what value represents true |
| `trueLabel(string $label)` | self | Set label for true value |
| `falseLabel(string $label)` | self | Set label for false value |

## Usage Examples

### Basic Column
```php
Column::make('Name', 'name')
```

### Sortable and Searchable
```php
Column::make('Email', 'email')
    ->sortable()
    ->searchable()
```

### Custom Data Transformation
```php
Column::make('Full Name')
    ->customData(fn($row) => $row->first_name . ' ' . $row->last_name)
```

### Inline Editing
```php
Column::make('Status', 'status')
    ->editable('select', [
        'options' => ['pending', 'approved', 'rejected']
    ])
```

### Custom Sort Logic
```php
Column::make('Priority', 'priority')
    ->sortable(function ($query, $direction) {
        return $query->orderByRaw("FIELD(priority, 'high', 'medium', 'low') {$direction}");
    })
```

### Custom Search Logic
```php
Column::make('Full Name')
    ->customData(fn($row) => $row->first_name . ' ' . $row->last_name)
    ->searchable(function ($query, $search) {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%");
        });
    })
```

### Mobile Responsive
```php
Column::make('Description', 'description')
    ->hideOnMobile()

Column::make('Title', 'title')
    ->collapseOnMobile()
    ->cardTitle()
```

### Secondary Header (Aggregation Row)
```php
Column::make('Price', 'price')
    ->sortable()
    ->secondaryHeader(function ($rows) {
        return 'Subtotal: $' . number_format($rows->sum('price'), 2);
    })
```

### Styling
```php
Column::make('Price', 'price')
    ->styling('font-bold text-green-600')
    ->pushRight()
```

### Different Update Field
```php
Column::make('Category', 'category.name')
    ->editable('select', ['options' => $categories])
    ->setUpdateField('category_id')
```

### Relationship Column with Dot Notation
```php
// Dot notation automatically resolves Eloquent relationships.
// 'profile.bio' fetches $user->profile->bio and handles the join automatically.
Column::make('Bio', 'profile.bio')
    ->sortable()
    ->searchable()
```

### Virtual Column with Relationship Sorting & Searching
```php
// When a column displays data from a hasMany or complex relationship,
// you need custom sort and search callbacks because dot notation
// only works for belongsTo/hasOne.
Column::make('Theme', 'theme_setting')
    ->customData(fn($row) => $row->settings->firstWhere('key', 'theme')?->value ?? '—')
    ->sortable(function($query, $direction) {
        // Subquery sort: order the parent query by a value from a related table
        $query->orderBy(
            \App\Models\UserSetting::select('value')
                ->whereColumn('user_settings.user_id', 'users.id')
                ->where('key', 'theme'),
            $direction
        );
    })
    ->searchable(function($query, $term) {
        // Search within a hasMany relationship
        $query->orWhereHas('settings', function($q) use ($term) {
            $q->where('key', 'theme')->where('value', 'like', "%$term%");
        });
    })
```

### Editable with Select Options from Model
```php
Column::make('Status', 'status')
    ->editable('select', [
        ['value' => 'draft', 'label' => 'Draft'],
        ['value' => 'published', 'label' => 'Published'],
        ['value' => 'archived', 'label' => 'Archived'],
    ])
    ->setUpdateField('status')  // explicit DB field if different from display key
```

### Editable with Custom Callback
```php
Column::make('Priority', 'priority')
    ->editable('select', [1 => 'Low', 2 => 'Medium', 3 => 'High'], 'handlePriorityUpdate')
    // 'handlePriorityUpdate' is a method on your table component:
    // public function handlePriorityUpdate($id, $field, $value, $table) { ... }
```

## Architecture Notes

- **Fluent Builder Pattern**: All configuration methods return `$this` for method chaining.
- **Index vs Key**: The `$index` parameter determines which data field to display. The `$key` property is auto-generated for internal tracking.
- **Custom Data**: When `customData()` is set, `$has_modified_data` is marked true and the callback overrides raw data display.
- **Visibility Logic**: `$isVisible` and `$isHidden` work together; `hideWhen()` sets `$isHidden` based on runtime conditions.
- **Mobile Behavior**: Three distinct mobile strategies: `hideOnMobile()`, `showOnMobile()`, and `collapseOnMobile()`.
- **Card View**: Separate properties control card title (`$cardTitle`), modal trigger (`$triggerCardInfoModal`), and visibility (`$showOnCard`).
- **HTML Safety**: By default, column output is escaped. Use `toHtml()` to render raw HTML.
- **Edit Callbacks**: The `$editableCallback` is triggered when inline editing completes, receiving the row, new value, and column.
- **Dot Notation**: Columns with dot-notation keys like `'profile.bio'` automatically resolve Eloquent relationships using `data_get()`. This works for belongsTo and hasOne. For hasMany or complex relationships, use `customData()` + custom `sortable()`/`searchable()` callbacks.
- **Callback Signatures**: `sortable(fn($query, $direction))` receives the Eloquent builder and sort direction. `searchable(fn($query, $term))` receives the builder and search term. Both operate within an `orWhere` group.
- **Editable Callbacks**: The `editable()` third parameter can be a Closure `fn($id, $field, $value, $table)` or a string method name on the table component. String callbacks invoke `$this->{$methodName}(...)`.
- **Secondary Header**: `secondaryHeader(fn($rows))` receives the current page's rows as a Collection. The callback return value is rendered in a second header row below the main header. Output supports HTML (rendered with `{!! !!}`). The secondary header row only appears when at least one column defines it; columns without a callback render an empty cell.

## Common Patterns

### Computed Column (No Index)
```php
Column::make('Actions')
    ->customData(fn($row) => view('partials.actions', compact('row')))
    ->toHtml()
    ->hideFromSelector()
```

### Boolean Display
```php
Column::make('Active', 'is_active')
    ->isBool()
    ->trueLabel('Yes')
    ->falseLabel('No')
```

### Conditional Visibility
```php
Column::make('Admin Notes', 'admin_notes')
    ->hideWhen(!auth()->user()->isAdmin())
```

### Sort by Related Column
```php
Column::make('Category', 'category.name')
    ->sortColumnBy('category_id')
```

### Secondary Header with Multiple Columns
```php
// Only columns with secondaryHeader() show content; others render empty cells.
Column::make('Quantity', 'qty')
    ->secondaryHeader(fn($rows) => 'Items: ' . $rows->sum('qty')),

Column::make('Price', 'price')
    ->secondaryHeader(fn($rows) => '<strong>$' . number_format($rows->sum('price'), 2) . '</strong>'),
```
