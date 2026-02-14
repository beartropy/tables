# Filter — AI Reference

## Class
- `Beartropy\Tables\Classes\Filters\Filter`
- Base class for all filter types

## Props (constructor)
| Prop | PHP Type | Default | Description |
|------|----------|---------|-------------|
| `$label` | string | required | Display label for filter |
| `$column` | ?string | null | Database column or array key to filter on |

## Fluent Methods
| Method | Return | Description |
|--------|--------|-------------|
| `query(callable $callback)` | self | Custom query logic for filter |

## Usage Examples

### Basic Filter (Extended by Subclasses)
```php
// Filter is typically not used directly, but extended by:
// FilterBool, FilterDateRange, FilterSelect, FilterSelectMagic, FilterString
```

### Query Callback Signature
```php
// The ->query() callback receives THREE parameters:
->query(function($query, $value, $filter) {
    // $query  — The Eloquent Builder instance
    // $value  — The user's input (string for FilterString, selected option for FilterSelect, etc.)
    // $filter — The filter object itself (access $filter->key, $filter->label, $filter->type, etc.)

    $query->where('column', 'like', "%$value%");
})
```

### Dot Notation for Relationship Columns
```php
// When the index/column uses dot notation, the default query
// automatically handles relationship joins via whereHas().
// Example: 'profile.bio' → whereHas('profile', fn($q) => $q->where('bio', ...))
FilterString::make('Bio', 'profile.bio')
FilterSelect::make('Bio', Profile::distinct()->pluck('bio')->toArray(), 'profile.bio')
```

### Virtual Filter with Custom Query (hasMany)
```php
// For hasMany or complex relationships where dot notation doesn't work,
// use a custom key and provide a ->query() callback.
FilterSelect::make('Theme',
    UserSetting::where('key', 'theme')->distinct()->pluck('value')->toArray(),
    'theme_setting'  // arbitrary key — not a real DB column
)->query(function($query, $value, $filter) {
    // Custom query resolves the hasMany relationship manually
    $query->whereHas('settings', function($q) use ($value) {
        $q->where('key', 'theme')->where('value', 'like', "%$value%");
    });
})
```

### FilterString Spanning Multiple Columns
```php
FilterString::make('Name')
    ->query(function($query, $value) {
        $query->where('custom_col', 'like', "%$value%")
              ->orWhere('other_col', 'like', "%$value%");
    })
```

### Custom Query Logic
```php
FilterString::make('Search', 'name')
    ->query(function ($query, $value) {
        return $query->where(function ($q) use ($value) {
            $q->where('first_name', 'like', "%{$value}%")
              ->orWhere('last_name', 'like', "%{$value}%");
        });
    })
```

### Multi-Column Filter
```php
FilterString::make('Global Search')
    ->query(function ($query, $value) {
        return $query->where(function ($q) use ($value) {
            $q->where('name', 'like', "%{$value}%")
              ->orWhere('email', 'like', "%{$value}%")
              ->orWhere('phone', 'like', "%{$value}%");
        });
    })
```

### Relationship Filter
```php
FilterSelect::make('Category', ['News', 'Blog', 'Tutorial'], 'category_id')
    ->query(function ($query, $value) {
        return $query->whereHas('category', function ($q) use ($value) {
            $q->where('name', $value);
        });
    })
```

## Architecture Notes

- **Base Class**: Filter is the abstract base class. Use specific filter types in practice: FilterBool, FilterDateRange, FilterSelect, FilterSelectMagic, FilterString.
- **Column Property**: The `$column` parameter determines which database column to filter. If null, the filter must provide custom query logic.
- **Key Generation**: The `$key` property is auto-generated for internal tracking (similar to Column).
- **Input Storage**: The `$input` property stores the user's filter input value.
- **Query Callback**: The `$queryCallback` property stores custom query logic set via `query()` method.
- **Default Behavior**: Each filter subclass implements default query logic. Use `query()` to override.
- **Fluent Pattern**: Returns `$this` for method chaining.
- **Query Callback — 3 Parameters**: `->query(fn($query, $value, $filter))`. The `$filter` parameter provides access to the filter's metadata (key, label, type, input). Most callbacks only use `$query` and `$value`.
- **Dot Notation Automatic Resolution**: When a filter key contains a `.` (e.g., `profile.bio`), `applyFiltersToQuery()` automatically splits it into a relationship name and column, then uses `whereHas()`. No custom `->query()` callback is needed for simple belongsTo/hasOne relationships.
- **Virtual Filters**: Use an arbitrary key (like `'theme_setting'`) when filtering data that doesn't map to a direct column. Always pair with a `->query()` callback that implements the actual filtering logic.

## Common Patterns

### Override Default Query
```php
FilterString::make('Name', 'name')
    ->query(function ($query, $value) {
        return $query->where('LOWER(name)', 'like', '%' . strtolower($value) . '%');
    })
```

### JSON Column Filter
```php
FilterString::make('Metadata', 'metadata->key')
    ->query(function ($query, $value) {
        return $query->where('metadata->key', 'like', "%{$value}%");
    })
```

### Computed Filter
```php
FilterBool::make('Has Orders')
    ->query(function ($query, $value) {
        if ($value === 'true') {
            return $query->has('orders');
        }
        return $query->doesntHave('orders');
    })
```

### Scope-Based Filter
```php
FilterBool::make('Active')
    ->query(function ($query, $value) {
        if ($value === 'true') {
            return $query->active();
        }
        return $query->inactive();
    })
```

### Date Comparison Filter
```php
FilterString::make('Year', 'created_at')
    ->query(function ($query, $value) {
        return $query->whereYear('created_at', $value);
    })
```
