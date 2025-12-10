<?php

namespace Beartropy\Tables\Traits;

trait Bulk
{
    public $has_bulk = false;
    public $yat_selected_checkbox = [];
    public $selectAll = false; // Controls the "Select All" checkbox

    public $pageSelected = false;
    public $allSelected = false;

    public function emptySelection() {
        $this->yat_selected_checkbox = [];
        $this->selectAll = false;
    }

    public function hasBulk(Bool $bool) {
        $this->has_bulk = $bool;
    }

    public function updatedSelectAll($value)
    {
        #$this->select_all_data($value);
        $this->selectCurrentPage($value);
    }

    public function selectCurrentPage($value) {
        
        $data = $this->getCurrentPageData();
        $this->yat_selected_checkbox = $value ? $data->pluck($this->column_id)->toArray() : [];
        $this->pageSelected = true;
        $this->allSelected = false;
    }

    public function select_all_data($value) {
        $data = $this->getAfterFiltersData();
        // If selectAll is checked, select all visible row IDs; otherwise, clear the selected array
        $this->yat_selected_checkbox = $value ? $data->pluck($this->column_id)->toArray() : [];
        $this->pageSelected = false;
        $this->allSelected = true;
    }

/*     public function changeYatSelectedCheckbox($id)
    {

        if (in_array($id, $this->yat_selected_checkbox)) {
            $this->yat_selected_checkbox = array_diff($this->yat_selected_checkbox, [$id]);
        } else {
            $this->yat_selected_checkbox[] = $id;
        }

    } */

    public function getSelectedRows() {
        return $this->yat_selected_checkbox;
    }

}