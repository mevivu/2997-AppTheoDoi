<?php

namespace App\Admin\DataTables\Step;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Step\StepRepositoryInterface;
use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Builder;

class StepDataTable extends BaseDataTable
{
    protected $nameTable = 'stepTable';


    public function __construct(
        StepRepositoryInterface $repository
    )
    {
        $this->repository = $repository;

        parent::__construct();

    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.step.datatable.action',
            'title' => 'admin.step.datatable.title',
            'checkbox' => 'admin.common.checkbox',
        ];
    }


    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [1, 2, 3];
        $this->columnSearchDate = [3];

    }


    /**
     * Get query source of dataTable.
     *
     * @return Builder
     */
    public function query(): Builder
    {
        $id = request()->route('developGuideId');
        return $this->repository->getByQueryBuilder(
            [
                'guide_id' => $id
            ]
        );
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.step', []);
    }

    public function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            //
        ];
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'checkbox' => $this->view['checkbox'],
            'title' => $this->view['title'],
            'created_at' => function ($query) {
                return format_datetime($query->created_at);
            },
        ];
    }

    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [
            'action' => $this->view['action'],

        ];
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = [
            'title',
            'action',
            'checkbox',
        ];
    }


}
