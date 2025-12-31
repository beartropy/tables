<?php

namespace Beartropy\Tables\Classes\Filters;


use Beartropy\Tables\Classes\Filters\Filter;
use Beartropy\Tables\Traits\Filters;

class FilterBool extends Filter
{
    use Filters;

    /**
     * @var string
     */
    public $type = 'bool';

    /**
     * @var array
     */
    public $compared_with;

    /**
     * Create a new FilterBool instance.
     *
     * @param string $label
     * @param array|null $compared_with
     * @param string|null $index
     */
    public function __construct(string $label, ?array $compared_with = null, ?string $index = null)
    {
        parent::__construct($label, $index);

        if (!$compared_with) {
            $compared_with = ["true" => true, "false" => false];
        }
        $this->compared_with = $compared_with;
    }

    /**
     * Static factory method.
     *
     * @param string $label
     * @param array|null $compared_with
     * @param string|null $index
     * @return \Beartropy\Tables\Classes\Filters\Filter
     */
    public static function make(string $label, ?array $compared_with = null, ?string $index = null): Filter
    {
        return new static($label, $compared_with, $index);
    }
}
