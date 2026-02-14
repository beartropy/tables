# FilterString — AI Reference

## Class
- `Beartropy\Tables\Classes\Filters\FilterString` extends `Filter`

## Props (constructor)
| Prop | PHP Type | Default | Description |
|------|----------|---------|-------------|
| `$label` | string | required | Display label for filter |
| `$index` | ?string | null | Database column or array key to filter on |

## Fluent Methods
Inherits from `Filter`:

| Method | Return | Description |
|--------|--------|-------------|
| `make(string $label, ?string $index = null)` | static | Static factory constructor |
| `query(callable $callback)` | self | Custom query logic for filter |

## Usage Examples

### Basic String Filter
```php
FilterString::make('Name', 'name')
```

### Email Filter
```php
FilterString::make('Email', 'email')
```

### Custom Query Logic
```php
FilterString::make('Search', 'name')
    ->query(function ($query, $value) {
        return $query->where('name', 'like', "%{$value}%");
    })
```

### Case-Insensitive Search
```php
FilterString::make('Name', 'name')
    ->query(function ($query, $value) {
        return $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($value) . '%']);
    })
```

### Multi-Column Search
```php
FilterString::make('Search')
    ->query(function ($query, $value) {
        return $query->where(function ($q) use ($value) {
            $q->where('name', 'like', "%{$value}%")
              ->orWhere('email', 'like', "%{$value}%")
              ->orWhere('phone', 'like', "%{$value}%");
        });
    })
```

### Relationship Search
```php
FilterString::make('Category', 'category.name')
    ->query(function ($query, $value) {
        return $query->whereHas('category', function ($q) use ($value) {
            $q->where('name', 'like', "%{$value}%");
        });
    })
```

### Dot Notation for Relationship Column
```php
// Dot notation is auto-resolved by applyFiltersToQuery().
// 'profile.bio' → whereHas('profile', fn($q) => $q->where('bio', 'like', ...))
FilterString::make('Bio', 'profile.bio')
```

### Virtual Filter for HasMany Relationship
```php
// When filtering data from a hasMany relationship (e.g., user settings),
// use a virtual key and custom query callback.
FilterString::make('Theme', 'theme_setting')
    ->query(function($query, $value, $filter) {
        // $value is the user's text input
        $query->whereHas('settings', function($q) use ($value) {
            $q->where('key', 'theme')->where('value', 'like', "%$value%");
        });
    })
```

### Multi-Column Search Without Index
```php
// Omit the index when the filter doesn't map to a single column.
// The custom query handles everything.
FilterString::make('Name')
    ->query(function($query, $value) {
        $query->where('custom_col', 'like', "%$value%")
              ->orWhere('other_col', 'like', "%$value%");
    })
```

## Architecture Notes

- **Type Property**: The `$type` property is set to `'string'`, which determines the UI component rendered.
- **Default Query Behavior**: By default, FilterString queries using `WHERE column LIKE '%value%'` for partial matches.
- **UI Rendering**: Renders as a text input field.
- **Real-Time Search**: Typically triggers on input change with debouncing (implementation depends on frontend).
- **Empty Values**: When the input is empty, the filter is not applied.
- **Custom Logic**: Use `query()` method to override default LIKE behavior.
- **Case Sensitivity**: Default LIKE behavior is case-insensitive on most databases (MySQL), but may be case-sensitive on others (PostgreSQL). Use `whereRaw('LOWER(column) LIKE ?', ...)` for guaranteed case-insensitivity.
- **Query Callback — 3 Parameters**: `->query(fn($query, $value, $filter))`. The third `$filter` parameter is the filter object. Most callbacks only need `$query` and `$value`.
- **Dot Notation**: `'profile.bio'` is auto-resolved using `whereHas()` for model-based tables. No custom `->query()` needed for simple belongsTo/hasOne.
- **Virtual Filters**: Use any key (like `'theme_setting'`) when the filter doesn't map to a direct column. Always provide a `->query()` callback.

## Common Patterns

### Exact Match
```php
FilterString::make('Code', 'product_code')
    ->query(function ($query, $value) {
        return $query->where('product_code', $value);
    })
```

### Starts With
```php
FilterString::make('Name Prefix', 'name')
    ->query(function ($query, $value) {
        return $query->where('name', 'like', "{$value}%");
    })
```

### Full-Text Search
```php
FilterString::make('Description', 'description')
    ->query(function ($query, $value) {
        return $query->whereFullText('description', $value);
    })
```

### JSON Column Search
```php
FilterString::make('Metadata', 'metadata->key')
    ->query(function ($query, $value) {
        return $query->where('metadata->key', 'like', "%{$value}%");
    })
```

### Multiple Related Columns
```php
FilterString::make('User Search')
    ->query(function ($query, $value) {
        return $query->whereHas('user', function ($q) use ($value) {
            $q->where('first_name', 'like', "%{$value}%")
              ->orWhere('last_name', 'like', "%{$value}%")
              ->orWhere('email', 'like', "%{$value}%");
        });
    })
```

### Trim and Sanitize
```php
FilterString::make('Name', 'name')
    ->query(function ($query, $value) {
        $sanitized = trim(strtolower($value));
        return $query->whereRaw('LOWER(name) LIKE ?', ["%{$sanitized}%"]);
    })
```

### Numeric String Filter
```php
FilterString::make('Order Number', 'order_number')
    ->query(function ($query, $value) {
        // Only search if value is numeric
        if (is_numeric($value)) {
            return $query->where('order_number', $value);
        }
        return $query;
    })
```

### Comma-Separated Search
```php
FilterString::make('Tags', 'tags')
    ->query(function ($query, $value) {
        $tags = array_map('trim', explode(',', $value));
        return $query->where(function ($q) use ($tags) {
            foreach ($tags as $tag) {
                $q->orWhere('tags', 'like', "%{$tag}%");
            }
        });
    })
```

### Wildcard Search
```php
FilterString::make('Pattern', 'name')
    ->query(function ($query, $value) {
        // User can use * as wildcard
        $pattern = str_replace('*', '%', $value);
        return $query->where('name', 'like', $pattern);
    })
```

### PostgreSQL ILIKE
```php
FilterString::make('Name', 'name')
    ->query(function ($query, $value) {
        // Case-insensitive on PostgreSQL
        return $query->where('name', 'ilike', "%{$value}%");
    })
```
