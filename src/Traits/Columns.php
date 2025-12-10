<?php

namespace Beartropy\Tables\Traits;

use Closure;
use Exception;
use Illuminate\Support\Str;

trait Columns
{
    public $column_id = 'id';
    public $custom_column_id = 'id';
    public $columns;
    public $show_column_toggle = true;
    public $column_toggle_dd_status = false;

    public function setColumns() {
        $this->columns = collect($this->columns());
        $this->columns = $this->columns->map(function ($column) {
            if ($this->yat_is_mobile && $column->hide_on_mobile) {
            $column->isVisible = false;
            }
            return $column;
        });

        $this->columns = $this->columns->map(function ($item) {
            return (object) get_object_vars($item);
        });
    }

    public function showColumnToggle(bool $bool) {
        $this->show_column_toggle = $bool;
    }

    public function setColumnID(String $column_id) {
        $this->custom_column_id = $column_id;
    }

    public function hideOnMobile(Bool $bool): self {
        $this->hide_on_mobile = true;
        return $this;
    }

    public function view($view): self {
        $this->hasView = true;
        $this->view = $view;
        return $this;
    }

    public function styling(String $classes): self {
        $this->classes = $classes;
        return $this;
    }

    public function thStyling(String $classes): self {
        $this->th_classes = $classes;
        return $this;
    }

    public function thWrapperStyling(String $classes): self {
        $this->th_wrapper_classes = $classes;
        return $this;
    }

    public function disableToggleWhen(Closure $function): self {
        $this->disableToggleWhen = $function;
        return $this;
    }
    public function hideToggleWhen(Closure $function): self {
        $this->hideToggleWhen = $function;
        return $this;
    }

    public function trigger(String $trigger): self {
        $this->trigger = $trigger;
        return $this;
    }

    public function isBool(): self {
        $this->isBool = true;
        return $this;
    }

    public function trueIs($true): self {
        $this->what_is_true = $true;
        return $this;
    }

    public function trueLabel($string): self {
        $this->true_icon = $string;
        return $this;
    }

    public function falseLabel($string): self {
        $this->false_icon = $string;
        return $this;
    }

    public function toHtml(): self {
        $this->isHtml = true;
        return $this;
    }

    public function text($text): self {
        if ($this->isLink) {
            $this->text = $text;
        }
        return $this;
    }

    public function href(Closure $function): self {
        if ($this->isLink) {
            $this->href = $function;
        }
        return $this;
    }

    public function target(string $target): self {
        if ($this->isLink) {
            $this->target = $target;
        }
        return $this;
    }

    public function popup(array $array = ["width" => 750,"height"=>800]): self {
        if ($this->isLink) {
            $this->popup = $array;
        }
        return $this;
    }

    public function classes($classes): self {
        if ($this->isLink) {
            $this->tag_classes = $classes;
        }
        return $this;
    }

    public function customData(Closure $function): self {
        $this->customData = $function;
        return $this;
    }

    public function hideWhen(Bool $bool): self {
        $this->isHidden = $bool;
        if ($bool) {
            $this->hideFromSelector = true;
        }
        return $this;
    }

    public function hideFromSelector(Bool $bool): self {
        $this->hideFromSelector = $bool;
        return $this;
    }

    public function isVisible(Bool $bool): self {
        $this->isVisible = $bool;
        return $this;
    }

    public function sortColumnBy(String $column): self {
        $this->sortColumnBy = $column;
        return $this;
    }

/*     public function generateTempID() {
        if ($this->has_bulk) {
            if (!$this->yatTableData->isEmpty()) {
                $idColumn = $this->column_id;
                if (is_array($this->yatTableData->first())) {
                    if (!array_key_exists($idColumn, $this->yatTableData->first())) {
                        $this->yatTableData = $this->yatTableData->map(function ($item, $index) use ($idColumn) {
                            $item[$idColumn] = 'yat-' . $index; // Create a temporary ID
                            return $item;
                        });
                    }
                } else {
                    if(!property_exists($this->yatTableData->first(), $idColumn)) {
                        $this->yatTableData = $this->yatTableData->map(function ($item, $index) use ($idColumn) {
                            $item->$idColumn = 'yat-' . $index; // Create a temporary ID
                            return $item;
                        });
                    }
                }
            }
        }
    } */
}

