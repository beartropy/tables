<?php

namespace Beartropy\Tables\Traits;

use Beartropy\Tables\Collections\TableCollection;
use Illuminate\Support\Facades\Cache;

trait Data
{
    /**
     * Cache prefix used in the Data trait scope.
     */
    public string $cachePrefix = '';

    /**
     * Total count of all data records (before filtering).
     */
    public ?int $all_data_count = null;

    /**
     * Count of data records after filtering.
     */
    public ?int $filtered_data_count = null;

    /**
     * Strip modified row internal keys from the collection.
     *
     * Removes keys ending with '_original' and potentially restores original values.
     *
     * @param  \Illuminate\Support\Collection  $collection
     * @return \Illuminate\Support\Collection
     */
    public function stripModifiedRows($collection)
    {
        $collection = collect($collection);
        $collection = $collection->transform(function ($item) {
            foreach ($item as $key => $value) {
                if (str_ends_with($key, '_original')) {
                    $originalKey = substr($key, 0, -9);
                    $item[$originalKey] = $value;
                    unset($item[$key]);
                }
            }

            return $item;
        });

        return $collection;
    }

    /**
     * Get all original data without modifications.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllOriginalData()
    {
        return $this->stripModifiedRows($this->getAllData());
    }

    /**
     * Get all data available to the table.
     *
     * Fetches from model/database if configured, or returns cached data.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllData()
    {
        if ($this->model) {
            $data = $this->newQuery()->get();

            return $this->processCollection($data);
        }

        // For cached/array tables, processCollection ran on mount (different request),
        // so column labels must be set here for export to pick them up.
        TableCollection::setColumnLabels(
            collect($this->getFreshColumns())->pluck('label', 'key')->all()
        );

        return $this->getCachedData();
    }

    /**
     * Get original data after filters are applied.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAfterFiltersOriginalData()
    {
        return $this->stripModifiedRows($this->getAfterFiltersData());
    }

    /**
     * Get data after applying search, filters, and sorting.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAfterFiltersData()
    {
        if ($this->model) {
            $query = $this->newQuery();

            // Apply Search
            if (method_exists($this, 'applySearchToQuery')) {
                $this->applySearchToQuery($query);
            }
            // Apply Filters
            if (method_exists($this, 'applyFiltersToQuery')) {
                $this->applyFiltersToQuery($query);
            }
            // Apply Sort
            if (method_exists($this, 'applySortToQuery')) {
                $this->applySortToQuery($query);
            }

            $data = $query->get();
            $this->filtered_data_count = $data->count();

            return $this->processCollection($data);
        } else {
            $data = $this->filteredData();
            $data = $this->applyFilters($data);
            if (is_null($data)) {
                $this->filtered_data_count = 0;
            } else {
                $this->filtered_data_count = count($data);
            }

            return $data;
        }
    }

    /**
     * Get selected original data without modifications.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getSelectedOriginalData()
    {
        return $this->stripModifiedRows($this->getSelectedData());
    }

    /**
     * Get selected rows data.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getSelectedData()
    {
        if ($this->model) {
            $selectedIds = $this->getSelectedRows();
            if (empty($selectedIds)) {
                return collect([]);
            }
            $query = $this->newQuery()->whereIn($this->custom_column_id ?? 'id', $selectedIds);

            $data = $query->get();

            return $this->processCollection($data);
        }

        return $this->getAllData()->whereIn('id', $this->getSelectedRows())->values();
    }

    /**
     * Get a single row by its ID.
     *
     * @param  mixed  $id
     * @return mixed|null
     */
    public function getRowByID($id)
    {
        if ($this->model) {
            $row = $this->newQuery()->where($this->custom_column_id ?? 'id', $id)->first();

            if (! $row) {
                return null;
            }

            $metadata = $this->getColumnMetadata();

            return $this->transformRow($row, $metadata['customData'], $metadata['linkColumns'], $metadata['toggleColumns'], $metadata['cardTitleCallbacks']);
        }

        return $this->getAllData()->where('id', $id)->first();
    }

    /**
     * Get data for the current page.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCurrentPageData()
    {
        if ($this->with_pagination) {
            $paginatedData = $this->getPageData($this->currentPageNumber);
            $this->forcePageNumber = $this->currentPageNumber;

            return collect($paginatedData->items());
        } else {
            $paginatedData = $this->getAfterFiltersData();

            return $this->sortData($paginatedData);
        }
    }

    /**
     * Parse and cache the initial data.
     *
     * Clears existing cache, resets pagination, and re-processes data from source.
     * Dispatches events based on data availability.
     *
     * @return void
     */
    public function parseData()
    {

        $this->clearData();
        $this->resetPage();

        $data = $this->data();

        $this->dispatch('yatDataGathered');
        if ($data) {
            $this->dispatch('yatDataGatheredWithData');
        } else {
            $this->dispatch('yatDataGatheredEmpty');
        }

        $this->userData = $this->processCollection(collect($data));
    }

