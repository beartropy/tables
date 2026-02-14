# LinkColumn

A column type for displaying clickable links with customizable URLs and text.

## Basic Usage

```php
use Beartropy\Tables\Classes\Columns\LinkColumn;

LinkColumn::make('Name', 'name')
    ->href(fn($row) => route('users.show', $row->id))
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
| `href(Closure $callback)` | Set URL using callback |
| `text(string\|Closure $text)` | Set link text |
| `target(string $target)` | Set link target (_blank, etc.) |
| `popup(array $config)` | Open in popup window |
| `classes(string $classes)` | Add CSS classes to link |

## Examples

### Basic Link

```php
LinkColumn::make('Name', 'name')
    ->href(fn($row) => route('users.show', $row->id))
```

### Custom Link Text

```php
LinkColumn::make('View')
    ->href(fn($row) => route('posts.show', $row))
    ->text('View Details')
```

### External Link

```php
LinkColumn::make('Website', 'website_url')
    ->href(fn($row) => $row->website_url)
    ->target('_blank')
```

### Styled Link

```php
LinkColumn::make('Edit')
    ->href(fn($row) => route('users.edit', $row))
    ->text('Edit')
    ->classes('text-blue-600 hover:text-blue-800 font-semibold')
```

### Popup Window

```php
LinkColumn::make('Preview')
    ->href(fn($row) => route('preview', $row))
    ->text('Preview')
    ->popup(['width' => 800, 'height' => 600])
```

### Dynamic Text

```php
LinkColumn::make('Action')
    ->text(fn($row) => $row->status === 'pending' ? 'Review' : 'View')
    ->href(fn($row) => route('items.show', $row))
```

### Email Link

```php
LinkColumn::make('Email', 'email')
    ->href(fn($row) => 'mailto:' . $row->email)
```

### Phone Link

```php
LinkColumn::make('Phone', 'phone')
    ->href(fn($row) => 'tel:' . $row->phone)
```

### Link to Related Model

```php
LinkColumn::make('Category', 'category.name')
    ->href(fn($row) => route('categories.show', $row->category_id))
```

### Download Link

```php
LinkColumn::make('Download')
    ->text('Download PDF')
    ->href(fn($row) => route('documents.download', $row))
    ->classes('text-green-600')
```
