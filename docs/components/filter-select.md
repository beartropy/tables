# FilterSelect

A filter type for selecting from a predefined list of options.

## Basic Usage

```php
use Beartropy\Tables\Classes\Filters\FilterSelect;

FilterSelect::make('Status', ['pending', 'approved', 'rejected'], 'status')
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `$label` | string | required | Display label for filter |
| `$options` | array | required | Array of options for dropdown |
| `$index` | string | null | Database column to filter on |

## Fluent Methods

| Method | Description |
|--------|-------------|
| `make(string $label, array $options, ?string $index)` | Static factory |
| `query(callable $callback)` | Custom query logic |

## Examples

### Basic Select

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

### From Model

```php
FilterSelect::make('Category',
    Category::pluck('name', 'id')->toArray(),
    'category_id'
)
```

### Numeric Options

```php
FilterSelect::make('Priority', [
    1 => 'High',
    2 => 'Medium',
    3 => 'Low'
], 'priority')
```

### Boolean-Like

```php
FilterSelect::make('Active', [
    1 => 'Yes',
    0 => 'No'
], 'is_active')
```

### Order Status

```php
FilterSelect::make('Order Status', [
    'pending' => 'Pending',
    'processing' => 'Processing',
    'completed' => 'Completed',
    'cancelled' => 'Cancelled'
], 'status')
```

### Custom Query

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
FilterSelect::make('Department',
    Department::pluck('name', 'id')->toArray(),
    'department_id'
)
    ->query(function ($query, $value) {
        return $query->whereHas('department', function ($q) use ($value) {
            $q->where('id', $value);
        });
    })
```

### Cached Options

```php
FilterSelect::make('Category',
    Cache::remember('categories', 3600, fn() =>
        Category::pluck('name', 'id')->toArray()
    ),
    'category_id'
)
```

### Dot Notation for Related Model

```php
// Auto-resolves relationship joins for belongsTo/hasOne
FilterSelect::make('Bio',
    Profile::distinct()->pluck('bio')->toArray(),
    'profile.bio'
)
```

### HasMany with Custom Query

```php
// For hasMany relationships, use a virtual key + custom query
FilterSelect::make('Theme',
    UserSetting::where('key', 'theme')->distinct()->pluck('value')->toArray(),
    'theme_setting'
)->query(function($query, $value, $filter) {
    $query->whereHas('settings', function($q) use ($value) {
        $q->where('key', 'theme')->where('value', 'like', "%$value%");
    });
})
```
