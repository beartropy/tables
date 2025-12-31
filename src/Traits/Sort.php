<?php

namespace Beartropy\Tables\Traits;

trait Sort
{

    /**
     * @var string|null
     */
    public $sortColumn; // Default column to sort by

    /**
     * @var string
     */
    public $sortDirection = 'asc'; // Default sort direction

    /**
     * Set sort direction to ascending.
     *
     * @param bool $bool
     * @return void
     */
    public function setSortDirectionAsc(Bool $bool)
    {
        if ($bool) {
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Set sort direction to descending.
     *
     * @param bool $bool
     * @return void
     */
    public function setSortDirectionDesc(Bool $bool)
    {
        if ($bool) {
            $this->sortDirection = 'desc';
        }
    }

    /**
     * Set the column to sort by.
     *
     * @param string $column
     * @return void
     */
    public function setSortColumn(String $column)
    {
        $this->sortColumn = $column;
    }

    /**
     * Toggle sorting for a specific column.
     *
     * @param string $column
     * @return void
     */
    public function sortBy($column)
    {
        $this->emptySelection();
        $colObject = $this->columns->where('key', $column)->first();

        if (!$colObject || !$colObject->isSortable) {
            return;
        }

        $sort_column = $colObject->key;

        if ($this->sortColumn === $sort_column) {
            // If already sorting by this column, toggle the direction
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            // Otherwise, set to this column and default to ascending
            $this->sortColumn = $sort_column;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Sort the data collection (Array mode).
     *
     * @param \Illuminate\Support\Collection $data
     * @return \Illuminate\Support\Collection
     */
    public function sortData($data)
    {

        if ($this->sortColumn) {
            // Retrieve fresh columns to access the closure if sorting by user selection
            $freshCols = $this->getFreshColumns();
            $sort_column = $freshCols->where('key', $this->sortColumn)->first();



            if ($sort_column && $sort_column->has_modified_data) {
                $sort_column = $sort_column->key . "_original";
            } else if ($sort_column) {
                $sort_column = $sort_column->key;
            } else {
                // If the sort column is not found in fresh columns,
                // we cannot proceed with sorting based on it.
                // This might happen if the column was dynamically added/removed
                // or if the sortColumn property is out of sync.
                // For now, we'll just return the data as is, unsorted.
                return $data;
            }

            if ($this->sortDirection === 'desc') {

                /*                 $data = $data->sortByDesc(function ($item) use ($sort_column) {
                    return $item[strtolower($sort_column)];
                },SORT_NATURAL|SORT_FLAG_CASE); */
                $data = $data->sortByDesc(function ($item) use ($sort_column) {
                    $value = $item[strtolower($sort_column)];
                    return is_array($value) ? implode(' ', $value) : $value;
                }, SORT_NATURAL | SORT_FLAG_CASE);
            } else {
                /*                 $data = $data->sortBy(function ($item) use ($sort_column) {
                    return $item[strtolower($sort_column)];
                },SORT_NATURAL|SORT_FLAG_CASE); */
                $data = $data->sortBy(function ($item) use ($sort_column) {
                    $value = $item[strtolower($sort_column)];
                    return is_array($value) ? implode(' ', $value) : $value;
                }, SORT_NATURAL | SORT_FLAG_CASE);
            }
        }

        return $data;
    }

    /**
     * Apply sorting to the Eloquent query.
     *
     * Handles relationship sorting and custom callbacks.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function applySortToQuery($query)
    {
        if ($this->sortColumn) {
            // Retrieve fresh columns to access the closure if sorting by user selection
            $freshCols = $this->getFreshColumns();
            $sort_column = $freshCols->where('key', $this->sortColumn)->first();

            if ($sort_column) {
                // Check for custom sort callback
                if (property_exists($sort_column, 'sortableCallback') && is_callable($sort_column->sortableCallback)) {
                    call_user_func($sort_column->sortableCallback, $query, $this->sortDirection);
                } else {
                    // Standard sort
                    // Use index if available as it represents the data path
                    $targetObject = $sort_column->index ?? $sort_column->key;

                    if (str_contains($targetObject, '.')) {
                        $parts = explode('.', $targetObject);
                        $relationName = $parts[0];
                        $columnName = $parts[1];

                        $model = $query->getModel();

                        if (method_exists($model, $relationName)) {
                            $relation = $model->{$relationName}();

                            // Handle HasOne relationship
                            if ($relation instanceof \Illuminate\Database\Eloquent\Relations\HasOne) {
                                $relatedModel = $relation->getRelated();
                                $relatedTable = $relatedModel->getTable();
                                $foreignKey = $relation->getForeignKeyName(); // profile.user_id
                                $localKey = $relation->getLocalKeyName(); // users.id
                                $parentTable = $model->getTable();

                                $subQuery = $relatedModel->newQuery()
                                    ->select($columnName)
                                    ->whereColumn("{$relatedTable}.{$foreignKey}", "{$parentTable}.{$localKey}");

                                $query->orderBy($subQuery, $this->sortDirection);
                                return $query;
                            }
                            // Handle BelongsTo relationship
                            elseif ($relation instanceof \Illuminate\Database\Eloquent\Relations\BelongsTo) {
                                $relatedModel = $relation->getRelated();
                                $relatedTable = $relatedModel->getTable();
                                $foreignKey = $relation->getForeignKeyName(); // post.user_id
                                $ownerKey = $relation->getOwnerKeyName(); // user.id
                                $parentTable = $model->getTable();

                                $subQuery = $relatedModel->newQuery()
                                    ->select($columnName)
                                    ->whereColumn("{$relatedTable}.{$ownerKey}", "{$parentTable}.{$foreignKey}");

                                $query->orderBy($subQuery, $this->sortDirection);
                                return $query;
                            }
                        }
                    }

                    $query->orderBy($targetObject, $this->sortDirection);
                }
            }
        }
        return $query;
    }
}
