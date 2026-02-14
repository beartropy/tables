# ToggleColumn

A column type for displaying interactive toggle switches that update the database.

## Basic Usage

```php
use Beartropy\Tables\Classes\Columns\ToggleColumn;

ToggleColumn::make('Active', 'is_active')
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
| `trigger(string $event)` | Dispatch Livewire event on toggle |
| `disableToggleWhen(Closure $callback)` | Disable toggle based on condition |
| `hideToggleWhen(Closure $callback)` | Hide toggle based on condition |
| `trueIs(mixed $value)` | Define what value represents true |

## Examples

### Basic Toggle

```php
ToggleColumn::make('Active', 'is_active')
```

### With Event Trigger

```php
ToggleColumn::make('Featured', 'is_featured')
    ->trigger('featureChanged')
```

### Conditional Disable

```php
ToggleColumn::make('Published', 'is_published')
    ->disableToggleWhen(fn($row) => $row->status === 'archived')
```

### Authorization Check

```php
ToggleColumn::make('Active', 'is_active')
    ->hideToggleWhen(fn($row) => !auth()->user()->can('update', $row))
```

### Custom True Value

```php
ToggleColumn::make('Status', 'status')
    ->trueIs('active')
```

### Admin-Only Toggle

```php
ToggleColumn::make('Verified', 'is_verified')
    ->disableToggleWhen(fn($row) => !auth()->user()->isAdmin())
```

### Multiple Conditions

```php
ToggleColumn::make('Active', 'is_active')
    ->disableToggleWhen(fn($row) => $row->is_locked || $row->is_deleted)
```

### Locked Row

```php
ToggleColumn::make('Published', 'is_published')
    ->disableToggleWhen(fn($row) => $row->locked_at !== null)
```

### Centered Toggle

```php
ToggleColumn::make('Active', 'is_active')
    ->centered()
```

### In Card View

```php
ToggleColumn::make('Active', 'is_active')
    ->showOnCard()
```
