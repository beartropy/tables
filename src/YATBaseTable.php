<?php

namespace Beartropy\Tables;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Beartropy\Tables\Traits\Bulk;
use Beartropy\Tables\Traits\Data;
use Beartropy\Tables\Traits\Sort;
use Beartropy\Tables\Traits\View;
use Beartropy\Tables\Traits\Cache;
use Beartropy\Tables\Traits\Search;
use Beartropy\Tables\Traits\Columns;
use Beartropy\Tables\Traits\Filters;
use Beartropy\Tables\Traits\Options;
use Beartropy\Tables\Traits\Spinner;
use Beartropy\Tables\Traits\Pagination;
use Beartropy\Tables\Traits\StateHandler;
use Beartropy\Tables\Traits\RowManipulators;
use Beartropy\Tables\Traits\Editable;

/**
 * Base Table Component.
 *
 * This component serves as the foundation for all Beartropy Tables.
 * It integrates various traits to provide functionality such as sorting,
 * filtering, pagination, and bulk actions.
 *
 * @property mixed $model The model or builder instance used for the table data.
 * @property array $with An array of relationships to eager load.
 * @property bool $with_pagination Indicates if pagination is enabled.
 * @property string|null $layout The layout to be used for rendering the table.
 * 
 * @package Beartropy\Tables
 */
class YATBaseTable extends Component
{

    use
        WithPagination, WithoutUrlPagination
        ;
    use 
        Cache,
        Data,
        Columns,
        Bulk,
        Search,
        Pagination,
        Sort,
        Options,
        StateHandler,
        RowManipulators,
        View,
        Filters,
        Spinner,
        Editable
        ;

    /**
     * The model or a query builder instance.
     *
     * @var mixed
     */
    public $model;

    /**
     * Relationships to eager load with the model.
     *
     * @var array<int, string>
     */
    public $with = [];

    /**
     * Internal storage for user-specific configuration.
     *
     * @var mixed
     */
    private $userData;

    /**
     * Refresh the table component.
     *
     * This method acts as a listener for the 'refresh' event.
     * It re-initializes the component by calling the mount method.
     *
     * @return void
     */
    #[On('refresh')]
    public function refresh(): void
    {
        $this->mount();
    }

    /**
     * Initialize the component.
     *
     * This method prepares the environment, settings, columns, options,
     * filters, and view configurations. If no model is provided, it attempts
     * to parse and cache data.
     *
     * @return void
     */
    public function mount() {
        $this->gatherEnvData();
        $this->setColumns();
        $this->settings();
        if (!$this->model) {
            $this->parseData();
            $this->cacheData();
        }
        $this->setOptions();
        $this->setTableState();
        $this->setFilters();
        $this->mountView();
    }

    /**
     * Configure the table display mode.
     *
     * Helper to quickly enable or disable table-only view.
     * Disabling "only table" enables global search, pagination, and column toggle.
     *
     * @param bool $status Whether to show only the table (defaults to true).
     *
     * @return void
     */
    public function showOnlyTable(bool $status = true) {
        $this->useGlobalSearch(!$status);
        $this->usePagination(!$status);
        $this->showColumnToggle(!$status);
    }

    /**
     * Render the component.
     *
     * Calculates the data to be displayed, applying sorting and filtering
     * if pagination is not used. Returns the view with the row data.
     *
     * @return \Illuminate\contracts\View\View The rendered view.
     */
    public function render()
    {
        
        if ($this->with_pagination) {
            $paginatedData = $this->paginateData();
        } else {
            $paginatedData=$this->getAfterFiltersData();
            $paginatedData = $this->sortData($paginatedData);
        }
        
        $view = view('yat::livewire.yat-table', [
            'rows' => $paginatedData
        ]);

        if ($this->layout) {
            $view->layout($this->layout);
        }

        return $view;
    }
}