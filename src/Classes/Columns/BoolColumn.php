<?php

namespace Beartropy\Tables\Classes\Columns;


use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\Traits\Columns;

class BoolColumn extends Column
{
    use Columns;

    /**
     * @var bool
     */
    public $isBool = true;

    /**
     * @var mixed
     */
    public $what_is_true = 1;

    /**
     * @var string
     */
    public $true_icon = '<span style="color: green; font-family: Arial, sans-serif;">&#10004;</span>';

    /**
     * @var string
     */
    public $false_icon = '<span style="color: red; font-family: Arial, sans-serif;">&#10005;</span>';

    /**
     * Create a new BoolColumn instance.
     *
     * @param string $label
     * @param string|null $key
     */
    public function __construct(string $label, ?string $key = null)
    {
        parent::__construct($label, $key);
    }

    /**
     * Static factory method.
     *
     * @param string $label
     * @param string|null $key
     * @return \Beartropy\Tables\Classes\Columns\Column
     */
    public static function make(string $label, ?string $key = null): Column
    {
        return new static($label, $key);
    }
}
