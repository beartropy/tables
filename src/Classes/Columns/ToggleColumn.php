<?php

namespace Beartropy\Tables\Classes\Columns;


use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\Traits\Columns;

class ToggleColumn extends Column
{
    use Columns;

    /**
     * @var bool
     */
    public $isToggle = true;

    /**
     * @var mixed
     */
    public $what_is_true = 1;

    /**
     * @var bool|string
     */
    public $trigger = false;

    /**
     * @var callable|null
     */
    public $disableToggleWhen = null;

    /**
     * @var callable|null
     */
    public $hideToggleWhen = null;

    /**
     * Create a new ToggleColumn instance.
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
