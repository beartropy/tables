<?php

namespace Beartropy\Tables\Classes\Filters;


use Beartropy\Tables\Classes\Filters\Filter;
use Beartropy\Tables\Traits\Filters;

class FilterSelect extends Filter
{
    use Filters;

    public $type = 'select';
    public $options;

    public function __construct(string $label, array $options, ?string $index = null) {
        parent::__construct($label, $index);
        $this->options = $options;
    }

    public static function make(string $label, array $options, ?string $index = null): Filter
    {
        return new static($label, $options, $index);
    }
}