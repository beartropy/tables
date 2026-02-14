# DateColumn — AI Reference

## Class
- `Beartropy\Tables\Classes\Columns\DateColumn` extends `Column`

## Props (constructor)
| Prop | PHP Type | Default | Description |
|------|----------|---------|-------------|
| `$label` | string | required | Display label for column header |
| `$index` | ?string | null | Database column or array key to fetch data from |

## Fluent Methods
Inherits all methods from `Column`, plus:

| Method | Return | Description |
|--------|--------|-------------|
| `inputFormat(string $format)` | self | Set the input date format (how data is stored) |
| `outputFormat(string $format)` | self | Set the display date format (how date is shown) |
| `emptyValue(string $value)` | self | Value to display when date is null/empty |

## Usage Examples

### Basic Date Column
```php
DateColumn::make('Created', 'created_at')
```

### Custom Output Format
```php
DateColumn::make('Published', 'published_at')
    ->outputFormat('M d, Y')
```

### Full DateTime Format
```php
DateColumn::make('Last Login', 'last_login_at')
    ->outputFormat('Y-m-d H:i:s')
```

### Custom Input/Output Formats
```php
DateColumn::make('Birthday', 'birth_date')
    ->inputFormat('Y-m-d')
    ->outputFormat('F j, Y')
```

### Handle Empty Dates
```php
DateColumn::make('Completed', 'completed_at')
    ->outputFormat('M d, Y')
    ->emptyValue('Not completed')
```

### Sortable Date
```php
DateColumn::make('Created', 'created_at')
    ->outputFormat('M d, Y g:i A')
    ->sortable()
```

### Hidden on Mobile
```php
DateColumn::make('Updated', 'updated_at')
    ->outputFormat('Y-m-d')
    ->hideOnMobile()
```

## Architecture Notes

- **Default Output Format**: `Y-m-d` (ISO 8601 date format).
- **Input Format**: By default `$inputFormat` is `null`, which means the date is expected in a format parseable by PHP's date functions.
- **Empty Value Handling**: By default, `$emptyValue` is an empty string `''`. When the date value is null or empty, this value is displayed instead.
- **isDate Flag**: The `$isDate` property is set to `true`, signaling to the table renderer that this is a date column.
- **Date Parsing**: The column uses PHP's date formatting functions internally. Carbon dates from Eloquent are automatically handled.
- **Inheritance**: All `Column` fluent methods work (sorting, searching, styling, mobile behavior, etc.).

## Common Patterns

### Human-Readable Format
```php
DateColumn::make('Created', 'created_at')
    ->outputFormat('F j, Y')
```

### Short Date Format
```php
DateColumn::make('Due Date', 'due_date')
    ->outputFormat('m/d/Y')
```

### Date and Time
```php
DateColumn::make('Scheduled', 'scheduled_at')
    ->outputFormat('Y-m-d h:i A')
```

### Relative Date (Using Custom Data)
```php
Column::make('Created', 'created_at')
    ->customData(fn($row) => $row->created_at->diffForHumans())
```

### Null-Safe Display
```php
DateColumn::make('Deleted', 'deleted_at')
    ->outputFormat('M d, Y')
    ->emptyValue('—')
```

### European Format
```php
DateColumn::make('Date', 'date')
    ->outputFormat('d/m/Y')
```

### With Timezone
```php
DateColumn::make('Event Time', 'event_at')
    ->outputFormat('Y-m-d H:i:s T')
```

### Card View Date
```php
DateColumn::make('Created', 'created_at')
    ->outputFormat('M d, Y')
    ->showOnCard()
```
