<?php

namespace Beartropy\Tables\Traits;

use Beartropy\Tables\Classes\Columns\Column;
use Illuminate\Database\Eloquent\Model;

trait Editable
{
    /**
     * Authorize a field update before persisting.
     *
     * Override this method in your table component to add policy checks or custom authorization logic.
     *
     * @param  Model  $record  The Eloquent model being updated.
     * @param  string  $field  The field/column key being updated.
     * @param  mixed  $value  The new value.
     */
    public function authorizeFieldUpdate(Model $record, string $field, mixed $value): bool
    {
        return true;
    }

    /**
     * Update a specific field for a row.
     *
     * Handles inline editing updates. Supports callbacks, Eloquent models, and array data.
     * Dispatches 'table-field-updated' event.
     *
     * @param  mixed  $id  The row ID.
     * @param  string  $field  The field/column key to update.
     * @param  mixed  $value  The new value.
     * @return bool|void Returns true if successful, false otherwise.
     */
    public function updateField($id, $field, $value)
    {
        $column = \collect($this->columns)->firstWhere('key', $field);

        if (! $column) {
            return;
        }

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
                        if (! $this->authorizeFieldUpdate($record, $field, $value)) {
                            return false;
                        }

                        if ($value === '') {
                            $value = null;
                        }

                        $saveField = $column->updateField ?? $field;
                        $saved = $record->update([$saveField => $value]);

                        $this->clearData();

                        return $saved;
                    } else {
                        \Illuminate\Support\Facades\Log::warning("BeartropyTable Record not found: $id");

                        return false;
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('BeartropyTable Editable Error: '.$e->getMessage());

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
