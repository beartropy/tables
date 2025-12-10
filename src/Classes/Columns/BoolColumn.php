<?php

namespace Beartropy\Tables\Classes\Columns;


use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\Traits\Columns;

class BoolColumn extends Column
{
    use Columns;

    public $isBool = true;
    public $what_is_true = 1;
    public $true_icon = '<span style="color: green; font-family: Arial, sans-serif;">&#10004;</span>';
    public $false_icon = '<span style="color: red; font-family: Arial, sans-serif;">&#10005;</span>';


    public function __construct(string $label, ?string $key = null) {
        parent::__construct($label, $key);
    }

    public static function make(string $label, ?string $key = null): Column
    {
        return new static($label, $key);
    }
}
