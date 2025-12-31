<?php

namespace Beartropy\Tables\Traits;

trait Options
{

    /**
     * Options available for the table (e.g. for select filters or other UI elements).
     *
     * @var array
     */
    public $options;

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
                    // Si es string, lo convertimos a la estructura deseada
                    if (is_string($value)) {
                        return ['label' => $value, 'icon' => null];
                    }
                    // Si es array, rellenamos lo que falte con nulos
                    return array_merge(['label' => '', 'icon' => null], $value);
                })
                ->all();
        } catch (\Throwable $th) {
        }
    }
}
