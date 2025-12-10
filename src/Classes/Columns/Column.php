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
    public $has_modified_data = false;
    public $hide_on_mobile = false;

    protected static $existingKeys = [];

    public function __construct(string $label, ?string $index = null) {
        $this->label = trim($label);
        $this->index = $index ?? $this->key;
        $this->key = $this->generateUniqueKey($label);
    }

    public static function make(string $label, ?string $key = null): Column
    {
        return new static($label, $key);
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