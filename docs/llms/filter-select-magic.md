# FilterSelectMagic — AI Reference

## Class
- `Beartropy\Tables\Classes\Filters\FilterSelectMagic` extends `Filter`

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

### Basic Magic Select Filter
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

### Custom Query Logic
```php
FilterSelectMagic::make('Status', 'status')
    ->query(function ($query, $value) {
        return $query->where('status', strtolower($value));
    })
```

### Relationship Column
```php
FilterSelectMagic::make('Category', 'category.name')
```

### When Dot Notation Doesn't Work
```php
// FilterSelectMagic auto-populates options from distinct values.
// For model-based tables, it uses SELECT DISTINCT.
// However, dot notation columns fall back to the slower getAllData()->pluck() path
// because the DISTINCT query can't resolve relationships.
// For relationship columns, prefer FilterSelect with explicit options:
FilterSelect::make('Bio',
    \App\Models\Profile::distinct()->pluck('bio')->toArray(),
    'profile.bio'
)
// instead of: FilterSelectMagic::make('Bio', 'profile.bio')
```

## Architecture Notes

- **Type Property**: The `$type` property is set to `'magic-select'`, which determines the UI component rendered.
- **Auto-Populated Options**: The `$options` property is automatically populated at runtime with distinct values from the specified column.
- **Model-Based Tables**: For Eloquent model tables, uses `SELECT DISTINCT column FROM table` to populate options.
- **Array-Based Tables**: For array data tables, uses `pluck(column)->unique()` to extract unique values.
- **No Manual Options**: Unlike FilterSelect, you don't provide options manually — they're dynamically generated from existing data.
- **Default Query Behavior**: By default, FilterSelectMagic queries using `WHERE column = value`.
- **UI Rendering**: Renders as a select dropdown with an empty/"All" option plus all distinct column values.
- **Performance**: For large datasets, consider using FilterSelect with cached options instead, as magic select queries the database for distinct values.
- **Custom Logic**: Use `query()` method to override default WHERE behavior.
- **Dot Notation Fallback**: When the column key contains a `.`, the DISTINCT optimization is skipped and `getAllData()->pluck()` is used instead. For relationship columns on large tables, use FilterSelect with pre-loaded options.

## Common Patterns

### Status from Existing Data
```php
// Automatically populates dropdown with all unique status values
FilterSelectMagic::make('Status', 'status')
```

### Type Filter
```php
// Automatically populates with all unique types in the data
FilterSelectMagic::make('Type', 'type')
```

### Related Column
```php
// Works with relationship columns
FilterSelectMagic::make('Author', 'author.name')
    ->query(function ($query, $value) {
        return $query->whereHas('author', function ($q) use ($value) {
            $q->where('name', $value);
        });
    })
```

### Case-Insensitive Filter
```php
FilterSelectMagic::make('Category', 'category')
    ->query(function ($query, $value) {
        return $query->whereRaw('LOWER(category) = ?', [strtolower($value)]);
    })
```

### Enum-Like Values
```php
// Automatically discovers all priority values
FilterSelectMagic::make('Priority', 'priority')
```

### JSON Column
```php
FilterSelectMagic::make('Language', 'preferences->language')
```

### With Scope
```php
FilterSelectMagic::make('Department', 'department')
    ->query(function ($query, $value) {
        return $query->inDepartment($value);
    })
```

## Common Patterns vs FilterSelect

### Use FilterSelectMagic When:
- Column has a small, stable set of distinct values
- Values change infrequently
- You want automatic option discovery
- The table data size is moderate

### Use FilterSelect When:
- You need custom display labels different from stored values
- You want to limit options to a specific subset
- Performance is critical (large datasets)
- You need options from a different table/source
- Options should be cached

## Example Comparison

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

## Performance Considerations

```php
// For large tables, consider adding index
// Migration:
$table->index('status');

// Or use FilterSelect with cached options:
FilterSelect::make('Status',
    Cache::remember('statuses', 3600, fn() =>
        Model::distinct()->pluck('status', 'status')->toArray()
    ),
    'status'
)
```
