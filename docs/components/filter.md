# Filter

The base filter class for creating table filters. Use specific filter types in practice.

## Basic Usage

```php
// Use specific filter types instead:
// FilterBool, FilterDateRange, FilterSelect, FilterSelectMagic, FilterString
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `$label` | string | required | Display label for filter |
| `$column` | string | null | Database column or array key to filter on |

## Fluent Methods

| Method | Description |
|--------|-------------|
| `query(callable $callback)` | Custom query logic for filter |

## Examples

### Custom Query Logic

```php
use Beartropy\Tables\Classes\Filters\FilterString;

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
use Beartropy\Tables\Classes\Filters\FilterSelect;

FilterSelect::make('Category', ['News', 'Blog', 'Tutorial'], 'category_id')
    ->query(function ($query, $value) {
        return $query->whereHas('category', function ($q) use ($value) {
            $q->where('name', $value);
        });
    })
```

### JSON Column Filter

```php
FilterString::make('Metadata', 'metadata->key')
    ->query(function ($query, $value) {
        return $query->where('metadata->key', 'like', "%{$value}%");
    })
```

### Scope-Based Filter

```php
use Beartropy\Tables\Classes\Filters\FilterBool;

FilterBool::make('Active')
    ->query(function ($query, $value) {
        if ($value === 'true') {
            return $query->active();
        }
        return $query->inactive();
    })
```

### Query Callback Parameters

```php
// All filter ->query() callbacks receive 3 parameters:
->query(function($query, $value, $filter) {
    // $query  — Eloquent Builder
    // $value  — User's input
    // $filter — The filter object ($filter->key, $filter->label, $filter->type)
})
```

### Dot Notation (Automatic Relationship Resolution)

```php
// Filters with dot-notation keys auto-resolve via whereHas()
FilterString::make('Bio', 'profile.bio')
FilterSelect::make('Bio', Profile::distinct()->pluck('bio')->toArray(), 'profile.bio')
```

### Virtual Filter for hasMany

```php
FilterSelect::make('Theme',
    UserSetting::where('key', 'theme')->distinct()->pluck('value')->toArray(),
    'theme_setting'
)->query(function($query, $value, $filter) {
    $query->whereHas('settings', function($q) use ($value) {
        $q->where('key', 'theme')->where('value', 'like', "%$value%");
    });
})
```