    /**
     * Process a collection of rows.
     *
     * Applies transformations (custom data, links, toggles) to each row.
     *
     * @param  \Illuminate\Support\Collection  $collection
     * @return \Illuminate\Support\Collection
     */
    public function processCollection($collection)
    {
        $metadata = $this->getColumnMetadata();

        $processed = $collection->map(function ($row) use ($metadata) {
            // Normalize stdClass to ArrayObject so callbacks support both
            // $row->key (object syntax) and $row['key'] (array syntax)
            if ($row instanceof \stdClass) {
                $row = new \ArrayObject(
                    json_decode(json_encode($row), true),
                    \ArrayObject::ARRAY_AS_PROPS
                );
            }

            return $this->transformRow(
                $row,
                $metadata['customData'],
                $metadata['linkColumns'],
                $metadata['toggleColumns'],
                $metadata['cardTitleCallbacks'],
            );
        });

        // Keys like `name_original` are intentionally preserved for search/filter access on unmodified values.

        // Wrap in TableCollection with column labels so GenericExport can use display names as headers.
        $tableCollection = new TableCollection($processed->all());
        TableCollection::setColumnLabels(
            collect($this->getFreshColumns())->pluck('label', 'key')->all()
        );

        return $tableCollection;
    }

    /**
     * Transform a single row.
     *
     * @param  mixed  $row  The row object or array.
     * @param  array  $customData
     * @param  array  $linkColumns
     * @param  array  $toggleColumns
     * @param  array  $cardTitleCallbacks
     * @return array The transformed row data.
     */
    public function transformRow($row, $customData, $linkColumns, $toggleColumns, $cardTitleCallbacks = [])
    {
        $parsedRow = [];
        // Support object or array row
        if (is_object($row) && method_exists($row, 'toArray')) {
            $rowArray = $row->toArray();
        } else {
            $rowArray = (array) $row;
        }

        foreach ($this->columns as $column) {
            $columnIndex = $column->index ?? $column->key;
            // Fallback for accessors that might not be in toArray() but are accessible on the object
            // Also support Dot Notation using data_get
            if (is_object($row)) {
                $parsedValue = data_get($row, $columnIndex);
            } else {
                $parsedValue = data_get($rowArray, $columnIndex, '');
            }
            // If data_get matched nothing and returned default (null/empty), check direct usage if needed?
            // data_get is robust.
            $rawValue = $parsedValue;

            // Handle Custom Data
            if (isset($customData[$column->key])) {
                $parsedValue = call_user_func_array($customData[$column->key]['function'], [$row, $rawValue ?? null]);
                $parsedRow[strtolower($column->key.'_original')] = $rawValue ?? '';
                $column->has_modified_data = true;
            }

            // Handle Links
            if (isset($linkColumns[$column->key])) {
                $href = call_user_func_array($linkColumns[$column->key]['function'], [$row, $rawValue ?? null]);
                $text = $column->text ?? ($rawValue ?? '');
                $parsedValue = json_encode([$href, $text]);
                $parsedRow[strtolower($column->key.'_original')] = $text ?? '';
                $column->has_modified_data = true;
            }

            // Handle Toggles
            if (isset($toggleColumns[$column->key])) {
                $parsedValue = $parsedValue === $column->what_is_true;
                if (isset($toggleColumns[$column->key]['disableToggleWhen'])) {
                    $parsedRow[strtolower($column->key.'_disabled')] = call_user_func_array($toggleColumns[$column->key]['disableToggleWhen'], [$row]);
                }
                if (isset($toggleColumns[$column->key]['hideToggleWhen'])) {
                    $parsedRow[strtolower($column->key.'_hidden')] = call_user_func_array($toggleColumns[$column->key]['hideToggleWhen'], [$row]);
                }
            }

            // Handle Date formatting
            if (property_exists($column, 'isDate') && $column->isDate) {
                if ($parsedValue !== null && $parsedValue !== '') {
                    try {
                        $date = $column->inputFormat
                            ? \DateTime::createFromFormat($column->inputFormat, $parsedValue)
                            : new \DateTime($parsedValue);

                        if ($date) {
                            $parsedValue = $date->format($column->outputFormat ?? 'Y-m-d');
                        }
                    } catch (\Exception $e) {
                        // Keep original value if parsing fails
                    }
                } else {
                    $parsedValue = $column->emptyValue ?? '';
                }
            }

            $parsedRow[$column->key] = $parsedValue;

            if ($column->updateField) {
                if (is_object($row)) {
                    $updateValue = data_get($row, $column->updateField);
                } else {
                    $updateValue = data_get($rowArray, $column->updateField, '');
                }
                $parsedRow[$column->updateField] = $updateValue;
            }

            // Handle Card Title Callback
            if (isset($cardTitleCallbacks[$column->key])) {
                $parsedRow[$column->key.'_card_title'] = call_user_func_array($cardTitleCallbacks[$column->key]['function'], [$row, $parsedValue ?? null]);
            }
        }

        if ($this->custom_column_id) {
            $parsedRow['id'] = $rowArray[$this->custom_column_id] ?? null;
        }

        return $parsedRow;
    }

