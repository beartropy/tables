# Yet Another Table

[![Laravel](https://img.shields.io/static/v1?label=laravel&message=%E2%89%A510.0&color=0078BE&logo=laravel&style=flat-square")](https://packagist.org/packages/beartropy/tables)
[![Version](https://img.shields.io/packagist/v/beartropy/tables)](https://packagist.org/packages/beartropy/tables)
[![Downloads](https://img.shields.io/packagist/dt/beartropy/tables)](https://packagist.org/packages/beartropy/tables)
[![Licence](https://img.shields.io/packagist/l/beartropy/tables)](https://packagist.org/packages/beartropy/tables)


This is yet another laravel livewire table and come as is.
You can filter, you can sort, you can bulk, toggle columns, the basics. The data input is a collection/array, we cant handle models.

Consider using other packages like the ones that heavily inspired this one (rappasoft livewire tables/powergrid tables) for better performance and more and better features.

## Requirements

- Laravel 10/11
- Livewire 3

## Eloquent Support

While maintaining backward compatibility with collections/arrays, version 2.0+ introduces native Eloquent Model support. This enables server-side pagination, searching, and sorting directly on the database.

### Basic Usage

To use Eloquent mode, define the `public $model` property in your component instead of returning data in the `data()` method.

```php
class UsersTable extends YATBaseTable
{
    public $model = User::class;
    // ...
}
```

### Relationship Support

You can display data from related models using dot notation and manage eager loading.

#### Eager Loading
Avoid N+1 queries by defining the `$with` property.

```php
public $with = ['role', 'settings'];
```

#### Dot Notation
Access related attributes easily in your column definitions.

```php
Column::make('Role', 'role.name')
```

#### HasMany Relationships (Custom Data)
For `HasMany` relationships where you need to filter specific items (like getting a specific setting from a collection), use `customData`.

```php
Column::make('Theme', 'theme_setting')
    ->customData(function($row) {
        // Assuming $row->settings is a collection of user settings
        return $row->settings->firstWhere('key', 'theme')?->value ?? 'N/A';
    })
```

#### Searching Relationships
Searching works automatically with dot notation columns. A search for a `role.name` column will automatically perform a `whereHas` query.


## Installation

```
composer require beartropy/tables
```

You may want to let the user save the table arrange of columns. If so, publish the migrations...

```
php artisan vendor:publish --provider="Beartropy\\Tables\\YATProvider" --tag="migrations"
```

...and run them.


```
php artisan migrate
```

That's it. You are ready to go.


### Optionals

You may want to publish the lang settings and change all the 6 keys. Up to you.

```
php artisan vendor:publish --provider="Beartropy\\Tables\\YATProvider" --tag="lang"
```


## Creating a table

### The artisan way

To create a table you can run the following:

```
php artisan make:yatable MyBrandNewTable
```

### Rendering the table

This is nothing more than a livewire component, so the rendering is just the same:

#### Fullpage layout

Add the layout attribute right before the class with your layout:

```
#[Layout('layouts.app')]
class MyBrandNewTable extends YATBaseTable
```

#### Included in blade view

```
<livewire:mybrandnewtable />
```

[Livewire docs to render component](https://livewire.laravel.com/docs/components#rendering-components)


## Base functions

### settings(): void

Here you can set some settings to the table in general: title, column id, bulk, state handler, etc.

### data(): collection

Here you return the data. As is, a collection of arrays or objects, you return it we display it. When you create a table an example of data is in the stub in order to see what is expected.

### columns(): array

Here you return an array of column instances with the modifiers you want.

### filters(): array

Here you return an array of filter instances.

### options(): array

Here you return an array with function => label form. 

This will generate an "Options" button in the view that will display a dropdown with the options you set and they will call the function established as key. 

## Passing data to the table

To pass data to the component, either from a route or from an attribute in the `<livewire>` tag, you only need to declare it as a public property in the table component.

```
<livewire:mytable color_type="primary">
```


```
class MyTable extends YATBaseTable
{
    public $color_type;
...
```

## Using the mount() function

If you need to use the `mount()` function in the table component, be sure to call the parent `mount()` order to avoid errors.

```
public function mount() {
    parent::mount();

    mySoNeededMountFunction();
}
```

# Contents

## [Settings](#settings-1)

### [View](#view)
- [Set table title](#set-table-title)
- [Override title classes](#override-title-classes)
- [Set a custom header](#set-a-custom-header)
- [Set component classes](#set-component-classes)
- [Add table classes](#add-table-classes)
- [Set table classes](#set-table-classes)
- [Set sticky header](#set-sticky-header)
- [Table spinner](#table-spinner)
- [Table spinner view](#table-spinner-view)
- [Modals View](#modals-view)
- [Set Table Theme](#set-table-theme)
- [Add Custom Buttons](#add-custom-buttons)
- [Custom View Slots](#custom-view-slots)
- [Mobile Cards (Automatic)](#mobile-cards-automatic)
- [Force Card View](#force-card-view)
- [Mobile Modal Buttons](#mobile-modal-buttons)

### [Sorting](#sorting-1)
- [Set default sort column](#set-default-sort-column)
- [Set default sort direction asc](#set-default-sort-direction-asc)
- [Set default sort direction desc](#set-default-sort-direction-desc)

### [Bulk](#bulk-1)
- [Enable bulk](#enable-bulk)
- [Get selected rows](#get-selected-rows)

### [Table state handler](#table-state-handler)
- [Enable state handler](#enable-state-handler)

### [Pagination](#pagination-1)
- [Use pagination](#use-pagination)
- [Set default per page rows value](#set-default-per-page-rows-value)
- [Set options for per page rows](#set-options-for-per-page-rows)

### [Misc](#misc-1)
- [Set column ID](#set-column-id)
- [Show column toggle button](#show-column-toggle-button)
- [Add row to table](#add-row-to-table)
- [Remove row from table](#remove-row-from-table)
- [Expanded rows](#expanded-rows)
- [Cache Prefix](#cache-prefix)



## [Columns](#columns-1)

- [Styling](#styling-1)
- [Custom Data](#custom-data)
- [Hidding columns](#hidding-columns)
- [Hidding columns from selector](#hidding-columns-from-selector)
- [Hide column by default](#hide-column-by-default)
- [Boolean columns](#boolean-columns)
- [Link columns](#link-columns)
- [Mobile Card Title](#mobile-card-title)
- [Show on Card Body](#show-on-card-body)

## [Filters](#filters-1)

- [String filter](#string-filter)
- [DateRange Filter](#daterange-filter)
- [Select Filter](#select-filter)
- [Select Magic Filter](#select-magic-filter)
- [Bool Filter](#bool-filter)


## [Options](#options-1)

## [Export](#export-1)

## Settings

### View

#### Set table title
`setTitle(string $title)`

By default the component shows only the table. Use this to display a title.

```
$this->setTitle('My brand new table');
```

#### Override title classes
`overrideTitleClasses(string $classes)`

The title is displayed in a single div, if you want to change the default styling you can use this function to override the default classes and apply your own.

> Tailwind classes only

```
$this->overrideTitleClasses('text-3xl text-red-700 dark:text-red-300');
```

#### Set a custom header
`setCustomHeader(string $html)`

If you want, you can add your own html in the header in order to replace the title at all or add some stuff.

This will render at the top of the component any html you pass.


```
$this->setCustomHeader('
    <div class="flex justify-between">
        <div class="text-2xl font-bold">My brand new table</div>
        <div><button href="/">Go back</button></div>
    </div>
');
```

#### Set component classes
`setComponentClasses(string $classes)`

Here you can set classes for the component wrapper, this includes the buttons, the pagination, the title, etc. Its the whole view.

> Tailwind classes only

```
$this->setComponentClasses('bg-black p-4');
```

#### Add table classes
`addTableClasses(string $classes)`

Here you can add classes to the table wrapper. Only the table, buttons, pagination, title, etc are outside this wrapper.

> Tailwind classes only

```
$this->addTableClasses('text-xl');
```

#### Set table classes
`setTableClasses(string $classes)`

Same as `addTableClasses`, but instead of adding classes to the wrapper you override the defaults leaveing only what you pass to the function.

> Tailwind classes only

```
$this->setTableClasses('max-h-64 md:max-h-80 lg:max-h-[30rem] overflow-y-scroll');
```

#### Set sticky header
`setStickyHeader()`

Disabled by defualt, enable this to stick the table header, useful when disabled pagination with a scrolling table.

```
$this->setStickyHeader();
```

#### Table spinner
`useTableSpinner(bool $bool)`

Enabled by default, set this to `false` to disable the loading spinner.

```
$this->useTableSpinner();
```

#### Table spinner view
`setTableSpinnerView(string $view)`

If `useTableSpinner` is activated, you can use your own blade to display the spinner/message you want when loading.

```
$this->setTableSpinnerView('myviews.myspinner');
```

#### Modals View
`setModalsView(string $view)`

This adds the passed view at the bottom of the component, useful for modals.

```
$this->setModalsView('myviews.mymodals');
```

#### Set Table Theme
`setTheme(string $theme)`

Sets the color theme for the table. Available themes are:
`slate`, `gray`, `zinc`, `neutral`, `stone`, `red`, `orange`, `amber`, `yellow`, `lime`, `green`, `emerald`, `teal`, `cyan`, `sky`, `blue`, `indigo`, `violet`, `purple`, `fuchsia`, `pink`, `rose`.

```php
$this->setTheme('zinc');
```

#### Add Custom Buttons
`addButtons(array $buttons)`

Add custom buttons to the table header (right side). The array keys should be unique identifiers and values can be any data needed by your custom view.
To render these buttons, the component uses `yat::livewire.parts.custom-buttons` by default, but you can override the view logic if needed.

```php
$this->addButtons(['create' => 'Create User', 'export' => 'Export Data']);
```

#### Custom View Slots
The table provides several slots to inject custom Blade views at specific positions in the header.

- `setMostLeftView(string $view)`: Far left, before the filter button.
- `setLessLeftView(string $view)`: Left, between global search and the filter button (or search if no filter).
- `setLessRightView(string $view)`: Right, before custom buttons.
- `setMostRightView(string $view)`: Far right, after pagination.

```php
$this->setMostRightView('my.custom.settings-icon');
```

#### Mobile Cards (Automatic)
`showCardsOnMobile(bool $bool)`

Use this to automatically switch to a card view layout when a mobile device is detected.

```php
$this->showCardsOnMobile(true);
```

#### Force Card View
`useCards(bool $bool)`

Use this to force the card view layout regardless of the device type (e.g. for a card grid view on desktop). On desktop, cards will be arranged in a responsive grid.

```php
$this->useCards(true);
```

#### Mobile Modal Buttons
`addCardModalButtons(array $buttons)`

When clicking a card title (if configured), a details modal appears. You can add custom action buttons to the footer of this modal.
The action method will automatically receive the **entire row data** as a parameter.

```php
$this->addCardModalButtons([
    ["label" => 'Edit', "action" => "editRow", "color" => "blue"],
    ["label" => 'Delete', "action" => "deleteRow", "color" => "red"]
]);
```

```php
public function editRow($row) {
    // $row contains all the row data
    // $id = $row['id'];
}
```


### Sorting

#### Set default sort column

`setSortColumn(string $column)`

By default data is displayed as is, you can set a default sort by a column.

```
$this->setSortColumn('createtime');
```

#### Set default sort direction asc

`setSortDirectionAsc(bool $boolean)`

If *setSortColumn* is set, by default the order will be 'asc', but you can enforce that if necessary.

```
$this->setSortDirectionAsc(true);
```

#### Set default sort direction desc

`setSortDirectionDesc(bool $boolean)`

If *setSortColumn* is set, you can set the direction 'desc' by default.

```
$this->setSortDirectionDesc(true);
```

#### Set search placeholder
`setSearchLabel(string $title)`

By default the global search placeholder is "Search", you can change it for whatever you want here.

```
$this->setSearchLabel('Find');
```

### Bulk

#### Enable bulk
`hasBulk(bool $boolean)`

Disabled by default. 
Set hasBulk to true to enable the functionality.
This will use the 'id' column to identify the row, if there is no 'id' column in your data you must set which column to use with [setColumnID](#set-column-id)

> Bulk functionality can be combined with [Options](#options-1)

#### Get selected rows

`getSelectedRows()`

This will return an array of ids of the current selected options.


```
$ids = $this->getSelectedWRows();
```

#### Remove row from table

`removeRowFromTable(string $id, ?bool $resetSelected = true)`

Remove a row by its id from the table.

> By default this function reset the selected rows, if you dont want that pass a `false` as a second parameter.


```
$this->removeRowFromTable(3);
```

### Table state handler

#### Enable state handler
`useStateHandler(bool $boolean)`

> In order to enable this feature you need to publish and run the migration of the package.

Disabled by default. When enabled in column toggle button the user can save the current set of column's visibility. Each time the component is reloaded the columns will be visibile according to user election.

This functionality distincts users and tables, you can have many to many, each will get their custom visualization per table.

```
$this->useStateHandler(true);
```

### Pagination

#### Use pagination
`usePagination(bool $bool)`

Enabled by default, set this to `false` to disable pagination.

#### Set default per page rows value
`setPerPageDefault(int $number)`

By default 10 rows are displayed. Change this to show as many as you want per page.

```
$this->setPerPageDefault(25);
```

#### Set options for per page rows
`setPerPageOptions(int $number)`

Default value: ["10", "15", "25", "50"].

```
$this->setPerPageOptions(["10","20","50","100"]);
```

### Misc

#### Set column ID
`setColumnID(string $column)`

By default 'id' is the assumed column for identifying a row in order to use bulk functions. If you want to set another column of your data you can do that using this.

```
$this->setColumnID('ticket');
```

#### Show column toggle button
`showColumnToggle(bool $bool)`

Enabled by default, you can disable this button and remove it from view.

```
$this->showColumnToggle(false);
```

#### Add row to table
`$this->addRowToTable($row)`

You can add a row to the table dinamically passing all the attributes required to form a row in your table.

```
$this->addRowToTable(["id"=>43,"color"=>"slate",...]);
```

#### Remove row from table
`$this->removeRowFromTable($id, $resetSelected = true)`

You can delete a row to the table dinamically passing the row ID, if you want to leave the "selectedRows" data, set $resetSelected to false.

```
$this->removeRowFromTable(43);
```

#### Expanded rows
`$this->toggleExpandedRow($rowId, $content, $is_component=false)`

You can trigger this function in order to create and display on demand a new row under the given row in $rowId.

This function handles two types of content: html content and livewire component.

When $is_component is set to false you can pass html to the $content variable and it will be rendered as {!! $content !!}

When $is_component is set to true, the content variable must be an array with the keys "component" and "parameters", as name of the component and array with paramteres to pass to it.

```
$content="<div class="text-base">More data!</div>";
$this->toggleExpandedRow(43,$content);

----

$content=[
    "component" => "route.to.my.livewire.component",
    "parameters" => ["my_custom_parameter" => "some_data"]
];
$this->toggleExpandedRow(43,$content,true);
```

#### Cache Prefix
`setCachePrefix(string $prefix)`

Sets a custom prefix for the cache keys used by the table. This is useful when you have multiple instances of the same table or need to segregate cache data specifically.
Beartropy Tables supports caching for both authenticated users and guest users (via session ID).

```php
$this->setCachePrefix('my-custom-table');
```


## Columns
`Column::make(string label, ?string $field)`
In order to render the data the way you need it, you have to create a "columns" function returning an array of any of the Column objects we provide.

```
public function columns(): array {
    return [
        Column::make('Name','name')
            ->styling('text-lg font-bold')
            ->hideFromSelector(true),

        Column::make('Hex Code','hex'),

        Column::make('Color')
            ->customData(function($row, $value){
                return '<span style="color:'.$row['hex'].'">██████████████</span>';
            })
            ->toHtml(),

        LinkColumn::make('Google it','name')
            ->href(function($row, $value){
                return "https://www.google.com/search?q=".$value;
            })
            ->text('Google'),

        BoolColumn::make('Primary','isPrimary')
    ];
}
```

### Column

The Column class provides several modifiers to customize your table:

#### Styling
`styling(string $classes)`

You can pass classes to add to all `<td>` in the column. The classes will be prefixed with `!` in order to override other conflicting classes.

```
Column::make('Big text column','bigtext')
    ->styling('text-7xl')
```

#### Custom Data
`customData(Closure $function)`

To customize the data of the column you can pass any html in a closure function with `$row` and `$value` variable available.

```
Column::make('Upper Name','name')
    ->customData(function($row, $value){
        return strtoupper($value);
    })
```

```
Column::make('Full Name')
    ->customData(function($row, $value){
        return $row['firstname'] . " " . $row['lastname'];
    })
```

> Tip: if you pass html, you need to add the ->toHtml() modifier

```
Column::make('Big Name','name')
    ->customData(function($row, $value){
        return '<div class="text-3xl">'.$value.'</div>';
    })->toHtml()
```

#### Hidding columns
`hideWhen(bool $bool)`

Hides the column from the view. When `true` the user cannot access this column at all.

```
Column::make('Only Admins','sensitive-row')
    ->hideWhen(Auth::user()->isAdmin)
```

#### Hidding columns from selector
`hideFromSelector(bool $bool)`

Hides the column from the toggle dropdown.

```
Column::make('Always visible','force-data')
    ->hideFromSelector(true)
```

#### Hide column by default
`isVisible(bool $bool)`

By default all columns are visible. IF you set this to `true` the table will render with this column hidden, but the user can display it any time with the column toggle switch.

```
Column::make('Not so important data','meh-column')
    ->isVisible(false)
```

#### Boolean columns
`BoolColumn::make(string $label, string $field)`

You can use this column to boolean values, they will render in table view as ✔️ and ❌.

You can set what is considered `true` for a strict comparison with the `trueIs()`.

Also, you can customize the label/icons to appear in each case with `trueLabel()` and `falseLabel()`

```
BoolColumn::make('User status','isActive')
    ->trueIs('enabled')
    ->trueLabel('User Active')
    ->falseLabel('<i class="icon"></i>')
```

#### Link columns
`LinkColumn::make(string $label, ?string $field)`

Link column takes an destination and generates a link with the closure function.

```
LinkColumn::make('Edit user','id')
    ->text('Edit')
    ->href(function($row, $value){
        return '/edit/'.$value;
    })
```



#### Mobile Card Title
`cardTitle(bool $bool)`

Sets this column as the primary title for the mobile card view. The title becomes clickable and opens the details modal.

```php
Column::make('Name', 'name')->cardTitle()
```

#### Show on Card Body
`showOnCard(bool $bool)`

Explicitly includes this column in the body of the mobile card. Columns without this (or `cardTitle`) will not be shown on the card itself, but will still appear in the details modal.

```php
Column::make('Description', 'desc')->showOnCard()
```


#### Custom Sorting & Searching
You can define custom logic for sorting and searching, which is useful for calculated columns or complex relationships (e.g., HasMany).

```php
Column::make('Theme', 'theme_setting')
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
    });
```

## Filters
`[Filter]::make(string label, ?string $column)`

In addition to the search input that filters globally on the table, you can add filters by column.

> Note: if you pass only the *label* parameter, the filter will search for a column with same label and match, if you want to specify a column use the second parameter to assign the filter to the column label you want.

To enable filters, you need to create a "filters" function that returns an array of any of the Filter objects we provide.

```
public function filters(): array {
    return [
        FilterString::make('name'),
        FilterDateRange::make('Created At')
    ];
}
```

### String filter
`FilterString::make(string $label, ?string $column)`

This filter will search in column with label `$label` and filter the table accordingly.

```
FilterString::make('name') # This will search in column with label 'name'
```

```
FilterString::make('Edad', 'age') # This will search in column with label 'age'
```

### DateRange Filter
`FilterDateRange::make(string $label, ?string $column)`

This filter uses flatpickr to display a date range select and filter the table based on user input.

```
FilterDateRange::make('created_at') # This will search in column with label 'name'
```

### Select Filter
`FilterSelect::make(string $label, array $options)`

This filter displays a `select` with the options given.

```
FilterSelect::make('type',["primary","secondary"])
```

### Select Magic Filter
`FilterSelectMagic::make(string $label, array $options)`

Same as select filter, but with magic.
This filter with get all values from the column given, make the unique and display them.

```
[
    ["id"=>1, "color"=>"red", "type"=>"Primary"],
    ["id"=>2, "color"=>"violet", "type"=>"Secondary"],
    ["id"=>2, "color"=>"green", "type"=>"Primary"],
]

FilterSelectMagic::make('type')
# Will display Primary and Secondary in the <select>
```

### Bool filter
`FilterBool::make(string $label, ?array $compared_with = null, ?string $index = null)`

This filter displays a `toggle` to make boolean comparisons.
By default it will compare `true` and `false`, but you can pass an array `["true" => "My True value", "false" => "My false value"]` and it will compare to that.

```
FilterBool::make('isprimary')
```


### Custom Filter Logic
`->query(callable $callback)`

For complex scenarios where standard filtering isn't enough (e.g., filtering by a value in a `HasMany` relation), you can define a custom query callback.

```php
FilterString::make('Theme', 'theme_setting')->query(function($query, $value, $filter) {
    // $value is the user input
    $query->whereHas('settings', function($q) use ($value) {
        $q->where('key', 'theme')->where('value', 'like', "%$value%");
    });
});
```

## Options

You can add an additional button next to the column toggle to display a list of custom options/actions.
This options/actions will 'wire:click' the key given.

```
public function options(): array {
    return [
        'export' => 'Export selected rows',
        'remove' => 'Delete selected rows',
    ];
}

public function remove() {
    foreach ($this->getSelectedRows() as $id) {
        $this->removeRowFromTable($id);
    }
}
```

In this example, when user clicks on 'Delete selected rows' the function 'remove' will be called.

> Make sure you implement this functions in your component in order to avoid errors.


## Export

You have three methods in order to handle the data to export:

```
$this->getAllData() # Will return collection of all data

$this->getAfterFiltersData() # Will return collection of filtered data (global search and custom filters)

$this->getSelectedData() # Will return collection of data by selected rows
```

If you have [Laravel Excel](https://laravel-excel.com/) the stub comes with three functions added to the Options dropdown in order to export the data by this criterias that will work out of the box.

If you have other ways to expor the data or simply dont want to export at all feel free to delete the functions in the component.

