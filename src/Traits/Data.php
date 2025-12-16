<?php

namespace Beartropy\Tables\Traits;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

trait Data
{

    public $cachePrefix = '';
    public $all_data_count;
    public $filtered_data_count;

    public function stripModifiedRows($collection) {
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

    public function getAllOriginalData() {
        return $this->stripModifiedRows($this->getAllData());
    }

    public function getAllData() {
        if ($this->model) {
            $query = $this->model::query();
             if (!empty($this->with)) {
                $query->with($this->with);
            }
            $data = $query->get();
            return $this->processCollection($data);
        }
        return $this->getCachedData();
    }

    public function getAfterFiltersOriginalData() {
        return $this->stripModifiedRows($this->getAfterFiltersData());
    }

    public function getAfterFiltersData() {
        if ($this->model) {
            $query = $this->model::query();
            
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

            if (!empty($this->with)) {
                $query->with($this->with);
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

    public function getSelectedOriginalData() {
        return $this->stripModifiedRows($this->getSelectedData());
    }

    public function getSelectedData() {
        if ($this->model) {
            $selectedIds = $this->getSelectedRows();
            if (empty($selectedIds)) {
                return collect([]);
            }
            $query = $this->model::whereIn($this->custom_column_id ?? 'id', $selectedIds);
            
             // Apply Sort if desired? Usually selected data is just the data.
             // But let's return it as is or maybe sorted by default order.
             // Let's keep distinct logic minimal.

            if (!empty($this->with)) {
                $query->with($this->with);
            }
            
            $data = $query->get();
            return $this->processCollection($data);
        }
        return $this->getAllData()->whereIn('id', $this->getSelectedRows())->values();
    }

    public function getRowByID($id) {
        if ($this->model) {
            $query = $this->model::where($this->custom_column_id ?? 'id', $id);
            if (!empty($this->with)) {
                $query->with($this->with);
            }
            $row = $query->first();

            if (!$row) return null;
            
            // We return the transformed row to maintain consistency 
            // because other methods return transformed rows (arrays).
            // However, verify if getRowByID usually is used for editing where we might want the object?
            // The original implementation: `return $this->getAllData()->where('id', $id)->first();`
            // `getAllData` calls `getCachedData` which calls `parseData`.
            // `parseData` populates `userData` with transformed arrays.
            // So existing implementation returns an ARRAY.
            
            // We should transform this single row.
            // But processCollection works on collection.
            // We have `transformRow`.
            
            $customData = $this->getCustomData();
            $linkColumns = $this->getLinkColumns();
            $toggleColumns = $this->getToggleColumns();
            
            return $this->transformRow($row, $customData, $linkColumns, $toggleColumns);
        }
        return $this->getAllData()->where('id', $id)->first();
    }

    public function getCurrentPageData() {
        if ($this->with_pagination) {
            $paginatedData = $this->getPageData($this->currentPageNumber);
            $this->forcePageNumber = $this->currentPageNumber;
            return collect($paginatedData->items());
        } else {
            $paginatedData=$this->getAfterFiltersData();
            return $this->sortData($paginatedData);
        }
    }

    public function parseData() {

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

    public function processCollection($collection) {
        $customData = $this->getCustomData();
        $linkColumns = $this->getLinkColumns();
        $toggleColumns = $this->getToggleColumns();
        
        // We'll calculate columns upfront if not already done, 
        // to avoid repeated getter calls if we were using getters, 
        // essentially ensuring we have the column definitions ready.
        // In current implementation $this->columns is a collection/array already set.
        
        $processed = $collection->map(function($row) use ($customData, $linkColumns, $toggleColumns) {
            return $this->transformRow($row, $customData, $linkColumns, $toggleColumns);
        });

        // The original implementation had a map at the end to remove _original
        // But looking at transformRow logic closer, it seems we might want to keep _original 
        // if we want to allow searching on original values in the array implementation.
        // However, the original code did:
        // $this->userData->push($parsedRow); AND THEN $this->userData->map(... except(['_original']))
        // Wait, if we remove _original, searching traits that rely on it might break?
        // Let's check Traits/Search.php line 36: if (str_ends_with($key, '_original'))
        // So the original code REMOVES _original from $this->userData?
        
        // Let's re-read the original Data.php carefully.
        // Line 131: $this->userData = $this->userData->map(function ($item) { return collect($item)->except(['_original'])->toArray(); });
        // Yes, it specifically removes it.
        // So we should replicate that behavior for userData, BUT, Search.php line 40 checks for _original?
        // Ah, Search.php::filteredData() calls $this->getAllData().
        // getAllData() -> getCachedData().
        // If cached data doesn't have _original, search might fail if it relies on it.
        
        // Let's look closely at `Data.php`:
        // 131: except(['_original'])
        // Traits/Search.php line 40: !array_key_exists($baseKey . '_original', $data->first())
        
        // Use cases for _original:
        // 1. stripModifiedRows calls getAllData() which gets cached data.
        
        // Wait, if parseData consistently removes `_original`, then `_original` effectively never exists in the cache?
        // If so, `stripModifiedRows` logic:
        // if (str_ends_with($key, '_original')) ...
        // would never find anything.
        
        // Let's check parseData again in the file view I got.
        // 131: $this->userData = $this->userData->map(function ($item) {
        // 132:    return collect($item)->except(['_original'])->toArray();
        // 133: });
        
        // If `except(['_original'])` is called, it removes the key literally named "_original" (if it existed), OR does it remove keys ENDING in _original?
        // Laravel collection `except` takes keys.
        // The code in loop (104, 111) does: $parsedRow[strtolower($column->key."_original")] = ...
        // So the keys are like `name_original`, `status_original`.
        // `except(['_original'])` would only remove a key named exactly "_original".
        // It WON'T remove `name_original`.
        // So my previous assumption that it clears them was based on a misunderstanding of `except` vs wildcard. 
        // Unless I missed something, `except(['_original'])` is likely a bug or a leftover if there aren't keys literally named `_original`. 
        // But okay, I will strictly follow the existing logic which preserves `name_original`.
        
        return $processed;
    }

    public function transformRow($row, $customData, $linkColumns, $toggleColumns) {
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
                $parsedRow[strtolower($column->key."_original")] = $rawValue ?? '';
                $column->has_modified_data = true;
            }
            
            // Handle Links
            if (isset($linkColumns[$column->key])) {
                $href = call_user_func_array($linkColumns[$column->key]['function'], [$row, $rawValue ?? null]);
                $text = $column->text ?? ($rawValue ?? '');
                $parsedValue = json_encode(array($href, $text));
                $parsedRow[strtolower($column->key."_original")] = $text ?? '';
                $column->has_modified_data = true;
            }
            
            // Handle Toggles
            if(isset($toggleColumns[$column->key])) {
                $parsedValue = $parsedValue === $column->what_is_true;
                if (isset($toggleColumns[$column->key]['disableToggleWhen'])) {
                    $parsedRow[strtolower($column->key."_disabled")] = call_user_func_array($toggleColumns[$column->key]['disableToggleWhen'], [$row]);
                }
                if (isset($toggleColumns[$column->key]['hideToggleWhen'])) {
                    $parsedRow[strtolower($column->key."_hidden")] = call_user_func_array($toggleColumns[$column->key]['hideToggleWhen'], [$row]);
                }
            }
            
            $parsedRow[$column->key] = $parsedValue;
        }

        if ($this->custom_column_id) {
            $parsedRow['id'] = $rowArray[$this->custom_column_id] ?? null;
        }
        
        // If the original `except(['_original'])` was intended to remove something specific, I'll keep it here just in case,
        // but applied to the collected row if needed. 
        // For now, I'll return the array.
        return $parsedRow;
    }

    public function getCustomData() {
        $customData = [];
        foreach ($this->getFreshColumns() as $column) {
            if (property_exists($column, 'customData') && is_callable($column->customData)) {
                $customData[$column->key] = [
                    'function' => $column->customData,
                ];
            }
        }
        return $customData;
    }

    public function getLinkColumns() {
        $linkColumns = [];
        foreach ($this->getFreshColumns() as $column) {
            if (property_exists($column,'isLink') && $column->isLink) {
                if (property_exists($column, 'href')) {
                    $linkColumns[$column->key] = [
                        'function' => $column->href,
                    ];
                }
            }
        }
        return $linkColumns;
    }

    public function getToggleColumns() {
        $toggleColumns = [];
        foreach ($this->getFreshColumns() as $column) {
            
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
        }
        return $toggleColumns;
    }

    public function exportToClipboard($collection, bool $tabs = true)
    {
        if ($collection->isEmpty()) {
            $this->csvString = '';
            return;
        }

        // Clean collection: remove _original keys
        $collection = $collection->map(function ($row) {
            return collect($row)->reject(function ($value, $key) {
                return str_ends_with($key, '_original');
            })->all();
        });

        $headers = array_keys($collection->first());
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
            $lines[] = '"' . implode('","', $headers) . '"';

            foreach ($collection as $row) {
                $escaped = array_map(function ($v) {
                    if (is_array($v)) {
                        $v = implode(';', $v);
                    }
                    return str_replace('"', '""', $v);
                }, $row);

                $lines[] = '"' . implode('","', $escaped) . '"';
            }
        }

        $this->csvString = implode("\n", $lines);

        $this->dispatch('copy-yatable-to-clipboard', csv: $this->csvString);
    }
}
