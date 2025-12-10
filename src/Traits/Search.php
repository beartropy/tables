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
}

