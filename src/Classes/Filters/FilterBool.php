<?php

namespace Beartropy\Tables\Classes\Filters;

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
     */
    public function __construct(string $label, ?array $compared_with = null, ?string $index = null)
    {
        parent::__construct($label, $index);

        if (! $compared_with) {
            $compared_with = ['true' => true, 'false' => false];
        }
        $this->compared_with = $compared_with;
    }

    /**
     * Static factory method.
     */
    public static function make(string $label, ?array $compared_with = null, ?string $index = null): Filter
    {
        return new static($label, $compared_with, $index);
    }
}
