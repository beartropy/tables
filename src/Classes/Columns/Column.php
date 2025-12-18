<?php

namespace Beartropy\Tables\Classes\Columns;

use Illuminate\Support\Str;
use Beartropy\Tables\Traits\Columns;

class Column
{
    use Columns;

    public $label;
    public $key;
    public $index;
    public $isVisible = true;
    public $isHidden = false;
    public $hideFromSelector = false;
    public $customData = null;
    public $classes = '';
    public $th_classes = '';
    public $th_wrapper_classes = '';
    public $has_modified_data = false;
    public $hide_on_mobile = false;
    public $collapseOnMobile = false;
    public $show_on_mobile = false;
    public $cardTitle = false;
    public $showOnCard = false;
    
    public $sortableCallback = null;
    public $searchableCallback = null;
    public $isSortable = false;
    public $isSearchable = true;

    protected static $existingKeys = [];

    public function __construct(string $label, ?string $index = null) {
        $this->label = trim($label);
        $this->key = $this->generateUniqueKey($label);
        $this->index = $index ?? $this->key;
    }

    public static function make(string $label, ?string $key = null): Column
    {
        return new static($label, $key);
    }

    public function collapseOnMobile(bool $bool = true): self {
        $this->collapseOnMobile = $bool;
        return $this;
    }

    public function cardTitle(bool $bool = true): self {
        $this->cardTitle = $bool;
        return $this;
    }

    public function showOnCard(bool $bool = true): self {
        $this->showOnCard = $bool;
        return $this;
    }

    public function sortable($callback = true): self {
        if (is_callable($callback)) {
            $this->sortableCallback = $callback;
            $this->isSortable = true;
        } else {
            $this->isSortable = $callback;
        }
        return $this;
    }

    public function searchable($callback = true): self {
        if (is_callable($callback)) {
            $this->searchableCallback = $callback;
            $this->isSearchable = true;
        } else {
            $this->isSearchable = $callback;
        }
        return $this;
    }

    public function pushLeft(): self {
        $this->classes .= ' text-left';
        if (!isset($this->th_wrapper_classes)) {
            $this->th_wrapper_classes = '';
        }
        $this->th_wrapper_classes .= ' text-left';
        return $this;
    }

    public function pushRight(): self {
        $this->classes .= ' text-right';
        if (!isset($this->th_wrapper_classes)) {
            $this->th_wrapper_classes = '';
        }
        $this->th_wrapper_classes .= ' text-right';
        return $this;
    }

    public static function resetStaticKeys() {
        static::$existingKeys = [];
    }

    protected function generateUniqueKey(string $label): string {
        
        // Convert label to a slug with underscore separator
        if ($label == "#") $label = "hash";
        $baseKey = Str::slug($label, '_');
        $key = $baseKey;
        $counter = 1;

        // Ensure uniqueness across all created Column objects
        while (in_array($key, static::$existingKeys)) {
            $key = $baseKey."_".$counter++;
        }

        // Store the key to prevent future duplicates
        static::$existingKeys[] = $key;

        return $key;
    }

}