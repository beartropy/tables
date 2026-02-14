<?php

namespace Beartropy\Tables\Classes\Filters;

use Beartropy\Tables\Traits\Filters;

class FilterDateRange extends Filter
{
    use Filters;

    /**
     * @var string
     */
    public $type = 'daterange';

    /**
     * @var array
     */
    public $daterange = [];

    /**
     * Create a new FilterDateRange instance.
     */
    public function __construct(string $label, ?string $index = null)
    {
        parent::__construct($label, $index);
    }

    /**
     * Static factory method.
     */
    public static function make(string $label, ?string $index = null): Filter
    {
        return new static($label, $index);
    }
}
