# BoolColumn — AI Reference

## Class
- `Beartropy\Tables\Classes\Columns\BoolColumn` extends `Column`

## Props (constructor)
| Prop | PHP Type | Default | Description |
|------|----------|---------|-------------|
| `$label` | string | required | Display label for column header |
| `$index` | ?string | null | Database column or array key to fetch data from |

## Fluent Methods
Inherits all methods from `Column`, plus:

| Method | Return | Description |
|--------|--------|-------------|
| `trueIs(mixed $value)` | self | Define what value represents true (inherited but commonly used) |
| `trueLabel(string $label)` | self | Set custom label for true values (inherited) |
| `falseLabel(string $label)` | self | Set custom label for false values (inherited) |

## Usage Examples

### Basic Boolean Column
```php
BoolColumn::make('Active', 'is_active')
```

### Custom True Value
```php
BoolColumn::make('Published', 'status')
    ->trueIs('published')
```

### Custom Labels
```php
BoolColumn::make('Verified', 'is_verified')
    ->trueLabel('Verified')
    ->falseLabel('Not Verified')
```

### With Sorting and Searching
```php
BoolColumn::make('Featured', 'is_featured')
    ->sortable()
    ->searchable()
```

### Styled Icons
```php
BoolColumn::make('Active', 'is_active')
    ->styling('text-center')
```

## Architecture Notes

- **Automatic Icon Display**: BoolColumn automatically renders green checkmark HTML for true values and red X HTML for false values.
- **Default True Value**: By default, `$what_is_true` is `1`, meaning the column checks if the value equals `1`.
- **Icon HTML**:
  - True icon: `<svg class="h-5 w-5 text-green-500" ... ><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>`
  - False icon: `<svg class="h-5 w-5 text-red-500" ... ><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>`
- **Inheritance**: All `Column` fluent methods work (sorting, searching, styling, mobile behavior, etc.).
- **isBool Flag**: The `$isBool` property is set to `true`, which the table rendering logic uses to determine display logic.

## Common Patterns

### Status Column with String Values
```php
BoolColumn::make('Status', 'status')
    ->trueIs('active')
    ->trueLabel('Active')
    ->falseLabel('Inactive')
```

### Centered Icon
```php
BoolColumn::make('Published', 'is_published')
    ->centered()
```

### Non-Sortable Boolean
```php
BoolColumn::make('Has Notes', 'notes_count')
    ->trueIs(fn($value) => $value > 0)
    ->sortable(false)
```

### Hidden on Mobile
```php
BoolColumn::make('Email Verified', 'email_verified_at')
    ->trueIs(fn($value) => !is_null($value))
    ->hideOnMobile()
```

### With Custom Data
```php
BoolColumn::make('In Stock', 'quantity')
    ->customData(fn($row) => $row->quantity > 0)
    ->trueIs(true)
```
