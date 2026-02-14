<?php

namespace Beartropy\Tables\Classes\Filters;

use Beartropy\Tables\Traits\Filters;

class FilterSelect extends Filter
{
    use Filters;

    /**
     * @var string
     */
    public $type = 'select';

    /**
     * @var array
     */
    public $options;

    /**
     * Create a new FilterSelect instance.
     */
    public function __construct(string $label, array $options, ?string $index = null)
    {
        parent::__construct($label, $index);
        $this->options = $options;
    }

    /**
     * Static factory method.
     */
    public static function make(string $label, array $options, ?string $index = null): Filter
    {
        return new static($label, $options, $index);
    }
}
