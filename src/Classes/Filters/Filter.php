<?php

namespace Beartropy\Tables\Classes\Filters;

use Beartropy\Tables\Traits\Filters;
use Illuminate\Support\Str;

class Filter
{

    use Filters;

    public $label;
    public $column;
    public $key;
    public $input;

    protected static $existingKeys = [];

    public function __construct(string $label, ?string $column = null) {
        $this->label = trim($label);
        $this->column = $column;
    }

}