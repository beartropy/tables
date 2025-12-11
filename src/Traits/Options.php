<?php

namespace Beartropy\Tables\Traits;

trait Options
{

    public $options;

    public function setOptions() {
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
        } catch (\Throwable $th) {}
    }
}