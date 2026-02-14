# ToggleColumn — AI Reference

## Class
- `Beartropy\Tables\Classes\Columns\ToggleColumn` extends `Column`

## Props (constructor)
| Prop | PHP Type | Default | Description |
|------|----------|---------|-------------|
| `$label` | string | required | Display label for column header |
| `$index` | ?string | null | Database column or array key to fetch data from |

## Fluent Methods
Inherits all methods from `Column`, plus:

| Method | Return | Description |
|--------|--------|-------------|
| `trigger(string $event)` | self | Set Livewire event to dispatch when toggled |
| `disableToggleWhen(Closure $callback)` | self | Disable toggle based on row condition |
| `hideToggleWhen(Closure $callback)` | self | Hide toggle (show static value) based on row condition |
| `trueIs(mixed $value)` | self | Define what value represents true (inherited) |

## Usage Examples

### Basic Toggle Column
```php
ToggleColumn::make('Active', 'is_active')
```

### Toggle with Event Trigger
```php
ToggleColumn::make('Featured', 'is_featured')
    ->trigger('featureChanged')
```

### Conditional Disable
```php
ToggleColumn::make('Published', 'is_published')
    ->disableToggleWhen(fn($row) => $row->status === 'archived')
```

### Conditional Hide
```php
ToggleColumn::make('Active', 'is_active')
    ->hideToggleWhen(fn($row) => !auth()->user()->can('update', $row))
```

### Custom True Value
```php
ToggleColumn::make('Status', 'status')
    ->trueIs('active')
```

### Toggle with Authorization
```php
ToggleColumn::make('Verified', 'is_verified')
    ->disableToggleWhen(fn($row) => !auth()->user()->isAdmin())
```

### Multiple Conditions
```php
ToggleColumn::make('Active', 'is_active')
    ->disableToggleWhen(fn($row) => $row->is_locked || $row->is_deleted)
    ->hideToggleWhen(fn($row) => $row->is_archived)
```

## Architecture Notes

- **isToggle Flag**: The `$isToggle` property is set to `true`, indicating this column renders as an interactive toggle switch.
- **Default True Value**: By default, `$what_is_true` is `1`, meaning the toggle is "on" when the value equals `1`.
- **Trigger Event**: The `$trigger` property stores a Livewire event name to dispatch when the toggle changes. If false, no event is dispatched.
- **Disable Logic**: The `$disableToggleWhen` Closure receives the row and returns boolean. When true, the toggle is rendered but disabled (not clickable).
- **Hide Logic**: The `$hideToggleWhen` Closure receives the row and returns boolean. When true, the toggle is hidden and only the static boolean value is shown.
- **Auto-Update**: Toggling automatically updates the database row via Livewire. The update uses the column's `$index` or `$updateField`.
- **Permission Checking**: Common pattern is to use `disableToggleWhen()` or `hideToggleWhen()` for authorization checks.
- **Inheritance**: All `Column` fluent methods work (sorting, searching, styling, mobile behavior, etc.).

## Common Patterns

### Admin-Only Toggle
```php
ToggleColumn::make('Featured', 'is_featured')
    ->hideToggleWhen(fn($row) => !auth()->user()->isAdmin())
```

### Toggle with Confirmation
```php
// In your Livewire component, listen for the event
protected $listeners = ['statusChanged' => 'handleStatusChange'];

// In your columns
ToggleColumn::make('Active', 'is_active')
    ->trigger('statusChanged')
```

### Locked Row Toggle
```php
ToggleColumn::make('Published', 'is_published')
    ->disableToggleWhen(fn($row) => $row->locked_at !== null)
```

### Role-Based Toggle
```php
ToggleColumn::make('Verified', 'is_verified')
    ->hideToggleWhen(fn($row) => !auth()->user()->hasRole('admin'))
```

### Status Toggle with String Values
```php
ToggleColumn::make('Status', 'status')
    ->trueIs('enabled')
    ->trigger('statusUpdated')
```

### Toggle with Custom Styling
```php
ToggleColumn::make('Active', 'is_active')
    ->styling('flex justify-center')
    ->centered()
```

### Toggle in Card View
```php
ToggleColumn::make('Active', 'is_active')
    ->showOnCard()
```

### Complex Authorization
```php
ToggleColumn::make('Published', 'is_published')
    ->disableToggleWhen(function ($row) {
        return !auth()->user()->can('publish', $row)
            || $row->draft_only;
    })
```
