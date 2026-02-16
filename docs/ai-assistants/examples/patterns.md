# Advanced Patterns - Beartropy Tables

Ready-to-use patterns for bulk actions, inline editing, export, and more.

## Bulk Actions Table

### Livewire Component
```php
<?php

namespace App\Livewire;

use App\Models\User;
use Beartropy\Tables\BeartropyTable;
use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\Classes\Columns\BoolColumn;

class BulkUsersTable extends BeartropyTable
{
    public $model = User::class;

    public bool $has_bulk = true;
    public bool $with_pagination = true;

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')->sortable()->searchable(),
            Column::make('Email', 'email')->sortable()->searchable(),
            Column::make('Role', 'role')->sortable(),
            BoolColumn::make('Active', 'is_active')->centered(),
        ];
    }

    /**
     * Handle bulk delete action.
     */
    public function bulkDelete(): void
    {
        $selected = $this->getSelectedRows();

        if (empty($selected)) {
            return;
        }

        User::whereIn('id', $selected)->delete();

        $this->emptySelection();
        $this->refresh();

        session()->flash('success', count($selected) . ' users deleted.');
    }

    /**
     * Handle bulk status change.
     */
    public function bulkActivate(): void
    {
        $selected = $this->getSelectedRows();

        User::whereIn('id', $selected)->update(['is_active' => true]);

        $this->emptySelection();
        $this->refresh();
    }
}
```

### Blade Integration
```blade
<div>
    {{-- Bulk action buttons (shown when rows are selected) --}}
    @if(count($this->getSelectedRows()) > 0)
        <div class="flex gap-2 mb-4">
            <span class="text-sm font-medium">
                {{ count($this->getSelectedRows()) }} selected
            </span>
            <button wire:click="bulkActivate" class="text-sm text-blue-600">Activate</button>
            <button wire:click="bulkDelete" wire:confirm="Delete selected?" class="text-sm text-red-600">Delete</button>
        </div>
    @endif

    <livewire:bulk-users-table />
</div>
```

---

## Inline Editing Table

### Livewire Component
```php
<?php

namespace App\Livewire;

use App\Models\Product;
use Beartropy\Tables\BeartropyTable;
use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\Classes\Columns\ToggleColumn;

class EditableProductsTable extends BeartropyTable
{
    public $model = Product::class;

    public function columns(): array
    {
        return [
            // Text input editing
            Column::make('Name', 'name')
                ->sortable()
                ->searchable()
                ->editable(),

            // Select dropdown editing
            Column::make('Status', 'status')
                ->editable('select', [
                    ['value' => 'draft', 'label' => 'Draft'],
                    ['value' => 'published', 'label' => 'Published'],
                    ['value' => 'archived', 'label' => 'Archived'],
                ]),

            // Editable with different DB field
            Column::make('Category', 'category.name')
                ->editable('select', [
                    ['value' => 1, 'label' => 'Electronics'],
                    ['value' => 2, 'label' => 'Clothing'],
                    ['value' => 3, 'label' => 'Books'],
                ])
                ->setUpdateField('category_id'),

            // Toggle column for boolean fields
            ToggleColumn::make('Featured', 'is_featured')
                ->centered(),

            // Editable with custom callback
            Column::make('Priority', 'priority')
                ->editable('select', [
                    ['value' => 1, 'label' => 'Low'],
                    ['value' => 2, 'label' => 'Medium'],
                    ['value' => 3, 'label' => 'High'],
                ], 'handlePriorityUpdate'),
        ];
    }

    /**
     * Custom callback for priority updates.
     */
    public function handlePriorityUpdate(int $id, string $field, mixed $value, $table): void
    {
        $product = Product::find($id);
        $product->update([$field => $value]);

        // Custom side effects
        if ($value == 3) {
            // Notify team about high-priority product
        }
    }

    /**
     * Authorization for inline editing.
     */
    public function authorizeFieldUpdate(\Illuminate\Database\Eloquent\Model $record, string $field, mixed $value): bool
    {
        return auth()->user()->can('update', $record);
    }
}
```

---

## Table with Custom Data Transformation

