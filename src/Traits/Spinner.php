<?php

namespace Beartropy\Tables\Traits;

trait Spinner
{
    /**
     * Actions that trigger the loading spinner.
     *
     * @var string
     */
    public $trigger_spinner = 'gotoPage, previousPage, nextPage, updatedSelectAll, sortBy, removeRowFromTable, yat_global_search, filters, perPage';

    /**
     * @var bool
     */
    public $loading_table_spinner = true;
    /**
     * @var string|null
     */
    public $loading_table_spinner_custom_view;

    /**
     * Enable or disable the table loading spinner.
     *
     * @param bool $bool
     * @return void
     */
    public function useTableSpinner(bool $bool)
    {
        $this->loading_table_spinner = $bool;
    }

    /**
     * Set a custom view for the loading spinner.
     *
     * @param string $view
     * @return void
     */
    public function setTableSpinnerView(string $view)
    {
        $this->loading_table_spinner_custom_view = $view;
    }

    /**
     * Add more triggers (methods/properties) to the spinner.
     *
     * @param array $targets
     * @return void
     */
    public function addTargetsToSpinner(array $targets)
    {
        if (empty($targets)) return;
        $this->trigger_spinner .= ', ' . implode(' ', $targets);
    }
}
