<?php

namespace Beartropy\Tables\Traits;

trait Pagination
{

    /**
     * The pagination theme to use (e.g. 'tailwind', 'bootstrap').
     *
     * @var string
     */
    public $paginationTheme = 'tailwind'; // Use Tailwind for pagination

    /**
     * Number of items per page.
     *
     * @var int|string
     */
    public $perPage = "10";

    /**
     * Display value for per page selector.
     *
     * @var string
     */
    public $perPageDisplay = "10";

    /**
     * Available options for items per page.
     *
     * @var array<string>
     */
    public $perPageOptions = ["10", "15", "25", "50", "100", "Total"];

    /**
     * Whether pagination is enabled.
     *
     * @var bool
     */
    public $with_pagination = true;

    /**
     * Current page number.
     *
     * @var int|null
     */
    public $currentPageNumber;

    /**
     * Force a specific page number.
     *
     * @var int|false
     */
    public $forcePageNumber = false;

    /**
     * Update the per-page display value.
     *
     * Handles the 'Total' option to show all records.
     * Resets selection when page size changes.
     *
     * @param string|int $value
     * @return void
     */
    public function updatedPerPageDisplay($value)
    {
        if ($value == 'Total') {
            $this->perPage = 9999999999999;
            $this->perPageDisplay = 'Total';
        } else {
            $this->perPage = $value;
            $this->perPageDisplay = $value;
        }
        $this->updatedSelectAll(false);
        $this->selectAll = false;
        $this->allSelected = false;
        $this->pageSeleted = false;
    }

    /**
     * Enable or disable pagination.
     *
     * @param bool $bool
     * @return void
     */
    public function usePagination(bool $bool)
    {
        $this->with_pagination = $bool;
    }

    /**
     * Set the default items per page.
     *
     * @param int $number Passing 0 sets it to 'Total' (all items).
     * @return void
     */
    public function setPerPageDefault(Int $number)
    {
        if ($number == 0) {
            $this->perPage = 9999999999999;
            $this->perPageDisplay = 'Total';
        } else {
            $this->perPage = $number;
            $this->perPageDisplay = $number;
        }
    }

    /**
     * Set available per-page options.
     *
     * @param array $array
     * @return void
     */
    public function setPerPageOptions(array $array)
    {
        $this->perPageOptions = $array;
    }

    /**
     * specific method to handle pagination logic.
     *
     * Applies search, filters, and sort logic to the query or collection,
     * then paginates the result.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginateData()
    {
        if ($this->model) {
            $query = $this->model::query();

            if (!empty($this->with)) {
                $query->with($this->with);
            }

            // Apply Search
            if (method_exists($this, 'applySearchToQuery')) {
                $this->applySearchToQuery($query);
            }

            // Apply Filters
            if (method_exists($this, 'applyFiltersToQuery')) {
                $this->applyFiltersToQuery($query);
            }

            // Apply Sort
            if (method_exists($this, 'applySortToQuery')) {
                $this->applySortToQuery($query);
            }

            $currentPage = $this->currentPageNumber = \Illuminate\Pagination\Paginator::resolveCurrentPage();
            if ($this->forcePageNumber) {
                $currentPage = $this->forcePageNumber;
            }

            // Paginate
            $paginatedData = $query->paginate($this->perPage, ['*'], 'page', $currentPage);

            // Transform the data using our new Data trait method
            // We need to support the processCollection logic but on the collection inside paginator
            $transformedCollection = $this->processCollection($paginatedData->getCollection());
            $paginatedData->setCollection($transformedCollection);

            $this->forcePageNumber = false;
            return $paginatedData;
        } else {
            $data = $this->getAfterFiltersData();

            // Apply sorting before pagination
            $data = $this->sortData($data);

            $currentPage = $this->currentPageNumber = \Illuminate\Pagination\Paginator::resolveCurrentPage();
            if ($this->forcePageNumber) {
                $currentPage = $this->forcePageNumber;
            }

            $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator(
                $data->forPage($currentPage, $this->perPage),
                $data->count(),
                $this->perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );
            $this->forcePageNumber = false;
            return $paginatedData;
        }
    }

    /**
     * Get paginated data for a specific page number.
     *
     * Used for array-based data sources where we slice the collection manually.
     *
     * @param int $currentPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPageData($currentPage)
    {
        $data = $this->getAfterFiltersData();

        // Apply sorting before pagination
        $data = $this->sortData($data);

        $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator(
            $data->forPage($currentPage, $this->perPage),
            $data->count(),
            $this->perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return $paginatedData;
    }
}
