<?php

namespace Beartropy\Tables\Classes\Columns;


use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\Traits\Columns;

class LinkColumn extends Column
{
    use Columns;

    /**
     * @var bool
     */
    public $isLink = true;

    /**
     * @var mixed
     */
    public $href;

    /**
     * @var string|null
     */
    public $text;

    /**
     * @var string|null
     */
    public $tag_styles;

    /**
     * @var bool
     */
    public $has_modified_data = false;

    /**
     * Create a new LinkColumn instance.
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
