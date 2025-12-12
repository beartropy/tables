<?php

namespace Beartropy\Tables\Traits;

use Exception;

trait RowManipulators
{

    public $yatable_expanded_rows = [];
    public $yatable_expanded_rows_is_component = false;
    public $yatable_expanded_rows_content = [];

    public function removeRowFromTable($id, $resetSelected = true) {
        $data = $this->getAllData();
        $data = $data->reject(function ($item) use ($id) {
            return $item[$this->column_id] == $id;
        });
        if ($resetSelected) {
            $this->emptySelection();
        }
        $this->updateCacheData($data);
    }

    public function addRowToTable($row) {
        $data = $this->getAllData();
        if (!isset($row['id'])) {
            $row['id'] = $row[$this->column_id];
        }
        $data->push($row);
        $this->updateCacheData($data);
    }

    public function toggleExpandedRow($rowId,$content,$is_component=false) {
        $this->yatable_expanded_rows_is_component = $is_component;
        if (in_array($rowId, $this->yatable_expanded_rows)) {
            // If the row is already expanded, remove it
            $this->yatable_expanded_rows = array_diff($this->yatable_expanded_rows, [$rowId]);
            unset($this->yatable_expanded_rows_content[$rowId]);
        } else {
            // Otherwise, add it to the expanded rows
            $this->yatable_expanded_rows[] = $rowId;
            if ($is_component) {
                if(!is_array($content) || !isset($content['component']) || !isset($content['parameters'])) {
                    throw new Exception("When toggleExpandedRow \$is_component is true \$content must be an array with keys component and parameters", 1);
                }
            }
            $this->yatable_expanded_rows_content[$rowId] = $content;
        }
    }

    public function updateRowOnTable($id, $newData) {
        $data = $this->getAllData();
        $data = $data->map(function ($item) use ($id, $newData) {
            if ($item[$this->column_id] == $id) {
                $item = array_merge($item, $newData);
            }
            return $item;
        });
        $this->updateCacheData($data);
    }

    public function expandMobileRow($rowId) {
        // Find the row data
        $row = $this->getAllData()->firstWhere($this->column_id, $rowId);

        if (!$row) return;

        // Render the details view
        $content = view('yat::livewire.parts.mobile-details', [
            'row' => $row,
            'columns' => $this->mobileCollapsedColumns,
            'row_id_name' => $this->column_id
        ])->render();

        $this->toggleExpandedRow($rowId, $content);
    }

    public function toggleBoolean($id, $column) {

        $trigger = $this->columns->where('key',$column)->first()->trigger;
        if ($trigger) {
            $this->$trigger($id,$column);
        }

        $data = $this->getAllData();
        $data = $data->map(function ($item) use ($id, $column) {
            if ($item[$this->column_id] == $id) {
                $item[$column] = !$item[$column];
            }
            return $item;
        });
        $this->updateCacheData($data);

    }
}
