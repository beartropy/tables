# FilterString

A filter type for text input with search functionality.

## Basic Usage

```php
use Beartropy\Tables\Classes\Filters\FilterString;

FilterString::make('Name', 'name')
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `$label` | string | required | Display label for filter |
| `$index` | string | null | Database column to filter on |

## Fluent Methods

| Method | Description |
|--------|-------------|
| `make(string $label, ?string $index)` | Static factory |
| `query(callable $callback)` | Custom query logic |

## Examples

### Basic String Filter

```php
FilterString::make('Name', 'name')
```

### Email Filter

```php
FilterString::make('Email', 'email')
```

### Custom Search

```php
FilterString::make('Search', 'name')
    ->query(function ($query, $value) {
        return $query->where('name', 'like', "%{$value}%");
    })
```

### Case-Insensitive

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

### Relationship Search

```php
FilterString::make('Category', 'category.name')
    ->query(function ($query, $value) {
        return $query->whereHas('category', function ($q) use ($value) {
            $q->where('name', 'like', "%{$value}%");
        });
    })
```

### Full-Text Search

```php
FilterString::make('Description', 'description')
    ->query(function ($query, $value) {
        return $query->whereFullText('description', $value);
    })
```

### JSON Column

```php
FilterString::make('Metadata', 'metadata->key')
    ->query(function ($query, $value) {
        return $query->where('metadata->key', 'like', "%{$value}%");
    })
```

### Dot Notation for Relationship

```php
// Auto-resolves via whereHas() for model-based tables
FilterString::make('Bio', 'profile.bio')
```

### HasMany with Custom Query

```php
FilterString::make('Theme', 'theme_setting')
    ->query(function($query, $value, $filter) {
        $query->whereHas('settings', function($q) use ($value) {
            $q->where('key', 'theme')->where('value', 'like', "%$value%");
        });
    })
```

### Multi-Column Without Index

```php
// No index — custom query searches multiple columns
FilterString::make('Name')
    ->query(function($query, $value) {
        $query->where('custom_col', 'like', "%$value%")
              ->orWhere('other_col', 'like', "%$value%");
    })
```
