# LinkColumn — AI Reference

## Class
- `Beartropy\Tables\Classes\Columns\LinkColumn` extends `Column`

## Props (constructor)
| Prop | PHP Type | Default | Description |
|------|----------|---------|-------------|
| `$label` | string | required | Display label for column header |
| `$index` | ?string | null | Database column or array key to fetch data from |

## Fluent Methods
Inherits all methods from `Column`, plus:

| Method | Return | Description |
|--------|--------|-------------|
| `href(Closure $callback)` | self | Set URL using callback receiving row data |
| `text(string\|Closure $text)` | self | Set link text (if different from column value) |
| `target(string $target)` | self | Set link target (_blank, _self, etc.) |
| `popup(array $config)` | self | Open link in popup window with dimensions |
| `classes(string $classes)` | self | Add CSS classes to anchor tag |

## Usage Examples

### Basic Link Column
```php
LinkColumn::make('Name', 'name')
    ->href(fn($row) => route('users.show', $row->id))
```

### Link with Custom Text
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

### Link with Custom Classes
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

### Conditional Link
```php
LinkColumn::make('Name', 'name')
    ->href(fn($row) => $row->is_active
        ? route('users.show', $row)
        : null
    )
```

### Dynamic Text
```php
LinkColumn::make('Action')
    ->text(fn($row) => $row->status === 'pending' ? 'Review' : 'View')
    ->href(fn($row) => route('items.show', $row))
```

### Link to Related Model
```php
LinkColumn::make('Category', 'category.name')
    ->href(fn($row) => route('categories.show', $row->category_id))
```

## Architecture Notes

- **isLink Flag**: The `$isLink` property is set to `true`, indicating this column renders as a hyperlink.
- **href Property**: The `$href` property stores a Closure that receives the row data and returns the URL string.
- **text Property**: If null, the column displays the value from `$index`. If set (string or Closure), it overrides the display text.
- **tag_styles**: The `$tag_styles` property stores additional HTML attributes for the anchor tag (target, popup config).
- **Popup Configuration**: The `popup()` method accepts an array with `width` and `height` keys, which triggers JavaScript window.open() behavior.
- **Modified Data Flag**: When `text()` is set with a Closure, `$has_modified_data` is marked true.
- **Inheritance**: All `Column` fluent methods work (sorting, searching, styling, mobile behavior, etc.).

## Common Patterns

### Edit Link
```php
LinkColumn::make('Edit')
    ->href(fn($row) => route('users.edit', $row))
    ->text('Edit')
    ->classes('text-indigo-600 hover:underline')
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

### Conditional External Link
```php
LinkColumn::make('Website', 'website')
    ->href(fn($row) => $row->website)
    ->target('_blank')
    ->hideWhen(fn($row) => empty($row->website))
```

### Download Link
```php
LinkColumn::make('Download')
    ->text('Download PDF')
    ->href(fn($row) => route('documents.download', $row))
    ->classes('text-green-600')
```

### Link with Icon
```php
LinkColumn::make('View')
    ->href(fn($row) => route('items.show', $row))
    ->text('<svg>...</svg> View')
    ->toHtml()
    ->classes('inline-flex items-center gap-1')
```

### Named Route with Parameters
```php
LinkColumn::make('Product', 'name')
    ->href(fn($row) => route('products.show', [
        'category' => $row->category_id,
        'product' => $row->id
    ]))
```

### Link in Card View
```php
LinkColumn::make('Title', 'title')
    ->href(fn($row) => route('posts.show', $row))
    ->cardTitle()
    ->showOnCard()
```
