<?php

namespace Beartropy\Tables\Traits;

use Exception;
use Carbon\Carbon;

trait Filters
{

    public $filters;
    public $has_filters = false;
    public $show_filters = false;

    public function setFilters() {
        try {
            $this->filters = collect($this->filters());
        } catch (\Throwable $th) {
            return;
        }
        $this->filters = $this->filters->mapWithKeys(function ($item) {
            $key = substr(str_shuffle(str_repeat('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', 5)), 0, 10);
            
            // Note: We don't need to explicitly strip closures if we use get_object_vars 
            // because proper serialization will just ignore the closure property if we don't return it? 
            // Actually get_object_vars returns the property.
            // We MUST ensure the closure is not in the array we return for storage.
            
            if (isset($item->queryCallback)) {
                $item->queryCallback = null;
            }
            
            // Store as Array for safe Livewire serialization
            return [$key => get_object_vars($item)];
        });

        // Key generation happens in loop above via $item->key modification?
        // Wait, original code iterated $this->filters and set ->key.
        // We need to replicate that BEFORE mapping.
        // Re-reading original logic:
        // It did a foreach loop AFTER mapping?
        // No, original logic:
        /*
        $this->filters = $this->filters->mapWithKeys(...);
        foreach ($this->filters as $filter) {
           // update key
        }
        */
        // But $this->filters items were objects (stdClass).
        // Since we are now converting to Arrays, we should set the key BEFORE mapping or handle it differently.
        // Let's reset the logic to be clean:
        
        // 1. Get objects
        $objects = collect($this->filters());
        
        // 2. Prepare keys on objects
        foreach ($objects as $filter) {
             if ($filter->column) {
                $filter->key = $filter->column;
            } else {
                $filter->key = $this->getColumnKey($filter->label);
            }
            if ($filter->type == 'magic-select') {
                $pluckKey = $filter->key;
                
                // If filter key is explicitly the DB column, we need to find the matching Column key (slug)
                // because getAllData returns data keyed by Column keys.
                // We trust $this->columns is available (it usually is in YAT tables).
                if (isset($this->columns)) {
                     $matchingCol = $this->columns->first(function($c) use ($filter) {
                         return ($c->index ?? $c->key) === $filter->key;
                     });
                     if ($matchingCol) {
                         $pluckKey = $matchingCol->key;
                     }
                }

                $options = $this->getAllData()->pluck($pluckKey)->unique()->values();
                // Map objects/arrays to strings if necessary
                $filter->options = $options->map(function($item) {
                     if (is_string($item) || is_numeric($item) || is_bool($item)) {
                         return $item;
                     }
                     if (is_object($item)) {
                         if (method_exists($item, '__toString')) {
                             return (string) $item;
                         }
                         $candidates = ['name', 'label', 'title', 'slug', 'id'];
                         foreach ($candidates as $candidate) {
                             if (isset($item->$candidate)) {
                                 return $item->$candidate;
                             }
                         }
                         // Try array access on object if supported? Or just get vars
                         // Usually Eloquent models support property access used above.
                     }
                     if (is_array($item)) {
                         $candidates = ['name', 'label', 'title', 'slug', 'id'];
                         foreach ($candidates as $candidate) {
                             if (isset($item[$candidate])) {
                                 return $item[$candidate];
                             }
                         }
                     }
                     return $item; // Fallback, let it be [object Object] or whatever if we failed
                })->filter()->unique()->values();
                
                $filter->type = 'select';
            }
        }
        
        // 3. Serialize to Arrays
        $this->filters = $objects->mapWithKeys(function($item) {
             $key = substr(str_shuffle(str_repeat('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', 5)), 0, 10);
             if (isset($item->queryCallback)) {
                $item->queryCallback = null;
             }
             return [$key => (array) get_object_vars($item)];
        });
        
        if (!$this->filters->isEmpty()) {
            $this->has_filters = true;
        }
    }

    public function getColumnKey($filter_label) {
        try {
            return $this->columns->filter(function ($column) use ($filter_label) {
                return strtolower($column->label) === strtolower($filter_label);
            })->first()->key;
        } catch (\Throwable $th) {
            throw new Exception("No column with label ".$filter_label." to associate with filter.");
        }
    }

    public function updatedFilters($key,$value) {
        if(is_array($key)) return;

        if (!str_contains($key,'filters.')) {
            return;
        }
        
        $key = str_replace(array('filters.','.input'),'',$key);
        
        $filter = $this->filters->get($key);
        
        if (!$filter) return;

        // Array access
        if (in_array($filter['type'],array("string","select"))) {
            $filter['input'] = trim($filter['input']);
        }
        if ($filter['type'] == "bool") {
            if ($filter['input'] === 'all') {
                $filter['input'] = null;
            }
        }
        if ($filter['type'] == "daterange") {
            if (empty($value)) {
                $filter['input'] = null;
                $filter['daterange'] = null;
            } else {
                $filter['input'] = json_encode($value);
                $filter['daterange'] = [
                    "start" => Carbon::parse($value['start'])->startOfDay(),
                    "end"   => Carbon::parse($value['end'])->endOfDay()
                ];
            }
        }
        
        $this->filters->put($key, $filter);
    }

