<?php

namespace Beartropy\Tables\Classes\Columns;

use Beartropy\Tables\Traits\Columns;

class DateColumn extends Column
{
    use Columns;

    /**
     * @var bool
     */
    public $isDate = true;

    /**
     * The expected input format of the date.
     *
     * @var string|null
     */
    public $inputFormat = null;

    /**
     * The desired output format for the date.
     *
     * @var string
     */
    public $outputFormat = 'Y-m-d';

    /**
     * The value to display when the date is empty or null.
     *
     * @var string
     */
    public $emptyValue = '';

    /**
     * Create a new DateColumn instance.
     */
    public function __construct(string $label, ?string $key = null)
    {
        parent::__construct($label, $key);
    }

    /**
     * Static factory method.
     */
    public static function make(string $label, ?string $key = null): Column
    {
        return new static($label, $key);
    }

    /**
     * Set the expected input format of the date.
     */
    public function inputFormat(string $format): self
    {
        $this->inputFormat = $format;

        return $this;
    }

    /**
     * Set the desired output format for the date.
     */
    public function outputFormat(string $format): self
    {
        $this->outputFormat = $format;

        return $this;
    }

    /**
     * Set the value to display when the date is empty or null.
     */
    public function emptyValue(string $value): self
    {
        $this->emptyValue = $value;

        return $this;
    }
}
