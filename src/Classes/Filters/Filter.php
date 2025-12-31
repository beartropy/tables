<?php

namespace Beartropy\Tables\Classes\Filters;

use Beartropy\Tables\Traits\Filters;
use Illuminate\Support\Str;

class Filter
{

    use Filters;

    /**
     * @var string
     */
    public $label;

    /**
     * @var string|null
     */
    public $column;

    /**
     * @var string|null
     */
    public $key;

    /**
     * @var mixed
     */
    public $input;

    /**
     * @var callable|null
     */
    public $queryCallback;

    /**
     * Define a custom query callback for the filter.
     *
     * @param callable $callback
     * @return self
     */
    public function query(callable $callback)
    {
        $this->queryCallback = $callback;
        return $this;
    }

    protected static $existingKeys = [];

    public function __construct(string $label, ?string $column = null)
    {
        $this->label = trim($label);
        $this->column = $column;
    }
}
