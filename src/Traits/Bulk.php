<?php

namespace Beartropy\Tables\Traits;

trait Bulk
{
    /**
     * Indicates if the table has bulk actions enabled.
     *
     * @var bool
     */
    public $has_bulk = false;

    /**
     * Array of selected row IDs for bulk actions.
     *
     * @var array<int|string>
     */
    public $yat_selected_checkbox = [];

    /**
     * Controls the "Select All" checkbox state.
     *
     * @var bool
     */
    public $selectAll = false; // Controls the "Select All" checkbox

    /**
     * Indicates if the current page is selected.
     *
     * @var bool
     */
    public $pageSelected = false;

    /**
     * Indicates if all data (across all pages) is selected.
     *
     * @var bool
     */
    public $allSelected = false;

    /**
     * Clear the current selection.
     *
     * Resets the selected checkboxes and the "select all" state.
     *
     * @return void
     */
    public function emptySelection()
    {
        $this->yat_selected_checkbox = [];
        $this->selectAll = false;
    }

    /**
     * Enable or disable bulk actions.
     *
     * @param bool $bool
     * @return void
     */
    public function hasBulk(Bool $bool)
    {
        $this->has_bulk = $bool;
    }

    /**
     * Handle updates to the "Select All" checkbox.
     *
     * This method is triggered by Livewire when $selectAll property changes.
     *
     * @param bool $value The new value of the checkbox.
     * @return void
     */
    public function updatedSelectAll($value)
    {
        #$this->select_all_data($value);
        $this->selectCurrentPage($value);
    }

    /**
     * Select all rows on the current page.
     *
     * @param bool $value If true, selects all rows on the current page. If false, deselects them.
     * @return void
     */
    public function selectCurrentPage($value)
    {

        $data = $this->getCurrentPageData();
        $this->yat_selected_checkbox = $value ? $data->pluck($this->column_id)->toArray() : [];
        $this->pageSelected = true;
        $this->allSelected = false;
    }

    /**
     * Select all rows across all pages (after filters).
     *
     * @param bool $value If true, selects all matching rows. If false, clears selection.
     * @return void
     */
    public function select_all_data($value)
    {
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

    /**
     * Get the list of selected row IDs.
     *
     * @return array<int|string>
     */
    public function getSelectedRows()
    {
        return $this->yat_selected_checkbox;
    }
}
