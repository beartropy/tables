# Column

The base column class for defining table columns with support for sorting, searching, inline editing, and responsive behavior.

## Basic Usage

```php
use Beartropy\Tables\Classes\Columns\Column;

Column::make('Name', 'name')
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `$label` | string | required | Display label for column header |
| `$index` | string | null | Database column or array key to fetch data from |

## Fluent Methods

| Method | Description |
|--------|-------------|
| `make(string $label, ?string $key)` | Static factory constructor |
| `sortable(bool\|callable)` | Enable sorting or custom sort logic |
| `searchable(bool\|callable)` | Enable searching or custom search logic |
| `editable(string $type, array $options, ?callable $callback)` | Enable inline editing |
| `setUpdateField(string $field)` | Set database field for updates |
| `pushLeft()` | Align content left |
| `pushRight()` | Align content right |
| `centered()` | Center content |
| `collapseOnMobile(bool)` | Show as label + value on mobile |
| `cardTitle(bool\|callable)` | Use as card title in card view |
| `triggerCardInfoModal(bool)` | Open modal when card title clicked |
| `showOnCard(bool)` | Show column in card view |
| `hideOnMobile(bool)` | Hide on mobile devices |
| `showOnMobile(bool)` | Force show on mobile |
| `view(string $view)` | Use custom Blade view |
| `styling(string $classes)` | Add CSS classes to td |
| `thStyling(string $classes)` | Add CSS classes to th |
| `thWrapperStyling(string $classes)` | Add CSS classes to th wrapper |
| `customData(Closure $callback)` | Transform data with callback |
| `hideWhen(bool $condition)` | Hide column conditionally |
| `hideFromSelector(bool)` | Hide from column toggle |
| `isVisible(bool)` | Set column visibility |
| `secondaryHeader(callable $callback)` | Add a secondary header row with computed content |
| `sortColumnBy(string $column)` | Sort by different column |
| `toHtml()` | Render as raw HTML |
| `isBool()` | Mark as boolean type |
| `trueIs(mixed $value)` | Define true value |
| `trueLabel(string $label)` | Set label for true |
| `falseLabel(string $label)` | Set label for false |

## Examples

### Basic Column

```php
Column::make('Name', 'name')
```

### Sortable and Searchable

```php
Column::make('Email', 'email')
    ->sortable()
    ->searchable()
```

### Custom Data Transformation

```php
Column::make('Full Name')
    ->customData(fn($row) => $row->first_name . ' ' . $row->last_name)
```

### Styled Column

```php
Column::make('Price', 'price')
    ->styling('font-bold text-green-600')
    ->pushRight()
```

### Mobile Responsive

```php
Column::make('Description', 'description')
    ->hideOnMobile()

Column::make('Title', 'title')
    ->collapseOnMobile()
```

### Inline Editing

```php
Column::make('Status', 'status')
    ->editable('select', [
        'options' => ['pending', 'approved', 'rejected']
    ])
```

### Custom Sort Logic

```php
Column::make('Priority', 'priority')
    ->sortable(function ($query, $direction) {
        return $query->orderByRaw("FIELD(priority, 'high', 'medium', 'low') {$direction}");
    })
```

### Custom Search Logic

```php
Column::make('Full Name')
    ->customData(fn($row) => $row->first_name . ' ' . $row->last_name)
    ->searchable(function ($query, $search) {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%");
        });
    })
```

### Actions Column

```php
Column::make('Actions')
    ->customData(fn($row) => view('partials.actions', compact('row')))
    ->toHtml()
    ->hideFromSelector()
```

### Relationship Column (Dot Notation)

```php
// Dot notation auto-resolves belongsTo/hasOne relationships
Column::make('Bio', 'profile.bio')
    ->sortable()
    ->searchable()
```

### Virtual Column from hasMany Relationship

```php
// For hasMany, use customData + custom sort/search callbacks
Column::make('Theme', 'theme_setting')
    ->customData(fn($row) => $row->settings->firstWhere('key', 'theme')?->value ?? '—')
    ->sortable(function($query, $direction) {
        $query->orderBy(
            UserSetting::select('value')
                ->whereColumn('user_settings.user_id', 'users.id')
                ->where('key', 'theme'),
            $direction
        );
    })
    ->searchable(function($query, $term) {
        $query->orWhereHas('settings', function($q) use ($term) {
            $q->where('key', 'theme')->where('value', 'like', "%$term%");
        });
    })
```

### Editable Select with Object Options

```php
Column::make('Status', 'status')
    ->editable('select', [
        ['value' => 'draft', 'label' => 'Draft'],
        ['value' => 'published', 'label' => 'Published'],
        ['value' => 'archived', 'label' => 'Archived'],
    ])
```

### Conditional Visibility

```php
Column::make('Admin Notes', 'admin_notes')
    ->hideWhen(!auth()->user()->isAdmin())
```

### Secondary Header (Aggregation Row)

```php
// The callback receives the current page's rows as a Collection.
// A second header row appears below the main header with the returned content.
Column::make('Price', 'price')
    ->sortable()
    ->secondaryHeader(function ($rows) {
        return 'Subtotal: $' . number_format($rows->sum('price'), 2);
    })
```

### Card View Title

```php
Column::make('Product Name', 'name')
    ->cardTitle()
    ->showOnCard()
```
