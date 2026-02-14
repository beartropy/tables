<?php

namespace Beartropy\Tables\Classes\Filters;

use Beartropy\Tables\Traits\Filters;

class FilterSelectMagic extends Filter
{
    use Filters;

    /**
     * @var string
     */
    public $type = 'magic-select';

    /**
     * @var mixed
     */
    public $options;

    /**
     * Create a new FilterSelectMagic instance.
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
