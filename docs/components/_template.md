# {Component Name}

Brief one-line description.

## Basic Usage

```php
Column::make('Label', 'column_key')
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | `string` | — | Display label |

## Fluent Methods

| Method | Description |
|--------|-------------|
| `searchable()` | Enable search |

## Examples

### Basic Column

```php
Column::make('Name', 'name')
```

### With Custom Data

```php
Column::make('Full Name', 'name')
    ->customData(fn($row) => $row->first_name . ' ' . $row->last_name)
```

### In a Table Component

```php
public function columns(): array
{
    return [
        Column::make('Name', 'name')->searchable(),
        Column::make('Email', 'email'),
    ];
}
```
