<?php

namespace App\Admin\DataTables\Slider;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Slider\SliderRepositoryInterface;
use App\Admin\Traits\GetConfig;
use Illuminate\Database\Eloquent\Builder;

class SliderDataTable extends BaseDataTable
{

    use GetConfig;
    /**
     * Available button actions. When calling an action, the value will be used
     * as the function name (so it should be available)
     * If you want to add or disable an action, overload and modify this property.
     *
     * @var array
     */
    // protected array $actions = ['pageLength', 'excel', 'reset', 'reload'];
    protected array $actions = ['reset', 'reload'];

    protected $nameTable = 'sliderTable';


    public function __construct(
        SliderRepositoryInterface $repository
    ) {
        parent::__construct();

        $this->repository = $repository;
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.sliders.datatable.action',
            'editlink' => 'admin.sliders.datatable.editlink',
            'status' => 'admin.sliders.datatable.status',
            'items' => 'admin.sliders.datatable.items',
        ];
    }

    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [0, 1, 2, 3, 4];

        $this->columnSearchSelect = [4];
    }

    /**
     * Get query source of dataTable.
     *
     * @return Builder
     */
    public function query(): Builder
    {
        return $this->repository->getQueryBuilderWithRelations();
    }


    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.slider', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'items' => $this->view['items'],
            'status' => $this->view['status'],
            'name' => $this->view['editlink'],
            'created_at' => '{{ date("d-m-Y", strtotime($created_at)) }}',
        ];
    }

    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [
            //
        ];
    }


    protected function addColumnAction(): void
    {
        $this->instanceDataTable = $this->instanceDataTable->addColumn('action', $this->view['action']);
    }
    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = ['name', 'status', 'items'];
    }

}
