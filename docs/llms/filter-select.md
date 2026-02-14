# FilterSelect — AI Reference

## Class
- `Beartropy\Tables\Classes\Filters\FilterSelect` extends `Filter`

## Props (constructor)
| Prop | PHP Type | Default | Description |
|------|----------|---------|-------------|
| `$label` | string | required | Display label for filter |
| `$options` | array | required | Array of options for select dropdown |
| `$index` | ?string | null | Database column or array key to filter on |

## Fluent Methods
Inherits from `Filter`:

| Method | Return | Description |
|--------|--------|-------------|
| `make(string $label, array $options, ?string $index = null)` | static | Static factory constructor |
| `query(callable $callback)` | self | Custom query logic for filter |

## Usage Examples

### Basic Select Filter
```php
FilterSelect::make('Status', ['pending', 'approved', 'rejected'], 'status')
```

### Associative Options
```php
FilterSelect::make('Role', [
    'admin' => 'Administrator',
    'editor' => 'Editor',
    'viewer' => 'Viewer'
], 'role')
```

### Numeric Options
```php
FilterSelect::make('Priority', [
    1 => 'High',
    2 => 'Medium',
    3 => 'Low'
], 'priority')
```

### From Model
```php
FilterSelect::make('Category', Category::pluck('name', 'id')->toArray(), 'category_id')
```

### Custom Query Logic
```php
FilterSelect::make('Status', ['active', 'inactive'], 'status')
    ->query(function ($query, $value) {
        if ($value === 'active') {
            return $query->where('is_active', true);
        }
        return $query->where('is_active', false);
    })
```

### Relationship Filter
```php
FilterSelect::make('Department', Department::pluck('name', 'id')->toArray(), 'department_id')
    ->query(function ($query, $value) {
        return $query->whereHas('department', function ($q) use ($value) {
            $q->where('id', $value);
        });
    })
```

### Dot Notation for Related Model Column
```php
// When the third parameter uses dot notation (e.g., 'profile.bio'),
// the default query automatically handles the relationship join.
// The table splits on '.', uses 'profile' as the relationship name
// and 'bio' as the column within that relationship.
FilterSelect::make('Bio',
    \App\Models\Profile::distinct()->pluck('bio')->toArray(),
    'profile.bio'
)
```

### HasMany Relationship with Custom Query
```php
// For hasMany or polymorphic relationships, dot notation doesn't work.
// Use a virtual key + custom query callback.
// The callback receives ($query, $value, $filter) — three parameters.
FilterSelect::make('Theme',
    \App\Models\UserSetting::where('key', 'theme')
        ->distinct()
        ->pluck('value')
        ->toArray(),
    'theme_setting'  // virtual key — not a real DB column
)->query(function($query, $value, $filter) {
    // $value is the selected option from the dropdown
    $query->whereHas('settings', function($q) use ($value) {
        $q->where('key', 'theme')->where('value', 'like', "%$value%");
    });
})
```

### Paired Column + Filter for Virtual Field
```php
// In columns():
Column::make('Theme', 'theme_setting')
    ->customData(fn($row) => $row->settings->firstWhere('key', 'theme')?->value ?? '—')
    ->sortable(function($query, $direction) {
        $query->orderBy(
            \App\Models\UserSetting::select('value')
                ->whereColumn('user_settings.user_id', 'users.id')
                ->where('key', 'theme'),
            $direction
        );
    })

// In filters() — same virtual key, matching query logic:
FilterSelect::make('Theme',
    \App\Models\UserSetting::where('key', 'theme')->distinct()->pluck('value')->toArray(),
    'theme_setting'
)->query(function($query, $value, $filter) {
    $query->whereHas('settings', function($q) use ($value) {
        $q->where('key', 'theme')->where('value', 'like', "%$value%");
    });
})
```

## Architecture Notes

- **Type Property**: The `$type` property is set to `'select'`, which determines the UI component rendered.
- **Options Property**: The `$options` array stores the dropdown options. Keys are the values stored/compared, values are the display labels.
- **Required Options**: Unlike other filter types, FilterSelect requires the `$options` parameter in the constructor.
- **Default Query Behavior**: By default, FilterSelect queries using `WHERE column = value`, where value is the selected option key.
- **UI Rendering**: Renders as a select dropdown with an empty/"All" option plus all provided options.
- **Array vs Associative**:
  - Indexed array: `['pending', 'approved']` → both key and value are 'pending', 'approved'
  - Associative array: `['p' => 'Pending', 'a' => 'Approved']` → 'p' is stored, 'Pending' is displayed
- **Custom Logic**: Use `query()` method to override default WHERE behavior.
- **Query Callback — 3 Parameters**: `->query(fn($query, $value, $filter))`. The third `$filter` parameter is the filter object itself, giving access to `$filter->key`, `$filter->label`, etc. Most callbacks only need `$query` and `$value`.
- **Dot Notation**: When the index uses dot notation (e.g., `'profile.bio'`), `applyFiltersToQuery()` automatically resolves it using `whereHas()`. This works for belongsTo/hasOne relationships. For hasMany, use a virtual key + `->query()`.

## Common Patterns

### Boolean-Like Select
```php
FilterSelect::make('Active', [
    1 => 'Yes',
    0 => 'No'
], 'is_active')
```

### Status Filter
```php
FilterSelect::make('Order Status', [
    'pending' => 'Pending',
    'processing' => 'Processing',
    'completed' => 'Completed',
    'cancelled' => 'Cancelled'
], 'status')
```

### Enum-Based Filter
```php
FilterSelect::make('Type', [
    'individual' => 'Individual',
    'business' => 'Business',
    'nonprofit' => 'Non-Profit'
], 'account_type')
```

### Dynamic Options from Relationship
```php
// In your table component
public function filters(): array
{
    return [
        FilterSelect::make('Author',
            User::where('is_author', true)->pluck('name', 'id')->toArray(),
            'author_id'
        ),
    ];
}
```

### Multiple Column Filter
```php
FilterSelect::make('Status', ['draft', 'published'], 'status')
    ->query(function ($query, $value) {
        if ($value === 'published') {
            return $query->whereNotNull('published_at');
        }
        return $query->whereNull('published_at');
    })
```

### Scope-Based Filter
```php
FilterSelect::make('Type', ['all', 'recent', 'archived'], 'type')
    ->query(function ($query, $value) {
        return match ($value) {
            'recent' => $query->recent(),
            'archived' => $query->archived(),
            default => $query,
        };
    })
```

### JSON Column Filter
```php
FilterSelect::make('Language', [
    'en' => 'English',
    'es' => 'Spanish',
    'fr' => 'French'
], 'preferences->language')
```

### Cached Options
```php
// In your table component
public function filters(): array
{
    $categories = Cache::remember('category-options', 3600, function () {
        return Category::pluck('name', 'id')->toArray();
    });

    return [
        FilterSelect::make('Category', $categories, 'category_id'),
    ];
}
```