### Livewire Component
```php
<?php

namespace App\Livewire;

use App\Models\Order;
use Beartropy\Tables\BeartropyTable;
use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\Classes\Columns\DateColumn;
use Beartropy\Tables\Classes\Columns\LinkColumn;

class OrdersTable extends BeartropyTable
{
    public $model = Order::class;

    public array $with = ['customer', 'items'];

    public function columns(): array
    {
        return [
            // Link to order detail
            LinkColumn::make('Order #', 'order_number')
                ->href(fn($row) => route('orders.show', $row->id))
                ->sortable(),

            // Relationship column
            Column::make('Customer', 'customer.name')
                ->sortable()
                ->searchable(),

            // Computed column from hasMany
            Column::make('Items', 'item_count')
                ->customData(fn($row) => $row->items->count() . ' items')
                ->centered(),

            // Formatted currency
            Column::make('Total', 'total')
                ->customData(fn($row) => '$' . number_format($row->total, 2))
                ->pushRight()
                ->styling('font-bold'),

            // Status with HTML badge
            Column::make('Status', 'status')
                ->customData(fn($row) => '<span class="px-2 py-1 rounded text-xs font-medium ' . match($row->status) {
                    'completed' => 'bg-green-100 text-green-800',
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'cancelled' => 'bg-red-100 text-red-800',
                    default => 'bg-gray-100 text-gray-800',
                } . '">' . ucfirst($row->status) . '</span>')
                ->toHtml()
                ->centered(),

            DateColumn::make('Ordered', 'created_at')
                ->outputFormat('M d, Y H:i')
                ->sortable(),
        ];
    }
}
```

---

## Table with Virtual Columns (hasMany Relationships)

### Livewire Component
```php
<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\UserSetting;
use Beartropy\Tables\BeartropyTable;
use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\Classes\Filters\FilterSelect;

class UserSettingsTable extends BeartropyTable
{
    public $model = User::class;

    public array $with = ['settings'];

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')->sortable()->searchable(),

            // Virtual column — data from hasMany, needs custom sort/search
            Column::make('Theme', 'theme_setting')
                ->customData(fn($row) => $row->settings->firstWhere('key', 'theme')?->value ?? '-')
                ->sortable(function ($query, $direction) {
                    $query->orderBy(
                        UserSetting::select('value')
                            ->whereColumn('user_settings.user_id', 'users.id')
                            ->where('key', 'theme'),
                        $direction
                    );
                })
                ->searchable(function ($query, $term) {
                    $query->orWhereHas('settings', function ($q) use ($term) {
                        $q->where('key', 'theme')->where('value', 'like', "%$term%");
                    });
                }),

            Column::make('Language', 'language_setting')
                ->customData(fn($row) => $row->settings->firstWhere('key', 'language')?->value ?? '-'),
        ];
    }

    public function filters(): array
    {
        return [
            // Virtual filter with custom query to match the virtual column
            FilterSelect::make('Theme',
                UserSetting::where('key', 'theme')
                    ->distinct()
                    ->pluck('value')
                    ->toArray(),
                'theme_setting'
            )->query(function ($query, $value) {
                $query->whereHas('settings', function ($q) use ($value) {
                    $q->where('key', 'theme')->where('value', $value);
                });
            }),
        ];
    }
}
```

---

## Table with Expanded Rows

### Livewire Component
```php
<?php

namespace App\Livewire;

use App\Models\Order;
use Beartropy\Tables\BeartropyTable;
use Beartropy\Tables\Classes\Columns\Column;

class ExpandableOrdersTable extends BeartropyTable
{
    public $model = Order::class;

    public array $with = ['items'];

    public function columns(): array
    {
        return [
            Column::make('Order #', 'order_number')->sortable(),
            Column::make('Customer', 'customer_name')->searchable(),
            Column::make('Total', 'total')
                ->customData(fn($row) => '$' . number_format($row->total, 2))
                ->pushRight(),
        ];
    }

    /**
     * Toggle expanded row with order items detail.
     */
    public function toggleDetail(int $orderId): void
    {
        $order = Order::with('items')->find($orderId);

        $html = '<div class="p-4 space-y-2">';
        foreach ($order->items as $item) {
            $html .= "<div class='flex justify-between'>";
            $html .= "<span>{$item->name}</span>";
            $html .= "<span>\${$item->price}</span>";
            $html .= '</div>';
        }
        $html .= '</div>';

        $this->toggleExpandedRow($orderId, $html);
    }
}
```

