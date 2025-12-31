<?php

namespace Beartropy\Tables\Classes\Filters;


use Beartropy\Tables\Classes\Filters\Filter;
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
     *
     * @param string $label
     * @param array $options
     * @param string|null $index
     */
    public function __construct(string $label, array $options, ?string $index = null)
    {
        parent::__construct($label, $index);
        $this->options = $options;
    }

    /**
     * Static factory method.
     *
     * @param string $label
     * @param array $options
     * @param string|null $index
     * @return \Beartropy\Tables\Classes\Filters\Filter
     */
    public static function make(string $label, array $options, ?string $index = null): Filter
    {
        return new static($label, $options, $index);
    }
}
