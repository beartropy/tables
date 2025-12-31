<?php

namespace Beartropy\Tables\Traits;

trait Search
{

    /**
     * @var string
     */
    public $yat_global_search = ''; // Search input binding

    /**
     * @var string|null
     */
    public $yat_global_search_label;

    /**
     * @var bool
     */
    public $useGlobalSearch = true;

    /**
     * Enable or disable global search.
     *
     * @param bool $status
     * @return void
     */
    public function useGlobalSearch(bool $status = true)
    {
        $this->useGlobalSearch = $status;
    }

    /**
     * Set the label for the global search input.
     *
     * @param string $label
     * @return void
     */
    public function setSearchLabel(string $label)
    {
        $this->yat_global_search_label = $label;
    }

    /**
     * Handle updates to the global search input.
     *
     * Resets pagination when search query changes.
     *
     * @return void
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Filter the data based on global search (Array mode).
     *
     * @return \Illuminate\Support\Collection
     */
    public function filteredData()
    {

        $data = $this->getAllData();

        // Ensure the search term is properly trimmed and lowercased
        $searchTerm = strtolower(trim($this->yat_global_search));

        // If no search term, return the original collection
        if (empty($searchTerm)) {
            return $data;
        }

        // Preprocess the keys to search
        $searchableKeys = $this->getFreshColumns()->filter(function ($column) {
            return $column->isSearchable;
        })->map(function ($column) {
            return $column->key;
        });

        // Filter the collection
        return $data->filter(function ($item) use ($searchTerm, $searchableKeys) {
            foreach ($searchableKeys as $key) {
                if (isset($item[$key]) && is_array($item[$key])) $item[$key] = implode(' ', $item[$key]);
                if (isset($item[$key]) && str_contains(strtolower($item[$key]), strtolower($searchTerm))) {
                    return true; // Match found
                }
            }
            return false; // No match
        });
    }

    /**
     * Apply global search to the Eloquent query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function applySearchToQuery($query)
    {
        $searchTerm = trim($this->yat_global_search);

        if (empty($searchTerm)) {
            return $query;
        }

        return $query->where(function ($q) use ($searchTerm) {
            foreach ($this->getFreshColumns() as $column) {
                // Check for custom search callback
                if (property_exists($column, 'searchableCallback') && is_callable($column->searchableCallback)) {
                    call_user_func($column->searchableCallback, $q, $searchTerm);
                    continue;
                }

                // If not explicitly searchable, we might skip? 
                // Existing implementation searched everything. 
                // But now we have $column->isSearchable.
                // Assuming default true for backward compat.
                if (property_exists($column, 'isSearchable') && $column->isSearchable === false) {
                    continue;
                }

                // Use index if available as it represents the data path (e.g. profile.bio)
                $targetObject = $column->index ?? $column->key;

                // Determine if column target is a relationship (contains dot)
                if (str_contains($targetObject, '.')) {
                    $parts = explode('.', $targetObject);
                    $attribute = array_pop($parts);
                    $relation = implode('.', $parts);

                    $q->orWhereHas($relation, function ($relQuery) use ($attribute, $searchTerm) {
                        $relQuery->where($attribute, 'like', '%' . $searchTerm . '%');
                    });
                } else {
                    $q->orWhere($targetObject, 'like', '%' . $searchTerm . '%');
                }
            }
        });
    }
}