    /**
     * Get all column metadata in a single pass over getFreshColumns().
     *
     * Consolidates custom data, link columns, toggle columns, and card title callbacks
     * to avoid iterating the column definitions multiple times.
     *
     * @return array{customData: array, linkColumns: array, toggleColumns: array, cardTitleCallbacks: array}
     */
    public function getColumnMetadata(): array
    {
        $customData = [];
        $linkColumns = [];
        $toggleColumns = [];
        $cardTitleCallbacks = [];

        foreach ($this->getFreshColumns() as $column) {
            if (property_exists($column, 'customData') && is_callable($column->customData)) {
                $customData[$column->key] = [
                    'function' => $column->customData,
                ];
            }

            if (property_exists($column, 'isLink') && $column->isLink && property_exists($column, 'href')) {
                $linkColumns[$column->key] = [
                    'function' => $column->href,
                ];
            }

            if (property_exists($column, 'disableToggleWhen') && is_callable($column->disableToggleWhen)) {
                $toggleColumns['disable'][$column->key] = [
                    'function' => $column->disableToggleWhen,
                ];
            }
            if (property_exists($column, 'hideToggleWhen') && is_callable($column->hideToggleWhen)) {
                $toggleColumns['hide'][$column->key] = [
                    'function' => $column->hideToggleWhen,
                ];
            }

            if (property_exists($column, 'cardTitleCallback') && is_callable($column->cardTitleCallback)) {
                $cardTitleCallbacks[$column->key] = [
                    'function' => $column->cardTitleCallback,
                ];
            }
        }

        return [
            'customData' => $customData,
            'linkColumns' => $linkColumns,
            'toggleColumns' => $toggleColumns,
            'cardTitleCallbacks' => $cardTitleCallbacks,
        ];
    }

    /**
     * Export the current table data to clipboard (CSV/TSV).
     *
     * @param  \Illuminate\Support\Collection  $collection
     * @param  bool  $tabs  If true, uses tabs (TSV); otherwise uses commas (CSV).
     * @return void
     */
    public function exportToClipboard($collection, bool $tabs = true)
    {
        if ($collection->isEmpty()) {
            $this->csvString = '';

            return;
        }

        // Use column definitions to filter data and build proper headers
        $columns = $this->getFreshColumns();
        $columnKeys = collect($columns)->pluck('key')->all();
        $columnLabels = collect($columns)->pluck('label', 'key')->all();

        $collection = $collection->map(function ($row) use ($columnKeys) {
            return collect($row)->only($columnKeys)->all();
        });

        $headers = array_map(function ($key) use ($columnLabels) {
            return $columnLabels[$key] ?? $key;
        }, array_keys($collection->first()));
        $lines = [];

        if ($tabs) {
            // TSV: Tab-separated values
            $lines[] = implode("\t", $headers);

            foreach ($collection as $row) {
                $escaped = array_map(function ($v) {
                    if (is_array($v)) {
                        $v = implode(';', $v);
                    }

                    return trim(preg_replace("/\s+/", ' ', $v));
                }, $row);

                $lines[] = implode("\t", $escaped);
            }
        } else {
            // CSV: Comma-separated values with quotes
            $lines[] = '"'.implode('","', $headers).'"';

            foreach ($collection as $row) {
                $escaped = array_map(function ($v) {
                    if (is_array($v)) {
                        $v = implode(';', $v);
                    }

                    return str_replace('"', '""', $v);
                }, $row);

                $lines[] = '"'.implode('","', $escaped).'"';
            }
        }

        $this->csvString = implode("\n", $lines);

        $this->dispatch('copy-yatable-to-clipboard', csv: $this->csvString);
    }
}