---

## Table with Theming

### Livewire Component
```php
<?php

namespace App\Livewire;

use App\Models\User;
use Beartropy\Tables\BeartropyTable;
use Beartropy\Tables\Classes\Columns\Column;

class ThemedUsersTable extends BeartropyTable
{
    public $model = User::class;

    public function mount(): void
    {
        parent::mount();

        // Set theme color
        $this->setTheme('blue');

        // Custom title
        $this->setTitle('User Management');

        // Enable row striping
        $this->stripRows();

        // Sticky header
        $this->sticky_header = true;
    }

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')->sortable()->searchable(),
            Column::make('Email', 'email')->sortable()->searchable(),
        ];
    }
}
```

---

## Complete Real-World Example

### Full-Featured Users Table
```php
<?php

namespace App\Livewire;

use App\Models\User;
use Beartropy\Tables\BeartropyTable;
use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\Classes\Columns\BoolColumn;
use Beartropy\Tables\Classes\Columns\DateColumn;
use Beartropy\Tables\Classes\Columns\LinkColumn;
use Beartropy\Tables\Classes\Columns\ToggleColumn;
use Beartropy\Tables\Classes\Filters\FilterString;
use Beartropy\Tables\Classes\Filters\FilterSelect;
use Beartropy\Tables\Classes\Filters\FilterBool;
use Beartropy\Tables\Classes\Filters\FilterDateRange;
use Beartropy\Tables\Classes\Filters\FilterSelectMagic;

class FullUsersTable extends BeartropyTable
{
    public $model = User::class;
    public array $with = ['profile'];
    public bool $with_pagination = true;
    public bool $has_bulk = true;
    public bool $showCardsOnMobile = true;

    public function mount(): void
    {
        parent::mount();
        $this->setTheme('blue');
        $this->setTitle('Users');
        $this->stripRows();
    }

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')
                ->sortable()->searchable()->cardTitle(),

            LinkColumn::make('Email', 'email')
                ->href(fn($row) => "mailto:{$row->email}")
                ->showOnCard(),

            Column::make('Role', 'role')
                ->editable('select', [
                    ['value' => 'admin', 'label' => 'Admin'],
                    ['value' => 'editor', 'label' => 'Editor'],
                    ['value' => 'viewer', 'label' => 'Viewer'],
                ]),

            ToggleColumn::make('Active', 'is_active')
                ->disableToggleWhen(fn($row) => !auth()->user()->can('update', $row))
                ->centered(),

            BoolColumn::make('Verified', 'email_verified_at')
                ->trueIs(fn($v) => !is_null($v))
                ->centered()
                ->hideOnMobile(),

            DateColumn::make('Joined', 'created_at')
                ->outputFormat('M d, Y')
                ->sortable()
                ->showOnCard(),
        ];
    }

    public function filters(): array
    {
        return [
            FilterString::make('Name', 'name'),
            FilterSelectMagic::make('Role', 'role'),
            FilterBool::make('Active', null, 'is_active'),
            FilterDateRange::make('Joined', 'created_at'),
        ];
    }

    public function authorizeFieldUpdate(\Illuminate\Database\Eloquent\Model $record, string $field, mixed $value): bool
    {
        return auth()->user()->can('update', $record);
    }

    public function bulkDelete(): void
    {
        $selected = $this->getSelectedRows();
        User::whereIn('id', $selected)->delete();
        $this->emptySelection();
        $this->refresh();
    }

    public function bulkDeactivate(): void
    {
        $selected = $this->getSelectedRows();
        User::whereIn('id', $selected)->update(['is_active' => false]);
        $this->emptySelection();
        $this->refresh();
    }
}
```

---

These patterns cover advanced table functionality. Combine them freely to build exactly the table your application needs!
