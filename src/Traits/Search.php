<?php

namespace Beartropy\Tables\Traits;

trait Search
{

    public $yat_global_search = ''; // Search input binding
    public $yat_global_search_label;

    public function setSearchLabel(string $label) {
        $this->yat_global_search_label = $label;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

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
        $searchableKeys = collect($data->first() ?? [])->keys()->filter(function ($key) use ($data) {
            // Include keys ending with "_search" or those without corresponding "_search" keys
            if (str_ends_with($key, '_original')) {
                return true;
            }
            $baseKey = preg_replace('/_search$/', '', $key);
            return !array_key_exists($baseKey . '_original', $data->first());
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

    public function applySearchToQuery($query)
    {
        $searchTerm = trim($this->yat_global_search);
        
        if (empty($searchTerm)) {
            return $query;
        }

        return $query->where(function ($q) use ($searchTerm) {
            foreach ($this->columns as $column) {
                // If it's a custom data column (calculated), we can't search it in DB easily unless suppressed
                // We'll rely on the user to mark columns as unsearchable if needed, but for now we search all defined columns
                // that match DB columns. We should skip if marked hidden? 
                // The prompt says "leaving pagination, search etc on side of database".
                
                // We assume column key is the DB column name.
                // We'll check if the column should be searchable.
                
                // For now, search all keys.
                $q->orWhere($column->key, 'like', '%' . $searchTerm . '%');
            }
        });
    }
}

