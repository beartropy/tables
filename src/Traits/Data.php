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
        return $this->getCachedData();
    }

    public function getAfterFiltersOriginalData() {
        return $this->stripModifiedRows($this->getAfterFiltersData());
    }

    public function getAfterFiltersData() {
        $data = $this->filteredData();
        $data = $this->applyFilters($data);
        if (is_null($data)) {
            $this->filtered_data_count = 0;
        } else {
            $this->filtered_data_count = count($data);
        }
        return $data;
    }

    public function getSelectedOriginalData() {
        return $this->stripModifiedRows($this->getSelectedData());
    }

    public function getSelectedData() {
        return $this->getAllData()->whereIn('id', $this->getSelectedRows())->values();
    }

    public function getRowByID($id) {
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

        $this->userData = collect();

        $data = $this->data();

        $this->dispatch('yatDataGathered');
        if ($data) {
            $this->dispatch('yatDataGatheredWithData');
        } else {
            $this->dispatch('yatDataGatheredEmpty');
        }

        $customData = $this->getCustomData();
        $linkColumns = $this->getLinkColumns();
        $toggleColumns = $this->getToggleColumns();

        foreach ($data as $key => $row) {
            $parsedRow = [];
            $row = (array) $row;
            foreach ($this->columns as $column) {
                $parsedValue = $row[$column->index] ?? '';
                if (isset($customData[$column->key])) {
                    $parsedValue = call_user_func_array($customData[$column->key]['function'], [$row, $row[$column->index] ?? null]);
                    $parsedRow[strtolower($column->key."_original")] = $row[$column->index] ?? '';
                    $column->has_modified_data = true;
                }
                if (isset($linkColumns[$column->key])) {
                    $href = call_user_func_array($linkColumns[$column->key]['function'], [$row, $row[$column->index] ?? null]);
                    $text = $column->text ?? $row[$column->index];
                    $parsedValue = json_encode(array($href,$text));
                    $parsedRow[strtolower($column->key."_original")] = $text ?? '';
                    $column->has_modified_data = true;
                }
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
            if ($this->has_bulk && $this->custom_column_id) {
                $parsedRow['id'] = $row[$this->custom_column_id];
            }
            $this->userData->push($parsedRow);
        }

        $this->userData = $this->userData->map(function ($item) {
            return collect($item)->except(['_original'])->toArray();
        });
    }

    public function getCustomData() {
        $customData = [];
        foreach ($this->columns as $key => $column) {
            if (is_callable($column->customData)) {
                $customData[$column->key] = ['index'=> $column->index, 'function' => $column->customData];
            }
            unset($this->columns[$key]->customData);
        }
        return $customData;
    }

    public function getLinkColumns() {
        $linkColumns = [];
        foreach ($this->columns as $key => $column) {
            if (property_exists($column,'isLink') && $column->isLink) {
                $linkColumns[$column->key] = ['index'=> $column->index, 'function' => $column->href];
            }
            unset($this->columns[$key]->href);
        }
        return $linkColumns;
    }

    public function getToggleColumns() {
        $toggleColumns = [];
        foreach ($this->columns as $key => $column) {
            if (property_exists($column,'isToggle') && $column->isToggle) {
                $toggleColumns[$column->key] = ['index'=> $column->index];
                if (is_callable($column->disableToggleWhen)) {
                    $toggleColumns[$column->key]['disableToggleWhen'] = $column->disableToggleWhen;
                    unset($this->columns[$key]->disableToggleWhen);
                }
                if (is_callable($column->hideToggleWhen)) {
                    $toggleColumns[$column->key]['hideToggleWhen'] = $column->hideToggleWhen;
                    unset($this->columns[$key]->hideToggleWhen);
                }
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
