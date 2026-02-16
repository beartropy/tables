---
name: bt-tables-setup
description: Help users install and configure Beartropy Tables in their Laravel/Livewire projects
version: 1.0.0
author: Beartropy
tags: [beartropy, tables, installation, setup, configuration]
---

# Beartropy Tables Setup Guide

You are an expert in helping users install and configure Beartropy Tables in their Laravel applications.

---

## Requirements

- PHP >= 8.2
- Laravel >= 11.x
- Livewire >= 3.x
- Beartropy UI (required dependency, installed automatically)

---

## Installation

### Step 1: Install via Composer

```bash
composer require beartropy/tables
```

### Step 2: Publish Migrations

```bash
php artisan vendor:publish --tag=migrations
```

This publishes the `create_yat_user_table_config` migration for persisting user column preferences.

### Step 3: Run Migrations

```bash
php artisan migrate
```

### Step 4: Publish Language Files (optional)

```bash
php artisan vendor:publish --tag=lang
```

---

## Creating a Table Component

Use the artisan command to scaffold a new table:

```bash
php artisan make:btable MyTable
```

Or with a model:

```bash
php artisan make:btable MyTable --model=App\\Models\\User
```

---

## Basic Usage

### Array-based Table

```php
class MyTable extends BeartropyTable
{
    public function data(): array
    {
        return [
            ['id' => 1, 'name' => 'Alice', 'email' => 'alice@example.com'],
            ['id' => 2, 'name' => 'Bob', 'email' => 'bob@example.com'],
        ];
    }

    public function columns(): array
    {
        return [
            Column::make('Name', 'name'),
            Column::make('Email', 'email'),
        ];
    }
}
```

### Model-based Table

```php
use App\Models\User;
use Beartropy\Tables\BeartropyTable;
use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\Classes\Columns\ToggleColumn;

class UserTable extends BeartropyTable
{
    public $model = User::class;

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')->searchable(),
            Column::make('Email', 'email')->searchable(),
            ToggleColumn::make('Active', 'is_active'),
        ];
    }
}
```

### Rendering in Blade

```blade
<livewire:user-table />
```

---

## Configuration

The package uses view namespace `yat::` and translation namespace `yat::yat.*`.

Key features are configured per-component via properties, method overrides, and the `settings()` method on your table class:
- `$model` — Eloquent model class for DB-backed tables
- `$with` — eager loading relationships
- `$with_pagination` — enable/disable pagination
- `$has_bulk` — enable bulk selection
- `$has_counter` — show row numbers
- `columns()` — define table columns
- `filters()` — define table filters
- `options()` — define bulk action options
- `settings()` — programmatic configuration (theming, pagination defaults, search label, component size, state handler, etc.)

### Settings Example

```php
public function settings(): void
{
    $this->setTheme('beartropy');
    $this->setComponentSize('sm');
    $this->setTitle('Users');
    $this->setPerPageDefault(25);
    $this->setSearchLabel('Search users...');
    $this->useStateHandler(true);
}
```

---

## AI Coding Skills

Install Beartropy skills for AI assistants:

```bash
php artisan beartropy:skills
```

---

## Common Issues & Solutions

### Table not rendering
Ensure the Livewire component is registered. The package auto-registers `BeartropyTable` (and the legacy alias `YATBaseTable`), but custom components need to follow Livewire 3 auto-discovery conventions (namespace `App\Livewire`).

### Filters not working
Verify your `filters()` method returns valid Filter objects and that column keys match your data structure.

### Search case sensitivity
Model-mode search uses `LOWER()` wrapping for consistent case-insensitive behavior across database engines.
