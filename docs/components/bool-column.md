# BoolColumn

A column type for displaying boolean values with checkmark/X icons.

## Basic Usage

```php
use Beartropy\Tables\Classes\Columns\BoolColumn;

BoolColumn::make('Active', 'is_active')
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `$label` | string | required | Display label for column header |
| `$index` | string | null | Database column or array key |

## Fluent Methods

Inherits all methods from Column, plus commonly used:

| Method | Description |
|--------|-------------|
| `trueIs(mixed $value)` | Define what value represents true |
| `trueLabel(string $label)` | Custom label for true values |
| `falseLabel(string $label)` | Custom label for false values |

## Examples

### Basic Boolean

```php
BoolColumn::make('Active', 'is_active')
```

### Custom True Value

```php
BoolColumn::make('Published', 'status')
    ->trueIs('published')
```

### With Custom Labels

```php
BoolColumn::make('Verified', 'is_verified')
    ->trueLabel('Verified')
    ->falseLabel('Not Verified')
```

### Centered Icon

```php
BoolColumn::make('Featured', 'is_featured')
    ->centered()
```

### Sortable Boolean

```php
BoolColumn::make('Active', 'is_active')
    ->sortable()
```

### Hidden on Mobile

```php
BoolColumn::make('Email Verified', 'email_verified_at')
    ->trueIs(fn($value) => !is_null($value))
    ->hideOnMobile()
```

### Status with String Values

```php
BoolColumn::make('Status', 'status')
    ->trueIs('active')
    ->trueLabel('Active')
    ->falseLabel('Inactive')
```
