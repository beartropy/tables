# FilterSelectMagic

A filter type that automatically populates options from distinct column values.

## Basic Usage

```php
use Beartropy\Tables\Classes\Filters\FilterSelectMagic;

FilterSelectMagic::make('Status', 'status')
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

### Basic Magic Select

```php
FilterSelectMagic::make('Status', 'status')
```

### Category Filter

```php
FilterSelectMagic::make('Category', 'category')
```

### Role Filter

```php
FilterSelectMagic::make('Role', 'role')
```

### Type Filter

```php
FilterSelectMagic::make('Type', 'type')
```

### Custom Query

```php
FilterSelectMagic::make('Status', 'status')
    ->query(function ($query, $value) {
        return $query->where('status', strtolower($value));
    })
```

### Relationship Column

```php
FilterSelectMagic::make('Author', 'author.name')
    ->query(function ($query, $value) {
        return $query->whereHas('author', function ($q) use ($value) {
            $q->where('name', $value);
        });
    })
```

### Case-Insensitive

```php
FilterSelectMagic::make('Category', 'category')
    ->query(function ($query, $value) {
        return $query->whereRaw('LOWER(category) = ?', [strtolower($value)]);
    })
```

### Priority Filter

```php
FilterSelectMagic::make('Priority', 'priority')
```

### Department Filter

```php
FilterSelectMagic::make('Department', 'department')
    ->query(function ($query, $value) {
        return $query->inDepartment($value);
    })
```

### Limitation: Relationship Columns

```php
// Dot notation falls back to loading all data (slower).
// For relationship columns, prefer FilterSelect:
FilterSelect::make('Bio',
    Profile::distinct()->pluck('bio')->toArray(),
    'profile.bio'
)
```

## When to Use

**Use FilterSelectMagic when:**
- Column has a small set of distinct values
- Values change infrequently
- You want automatic option discovery
- Table data size is moderate

**Use FilterSelect when:**
- You need custom display labels
- You want to limit options to a subset
- Performance is critical (large datasets)
- Options should be cached
- Options come from a different table

## Comparison

```php
// FilterSelectMagic: Auto-discovers all statuses
FilterSelectMagic::make('Status', 'status')

// FilterSelect: Explicit options with custom labels
FilterSelect::make('Status', [
    'p' => 'Pending',
    'a' => 'Approved',
    'r' => 'Rejected'
], 'status')
```