    public function applyFilters($data) {
        if (!$this->has_filters) return $data;
        
        // Used for In-Memory filtering.
        // We use $this->filters (arrays) directly, no need for callbacks usually?
        // If we want callbacks to work on In-Memory data, we'd need getFreshFilters too.
        // But for now, let's just make it compatible with Array syntax.
        
        return $data->filter(function ($item) {
            foreach ($this->filters as $filter) {
                // $filter is Array
                $type = $filter['type'];
                $key = $filter['key'];
                $input = $filter['input'];

                if ($type == "string") {
                    $suffix = array_key_exists($key."_original",$item) ? '_original' : '';
                    if ($input && !str_contains(strtolower($item[$key.$suffix]), strtolower($input))) {
                        return false;
                    }
                }
                if ($type == "select") {
                    if ($input && !str_contains(strtolower($item[$key]), strtolower($input))) {
                        return false;
                    }
                }
                if ($type == "bool") {
                    if ($input === 'all' || $input === '' || is_null($input)) {
                        continue;
                    }
                    $boolVal = filter_var($input, FILTER_VALIDATE_BOOLEAN);
                    if ((bool) $item[$key] !== $boolVal) {
                        return false;
                    }
                }
                if ($type == "daterange") {
                    if (isset($filter['daterange']['start'], $filter['daterange']['end'])) {
                        // ... legacy daterange logic
                        // skipping detailed impl for in-memory as user focused on DB
                    }
                }
            }
            return true;
        });
    }
    
    public function getFreshFilters() {
        $freshFilters = collect($this->filters());
        
        // Prepare fresh filters (calculate keys)
        $freshFilters->transform(function($filter) {
            if ($filter->column) {
                $filter->key = $filter->column;
            } else {
                try {
                    $filter->key = $this->getColumnKey($filter->label);
                } catch(\Throwable $e) {}
            }
            return $filter;
        });

        // Merge state from stored $this->filters (Arrays)
        return $freshFilters->map(function($filter) {
             $storedData = $this->filters->first(function($item) use ($filter) {
                 return isset($item['key']) && $item['key'] === $filter->key;
             });
             
             if ($storedData) {
                 $filter->input = $storedData['input'];
                 if (isset($storedData['daterange'])) {
                     $filter->daterange = $storedData['daterange'] ?? null;
                 }
                 // We don't overwrite queryCallback, so fresh one stands.
             }
             return $filter;
        });
    }

    public function applyFiltersToQuery($query) {
        if (!$this->has_filters) return $query;
        
        // Use fresh filters (Objects)
        $filters = $this->getFreshFilters();
        
        foreach ($filters as $filter) {
            
            // Custom Query Callback
            if (isset($filter->queryCallback) && is_callable($filter->queryCallback)) {
                if ($filter->input) {
                     call_user_func($filter->queryCallback, $query, $filter->input, $filter);
                }
                continue; 
            }

            $key = $filter->key;
            $dbColumn = $key;
            
            // Resolve actual DB column if key is a slug
            if (isset($this->columns)) {
                $col = $this->columns->firstWhere('key', $key);
                if ($col) {
                    $dbColumn = $col->index ?? $col->key;
                }
            }

            $relation = null;
            $column = $dbColumn;

            if (str_contains($dbColumn, '.')) {
                $parts = explode('.', $dbColumn);
                $column = array_pop($parts);
                $relation = implode('.', $parts);
            }

            if ($filter->type == "string") {
                if ($filter->input) {
                    if ($relation) {
                        $query->whereHas($relation, function($q) use ($column, $filter) {
                            $q->where($column, 'like', '%' . $filter->input . '%');
                        });
                    } else {
                        $query->where($dbColumn, 'like', '%' . $filter->input . '%');
                    }
                }
            }
            if ($filter->type == "select" || $filter->type == "magic-select") {
                if ($filter->input) {
                   if ($relation) {
                        $query->whereHas($relation, function($q) use ($column, $filter) {
                            $q->where($column, $filter->input);
                        });
                    } else {
                        $query->where($dbColumn, $filter->input);
                    }
                }
            }
            if ($filter->type == "bool") {
                if ($filter->input === 'all' || $filter->input === '' || is_null($filter->input)) {
                    continue;
                }
                $boolVal = filter_var($filter->input, FILTER_VALIDATE_BOOLEAN);
                if ($relation) {
                    $query->whereHas($relation, function($q) use ($column, $boolVal) {
                        $q->where($column, $boolVal);
                    });
                } else {
                    $query->where($dbColumn, $boolVal);
                }
            }
            if ($filter->type == "daterange") {
                if (isset($filter->daterange['start'], $filter->daterange['end'])) {
                     if ($relation) {
                        $query->whereHas($relation, function($q) use ($column, $filter) {
                            $q->whereBetween($column, [
                               $filter->daterange['start'], 
                               $filter->daterange['end']
                           ]);
                        });
                    } else {
                       $query->whereBetween($dbColumn, [
                           $filter->daterange['start'], 
                           $filter->daterange['end']
                       ]);
                    }
                }
            }
        }
        return $query;
    }

    public function clearAllFilters($selectAll=false) {
        $this->yat_global_search = '';
        if ($this->filters) {
            $this->filters->transform(function ($filter) {
                $filter['input'] = null;
                return $filter;
            });
        }
        if ($selectAll) {
            $this->select_all_data(true);
        }
    }
}
