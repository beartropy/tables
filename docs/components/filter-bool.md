# FilterBool

A filter type for boolean or two-option selections.

## Basic Usage

```php
use Beartropy\Tables\Classes\Filters\FilterBool;

FilterBool::make('Active', null, 'is_active')
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `$label` | string | required | Display label for filter |
| `$compared_with` | array | `['true' => true, 'false' => false]` | Options map |
| `$index` | string | null | Database column to filter on |

## Fluent Methods

| Method | Description |
|--------|-------------|
| `make(string $label, ?array $compared_with, ?string $index)` | Static factory |
| `query(callable $callback)` | Custom query logic |

## Examples

### Basic Boolean Filter

```php
FilterBool::make('Active', null, 'is_active')
```

### Custom Options

```php
FilterBool::make('Status', [
    'published' => 'published',
    'draft' => 'draft'
], 'status')
```

### Yes/No Filter

```php
FilterBool::make('Verified', [
    'yes' => 1,
    'no' => 0
], 'is_verified')
```

### Enabled/Disabled

```php
FilterBool::make('Account Status', [
    'enabled' => true,
    'disabled' => false
], 'is_enabled')
```

### Has Relationship

```php
FilterBool::make('Has Orders', [
    'yes' => true,
    'no' => false
])
    ->query(function ($query, $value) {
        return $value === true
            ? $query->has('orders')
            : $query->doesntHave('orders');
    })
```

### Scope-Based

```php
FilterBool::make('Status', [
    'active' => 'active',
    'inactive' => 'inactive'
])
    ->query(function ($query, $value) {
        return $value === 'active'
            ? $query->active()
            : $query->inactive();
    })
```

### Priority Levels

```php
FilterBool::make('Priority', [
    'high' => 'high',
    'low' => 'low'
], 'priority')
```

### Relationship Existence Check

```php
FilterBool::make('Has Theme Setting')
    ->query(function($query, $value, $filter) {
        if (filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
            $query->whereHas('settings', fn($q) => $q->where('key', 'theme'));
        } else {
            $query->whereDoesntHave('settings', fn($q) => $q->where('key', 'theme'));
        }
    })
```
