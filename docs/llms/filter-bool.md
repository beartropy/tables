# FilterBool — AI Reference

## Class
- `Beartropy\Tables\Classes\Filters\FilterBool` extends `Filter`

## Props (constructor)
| Prop | PHP Type | Default | Description |
|------|----------|---------|-------------|
| `$label` | string | required | Display label for filter |
| `$compared_with` | ?array | `['true' => true, 'false' => false]` | Map of display options to comparison values |
| `$index` | ?string | null | Database column or array key to filter on |

## Fluent Methods
Inherits from `Filter`:

| Method | Return | Description |
|--------|--------|-------------|
| `make(string $label, ?array $compared_with = null, ?string $index = null)` | static | Static factory constructor |
| `query(callable $callback)` | self | Custom query logic for filter |

## Usage Examples

### Basic Boolean Filter
```php
FilterBool::make('Active', null, 'is_active')
```

### Custom Comparison Values
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

### Enabled/Disabled Filter
```php
FilterBool::make('Account Status', [
    'enabled' => true,
    'disabled' => false
], 'is_enabled')
```

### Custom Query Logic
```php
FilterBool::make('Has Orders')
    ->query(function ($query, $value) {
        if ($value === 'true') {
            return $query->has('orders');
        }
        return $query->doesntHave('orders');
    })
```

### String-Based Boolean
```php
FilterBool::make('Type', [
    'premium' => 'premium',
    'free' => 'free'
], 'account_type')
```

### Relationship-Based Boolean with Custom Query
```php
// Filter users who have/don't have a specific setting
FilterBool::make('Has Theme Setting')
    ->query(function($query, $value, $filter) {
        if (filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
            $query->whereHas('settings', fn($q) => $q->where('key', 'theme'));
        } else {
            $query->whereDoesntHave('settings', fn($q) => $q->where('key', 'theme'));
        }
    })
```

## Architecture Notes

- **Type Property**: The `$type` property is set to `'bool'`, which determines the UI component rendered.
- **Default Comparison**: If `$compared_with` is null, defaults to `['true' => true, 'false' => false]`.
- **Comparison Map**: The array keys are the display labels shown in the UI dropdown. The array values are what gets compared against the database column.
- **Query Behavior**: By default, FilterBool queries using `WHERE column = value`. The value is determined by user selection mapped through `$compared_with`.
- **Three States**: The filter dropdown includes an empty/"All" option, plus the two comparison options.
- **Custom Logic**: Use `query()` method to override default WHERE behavior for complex filtering.
- **UI Rendering**: Renders as a select dropdown with the label options from `$compared_with` keys.
- **Query Callback — 3 Parameters**: `->query(fn($query, $value, $filter))`. The `$value` for bool filters is the string `'true'` or `'false'` from the dropdown selection. Use `filter_var($value, FILTER_VALIDATE_BOOLEAN)` to convert to actual boolean.

## Common Patterns

### Has/Doesn't Have Relationship
```php
FilterBool::make('Has Comments', [
    'yes' => true,
    'no' => false
])
    ->query(function ($query, $value) {
        return $value === true
            ? $query->has('comments')
            : $query->doesntHave('comments');
    })
```

### Scope-Based Filter
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

### Null vs Not Null
```php
FilterBool::make('Has Notes', [
    'yes' => 'has',
    'no' => 'none'
], 'notes')
    ->query(function ($query, $value) {
        return $value === 'has'
            ? $query->whereNotNull('notes')
            : $query->whereNull('notes');
    })
```

### Integer Comparison
```php
FilterBool::make('Status', [
    'approved' => 1,
    'pending' => 0
], 'is_approved')
```

### Date-Based Boolean
```php
FilterBool::make('Expired', [
    'yes' => 'expired',
    'no' => 'active'
], 'expires_at')
    ->query(function ($query, $value) {
        return $value === 'expired'
            ? $query->where('expires_at', '<', now())
            : $query->where('expires_at', '>=', now());
    })
```

### Multiple Values
```php
FilterBool::make('Priority', [
    'high' => 'high',
    'low' => 'low'
], 'priority')
```
