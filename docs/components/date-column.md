# DateColumn

A column type for displaying dates with customizable formatting.

## Basic Usage

```php
use Beartropy\Tables\Classes\Columns\DateColumn;

DateColumn::make('Created', 'created_at')
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `$label` | string | required | Display label for column header |
| `$index` | string | null | Database column or array key |

## Fluent Methods

Inherits all Column methods, plus:

| Method | Description |
|--------|-------------|
| `inputFormat(string $format)` | Set input date format |
| `outputFormat(string $format)` | Set display date format (default: 'Y-m-d') |
| `emptyValue(string $value)` | Value to show when date is null |

## Examples

### Basic Date

```php
DateColumn::make('Created', 'created_at')
```

### Custom Format

```php
DateColumn::make('Published', 'published_at')
    ->outputFormat('M d, Y')
```

### Full DateTime

```php
DateColumn::make('Last Login', 'last_login_at')
    ->outputFormat('Y-m-d H:i:s')
```

### Human-Readable Format

```php
DateColumn::make('Created', 'created_at')
    ->outputFormat('F j, Y')
```

### With Empty Value

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

### Short Format

```php
DateColumn::make('Due Date', 'due_date')
    ->outputFormat('m/d/Y')
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

### Hidden on Mobile

```php
DateColumn::make('Updated', 'updated_at')
    ->outputFormat('Y-m-d')
    ->hideOnMobile()
```
