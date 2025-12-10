<?php

namespace Beartropy\Tables\Classes\Columns;


use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\Traits\Columns;

class LinkColumn extends Column
{
    use Columns;

    public $isLink = true;
    public $href;
    public $text;
    public $tag_styles;
    public $has_modified_data = false;

    public function __construct(string $label, ?string $key = null) {
        parent::__construct($label, $key);
    }

    public static function make(string $label, ?string $key = null): Column
    {
        return new static($label, $key);
    }
}