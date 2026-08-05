<div align="center">
    <h1>🐻 Beartropy Tables</h1>
    <p><strong>A flexible Livewire table component for the TALL stack</strong></p>
    <p>Tailwind • Alpine • Laravel • Livewire</p>
</div>

<div align="center">
    <a href="https://packagist.org/packages/beartropy/tables"><img src="https://img.shields.io/packagist/v/beartropy/tables.svg?style=flat-square&color=indigo" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/beartropy/tables"><img src="https://img.shields.io/packagist/dt/beartropy/tables.svg?style=flat-square&color=blue" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/beartropy/tables"><img src="https://img.shields.io/packagist/l/beartropy/tables?style=flat-square&color=slate" alt="License"></a>
</div>

<br>

This is yet another Laravel Livewire table package provided "as is". It offers the essentials for building data tables: filtering, searching, sorting, bulk actions, and column toggling.

## 📚 Documentation

The full documentation for this package involves installation, configuration, and advanced usage examples.

👉 **[Read the full documentation at beartropy.com/tables](https://beartropy.com/tables)**

## ✨ Key Features

*   **Searchable**: Built-in search functionality to quickly find data.
*   **Sortable**: Easy column sorting support for any field.
*   **Filters**: Flexible filtering system to refine datasets.
*   **Bulk Actions**: Perform actions on multiple selected rows at once.
*   **Column Toggling**: Allow users to show or hide columns dynamically.
*   **TALL Stack Optimized**: Built seamlessly for Tailwind CSS, Alpine.js, Laravel, and Livewire.

## 🔐 Inline Editing & Toggle Authorization

Inline editing and boolean toggles are write actions reachable from the browser, so the table decides what may be written.

Only columns declared with `->editable()` can be written through `updateField()`. A column that merely exists is not a write target, and `toggleBoolean()` ignores any column key that is not part of `columns()`.

On top of that, three hooks let you apply your own policy checks. **All three default to permitting the write**, so a table that exposes sensitive columns must override them — they are hooks, not automatic protection.

```php
use Illuminate\Database\Eloquent\Model;

// Eloquent-backed tables: receives the resolved record.
public function authorizeFieldUpdate(Model $record, string $field, mixed $value): bool
{
    return auth()->user()->can('update', $record);
}

// Callback columns and array/stdClass tables: no model exists, so this
// receives the raw row id instead of a record.
public function authorizeFieldUpdateRaw($id, string $field, mixed $value): bool
{
    return auth()->user()->can('editTables');
}

// Boolean toggle columns.
public function authorizeToggle($id, string $column): bool
{
    return auth()->user()->can('editTables');
}
```

## 🚀 Quick Installation

You can install the package via composer:

```bash
composer require beartropy/tables
```

## 🤝 Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

> [!NOTE]
> **Disclaimer**: This software is provided "as is", without warranty of any kind, express or implied. Use at your own risk.