# Filter Examples - Beartropy Tables

Ready-to-use filter examples for Beartropy Tables.

## Table with All Filter Types

### Livewire Component
```php
<?php

namespace App\Livewire;

use App\Models\User;
use Beartropy\Tables\YATBaseTable;
use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\Classes\Columns\BoolColumn;
use Beartropy\Tables\Classes\Columns\DateColumn;
use Beartropy\Tables\Classes\Filters\FilterString;
use Beartropy\Tables\Classes\Filters\FilterSelect;
use Beartropy\Tables\Classes\Filters\FilterBool;
use Beartropy\Tables\Classes\Filters\FilterDateRange;
use Beartropy\Tables\Classes\Filters\FilterSelectMagic;

class UsersTable extends YATBaseTable
{
    public $model = User::class;

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')->sortable()->searchable(),
            Column::make('Email', 'email')->sortable()->searchable(),
            Column::make('Role', 'role')->sortable(),
            BoolColumn::make('Active', 'is_active')->centered(),
            DateColumn::make('Joined', 'created_at')->outputFormat('M d, Y')->sortable(),
        ];
    }

    public function filters(): array
    {
        return [
            // Text search — filters with LIKE
            FilterString::make('Name', 'name'),

            // Select dropdown — exact match
            FilterSelect::make('Role', [
                'admin' => 'Administrator',
                'editor' => 'Editor',
                'viewer' => 'Viewer',
            ], 'role'),

            // Boolean — true/false toggle
            FilterBool::make('Active', null, 'is_active'),

            // Date range — start/end date picker
            FilterDateRange::make('Joined', 'created_at'),

            // Magic select — auto-populates from distinct column values
            FilterSelectMagic::make('Status', 'status'),
        ];
    }
}
```

---

## Multi-Column Search Filter

```php
public function filters(): array
{
    return [
        // Single filter that searches across multiple columns
        FilterString::make('Search')
            ->query(function ($query, $value) {
                $query->where(function ($q) use ($value) {
                    $q->where('name', 'like', "%$value%")
                      ->orWhere('email', 'like', "%$value%")
                      ->orWhere('phone', 'like', "%$value%");
                });
            }),
    ];
}
```

---

## Relationship Filters

### Simple (Dot Notation)

```php
public function filters(): array
{
    return [
        // belongsTo/hasOne — dot notation works automatically
        FilterString::make('Author Name', 'author.name'),

        FilterSelect::make('Category',
            \App\Models\Category::pluck('name', 'id')->toArray(),
            'category.name'
        ),
    ];
}
```

### Complex (hasMany with Custom Query)

```php
public function filters(): array
{
    return [
        // hasMany requires a custom query callback
        FilterSelect::make('Tag',
            \App\Models\Tag::pluck('name')->toArray(),
            'tag_filter' // virtual key — not a real column
        )->query(function ($query, $value) {
            $query->whereHas('tags', function ($q) use ($value) {
                $q->where('name', $value);
            });
        }),

        // hasMany through (e.g., user settings)
        FilterSelect::make('Theme',
            \App\Models\UserSetting::where('key', 'theme')
                ->distinct()
                ->pluck('value')
                ->toArray(),
            'theme_setting'
        )->query(function ($query, $value) {
            $query->whereHas('settings', function ($q) use ($value) {
                $q->where('key', 'theme')->where('value', 'like', "%$value%");
            });
        }),
    ];
}
```

---

## Scope-Based Filters

```php
public function filters(): array
{
    return [
        FilterSelect::make('Period', [
            'today' => 'Today',
            'this_week' => 'This Week',
            'this_month' => 'This Month',
            'this_year' => 'This Year',
        ], 'period')
        ->query(function ($query, $value) {
            return match ($value) {
                'today' => $query->whereDate('created_at', today()),
                'this_week' => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
                'this_month' => $query->whereMonth('created_at', now()->month),
                'this_year' => $query->whereYear('created_at', now()->year),
                default => $query,
            };
        }),
    ];
}
```

---

## Computed / Virtual Filters

```php
public function filters(): array
{
    return [
        // Filter by existence of a relationship
        FilterBool::make('Has Orders')
            ->query(function ($query, $value) {
                if ($value === 'true') {
                    return $query->has('orders');
                }
                return $query->doesntHave('orders');
            }),

        // Filter by aggregate
        FilterSelect::make('Order Count', [
            'none' => 'No Orders',
            'few' => '1-5 Orders',
            'many' => '5+ Orders',
        ], 'order_count')
        ->query(function ($query, $value) {
            return match ($value) {
                'none' => $query->doesntHave('orders'),
                'few' => $query->has('orders', '>=', 1)->has('orders', '<=', 5),
                'many' => $query->has('orders', '>', 5),
                default => $query,
            };
        }),
    ];
}
```

---

## Dynamic Options from Database

```php
public function filters(): array
{
    return [
        // Options loaded from model
        FilterSelect::make('Category',
            \App\Models\Category::orderBy('name')->pluck('name', 'id')->toArray(),
            'category_id'
        ),

        // Cached options for performance
        FilterSelect::make('Country',
            Cache::remember('country-options', 3600, function () {
                return \App\Models\Country::pluck('name', 'id')->toArray();
            }),
            'country_id'
        ),

        // Options from enum
        FilterSelect::make('Status',
            collect(\App\Enums\OrderStatus::cases())
                ->mapWithKeys(fn($s) => [$s->value => $s->name])
                ->toArray(),
            'status'
        ),
    ];
}
```

---

## JSON Column Filter

```php
public function filters(): array
{
    return [
        FilterString::make('Metadata Key', 'metadata->key')
            ->query(function ($query, $value) {
                $query->where('metadata->key', 'like', "%$value%");
            }),

        FilterSelect::make('Language', [
            'en' => 'English',
            'es' => 'Spanish',
            'fr' => 'French',
        ], 'preferences->language'),
    ];
}
```

---

These filter examples cover the most common filtering patterns. Combine them freely based on your table's requirements!
