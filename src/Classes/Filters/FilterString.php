<?php

namespace Beartropy\Tables\Classes\Filters;

use Beartropy\Tables\Traits\Filters;

class FilterString extends Filter
{
    use Filters;

    /**
     * @var string
     */
    public $type = 'string';

    /**
     * Create a new FilterString instance.
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
