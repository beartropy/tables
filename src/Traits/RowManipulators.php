<?php

namespace Beartropy\Tables\Traits;

use Exception;

trait RowManipulators
{

    /**
     * @var array
     */
    public $yatable_expanded_rows = [];

    /**
     * @var bool
     */
    public $yatable_expanded_rows_is_component = false;

    /**
     * @var array
     */
    public $yatable_expanded_rows_content = [];

    /**
     * Remove a row from the table data (and cache).
     *
     * @param mixed $id
     * @param bool $resetSelected Whether to clear selection after removal.
     * @return void
     */
    public function removeRowFromTable($id, $resetSelected = true)
    {
        $data = $this->getAllData();
        $data = $data->reject(function ($item) use ($id) {
            return $item[$this->column_id] == $id;
        });
        if ($resetSelected) {
            $this->emptySelection();
        }
        $this->updateCacheData($data);
    }

    /**
     * Add a new row to the table data (and cache).
     *
     * @param array $row
     * @return void
     */
    public function addRowToTable($row)
    {
        $data = $this->getAllData();
        if (!isset($row['id'])) {
            $row['id'] = $row[$this->column_id];
        }
        $data->push($row);
        $this->updateCacheData($data);
    }

    /**
     * Toggle the expansion state of a row.
     *
     * @param mixed $rowId
     * @param mixed $content Content string or array if is_component is true.
     * @param bool $is_component Whether the content points to a Livewire component.
     * @return void
     * @throws Exception
     */
    public function toggleExpandedRow($rowId, $content, $is_component = false)
    {
        $this->yatable_expanded_rows_is_component = $is_component;
        if (in_array($rowId, $this->yatable_expanded_rows)) {
            // If the row is already expanded, remove it
            $this->yatable_expanded_rows = array_diff($this->yatable_expanded_rows, [$rowId]);
            unset($this->yatable_expanded_rows_content[$rowId]);
        } else {
            // Otherwise, add it to the expanded rows
            $this->yatable_expanded_rows[] = $rowId;
            if ($is_component) {
                if (!is_array($content) || !isset($content['component']) || !isset($content['parameters'])) {
                    throw new Exception("When toggleExpandedRow \$is_component is true \$content must be an array with keys component and parameters", 1);
                }
            }
            $this->yatable_expanded_rows_content[$rowId] = $content;
        }
    }

    /**
     * Update a row's data in the table (and cache).
     *
     * @param mixed $id
     * @param array $newData Key-value pairs to update.
     * @return void
     */
    public function updateRowOnTable($id, $newData)
    {
        $data = $this->getAllData();
        $data = $data->map(function ($item) use ($id, $newData) {
            if ($item[$this->column_id] == $id) {
                $item = array_merge($item, $newData);
            }
            return $item;
        });
        $this->updateCacheData($data);
    }

    /**
     * Expand the mobile details view for a row.
     *
     * @param mixed $rowId
     * @return void
     */
    public function expandMobileRow($rowId)
    {
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

    /**
     * Toggle a boolean column value for a row.
     *
     * Dispatches trigger method if defined on the column.
     *
     * @param mixed $id
     * @param string $column Column key.
     * @return void
     */
    public function toggleBoolean($id, $column)
    {

        $trigger = $this->columns->where('key', $column)->first()->trigger;
        if ($trigger) {
            $this->$trigger($id, $column);
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
