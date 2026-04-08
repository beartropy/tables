<?php

namespace Beartropy\Tables\Collections;

use Illuminate\Support\Collection;

class TableCollection extends Collection
{
    /**
     * Column key → label map set by processCollection().
     *
     * Stored as a static so it survives Collection operations
     * (filter, map, etc.) that return new instances via `new static(...)`.
     *
     * @var array<string, string>
     */
    protected static array $columnLabels = [];

    /**
     * Store column labels for the current export context.
     */
    public static function setColumnLabels(array $labels): void
    {
        static::$columnLabels = $labels;
    }

    /**
     * Retrieve the column labels set by processCollection().
     *
     * @return array<string, string> key => label
     */
    public static function getColumnLabels(): array
    {
        return static::$columnLabels;
    }
}
