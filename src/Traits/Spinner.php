<?php

namespace Beartropy\Tables\Traits;

trait Spinner
{
    /**
     * Actions that trigger the loading spinner.
     */
    public string $trigger_spinner = 'gotoPage, previousPage, nextPage, updatedSelectAll, sortBy, removeRowFromTable, yat_global_search, filters, perPage';

    public bool $loading_table_spinner = true;

    public ?string $loading_table_spinner_custom_view = null;

    /**
     * Enable or disable the table loading spinner.
     *
     * @return void
     */
    public function useTableSpinner(bool $bool)
    {
        $this->loading_table_spinner = $bool;
    }

    /**
     * Set a custom view for the loading spinner.
     *
     * @return void
     */
    public function setTableSpinnerView(string $view)
    {
        $this->loading_table_spinner_custom_view = $view;
    }

    /**
     * Add more triggers (methods/properties) to the spinner.
     *
     * @return void
     */
    public function addTargetsToSpinner(array $targets)
    {
        if (empty($targets)) {
            return;
        }
        $this->trigger_spinner .= ', '.implode(' ', $targets);
    }
}
