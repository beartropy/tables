<?php

namespace Beartropy\Tables\Traits;

trait Spinner
{
    public $trigger_spinner='gotoPage, previousPage, nextPage, updatedSelectAll, sortBy, removeRowFromTable, yat_global_search, filters, perPage';

    public $loading_table_spinner = true;
    public $loading_table_spinner_custom_view;

    public function useTableSpinner(bool $bool) {
        $this->loading_table_spinner = $bool;
    }

    public function setTableSpinnerView(string $view) {
        $this->loading_table_spinner_custom_view = $view;
    }

    public function addTargetsToSpinner(array $targets) {
        if (empty($targets)) return;
        $this->trigger_spinner .= ', '.implode(' ',$targets);
    }

}