<?php

namespace Beartropy\Tables\Traits;

use Beartropy\Tables\Classes\Columns\Column;
use Illuminate\Database\Eloquent\Model;

trait Editable
{
    /**
     * Update a specific field for a row.
     *
     * Handles inline editing updates. Supports callbacks, Eloquent models, and array data.
     * Dispatches 'table-field-updated' event.
     *
     * @param mixed $id The row ID.
     * @param string $field The field/column key to update.
     * @param mixed $value The new value.
     * @return bool|void Returns true if successful, false otherwise.
     */
    public function updateField($id, $field, $value)
    {
        // Find the column definition
        $column = \collect($this->columns)->firstWhere('key', $field);

        if (!$column) {
            return;
        }

        // Emit expanded event for UI feedback if needed
        $this->dispatch('table-field-updated', id: $id, field: $field, value: $value);

        // 1. Component Method by Name (String)
        if (is_string($column->editableCallback) && method_exists($this, $column->editableCallback)) {
            $this->{$column->editableCallback}($id, $field, $value, $this);
            return true;
        }

        // 2. User Callback (Closure)
        if ($column->editableCallback && is_callable($column->editableCallback)) {
            call_user_func($column->editableCallback, $id, $field, $value, $this);
            return true;
        }

        // 3. Eloquent Model Update
        if ($this->model) {
            if (class_exists($this->model)) {
                try {
                    $record = $this->model::find($id);
                    if ($record) {
                        // Convert empty strings to null
                        if ($value === '') {
                            $value = null;
                        }

                        $saveField = $column->updateField ?? $field;
                        $record->$saveField = $value;
                        $saved = $record->save();

                        // Invalidate cache to ensure persistence across pagination
                        $this->clearData();

                        \Illuminate\Support\Facades\Log::info("YATBaseTable Saved ($id): Field=$saveField Value=" . var_export($value, true) . " Result=" . ($saved ? 'true' : 'false'));
                        return $saved;
                    } else {
                        \Illuminate\Support\Facades\Log::warning("YATBaseTable Record not found: $id");
                        return false;
                    }
                } catch (\Exception $e) {
                    // Log error to help debugging
                    \Illuminate\Support\Facades\Log::error("YATBaseTable Editable Error: " . $e->getMessage());
                    return false;
                }
            }
            return false;
        }

        // 4. Array Data Update (Fallback)
        $this->updateRowOnTable($id, [$field => $value]);
        return true;
    }
}
