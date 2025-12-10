<?php

namespace Beartropy\Tables\Classes\Columns;


use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\Traits\Columns;

class ToggleColumn extends Column
{
    use Columns;

    public $isToggle = true;
    public $what_is_true = 1;
    public $trigger = false;
    public $disableToggleWhen = null;
    public $hideToggleWhen = null;


    public function __construct(string $label, ?string $key = null) {
        parent::__construct($label, $key);
    }

    public static function make(string $label, ?string $key = null): Column
    {
        return new static($label, $key);
    }
}
