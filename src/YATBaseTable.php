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
        Spinner
        ;

    public $model;

    private $userData;

    #[On('refresh')]
    public function refresh(): void
    {
        $this->mount();
    }

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