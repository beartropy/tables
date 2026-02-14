<?php

namespace Beartropy\Tables\Traits;

trait Options
{
    /**
     * Options available for the table (e.g. for select filters or other UI elements).
     */
    public ?array $options = null;

    /**
     * Initialize and normalize options.
     *
     * Converts string options to ['label' => '...', 'icon' => null] format.
     *
     * @return void
     */
    public function setOptions()
    {
        try {
            $this->options = collect($this->options())
                ->map(function ($value) {
                    // If string, convert to the expected structure
                    if (is_string($value)) {
                        return ['label' => $value, 'icon' => null];
                    }

                    // If array, fill in missing keys with nulls
                    return array_merge(['label' => '', 'icon' => null], $value);
                })
                ->all();
        } catch (\Throwable $th) {
        }
    }
}
