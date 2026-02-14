<?php

namespace Beartropy\Tables\Traits;

use Closure;

trait Columns
{
    /**
     * The unique identifier column for rows.
     */
    public string $column_id = 'id';

    /**
     * A custom column ID override.
     */
    public string $custom_column_id = 'id';

    /**
     * Collection of defined columns.
     *
     * @var \Illuminate\Support\Collection|null
     */
    public $columns;

    /**
     * Whether to show the column toggle dropdown.
     */
    public bool $show_column_toggle = true;

    /**
     * Status of the column toggle dropdown (open/closed).
     */
    public bool $column_toggle_dd_status = false;

    /**
     * Indicates if there are columns collapsed on mobile view.
     */
    public bool $hasMobileCollapsedColumns = false;

    /**
     * Array of columns that are collapsed on mobile.
     */
    public array $mobileCollapsedColumns = [];

    /**
     * Initialize and process column definitions.
     *
     * Handles mobile visibility, collapse behavior, and strips closures for Livewire serialization.
     *
     * @return void
     */
    public function setColumns()
    {
        $this->mobileCollapsedColumns = [];
        $this->columns = collect($this->columns());
        $this->columns = $this->columns->map(function ($column) {
            if ($column->show_on_mobile && ! $this->yat_is_mobile) {
                $column->isVisible = false;
            }
            if ($this->yat_is_mobile) {
                if ($column->hide_on_mobile) {
                    $column->isVisible = false;
                }
                if ($column->collapseOnMobile) {
                    $column->isVisible = false;
                    $this->hasMobileCollapsedColumns = true;
                    // We need to keep track of collapsed columns to render them in the details view
                    $colVars = get_object_vars($column);
                    foreach ($colVars as $key => $value) {
                        if ($value instanceof Closure) {
                            $colVars[$key] = null;
                        }
                    }
                    $this->mobileCollapsedColumns[] = (object) $colVars;
                }
            }

            return $column;
        });

        $this->columns = $this->columns->map(function ($item) {
            $vars = get_object_vars($item);
            foreach ($vars as $key => $value) {
                if ($value instanceof Closure) {
                    $vars[$key] = null;
                }
            }

            return (object) $vars;
        });
    }

    /**
     * Get a fresh collection of column instances with closures intact.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getFreshColumns()
    {
        if (method_exists(\Beartropy\Tables\Classes\Columns\Column::class, 'resetStaticKeys')) {
            \Beartropy\Tables\Classes\Columns\Column::resetStaticKeys();
        }

        return collect($this->columns());
    }

    /**
     * Show or hide the column toggle dropdown.
     *
     * @return void
     */
    public function showColumnToggle(bool $bool)
    {
        $this->show_column_toggle = $bool;
    }

    /**
     * Set the primary key column used to identify rows.
     *
     * @return void
     */
    public function setColumnID(string $column_id)
    {
        $this->custom_column_id = $column_id;
    }

    /**
     * Hide this column on mobile devices.
     */
    public function hideOnMobile(bool $bool): self
    {
        $this->hide_on_mobile = true;

        return $this;
    }

    /**
     * Show this column only on mobile devices.
     */
    public function showOnMobile(bool $bool = true): self
    {
        $this->show_on_mobile = $bool;

        return $this;
    }

    /**
     * Set a custom Blade view for rendering the column cell.
     *
     * @param  string  $view
     */
    public function view($view): self
    {
        $this->hasView = true;
        $this->view = $view;

        return $this;
    }

    /**
     * Set CSS classes for the column's table cell (td).
     */
    public function styling(string $classes): self
    {
        $this->classes = $classes;

        return $this;
    }

    /**
     * Set CSS classes for the column's table header (th).
     */
    public function thStyling(string $classes): self
    {
        $this->th_classes = $classes;

        return $this;
    }

    /**
     * Set CSS classes for the column's header wrapper element.
     */
    public function thWrapperStyling(string $classes): self
    {
        $this->th_wrapper_classes = $classes;

        return $this;
    }

    /**
     * Set a closure that determines when the toggle should be disabled.
     */
    public function disableToggleWhen(Closure $function): self
    {
        $this->disableToggleWhen = $function;

        return $this;
    }

    /**
     * Set a closure that determines when the toggle should be hidden.
     */
    public function hideToggleWhen(Closure $function): self
    {
        $this->hideToggleWhen = $function;

        return $this;
    }

    /**
     * Set the method name to call when a toggle column is toggled.
     */
    public function trigger(string $trigger): self
    {
        $this->trigger = $trigger;

        return $this;
    }

    /**
     * Mark this column as a boolean column.
     */
    public function isBool(): self
    {
        $this->isBool = true;

        return $this;
    }

    /**
     * Set the value that represents "true" for boolean columns.
     *
     * @param  mixed  $true
     */
    public function trueIs($true): self
    {
        $this->what_is_true = $true;

        return $this;
    }

    /**
     * Set the display label/icon for the "true" state.
     *
     * @param  string  $string
     */
    public function trueLabel($string): self
    {
        $this->true_icon = $string;

        return $this;
    }

    /**
     * Set the display label/icon for the "false" state.
     *
     * @param  string  $string
     */
    public function falseLabel($string): self
    {
        $this->false_icon = $string;

        return $this;
    }

    /**
     * Allow the column value to be rendered as raw HTML.
     */
    public function toHtml(): self
    {
        $this->isHtml = true;

        return $this;
    }

    /**
     * Set the display text for a link column.
     *
     * @param  string  $text
     */
    public function text($text): self
    {
        if ($this->isLink) {
            $this->text = $text;
        }

        return $this;
    }

    /**
     * Set a closure that resolves the URL for a link column.
     */
    public function href(Closure $function): self
    {
        if ($this->isLink) {
            $this->href = $function;
        }

        return $this;
    }

    /**
     * Set the target attribute for a link column (e.g. '_blank').
     */
    public function target(string $target): self
    {
        if ($this->isLink) {
            $this->target = $target;
        }

        return $this;
    }

    /**
     * Open the link in a popup window with the given dimensions.
     *
     * @param  array{width: int, height: int}  $array
     */
    public function popup(array $array = ['width' => 750, 'height' => 800]): self
    {
        if ($this->isLink) {
            $this->popup = $array;
        }

        return $this;
    }

    /**
     * Set CSS classes for a link column's anchor tag.
     *
     * @param  string  $classes
     */
    public function classes($classes): self
    {
        if ($this->isLink) {
            $this->tag_classes = $classes;
        }

        return $this;
    }

    /**
     * Set a closure to transform the column's display value.
     */
    public function customData(Closure $function): self
    {
        $this->customData = $function;

        return $this;
    }

    /**
     * Conditionally hide the column and remove it from the column selector.
     */
    public function hideWhen(bool $bool): self
    {
        $this->isHidden = $bool;
        if ($bool) {
            $this->hideFromSelector = true;
        }

        return $this;
    }

    /**
     * Hide this column from the column toggle selector.
     */
    public function hideFromSelector(bool $bool): self
    {
        $this->hideFromSelector = $bool;

        return $this;
    }

    /**
     * Set the column's initial visibility.
     */
    public function isVisible(bool $bool): self
    {
        $this->isVisible = $bool;

        return $this;
    }

    /**
     * Set an alternative column key to use when sorting this column.
     */
    public function sortColumnBy(string $column): self
    {
        $this->sortColumnBy = $column;

        return $this;
    }
}
