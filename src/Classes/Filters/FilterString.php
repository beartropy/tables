<?php

namespace Beartropy\Tables\Classes\Filters;


use Beartropy\Tables\Classes\Filters\Filter;
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
     *
     * @param string $label
     * @param string|null $index
     */
    public function __construct(string $label, ?string $index = null)
    {
        parent::__construct($label, $index);
    }

    /**
     * Static factory method.
     *
     * @param string $label
     * @param string|null $index
     * @return \Beartropy\Tables\Classes\Filters\Filter
     */
    public static function make(string $label, ?string $index = null): Filter
    {
        return new static($label, $index);
    }
}
