# FilterDateRange

A filter type for selecting a date range with start and end dates.

## Basic Usage

```php
use Beartropy\Tables\Classes\Filters\FilterDateRange;

FilterDateRange::make('Created Date', 'created_at')
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

### Basic Date Range

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

### Include Time

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

### Published Date Filter

```php
FilterDateRange::make('Published Date', 'published_at')
```

### Custom Query Logic

```php
FilterDateRange::make('Date Range', 'date')
    ->query(function ($query, $daterange) {
        if (!empty($daterange['start'])) {
            $query->where('date', '>=', $daterange['start']);
        }
        if (!empty($daterange['end'])) {
            $query->where('date', '<=', $daterange['end']);
        }
        return $query;
    })
```

### Multiple Date Columns

```php
FilterDateRange::make('Event Period')
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
