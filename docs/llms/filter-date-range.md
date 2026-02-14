# FilterDateRange — AI Reference

## Class
- `Beartropy\Tables\Classes\Filters\FilterDateRange` extends `Filter`

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

### Basic Date Range Filter
```php
FilterDateRange::make('Created Date', 'created_at')
```

### Updated Date Range
```php
FilterDateRange::make('Updated', 'updated_at')
```

### Custom Date Range
```php
FilterDateRange::make('Event Date', 'event_date')
```

### Custom Query Logic
```php
FilterDateRange::make('Published Date', 'published_at')
    ->query(function ($query, $daterange) {
        if (!empty($daterange['start'])) {
            $query->where('published_at', '>=', $daterange['start']);
        }
        if (!empty($daterange['end'])) {
            $query->where('published_at', '<=', $daterange['end']);
        }
        return $query;
    })
```

### With Time
```php
FilterDateRange::make('Scheduled', 'scheduled_at')
    ->query(function ($query, $daterange) {
        if (!empty($daterange['start'])) {
            $query->where('scheduled_at', '>=', $daterange['start'] . ' 00:00:00');
        }
        if (!empty($daterange['end'])) {
            $query->where('scheduled_at', '<=', $daterange['end'] . ' 23:59:59');
        }
        return $query;
    })
```

## Architecture Notes

- **Type Property**: The `$type` property is set to `'daterange'`, which determines the UI component rendered.
- **Daterange Property**: The `$daterange` property is an empty array by default, populated with `['start' => ..., 'end' => ...]` when user selects dates.
- **Default Query Behavior**: By default, FilterDateRange applies `whereBetween()` on the specified column using the start and end dates.
- **UI Rendering**: Renders as a date range picker with two date inputs (start date and end date).
- **Date Format**: Dates are typically in `Y-m-d` format (ISO 8601).
- **Partial Ranges**: Users can select only a start date, only an end date, or both. The default query handles partial ranges appropriately.
- **Custom Logic**: Use `query()` method to override default behavior, such as including time or using different comparison operators.

## Common Patterns

### Include Time Range
```php
FilterDateRange::make('Activity', 'activity_at')
    ->query(function ($query, $daterange) {
        if (!empty($daterange['start'])) {
            $query->where('activity_at', '>=', $daterange['start'] . ' 00:00:00');
        }
        if (!empty($daterange['end'])) {
            $query->where('activity_at', '<=', $daterange['end'] . ' 23:59:59');
        }
        return $query;
    })
```

### Filter on JSON Date
```php
FilterDateRange::make('Metadata Date', 'metadata->date')
    ->query(function ($query, $daterange) {
        if (!empty($daterange['start'])) {
            $query->where('metadata->date', '>=', $daterange['start']);
        }
        if (!empty($daterange['end'])) {
            $query->where('metadata->date', '<=', $daterange['end']);
        }
        return $query;
    })
```

### Exclusive Range
```php
FilterDateRange::make('Between Dates', 'date')
    ->query(function ($query, $daterange) {
        if (!empty($daterange['start'])) {
            $query->where('date', '>', $daterange['start']);
        }
        if (!empty($daterange['end'])) {
            $query->where('date', '<', $daterange['end']);
        }
        return $query;
    })
```

### Multiple Date Columns
```php
FilterDateRange::make('Date Range')
    ->query(function ($query, $daterange) {
        return $query->where(function ($q) use ($daterange) {
            if (!empty($daterange['start'])) {
                $q->where('start_date', '>=', $daterange['start']);
            }
            if (!empty($daterange['end'])) {
                $q->where('end_date', '<=', $daterange['end']);
            }
        });
    })
```

### UTC Conversion
```php
FilterDateRange::make('Created', 'created_at')
    ->query(function ($query, $daterange) {
        if (!empty($daterange['start'])) {
            $start = Carbon::parse($daterange['start'])->startOfDay()->utc();
            $query->where('created_at', '>=', $start);
        }
        if (!empty($daterange['end'])) {
            $end = Carbon::parse($daterange['end'])->endOfDay()->utc();
            $query->where('created_at', '<=', $end);
        }
        return $query;
    })
```

### Overlapping Ranges
```php
FilterDateRange::make('Event Period')
    ->query(function ($query, $daterange) {
        if (!empty($daterange['start']) && !empty($daterange['end'])) {
            return $query->where(function ($q) use ($daterange) {
                $q->whereBetween('start_date', [$daterange['start'], $daterange['end']])
                  ->orWhereBetween('end_date', [$daterange['start'], $daterange['end']])
                  ->orWhere(function ($q2) use ($daterange) {
                      $q2->where('start_date', '<=', $daterange['start'])
                         ->where('end_date', '>=', $daterange['end']);
                  });
            });
        }
        return $query;
    })
```
